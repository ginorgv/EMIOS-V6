// Eventos de los botones (por funcionalidad)
TLNT.Navegacion.botones_secciones_smartmeter = [
    // Medición
    {	selector: '.boton_smartmeter_medicion',
		funcion: 	boton_smartmeter_medicion
	},
    // Consumos y costes
    {	selector: '#boton_smartmeter_consumos_costes_generales_ver_informe',
		funcion: 	boton_smartmeter_consumos_costes_generales_ver_informe
	},
	{	selector: '#boton_smartmeter_consumos_costes_generales_generar_pdf',
		funcion: 	boton_smartmeter_consumos_costes_generales_generar_pdf
	},
    {	selector: '#boton_smartmeter_consumos_costes_generales_anyadir_informe_automatico',
		funcion: 	boton_smartmeter_consumos_costes_generales_anyadir_informe_automatico
	},
    {	selector: '#boton_smartmeter_consumos_costes_totales_ver_informe',
		funcion: 	boton_smartmeter_consumos_costes_totales_ver_informe
	},
    {	selector: '#boton_smartmeter_consumos_costes_totales_generar_pdf',
		funcion: 	boton_smartmeter_consumos_costes_totales_generar_pdf
	},
    {	selector: '#boton_smartmeter_consumos_costes_totales_anyadir_informe_automatico',
		funcion: 	boton_smartmeter_consumos_costes_totales_anyadir_informe_automatico
	},
    {	selector: '#boton_smartmeter_consumos_costes_tramos_ver_informe',
		funcion: 	boton_smartmeter_consumos_costes_tramos_ver_informe
	},
    {	selector: '#boton_smartmeter_consumos_costes_tramos_anyadir_informe_automatico',
		funcion: 	boton_smartmeter_consumos_costes_tramos_anyadir_informe_automatico
	},
    {	selector: '#boton_smartmeter_consumos_costes_tramos_generar_pdf',
		funcion: 	boton_smartmeter_consumos_costes_tramos_generar_pdf
	},
    {	selector: '#boton_smartmeter_excesos_potencia_ver_informe',
		funcion: 	boton_smartmeter_excesos_potencia_ver_informe
	},
    {	selector: '#boton_smartmeter_excesos_potencia_generar_pdf',
		funcion: 	boton_smartmeter_excesos_potencia_generar_pdf
	},
    {	selector: '#boton_smartmeter_excesos_potencia_anyadir_informe_automatico',
		funcion: 	boton_smartmeter_excesos_potencia_anyadir_informe_automatico
	},
    {   selector: '#boton_smartmeter_excesos_energia_reactiva_ver_informe',
		funcion: 	boton_smartmeter_excesos_energia_reactiva_ver_informe
	},
    {	selector: '#boton_smartmeter_excesos_energia_reactiva_generar_pdf',
		funcion: 	boton_smartmeter_excesos_energia_reactiva_generar_pdf
	},
    {	selector: '#boton_smartmeter_excesos_energia_reactiva_anyadir_informe_automatico',
		funcion: 	boton_smartmeter_excesos_energia_reactiva_anyadir_informe_automatico
	},
    {   selector: '#boton_smartmeter_cortes_tension_ver_informe',
		funcion: 	boton_smartmeter_cortes_tension_ver_informe
	},
    {	selector: '#boton_smartmeter_cortes_tension_generar_pdf',
		funcion: 	boton_smartmeter_cortes_tension_generar_pdf
	},
    {	selector: '#boton_smartmeter_cortes_tension_anyadir_informe_automatico',
		funcion: 	boton_smartmeter_cortes_tension_anyadir_informe_automatico
	},
    {	selector: '#boton_smartmeter_excesos_caudal_ver_informe',
		funcion: 	boton_smartmeter_excesos_caudal_ver_informe
	},
    {	selector: '#boton_smartmeter_excesos_caudal_generar_pdf',
		funcion: 	boton_smartmeter_excesos_caudal_generar_pdf
	},
    {	selector: '#boton_smartmeter_excesos_caudal_anyadir_informe_automatico',
		funcion: 	boton_smartmeter_excesos_caudal_anyadir_informe_automatico
	},
	{	selector: '#boton_smartmeter_comparacion_periodos_ver_informe',
		funcion: 	boton_smartmeter_comparacion_periodos_ver_informe
	},
	{	selector: '#boton_smartmeter_comparacion_periodos_generar_pdf',
		funcion: 	boton_smartmeter_comparacion_periodos_generar_pdf
	},
    {	selector: '#boton_smartmeter_comparacion_periodos_anyadir_informe_automatico',
		funcion: 	boton_smartmeter_comparacion_periodos_anyadir_informe_automatico
	},
    {	selector: '#boton_smartmeter_simulador_tarifas_ver_informe',
		funcion: 	boton_smartmeter_simulador_tarifas_ver_informe
	},
    {	selector: '#boton_smartmeter_simulador_tarifas_generar_pdf',
		funcion: 	boton_smartmeter_simulador_tarifas_generar_pdf
	},
	{	selector: '#boton_smartmeter_mapa_consumos_costes_ver_informe',
		funcion: 	boton_smartmeter_mapa_consumos_costes_ver_informe
	},
    // Autoconsumo
    {	selector: '#boton_smartmeter_simulador_autoconsumo_ver_informe',
		funcion: 	boton_smartmeter_simulador_autoconsumo_ver_informe
	},
	{	selector: '#boton_smartmeter_simulador_autoconsumo_generar_pdf',
		funcion: 	boton_smartmeter_simulador_autoconsumo_generar_pdf
	},
    // Potencias
    {   selector: '#boton_smartmeter_optimizador_potencias_automatico_ver_informe',
        funcion:    boton_smartmeter_optimizador_potencias_automatico_ver_informe
    },
    {   selector: '#boton_smartmeter_optimizador_potencias_automatico_generar_pdf',
        funcion:    boton_smartmeter_optimizador_potencias_automatico_generar_pdf
    },
    {   selector: '#boton_smartmeter_optimizador_potencias_manual_descargar_plantilla_fichero',
        funcion:    boton_smartmeter_optimizador_potencias_manual_descargar_plantilla_fichero
    },
    {   selector: '#boton_smartmeter_optimizador_potencias_manual_ver_informe',
        funcion:    boton_smartmeter_optimizador_potencias_manual_ver_informe
    },
    {   selector: '#boton_smartmeter_optimizador_potencias_manual_generar_pdf',
        funcion:    boton_smartmeter_optimizador_potencias_manual_generar_pdf
    },
    {   selector: '#boton_smartmeter_simulador_potencias_automatico_ver_informe',
        funcion:    boton_smartmeter_simulador_potencias_automatico_ver_informe
    },
    {   selector: '#boton_smartmeter_simulador_potencias_automatico_generar_pdf',
        funcion:    boton_smartmeter_simulador_potencias_automatico_generar_pdf
    },
    {   selector: '#boton_smartmeter_simulador_potencias_manual_descargar_plantilla_fichero',
        funcion:    boton_smartmeter_simulador_potencias_manual_descargar_plantilla_fichero
    },
    {   selector: '#boton_smartmeter_simulador_potencias_manual_ver_informe',
        funcion:    boton_smartmeter_simulador_potencias_manual_ver_informe
    },
    {   selector: '#boton_smartmeter_simulador_potencias_manual_generar_pdf',
        funcion:    boton_smartmeter_simulador_potencias_manual_generar_pdf
    },
    // Energía reactiva
    {   selector: '#boton_smartmeter_simulador_bateria_condensadores_ver_informe',
		funcion: 	boton_smartmeter_simulador_bateria_condensadores_ver_informe
	},
    {	selector: '#boton_smartmeter_simulador_bateria_condensadores_generar_pdf',
		funcion: 	boton_smartmeter_simulador_bateria_condensadores_generar_pdf
	},
    // Compra de energía
    {	selector: '.boton_smartmeter_mostrar_ventana_importacion_valores_diarios_compra_energia_sensor',
		funcion: 	boton_smartmeter_mostrar_ventana_importacion_valores_diarios_compra_energia_sensor
	},
    {	selector: '.boton_smartmeter_mostrar_ventana_recalculo_valores_compra_energia_sensor',
		funcion: 	boton_smartmeter_mostrar_ventana_recalculo_valores_compra_energia_sensor
	},
    {   selector: '#boton_smartmeter_prevision_compra_energia_ver_informe',
		funcion: 	boton_smartmeter_prevision_compra_energia_ver_informe
	},
    {	selector: '#boton_smartmeter_prevision_compra_energia_generar_pdf',
		funcion: 	boton_smartmeter_prevision_compra_energia_generar_pdf
	},
    {	selector: '#boton_smartmeter_prevision_compra_energia_exportar_importar_valores_diarios',
		funcion: 	boton_smartmeter_prevision_compra_energia_exportar_importar_valores_diarios
	},
    {   selector: '#boton_smartmeter_desvios_compra_energia_ver_informe',
		funcion: 	boton_smartmeter_desvios_compra_energia_ver_informe
	},
    {	selector: '#boton_smartmeter_desvios_compra_energia_generar_pdf',
		funcion: 	boton_smartmeter_desvios_compra_energia_generar_pdf
	},
    {	selector: '#boton_smartmeter_desvios_compra_energia_anyadir_informe_automatico',
		funcion: 	boton_smartmeter_desvios_compra_energia_anyadir_informe_automatico
	},
    {   selector: '#boton_smartmeter_desvios_ponderados_compra_energia_ver_informe',
		funcion: 	boton_smartmeter_desvios_ponderados_compra_energia_ver_informe
	},
    {	selector: '#boton_smartmeter_desvios_ponderados_compra_energia_generar_pdf',
		funcion: 	boton_smartmeter_desvios_ponderados_compra_energia_generar_pdf
	},
    {	selector: '#boton_smartmeter_desvios_ponderados_compra_energia_anyadir_informe_automatico',
		funcion: 	boton_smartmeter_desvios_ponderados_compra_energia_anyadir_informe_automatico
	},
    // Caudales
    {   selector: '#boton_smartmeter_optimizador_caudales_automatico_ver_informe',
        funcion:    boton_smartmeter_optimizador_caudales_automatico_ver_informe
    },
    {   selector: '#boton_smartmeter_optimizador_caudales_automatico_generar_pdf',
        funcion:    boton_smartmeter_optimizador_caudales_automatico_generar_pdf
    },
    {   selector: '#boton_smartmeter_optimizador_caudales_manual_descargar_plantilla_fichero',
        funcion:    boton_smartmeter_optimizador_caudales_manual_descargar_plantilla_fichero
    },
    {   selector: '#boton_smartmeter_optimizador_caudales_manual_ver_informe',
        funcion:    boton_smartmeter_optimizador_caudales_manual_ver_informe
    },
    {   selector: '#boton_smartmeter_optimizador_caudales_manual_generar_pdf',
        funcion:    boton_smartmeter_optimizador_caudales_manual_generar_pdf
    },
    {   selector: '#boton_smartmeter_simulador_caudales_automatico_ver_informe',
        funcion:    boton_smartmeter_simulador_caudales_automatico_ver_informe
    },
    {   selector: '#boton_smartmeter_simulador_caudales_automatico_generar_pdf',
        funcion:    boton_smartmeter_simulador_caudales_automatico_generar_pdf
    },
    {   selector: '#boton_smartmeter_simulador_caudales_manual_descargar_plantilla_fichero',
        funcion:    boton_smartmeter_simulador_caudales_manual_descargar_plantilla_fichero
    },
    {   selector: '#boton_smartmeter_simulador_caudales_manual_ver_informe',
        funcion:    boton_smartmeter_simulador_caudales_manual_ver_informe
    },
    {   selector: '#boton_smartmeter_simulador_caudales_manual_generar_pdf',
        funcion:    boton_smartmeter_simulador_caudales_manual_generar_pdf
    },
    // Facturas
    {	selector: '#boton_smartmeter_simulador_factura_ver_informe',
		funcion: 	boton_smartmeter_simulador_factura_ver_informe
	},
    {   selector: '#boton_smartmeter_simulador_factura_generar_pdf',
        funcion:    boton_smartmeter_simulador_factura_generar_pdf
    },
    {	selector: '#boton_smartmeter_simulador_factura_anyadir_informe_automatico',
		funcion: 	boton_smartmeter_simulador_factura_anyadir_informe_automatico
	},
    {	selector: '.boton_smartmeter_mostrar_ventana_validacion_facturas',
		funcion: 	boton_smartmeter_mostrar_ventana_validacion_facturas
	},
    {	selector: '#boton_smartmeter_filtro_validaciones_facturas',
		funcion: 	boton_smartmeter_filtro_validaciones_facturas
	},
    // Informes personalizados
    {   selector: '#boton_smartmeter_resultados_mensuales_subir_fichero_boton',
		funcion: 	boton_smartmeter_resultados_mensuales_subir_fichero
	},
    {   selector: '#boton_smartmeter_estudio_general_ver_informe',
		funcion: 	boton_smartmeter_estudio_general_ver_informe
	},
    {	selector: '#boton_smartmeter_estudio_general_generar_pdf',
		funcion: 	boton_smartmeter_estudio_general_generar_pdf
	},
    {	selector: '#boton_smartmeter_estudio_general_anyadir_informe_automatico',
		funcion: 	boton_smartmeter_estudio_general_anyadir_informe_automatico
	},
    // Tarifas
    {	selector: '.boton_smartmeter_mostrar_ventana_recalculo_datos',
		funcion: 	boton_smartmeter_mostrar_ventana_recalculo_datos
	},
    {	selector: '.boton_smartmeter_mostrar_ventana_asignacion_tarifa_grupo_tarifas_sensores',
		funcion: 	boton_smartmeter_mostrar_ventana_asignacion_tarifa_grupo_tarifas_sensores
	},
    {	selector: '.boton_smartmeter_mostrar_ventana_modificar_tarifas_electricidad_Espanya',
		funcion: 	boton_smartmeter_mostrar_ventana_modificar_tarifas_electricidad_Espanya
	},
    {	selector: '.boton_smartmeter_mostrar_ventana_modificar_tarifas_electricidad_Portugal',
		  funcion: 	boton_smartmeter_mostrar_ventana_modificar_tarifas_electricidad_Portugal
	  },
    {	selector: '#boton_smartmeter_mostrar_ventana_exportacion_valores_parametros_energia_electrica_Espanya',
		funcion: 	boton_smartmeter_mostrar_ventana_exportacion_valores_parametros_energia_electrica_Espanya
	},
    {	selector: '#boton_smartmeter_mostrar_ventana_exportacion_costes_conceptos_consumo_sensor_electricidad_Espanya',
		funcion: 	boton_smartmeter_mostrar_ventana_exportacion_costes_conceptos_consumo_sensor_electricidad_Espanya
	},
    {	selector: '#boton_smartmeter_filtro_tarifas_tabla_electricidad_Espanya',
		funcion: 	boton_smartmeter_filtro_tarifas_tabla_electricidad_Espanya
	},
    {	selector: '#boton_smartmeter_actualizar_informacion_parametros_energia_electricidad_Espanya',
		funcion: 	boton_smartmeter_actualizar_informacion_parametros_energia_electricidad_Espanya
	},
    {	selector: '.boton_smartmeter_mostrar_ventana_modificar_tarifas_gas_Espanya',
		funcion: 	boton_smartmeter_mostrar_ventana_modificar_tarifas_gas_Espanya
	},
    {	selector: '#boton_smartmeter_filtro_tarifas_tabla_gas_Espanya',
		funcion: 	boton_smartmeter_filtro_tarifas_tabla_gas_Espanya
	},
    {	selector: '.boton_smartmeter_mostrar_ventana_modificar_tarifas_agua_Espanya',
		funcion: 	boton_smartmeter_mostrar_ventana_modificar_tarifas_agua_Espanya
	},
    {	selector: '#boton_smartmeter_filtro_tarifas_tabla_agua_Espanya',
		funcion: 	boton_smartmeter_filtro_tarifas_tabla_agua_Espanya
	},
    // Grupos de tarifas
    {	selector: '#boton_smartmeter_filtro_grupos_tarifas_tabla',
		funcion: 	boton_smartmeter_filtro_grupos_tarifas_tabla
	},
    // Ayuda (consumos y costes)
    {	selector: '#boton_ayuda_exclusion_fechas_smartmeter_consumos_costes_generales',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_smartmeter_consumos_costes_generales',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_exclusion_fechas_smartmeter_consumos_costes_totales',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_smartmeter_consumos_costes_totales',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_exclusion_fechas_smartmeter_consumos_costes_tramos',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_smartmeter_consumos_costes_tramos',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_exclusion_fechas_smartmeter_comparacion_periodos',
		funcion: 	boton_ayuda_fechas
	},
    // Ayuda (autoconsumo)
    {	selector: '#boton_ayuda_exclusion_fechas_smartmeter_simulador_autoconsumo',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_smartmeter_simulador_autoconsumo',
		funcion: 	boton_ayuda_fechas
	},
    // Ayuda (potencias)
    {	selector: '#boton_ayuda_exclusion_fechas_smartmeter_optimizador_potencias_automatico',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_smartmeter_optimizador_potencias_automatico',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_exclusion_fechas_smartmeter_simulador_potencias_automatico',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_smartmeter_simulador_potencias_automatico',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_smartmeter_ayuda_optimizador_potencias_manual',
		funcion: 	boton_smartmeter_ayuda_potencias_manuales
	},
    {	selector: '#boton_smartmeter_ayuda_simulador_potencias_manual',
		funcion: 	boton_smartmeter_ayuda_potencias_manuales
	},
    // Ayuda (energía reactiva)
    {	selector: '#boton_smartmeter_ayuda_simulador_bateria_condensadores',
		funcion: 	boton_smartmeter_ayuda_simulador_bateria_condensadores
	},
    // Ayuda (caudales)
    {	selector: '#boton_ayuda_exclusion_fechas_smartmeter_optimizador_caudales_automatico',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_smartmeter_optimizador_caudales_automatico',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_exclusion_fechas_smartmeter_simulador_caudales_automatico',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_smartmeter_simulador_caudales_automatico',
		funcion: 	boton_ayuda_fechas
	},
    // Ayuda (facturas)
    {	selector: '#boton_ayuda_exclusion_fechas_smartmeter_simulador_factura',
		funcion: 	boton_ayuda_fechas
	},
    // Ayuda (informes personalizados)
    {	selector: '#boton_smartmeter_ayuda_estudio_general',
		funcion: 	boton_smartmeter_ayuda_estudio_general
	}
];


