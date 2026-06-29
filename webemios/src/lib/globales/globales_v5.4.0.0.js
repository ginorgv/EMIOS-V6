// Formatos de fecha
var formato_fecha_local_jqplot = FORMATO_FECHA_LOCAL_JPLOT_DEFECTO;
var formato_fecha_local_jquery_ui = FORMATO_FECHA_LOCAL_JQUERY_UI_DEFECTO;
var formato_dia_anyo_local_jqplot = FORMATO_DIA_ANYO_LOCAL_JPLOT_DEFECTO;
var formato_dia_anyo_local_jquery_ui = FORMATO_DIA_ANYO_LOCAL_JQUERY_UI_DEFECTO;

// Unidades
var moneda = MONEDA_DEFECTO;
var unidad_medida_temperatura = UNIDAD_MEDIDA_TEMPERATURA_DEFECTO;
var unidad_medida_velocidad = UNIDAD_MEDIDA_VELOCIDAD_DEFECTO;

// Países de tarifas
var pais_tarifas_electricas = PAIS_TARIFAS_ELECTRICAS_DEFECTO;
var pais_tarifas_gas = PAIS_TARIFAS_GAS_DEFECTO;
var pais_tarifas_agua = PAIS_TARIFAS_AGUA_DEFECTO;

// Medición por defecto
var medicion = MEDICION_DEFECTO;

// Variables globales utilizadas en los mapas
var mapa_global = null;
var capas_mapa_globales = null;

// Latitud, longitud y zoom por defecto del mapa
var latitud_mapa_defecto = null;
var latitud_mapa_defecto = null;
var zoom_mapa_defecto = null;

// Variables globales de mapa
var tipo_mapa = null;
var nombre_mapa = null;
var ruta_fichero_imagen_mapa_local = null;
var anchura_imagen_mapa_local = null;
var altura_imagen_mapa_local = null;
var factor_reduccion_imagen_mapa_local = null;
var multiplicador_distancia_cluster_mapa = null;

// Filtro de mapa (para mostrar todas las capas)
var texto_filtro_mapa = null;
var tipo_filtro_mapa = null;
var clase_filtro_mapa = null;
var id_grupo_filtro_mapa = null;
var estado_filtro_mapa = null;
var id_ratio_filtro_mapa = null;

// Actualización periódica de página
var temporizador_actualizacion_pagina = null;

// Flag de pantalla completa activada en la última actualizacion o centrado del mapa
var pantalla_completa_activada_ultima_actualizacion_centrado_mapa = false;

// Intervalos de actualizaciones periódicas
var tipo_nodo_actualizacion_periodica = null;
var segundos_intervalo_actualizacion_tabla_nodos = null;
var actualizacion_periodica_operaciones_datos_sensores_activada = false;
var segundos_intervalo_actualizacion_tabla_operaciones_datos_sensores = null;
var actualizacion_periodica_eventos_activada = false;
var segundos_intervalo_actualizacion_tabla_eventos = null;
var actualizacion_periodica_reglas_activada = false;
var segundos_intervalo_actualizacion_tabla_reglas = null;
var segundos_intervalo_actualizacion_mapa = null;
var segundos_intervalo_actualizacion_widgets = null;

// Variables de actualización periódica de widgets
var ids_pestanyas_actualizacion_periodica_widgets = [];
var numero_actualizacion_periodica_widgets = 0;

// Actualización de fecha y hora de pestaña de widgets
var temporizador_actualizacion_fecha_hora_pestanya_widgets = null;

// Flags de librerías soportadas
var libreria_dom_to_image_soportada = null;

// Variables de gráficas de jqplot
var graficas_jqplot = [];
var parametros_graficas_jqplot = [];

// Colores de series de gráficas de jqplot
var colores_graficas_jqplot = [];

// Pantalla completa al inicio
var pantalla_completa_inicio = false;

// Variables para recuperar información de una gráfica con el botón derecho
var info_grafica_boton_derecho_recuperada = false;
var fecha_hora_grafica_boton_derecho = null;
var indice_serie_grafica_boton_derecho = null;
var nombre_serie_grafica_boton_derecho = null;
var nombre_primera_serie_grafica_boton_derecho = null;

// Información de dibujado de informes
var modulo_informe_dibujado = null;
var tipo_informe_dibujado = null;
var informacion_extra_informe_dibujado = null;
var numero_elemento_plantilla_informe_dibujado = null;

// Permisos utilizados en menús contextuales
var exportacion_valores_sensores = false;
var adicion_comentarios_sensores = false;
var adicion_comentarios_actuadores = false;

// Variables auxiliares para los menús contextuales
var grafica_con_comentarios = false;

// Flags para evitar peticiones PHP redundantes en TLNT_configuracion_XXX
var recuperando_lista_ids_origenes_evento_personal = false;
var recuperando_lista_granularidades_evento_personal = false;
var recuperando_lista_ids_origenes_evento_sensores = false;
var recuperando_lista_granularidades_evento_sensores = false;

// Flags para evitar peticiones PHP innecesarios en TLNT_configuracion_XXX
var realizando_acciones_tipo_seleccion_sensores_desvios_ponderados_compra_energia = false;
