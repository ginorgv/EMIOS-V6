// Eventos de los botones (por funcionalidad)
TLNT.Navegacion.botones_secciones_sensores = [
    // Sensores
    {	selector: '.boton_sensores_envia_accion_herramientas_sensores',
		funcion: 	boton_sensores_envia_accion_herramientas_sensores
	},
    {	selector: '.boton_sensores_mostrar_ventana_importacion_valores_sensor',
		funcion: 	boton_sensores_mostrar_ventana_importacion_valores_sensor
	},
    {	selector: '.boton_sensores_mostrar_ventana_exportacion_valores_sensor',
		funcion: 	boton_sensores_mostrar_ventana_exportacion_valores_sensor
	},
    {	selector: '.boton_sensores_mostrar_ventana_borrado_valores_sensor',
		funcion: 	boton_sensores_mostrar_ventana_borrado_valores_sensor
	},
    {	selector: '.boton_sensores_mostrar_ventana_recalculo_valores_clase_sensor',
		funcion: 	boton_sensores_mostrar_ventana_recalculo_valores_clase_sensor
	},
    {	selector: '.boton_sensores_mostrar_ventana_envio_valores_manuales_sensor',
		funcion: 	boton_sensores_mostrar_ventana_envio_valores_manuales_sensor
	},
    {	selector: '.boton_sensores_mostrar_ventana_asignacion_localizacion',
		funcion: 	boton_sensores_mostrar_ventana_asignacion_localizacion
	},
    {	selector: '.boton_sensores_mostrar_ventana_asignacion_grupo',
		funcion: 	boton_sensores_mostrar_ventana_asignacion_grupo
	},
    {	selector: '#boton_sensores_filtro_sensores_tabla',
		funcion: 	boton_sensores_filtro_sensores_tabla
	},
    {	selector: '#boton_sensores_filtro_grupos_tabla',
		funcion: 	boton_sensores_filtro_grupos_tabla
	},
    // Eventos
    {	selector: '#boton_sensores_filtro_eventos_tabla',
		funcion: 	boton_sensores_filtro_eventos_tabla
	},
    {	selector: '#boton_sensores_filtro_historico_eventos',
		funcion: 	boton_sensores_filtro_historico_eventos
	},
    {	selector: '#boton_sensores_activaciones_eventos_ver_informe',
		funcion: 	boton_sensores_activaciones_eventos_ver_informe
	},
    {	selector: '#boton_sensores_activaciones_eventos_generar_pdf',
		funcion: 	boton_sensores_activaciones_eventos_generar_pdf
	},
    {	selector: '#boton_sensores_activaciones_eventos_anyadir_informe_automatico',
		funcion: 	boton_sensores_activaciones_eventos_anyadir_informe_automatico
	},
    // Información
    {	selector: '#boton_sensores_informacion_temperatura_ver_informe',
		funcion: 	boton_sensores_informacion_temperatura_ver_informe
	},
    {	selector: '#boton_sensores_informacion_temperatura_generar_pdf',
		funcion: 	boton_sensores_informacion_temperatura_generar_pdf
	},
    {	selector: '#boton_sensores_informacion_temperatura_anyadir_informe_automatico',
		funcion: 	boton_sensores_informacion_temperatura_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_informacion_humedad_ver_informe',
		funcion: 	boton_sensores_informacion_humedad_ver_informe
	},
    {	selector: '#boton_sensores_informacion_humedad_generar_pdf',
		funcion: 	boton_sensores_informacion_humedad_generar_pdf
	},
    {	selector: '#boton_sensores_informacion_humedad_anyadir_informe_automatico',
		funcion: 	boton_sensores_informacion_humedad_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_informacion_luz_interior_ver_informe',
		funcion: 	boton_sensores_informacion_luz_interior_ver_informe
	},
    {	selector: '#boton_sensores_informacion_luz_interior_generar_pdf',
		funcion: 	boton_sensores_informacion_luz_interior_generar_pdf
	},
    {	selector: '#boton_sensores_informacion_luz_interior_anyadir_informe_automatico',
		funcion: 	boton_sensores_informacion_luz_interior_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_informacion_viento_ver_informe',
		funcion: 	boton_sensores_informacion_viento_ver_informe
	},
    {	selector: '#boton_sensores_informacion_viento_generar_pdf',
		funcion: 	boton_sensores_informacion_viento_generar_pdf
	},
    {	selector: '#boton_sensores_informacion_viento_anyadir_informe_automatico',
		funcion: 	boton_sensores_informacion_viento_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_informacion_energia_activa_ver_informe',
		funcion: 	boton_sensores_informacion_energia_activa_ver_informe
	},
    {	selector: '#boton_sensores_informacion_energia_activa_generar_pdf',
		funcion: 	boton_sensores_informacion_energia_activa_generar_pdf
	},
    {	selector: '#boton_sensores_informacion_energia_activa_anyadir_informe_automatico',
		funcion: 	boton_sensores_informacion_energia_activa_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_informacion_energia_reactiva_ver_informe',
		funcion: 	boton_sensores_informacion_energia_reactiva_ver_informe
	},
    {	selector: '#boton_sensores_informacion_energia_reactiva_generar_pdf',
		funcion: 	boton_sensores_informacion_energia_reactiva_generar_pdf
	},
    {	selector: '#boton_sensores_informacion_energia_reactiva_anyadir_informe_automatico',
		funcion: 	boton_sensores_informacion_energia_reactiva_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_informacion_cortes_tension_ver_informe',
		funcion: 	boton_sensores_informacion_cortes_tension_ver_informe
	},
    {	selector: '#boton_sensores_informacion_cortes_tension_generar_pdf',
		funcion: 	boton_sensores_informacion_cortes_tension_generar_pdf
	},
    {	selector: '#boton_sensores_informacion_cortes_tension_anyadir_informe_automatico',
		funcion: 	boton_sensores_informacion_cortes_tension_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_informacion_compra_energia_ver_informe',
		funcion: 	boton_sensores_informacion_compra_energia_ver_informe
	},
    {	selector: '#boton_sensores_informacion_compra_energia_generar_pdf',
		funcion: 	boton_sensores_informacion_compra_energia_generar_pdf
	},
    {	selector: '#boton_sensores_informacion_compra_energia_anyadir_informe_automatico',
		funcion: 	boton_sensores_informacion_compra_energia_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_informacion_gas_ver_informe',
		funcion: 	boton_sensores_informacion_gas_ver_informe
	},
    {	selector: '#boton_sensores_informacion_gas_generar_pdf',
		funcion: 	boton_sensores_informacion_gas_generar_pdf
	},
    {	selector: '#boton_sensores_informacion_gas_anyadir_informe_automatico',
		funcion: 	boton_sensores_informacion_gas_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_informacion_agua_ver_informe',
		funcion: 	boton_sensores_informacion_agua_ver_informe
	},
    {	selector: '#boton_sensores_informacion_agua_generar_pdf',
		funcion: 	boton_sensores_informacion_agua_generar_pdf
	},
    {	selector: '#boton_sensores_informacion_agua_anyadir_informe_automatico',
		funcion: 	boton_sensores_informacion_agua_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_informacion_generica_ver_informe',
		funcion: 	boton_sensores_informacion_generica_ver_informe
	},
    {	selector: '#boton_sensores_informacion_generica_generar_pdf',
		funcion: 	boton_sensores_informacion_generica_generar_pdf
	},
    {	selector: '#boton_sensores_informacion_generica_anyadir_informe_automatico',
		funcion: 	boton_sensores_informacion_generica_anyadir_informe_automatico
	},
    // Análisis
    {	selector: '#boton_sensores_analisis_horario_ver_informe',
		funcion: 	boton_sensores_analisis_horario_ver_informe
	},
    {	selector: '#boton_sensores_analisis_horario_generar_pdf',
		funcion: 	boton_sensores_analisis_horario_generar_pdf
	},
    {	selector: '#boton_sensores_analisis_horario_anyadir_informe_automatico',
		funcion: 	boton_sensores_analisis_horario_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_analisis_diario_ver_informe',
		funcion: 	boton_sensores_analisis_diario_ver_informe
	},
    {	selector: '#boton_sensores_analisis_diario_generar_pdf',
		funcion: 	boton_sensores_analisis_diario_generar_pdf
	},
    {	selector: '#boton_sensores_analisis_diario_anyadir_informe_automatico',
		funcion: 	boton_sensores_analisis_diario_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_analisis_comportamiento_ver_informe',
		funcion: 	boton_sensores_analisis_comportamiento_ver_informe
	},
    {	selector: '#boton_sensores_analisis_comportamiento_generar_pdf',
		funcion: 	boton_sensores_analisis_comportamiento_generar_pdf
	},
    {	selector: '#boton_sensores_analisis_comportamiento_anyadir_informe_automatico',
		funcion: 	boton_sensores_analisis_comportamiento_anyadir_informe_automatico
	},
    // Comparación
    {	selector: '#boton_sensores_comparacion_periodos_ver_informe',
		funcion: 	boton_sensores_comparacion_periodos_ver_informe
	},
    {	selector: '#boton_sensores_comparacion_periodos_generar_pdf',
		funcion: 	boton_sensores_comparacion_periodos_generar_pdf
	},
    {	selector: '#boton_sensores_comparacion_periodos_anyadir_informe_automatico',
		funcion: 	boton_sensores_comparacion_periodos_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_comparacion_perfil_horario_ver_informe',
		funcion: 	boton_sensores_comparacion_perfil_horario_ver_informe
	},
    {	selector: '#boton_sensores_comparacion_perfil_horario_anyadir_informe_automatico',
		funcion: 	boton_sensores_comparacion_perfil_horario_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_comparacion_perfil_horario_generar_pdf',
		funcion: 	boton_sensores_comparacion_perfil_horario_generar_pdf
	},
    {	selector: '#boton_sensores_comparacion_campos_iguales_ver_informe',
		funcion: 	boton_sensores_comparacion_campos_iguales_ver_informe
	},
    {	selector: '#boton_sensores_comparacion_campos_iguales_generar_pdf',
		funcion: 	boton_sensores_comparacion_campos_iguales_generar_pdf
	},
    {	selector: '#boton_sensores_comparacion_campos_iguales_anyadir_informe_automatico',
		funcion: 	boton_sensores_comparacion_campos_iguales_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_comparacion_campos_diferentes_ver_informe',
		funcion: 	boton_sensores_comparacion_campos_diferentes_ver_informe
	},
    {	selector: '#boton_sensores_comparacion_campos_diferentes_generar_pdf',
		funcion: 	boton_sensores_comparacion_campos_diferentes_generar_pdf
	},
    {	selector: '#boton_sensores_comparacion_campos_diferentes_anyadir_informe_automatico',
		funcion: 	boton_sensores_comparacion_campos_diferentes_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_valores_generales_ver_informe',
		funcion: 	boton_sensores_valores_generales_ver_informe
	},
    {	selector: '#boton_sensores_valores_generales_generar_pdf',
		funcion: 	boton_sensores_valores_generales_generar_pdf
	},
    {	selector: '#boton_sensores_valores_generales_anyadir_informe_automatico',
		funcion: 	boton_sensores_valores_generales_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_incrementos_totales_ver_informe',
		funcion: 	boton_sensores_incrementos_totales_ver_informe
	},
    {	selector: '#boton_sensores_incrementos_totales_generar_pdf',
		funcion: 	boton_sensores_incrementos_totales_generar_pdf
	},
    {	selector: '#boton_sensores_incrementos_totales_anyadir_informe_automatico',
		funcion: 	boton_sensores_incrementos_totales_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_analisis_comparativo_ver_informe',
		funcion: 	boton_sensores_analisis_comparativo_ver_informe
	},
    {	selector: '#boton_sensores_analisis_comparativo_generar_pdf',
		funcion: 	boton_sensores_analisis_comparativo_generar_pdf
	},
    {	selector: '#boton_sensores_analisis_comparativo_anyadir_informe_automatico',
		funcion: 	boton_sensores_analisis_comparativo_anyadir_informe_automatico
	},
    // Estadística
    {	selector: '#boton_sensores_histograma_ver_informe',
		funcion: 	boton_sensores_histograma_ver_informe
	},
    {	selector: '#boton_sensores_histograma_generar_pdf',
		funcion: 	boton_sensores_histograma_generar_pdf
	},
    {	selector: '#boton_sensores_histograma_anyadir_informe_automatico',
		funcion: 	boton_sensores_histograma_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_correlacion_ver_informe',
		funcion: 	boton_sensores_correlacion_ver_informe
	},
    {	selector: '#boton_sensores_correlacion_generar_pdf',
		funcion: 	boton_sensores_correlacion_generar_pdf
	},
    {	selector: '#boton_sensores_correlacion_anyadir_informe_automatico',
		funcion: 	boton_sensores_correlacion_anyadir_informe_automatico
	},
    {	selector: '#boton_sensores_correlacion_mostrar_ventana_anyadir_linea_base',
		funcion: 	boton_sensores_correlacion_mostrar_ventana_anyadir_linea_base
	},
    // Ayuda (información)
    {	selector: '#boton_sensores_ayuda_informe_temperatura_informacion',
		funcion: 	boton_sensores_ayuda_informes_informacion
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_informacion_temperatura',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_informacion_temperatura',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_sensores_ayuda_informe_humedad_informacion',
		funcion: 	boton_sensores_ayuda_informes_informacion
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_informacion_humedad',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_informacion_humedad',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_sensores_ayuda_informe_luz_interior_informacion',
		funcion: 	boton_sensores_ayuda_informes_informacion
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_informacion_luz_interior',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_informacion_luz_interior',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_sensores_ayuda_informe_viento_informacion',
		funcion: 	boton_sensores_ayuda_informes_informacion
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_informacion_viento',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_informacion_viento',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_sensores_ayuda_informe_energia_activa_informacion',
		funcion: 	boton_sensores_ayuda_informes_informacion
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_informacion_energia_activa',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_informacion_energia_activa',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_sensores_ayuda_informe_energia_reactiva_informacion',
		funcion: 	boton_sensores_ayuda_informes_informacion
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_informacion_energia_reactiva',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_informacion_energia_reactiva',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_sensores_ayuda_informe_cortes_tension_informacion',
		funcion: 	boton_sensores_ayuda_informes_informacion
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_informacion_cortes_tension',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_informacion_cortes_tension',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_sensores_ayuda_informe_gas_informacion',
		funcion: 	boton_sensores_ayuda_informes_informacion
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_informacion_gas',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_informacion_gas',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_sensores_ayuda_informe_generica_informacion',
		funcion: 	boton_sensores_ayuda_informes_informacion
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_informacion_generica',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_informacion_generica',
		funcion: 	boton_ayuda_fechas
	},
    // Ayuda (análisis)
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_analisis_horario',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_analisis_horario',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_analisis_diario',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_analisis_diario',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_analisis_comportamiento',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_analisis_comportamiento',
		funcion: 	boton_ayuda_fechas
	},
    // Ayuda (comparación)
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_comparacion_periodos',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_agrupaciones_dias_semana_sensores_comparacion_perfil_horario',
		funcion: 	boton_ayuda_agrupaciones_dias_semana
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_comparacion_perfil_horario',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_comparacion_campos_iguales',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_comparacion_campos_iguales',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_comparacion_campos_diferentes',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_comparacion_campos_diferentes',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_valores_generales',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_valores_generales',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_incrementos_totales',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_incrementos_totales',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_analisis_comparativo',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_analisis_comparativo',
		funcion: 	boton_ayuda_fechas
	},
    // Ayuda (estadística)
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_histograma',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_histograma',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_exclusion_fechas_sensores_correlacion',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_ayuda_inclusion_fechas_sensores_correlacion',
		funcion: 	boton_ayuda_fechas
	},
    {	selector: '#boton_sensores_ayuda_informe_correlacion',
		funcion: 	boton_sensores_ayuda_informe_correlacion
	},
    // Mapa
    {	selector: '#boton_sensores_filtro_sensores_mapa',
		funcion: 	boton_sensores_filtro_sensores_mapa
	}
];


TLNT.Navegacion.botones_tablas_datos_sensores = [
    // Tabla de eventos
    {	selector: '.boton_sensores_mostrar_ventana_anyadir_modificar_evento',
		funcion: 	boton_sensores_mostrar_ventana_anyadir_modificar_evento
	},
    {	selector: '.boton_sensores_actualizar_tabla_eventos',
		funcion: 	boton_sensores_actualizar_tabla_eventos
	},
    {	selector: '.boton_sensores_actualizacion_periodica_tabla_eventos',
		funcion: 	boton_sensores_actualizacion_periodica_tabla_eventos
	},
	{	selector: '.boton_sensores_eliminar_evento',
		funcion: 	boton_sensores_eliminar_evento
	},
    // Ayuda (eventos)
    {	selector: '.boton_sensores_ayuda_tabla_eventos',
		funcion: 	boton_sensores_ayuda_tabla_eventos
	}
];


TLNT.Navegacion.botones_detalles_tablas_datos_sensores = [
    // Comentarios
    {   selector: '.boton_mostrar_ventana_anyadir_modificar_comentario',
		funcion: 	boton_mostrar_ventana_anyadir_modificar_comentario
	},
    // Sensores
    {	selector: '.boton_sensores_envia_accion_herramientas_sensor',
		funcion: 	boton_sensores_envia_accion_herramientas_sensor
	},
    {	selector: '.boton_sensores_mostrar_ventana_importacion_valores_sensor',
		funcion: 	boton_sensores_mostrar_ventana_importacion_valores_sensor
	},
    {	selector: '.boton_sensores_mostrar_ventana_exportacion_valores_sensor',
		funcion: 	boton_sensores_mostrar_ventana_exportacion_valores_sensor
	},
    {	selector: '.boton_sensores_mostrar_ventana_borrado_valores_sensor',
		funcion: 	boton_sensores_mostrar_ventana_borrado_valores_sensor
	},
    {	selector: '.boton_sensores_mostrar_ventana_recalculo_valores_clase_sensor',
		funcion: 	boton_sensores_mostrar_ventana_recalculo_valores_clase_sensor
	},
    {	selector: '.boton_sensores_mostrar_ventana_envio_valores_manuales_sensor',
		funcion: 	boton_sensores_mostrar_ventana_envio_valores_manuales_sensor
	},
    // Eventos
    {	selector: '.boton_sensores_refrescar_tabla_evento',
		funcion: 	boton_sensores_refrescar_tabla_evento
	}
];


TLNT.Navegacion.botones_tablas_datos_informes_sensores = [
    // Comentarios
    {   selector: '.boton_mostrar_ventana_anyadir_modificar_comentario',
		funcion: 	boton_mostrar_ventana_anyadir_modificar_comentario
	},
    {	selector: '.boton_eliminar_comentario',
		funcion: 	boton_eliminar_comentario
	}
];


TLNT.Navegacion.botones_ventanas_modales_sensores = [
    // Sensores
    {	selector: '.boton_sensores_importar_valores_sensor',
		funcion: 	boton_sensores_importar_valores_sensor
	},
	{	selector: '.boton_sensores_exportar_valores_sensor',
		funcion: 	boton_sensores_exportar_valores_sensor
	},
	{	selector: '.boton_sensores_borrar_valores_sensor',
		funcion: 	boton_sensores_borrar_valores_sensor
	},
    {	selector: '.boton_sensores_recalcular_valores_clase_sensor',
		funcion: 	boton_sensores_recalcular_valores_clase_sensor
	},
    {	selector: '.boton_sensores_enviar_valores_manuales_sensor',
		funcion: 	boton_sensores_enviar_valores_manuales_sensor
	},
    // Ayuda (sensores)
    {	selector: '.boton_sensores_ayuda_importacion_valores_sensor',
		funcion: 	boton_sensores_ayuda_importacion_valores_sensor
	},
    {	selector: '#boton_sensores_ayuda_formato_fecha_importacion_valores_sensor',
		funcion: 	boton_sensores_ayuda_formato_fecha_importacion_valores_sensor
	},
    {	selector: '#boton_sensores_ayuda_valores_clase_exportacion',
		funcion: 	boton_sensores_ayuda_valores_clase_exportacion
	},
    {	selector: '#boton_sensores_ayuda_calibracion_interfaz_sensor',
		funcion: 	boton_sensores_ayuda_calibracion_interfaz_sensor
	},
    {	selector: '#boton_sensores_ayuda_calibracion_externo_sensor',
		funcion: 	boton_sensores_ayuda_calibracion_externo_sensor
	},
    {	selector: '#boton_sensores_ayuda_calibracion_procesado_sensor',
		funcion: 	boton_sensores_ayuda_calibracion_procesado_sensor
	},
    {	selector: '#boton_sensores_ayuda_funcion_valores_horaria_procesado_sensor',
		funcion: 	boton_sensores_ayuda_funcion_valores_procesado_sensor
	},
    {	selector: '#boton_sensores_ayuda_funcion_valores_cuartohoraria_procesado_sensor',
		funcion: 	boton_sensores_ayuda_funcion_valores_procesado_sensor
	},
    {	selector: '#boton_sensores_ayuda_valores_prueba_funcion_valores_procesado_sensor',
		funcion: 	boton_sensores_ayuda_valores_prueba_funcion_valores_procesado_sensor
	},
    // Eventos
    {	selector: '.boton_sensores_anyadir_modificar_evento',
		funcion: 	boton_sensores_anyadir_modificar_evento
	},
    // Ayuda (eventos)
    {	selector: '#boton_sensores_ayuda_agrupaciones_dias_semana_evento_perfil_horario',
		funcion: 	boton_ayuda_agrupaciones_dias_semana
	},
    {	selector: '#boton_sensores_ayuda_parametros_evento',
		funcion: 	boton_sensores_ayuda_parametros_evento
	},
    // Ayuda (eventos)
    {	selector: '#boton_ayuda_exclusion_fechas_evento',
		funcion: 	boton_ayuda_fechas
	},
    // Correlacion
    {	selector: '.boton_sensores_correlacion_anyadir_linea_base',
		funcion: 	boton_sensores_correlacion_anyadir_linea_base
	}
];


TLNT.Navegacion.botones_controles_interfaces_sensores = [
    // Ayuda
    {	selector: '#boton_sensores_ayuda_tipos_registro_modbus_sensor',
		funcion: 	boton_sensores_ayuda_tipos_registro_modbus_sensor
	},
    {	selector: '#boton_sensores_ayuda_tipos_dato_modbus_sensor',
		funcion: 	boton_sensores_ayuda_tipos_dato_modbus_sensor
	},
    {	selector: '#boton_sensores_ayuda_formato_fecha_sensor_externo_ficheros_csv_sensor',
		funcion: 	boton_sensores_ayuda_formato_fecha_sensor_externo_ficheros_csv_sensor
	},
    {	selector: '#boton_sensores_ayuda_idema_sensor_externo_http_emios_sensor',
		funcion: 	boton_sensores_ayuda_idema_sensor_externo_http_emios_sensor
	},
    {	selector: '#boton_sensores_ayuda_valores_aleatorios_sensor',
		funcion: 	boton_sensores_ayuda_valores_aleatorios_sensor
	}
];


//
// Funciones de establecimiento de eventos (por funcionalidad)
//


TLNT.Navegacion.establece_eventos_secciones_sensores = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_secciones_sensores);

    establece_eventos_secciones_sensores_principal();
    establece_eventos_secciones_sensores_informes();
};


TLNT.Navegacion.establece_eventos_tablas_datos_sensores = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_tablas_datos_sensores);
};


TLNT.Navegacion.establece_eventos_detalles_tablas_datos_sensores = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_detalles_tablas_datos_sensores);
};


TLNT.Navegacion.establece_eventos_tablas_datos_informes_sensores = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_tablas_datos_informes_sensores);
};


TLNT.Navegacion.establece_eventos_ventanas_modales_sensores = function() {
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_ventanas_modales_sensores);

    establece_eventos_ventanas_modales_sensores_herramientas();
    establece_eventos_ventanas_modales_sensores_sensores();
    establece_eventos_ventanas_modales_sensores_interfaces_sensores();
    establece_eventos_ventanas_modales_sensores_tipo_sensor_externo();
    establece_eventos_ventanas_modales_sensores_clase_sensor();
    establece_eventos_ventanas_modales_sensores_hijos_sensores();
    establece_eventos_ventanas_modales_sensores_grupos_sensores();
    establece_eventos_ventanas_modales_sensores_eventos();
};


//
// Funciones auxiliares para establecer las acciones de los controles
//


establece_eventos_secciones_sensores_principal = function() {
    // Habilitación de selección de ratio en sensores
    $('#pestanyas-principal-sensores').off('shown.bs.tab');
    $('#pestanyas-principal-sensores').on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var href_pestanya_activa = $('#pestanyas-principal-sensores .active > a').attr('href');
        var id_pestanya_activa = href_pestanya_activa.replace("#tab-", "");
        switch (id_pestanya_activa) {
            case "sensores": {
                $('#control_id_ratio_seleccion_localizacion_actual').show();
                break;
            }
            default: {
                $('#control_id_ratio_seleccion_localizacion_actual').hide();
                break;
            }
        }
    });
};


establece_eventos_secciones_sensores_informes = function() {
    establece_eventos_secciones_sensores_informes_eventos();
    establece_eventos_secciones_sensores_informes_informacion();
    establece_eventos_secciones_sensores_informes_analisis();
    establece_eventos_secciones_sensores_informes_comparacion();
    establece_eventos_secciones_sensores_informes_estadistica();
};