TLNT.Navegacion.botones_tablas_datos_smartmeter = [
    // Tabla de tarifas
    {	selector: '.boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_electricidad_Espanya',
		  funcion: 	boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_electricidad_Espanya
	  },
    {	selector: '.boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_electricidad_Portugal',
		    funcion: 	boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_electricidad_Portugal
	  },
    {	selector: '.boton_smartmeter_actualizar_tabla_tarifas_electricidad_Espanya',
		  funcion: 	boton_smartmeter_actualizar_tabla_tarifas_electricidad_Espanya
	  },
    {	selector: '.boton_smartmeter_actualizar_tabla_tarifas_electricidad_Portugal',
        funcion: 	boton_smartmeter_actualizar_tabla_tarifas_electricidad_Portugal
    },
    {	selector: '.boton_smartmeter_eliminar_tarifa_electricidad_Espanya',
		    funcion: 	boton_smartmeter_eliminar_tarifa_electricidad_Espanya
	  },
    {	selector: '.boton_smartmeter_eliminar_tarifa_electricidad_Portugal',
		    funcion: 	boton_smartmeter_eliminar_tarifa_electricidad_Portugal
	  },
    {	selector: '.boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_gas_Espanya',
		    funcion: 	boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_gas_Espanya
	  },
    {	selector: '.boton_smartmeter_actualizar_tabla_tarifas_gas_Espanya',
		funcion: 	boton_smartmeter_actualizar_tabla_tarifas_gas_Espanya
	},
    {	selector: '.boton_smartmeter_eliminar_tarifa_gas_Espanya',
		funcion: 	boton_smartmeter_eliminar_tarifa_gas_Espanya
	},
    {	selector: '.boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_agua_Espanya',
		funcion: 	boton_smartmeter_mostrar_ventana_anyadir_modificar_tarifa_agua_Espanya
	},
    {	selector: '.boton_smartmeter_actualizar_tabla_tarifas_agua_Espanya',
		funcion: 	boton_smartmeter_actualizar_tabla_tarifas_agua_Espanya
	},
    {	selector: '.boton_smartmeter_eliminar_tarifa_agua_Espanya',
		funcion: 	boton_smartmeter_eliminar_tarifa_agua_Espanya
	},
    // Tabla de grupos de tarifas
    {	selector: '.boton_smartmeter_mostrar_ventana_anyadir_modificar_grupo_tarifas',
		funcion: 	boton_smartmeter_mostrar_ventana_anyadir_modificar_grupo_tarifas
	},
    {	selector: '.boton_smartmeter_actualizar_tabla_grupos_tarifas',
		funcion: 	boton_smartmeter_actualizar_tabla_grupos_tarifas
	},
    {	selector: '.boton_smartmeter_eliminar_grupo_tarifas',
		funcion: 	boton_smartmeter_eliminar_grupo_tarifas
	},
    // Tabla de validaciones de facturas
    {	selector: '.boton_smartmeter_eliminar_validacion_factura',
		funcion: 	boton_smartmeter_eliminar_validacion_factura
	},
    {	selector: '.boton_smartmeter_eliminar_fichero_excel',
		funcion: 	boton_smartmeter_eliminar_fichero_excel
	},
    // Acción del boton descargar fichero excel
    {   selector: '.boton_smartmeter_descargar_fichero_excel',
        funcion:    boton_smartmeter_descargar_fichero_excel
    },
    {   selector: '.boton_smartmeter_actualizar_tabla_ficheros_excel',
        funcion:    boton_smartmeter_actualizar_tabla_ficheros_excel
    }
];


TLNT.Navegacion.botones_detalles_tablas_datos_smartmeter = [
    // Periodos de cálculo de costes 'pass-pool' de tarifas
	{	selector: '.boton_smartmeter_mostrar_ventana_anyadir_modificar_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya',
		funcion: 	boton_smartmeter_mostrar_ventana_anyadir_modificar_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya
	},
    {	selector: '.boton_smartmeter_actualizar_tabla_periodos_calculo_costes_pass_pool_tarifa_electricidad_Espanya',
		funcion: 	boton_smartmeter_actualizar_tabla_periodos_calculo_costes_pass_pool_tarifa_electricidad_Espanya
	},
	{	selector: '.boton_smartmeter_eliminar_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya',
		funcion: 	boton_smartmeter_eliminar_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya
	},
    // Conceptos de coste 'pass-through' de tarifas
	{	selector: '.boton_smartmeter_mostrar_ventana_anyadir_modificar_concepto_coste_pass_through_tarifa_electricidad_Espanya',
		funcion: 	boton_smartmeter_mostrar_ventana_anyadir_modificar_concepto_coste_pass_through_tarifa_electricidad_Espanya
	},
    {	selector: '.boton_smartmeter_actualizar_tabla_conceptos_coste_pass_through_tarifa_electricidad_Espanya',
		funcion: 	boton_smartmeter_actualizar_tabla_conceptos_coste_pass_through_tarifa_electricidad_Espanya
	},
	{	selector: '.boton_smartmeter_eliminar_concepto_coste_pass_through_tarifa_electricidad_Espanya',
		funcion: 	boton_smartmeter_eliminar_concepto_coste_pass_through_tarifa_electricidad_Espanya
	},
    // Conceptos de coste 'cierre' de tarifas
	{	selector: '.boton_smartmeter_mostrar_ventana_anyadir_modificar_concepto_coste_cierre_tarifa_electricidad_Espanya',
		funcion: 	boton_smartmeter_mostrar_ventana_anyadir_modificar_concepto_coste_cierre_tarifa_electricidad_Espanya
	},
    {	selector: '.boton_smartmeter_actualizar_tabla_conceptos_coste_cierre_tarifa_electricidad_Espanya',
		funcion: 	boton_smartmeter_actualizar_tabla_conceptos_coste_cierre_tarifa_electricidad_Espanya
	},
	{	selector: '.boton_smartmeter_eliminar_concepto_coste_cierre_tarifa_electricidad_Espanya',
		funcion: 	boton_smartmeter_eliminar_concepto_coste_cierre_tarifa_electricidad_Espanya
	},
    // Conceptos adicionales de facturas de tarifas
	{	selector: '.boton_smartmeter_mostrar_ventana_anyadir_modificar_concepto_adicional_factura_tarifa',
		funcion: 	boton_smartmeter_mostrar_ventana_anyadir_modificar_concepto_adicional_factura_tarifa
	},
    {	selector: '.boton_smartmeter_actualizar_tabla_conceptos_adicionales_factura_tarifa',
		funcion: 	boton_smartmeter_actualizar_tabla_conceptos_adicionales_factura_tarifa
	},
	{	selector: '.boton_smartmeter_eliminar_concepto_adicional_factura_tarifa',
		funcion: 	boton_smartmeter_eliminar_concepto_adicional_factura_tarifa
	},
    // Facturas
    {	selector: '.boton_smartmeter_mostrar_ventana_modificar_observaciones_validacion_factura',
		funcion: 	boton_smartmeter_mostrar_ventana_modificar_observaciones_validacion_factura
	},
    // Tarifas
    {	selector: '.boton_smartmeter_refrescar_tabla_tarifa',
		funcion: 	boton_smartmeter_refrescar_tabla_tarifa
	},
    {	selector: '.boton_smartmeter_mostrar_ventana_asignacion_tarifa_sensores',
		funcion: 	boton_smartmeter_mostrar_ventana_asignacion_tarifa_sensores
	},
    // Grupos de tarifas
    {	selector: '.boton_smartmeter_refrescar_tabla_grupo_tarifas',
		funcion: 	boton_smartmeter_refrescar_tabla_grupo_tarifas
	},
    {	selector: '.boton_smartmeter_mostrar_ventana_asignacion_grupo_tarifas_sensores',
		funcion: 	boton_smartmeter_mostrar_ventana_asignacion_grupo_tarifas_sensores
	}
];


