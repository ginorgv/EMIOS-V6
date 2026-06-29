import logging
import traceback

from comun.herramientas import util_sistema
from constantes.constantes import *



#-----------------------------------------------------------------------------
# MAIN: concatena ficheros de Web EMIOS
# - Nota: Este script se guarda en 'Web EMIOS' aunque hay que ejecutarlo desde el
#   directorio raíz de Servicios EMIOS
#-----------------------------------------------------------------------------
if __name__ == "__main__":
	logging.basicConfig(format = '%(asctime)s [%(levelname)s] {%(module)s.%(funcName)s()} %(message)s', level = logging.INFO)

	logging.info("Inicio");
	ruta_destino = "../web"
	prefijo_ruta_origen = "../web"

	lista_nombres_ficheros_origen = []
	lista_nombres_ficheros_destino = []

	# Ficheros de estilos de librerías (.css) del módulo común
	nombres_ficheros_web_comun_rsc_css = [
		'comun/rsc/lib/bootstrap/css/bootstrap.css',
		'comun/rsc/lib/bootstrap/css/bootstrap-colored.css',
		'comun/rsc/lib/bootstrap-datepicker/css/datepicker.css',
		'comun/rsc/lib/bootstrap-timepicker/css/bootstrap-timepicker.css',
		'comun/rsc/lib/jquery-alert/jquery.alerts.css']
	lista_nombres_ficheros_origen.append(nombres_ficheros_web_comun_rsc_css)
	lista_nombres_ficheros_destino.append("css/comun_rsc_v5.2.0.0.css")

	# Ficheros javascript de librerías (.js) del módulo común
	nombres_ficheros_web_comun_rsc_js = [
		'comun/rsc/lib/jquery/jquery-2.2.4.js',
		'comun/rsc/lib/jquery/jquery-migrate-1.2.1.js',
		'comun/rsc/lib/jquery-ui/jquery-ui.min.js',
		'comun/rsc/lib/bootstrap/js/bootstrap_v3.2.0.0.js',
		'comun/rsc/lib/bootstrap-timepicker/js/bootstrap-timepicker_v3.4.0.0.js',
		'comun/rsc/lib/bootstrap-datepicker/js/bootstrap-datepicker_v3.4.0.0.js',
		'comun/rsc/lib/bootstrap-datepicker/js/locales/bootstrap-datepicker.es.js',
		'comun/rsc/lib/jquery.blockUI/jquery.blockUI.js',
		'comun/rsc/lib/jquery-alert/jquery.alerts_v6.0.0.0.js',
		'comun/rsc/lib/md5/md5.js']
	lista_nombres_ficheros_origen.append(nombres_ficheros_web_comun_rsc_js)
	lista_nombres_ficheros_destino.append("js/comun_rsc_v5.2.0.0.js")

	# Ficheros javascript de fuentes (.js) del módulo común
	nombres_ficheros_web_comun_src_js = [
		'comun/src/lib/constantes/constantes_v5.4.0.0.js',
		'comun/src/lib/globales/globales_v5.2.0.0.js',
		'comun/src/lib/herramientas/util_cadenas_v5.4.0.0.js',
		'comun/src/lib/herramientas/util_criptografia_v4.0.0.0.js',
		'comun/src/lib/herramientas/util_html_v5.2.0.0.js',
		'comun/src/lib/herramientas/util_matematicas_v5.2.0.0.js',
		'comun/src/lib/herramientas/util_pantalla_v4.0.0.0.js',
		'comun/src/lib/herramientas/util_pie_pagina_v5.2.0.0.js',
		'comun/src/lib/herramientas/util_sistema_v5.2.0.0.js',
		'comun/src/lib/herramientas/util_tabla_datos_v5.4.0.0.js',
		'comun/src/lib/herramientas/util_tiempos_v5.2.0.0.js',
		'comun/TLNT/TLNT_v5.2.0.0_R2.js',
		'comun/src/login/login_v5.0.0.0.js']
	lista_nombres_ficheros_origen.append(nombres_ficheros_web_comun_src_js)
	lista_nombres_ficheros_destino.append("js/comun_src_v5.4.0.0_R2.js")

	# Ficheros javascript de librerías (.js) de Web EMIOS
	nombres_ficheros_web_rsc_js = [
		'rsc/lib/autosize/dist/autosize.js',
		'rsc/lib/canvg/StackBlur.js',
		'rsc/lib/canvg/rgbcolor.js',
		'rsc/lib/canvg/canvg.js',
		'rsc/lib/chosen/chosen.jquery_v3.2.0.0.js',
		'rsc/lib/d3js/d3.js',
		'rsc/lib/d3js/plugins/heatmap/heatmap_v5.4.0.0_R2.js',
		'rsc/lib/d3js/plugins/topologiaarbol/topologiaarbol_v5.2.0.0.js',
		'rsc/lib/d3js/plugins/windhistory/windhistory_v5.2.0.0.js',
		'rsc/lib/dom-to-image/dom-to-image.js',
		'rsc/lib/html2canvas/html2canvas.js',
		'rsc/lib/jqplot/jquery.jqplot_v4.0.0.0.js',
		'rsc/lib/jqplot/plugins/jqplot.highlighter_v5.2.0.0.js',
		'rsc/lib/jqplot/plugins/jqplot.cursor_v5.0.0.0.js',
		'rsc/lib/jqplot/plugins/jqplot.dateAxisRenderer_v3.0.0.0.js',
		'rsc/lib/jqplot/plugins/jqplot.canvasTextRenderer.min.js',
		'rsc/lib/jqplot/plugins/jqplot.canvasAxisTickRenderer_v5.0.0.0.js',
		'rsc/lib/jqplot/plugins/jqplot.pointLabels.min.js',
		'rsc/lib/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js',
		'rsc/lib/jqplot/plugins/jqplot.enhancedLegendRenderer.min.js',
		'rsc/lib/jqplot/plugins/jqplot.canvasOverlay_v5.0.0.0.js',
		'rsc/lib/jqplot/plugins/jqplot.categoryAxisRenderer.min.js',
		'rsc/lib/jqplot/plugins/jqplot.barRenderer.min.js',
		'rsc/lib/jqplot/plugins/jqplot.meterGaugeRenderer.min.js',
		'rsc/lib/jqplot/plugins/jqplot.pieRenderer_v3.2.0.0.js',
		'rsc/lib/justgage/raphael-2.1.4_v5.0.0.0.js',
		'rsc/lib/justgage/justgage_v5.0.0.0.js',
		'rsc/lib/jquery.multiselect2side/js/jquery.multiselect2side_v4.0.0.0.js',
		'rsc/lib/leaflet/dist/leaflet-src_v5.2.0.0.js',
		'rsc/lib/leaflet/plugins/leaflet-providers.js',
		'rsc/lib/leaflet/plugins/markercluster/leaflet.markercluster.js',
		'rsc/lib/leaflet/plugins/heatmap/heatmap.js',
		'rsc/lib/leaflet/plugins/heatmap/leaflet-heatmap.js',
		'rsc/lib/SVG.toDataURL/base64.js',
		'rsc/lib/SVG.toDataURL/svg_todataurl.js']
	lista_nombres_ficheros_origen.append(nombres_ficheros_web_rsc_js)
	lista_nombres_ficheros_destino.append("js/web_rsc_v5.4.0.0_R2.js")

	# Ficheros javascript de estilos (.php) de Web EMIOS
	nombres_ficheros_web_rsc_php = [
		'rsc/lib/d3js/plugins/heatmap/heatmap.php',
		'rsc/lib/d3js/plugins/topologiaarbol/topologiaarbol.php',
		'rsc/lib/d3js/plugins/windhistory/windhistory.php']
	lista_nombres_ficheros_origen.append(nombres_ficheros_web_rsc_php)
	lista_nombres_ficheros_destino.append("css/web_rsc.php")

	# Ficheros javascript de estilos (.css) de Web EMIOS
	nombres_ficheros_web_rsc_css = [
		'rsc/lib/chosen/chosen.css',
		'rsc/lib/jqplot/jquery.jqplot.css',
		'rsc/lib/jquery.multiselect2side/css/jquery.multiselect2side.css',
		'rsc/lib/leaflet/dist/leaflet.css',
		'rsc/lib/leaflet/plugins/markercluster/MarkerCluster.css',
		'rsc/lib/leaflet/plugins/markercluster/MarkerCluster.Default.css']
	lista_nombres_ficheros_origen.append(nombres_ficheros_web_rsc_css)
	lista_nombres_ficheros_destino.append("css/web_rsc_v5.2.0.0.css")

	# Ficheros javascript de fuentes (.js) de Web EMIOS
	nombres_ficheros_web_src_js = [
		'src/lib/constantes/constantes_v6.0.0.0.js',
		'src/lib/globales/globales_v5.4.0.0.js',
		'src/lib/herramientas/util_graficas_v5.4.0.0_R2.js',
		'src/lib/herramientas/util_graficos_v5.4.0.0.js',
		'src/lib/herramientas/util_librerias_v3.2.0.0.js',
		'src/lib/herramientas/util_menus_v5.4.5.0_R2.js',
		'src/lib/herramientas/util_pie_pagina_v4.0.0.0.js',
		'src/lib/herramientas/util_sesion_v3.4.0.0.js',
		'src/lib/herramientas/util_tablas_datos_v5.2.0.0.js',
		'src/lib/modulos/util_informes_v5.4.0.0.js',
		'src/lib/modulos/util_mediciones_v5.2.0.0.js',
		'src/lib/modulos/util_modulos_v5.4.0.0.js',
		'src/lib/modulos/AccionesUsuario/eventos_acciones_v4.0.0.0.js',
		'src/lib/modulos/ayuda/eventos_ayuda_v6.0.0.0.js',
		'src/lib/modulos/ayuda/util_ayuda_v6.0.0.0.js',
		'src/lib/modulos/Comentarios/eventos_comentarios_v5.4.0.0.js',
		'src/lib/modulos/Comentarios/util_comentarios_v4.0.0.0.js',
		'src/lib/modulos/imagenes/util_imagenes_v5.2.0.0.js',
		'src/lib/modulos/InformesFichero/eventos_informes_automaticos_v5.2.1.0.js',
		'src/lib/modulos/InformesFichero/util_informes_fichero_v5.0.0.0.js',
		'src/lib/modulos/localizaciones/eventos_localizaciones_v5.2.0.0.js',
		'src/lib/modulos/localizaciones/util_localizaciones_v3.2.0.0.js',
		'src/lib/modulos/mapas/eventos_mapa_v5.2.0.0.js',
		'src/lib/modulos/mapas/util_mapa_v5.4.0.0.js',
		'src/lib/modulos/Nodos/administracion/eventos_administracion_nodos_v5.4.1.0.js',
		'src/lib/modulos/Nodos/administracion/eventos_administracion_hijos_sensores_v5.4.0.0.js',
		'src/lib/modulos/Nodos/administracion/eventos_herramientas_nodos_v5.2.0.0.js',
		'src/lib/modulos/Nodos/administracion/util_administracion_actuadores_validaciones_v5.2.1.0.js',
		'src/lib/modulos/Nodos/administracion/util_administracion_nodos_validaciones_v4.0.0.0.js',
		'src/lib/modulos/Nodos/administracion/util_administracion_sensores_validaciones_v5.4.0.0_R2.js',
		'src/lib/modulos/Nodos/util_nodos_v5.4.0.0_R2.js',
		'src/lib/modulos/Periodos/eventos_periodos_v5.0.0.0.js',
		'src/lib/modulos/Procesado/eventos_procesado_v5.2.0.0_R2.js',
		'src/lib/modulos/Procesado/util_dibujado_informes_procesado_v5.2.0.0.js',
        'src/lib/modulos/Procesado/util_procesado_v5.2.0.0.js',
		'src/lib/modulos/RangosDias/eventos_rangos_dias_v4.0.0.0.js',
		'src/lib/modulos/widgets/eventos_widgets_v5.4.0.0.js',
		'src/lib/modulos/widgets/util_dibujado_widgets_v5.0.0.0.js',
		'src/lib/modulos/widgets/util_graficas_widgets_v5.4.0.0_R2.js',
		'src/lib/modulos/widgets/util_graficos_widgets_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloAdministracion/eventos_modulo_administracion_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloAdministracion/util_modulo_administracion_v4.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloAdministracion/Clientes/eventos_clientes_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloAdministracion/Licencias/eventos_licencias_v5.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloAdministracion/Preferencias/eventos_preferencias_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/eventos_usuarios_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloMonitorizacion/eventos_modulo_monitorizacion_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloMonitorizacion/util_modulo_monitorizacion_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloPersonal/util_modulo_personal_v4.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/eventos_plantillas_informes_pdf_v4.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/eventos_plantillas_informes_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/eventos_elementos_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/util_dibujado_elementos_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/InformesFichero/eventos_plantillas_informes_informes_automaticos_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/InformesFichero/eventos_plantillas_informes_informes_fichero_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/eventos_parametros_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloRed/eventos_modulo_red_v5.2.0.0_R2.js',
		'src/modulos/ModulosWeb/ModuloRed/util_modulo_red_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloLocalizaciones/eventos_modulo_localizaciones_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloLocalizaciones/util_modulo_localizaciones_v5.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/eventos_equipos_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/eventos_instalaciones_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/eventos_localizaciones_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/eventos_hijas_localizaciones_v5.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/eventos_ratios_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloLocalizaciones/widgets/util_dibujado_widgets_localizaciones_v5.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/eventos_modulo_sensores_v5.4.0.0_R2.js',
		'src/modulos/ModulosWeb/ModuloSensores/util_modulo_sensores_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/util_importacion_valores_sensor_validaciones_v5.4.0.0_R2.js',
		'src/modulos/ModulosWeb/ModuloSensores/Analisis/eventos_analisis_pdf_v4.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Analisis/eventos_analisis_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Analisis/util_dibujado_informes_analisis_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Analisis/InformesFichero/eventos_analisis_informes_automaticos_v4.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Analisis/InformesFichero/eventos_analisis_informes_fichero_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Comparacion/eventos_comparacion_pdf_v4.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Comparacion/eventos_comparacion_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Comparacion/util_dibujado_informes_comparacion_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Comparacion/InformesFichero/eventos_comparacion_informes_automaticos_v5.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Comparacion/InformesFichero/eventos_comparacion_informes_fichero_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Estadistica/eventos_estadistica_pdf_v4.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Estadistica/eventos_estadistica_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Estadistica/util_dibujado_informes_estadistica_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Estadistica/InformesFichero/eventos_estadistica_informes_automaticos_v4.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Estadistica/InformesFichero/eventos_estadistica_informes_fichero_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Eventos/eventos_eventos_pdf_v3.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Eventos/eventos_eventos_v5.2.0.0_R2.js',
		'src/modulos/ModulosWeb/ModuloSensores/Eventos/util_administracion_eventos_validaciones_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Eventos/util_dibujado_informes_eventos_v5.2.0.0_R3.js',
		'src/modulos/ModulosWeb/ModuloSensores/Eventos/InformesFichero/eventos_eventos_informes_automaticos_v3.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Eventos/InformesFichero/eventos_eventos_informes_fichero_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Informacion/eventos_informacion_pdf_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Informacion/eventos_informacion_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Informacion/util_dibujado_informes_informacion_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Informacion/InformesFichero/eventos_informacion_informes_automaticos_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/Informacion/InformesFichero/eventos_informacion_informes_fichero_v5.4.1.0.js',
		'src/modulos/ModulosWeb/ModuloSensores/widgets/util_dibujado_widgets_sensores_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloActuadores/eventos_modulo_actuadores_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloActuadores/util_acciones_v5.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloActuadores/util_administracion_acciones_validaciones_v3.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloActuadores/util_modulo_actuadores_v4.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloActuadores/Informacion/eventos_informacion_pdf_v4.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloActuadores/Informacion/eventos_informacion_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloActuadores/Informacion/util_dibujado_informes_informacion_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloActuadores/Informacion/InformesFichero/eventos_informacion_informes_automaticos_v4.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloActuadores/Informacion/InformesFichero/eventos_informacion_informes_fichero_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloActuadores/Programaciones/eventos_programaciones_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloActuadores/Reglas/eventos_reglas_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/eventos_acciones_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/eventos_sucesos_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloActuadores/widgets/util_dibujado_widgets_actuadores_v5.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/eventos_modulo_smartmeter_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/util_modulo_smartmeter_v5.2.0.0.js',
        'src/modulos/ModulosWeb/ModuloSmartmeter/Autoconsumo/eventos_autoconsumo_pdf_v5.2.0.0.js',
        'src/modulos/ModulosWeb/ModuloSmartmeter/Autoconsumo/eventos_autoconsumo_v5.2.0.0.js',
        'src/modulos/ModulosWeb/ModuloSmartmeter/Autoconsumo/util_dibujado_informes_autoconsumo_v5.2.0.0.js',
        'src/modulos/ModulosWeb/ModuloSmartmeter/Autoconsumo/InformesFichero/eventos_autoconsumo_informes_fichero_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/eventos_caudales_pdf_v4.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/eventos_caudales_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/InformesFichero/eventos_caudales_informes_fichero_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/Espanya/eventos_caudales_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/Espanya/util_dibujado_informes_caudales_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/Espanya/InformesFichero/eventos_caudales_informes_fichero_Espanya_v3.4.0.0.js',
        'src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/eventos_compra_energia_pdf_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/eventos_compra_energia_v5.4.0.0_R2.js',
        'src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/InformesFichero/eventos_compra_energia_informes_fichero_v5.4.0.0.js',
        'src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/InformesFichero/eventos_compra_energia_informes_automaticos_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/Espanya/eventos_compra_energia_Espanya_v5.4.0.0.js',
        'src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/Espanya/util_dibujado_informes_compra_energia_Espanya_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/Espanya/InformesFichero/eventos_compra_energia_informes_fichero_Espanya_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/eventos_consumos_costes_pdf_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/eventos_consumos_costes_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/util_dibujado_informes_consumos_costes_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/InformesFichero/eventos_consumos_costes_informes_automaticos_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/InformesFichero/eventos_consumos_costes_informes_fichero_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/eventos_consumos_costes_electricidad_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/util_dibujado_informes_consumos_costes_electricidad_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/InformesFichero/eventos_consumos_costes_informes_fichero_electricidad_v4.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/Espanya/eventos_consumos_costes_electricidad_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/Espanya/util_dibujado_informes_consumos_costes_electricidad_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/Espanya/InformesFichero/eventos_consumos_costes_informes_fichero_electricidad_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/gas/util_dibujado_informes_consumos_costes_gas_v3.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/gas/Espanya/eventos_consumos_costes_gas_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/gas/Espanya/util_dibujado_informes_consumos_costes_gas_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/gas/Espanya/InformesFichero/eventos_consumos_costes_informes_fichero_gas_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/EnergiaReactiva/eventos_energia_reactiva_pdf_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/EnergiaReactiva/eventos_energia_reactiva_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/EnergiaReactiva/InformesFichero/eventos_energia_reactiva_informes_fichero_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/EnergiaReactiva/Espanya/eventos_energia_reactiva_Espanya_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/EnergiaReactiva/Espanya/util_dibujado_informes_energia_reactiva_Espanya_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/EnergiaReactiva/Espanya/InformesFichero/eventos_energia_reactiva_informes_fichero_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/eventos_facturas_pdf_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/eventos_facturas_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/util_dibujado_informes_facturas_v5.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/InformesFichero/eventos_facturas_informes_automaticos_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/InformesFichero/eventos_facturas_informes_fichero_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/agua/Espanya/eventos_facturas_agua_Espanya_v5.4.0.0_R2.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/agua/Espanya/util_dibujado_informes_facturas_agua_Espanya_v5.4.0.0_R2.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/agua/Espanya/InformesFichero/eventos_facturas_informes_fichero_agua_Espanya_v5.4.0.0_R2.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Espanya/eventos_facturas_electricidad_Espanya_v5.4.0.0_R2.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Espanya/util_dibujado_informes_facturas_electricidad_Espanya_v5.4.0.0_R2.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Espanya/InformesFichero/eventos_facturas_informes_fichero_electricidad_Espanya_v5.4.0.0_R2.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/gas/Espanya/eventos_facturas_gas_Espanya_v5.4.0.0_R2.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/gas/Espanya/util_dibujado_informes_facturas_gas_Espanya_v5.4.0.0_R2.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/gas/Espanya/InformesFichero/eventos_facturas_informes_fichero_gas_Espanya_v5.4.0.0_R2.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/eventos_informes_personalizados_pdf_v5.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/eventos_informes_personalizados_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/agua/Espanya/eventos_informes_personalizados_agua_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/agua/Espanya/util_dibujado_informes_personalizados_agua_Espanya_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/agua/Espanya/InformesFichero/eventos_informes_personalizados_informes_fichero_agua_Espanya_v5.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/electricidad/Espanya/eventos_informes_personalizados_electricidad_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/electricidad/Espanya/util_dibujado_informes_personalizados_electricidad_Espanya_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/electricidad/Espanya/InformesFichero/eventos_informes_personalizados_informes_fichero_electricidad_Espanya_v5.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/gas/Espanya/eventos_informes_personalizados_gas_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/gas/Espanya/util_dibujado_informes_personalizados_gas_Espanya_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/gas/Espanya/InformesFichero/eventos_informes_personalizados_informes_fichero_gas_Espanya_v5.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/InformesFichero/eventos_informes_personalizados_informes_automaticos_v5.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/InformesFichero/eventos_informes_personalizados_informes_fichero_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/eventos_potencias_pdf_v4.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/eventos_potencias_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/InformesFichero/eventos_potencias_informes_fichero_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/Espanya/eventos_potencias_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/Espanya/util_dibujado_informes_potencias_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/Espanya/InformesFichero/eventos_potencias_informes_fichero_Espanya_v3.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/eventos_tarifas_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_dibujado_tarifas_v5.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/eventos_tarifas_agua_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/util_dibujado_tarifas_agua_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/eventos_tarifas_electricidad_Espanya_v6.0.1.0_R2.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_dibujado_tarifas_electricidad_Espanya_v5.2.0.0.js',
        'src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya_v5.6.0.0.js',
        'src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/eventos_tarifas_electricidad_Portugal_v5.4.0.6.js',
        'src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Portugal/eventos_facturas_electricidad_Portugal_v5.4.0.0_R2.js',
        'src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Portugal/util_dibujado_informes_facturas_electricidad_Portugal_v5.4.0.0_R2.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/eventos_tarifas_gas_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/util_dibujado_tarifas_gas_Espanya_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloSmartmeter/widgets/util_dibujado_widgets_smartmeter_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloProyectos/util_modulo_proyectos_v4.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloProyectos/Informacion/eventos_informacion_pdf_v3.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloProyectos/Informacion/eventos_informacion_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloProyectos/Informacion/util_dibujado_informes_informacion_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloProyectos/Informacion/InformesFichero/eventos_informacion_informes_automaticos_v4.0.0.0.js',
		'src/modulos/ModulosWeb/ModuloProyectos/Informacion/InformesFichero/eventos_informacion_informes_fichero_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloProyectos/LineasBase/eventos_lineas_base_pdf_v3.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloProyectos/LineasBase/eventos_lineas_base_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_dibujado_informes_lineas_base_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloProyectos/LineasBase/InformesFichero/eventos_lineas_base_informes_automaticos_v5.2.0.0.js',
		'src/modulos/ModulosWeb/ModuloProyectos/LineasBase/InformesFichero/eventos_lineas_base_informes_fichero_v5.4.0.0.js',
		'src/modulos/ModulosWeb/ModuloProyectos/Proyectos/eventos_proyectos_v5.2.0.0_R2.js',
		'src/modulos/ModulosWeb/ModuloProyectos/widgets/util_dibujado_widgets_proyectos_v5.0.0.0.js',
		'TLNT/config/modulos/TLNT_configuracion_actuadores_v5.4.0.0.js',
		'TLNT/config/modulos/TLNT_configuracion_administracion_v5.0.0.0.js',
		'TLNT/config/modulos/TLNT_configuracion_localizaciones_v5.4.3.0.js',
		'TLNT/config/modulos/TLNT_configuracion_modulos_v5.4.0.0.js',
		'TLNT/config/modulos/TLNT_configuracion_monitorizacion_v5.2.0.0.js',
		'TLNT/config/modulos/TLNT_configuracion_personal_v5.4.3.0.js',
		'TLNT/config/modulos/TLNT_configuracion_proyectos_v5.2.0.0.js',
		'TLNT/config/modulos/TLNT_configuracion_red_v5.2.0.0.js',
		'TLNT/config/modulos/TLNT_configuracion_sensores_v5.4.0.0_R2.js',
		'TLNT/config/modulos/TLNT_configuracion_smartmeter_v6.0.1.0_R2.js',
		'TLNT/config/TLNT_configuracion_v5.2.0.0.js']
	lista_nombres_ficheros_origen.append(nombres_ficheros_web_src_js)
	lista_nombres_ficheros_destino.append("js/web_src_v6.0.0.0.js")

	# Se concatenan y copian en el destino los ficheros correspondientes
	logging.info("Inicio");
	for i in range(len(lista_nombres_ficheros_origen)):
		nombres_ficheros_origen = lista_nombres_ficheros_origen[i]
		nombre_fichero_destino = lista_nombres_ficheros_destino[i]

		# Se concatena y crea el fichero correspondiente
		ruta_fichero_destino = util_sistema.dame_ruta_fichero(ruta_destino, nombre_fichero_destino)
		with open(ruta_fichero_destino, 'w', encoding = "utf-8") as fichero_destino:
			for nombre_fichero_origen in nombres_ficheros_origen:
				ruta_fichero_origen = util_sistema.dame_ruta_fichero(prefijo_ruta_origen, nombre_fichero_origen)
				with open(ruta_fichero_origen, encoding = "utf-8") as fichero_js:
					fichero_destino.write("/* Inicio fichero: '" + str(nombre_fichero_origen) + "'*/\n\n")
					for linea in fichero_js:
						fichero_destino.write(linea)
					fichero_destino.write("\n/* Fin fichero: '" + str(nombre_fichero_origen) + "'*/\n\n")
					logging.info("Fichero añadido: '" + str(ruta_fichero_origen) + "'")
		logging.info("Fichero concatenado creado: '" + str(ruta_fichero_destino) + "'")
	logging.info("Fin");