establece_eventos_secciones_sensores_informes_eventos = function() {
    // Desactivación de eventos anteriores
    $("#clase_sensor_sensores_activaciones_eventos").off();
    $("#origen_evento_sensores_activaciones_eventos").off();
    $("#id_origen_evento_sensores_activaciones_eventos").off();
    $("#granularidad_evento_sensores_activaciones_eventos").off();
    $("#campo_sensores_activaciones_eventos").off();

    // Mostrar lista doble para la selección de eventos de activaciones de eventos
    if ($('#select_eventos_no_visible_sensores_activaciones_eventos').length) {
        $('#select_eventos_no_visible_sensores_activaciones_eventos').attr("id", "select_eventos_visible_sensores_activaciones_eventos");
        TLNT.Navegacion.convierte_lista_doble("ids_eventos_sensores_activaciones_eventos", true);
    };

    // Funciones de recarga de listas de parámetros de activaciones de eventos
    var funcion_recarga_ids_origenes_evento_activaciones_eventos = function() {
        recuperando_lista_ids_origenes_evento_sensores = true;
        $.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/dame_lista_ids_origenes_evento.php", {
			clase_sensor: $("#clase_sensor_sensores_activaciones_eventos").val(),
            origen: $("#origen_evento_sensores_activaciones_eventos").val(),
            id_origen: $("#id_origen_evento_sensores_activaciones_eventos").val(),
            opciones_extra: OPCIONES_EXTRA_LISTA_IDS_ORIGENES_EVENTO_TODOS
		},
		function (data, status) {
            recuperando_lista_ids_origenes_evento_sensores = false;
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#id_origen_evento_sensores_activaciones_eventos").html(resultado.html);
            $("#id_origen_evento_sensores_activaciones_eventos").trigger('change');
            funcion_habilita_lista_ids_origenes_evento_activaciones_eventos();
		});
    };
    var funcion_habilita_lista_ids_origenes_evento_activaciones_eventos = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_origenes = $("select#id_origen_evento_sensores_activaciones_eventos" + " option").length;
        if (numero_origenes <= 1) {
            $("#id_origen_evento_sensores_activaciones_eventos").attr('disabled', true).trigger("chosen:updated");
        }
        else {
            $("#id_origen_evento_sensores_activaciones_eventos").removeAttr('disabled').trigger("chosen:updated");
        }
    };

    var funcion_recarga_granularidades_evento_activaciones_eventos = function() {
        var clase_sensor = $("#clase_sensor_sensores_activaciones_eventos").val();

        recuperando_lista_granularidades_evento_sensores = true;
        $.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/dame_lista_granularidades_evento.php", {
			clase_sensor: clase_sensor,
            granularidad: GRANULARIDAD_TODAS,
            opciones_extra: OPCIONES_EXTRA_LISTA_GRANULARIDADES_EVENTO_TODAS
		},
		function (data, status) {
            recuperando_lista_granularidades_evento_sensores = false;
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#granularidad_evento_sensores_activaciones_eventos").html(resultado.html);
            $("#granularidad_evento_sensores_activaciones_eventos").trigger('change');
            funcion_habilita_granularidad_evento_activaciones_eventos();
		});
    };
    var funcion_habilita_granularidad_evento_activaciones_eventos = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_granularidades = $("select#granularidad_evento_sensores_activaciones_eventos" + " option").length;
        if (numero_granularidades <= 1) {
            $("#granularidad_evento_sensores_activaciones_eventos").attr('disabled', true);
        }
        else {
            $("#granularidad_evento_sensores_activaciones_eventos").removeAttr('disabled');
        }
    };

    var funcion_recarga_campos_sensor_activaciones_eventos = function() {
        if ((recuperando_lista_ids_origenes_evento_sensores == true) ||
            (recuperando_lista_granularidades_evento_sensores == true)) {
            return;
        }

        var origen_evento = $("#origen_evento_sensores_activaciones_eventos").val();
        var id_sensor = null;
        switch (origen_evento) {
            case ORIGEN_EVENTO_SENSOR: {
                id_sensor = $("#id_origen_evento_sensores_activaciones_eventos").val();
                break;
            }
            case ORIGEN_EVENTO_GRUPO_SENSORES: {
                id_sensor = ID_NINGUNO;
            }
        }
        var clase_sensor = $("#clase_sensor_sensores_activaciones_eventos").val();
        var campo = $("#campo_sensores_activaciones_eventos").val();

        // Campo por defecto
        if (campo == CAMPO_NINGUNO) {
            switch (clase_sensor) {
                case CLASE_NINGUNA: {
                    break;
                }
                case CLASE_SENSOR_TEMPERATURA: {
                    campo = CAMPO_TEMPERATURA;
                    break;
                }
                case CLASE_SENSOR_HUMEDAD: {
                    campo = CAMPO_HUMEDAD;
                    break;
                }
                case CLASE_SENSOR_LUZ_INTERIOR: {
                    campo = CAMPO_ILUMINACION;
                    break;
                }
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                case CLASE_SENSOR_ENERGIA_REACTIVA: {
                    campo = CAMPO_INCREMENTO;
                    break;
                }
                case CLASE_SENSOR_CORTES_TENSION: {
                    campo = CAMPO_CORTES;
                    break;
                }
                case CLASE_SENSOR_COMPRA_ENERGIA: {
                    campo = CAMPO_CONSUMO_ESTIMADO;
                    break;
                }
                case CLASE_SENSOR_GAS: {
                    campo = CAMPO_INCREMENTO;
                    break;
                }
                case CLASE_SENSOR_AGUA: {
                    campo = CAMPO_INCREMENTO;
                    break;
                }
                case CLASE_SENSOR_GENERICA: {
                    campo = CAMPO_VALOR;
                    break;
                }
            }
        }

        $.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/dame_lista_campos_sensor_activaciones_eventos.php", {
			clase_sensor: clase_sensor,
            id_sensor: id_sensor,
            granularidad: $("#granularidad_evento_sensores_activaciones_eventos").val(),
            campo: campo
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#campo_sensores_activaciones_eventos").html(resultado.html);
            funcion_habilita_campo_activaciones_eventos();
		});
    };
    var funcion_habilita_campo_activaciones_eventos = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_campos = $("select#campo_sensores_activaciones_eventos" + " option").length;
        if (numero_campos <= 1) {
            $("#campo_sensores_activaciones_eventos").attr('disabled', true);
        }
        else {
            $("#campo_sensores_activaciones_eventos").removeAttr('disabled');
        }
    };

    var funcion_recarga_lista_doble_eventos_activaciones_eventos = function() {
        if ((recuperando_lista_ids_origenes_evento_sensores == true) ||
            (recuperando_lista_granularidades_evento_sensores == true)) {
            return;
        }

        var ids_eventos = [];
        $("#ids_eventos_sensores_activaciones_eventos option").each(function() {
            if (typeof($(this).attr("selected")) !== "undefined") {
                ids_eventos.push($(this).val());
            }
        });

        $.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/dame_lista_eventos.php", {
            clase_sensor: $("#clase_sensor_sensores_activaciones_eventos").val(),
            origen: $("#origen_evento_sensores_activaciones_eventos").val(),
            id_origen: $("#id_origen_evento_sensores_activaciones_eventos").val(),
            granularidad: $("#granularidad_evento_sensores_activaciones_eventos").val(),
            ids_eventos: ids_eventos
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            // Recarga de lista doble
            // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
            $("#ids_eventos_sensores_activaciones_eventos").multiselect2side('destroy');
            $("#ids_eventos_sensores_activaciones_eventos").html(resultado.html);
            TLNT.Navegacion.convierte_lista_doble("ids_eventos_sensores_activaciones_eventos", true);
        });
    };

    // Acciones a realizar al modificar la clase de sensor de activaciones de eventos
    var funcion_realiza_acciones_clase_sensor_activaciones_eventos = function() {
        funcion_recarga_ids_origenes_evento_activaciones_eventos();
        funcion_recarga_granularidades_evento_activaciones_eventos();
    };
    $("#clase_sensor_sensores_activaciones_eventos").change(funcion_realiza_acciones_clase_sensor_activaciones_eventos);

    // Acciones a realizar al modificar el origen de evento de activaciones de eventos
    var funcion_realiza_acciones_origen_evento_activaciones_eventos = function() {
        funcion_recarga_ids_origenes_evento_activaciones_eventos();
    };
    $("#origen_evento_sensores_activaciones_eventos").change(funcion_realiza_acciones_origen_evento_activaciones_eventos);

    // Acciones a realizar al modificar el identificador de origen de evento de activaciones de eventos
    var funcion_realiza_acciones_id_origen_evento_activaciones_eventos = function() {
        funcion_recarga_campos_sensor_activaciones_eventos();
        funcion_recarga_lista_doble_eventos_activaciones_eventos();
    };
    $("#id_origen_evento_sensores_activaciones_eventos").show(funcion_habilita_lista_ids_origenes_evento_activaciones_eventos);
    $("#id_origen_evento_sensores_activaciones_eventos").change(funcion_realiza_acciones_id_origen_evento_activaciones_eventos);

    // Acciones a realizar al modificar la granularidad de evento de activaciones de eventos
    var funcion_realiza_acciones_granularidad_evento_activaciones_eventos = function() {
        funcion_recarga_campos_sensor_activaciones_eventos();
        funcion_recarga_lista_doble_eventos_activaciones_eventos();
    };
    $("#granularidad_evento_sensores_activaciones_eventos").show(funcion_habilita_granularidad_evento_activaciones_eventos);
    $("#granularidad_evento_sensores_activaciones_eventos").change(funcion_realiza_acciones_granularidad_evento_activaciones_eventos);

    // Acciones a realizar al modificar el campo de sensor de activaciones de eventos
    $("#campo_sensores_activaciones_eventos").show(funcion_habilita_campo_activaciones_eventos);
};


establece_eventos_secciones_sensores_informes_informacion = function() {
    // Desactivación de eventos anteriores
    $("#campo_sensores_informacion_temperatura").off();
    $("#campo_sensores_informacion_energia_activa").off();
    $("#campo_sensores_informacion_energia_reactiva").off();
    $("#campo_sensores_informacion_compra_energia").off();
    $("#campo_sensores_informacion_energia_gas").off();
    $("#campo_sensores_informacion_energia_agua").off();
    $("#intervalo_valores_sensores_informacion_temperatura").off();
    $("#intervalo_valores_sensores_informacion_humedad").off();
    $("#intervalo_valores_sensores_informacion_luz_interior").off();
    $("#intervalo_valores_sensores_informacion_viento").off();
    $("#intervalo_valores_sensores_informacion_energia_activa").off();
    $("#intervalo_valores_sensores_informacion_energia_reactiva").off();
    $("#intervalo_valores_sensores_informacion_compra_energia").off();
    $("#intervalo_valores_sensores_informacion_gas").off();
    $("#intervalo_valores_sensores_informacion_agua").off();
    $("#intervalo_valores_sensores_informacion_generica").off();

    // Habilitación de selección de ratio en información
    $('#pestanyas-informacion-sensores').off('shown.bs.tab');
    $('#pestanyas-informacion-sensores').on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var href_pestanya_activa = $('#pestanyas-informacion-sensores .active > a').attr('href');
        var id_pestanya_activa = href_pestanya_activa.replace("#tab-", "");
        switch (id_pestanya_activa) {
            case "energia-activa":
            case "energia-reactiva":
            case "gas":
            case "agua":
            case "generica": {
                $('#control_id_ratio_seleccion_localizacion_actual').show();
                break;
            }
            default: {
                $('#control_id_ratio_seleccion_localizacion_actual').hide();
                break;
            }
        }
    });

    // Selecciona el campo por defecto de la clase de sensor
    var funcion_selecciona_campo_defecto_informes_informacion_clase_sensor = function(clase) {
        var id_controles = null;
        var campo = null;
        switch (clase) {
            case CLASE_SENSOR_ENERGIA_ACTIVA: {
                id_controles = "informacion_energia_activa";
                campo = CAMPO_INCREMENTO;
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA: {
                id_controles = "informacion_energia_reactiva";
                campo = CAMPO_INCREMENTO;
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA: {
                id_controles = "informacion_compra_energia";
                campo = CAMPO_CONSUMO_ESTIMADO;
                break;
            }
            case CLASE_SENSOR_GAS: {
                id_controles = "informacion_gas";
                campo = CAMPO_INCREMENTO;
                break;
            }
            case CLASE_SENSOR_AGUA: {
                id_controles = "informacion_agua";
                campo = CAMPO_INCREMENTO;
                break;
            }
        }
        $("#campo_sensores_" + id_controles).val(campo);
    };

    // Selección de campo por defecto y recarga de los intervalos de valores de energia_activa
    var funcion_selecciona_campo_defecto_informacion_energia_activa = function() {
        funcion_selecciona_campo_defecto_informes_informacion_clase_sensor(CLASE_SENSOR_ENERGIA_ACTIVA);
    };
    $("#campo_sensores_informacion_energia_activa").show(funcion_selecciona_campo_defecto_informacion_energia_activa);
    var funcion_recarga_intervalos_valores_informacion_energia_activa = function() {
        funcion_recarga_intervalos_valores_informes_informacion_comparacion("informacion_energia_activa", CLASE_SENSOR_ENERGIA_ACTIVA);
    };
    $("#campo_sensores_informacion_energia_activa").change(funcion_recarga_intervalos_valores_informacion_energia_activa);

    // Selección por defecto y recarga de los intervalos de valores de energía reactiva
    var funcion_selecciona_campo_defecto_informacion_energia_reactiva = function() {
        funcion_selecciona_campo_defecto_informes_informacion_clase_sensor(CLASE_SENSOR_ENERGIA_REACTIVA);
    };
    $("#campo_sensores_informacion_energia_reactiva").show(funcion_selecciona_campo_defecto_informacion_energia_reactiva);
    var funcion_recarga_intervalos_valores_informacion_energia_reactiva = function() {
        funcion_recarga_intervalos_valores_informes_informacion_comparacion("informacion_energia_reactiva", CLASE_SENSOR_ENERGIA_REACTIVA);
    };
    $("#campo_sensores_informacion_energia_reactiva").change(funcion_recarga_intervalos_valores_informacion_energia_reactiva);

    // Selección por defecto y recarga de los intervalos de valores de compra de energía
    var funcion_selecciona_campo_defecto_informacion_compra_energia = function() {
        funcion_selecciona_campo_defecto_informes_informacion_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA);
    };
    $("#campo_sensores_informacion_compra_energia").show(funcion_selecciona_campo_defecto_informacion_compra_energia);
    var funcion_recarga_intervalos_valores_informacion_compra_energia = function() {
        funcion_recarga_intervalos_valores_informes_informacion_comparacion("informacion_compra_energia", CLASE_SENSOR_COMPRA_ENERGIA);
    };
    $("#campo_sensores_informacion_compra_energia").change(funcion_recarga_intervalos_valores_informacion_compra_energia);

    // Selección por defecto y recarga de los intervalos de valores de gas
    var funcion_selecciona_campo_defecto_informacion_gas = function() {
        funcion_selecciona_campo_defecto_informes_informacion_clase_sensor(CLASE_SENSOR_GAS);
    };
    $("#campo_sensores_informacion_gas").show(funcion_selecciona_campo_defecto_informacion_gas);
    var funcion_recarga_intervalos_valores_informacion_gas = function() {
        funcion_recarga_intervalos_valores_informes_informacion_comparacion("informacion_gas", CLASE_SENSOR_GAS);
    };
    $("#campo_sensores_informacion_gas").change(funcion_recarga_intervalos_valores_informacion_gas);

    // Selección por defecto y recarga de los intervalos de valores de agua
    var funcion_selecciona_campo_defecto_informacion_agua = function() {
        funcion_selecciona_campo_defecto_informes_informacion_clase_sensor(CLASE_SENSOR_AGUA);
    };
    $("#campo_sensores_informacion_agua").show(funcion_selecciona_campo_defecto_informacion_agua);
    var funcion_recarga_intervalos_valores_informacion_agua = function() {
        funcion_recarga_intervalos_valores_informes_informacion_comparacion("informacion_agua", CLASE_SENSOR_AGUA);
    };
    $("#campo_sensores_informacion_agua").change(funcion_recarga_intervalos_valores_informacion_agua);

    // Control de parámetros extra de campo
    var funcion_muestra_control_referencia_temperatura = function() {
        funcion_muestra_control_parametros_extra_campo_informe("sensores_informacion_temperatura", CLASE_SENSOR_TEMPERATURA);
    };
    $("#campo_sensores_informacion_temperatura").show(funcion_muestra_control_referencia_temperatura);
    $("#campo_sensores_informacion_temperatura").change(funcion_muestra_control_referencia_temperatura);

    // Habilitación del selector de tipo de mapa de calor de la sección información
    var funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion = function(id_controles) {
        var intervalo_valores = $("#intervalo_valores_" + id_controles).val();
        switch (intervalo_valores) {
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            case INTERVALO_VALORES_CUARTOHORA:
            case INTERVALO_VALORES_HORA: {
                $("#tipo_mapa_calor_" + id_controles).prop('disabled', false);
                break;
            }
            default: {
                $("#tipo_mapa_calor_" + id_controles).val(TIPO_MAPA_CALOR_NINGUNO);
                $("#tipo_mapa_calor_" + id_controles).prop('disabled', 'disabled');
                break;
            }
        }
    };

    // Habilitación del selector de tipo de mapa de calor del informe de temperatura
    var funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_temperatura = function() {
        funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion("sensores_informacion_temperatura");
    };
    $("#intervalo_valores_sensores_informacion_temperatura").change(funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_temperatura);

    // Habilitación del selector de tipo de mapa de calor del informe de humedad
    var funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_humedad = function() {
        funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion("sensores_informacion_humedad");
    };
    $("#intervalo_valores_sensores_informacion_humedad").change(funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_humedad);

    // Habilitación del selector de tipo de mapa de calor del informe de luz interior
    var funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_luz_interior = function() {
        funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion("sensores_informacion_luz_interior");
    };
    $("#intervalo_valores_sensores_informacion_luz_interior").change(funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_luz_interior);

    // Habilitación del selector de tipo de mapa de calor del informe de viento
    var funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_viento = function() {
        funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion("sensores_informacion_viento");
    };
    $("#intervalo_valores_sensores_informacion_viento").change(funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_viento);

    // Habilitación del selector de tipo de mapa de calor del informe de energía activa
    var funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_energia_activa = function() {
        funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion("sensores_informacion_energia_activa");
    };
    $("#intervalo_valores_sensores_informacion_energia_activa").change(funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_energia_activa);

    // Habilitación del selector de tipo de mapa de calor del informe de energía reactiva
    var funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_energia_reactiva = function() {
        funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion("sensores_informacion_energia_reactiva");
    };
    $("#intervalo_valores_sensores_informacion_energia_reactiva").change(funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_energia_reactiva);

    // Habilitación del selector de tipo de mapa de calor del informe de compra de energía
    var funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_compra_energia = function() {
        funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion("sensores_informacion_compra_energia");
    };
    $("#intervalo_valores_sensores_informacion_compra_energia").change(funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_compra_energia);

    // Habilitación del selector de tipo de mapa de calor del informe de gas
    var funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_gas = function() {
        funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion("sensores_informacion_gas");
    };
    $("#intervalo_valores_sensores_informacion_gas").change(funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_gas);

    // Habilitación del selector de tipo de mapa de calor del informe de agua
    var funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_agua = function() {
        funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion("sensores_informacion_agua");
    };
    $("#intervalo_valores_sensores_informacion_agua").change(funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_agua);

    // Habilitación del selector de tipo de mapa de calor del informe genérico
    var funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_generica = function() {
        funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion("sensores_informacion_generica");
    };
    $("#intervalo_valores_sensores_informacion_generica").change(funcion_habilita_tipo_mapa_calor_intervalo_valores_informacion_generica);

    // Recarga los campos de valores de clase genérica según el intervalo de valores
    var funcion_recarga_campos_intervalo_valores_sensores_informacion_generica = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_informacion_generica").val();
        funcion_recarga_campos_intervalo_valores("sensores_informacion_generica", true, intervalo_valores);
    };
    $("#intervalo_valores_sensores_informacion_generica").change(funcion_recarga_campos_intervalo_valores_sensores_informacion_generica);
};


establece_eventos_secciones_sensores_informes_analisis = function() {
    // Desactivación de eventos anteriores
    $("#clase_sensor_sensores_analisis_horario").off();
    $("#clase_sensor_sensores_analisis_diario").off();
    $("#clase_sensor_sensores_analisis_comportamiento").off();
    $("#campo_sensores_analisis_horario").off();
    $("#campo_sensores_analisis_diario").off();
    $("#campo_sensores_analisis_comportamiento").off();

    // Mostrar lista doble para la selección de sensores de análisis de comportamiento
    if ($('#select_sensores_no_visible_sensores_analisis_comportamiento').length) {
        $('#select_sensores_no_visible_sensores_analisis_comportamiento').attr("id", "select_sensores_visible_sensores_analisis_comportamiento");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_sensores_analisis_comportamiento", true);
    };

    // Deshabilitación inicial de listas desplegables de sensores
    $("#id_sensor_sensores_analisis_horario").attr('disabled', true).trigger("chosen:updated");
    $("#id_sensor_sensores_analisis_diario").attr('disabled', true).trigger("chosen:updated");

    // Recarga de los sensores y los campos de una clase de la sección análisis horario
    var funcion_recarga_sensores_campos_clase_analisis_horario = function() {
        funcion_recarga_sensores_campos_clase("sensores_analisis_horario", false, null);
    };
    $("#clase_sensor_sensores_analisis_horario").change(funcion_recarga_sensores_campos_clase_analisis_horario);

    // Recarga de los sensores y los campos de una clase de la sección análisis diario
    var funcion_recarga_sensores_campos_clase_analisis_diario = function() {
        funcion_recarga_sensores_campos_clase("sensores_analisis_diario", false, null);
    };
    $("#clase_sensor_sensores_analisis_diario").change(funcion_recarga_sensores_campos_clase_analisis_diario);

    // Recarga de los sensores de una clase de sensor de la sección análisis de comportamiento
    var funcion_recarga_lista_doble_sensores_campos_clase_analisis_comportamiento = function() {
        var id_controles = "sensores_analisis_comportamiento";
        funcion_recarga_lista_doble_sensores_campos_clase(
            id_controles,
            false,
            null);
    };
    $("#clase_sensor_sensores_analisis_comportamiento").change(funcion_recarga_lista_doble_sensores_campos_clase_analisis_comportamiento);

    // Recarga de los campos de una clase de sensor de la sección análisis de comportamiento
    var funcion_recarga_campos_clase_sensor_analisis_comportamiento = function() {
        funcion_recarga_campos_clase_sensor("sensores_analisis_comportamiento", false, null);
    };
    $("#clase_sensor_sensores_analisis_comportamiento").change(funcion_recarga_campos_clase_sensor_analisis_comportamiento);

    // Control de parámetros extra de campo
    var funcion_muestra_control_parametros_extra_campo_sensores_analisis_horario = function() {
        var clase_sensor = $("#clase_sensor_sensores_analisis_horario").val();
        funcion_muestra_control_parametros_extra_campo_informe("sensores_analisis_horario", clase_sensor);
    };
    $("#campo_sensores_analisis_horario").show(funcion_muestra_control_parametros_extra_campo_sensores_analisis_horario);
    $("#campo_sensores_analisis_horario").change(funcion_muestra_control_parametros_extra_campo_sensores_analisis_horario);

    var funcion_muestra_control_parametros_extra_campo_sensores_analisis_diario = function() {
        var clase_sensor = $("#clase_sensor_sensores_analisis_diario").val();
        funcion_muestra_control_parametros_extra_campo_informe("sensores_analisis_diario", clase_sensor);
    };
    $("#campo_sensores_analisis_diario").show(funcion_muestra_control_parametros_extra_campo_sensores_analisis_diario);
    $("#campo_sensores_analisis_diario").change(funcion_muestra_control_parametros_extra_campo_sensores_analisis_diario);

    var funcion_muestra_control_parametros_extra_campo_sensores_analisis_comportamiento = function() {
        var clase_sensor = $("#clase_sensor_sensores_analisis_comportamiento").val();
        funcion_muestra_control_parametros_extra_campo_informe("sensores_analisis_comportamiento", clase_sensor);
    };
    $("#campo_sensores_analisis_comportamiento").show(funcion_muestra_control_parametros_extra_campo_sensores_analisis_comportamiento);
    $("#campo_sensores_analisis_comportamiento").change(funcion_muestra_control_parametros_extra_campo_sensores_analisis_comportamiento);
};