TLNT.Navegacion.botones_tablas_datos_informes_smartmeter = [
    // Comentarios
    {	selector: '.boton_mostrar_ventana_anyadir_comentarios',
		funcion: 	boton_mostrar_ventana_anyadir_comentarios
	},
    {   selector: '.boton_mostrar_ventana_anyadir_modificar_comentario',
		funcion: 	boton_mostrar_ventana_anyadir_modificar_comentario
	},
    {	selector: '.boton_eliminar_comentario',
		funcion: 	boton_eliminar_comentario
	}
];


TLNT.Navegacion.botones_ventanas_modales_smartmeter = [
    // Compra de energía
    {	selector: '.boton_smartmeter_importar_valores_diarios_compra_energia_sensor',
		  funcion: 	boton_smartmeter_importar_valores_diarios_compra_energia_sensor
	  },
    {	selector: '.boton_smartmeter_recalcular_valores_compra_energia_sensor',
		  funcion: 	boton_smartmeter_recalcular_valores_compra_energia_sensor
	   },
    // Facturas
    {	selector: '.boton_smartmeter_validar_facturas',
		funcion: 	boton_smartmeter_validar_facturas
	},
    {	selector: '.boton_smartmeter_modificar_observaciones_validacion_factura',
		funcion: 	boton_smartmeter_modificar_observaciones_validacion_factura
	},
    // Tarifas
    {	selector: '.boton_smartmeter_recalcular_datos',
		funcion: 	boton_smartmeter_recalcular_datos
	},
    {	selector: '.boton_smartmeter_asignar_tarifa_grupo_tarifas_sensores',
		funcion: 	boton_smartmeter_asignar_tarifa_grupo_tarifas_sensores
	},
    {	selector: '.boton_smartmeter_modificar_tarifas_electricidad_Espanya',
		funcion: 	boton_smartmeter_modificar_tarifas_electricidad_Espanya
	},
    {	selector: '.boton_smartmeter_anyadir_modificar_tarifa_electricidad_Espanya',
		funcion: 	boton_smartmeter_anyadir_modificar_tarifa_electricidad_Espanya
	},
    {	selector: '.boton_smartmeter_anyadir_modificar_tarifa_electricidad_Portugal',
		  funcion: 	boton_smartmeter_anyadir_modificar_tarifa_electricidad_Portugal
	  },
    {	selector: '.boton_smartmeter_exportar_valores_parametros_energia_electrica_Espanya',
		funcion: 	boton_smartmeter_exportar_valores_parametros_energia_electrica_Espanya
	},
    {	selector: '.boton_smartmeter_exportar_costes_conceptos_consumo_sensor_electricidad_Espanya',
		funcion: 	boton_smartmeter_exportar_costes_conceptos_consumo_sensor_electricidad_Espanya
	},
    {	selector: '.boton_smartmeter_modificar_tarifas_gas_Espanya',
		funcion: 	boton_smartmeter_modificar_tarifas_gas_Espanya
	},
    {	selector: '.boton_smartmeter_anyadir_modificar_tarifa_gas_Espanya',
		funcion: 	boton_smartmeter_anyadir_modificar_tarifa_gas_Espanya
	},
    {	selector: '.boton_smartmeter_modificar_tarifas_agua_Espanya',
		funcion: 	boton_smartmeter_modificar_tarifas_agua_Espanya
	},
    {	selector: '.boton_smartmeter_anyadir_modificar_tarifa_agua_Espanya',
		funcion: 	boton_smartmeter_anyadir_modificar_tarifa_agua_Espanya
	},
    // Periodos de cálculo de costes 'pass-pool' de tarifas
    {	selector: '.boton_smartmeter_anyadir_modificar_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya',
		funcion: 	boton_smartmeter_anyadir_modificar_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya
	},
    // Conceptos de coste 'pass-through' de tarifas
    {	selector: '.boton_smartmeter_anyadir_modificar_concepto_coste_pass_through_tarifa_electricidad_Espanya',
		funcion: 	boton_smartmeter_anyadir_modificar_concepto_coste_pass_through_tarifa_electricidad_Espanya
	},
    // Conceptos adicionales de facturas de tarifas
    {	selector: '.boton_smartmeter_anyadir_modificar_concepto_adicional_factura_tarifa',
		funcion: 	boton_smartmeter_anyadir_modificar_concepto_adicional_factura_tarifa
	},
    // Grupos de tarifas
    {	selector: '.boton_smartmeter_anyadir_modificar_grupo_tarifas',
		funcion: 	boton_smartmeter_anyadir_modificar_grupo_tarifas
	},
    // Ayuda (tarifas pass-through)
    {	selector: '#boton_smartmeter_ayuda_formula_precio_consumo_pass_through_tarifa_electrica',
		funcion: 	boton_smartmeter_ayuda_formula_precio_consumo_pass_through_tarifa_electrica
	},
    // Ayuda (tarifas cierre)
    {	selector: '#boton_smartmeter_ayuda_formula_precio_consumo_cierre_tarifa_electrica',
		funcion: 	boton_smartmeter_ayuda_formula_precio_consumo_cierre_tarifa_electrica
	},
    {	selector: '#boton_smartmeter_ayuda_limites_consumo_tramos_tarifa_agua',
		funcion: 	boton_smartmeter_ayuda_limites_consumo_tramos_tarifa_agua
	},
    {	selector: '#boton_smartmeter_ayuda_precios_consumo_tramos_tarifa_agua',
		funcion: 	boton_smartmeter_ayuda_precios_consumo_tramos_tarifa_agua
	},
    {	selector: '#boton_smartmeter_ayuda_limites_consumo_tramos_concepto_adicional_factura_tarifa',
		funcion: 	boton_smartmeter_ayuda_limites_consumo_tramos_concepto_adicional_factura_tarifa
	}
];


//
// Funciones de establecimiento de eventos (por funcionalidad)
//


TLNT.Navegacion.establece_eventos_secciones_smartmeter = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_secciones_smartmeter);

    establece_eventos_secciones_smartmeter_informes();
    establece_eventos_secciones_smartmeter_resultados_mensuales();
};


TLNT.Navegacion.establece_eventos_contenido_informes_smartmeter = function() {
    establece_eventos_contenido_informes_smartmeter_informes();
};


TLNT.Navegacion.establece_eventos_tablas_datos_smartmeter = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_tablas_datos_smartmeter);
};


TLNT.Navegacion.establece_eventos_detalles_tablas_datos_smartmeter = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_detalles_tablas_datos_smartmeter);
};


TLNT.Navegacion.establece_eventos_tablas_datos_informes_smartmeter = function() {
   TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_tablas_datos_informes_smartmeter);
};


TLNT.Navegacion.establece_eventos_ventanas_modales_smartmeter = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_ventanas_modales_smartmeter);

    establece_eventos_ventanas_modales_smartmeter_compra_energia_herramientas();
    establece_eventos_ventanas_modales_smartmeter_tarifas();
    establece_eventos_ventanas_modales_smartmeter_tarifas_electricas();
    establece_eventos_ventanas_modales_smartmeter_facturas_electricas();
    establece_eventos_ventanas_modales_smartmeter_tarifas_gas();
    establece_eventos_ventanas_modales_smartmeter_tarifas_agua();
    establece_eventos_ventanas_modales_smartmeter_conceptos_adicionales_factura_tarifas();
};

//
// Funciones auxiliares para establecer las acciones de los controles
//


establece_eventos_secciones_smartmeter_informes = function() {
    establece_eventos_secciones_smartmeter_informes_consumos_costes();
    establece_eventos_secciones_smartmeter_informes_autoconsumo();
    establece_eventos_secciones_smartmeter_informes_potencias();
    establece_eventos_secciones_smartmeter_informes_compra_energia();
    establece_eventos_secciones_smartmeter_informes_caudales();
    establece_eventos_secciones_smartmeter_informes_facturas();
    establece_eventos_secciones_smartmeter_informes_informes_personalizados();
};


establece_eventos_secciones_smartmeter_informes_consumos_costes = function() {
    // Desactivación de eventos anteriores
    $("#id_sensor_smartmeter_simulador_tarifas").off();

    // Habilitación de selección de ratio
    $('#pestanyas-consumos-costes-smartmeter').off('shown.bs.tab');
    $('#pestanyas-consumos-costes-smartmeter').on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var href_pestanya_activa = $('#pestanyas-consumos-costes-smartmeter .active > a').attr('href');
        var id_pestanya_activa = href_pestanya_activa.replace("#tab-", "");
        switch (id_pestanya_activa) {
            case "consumos-costes-generales":
            case "consumos-costes-totales":
            case "consumos-costes-agregados":
            case "consumos-costes-tramos":
            case "comparacion-periodos":
            case "mapa-consumos-costes": {
                $('#control_id_ratio_seleccion_localizacion_actual').show();
                break;
            }
            default: {
                $('#control_id_ratio_seleccion_localizacion_actual').hide();
                break;
            }
        }
    });

    // Mostrar lista doble para la selección de sensores
    if ($('#select_sensores_no_visible_smartmeter_consumos_costes_generales').length) {
        $('#select_sensores_no_visible_smartmeter_consumos_costes_generales').attr("id", "select_sensores_visible_smartmeter_consumos_costes_generales");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_smartmeter_consumos_costes_generales", true);
    };
    if ($('#select_sensores_no_visible_smartmeter_consumos_costes_totales').length) {
        $('#select_sensores_no_visible_smartmeter_consumos_costes_totales').attr("id", "select_sensores_visible_smartmeter_consumos_costes_totales");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_smartmeter_consumos_costes_totales", true);
    };
    if ($('#select_sensores_no_visible_smartmeter_mapa_consumos_costes').length) {
        $('#select_sensores_no_visible_smartmeter_mapa_consumos_costes').attr("id", "select_sensores_visible_smartmeter_mapa_consumos_costes");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_smartmeter_mapa_consumos_costes", true);
    };

    // Mostrar lista doble para la selección de tarifas
    if ($('#select_tarifas_no_visible_smartmeter_simulador_tarifas').length) {
        $('#select_tarifas_no_visible_smartmeter_simulador_tarifas').attr("id", "select_tarifas_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_tarifas_smartmeter_simulador_tarifas", true);
    };
};


establece_eventos_secciones_smartmeter_informes_autoconsumo = function() {
    // Desactivación de eventos anteriores
    $("#tipo_autoconsumo_smartmeter_simulador_autoconsumo").off();

    // Selección de la tarifa en el informe correspondiente
    var funcion_selecciona_tarifa_simulador_autoconsumo = function() {
        if ($('#id_tarifa_smartmeter_simulador_autoconsumo').length) {
            funcion_selecciona_tarifa("smartmeter_simulador_autoconsumo");
        }
    };
    $("#id_sensor_smartmeter_simulador_autoconsumo").show(funcion_selecciona_tarifa_simulador_autoconsumo);
    $("#id_sensor_smartmeter_simulador_autoconsumo").change(funcion_selecciona_tarifa_simulador_autoconsumo);

    // Muestra los controles dependientes del tipo de autoconsumo
    var funcion_muestra_controles_tipo_autoconsumo_simulador_autoconsumo = function() {
        var tipo_autoconsumo = $("#tipo_autoconsumo_smartmeter_simulador_autoconsumo").val();
        switch (tipo_autoconsumo) {
            case TIPO_AUTOCONSUMO_SIN_ACUMULACION: {
                // Nota: Se utiliza el 'parent' porque si no queda un hueco entre los parámetros (el 'span' padre)
                $("#control_numero_capacidad_acumulacion_smartmeter_simulador_autoconsumo").hide();
                $("#control_numero_capacidad_acumulacion_smartmeter_simulador_autoconsumo").parent().hide();
                $("#numero_capacidad_acumulacion_smartmeter_simulador_autoconsumo").removeClass('TLNT_input_mandatory');
                break;
            }
            case TIPO_AUTOCONSUMO_CON_ACUMULACION: {
                $("#control_numero_capacidad_acumulacion_smartmeter_simulador_autoconsumo").parent().show();
                $("#control_numero_capacidad_acumulacion_smartmeter_simulador_autoconsumo").show();
                $("#numero_capacidad_acumulacion_smartmeter_simulador_autoconsumo").addClass('TLNT_input_mandatory');
                break;
            }
        }
    };
    $("#tipo_autoconsumo_smartmeter_simulador_autoconsumo").show(funcion_muestra_controles_tipo_autoconsumo_simulador_autoconsumo);
    $("#tipo_autoconsumo_smartmeter_simulador_autoconsumo").change(funcion_muestra_controles_tipo_autoconsumo_simulador_autoconsumo);
};


