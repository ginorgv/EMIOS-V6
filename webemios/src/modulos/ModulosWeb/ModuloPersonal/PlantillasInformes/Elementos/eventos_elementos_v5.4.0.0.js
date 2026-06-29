//
// Funciones de elementos de las plantillas de informes
//


function boton_personal_mostrar_ventana_anyadir_modificar_elemento_plantilla_informe(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_plantilla_informe = params[1];
    var id_elemento = params[2];
    var tipo_operacion_administracion = params[3];

    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/muestra_ventana_anyadir_modificar_elemento.php", {
        id_plantilla_informe: id_plantilla_informe,
        id_elemento: id_elemento,
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


function boton_personal_eliminar_elemento_plantilla_informe(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_plantilla_informe = params[1];
	var id_elemento = params[2];
    var tipo = params[3];
    var nombre_elemento = $(this).attr('nombre_elemento');

    jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar el elemento?") + "\n(" + escapeHtml(nombre_elemento) + ")", TLNT.Idiomas._("Pregunta"), function(res) {
		if (res == true) {
			$.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/elimina_elemento.php", {
                id_plantilla_informe: id_plantilla_informe,
                id_elemento: id_elemento,
                tipo: tipo
			},
			function(data, status) {
				var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                jInfo(resultado.msg);
				actualiza_tabla_elementos_plantilla_informe(id_plantilla_informe);
                switch (tipo) {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA: {
                        recarga_controles_subtitulos_portadas_informe_plantilla_informe(id_plantilla_informe);
                        break;
                    }
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO: {
                        recarga_controles_titulos_informe_plantilla_informe(id_plantilla_informe);
                        break;
                    }
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO: {
                        recarga_controles_textos_informe_plantilla_informe(id_plantilla_informe);
                        break;
                    }
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN: {
                        recarga_controles_imagenes_informe_plantilla_informe(id_plantilla_informe);
                        break;
                    }
                }
			});
		}
	});
}