establece_eventos_secciones_sensores_informes_comparacion = function() {
    // Desactivación de eventos anteriores
    $("#clase_sensor_sensores_comparacion_periodos").off();
    $("#clase_sensor_sensores_comparacion_perfil_horario").off();
    $("#clase_sensor_sensores_comparacion_campos_iguales").off();
    $("#clase_sensor_1_sensores_comparacion_campos_diferentes").off();
    $("#clase_sensor_2_sensores_comparacion_campos_diferentes").off();
    $("#clase_sensor_3_sensores_comparacion_campos_diferentes").off();
    $("#clase_sensor_4_sensores_comparacion_campos_diferentes").off();
    $("#clase_sensor_5_sensores_comparacion_campos_diferentes").off();
    $("#clase_sensor_sensores_analisis_comparativo").off();
    $("#clase_sensor_1_sensores_valores_generales").off();
    $("#clase_sensor_2_sensores_valores_generales").off();
    $("#clase_sensor_3_sensores_valores_generales").off();
    $("#clase_sensor_1_sensores_incrementos_totales").off();
    $("#clase_sensor_2_sensores_incrementos_totales").off();
    $("#clase_sensor_3_sensores_incrementos_totales").off();
    $("#campo_sensores_comparacion_periodos").off();
    $("#campo_sensores_comparacion_perfil_horario").off();
    $("#campo_sensores_comparacion_campos_iguales").off();
    $("#campo_1_sensores_comparacion_campos_diferentes").off();
    $("#campo_2_sensores_comparacion_campos_diferentes").off();
    $("#campo_3_sensores_comparacion_campos_diferentes").off();
    $("#campo_4_sensores_comparacion_campos_diferentes").off();
    $("#campo_5_sensores_comparacion_campos_diferentes").off();
    $("#campo_sensores_analisis_comparativo").off();
    $("#campo_1_sensores_valores_generales").off();
    $("#campo_2_sensores_valores_generales").off();
    $("#campo_3_sensores_valores_generales").off();
    $("#campo_1_sensores_incrementos_totales").off();
    $("#campo_2_sensores_incrementos_totales").off();
    $("#campo_3_sensores_incrementos_totales").off();
    $("#intervalo_valores_sensores_comparacion_periodos").off();
    $("#intervalo_valores_sensores_comparacion_perfil_horario").off();
    $("#intervalo_valores_sensores_comparacion_campos_iguales").off();
    $("#intervalo_valores_sensores_analisis_comparativo").off();
    $("#intervalo_valores_sensores_valores_generales").off();
    $("#intervalo_valores_sensores_incrementos_totales").off();
    $("#tipo_perfil_horario_sensores_comparacion_perfil_horario").off();
    $("#agregacion_sensores_valores_generales").off();
    $("#agregacion_sensores_incrementos_totales").off();
    $("#tipo_mapa_calor_analisis_comparativo").off();

    // Habilitación de selección de ratio en comparacion
    $('#pestanyas-comparacion-sensores').off('shown.bs.tab');
    $('#pestanyas-comparacion-sensores').on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var href_pestanya_activa = $('#pestanyas-comparacion-sensores .active > a').attr('href');
        var id_pestanya_activa = href_pestanya_activa.replace("#tab-", "");
        switch (id_pestanya_activa) {
            case "comparacion-perfil-horario": {
                $('#control_id_ratio_seleccion_localizacion_actual').hide();
                break;
            }
            default: {
                $('#control_id_ratio_seleccion_localizacion_actual').show();
                break;
            }
        }
    });

    // Mostrar lista doble para la selección de sensores de comparación de campos iguales
    if ($('#select_sensores_no_visible_sensores_comparacion_campos_iguales').length) {
        $('#select_sensores_no_visible_sensores_comparacion_campos_iguales').attr("id", "select_sensores_visible_sensores_comparacion_campos_iguales");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_sensores_comparacion_campos_iguales", true);
    };

    // Mostrar lista doble para la selección de sensores de comparación de análisis comparativo
    if ($('#select_sensores_no_visible_sensores_analisis_comparativo').length) {
        $('#select_sensores_no_visible_sensores_analisis_comparativo').attr("id", "select_sensores_visible_sensores_analisis_comparativo");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_sensores_analisis_comparativo", true);
    };

    // Mostrar lista doble para la selección de sensores de comparación de valores generales
    if ($('#select_sensores_no_visible_sensores_valores_generales').length) {
        $('#select_sensores_no_visible_sensores_valores_generales').attr("id", "select_sensores_visible_sensores_valores_generales");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_sensores_valores_generales", true);
    };

    // Mostrar lista doble para la selección de sensores de comparación de incrementos totales
    if ($('#select_sensores_no_visible_sensores_incrementos_totales').length) {
        $('#select_sensores_no_visible_sensores_incrementos_totales').attr("id", "select_sensores_visible_sensores_incrementos_totales");
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_sensores_incrementos_totales", true);
    };

    // Deshabilitación inicial de listas desplegables de sensores
    $("#id_sensor_sensores_comparacion_periodos").attr('disabled', true).trigger("chosen:updated");
    $("#id_sensor_sensores_comparacion_perfil_horario").attr('disabled', true).trigger("chosen:updated");
    $("#id_sensor_sensores_comparacion_campos_iguales").attr('disabled', true).trigger("chosen:updated");
    $("#id_sensor_1_sensores_comparacion_campos_diferentes").attr('disabled', true).trigger("chosen:updated");
    $("#id_sensor_2_sensores_comparacion_campos_diferentes").attr('disabled', true).trigger("chosen:updated");
    $("#id_sensor_3_sensores_comparacion_campos_diferentes").attr('disabled', true).trigger("chosen:updated");
    $("#id_sensor_4_sensores_comparacion_campos_diferentes").attr('disabled', true).trigger("chosen:updated");
    $("#id_sensor_5_sensores_comparacion_campos_diferentes").attr('disabled', true).trigger("chosen:updated");
    $("#id_sensor_sensores_analisis_comparativo").attr('disabled', true).trigger("chosen:updated");

    // Recarga de los sensores y los campos de una clase del informe de comparación de periodos
    var funcion_recarga_sensores_campos_clase_comparacion_periodos = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_comparacion_periodos").val();
        funcion_recarga_sensores_campos_clase("sensores_comparacion_periodos", true, intervalo_valores);
    };
    $("#clase_sensor_sensores_comparacion_periodos").change(funcion_recarga_sensores_campos_clase_comparacion_periodos);

    // Recarga de los sensores y los campos de una clase del informe de comparación con perfil horario
    var funcion_recarga_sensores_campos_clase_comparacion_perfil_horario = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_comparacion_perfil_horario").val();
        funcion_recarga_sensores_campos_clase("sensores_comparacion_perfil_horario", true, intervalo_valores);
    };
    $("#clase_sensor_sensores_comparacion_perfil_horario").change(funcion_recarga_sensores_campos_clase_comparacion_perfil_horario);

    // Recarga de los sensores y los campos de una clase del informe de comparación de campos iguales
    var funcion_recarga_sensores_campos_clase_comparacion_campos_iguales = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_comparacion_campos_iguales").val();
        funcion_recarga_sensores_campos_clase("sensores_comparacion_campos_iguales", true, intervalo_valores);
    };
    $("#clase_sensor_sensores_comparacion_campos_iguales").change(funcion_recarga_sensores_campos_clase_comparacion_campos_iguales);

    // Recarga de los sensores y los campos de una clase del informe de comparación de campos diferentes
    var funcion_recarga_sensores_campos_clase_1_sensores_comparacion_campos_diferentes = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_comparacion_campos_diferentes").val();
        funcion_recarga_sensores_campos_clase("1_sensores_comparacion_campos_diferentes", true, intervalo_valores);
    };
    $("#clase_sensor_1_sensores_comparacion_campos_diferentes").change(funcion_recarga_sensores_campos_clase_1_sensores_comparacion_campos_diferentes);
    var funcion_recarga_sensores_campos_clase_2_sensores_comparacion_campos_diferentes = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_comparacion_campos_diferentes").val();
        funcion_recarga_sensores_campos_clase("2_sensores_comparacion_campos_diferentes", true, intervalo_valores);
    };
    $("#clase_sensor_2_sensores_comparacion_campos_diferentes").change(funcion_recarga_sensores_campos_clase_2_sensores_comparacion_campos_diferentes);
    var funcion_recarga_sensores_campos_clase_3_sensores_comparacion_campos_diferentes = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_comparacion_campos_diferentes").val();
        funcion_recarga_sensores_campos_clase("3_sensores_comparacion_campos_diferentes", true, intervalo_valores);
    };
    $("#clase_sensor_3_sensores_comparacion_campos_diferentes").change(funcion_recarga_sensores_campos_clase_3_sensores_comparacion_campos_diferentes);
    var funcion_recarga_sensores_campos_clase_4_sensores_comparacion_campos_diferentes = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_comparacion_campos_diferentes").val();
        funcion_recarga_sensores_campos_clase("4_sensores_comparacion_campos_diferentes", true, intervalo_valores);
    };
    $("#clase_sensor_4_sensores_comparacion_campos_diferentes").change(funcion_recarga_sensores_campos_clase_4_sensores_comparacion_campos_diferentes);
    var funcion_recarga_sensores_campos_clase_5_sensores_comparacion_campos_diferentes = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_comparacion_campos_diferentes").val();
        funcion_recarga_sensores_campos_clase("5_sensores_comparacion_campos_diferentes", true, intervalo_valores);
    };
    $("#clase_sensor_5_sensores_comparacion_campos_diferentes").change(funcion_recarga_sensores_campos_clase_5_sensores_comparacion_campos_diferentes);

    // Recarga de los sensores y los campos de una clase del informe de comparación de análisis comparativo
    var funcion_recarga_sensores_campos_clase_analisis_comparativo = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_analisis_comparativo").val();
        funcion_recarga_sensores_campos_clase("sensores_analisis_comparativo", true, intervalo_valores);
    };
    $("#clase_sensor_sensores_analisis_comparativo").change(funcion_recarga_sensores_campos_clase_analisis_comparativo);

    // Recarga de los sensores de una clase de sensor del informe de comparación de campos iguales
    var funcion_recarga_lista_doble_sensores_clase_comparacion_campos_iguales = function() {
        var id_controles = "sensores_comparacion_campos_iguales";
        var clase_sensor = $("#clase_sensor_sensores_comparacion_campos_iguales").val();
        funcion_recarga_lista_doble_sensores_clase(
            id_controles,
            clase_sensor);
    };
    $("#clase_sensor_sensores_comparacion_campos_iguales").change(funcion_recarga_lista_doble_sensores_clase_comparacion_campos_iguales);

    // Recarga de los sensores de una clase de sensor del informe de comparación de análisis comparativo
    var funcion_recarga_lista_doble_sensores_clase_analisis_comparativo = function() {
        var id_controles = "sensores_analisis_comparativo";
        var clase_sensor = $("#clase_sensor_sensores_analisis_comparativo").val();
        funcion_recarga_lista_doble_sensores_clase(
            id_controles,
            clase_sensor);
    };
    $("#clase_sensor_sensores_analisis_comparativo").change(funcion_recarga_lista_doble_sensores_clase_analisis_comparativo);

    // Recarga de los sensores de una clase de sensor del informe de comparación de valores generales
    var funcion_recarga_lista_doble_sensores_clases_valores_generales = function() {
        var id_controles = "sensores_valores_generales";
        var clases_sensor = [];
        for (var i = 0; i < NUMERO_CLASES_SENSOR_VALORES_GENERALES; i++) {
            var numero_clase_sensor = i + 1;
            var id_lista_clases_sensor = "clase_sensor_" + numero_clase_sensor + "_" + id_controles;
            var clase_sensor = $("#" + id_lista_clases_sensor).val();
            clases_sensor.push(clase_sensor);
        }
        funcion_recarga_lista_doble_sensores_clases(
            id_controles,
            clases_sensor);
    };
    $("#clase_sensor_1_sensores_valores_generales").change(funcion_recarga_lista_doble_sensores_clases_valores_generales);
    $("#clase_sensor_2_sensores_valores_generales").change(funcion_recarga_lista_doble_sensores_clases_valores_generales);
    $("#clase_sensor_3_sensores_valores_generales").change(funcion_recarga_lista_doble_sensores_clases_valores_generales);

    // Recarga de los sensores de una clase de sensor del informe de comparación de incrementos totales
    var funcion_recarga_lista_doble_sensores_clases_incrementos_totales = function() {
        var id_controles = "sensores_incrementos_totales";
        var clases_sensor = [];
        for (var i = 0; i < NUMERO_CLASES_SENSOR_INCREMENTOS_TOTALES; i++) {
            var numero_clase_sensor = i + 1;
            var id_lista_clases_sensor = "clase_sensor_" + numero_clase_sensor + "_" + id_controles;
            var clase_sensor = $("#" + id_lista_clases_sensor).val();
            clases_sensor.push(clase_sensor);
        }
        funcion_recarga_lista_doble_sensores_clases(
            id_controles,
            clases_sensor);
    };
    $("#clase_sensor_1_sensores_incrementos_totales").change(funcion_recarga_lista_doble_sensores_clases_incrementos_totales);
    $("#clase_sensor_2_sensores_incrementos_totales").change(funcion_recarga_lista_doble_sensores_clases_incrementos_totales);
    $("#clase_sensor_3_sensores_incrementos_totales").change(funcion_recarga_lista_doble_sensores_clases_incrementos_totales);

    // Recarga de los campos de una clase de sensor del informe de comparación de valores generales
    var funcion_recarga_campos_clase_sensor_1_valores_generales = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_valores_generales").val();
        funcion_recarga_campos_clase_sensor("1_sensores_valores_generales", true, intervalo_valores);
    };
    $("#clase_sensor_1_sensores_valores_generales").change(funcion_recarga_campos_clase_sensor_1_valores_generales);
    var funcion_recarga_campos_clase_sensor_2_valores_generales = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_valores_generales").val();
        funcion_recarga_campos_clase_sensor("2_sensores_valores_generales", true, intervalo_valores);
    };
    $("#clase_sensor_2_sensores_valores_generales").change(funcion_recarga_campos_clase_sensor_2_valores_generales);
    var funcion_recarga_campos_clase_sensor_3_valores_generales = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_valores_generales").val();
        funcion_recarga_campos_clase_sensor("3_sensores_valores_generales", true, intervalo_valores);
    };
    $("#clase_sensor_3_sensores_valores_generales").change(funcion_recarga_campos_clase_sensor_3_valores_generales);

    // Recarga de los campos de una clase de sensor del informe de comparación de incrementos totales
    var funcion_recarga_campos_clase_sensor_1_incrementos_totales = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_incrementos_totales").val();
        funcion_recarga_campos_incrementos_clase_sensor("1_sensores_incrementos_totales", intervalo_valores);
    };
    $("#clase_sensor_1_sensores_incrementos_totales").change(funcion_recarga_campos_clase_sensor_1_incrementos_totales);
    var funcion_recarga_campos_clase_sensor_2_incrementos_totales = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_incrementos_totales").val();
        funcion_recarga_campos_incrementos_clase_sensor("2_sensores_incrementos_totales", intervalo_valores);
    };
    $("#clase_sensor_2_sensores_incrementos_totales").change(funcion_recarga_campos_clase_sensor_2_incrementos_totales);
    var funcion_recarga_campos_clase_sensor_3_incrementos_totales = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_incrementos_totales").val();
        funcion_recarga_campos_incrementos_clase_sensor("3_sensores_incrementos_totales", intervalo_valores);
    };
    $("#clase_sensor_3_sensores_incrementos_totales").change(funcion_recarga_campos_clase_sensor_3_incrementos_totales);

    // Control de parámetros extra de campo
    var funcion_muestra_control_parametros_extra_campo_sensores_comparacion_periodos = function() {
        var clase_sensor = $("#clase_sensor_sensores_comparacion_periodos").val();
        funcion_muestra_control_parametros_extra_campo_informe("sensores_comparacion_periodos", clase_sensor);
    };
    $("#campo_sensores_comparacion_periodos").show(funcion_muestra_control_parametros_extra_campo_sensores_comparacion_periodos);
    $("#campo_sensores_comparacion_periodos").change(funcion_muestra_control_parametros_extra_campo_sensores_comparacion_periodos);

    var funcion_muestra_control_parametros_extra_campo_sensores_comparacion_perfil_horario = function() {
        var clase_sensor = $("#clase_sensor_sensores_comparacion_perfil_horario").val();
        funcion_muestra_control_parametros_extra_campo_informe("sensores_comparacion_perfil_horario", clase_sensor);
    };
    $("#campo_sensores_comparacion_perfil_horario").show(funcion_muestra_control_parametros_extra_campo_sensores_comparacion_perfil_horario);
    $("#campo_sensores_comparacion_perfil_horario").change(funcion_muestra_control_parametros_extra_campo_sensores_comparacion_perfil_horario);

    var funcion_muestra_control_parametros_extra_campo_sensores_comparacion_campos_iguales = function() {
        var clase_sensor = $("#clase_sensor_sensores_comparacion_campos_iguales").val();
        funcion_muestra_control_parametros_extra_campo_informe("sensores_comparacion_campos_iguales", clase_sensor);
    };
    $("#campo_sensores_comparacion_campos_iguales").show(funcion_muestra_control_parametros_extra_campo_sensores_comparacion_campos_iguales);
    $("#campo_sensores_comparacion_campos_iguales").change(funcion_muestra_control_parametros_extra_campo_sensores_comparacion_campos_iguales);

    var funcion_muestra_control_parametros_extra_campo_1_sensores_comparacion_campos_diferentes = function() {
        var clase_sensor = $("#clase_sensor_1_sensores_comparacion_campos_diferentes").val();
        funcion_muestra_control_parametros_extra_campo_informe("1_sensores_comparacion_campos_diferentes", clase_sensor);
    };
    $("#campo_1_sensores_comparacion_campos_diferentes").show(funcion_muestra_control_parametros_extra_campo_1_sensores_comparacion_campos_diferentes);
    $("#campo_1_sensores_comparacion_campos_diferentes").change(funcion_muestra_control_parametros_extra_campo_1_sensores_comparacion_campos_diferentes);
    var funcion_muestra_control_parametros_extra_campo_2_sensores_comparacion_campos_diferentes = function() {
        var clase_sensor = $("#clase_sensor_2_sensores_comparacion_campos_diferentes").val();
        funcion_muestra_control_parametros_extra_campo_informe("2_sensores_comparacion_campos_diferentes", clase_sensor);
    };
    $("#campo_2_sensores_comparacion_campos_diferentes").show(funcion_muestra_control_parametros_extra_campo_2_sensores_comparacion_campos_diferentes);
    $("#campo_2_sensores_comparacion_campos_diferentes").change(funcion_muestra_control_parametros_extra_campo_2_sensores_comparacion_campos_diferentes);
    var funcion_muestra_control_parametros_extra_campo_3_sensores_comparacion_campos_diferentes = function() {
        var clase_sensor = $("#clase_sensor_3_sensores_comparacion_campos_diferentes").val();
        funcion_muestra_control_parametros_extra_campo_informe("3_sensores_comparacion_campos_diferentes", clase_sensor);
    };
    $("#campo_3_sensores_comparacion_campos_diferentes").show(funcion_muestra_control_parametros_extra_campo_3_sensores_comparacion_campos_diferentes);
    $("#campo_3_sensores_comparacion_campos_diferentes").change(funcion_muestra_control_parametros_extra_campo_3_sensores_comparacion_campos_diferentes);
    var funcion_muestra_control_parametros_extra_campo_4_sensores_comparacion_campos_diferentes = function() {
        var clase_sensor = $("#clase_sensor_4_sensores_comparacion_campos_diferentes").val();
        funcion_muestra_control_parametros_extra_campo_informe("4_sensores_comparacion_campos_diferentes", clase_sensor);
    };
    $("#campo_4_sensores_comparacion_campos_diferentes").show(funcion_muestra_control_parametros_extra_campo_4_sensores_comparacion_campos_diferentes);
    $("#campo_4_sensores_comparacion_campos_diferentes").change(funcion_muestra_control_parametros_extra_campo_4_sensores_comparacion_campos_diferentes);
    var funcion_muestra_control_parametros_extra_campo_5_sensores_comparacion_campos_diferentes = function() {
        var clase_sensor = $("#clase_sensor_5_sensores_comparacion_campos_diferentes").val();
        funcion_muestra_control_parametros_extra_campo_informe("5_sensores_comparacion_campos_diferentes", clase_sensor);
    };
    $("#campo_5_sensores_comparacion_campos_diferentes").show(funcion_muestra_control_parametros_extra_campo_5_sensores_comparacion_campos_diferentes);
    $("#campo_5_sensores_comparacion_campos_diferentes").change(funcion_muestra_control_parametros_extra_campo_5_sensores_comparacion_campos_diferentes);

    var funcion_muestra_control_parametros_extra_campo_sensores_analisis_comparativo = function() {
        var clase_sensor = $("#clase_sensor_sensores_analisis_comparativo").val();
        funcion_muestra_control_parametros_extra_campo_informe("sensores_analisis_comparativo", clase_sensor);
    };
    $("#campo_sensores_analisis_comparativo").show(funcion_muestra_control_parametros_extra_campo_sensores_analisis_comparativo);
    $("#campo_sensores_analisis_comparativo").change(funcion_muestra_control_parametros_extra_campo_sensores_analisis_comparativo);

    var funcion_muestra_control_parametros_extra_campo_1_sensores_valores_generales = function() {
        var clase_sensor = $("#clase_sensor_1_sensores_valores_generales").val();
        funcion_muestra_control_parametros_extra_campo_informe("1_sensores_valores_generales", clase_sensor);
    };
    $("#campo_1_sensores_valores_generales").show(funcion_muestra_control_parametros_extra_campo_1_sensores_valores_generales);
    $("#campo_1_sensores_valores_generales").change(funcion_muestra_control_parametros_extra_campo_1_sensores_valores_generales);
    var funcion_muestra_control_parametros_extra_campo_2_sensores_valores_generales = function() {
        var clase_sensor = $("#clase_sensor_2_sensores_valores_generales").val();
        funcion_muestra_control_parametros_extra_campo_informe("2_sensores_valores_generales", clase_sensor);
    };
    $("#campo_2_sensores_valores_generales").show(funcion_muestra_control_parametros_extra_campo_2_sensores_valores_generales);
    $("#campo_2_sensores_valores_generales").change(funcion_muestra_control_parametros_extra_campo_2_sensores_valores_generales);
    var funcion_muestra_control_parametros_extra_campo_3_sensores_valores_generales = function() {
        var clase_sensor = $("#clase_sensor_3_sensores_valores_generales").val();
        funcion_muestra_control_parametros_extra_campo_informe("3_sensores_valores_generales", clase_sensor);
    };
    $("#campo_3_sensores_valores_generales").show(funcion_muestra_control_parametros_extra_campo_3_sensores_valores_generales);
    $("#campo_3_sensores_valores_generales").change(funcion_muestra_control_parametros_extra_campo_3_sensores_valores_generales);

    var funcion_muestra_control_parametros_extra_campo_1_sensores_incrementos_totales = function() {
        var clase_sensor = $("#clase_sensor_1_sensores_incrementos_totales").val();
        funcion_muestra_control_parametros_extra_campo_informe("1_sensores_incrementos_totales", clase_sensor);
    };
    $("#campo_1_sensores_incrementos_totales").show(funcion_muestra_control_parametros_extra_campo_1_sensores_incrementos_totales);
    $("#campo_1_sensores_incrementos_totales").change(funcion_muestra_control_parametros_extra_campo_1_sensores_incrementos_totales);
    var funcion_muestra_control_parametros_extra_campo_2_sensores_incrementos_totales = function() {
        var clase_sensor = $("#clase_sensor_2_sensores_incrementos_totales").val();
        funcion_muestra_control_parametros_extra_campo_informe("2_sensores_incrementos_totales", clase_sensor);
    };
    $("#campo_2_sensores_incrementos_totales").show(funcion_muestra_control_parametros_extra_campo_2_sensores_incrementos_totales);
    $("#campo_2_sensores_incrementos_totales").change(funcion_muestra_control_parametros_extra_campo_2_sensores_incrementos_totales);
    var funcion_muestra_control_parametros_extra_campo_3_sensores_incrementos_totales = function() {
        var clase_sensor = $("#clase_sensor_3_sensores_incrementos_totales").val();
        funcion_muestra_control_parametros_extra_campo_informe("3_sensores_incrementos_totales", clase_sensor);
    };
    $("#campo_3_sensores_incrementos_totales").show(funcion_muestra_control_parametros_extra_campo_3_sensores_incrementos_totales);
    $("#campo_3_sensores_incrementos_totales").change(funcion_muestra_control_parametros_extra_campo_3_sensores_incrementos_totales);

    // Habilita la lista de intervalos de valores de comparación de periodos
    var funcion_habilita_intervalos_valores_comparacion_periodos = function() {
        funcion_habilita_intervalos_valores_informes("comparacion_periodos");
    };
    $("#intervalo_valores_sensores_comparacion_periodos").show(funcion_habilita_intervalos_valores_comparacion_periodos);
    $("#intervalo_valores_sensores_comparacion_periodos").change(funcion_habilita_intervalos_valores_comparacion_periodos);

    // Habilita la lista de intervalos de valores de comparación de campos iguales
    var funcion_habilita_intervalos_valores_comparacion_campos_iguales = function() {
        funcion_habilita_intervalos_valores_informes("comparacion_campos_iguales");
    };
    $("#intervalo_valores_sensores_comparacion_campos_iguales").show(funcion_habilita_intervalos_valores_comparacion_campos_iguales);
    $("#intervalo_valores_sensores_comparacion_campos_iguales").change(funcion_habilita_intervalos_valores_comparacion_campos_iguales);

    // Habilita la lista de intervalos de valores de comparación de valores generales
    var funcion_habilita_intervalos_valores_comparacion_valores_generales = function() {
        funcion_habilita_intervalos_valores_informes("valores_generales");
    };
    $("#intervalo_valores_sensores_valores_generales").show(funcion_habilita_intervalos_valores_comparacion_valores_generales);
    $("#intervalo_valores_sensores_valores_generales").change(funcion_habilita_intervalos_valores_comparacion_valores_generales);

    // Habilita la lista de intervalos de valores de comparación de incrementos totales
    var funcion_habilita_intervalos_valores_comparacion_incrementos_totales = function() {
        funcion_habilita_intervalos_valores_informes("incrementos_totales");
    };
    $("#intervalo_valores_sensores_incrementos_totales").show(funcion_habilita_intervalos_valores_comparacion_incrementos_totales);
    $("#intervalo_valores_sensores_incrementos_totales").change(funcion_habilita_intervalos_valores_comparacion_incrementos_totales);

    // Habilita la lista de intervalos de valores de comparación de análisis comparativo
    var funcion_habilita_intervalos_valores_analisis_comparativo = function() {
        funcion_habilita_intervalos_valores_informes("analisis_comparativo");
    };
    $("#intervalo_valores_sensores_analisis_comparativo").show(funcion_habilita_intervalos_valores_analisis_comparativo);
    $("#intervalo_valores_sensores_analisis_comparativo").change(funcion_habilita_intervalos_valores_analisis_comparativo);

    // Recarga de los intervalos de valores de comparación de periodos
    var funcion_recarga_intervalos_valores_comparacion_periodos = function() {
        var clase_sensor = $("#clase_sensor_sensores_comparacion_periodos").val();
        funcion_recarga_intervalos_valores_informes_informacion_comparacion("comparacion_periodos", clase_sensor);
    };
    $("#campo_sensores_comparacion_periodos").change(funcion_recarga_intervalos_valores_comparacion_periodos);

    // Recarga de los intervalos de valores de comparación de campos iguales
    var funcion_recarga_intervalos_valores_comparacion_campos_iguales = function() {
        var clase_sensor = $("#clase_sensor_sensores_comparacion_campos_iguales").val();
        funcion_recarga_intervalos_valores_informes_informacion_comparacion("comparacion_campos_iguales", clase_sensor);
    };
    $("#campo_sensores_comparacion_campos_iguales").change(funcion_recarga_intervalos_valores_comparacion_campos_iguales);

    // Recarga de los intervalos de valores de comparación de análisis comparativo
    var funcion_recarga_intervalos_valores_analisis_comparativo = function() {
        var clase_sensor = $("#clase_sensor_sensores_analisis_comparativo").val();
        var intervalo_valores = $("#intervalo_valores_sensores_analisis_comparativo").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_lista_intervalos_valores_informe_analisis_comparativo_clase_sensor.php", {
            clase_sensor: clase_sensor,
            intervalo_valores: intervalo_valores
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#intervalo_valores_sensores_analisis_comparativo").html(resultado.html);

            // Intervalo de valores por defecto
            if (intervalo_valores == INTERVALO_VALORES_NINGUNO) {
                intervalo_valores = INTERVALO_VALORES_HORA;
                $("#intervalo_valores_sensores_analisis_comparativo").val(intervalo_valores);
            }

            $("#intervalo_valores_sensores_analisis_comparativo").trigger("change");
        });
    };
    $("#campo_sensores_analisis_comparativo").change(funcion_recarga_intervalos_valores_analisis_comparativo);

    // Recarga la lista de intervalos de valores del informe de valores generales
    var funcion_recarga_intervalos_valores_comparacion_valores_generales = function() {
        var clase_sensor = $("#clase_sensor_1_sensores_valores_generales").val();
        var campo = $("#campo_1_sensores_valores_generales").val();
        var intervalo_valores = $("#intervalo_valores_sensores_valores_generales").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_lista_intervalos_valores_informe_valores_generales_clase_sensor_campo.php", {
            clase_sensor: clase_sensor,
            campo: campo,
            intervalo_valores: intervalo_valores
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#intervalo_valores_sensores_valores_generales").html(resultado.html);

            // Intervalo de valores por defecto
            if (intervalo_valores == INTERVALO_VALORES_NINGUNO) {
                switch (clase_sensor) {
                    case CLASE_SENSOR_CORTES_TENSION: {
                        intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL_LINEAS;
                        break;
                    }
                    default: {
                        intervalo_valores = INTERVALO_VALORES_HORA;
                        break;
                    }
                }
                $("#intervalo_valores_sensores_valores_generales").val(intervalo_valores);
            }

            $("#intervalo_valores_sensores_valores_generales").trigger("change");
        });
    };
    $("#campo_1_sensores_valores_generales").change(funcion_recarga_intervalos_valores_comparacion_valores_generales);

    // Recarga la lista de intervalos de valores de los informes de incrementos totales
    var funcion_recarga_intervalos_valores_comparacion_incrementos_totales = function() {
        var clase_sensor = $("#clase_sensor_1_sensores_incrementos_totales").val();
        var campo = $("#campo_1_sensores_incrementos_totales").val();
        var intervalo_valores = $("#intervalo_valores_sensores_incrementos_totales").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_lista_intervalos_valores_informe_incrementos_totales_clase_sensor_campo.php", {
            clase_sensor: clase_sensor,
            campo: campo,
            intervalo_valores: intervalo_valores
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#intervalo_valores_sensores_incrementos_totales").html(resultado.html);

            // Intervalo de valores por defecto
            if (intervalo_valores == INTERVALO_VALORES_NINGUNO) {
                switch (clase_sensor) {
                    case CLASE_SENSOR_CORTES_TENSION: {
                        intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
                        break;
                    }
                    default: {
                        intervalo_valores = INTERVALO_VALORES_HORA;
                        break;
                    }
                }
                $("#intervalo_valores_sensores_incrementos_totales").val(intervalo_valores);
            }

            $("#intervalo_valores_sensores_incrementos_totales").trigger("change");
        });
    };
    $("#campo_1_sensores_incrementos_totales").change(funcion_recarga_intervalos_valores_comparacion_incrementos_totales);

    // Habilitación del selector de tipo agregación de comparación de valores generales
    var funcion_habilita_agregacion_comparacion_valores_generales = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_agregaciones = $("select#agregacion_sensores_valores_generales option").length;
        if (numero_agregaciones <= 1) {
            $("#agregacion_sensores_valores_generales").attr('disabled', true);
        }
        else {
            $("#agregacion_sensores_valores_generales").removeAttr('disabled');
        }
    };
    $("#agregacion_sensores_valores_generales").show(funcion_habilita_agregacion_comparacion_valores_generales);
    $("#agregacion_sensores_valores_generales").change(funcion_habilita_agregacion_comparacion_valores_generales);

    // Habilitación del selector de tipo agregación de comparación de incrementos totales
    var funcion_habilita_agregacion_comparacion_incrementos_totales = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_agregaciones = $("select#agregacion_sensores_incrementos_totales option").length;
        if (numero_agregaciones <= 1) {
            $("#agregacion_sensores_incrementos_totales").attr('disabled', true);
        }
        else {
            $("#agregacion_sensores_incrementos_totales").removeAttr('disabled');
        }
    };
    $("#agregacion_sensores_incrementos_totales").show(funcion_habilita_agregacion_comparacion_incrementos_totales);
    $("#agregacion_sensores_incrementos_totales").change(funcion_habilita_agregacion_comparacion_incrementos_totales);

    // Recarga las agregaciones según la clase y el intervalo y el campo en el informe de comparación de valores generales
    var funcion_recarga_agregaciones_comparacion_valores_generales = function() {
        var clase_sensor = $("#clase_sensor_1_sensores_valores_generales").val();
        var campo = $("#campo_1_sensores_valores_generales").val();
        var intervalo_valores = $("#intervalo_valores_sensores_valores_generales").val();
        var agregacion = $("#agregacion_sensores_valores_generales").val();
        switch (intervalo_valores) {
            case INTERVALO_VALORES_NINGUNO:
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS: {
                $("#agregacion_sensores_valores_generales").val(AGREGACION_NINGUNA);
                $("#agregacion_sensores_valores_generales").attr('disabled', true);
                break;
            }
            default: {
                $.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_lista_agregaciones_campo_clase_sensor.php", {
                    clase_sensor: clase_sensor,
                    campo: campo,
                    tipos_agregacion: TIPOS_AGREGACION_TODOS,
                    agregacion: agregacion
                },
                function (data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }

                    $("#agregacion_sensores_valores_generales").html(resultado.html);
                    $("#agregacion_sensores_valores_generales").trigger("change");
                });
                break;
            }
        }
    };

    // Nota: Al cambiar el campo, se recarga siempre el intervalo (no hace falta añadir la función en el cambio de campo)
    $("#intervalo_valores_sensores_valores_generales").change(funcion_recarga_agregaciones_comparacion_valores_generales);

    // Recarga las agregaciones según la clase y el intervalo y el campo en el informe de comparación de incrementos totales
    var funcion_recarga_agregaciones_comparacion_incrementos_totales = function() {
        var clase_sensor = $("#clase_sensor_1_sensores_incrementos_totales").val();
        var campo = $("#campo_1_sensores_incrementos_totales").val();
        var intervalo_valores = $("#intervalo_valores_sensores_incrementos_totales").val();
        var agregacion = $("#agregacion_sensores_incrementos_totales").val();
        switch (intervalo_valores) {
            case INTERVALO_VALORES_NINGUNO:
            case INTERVALO_VALORES_TIEMPO_REAL: {
                $("#agregacion_sensores_incrementos_totales").val(AGREGACION_NINGUNA);
                $("#agregacion_sensores_incrementos_totales").attr('disabled', true);
                break;
            }
            default: {
                $.post("./src/modulos/ModulosWeb/ModuloSensores/Comparacion/dame_lista_agregaciones_campo_clase_sensor.php", {
                    clase_sensor: clase_sensor,
                    campo: campo,
                    tipos_agregacion: TIPOS_AGREGACION_CON_CLASES,
                    agregacion: agregacion
                },
                function (data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }

                    $("#agregacion_sensores_incrementos_totales").html(resultado.html);
                    $("#agregacion_sensores_incrementos_totales").trigger("change");
                });
                break;
            }
        }
    };

    // Nota: Al cambiar el campo, se recarga siempre el intervalo (no hace falta añadir la función en el cambio de campo)
    $("#intervalo_valores_sensores_incrementos_totales").change(funcion_recarga_agregaciones_comparacion_incrementos_totales);

    // Habilitación del selector de tipo de mapa de calor de la sección comparación
    var funcion_habilita_tipo_mapa_calor_intervalo_valores_comparacion = function(id_controles) {
        var intervalo_valores = $("#intervalo_valores_" + id_controles).val();
        switch (intervalo_valores) {
            case INTERVALO_VALORES_HORA: {
                $("#tipo_mapa_calor_" + id_controles).prop('disabled', false);
                break;
            }
            default: {
                $("#tipo_mapa_calor_" + id_controles).val(TIPO_MAPA_CALOR_NINGUNO);
                $("#tipo_mapa_calor_" + id_controles).prop('disabled', 'disabled');
                break;
            }
        }
    };

    // Habilitación del selector de tipo de mapa de calor del informe de comparación de periodos
    var funcion_habilita_tipo_mapa_calor_intervalo_valores_comparacion_periodos = function() {
        funcion_habilita_tipo_mapa_calor_intervalo_valores_comparacion("sensores_comparacion_periodos");
    };
    $("#intervalo_valores_sensores_comparacion_periodos").change(funcion_habilita_tipo_mapa_calor_intervalo_valores_comparacion_periodos);

    // Habilitación del selector de tipo de mapa de calor del informe de comparación con perfil horario
    var funcion_habilita_tipo_mapa_calor_intervalo_valores_comparacion_perfil_horario = function() {
        funcion_habilita_tipo_mapa_calor_intervalo_valores_comparacion("sensores_comparacion_perfil_horario");
    };
    $("#intervalo_valores_sensores_comparacion_perfil_horario").change(funcion_habilita_tipo_mapa_calor_intervalo_valores_comparacion_perfil_horario);

    // Habilitación del selector de tipo de mapa de calor del informe de comparación de campos iguales
    var funcion_habilita_tipo_mapa_calor_intervalo_valores_comparacion_campos_iguales = function() {
        funcion_habilita_tipo_mapa_calor_intervalo_valores_comparacion("sensores_comparacion_campos_iguales");
    };
    $("#intervalo_valores_sensores_comparacion_campos_iguales").change(funcion_habilita_tipo_mapa_calor_intervalo_valores_comparacion_campos_iguales);

    // Recarga los campos de valores de comparación de periodos según el intervalo de valores
    var funcion_recarga_campos_intervalo_valores_sensores_comparacion_periodos = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_comparacion_periodos").val();
        funcion_recarga_campos_intervalo_valores("sensores_comparacion_periodos", true, intervalo_valores);
    };
    $("#intervalo_valores_sensores_comparacion_periodos").change(funcion_recarga_campos_intervalo_valores_sensores_comparacion_periodos);

    // Recarga los campos de valores de comparación con perfil horario según el intervalo de valores
    var funcion_recarga_campos_intervalo_valores_sensores_comparacion_perfil_horario = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_comparacion_perfil_horario").val();
        funcion_recarga_campos_intervalo_valores("sensores_comparacion_perfil_horario", true, intervalo_valores);
    };
    $("#intervalo_valores_sensores_comparacion_perfil_horario").change(funcion_recarga_campos_intervalo_valores_sensores_comparacion_perfil_horario);

    // Recarga los campos de valores de comparación de valores iguales según el intervalo de valores
    var funcion_recarga_campos_intervalo_valores_sensores_comparacion_campos_iguales = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_comparacion_campos_iguales").val();
        funcion_recarga_campos_intervalo_valores("sensores_comparacion_campos_iguales", true, intervalo_valores);
    };
    $("#intervalo_valores_sensores_comparacion_campos_iguales").change(funcion_recarga_campos_intervalo_valores_sensores_comparacion_campos_iguales);

    // Recarga los campos de valores de comparación de valores diferentes según el intervalo de valores
    var funcion_recarga_campos_intervalo_valores_sensores_comparacion_campos_diferentes = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_comparacion_campos_diferentes").val();
        funcion_recarga_campos_intervalo_valores("1_sensores_comparacion_campos_diferentes", true, intervalo_valores);
        funcion_recarga_campos_intervalo_valores("2_sensores_comparacion_campos_diferentes", true, intervalo_valores);
        funcion_recarga_campos_intervalo_valores("3_sensores_comparacion_campos_diferentes", true, intervalo_valores);
        funcion_recarga_campos_intervalo_valores("4_sensores_comparacion_campos_diferentes", true, intervalo_valores);
        funcion_recarga_campos_intervalo_valores("5_sensores_comparacion_campos_diferentes", true, intervalo_valores);
    };
    $("#intervalo_valores_sensores_comparacion_campos_diferentes").change(funcion_recarga_campos_intervalo_valores_sensores_comparacion_campos_diferentes);

    // Recarga los campos de valores del informe de análisis comparativo según el intervalo de valores
    var funcion_recarga_campos_intervalo_valores_sensores_analisis_comparativo = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_analisis_comparativo").val();
        funcion_recarga_campos_intervalo_valores("sensores_analisis_comparativo", true, intervalo_valores);
    };
    $("#intervalo_valores_sensores_analisis_comparativo").change(funcion_recarga_campos_intervalo_valores_sensores_analisis_comparativo);

    // Recarga los campos de valores del informe de valores generales según el intervalo de valores
    var funcion_recarga_campos_intervalo_valores_sensores_valores_generales = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_valores_generales").val();
        funcion_recarga_campos_intervalo_valores("1_sensores_valores_generales", true, intervalo_valores);
        funcion_recarga_campos_intervalo_valores("2_sensores_valores_generales", true, intervalo_valores);
        funcion_recarga_campos_intervalo_valores("3_sensores_valores_generales", true, intervalo_valores);
    };
    $("#intervalo_valores_sensores_valores_generales").change(funcion_recarga_campos_intervalo_valores_sensores_valores_generales);

    // Recarga los campos de incrementos de valores del informe de incrementos totales según el intervalo de valores
    var funcion_recarga_campos_intervalo_valores_sensores_incrementos_totales = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_incrementos_totales").val();
        funcion_recarga_campos_intervalo_valores("1_sensores_incrementos_totales", true, intervalo_valores);
        funcion_recarga_campos_intervalo_valores("2_sensores_incrementos_totales", true, intervalo_valores);
        funcion_recarga_campos_intervalo_valores("3_sensores_incrementos_totales", true, intervalo_valores);
    };
    $("#intervalo_valores_sensores_incrementos_totales").change(funcion_recarga_campos_intervalo_valores_sensores_incrementos_totales);

    // Habilitación de agrupaciones de días en comparación con perfil horario
    var funcion_habilita_agrupaciones_dias_comparacion_perfil_horario = function() {
        var tipo_perfil_horario = $("#tipo_perfil_horario_sensores_comparacion_perfil_horario").val();
        switch (tipo_perfil_horario) {
            case TIPO_PERFIL_HORARIO_CONFIGURABLE: {
                $("#cadena_agrupaciones_dias_semana_sensores_comparacion_perfil_horario").val("1-2-3-4-5, 6-7");
                $("#control_cadena_agrupaciones_dias_semana_sensores_comparacion_perfil_horario").show();
                break;
            }
            default: {
                $("#cadena_agrupaciones_dias_semana_sensores_comparacion_perfil_horario").val("");
                $("#control_cadena_agrupaciones_dias_semana_sensores_comparacion_perfil_horario").hide();
                break;
            }
        }
    };
    $("#tipo_perfil_horario_sensores_comparacion_perfil_horario").show(funcion_habilita_agrupaciones_dias_comparacion_perfil_horario);
    $("#tipo_perfil_horario_sensores_comparacion_perfil_horario").change(funcion_habilita_agrupaciones_dias_comparacion_perfil_horario);

    // Habilitación del selector de tipo de mapa de calor del informe de comparación de análisis comparativo
    var funcion_habilita_tipo_mapa_calor_comparacion_analisis_comparativo = function() {
        var id_sensor = $("#id_sensor_sensores_analisis_comparativo").val();
        if (id_sensor == ID_NINGUNO) {
            $("#tipo_mapa_calor_sensores_analisis_comparativo").val(TIPO_MAPA_CALOR_NINGUNO);
            $("#tipo_mapa_calor_sensores_analisis_comparativo").prop('disabled', 'disabled');
        }
        else {
            $("#tipo_mapa_calor_sensores_analisis_comparativo").prop('disabled', false);
        }
    };
    $("#id_sensor_sensores_analisis_comparativo").show(funcion_habilita_tipo_mapa_calor_comparacion_analisis_comparativo);
    $("#id_sensor_sensores_analisis_comparativo").change(funcion_habilita_tipo_mapa_calor_comparacion_analisis_comparativo);
};