establece_eventos_secciones_smartmeter_informes_potencias = function() {
    // Desactivación de eventos anteriores
    $("#id_sensor_smartmeter_optimizador_potencias_automatico").off();
    $("#id_sensor_smartmeter_simulador_potencias_automatico").off();
    $("#id_tarifa_smartmeter_simulador_potencias_automatico").off();
    $("#id_tarifa_smartmeter_simulador_potencias_manual").off();
    $("#fichero_smartmeter_optimizador_potencias_manual_text").off();
    $("#fichero_smartmeter_optimizador_potencias_manual_file").off();
    $("#boton_smartmeter_optimizador_potencias_manual_seleccion_fichero").off();
    $("#fichero_smartmeter_simulador_potencias_manual_text").off();
    $("#fichero_smartmeter_simulador_potencias_manual_file").off();
    $("#boton_smartmeter_simulador_potencias_manual_seleccion_fichero").off();

    // Fichero de potencias máximas en optimizador de potencias manual (selección de fichero de potencias máximas)
    $("#fichero_smartmeter_optimizador_potencias_manual_text").show(function() {
        $('#fichero_smartmeter_optimizador_potencias_manual_file').hide();
    });
    $('#fichero_smartmeter_optimizador_potencias_manual_file').change(function() {
        var fichero = $(this).val().split('\\').pop();
        $('#fichero_smartmeter_optimizador_potencias_manual_text').val(fichero);
        $("#fichero_smartmeter_optimizador_potencias_manual_oculto").attr("nombre", fichero);
    });
    $('#boton_smartmeter_optimizador_potencias_manual_seleccion_fichero').click(function() {
        $('#fichero_smartmeter_optimizador_potencias_manual_file').click();
    });

    // Fichero de potencias máximas en simulador de potencias manual (selección de fichero de potencias máximas)
    $("#fichero_smartmeter_simulador_potencias_manual_text").show(function() {
        $('#fichero_smartmeter_simulador_potencias_manual_file').hide();
    });
    $('#fichero_smartmeter_simulador_potencias_manual_file').change(function() {
        var fichero = $(this).val().split('\\').pop();
        $('#fichero_smartmeter_simulador_potencias_manual_text').val(fichero);
        $("#fichero_smartmeter_simulador_potencias_manual_oculto").attr("nombre", fichero);
    });
    $('#boton_smartmeter_simulador_potencias_manual_seleccion_fichero').click(function() {
        $('#fichero_smartmeter_simulador_potencias_manual_file').click();
    });

    // Se muestran/ocultan los controles de potencias según el número de tramos de la tarifa eléctrica seleccionada
    var funcion_muestra_controles_potencias_simulador_potencias = function(id_controles) {
        var id_tarifa = $("#id_tarifa_" + id_controles).val();
        switch (pais_tarifas_electricas) {
            case PAIS_ESPANYA: {
                if (id_tarifa == ID_NINGUNO) {
                    $("#potencias_" + id_controles).hide();
                    $("#potencias_sin_tarifa_electrica_seleccionada_" + id_controles).show();
                }
                else {
                    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/dame_info_tramos_tarifa_electricidad_Espanya.php", {
                        id_tarifa: id_tarifa
                    },
                    function (data, status) {
                        var resultado = dame_resultado_ejecucion_script_php_json(data);
                        if (resultado == null) {
                            return;
                        }

                        // Controles de potencias de tramos
                        var numero_tramos_tarifa_electrica = parseInt(resultado.numero_tramos);
                        var info_tramos_tarifa_electrica = resultado.info_tramos;
                        for (var i = 1; i <= numero_tramos_tarifa_electrica; i++) {
                            $("#etiqueta_potencia_manual_tramo_" + i + "_" + id_controles).show();
                            $("#potencia_manual_tramo_" + i + "_" + id_controles).show();
                            $("#potencia_manual_tramo_" + i + "_" + id_controles).val(info_tramos_tarifa_electrica[i]["potencia"]);
                        }
                        for (var i = numero_tramos_tarifa_electrica + 1; i <= NUMERO_MAXIMO_TRAMOS_TARIFA_ELECTRICA; i++) {
                            $("#etiqueta_potencia_manual_tramo_" + i + "_" + id_controles).hide();
                            $("#potencia_manual_tramo_" + i + "_" + id_controles).hide();
                            $("#potencia_manual_tramo_" + i + "_" + id_controles).val(0);
                        }

                        // Texto de que no hay tarifa eléctrica seleccionada
                        if (numero_tramos_tarifa_electrica == 0) {
                            $("#potencias_" + id_controles).hide();
                            $("#potencias_sin_tarifa_electrica_seleccionada_" + id_controles).show();
                        }
                        else {
                            $("#potencias_" + id_controles).show();
                            $("#potencias_sin_tarifa_electrica_seleccionada_" + id_controles).hide();
                        }
                    });
                }
                break;
            }
        }
    };
    var funcion_muestra_controles_potencias_simulador_potencias_automatico = function() {
        funcion_muestra_controles_potencias_simulador_potencias("smartmeter_simulador_potencias_automatico");
    };
    $("#id_tarifa_smartmeter_simulador_potencias_automatico").show(funcion_muestra_controles_potencias_simulador_potencias_automatico);
    $("#id_tarifa_smartmeter_simulador_potencias_automatico").change(funcion_muestra_controles_potencias_simulador_potencias_automatico);
    var funcion_muestra_controles_potencias_simulador_potencias_manual = function() {
        funcion_muestra_controles_potencias_simulador_potencias("smartmeter_simulador_potencias_manual");
    };
    $("#id_tarifa_smartmeter_simulador_potencias_manual").show(funcion_muestra_controles_potencias_simulador_potencias_manual);
    $("#id_tarifa_smartmeter_simulador_potencias_manual").change(funcion_muestra_controles_potencias_simulador_potencias_manual);

    // Selección de la tarifa en el informe correspondiente
    var funcion_selecciona_tarifa_optimizador_potencias_automatico = function() {
        funcion_selecciona_tarifa("smartmeter_optimizador_potencias_automatico");
    };
    $("#id_sensor_smartmeter_optimizador_potencias_automatico").show(funcion_selecciona_tarifa_optimizador_potencias_automatico);
    $("#id_sensor_smartmeter_optimizador_potencias_automatico").change(funcion_selecciona_tarifa_optimizador_potencias_automatico);
    var funcion_selecciona_tarifa_simulador_potencias_automatico = function() {
        funcion_selecciona_tarifa("smartmeter_simulador_potencias_automatico");
    };
    $("#id_sensor_smartmeter_simulador_potencias_automatico").show(funcion_selecciona_tarifa_simulador_potencias_automatico);
    $("#id_sensor_smartmeter_simulador_potencias_automatico").change(funcion_selecciona_tarifa_simulador_potencias_automatico);
};


establece_eventos_secciones_smartmeter_informes_compra_energia = function() {
    // Desactivación de eventos anteriores
    $("#tipo_perfil_horario_smartmeter_prevision_compra_energia").off();
    $("#id_sensor_smartmeter_desvios_ponderados_compra_energia").off();

    // Habilitación de agrupaciones de días en previsión de compra de energía
    var funcion_habilita_agrupaciones_dias_prevision_compra_energia = function() {
        var tipo_perfil_horario = $("#tipo_perfil_horario_smartmeter_prevision_compra_energia").val();
        switch (tipo_perfil_horario) {
            case TIPO_PERFIL_HORARIO_CONFIGURABLE: {
                $("#cadena_agrupaciones_dias_semana_smartmeter_prevision_compra_energia").val("1-2-3-4-5, 6-7");
                $("#control_cadena_agrupaciones_dias_semana_smartmeter_prevision_compra_energia").show();
                break;
            }
            default: {
                $("#cadena_agrupaciones_dias_semana_smartmeter_prevision_compra_energia").val("");
                $("#control_cadena_agrupaciones_dias_semana_smartmeter_prevision_compra_energia").hide();
                break;
            }
        }
    };
    $("#tipo_perfil_horario_smartmeter_prevision_compra_energia").show(funcion_habilita_agrupaciones_dias_prevision_compra_energia);
    $("#tipo_perfil_horario_smartmeter_prevision_compra_energia").change(funcion_habilita_agrupaciones_dias_prevision_compra_energia);

    // Actualiza la lista de sensores hijos del informe de desvíos ponderados de compra de energía
    var funcion_actualiza_lista_sensores_hijos_desvios_ponderados_compra_energia = function() {
        var id_sensor = $("#id_sensor_smartmeter_desvios_ponderados_compra_energia").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores_hijos.php", {
            clase_sensor: CLASE_SENSOR_COMPRA_ENERGIA,
            id_sensor_padre: id_sensor,
            opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_NINGUNO
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_sensor_hijo_smartmeter_desvios_ponderados_compra_energia").html(resultado.html);
            $("#id_sensor_hijo_smartmeter_desvios_ponderados_compra_energia").trigger("chosen:updated");

            // Se deshabilita si sólo hay un valor para elegir
            if ($("select#id_sensor_hijo_smartmeter_desvios_ponderados_compra_energia option").length <= 1) {
                $("#id_sensor_hijo_smartmeter_desvios_ponderados_compra_energia").attr('disabled', true).trigger("chosen:updated");
            }
            else {
                $("#id_sensor_hijo_smartmeter_desvios_ponderados_compra_energia").removeAttr('disabled').trigger("chosen:updated");
            }
        });
    };
    $("#id_sensor_smartmeter_desvios_ponderados_compra_energia").change(funcion_actualiza_lista_sensores_hijos_desvios_ponderados_compra_energia);
};


establece_eventos_secciones_smartmeter_informes_caudales = function() {
    // Desactivación de eventos anteriores
    $("#id_sensor_smartmeter_optimizador_caudales_automatico").off();
    $("#id_sensor_smartmeter_simulador_caudales_automatico").off();
    $("#id_tarifa_smartmeter_simulador_caudales_automatico").off();
    $("#id_tarifa_smartmeter_simulador_caudales_manual").off();
    $("#fichero_smartmeter_optimizador_caudales_manual_text").off();
    $("#fichero_smartmeter_optimizador_caudales_manual_file").off();
    $("#boton_smartmeter_optimizador_caudales_manual_seleccion_fichero").off();
    $("#fichero_smartmeter_simulador_caudales_manual_text").off();
    $("#fichero_smartmeter_simulador_caudales_manual_file").off();
    $("#boton_smartmeter_simulador_caudales_manual_seleccion_fichero").off();

    // Fichero de caudales máximas en optimizador de caudales manual (selección de fichero de caudales máximos)
    $("#fichero_smartmeter_optimizador_caudales_manual_text").show(function() {
        $('#fichero_smartmeter_optimizador_caudales_manual_file').hide();
    });
    $('#fichero_smartmeter_optimizador_caudales_manual_file').change(function() {
        var fichero = $(this).val().split('\\').pop();
        $('#fichero_smartmeter_optimizador_caudales_manual_text').val(fichero);
        $("#fichero_smartmeter_optimizador_caudales_manual_oculto").attr("nombre", fichero);
    });
    $('#boton_smartmeter_optimizador_caudales_manual_seleccion_fichero').click(function() {
        $('#fichero_smartmeter_optimizador_caudales_manual_file').click();
    });

    // Fichero de caudales máximas en simulador de caudales manual (selección de fichero de caudales máximos)
    $("#fichero_smartmeter_simulador_caudales_manual_text").show(function() {
        $('#fichero_smartmeter_simulador_caudales_manual_file').hide();
    });
    $('#fichero_smartmeter_simulador_caudales_manual_file').change(function() {
        var fichero = $(this).val().split('\\').pop();
        $('#fichero_smartmeter_simulador_caudales_manual_text').val(fichero);
        $("#fichero_smartmeter_simulador_caudales_manual_oculto").attr("nombre", fichero);
    });
    $('#boton_smartmeter_simulador_caudales_manual_seleccion_fichero').click(function() {
        $('#fichero_smartmeter_simulador_caudales_manual_file').click();
    });

    // Se muestran/ocultan los controles de caudales según la tarifa de gas seleccionada
    var funcion_muestra_controles_caudales_simulador_caudales = function(id_controles) {
        var id_tarifa = $("#id_tarifa_" + id_controles).val();
        if (id_tarifa == ID_NINGUNO) {
            $("#caudales_" + id_controles).hide();
            $("#caudales_sin_tarifa_gas_seleccionada_" + id_controles).show();
        }
        else {
            switch (pais_tarifas_gas) {
                case PAIS_ESPANYA: {
                    $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/dame_info_tarifa_gas_Espanya.php", {
                        id_tarifa: id_tarifa
                    },
                    function (data, status) {
                        var resultado = dame_resultado_ejecucion_script_php_json(data);
                        if (resultado == null) {
                            return;
                        }

                        // Controles de caudales diarios
                        var caudal_diario = parseInt(resultado.caudal_diario_tarifa_gas);
                        $("#caudal_manual_" + id_controles).val(caudal_diario);

                        // Texto de que no hay tarifa de gas seleccionada
                        if (id_tarifa == ID_NINGUNO) {
                            $("#caudales_" + id_controles).hide();
                            $("#caudales_sin_tarifa_gas_seleccionada_" + id_controles).show();
                        }
                        else {
                            $("#caudales_" + id_controles).show();
                            $("#caudales_sin_tarifa_gas_seleccionada_" + id_controles).hide();
                        }
                    });
                    break;
                }
            }
        }
    };
    var funcion_muestra_controles_caudales_simulador_caudales_automatico = function() {
        funcion_muestra_controles_caudales_simulador_caudales("smartmeter_simulador_caudales_automatico");
    };
    $("#id_tarifa_smartmeter_simulador_caudales_automatico").show(funcion_muestra_controles_caudales_simulador_caudales_automatico);
    $("#id_tarifa_smartmeter_simulador_caudales_automatico").change(funcion_muestra_controles_caudales_simulador_caudales_automatico);
    var funcion_muestra_controles_caudales_simulador_caudales_manual = function() {
        funcion_muestra_controles_caudales_simulador_caudales("smartmeter_simulador_caudales_manual");
    };
    $("#id_tarifa_smartmeter_simulador_caudales_manual").show(funcion_muestra_controles_caudales_simulador_caudales_manual);
    $("#id_tarifa_smartmeter_simulador_caudales_manual").change(funcion_muestra_controles_caudales_simulador_caudales_manual);

    // Selección de la tarifa en el informe correspondiente
    var funcion_selecciona_tarifa_optimizador_caudales_automatico = function() {
        funcion_selecciona_tarifa("smartmeter_optimizador_caudales_automatico");
    };
    $("#id_sensor_smartmeter_optimizador_caudales_automatico").show(funcion_selecciona_tarifa_optimizador_caudales_automatico);
    $("#id_sensor_smartmeter_optimizador_caudales_automatico").change(funcion_selecciona_tarifa_optimizador_caudales_automatico);
    var funcion_selecciona_tarifa_simulador_caudales_automatico = function() {
        funcion_selecciona_tarifa("smartmeter_simulador_caudales_automatico");
    };
    $("#id_sensor_smartmeter_simulador_caudales_automatico").show(funcion_selecciona_tarifa_simulador_caudales_automatico);
    $("#id_sensor_smartmeter_simulador_caudales_automatico").change(funcion_selecciona_tarifa_simulador_caudales_automatico);
};