function boton_personal_anyadir_modificar_elemento_plantilla_informe() {
    // Comprobación de datos correctos de las pestañas visibles
    var tipo = $('#tipo_elemento_plantilla_informe').val();
    if (tipo == TIPO_NINGUNO) {
        jAlert(TLNT.Idiomas._('No hay tipo de elemento seleccionado'));
        return;
    }

    var ids_pestanyas_visibles = ["tab-principal"];
    switch (tipo) {
        // Elementos generales
        case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA: {
            ids_pestanyas_visibles.push("tab-tipo-portada");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO: {
            ids_pestanyas_visibles.push("tab-tipo-titulo");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO: {
            ids_pestanyas_visibles.push("tab-tipo-texto");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_NOTAS: {
            ids_pestanyas_visibles.push("tab-tipo-notas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN: {
            ids_pestanyas_visibles.push("tab-tipo-imagen");
            break;
        }
        // Elementos de varios módulos
        case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS: {
            ids_pestanyas_visibles.push("tab-tipo-comentarios-principal");
            ids_pestanyas_visibles.push("tab-tipo-comentarios-sensores");
            ids_pestanyas_visibles.push("tab-tipo-comentarios-actuadores");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        // Elementos de sensores (Eventos)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS: {
            ids_pestanyas_visibles.push("tab-tipo-sensores-activaciones-eventos-principal");
            ids_pestanyas_visibles.push("tab-tipo-sensores-activaciones-eventos-eventos");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            break;
        }
        // Elementos de sensores (Información)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION: {
            ids_pestanyas_visibles.push("tab-tipo-sensores-informacion");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        // Elementos de sensores (Análisis)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO: {
            ids_pestanyas_visibles.push("tab-tipo-sensores-analisis-horario");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO: {
            ids_pestanyas_visibles.push("tab-tipo-sensores-analisis-diario");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO: {
            ids_pestanyas_visibles.push("tab-tipo-sensores-analisis-comportamiento-principal");
            ids_pestanyas_visibles.push("tab-tipo-sensores-analisis-comportamiento-sensores");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        // Elementos de sensores (Comparación)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS: {
            ids_pestanyas_visibles.push("tab-tipo-sensores-comparacion-periodos");
            ids_pestanyas_visibles.push("tab-duracion-separacion-periodos");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO: {
            ids_pestanyas_visibles.push("tab-tipo-sensores-comparacion-perfil-horario-principal");
            ids_pestanyas_visibles.push("tab-tipo-sensores-comparacion-perfil-horario-perfil-horario");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES: {
            ids_pestanyas_visibles.push("tab-tipo-sensores-comparacion-campos-iguales-principal");
            ids_pestanyas_visibles.push("tab-tipo-sensores-comparacion-campos-iguales-sensores-secundarios");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES: {
            ids_pestanyas_visibles.push("tab-tipo-sensores-comparacion-campos-diferentes-principal");
            ids_pestanyas_visibles.push("tab-tipo-sensores-comparacion-campos-diferentes-sensor-1");
            ids_pestanyas_visibles.push("tab-tipo-sensores-comparacion-campos-diferentes-sensor-2");
            ids_pestanyas_visibles.push("tab-tipo-sensores-comparacion-campos-diferentes-sensor-3");
            ids_pestanyas_visibles.push("tab-tipo-sensores-comparacion-campos-diferentes-sensor-4");
            ids_pestanyas_visibles.push("tab-tipo-sensores-comparacion-campos-diferentes-sensor-5");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO: {
            ids_pestanyas_visibles.push("tab-tipo-sensores-analisis-comparativo-principal");
            ids_pestanyas_visibles.push("tab-tipo-sensores-analisis-comparativo-sensores");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES: {
            ids_pestanyas_visibles.push("tab-tipo-sensores-valores-generales-principal");
            ids_pestanyas_visibles.push("tab-tipo-sensores-valores-generales-campo-1");
            ids_pestanyas_visibles.push("tab-tipo-sensores-valores-generales-campo-2");
            ids_pestanyas_visibles.push("tab-tipo-sensores-valores-generales-campo-3");
            ids_pestanyas_visibles.push("tab-tipo-sensores-valores-generales-sensores");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES: {
            ids_pestanyas_visibles.push("tab-tipo-sensores-incrementos-totales-principal");
            ids_pestanyas_visibles.push("tab-tipo-sensores-incrementos-totales-campo-1");
            ids_pestanyas_visibles.push("tab-tipo-sensores-incrementos-totales-campo-2");
            ids_pestanyas_visibles.push("tab-tipo-sensores-incrementos-totales-campo-3");
            ids_pestanyas_visibles.push("tab-tipo-sensores-incrementos-totales-sensores");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        // Elementos de sensores (Estadística)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA: {
            ids_pestanyas_visibles.push("tab-tipo-sensores-histograma");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION: {
            ids_pestanyas_visibles.push("tab-tipo-sensores-correlacion-sensores-principal");
            ids_pestanyas_visibles.push("tab-tipo-sensores-correlacion-sensor-independiente-1");
            ids_pestanyas_visibles.push("tab-tipo-sensores-correlacion-sensor-independiente-2");
            ids_pestanyas_visibles.push("tab-tipo-sensores-correlacion-sensor-independiente-3");
            ids_pestanyas_visibles.push("tab-tipo-sensores-correlacion-sensor-independiente-4");
            ids_pestanyas_visibles.push("tab-tipo-sensores-correlacion-sensor-dependiente");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        // Elementos de actuadores (Información)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS: {
            ids_pestanyas_visibles.push("tab-tipo-actuadores-informacion-acciones-enviadas-principal");
            ids_pestanyas_visibles.push("tab-tipo-actuadores-informacion-acciones-enviadas-sensor");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        // Elementos de SmartMeter (Consumos y costes)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES: {
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-consumos-costes-generales-principal");
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-consumos-costes-generales-sensores");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES: {
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-consumos-costes-totales-principal");
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-consumos-costes-totales-sensores");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS: {
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-comparacion-periodos");
            ids_pestanyas_visibles.push("tab-duracion-separacion-periodos");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS: {
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-simulador-tarifas-principal");
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-simulador-tarifas-tarifas");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD: {
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-consumos-costes-tramos-electricidad");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD: {
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-cortes-tension-electricidad");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD: {
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-excesos-potencia-electricidad");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD: {
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-excesos-energia-reactiva-electricidad");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS: {
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-excesos-caudal-gas");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        // Elementos de SmartMeter (Compra de energía)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA: {
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-desvios-compra-energia");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA: {
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-desvios-ponderados-compra-energia");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            ids_pestanyas_visibles.push("tab-horario-semanal-fechas");
            break;
        }
        // Elementos de SmartMeter (Facturas)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA: {
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-simulador-factura-principal");
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-simulador-factura-reparto-costes");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            break;
        }
        // Elementos de SmartMeter (Tarifas)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION: {
            ids_pestanyas_visibles.push("tab-tipo-smartmeter-instalacion");
            break;
        }
        // Elementos de proyectos (Líneas base)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE: {
            ids_pestanyas_visibles.push("tab-tipo-proyectos-simulador-linea-base");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            break;
        }
        // Elementos de proyectos (Información)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO: {
            ids_pestanyas_visibles.push("tab-tipo-proyectos-informacion-proyecto");
            ids_pestanyas_visibles.push("tab-periodo-tiempo");
            break;
        }
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

    // Nombre de elemento
    var nombre = $('#nombre_elemento_plantilla_informe').val();
    if (comprueba_longitud_cadena(nombre, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
        $("#nombre_elemento_plantilla_informe").addClass('data-check-failed');
        return;
    }

    // Nombre por defecto del elemento (si el nombre no es obligatorio)
    if (nombre == "") {
        switch (tipo) {
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_PAGINA: {
                nombre = TLNT.Idiomas._("Salto de página");
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_LINEA: {
                nombre = TLNT.Idiomas._("Salto de línea");
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA: {
                nombre = TLNT.Idiomas._("Portada");
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO: {
                nombre = TLNT.Idiomas._("Título");
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO: {
                nombre = TLNT.Idiomas._("Texto");
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_NOTAS: {
                nombre = TLNT.Idiomas._("Notas");
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN: {
                nombre = TLNT.Idiomas._("Imagen");
                break;
            }
        }
    }

    // Plantilla de informe destino
    var id_plantilla_informe_destino = $('#id_plantilla_informe_destino_elemento_plantilla_informe').val();

    // Elementos del informe
    var elementos_informe = [];
    var cadena_elementos_informe = "";
    switch (tipo) {
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO: {
            // Elementos del informe
            $("#elementos_informe_elemento_plantilla_informe option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    elementos_informe.push($(this).val());
                }
            });
            if (elementos_informe.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos un elemento"));
                return;
            }

            // Cadena de elementos del informe
            cadena_elementos_informe = elementos_informe.join(SEPARADOR_PARAMETROS_SIMPLES);
            break;
        }
    }

    // Parámetros de tipo (de pestañas comunes a varios tipos de elementos)

    // Periodo de tiempo
    if (ids_pestanyas_visibles.indexOf("tab-periodo-tiempo") > -1) {
        var modificar_periodo_tiempo = $('#modificar_periodo_tiempo_elemento_plantilla_informe').val();
        var periodo_tiempo = $('#periodo_tiempo_elemento_plantilla_informe').val();
        var iniciar_comienzo_periodo_tiempo = $('#iniciar_comienzo_periodo_tiempo_elemento_plantilla_informe').val();
        var numero_periodos_tiempo = $('#numero_periodos_tiempo_elemento_plantilla_informe').val();
        if ((modificar_periodo_tiempo == VALOR_SI) && (numero_periodos_tiempo < 1)) {
            jAlert(TLNT.Idiomas._("El número de periodos de tiempo debe ser mayor o igual que 1"));
            return;
        }
        numero_periodos_tiempo -= 1;
        var fecha_inicio_periodo_tiempo = $('#fecha_inicio_periodo_tiempo_elemento_plantilla_informe').val();
        var fecha_inicio_periodo_tiempo_base_datos = convierte_formato_fecha(fecha_inicio_periodo_tiempo, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);
    }

    // Duración y separación de periodos
    if (ids_pestanyas_visibles.indexOf("tab-duracion-separacion-periodos") > -1) {
        var modificar_duracion_periodos = $('#modificar_duracion_periodos_elemento_plantilla_informe').val();
        var periodo_tiempo_duracion_periodos = $('#periodo_tiempo_duracion_periodos_elemento_plantilla_informe').val();
        var iniciar_comienzo_periodo_tiempo_duracion_periodos = $('#iniciar_comienzo_periodo_tiempo_duracion_periodos_elemento_plantilla_informe').val();
        var numero_periodos_tiempo_duracion_periodos = $('#numero_periodos_tiempo_duracion_periodos_elemento_plantilla_informe').val();
        if ((modificar_duracion_periodos == VALOR_SI) && (numero_periodos_tiempo_duracion_periodos < 1)) {
            jAlert(TLNT.Idiomas._("El número de periodos de tiempo debe ser mayor o igual que 1"));
            return;
        }
        numero_periodos_tiempo_duracion_periodos -= 1;
        var duracion_periodos_completos = $('#duracion_periodos_completos_elemento_plantilla_informe').val();
        var modificar_desplazamiento_periodo_anterior = $('#modificar_desplazamiento_periodo_anterior_elemento_plantilla_informe').val();
        var periodo_tiempo_desplazamiento_periodo_anterior = $('#periodo_tiempo_desplazamiento_periodo_anterior_elemento_plantilla_informe').val();
        var numero_periodos_tiempo_desplazamiento_periodo_anterior = $('#numero_periodos_tiempo_desplazamiento_periodo_anterior_elemento_plantilla_informe').val();
        if ((modificar_desplazamiento_periodo_anterior == VALOR_SI) && (numero_periodos_tiempo_desplazamiento_periodo_anterior <= 0)) {
            jAlert(TLNT.Idiomas._("El número de periodos de tiempo de desplazamiento del periodo anterior debe ser mayor o igual que 1"));
            return;
        }
        var ajustar_dias_semana = $('#ajustar_dias_semana_elemento_plantilla_informe').val();
    }

    // Horario semanal y fechas
    if (ids_pestanyas_visibles.indexOf("tab-horario-semanal-fechas") > -1) {
        var horario_semanal = dame_horario_semanal_controles("elemento_plantilla_informe", false);
        if (horario_semanal.correcto == false) {
            return;
        }
        var cadena_horario_semanal = dame_cadena_horario_semanal(horario_semanal);

        var exclusion_fechas = dame_fechas_controles("exclusion_fechas_elemento_plantilla_informe");
        if (exclusion_fechas.correcto == false) {
            return;
        }
        var cadena_exclusion_fechas = dame_cadena_fechas(exclusion_fechas);
        var inclusion_fechas = dame_fechas_controles("inclusion_fechas_elemento_plantilla_informe");
        if (inclusion_fechas.correcto == false) {
            return;
        }
        var cadena_inclusion_fechas = dame_cadena_fechas(inclusion_fechas);
    }

    // Parámetros de tipo
    var parametros_tipo = "";
    var parametros_tipo_json = "";
    switch (tipo) {
        case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA: {
            // Título y subtítulo
            var titulo = $('#titulo_elemento_plantilla_informe_portada').val();
            var subtitulo = $('#subtitulo_elemento_plantilla_informe_portada').val();
            if (comprueba_longitud_cadena(titulo, NUMERO_MAXIMO_CARACTERES_TITULO) == false) {
                $("#titulo_elemento_plantilla_informe_portada").addClass('data-check-failed');
                return;
            }
            if (comprueba_longitud_cadena(subtitulo, NUMERO_MAXIMO_CARACTERES_TITULO) == false) {
                $("#subtitulo_elemento_plantilla_informe_portada").addClass('data-check-failed');
                return;
            }

            // Parámetros de tipo json
            var textos_portada = {
                "titulo": titulo,
                "subtitulo": subtitulo
            };
            parametros_tipo_json = JSON.stringify(textos_portada);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO: {
            // Título
            var titulo = $('#titulo_elemento_plantilla_informe_titulo').val();
            if (comprueba_longitud_cadena(titulo, NUMERO_MAXIMO_CARACTERES_TITULO) == false) {
                $("#titulo_elemento_plantilla_informe_titulo").addClass('data-check-failed');
                return;
            }

            // Parámetros de tipo json
            var textos_titulo = {
                "titulo": titulo
            };
            parametros_tipo_json = JSON.stringify(textos_titulo);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO: {
            // Título y texto
            var titulo = $('#titulo_elemento_plantilla_informe_texto').val();
            var texto = $('#texto_elemento_plantilla_informe_texto').val();
            if (comprueba_longitud_cadena(titulo, NUMERO_MAXIMO_CARACTERES_TITULO) == false) {
                $("#titulo_elemento_plantilla_informe_texto").addClass('data-check-failed');
                return;
            }
            if (comprueba_longitud_cadena(texto, NUMERO_MAXIMO_CARACTERES_TEXTO) == false) {
                $("#texto_elemento_plantilla_informe_texto").addClass('data-check-failed');
                return;
            }

            // Parámetros de tipo json
            var textos_texto = {
                "titulo": titulo,
                "texto": texto
            };
            parametros_tipo_json = JSON.stringify(textos_texto);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_NOTAS: {
            // Título
            var titulo = $('#titulo_elemento_plantilla_informe_notas').val();

            // Parámetros de tipo json
            var textos_notas = {
                "titulo": titulo
            };
            parametros_tipo_json = JSON.stringify(textos_notas);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN: {
            // Título y nombre de imagen
            var titulo = $('#titulo_elemento_plantilla_informe_imagen').val();
            var nombre_imagen = $('#nombre_imagen_elemento_plantilla_informe_imagen').val();
            if (comprueba_longitud_cadena(titulo, NUMERO_MAXIMO_CARACTERES_TITULO) == false) {
                $("#titulo_elemento_plantilla_informe_imagen").addClass('data-check-failed');
                return;
            }
            if (comprueba_longitud_cadena(nombre_imagen, NUMERO_MAXIMO_CARACTERES_NOMBRE) == false) {
                $("#nombre_imagen_elemento_plantilla_informe_imagen").addClass('data-check-failed');
                return;
            }

            // Altura máxima
            var altura_maxima = $('#altura_maxima_elemento_plantilla_informe_imagen').val();
            if ((parseInt(altura_maxima) < ALTURA_MAXIMA_MINIMA_ELEMENTO_PLANTILLA_INFORME_IMAGEN) ||
                (parseInt(altura_maxima) > ALTURA_MAXIMA_MAXIMA_ELEMENTO_PLANTILLA_INFORME_IMAGEN)) {
                var descripcion_error = TLNT.Idiomas._('La altura máxima es incorrecta') +
                    " (" + TLNT.Idiomas._('rango de valores') + ": " +
                    ALTURA_MAXIMA_MINIMA_ELEMENTO_PLANTILLA_INFORME_IMAGEN + " - " + ALTURA_MAXIMA_MAXIMA_ELEMENTO_PLANTILLA_INFORME_IMAGEN + ")";
                jAlert(descripcion_error);
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                altura_maxima].join(SEPARADOR_PARAMETROS_COMPUESTOS);

            // Parámetros de tipo json
            var textos_imagen = {
                "titulo": titulo,
                "nombre_imagen": nombre_imagen
            };
            parametros_tipo_json = JSON.stringify(textos_imagen);
            break;
        }
        // Elementos de varios módulos
        case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS: {
            // Visibilidad de comentarios
            var visibilidad_comentarios = $('#visibilidad_comentarios_elemento_plantilla_informe_comentarios').val();

            // Sensores
            var clase_sensor = $('#clase_sensor_elemento_plantilla_informe_comentarios').val();
            var tipo_seleccion_sensores = $('#tipo_seleccion_sensores_elemento_plantilla_informe_comentarios').val();
            var ids_sensores = [];
            $("#ids_sensores_elemento_plantilla_informe_comentarios option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_sensores.push($(this).val());
                }
            });

            // Actuadores y grupos de actuadores
            var clase_actuador = $('#clase_actuador_elemento_plantilla_informe_comentarios').val();
            var tipo_seleccion_actuadores = $('#tipo_seleccion_actuadores_elemento_plantilla_informe_comentarios').val();
            var ids_actuadores = [];
            $("#ids_actuadores_elemento_plantilla_informe_comentarios option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_actuadores.push($(this).val());
                }
            });
            var tipo_seleccion_grupos_actuadores = $('#tipo_seleccion_grupos_actuadores_elemento_plantilla_informe_comentarios').val();
            var ids_grupos_actuadores = [];
            $("#ids_grupos_actuadores_elemento_plantilla_informe_comentarios option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_grupos_actuadores.push($(this).val());
                }
            });
            if ((ids_sensores.length == 0) && (ids_actuadores.length == 0) && (ids_grupos_actuadores.length == 0)) {
                jAlert(TLNT.Idiomas._("Seleccione al menos un objeto de comentarios"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                visibilidad_comentarios,
                clase_sensor,
                tipo_seleccion_sensores,
                ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES),
                clase_actuador,
                tipo_seleccion_actuadores,
                ids_actuadores.join(SEPARADOR_PARAMETROS_SIMPLES),
                tipo_seleccion_grupos_actuadores,
                ids_grupos_actuadores.join(SEPARADOR_PARAMETROS_SIMPLES),
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        // Elementos de sensores (Eventos)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS: {
            // Clase de sensor
            var clase_sensor = $('#clase_sensor_elemento_plantilla_informe_sensores_activaciones_eventos').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }

            // Origen de evento
            var origen_evento = $('#origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos').val();
            var tipo_seleccion_origen_evento = $('#tipo_seleccion_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos').val();
            var id_origen_evento = $('#id_origen_evento_elemento_plantilla_informe_sensores_activaciones_eventos').val();

            // Granularidad de evento
            var granularidad_evento = $('#granularidad_evento_elemento_plantilla_informe_sensores_activaciones_eventos').val();

            // Campo de sensor
            var campo = $('#campo_elemento_plantilla_informe_sensores_activaciones_eventos').val();

            // Si hay gráfica de valores o de incrementos, debe ser tipo de origen sensor, con sensor y campo seleccionados
            if (((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_GRAFICA_VALORES_SENSOR) > -1) ||
                (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ACTIVACIONES_EVENTOS_GRAFICA_VALORES_ACUMULADOS_SENSOR) > -1)) &&
                ((origen_evento != ORIGEN_EVENTO_SENSOR) ||
                (id_origen_evento == ID_NINGUNO) ||
                (campo == CAMPO_NINGUNO))) {
                jAlert(TLNT.Idiomas._("No hay sensor y campo seleccionados"));
                return;
            }

            // Identificadores de eventos
            var ids_eventos = [];
            $("#ids_eventos_elemento_plantilla_informe_sensores_activaciones_eventos option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_eventos.push($(this).val());
                }
            });
            if (tipo_seleccion_origen_evento == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO) {
                if (ids_eventos.length == 0) {
                    jAlert(TLNT.Idiomas._("Seleccione al menos un evento"));
                    return;
                }
            }

            // Filtro de nombres de eventos
            var filtro_nombres_eventos = $('#filtro_nombres_eventos_elemento_plantilla_informe_sensores_activaciones_eventos').val();

            // Parámetros de tipo
            parametros_tipo = [
                clase_sensor,
                origen_evento,
                tipo_seleccion_origen_evento,
                id_origen_evento,
                granularidad_evento,
                campo,
                ids_eventos.join(SEPARADOR_PARAMETROS_SIMPLES),
                filtro_nombres_eventos,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        // Elementos de sensores (Información)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION: {
            // Ratio
            var id_ratio = $('#id_ratio_elemento_plantilla_informe_sensores_informacion').val();

            // Clase de sensor e identificador de sensor
            var clase_sensor = $('#clase_sensor_elemento_plantilla_informe_sensores_informacion').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_informacion').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_sensores_informacion').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Campo y parámetros extra de sensor
            var campo = $('#campo_elemento_plantilla_informe_sensores_informacion').val();
            var parametros_extra_campo = $('#parametros_extra_campo_elemento_plantilla_informe_sensores_informacion').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Intervalo de valores, tipo de mapa de calor y comentarios
            var intervalo_valores = $('#intervalo_valores_elemento_plantilla_informe_sensores_informacion').val();
            var tipo_mapa_calor = $('#tipo_mapa_calor_elemento_plantilla_informe_sensores_informacion').val();
            var comentarios = $('#comentarios_elemento_plantilla_informe_sensores_informacion').val();

            // Si hay mapa de calor, debe haber tipo de mapa de calor seleccionado
            var error_tipo_mapa_calor_no_seleccionado = false;
            switch (clase_sensor) {
                case CLASE_SENSOR_TEMPERATURA: {
                    if ((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_TEMPERATURA) > -1) &&
                        (tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO)) {
                        error_tipo_mapa_calor_no_seleccionado = true;
                    }
                    break;
                }
                case CLASE_SENSOR_HUMEDAD: {
                    if ((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_HUMEDAD) > -1) &&
                        (tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO)) {
                        error_tipo_mapa_calor_no_seleccionado = true;
                    }
                    break;
                }
                case CLASE_SENSOR_LUZ_INTERIOR: {
                    if (((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_LUZ) > -1) ||
                        (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_LUZ_ARTIFICIAL) > -1)) &&
                        (tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO)) {
                        error_tipo_mapa_calor_no_seleccionado = true;
                    }
                    break;
                }
                case CLASE_SENSOR_VIENTO: {
                    if (((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VELOCIDAD_VIENTO) > -1) ||
                        (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_DIRECCION_VIENTO) > -1)) &&
                        (tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO)) {
                        error_tipo_mapa_calor_no_seleccionado = true;
                    }
                    break;
                }
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                case CLASE_SENSOR_COMPRA_ENERGIA:
                case CLASE_SENSOR_GAS:
                case CLASE_SENSOR_AGUA:
                case CLASE_SENSOR_GENERICA: {
                    if ((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VALORES) > -1) &&
                        (tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO)) {
                        error_tipo_mapa_calor_no_seleccionado = true;
                    }
                    break;
                }
                case CLASE_SENSOR_CORTES_TENSION: {
                    if ((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_CORTES_TENSION) > -1) &&
                        (tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO)) {
                        error_tipo_mapa_calor_no_seleccionado = true;
                    }
                    break;
                }
            }
            if (error_tipo_mapa_calor_no_seleccionado == true) {
                jAlert(TLNT.Idiomas._("No hay tipo de mapa de calor seleccionado"));
                return;
            }

            // Si hay tabla de comentarios, debe haber comentarios en tabla seleccionado
            if ((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS) > -1) &&
                (comentarios != COMENTARIOS_GRAFICA_TABLA)) {
                jAlert(TLNT.Idiomas._("La tabla de comentarios no está seleccionada"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clase_sensor,
                campo_parametros_extra,
                tipo_seleccion_sensor,
                id_sensor,
                intervalo_valores,
                tipo_mapa_calor,
                comentarios,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        // Elementos de sensores (Análisis)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO: {
            // Ratio
            var id_ratio = $('#id_ratio_elemento_plantilla_informe_sensores_analisis_horario').val();

            // Clase de sensor e identificador de sensor
            var clase_sensor = $('#clase_sensor_elemento_plantilla_informe_sensores_analisis_horario').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_analisis_horario').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_sensores_analisis_horario').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Campo y parámetros extra de sensor
            var campo = $('#campo_elemento_plantilla_informe_sensores_analisis_horario').val();
            var parametros_extra_campo = $('#parametros_extra_campo_elemento_plantilla_informe_sensores_analisis_horario').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Tipo de mapa de calor
            var tipo_mapa_calor = $('#tipo_mapa_calor_elemento_plantilla_informe_sensores_analisis_horario').val();

            // Si hay mapa de calor, debe haber tipo de mapa de calor seleccionado
            if ((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_HORARIO_MAPA_CALOR_VALORES) > -1) &&
                (tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO)) {
                jAlert(TLNT.Idiomas._("No hay tipo de mapa de calor seleccionado"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clase_sensor,
                campo_parametros_extra,
                tipo_seleccion_sensor,
                id_sensor,
                tipo_mapa_calor,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO: {
            // Ratio
            var id_ratio = $('#id_ratio_elemento_plantilla_informe_sensores_analisis_diario').val();

            // Clase de sensor e identificador de sensor
            var clase_sensor = $('#clase_sensor_elemento_plantilla_informe_sensores_analisis_diario').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_analisis_diario').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_sensores_analisis_diario').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Campo y parámetros extra de sensor
            var campo = $('#campo_elemento_plantilla_informe_sensores_analisis_diario').val();
            var parametros_extra_campo = $('#parametros_extra_campo_elemento_plantilla_informe_sensores_analisis_diario').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Tipo de mapa de calor
            var tipo_mapa_calor = $('#tipo_mapa_calor_elemento_plantilla_informe_sensores_analisis_diario').val();

            // Si hay mapa de calor, debe haber tipo de mapa de calor seleccionado
            if ((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_DIARIO_MAPA_CALOR_VALORES) > -1) &&
                (tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO)) {
                jAlert(TLNT.Idiomas._("No hay tipo de mapa de calor seleccionado"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clase_sensor,
                campo_parametros_extra,
                tipo_seleccion_sensor,
                id_sensor,
                tipo_mapa_calor,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO: {
            // Ratio
            var id_ratio = $('#id_ratio_elemento_plantilla_informe_sensores_analisis_comportamiento').val();

            // Se comprueba si hay clase de sensor y sensores seleccionados
            var clase_sensor = $('#clase_sensor_elemento_plantilla_informe_sensores_analisis_comportamiento').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var tipo_seleccion_sensores = $('#tipo_seleccion_sensores_elemento_plantilla_informe_sensores_analisis_comportamiento').val();
            var ids_sensores = [];
            $("#ids_sensores_elemento_plantilla_informe_sensores_analisis_comportamiento option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_sensores.push($(this).val());
                }
            });
            if (ids_sensores.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
                return;
            }

            // Campo y parámetros extra de sensor
            var campo = $('#campo_elemento_plantilla_informe_sensores_analisis_comportamiento').val();
            var parametros_extra_campo = $('#parametros_extra_campo_elemento_plantilla_informe_sensores_analisis_comportamiento').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clase_sensor,
                campo_parametros_extra,
                tipo_seleccion_sensores,
                ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES),
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        // Elementos de sensores (Comparación)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS: {
            // Ratio
            var id_ratio = $('#id_ratio_elemento_plantilla_informe_sensores_comparacion_periodos').val();

            // Clase de sensor e identificador de sensor
            var clase_sensor = $('#clase_sensor_elemento_plantilla_informe_sensores_comparacion_periodos').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_comparacion_periodos').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_sensores_comparacion_periodos').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Campo y parámetros extra de sensor
            var campo = $('#campo_elemento_plantilla_informe_sensores_comparacion_periodos').val();
            var parametros_extra_campo = $('#parametros_extra_campo_elemento_plantilla_informe_sensores_comparacion_periodos').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Intervalo de valores
            var intervalo_valores = $('#intervalo_valores_elemento_plantilla_informe_sensores_comparacion_periodos').val();
            if ((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_GRAFICA_DIFERENCIAS) > -1) &&
                ((intervalo_valores == INTERVALO_VALORES_TIEMPO_REAL_PUNTOS) || (intervalo_valores == INTERVALO_VALORES_TIEMPO_REAL_LINEAS))) {
                jAlert(TLNT.Idiomas._("No hay gráfica de diferencias con intervalo de tiempo real"));
                return;
            }
            if ((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_GRAFICA_DIFERENCIAS_ACUMULADAS) > -1) &&
                ((intervalo_valores == INTERVALO_VALORES_TIEMPO_REAL_PUNTOS) || (intervalo_valores == INTERVALO_VALORES_TIEMPO_REAL_LINEAS))) {
                jAlert(TLNT.Idiomas._("No hay gráfica de diferencias acumuladas con intervalo de tiempo real"));
                return;
            }

            // Tipo de mapa de calor
            var tipo_mapa_calor = $('#tipo_mapa_calor_elemento_plantilla_informe_sensores_comparacion_periodos').val();
            if ((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_PERIODOS_MAPA_CALOR_DIFERENCIAS) > -1) &&
                (tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO)) {
                jAlert(TLNT.Idiomas._("No hay tipo de mapa de calor seleccionado"));
                return;
            }

            // Sólo hay mapa de calor de diferencias con intervalo de valores horario (o cuartohorario)
            if (((intervalo_valores != INTERVALO_VALORES_HORA) && (intervalo_valores != INTERVALO_VALORES_CUARTOHORA)) &&
                (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO)) {
                jAlert(TLNT.Idiomas._("No hay mapa de calor de diferencias con el intervalo de valores seleccionado"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clase_sensor,
                campo_parametros_extra,
                tipo_seleccion_sensor,
                id_sensor,
                intervalo_valores,
                tipo_mapa_calor,
                modificar_duracion_periodos,
                periodo_tiempo_duracion_periodos,
                iniciar_comienzo_periodo_tiempo_duracion_periodos,
                numero_periodos_tiempo_duracion_periodos,
                duracion_periodos_completos,
                modificar_desplazamiento_periodo_anterior,
                periodo_tiempo_desplazamiento_periodo_anterior,
                numero_periodos_tiempo_desplazamiento_periodo_anterior,
                ajustar_dias_semana,
                cadena_horario_semanal,
                cadena_exclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO: {
            // Clase de sensor e identificador de sensor
            var clase_sensor = $('#clase_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_sensores_comparacion_perfil_horario').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Campo y parámetros extra de sensor
            var campo = $('#campo_elemento_plantilla_informe_sensores_comparacion_perfil_horario').val();
            var parametros_extra_campo = $('#parametros_extra_campo_elemento_plantilla_informe_sensores_comparacion_perfil_horario').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Intervalo de valores
            var intervalo_valores = $('#intervalo_valores_elemento_plantilla_informe_sensores_comparacion_perfil_horario').val();
            if ((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_DIFERENCIAS) > -1) &&
                ((intervalo_valores == INTERVALO_VALORES_TIEMPO_REAL_PUNTOS) || (intervalo_valores == INTERVALO_VALORES_TIEMPO_REAL_LINEAS))) {
                jAlert(TLNT.Idiomas._("No hay gráfica de diferencias con intervalo de tiempo real"));
                return;
            }
            if ((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_GRAFICA_DIFERENCIAS_ACUMULADAS) > -1) &&
                ((intervalo_valores == INTERVALO_VALORES_TIEMPO_REAL_PUNTOS) || (intervalo_valores == INTERVALO_VALORES_TIEMPO_REAL_LINEAS))) {
                jAlert(TLNT.Idiomas._("No hay gráfica de diferencias acumuladas con intervalo de tiempo real"));
                return;
            }

            // Tipo de mapa de calor
            var tipo_mapa_calor = $('#tipo_mapa_calor_elemento_plantilla_informe_sensores_comparacion_perfil_horario').val();
            if ((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_MAPA_CALOR_DIFERENCIAS) > -1) &&
                (tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO)) {
                jAlert(TLNT.Idiomas._("No hay tipo de mapa de calor seleccionado"));
                return;
            }

            // Sólo hay mapa de calor de diferencias con intervalo de valores horario (o cuartohorario)
            if ((intervalo_valores != INTERVALO_VALORES_HORA) &&
                (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO)) {
                jAlert(TLNT.Idiomas._("No hay mapa de calor de diferencias con el intervalo de valores seleccionado"));
                return;
            }

            // Se recuperan las fechas
            var cadena_fecha_inicio_perfil_horario_local = $('#fecha_inicio_perfil_horario_elemento_plantilla_informe_sensores_comparacion_perfil_horario').val();
            var cadena_fecha_fin_perfil_horario_local = $('#fecha_fin_perfil_horario_elemento_plantilla_informe_sensores_comparacion_perfil_horario').val();
            var fechas_correctas = comprueba_fechas_inicio_fin_correctas(cadena_fecha_inicio_perfil_horario_local, null, cadena_fecha_fin_perfil_horario_local, null);
            if (fechas_correctas == false) {
                return;
            }
            // Fechas
            var cadena_fecha_inicio_perfil_horario = convierte_formato_fecha(cadena_fecha_inicio_perfil_horario_local, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);
            var cadena_fecha_fin_perfil_horario = convierte_formato_fecha(cadena_fecha_fin_perfil_horario_local, formato_fecha_local_jquery_ui, FORMATO_FECHA_BASE_DATOS_JQUERY_UI);

            // Tipo de perfil horario y agrupaciones de días de la semana
            var tipo_perfil_horario = $('#tipo_perfil_horario_elemento_plantilla_informe_sensores_comparacion_perfil_horario').val();
            var agrupaciones_dias_semana = dame_agrupaciones_dias_semana_control("agrupaciones_dias_semana_elemento_plantilla_informe_sensores_comparacion_perfil_horario");
            if (agrupaciones_dias_semana.correcto == false) {
                return;
            }
            if (tipo_perfil_horario == TIPO_PERFIL_HORARIO_CONFIGURABLE) {
                if (agrupaciones_dias_semana.agrupaciones_dias.length == 0) {
                    jAlert(TLNT.Idiomas._("No hay agrupaciones de días de la semana"));
                    return;
                }
            }
            var cadena_agrupaciones_dias_semana = dame_cadena_agrupaciones_dias_semana(agrupaciones_dias_semana);

            // Parámetros de tipo
            parametros_tipo = [
                clase_sensor,
                campo_parametros_extra,
                tipo_seleccion_sensor,
                id_sensor,
                intervalo_valores,
                tipo_mapa_calor,
                cadena_fecha_inicio_perfil_horario,
                cadena_fecha_fin_perfil_horario,
                tipo_perfil_horario,
                cadena_agrupaciones_dias_semana,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES: {
            // Ratio
            var id_ratio = $('#id_ratio_elemento_plantilla_informe_sensores_comparacion_campos_iguales').val();

            // Se comprueba si hay clase de sensor y sensores seleccionados
            var clase_sensor = $('#clase_sensor_elemento_plantilla_informe_sensores_comparacion_campos_iguales').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var tipo_seleccion_sensor_principal = $('#tipo_seleccion_sensor_principal_elemento_plantilla_informe_sensores_comparacion_campos_iguales').val();
            var id_sensor_principal = $('#id_sensor_principal_elemento_plantilla_informe_sensores_comparacion_campos_iguales').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }
            var tipo_seleccion_sensores_secundarios = $('#tipo_seleccion_sensores_secundarios_elemento_plantilla_informe_sensores_comparacion_campos_iguales').val();
            var ids_sensores_secundarios = [];
            $("#ids_sensores_secundarios_elemento_plantilla_informe_sensores_comparacion_campos_iguales option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_sensores_secundarios.push($(this).val());
                }
            });
            if (ids_sensores_secundarios.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
                return;
            }

            // No se permite que el sensor principal sea el mismo que algún sensor secundario
            for (var i = 0; i < ids_sensores_secundarios.length; i++) {
                if (id_sensor_principal == ids_sensores_secundarios[i]) {
                    jAlert(TLNT.Idiomas._("El sensor principal coincide con algún sensor secundario"));
                    return (null);
                }
            }

            // Campo y parámetros extra de sensor
            var campo = $('#campo_elemento_plantilla_informe_sensores_comparacion_campos_iguales').val();
            var parametros_extra_campo = $('#parametros_extra_campo_elemento_plantilla_informe_sensores_comparacion_campos_iguales').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Intervalo de valores
            var intervalo_valores = $('#intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_iguales').val();
            if ((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_GRAFICA_DIFERENCIAS) > -1) &&
                ((intervalo_valores == INTERVALO_VALORES_TIEMPO_REAL_PUNTOS) || (intervalo_valores == INTERVALO_VALORES_TIEMPO_REAL_LINEAS))) {
                jAlert(TLNT.Idiomas._("No hay gráfica de diferencias con intervalo de tiempo real"));
                return;
            }

            // Tipo de mapa de calor
            var tipo_mapa_calor = $('#tipo_mapa_calor_elemento_plantilla_informe_sensores_comparacion_campos_iguales').val();
            if ((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_MAPAS_CALOR_DIFERENCIAS) > -1) &&
                (tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO)) {
                jAlert(TLNT.Idiomas._("No hay tipo de mapa de calor seleccionado"));
                return;
            }

            // Sólo hay mapa de calor de diferencias con intervalo de valores horario (o cuartohorario)
            if (((intervalo_valores != INTERVALO_VALORES_HORA) && (intervalo_valores != INTERVALO_VALORES_CUARTOHORA)) &&
                (tipo_mapa_calor != TIPO_MAPA_CALOR_NINGUNO)) {
                jAlert(TLNT.Idiomas._("No hay mapa de calor de diferencias con el intervalo de valores seleccionado"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clase_sensor,
                campo_parametros_extra,
                tipo_seleccion_sensor_principal,
                id_sensor_principal,
                tipo_seleccion_sensores_secundarios,
                ids_sensores_secundarios.join(SEPARADOR_PARAMETROS_SIMPLES),
                intervalo_valores,
                tipo_mapa_calor,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES: {
            // Ratio
            var id_ratio = $('#id_ratio_elemento_plantilla_informe_sensores_comparacion_campos_diferentes').val();

            // Parámetros de sensores
            var clases_sensores = [];
            var tipos_seleccion = [];
            var ids_sensores = [];
            var campos_parametros_extra = [];
            var salir_funcion = false;
            for (var i = 0; i < NUMERO_SENSORES_COMPARACION_CAMPOS_DIFERENTES; i++) {
                var numero_sensor = i + 1;
                var id_lista_clases_sensor = "clase_sensor_" + numero_sensor + "_elemento_plantilla_informe_sensores_comparacion_campos_diferentes";
                var id_lista_tipos_seleccion = "tipo_seleccion_sensor_" + numero_sensor + "_elemento_plantilla_informe_sensores_comparacion_campos_diferentes";
                var id_lista_sensores = "id_sensor_" + numero_sensor + "_elemento_plantilla_informe_sensores_comparacion_campos_diferentes";
                var id_lista_campos = "campo_" + numero_sensor + "_elemento_plantilla_informe_sensores_comparacion_campos_diferentes";
                var id_lista_parametros_extra_campo = "parametros_extra_campo_" + numero_sensor + "_elemento_plantilla_informe_sensores_comparacion_campos_diferentes";

                var clase_sensor = $('#' + id_lista_clases_sensor).val();
                if (clase_sensor != CLASE_NINGUNA) {
                    clases_sensores.push(clase_sensor);

                    // Tipo de selección de sensor
                    var tipo_seleccion = $('#' + id_lista_tipos_seleccion).val();
                    tipos_seleccion.push(tipo_seleccion);

                    // Identificador de sensor
                    var id_sensor = $('#' + id_lista_sensores).val();
                    if (id_sensor == ID_NINGUNO) {
                        jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                        salir_funcion = true;
                        return;
                    }
                    ids_sensores.push(id_sensor);

                    // Campo y parámetros extra
                    var campo = $('#' + id_lista_campos).val();
                    var parametros_extra_campo = $('#' + id_lista_parametros_extra_campo).val();
                    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
                    if (parametros_extra_campo_correctos == false) {
                        salir_funcion = true;
                        return;
                    }
                    var campo_parametros_extra = campo;
                    if (parametros_extra_campo != "") {
                        campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
                    }
                    campos_parametros_extra.push(campo_parametros_extra);
                };
            }
            if (salir_funcion == true) {
                return;
            }
            if (clases_sensores.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
                return;
            }

            // Intervalo de valores y unificar escalas
            var intervalo_valores = $('#intervalo_valores_elemento_plantilla_informe_sensores_comparacion_campos_diferentes').val();
            var unificar_escalas = $('#unificar_escalas_elemento_plantilla_informe_sensores_comparacion_campos_diferentes').val();

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clases_sensores.join(SEPARADOR_PARAMETROS_SIMPLES),
                campos_parametros_extra.join(SEPARADOR_PARAMETROS_SIMPLES),
                tipos_seleccion.join(SEPARADOR_PARAMETROS_SIMPLES),
                ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES),
                intervalo_valores,
                unificar_escalas,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO: {
            // Ratio
            var id_ratio = $('#id_ratio_elemento_plantilla_informe_sensores_analisis_comparativo').val();

            // Se comprueba si hay clase de sensor seleccionada
            var clase_sensor = $('#clase_sensor_elemento_plantilla_informe_sensores_analisis_comparativo').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }

            // Campo y parámetros extra de sensor
            var campo = $('#campo_elemento_plantilla_informe_sensores_analisis_comparativo').val();
            var parametros_extra_campo = $('#parametros_extra_campo_elemento_plantilla_informe_sensores_analisis_comparativo').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Sensores
            var tipo_seleccion_sensores_agregados = $('#tipo_seleccion_sensores_agregados_elemento_plantilla_informe_sensores_analisis_comparativo').val();
            var ids_sensores_agregados = [];
            $("#ids_sensores_agregados_elemento_plantilla_informe_sensores_analisis_comparativo option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_sensores_agregados.push($(this).val());
                }
            });
            if (ids_sensores_agregados.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos un sensor agregado"));
                return;
            }
            var tipo_seleccion_sensor_destacado = $('#tipo_seleccion_sensor_destacado_elemento_plantilla_informe_sensores_analisis_comparativo').val();
            var id_sensor_destacado = $('#id_sensor_destacado_elemento_plantilla_informe_sensores_analisis_comparativo').val();

            // Intervalo de valores
            var intervalo_valores = $('#intervalo_valores_elemento_plantilla_informe_sensores_analisis_comparativo').val();

            // Tipo de mapa de calor
            var tipo_mapa_calor = $('#tipo_mapa_calor_elemento_plantilla_informe_sensores_analisis_comparativo').val();
            if ((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_ANALISIS_COMPARATIVO_MAPA_CALOR_DIFERENCIAS) > -1) &&
                (tipo_mapa_calor == TIPO_MAPA_CALOR_NINGUNO)) {
                jAlert(TLNT.Idiomas._("No hay tipo de mapa de calor seleccionado"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clase_sensor,
                campo_parametros_extra,
                tipo_seleccion_sensores_agregados,
                ids_sensores_agregados.join(SEPARADOR_PARAMETROS_SIMPLES),
                tipo_seleccion_sensor_destacado,
                id_sensor_destacado,
                intervalo_valores,
                tipo_mapa_calor,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES: {
            // Ratio
            var id_ratio = $('#id_ratio_elemento_plantilla_informe_sensores_valores_generales').val();

            // Se recuperan las clases de sensor y campos seleccionados
            var clases_sensor = [];
            var campos_parametros_extra = [];
            var salir_funcion = false;
            for (var i = 0; i < NUMERO_CLASES_SENSOR_VALORES_GENERALES; i++) {
                var numero_campo = i + 1;
                var id_lista_clases_sensor = "clase_sensor_" + numero_campo + "_elemento_plantilla_informe_sensores_valores_generales";
                var id_lista_campos = "campo_" + numero_campo + "_elemento_plantilla_informe_sensores_valores_generales";
                var id_lista_parametros_extra_campo = "parametros_extra_campo_" + numero_campo + "_elemento_plantilla_informe_sensores_valores_generales";

                var clase_sensor = $('#' + id_lista_clases_sensor).val();
                if (clase_sensor != CLASE_NINGUNA) {
                    clases_sensor.push(clase_sensor);

                    // Se recupera el campo y los parámetros extra
                    var campo = $('#' + id_lista_campos).val();
                    var parametros_extra_campo = $('#' + id_lista_parametros_extra_campo).val();
                    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
                    if (parametros_extra_campo_correctos == false) {
                        salir_funcion = true;
                        return;
                    }
                    var campo_parametros_extra = campo;
                    if (parametros_extra_campo != "") {
                        campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
                    }
                    campos_parametros_extra.push(campo_parametros_extra);
                }
                else {
                    // Nota: La primera clase debe estar seleccionada (es la que determina el intervalo y la agregación)
                    if (i == 0) {
                        jAlert(TLNT.Idiomas._("Debe seleccionar la primera clase de sensor"));
                        salir_funcion = true;
                        return;
                    }
                }
            }
            if (salir_funcion == true) {
                return;
            }
            if (clases_sensor.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos una clase de sensor"));
                return;
            }

            // Se comprueba si sensores seleccionados
            var tipo_seleccion_sensores = $('#tipo_seleccion_sensores_elemento_plantilla_informe_sensores_valores_generales').val();
            var ids_sensores = [];
            $("#ids_sensores_elemento_plantilla_informe_sensores_valores_generales option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_sensores.push($(this).val());
                }
            });
            if (ids_sensores.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
                return;
            }

            // Intervalo de valores y agregación
            var intervalo_valores = $('#intervalo_valores_elemento_plantilla_informe_sensores_valores_generales').val();
            var agregacion = $('#agregacion_elemento_plantilla_informe_sensores_valores_generales').val();

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clases_sensor.join(SEPARADOR_PARAMETROS_SIMPLES),
                campos_parametros_extra.join(SEPARADOR_PARAMETROS_SIMPLES),
                tipo_seleccion_sensores,
                ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES),
                intervalo_valores,
                agregacion,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES: {
            // Ratio
            var id_ratio = $('#id_ratio_elemento_plantilla_informe_sensores_incrementos_totales').val();

            // Se recuperan las clases de sensor y campos seleccionados
            var clases_sensor = [];
            var campos_parametros_extra = [];
            var salir_funcion = false;
            for (var i = 0; i < NUMERO_CLASES_SENSOR_VALORES_GENERALES; i++) {
                var numero_campo = i + 1;
                var id_lista_clases_sensor = "clase_sensor_" + numero_campo + "_elemento_plantilla_informe_sensores_incrementos_totales";
                var id_lista_campos = "campo_" + numero_campo + "_elemento_plantilla_informe_sensores_incrementos_totales";
                var id_lista_parametros_extra_campo = "parametros_extra_campo_" + numero_campo + "_elemento_plantilla_informe_sensores_incrementos_totales";

                var clase_sensor = $('#' + id_lista_clases_sensor).val();
                if (clase_sensor != CLASE_NINGUNA) {
                    clases_sensor.push(clase_sensor);

                    // Se recupera el campo y los parámetros extra
                    var campo = $('#' + id_lista_campos).val();
                    var parametros_extra_campo = $('#' + id_lista_parametros_extra_campo).val();
                    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
                    if (parametros_extra_campo_correctos == false) {
                        salir_funcion = true;
                        return;
                    }
                    var campo_parametros_extra = campo;
                    if (parametros_extra_campo != "") {
                        campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
                    }
                    campos_parametros_extra.push(campo_parametros_extra);
                }
                else {
                    // Nota: La primera clase debe estar seleccionada (es la que determina el intervalo y la agregación)
                    if (i == 0) {
                        jAlert(TLNT.Idiomas._("Debe seleccionar la primera clase de sensor"));
                        salir_funcion = true;
                        return;
                    }
                }
            }
            if (salir_funcion == true) {
                return;
            }
            if (clases_sensor.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos una clase de sensor"));
                return;
            }

            // Se comprueba si sensores seleccionados
            var tipo_seleccion_sensores = $('#tipo_seleccion_sensores_elemento_plantilla_informe_sensores_incrementos_totales').val();
            var ids_sensores = [];
            $("#ids_sensores_elemento_plantilla_informe_sensores_incrementos_totales option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_sensores.push($(this).val());
                }
            });
            if (ids_sensores.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
                return;
            }

            // Intervalo de valores y agregación
            var intervalo_valores = $('#intervalo_valores_elemento_plantilla_informe_sensores_incrementos_totales').val();
            var agregacion = $('#agregacion_elemento_plantilla_informe_sensores_incrementos_totales').val();

            // Si hay gráficas de incrementos (apilados), el intervalo de valores no puede ser tiempo real
            if (((elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_INCREMENTOS) > -1) ||
                (elementos_informe.indexOf(ELEMENTO_INFORME_SENSORES_INCREMENTOS_TOTALES_GRAFICA_INCREMENTOS_ACUMULADOS) > -1)) &&
                (intervalo_valores == INTERVALO_VALORES_TIEMPO_REAL)) {
                jAlert(TLNT.Idiomas._("El intervalo de valores no puede ser tiempo real"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clases_sensor.join(SEPARADOR_PARAMETROS_SIMPLES),
                campos_parametros_extra.join(SEPARADOR_PARAMETROS_SIMPLES),
                tipo_seleccion_sensores,
                ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES),
                intervalo_valores,
                agregacion,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        // Elementos de sensores (Estadística)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA: {
            // Ratio
            var id_ratio = $('#id_ratio_elemento_plantilla_informe_sensores_histograma').val();

            // Clase de sensor e identificador de sensor
            var clase_sensor = $('#clase_sensor_elemento_plantilla_informe_sensores_histograma').val();
            if (clase_sensor == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase seleccionada"));
                return;
            }
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_sensores_histograma').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_sensores_histograma').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Campo y parámetros extra de sensor
            var campo = $('#campo_elemento_plantilla_informe_sensores_histograma').val();
            var parametros_extra_campo = $('#parametros_extra_campo_elemento_plantilla_informe_sensores_histograma').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }
            var campo_parametros_extra = campo;
            if (parametros_extra_campo != "") {
                campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
            }

            // Intervalo de valores y detalle
            var intervalo_valores = $('#intervalo_valores_elemento_plantilla_informe_sensores_histograma').val();
            var detalle = $('#detalle_elemento_plantilla_informe_sensores_histograma').val();

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clase_sensor,
                campo_parametros_extra,
                tipo_seleccion_sensor,
                id_sensor,
                intervalo_valores,
                detalle,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION: {
            // Ratio
            var id_ratio = $('#id_ratio_elemento_plantilla_informe_sensores_correlacion').val();

            // Parámetros de sensores independientes
            var clases_sensores_independientes = [];
            var tipos_seleccion_independientes = [];
            var ids_sensores_independientes = [];
            var campos_parametros_extra_independientes = [];
            var salir_funcion = false;
            for (var i = 0; i < NUMERO_SENSORES_INDEPENDIENTES_CORRELACION; i++) {
                var numero_sensor = i + 1;
                var id_lista_clases_sensor = "clase_sensor_independiente_" + numero_sensor + "_elemento_plantilla_informe_sensores_correlacion";
                var id_lista_tipos_seleccion = "tipo_seleccion_sensor_independiente_" + numero_sensor + "_elemento_plantilla_informe_sensores_correlacion";
                var id_lista_sensores = "id_sensor_independiente_" + numero_sensor + "_elemento_plantilla_informe_sensores_correlacion";
                var id_lista_campos = "campo_independiente_" + numero_sensor + "_elemento_plantilla_informe_sensores_correlacion";
                var id_lista_parametros_extra_campo = "parametros_extra_campo_independiente_" + numero_sensor + "_elemento_plantilla_informe_sensores_correlacion";

                var clase_sensor = $('#' + id_lista_clases_sensor).val();
                if (clase_sensor != CLASE_NINGUNA) {
                    clases_sensores_independientes.push(clase_sensor);

                    // Tipo de selección de sensor
                    var tipo_seleccion = $('#' + id_lista_tipos_seleccion).val();
                    tipos_seleccion_independientes.push(tipo_seleccion);

                    // Identificador de sensor
                    var id_sensor = $('#' + id_lista_sensores).val();
                    if (id_sensor == ID_NINGUNO) {
                        jAlert(TLNT.Idiomas._("No hay sensor independiente seleccionado"));
                        salir_funcion = true;
                        return;
                    }
                    ids_sensores_independientes.push(id_sensor);

                    // Se recupera el campo y los parámetros extra del sensor seleccionado
                    var campo = $('#' + id_lista_campos).val();
                    var parametros_extra_campo = $('#' + id_lista_parametros_extra_campo).val();
                    var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
                    if (parametros_extra_campo_correctos == false) {
                        salir_funcion = true;
                        return;
                    }
                    var campo_parametros_extra = campo;
                    if (parametros_extra_campo != "") {
                        campo_parametros_extra += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo;
                    }
                    campos_parametros_extra_independientes.push(campo_parametros_extra);
                };
            }
            if (salir_funcion == true) {
                return;
            }
            if (clases_sensores_independientes.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos un sensor independiente"));
                return;
            }

            // Se comprueba si hay clase de sensor dependiente y sensor dependiente seleccionados
            var clase_sensor_dependiente = $('#clase_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion').val();
            if (clase_sensor_dependiente == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase dependiente seleccionada"));
                return;
            }
            var tipo_seleccion_dependiente = $('#tipo_seleccion_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion').val();
            var id_sensor_dependiente = $('#id_sensor_dependiente_elemento_plantilla_informe_sensores_correlacion').val();
            if (id_sensor_dependiente == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor dependiente seleccionado"));
                return;
            }

            // Campo y parámetros extra de sensor dependiente
            var campo_dependiente = $('#campo_dependiente_elemento_plantilla_informe_sensores_correlacion').val();
            var parametros_extra_campo_dependiente = $('#parametros_extra_campo_dependiente_elemento_plantilla_informe_sensores_correlacion').val();
            var parametros_extra_campo_dependiente_correctos = comprueba_parametros_extra_campo_clase_sensor(
                clase_sensor_dependiente,
                campo_dependiente,
                parametros_extra_campo_dependiente);
            if (parametros_extra_campo_dependiente_correctos == false) {
                return;
            }
            var campo_parametros_extra_dependiente = campo_dependiente;
            if (parametros_extra_campo_dependiente != "") {
                campo_parametros_extra_dependiente += SEPARADOR_CAMPO_PARAMETROS_EXTRA + parametros_extra_campo_dependiente;
            }

            // Intervalo de valores y función de correlación
            var intervalo_valores = $('#intervalo_valores_elemento_plantilla_informe_sensores_correlacion').val();
            var funcion_correlacion = $('#funcion_correlacion_elemento_plantilla_informe_sensores_correlacion').val();

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                clases_sensores_independientes.join(SEPARADOR_PARAMETROS_SIMPLES),
                campos_parametros_extra_independientes.join(SEPARADOR_PARAMETROS_SIMPLES),
                tipos_seleccion_independientes.join(SEPARADOR_PARAMETROS_SIMPLES),
                ids_sensores_independientes.join(SEPARADOR_PARAMETROS_SIMPLES),
                clase_sensor_dependiente,
                campo_parametros_extra_dependiente,
                tipo_seleccion_dependiente,
                id_sensor_dependiente,
                intervalo_valores,
                funcion_correlacion,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        // Elementos de actuadores (Informacion)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS: {
            // Filtro de acciones
            var clase_actuador = $('#clase_actuador_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas').val();
            if (clase_actuador == CLASE_NINGUNA) {
                jAlert(TLNT.Idiomas._("No hay clase de actuador seleccionada"));
                return;
            }
            var destino_accion = $('#destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas').val();
            var tipo_seleccion_destino_accion = $('#tipo_seleccion_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas').val();
            var id_destino_accion = $('#id_destino_accion_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas').val();
            if (id_destino_accion == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay destino seleccionado"));
                return;
            }
            var origen_acciones = $('#origen_acciones_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas').val();

            // Sensor
            var clase_sensor = $('#clase_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas').val();
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas').val();
            if ((clase_sensor != CLASE_NINGUNA) && (id_sensor == ID_NINGUNO)) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Campo y parámetros extra de sensor
            var campo = $('#campo_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas').val();
            var parametros_extra_campo = $('#parametros_extra_campo_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas').val();
            var parametros_extra_campo_correctos = comprueba_parametros_extra_campo_clase_sensor(clase_sensor, campo, parametros_extra_campo);
            if (parametros_extra_campo_correctos == false) {
                return;
            }

            // Intervalo de valores de sensor
            var intervalo_valores = $('#intervalo_valores_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas').val();
            if ((id_sensor != ID_NINGUNO) && (intervalo_valores == INTERVALO_VALORES_NINGUNO)) {
                jAlert(TLNT.Idiomas._("No hay intervalo de valores de sensor seleccionado"));
                return;
            }

            // Comentarios
            var comentarios = $('#comentarios_elemento_plantilla_informe_actuadores_informacion_acciones_enviadas').val();

            // Si hay gráfica de valores o de incrementos, debe haber sensor seleccionado
            if (((elementos_informe.indexOf(ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_GRAFICA_VALORES_SENSOR) > -1) ||
                (elementos_informe.indexOf(ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_GRAFICA_VALORES_ACUMULADOS_SENSOR) > -1)) &&
                (id_sensor == ID_NINGUNO)) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Si hay tabla de comentarios, debe haber comentarios en tabla seleccionado
            if ((elementos_informe.indexOf(ELEMENTO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TABLA_COMENTARIOS) > -1) &&
                (comentarios != COMENTARIOS_GRAFICA_TABLA)) {
                jAlert(TLNT.Idiomas._("La tabla de comentarios no está seleccionada"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                clase_actuador,
                destino_accion,
                tipo_seleccion_destino_accion,
                id_destino_accion,
                origen_acciones,
                clase_sensor,
                tipo_seleccion_sensor,
                id_sensor,
                campo,
                intervalo_valores,
                comentarios,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        // Elementos de SmartMeter (Consumos y costes)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES: {
            // Ratio
            var id_ratio = $('#id_ratio_elemento_plantilla_informe_smartmeter_consumos_costes_generales').val();

            // Medición
            var medicion = $('#medicion_elemento_plantilla_informe_smartmeter_consumos_costes_generales').val();

            // Se comprueba si hay sensores seleccionados
            var tipo_seleccion_sensores = $('#tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_generales').val();
            var ids_sensores = [];
            $("#ids_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_generales option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_sensores.push($(this).val());
                }
            });
            if (ids_sensores.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
                return;
            }

            // Intervalo de valores, agregación y comentarios
            var intervalo_valores = $('#intervalo_valores_elemento_plantilla_informe_smartmeter_consumos_costes_generales').val();
            var agregacion = $('#agregacion_elemento_plantilla_informe_smartmeter_consumos_costes_generales').val();
            var comentarios = $('#comentarios_elemento_plantilla_informe_smartmeter_consumos_costes_generales').val();

            // Si hay tabla de comentarios, debe haber comentarios en tabla seleccionado
            if ((elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TABLA_COMENTARIOS) > -1) &&
                (comentarios != COMENTARIOS_GRAFICA_TABLA)) {
                jAlert(TLNT.Idiomas._("La tabla de comentarios no está seleccionada"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                medicion,
                tipo_seleccion_sensores,
                ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES),
                intervalo_valores,
                agregacion,
                comentarios,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES: {
            // Ratio
            var id_ratio = $('#id_ratio_elemento_plantilla_informe_smartmeter_consumos_costes_totales').val();

            // Medición
            var medicion = $('#medicion_elemento_plantilla_informe_smartmeter_consumos_costes_totales').val();

            // Se comprueba si hay sensores seleccionados
            var tipo_seleccion_sensores = $('#tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_totales').val();
            var ids_sensores = [];
            $("#ids_sensores_elemento_plantilla_informe_smartmeter_consumos_costes_totales option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_sensores.push($(this).val());
                }
            });
            if (ids_sensores.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos un sensor"));
                return;
            }

            // Intervalo de valores
            var intervalo_valores = $('#intervalo_valores_elemento_plantilla_informe_smartmeter_consumos_costes_totales').val();

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                medicion,
                tipo_seleccion_sensores,
                ids_sensores.join(SEPARADOR_PARAMETROS_SIMPLES),
                intervalo_valores,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS: {
            // Ratio
            var id_ratio = $('#id_ratio_elemento_plantilla_informe_smartmeter_comparacion_periodos').val();

            // Medición
            var medicion = $('#medicion_elemento_plantilla_informe_smartmeter_comparacion_periodos').val();

            // Identificador de sensor
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_comparacion_periodos').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_smartmeter_comparacion_periodos').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Intervalo de valores
            var intervalo_valores = $('#intervalo_valores_elemento_plantilla_informe_smartmeter_comparacion_periodos').val();

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                medicion,
                tipo_seleccion_sensor,
                id_sensor,
                intervalo_valores,
                modificar_duracion_periodos,
                periodo_tiempo_duracion_periodos,
                iniciar_comienzo_periodo_tiempo_duracion_periodos,
                numero_periodos_tiempo_duracion_periodos,
                duracion_periodos_completos,
                modificar_desplazamiento_periodo_anterior,
                periodo_tiempo_desplazamiento_periodo_anterior,
                numero_periodos_tiempo_desplazamiento_periodo_anterior,
                ajustar_dias_semana,
                cadena_horario_semanal,
                cadena_exclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS: {
            // Medición
            var medicion = $('#medicion_elemento_plantilla_informe_smartmeter_simulador_tarifas').val();

            // Identificador de sensor
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_simulador_tarifas').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_smartmeter_simulador_tarifas').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Identificadores de tarifas
            var ids_tarifas = [];
            $("#ids_tarifas_elemento_plantilla_informe_smartmeter_simulador_tarifas option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_tarifas.push($(this).val());
                }
            });
            if (ids_tarifas.length == 0) {
                jAlert(TLNT.Idiomas._("Seleccione al menos una tarifa"));
                return (null);
            }

            // Parámetros de tipo
            parametros_tipo = [
                medicion,
                tipo_seleccion_sensor,
                id_sensor,
                ids_tarifas.join(SEPARADOR_PARAMETROS_SIMPLES),
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD: {
            // Ratio
            var id_ratio = $('#id_ratio_elemento_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad').val();

            // Identificador de sensor
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_smartmeter_consumos_costes_tramos_electricidad').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                id_ratio,
                tipo_seleccion_sensor,
                id_sensor,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD: {
            // Identificador de sensor
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_cortes_tension_electricidad').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_smartmeter_cortes_tension_electricidad').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                tipo_seleccion_sensor,
                id_sensor,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD: {
            // Identificador de sensor
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Granularidad
            var granularidad = $('#granularidad_elemento_plantilla_informe_smartmeter_excesos_potencia_electricidad').val();

            // Parámetros de tipo
            parametros_tipo = [
                tipo_seleccion_sensor,
                id_sensor,
                granularidad,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD: {
            // Identificador de sensor
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_smartmeter_excesos_energia_reactiva_electricidad').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                tipo_seleccion_sensor,
                id_sensor,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS: {
            // Identificador de sensor
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_excesos_caudal_gas').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_smartmeter_excesos_caudal_gas').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                tipo_seleccion_sensor,
                id_sensor,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        // Elementos de SmartMeter (Compra de energía)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA: {
            // Identificador de sensor
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_desvios_compra_energia').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_smartmeter_desvios_compra_energia').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                tipo_seleccion_sensor,
                id_sensor,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA: {
            // Identificador de sensor
            var tipo_seleccion_sensores = $('#tipo_seleccion_sensores_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }
            var id_sensor_hijo = $('#id_sensor_hijo_elemento_plantilla_informe_smartmeter_desvios_ponderados_compra_energia').val();
            if (id_sensor_hijo == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor hijo seleccionado"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                tipo_seleccion_sensores,
                id_sensor,
                id_sensor_hijo,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos,
                cadena_horario_semanal,
                cadena_exclusion_fechas,
                cadena_inclusion_fechas].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        // Elementos de SmartMeter (Facturas)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA: {
            // Medición
            var medicion = $('#medicion_elemento_plantilla_informe_smartmeter_simulador_factura').val();

            // Identificador de sensor
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_simulador_factura').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_smartmeter_simulador_factura').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Identificador de tarifa
            var id_tarifa = $('#id_tarifa_elemento_plantilla_informe_smartmeter_simulador_factura').val();

            // Sensores de reparto de costes
            var tipo_seleccion_sensores_reparto_costes = $('#tipo_seleccion_sensores_reparto_costes_elemento_plantilla_informe_smartmeter_simulador_factura').val();
            var ids_sensores_reparto_costes = [];
            $("#ids_sensores_reparto_costes_elemento_plantilla_informe_smartmeter_simulador_factura option").each(function() {
                if (typeof($(this).attr("selected")) !== "undefined") {
                    ids_sensores_reparto_costes.push($(this).val());
                }
            });

            // Si hay elementos de reparto de costes, debe de haber sensores de reparto de costes seleccionados
            if (((elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_REPARTO_COSTES) > -1) ||
                (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_GRAFICA_PORCENTAJES_REPARTO_COSTES) > -1) ||
                (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_TABLA_REPARTO_COSTES) > -1) ||
                (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_GAS_ESPANYA_GRAFICA_PORCENTAJES_REPARTO_COSTES) > -1) ||
                (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_TABLA_REPARTO_COSTES) > -1) ||
                (elementos_informe.indexOf(ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_AGUA_ESPANYA_GRAFICA_PORCENTAJES_REPARTO_COSTES) > -1)) &&
                (ids_sensores_reparto_costes.length == 0)) {
                jAlert(TLNT.Idiomas._("No hay sensores de reparto de costes seleccionados"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                medicion,
                tipo_seleccion_sensor,
                id_sensor,
                id_tarifa,
                tipo_seleccion_sensores_reparto_costes,
                ids_sensores_reparto_costes,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        // Elementos de SmartMeter (Tarifas)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION: {
            // Medición
            var medicion = $('#medicion_elemento_plantilla_informe_smartmeter_instalacion').val();

            // Identificador de sensor
            var tipo_seleccion_sensor = $('#tipo_seleccion_sensor_elemento_plantilla_informe_smartmeter_instalacion').val();
            var id_sensor = $('#id_sensor_elemento_plantilla_informe_smartmeter_instalacion').val();
            if (id_sensor == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay sensor seleccionado"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                medicion,
                tipo_seleccion_sensor,
                id_sensor].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        // Elementos de proyectos (Líneas base)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE: {
            // Línea base
            var tipo_seleccion_linea_base = $('#tipo_seleccion_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base').val();
            var id_linea_base = $('#id_linea_base_elemento_plantilla_informe_proyectos_simulador_linea_base').val();
            if (id_linea_base == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay línea base seleccionada"));
                return;
            }

            // Comentarios
            var comentarios = $('#comentarios_elemento_plantilla_informe_proyectos_simulador_linea_base').val();

            // Si hay tabla de comentarios, debe haber comentarios en tabla seleccionado
            if ((elementos_informe.indexOf(ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TABLA_COMENTARIOS) > -1) &&
                (comentarios != COMENTARIOS_GRAFICA_TABLA)) {
                jAlert(TLNT.Idiomas._("La tabla de comentarios no está seleccionada"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                tipo_seleccion_linea_base,
                id_linea_base,
                comentarios,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
        // Elementos de proyectos (Informacion)
        case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO: {
            // Proyecto
            var tipo_seleccion_proyecto = $('#tipo_seleccion_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto').val();
            var id_proyecto = $('#id_proyecto_elemento_plantilla_informe_proyectos_informacion_proyecto').val();
            if (id_proyecto == ID_NINGUNO) {
                jAlert(TLNT.Idiomas._("No hay proyecto seleccionado"));
                return;
            }

            // Parámetros de tipo
            parametros_tipo = [
                tipo_seleccion_proyecto,
                id_proyecto,
                modificar_periodo_tiempo,
                periodo_tiempo,
                iniciar_comienzo_periodo_tiempo,
                numero_periodos_tiempo,
                fecha_inicio_periodo_tiempo_base_datos].join(SEPARADOR_PARAMETROS_COMPUESTOS);
            break;
        }
    }

    // Modo de visibilidad y parámetros requeridos del elemento
    var modo_visibilidad = $('#modo_visibilidad_elemento_plantilla_informe').val();
    var parametros_requeridos = [];
    $("#parametros_requeridos_elemento_plantilla_informe option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            parametros_requeridos.push($(this).val());
        }
    });
    var cadena_parametros_requeridos = parametros_requeridos.join(SEPARADOR_PARAMETROS_SIMPLES);

    // Parámetros de la ventana
    var anyadir_elemento = $("#parametros_ventana_anyadir_modificar_elemento_plantilla_informe").attr("anyadir_elemento");
    var id_plantilla_informe = $("#parametros_ventana_anyadir_modificar_elemento_plantilla_informe").attr("id_plantilla_informe");
    var id_elemento = $("#parametros_ventana_anyadir_modificar_elemento_plantilla_informe").attr("id_elemento");

    // Se añade o modifica el elemento
    if (anyadir_elemento == true) {
        // Flag de duplicar elemento
        var id_elemento_anterior = id_elemento;
        var duplicar_elemento = (id_elemento_anterior != ID_NINGUNO);

        // Se comprueba la imagen
        switch (tipo) {
            case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN: {
                var duplicar_imagen = false;
                if ($('#fichero_imagen_elemento_plantilla_informe_imagen_text').val() == "") {
                    if (duplicar_elemento == true) {
                        // Nota: Es un duplicado y ya había imagen: no hace faltar subir un nuevo fichero de imagen,
                        // se duplicará la imagen anterior
                        duplicar_imagen = true;
                    }
                    else {
                        jAlert(TLNT.Idiomas._("Hay que seleccionar un fichero de imagen"));
                        return;
                    }
                }
                else {
                    var imagen_correcta = comprueba_imagen_correcta(
                        ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN, "fichero_imagen_elemento_plantilla_informe_imagen_file");
                    if (imagen_correcta == false) {
                        $('#fichero_imagen_elemento_plantilla_informe_imagen_text').addClass('data-check-failed');
                        $('#fichero_imagen_elemento_plantilla_informe_imagen_text').val("");
                        return;
                    }
                }
                break;
            }
        }

        // Se añade el elemento
        $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/anyade_elemento.php", {
            nombre: nombre,
            id_plantilla_informe: id_plantilla_informe,
            tipo: tipo,
            id_plantilla_informe_destino: id_plantilla_informe_destino,
            parametros_tipo: parametros_tipo,
            parametros_tipo_json: parametros_tipo_json,
            elementos_informe: cadena_elementos_informe,
            modo_visibilidad: modo_visibilidad,
            parametros_requeridos: cadena_parametros_requeridos
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Identificador de elemento añadido
            var id_elemento = resultado.id_elemento;

            // Se guarda la imagen (o se duplica la anterior si corresponde)
            switch (tipo) {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN: {
                    if (duplicar_imagen == false) {
                        var id_origen = [
                            id_plantilla_informe,
                            id_elemento].join(SEPARADOR_PARAMETROS_SIMPLES);
                        var control_fichero_imagen = $('#fichero_imagen_elemento_plantilla_informe_imagen_file')[0];
                        var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN, id_origen, control_fichero_imagen);
                        if (imagen_guardada_correcta == false) {
                            return;
                        }
                    }
                    else {
                        if (id_plantilla_informe_destino == ID_NINGUNO) {
                            id_plantilla_informe_destino = id_plantilla_informe;
                        }
                        var id_origen = [
                            id_plantilla_informe_destino,
                            id_elemento].join(SEPARADOR_PARAMETROS_SIMPLES);
                        var id_origen_anterior = [
                            id_plantilla_informe,
                            id_elemento_anterior].join(SEPARADOR_PARAMETROS_SIMPLES);
                        var imagen_duplicada_correcta = duplica_imagen_base_datos(ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN, id_origen_anterior, id_origen);
                        if (imagen_duplicada_correcta == false) {
                            return;
                        }
                    }
                    break;
                }
            }

            // Se actualiza la plantilla de informe correspondiente
            if (id_plantilla_informe_destino != ID_NINGUNO) {
                id_plantilla_informe = id_plantilla_informe_destino;
            }

            jInfo(resultado.msg);
            actualiza_tabla_elementos_plantilla_informe(id_plantilla_informe);
            switch (tipo) {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA: {
                    recarga_controles_subtitulos_portadas_informe_plantilla_informe(id_plantilla_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO: {
                    recarga_controles_titulos_informe_plantilla_informe(id_plantilla_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO: {
                    recarga_controles_textos_informe_plantilla_informe(id_plantilla_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN: {
                    recarga_controles_imagenes_informe_plantilla_informe(id_plantilla_informe);
                    break;
                }
            }
        });
    }
    else {
        // Tipo anterior
        var tipo_anterior = $("#parametros_ventana_anyadir_modificar_elemento_plantilla_informe").attr("tipo_elemento");

        // Se comprueba la imagen
        switch (tipo) {
            case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN: {
                if ($('#fichero_imagen_elemento_plantilla_informe_imagen_text').val() != "") {
                    var imagen_correcta = comprueba_imagen_correcta(
                        ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN, "fichero_imagen_elemento_plantilla_informe_imagen_file");
                    if (imagen_correcta == false) {
                        $('#fichero_imagen_elemento_plantilla_informe_imagen_text').addClass('data-check-failed');
                        $('#fichero_imagen_elemento_plantilla_informe_imagen_text').val("");
                        return;
                    }
                }
                break;
            }
        }

        // Se modifica el elemento
        $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/modifica_elemento.php", {
            id_elemento: id_elemento,
            nombre: nombre,
            id_plantilla_informe: id_plantilla_informe,
            tipo: tipo,
            parametros_tipo: parametros_tipo,
            parametros_tipo_json: parametros_tipo_json,
            elementos_informe: cadena_elementos_informe,
            modo_visibilidad: modo_visibilidad,
            parametros_requeridos: cadena_parametros_requeridos,
            tipo_anterior: tipo_anterior
        },
        function(data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Se guarda la imagen
            switch (tipo) {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN: {
                    if ($('#fichero_imagen_elemento_plantilla_informe_imagen_text').val() != "") {
                        var control_fichero_imagen = $('#fichero_imagen_elemento_plantilla_informe_imagen_file')[0];
                        var id_origen = [
                            id_plantilla_informe,
                            id_elemento].join(SEPARADOR_PARAMETROS_SIMPLES);
                        var imagen_guardada_correcta = guarda_imagen_base_datos_fichero_imagen(ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN, id_origen, control_fichero_imagen);
                        if (imagen_guardada_correcta == false) {
                            return;
                        }
                    }
                    break;
                }
            }

            // Se muestra el mensaje y se actualiza la tabla de elementos de plantilla de informe
            jInfo(resultado.msg);
            actualiza_tabla_elementos_plantilla_informe(id_plantilla_informe);

            // Recarga de los controles correspondientes
            switch (tipo) {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA: {
                    recarga_controles_subtitulos_portadas_informe_plantilla_informe(id_plantilla_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO: {
                    recarga_controles_titulos_informe_plantilla_informe(id_plantilla_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO: {
                    recarga_controles_textos_informe_plantilla_informe(id_plantilla_informe);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN: {
                    recarga_controles_imagenes_informe_plantilla_informe(id_plantilla_informe);
                    break;
                }
            }
            $('#ventana_modal').modal('hide');
        });
    }
}


function boton_personal_actualizar_tabla_elementos_plantilla_informe() {
    var params = this.id.split('__');
	var id_plantilla_informe = params[1];

	actualiza_tabla_elementos_plantilla_informe(id_plantilla_informe);
}


function actualiza_tabla_elementos_plantilla_informe(id_plantilla_informe) {
	$.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/dame_tabla_elementos.php", {
		id_plantilla_informe: id_plantilla_informe
	},
	function(data, status) {
		var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        var id_elemento_elementos = "elementos-plantilla-informe" + SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES + id_plantilla_informe;
        $('#' + id_elemento_elementos).html(resultado.html);

		// Establecimiento de eventos
		TLNT.Navegacion.establece_eventos_detalles_tablas_datos();
        TLNT.Navegacion.establece_eventos_mostrar_ocultar_detalles_tablas_datos();
	});
}


function boton_personal_mostrar_ventana_modificar_posiciones_elementos_plantilla_informe(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_plantilla_informe = params[1];

    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/muestra_ventana_modificar_posiciones_elementos.php", {
        id_plantilla_informe: id_plantilla_informe
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

        // Se modifica el estilo de la ventana modal
        $('#ventana_modal .modal-body').removeClass('mostrar-barra-desplazamiento-y');
        $('#ventana_modal .modal-body').addClass('mostrar-todos-elementos-y');

		// Eventos de ventanas modales
		TLNT.Navegacion.establece_eventos_ventanas_modales();
	});
}


// Modifica las posiciones de los elementos
function boton_modificar_posiciones_elementos_plantilla_informe() {
	// Identificador de plantilla de informes
    var params = this.id.split('__');
	var id_plantilla_informe = params[1];

    // Se recuperan los identificadores de los elementos (ordenados)
    var ids_elementos = [];
    $("#posicion_elementos option").each(function() {
        ids_elementos.push($(this).attr("id"));
    });

    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/modifica_posiciones_elementos.php", {
        id_plantilla_informe: id_plantilla_informe,
        ids_elementos: ids_elementos
    },
    function(data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        jInfo(resultado.msg);
        actualiza_tabla_elementos_plantilla_informe(id_plantilla_informe);
        $('#ventana_modal').modal('hide');
    });
}


function boton_personal_mostrar_ventana_eliminar_elementos_plantilla_informe(event) {
	TLNT.Navegacion.detiene_propagacion_evento(event);

    var params = this.id.split('__');
	var id_plantilla_informe = params[1];

    $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/muestra_ventana_eliminar_elementos.php", {
        id_plantilla_informe: id_plantilla_informe
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

        // Se modifica el estilo de la ventana modal
        $('#ventana_modal .modal-body').removeClass('mostrar-barra-desplazamiento-y');
        $('#ventana_modal .modal-body').addClass('mostrar-todos-elementos-y');

		// Eventos de ventanas modales
		TLNT.Navegacion.establece_eventos_ventanas_modales();
	});
}


// Elimina los elementos
function boton_eliminar_elementos_plantilla_informe() {
	// Identificador de plantilla de informes
    var params = this.id.split('__');
	var id_plantilla_informe = params[1];

    // Se recuperan los identificadores y los tipos de los elementos (ordenados)
    var ids_elementos = [];
    var tipos_elementos = [];
    $("#elementos option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_elementos.push($(this).attr("id"));
            tipos_elementos.push($(this).attr("tipo"));
        }
    });

    if (ids_elementos.length == 0) {
        jAlert(TLNT.Idiomas._("No hay elementos seleccionados"));
    }
    else {
        jConfirmAcceptCancelAlert(TLNT.Idiomas._("¿Está seguro de que desea eliminar los elementos?"), TLNT.Idiomas._("Pregunta"), function(res) {
            if (res == true) {
                $.post("./src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/elimina_elementos.php", {
                    id_plantilla_informe: id_plantilla_informe,
                    ids_elementos: ids_elementos,
                    tipos_elementos: tipos_elementos
                },
                function(data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }

                    jInfo(resultado.msg);
                    actualiza_tabla_elementos_plantilla_informe(id_plantilla_informe);
                    var borrados_elementos_portada = false;
                    var borrados_elementos_titulo = false;
                    var borrados_elementos_texto = false;
                    var borrados_elementos_imagen = false;
                    for (var i = 0; i < tipos_elementos.length; i++) {
                        switch (tipos_elementos[i]) {
                            case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA: {
                               borrados_elementos_portada = true;
                               break;
                            }
                            case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO: {
                               borrados_elementos_titulo = true;
                               break;
                            }
                            case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO: {
                               borrados_elementos_texto = true;
                               break;
                            }
                            case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN: {
                               borrados_elementos_imagen = true;
                               break;
                            }
                        }
                    }
                    if (borrados_elementos_portada == true) {
                        recarga_controles_subtitulos_portadas_informe_plantilla_informe(id_plantilla_informe);
                    }
                    if (borrados_elementos_titulo == true) {
                        recarga_controles_titulos_informe_plantilla_informe(id_plantilla_informe);
                    }
                    if (borrados_elementos_texto == true) {
                        recarga_controles_textos_informe_plantilla_informe(id_plantilla_informe);
                    }
                    if (borrados_elementos_imagen == true) {
                        recarga_controles_imagenes_informe_plantilla_informe(id_plantilla_informe);
                    }
                    $('#ventana_modal').modal('hide');
                });
            }
        });
    }
}