establece_eventos_secciones_sensores_informes_estadistica = function() {
    // Desactivación de eventos anteriores
    $("#clase_sensor_sensores_histograma").off();
    $("#clase_sensor_independiente_1_sensores_correlacion").off();
    $("#clase_sensor_independiente_2_sensores_correlacion").off();
    $("#clase_sensor_independiente_3_sensores_correlacion").off();
    $("#clase_sensor_independiente_4_sensores_correlacion").off();
    $("#clase_sensor_dependiente_sensores_correlacion").off();
    $("#campo_sensores_histograma").off();
    $("#campo_independiente_1_sensores_correlacion").off();
    $("#campo_independiente_2_sensores_correlacion").off();
    $("#campo_independiente_3_sensores_correlacion").off();
    $("#campo_independiente_4_sensores_correlacion").off();
    $("#campo_dependiente_sensores_correlacion").off();
    $("#intervalo_valores_sensores_histograma").off();
    $("#intervalo_valores_sensores_correlacion").off();

    // Deshabilitación inicial de listas desplegables de sensores
    $("#id_sensor_sensores_histograma").attr('disabled', true).trigger("chosen:updated");
    $("#id_sensor_independiente_1_sensores_correlacion").attr('disabled', true).trigger("chosen:updated");
    $("#id_sensor_independiente_2_sensores_correlacion").attr('disabled', true).trigger("chosen:updated");
    $("#id_sensor_independiente_3_sensores_correlacion").attr('disabled', true).trigger("chosen:updated");
    $("#id_sensor_independiente_4_sensores_correlacion").attr('disabled', true).trigger("chosen:updated");
    $("#id_sensor_dependiente_sensores_correlacion").attr('disabled', true).trigger("chosen:updated");

    // Recarga de los sensores y los campos de una clase de la sección histograma
    var funcion_recarga_sensores_campos_clase_histograma = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_histograma").val();
        funcion_recarga_sensores_campos_clase("sensores_histograma", true, intervalo_valores);
    };
    $("#clase_sensor_sensores_histograma").change(funcion_recarga_sensores_campos_clase_histograma);

    // Recarga de los sensores y los campos de una clase de la sección correlación
    var funcion_recarga_sensores_campos_clase_independiente_1_correlacion = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_correlacion").val();
        funcion_recarga_sensores_campos_clase("independiente_1_sensores_correlacion", true, intervalo_valores);
    };
    $("#clase_sensor_independiente_1_sensores_correlacion").change(funcion_recarga_sensores_campos_clase_independiente_1_correlacion);
    var funcion_recarga_sensores_campos_clase_independiente_2_correlacion = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_correlacion").val();
        funcion_recarga_sensores_campos_clase("independiente_2_sensores_correlacion", true, intervalo_valores);
    };
    $("#clase_sensor_independiente_2_sensores_correlacion").change(funcion_recarga_sensores_campos_clase_independiente_2_correlacion);
    var funcion_recarga_sensores_campos_clase_independiente_3_correlacion = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_correlacion").val();
        funcion_recarga_sensores_campos_clase("independiente_3_sensores_correlacion", true, intervalo_valores);
    };
    $("#clase_sensor_independiente_3_sensores_correlacion").change(funcion_recarga_sensores_campos_clase_independiente_3_correlacion);
    var funcion_recarga_sensores_campos_clase_independiente_4_correlacion = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_correlacion").val();
        funcion_recarga_sensores_campos_clase("independiente_4_sensores_correlacion", true, intervalo_valores);
    };
    $("#clase_sensor_independiente_4_sensores_correlacion").change(funcion_recarga_sensores_campos_clase_independiente_4_correlacion);
    var funcion_recarga_sensores_campos_clase_dependiente_correlacion = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_correlacion").val();
        funcion_recarga_sensores_campos_clase("dependiente_sensores_correlacion", true, intervalo_valores);
    };
    $("#clase_sensor_dependiente_sensores_correlacion").change(funcion_recarga_sensores_campos_clase_dependiente_correlacion);

    // Control de parámetros extra de campo
    var funcion_muestra_control_parametros_extra_campo_sensores_histograma = function() {
        var clase_sensor = $("#clase_sensor_sensores_histograma").val();
        funcion_muestra_control_parametros_extra_campo_informe("sensores_histograma", clase_sensor);
    };
    $("#campo_sensores_histograma").show(funcion_muestra_control_parametros_extra_campo_sensores_histograma);
    $("#campo_sensores_histograma").change(funcion_muestra_control_parametros_extra_campo_sensores_histograma);

    var funcion_muestra_control_parametros_extra_campo_independiente_1_sensores_correlacion = function() {
        var clase_sensor = $("#clase_sensor_independiente_1_sensores_correlacion").val();
        funcion_muestra_control_parametros_extra_campo_informe("independiente_1_sensores_correlacion", clase_sensor);
    };
    $("#campo_independiente_1_sensores_correlacion").show(funcion_muestra_control_parametros_extra_campo_independiente_1_sensores_correlacion);
    $("#campo_independiente_1_sensores_correlacion").change(funcion_muestra_control_parametros_extra_campo_independiente_1_sensores_correlacion);

    var funcion_muestra_control_parametros_extra_campo_independiente_2_sensores_correlacion = function() {
        var clase_sensor = $("#clase_sensor_independiente_2_sensores_correlacion").val();
        funcion_muestra_control_parametros_extra_campo_informe("independiente_2_sensores_correlacion", clase_sensor);
    };
    $("#campo_independiente_2_sensores_correlacion").show(funcion_muestra_control_parametros_extra_campo_independiente_2_sensores_correlacion);
    $("#campo_independiente_2_sensores_correlacion").change(funcion_muestra_control_parametros_extra_campo_independiente_2_sensores_correlacion);

    var funcion_muestra_control_parametros_extra_campo_independiente_3_sensores_correlacion = function() {
        var clase_sensor = $("#clase_sensor_independiente_3_sensores_correlacion").val();
        funcion_muestra_control_parametros_extra_campo_informe("independiente_3_sensores_correlacion", clase_sensor);
    };
    $("#campo_independiente_3_sensores_correlacion").show(funcion_muestra_control_parametros_extra_campo_independiente_3_sensores_correlacion);
    $("#campo_independiente_3_sensores_correlacion").change(funcion_muestra_control_parametros_extra_campo_independiente_3_sensores_correlacion);

    var funcion_muestra_control_parametros_extra_campo_independiente_4_sensores_correlacion = function() {
        var clase_sensor = $("#clase_sensor_independiente_4_sensores_correlacion").val();
        funcion_muestra_control_parametros_extra_campo_informe("independiente_4_sensores_correlacion", clase_sensor);
    };
    $("#campo_independiente_4_sensores_correlacion").show(funcion_muestra_control_parametros_extra_campo_independiente_4_sensores_correlacion);
    $("#campo_independiente_4_sensores_correlacion").change(funcion_muestra_control_parametros_extra_campo_independiente_4_sensores_correlacion);

    var funcion_muestra_control_parametros_extra_campo_dependiente_sensores_correlacion = function() {
        var clase_sensor = $("#clase_sensor_dependiente_sensores_correlacion").val();
        funcion_muestra_control_parametros_extra_campo_informe("dependiente_sensores_correlacion", clase_sensor);
    };
    $("#campo_dependiente_sensores_correlacion").show(funcion_muestra_control_parametros_extra_campo_dependiente_sensores_correlacion);
    $("#campo_dependiente_sensores_correlacion").change(funcion_muestra_control_parametros_extra_campo_dependiente_sensores_correlacion);

    // Habilita la lista de intervalos de valores del histograma
    var funcion_habilita_intervalos_valores_histograma = function() {
        funcion_habilita_intervalos_valores_informes("histograma");
    };
    $("#intervalo_valores_sensores_histograma").show(funcion_habilita_intervalos_valores_histograma);
    $("#intervalo_valores_sensores_histograma").change(funcion_habilita_intervalos_valores_histograma);

    // Recarga la lista de intervalos de valores según la clase y el campo seleccionada del histograma
    var funcion_recarga_intervalos_valores_clase_sensor_campo_histograma = function() {
        var clase_sensor = $("#clase_sensor_sensores_histograma").val();
        var campo = $("#campo_sensores_histograma").val();
        var intervalo_valores = $("#intervalo_valores_sensores_histograma").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/Estadistica/dame_lista_intervalos_valores_informe_histograma_clase_sensor_campo.php", {
            clase_sensor: clase_sensor,
            campo: campo,
            intervalo_valores: intervalo_valores
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#intervalo_valores_sensores_histograma").html(resultado.html);

            // Intervalo de valores por defecto
            if (intervalo_valores == INTERVALO_VALORES_NINGUNO) {
                switch (clase_sensor) {
                    case CLASE_SENSOR_ENERGIA_ACTIVA: {
                        intervalo_valores = INTERVALO_VALORES_HORA;
                        break;
                    }
                    case CLASE_SENSOR_CORTES_TENSION: {
                        intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
                        break;
                    }
                    default: {
                        intervalo_valores = INTERVALO_VALORES_HORA;
                        break;
                    }
                }
                $("#intervalo_valores_sensores_histograma").val(intervalo_valores);
            }

            // Notificación de cambio de intervalo de valores (para ejecutar las acciones correspondientes)
            $("#intervalo_valores_sensores_histograma").trigger('change');
        });
    };
    $("#campo_sensores_histograma").change(funcion_recarga_intervalos_valores_clase_sensor_campo_histograma);

    // Recarga los campos de valores de histograma según el intervalo de valores
    var funcion_recarga_campos_intervalo_valores_sensores_histograma = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_histograma").val();
        funcion_recarga_campos_intervalo_valores("sensores_histograma", true, intervalo_valores);
    };
    $("#intervalo_valores_sensores_histograma").change(funcion_recarga_campos_intervalo_valores_sensores_histograma);

    // Recarga los campos de valores de correlación según el intervalo de valores
    var funcion_recarga_campos_intervalo_valores_sensores_correlacion = function() {
        var intervalo_valores = $("#intervalo_valores_sensores_correlacion").val();
        funcion_recarga_campos_intervalo_valores("independiente_1_sensores_correlacion", true, intervalo_valores);
        funcion_recarga_campos_intervalo_valores("independiente_2_sensores_correlacion", true, intervalo_valores);
        funcion_recarga_campos_intervalo_valores("independiente_3_sensores_correlacion", true, intervalo_valores);
        funcion_recarga_campos_intervalo_valores("independiente_4_sensores_correlacion", true, intervalo_valores);
        funcion_recarga_campos_intervalo_valores("dependiente_sensores_correlacion", true, intervalo_valores);
    };
    $("#intervalo_valores_sensores_correlacion").change(funcion_recarga_campos_intervalo_valores_sensores_correlacion);
};


establece_eventos_ventanas_modales_sensores_herramientas = function() {
    // Desactivación de eventos anteriores
    $('#fichero_importacion_valores_sensor_text').off();
    $('#fichero_importacion_valores_sensor_file').off();
    $('#boton_importacion_valores_sensor_seleccionar_fichero').off();
    $('#clase_sensor_importacion_valores_sensor').off();
    $('#formato_fichero_valores_importacion_valores_sensor').off();
    $('#caracter_separador_importacion_valores_sensor').off();
    $('#numero_lineas_cabeceras_importacion_valores_sensor').off();
    $('#columna_fecha_importacion_valores_sensor').off();
    $('#formato_fecha_importacion_valores_sensor').off();
    $('#hora_columna_independiente_importacion_valores_sensor').off();
    $('#columna_hora_importacion_valores_sensor').off();
    $('#formato_hora_importacion_valores_sensor').off();
    $('#zona_horaria_importacion_valores_sensor').off();
    $('#tipo_valores_sensor_importacion_valores_sensor').off();
    $('#tipo_horas_incrementos_importacion_valores_sensor').off();
    $('#tipo_incrementos_importacion_valores_sensor').off();
    $('#clase_sensor_exportacion_valores_sensor').off();
    $('#id_sensor_exportacion_valores_sensor').off();
    $('#intervalo_valores_exportacion_valores_sensor').off();
    $('#clase_sensor_borrado_valores_sensor').off();
    $('#clase_sensor_recalculo_valores_clase_sensor').off();
    $('#clase_sensor_envio_valores_manuales_sensor').off();
    $('#id_sensor_envio_valores_manuales_sensor').off();
    $('#tipo_horas_incrementos_envio_valores_manuales_sensor').off();
    $('#tipo_incrementos_envio_valores_manuales_sensor').off();

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

    // Habilita y muestra los controles dependientes del tipo de valores
    // - Nota: Si dentro de una función (A), se llama a otra función (B), está última tiene que definirse antes (B antes que A)
    var funcion_habilita_muestra_controles_tipo_valores_sensor_importacion_valores_sensor = function() {
        funcion_habilita_muestra_controles_tipo_valores_sensor("importacion_valores_sensor");
    };
    $("#tipo_valores_sensor_importacion_valores_sensor").show(funcion_habilita_muestra_controles_tipo_valores_sensor_importacion_valores_sensor);
    $("#tipo_valores_sensor_importacion_valores_sensor").change(funcion_habilita_muestra_controles_tipo_valores_sensor_importacion_valores_sensor);

    // Habilita y muestra los controles dependientes del tipo de horas de incrementos de valores
    var funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_importacion_valores_sensor = function() {
        funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor("importacion_valores_sensor");
    };
    $("#tipo_horas_incrementos_importacion_valores_sensor").show(funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_importacion_valores_sensor);
    $("#tipo_horas_incrementos_importacion_valores_sensor").change(funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_importacion_valores_sensor);

    // Función para establecer el formato a personalizado si se modifican 'manualmente' los parámetros de la importación
    var function_establece_formato_fichero_valores_personalizado_importacion_valores_sensor = function() {
        var id_control_modificado = $(this).attr("id");
        function_establece_formato_fichero_valores_personalizado(id_control_modificado, "importacion_valores_sensor");
    };
    $('#caracter_separador_importacion_valores_sensor').on("input", function_establece_formato_fichero_valores_personalizado_importacion_valores_sensor);
    $('#numero_lineas_cabeceras_importacion_valores_sensor').on("input", function_establece_formato_fichero_valores_personalizado_importacion_valores_sensor);
    $('#columna_fecha_importacion_valores_sensor').on("input", function_establece_formato_fichero_valores_personalizado_importacion_valores_sensor);
    $('#formato_fecha_importacion_valores_sensor').on("input", function_establece_formato_fichero_valores_personalizado_importacion_valores_sensor);
    $('#hora_columna_independiente_importacion_valores_sensor').on("change", function_establece_formato_fichero_valores_personalizado_importacion_valores_sensor);
    $('#columna_hora_importacion_valores_sensor').on("input", function_establece_formato_fichero_valores_personalizado_importacion_valores_sensor);
    $('#formato_hora_importacion_valores_sensor').on("input", function_establece_formato_fichero_valores_personalizado_importacion_valores_sensor);
    $('#tipo_valores_sensor_importacion_valores_sensor').on("change", function_establece_formato_fichero_valores_personalizado_importacion_valores_sensor);
    $('#tipo_incrementos_importacion_valores_sensor').on("change", function_establece_formato_fichero_valores_personalizado_importacion_valores_sensor);

    // Ventana de importación de valores
    var funcion_realiza_acciones_iniciales_clase_sensor_importacion_valores_sensor = function() {
        var id_historico_importacion_valores_sensor = parseInt($("#parametros_ventana_importacion_valores_sensor").attr("id_historico_importacion_valores_sensor"));
        if (id_historico_importacion_valores_sensor != ID_NINGUNO) {
            funcion_realiza_acciones_iniciales_clase_sensor_repetir_importacion_valores_sensor();
        }
        else {
            funcion_realiza_acciones_clase_sensor_importacion_valores_sensor();
        }
    };
    var funcion_realiza_acciones_iniciales_clase_sensor_repetir_importacion_valores_sensor = function() {
        // Habilitación de la lista de sensores
        if ($("select#id_sensor_importacion_valores_sensor option").length <= 1) {
            $("#id_sensor_importacion_valores_sensor").attr('disabled', true).trigger("chosen:updated");
        }
        else {
            $("#id_sensor_importacion_valores_sensor").removeAttr('disabled').trigger("chosen:updated");
        }

        // Clase de sensor
        var clase_sensor = $("#clase_sensor_importacion_valores_sensor").val();
        var caracteristicas_clase_sensor = dame_caracteristicas_clase_sensor(clase_sensor);

        // Tipo de valores del sensor
        var tipo_clase_sensor = caracteristicas_clase_sensor["tipo"];
        switch (tipo_clase_sensor) {
            case TIPO_CLASE_SENSOR_PUNTUAL: {
                $("#tipo_valores_sensor_importacion_valores_sensor").attr('disabled', 'disabled');
                break;
            }
            case TIPO_CLASE_SENSOR_INCREMENTAL: {
                $("#tipo_valores_sensor_importacion_valores_sensor").removeAttr('disabled');
                break;
            }
        }

        // Controles dependientes del tipo de valores del sensor
        funcion_habilita_muestra_controles_tipo_valores_sensor("importacion_valores_sensor");
    };
    var funcion_realiza_acciones_clase_sensor_importacion_valores_sensor = function() {
        // Habilitación de la lista de sensores
        if ($("select#id_sensor_importacion_valores_sensor option").length <= 1) {
            $("#id_sensor_importacion_valores_sensor").attr('disabled', true).trigger("chosen:updated");
        }
        else {
            $("#id_sensor_importacion_valores_sensor").removeAttr('disabled').trigger("chosen:updated");
        }

        // Clase de sensor
        var clase_sensor = $("#clase_sensor_importacion_valores_sensor").val();
        var caracteristicas_clase_sensor = dame_caracteristicas_clase_sensor(clase_sensor);

        // Número de valores
        var numero_valores_clase_sensor = caracteristicas_clase_sensor["numero_valores"];
        $("#numero_valores_importacion_valores_sensor").val(numero_valores_clase_sensor);

        // Números de columnas de valores
        funcion_establece_columnas_horario_verano_valores(clase_sensor, "importacion_valores_sensor");

        // Tipo de valores
        function_establece_tipo_valores_sensor(clase_sensor, "importacion_valores_sensor");
    };
    var funcion_recarga_lista_sensores_importacion_valores_sensor = function() {
        var clase_sensor = $("#clase_sensor_importacion_valores_sensor").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores.php", {
            clase_sensor: clase_sensor,
            opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_NINGUNO
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_sensor_importacion_valores_sensor").html(resultado.html);
            $("#id_sensor_importacion_valores_sensor").trigger("chosen:updated");

            // Realiza las acciones dependientes de la clase de sensor (excepto recargar la lista de sensores)
            funcion_realiza_acciones_clase_sensor_importacion_valores_sensor();
        });
    };
    $("#clase_sensor_importacion_valores_sensor").show(funcion_realiza_acciones_iniciales_clase_sensor_importacion_valores_sensor);
    $("#clase_sensor_importacion_valores_sensor").change(funcion_recarga_lista_sensores_importacion_valores_sensor);

    // Establece los valores por defecto dependiendo del formato de fichero de valores
    var funcion_realiza_acciones_formato_fichero_valores_importacion_valores_sensor = function() {
        var clase_sensor = $("#clase_sensor_importacion_valores_sensor").val();
        funcion_realiza_acciones_formato_fichero_valores(clase_sensor, "importacion_valores_sensor");
    };
    $("#formato_fichero_valores_importacion_valores_sensor").change(funcion_realiza_acciones_formato_fichero_valores_importacion_valores_sensor);

    // Habilita y muestra los controles dependientes de hora en columna independiente
    var funcion_habilita_muestra_controles_hora_columna_independiente_importacion_valores_sensor = function() {
        funcion_habilita_muestra_controles_hora_columna_independiente("importacion_valores_sensor");
    };
    $("#hora_columna_independiente_importacion_valores_sensor").show(funcion_habilita_muestra_controles_hora_columna_independiente_importacion_valores_sensor);
    $("#hora_columna_independiente_importacion_valores_sensor").change(funcion_habilita_muestra_controles_hora_columna_independiente_importacion_valores_sensor);

    // Habilita y muestra el control de columna de horario de verano
    var funcion_habilita_muestra_control_columna_horario_verano_importacion_valores_sensor = function() {
        funcion_habilita_muestra_control_columna_horario_verano("importacion_valores_sensor");
    };
    $("#zona_horaria_importacion_valores_sensor").show(funcion_habilita_muestra_control_columna_horario_verano_importacion_valores_sensor);
    $("#zona_horaria_importacion_valores_sensor").change(funcion_habilita_muestra_control_columna_horario_verano_importacion_valores_sensor);

    // Muestra o oculta el control de tipos de incrementos de valores y establece el valor por defecto
    var funcion_muestra_establece_tipos_incrementos_valores_defecto_exportaciones_valores_sensor = function() {
        var intervalo_valores = $("#intervalo_valores_exportacion_valores_sensor").val();
        switch (intervalo_valores) {
            case INTERVALO_VALORES_TIEMPO_REAL: {
                var clase_sensor = $("#clase_sensor_exportacion_valores_sensor").val();
                var caracteristicas_clase_sensor = dame_caracteristicas_clase_sensor(clase_sensor);
                var numero_campos_puntuales = caracteristicas_clase_sensor["campos_puntuales"].length;
                var numero_campos_incrementos = caracteristicas_clase_sensor["campos_incrementos"].length;
                if ((numero_campos_puntuales > 0) && (numero_campos_incrementos > 0)) {
                    var id_sensor = $("#id_sensor_exportacion_valores_sensor").val();
                    if (id_sensor == ID_NINGUNO) {
                        $("#control_tipo_incrementos_valores_exportacion_valores_sensor").hide();
                    }
                    else {
                        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_tipo_valores_sensor.php", {
                            id_sensor: id_sensor
                        },
                        function (data, status) {
                            var resultado = dame_resultado_ejecucion_script_php_json(data);
                            if (resultado == null) {
                                return;
                            }

                            var tipo_valores_sensor = resultado.tipo_valores_sensor;
                            switch (tipo_valores_sensor) {
                                case TIPO_VALORES_SENSOR_PUNTUALES: {
                                    $("#control_tipo_incrementos_valores_exportacion_valores_sensor").hide();
                                    break;
                                }
                                case TIPO_VALORES_SENSOR_INCREMENTALES: {
                                    $("#tipo_incrementos_valores_exportacion_valores_sensor").val(TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL);
                                    $("#control_tipo_incrementos_valores_exportacion_valores_sensor").show();
                                    break;
                                }
                            }
                        });
                    }
                }
                else {
                    if (numero_campos_puntuales > 0) {
                        $("#control_tipo_incrementos_valores_exportacion_valores_sensor").hide();
                    }
                    if (numero_campos_incrementos > 0) {
                        $("#tipo_incrementos_valores_exportacion_valores_sensor").val(TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL);
                        $("#control_tipo_incrementos_valores_exportacion_valores_sensor").show();
                    }
                }
                break;
            }
            default: {
                $("#control_tipo_incrementos_valores_exportacion_valores_sensor").hide();
                break;
            }
        }
    };

    // Ventana de exportación de valores (sensores por clase)
    var funcion_realiza_acciones_clase_sensor_exportacion_valores_sensor = function() {
        // Habilitación de la lista de sensores
        if ($("select#id_sensor_exportacion_valores_sensor option").length <= 1) {
            $("#id_sensor_exportacion_valores_sensor").attr('disabled', true).trigger("chosen:updated");
        }
        else {
            $("#id_sensor_exportacion_valores_sensor").removeAttr('disabled').trigger("chosen:updated");
        }

        // Clase de sensor
        var clase_sensor = $("#clase_sensor_exportacion_valores_sensor").val();

        // Intervalo de valores
        var intervalo_valores = "";
        switch (clase_sensor) {
            case CLASE_NINGUNA: {
                break;
            }
            case CLASE_SENSOR_ENERGIA_ACTIVA: {
                intervalo_valores = INTERVALO_VALORES_CUARTOHORA;
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION: {
                intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
                break;
            }
            default: {
                intervalo_valores = INTERVALO_VALORES_HORA;
                break;
            }
        }
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_intervalos_valores_exportacion_clase_sensor.php", {
            clase_sensor: clase_sensor,
            intervalo_valores: intervalo_valores
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#intervalo_valores_exportacion_valores_sensor").html(resultado.html);
            $("#intervalo_valores_exportacion_valores_sensor").trigger('change');
            if ($("select#intervalo_valores_exportacion_valores_sensor option").length <= 1) {
                $("#intervalo_valores_exportacion_valores_sensor").prop('disabled', 'disabled');
            }
            else {
                $("#intervalo_valores_exportacion_valores_sensor").prop('disabled', false);
            }
        });

        // Valores de clase de sensor
        var valores_clase_sensor = null;
        switch (clase_sensor) {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            case CLASE_SENSOR_COMPRA_ENERGIA:
            case CLASE_SENSOR_GAS: {
                switch (intervalo_valores) {
                    case INTERVALO_VALORES_NINGUNO:
                    case INTERVALO_VALORES_TIEMPO_REAL: {
                        valores_clase_sensor = false;
                        break;
                    }
                    default: {
                        valores_clase_sensor = true;
                        break;
                    }
                }
                break;
            }
            default: {
                valores_clase_sensor = false;
                break;
            }
        }
        $("#valores_clase_sensor_exportacion_valores_sensor").val(VALOR_NO);
        if (valores_clase_sensor == true) {
            $("#control_valores_clase_sensor_exportacion_valores_sensor").show();
        }
        else {
            $("#control_valores_clase_sensor_exportacion_valores_sensor").hide();
        }

        // Tipos de incrementos de valores
        funcion_muestra_establece_tipos_incrementos_valores_defecto_exportaciones_valores_sensor();
    };
    var funcion_recarga_lista_sensores_exportacion_valores_sensor = function() {
        var clase_sensor = $("#clase_sensor_exportacion_valores_sensor").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores.php", {
            clase_sensor: clase_sensor,
            opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_NINGUNO
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_sensor_exportacion_valores_sensor").html(resultado.html);
            $("#id_sensor_exportacion_valores_sensor").trigger("chosen:updated");

            // Realiza las acciones dependientes de la clase de sensor (excepto recargar la lista de sensores)
            funcion_realiza_acciones_clase_sensor_exportacion_valores_sensor();
        });
    };
    $("#clase_sensor_exportacion_valores_sensor").show(funcion_realiza_acciones_clase_sensor_exportacion_valores_sensor);
    $("#clase_sensor_exportacion_valores_sensor").change(funcion_recarga_lista_sensores_exportacion_valores_sensor);

    // Identificador de sensor
    var funcion_realiza_acciones_sensor_exportacion_valores_sensor = function() {
        funcion_muestra_establece_tipos_incrementos_valores_defecto_exportaciones_valores_sensor();
    };
    $("#id_sensor_exportacion_valores_sensor").change(funcion_realiza_acciones_sensor_exportacion_valores_sensor);

    // Intervalo de valores de exportación de valores de sensor
    var funcion_realiza_acciones_intervalo_valores_exportacion_valores_sensor = function() {
        var clase = $("#clase_sensor_exportacion_valores_sensor").val();
        var intervalo_valores = $("#intervalo_valores_exportacion_valores_sensor").val();
        switch (clase) {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            case CLASE_SENSOR_COMPRA_ENERGIA:
            case CLASE_SENSOR_GAS: {
                switch (intervalo_valores) {
                    case INTERVALO_VALORES_NINGUNO:
                    case INTERVALO_VALORES_TIEMPO_REAL: {
                        $("#control_valores_clase_sensor_exportacion_valores_sensor").hide();
                        break;
                    }
                    default: {
                        $("#control_valores_clase_sensor_exportacion_valores_sensor").show();
                        break;
                    }
                }
                break;
            }
            default: {
                break;
            }
        }
        funcion_muestra_establece_tipos_incrementos_valores_defecto_exportaciones_valores_sensor();
    };
    $("#intervalo_valores_exportacion_valores_sensor").show(funcion_realiza_acciones_intervalo_valores_exportacion_valores_sensor);
    $("#intervalo_valores_exportacion_valores_sensor").change(funcion_realiza_acciones_intervalo_valores_exportacion_valores_sensor);

    // Ventana de borrado de valores (sensores por clase)
    var funcion_realiza_acciones_clase_sensor_borrado_valores_sensor = function() {
        // Habilitación de la lista de sensores
        if ($("select#id_sensor_borrado_valores_sensor option").length <= 1) {
            $("#id_sensor_borrado_valores_sensor").attr('disabled', true).trigger("chosen:updated");
        }
        else {
            $("#id_sensor_borrado_valores_sensor").removeAttr('disabled').trigger("chosen:updated");
        }
    };
    var funcion_recarga_lista_sensores_borrado_valores_sensor = function() {
        var clase_sensor = $("#clase_sensor_borrado_valores_sensor").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores.php", {
            clase_sensor: clase_sensor,
            opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_NINGUNO
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_sensor_borrado_valores_sensor").html(resultado.html);
            $("#id_sensor_borrado_valores_sensor").trigger("chosen:updated");

            // Realiza las acciones dependientes de la clase de sensor (excepto recargar la lista de sensores)
            funcion_realiza_acciones_clase_sensor_borrado_valores_sensor();
        });
    };
    $("#clase_sensor_borrado_valores_sensor").show(funcion_realiza_acciones_clase_sensor_borrado_valores_sensor);
    $("#clase_sensor_borrado_valores_sensor").change(funcion_recarga_lista_sensores_borrado_valores_sensor);

    // Ventana de recalculos de valores de clase (sensores por clase)
    var funcion_realiza_acciones_clase_sensor_recalculo_valores_clase_sensor = function() {
        // Habilitación de la lista de sensores
        if ($("select#id_sensor_recalculo_valores_clase_sensor option").length <= 1) {
            $("#id_sensor_recalculo_valores_clase_sensor").attr('disabled', true).trigger("chosen:updated");
        }
        else {
            $("#id_sensor_recalculo_valores_clase_sensor").removeAttr('disabled').trigger("chosen:updated");
        }
    };
    var funcion_recarga_lista_sensores_recalculo_valores_clase_sensor = function() {
        var clase_sensor = $("#clase_sensor_recalculo_valores_clase_sensor").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores.php", {
            clase_sensor: clase_sensor,
            opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_NINGUNO
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_sensor_recalculo_valores_clase_sensor").html(resultado.html);
            $("#id_sensor_recalculo_valores_clase_sensor").trigger("chosen:updated");

            // Realiza las acciones dependientes de la clase de sensor (excepto recargar la lista de sensores)
            funcion_realiza_acciones_clase_sensor_recalculo_valores_clase_sensor();
        });
    };
    $("#clase_sensor_recalculo_valores_clase_sensor").show(funcion_realiza_acciones_clase_sensor_recalculo_valores_clase_sensor);
    $("#clase_sensor_recalculo_valores_clase_sensor").change(funcion_recarga_lista_sensores_recalculo_valores_clase_sensor);

    // Ventana de envio de valores manuales
    var funcion_realiza_acciones_clase_sensor_envio_valores_manuales_sensor = function() {
        // Habilitación de la lista de sensores
        if ($("select#id_sensor_envio_valores_manuales_sensor option").length <= 1) {
            $("#id_sensor_envio_valores_manuales_sensor").attr('disabled', true).trigger("chosen:updated");
        }
        else {
            $("#id_sensor_envio_valores_manuales_sensor").removeAttr('disabled').trigger("chosen:updated");
        }
    };
    var funcion_recarga_lista_sensores_envio_valores_manuales_sensor = function() {
        var clase_sensor = $("#clase_sensor_envio_valores_manuales_sensor").val();
        $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores_externos.php", {
            clase_sensor: clase_sensor,
            clase_sensor_externo: CLASE_SENSOR_EXTERNO_NINGUNA
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_sensor_envio_valores_manuales_sensor").html(resultado.html);
            $("#id_sensor_envio_valores_manuales_sensor").trigger("chosen:updated");
            $("#id_sensor_envio_valores_manuales_sensor").trigger('change');

            // Realiza las acciones dependientes de la clase de sensor (excepto recargar la lista de sensores)
            funcion_realiza_acciones_clase_sensor_envio_valores_manuales_sensor();
        });
    };
    $("#clase_sensor_envio_valores_manuales_sensor").show(funcion_realiza_acciones_clase_sensor_envio_valores_manuales_sensor);
    $("#clase_sensor_envio_valores_manuales_sensor").change(funcion_recarga_lista_sensores_envio_valores_manuales_sensor);

    var funcion_habilita_controles_recarga_fecha_hora_envio_valores_manuales_sensor = function() {
        var id_sensor = parseInt($("#id_sensor_envio_valores_manuales_sensor").val());
        switch (id_sensor) {
            case ID_NINGUNO: {
                $("#controles_fecha_hora_envio_valores_manuales_sensor").hide();
                $("#control_valores_envio_valores_manuales_sensor").hide();
                $("#control_incrementos_envio_valores_manuales_sensor").hide();
                $("#control_horas_incrementos_envio_valores_manuales_sensor").hide();
                $("#valores_envio_valores_manuales_sensor").removeClass('TLNT_input_mandatory');
                $("#incrementos_envio_valores_manuales_sensor").removeClass('TLNT_input_mandatory');
                $("#horas_incrementos_envio_valores_manuales_sensor").removeClass('TLNT_input_mandatory');
                break;
            }
            default: {
                $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_info_sensor_envio_valores_manuales.php", {
                    id_sensor: id_sensor
                },
                function (data, status) {
                    var resultado = dame_resultado_ejecucion_script_php_json(data);
                    if (resultado == null) {
                        return;
                    }

                    var tipo_valores_sensor = resultado.tipo_valores_sensor;
                    var fecha_envio_valores_manuales = resultado.fecha_envio_valores_manuales;
                    var hora_envio_valores_manuales = resultado.hora_envio_valores_manuales;
                    var horas_incrementos_envio_valores_manuales = resultado.horas_incrementos_envio_valores_manuales;

                    $("#controles_fecha_hora_envio_valores_manuales_sensor").show();
                    switch (tipo_valores_sensor) {
                        case TIPO_VALORES_SENSOR_PUNTUALES: {
                            $("#control_valores_envio_valores_manuales_sensor").show();
                            $("#control_incrementos_envio_valores_manuales_sensor").hide();
                            $("#control_tipo_horas_incrementos_envio_valores_manuales_sensor").hide();
                            $("#control_horas_incrementos_envio_valores_manuales_sensor").hide();
                            $("#control_tipo_incrementos_envio_valores_manuales_sensor").hide();
                            $("#valores_envio_valores_manuales_sensor").addClass('TLNT_input_mandatory');
                            $("#incrementos_envio_valores_manuales_sensor").removeClass('TLNT_input_mandatory');
                            $("#horas_incrementos_envio_valores_manuales_sensor").removeClass('TLNT_input_mandatory');
                            break;
                        }
                        case TIPO_VALORES_SENSOR_INCREMENTALES: {
                            $("#control_valores_envio_valores_manuales_sensor").hide();
                            $("#control_incrementos_envio_valores_manuales_sensor").show();
                            $("#control_tipo_horas_incrementos_envio_valores_manuales_sensor").show();
                            $("#control_horas_incrementos_envio_valores_manuales_sensor").show();
                            $("#control_tipo_incrementos_envio_valores_manuales_sensor").show();
                            $("#valores_envio_valores_manuales_sensor").removeClass('TLNT_input_mandatory');
                            $("#incrementos_envio_valores_manuales_sensor").addClass('TLNT_input_mandatory');
                            $("#horas_incrementos_envio_valores_manuales_sensor").addClass('TLNT_input_mandatory');
                            break;
                        }
                    }
                    TLNT.Navegacion.establece_fecha_control("fecha_envio_valores_manuales_sensor", fecha_envio_valores_manuales);
                    $("#hora_envio_valores_manuales_sensor").val(hora_envio_valores_manuales);
                    $("#horas_incrementos_envio_valores_manuales_sensor").val(horas_incrementos_envio_valores_manuales);
                    $("#parametros_ventana_envio_valores_manuales").attr('tipo_valores_sensor', tipo_valores_sensor);
                });
                break;
            }
        }
    };
    $("#id_sensor_envio_valores_manuales_sensor").show(funcion_habilita_controles_recarga_fecha_hora_envio_valores_manuales_sensor);
    $("#id_sensor_envio_valores_manuales_sensor").change(funcion_habilita_controles_recarga_fecha_hora_envio_valores_manuales_sensor);

    // Habilita y muestra los controles dependientes del tipo de horas de incrementos de valores
    var funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_envio_valores_manuales_sensor = function() {
        funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor("envio_valores_manuales_sensor");
    };
    $("#tipo_horas_incrementos_envio_valores_manuales_sensor").change(funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_envio_valores_manuales_sensor);
};