establece_eventos_secciones_smartmeter_informes_facturas = function() {
    // Desactivación de eventos anteriores
    $("#id_sensor_smartmeter_simulador_factura").off();

    // Mostrar lista doble para la selección de sensores de reparto de costes
    if ($('#select_sensores_no_visible_reparto_costes_smartmeter_simulador_factura').length) {
        $('#select_sensores_no_visible_reparto_costes_smartmeter_simulador_factura').attr("id", "select_sensores_visible_reparto_costes_smartmeter_simulador_factura");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_reparto_costes_smartmeter_simulador_factura", true);
    };

    // Tarifa a 'actual' si se modifica el sensor
    $('#id_sensor_smartmeter_simulador_factura').change(function() {
        $("#id_tarifa_smartmeter_simulador_factura").val(ID_NINGUNO);
        $("#id_tarifa_smartmeter_simulador_factura").trigger("chosen:updated");
    });
};


establece_eventos_secciones_smartmeter_informes_informes_personalizados = function() {
    // Desactivación de eventos anteriores
    $("#texto-introduccion-estudio-general").off();

    // Habilitación de selección de ratio
    $('#pestanyas-informes-personalizados-smartmeter').off('shown.bs.tab');
    $('#pestanyas-informes-personalizados-smartmeter').on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var href_pestanya_activa = $('#pestanyas-informes-personalizados-smartmeter .active > a').attr('href');
        var id_pestanya_activa = href_pestanya_activa.replace("#tab-", "");
        switch (id_pestanya_activa) {
            case "estudio-general": {
                $('#control_id_ratio_seleccion_localizacion_actual').show();
                break;
            }
            default: {
                $('#control_id_ratio_seleccion_localizacion_actual').hide();
                break;
            }
        }
    });

    // Mostrar lista doble para la selección de apartados del informe personalizado
    if ($('#select_apartados_no_visible_smartmeter_estudio_general').length) {
        $('#select_apartados_no_visible_smartmeter_estudio_general').attr("id", "select_apartados_visible_smartmeter_estudio_general");
        TLNT.Navegacion.convierte_lista_doble("ids_apartados_smartmeter_estudio_general", false);
    };

    // Texto por defecto y ajuste de tamaño de textos de estudio general
    switch (medicion) {
        case MEDICION_ELECTRICIDAD: {
            switch (pais_tarifas_electricas) {
                case PAIS_ESPANYA: {
                    $("#texto-introduccion-estudio-general").val(INTRODUCCION_DEFECTO_INFORME_ESTUDIO_GENERAL_ELECTRICIDAD_ESPANYA);
                    break;
                }
            }
            break;
        }
        case MEDICION_GAS: {
            switch (pais_tarifas_gas) {
                case PAIS_ESPANYA: {
                    $("#texto-introduccion-estudio-general").val(INTRODUCCION_DEFECTO_INFORME_ESTUDIO_GENERAL_GAS_ESPANYA);
                    break;
                }
            }
            break;
        }
        case MEDICION_AGUA: {
            switch (pais_tarifas_agua) {
                case PAIS_ESPANYA: {
                    $("#texto-introduccion-estudio-general").val(INTRODUCCION_DEFECTO_INFORME_ESTUDIO_GENERAL_AGUA_ESPANYA);
                    break;
                }
            }
            break;
        }
    }
    $("#texto-introduccion-estudio-general").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
    $("#texto-introduccion-estudio-general").trigger("input");

    // Ajuste de textos
    TLNT.Navegacion.redimensiona_textarea(".area-texto-informe");

    // Resultados mensuales
    $('#boton_smartmeter_resultados_mensuales_subir_fichero').off();
    $('#boton_smartmeter_resultados_mensuales_subir_fichero').click();


};


establece_eventos_contenido_informes_smartmeter_informes = function() {
    establece_eventos_contenido_informes_smartmeter_informes_informes_personalizados();
};


establece_eventos_contenido_informes_smartmeter_informes_informes_personalizados = function() {
    $("#area-texto-informe").off();

    // Contador de caracteres de areas de texto
    $(".area-texto-informe").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
};


establece_eventos_ventanas_modales_smartmeter_compra_energia_herramientas = function() {
    // Desactivación de eventos anteriores
    $('#fichero_importacion_valores_diarios_compra_energia_sensor_text').off();
    $('#fichero_importacion_valores_diarios_compra_energia_sensor_file').off();

    // Ventana de importación de valores diarios de compra de energía (selección de fichero de valores)
    $("#fichero_importacion_valores_diarios_compra_energia_sensor_text").show(function() {
        $('#fichero_importacion_valores_diarios_compra_energia_sensor_file').hide();
    });
    $('#fichero_importacion_valores_diarios_compra_energia_sensor_file').change(function() {
        var fichero = $(this).val().split('\\').pop();
        $('#fichero_importacion_valores_diarios_compra_energia_sensor_text').val(fichero);
    });
    $('#boton_importacion_valores_diarios_compra_energia_sensor_seleccionar_fichero').click(function() {
        $('#fichero_importacion_valores_diarios_compra_energia_sensor_file').click();
    });
};


establece_eventos_ventanas_modales_smartmeter_tarifas = function() {
    // Desactivación de eventos anteriores
    $("#descripcion_grupo_tarifas").off();
    $("#id_grupo_tarifa").off();
    $("#expiracion_tarifa").off();
    $("#id_tarifa_asignacion_tarifa_grupo_tarifas_sensores").off();
    $("#id_grupo_tarifas_asignacion_tarifa_grupo_tarifas_sensores").off();

    // Contador de caracteres de descripción de grupo de tarifas
    $("#descripcion_grupo_tarifas").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);

    // Mostrar lista doble para los sensores
    if ($('#select_sensores_asignacion_tarifa_grupo_tarifas_sensores_no_visible').length) {
        $('#select_sensores_asignacion_tarifa_grupo_tarifas_sensores_no_visible').attr("id", "select_sensores_asignacion_tarifa_grupo_tarifas_sensores_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_asignacion_tarifa_grupo_tarifas_sensores", true);
    }

    // Mostrar lista doble para las tarifas y los grupos de tarifas
    if ($('#select_tarifas_recalculo_datos_no_visible').length) {
        $('#select_tarifas_recalculo_datos_no_visible').attr("id", "select_tarifas_recalculo_datos_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_tarifas_recalculo_datos", true);
    }
    if ($('#select_grupos_tarifas_recalculo_datos_no_visible').length) {
        $('#select_grupos_tarifas_recalculo_datos_no_visible').attr("id", "select_grupos_tarifas_recalculo_datos_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_grupos_tarifas_recalculo_datos", true);
    }

    // Habilita y muestra los controles dependientes del grupo de la tarifa
    var funcion_habilita_muestra_controles_grupo_tarifa = function() {
        var id_grupo_tarifa = $("#id_grupo_tarifa").val();
        switch (id_grupo_tarifa) {
            case ID_NINGUNO.toString(): {
                $("#expiracion_tarifa").prop('disabled', false);
                break;
            }
            default: {
                $("#expiracion_tarifa").val(EXPIRACION_TARIFA_SI);
                $("#expiracion_tarifa").trigger('change');
                $("#expiracion_tarifa").prop('disabled', 'disabled');
                break;
            }
        }
    };
    $("#id_grupo_tarifa").show(funcion_habilita_muestra_controles_grupo_tarifa);
    $("#id_grupo_tarifa").change(funcion_habilita_muestra_controles_grupo_tarifa);

    // Habilita y muestra los controles dependientes de la expiración de tarifa
    var funcion_habilita_muestra_controles_expiracion_tarifa = function() {
        var expiracion_tarifa = $("#expiracion_tarifa").val();
        switch (expiracion_tarifa) {
            case EXPIRACION_TARIFA_SI: {
                $("#control_fecha_expiracion_tarifa").show();
                $("#control_numero_dias_preaviso_expiracion_tarifa").show();
                $("#control_numero_dias_preaviso_expiracion_tarifa").addClass('TLNT_input_mandatory');
                break;
            }
            case EXPIRACION_TARIFA_NINGUNO:
            case EXPIRACION_TARIFA_NO: {
                $("#control_fecha_expiracion_tarifa").hide();
                $("#control_numero_dias_preaviso_expiracion_tarifa").hide();
                $("#control_numero_dias_preaviso_expiracion_tarifa").removeClass('TLNT_input_mandatory');
                break;
            }
        }
    };
    $("#expiracion_tarifa").show(funcion_habilita_muestra_controles_expiracion_tarifa);
    $("#expiracion_tarifa").change(funcion_habilita_muestra_controles_expiracion_tarifa);

    // Realiza acciones al cambiar la tarifa en la asignación de tarifa o grupo a sensores
    var funcion_realiza_acciones_tarifa_asignacion_tarifa_grupo_tarifas_sensores = function() {
        var id_tarifa = $("#id_tarifa_asignacion_tarifa_grupo_tarifas_sensores").val();
        if (id_tarifa != ID_NINGUNO.toString()) {
            $("#id_grupo_tarifas_asignacion_tarifa_grupo_tarifas_sensores").val(ID_NINGUNO).trigger("chosen:updated");
        }
    };
    $("#id_tarifa_asignacion_tarifa_grupo_tarifas_sensores").change(funcion_realiza_acciones_tarifa_asignacion_tarifa_grupo_tarifas_sensores);

    // Realiza acciones al cambiar el grupo de tarifas en la asignación de tarifa o grupo a sensores
    var funcion_realiza_acciones_grupo_tarifas_asignacion_tarifa_grupo_tarifas_sensores = function() {
        var id_grupo_tarifas = $("#id_grupo_tarifas_asignacion_tarifa_grupo_tarifas_sensores").val();
        if (id_grupo_tarifas != ID_NINGUNO.toString()) {
            $("#id_tarifa_asignacion_tarifa_grupo_tarifas_sensores").val(ID_NINGUNO).trigger("chosen:updated");
        }
    };
    $("#id_grupo_tarifas_asignacion_tarifa_grupo_tarifas_sensores").change(funcion_realiza_acciones_grupo_tarifas_asignacion_tarifa_grupo_tarifas_sensores);
};


establece_eventos_ventanas_modales_smartmeter_tarifas_electricas = function() {
    // Desactivación de eventos anteriores
    $("#descripcion_tarifa_electrica").off();
    $("#tipo_tarifa_electrica").off();
    $("#contrato_tarifa_electrica").off();
    $("#tipo_calculo_coste_pass_pool_tarifa_electrica").off();
    $("#formula_precio_consumo_pass_through_tarifa_electrica").off();
    $("#tipo_medida_tarifa_electrica").off();

    // Contador de caracteres de descripción de tarifa eléctrica
    $("#descripcion_tarifa_electrica").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);

    // Mostrar lista doble para las tarifas
    if ($('#select_tarifas_electricas_tarifa_electrica_no_visible').length) {
        $('#select_tarifas_electricas_tarifa_electrica_no_visible').attr("id", "select_tarifas_electricas_tarifa_electrica_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_tarifas_electricas_tarifa_electrica", true);
    }

    // Recarga la lista de tarifa eléctricas de la ventana de modificación
    var funcion_recarga_lista_tarifas_electricas_modificacion_tarifas_electricas = function() {
        var tipo_tarifa_electrica = $("#tipo_tarifa_electrica").val();
        var contrato_tarifa_electrica = $("#contrato_tarifa_electrica").val();

        switch (pais_tarifas_electricas) {
            case PAIS_ESPANYA: {
                $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/dame_lista_tarifas_tipo_contrato_electricidad_Espanya.php", {
                    tipo: tipo_tarifa_electrica,
                    contrato: contrato_tarifa_electrica
                },
                function (data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }

                    // Recarga de lista doble
                    // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
                    $("#ids_tarifas_electricas_tarifa_electrica").multiselect2side('destroy');
                    $("#ids_tarifas_electricas_tarifa_electrica").html(resultado.html);
                    TLNT.Navegacion.convierte_lista_doble("ids_tarifas_electricas_tarifa_electrica", true);
                });
                break;
            }
        }
    };

    // Habilita y muestra los controles dependientes del contrato de la tarifa eléctrica
    var funcion_habilita_muestra_controles_contrato_tarifa_electrica = function() {
        var tipo_tarifa_electrica = $("#tipo_tarifa_electrica").val();
        var contrato_tarifa_electrica = $("#contrato_tarifa_electrica").val();

        switch (pais_tarifas_electricas) {
            case PAIS_ESPANYA: {
                switch (tipo_tarifa_electrica) {
                    case TIPO_TARIFA_NINGUNO:
                    case TIPO_TARIFA_TODOS: {
                        $("#titulo-tab-contrato-fijo").hide();
                        $("#titulo-tab-contrato-pass-pool").hide();
                        $("#titulo-tab-contrato-pass-through").hide();
                        $("#titulo-tab-contrato-cierre").hide();
                        break;
                    }
                    default: {
                        switch (contrato_tarifa_electrica) {
                            case CONTRATO_TARIFA_ELECTRICA_NINGUNO:
                            case CONTRATO_TARIFA_ELECTRICA_TODOS: {
                                $("#titulo-tab-contrato-fijo").hide();
                                $("#titulo-tab-contrato-pass-pool").hide();
                                $("#titulo-tab-contrato-pass-through").hide();
                                $("#titulo-tab-contrato-cierre").hide();
                                break;
                            }
                            case CONTRATO_TARIFA_ELECTRICA_ESPANYA_FIJO: {
                                $("#titulo-tab-contrato-fijo").show();
                                $("#titulo-tab-contrato-pass-pool").hide();
                                $("#titulo-tab-contrato-pass-through").hide();
                                $("#titulo-tab-contrato-cierre").hide();
                                break;
                            }
                            case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL: {
                                $("#titulo-tab-contrato-fijo").hide();
                                $("#titulo-tab-contrato-pass-pool").show();
                                $("#titulo-tab-contrato-pass-through").hide();
                                $("#titulo-tab-contrato-cierre").hide();
                                break;
                            }
                            case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH: {
                                $("#titulo-tab-contrato-fijo").hide();
                                $("#titulo-tab-contrato-pass-pool").hide();
                                $("#titulo-tab-contrato-pass-through").show();
                                $("#titulo-tab-contrato-cierre").hide();
                                break;
                            }
                            case CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE: {
                                $("#titulo-tab-contrato-fijo").hide();
                                $("#titulo-tab-contrato-pass-pool").hide();
                                $("#titulo-tab-contrato-pass-through").hide();
                                $("#titulo-tab-contrato-cierre").show();
                                break;
                            }
                        }
                        break;
                    }
                }
                break;
            }
        }
    };
    $("#contrato_tarifa_electrica").show(funcion_habilita_muestra_controles_contrato_tarifa_electrica);

    // Habilita y muestra los controles dependientes del tipo de la tarifa eléctrica
    var funcion_habilita_muestra_controles_tipo_tarifa_electrica = function() {
        var tipo_tarifa_electrica = $("#tipo_tarifa_electrica").val();
        var tipo_administracion_tarifas_electricas = $("#tabs-administracion-tarifa-electrica").attr("tipo-administracion");

        switch (pais_tarifas_electricas) {
            case PAIS_ESPANYA: {
                // Información de tipo de tarifa eléctrico
                var caracteristicas_tipo_tarifa_electrica = dame_caracteristicas_tipo_tarifa_electrica_Espanya(tipo_tarifa_electrica);
                var numero_tramos = caracteristicas_tipo_tarifa_electrica["numero_tramos"];
                var tipo_calculo_coste_potencias = caracteristicas_tipo_tarifa_electrica["tipo_calculo_coste_potencias"];
                var parametros_medida_datos_facturacion = caracteristicas_tipo_tarifa_electrica["parametros_medida_datos_facturacion"];
                var tipo_tarifa_canarias = caracteristicas_tipo_tarifa_electrica["tipo_tarifa_canarias"];

                // Pestaña de precios de consumo de tarifa de acceso
                if (numero_tramos > 0) {
                    $("#titulo-tab-precios-consumo-tarifa-acceso-tramos").show();
                    switch (tipo_administracion_tarifas_electricas) {
                        case TIPO_ADMINISTRACION_TARIFAS_UNICA: {
                            for (var i = 1; i <= numero_tramos; i++) {
                                $("#precio_consumo_tarifa_acceso_tramo_tarifa_electrica__" + i).addClass('TLNT_input_mandatory');
                            }
                            break;
                        }
                        case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE: {
                            $("#precio_consumo_tarifa_acceso_tramo_tarifa_electrica__" + i).removeClass('TLNT_input_mandatory');
                            break;
                        }
                    }
                }
                else {
                    $("#titulo-tab-precios-consumo-tarifa-acceso-tramos").hide();
                    for (var i = 1; i <= numero_tramos; i++) {
                        $("#precio_consumo_tarifa_acceso_tramo_tarifa_electrica__" + i).removeClass('TLNT_input_mandatory');
                    }
                }

                // Pestaña de excesos de potencia de máximos mensuales
                switch (tipo_calculo_coste_potencias) {
                    case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES: {
                        $("#titulo-tab-excesos-potencia-maximos-mensuales").show();
                        break;
                    }
                    default: {
                        $("#titulo-tab-excesos-potencia-maximos-mensuales").hide();
                        break;
                    }
                }

                // Pestaña de medida de datos de facturación
                if (parametros_medida_datos_facturacion == true) {
                    $("#titulo-tab-medida-datos-facturacion").show();
                }
                else {
                    $("#titulo-tab-medida-datos-facturacion").hide();
                }

                // Pestaña de factura
                if (tipo_tarifa_electrica != TIPO_TARIFA_NINGUNO) {
                    $("#titulo-tab-factura").show();
                    switch (tipo_administracion_tarifas_electricas) {
                        case TIPO_ADMINISTRACION_TARIFAS_UNICA: {
                            $("#impuesto_electrico_tarifa_electrica").addClass('TLNT_input_mandatory');
                            $("#alquiler_contador_tarifa_electrica").addClass('TLNT_input_mandatory');
                            if (tipo_tarifa_canarias == false) {
                                $("#iva_tarifa_electrica").addClass('TLNT_input_mandatory');
                                $("#igic_reducido_tarifa_electrica").removeClass('TLNT_input_mandatory');
                                $("#igic_normal_tarifa_electrica").removeClass('TLNT_input_mandatory');
                            }
                            else {
                                $("#iva_tarifa_electrica").removeClass('TLNT_input_mandatory');
                                $("#igic_reducido_tarifa_electrica").addClass('TLNT_input_mandatory');
                                $("#igic_normal_tarifa_electrica").addClass('TLNT_input_mandatory');
                            }
                            break;
                        }
                        case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE: {
                            $("#impuesto_electrico_tarifa_electrica").removeClass('TLNT_input_mandatory');
                            $("#alquiler_contador_tarifa_electrica").removeClass('TLNT_input_mandatory');
                            $("#iva_tarifa_electrica").removeClass('TLNT_input_mandatory');
                            $("#igic_reducido_tarifa_electrica").removeClass('TLNT_input_mandatory');
                            $("#igic_normal_tarifa_electrica").removeClass('TLNT_input_mandatory');
                            break;
                        }
                    }
                    if (tipo_tarifa_electrica != TIPO_TARIFA_TODOS) {
                        if (tipo_tarifa_canarias == false) {
                            $("#control_iva_tarifa_electrica").show();
                            $("#control_igic_reducido_tarifa_electrica").hide();
                            $("#control_igic_normal_tarifa_electrica").hide();
                        }
                        else {
                            $("#control_iva_tarifa_electrica").hide();
                            $("#control_igic_reducido_tarifa_electrica").show();
                            $("#control_igic_normal_tarifa_electrica").show();
                        }
                    }
                    else {
                        $("#control_iva_tarifa_electrica").show();
                        $("#control_igic_reducido_tarifa_electrica").show();
                        $("#control_igic_normal_tarifa_electrica").show();
                    }
                }
                else {
                    $("#titulo-tab-factura").hide();
                    $("#impuesto_electrico_tarifa_electrica").removeClass('TLNT_input_mandatory');
                    $("#alquiler_contador_tarifa_electrica").removeClass('TLNT_input_mandatory');
                    $("#iva_tarifa_electrica").removeClass('TLNT_input_mandatory');
                    $("#igic_reducido_tarifa_electrica").removeClass('TLNT_input_mandatory');
                    $("#igic_normal_tarifa_electrica").removeClass('TLNT_input_mandatory');
                }

                // Se muestran u ocultan las pestañas restantes
                funcion_habilita_muestra_controles_contrato_tarifa_electrica();
                switch (tipo_tarifa_electrica) {
                    case TIPO_TARIFA_NINGUNO:
                    case TIPO_TARIFA_TODOS: {
                        $("#titulo-tab-precios-potencias-tramos").hide();
                        $("#titulo-tab-potencias-tramos").hide();
                        break;
                    }
                    default: {
                        $("#titulo-tab-precios-potencias-tramos").show();
                        $("#titulo-tab-potencias-tramos").show();
                        break;
                    }
                }
                break;
            }
        }
    };
    $("#tipo_tarifa_electrica").show(funcion_habilita_muestra_controles_tipo_tarifa_electrica);

    // Realiza acciones al cambiar el tipo de tarifa eléctrica
    // - Recarga los controles de cada una de las pestañas
    // - Recarga la lista de tarifas (si es necesario)
    var funcion_realiza_acciones_tipo_tarifa_electrica_modificada = function() {
        var tipo_tarifa_electrica = $("#tipo_tarifa_electrica").val();
        var tipo_administracion_tarifas_electricas = $("#tabs-administracion-tarifa-electrica").attr("tipo-administracion");
        var id_tarifa = $("#parametros_ventana_anyadir_modificar_tarifa_electrica").attr("id_tarifa_electrica");

        switch (pais_tarifas_electricas) {
            case PAIS_ESPANYA: {
                $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/dame_controles_tramos_tipo_tarifa_electricidad_Espanya.php", {
                    tipo: tipo_tarifa_electrica,
                    tipo_administracion: tipo_administracion_tarifas_electricas,
                    id_tarifa : id_tarifa
                },
                function (data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }

                    $("#tab-contrato-fijo").html(resultado.html.controles_precios_consumo);
                    $("#controles-coeficientes-precio-consumo-pass-pool-tramos-tarifa-electrica").html(resultado.html.controles_coeficientes_precio_consumo_pass_pool);
                    $("#tab-precios-consumo-tarifa-acceso-tramos").html(resultado.html.controles_precios_consumo_tarifa_acceso);
                    $("#tab-precios-potencias-tramos").html(resultado.html.controles_precios_potencias);
                    $("#tab-potencias-tramos").html(resultado.html.controles_potencias);

                    // Se habilitan los controles del tipo de tarifa eléctrica
                    funcion_habilita_muestra_controles_tipo_tarifa_electrica();

                    // Se recargan las tarifas eléctricas (si es necesario)
                    if (tipo_administracion_tarifas_electricas == TIPO_ADMINISTRACION_TARIFAS_MULTIPLE) {
                        funcion_recarga_lista_tarifas_electricas_modificacion_tarifas_electricas();
                    }
                });
                break;
            }
        }
    };
    $("#tipo_tarifa_electrica").change(funcion_realiza_acciones_tipo_tarifa_electrica_modificada);

    // Realiza acciones al cambiar el contrato de tarifa eléctrica
    // - Habilita las pestañas correspondientes
    // - Recarga la lista de tarifas (si es necesario)
    var funcion_realiza_acciones_contrato_tarifa_electrica_modificada = function() {
        var tipo_administracion_tarifas_electricas = $("#tabs-administracion-tarifa-electrica").attr("tipo-administracion");

        // Habilita las pestañas correspondientes
        funcion_habilita_muestra_controles_contrato_tarifa_electrica();

        // Se recargan las tarifas eléctricas (si es necesario)
        if (tipo_administracion_tarifas_electricas == TIPO_ADMINISTRACION_TARIFAS_MULTIPLE) {
            funcion_recarga_lista_tarifas_electricas_modificacion_tarifas_electricas();
        }
    };
    $("#contrato_tarifa_electrica").change(funcion_realiza_acciones_contrato_tarifa_electrica_modificada);

    // Habilita y muestra los controles dependientes del tipo de cálculo de costes de 'pass-pool' de tarifa eléctrica
    var funcion_habilita_muestra_controles_tipo_calculo_coste_pass_pool_tarifa_electrica = function() {
        var contrato_tarifa_electrica = $("#contrato_tarifa_electrica").val();
        if (contrato_tarifa_electrica == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL) {
            tipo_calculo_coste_pass_pool_tarifa_electrica = $("#tipo_calculo_coste_pass_pool_tarifa_electrica").val();
            switch (pais_tarifas_electricas) {
                case PAIS_ESPANYA: {
                    switch (tipo_calculo_coste_pass_pool_tarifa_electrica) {
                        case TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_AUTOMATICO: {
                            $("#control_dia_calculo_coste_automatico_pass_pool_tarifa_electrica").show();
                            $("#dia_calculo_coste_automatico_pass_pool_tarifa_electrica").addClass('TLNT_input_mandatory');
                            break;
                        }
                        case TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_NINGUNO:
                        case TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_MANUAL: {
                            $("#control_dia_calculo_coste_automatico_pass_pool_tarifa_electrica").hide();
                            $("#dia_calculo_coste_automatico_pass_pool_tarifa_electrica").removeClass('TLNT_input_mandatory');
                            break;
                        }
                    }
                    break;
                }
            }
        }
    };
    $("#tipo_calculo_coste_pass_pool_tarifa_electrica").show(funcion_habilita_muestra_controles_tipo_calculo_coste_pass_pool_tarifa_electrica);
    $("#tipo_calculo_coste_pass_pool_tarifa_electrica").change(funcion_habilita_muestra_controles_tipo_calculo_coste_pass_pool_tarifa_electrica);
    $("#tipo_calculo_coste_cierre_tarifa_electrica").show(funcion_habilita_muestra_controles_tipo_calculo_coste_pass_pool_tarifa_electrica);
    $("#tipo_calculo_coste_cierre_tarifa_electrica").change(funcion_habilita_muestra_controles_tipo_calculo_coste_pass_pool_tarifa_electrica);

    // Contador de caracteres de fórmula de precio de consumo
    $("#formula_precio_consumo_pass_through_tarifa_electrica").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);

    // Habilita y muestra los controles dependientes del tipo de medida de la tarifa eléctrica
    var funcion_habilita_muestra_controles_tipo_medida_tarifa_electrica = function() {
        var tipo_medida_tarifa_electrica = $("#tipo_medida_tarifa_electrica").val();

        switch (pais_tarifas_electricas) {
            case PAIS_ESPANYA: {
                switch (tipo_medida_tarifa_electrica) {
                    case TIPO_MEDIDA_TARIFA_ELECTRICA_BAJA_TENSION: {
                        $("#control_potencia_nominal_transformador_tarifa_electrica").show();
                        $("#potencia_nominal_transformador_tarifa_electrica").addClass('TLNT_input_mandatory');
                        break;
                    }
                    default: {
                        $("#control_potencia_nominal_transformador_tarifa_electrica").hide();
                        $("#potencia_nominal_transformador_tarifa_electrica").removeClass('TLNT_input_mandatory');
                        break;
                    }
                }
                break;
            }
        }
    };
    $("#tipo_medida_tarifa_electrica").show(funcion_habilita_muestra_controles_tipo_medida_tarifa_electrica);
    $("#tipo_medida_tarifa_electrica").change(funcion_habilita_muestra_controles_tipo_medida_tarifa_electrica);
};