establece_eventos_ventanas_modales_sensores_sensores = function() {
    // Desactivación de eventos anteriores
    $("#descripcion_sensor").off();
    $("#clase_sensor").off();
    $("#tipo_sensor").off();
    $("#tipo_valores_sensor").off();
    $("#incrementos_tiempo_real_horarios_sensor").off();
    $("#id_localizacion_sensor").off();
    $("#clase_procesado_sensor").off();
    $("#misma_funcion_valores_cuartohoraria_procesado_sensor").off();
    $("#funcion_valores_horaria_procesado_sensor").off();
    $("#funcion_valores_cuartohoraria_procesado_sensor").off();
    $("#id_sensor_hijo").off();
    $("#clase_grupo_sensores").off();

    // Mostrar lista doble para la selección de sensores hijos de compra de energía
    if ($('#select_clase_compra_energia_ids_sensores_hijos_sensor_no_visible').length) {
        $('#select_clase_compra_energia_ids_sensores_hijos_sensor_no_visible').attr("id", "select_clase_compra_energia_ids_sensores_hijos_sensor_visible");
        TLNT.Navegacion.convierte_lista_doble("clase_compra_energia_ids_sensores_hijos_sensor", true);
    };

    // Contador de caracteres de descripción de sensor
    $("#descripcion_sensor").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);

    // Muestra o oculta el control de tipo de valores y establece el valor por defecto
    var funcion_muestra_establece_tipo_valores_defecto = function() {
        var tipo_sensor = $("#tipo_sensor").val();
        var clase_sensor = $("#clase_sensor").val();
        var anyadir_nodo = $("#parametros_ventana_anyadir_modificar_nodo").attr("anyadir_nodo");
        var id_nodo = $("#parametros_ventana_anyadir_modificar_nodo").attr("id_nodo");
        switch (tipo_sensor) {
            case TIPO_SENSOR_EXTERNO:
            case TIPO_SENSOR_PROCESADO: {
                if (anyadir_nodo == VALOR_SI) {
                    switch (clase_sensor) {
                        case CLASE_NINGUNA: {
                            $("#tipo_valores_sensor").val(TIPO_NINGUNO);
                            $("#control_tipo_valores_sensor").hide();
                            break;
                        }
                        case CLASE_SENSOR_ENERGIA_ACTIVA:
                        case CLASE_SENSOR_ENERGIA_REACTIVA:
                        case CLASE_SENSOR_GAS:
                        case CLASE_SENSOR_AGUA:
                        case CLASE_SENSOR_GENERICA: {
                            if (id_nodo == ID_NINGUNO) {
                                $("#tipo_valores_sensor").val(TIPO_NINGUNO);
                            }
                            $("#control_tipo_valores_sensor").show();
                            $("#tipo_valores_sensor").removeAttr('disabled');
                            break;
                        }
                        case CLASE_SENSOR_CORTES_TENSION:
                        case CLASE_SENSOR_COMPRA_ENERGIA: {
                            $("#tipo_valores_sensor").val(TIPO_VALORES_SENSOR_INCREMENTALES);
                            $("#control_tipo_valores_sensor").show();
                            $("#tipo_valores_sensor").attr('disabled', 'disabled');
                            break;
                        }
                        default: {
                            $("#tipo_valores_sensor").val(TIPO_VALORES_SENSOR_PUNTUALES);
                            $("#control_tipo_valores_sensor").show();
                            $("#tipo_valores_sensor").attr('disabled', 'disabled');
                            break;
                        }
                    }
                }
                else {
                    $("#tipo_valores_sensor").attr('disabled', 'disabled');
                }
                break;
            }
            case TIPO_SENSOR_REAL:
            case TIPO_SENSOR_VIRTUAL: {
                switch (clase_sensor) {
                    case CLASE_SENSOR_CORTES_TENSION: {
                        $("#tipo_valores_sensor").val(TIPO_VALORES_SENSOR_INCREMENTALES);
                        $("#control_tipo_valores_sensor").show();
                        $("#tipo_valores_sensor").attr('disabled', 'disabled');
                        break;
                    }
                    default: {
                        $("#tipo_valores_sensor").val(TIPO_VALORES_SENSOR_PUNTUALES);
                        $("#control_tipo_valores_sensor").show();
                        $("#tipo_valores_sensor").attr('disabled', 'disabled');
                        break;
                    }
                }
                break;
            }
            case TIPO_NINGUNO: {
                $("#control_tipo_valores_sensor").hide();
                break;
            }
        }
    };

    // Muestra o oculta el control de tipos de cambio de valores puntuales y establece el valor por defecto
    var funcion_muestra_establece_cambio_valores_puntuales_defecto = function() {
        var clase_sensor = $("#clase_sensor").val();
        var tipo_valores_sensor = $("#tipo_valores_sensor").val();
        var anyadir_nodo = $("#parametros_ventana_anyadir_modificar_nodo").attr("anyadir_nodo");
        switch (tipo_valores_sensor) {
            case TIPO_VALORES_SENSOR_PUNTUALES: {
                if (anyadir_nodo == VALOR_SI) {
                    switch (clase_sensor) {
                        case CLASE_SENSOR_GENERICA: {
                            $("#cambio_valores_puntuales_sensor").val(CAMBIO_VALORES_PUNTUALES_SENSOR_GRADUAL);
                            $("#control_cambio_valores_puntuales_sensor").show();
                            break;
                        }
                        default: {
                            $("#cambio_valores_puntuales_sensor").val(CAMBIO_VALORES_PUNTUALES_SENSOR_GRADUAL);
                            $("#control_cambio_valores_puntuales_sensor").hide();
                            break;
                        }
                    }
                }
                else {
                    switch (clase_sensor) {
                        case CLASE_SENSOR_GENERICA: {
                            $("#cambio_valores_puntuales_sensor").attr('disabled', 'disabled');
                            $("#control_cambio_valores_puntuales_sensor").show();
                            break;
                        }
                        default: {
                            $("#cambio_valores_puntuales_sensor").val(CAMBIO_VALORES_PUNTUALES_SENSOR_GRADUAL);
                            $("#control_cambio_valores_puntuales_sensor").hide();
                            break;
                        }
                    }
                }
                break;
            }
            case TIPO_VALORES_SENSOR_INCREMENTALES: {
                $("#cambio_valores_puntuales_sensor").val(CAMBIO_VALORES_PUNTUALES_SENSOR_GRADUAL);
                $("#control_cambio_valores_puntuales_sensor").hide();
                break;
            }
            case TIPO_NINGUNO: {
                $("#cambio_valores_puntuales_sensor").val(CAMBIO_VALORES_PUNTUALES_SENSOR_GRADUAL);
                $("#control_cambio_valores_puntuales_sensor").hide();
                break;
            }
        }
    };

    // Muestra o oculta el control de incrementos en tiempo real horarios y establece el valor por defecto
    var funcion_muestra_establece_incrementos_tiempo_real_horarios_defecto = function() {
        var tipo_sensor = $("#tipo_sensor").val();
        var clase_sensor = $("#clase_sensor").val();
        var anyadir_nodo = $("#parametros_ventana_anyadir_modificar_nodo").attr("anyadir_nodo");
        switch (tipo_sensor) {
            case TIPO_SENSOR_REAL:
            case TIPO_SENSOR_VIRTUAL:
            case TIPO_SENSOR_EXTERNO: {
                if (anyadir_nodo == VALOR_SI) {
                    switch (clase_sensor) {
                        case CLASE_SENSOR_GENERICA: {
                            $("#control_incrementos_tiempo_real_horarios_sensor").show();
                            break;
                        }
                        default: {
                            $("#incrementos_tiempo_real_horarios_sensor").val(VALOR_SI);
                            $("#control_incrementos_tiempo_real_horarios_sensor").hide();
                            break;
                        }
                    }
                }
                else {
                    switch (clase_sensor) {
                        case CLASE_SENSOR_GENERICA: {
                            $("#control_incrementos_tiempo_real_horarios_sensor").show();
                            var incrementos_tiempo_real_horarios = $("#incrementos_tiempo_real_horarios_sensor").val();
                            if (incrementos_tiempo_real_horarios == VALOR_SI) {
                                $("#control_granularidad_cuartohoraria_sensor").show();
                            }
                            else {
                                $("#granularidad_cuartohoraria_sensor").val(VALOR_NO);
                                $("#control_granularidad_cuartohoraria_sensor").hide();
                            }
                            break;
                        }
                        default: {
                            $("#incrementos_tiempo_real_horarios_sensor").val(VALOR_SI);
                            $("#control_incrementos_tiempo_real_horarios_sensor").hide();
                            break;
                        }
                    }
                }
                break;
            }
            default: {
                $("#incrementos_tiempo_real_horarios_sensor").val(VALOR_SI);
                $("#control_incrementos_tiempo_real_horarios_sensor").hide();
                break;
            }
        }
    };

    // Muestra o oculta el control de granularidad cuartohoraria y establece el valor por defecto
    var funcion_muestra_establece_granularidad_cuartohoraria_defecto = function() {
        var anyadir_nodo = $("#parametros_ventana_anyadir_modificar_nodo").attr("anyadir_nodo");
        var id_nodo = $("#parametros_ventana_anyadir_modificar_nodo").attr("id_nodo");
        var clase_sensor = $("#clase_sensor").val();

        var caracteristicas_clase_sensor = dame_caracteristicas_clase_sensor(clase_sensor);
        var granularidad_cuartohoraria = caracteristicas_clase_sensor["granularidad_cuartohoraria"];
        if (granularidad_cuartohoraria == true) {
            if ((anyadir_nodo == VALOR_SI) && (id_nodo == ID_NINGUNO)) {
                $("#granularidad_cuartohoraria_sensor").val(VALOR_SI);
            }
            $("#control_granularidad_cuartohoraria_sensor").show();
        }
        else {
            $("#granularidad_cuartohoraria_sensor").val(VALOR_NO);
            $("#control_granularidad_cuartohoraria_sensor").hide();
        }
    };

    // Muestra o oculta el control de incrementos negativos válidos y establece el valor por defecto
    var funcion_muestra_establece_incrementos_negativos_validos_defecto = function() {
        var anyadir_nodo = $("#parametros_ventana_anyadir_modificar_nodo").attr("anyadir_nodo");
        var id_nodo = $("#parametros_ventana_anyadir_modificar_nodo").attr("id_nodo");
        var clase_sensor = $("#clase_sensor").val();
        switch (clase_sensor) {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            case CLASE_SENSOR_GAS:
            case CLASE_SENSOR_AGUA: {
                if ((anyadir_nodo == VALOR_SI) && (id_nodo == ID_NINGUNO)) {
                    $("#incrementos_negativos_validos_sensor").val(VALOR_NO);
                }
                $("#control_incrementos_negativos_validos_sensor").show();
                break;
            }
            case CLASE_SENSOR_GENERICA: {
                if ((anyadir_nodo == VALOR_SI) && (id_nodo == ID_NINGUNO)) {
                    $("#incrementos_negativos_validos_sensor").val(VALOR_SI);
                }
                $("#control_incrementos_negativos_validos_sensor").show();
                break;
            }
            default: {
                $("#incrementos_negativos_validos_sensor").val(VALOR_NO);
                $("#control_incrementos_negativos_validos_sensor").hide();
                break;
            }
        }
    };

    // Muestra o oculta el control de notificar todos los eventos
    var funcion_muestra_notificar_todos_eventos = function() {
        var tipo_sensor = $("#tipo_sensor").val();
        var clase_sensor = $("#clase_sensor").val();
        var mostrar_notificar_todos_eventos = false;
        switch (tipo_sensor) {
            case TIPO_SENSOR_PROCESADO:
            case TIPO_SENSOR_EXTERNO: {
                mostrar_notificar_todos_eventos = true;
                break;
            }
        }
        switch (clase_sensor) {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            case CLASE_SENSOR_COMPRA_ENERGIA:
            case CLASE_SENSOR_GAS:
            case CLASE_SENSOR_AGUA: {
                mostrar_notificar_todos_eventos = true;
                break;
            }
        }
        if (mostrar_notificar_todos_eventos == true) {
            $("#control_notificar_todos_eventos_sensor").show();
        }
        else {
            $("#control_notificar_todos_eventos_sensor").hide();
        }
    };

    // Recarga la lista de clases de sensor virtual correspondientes a la nueva clase del sensor
    var funcion_recarga_lista_clases_sensor_virtual_clase_sensor = function() {
        var tipo_sensor = $("#tipo_sensor").val();
        if (tipo_sensor == TIPO_SENSOR_VIRTUAL) {
            $.post("./src/lib/modulos/Nodos/administracion/dame_lista_clases_sensor_virtual_clase_sensor.php", {
                clase_sensor: $("#clase_sensor").val(),
                clase_virtual: $("#clase_virtual_sensor").val()
            },
            function (data, status) {
                var resultado = dame_resultado_ejecucion_script_php_json(data);
                if (resultado == null) {
                    return;
                }

                $("#clase_virtual_sensor").html(resultado.html);
            });
        }
    };

    // Habilita la lista de identificadores de grupos de un sensor
    var funcion_habilita_lista_ids_grupos_sensor = function() {
        if ($("select#id_grupo_sensor option").length <= 1) {
            $("#id_grupo_sensor").prop('disabled', 'disabled');
        }
        else {
            $("#id_grupo_sensor").prop('disabled', false);
        }
        $("#id_grupo_sensor").trigger("chosen:updated");
    };

    // Muestra los controles de localización de un sensor
    var funcion_muestra_controles_localizacion_sensor = function() {
        var mostrar_controles_localizaciones = $("#parametros_ventana_anyadir_modificar_nodo").attr("mostrar_controles_localizaciones");
        if (mostrar_controles_localizaciones == VALOR_SI) {
            $("#control_id_localizacion_sensor").show();
        }
        else {
            $("#control_id_localizacion_sensor").hide();
            $("#control_visible_localizaciones_hijas_sensor").hide();
        }
    };

    // Habilita la lista de identificadores de localizaciones de un sensor
    var funcion_habilita_lista_ids_localizaciones_sensor = function() {
        if ($("select#id_localizacion_sensor option").length <= 1) {
            $("#id_localizacion_sensor").prop('disabled', 'disabled');
        }
        else {
            $("#id_localizacion_sensor").prop('disabled', false);
        }
    };

    // Habilita y muestra los controles dependientes de la clase de sensor
    var funcion_habilita_muestra_controles_clase_sensor = function() {
        $("#titulo-tab-clase-energia-activa").hide();
        $("#titulo-tab-clase-energia-reactiva").hide();
        $("#titulo-tab-clase-cortes-tension").hide();
        $("#titulo-tab-clase-compra-energia").hide();
        $("#titulo-tab-clase-gas").hide();
        $("#titulo-tab-clase-agua").hide();
        $("#titulo-tab-clase-generica").hide();
        var clase_sensor = $("#clase_sensor").val();
        var anyadir_nodo = $("#parametros_ventana_anyadir_modificar_nodo").attr("anyadir_nodo");
        switch (clase_sensor) {
            case CLASE_SENSOR_ENERGIA_ACTIVA: {
                $("#titulo-tab-clase-energia-activa").show();
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA: {
                $("#titulo-tab-clase-energia-reactiva").show();
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION: {
                $("#titulo-tab-clase-cortes-tension").show();
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA: {
                if (anyadir_nodo == VALOR_SI) {
                    $("#tipo_sensor").val(TIPO_SENSOR_EXTERNO);
                    $("#tipo_sensor").trigger('change');
                    $("#tipo_sensor").prop('disabled', 'disabled');
                }
                $("#titulo-tab-clase-compra-energia").show();
                $("#clase_externo_sensor").prop('disabled', 'disabled');
                $("#calibracion_externo_sensor").prop('disabled', 'disabled');
                break;
            }
            case CLASE_SENSOR_GAS: {
                $("#titulo-tab-clase-gas").show();
                break;
            }
            case CLASE_SENSOR_AGUA: {
                $("#titulo-tab-clase-agua").show();
                break;
            }
            case CLASE_SENSOR_GENERICA: {
                $("#titulo-tab-clase-generica").show();
                break;
            }
        }
        if (anyadir_nodo == VALOR_SI) {
            if (clase_sensor != CLASE_SENSOR_COMPRA_ENERGIA) {
                $("#tipo_sensor").prop('disabled', false);
                $("#clase_externo_sensor").prop('disabled', false);
                $("#calibracion_externo_sensor").prop('disabled', false);
            }
        }

        // Muestra u oculta el control de tipo de cambio de valores puntuales y establece los valores por defecto
        funcion_muestra_establece_cambio_valores_puntuales_defecto();

        // Muestra u oculta el control de incrementos negativos válidos y establece el valor por defecto
        funcion_muestra_establece_incrementos_negativos_validos_defecto();

        // Muestra u oculta el control de granularidad cuartohoraria y establece el valor por defecto
        funcion_muestra_establece_granularidad_cuartohoraria_defecto();

        // Habilita el identificador de grupo de sensor
        funcion_habilita_lista_ids_grupos_sensor();

        // Muestra los controles de localización del sensor
        funcion_muestra_controles_localizacion_sensor();

        // Habilita el identificador de localización de sensor
        funcion_habilita_lista_ids_localizaciones_sensor();

        // Muestra u oculta el control de notificar todos los eventos
        funcion_muestra_notificar_todos_eventos();
    };
    $("#clase_sensor").show(funcion_habilita_muestra_controles_clase_sensor);

    // Si se modifica la clase del sensor:
    // - Hay que actualizar la lista de grupos de sensores correspondientes a la nueva clase del sensor
    // - Si el sensor es virtual hay que modificar la lista de clases de sensor virtual correspondientes a la nueva clase del sensor
    // - Si el sensor es externo, comprobar el campo tipo de valores (poner el valor correcto y habilitar/deshabilitar según corresponda)
    var funcion_realiza_acciones_clase_sensor_modificada = function() {
        var clase_sensor = $("#clase_sensor").val();
        $.post("./src/lib/modulos/Nodos/administracion/dame_lista_grupos_sensores.php", {
			clase_sensor: clase_sensor,
            id_grupo_seleccionado: ID_NINGUNO,
            opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_NINGUNO
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#id_grupo_sensor").html(resultado.html);
            $("#id_grupo_sensor").trigger("chosen:updated");

            // Recarga la lista de clases de sensor virtual
            funcion_recarga_lista_clases_sensor_virtual_clase_sensor();

            // Muestra u oculta el control de tipo de valores y establece los valores por defecto
            funcion_muestra_establece_tipo_valores_defecto();

            // Muestra u oculta el control de tipo de cambio de valores puntuales y establece los valores por defecto
            funcion_muestra_establece_cambio_valores_puntuales_defecto();

            // Muestra u oculta el control de incrementos en tiempo real horarios y establece los valores por defecto
            funcion_muestra_establece_incrementos_tiempo_real_horarios_defecto();

            // Muestra y oculta controles según la clase de sensor
            funcion_habilita_muestra_controles_clase_sensor();
		});
    };
    $("#clase_sensor").change(funcion_realiza_acciones_clase_sensor_modificada);

    // Acciones a realizar dependiendo del tipo de sensor
    var funcion_realiza_acciones_tipo_sensor = function() {
        $("#titulo-tab-tipo-real").hide();
        $("#titulo-tab-tipo-virtual").hide();
        $("#titulo-tab-tipo-procesado").hide();
        $("#titulo-tab-tipo-externo").hide();
        $("#titulo-tab-envio").hide();
        var tipo_sensor = $("#tipo_sensor").val();
        var anyadir_nodo = $("#parametros_ventana_anyadir_modificar_nodo").attr("anyadir_nodo");
        switch (tipo_sensor) {
            case TIPO_NINGUNO: {
                $("#control_guardar_valores_base_datos_sensor").hide();
                if (anyadir_nodo == VALOR_SI) {
                    $("#clase_sensor").removeAttr('disabled');
                    $("#control_tipo_valores_sensor").hide();
                }
                break;
            }
            case TIPO_SENSOR_REAL: {
                $("#control_guardar_valores_base_datos_sensor").show();
                $("#guardar_valores_base_datos_sensor").removeAttr('disabled');
                if (anyadir_nodo == VALOR_SI) {
                    $("#clase_sensor").removeAttr('disabled');
                }

                $("#titulo-tab-tipo-real").show();
                $("#titulo-tab-envio").show();

                $("#id_externo_sensor").removeClass('TLNT_input_mandatory TLNT_input_numerical');
                $("#control_frecuencia_muestreo_sensor").show();
                $("#frecuencia_muestreo_sensor").addClass('TLNT_input_mandatory TLNT_input_numerical');
                break;
            }
            case TIPO_SENSOR_VIRTUAL: {
                $("#control_guardar_valores_base_datos_sensor").show();
                if (anyadir_nodo == VALOR_SI) {
                    $("#clase_sensor").removeAttr('disabled');
                    funcion_recarga_lista_clases_sensor_virtual_clase_sensor();
                }

                $("#titulo-tab-tipo-virtual").show();
                $("#titulo-tab-envio").show();

                $("#id_externo_sensor").removeClass('TLNT_input_mandatory TLNT_input_numerical');
                $("#control_frecuencia_muestreo_sensor").show();
                $("#frecuencia_muestreo_sensor").addClass('TLNT_input_mandatory TLNT_input_numerical');
                break;
            }
            case TIPO_SENSOR_PROCESADO: {
                $("#control_guardar_valores_base_datos_sensor").hide();
                $("#guardar_valores_base_datos_sensor").val(VALOR_SI);
                if (anyadir_nodo == VALOR_SI) {
                    $("#clase_sensor").removeAttr('disabled');
                }

                $("#titulo-tab-tipo-procesado").show();
                $("#titulo-tab-envio").show();

                $("#id_externo_sensor").removeClass('TLNT_input_mandatory TLNT_input_numerical');
                $("#control_frecuencia_muestreo_sensor").hide();
                $("#frecuencia_muestreo_sensor").removeClass('TLNT_input_mandatory TLNT_input_numerical');
                break;
            }
            case TIPO_SENSOR_EXTERNO: {
                $("#control_guardar_valores_base_datos_sensor").show();
                if (anyadir_nodo == VALOR_SI) {
                    $("#clase_sensor").removeAttr('disabled');
                }

                $("#titulo-tab-tipo-externo").show();
                $("#titulo-tab-envio").show();

                $("#id_externo_sensor").addClass('TLNT_input_mandatory TLNT_input_numerical');
                $("#control_frecuencia_muestreo_sensor").hide();
                $("#frecuencia_muestreo_sensor").removeClass('TLNT_input_mandatory TLNT_input_numerical');
                break;
            }
        }

        // Muestra u oculta el control de tipo de valores y establece el valor por defecto
        funcion_muestra_establece_tipo_valores_defecto();

        // Muestra u oculta el control de incrementos en tiempo real horarios y establece los valores por defecto
        funcion_muestra_establece_incrementos_tiempo_real_horarios_defecto();

        // Muestra u oculta el control de notificar todos los eventos
        funcion_muestra_notificar_todos_eventos();
    };
    $("#tipo_sensor").show(funcion_realiza_acciones_tipo_sensor);
    $("#tipo_sensor").change(funcion_realiza_acciones_tipo_sensor);

    // Tipo de valores
    $("#tipo_valores_sensor").change(funcion_muestra_establece_cambio_valores_puntuales_defecto);

    // Incrementos en tiempo real horarios
    $("#incrementos_tiempo_real_horarios_sensor").change(funcion_muestra_establece_incrementos_tiempo_real_horarios_defecto);

    // Habilita y muestra los controles dependientes de la localización del sensor
    var funcion_habilita_muestra_controles_localizacion_sensor = function() {
        var id_localizacion_sensor = $("#id_localizacion_sensor").val();
        switch (id_localizacion_sensor) {
            case ID_NINGUNO.toString(): {
                $("#control_visible_localizaciones_hijas_sensor").hide();
                break;
            }
            default: {
                var mostrar_controles_localizaciones = $("#parametros_ventana_anyadir_modificar_nodo").attr("mostrar_controles_localizaciones");
                if (mostrar_controles_localizaciones == VALOR_SI) {
                    $("#control_visible_localizaciones_hijas_sensor").show();
                }
                break;
            }
        }
    };
    $("#id_localizacion_sensor").show(funcion_habilita_muestra_controles_localizacion_sensor);
    $("#id_localizacion_sensor").change(funcion_habilita_muestra_controles_localizacion_sensor);

    // Habilita y muestra los controles dependientes de si la función de valores cuartohoraria es la misma que la horaria
    var funcion_habilita_muestra_controles_misma_funcion_valores_cuartohoraria_procesado_sensor = function() {
        var misma_funcion_valores_procesado_sensor = $("#misma_funcion_valores_cuartohoraria_procesado_sensor").val();
        if (misma_funcion_valores_procesado_sensor == VALOR_SI) {
            $("#control_funcion_valores_cuartohoraria_procesado_sensor").hide();
            $("#funcion_valores_cuartohoraria_procesado_sensor").val("");
        }
        else {
            $("#control_funcion_valores_cuartohoraria_procesado_sensor").show();
        }
    };
    $("#misma_funcion_valores_cuartohoraria_procesado_sensor").show(funcion_habilita_muestra_controles_misma_funcion_valores_cuartohoraria_procesado_sensor);
    $("#misma_funcion_valores_cuartohoraria_procesado_sensor").change(funcion_habilita_muestra_controles_misma_funcion_valores_cuartohoraria_procesado_sensor);

    // Habilita y muestra los controles dependientes de la clase de procesado del sensor
    var funcion_habilita_muestra_controles_clase_procesado_sensor = function() {
        var clase_procesado_sensor = $("#clase_procesado_sensor").val();
        switch (clase_procesado_sensor) {
            case CLASE_SENSOR_PROCESADO_FUNCION_VALORES: {
                var anyadir_nodo = $("#parametros_ventana_anyadir_modificar_nodo").attr("anyadir_nodo");
                var clase_granularidad_cuartohoraria = $("#parametros_ventana_anyadir_modificar_sensor").attr("clase_granularidad_cuartohoraria");
                if (anyadir_nodo == VALOR_SI) {
                    $("#control_funcion_valores_horaria_procesado_sensor").hide();
                    $("#control_misma_funcion_valores_cuartohoraria_procesado_sensor").hide();
                    $("#control_funcion_valores_cuartohoraria_procesado_sensor").hide();
                    $("#control_valores_prueba_funcion_valores_procesado_sensor").hide();
                }
                else {
                    $("#control_funcion_valores_horaria_procesado_sensor").show();
                    if (clase_granularidad_cuartohoraria == VALOR_SI) {
                        $("#control_misma_funcion_valores_cuartohoraria_procesado_sensor").show();
                        funcion_habilita_muestra_controles_misma_funcion_valores_cuartohoraria_procesado_sensor();
                    }
                    else {
                        $("#control_misma_funcion_valores_cuartohoraria_procesado_sensor").hide();
                        $("#control_funcion_valores_cuartohoraria_procesado_sensor").hide();
                    }
                    $("#control_valores_prueba_funcion_valores_procesado_sensor").show();
                }
                break;
            }
            default: {
                $("#control_funcion_valores_horaria_procesado_sensor").hide();
                $("#control_misma_funcion_valores_cuartohoraria_procesado_sensor").hide();
                $("#control_funcion_valores_cuartohoraria_procesado_sensor").hide();
                $("#control_valores_prueba_funcion_valores_procesado_sensor").hide();
                break;
            }
        }
    };
    $("#clase_procesado_sensor").show(funcion_habilita_muestra_controles_clase_procesado_sensor);
    $("#clase_procesado_sensor").change(funcion_habilita_muestra_controles_clase_procesado_sensor);

    // Habilitación de sensor
    var funcion_habilita_sensor_clase_hijo_sensor_procesado = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_sensores = $("select#id_sensor_hijo option").length;
        if (numero_sensores <= 1) {
            $("#id_sensor_hijo").attr('disabled', true).trigger("chosen:updated");
        }
        else {
            $("#id_sensor_hijo").removeAttr('disabled').trigger("chosen:updated");
        }
    };
    $("#id_sensor_hijo").show(funcion_habilita_sensor_clase_hijo_sensor_procesado);

    // Recarga de los sensores de una clase de sensor de la ventana de anyadir/modificar hijo de sensor
    var funcion_recarga_sensores_clase_hijo_sensor_procesado = function() {
        var id_sensor_padre = $("#parametros_ventana_anyadir_modificar_hijo_sensor").attr("id_sensor_padre");
        var clase_sensor_hijo = $("#clase_sensor_hijo_sensor_procesado").val();
        $.post("./src/lib/modulos/Nodos/administracion/dame_lista_sensores_hijos_administracion.php", {
            id_sensor_padre: id_sensor_padre,
            clase_sensor_hijo: clase_sensor_hijo
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#id_sensor_hijo").html(resultado.html);

            // Se habilita el sensor
            funcion_habilita_sensor_clase_hijo_sensor_procesado();

            // Se recargan los campos de la clase del hijo de sensor de procesado
            funcion_recarga_campos_clase_hijo_sensor_procesado();
        });
    };
    $("#clase_sensor_hijo_sensor_procesado").change(funcion_recarga_sensores_clase_hijo_sensor_procesado);

    // Recarga de los campos de la clase de un hijo de un sensor de procesado
    var funcion_recarga_campos_clase_hijo_sensor_procesado = function() {
        var tipo_sensor_padre = $("#parametros_ventana_anyadir_modificar_hijo_sensor").attr("tipo_sensor_padre");
        if (tipo_sensor_padre == TIPO_SENSOR_PROCESADO) {
            var numero_campos_sensor_padre = $("#parametros_ventana_anyadir_modificar_hijo_sensor").attr("numero_campos_sensor_padre");
            var clase_sensor_hijo = $("#clase_sensor_hijo_sensor_procesado").val();
            for (var i = 0; i < numero_campos_sensor_padre; i++) {
                funcion_recarga_campo_clase_hijo_sensor_procesado(clase_sensor_hijo, i);
            }
        }
    };

    // Recarga de los campos de la clase de un hijo de un sensor de procesado
    var funcion_recarga_campo_clase_hijo_sensor_procesado = function(clase_sensor_hijo, numero_campo_sensor_padre) {
        var id_elemento_campo = "campo_hijo_sensor_procesado_" + numero_campo_sensor_padre;
        $.post("./src/lib/modulos/Nodos/administracion/dame_lista_campos_clase_sensor_hijo_sensor_procesado.php", {
            clase_sensor: clase_sensor_hijo,
            campo: $(id_elemento_campo).val(),
            numero_campo_sensor_padre: numero_campo_sensor_padre
        },
        function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

            $("#" + id_elemento_campo).html(resultado.html);

            // Se deshabilita si sólo hay un valor para elegir
            var numero_campos = $("select#" + id_elemento_campo + " option").length;
            if (numero_campos <= 1) {
                $("#" + id_elemento_campo).attr('disabled', true);
            }
            else {
                $("#" + id_elemento_campo).removeAttr('disabled');
            }
        });
    };

    // Eventos de controles de interfaces de sensores
    establece_eventos_ventanas_modales_sensores_interfaces_sensores();

    // Eventos de controles de tipo de sensor externo
    establece_eventos_ventanas_modales_sensores_tipo_sensor_externo();

    // Muestra los controles de localización de un grupo de sensores
    var funcion_muestra_controles_localizacion_grupo_sensores = function() {
        var mostrar_controles_localizaciones = $("#parametros_ventana_anyadir_modificar_nodo").attr("mostrar_controles_localizaciones");
        if (mostrar_controles_localizaciones == VALOR_SI) {
            $("#control_id_localizacion_grupo_sensores").show();
        }
        else {
            $("#control_id_localizacion_grupo_sensores").hide();
        }
    };

    // Habilita la lista de identificadores de localizaciones de un grupo de sensores
    var funcion_habilita_lista_ids_localizaciones_grupo_sensores = function() {
        if ($("select#id_localizacion_grupo_sensores option").length <= 1) {
            $("#id_localizacion_grupo_sensores").prop('disabled', 'disabled');
        }
        else {
            $("#id_localizacion_grupo_sensores").prop('disabled', false);
        }
    };

    // Se habilitan y muestran controles dependientes de la clase de grupo de sensores
    var funcion_habilita_muestra_controles_clase_grupo_sensores = function() {
        // Muestra los controles de localización del grupo de sensores
        funcion_muestra_controles_localizacion_grupo_sensores();

        // Habilita el identificador de localización del grupo de sensores
        funcion_habilita_lista_ids_localizaciones_grupo_sensores();
    };
    $("#clase_grupo_sensores").show(funcion_habilita_muestra_controles_clase_grupo_sensores);
    $("#clase_grupo_sensores").show(funcion_habilita_muestra_controles_clase_grupo_sensores);

    // Contadores de caracteres de funciones de valores de procesado
    $("#funcion_valores_horaria_procesado_sensor").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
    $("#funcion_valores_cuartohoraria_procesado_sensor").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
};