establece_eventos_ventanas_modales_smartmeter_facturas_electricas = function() {
    // Desactivación de eventos anteriores
    $('#ficheros_validacion_facturas_text').off();
    $('#ficheros_validacion_facturas_files').off();
    $('#boton_validacion_facturas_seleccionar_ficheros').off();
    $('#observaciones_validacion_factura').off();

    // Ventana de validación de facturas (selección de ficheros de facturas)
    $("#ficheros_validacion_facturas_text").show(function() {
        $('#ficheros_validacion_facturas_files').hide();
    });
    $('#ficheros_validacion_facturas_files').change(function() {
        var nombres_ficheros = "";
        var numero_ficheros = $(this).get(0).files.length;
        for (var i = 0; i < numero_ficheros; i++) {
            var nombre_fichero = $(this).get(0).files.item(i).name;
            if (i > 0) {
                nombres_ficheros += "\n";
            }
            nombres_ficheros += nombre_fichero;
        }
        $('#ficheros_validacion_facturas_text').val(nombres_ficheros);
    });
    $('#boton_validacion_facturas_seleccionar_ficheros').click(function() {
        $('#ficheros_validacion_facturas_files').click();
    });

    // Contador de caracteres de observaciones de la validación de factura
    $("#observaciones_validacion_factura").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
};


establece_eventos_ventanas_modales_smartmeter_tarifas_gas = function() {
    // Desactivación de eventos anteriores
    $("#descripcion_tarifa_gas").off();
    $("#tipo_tarifa_gas").off();

    // Contador de caracteres de descripción de tarifa eléctrica
    $("#descripcion_tarifa_gas").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);

    // Mostrar lista doble para las tarifas
    if ($('#select_tarifas_gas_tarifa_gas_no_visible').length) {
        $('#select_tarifas_gas_tarifa_gas_no_visible').attr("id", "select_tarifas_gas_tarifa_gas_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_tarifas_gas_tarifa_gas", true);
    }

    // Recarga la lista de tarifa de gas de la ventana de modificación
    var funcion_recarga_lista_tarifas_gas_modificacion_tarifas_gas = function() {
        var tipo_tarifa_gas = $("#tipo_tarifa_gas").val();

        switch (pais_tarifas_gas) {
            case PAIS_ESPANYA: {
                $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/dame_lista_tarifas_tipo_gas_Espanya.php", {
                    tipo: tipo_tarifa_gas,
                },
                function (data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }

                    // Recarga de lista doble
                    // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
                    $("#ids_tarifas_gas_tarifa_gas").multiselect2side('destroy');
                    $("#ids_tarifas_gas_tarifa_gas").html(resultado.html);
                    TLNT.Navegacion.convierte_lista_doble("ids_tarifas_gas_tarifa_gas", true);
                });
                break;
            }
        }
    };

    // Habilita y muestra los controles dependientes del tipo de la tarifa de gas
    var funcion_habilita_muestra_controles_tipo_tarifa_gas = function() {
        var tipo_tarifa_gas = $("#tipo_tarifa_gas").val();
        var tipo_administracion_tarifas_gas = $("#tabs-administracion-tarifa-gas").attr("tipo-administracion");

        switch (pais_tarifas_gas) {
            case PAIS_ESPANYA: {
                $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/dame_caracteristicas_tipo_tarifa_gas_Espanya.php", {
                    tipo: tipo_tarifa_gas
                },
                function (data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }

                    var tipo_calculo_coste_termino_fijo = resultado.tipo_calculo_coste_termino_fijo;

                    if (tipo_tarifa_gas != TIPO_TARIFA_NINGUNO) {
                        $("#titulo-tab-factura").show();
                        switch (tipo_administracion_tarifas_gas) {
                            case TIPO_ADMINISTRACION_TARIFAS_UNICA: {
                                $("#impuesto_gas_tarifa_gas").addClass('TLNT_input_mandatory');
                                $("#alquiler_contador_tarifa_gas").addClass('TLNT_input_mandatory');
                                $("#iva_tarifa_gas").addClass('TLNT_input_mandatory');
                                break;
                            }
                            case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE: {
                                $("#impuesto_gas_tarifa_gas").removeClass('TLNT_input_mandatory');
                                $("#alquiler_contador_tarifa_gas").removeClass('TLNT_input_mandatory');
                                $("#iva_tarifa_gas").removeClass('TLNT_input_mandatory');
                                break;
                            }
                        }
                    }
                    else {
                        $("#titulo-tab-factura").hide();
                        $("#impuesto_gas_tarifa_gas").removeClass('TLNT_input_mandatory');
                        $("#alquiler_contador_tarifa_gas").removeClass('TLNT_input_mandatory');
                        $("#iva_tarifa_gas").removeClass('TLNT_input_mandatory');
                    }

                    switch (tipo_tarifa_gas) {
                        case TIPO_TARIFA_NINGUNO:
                        case TIPO_TARIFA_TODOS: {
                            $("#titulo-tab-parametros").hide();
                            $("#factor_conversion_tarifa_gas").removeClass('TLNT_input_mandatory');
                            $("#precio_consumo_tarifa_gas").removeClass('TLNT_input_mandatory');
                            $("#precio_caudal_diario_tarifa_gas").removeClass('TLNT_input_mandatory');
                            $("#caudal_diario_tarifa_gas").removeClass('TLNT_input_mandatory');
                            $("#precio_termino_fijo_diario_tarifa_gas").removeClass('TLNT_input_mandatory');
                            break;
                        }
                        default: {
                            $("#titulo-tab-parametros").show();
                            switch (tipo_administracion_tarifas_gas) {
                                case TIPO_ADMINISTRACION_TARIFAS_UNICA: {
                                    $("#factor_conversion_tarifa_gas").addClass('TLNT_input_mandatory');
                                    $("#precio_consumo_tarifa_gas").addClass('TLNT_input_mandatory');
                                    switch (tipo_calculo_coste_termino_fijo) {
                                        case TIPO_CALCULO_COSTE_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES: {
                                            $("#control_precio_caudal_diario_tarifa_gas").show();
                                            $("#precio_caudal_diario_tarifa_gas").addClass('TLNT_input_mandatory');
                                            $("#control_caudal_diario_tarifa_gas").show();
                                            $("#caudal_diario_tarifa_gas").addClass('TLNT_input_mandatory');
                                            $("#control_precio_termino_fijo_diario_tarifa_gas").hide();
                                            $("#precio_termino_fijo_diario_tarifa_gas").removeClass('TLNT_input_mandatory');
                                            $("#control_capacidad_contratada_gas").hide();
                                            $("#control_termino_fijo_gas").hide();
                                            $("#control_termino_fijo_gas_por_cliente").hide();
                                            $("#control_termino_variable_gas").hide();
                                            $("#control_excesos_demanda_gas").hide();
                                            break;
                                        }
                                        case TIPO_CALCULO_COSTE_TERMINO_FIJO_SIN_EXCESOS: {
                                            $("#control_precio_caudal_diario_tarifa_gas").hide();
                                            $("#precio_caudal_diario_tarifa_gas").removeClass('TLNT_input_mandatory');
                                            $("#control_caudal_diario_tarifa_gas").hide();
                                            $("#caudal_diario_tarifa_gas").removeClass('TLNT_input_mandatory');
                                            $("#control_precio_termino_fijo_diario_tarifa_gas").show();
                                            $("#precio_termino_fijo_diario_tarifa_gas").addClass('TLNT_input_mandatory');
                                            $("#control_capacidad_contratada_gas").hide();
                                            $("#control_termino_fijo_gas").hide();
                                            $("#control_termino_fijo_gas_por_cliente").hide();
                                            $("#control_termino_variable_gas").hide();
                                            $("#control_excesos_demanda_gas").hide();
                                            break;
                                        }
                                        case TIPO_CALCULO_COSTE_TARIFAS_2021: {
                                            $("#control_precio_consumo_tarifa_gas").hide();
                                            $("#precio_consumo_tarifa_gas").removeClass('TLNT_input_mandatory');
                                            $("#control_precio_caudal_diario_tarifa_gas").hide();
                                            $("#control_caudal_diario_tarifa_gas").hide();
                                            $("#control_precio_termino_fijo_diario_tarifa_gas").hide();
                                            $("#control_capacidad_contratada_gas").show();
                                            $("#capacidad_contratada_tarifa_gas").addClass('TLNT_input_mandatory');
                                            $("#control_termino_fijo_gas").show();
                                            $("#termino_fijo_tarifa_gas").addClass('TLNT_input_mandatory');
                                            $("#control_termino_fijo_gas_por_cliente").hide();
                                            $("#control_termino_variable_gas").show();
                                            $("#termino_variable_tarifa_gas").addClass('TLNT_input_mandatory');
                                            $("#control_excesos_demanda_gas").show();
                                            $("#exceso_demanda_tarifa_gas").addClass('TLNT_input_mandatory');
                                            break;
                                        }
                                        case TIPO_CALCULO_COSTE_POR_CLIENTE: {
                                            $("#control_precio_consumo_tarifa_gas").hide();
                                            $("#precio_consumo_tarifa_gas").removeClass('TLNT_input_mandatory');
                                            $("#control_precio_caudal_diario_tarifa_gas").hide();
                                            $("#control_caudal_diario_tarifa_gas").hide();
                                            $("#control_precio_termino_fijo_diario_tarifa_gas").hide();
                                            $("#control_capacidad_contratada_gas").hide();
                                            $("#capacidad_contratada_tarifa_gas").removeClass('TLNT_input_mandatory');
                                            $("#control_termino_fijo_gas").hide();
                                            $("#control_termino_fijo_gas_por_cliente").show();
                                            $("#termino_fijo_tarifa_gas").addClass('TLNT_input_mandatory');
                                            $("#control_termino_variable_gas").show();
                                            $("#termino_variable_tarifa_gas").addClass('TLNT_input_mandatory');
                                            $("#control_excesos_demanda_gas").hide();
                                            $("#exceso_demanda_tarifa_gas").removeClass('TLNT_input_mandatory');
                                            break;
                                        }
                                    }
                                    break;
                                }
                                case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE: {
                                    $("#factor_conversion_tarifa_gas").removeClass('TLNT_input_mandatory');
                                    $("#precio_consumo_tarifa_gas").removeClass('TLNT_input_mandatory');
                                    $("#precio_caudal_diario_tarifa_gas").removeClass('TLNT_input_mandatory');
                                    $("#caudal_diario_tarifa_gas").removeClass('TLNT_input_mandatory');
                                    $("#precio_diario_tarifa_gas").removeClass('TLNT_input_mandatory');
                                    switch (tipo_calculo_coste_termino_fijo) {
                                        case TIPO_CALCULO_COSTE_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES: {
                                            $("#control_precio_caudal_diario_tarifa_gas").show();
                                            $("#control_caudal_diario_tarifa_gas").show();
                                            $("#control_precio_termino_fijo_diario_tarifa_gas").hide();
                                            break;
                                        }
                                        case TIPO_CALCULO_COSTE_TERMINO_FIJO_SIN_EXCESOS: {
                                            $("#control_precio_caudal_diario_tarifa_gas").hide();
                                            $("#control_caudal_diario_tarifa_gas").hide();
                                            $("#control_precio_termino_fijo_diario_tarifa_gas").show();
                                            break;
                                        }
                                        case TIPO_CALCULO_COSTE_TARIFAS_2021: {
                                            $("#control_precio_caudal_diario_tarifa_gas").hide();
                                            $("#control_caudal_diario_tarifa_gas").hide();
                                            $("#control_precio_termino_fijo_diario_tarifa_gas").hide();
                                            $("#control_capacidad_contratada_gas").show();
                                            $("#control_termino_fijo_gas").show();
                                            $("#control_termino_fijo_gas_por_cliente").hide();
                                            $("#control_termino_variable_gas").show();
                                            $("#control_excesos_demanda_gas").show();
                                            break;
                                        }
                                        case TIPO_CALCULO_COSTE_POR_CLIENTE: {
                                            $("#control_precio_caudal_diario_tarifa_gas").hide();
                                            $("#control_caudal_diario_tarifa_gas").hide();
                                            $("#control_precio_termino_fijo_diario_tarifa_gas").hide();
                                            $("#control_capacidad_contratada_gas").hide();
                                            $("#control_termino_fijo_gas").hide();
                                            $("#control_termino_fijo_gas_por_cliente").show();
                                            $("#control_termino_variable_gas").show();
                                            $("#control_excesos_demanda_gas").hide();
                                            break;
                                        }
                                    }
                                    break;
                                }
                            }
                            break;
                        }
                    }
                });
                break;
            };
        };
    };
    $("#tipo_tarifa_gas").show(funcion_habilita_muestra_controles_tipo_tarifa_gas);

    // Realiza acciones al cambiar el tipo de tarifa de gas
    // - Recarga la lista de tarifas (si es necesario)
    var funcion_realiza_acciones_tipo_tarifa_gas_modificada = function() {
        var tipo_administracion_tarifas_gas = $("#tabs-administracion-tarifa-gas").attr("tipo-administracion");

        // Habilita las pestañas correspondientes
        funcion_habilita_muestra_controles_tipo_tarifa_gas();

        // Se recargan las tarifas de gas (si es necesario)
        if (tipo_administracion_tarifas_gas == TIPO_ADMINISTRACION_TARIFAS_MULTIPLE) {
            funcion_recarga_lista_tarifas_gas_modificacion_tarifas_gas();
        }
    };
    $("#tipo_tarifa_gas").change(funcion_realiza_acciones_tipo_tarifa_gas_modificada);
};