establece_eventos_ventanas_modales_sensores_interfaces_sensores = function() {
    // Desactivación de eventos anteriores
    $("#clase_interfaz_sensor").off();

    // Se establecen los eventos de los controles de interfaces de sensores
    establece_eventos_ventanas_modales_sensores_controles_interfaces_sensores();

    // Se muestran los controles correspondientes a cada clase de interfaz
    var funcion_realiza_acciones_clase_interfaz_sensor_modificada = function() {
        var clase_interfaz_sensor = $("#clase_interfaz_sensor").val();

        $.post("./src/lib/modulos/Nodos/administracion/dame_controles_clase_interfaz_sensor.php", {
			clase_interfaz: clase_interfaz_sensor
		},
		function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#id_controles_clase_interfaz_sensor").html(resultado.html);

            // Se establecen los eventos de los controles de interfaces de sensores
            establece_eventos_ventanas_modales_sensores_controles_interfaces_sensores();
		});
    };
    $("#clase_interfaz_sensor").change(funcion_realiza_acciones_clase_interfaz_sensor_modificada);
};


establece_eventos_ventanas_modales_sensores_controles_interfaces_sensores = function() {
    // Desactivación de eventos anteriores
    $("#tipo_puerto_serie_clase_interfaz_asincrono_serie_sensor").off();

    // Habilita y muestra los controles dependientes del tipo de puerto serie seleccionado en interfaz asíncrono serie
    var funcion_habilita_muestra_controles_puerto_serie_clase_interfaz_asincrono_serie_sensor = function() {
        var tipo_puerto_serie = $("#tipo_puerto_serie_clase_interfaz_asincrono_serie_sensor").val();
        switch (tipo_puerto_serie) {
            case TIPO_PUERTO_SERIE_CLASE_INTERFAZ_ASINCRONO_SERIE_ARDUINO: {
                $("#protocolo_clase_interfaz_asincrono_serie_sensor").val(PROTOCOLO_EMIOS);
                $("#protocolo_clase_interfaz_asincrono_serie_sensor").attr('disabled', 'disabled');
                break;
            }
            case TIPO_PUERTO_SERIE_CLASE_INTERFAZ_ASINCRONO_SERIE_UART: {
                $("#protocolo_clase_interfaz_asincrono_serie_sensor").val(PROTOCOLO_API_XBEE);
                $("#protocolo_clase_interfaz_asincrono_serie_sensor").attr('disabled', 'disabled');
                break;
            }
            case TIPO_PUERTO_SERIE_CLASE_INTERFAZ_ASINCRONO_SERIE_XBEE: {
                $("#protocolo_clase_interfaz_asincrono_serie_sensor").val(PROTOCOLO_API_XBEE);
                $("#protocolo_clase_interfaz_asincrono_serie_sensor").attr('disabled', 'disabled');
                break;
            }
        }
    };
    $("#tipo_puerto_serie_clase_interfaz_asincrono_serie_sensor").show(funcion_habilita_muestra_controles_puerto_serie_clase_interfaz_asincrono_serie_sensor);
    $("#tipo_puerto_serie_clase_interfaz_asincrono_serie_sensor").change(funcion_habilita_muestra_controles_puerto_serie_clase_interfaz_asincrono_serie_sensor);

    // Botones de ayuda
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_controles_interfaces_sensores);
};

//Funcion cambia parametros select al cambiar API
funcion_cambia_api_sensores_externos = function() {

    var api_seleccionada = $("#api_seleccionada_sensor_externo_apis").find('option:selected').val();

    switch (api_seleccionada) {
        case "API_AXONTIME": {
            //$("#direccion_api_clase_externo_api").val("api.twinmeter.es");
            $("#control_campo_cups_id_api_sensor_externo").show().addClass('TLNT_input_mandatory TLNT_input_float');
            $("#control_campo_tipo_curva_api_sensor_externo").show();
            $("#control_campo_tipo_energia_api_sensor_externo").show();
            $("#control_campo_id_localizacion_api_sensor_externo").hide().removeClass('TLNT_input_mandatory TLNT_input_float');
            $("#control_campo_id_parametro_api_sensor_externo").hide().removeClass('TLNT_input_mandatory TLNT_input_float');
            $("#control_campo_usuario_api").hide().removeClass('TLNT_input_mandatory TLNT_input_float');
            $("#control_campo_password_api").hide().removeClass('TLNT_input_mandatory TLNT_input_float');

            break;
        }
        case "API_SGCLIMA": {
            //$("#direccion_api_clase_externo_api").val("sgclima.indoorclima.com");
            $("#control_campo_cups_id_api_sensor_externo").hide().removeClass('TLNT_input_mandatory TLNT_input_float');
            $("#control_campo_tipo_curva_api_sensor_externo").hide();
            $("#control_campo_tipo_energia_api_sensor_externo").hide();
            $("#control_campo_id_localizacion_api_sensor_externo").show().addClass('TLNT_input_mandatory TLNT_input_float');
            $("#control_campo_id_parametro_api_sensor_externo").show().addClass('TLNT_input_mandatory TLNT_input_float');
            $("#control_campo_usuario_api").show().addClass('TLNT_input_mandatory TLNT_input_float');
            $("#control_campo_password_api").show().addClass('TLNT_input_mandatory TLNT_input_float');
            break;
        }
        default:
            //$("#direccion_api_clase_externo_api").val("");
            $("#control_campo_cups_id_api_sensor_externo").hide().removeClass('TLNT_input_mandatory TLNT_input_float');
            $("#control_campo_tipo_curva_api_sensor_externo").hide();
            $("#control_campo_tipo_energia_api_sensor_externo").hide();
            $("#control_campo_id_localizacion_api_sensor_externo").hide().removeClass('TLNT_input_mandatory TLNT_input_float');
            $("#control_campo_id_parametro_api_sensor_externo").hide().removeClass('TLNT_input_mandatory TLNT_input_float');
            $("#control_campo_usuario_api").hide().removeClass('TLNT_input_mandatory TLNT_input_float');
            $("#control_campo_password_api").hide().removeClass('TLNT_input_mandatory TLNT_input_float');
            break;
    }

};


funcion_cambia_controles_csv_datadis = function() {
    var formato_fichero_seleccionado = $("#formato_fichero_valores_clase_externo_ficheros_csv_sensor").find('option:selected').val();

    if (formato_fichero_seleccionado == "datadis") {
        $("#prefijo_fichero_clase_externo_ficheros_csv_sensor").prop("disabled", true).removeClass("TLNT_input_mandatory");
        $("#caracter_separador_clase_externo_ficheros_csv_sensor").prop( "disabled", true);
        $("#numero_lineas_cabeceras_clase_externo_ficheros_csv_sensor").prop( "disabled", true);
        $("#columna_fecha_clase_externo_ficheros_csv_sensor").prop( "disabled", true);
        $("#formato_fecha_clase_externo_ficheros_csv_sensor").prop( "disabled", true);
        $("#control_campo_cups_datadis_api").show().addClass('TLNT_input_mandatory TLNT_input_float');
        $("#control_campo_distributor_code_datadis_api").show().addClass('TLNT_input_mandatory TLNT_input_float');
        $("#control_campo_measurement_type_api_sensor_externo").show().addClass('TLNT_input_mandatory TLNT_input_float');
        $("#control_campo_point_type_api_sensor_externo").show().addClass('TLNT_input_mandatory TLNT_input_float');
        $("#control_authorized_nif_api_sensor_externo").show().addClass('TLNT_input_mandatory TLNT_input_float');
    }
    else {
        $("#prefijo_fichero_clase_externo_ficheros_csv_sensor").prop("disabled", false).addClass("TLNT_input_mandatory");
        $("#caracter_separador_clase_externo_ficheros_csv_sensor").prop( "disabled", false);
        $("#numero_lineas_cabeceras_clase_externo_ficheros_csv_sensor").prop( "disabled", false);
        $("#columna_fecha_clase_externo_ficheros_csv_sensor").prop( "disabled", false);
        $("#formato_fecha_clase_externo_ficheros_csv_sensor").prop( "disabled", false);
        $("#control_campo_cups_datadis_api").hide().removeClass('TLNT_input_mandatory TLNT_input_float');
        $("#control_campo_distributor_code_datadis_api").hide().removeClass('TLNT_input_mandatory TLNT_input_float');
        $("#control_campo_measurement_type_api_sensor_externo").hide().removeClass('TLNT_input_mandatory TLNT_input_float');
        $("#control_campo_point_type_api_sensor_externo").hide().removeClass('TLNT_input_mandatory TLNT_input_float');
        $("#control_authorized_nif_api_sensor_externo").hide().removeClass('TLNT_input_mandatory TLNT_input_float');
    }
};


establece_eventos_ventanas_modales_sensores_tipo_sensor_externo = function() {
    // Desactivación de eventos anteriores
    $("#clase_externo_sensor").off();
    $("#formato_fichero_valores_clase_externo_ficheros_csv_sensor").off();
    $("#caracter_separador_clase_externo_ficheros_csv_sensor").off();
    $("#numero_lineas_cabeceras_clase_externo_ficheros_csv_sensor").off();
    $("#columna_fecha_clase_externo_ficheros_csv_sensor").off();
    $("#formato_fecha_clase_externo_ficheros_csv_sensor").off();
    $("#hora_columna_independiente_clase_externo_ficheros_csv_sensor").off();
    $("#columna_hora_clase_externo_ficheros_csv_sensor").off();
    $("#formato_hora_clase_externo_ficheros_csv_sensor").off();
    $("#tipo_valores_sensor_clase_externo_ficheros_csv_sensor").off();
    $('#tipo_horas_incrementos_clase_externo_ficheros_csv_sensor').off();
    $('#tipo_incrementos_clase_externo_ficheros_csv_sensor').off();
    $("#hora_columna_independiente_clase_externo_ficheros_csv_sensor").off();
    $("#zona_horaria_clase_externo_ficheros_csv_sensor").off();
    $("#proveedor_clase_externo_http_emios_tipo_meteorologico_sensor").off();
    $("#modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor").off();
    $("#tipo_clase_externo_http_emios_sensor").off();
    $("#tipo_valores_sensor_clase_externo_modbus_ip_sensor").off();
    $('#tipo_horas_incrementos_clase_externo_modbus_ip_sensor').off();
    $("#tipo_dato_clase_externo_modbus_ip_sensor").off();
    $("#tipo_registro_clase_externo_modbus_ip_sensor").off();
    $("#tipo_valores_sensor_clase_externo_http_xml_powerstudio_sensor").off();
    $('#tipo_horas_incrementos_clase_externo_http_xml_powerstudio_sensor').off();
    $("#tipo_valores_sensor_clase_externo_api_sensor").off();
    $('#tipo_horas_incrementos_clase_externo_api_sensor').off();

    // Se muestran los controles correspondientes a cada clase de sensor externo
    var funcion_muestra_controles_clase_sensor_externo = function() {
        var clase_sensor_externo = $("#clase_externo_sensor").val();
        $.post("./src/lib/modulos/Nodos/administracion/dame_controles_clase_sensor_externo.php", {
			clase_sensor_externo: clase_sensor_externo
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#id_controles_clase_externo_sensor").html(resultado.html);

            // Se establecen de nuevo los eventos de los controles de tipo de sensor externo
            // (para establecer las acciones de los nuevos controles)
            establece_eventos_ventanas_modales_sensores_tipo_sensor_externo();

            // Se establecen las acciones correspondientes a la clase de sensor
            funcion_realiza_acciones_clase_sensor_clase_externo_ficheros_csv_sensor();
		});
    };
    $("#clase_externo_sensor").change(funcion_muestra_controles_clase_sensor_externo);

    // Se realizan las acciones correspondientes al modificar la clase de sensor en un sensor de fichero CSV
    var funcion_realiza_acciones_clase_sensor_clase_externo_ficheros_csv_sensor = function() {
        var tipo_sensor = $("#tipo_sensor").val();
        if (tipo_sensor != TIPO_SENSOR_EXTERNO) {
            return;
        }
        var clase_sensor_externo = $("#clase_externo_sensor").val();
        if (clase_sensor_externo != CLASE_SENSOR_EXTERNO_FICHEROS_CSV) {
            return;
        }

        // Clase de sensor
        var clase_sensor = $("#clase_sensor").val();
        var caracteristicas_clase_sensor = dame_caracteristicas_clase_sensor(clase_sensor);

        // Número de valores
        var numero_valores_clase_sensor = caracteristicas_clase_sensor["numero_valores"];
        $("#numero_valores_clase_externo_ficheros_csv_sensor").val(numero_valores_clase_sensor);

        // Números de columnas
        funcion_establece_columnas_horario_verano_valores(clase_sensor, "clase_externo_ficheros_csv_sensor");

        // Tipo de valores
        function_establece_tipo_valores_sensor(clase_sensor, "clase_externo_ficheros_csv_sensor");
    };
    $("#clase_sensor").change(funcion_realiza_acciones_clase_sensor_clase_externo_ficheros_csv_sensor);

    // Función para establecer el formato a personalizado si se modifican 'manualmente' los parámetros del sensor
    var function_establece_formato_fichero_valores_personalizado_clase_externo_ficheros_csv_sensor = function() {
        var id_control_modificado = $(this).attr("id");
        function_establece_formato_fichero_valores_personalizado(id_control_modificado, "clase_externo_ficheros_csv_sensor");
    };
    $('#caracter_separador_clase_externo_ficheros_csv_sensor').on("input", function_establece_formato_fichero_valores_personalizado_clase_externo_ficheros_csv_sensor);
    $('#numero_lineas_cabeceras_clase_externo_ficheros_csv_sensor').on("input", function_establece_formato_fichero_valores_personalizado_clase_externo_ficheros_csv_sensor);
    $('#columna_fecha_clase_externo_ficheros_csv_sensor').on("input", function_establece_formato_fichero_valores_personalizado_clase_externo_ficheros_csv_sensor);
    $('#formato_fecha_clase_externo_ficheros_csv_sensor').on("input", function_establece_formato_fichero_valores_personalizado_clase_externo_ficheros_csv_sensor);
    $('#hora_columna_independiente_clase_externo_ficheros_csv_sensor').on("change", function_establece_formato_fichero_valores_personalizado_clase_externo_ficheros_csv_sensor);
    $('#columna_hora_clase_externo_ficheros_csv_sensor').on("input", function_establece_formato_fichero_valores_personalizado_clase_externo_ficheros_csv_sensor);
    $('#formato_hora_clase_externo_ficheros_csv_sensor').on("input", function_establece_formato_fichero_valores_personalizado_clase_externo_ficheros_csv_sensor);
    $('#tipo_valores_sensor_clase_externo_ficheros_csv_sensor').on("change", function_establece_formato_fichero_valores_personalizado_clase_externo_ficheros_csv_sensor);
    $('#tipo_incrementos_clase_externo_ficheros_csv_sensor').on("change", function_establece_formato_fichero_valores_personalizado_clase_externo_ficheros_csv_sensor);

    // Establece los valores por defecto dependiendo del formato de fichero de valores
    var funcion_realiza_acciones_formato_fichero_valores_clase_externo_ficheros_csv_sensor = function() {
        var clase_sensor = $("#clase_sensor").val();
        funcion_realiza_acciones_formato_fichero_valores(clase_sensor, "clase_externo_ficheros_csv_sensor");
    };
    $("#formato_fichero_valores_clase_externo_ficheros_csv_sensor").change(funcion_realiza_acciones_formato_fichero_valores_clase_externo_ficheros_csv_sensor);

    // Habilita y muestra los controles dependientes de la selección de hora en columna independiente en la pestaña de administración de sensor externo
    var funcion_habilita_muestra_controles_hora_columna_independiente_clase_externo_ficheros_csv_sensor = function() {
        funcion_habilita_muestra_controles_hora_columna_independiente("clase_externo_ficheros_csv_sensor");
    };
    $("#hora_columna_independiente_clase_externo_ficheros_csv_sensor").show(funcion_habilita_muestra_controles_hora_columna_independiente_clase_externo_ficheros_csv_sensor);
    $("#hora_columna_independiente_clase_externo_ficheros_csv_sensor").change(funcion_habilita_muestra_controles_hora_columna_independiente_clase_externo_ficheros_csv_sensor);

    var funcion_habilita_muestra_controles_tipo_csv_datadis = function() {
        funcion_cambia_controles_csv_datadis();
        console.log($("#formato_fichero_valores_clase_externo_ficheros_csv_sensor").val());
    };
    $("#formato_fichero_valores_clase_externo_ficheros_csv_sensor").show(funcion_habilita_muestra_controles_tipo_csv_datadis);
    $("#formato_fichero_valores_clase_externo_ficheros_csv_sensor").change(funcion_habilita_muestra_controles_tipo_csv_datadis);

    // Habilita y muestra el control de columna de horario de verano
    var funcion_habilita_muestra_control_columna_horario_verano_clase_externo_ficheros_csv_sensor = function() {
        funcion_habilita_muestra_control_columna_horario_verano("clase_externo_ficheros_csv_sensor");
    };
    $("#zona_horaria_clase_externo_ficheros_csv_sensor").show(funcion_habilita_muestra_control_columna_horario_verano_clase_externo_ficheros_csv_sensor);
    $("#zona_horaria_clase_externo_ficheros_csv_sensor").change(funcion_habilita_muestra_control_columna_horario_verano_clase_externo_ficheros_csv_sensor);

    // Acciones a realizar dependiendo de la selección de tipo de sensor externo http emios
    var funcion_realiza_acciones_tipo_clase_externo_http_emios_sensor = function() {
        var tipo_clase_externo_http_emios_sensor = $("#tipo_clase_externo_http_emios_sensor").val();
        var anyadir_nodo = $("#parametros_ventana_anyadir_modificar_nodo").attr("anyadir_nodo");
        var id_nodo = $("#parametros_ventana_anyadir_modificar_nodo").attr("id_nodo");
        switch (tipo_clase_externo_http_emios_sensor) {
            case TIPO_SENSOR_SOFTWARE_METEOROLOGICO: {
                $("#control_proveedor_clase_externo_http_emios_tipo_meteorologico_sensor").show();
                $("#control_tipo_informacion_clase_externo_http_emios_tipo_meteorologico_sensor").show();
                $("#control_clave_clase_externo_http_emios_tipo_meteorologico_sensor").show();
                $("#clave_clase_externo_http_emios_tipo_meteorologico_sensor").addClass('TLNT_input_mandatory');
                $("#control_modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor").show();
                $("#control_latitud_clase_externo_http_emios_tipo_meteorologico_sensor").show();
                $("#latitud_clase_externo_http_emios_tipo_meteorologico_sensor").addClass('TLNT_input_mandatory');
                $("#control_longitud_clase_externo_http_emios_tipo_meteorologico_sensor").show();
                $("#longitud_clase_externo_http_emios_tipo_meteorologico_sensor").addClass('TLNT_input_mandatory');
                $("#control_localidad_clase_externo_http_emios_tipo_meteorologico_sensor").show();
                $("#localidad_clase_externo_http_emios_tipo_meteorologico_sensor").addClass('TLNT_input_mandatory');
                $("#control_pais_clase_externo_http_emios_tipo_meteorologico_sensor").show();
                $("#pais_clase_externo_http_emios_tipo_meteorologico_sensor").addClass('TLNT_input_mandatory');

                if ((anyadir_nodo == VALOR_SI) && (id_nodo == ID_NINGUNO)) {
                    $("#modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor").val(MODO_LOCALIZACION_LOCALIDAD);
                    $("#modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor").trigger('change');
                }
                break;
            }
        }
    };
    $("#tipo_clase_externo_http_emios_sensor").show(funcion_realiza_acciones_tipo_clase_externo_http_emios_sensor);
    $("#tipo_clase_externo_http_emios_sensor").change(funcion_realiza_acciones_tipo_clase_externo_http_emios_sensor);

    // Se recarga la lista de tipos de sensor meteorológico correspondiente al proveedor
    var funcion_recarga_lista_tipos_informacion_meteorologica_clase_externo_http_emios_tipo_meteorologico_sensor = function() {
        var proveedor = $("#proveedor_clase_externo_http_emios_tipo_meteorologico_sensor").val();
        var tipo_seleccionado = $("#tipo_informacion_clase_externo_http_emios_tipo_meteorologico_sensor").val();
        $.post("./src/lib/modulos/Nodos/administracion/dame_lista_tipos_informacion_meteorologica.php", {
			proveedor: proveedor,
            tipo_seleccionado: tipo_seleccionado
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#tipo_informacion_clase_externo_http_emios_tipo_meteorologico_sensor").html(resultado.html);
		});
    };
    $("#proveedor_clase_externo_http_emios_tipo_meteorologico_sensor").change(funcion_recarga_lista_tipos_informacion_meteorologica_clase_externo_http_emios_tipo_meteorologico_sensor);

    // Habilita y muestra los modos de localización meteorológica correspondiente al proveedor
    var funcion_habilita_muestra_modos_localizacion_meteorologica_clase_externo_http_emios_tipo_meteorologico_sensor = function() {
        if ($("select#modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor option").length <= 1) {
            $("#control_modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor").hide();
        }
        else {
            $("#control_modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor").show();
        }
    };

    // Se recarga la lista de tipos de modos de localización meteorológica correspondiente al proveedor
    var funcion_recarga_lista_modos_localizacion_meteorologica_clase_externo_http_emios_tipo_meteorologico_sensor = function() {
        var proveedor = $("#proveedor_clase_externo_http_emios_tipo_meteorologico_sensor").val();
        var modo_seleccionado = $("#modo_localizacion_meteorologica_clase_externo_http_emios_tipo_meteorologico_sensor").val();
        $.post("./src/lib/modulos/Nodos/administracion/dame_lista_modos_localizacion_meteorologica.php", {
			proveedor: proveedor,
            modo_seleccionado: modo_seleccionado
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor").html(resultado.html);
            funcion_habilita_muestra_controles_modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor();
		});
    };
    $("#proveedor_clase_externo_http_emios_tipo_meteorologico_sensor").change(funcion_recarga_lista_modos_localizacion_meteorologica_clase_externo_http_emios_tipo_meteorologico_sensor);

    // Habilita y muestra los controles dependientes del modo de localización seleccionado para el tipo de sensor externo http emios meteorológico
    var funcion_habilita_muestra_controles_modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor = function() {
        var tipo_clase_externo_http_emios_sensor = $("#tipo_clase_externo_http_emios_sensor").val();
        if (tipo_clase_externo_http_emios_sensor == TIPO_SENSOR_SOFTWARE_METEOROLOGICO) {
            var modo_localizacion_externo_http_emios_tipo_meteorologico_sensor = $("#modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor").val();
            switch (modo_localizacion_externo_http_emios_tipo_meteorologico_sensor) {
                case MODO_LOCALIZACION_COORDENADAS_GEOGRAFICAS: {
                    $("#control_latitud_clase_externo_http_emios_tipo_meteorologico_sensor").show();
                    $("#latitud_clase_externo_http_emios_tipo_meteorologico_sensor").addClass('TLNT_input_mandatory TLNT_input_float');
                    $("#control_longitud_clase_externo_http_emios_tipo_meteorologico_sensor").show();
                    $("#longitud_clase_externo_http_emios_tipo_meteorologico_sensor").addClass('TLNT_input_mandatory TLNT_input_float');

                    $("#control_localidad_clase_externo_http_emios_tipo_meteorologico_sensor").hide();
                    $("#localidad_clase_externo_http_emios_tipo_meteorologico_sensor").removeClass('TLNT_input_mandatory');
                    $("#control_pais_clase_externo_http_emios_tipo_meteorologico_sensor").hide();
                    $("#pais_clase_externo_http_emios_tipo_meteorologico_sensor").removeClass('TLNT_input_mandatory');

                    $("#control_idema_clase_externo_http_emios_tipo_meteorologico_sensor").hide();
                    $("#idema_clase_externo_http_emios_tipo_meteorologico_sensor").removeClass('TLNT_input_mandatory');
                    break;
                }
                case MODO_LOCALIZACION_LOCALIDAD: {
                    $("#control_latitud_clase_externo_http_emios_tipo_meteorologico_sensor").hide();
                    $("#latitud_clase_externo_http_emios_tipo_meteorologico_sensor").removeClass('TLNT_input_mandatory TLNT_input_float');
                    $("#control_longitud_clase_externo_http_emios_tipo_meteorologico_sensor").hide();
                    $("#longitud_clase_externo_http_emios_tipo_meteorologico_sensor").removeClass('TLNT_input_mandatory TLNT_input_float');

                    $("#control_localidad_clase_externo_http_emios_tipo_meteorologico_sensor").show();
                    $("#localidad_clase_externo_http_emios_tipo_meteorologico_sensor").addClass('TLNT_input_mandatory');
                    $("#control_pais_clase_externo_http_emios_tipo_meteorologico_sensor").show();
                    $("#pais_clase_externo_http_emios_tipo_meteorologico_sensor").addClass('TLNT_input_mandatory');

                    $("#control_idema_clase_externo_http_emios_tipo_meteorologico_sensor").hide();
                    $("#idema_clase_externo_http_emios_tipo_meteorologico_sensor").removeClass('TLNT_input_mandatory');
                    break;
                }
                case MODO_LOCALIZACION_IDEMA: {
                    $("#control_latitud_clase_externo_http_emios_tipo_meteorologico_sensor").hide();
                    $("#latitud_clase_externo_http_emios_tipo_meteorologico_sensor").removeClass('TLNT_input_mandatory TLNT_input_float');
                    $("#control_longitud_clase_externo_http_emios_tipo_meteorologico_sensor").hide();
                    $("#longitud_clase_externo_http_emios_tipo_meteorologico_sensor").removeClass('TLNT_input_mandatory TLNT_input_float');

                    $("#control_localidad_clase_externo_http_emios_tipo_meteorologico_sensor").hide();
                    $("#localidad_clase_externo_http_emios_tipo_meteorologico_sensor").removeClass('TLNT_input_mandatory');
                    $("#control_pais_clase_externo_http_emios_tipo_meteorologico_sensor").hide();
                    $("#pais_clase_externo_http_emios_tipo_meteorologico_sensor").removeClass('TLNT_input_mandatory');

                    $("#control_idema_clase_externo_http_emios_tipo_meteorologico_sensor").show();
                    $("#idema_clase_externo_http_emios_tipo_meteorologico_sensor").addClass('TLNT_input_mandatory');
                    break;
                }
            }
            funcion_habilita_muestra_modos_localizacion_meteorologica_clase_externo_http_emios_tipo_meteorologico_sensor();
        }
    };
    $("#modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor").show(funcion_habilita_muestra_controles_modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor);
    $("#modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor").change(funcion_habilita_muestra_controles_modo_localizacion_clase_externo_http_emios_tipo_meteorologico_sensor);

    var funcion_habilita_muestra_controles_tipo_valores_sensor_clase_externo_ficheros_csv_sensor = function() {
        funcion_habilita_muestra_controles_tipo_valores_sensor("clase_externo_ficheros_csv_sensor");
    };
    $("#tipo_valores_sensor_clase_externo_ficheros_csv_sensor").show(funcion_habilita_muestra_controles_tipo_valores_sensor_clase_externo_ficheros_csv_sensor);
    $("#tipo_valores_sensor_clase_externo_ficheros_csv_sensor").change(funcion_habilita_muestra_controles_tipo_valores_sensor_clase_externo_ficheros_csv_sensor);

    var funcion_habilita_muestra_controles_tipo_valores_sensor_clase_externo_modbus_ip_sensor = function() {
        funcion_habilita_muestra_controles_tipo_valores_sensor("clase_externo_modbus_ip_sensor");
    };
    $("#tipo_valores_sensor_clase_externo_modbus_ip_sensor").show(funcion_habilita_muestra_controles_tipo_valores_sensor_clase_externo_modbus_ip_sensor);
    $("#tipo_valores_sensor_clase_externo_modbus_ip_sensor").change(funcion_habilita_muestra_controles_tipo_valores_sensor_clase_externo_modbus_ip_sensor);

    var funcion_habilita_muestra_controles_tipo_valores_sensor_clase_externo_http_xml_powerstudio_sensor = function() {
        funcion_habilita_muestra_controles_tipo_valores_sensor("clase_externo_http_xml_powerstudio_sensor");
    };
    $("#tipo_valores_sensor_clase_externo_http_xml_powerstudio_sensor").show(funcion_habilita_muestra_controles_tipo_valores_sensor_clase_externo_http_xml_powerstudio_sensor);
    $("#tipo_valores_sensor_clase_externo_http_xml_powerstudio_sensor").change(funcion_habilita_muestra_controles_tipo_valores_sensor_clase_externo_http_xml_powerstudio_sensor);

    var funcion_habilita_muestra_controles_tipo_valores_sensor_clase_externo_api_sensor = function() {
        funcion_cambia_api_sensores_externos("clase_externo_api_sensor");
    };
    $("#api_seleccionada_sensor_externo_apis").show(funcion_habilita_muestra_controles_tipo_valores_sensor_clase_externo_api_sensor);
    $("#api_seleccionada_sensor_externo_apis").change(funcion_habilita_muestra_controles_tipo_valores_sensor_clase_externo_api_sensor);


    var funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_clase_externo_ficheros_csv_sensor = function() {
        funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor("clase_externo_ficheros_csv_sensor");
    };
    $("#tipo_horas_incrementos_clase_externo_ficheros_csv_sensor").show(funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_clase_externo_ficheros_csv_sensor);
    $("#tipo_horas_incrementos_clase_externo_ficheros_csv_sensor").change(funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_clase_externo_ficheros_csv_sensor);

    var funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_clase_externo_modbus_ip_sensor = function() {
        funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor("clase_externo_modbus_ip_sensor");
    };
    $("#tipo_horas_incrementos_clase_externo_modbus_ip_sensor").show(funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_clase_externo_modbus_ip_sensor);
    $("#tipo_horas_incrementos_clase_externo_modbus_ip_sensor").change(funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_clase_externo_modbus_ip_sensor);

    var funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_clase_externo_http_xml_powerstudio_sensor = function() {
        funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor("clase_externo_http_xml_powerstudio_sensor");
    };
    $("#tipo_horas_incrementos_clase_externo_http_xml_powerstudio_sensor").show(funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_clase_externo_http_xml_powerstudio_sensor);
    $("#tipo_horas_incrementos_clase_externo_http_xml_powerstudio_sensor").change(funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_clase_externo_http_xml_powerstudio_sensor);

    var funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_clase_externo_api_sensor = function() {
        funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor("clase_externo_api_sensor");
    };
    $("#tipo_horas_incrementos_clase_externo_api_sensor").show(funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_clase_externo_api_sensor);
    $("#tipo_horas_incrementos_clase_externo_api_sensor").change(funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor_clase_externo_api_sensor);

    // Botones de ayuda
    TLNT.Navegacion.establece_eventos_botones(TLNT.Navegacion.botones_controles_interfaces_sensores);
};


establece_eventos_ventanas_modales_sensores_clase_sensor = function() {
    // Desactivación de eventos anteriores
    $("#clase_energia_activa_id_tarifa_sensor").off();
    $("#clase_energia_activa_id_grupo_tarifas_sensor").off();
    $("#clase_energia_activa_tipo_fichero_validacion_facturas_sensor").off();
    $("#clase_gas_id_tarifa_sensor").off();
    $("#clase_gas_id_grupo_tarifas_sensor").off();

    // Realiza acciones al cambiar la tarifa eléctrica de un sensor
    var funcion_realiza_acciones_tarifa_electrica_sensor_modificada = function() {
        var id_tarifa = $("#clase_energia_activa_id_tarifa_sensor").val();
        if (id_tarifa != ID_NINGUNO.toString()) {
            $("#clase_energia_activa_id_grupo_tarifas_sensor").val(ID_NINGUNO).trigger("chosen:updated");
        }
    };
    $("#clase_energia_activa_id_tarifa_sensor").change(funcion_realiza_acciones_tarifa_electrica_sensor_modificada);

    // Realiza acciones al cambiar el grupo de tarifas eléctricas de un sensor
    var funcion_realiza_acciones_grupo_tarifas_electricas_sensor_modificada = function() {
        var id_grupo_tarifas = $("#clase_energia_activa_id_grupo_tarifas_sensor").val();
        if (id_grupo_tarifas != ID_NINGUNO.toString()) {
            $("#clase_energia_activa_id_tarifa_sensor").val(ID_NINGUNO).trigger("chosen:updated");
        }
    };
    $("#clase_energia_activa_id_grupo_tarifas_sensor").change(funcion_realiza_acciones_grupo_tarifas_electricas_sensor_modificada);

    // Realiza acciones al cambiar el tipo de fichero de validación de facturas
    var funcion_habilita_muestra_controles_tipo_fichero_validacion_facturas_sensor = function() {
        var tipo_fichero = $("#clase_energia_activa_tipo_fichero_validacion_facturas_sensor").val();
        if (tipo_fichero == TIPO_NINGUNO) {
            $("#clase_energia_activa_prefijo_fichero_validacion_facturas_sensor").removeClass('TLNT_input_mandatory');
        }
        else {
            $("#clase_energia_activa_prefijo_fichero_validacion_facturas_sensor").addClass('TLNT_input_mandatory');
        }
    };
    $("#clase_energia_activa_tipo_fichero_validacion_facturas_sensor").show(funcion_habilita_muestra_controles_tipo_fichero_validacion_facturas_sensor);
    $("#clase_energia_activa_tipo_fichero_validacion_facturas_sensor").change(funcion_habilita_muestra_controles_tipo_fichero_validacion_facturas_sensor);

    // Realiza acciones al cambiar la tarifa de gas de un sensor
    var funcion_realiza_acciones_tarifa_gas_sensor_modificada = function() {
        var id_tarifa = $("#clase_gas_id_tarifa_sensor").val();
        if (id_tarifa != ID_NINGUNO.toString()) {
            $("#clase_gas_id_grupo_tarifas_sensor").val(ID_NINGUNO).trigger("chosen:updated");
        }
    };
    $("#clase_gas_id_tarifa_sensor").change(funcion_realiza_acciones_tarifa_gas_sensor_modificada);

    // Realiza acciones al cambiar el grupo de tarifas de gas de un sensor
    var funcion_realiza_acciones_grupo_tarifas_gas_sensor_modificada = function() {
        var id_grupo_tarifas = $("#clase_gas_id_grupo_tarifas_sensor").val();
        if (id_grupo_tarifas != ID_NINGUNO.toString()) {
            $("#clase_gas_id_tarifa_sensor").val(ID_NINGUNO).trigger("chosen:updated");
        }
    };
    $("#clase_gas_id_grupo_tarifas_sensor").change(funcion_realiza_acciones_grupo_tarifas_gas_sensor_modificada);
};


establece_eventos_ventanas_modales_sensores_hijos_sensores = function() {
    // Desactivación de eventos anteriores
    $("#funcion_hijo_sensor_procesado").off();

    // Realiza acciones al cambiar la función de un hijo de sensor de procesado
    var funcion_realiza_acciones_funcion_hijo_sensor_procesado = function() {
        var funcion_hijo_sensor_procesado = $("#funcion_hijo_sensor_procesado").val();

        $.post("./src/lib/modulos/Nodos/administracion/dame_controles_parametros_funcion_hijo_sensor_procesado.php", {
			funcion_hijo_sensor_procesado: funcion_hijo_sensor_procesado
		},
		function (data, status) {
            var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#id_controles_parametros_funcion_hijo_sensor_procesado").html(resultado.html);
		});
    };
    $("#funcion_hijo_sensor_procesado").change(funcion_realiza_acciones_funcion_hijo_sensor_procesado);
};


establece_eventos_ventanas_modales_sensores_grupos_sensores = function() {
    // Desactivación de eventos anteriores
    $("#descripcion_grupo_sensores").off();

    // Contador de caracteres de descripción de grupo de sensores
    $("#descripcion_grupo_sensores").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);
};


establece_eventos_ventanas_modales_sensores_eventos = function() {
    // Desactivación de eventos anteriores
    $("#descripcion_evento").off();
    $("#clase_sensor_evento").off();
    $("#origen_evento").off();
    $("#id_origen_evento").off();
    $("#granularidad_evento").off();
    $("#tipo_evento").off();
    $("#campo_evento").off();
    $("#tipo_perfil_horario_evento_perfil_horario").off();

    // Contador de caracteres de descripción de evento
    $("#descripcion_evento").on('input', TLNT.Navegacion.actualiza_contador_caracteres_textarea);

    // Habilita la lista de identificadores de origenes de un evento
    var funcion_habilita_lista_ids_origenes_evento = function() {
        if ($("select#id_origen_evento option").length <= 1) {
            $("#id_origen_evento").prop('disabled', 'disabled');
        }
        else {
            $("#id_origen_evento").prop('disabled', false);
        }
        $("#id_origen_evento").trigger("chosen:updated");
    };

    // Recarga la lista de identificadores de origenes de un evento
    var funcion_recarga_lista_ids_origenes_evento = function() {
        $.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/dame_lista_ids_origenes_evento.php", {
			clase_sensor: $("#clase_sensor_evento").val(),
            origen: $("#origen_evento").val(),
            id_origen: $("#id_origen_evento").val(),
            opciones_extra: OPCIONES_EXTRA_LISTA_IDS_ORIGENES_EVENTO_NINGUNO
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#id_origen_evento").html(resultado.html);
            $("#id_origen_evento").trigger("chosen:updated");
            funcion_habilita_lista_ids_origenes_evento();
		});
    };

    // Muestra y habilita la lista de granularidades de un evento
    var funcion_muestra_habilita_lista_granularidades_evento = function() {
        // Se oculta si no hay clase de sensor seleccionada
        var clase_sensor = $("#clase_sensor_evento").val();
        if (clase_sensor == CLASE_NINGUNA) {
            $("#control_granularidad_evento").hide();
        }
        else {
            $("#control_granularidad_evento").show();

            // Se deshabilita si sólo hay un valor para elegir
            var numero_granularidades = $("select#granularidad_evento" + " option").length;
            if (numero_granularidades <= 1) {
                $("#granularidad_evento").attr('disabled', true);
            }
            else {
                $("#granularidad_evento").removeAttr('disabled');
            }
        }
    };

    // Recarga la lista de granularidades de un evento
    var funcion_recarga_lista_granularidades_evento = function() {
        $.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/dame_lista_granularidades_evento.php", {
			clase_sensor: $("#clase_sensor_evento").val(),
            granularidad: $("#granularidad_evento").val(),
            opciones_extra: OPCIONES_EXTRA_LISTA_GRANULARIDADES_EVENTO_SIN_OPCIONES_EXTRA
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#granularidad_evento").html(resultado.html);
            $("#granularidad_evento").trigger('change');
            funcion_muestra_habilita_lista_granularidades_evento();
		});
    };

    // Muestra y habilita la lista de tipos de un evento
    var funcion_muestra_habilita_lista_tipos_evento = function() {
        // Se oculta si no hay clase de sensor seleccionada
        var clase_sensor = $("#clase_sensor_evento").val();
        if (clase_sensor == CLASE_NINGUNA) {
            $("#control_tipo_evento").hide();
        }
        else {
            $("#control_tipo_evento").show();

            // Se deshabilita si sólo hay un valor para elegir
            var numero_tipos = $("select#tipo_evento" + " option").length;
            if (numero_tipos <= 1) {
                $("#tipo_evento").attr('disabled', true);
            }
            else {
                $("#tipo_evento").removeAttr('disabled');
            }
        }
    };

    // Recarga la lista de tipos de evento
    var funcion_recarga_lista_tipos_evento = function() {
        $.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/dame_lista_tipos_evento.php", {
			clase_sensor: $("#clase_sensor_evento").val(),
            origen: $("#origen_evento").val(),
            granularidad: $("#granularidad_evento").val(),
            tipo: $("#tipo_evento").val()
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#tipo_evento").html(resultado.html);
            $("#tipo_evento").trigger('change');
            funcion_muestra_habilita_lista_tipos_evento();
		});
    };

    // Habilita la lista de campos del evento
    var funcion_habilita_lista_campos_evento = function() {
        if ($("select#campo_evento option").length <= 1) {
            $("#campo_evento").prop('disabled', 'disabled');
        }
        else {
            $("#campo_evento").prop('disabled', false);
        }
    };
    $("#campo_evento").show(funcion_habilita_lista_campos_evento);

    // Recarga la lista de campos de un evento
    var funcion_recarga_lista_campos_evento = function() {
        $.post("./src/modulos/ModulosWeb/ModuloSensores/Eventos/dame_lista_campos_evento.php", {
			clase_sensor: $("#clase_sensor_evento").val(),
            granularidad: $("#granularidad_evento").val(),
            tipo: $("#tipo_evento").val(),
            campo: $("#campo_evento").val()
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#campo_evento").html(resultado.html);
            funcion_habilita_lista_campos_evento();
		});
    };

    // Habilita la lista de líneas base de un evento
    var funcion_habilita_lista_lineas_base_evento = function() {
        // Se deshabilita si sólo hay un valor para elegir
        var numero_lineas_base = $("select#id_linea_base_evento_linea_base" + " option").length;
        if (numero_lineas_base <= 1) {
            $("#id_linea_base_evento_linea_base").attr('disabled', true);
        }
        else {
            $("#id_linea_base_evento_linea_base").removeAttr('disabled');
        }
        $("#id_linea_base_evento_linea_base").trigger("chosen:updated");
    };

    // Recarga la lista de líneas base de un evento
    var funcion_recarga_lista_lineas_base_evento = function() {
        $.post("./src/modulos/ModulosWeb/ModuloProyectos/LineasBase/dame_lista_lineas_base_sensor.php", {
            id_sensor: $("#id_origen_evento").val(),
            id_linea_base: $("#id_linea_base_evento_linea_base").val()
		},
		function (data, status) {
			var resultado = dame_resultado_ejecucion_script_php_json(data);
            if (resultado == null) {
                return;
            }

			$("#id_linea_base_evento_linea_base").html(resultado.html);
            $("#id_linea_base_evento_linea_base").trigger("chosen:updated");
            funcion_habilita_lista_lineas_base_evento();
		});
    };

    // Al mostrar la clase (de sensor), hay que mostrar y habilitar diferentes controles
    var funcion_realiza_acciones_clase_sensor_evento_visible = function() {
        funcion_habilita_lista_ids_origenes_evento();
        funcion_muestra_habilita_lista_granularidades_evento();
        funcion_muestra_habilita_lista_tipos_evento();
        funcion_habilita_lista_campos_evento();
    };
    $("#clase_sensor_evento").show(funcion_realiza_acciones_clase_sensor_evento_visible);

    // Al modificar la clase (de sensor), hay que recargar los identificadores de los orígenes y las granularidades
    // (al cambiar la granularidad se recargan los tipos, y al cambiar el tipo se recargan los campos)
    var funcion_realiza_acciones_clase_sensor_evento_modificada = function() {
        funcion_recarga_lista_ids_origenes_evento();
        funcion_recarga_lista_granularidades_evento();
    };
    $("#clase_sensor_evento").change(funcion_realiza_acciones_clase_sensor_evento_modificada);

    // Si se modifica el origen, hay que recargar los identificadores de los orígenes y los tipos de evento
    var funcion_realiza_acciones_origen_evento_modificado = function() {
        funcion_recarga_lista_ids_origenes_evento();
        funcion_recarga_lista_tipos_evento();
    };
    $("#origen_evento").change(funcion_realiza_acciones_origen_evento_modificado);

    // Si se modifica el identificador de origen, hay que recargar las líneas base del evento
    // (si el origen es sensor y el tipo de evento es línea base)
    var funcion_realiza_acciones_id_origen_evento_modificado = function() {
        var origen_evento = $("#origen_evento").val();
        var tipo_evento = $("#tipo_evento").val();
        if ((origen_evento == ORIGEN_EVENTO_SENSOR) && (tipo_evento == TIPO_EVENTO_LINEA_BASE)) {
            funcion_recarga_lista_lineas_base_evento();
        }
    };
    $("#id_origen_evento").change(funcion_realiza_acciones_id_origen_evento_modificado);

    // Funciones a realizar según la granularidad del evento
    var funcion_realiza_acciones_granularidad_evento = function() {
        funcion_recarga_lista_tipos_evento();
    };
    $("#granularidad_evento").change(funcion_realiza_acciones_granularidad_evento);

    // Muestra los controles dependientes del tipo de perfil horario
    // - Nota: Si dentro de una función (A), se llama a otra función (B), está última tiene que definirse antes (B antes que A)
    var funcion_muestra_controles_tipo_perfil_horario_evento = function() {
        var tipo_perfil_horario = $("#tipo_perfil_horario_evento_perfil_horario").val();
        switch (tipo_perfil_horario) {
            case TIPO_PERFIL_HORARIO_CONFIGURABLE: {
                $("#control_cadena_agrupaciones_dias_semana_evento_perfil_horario").show();
                break;
            }
            default: {
                $("#control_cadena_agrupaciones_dias_semana_evento_perfil_horario").hide();
                break;
            }
        }
    };
    $("#tipo_perfil_horario_evento_perfil_horario").change(funcion_muestra_controles_tipo_perfil_horario_evento);

    // Habilita y muestra los controles dependientes del tipo de evento
    var funcion_habilita_muestra_controles_tipo_evento = function() {
        $("#parametros_evento").removeClass('TLNT_input_mandatory');
        $("#numero_dias_perfil_horario_evento_perfil_horario").removeClass('TLNT_input_mandatory');
        $("#control_campo_evento").hide();
        $("#control_periodo_evento_incremento_acumulado_maximo").hide();
        $("#control_id_linea_base_evento_linea_base").hide();
        $("#control_intervalo_valores_evento_perfil_horario").hide();
        $("#control_numero_dias_perfil_horario_evento_perfil_horario").hide();
        $("#control_tipo_perfil_horario_evento_perfil_horario").hide();
        $("#control_cadena_agrupaciones_dias_semana_evento_perfil_horario").hide();
        $("#control_parametros_evento").hide();

        var mostrar_pestanya_horario_semanal_fechas = false;
        var mostrar_horario_semanal = true;
        var mostrar_exclusion_fechas = true;
        var mostrar_inclusion_fechas = true;

        var tipo_evento = $("#tipo_evento").val();
        switch (tipo_evento) {
            case TIPO_EVENTO_INCREMENTO_TEMPORAL_MINIMO:
            case TIPO_EVENTO_INCREMENTO_TEMPORAL_MAXIMO:
            case TIPO_EVENTO_VALOR_MINIMO:
            case TIPO_EVENTO_VALOR_MAXIMO:
            case TIPO_EVENTO_VALORES_MINIMO_MAXIMO:
            case TIPO_EVENTO_INTERVALO_VALORES:
            case TIPO_EVENTO_VALOR_EXACTO:
            case TIPO_EVENTO_VALOR_DIFERENTE:
            case TIPO_EVENTO_VALOR_EXACTO_BITS:
            case TIPO_EVENTO_VALOR_DIFERENTE_BITS:
            case TIPO_EVENTO_VALOR_REPETIDO: {
                $("#parametros_evento").addClass('TLNT_input_mandatory');
                $("#control_campo_evento").show();
                $("#control_parametros_evento").show();
                break;
            }
            case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_PERIODO_TIEMPO_ACTUAL:
            case TIPO_EVENTO_INCREMENTO_ACUMULADO_MAXIMO_ULTIMOS_PERIODOS_TIEMPO: {
                $("#parametros_evento").addClass('TLNT_input_mandatory');
                $("#control_campo_evento").show();
                $("#control_periodo_evento_incremento_acumulado_maximo").show();
                $("#control_parametros_evento").show();
                break;
            }
            case TIPO_EVENTO_LINEA_BASE: {
                $("#parametros_evento").addClass('TLNT_input_mandatory');
                $("#control_id_linea_base_evento_linea_base").show();
                $("#control_parametros_evento").show();
                break;
            }
            case TIPO_EVENTO_PERFIL_HORARIO: {
                $("#parametros_evento").addClass('TLNT_input_mandatory');
                $("#numero_dias_perfil_horario_evento_perfil_horario").addClass('TLNT_input_mandatory');
                $("#control_campo_evento").show();
                $("#control_parametros_evento").show();
                $("#control_intervalo_valores_evento_perfil_horario").show();
                $("#control_numero_dias_perfil_horario_evento_perfil_horario").show();
                $("#control_tipo_perfil_horario_evento_perfil_horario").show();
                funcion_muestra_controles_tipo_perfil_horario_evento();
                mostrar_pestanya_horario_semanal_fechas = true;
                mostrar_inclusion_fechas = false;
                break;
            }
        }

        // Pestaña de parámetros
        if (tipo_evento == ID_NINGUNO.toString()) {
            $("#titulo-tab-parametros").hide();
        }
        else {
            $("#titulo-tab-parametros").show();
        }

        // Pestaña de horario semanal y fechas
        if (mostrar_pestanya_horario_semanal_fechas == true) {
            $("#titulo-tab-horario-semanal-fechas").show();
            if (mostrar_horario_semanal == true) {
                $("#control_horario_semanal_evento").show();
            }
            else {
                $("#control_horario_semanal_evento").hide();
            }
            if (mostrar_exclusion_fechas == true) {
                $("#control_exclusion_fechas_evento").show();
            }
            else {
                $("#control_exclusion_fechas_evento").hide();
            }
            if (mostrar_inclusion_fechas == true) {
                $("#control_inclusion_fechas_evento").show();
            }
            else {
                $("#control_inclusion_fechas_evento").hide();
            }
        }
        else {
            $("#titulo-tab-horario-semanal-fechas").hide();
        }
    };
    $("#tipo_evento").show(funcion_habilita_muestra_controles_tipo_evento);
    $("#tipo_evento").change(funcion_habilita_muestra_controles_tipo_evento);

    // Funciones a realizar según el tipo del evento
    var funcion_realiza_acciones_tipo_evento = function() {
        funcion_habilita_muestra_controles_tipo_evento();
        funcion_recarga_lista_campos_evento();

        // Recarga de lista de líneas base (si es necesario)
        var tipo_evento = $("#tipo_evento").val();
        switch (tipo_evento) {
            case TIPO_EVENTO_LINEA_BASE: {
                funcion_realiza_acciones_id_origen_evento_modificado();
                break;
            }
        }
    };
    $("#tipo_evento").change(funcion_realiza_acciones_tipo_evento);
};