establece_eventos_ventanas_modales_smartmeter_tarifas_agua = function() {
    // Desactivación de eventos anteriores
    $("#descripcion_tarifa_agua").off();
    $("#tipo_tarifa_agua").off();

    // Contador de caracteres de descripción de tarifa eléctrica
    $("#descripcion_tarifa_agua").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);

    // Mostrar lista doble para las tarifas
    if ($('#select_tarifas_agua_tarifa_agua_no_visible').length) {
        $('#select_tarifas_agua_tarifa_agua_no_visible').attr("id", "select_tarifas_agua_tarifa_agua_visible");
        TLNT.Navegacion.convierte_lista_doble("ids_tarifas_agua_tarifa_agua", true);
    }

    // Recarga la lista de tarifa de agua de la ventana de modificación
    var funcion_recarga_lista_tarifas_agua_modificacion_tarifas_agua = function() {
        var tipo_tarifa_agua = $("#tipo_tarifa_agua").val();

        switch (pais_tarifas_agua) {
            case PAIS_ESPANYA: {
                $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/dame_lista_tarifas_tipo_agua_Espanya.php", {
                    tipo: tipo_tarifa_agua
                },
                function (data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }

                    // Recarga de lista doble
                    // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
                    $("#ids_tarifas_agua_tarifa_agua").multiselect2side('destroy');
                    $("#ids_tarifas_agua_tarifa_agua").html(resultado.html);
                    TLNT.Navegacion.convierte_lista_doble("ids_tarifas_agua_tarifa_agua", true);
                });
                break;
            }
        }
    };

    // Habilita y muestra los controles dependientes del tipo de la tarifa de agua
    var funcion_habilita_muestra_controles_tipo_tarifa_agua = function() {
        var tipo_tarifa_agua = $("#tipo_tarifa_agua").val();
        var tipo_administracion_tarifas_agua = $("#tabs-administracion-tarifa-agua").attr("tipo-administracion");

        switch (pais_tarifas_agua) {
            case PAIS_ESPANYA: {
                $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/dame_caracteristicas_tipo_tarifa_agua_Espanya.php", {
                    tipo: tipo_tarifa_agua
                },
                function (data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }

                    var tipo_tarifa_canarias = resultado.tipo_tarifa_canarias;

                    // Pestaña de factura
                    if (tipo_tarifa_agua != TIPO_TARIFA_NINGUNO) {
                        $("#titulo-tab-precios-consumo").show();
                        $("#titulo-tab-factura").show();
                        switch (tipo_administracion_tarifas_agua) {
                            case TIPO_ADMINISTRACION_TARIFAS_UNICA: {
                                $("#alquiler_contador_tarifa_agua").addClass('TLNT_input_mandatory');
                                if (tipo_tarifa_canarias == false) {
                                    $("#iva_consumo_tarifa_agua").addClass('TLNT_input_mandatory');
                                    $("#igic_consumo_tarifa_agua").removeClass('TLNT_input_mandatory');
                                    $("#iva_alquiler_contador_tarifa_agua").addClass('TLNT_input_mandatory');
                                    $("#igic_alquiler_contador_tarifa_agua").removeClass('TLNT_input_mandatory');
                                }
                                else {
                                    $("#iva_consumo_tarifa_agua").removeClass('TLNT_input_mandatory');
                                    $("#igic_consumo_tarifa_agua").addClass('TLNT_input_mandatory');
                                    $("#iva_alquiler_contador_tarifa_agua").removeClass('TLNT_input_mandatory');
                                    $("#igic_alquiler_contador_tarifa_agua").addClass('TLNT_input_mandatory');
                                }
                                break;
                            }
                            case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE: {
                                $("#alquiler_contador_tarifa_agua").removeClass('TLNT_input_mandatory');
                                $("#iva_consumo_tarifa_agua").removeClass('TLNT_input_mandatory');
                                $("#igic_consumo_tarifa_agua").removeClass('TLNT_input_mandatory');
                                $("#iva_alquiler_contador_tarifa_agua").removeClass('TLNT_input_mandatory');
                                $("#igic_alquiler_contador_tarifa_agua").removeClass('TLNT_input_mandatory');
                                break;
                            }
                        }
                        if (tipo_tarifa_agua != TIPO_TARIFA_TODOS) {
                            if (tipo_tarifa_canarias == false) {
                                $("#control_iva_consumo_tarifa_agua").show();
                                $("#control_igic_consumo_tarifa_agua").hide();
                                $("#control_iva_alquiler_contador_tarifa_agua").show();
                                $("#control_igic_alquiler_contador_tarifa_agua").hide();
                            }
                            else {
                                $("#control_iva_consumo_tarifa_agua").hide();
                                $("#control_igic_consumo_tarifa_agua").show();
                                $("#control_iva_alquiler_contador_tarifa_agua").hide();
                                $("#control_igic_alquiler_contador_tarifa_agua").show();
                            }
                        }
                        else {
                            $("#control_iva_consumo_tarifa_agua").show();
                            $("#control_igic_consumo_tarifa_agua").show();
                            $("#control_iva_alquiler_contador_tarifa_agua").show();
                            $("#control_igic_alquiler_contador_tarifa_agua").show();
                        }
                    }
                    else {
                        $("#titulo-tab-precios-consumo").hide();
                        $("#titulo-tab-factura").hide();
                        $("#alquiler_contador_tarifa_electrica").removeClass('TLNT_input_mandatory');
                        $("#iva_consumo_tarifa_agua").removeClass('TLNT_input_mandatory');
                        $("#igic_consumo_tarifa_agua").removeClass('TLNT_input_mandatory');
                        $("#iva_alquiler_contador_tarifa_agua").removeClass('TLNT_input_mandatory');
                        $("#igic_alquiler_contador_tarifa_agua").removeClass('TLNT_input_mandatory');
                    }
                });
                break;
            }
        }
    };
    $("#tipo_tarifa_agua").show(funcion_habilita_muestra_controles_tipo_tarifa_agua);

    // Realiza acciones al cambiar el tipo de tarifa de agua
    // - Recarga los controles de cada una de las pestañas
    // - Recarga la lista de tarifas (si es necesario)
    var funcion_realiza_acciones_tipo_tarifa_agua_modificada = function() {
        var tipo_administracion_tarifas_agua = $("#tabs-administracion-tarifa-agua").attr("tipo-administracion");

        // Se habilitan los controles del tipo de tarifa de agua
        funcion_habilita_muestra_controles_tipo_tarifa_agua();

        // Se recargan las tarifas de agua (si es necesario)
        if (tipo_administracion_tarifas_agua == TIPO_ADMINISTRACION_TARIFAS_MULTIPLE) {
            funcion_recarga_lista_tarifas_agua_modificacion_tarifas_agua();
        }
    };
    $("#tipo_tarifa_agua").change(funcion_realiza_acciones_tipo_tarifa_agua_modificada);
};


establece_eventos_ventanas_modales_smartmeter_conceptos_adicionales_factura_tarifas = function() {
    // Desactivación de eventos anteriores
    $("#tipo_concepto_adicional_factura_tarifa").off();

    // Habilita y muestra los controles dependientes del tipo de concepto adicional
    var funcion_habilita_muestra_controles_tipo_concepto_adicional = function() {
        var tipo_concepto_adicional = $("#tipo_concepto_adicional_factura_tarifa").val();

        switch (tipo_concepto_adicional) {
            case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_FIJO:
            case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_DIARIO: {
                $("#control_limites_consumo_tramos_concepto_adicional_factura_tarifa").hide();
                break;
            }
            case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_ABSOLUTO:
            case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_DIARIO: {
                $("#control_limites_consumo_tramos_concepto_adicional_factura_tarifa").show();
                break;
            }
        }
    };
    $("#tipo_concepto_adicional_factura_tarifa").show(funcion_habilita_muestra_controles_tipo_concepto_adicional);
    $("#tipo_concepto_adicional_factura_tarifa").change(funcion_habilita_muestra_controles_tipo_concepto_adicional);
};

establece_eventos_secciones_smartmeter_resultados_mensuales = function(){
    // Desactivación de eventos anteriores
    $('#boton_importacion_valores_sensor_seleccionar_fichero').off();

    // Ventana de importación de valores (selección de fichero de valores)
    $("#fichero_importacion_valores_sensor_text").show(function() {
        $('#fichero_importacion_valores_sensor_file').hide();
    });
    $('#fichero_importacion_valores_sensor_file').change(function() {
        var fichero = $(this).val().split('\\').pop();
        $('#fichero_importacion_valores_sensor_text').val(fichero);
    });
    $('#boton_importacion_valores_sensor_seleccionar_fichero').click(function() {
        $('#fichero_importacion_valores_sensor_file').click();
    });

};

//
// Funciones auxiliares (utilizadas en varias funciones)
//


// Se selecciona la tarifa del sensor seleccionado
funcion_selecciona_tarifa = function(id_controles) {
    var id_sensor = $("#id_sensor_" + id_controles).val();
    if (id_sensor == ID_NINGUNO) {
        $("#id_tarifa_" + id_controles).val(ID_NINGUNO);

        // Se deshabilita si sólo hay un valor para elegir
        var numero_tarifas = $("select#id_tarifa_" + id_controles + " option").length;
        if (numero_tarifas <= 1) {
            $("#id_tarifa_" + id_controles).attr('disabled', true).trigger("chosen:updated");
        }
        else {
            $("#id_tarifa_" + id_controles).removeAttr('disabled').trigger("chosen:updated");
        }
        $("#id_tarifa_" + id_controles).trigger('change');
    }
    else {
        $.post("./src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/dame_id_tarifa_id_sensor.php", {
            id_sensor: id_sensor
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Se selecciona la tarifa (si no está en la lista, se selecciona ninguna)
            var existe_valor = false;
            $("#id_tarifa_" + id_controles + " option").each(function() {
                if (this.value == resultado.id_tarifa) {
                    existe_valor = true;
                }
            });
            if (existe_valor == true) {
                $("#id_tarifa_" + id_controles).val(resultado.id_tarifa);
            }
            else {
                $("#id_tarifa_" + id_controles).val(ID_NINGUNO);
            }

            // Se deshabilita si sólo hay un valor para elegir
            var numero_tarifas = $("select#id_tarifa_" + id_controles + " option").length;
            if (numero_tarifas <= 1) {
                $("#id_tarifa_" + id_controles).attr('disabled', true).trigger("chosen:updated");
            }
            else {
                $("#id_tarifa_" + id_controles).removeAttr('disabled').trigger("chosen:updated");
            }
            $("#id_tarifa_" + id_controles).trigger('change');
        });
    }
};

// Funciones para mostrar u ocultar los DIV de subir excel en función de la pestaña seleccionada

funcion_oculta_estudio_general= function(){

    $("#tab-estudio-general").hide();
    $("#tab-resultados-mensuales").show();
};
funcion_muestra_estudio_general= function(){

    $("#tab-estudio-general").show();
    $("#tab-resultados-mensuales").hide();
};