//
// Funciones auxiliares (utilizadas en varias funciones)
//


// Recarga de los sensores de una clase de los controles especificados
funcion_recarga_sensores_clase = function(id_controles) {
    var clase_sensor = $("#clase_sensor_" + id_controles).val();
    $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores.php", {
        clase_sensor: clase_sensor,
        opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_NINGUNO
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#id_sensor_" + id_controles).html(resultado.html);
        $("#id_sensor_" + id_controles).trigger("chosen:updated");
        switch (clase_sensor) {
            case CLASE_NINGUNA: {
                $("#id_sensor_" + id_controles).attr('disabled', true).trigger("chosen:updated");
                break;
            }
            default: {
                $("#id_sensor_" + id_controles).removeAttr('disabled').trigger("chosen:updated");
                break;
            }
        }
    });
};


// Recarga de lista doble de sensores de una clase
funcion_recarga_lista_doble_sensores_clase = function(id_controles, clase_sensor) {
    $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores.php", {
        clase_sensor: clase_sensor,
        opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Recarga de lista doble
        // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
        $("#ids_sensores_" + id_controles).multiselect2side('destroy');
        $("#ids_sensores_" + id_controles).html(resultado.html);
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_" + id_controles, true);
    });
};


// Recarga de lista doble de sensores de varias clases
funcion_recarga_lista_doble_sensores_clases = function(id_controles, clases_sensor) {
    var ids_sensores = [];
    $("#ids_sensores_" + id_controles + " option").each(function() {
        if (typeof($(this).attr("selected")) !== "undefined") {
            ids_sensores.push($(this).val());
        }
    });
    $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores_clases.php", {
        clases_sensor: clases_sensor,
        ids_sensores: ids_sensores,
        opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Recarga de lista doble
        // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
        $("#ids_sensores_" + id_controles).multiselect2side('destroy');
        $("#ids_sensores_" + id_controles).html(resultado.html);
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_" + id_controles, true);
    });
};


// Carga la lista de campos en el control especificado
funcion_carga_lista_campos_clase_sensor = function(id_controles, html_lista_campos, clase_sensor) {
    $("#campo_" + id_controles).html(html_lista_campos);

    // Se deshabilita si sólo hay un valor para elegir
    var numero_campos = $("select#campo_" + id_controles + " option").length;
    if (numero_campos <= 1) {
        $("#campo_" + id_controles).attr('disabled', true);
    }
    else {
        $("#campo_" + id_controles).removeAttr('disabled');
    }

    // Valor seleccionado por defecto
    switch (clase_sensor) {
        case CLASE_SENSOR_ENERGIA_ACTIVA:
        case CLASE_SENSOR_ENERGIA_REACTIVA:
        case CLASE_SENSOR_AGUA: {
            $("#campo_" + id_controles).val(CAMPO_INCREMENTO);
            break;
        }
        case CLASE_SENSOR_COMPRA_ENERGIA: {
            $("#campo_" + id_controles).val(CAMPO_CONSUMO_ESTIMADO);
            break;
        }
        case CLASE_SENSOR_GAS: {
            $("#campo_" + id_controles).val(CAMPO_CONSUMO);
            break;
        }
    }

    $("#campo_" + id_controles).trigger('change');
};


// Recarga de los campos de una clase de sensor de los controles especificados
funcion_recarga_campos_clase_sensor = function(id_controles, tipo_agrupacion_valores, intervalo_valores) {
    var clase_sensor = $("#clase_sensor_" + id_controles).val();
    $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_campos_clase_sensor_parametros_extra.php", {
        clase_sensor: clase_sensor,
        tipo_agrupacion_valores: tipo_agrupacion_valores,
        intervalo_valores: intervalo_valores,
        campo: ""
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se carga la lista en el control correspondiente
        funcion_carga_lista_campos_clase_sensor(id_controles, resultado.html, clase_sensor);
    });
};

// Recarga de los campos incrementos de una clase de sensor de los controles especificados
funcion_recarga_campos_incrementos_clase_sensor = function(id_controles, intervalo_valores) {
    var clase_sensor = $("#clase_sensor_" + id_controles).val();
    $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_campos_incrementos_clase_sensor_tipo_agrupacion_valores_parametros_extra.php", {
        clase_sensor: clase_sensor,
        intervalo_valores: intervalo_valores,
        campo: ""
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Se carga la lista en el control correspondiente
        funcion_carga_lista_campos_clase_sensor(id_controles, resultado.html, clase_sensor);
    });
};


// Recarga de los sensores y los campos de una clase de los controles especificados
funcion_recarga_sensores_campos_clase = function(id_controles, tipo_agrupacion_valores, intervalo_valores) {
    var clase_sensor = $("#clase_sensor_" + id_controles).val();
    $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores_campos_clase_sensor_parametros_extra.php", {
        clase_sensor: clase_sensor,
        opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_NINGUNO,
        tipo_agrupacion_valores: tipo_agrupacion_valores,
        intervalo_valores: intervalo_valores,
        campo: ""
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Sensor
        $("#id_sensor_" + id_controles).html(resultado.html_lista_sensores);
        $("#id_sensor_" + id_controles).trigger("chosen:updated");
        switch (clase_sensor) {
            case CLASE_NINGUNA: {
                $("#id_sensor_" + id_controles).attr('disabled', true).trigger("chosen:updated");
                break;
            }
            default: {
                $("#id_sensor_" + id_controles).removeAttr('disabled').trigger("chosen:updated");
                break;
            }
        }

        // Se carga la lista en el control correspondiente
        funcion_carga_lista_campos_clase_sensor(id_controles, resultado.html_lista_campos, clase_sensor);
    });
};


// Recarga de los sensores y los campos de una clase de los controles especificados
funcion_recarga_lista_doble_sensores_campos_clase = function(id_controles, tipo_agrupacion_valores, intervalo_valores) {
    var clase_sensor = $("#clase_sensor_" + id_controles).val();
    $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_sensores_campos_clase_sensor_parametros_extra.php", {
        clase_sensor: clase_sensor,
        opciones_extra: OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA,
        tipo_agrupacion_valores: tipo_agrupacion_valores,
        intervalo_valores: intervalo_valores,
        campo: ""
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        // Recarga de lista doble
        // (http://stackoverflow.com/questions/8653301/jquery-multiselect-reload)
        $("#ids_sensores_" + id_controles).multiselect2side('destroy');
        $("#ids_sensores_" + id_controles).html(resultado.html_lista_sensores);
        TLNT.Navegacion.convierte_lista_doble("ids_sensores_" + id_controles, true);

        // Se carga la lista en el control correspondiente
        funcion_carga_lista_campos_clase_sensor(id_controles, resultado.html_lista_campos, clase_sensor);
    });
};


// Habilita la lista de intervalos de valores de informes
funcion_habilita_intervalos_valores_informes = function(id_controles) {
    // Se deshabilita si sólo hay un valor para elegir
    var numero_intervalos = $("select#intervalo_valores_sensores_" + id_controles + " option").length;
    if (numero_intervalos <= 1) {
        $("#intervalo_valores_sensores_" + id_controles).attr('disabled', true);
    }
    else {
        $("#intervalo_valores_sensores_" + id_controles).removeAttr('disabled');
    }
};


// Recarga la lista de intervalos de valores de informes de información y comparación
funcion_recarga_intervalos_valores_informes_informacion_comparacion = function(id_controles, clase_sensor) {
    var campo = $("#campo_sensores_" + id_controles).val();
    var intervalo_valores = $("#intervalo_valores_sensores_" + id_controles).val();
    $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_intervalos_valores_informes_informacion_comparacion_clase_sensor_campo.php", {
        clase_sensor: clase_sensor,
        campo: campo,
        intervalo_valores: intervalo_valores,
        opciones_extra: OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_SIN_OPCIONES_EXTRA
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#intervalo_valores_sensores_" + id_controles).html(resultado.html);

        // Intervalo seleccionado por defecto
        if (intervalo_valores == INTERVALO_VALORES_NINGUNO) {
            switch (clase_sensor) {
                case CLASE_SENSOR_ENERGIA_ACTIVA: {
                    intervalo_valores = INTERVALO_VALORES_HORA;
                    break;
                }
                case CLASE_SENSOR_CORTES_TENSION: {
                    intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL_LINEAS;
                    break;
                }
                default: {
                    intervalo_valores = INTERVALO_VALORES_HORA;
                    break;
                }
            }
            $("#intervalo_valores_sensores_" + id_controles).val(intervalo_valores);
        }

        $("#intervalo_valores_sensores_" + id_controles).trigger('change');
    });
};


// Recarga de los campos de una clase de sensor según el intervalo de valores de los controles especificados
funcion_recarga_campos_intervalo_valores = function(id_controles, tipo_agrupacion_valores, intervalo_valores) {
    if (id_controles == "sensores_informacion_generica") {
        var clase_sensor = CLASE_SENSOR_GENERICA;
    }
    else {
        var clase_sensor = $("#clase_sensor_" + id_controles).val();
        if (clase_sensor != CLASE_SENSOR_GENERICA) {
            return;
        }
    }
    var campo = $("#campo_" + id_controles).val();
    $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_campos_clase_sensor_parametros_extra.php", {
        clase_sensor: clase_sensor,
        tipo_agrupacion_valores: tipo_agrupacion_valores,
        intervalo_valores: intervalo_valores,
        campo: campo
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#campo_" + id_controles).html(resultado.html);

        // Se deshabilita si sólo hay un valor para elegir
        var numero_campos = $("select#campo_" + id_controles + " option").length;
        if (numero_campos <= 1) {
            $("#campo_" + id_controles).attr('disabled', true);
        }
        else {
            $("#campo_" + id_controles).removeAttr('disabled');
        }
    });
};


// Recarga de los campos incrementos de una clase de sensor según el intervalo de valores de los controles especificados
funcion_recarga_campos_incrementos_intervalo_valores = function(id_controles, intervalo_valores) {
    var clase_sensor = $("#clase_sensor_" + id_controles).val();
    if (clase_sensor != CLASE_SENSOR_GENERICA) {
        return;
    }

    var campo = $("#campo_" + id_controles).val();
    $.post("./src/modulos/ModulosWeb/ModuloSensores/dame_lista_campos_incrementos_clase_sensor_tipo_agrupacion_valores_parametros_extra.php", {
        clase_sensor: clase_sensor,
        intervalo_valores: intervalo_valores,
        campo: campo
    },
    function (data, status) {
        var resultado = dame_resultado_ejecucion_script_php_json(data);
        if (resultado == null) {
            return;
        }

        $("#campo_" + id_controles).html(resultado.html);

        // Se deshabilita si sólo hay un valor para elegir
        var numero_campos = $("select#campo_" + id_controles + " option").length;
        if (numero_campos <= 1) {
            $("#campo_" + id_controles).attr('disabled', true);
        }
        else {
            $("#campo_" + id_controles).removeAttr('disabled');
        }
    });
};


//
// Funciones de formatos de ficheros de valores
//


// Establece los valores por defecto dependiendo del formato de fichero de valores
funcion_realiza_acciones_formato_fichero_valores = function(clase_sensor, id_controles) {
    // Formato de fichero de valores
    var opcion_formato_fichero_valores = $("#formato_fichero_valores_" + id_controles).find('option:selected');
    var caracter_separador = opcion_formato_fichero_valores.attr('caracter_separador');
    var numero_filas_cabecera = opcion_formato_fichero_valores.attr('numero_filas_cabecera');
    var columna_fecha = opcion_formato_fichero_valores.attr('columna_fecha');
    var formato_fecha = opcion_formato_fichero_valores.attr('formato_fecha');
    var hora_columna_independiente = opcion_formato_fichero_valores.attr('hora_columna_independiente');
    var columna_hora = opcion_formato_fichero_valores.attr('columna_hora');
    var formato_hora = opcion_formato_fichero_valores.attr('formato_hora');
    var datadis = opcion_formato_fichero_valores.attr('datadis');
    if (typeof(caracter_separador) !== "undefined") {
        $("#caracter_separador_" + id_controles).val(caracter_separador);
    }
    if (typeof(numero_filas_cabecera) !== "undefined") {
        $("#numero_lineas_cabeceras_" + id_controles).val(numero_filas_cabecera);
    }
    if (typeof(columna_fecha) !== "undefined") {
        $("#columna_fecha_" + id_controles).val(columna_fecha);
    }
    if (typeof(formato_fecha) !== "undefined") {
        $("#formato_fecha_" + id_controles).val(formato_fecha);
    }
    if (typeof(hora_columna_independiente) !== "undefined") {
        $("#hora_columna_independiente_" + id_controles).val(hora_columna_independiente);
        funcion_habilita_muestra_controles_hora_columna_independiente(id_controles);
    }
    if (typeof(columna_hora) !== "undefined") {
        $("#columna_hora_" + id_controles).val(columna_hora);
    }
    if (typeof(formato_hora) !== "undefined") {
        $("#formato_hora_" + id_controles).val(formato_hora);
    }

    // Números de columnas
    funcion_establece_columnas_horario_verano_valores(clase_sensor, id_controles);

    // Tipo de valores
    function_establece_tipo_valores_sensor(clase_sensor, id_controles);
};


// Función para establecer el formato a personalizado si se modifican 'manualmente' los parámetros del fichero de valores
function_establece_formato_fichero_valores_personalizado = function(id_control_modificado, id_controles) {
    var formato_fichero_valores = $("#formato_fichero_valores_" + id_controles).val();
    if (formato_fichero_valores == FORMATO_FICHERO_VALORES_PERSONALIZADO) {
        return;
    }

    var opcion_formato_fichero_valores = $("#formato_fichero_valores_" + id_controles).find('option:selected');
    var valor_parametro_formato_fichero_valores = undefined;
    switch (id_control_modificado) {
        case "caracter_separador_" + id_controles: {
            valor_parametro_formato_fichero_valores = opcion_formato_fichero_valores.attr("caracter_separador");
            break;
        }
        case "numero_lineas_cabeceras_" + id_controles: {
            valor_parametro_formato_fichero_valores = opcion_formato_fichero_valores.attr("numero_filas_cabecera");
            break;
        }
        case "columna_fecha_" + id_controles: {
            valor_parametro_formato_fichero_valores = opcion_formato_fichero_valores.attr("columna_fecha");
            break;
        }
        case "formato_fecha_" + id_controles: {
            valor_parametro_formato_fichero_valores = opcion_formato_fichero_valores.attr("formato_fecha");
            break;
        }
        case "hora_columna_independiente_" + id_controles: {
            valor_parametro_formato_fichero_valores = opcion_formato_fichero_valores.attr("hora_columna_independiente");
            break;
        }
        case "columna_hora_" + id_controles: {
            valor_parametro_formato_fichero_valores = opcion_formato_fichero_valores.attr("columna_hora");
            break;
        }
        case "formato_hora_" + id_controles: {
            valor_parametro_formato_fichero_valores = opcion_formato_fichero_valores.attr("formato_hora");
            break;
        }
        case "tipo_valores_sensor_" + id_controles: {
            valor_parametro_formato_fichero_valores = opcion_formato_fichero_valores.attr("tipo_valores_sensor");
            break;
        }
        case "tipo_incrementos_" + id_controles: {
            valor_parametro_formato_fichero_valores = opcion_formato_fichero_valores.attr("tipo_incrementos");
            break;
        }
    };
    var existe_parametro_formato_fichero_valores = (typeof(valor_parametro_formato_fichero_valores) !== "undefined");
    if (existe_parametro_formato_fichero_valores == true) {
        $("#formato_fichero_valores_" + id_controles).val(FORMATO_FICHERO_VALORES_PERSONALIZADO);
    }
};


// Establece las columnas de horario de verano y de valores
funcion_establece_columnas_horario_verano_valores = function(clase_sensor, id_controles) {
    if (clase_sensor == CLASE_NINGUNA) {
        $("#columnas_valores_" + id_controles).val("");
    }
    var formato_fichero_valores = $("#formato_fichero_valores_" + id_controles).val();
    if (formato_fichero_valores == FORMATO_FICHERO_VALORES_PERSONALIZADO) {
        return;
    }

    var columna_horario_verano = "";
    var columnas_valores = "";
    switch (formato_fichero_valores) {
        case FORMATO_FICHERO_VALORES_WEB_EMIOS: {
            if (clase_sensor != CLASE_NINGUNA) {
                var caracteristicas_clase_sensor = dame_caracteristicas_clase_sensor(clase_sensor);
                var tipo_clase_sensor = caracteristicas_clase_sensor["tipo"];

                // Nota: Si la clase es incremental no se rellenan automáticamente porque las columnas pueden variar
                // si la exportación ha sido en tiempo real o en valores horarios (o cuartohorarios)
                if (tipo_clase_sensor == TIPO_CLASE_SENSOR_PUNTUAL) {
                    var numero_valores_clase_sensor = caracteristicas_clase_sensor["numero_valores"];
                    columna_horario_verano = numero_valores_clase_sensor + 2;
                    for (var i = 1; i <= numero_valores_clase_sensor; i++) {
                        if (columnas_valores != "") {
                            columnas_valores += ",";
                        }
                        columnas_valores += (i + 1);
                    }
                }
            }
            break;
        }
        default: {
            var opcion_formato_fichero_valores = $("#formato_fichero_valores_" + id_controles).find('option:selected');
            var valor_parametro_columna_horario_verano = opcion_formato_fichero_valores.attr("columna_horario_verano");
            var valor_parametro_columnas_valores = opcion_formato_fichero_valores.attr("columnas_valores_" + clase_sensor);
            if (typeof(valor_parametro_columna_horario_verano) !== "undefined") {
                columna_horario_verano = valor_parametro_columna_horario_verano;
            }
            if (typeof(valor_parametro_columnas_valores) !== "undefined") {
                columnas_valores = valor_parametro_columnas_valores;
            }
            break;
        }
    }
    $("#columna_horario_verano_" + id_controles).val(columna_horario_verano);
    $("#columnas_valores_" + id_controles).val(columnas_valores);
};


// Establece el tipo de valores del sensor
function_establece_tipo_valores_sensor = function(clase_sensor, id_controles) {
    if (clase_sensor == CLASE_NINGUNA) {
        $("#tipo_valores_sensor_" + id_controles).val(TIPO_NINGUNO);
        $("#tipo_valores_sensor_" + id_controles).attr('disabled', 'disabled');
    }
    else {
        var caracteristicas_clase_sensor = dame_caracteristicas_clase_sensor(clase_sensor);
        var formato_fichero_valores = $("#formato_fichero_valores_" + id_controles).val();
        if (formato_fichero_valores == FORMATO_FICHERO_VALORES_PERSONALIZADO) {
            var tipo_clase_sensor = caracteristicas_clase_sensor["tipo"];
            switch (tipo_clase_sensor) {
                case TIPO_CLASE_SENSOR_PUNTUAL: {
                    $("#tipo_valores_sensor_" + id_controles).val(TIPO_VALORES_SENSOR_PUNTUALES);
                    $("#tipo_valores_sensor_" + id_controles).attr('disabled', 'disabled');
                    break;
                }
                case TIPO_CLASE_SENSOR_INCREMENTAL: {
                    $("#tipo_valores_sensor_" + id_controles).val(TIPO_NINGUNO);
                    $("#tipo_valores_sensor_" + id_controles).removeAttr('disabled');
                    break;
                }
            }
        }
        else {
            var opcion_formato_fichero_valores = $("#formato_fichero_valores_" + id_controles).find('option:selected');
            var tipo_valores_sensor = opcion_formato_fichero_valores.attr('tipo_valores_sensor');
            var tipo_incrementos = opcion_formato_fichero_valores.attr('tipo_incrementos');
            var tipo_clase_sensor = caracteristicas_clase_sensor["tipo"];
            switch (tipo_clase_sensor) {
                case TIPO_CLASE_SENSOR_PUNTUAL: {
                    $("#tipo_valores_sensor_" + id_controles).val(TIPO_VALORES_SENSOR_PUNTUALES);
                    $("#tipo_valores_sensor_" + id_controles).attr('disabled', true);
                    break;
                }
                case TIPO_CLASE_SENSOR_INCREMENTAL: {
                    if (typeof(tipo_valores_sensor) === "undefined") {
                        tipo_valores_sensor = TIPO_NINGUNO;
                    }
                    if (typeof(tipo_incrementos) === "undefined") {
                        tipo_incrementos = TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL;
                    }
                    $("#tipo_valores_sensor_" + id_controles).val(tipo_valores_sensor);
                    $("#tipo_incrementos_" + id_controles).val(tipo_incrementos);
                    $("#tipo_valores_sensor_" + id_controles).removeAttr('disabled');
                    break;
                }
            }
        }
    }
    funcion_habilita_muestra_controles_tipo_valores_sensor(id_controles);
};


//
// Funciones de controles de ficheros de valores
//


// Habilita y muestra los controles de hora en columna independiente
var funcion_habilita_muestra_controles_hora_columna_independiente = function(id_controles) {
    var hora_columna_independiente = $("#hora_columna_independiente_" + id_controles).val();
    if (hora_columna_independiente == VALOR_NO) {
        $("#columna_hora_" + id_controles).removeClass('TLNT_input_mandatory TLNT_input_numerical');
        $("#formato_hora_" + id_controles).removeClass('TLNT_input_mandatory');
        $("#control_columna_hora_" + id_controles).hide();
        $("#control_formato_hora_" + id_controles).hide();
    }
    else {
        $("#columna_hora_" + id_controles).addClass('TLNT_input_mandatory TLNT_input_numerical');
        $("#formato_hora_" + id_controles).addClass('TLNT_input_mandatory');
        $("#control_columna_hora_" + id_controles).show();
        $("#control_formato_hora_" + id_controles).show();
    }
};


// Habilita y muestra el control de columna de horario de verano
var funcion_habilita_muestra_control_columna_horario_verano = function(id_controles) {
    var zona_horaria = $("#zona_horaria_" + id_controles).val();
    if (zona_horaria == ZONA_HORARIA_UTC) {
        $("#control_columna_horario_verano_" + id_controles).hide();
    }
    else {
        $("#control_columna_horario_verano_" + id_controles).show();
    }
};


// Habilita y muestra los controles dependientes del tipo de valores seleccionado
var funcion_habilita_muestra_controles_tipo_valores_sensor = function(id_controles) {
    var tipo_valores = $("#tipo_valores_sensor_" + id_controles).val();
    switch (tipo_valores) {
        case TIPO_NINGUNO:
        case TIPO_VALORES_SENSOR_PUNTUALES: {
            $("#horas_incrementos_" + id_controles).removeClass('TLNT_input_mandatory TLNT_input_float');
            $("#control_tipo_horas_incrementos_" + id_controles).hide();
            $("#control_horas_incrementos_" + id_controles).hide();
            $("#control_tipo_incrementos_" + id_controles).hide();
            break;
        }
        case TIPO_VALORES_SENSOR_INCREMENTALES: {
            $("#horas_incrementos_" + id_controles).addClass('TLNT_input_mandatory TLNT_input_float');
            $("#control_tipo_horas_incrementos_" + id_controles).show();
            funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor(id_controles);
            $("#control_tipo_incrementos_" + id_controles).show();
            break;
        }
    }
};


// Habilita y muestra los controles dependientes del tipo de horas de incrementos de valores seleccionado
var funcion_habilita_muestra_controles_tipo_horas_incrementos_valores_sensor = function(id_controles) {
    var tipo_horas_incrementos = $("#tipo_horas_incrementos_" + id_controles).val();
    switch (tipo_horas_incrementos) {
        case TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO: {
            $("#control_horas_incrementos_" + id_controles).show();
            break;
        }
        case TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_VARIABLE: {
            $("#control_horas_incrementos_" + id_controles).hide();
            $("#horas_incrementos_" + id_controles).val(0);
            break;
        }
    }
};