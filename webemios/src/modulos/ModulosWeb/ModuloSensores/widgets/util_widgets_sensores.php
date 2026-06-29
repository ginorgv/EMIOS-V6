<?php
	include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/ValoresMapaCalor.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_widgets.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Comparacion/util_informes_comparacion.php');


    // Constantes

    // Indices de parámetros de tipo de widgets
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_ID_RATIO", 0);
	define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_CLASE_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_GRANULARIDAD_SENSOR", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_CAMPO", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_ID_SENSOR", 4);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_UTILIZAR_COLORES_FONDO", 5);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_COLORES_FONDO", 6);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_VALOR_LIMITE_COLORES_FONDO_1", 7);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_VALOR_LIMITE_COLORES_FONDO_2", 8);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_SENSOR_ICONO", 9);

    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_CLASE_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_CAMPO", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_ID_SENSOR", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_PERIODO_TIEMPO", 4);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_INICIAR_COMIENZO_PERIODO_TIEMPO", 5);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_FECHA_INICIO_PERIODO_TIEMPO", 6);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_UTILIZAR_COLORES_FONDO", 7);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_COLORES_FONDO", 8);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_VALOR_LIMITE_COLORES_FONDO_1", 9);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_VALOR_LIMITE_COLORES_FONDO_2", 10);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_ICONO", 11);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_HORARIO_SEMANAL", 12);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_EXCLUSION_FECHAS", 13);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_DIGITAL_MEDIO_ACUMULADO_SENSOR_INCLUSION_FECHAS", 14);

    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_TIPO_GRAFICO", 0);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_ID_RATIO", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_CLASE_SENSOR", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_GRANULARIDAD_SENSOR", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_CAMPO", 4);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_ID_SENSOR", 5);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_VALOR_MINIMO_INDICADOR", 6);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_VALOR_MAXIMO_INDICADOR", 7);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_UTILIZAR_COLORES_FONDO", 8);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_COLORES_FONDO", 9);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_VALOR_LIMITE_COLORES_FONDO_1", 10);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_VALOR_LIMITE_COLORES_FONDO_2", 11);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_VALOR_DIGITAL", 12);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_SENSOR_ICONO", 13);

    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_TIPO_GRAFICO", 0);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_ID_RATIO", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_CLASE_SENSOR", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_CAMPO", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_ID_SENSOR", 4);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_PERIODO_TIEMPO", 5);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_INICIAR_COMIENZO_PERIODO_TIEMPO", 6);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_FECHA_INICIO_PERIODO_TIEMPO", 7);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_VALOR_MINIMO_INDICADOR", 8);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_VALOR_MAXIMO_INDICADOR", 9);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_UTILIZAR_COLORES_FONDO", 10);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_COLORES_FONDO", 11);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_VALOR_LIMITE_COLORES_FONDO_1", 12);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_VALOR_LIMITE_COLORES_FONDO_2", 13);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_VALOR_DIGITAL", 14);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_ICONO", 15);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_HORARIO_SEMANAL", 16);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_EXCLUSION_FECHAS", 17);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_ANALOGICO_MEDIO_ACUMULADO_SENSOR_INCLUSION_FECHAS", 18);

    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_CLASE_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_CAMPO", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_ID_SENSOR", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_PERIODO_TIEMPO", 4);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_INICIAR_COMIENZO_PERIODO_TIEMPO", 5);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_FECHA_INICIO_PERIODO_TIEMPO", 6);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_INTERVALO_VALORES", 7);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_HORARIO_SEMANAL", 8);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_EXCLUSION_FECHAS", 9);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_SENSOR_INCLUSION_FECHAS", 10);

    define("INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_CLASE_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_CAMPO", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_ID_SENSOR", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_COLORES_MAPA_CALOR", 4);
    define("INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_TIPO_MAPA_CALOR", 5);
    define("INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_PERIODO_TIEMPO", 6);
    define("INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_INICIAR_COMIENZO_PERIODO_TIEMPO", 7);
    define("INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_FECHA_INICIO_PERIODO_TIEMPO", 8);
    define("INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_HORARIO_SEMANAL", 9);
    define("INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_EXCLUSION_FECHAS", 10);
    define("INDICE_PARAMETRO_TIPO_WIDGET_MAPA_CALOR_SENSOR_INCLUSION_FECHAS", 11);

    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_CLASE_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_CAMPO", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_ID_SENSOR", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_PERIODO_TIEMPO", 4);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_INICIAR_COMIENZO_PERIODO_TIEMPO", 5);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_INTERVALO_VALORES", 6);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_HORARIO_SEMANAL", 7);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_PERIODOS_SENSOR_EXCLUSION_FECHAS", 8);

    define("INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_CLASE_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_CAMPO", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_ID_SENSOR", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_PERIODO_TIEMPO", 4);
    define("INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_INICIAR_COMIENZO_PERIODO_TIEMPO", 5);
    define("INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_INTERVALO_VALORES", 6);
    define("INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_UTILIZAR_COLORES_FONDO", 7);
    define("INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_COLORES_FONDO", 8);
    define("INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_TIPO_VALORES_LIMITE_COLORES_FONDO", 9);
    define("INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_VALOR_LIMITE_COLORES_FONDO_1", 10);
    define("INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_VALOR_LIMITE_COLORES_FONDO_2", 11);
    define("INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_ICONO", 12);
    define("INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_HORARIO_SEMANAL", 13);
    define("INDICE_PARAMETRO_TIPO_WIDGET_EVOLUCION_VALORES_COMPARACION_PERIODOS_SENSOR_EXCLUSION_FECHAS", 14);

    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_CLASE_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_CAMPO", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_IDS_SENSORES", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_PERIODO_TIEMPO", 4);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_INICIAR_COMIENZO_PERIODO_TIEMPO", 5);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_FECHA_INICIO_PERIODO_TIEMPO", 6);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_INTERVALO_VALORES", 7);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_HORARIO_SEMANAL", 8);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_EXCLUSION_FECHAS", 9);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_IGUALES_SENSORES_INCLUSION_FECHAS", 10);

    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_CLASES_SENSORES", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_CAMPOS", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_IDS_SENSORES", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_PERIODO_TIEMPO", 4);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_INICIAR_COMIENZO_PERIODO_TIEMPO", 5);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_FECHA_INICIO_PERIODO_TIEMPO", 6);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_INTERVALO_VALORES", 7);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_UNIFICAR_ESCALAS", 8);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_HORARIO_SEMANAL", 9);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_EXCLUSION_FECHAS", 10);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_COMPARACION_CAMPOS_DIFERENTES_SENSORES_INCLUSION_FECHAS", 11);

    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_CLASES_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_CAMPOS", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_IDS_SENSORES", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_PERIODO_TIEMPO", 4);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_INICIAR_COMIENZO_PERIODO_TIEMPO", 5);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_FECHA_INICIO_PERIODO_TIEMPO", 6);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_INTERVALO_VALORES", 7);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_AGREGACION", 8);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_HORARIO_SEMANAL", 9);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_EXCLUSION_FECHAS", 10);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_VALORES_GENERALES_SENSORES_INCLUSION_FECHAS", 11);

    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_ID_RATIO", 0);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_CLASES_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_CAMPOS", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_IDS_SENSORES", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_PERIODO_TIEMPO", 4);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_INICIAR_COMIENZO_PERIODO_TIEMPO", 5);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_FECHA_INICIO_PERIODO_TIEMPO", 6);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_INTERVALO_VALORES", 7);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_AGREGACION", 8);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_UTILIZAR_COLORES_FONDO", 9);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_COLORES_FONDO", 10);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_VALOR_LIMITE_COLORES_FONDO_1", 11);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_VALOR_LIMITE_COLORES_FONDO_2", 12);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_ICONO", 13);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_HORARIO_SEMANAL", 14);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_EXCLUSION_FECHAS", 15);
    define("INDICE_PARAMETRO_TIPO_WIDGET_VALOR_AGREGADO_VALORES_GENERALES_SENSORES_INCLUSION_FECHAS", 16);

    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_TIPO_GRAFICA", 0);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_ID_RATIO", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_CLASES_SENSOR", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_CAMPOS", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_IDS_SENSORES", 4);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_PERIODO_TIEMPO", 5);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_INICIAR_COMIENZO_PERIODO_TIEMPO", 6);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_FECHA_INICIO_PERIODO_TIEMPO", 7);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_INTERVALO_VALORES", 8);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_AGREGACION", 9);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_HORARIO_SEMANAL", 10);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_EXCLUSION_FECHAS", 11);
    define("INDICE_PARAMETRO_TIPO_WIDGET_GRAFICA_INCREMENTOS_TOTALES_SENSORES_INCLUSION_FECHAS", 12);


    // Devuelve los datos de un widget de tipo 'Valor digital de un sensor'
    function dame_datos_widget_valor_digital_sensor(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo de widget
        $id_ratio = $parametros_tipo["id_ratio"];
        $clase_sensor = $parametros_tipo["clase_sensor"];
        $granularidad_sensor = $parametros_tipo["granularidad_sensor"];
        $campo = $parametros_tipo["campo"];
        $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
        $id_sensor = $parametros_tipo["id_sensor"];
        $utilizar_colores_fondo = $parametros_tipo["utilizar_colores_fondo"];
        $colores_fondo = $parametros_tipo["colores_fondo"];
        $valor_limite_colores_fondo_1 = $parametros_tipo["valor_limite_colores_fondo_1"];
        $valor_limite_colores_fondo_2 = $parametros_tipo["valor_limite_colores_fondo_2"];
        $icono = $parametros_tipo["icono"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Granularidad del sensor
        switch ($granularidad_sensor)
        {
            case GRANULARIDAD_TIEMPO_REAL:
            {
                $campo_hora_ultimos_valores = "hora_ultimos_valores";
                $campo_ultimos_valores = "ultimos_valores";
                $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local"];
                break;
            }
            case GRANULARIDAD_CUARTOHORARIA:
            {
                $campo_hora_ultimos_valores = "hora_ultimos_valores_clase_cuartoshora";
                $campo_ultimos_valores = "ultimos_valores_clase_cuartoshora";
                $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local_sin_segundos"];
                break;
            }
            case GRANULARIDAD_HORARIA:
            {
                $campo_hora_ultimos_valores = "hora_ultimos_valores_clase_horas";
                $campo_ultimos_valores = "ultimos_valores_clase_horas";
                $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local_sin_segundos"];
                break;
            }
            default:
            {
                throw new Exception("Granularidad incorrecta: '".$granularidad_sensor."'");
            }
        }
        $fila_sensor = dame_fila_sensor($id_sensor);
        $cadena_fecha_hora_ultimos_valores_base_datos_utc = $fila_sensor[$campo_hora_ultimos_valores];
        $ultimos_valores = $fila_sensor[$campo_ultimos_valores];

        // Si los valores son de clase, pueden no estar todos: Se recupera el campo correspondiente (si existe)
        switch ($granularidad_sensor)
        {
            case GRANULARIDAD_CUARTOHORARIA:
            case GRANULARIDAD_HORARIA:
            {
                if ($ultimos_valores != NULL)
                {
                    $existe_valor_clase_sensor = existe_valor_clase_sensor_widget($ultimos_valores, $clase_sensor, $campo);
                    if ($existe_valor_clase_sensor == false)
                    {
                        $ultimos_valores = NULL;
                    }
                }
                else
                {
                    $ultimos_valores = NULL;
                }
                break;
            }
        }

        // Datos del widget
        if ($ultimos_valores == NULL)
        {
            $datos_widget_valor_digital_sensor = array("sin_valores" => true);
        }
        else
        {
            // Últimos valores del sensor (se calculan valores con parámetros extra si es necesario)
            $ultimos_valores = dame_ultimos_valores_calculados_widget($clase_sensor, $parametros_extra_campo, $ultimos_valores);

            // Timeout de envío y eventos de alarmas activados
            $parametros_clase = $fila_sensor["parametros_clase"];
            $incrementos_tiempo_real_horarios = $fila_sensor["incrementos_tiempo_real_horarios"];
            $timeout_envio = $fila_sensor["timeout_envio"];
            $eventos_alarma_activados = $fila_sensor["eventos_alarma_activados"];
            $eventos_alarma_activados_clase_cuartoshora = $fila_sensor["eventos_alarma_activados_clase_cuartoshora"];
            $eventos_alarma_activados_clase_horas = $fila_sensor["eventos_alarma_activados_clase_horas"];
            $timeout_envio_activado = NodoSensor::dame_timeout_envio_activado($timeout_envio);
            $hay_eventos_alarma_activados = NodoSensor::dame_hay_eventos_alarma_activados(
                $eventos_alarma_activados,
                $eventos_alarma_activados_clase_cuartoshora,
                $eventos_alarma_activados_clase_horas);

            // Hora de valores
            $zona_horaria = dame_zona_horaria_local();
            $cadena_fecha_hora_ultimos_valores_local_utc = convierte_formato_fecha($cadena_fecha_hora_ultimos_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $formato_fecha_hora_local);
            $cadena_fecha_hora_ultimos_valores_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_ultimos_valores_local_utc, $formato_fecha_hora_local, ZONA_HORARIA_UTC, $zona_horaria);
            $html_cadena_hora_valores = "(".$cadena_fecha_hora_ultimos_valores_local_local.")";

            // Cadena de valores
            $html_cadena_valores = dame_html_cadena_valores_sensor_widget_valor_digital_sensor(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_ultimos_valores_base_datos_utc,
                $ultimos_valores,
                $clase_sensor,
                $parametros_clase,
                $incrementos_tiempo_real_horarios,
                $granularidad_sensor,
                INTERVALO_VALORES_NINGUNO,
                $campo,
                $numero_columnas_fila_widget_clase_contenido_widget);
            if ($html_cadena_valores === NULL)
            {
                $datos_widget_valor_digital_sensor = array("sin_valores" => true);
            }
            else
            {
                // Índice de color de fondo
                $indice_color_fondo = dame_indice_color_fondo_widget_sensor(
                    $id_ratio,
                    $id_sensor,
                    $cadena_fecha_hora_ultimos_valores_base_datos_utc,
                    $ultimos_valores,
                    $clase_sensor,
                    $incrementos_tiempo_real_horarios,
                    $granularidad_sensor,
                    $campo,
                    $utilizar_colores_fondo,
                    $valor_limite_colores_fondo_1,
                    $valor_limite_colores_fondo_2);

                // Datos del widget
                $datos_widget_valor_digital_sensor = array(
                    "sin_valores" => false,
                    "html_cadena_valores" => $html_cadena_valores,
                    "html_cadena_hora_valores" => $html_cadena_hora_valores,
                    "timeout_envio_activado" => $timeout_envio_activado,
                    "hay_eventos_alarma_activados" => $hay_eventos_alarma_activados,
                    "colores_fondo" => $colores_fondo,
                    "indice_color_fondo" => $indice_color_fondo);
            }
        }

        // Botón de envío de valores manuales
        $mostrar_boton_envio_valores_manuales = false;
        $administracion_sensores = NodoSensor::dame_administracion_sensores();
        $envio_valores_manuales_sensores = ($_SESSION["parametros_modulo_sensores"]["envio_valores_manuales_sensores"] == VALOR_SI);
        if (($administracion_sensores == true) || ($envio_valores_manuales_sensores == true))
        {
            if ($fila_sensor["tipo"] == TIPO_SENSOR_EXTERNO)
            {
                $parametros_tipo_sensor = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor['parametros_tipo']);
                if ($parametros_tipo_sensor[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO] == CLASE_SENSOR_EXTERNO_NINGUNA)
                {
                    $mostrar_boton_envio_valores_manuales = true;
                }
            }
        }
        if ($mostrar_boton_envio_valores_manuales == true)
        {
            $fila_widget = dame_fila_widget($id_widget);
            $id_pestanya_widgets = $fila_widget["pestanya"];
            $html_boton_envio_valores_manuales = "
                <div class='contenedor-botones-widget'>
                    <button id='boton_mostrar_ventana_envio_valores_manuales__".$id_sensor."__".$clase_sensor."__".$id_pestanya_widgets."'
                        class='btn-mini btn btn-success boton-widget boton_sensores_mostrar_ventana_envio_valores_manuales_sensor_widget'>".
                        $idiomas->_("Enviar valores manuales")."
                    </button>
                </div>";
        }
        else
        {
            $html_boton_envio_valores_manuales = "";
        }
        $datos_widget_valor_digital_sensor["html_boton_envio_valores_manuales"] = $html_boton_envio_valores_manuales;

        // Icono
        $datos_widget_valor_digital_sensor["icono"] = $icono;

        // Se devuelven los datos del widget
        $datos_widget_valor_digital_sensor["res"] = "OK";
        return ($datos_widget_valor_digital_sensor);
    }


    // Devuelve los datos de un widget de tipo 'Valor digital medio / acumulado de un sensor'
    function dame_datos_widget_valor_digital_medio_acumulado_sensor(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        // Parámetros de tipo de widget
        $id_ratio = $parametros_tipo["id_ratio"];
        $clase_sensor = $parametros_tipo["clase_sensor"];
        $campo = $parametros_tipo["campo"];
        $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
        $id_sensor = $parametros_tipo["id_sensor"];
        $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
        $utilizar_colores_fondo = $parametros_tipo["utilizar_colores_fondo"];
        $colores_fondo = $parametros_tipo["colores_fondo"];
        $valor_limite_colores_fondo_1 = $parametros_tipo["valor_limite_colores_fondo_1"];
        $valor_limite_colores_fondo_2 = $parametros_tipo["valor_limite_colores_fondo_2"];
        $icono = $parametros_tipo["icono"];
        $horario_semanal = $parametros_tipo["horario_semanal"];
        $exclusion_fechas = $parametros_tipo["exclusion_fechas"];
        $inclusion_fechas = $parametros_tipo["inclusion_fechas"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Fechas de inicio y fin
        $fechas_horas_inicio_fin = dame_fechas_inicio_fin_periodo_tiempo_widget(
            $periodo_tiempo,
            $iniciar_comienzo_periodo_tiempo,
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local,
            $clase_sensor);
        $fecha_hora_inicio_local = $fechas_horas_inicio_fin["fecha_hora_inicio_local"];
        $fecha_hora_fin_local = $fechas_horas_inicio_fin["fecha_hora_fin_local"];

        // Conversión a UTC
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_fin_local_local = convierte_fecha_a_cadena($fecha_hora_fin_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Se recupera la información del valor medio / acumulado del campo del sensor
        $res_valor_medio_acumulado = dame_valor_medio_acumulado_campo_sensor_widget(
            $id_ratio,
            $clase_sensor,
            $id_sensor,
            $campo,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas,
            $parametros_extra_campo);
        if ($res_valor_medio_acumulado["sin_valores"] == true)
        {
            $datos_widget_valor_digital_medio_acumulado_sensor = array("sin_valores" => true);
        }
        else
        {
            // Datos de la respuesta
            $cadena_fecha_hora_inicio_valores_base_datos_utc = $res_valor_medio_acumulado["cadena_fecha_hora_inicio_valores_base_datos_utc"];
            $cadena_fecha_hora_fin_valores_base_datos_utc = $res_valor_medio_acumulado["cadena_fecha_hora_fin_valores_base_datos_utc"];
            $valor_medio_acumulado = $res_valor_medio_acumulado["valor_medio_acumulado"];
            $unidad_medida = $res_valor_medio_acumulado["unidad_medida"];
            $numero_decimales = $res_valor_medio_acumulado["numero_decimales"];

            // Horas de inicio y fin
            $cadena_fecha_hora_valor_inicial_local_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
            $cadena_fecha_hora_valor_inicial_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_valor_inicial_local_utc, $_SESSION["formato_fecha_hora_local_sin_segundos"], ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_valor_final_local_utc = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
            $cadena_fecha_hora_valor_final_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_valor_final_local_utc, $_SESSION["formato_fecha_hora_local_sin_segundos"], ZONA_HORARIA_UTC, $zona_horaria);

            // Cadena de hora de valores
            $html_cadena_hora_valores = "";
            $html_cadena_hora_valores .= "<span class='fecha-hora-widgets-valores'>(".$cadena_fecha_hora_valor_inicial_local_local."),</span>";
            $html_cadena_hora_valores .= "<span class='fecha-hora-widgets-valores'>(".$cadena_fecha_hora_valor_final_local_local.")</span>";

            // Cadena de valores
            $html_cadena_valores = dame_html_cadena_valor_medio_acumulado_sensor_widget_valor_digital_sensor(
                $valor_medio_acumulado,
                $unidad_medida,
                $numero_decimales,
                $numero_columnas_fila_widget_clase_contenido_widget);

            // Alarma y timeout de envío
            $fila_sensor = dame_fila_sensor($id_sensor);
            $timeout_envio = $fila_sensor["timeout_envio"];
            $eventos_alarma_activados = $fila_sensor["eventos_alarma_activados"];
            $eventos_alarma_activados_clase_cuartoshora = $fila_sensor["eventos_alarma_activados_clase_cuartoshora"];
            $eventos_alarma_activados_clase_horas = $fila_sensor["eventos_alarma_activados_clase_horas"];
            $timeout_envio_activado = NodoSensor::dame_timeout_envio_activado($timeout_envio);
            $hay_eventos_alarma_activados = NodoSensor::dame_hay_eventos_alarma_activados(
                $eventos_alarma_activados,
                $eventos_alarma_activados_clase_cuartoshora,
                $eventos_alarma_activados_clase_horas);

            // Color de fondo
            $indice_color_fondo = dame_indice_color_fondo_widget(
                $valor_medio_acumulado,
                $utilizar_colores_fondo,
                $valor_limite_colores_fondo_1,
                $valor_limite_colores_fondo_2);

            // Datos del widget
            $datos_widget_valor_digital_medio_acumulado_sensor = array(
                "sin_valores" => false,
                "html_cadena_valores" => $html_cadena_valores,
                "html_cadena_hora_valores" => $html_cadena_hora_valores,
                "timeout_envio_activado" => $timeout_envio_activado,
                "hay_eventos_alarma_activados" => $hay_eventos_alarma_activados,
                "colores_fondo" => $colores_fondo,
                "indice_color_fondo" => $indice_color_fondo);
        }

        // Sin botón de envío de valores manuales
        $datos_widget_valor_digital_medio_acumulado_sensor["html_boton_envio_valores_manuales"] = "";

        // Icono
        $datos_widget_valor_digital_medio_acumulado_sensor["icono"] = $icono;

        // Se devuelven los datos del widget
        $datos_widget_valor_digital_medio_acumulado_sensor["res"] = "OK";
        return ($datos_widget_valor_digital_medio_acumulado_sensor);
    }


    // Devuelve los datos de un widget de tipo 'Valor analógico de un sensor'
    function dame_datos_widget_valor_analogico_sensor(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        // Parámetros de tipo de widget
        $tipo_grafico = $parametros_tipo["tipo_grafico"];
        $id_ratio = $parametros_tipo["id_ratio"];
        $clase_sensor = $parametros_tipo["clase_sensor"];
        $granularidad_sensor = $parametros_tipo["granularidad_sensor"];
        $campo = $parametros_tipo["campo"];
        $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
        $id_sensor = $parametros_tipo["id_sensor"];
        $valor_minimo_indicador = $parametros_tipo["valor_minimo_indicador"];
        $valor_maximo_indicador = $parametros_tipo["valor_maximo_indicador"];
        $utilizar_colores_fondo = $parametros_tipo["utilizar_colores_fondo"];
        $colores_fondo = $parametros_tipo["colores_fondo"];
        $valor_limite_colores_fondo_1 = $parametros_tipo["valor_limite_colores_fondo_1"];
        $valor_limite_colores_fondo_2 = $parametros_tipo["valor_limite_colores_fondo_2"];
        $valor_digital = $parametros_tipo["valor_digital"];
        $icono = $parametros_tipo["icono"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Granularidad del sensor
        switch ($granularidad_sensor)
        {
            case GRANULARIDAD_TIEMPO_REAL:
            {
                $campo_hora_ultimos_valores = "hora_ultimos_valores";
                $campo_ultimos_valores = "ultimos_valores";
                $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local"];
                break;
            }
            case GRANULARIDAD_CUARTOHORARIA:
            {
                $campo_hora_ultimos_valores = "hora_ultimos_valores_clase_cuartoshora";
                $campo_ultimos_valores = "ultimos_valores_clase_cuartoshora";
                $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local_sin_segundos"];
                break;
            }
            case GRANULARIDAD_HORARIA:
            {
                $campo_hora_ultimos_valores = "hora_ultimos_valores_clase_horas";
                $campo_ultimos_valores = "ultimos_valores_clase_horas";
                $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local_sin_segundos"];
                break;
            }
            default:
            {
                throw new Exception("Granularidad incorrecta: '".$granularidad_sensor."'");
            }
        }
        $fila_sensor = dame_fila_sensor($id_sensor);
        $cadena_fecha_hora_ultimos_valores_base_datos_utc = $fila_sensor[$campo_hora_ultimos_valores];
        $ultimos_valores = $fila_sensor[$campo_ultimos_valores];

        // Si los valores son de clase, pueden no estar todos: Se recupera el campo correspondiente (si existe)
        switch ($granularidad_sensor)
        {
            case GRANULARIDAD_CUARTOHORARIA:
            case GRANULARIDAD_HORARIA:
            {
                if ($ultimos_valores != NULL)
                {
                    $existe_valor_clase_sensor = existe_valor_clase_sensor_widget($ultimos_valores, $clase_sensor, $campo);
                    if ($existe_valor_clase_sensor == false)
                    {
                        $ultimos_valores = NULL;
                    }
                }
                else
                {
                    $ultimos_valores = NULL;
                }
                break;
            }
        }

        // Datos del widget
        if ($ultimos_valores === NULL)
        {
            $datos_widget_valor_analogico_sensor = array("sin_valores" => true);
        }
        else
        {
            // Alarma y timeout de envío
            $parametros_clase = $fila_sensor["parametros_clase"];
            $incrementos_tiempo_real_horarios = $fila_sensor["incrementos_tiempo_real_horarios"];
            $timeout_envio = $fila_sensor["timeout_envio"];
            $eventos_alarma_activados = $fila_sensor["eventos_alarma_activados"];
            $eventos_alarma_activados_clase_cuartoshora = $fila_sensor["eventos_alarma_activados_clase_cuartoshora"];
            $eventos_alarma_activados_clase_horas = $fila_sensor["eventos_alarma_activados_clase_horas"];
            $timeout_envio_activado = NodoSensor::dame_timeout_envio_activado($timeout_envio);
            $hay_eventos_alarma_activados = NodoSensor::dame_hay_eventos_alarma_activados(
                $eventos_alarma_activados,
                $eventos_alarma_activados_clase_cuartoshora,
                $eventos_alarma_activados_clase_horas);

            // Se recuperan el valor y la unidad
            $ultimos_valores = dame_ultimos_valores_calculados_widget($clase_sensor, $parametros_extra_campo, $ultimos_valores);
            $valor_unidad_sensor = dame_valor_unidad_sensor_widget(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_ultimos_valores_base_datos_utc,
                $ultimos_valores,
                $clase_sensor,
                $parametros_clase,
                $incrementos_tiempo_real_horarios,
                $granularidad_sensor,
                INTERVALO_VALORES_NINGUNO,
                $campo);
            if ($valor_unidad_sensor === NULL)
            {
                $datos_widget_valor_analogico_sensor = array("sin_valores" => true);
            }
            else
            {
                // Hora de valor
                $zona_horaria = dame_zona_horaria_local();
                $cadena_fecha_hora_ultimos_valores_local_utc = convierte_formato_fecha($cadena_fecha_hora_ultimos_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $formato_fecha_hora_local);
                $cadena_fecha_hora_ultimos_valores_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_ultimos_valores_local_utc, $formato_fecha_hora_local, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_valor = "(".$cadena_fecha_hora_ultimos_valores_local_local.")";

                // Valor y unidad
                $valor_sensor = $valor_unidad_sensor["valor"];
                $cadena_valor_sensor = $valor_unidad_sensor["cadena_valor"];
                $unidad_medida_sensor = $valor_unidad_sensor["unidad"];
                switch ($valor_digital)
                {
                    case VALORES_DIGITALES_WIDGET_TIPO_VALOR_ANALOGICO_SENSOR_NINGUNO:
                    {
                        $cadena_valor_sensor = "";
                        break;
                    }
                    case VALORES_DIGITALES_WIDGET_TIPO_VALOR_ANALOGICO_SENSOR_SELECCIONADO:
                    {
                        $cadena_valor_sensor = $cadena_valor_sensor;
                        break;
                    }
                    default:
                    {
                        $cadena_valor_sensor = "";
                        break;
                    }
                }

                // Color de fondo
                $indice_color_fondo = dame_indice_color_fondo_widget_sensor(
                    $id_ratio,
                    $id_sensor,
                    $cadena_fecha_hora_ultimos_valores_base_datos_utc,
                    $ultimos_valores,
                    $clase_sensor,
                    $incrementos_tiempo_real_horarios,
                    $granularidad_sensor,
                    $campo,
                    $utilizar_colores_fondo,
                    $valor_limite_colores_fondo_1,
                    $valor_limite_colores_fondo_2);

                // Datos del widget
                $datos_widget_valor_analogico_sensor = array(
                    "sin_valores" => false,
                    "tipo_grafico" => $tipo_grafico,
                    "valor_sensor" => $valor_sensor,
                    "cadena_valor_sensor" => $cadena_valor_sensor,
                    "unidad_sensor" => $unidad_medida_sensor,
                    "cadena_hora_valor" => $cadena_hora_valor,
                    "valor_minimo_indicador" => $valor_minimo_indicador,
                    "valor_maximo_indicador" => $valor_maximo_indicador,
                    "colores_fondo" => $colores_fondo,
                    "valor_limite_colores_fondo_1" => $valor_limite_colores_fondo_1,
                    "valor_limite_colores_fondo_2" => $valor_limite_colores_fondo_2,
                    "valor_digital" => $valor_digital,
                    "timeout_envio_activado" => $timeout_envio_activado,
                    "hay_eventos_alarma_activados" => $hay_eventos_alarma_activados,
                    "indice_color_fondo" => $indice_color_fondo);
            }
        }

        // Icono
        $datos_widget_valor_analogico_sensor["icono"] = $icono;

        // Se devuelven los datos del widget
        $datos_widget_valor_analogico_sensor["res"] = "OK";
        return ($datos_widget_valor_analogico_sensor);
    }


    // Devuelve los datos de un widget de tipo 'Valor analógico medio / acumulado de un sensor'
    function dame_datos_widget_valor_analogico_medio_acumulado_sensor(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        // Parámetros de tipo de widget
        $tipo_grafico = $parametros_tipo["tipo_grafico"];
        $id_ratio = $parametros_tipo["id_ratio"];
        $clase_sensor = $parametros_tipo["clase_sensor"];
        $campo = $parametros_tipo["campo"];
        $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
        $id_sensor = $parametros_tipo["id_sensor"];
        $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
        $valor_minimo_indicador = $parametros_tipo["valor_minimo_indicador"];
        $valor_maximo_indicador = $parametros_tipo["valor_maximo_indicador"];
        $utilizar_colores_fondo = $parametros_tipo["utilizar_colores_fondo"];
        $colores_fondo = $parametros_tipo["colores_fondo"];
        $valor_limite_colores_fondo_1 = $parametros_tipo["valor_limite_colores_fondo_1"];
        $valor_limite_colores_fondo_2 = $parametros_tipo["valor_limite_colores_fondo_2"];
        $valor_digital = $parametros_tipo["valor_digital"];
        $icono = $parametros_tipo["icono"];
        $horario_semanal = $parametros_tipo["horario_semanal"];
        $exclusion_fechas = $parametros_tipo["exclusion_fechas"];
        $inclusion_fechas = $parametros_tipo["inclusion_fechas"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Fechas de inicio y fin
        $fechas_horas_inicio_fin = dame_fechas_inicio_fin_periodo_tiempo_widget(
            $periodo_tiempo,
            $iniciar_comienzo_periodo_tiempo,
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local,
            $clase_sensor);
        $fecha_hora_inicio_local = $fechas_horas_inicio_fin["fecha_hora_inicio_local"];
        $fecha_hora_fin_local = $fechas_horas_inicio_fin["fecha_hora_fin_local"];

        // Conversión a UTC
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_fin_local_local = convierte_fecha_a_cadena($fecha_hora_fin_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Se recupera la información del valor medio / acumulado del campo del sensor
        $res_valor_medio_acumulado = dame_valor_medio_acumulado_campo_sensor_widget(
            $id_ratio,
            $clase_sensor,
            $id_sensor,
            $campo,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas,
            $parametros_extra_campo);
        if ($res_valor_medio_acumulado["sin_valores"] == true)
        {
            $datos_widget_valor_analogico_medio_acumulado_sensor = array("sin_valores" => true);
        }
        else
        {
            // Datos de la respuesta
            $cadena_fecha_hora_inicio_valores_base_datos_utc = $res_valor_medio_acumulado["cadena_fecha_hora_inicio_valores_base_datos_utc"];
            $cadena_fecha_hora_fin_valores_base_datos_utc = $res_valor_medio_acumulado["cadena_fecha_hora_fin_valores_base_datos_utc"];
            $valor_medio_acumulado = $res_valor_medio_acumulado["valor_medio_acumulado"];
            $unidad_medida = $res_valor_medio_acumulado["unidad_medida"];
            $numero_decimales = $res_valor_medio_acumulado["numero_decimales"];

            // Fechas de inicio y fin
            $cadena_fecha_hora_valor_inicial_local_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
            $cadena_fecha_hora_valor_inicial_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_valor_inicial_local_utc, $_SESSION["formato_fecha_hora_local_sin_segundos"], ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_valor_final_local_utc = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
            $cadena_fecha_hora_valor_final_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_valor_final_local_utc, $_SESSION["formato_fecha_hora_local_sin_segundos"], ZONA_HORARIA_UTC, $zona_horaria);

            // Cadena de hora de valor
            $cadena_hora_valor = "";
            $cadena_hora_valor .= "<span class='fecha-hora-widgets-valores'>(".$cadena_fecha_hora_valor_inicial_local_local."),</span>";
            $cadena_hora_valor .= "<span class='fecha-hora-widgets-valores'>(".$cadena_fecha_hora_valor_final_local_local.")</span>";

            // Cadena de valor del sensor
            $cadena_valor_sensor = formatea_numero($valor_medio_acumulado, $numero_decimales);

            // Alarma y timeout de envío
            $fila_sensor = dame_fila_sensor($id_sensor);
            $timeout_envio = $fila_sensor["timeout_envio"];
            $eventos_alarma_activados = $fila_sensor["eventos_alarma_activados"];
            $eventos_alarma_activados_clase_cuartoshora = $fila_sensor["eventos_alarma_activados_clase_cuartoshora"];
            $eventos_alarma_activados_clase_horas = $fila_sensor["eventos_alarma_activados_clase_horas"];
            $timeout_envio_activado = NodoSensor::dame_timeout_envio_activado($timeout_envio);
            $hay_eventos_alarma_activados = NodoSensor::dame_hay_eventos_alarma_activados(
                $eventos_alarma_activados,
                $eventos_alarma_activados_clase_cuartoshora,
                $eventos_alarma_activados_clase_horas);

            // Color de fondo
            $indice_color_fondo = dame_indice_color_fondo_widget(
                $valor_medio_acumulado,
                $utilizar_colores_fondo,
                $valor_limite_colores_fondo_1,
                $valor_limite_colores_fondo_2);

            // Datos del widget
            $datos_widget_valor_analogico_medio_acumulado_sensor = array(
                "sin_valores" => false,
                "tipo_grafico" => $tipo_grafico,
                "valor_sensor" => $valor_medio_acumulado,
                "cadena_valor_sensor" => $cadena_valor_sensor,
                "unidad_sensor" => $unidad_medida,
                "cadena_hora_valor" => $cadena_hora_valor,
                "valor_minimo_indicador" => $valor_minimo_indicador,
                "valor_maximo_indicador" => $valor_maximo_indicador,
                "colores_fondo" => $colores_fondo,
                "valor_limite_colores_fondo_1" => $valor_limite_colores_fondo_1,
                "valor_limite_colores_fondo_2" => $valor_limite_colores_fondo_2,
                "valor_digital" => $valor_digital,
                "timeout_envio_activado" => $timeout_envio_activado,
                "hay_eventos_alarma_activados" => $hay_eventos_alarma_activados,
                "indice_color_fondo" => $indice_color_fondo);
        }

        // Icono
        $datos_widget_valor_analogico_medio_acumulado_sensor["icono"] = $icono;

        // Se devuelven los datos del widget
        $datos_widget_valor_analogico_medio_acumulado_sensor["res"] = "OK";
        return ($datos_widget_valor_analogico_medio_acumulado_sensor);
    }


    // Devuelve los datos de un widget de tipo 'Gráfica de valores de un sensor'
    function dame_datos_widget_grafica_valores_sensor(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros de tipo de widget
        $id_ratio = $parametros_tipo["id_ratio"];
        $clase_sensor = $parametros_tipo["clase_sensor"];
        $campo = $parametros_tipo["campo"];
        $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
        $id_sensor = $parametros_tipo["id_sensor"];
        $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
        $intervalo_valores = $parametros_tipo["intervalo_valores"];
        $horario_semanal = $parametros_tipo["horario_semanal"];
        $exclusion_fechas = $parametros_tipo["exclusion_fechas"];
        $inclusion_fechas = $parametros_tipo["inclusion_fechas"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Se obtiene el nombre del sensor
        $nombre_sensor = dame_nombre_sensor($id_sensor);

        // Fechas de inicio y fin (local)
        $fechas_horas_inicio_fin = dame_fechas_inicio_fin_periodo_tiempo_widget(
            $periodo_tiempo,
            $iniciar_comienzo_periodo_tiempo,
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local,
            $clase_sensor);
        $fecha_hora_inicio_local = $fechas_horas_inicio_fin["fecha_hora_inicio_local"];
        $fecha_hora_fin_local = $fechas_horas_inicio_fin["fecha_hora_fin_local"];

        // Zona horaria
        $zona_horaria = dame_zona_horaria_local();

        // Conversión a UTC
        $cadena_fecha_hora_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_fin_local_local = convierte_fecha_a_cadena($fecha_hora_fin_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Datos de la gráfica y valores y fechas mínimos y máximos
        $grafica_valores = new VectorDatos();
        $min_valor = INF;
        $max_valor = -INF;
        $min_fecha_local = NULL;
        $max_fecha_local = NULL;

        // Datos para la gráfica de valores
        $datos_grafica = new VectorDatos();

        // Mostrar líneas de valores
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            {
                $mostrar_lineas_valores = false;
                break;
            }
            default:
            {
                $mostrar_lineas_valores = true;
            }
        }

        // Se recupera la información del ratio (si aplica)
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);
        if ($aplicar_ratio == true)
        {
            $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                $intervalo_valores,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas);
        }

        // Se recuperan los valores del sensor
        $consulta_valores_sensor = dame_consulta_valores_sensor(
            $id_sensor,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            $intervalo_valores,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas,
            $parametros_extra_campo);
        $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
        if ($res_valores_sensor == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
        }

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Se recorren los valores del sensor
        $numero_valores = 0;
        $timestamp_fecha_hora_valor_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        while ($fila_valor_sensor = $res_valores_sensor->dame_siguiente_fila())
        {
            // Fecha y valor
            $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
            $valor = $fila_valor_sensor[$campo];
            if ($valor !== NULL)
            {
                $valor = (float) $valor;
                if ($aplicar_ratio == true)
                {
                    aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $valor);
                }
            }
            if ($valor === NULL)
            {
                continue;
            }

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_valor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_valor_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_valor_utc - $timestamp_fecha_hora_valor_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_grafica->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_valor_anterior_utc = $timestamp_fecha_hora_valor_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade el valor a los datos de la gráfica
            // Nota: No hace falta restar el desfase horario entre cliente y local ya que no se muestran las fechas en el eje Y
            // (sólo un texto con la fecha inicial y final y las fechas en los 'tooltips')
            $datos_grafica->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_utc, $valor);

            // Valores mínimo y máximo
            if ($valor > $max_valor)
            {
                $max_valor = $valor;
            }
            if ($valor < $min_valor)
            {
                $min_valor = $valor;
            }

            // Fechas mínima y máxima
            $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);
            if ($min_fecha_local === NULL)
            {
                $min_fecha_local = $fecha_hora_local;
            }
            $max_fecha_local = $fecha_hora_local;

            // Se incrementa el número de valores
            $numero_valores += 1;
        }

        // Si las fechas son iguales, se añade y elimina el intervalo de valores correspondiente
        if ($numero_valores > 0)
        {
            if ($min_fecha_local == $max_fecha_local)
            {
                $intervalo_fecha = dame_intervalo_fecha_intervalo_valores_fechas_iguales($intervalo_valores);
                $min_fecha_local->sub($intervalo_fecha);
                $max_fecha_local->add($intervalo_fecha);
            }
        }

        // Número de decimales de valores y tipo de líneas de valores
        $numero_decimales = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);
        $tipo_lineas_valores = dame_tipo_lineas_valores_intervalo_valores_campo_clase_sensor(
            $intervalo_valores,
            $clase_sensor,
            $id_sensor,
            $campo);

        // Valores mínimo y máximo
        if ($numero_valores > 0)
        {
            $min_valor = round($min_valor, 2);
            $max_valor = round($max_valor, 2);
        }

        // Datos para la gráfica de valores
        $grafica_valores->anyade_dato($datos_grafica->dame_datos());

        // Unidad de medida del sensor
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio_sensor, $unidad_medida);
        }

        // Horas de valores inicial y final
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            {
                $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local"];
                break;
            }
            default:
            {
                $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local_sin_segundos"];
                break;
            }
        }
        $cadena_fecha_hora_valor_inicial_local_local = "";
        if ($min_fecha_local !== NULL)
        {
            $cadena_fecha_hora_valor_inicial_local_local = convierte_fecha_a_cadena($min_fecha_local, $formato_fecha_hora_local);
        }
        $cadena_fecha_hora_valor_final_local_local = "";
        if ($max_fecha_local !== NULL)
        {
            $cadena_fecha_hora_valor_final_local_local = convierte_fecha_a_cadena($max_fecha_local, $formato_fecha_hora_local);
        }

        // Cadena de horas de valores
        $html_cadena_horas_valores = "";
        $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_fecha_hora_valor_inicial_local_local."),</span>";
        $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_fecha_hora_valor_final_local_local.")</span>";

        // Fechas mínima y máxima
        if (($min_fecha_local !== NULL) && ($max_fecha_local !== NULL))
        {
            $cadena_min_fecha_local = convierte_fecha_a_cadena($min_fecha_local, FORMATO_FECHA_HORA_JQPLOT);
            $cadena_max_fecha_local = convierte_fecha_a_cadena($max_fecha_local, FORMATO_FECHA_HORA_JQPLOT);
        }

        // Valores máximos y mínimos
        if ($min_valor == INF)
        {
            $min_valor = "ND";
        }
        if ($max_valor == -INF)
        {
            $max_valor = "ND";
        }

        // Resultado
        $datos_widget_grafica_valores = array(
            "nombre_sensor" => $nombre_sensor,
            "clase_sensor" => $clase_sensor,
            "campo" => $campo,
            "intervalo_valores" => $intervalo_valores,
            "mostrar_lineas_valores" => $mostrar_lineas_valores,
            "tipo_lineas_valores" => $tipo_lineas_valores,
            "datos_grafica" => $grafica_valores->dame_datos(),
            "numero_decimales" => $numero_decimales,
            "min_valor" => $min_valor,
            "max_valor" => $max_valor,
            "min_fecha" => $cadena_min_fecha_local,
            "max_fecha" => $cadena_max_fecha_local,
            "unidad_medida" => $unidad_medida,
            "html_cadena_horas_valores" => $html_cadena_horas_valores);

        // Se devuelven los datos del widget
        $datos_widget_grafica_valores["res"] = "OK";
        return ($datos_widget_grafica_valores);
    }


    // Devuelve los datos de un widget de tipo 'Mapa de calor de un sensor'
    function dame_datos_widget_mapa_calor_sensor(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros de tipo de widget
        $id_ratio = $parametros_tipo["id_ratio"];
        $clase_sensor = $parametros_tipo["clase_sensor"];
        $campo = $parametros_tipo["campo"];
        $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
        $id_sensor = $parametros_tipo["id_sensor"];
        $colores_mapa_calor = $parametros_tipo["colores_mapa_calor"];
        $tipo_mapa_calor = $parametros_tipo["tipo_mapa_calor"];
        $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
        $horario_semanal = $parametros_tipo["horario_semanal"];
        $exclusion_fechas = $parametros_tipo["exclusion_fechas"];
        $inclusion_fechas = $parametros_tipo["inclusion_fechas"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Fechas de inicio y fin (local)
        $fechas_horas_inicio_fin = dame_fechas_inicio_fin_periodo_tiempo_widget(
            $periodo_tiempo,
            $iniciar_comienzo_periodo_tiempo,
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local,
            $clase_sensor);
        $fecha_hora_inicio_local = $fechas_horas_inicio_fin["fecha_hora_inicio_local"];
        $fecha_hora_fin_local = $fechas_horas_inicio_fin["fecha_hora_fin_local"];

        // Zona horaria
        $zona_horaria = dame_zona_horaria_local();

        // Conversión a UTC
        $cadena_fecha_hora_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_fin_local_local = convierte_fecha_a_cadena($fecha_hora_fin_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Datos del mapa de calor y fechas mínimos y máximos
        $valores_mapa_calor = new ValoresMapaCalor($tipo_mapa_calor);
        $min_fecha_local = NULL;
        $max_fecha_local = NULL;

        // Intervalo de valores
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        if ($caracteristicas_clase_sensor["procesado_valores"] == true)
        {
            $intervalo_valores = INTERVALO_VALORES_HORA;
        }
        else
        {
            $intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
        }

        // Se recupera la información del ratio (si aplica)
        if ($intervalo_valores == INTERVALO_VALORES_HORA)
        {
            $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);
            if ($aplicar_ratio == true)
            {
                $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                    $id_ratio,
                    $id_sensor,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
                    INTERVALO_VALORES_HORA,
                    $horario_semanal,
                    $exclusion_fechas,
                    $inclusion_fechas);
            }
        }
        else
        {
            $aplicar_ratio = false;
        }

        // Se recuperan los valores del sensor
        $consulta_valores_sensor = dame_consulta_valores_sensor(
            $id_sensor,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            $intervalo_valores,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas,
            $parametros_extra_campo);
        $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
        if ($res_valores_sensor == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
        }

        // Se recorren los valores del sensor
        $numero_valores = 0;
        while ($fila_valor_sensor = $res_valores_sensor->dame_siguiente_fila())
        {
            // Fecha y valor
            $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
            $valor = $fila_valor_sensor[$campo];
            if ($valor !== NULL)
            {
                $valor = (float) $valor;
                if ($aplicar_ratio == true)
                {
                    aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $valor);
                }
            }
            if ($valor === NULL)
            {
                continue;
            }

            // Fechas mínima y máxima
            $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);
            if ($min_fecha_local === NULL)
            {
                $min_fecha_local = $fecha_hora_local;
            }
            $max_fecha_local = $fecha_hora_local;

            // Datos para el mapa de calor
            $valores_mapa_calor->anyade_valor_fecha_hora($fecha_hora_local, $valor);

            // Se incrementa el número de valores
            $numero_valores += 1;
        }

        // Descripción de campo y unidad de medida del sensor
        $descripcion_campo = dame_descripcion_campo_clase_sensor($clase_sensor, $campo);
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio_sensor, $unidad_medida);
        }

        // Horas de valores inicial y final
        $cadena_fecha_hora_valor_inicial_local_local = "";
        if ($min_fecha_local !== NULL)
        {
            $cadena_fecha_hora_valor_inicial_local_local = convierte_fecha_a_cadena($min_fecha_local, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
        }
        $cadena_fecha_hora_valor_final_local_local = "";
        if ($max_fecha_local !== NULL)
        {
            $cadena_fecha_hora_valor_final_local_local = convierte_fecha_a_cadena($max_fecha_local, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
        }

        // Cadena de horas de valores
        $html_cadena_horas_valores = "";
        $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_fecha_hora_valor_inicial_local_local."),</span>";
        $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_fecha_hora_valor_final_local_local.")</span>";

        // Resultado
        $datos_widget_mapa_calor = array(
            "tipo_mapa_calor" => $tipo_mapa_calor,
            "dias_mapa_calor" => $valores_mapa_calor->dame_dias(),
            "datos_mapa_calor" => $valores_mapa_calor->dame_datos(),
            "colores_mapa_calor" => $colores_mapa_calor,
            "descripcion_campo" => $descripcion_campo,
            "unidad_medida" => $unidad_medida,
            "html_cadena_horas_valores" => $html_cadena_horas_valores);

        // Se devuelven los datos del widget
        $datos_widget_mapa_calor["res"] = "OK";
        return ($datos_widget_mapa_calor);
    }


    // Devuelve los datos de un widget de tipo 'Gráfica de comparación de periodos de un sensor'
    function dame_datos_widget_grafica_comparacion_periodos_sensor(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        // Parámetros de tipo de widget
        $id_ratio = $parametros_tipo["id_ratio"];
        $clase_sensor = $parametros_tipo["clase_sensor"];
        $campo = $parametros_tipo["campo"];
        $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
        $id_sensor = $parametros_tipo["id_sensor"];
        $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        $intervalo_valores = $parametros_tipo["intervalo_valores"];
        $horario_semanal = $parametros_tipo["horario_semanal"];
        $exclusion_fechas = $parametros_tipo["exclusion_fechas"];

        // Se obtiene el nombre del sensor
        $nombre_sensor = dame_nombre_sensor($id_sensor);

        // Info de periodos (local)
        $info_periodos = dame_info_periodos_periodo_tiempo_widget($periodo_tiempo, $iniciar_comienzo_periodo_tiempo, $clase_sensor);
        $fecha_hora_inicio_anterior_local = $info_periodos["fecha_hora_inicio_periodo_anterior_local"];
        $fecha_hora_inicio_posterior_local = $info_periodos["fecha_hora_inicio_periodo_posterior_local"];
        $numero_dias_periodo = $info_periodos["numero_dias_periodo"];

        // Conversión a cadena
        $cadena_fecha_hora_inicio_anterior_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_anterior_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_inicio_posterior_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_posterior_local, $_SESSION["formato_fecha_hora_local"]);

        // Se recuperan los datos de comparación de periodos
        $parametros_comparacion = array(
            "id_ratio" => $id_ratio,
            "clase_sensor" => $clase_sensor,
            "id_sensor" => $id_sensor,
            "nombre_sensor" => $nombre_sensor,
            "campo" => $campo,
            "parametros_extra_campo" => $parametros_extra_campo,
            "fecha_hora_inicio_anterior" => $cadena_fecha_hora_inicio_anterior_local_local,
            "fecha_hora_inicio_posterior" => $cadena_fecha_hora_inicio_posterior_local_local,
            "numero_dias_periodo" => $numero_dias_periodo,
            "minutos_desfase_utc" => $minutos_desfase_utc,
            "intervalo_valores" => $intervalo_valores,
            "mostrar_grafica_diferencias" => false,
            "tipo_mapa_calor" => TIPO_MAPA_CALOR_NINGUNO,
            "horario_semanal" => json_encode($horario_semanal),
            "exclusion_fechas" => json_encode($exclusion_fechas));
        $res_comparacion = dame_comparacion_valores_sensor_periodos($parametros_comparacion);
        if ($res_comparacion["res"] == "OK")
        {
            // Comprobación de datos
            if ($res_comparacion["hay_datos"] == true)
            {
                // Cadenas de fechas
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
                    case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
                    {
                        $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local"];
                        break;
                    }
                    default:
                    {
                        $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local_sin_segundos"];
                        break;
                    }
                }
                $cadena_fecha_hora_inicio_posterior_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_posterior_local_local, $_SESSION["formato_fecha_hora_local"], $formato_fecha_hora_local);
                if (($res_comparacion["fecha_hora_inicio_valores_posterior"] !== NULL) && ($res_comparacion["fecha_hora_fin_valores_posterior"] !== NULL))
                {
                    $cadena_fecha_hora_valor_final_posterior_local_local = convierte_fecha_a_cadena($res_comparacion["fecha_hora_fin_valores_posterior"], $formato_fecha_hora_local);
                }

                // Mostrar líneas de valores
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
                    {
                        $mostrar_lineas_valores = false;
                        break;
                    }
                    default:
                    {
                        $mostrar_lineas_valores = true;
                        break;
                    }
                }

                // Cadena de horas de valores
                $html_cadena_horas_valores = "";
                $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_fecha_hora_inicio_posterior_local_local."),</span>";
                $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_fecha_hora_valor_final_posterior_local_local.")</span>";

                // Parámetros 'extra' para el dibujado del widget
                $res_comparacion["intervalo_valores"] = $intervalo_valores;
                $res_comparacion["mostrar_lineas_valores"] = $mostrar_lineas_valores;
                $res_comparacion["html_cadena_horas_valores"] = $html_cadena_horas_valores;
            }
        }

        // Datos del widget
        $datos_widget_grafica_comparacion_periodos = $res_comparacion;
        $datos_widget_grafica_comparacion_periodos["clase_sensor"] = $clase_sensor;
        $datos_widget_grafica_comparacion_periodos["campo"] = $campo;

        // Se devuelven los datos del widget
        return ($datos_widget_grafica_comparacion_periodos);
    }


    // Devuelve los datos de un widget de tipo 'Evolución de valores de comparación de periodos de un sensor'
    function dame_datos_widget_evolucion_valores_comparacion_periodos_sensor(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        // Parámetros de tipo de widget
        $id_ratio = $parametros_tipo["id_ratio"];
        $clase_sensor = $parametros_tipo["clase_sensor"];
        $campo = $parametros_tipo["campo"];
        $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
        $id_sensor = $parametros_tipo["id_sensor"];
        $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        $intervalo_valores = $parametros_tipo["intervalo_valores"];
        $utilizar_colores_fondo = $parametros_tipo["utilizar_colores_fondo"];
        $colores_fondo = $parametros_tipo["colores_fondo"];
        $tipo_valores_limite_colores_fondo = $parametros_tipo["tipo_valores_limite_colores_fondo"];
        $valor_limite_colores_fondo_1 = $parametros_tipo["valor_limite_colores_fondo_1"];
        $valor_limite_colores_fondo_2 = $parametros_tipo["valor_limite_colores_fondo_2"];
        $icono = $parametros_tipo["icono"];
        $horario_semanal = $parametros_tipo["horario_semanal"];
        $exclusion_fechas = $parametros_tipo["exclusion_fechas"];

        // Se obtiene el nombre del sensor
        $nombre_sensor = dame_nombre_sensor($id_sensor);

        // Info de periodos (local)
        $info_periodos = dame_info_periodos_periodo_tiempo_widget($periodo_tiempo, $iniciar_comienzo_periodo_tiempo, $clase_sensor);
        $fecha_hora_inicio_anterior_local = $info_periodos["fecha_hora_inicio_periodo_anterior_local"];
        $fecha_hora_inicio_posterior_local = $info_periodos["fecha_hora_inicio_periodo_posterior_local"];
        $numero_dias_periodo = $info_periodos["numero_dias_periodo"];

        // Conversión a cadena
        $cadena_fecha_hora_inicio_anterior_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_anterior_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_inicio_posterior_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_posterior_local, $_SESSION["formato_fecha_hora_local"]);

        // Se recuperan los datos de comparación de periodos
        $parametros_comparacion = array(
            "id_ratio" => $id_ratio,
            "clase_sensor" => $clase_sensor,
            "id_sensor" => $id_sensor,
            "nombre_sensor" => $nombre_sensor,
            "campo" => $campo,
            "parametros_extra_campo" => $parametros_extra_campo,
            "fecha_hora_inicio_anterior" => $cadena_fecha_hora_inicio_anterior_local_local,
            "fecha_hora_inicio_posterior" => $cadena_fecha_hora_inicio_posterior_local_local,
            "numero_dias_periodo" => $numero_dias_periodo,
            "minutos_desfase_utc" => $minutos_desfase_utc,
            "intervalo_valores" => $intervalo_valores,
            "mostrar_grafica_diferencias" => false,
            "tipo_mapa_calor" => TIPO_MAPA_CALOR_NINGUNO,
            "horario_semanal" => json_encode($horario_semanal),
            "exclusion_fechas" => json_encode($exclusion_fechas));
        $res_comparacion = dame_comparacion_valores_sensor_periodos($parametros_comparacion);
        if ($res_comparacion["res"] == "OK")
        {
            // Comprobación de datos
            if (($res_comparacion["hay_datos"] == true) && ($res_comparacion["hay_valores_solapados_periodos"] == true))
            {
                // Nota: Tamaño de la fuente dependiente de la configuración de la cuadrícula de widgets
                $clase_tamanyo_fuente_evolucion_valores_widget = "tamanyo-fuente-evolucion-valores-widget-evolucion-valores-comparacion-periodos-sensor-columnas-".$numero_columnas_fila_widget_clase_contenido_widget;
                $clase_tamanyo_fuente_texto_periodo_widget = "tamanyo-fuente-texto-periodo-widget-evolucion-valores-comparacion-periodos-sensor-columnas-".$numero_columnas_fila_widget_clase_contenido_widget;

                // Colores de fondo del widget
                if ($utilizar_colores_fondo == VALOR_SI)
                {
                    switch ($tipo_valores_limite_colores_fondo)
                    {
                        case TIPO_VALORES_LIMITE_COLORES_FONDO_WIDGET_ABSOLUTO:
                        {
                            $valor_diferencia = $res_comparacion["diferencia_valores_totales"];
                            break;
                        }
                        case TIPO_VALORES_LIMITE_COLORES_FONDO_WIDGET_PORCENTAJE:
                        {
                            $valor_diferencia = $res_comparacion["porcentaje_diferencia_valores_totales"];
                            break;
                        }
                    }
                    $indice_color_fondo = dame_indice_color_fondo_widget(
                        $valor_diferencia,
                        $utilizar_colores_fondo,
                        $valor_limite_colores_fondo_1,
                        $valor_limite_colores_fondo_2);
                }
                else
                {
                    $indice_color_fondo = ID_NINGUNO;
                }

                // Datos del widget
                $datos_widget_evolucion_valores_comparacion_periodos = array(
                    "texto_diferencia_valores_totales_sin_unidad" => $res_comparacion["texto_diferencia_valores_totales_sin_unidad"],
                    "unidad_medida" => $res_comparacion["unidad_medida"],
                    "texto_porcentaje_diferencia_valores_totales" => $res_comparacion["texto_porcentaje_diferencia_valores_totales"],
                    "texto_periodo" => $res_comparacion["texto_periodo"],
                    "clase_tamanyo_fuente_evolucion_valores_widget" => $clase_tamanyo_fuente_evolucion_valores_widget,
                    "clase_tamanyo_fuente_texto_periodo_widget" => $clase_tamanyo_fuente_texto_periodo_widget,
                    "colores_fondo" => $colores_fondo,
                    "indice_color_fondo" => $indice_color_fondo);

                // Cadenas de fechas
                $cadena_fecha_hora_inicio_posterior_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_posterior_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_hora_local_sin_segundos"]);
                if (($res_comparacion["fecha_hora_inicio_valores_posterior"] !== NULL) && ($res_comparacion["fecha_hora_fin_valores_posterior"] !== NULL))
                {
                    $cadena_hora_valor_final_posterior_local_local = convierte_fecha_a_cadena($res_comparacion["fecha_hora_fin_valores_posterior"], $_SESSION["formato_fecha_hora_local_sin_segundos"]);
                }

                // Parámetros 'extra' para el dibujado del widget
                $datos_widget_evolucion_valores_comparacion_periodos["cadena_hora_inicio_posterior"] = $cadena_fecha_hora_inicio_posterior_local_local;
                $datos_widget_evolucion_valores_comparacion_periodos["cadena_hora_valor_final_posterior"] = $cadena_hora_valor_final_posterior_local_local;
            }
            else
            {
                $datos_widget_evolucion_valores_comparacion_periodos = array("hay_datos" => false);
            }
        }
        else
        {
            $datos_widget_evolucion_valores_comparacion_periodos = array(
                "res" => "ERROR",
                "msg" => $res_comparacion["msg"]);
        }

        // Icono
        $datos_widget_evolucion_valores_comparacion_periodos["icono"] = $icono;

        // Se devuelven los datos del widget
        return ($datos_widget_evolucion_valores_comparacion_periodos);
    }


    // Devuelve los datos de un widget de tipo 'Gráfica de comparación de campos iguales de sensores'
    function dame_datos_widget_grafica_comparacion_campos_iguales_sensores(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        // Parámetros de tipo de widget
        $id_ratio = $parametros_tipo["id_ratio"];
        $clase_sensor = $parametros_tipo["clase_sensor"];
        $campo = $parametros_tipo["campo"];
        $parametros_extra_campo = $parametros_tipo["parametros_extra_campo"];
        $ids_sensores = $parametros_tipo["ids_sensores"];
        $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
        $intervalo_valores = $parametros_tipo["intervalo_valores"];
        $horario_semanal = $parametros_tipo["horario_semanal"];
        $exclusion_fechas = $parametros_tipo["exclusion_fechas"];
        $inclusion_fechas = $parametros_tipo["inclusion_fechas"];

        // Se obtienen los nombres de los sensores
        $nombres_sensores = dame_nombres_sensores($ids_sensores);

        // Fechas de inicio y fin (local)
        $fechas_horas_inicio_fin = dame_fechas_inicio_fin_periodo_tiempo_widget(
            $periodo_tiempo,
            $iniciar_comienzo_periodo_tiempo,
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local,
            $clase_sensor);
        $fecha_hora_inicio_local = $fechas_horas_inicio_fin["fecha_hora_inicio_local"];
        $fecha_hora_fin_local = $fechas_horas_inicio_fin["fecha_hora_fin_local"];

        // Conversión a cadena
        $cadena_fecha_hora_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_fin_local_local = convierte_fecha_a_cadena($fecha_hora_fin_local, $_SESSION["formato_fecha_hora_local"]);

        // Se recuperan los datos de comparación de campos iguales
        $parametros_comparacion = array(
            "id_ratio" => $id_ratio,
            "clase_sensor" => $clase_sensor,
            "ids_sensores" => $ids_sensores,
            "nombres_sensores" => $nombres_sensores,
            "campo" => $campo,
            "parametros_extra_campo" => $parametros_extra_campo,
            "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
            "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
            "minutos_desfase_utc" => $minutos_desfase_utc,
            "intervalo_valores" => $intervalo_valores,
            "tipo_mapa_calor" => TIPO_MAPA_CALOR_NINGUNO,
            "horario_semanal" => json_encode($horario_semanal),
            "exclusion_fechas" => json_encode($exclusion_fechas),
            "inclusion_fechas" => json_encode($inclusion_fechas));
        $res_comparacion = dame_comparacion_valores_campos_iguales_sensores($parametros_comparacion);
        if ($res_comparacion["res"] == "OK")
        {
            // Comprobación de datos
            if ($res_comparacion["hay_datos"] == true)
            {
                // Cadenas con las horas de los valores inicial y final
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
                    case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
                    {
                        $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local"];
                        break;
                    }
                    default:
                    {
                        $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local_sin_segundos"];
                        break;
                    }
                }
                if (($res_comparacion["min_fecha"] !== NULL) && ($res_comparacion["max_fecha"] !== NULL))
                {
                    $cadena_hora_valor_inicial_local_local = convierte_formato_fecha($res_comparacion["min_fecha"], FORMATO_FECHA_HORA_JQPLOT, $formato_fecha_hora_local);
                    $cadena_hora_valor_final_local_local = convierte_formato_fecha($res_comparacion["max_fecha"], FORMATO_FECHA_HORA_JQPLOT, $formato_fecha_hora_local);
                }

                // Mostrar líneas de valores
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
                    {
                        $mostrar_lineas_valores = false;
                        break;
                    }
                    default:
                    {
                        $mostrar_lineas_valores = true;
                        break;
                    }
                }

                // Cadena de horas de valores
                $html_cadena_horas_valores = "";
                $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_hora_valor_inicial_local_local."),</span>";
                $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_hora_valor_final_local_local.")</span>";

                // Parámetros 'extra' para el dibujado del widget
                $res_comparacion["intervalo_valores"] = $parametros_comparacion["intervalo_valores"];
                $res_comparacion["mostrar_lineas_valores"] = $mostrar_lineas_valores;
                $res_comparacion["html_cadena_horas_valores"] = $html_cadena_horas_valores;
            }
        }

        // Datos del widget
        $datos_widget_grafica_comparacion_campos_iguales = $res_comparacion;
        $datos_widget_grafica_comparacion_campos_iguales["clase_sensor"] = $clase_sensor;
        $datos_widget_grafica_comparacion_campos_iguales["campo"] = $campo;

        // Se devuelven los datos del widget
        return ($datos_widget_grafica_comparacion_campos_iguales);
    }


    // Devuelve los datos de un widget de tipo 'Gráfica de comparación de campos diferentes de sensores'
    function dame_datos_widget_grafica_comparacion_campos_diferentes_sensores(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        // Parámetros de tipo de widget
        $id_ratio = $parametros_tipo["id_ratio"];
        $clases_sensores = $parametros_tipo["clases_sensores"];
        $campos = $parametros_tipo["campos"];
        $parametros_extra_campos = $parametros_tipo["parametros_extra_campos"];
        $ids_sensores = $parametros_tipo["ids_sensores"];
        $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
        $intervalo_valores = $parametros_tipo["intervalo_valores"];
        $unificar_escalas = $parametros_tipo["unificar_escalas"];
        $horario_semanal = $parametros_tipo["horario_semanal"];
        $exclusion_fechas = $parametros_tipo["exclusion_fechas"];
        $inclusion_fechas = $parametros_tipo["inclusion_fechas"];

        // Mostrar líneas de valores
        switch ($intervalo_valores)
        {
            case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
            {
                $mostrar_lineas_valores = false;
                break;
            }
            default:
            {
                $mostrar_lineas_valores = true;
                break;
            }
        }

        // Se obtienen los nombres de los sensores
        $nombres_sensores = dame_nombres_sensores($ids_sensores);

        // Fechas de inicio y fin (local)
        $fechas_horas_inicio_fin = dame_fechas_inicio_fin_periodo_tiempo_widget(
            $periodo_tiempo,
            $iniciar_comienzo_periodo_tiempo,
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local,
            $clases_sensores);
        $fecha_hora_inicio_local = $fechas_horas_inicio_fin["fecha_hora_inicio_local"];
        $fecha_hora_fin_local = $fechas_horas_inicio_fin["fecha_hora_fin_local"];

        // Conversión a cadena
        $cadena_fecha_hora_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_fin_local_local = convierte_fecha_a_cadena($fecha_hora_fin_local, $_SESSION["formato_fecha_hora_local"]);

        // Se recuperan los datos de comparación de campos iguales
        $parametros_comparacion = array(
            "id_ratio" => $id_ratio,
            "clases_sensores" => $clases_sensores,
            "ids_sensores" => $ids_sensores,
            "nombres_sensores" => $nombres_sensores,
            "campos" => $campos,
            "parametros_extra_campos" => $parametros_extra_campos,
            "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
            "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
            "minutos_desfase_utc" => $minutos_desfase_utc,
            "intervalo_valores" => $intervalo_valores,
            "unificar_escalas" => $unificar_escalas,
            "horario_semanal" => json_encode($horario_semanal),
            "exclusion_fechas" => json_encode($exclusion_fechas),
            "inclusion_fechas" => json_encode($inclusion_fechas));
        $res_comparacion = dame_comparacion_valores_campos_diferentes_sensores($parametros_comparacion);
        if ($res_comparacion["res"] == "OK")
        {
            // Comprobación de datos
            if ($res_comparacion["hay_datos"] == true)
            {
                // Cadenas con las horas de los valores inicial y final
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
                    case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
                    {
                        $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local"];
                        break;
                    }
                    default:
                    {
                        $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local_sin_segundos"];
                        break;
                    }
                }
                if (($res_comparacion["min_fecha"] !== NULL) && ($res_comparacion["max_fecha"] !== NULL))
                {
                    $cadena_hora_valor_inicial_local_local = convierte_formato_fecha($res_comparacion["min_fecha"], FORMATO_FECHA_HORA_JQPLOT, $formato_fecha_hora_local);
                    $cadena_hora_valor_final_local_local = convierte_formato_fecha($res_comparacion["max_fecha"], FORMATO_FECHA_HORA_JQPLOT, $formato_fecha_hora_local);
                }

                // Cadena de horas de valores
                $html_cadena_horas_valores = "";
                $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_hora_valor_inicial_local_local."),</span>";
                $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_hora_valor_final_local_local.")</span>";

                // Parámetros 'extra' para el dibujado del widget
                $res_comparacion["intervalo_valores"] = $intervalo_valores;
                $res_comparacion["unificar_escalas"] = $unificar_escalas;
                $res_comparacion["mostrar_lineas_valores"] = $mostrar_lineas_valores;
                $res_comparacion["html_cadena_horas_valores"] = $html_cadena_horas_valores;
            }
        }

        // Datos del widget
        $datos_widget_grafica_comparacion_campos_diferentes = $res_comparacion;

        // Se devuelven los datos del widget
        return ($datos_widget_grafica_comparacion_campos_diferentes);
    }


    // Devuelve los datos de un widget de tipo 'Gráfica de valores generales de sensores'
    function dame_datos_widget_grafica_valores_generales_sensores(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        // Parámetros de tipo de widget
        $id_ratio = $parametros_tipo["id_ratio"];
        $clases_sensor = $parametros_tipo["clases_sensor"];
        $campos = $parametros_tipo["campos"];
        $parametros_extra_campos = $parametros_tipo["parametros_extra_campos"];
        $ids_sensores = $parametros_tipo["ids_sensores"];
        $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
        $intervalo_valores = $parametros_tipo["intervalo_valores"];
        $agregacion = $parametros_tipo["agregacion"];
        $horario_semanal = $parametros_tipo["horario_semanal"];
        $exclusion_fechas = $parametros_tipo["exclusion_fechas"];
        $inclusion_fechas = $parametros_tipo["inclusion_fechas"];

        // Se obtienen los nombres de los sensores
        $nombres_sensores = dame_nombres_sensores($ids_sensores);

        // Fechas de inicio y fin (local)
        $fechas_horas_inicio_fin = dame_fechas_inicio_fin_periodo_tiempo_widget(
            $periodo_tiempo,
            $iniciar_comienzo_periodo_tiempo,
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local,
            $clases_sensor);
        $fecha_hora_inicio_local = $fechas_horas_inicio_fin["fecha_hora_inicio_local"];
        $fecha_hora_fin_local = $fechas_horas_inicio_fin["fecha_hora_fin_local"];

        // Conversión a cadena
        $cadena_fecha_hora_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_fin_local_local = convierte_fecha_a_cadena($fecha_hora_fin_local, $_SESSION["formato_fecha_hora_local"]);

        // Se recuperan los datos de valores generales
        $parametros_valores_generales = array(
            "id_ratio" => $id_ratio,
            "clases_sensor" => $clases_sensor,
            "ids_sensores" => $ids_sensores,
            "nombres_sensores" => $nombres_sensores,
            "campos" => $campos,
            "parametros_extra_campos" => $parametros_extra_campos,
            "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
            "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
            "minutos_desfase_utc" => $minutos_desfase_utc,
            "intervalo_valores" => $intervalo_valores,
            "agregacion" => $agregacion,
            "horario_semanal" => json_encode($horario_semanal),
            "exclusion_fechas" => json_encode($exclusion_fechas),
            "inclusion_fechas" => json_encode($inclusion_fechas));
        $res_valores_generales = dame_valores_generales_sensores($parametros_valores_generales);
        if ($res_valores_generales["res"] == "OK")
        {
            // Comprobación de datos
            if ($res_valores_generales["hay_datos"] == true)
            {
                // Cadenas con las horas inicial y final de la consulta
                $res_valores_generales["fecha_hora_inicio_consulta"] = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_JQPLOT);
                $res_valores_generales["fecha_hora_fin_consulta"] = convierte_formato_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_JQPLOT);

                // Formatos de fechas
                $cadena_fecha_hora_inicio_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_hora_local_sin_segundos"]);
                $cadena_fecha_hora_fin_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_hora_local_sin_segundos"]);

                // Cadena de horas de valores
                $html_cadena_horas_valores = "";
                $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_fecha_hora_inicio_local_local."),</span>";
                $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_fecha_hora_fin_local_local.")</span>";

                // Mostrar líneas de valores
                switch ($intervalo_valores)
                {
                    case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
                    {
                        $mostrar_lineas_valores = false;
                        break;
                    }
                    default:
                    {
                        $mostrar_lineas_valores = true;
                        break;
                    }
                }

                // Parámetros 'extra' para el dibujado del widget
                $res_valores_generales["intervalo_valores"] = $intervalo_valores;
                $res_valores_generales["agregacion"] = $agregacion;
                $res_valores_generales["mostrar_lineas_valores"] = $mostrar_lineas_valores;
                $res_valores_generales["html_cadena_horas_valores"] = $html_cadena_horas_valores;
            }
        }

        // Datos del widget
        $datos_widget_grafica_valores_generales = $res_valores_generales;
        $datos_widget_grafica_valores_generales["clases_sensor"] = $clases_sensor;
        $datos_widget_grafica_valores_generales["campos"] = $campos;

        // Se devuelven los datos del widget
        return ($datos_widget_grafica_valores_generales);
    }


    // Devuelve los datos de un widget de tipo 'Valor agregado de valores generales de sensores'
    function dame_datos_widget_valor_agregado_valores_generales_sensores(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        // Parámetros de tipo de widget
        $id_ratio = $parametros_tipo["id_ratio"];
        $clases_sensor = $parametros_tipo["clases_sensor"];
        $campos = $parametros_tipo["campos"];
        $parametros_extra_campos = $parametros_tipo["parametros_extra_campos"];
        $ids_sensores = $parametros_tipo["ids_sensores"];
        $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
        $intervalo_valores = $parametros_tipo["intervalo_valores"];
        $utilizar_colores_fondo = $parametros_tipo["utilizar_colores_fondo"];
        $colores_fondo = $parametros_tipo["colores_fondo"];
        $valor_limite_colores_fondo_1 = $parametros_tipo["valor_limite_colores_fondo_1"];
        $valor_limite_colores_fondo_2 = $parametros_tipo["valor_limite_colores_fondo_2"];
        $agregacion = $parametros_tipo["agregacion"];
        $icono = $parametros_tipo["icono"];
        $horario_semanal = $parametros_tipo["horario_semanal"];
        $exclusion_fechas = $parametros_tipo["exclusion_fechas"];
        $inclusion_fechas = $parametros_tipo["inclusion_fechas"];

        // Se obtienen los nombres de los sensores
        $nombres_sensores = dame_nombres_sensores($ids_sensores);

        // Fechas de inicio y fin (local)
        $fechas_horas_inicio_fin = dame_fechas_inicio_fin_periodo_tiempo_widget(
            $periodo_tiempo,
            $iniciar_comienzo_periodo_tiempo,
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local,
            $clases_sensor);
        $fecha_hora_inicio_local = $fechas_horas_inicio_fin["fecha_hora_inicio_local"];
        $fecha_hora_fin_local = $fechas_horas_inicio_fin["fecha_hora_fin_local"];

        // Conversión a cadena
        $cadena_fecha_hora_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_fin_local_local = convierte_fecha_a_cadena($fecha_hora_fin_local, $_SESSION["formato_fecha_hora_local"]);

        // Se recuperan los datos de valores generales
        $parametros_valores_generales = array(
            "id_ratio" => $id_ratio,
            "clases_sensor" => $clases_sensor,
            "ids_sensores" => $ids_sensores,
            "nombres_sensores" => $nombres_sensores,
            "campos" => $campos,
            "parametros_extra_campos" => $parametros_extra_campos,
            "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
            "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
            "minutos_desfase_utc" => $minutos_desfase_utc,
            "intervalo_valores" => $intervalo_valores,
            "agregacion" => $agregacion,
            "horario_semanal" => json_encode($horario_semanal),
            "exclusion_fechas" => json_encode($exclusion_fechas),
            "inclusion_fechas" => json_encode($inclusion_fechas));
        $res_valores_generales = dame_valores_generales_sensores($parametros_valores_generales);
        if ($res_valores_generales["res"] == "OK")
        {
            // Comprobación de datos
            if ($res_valores_generales["hay_datos"] == true)
            {
                // Nota: Tamaño de la fuente dependiente de la configuración de la cuadrícula
                $clase_tamanyo_fuente_valor_agregado_widget = "tamanyo-fuente-texto-grande-widget-valor-digital-columnas-".$numero_columnas_fila_widget_clase_contenido_widget;

                // Colores de fondo del widget
                $valor_agregado = $res_valores_generales["valor_agregado"];
                $indice_color_fondo = dame_indice_color_fondo_widget(
                    $valor_agregado,
                    $utilizar_colores_fondo,
                    $valor_limite_colores_fondo_1,
                    $valor_limite_colores_fondo_2);

                // Datos del widget
                $datos_widget_valor_agregado_valores_generales = array(
                    "texto_valor_agregado_sin_unidad" => $res_valores_generales["texto_valor_agregado_sin_unidad"],
                    "unidad_medida" => $res_valores_generales["unidad_medida"],
                    "clase_tamanyo_fuente_valor_agregado_widget" => $clase_tamanyo_fuente_valor_agregado_widget,
                    "colores_fondo" => $colores_fondo,
                    "indice_color_fondo" => $indice_color_fondo);

                // Cadenas de fechas
                $zona_horaria = dame_zona_horaria_local();
                $cadena_fecha_hora_inicio_valores_local_utc = convierte_formato_fecha($res_valores_generales["fecha_hora_inicio_valores"], FORMATO_FECHA_HORA_JQPLOT, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
                $cadena_fecha_hora_inicio_valores_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_local_utc, $_SESSION["formato_fecha_hora_local_sin_segundos"], ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_fecha_hora_fin_valores_local_utc = convierte_formato_fecha($res_valores_generales["fecha_hora_fin_valores"], FORMATO_FECHA_HORA_JQPLOT, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
                $cadena_fecha_hora_fin_valores_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_local_utc, $_SESSION["formato_fecha_hora_local_sin_segundos"], ZONA_HORARIA_UTC, $zona_horaria);

                // Cadena de horas de valores
                $html_cadena_horas_valores = "";
                $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_fecha_hora_inicio_valores_local_local."),</span>";
                $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_fecha_hora_fin_valores_local_local.")</span>";

                // Parámetros 'extra' para el dibujado del widget
                $datos_widget_valor_agregado_valores_generales["html_cadena_horas_valores"] = $html_cadena_horas_valores;
            }
            else
            {
                $datos_widget_valor_agregado_valores_generales = array("hay_datos" => false);
            }
        }
        else
        {
            $datos_widget_valor_agregado_valores_generales = array(
                "res" => "ERROR",
                "msg" => $res_valores_generales["msg"]);
        }

        // Icono
        $datos_widget_valor_agregado_valores_generales["icono"] = $icono;

        // Se devuelven los datos del widget
        return ($datos_widget_valor_agregado_valores_generales);
    }


    // Devuelve los datos de un widget de tipo 'Gráfica de incrementos totales de sensores'
    function dame_datos_widget_grafica_incrementos_totales_sensores(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        // Parámetros de tipo de widget
        $tipo_grafica = $parametros_tipo["tipo_grafica"];
        $id_ratio = $parametros_tipo["id_ratio"];
        $clases_sensor = $parametros_tipo["clases_sensor"];
        $campos = $parametros_tipo["campos"];
        $parametros_extra_campos = $parametros_tipo["parametros_extra_campos"];
        $ids_sensores = $parametros_tipo["ids_sensores"];
        $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
        $intervalo_valores = $parametros_tipo["intervalo_valores"];
        $agregacion = $parametros_tipo["agregacion"];
        $horario_semanal = $parametros_tipo["horario_semanal"];
        $exclusion_fechas = $parametros_tipo["exclusion_fechas"];
        $inclusion_fechas = $parametros_tipo["inclusion_fechas"];

        // Se obtienen los nombres de los sensores
        $nombres_sensores = dame_nombres_sensores($ids_sensores);

        // Fechas de inicio y fin (local)
        $fechas_horas_inicio_fin = dame_fechas_inicio_fin_periodo_tiempo_widget(
            $periodo_tiempo,
            $iniciar_comienzo_periodo_tiempo,
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local,
            $clases_sensor);
        $fecha_hora_inicio_local = $fechas_horas_inicio_fin["fecha_hora_inicio_local"];
        $fecha_hora_fin_local = $fechas_horas_inicio_fin["fecha_hora_fin_local"];

        // Conversión a cadena
        $cadena_fecha_hora_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_fin_local_local = convierte_fecha_a_cadena($fecha_hora_fin_local, $_SESSION["formato_fecha_hora_local"]);

        // Se recuperan los datos de incrementos totales
        $parametros_incrementos = array(
            "id_ratio" => $id_ratio,
            "clases_sensor" => $clases_sensor,
            "campos" => $campos,
            "parametros_extra_campos" => $parametros_extra_campos,
            "ids_sensores" => $ids_sensores,
            "nombres_sensores" => $nombres_sensores,
            "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
            "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
            "intervalo_valores" => $intervalo_valores,
            "agregacion" => $agregacion,
            "horario_semanal" => json_encode($horario_semanal),
            "exclusion_fechas" => json_encode($exclusion_fechas),
            "inclusion_fechas" => json_encode($inclusion_fechas));
        $res_incrementos = dame_incrementos_totales_sensores($parametros_incrementos);
        if ($res_incrementos["res"] == "OK")
        {
            // Comprobación de datos
            if ($res_incrementos["hay_datos"] == true)
            {
                // Cadenas de fechas
                if (($res_incrementos["fecha_hora_inicio_incrementos"] !== NULL) && ($res_incrementos["fecha_hora_fin_incrementos"] !== NULL))
                {
                    $zona_horaria = dame_zona_horaria_local();
                    $cadena_hora_incremento_inicial_local_utc = convierte_formato_fecha($res_incrementos["fecha_hora_inicio_incrementos"], FORMATO_FECHA_HORA_JQPLOT, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
                    $cadena_hora_incremento_inicial_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_incremento_inicial_local_utc, $_SESSION["formato_fecha_hora_local_sin_segundos"], ZONA_HORARIA_UTC, $zona_horaria);
                    $cadena_hora_incremento_final_local_utc = convierte_formato_fecha($res_incrementos["fecha_hora_fin_incrementos"], FORMATO_FECHA_HORA_JQPLOT, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
                    $cadena_hora_incremento_final_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_incremento_final_local_utc, $_SESSION["formato_fecha_hora_local_sin_segundos"], ZONA_HORARIA_UTC, $zona_horaria);
                }

                // Cadena de horas de valores
                $html_cadena_horas_valores = "";
                $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_hora_incremento_inicial_local_local."),</span>";
                $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-graficas'>(".$cadena_hora_incremento_final_local_local.")</span>";

                // Parámetros 'extra' para el dibujado del widget
                $res_incrementos["tipo_grafica"] = $tipo_grafica;
                $res_incrementos["html_cadena_horas_valores"] = $html_cadena_horas_valores;
            }
        }

        // Datos del widget
        $datos_widget_grafica_incrementos_totales_sensores = $res_incrementos;

        // Se devuelven los datos del widget
        return ($datos_widget_grafica_incrementos_totales_sensores);
    }


    //
    // Funciones auxiliares para obtener los datos de los widgets
    //


    // Devuelve si existe el valor de clase de sensor
    function existe_valor_clase_sensor_widget($valores_clase_sensor, $clase_sensor, $campo)
    {
        switch ($clase_sensor)
        {
            // Clases con valores de clase específicos
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                if ($campo == CAMPO_TODOS)
                {
                    $existe_valor_clase_sensor = true;
                }
                else
                {
                    $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_clase_sensor);
                    switch($campo)
                    {
                        case CAMPO_INCREMENTO:
                        case CAMPO_INCREMENTO_POTENCIA:
                        {
                            $valor = $valores_clase_sensor[0];
                            break;
                        }
                        case CAMPO_TRAMO:
                        {
                            $valor = $valores_clase_sensor[1];
                            break;
                        }
                        case CAMPO_COSTE:
                        {
                            $valor = $valores_clase_sensor[2];
                            break;
                        }
                        case CAMPO_SOBREPOTENCIA:
                        {
                            $valor = $valores_clase_sensor[3];
                            break;
                        }
                        default:
                        {
                            throw new Exception("Campo desconocido: '".$campo."'");
                        }
                    }
                    $existe_valor_clase_sensor = ($valor != "");
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                if ($campo == CAMPO_TODOS)
                {
                    $existe_valor_clase_sensor = true;
                }
                else
                {
                    $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_clase_sensor);
                    switch ($campo)
                    {
                        case CAMPO_INCREMENTO:
                        case CAMPO_INCREMENTO_POTENCIA:
                        {
                            $valor = $valores_clase_sensor[0];
                            break;
                        }
                        case CAMPO_TRAMO:
                        {
                            $valor = $valores_clase_sensor[1];
                            break;
                        }
                        case CAMPO_COSENO_PHI:
                        {
                            $valor = $valores_clase_sensor[2];
                            break;
                        }
                        case CAMPO_PENALIZABLE:
                        {
                            $valor = $valores_clase_sensor[3];
                            break;
                        }
                        default:
                        {
                            throw new Exception("Campo desconocido: '".$campo."'");
                        }
                    }
                    $existe_valor_clase_sensor = ($valor != "");
                }
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                if ($campo == CAMPO_TODOS)
                {
                    $existe_valor_clase_sensor = true;
                }
                else
                {
                    $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_clase_sensor);
                    switch($campo)
                    {
                        case CAMPO_CONSUMO_ESTIMADO:
                        {
                            $valor = $valores_clase_sensor[0];
                            break;
                        }
                        case CAMPO_CONSUMO_REAL:
                        {
                            $valor = $valores_clase_sensor[1];
                            break;
                        }
                        case CAMPO_DESVIO_CONSUMO:
                        {
                            $valor = $valores_clase_sensor[2];
                            break;
                        }
                        case CAMPO_COSTE_DESVIO:
                        {
                            $valor = $valores_clase_sensor[3];
                            break;
                        }
                        case CAMPO_PENALIZABLE:
                        {
                            $valor = $valores_clase_sensor[4];
                            break;
                        }
                        default:
                        {
                            throw new Exception("Campo desconocido: '".$campo."'");
                        }
                    }
                    $existe_valor_clase_sensor = ($valor != "");
                }
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                if ($campo == CAMPO_TODOS)
                {
                    $existe_valor_clase_sensor = true;
                }
                else
                {
                    $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_clase_sensor);
                    switch($campo)
                    {
                        case CAMPO_INCREMENTO:
                        {
                            $valor = $valores_clase_sensor[0];
                            break;
                        }
                        case CAMPO_CONSUMO:
                        {
                            $valor = $valores_clase_sensor[1];
                            break;
                        }
                        case CAMPO_COSTE:
                        {
                            $valor = $valores_clase_sensor[2];
                            break;
                        }
                        default:
                        {
                            throw new Exception("Campo desconocido: '".$campo."'");
                        }
                    }
                    $existe_valor_clase_sensor = ($valor != "");
                }
                break;
            }
            case CLASE_SENSOR_GENERICA:
            {
                if ($campo == CAMPO_TODOS)
                {
                    $existe_valor_clase_sensor = true;
                }
                else
                {
                    $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_clase_sensor);
                    switch($campo)
                    {
                        case CAMPO_VALOR:
                        {
                            $valor = $valores_clase_sensor[0];
                            break;
                        }
                        case CAMPO_INCREMENTO:
                        {
                            $valor = $valores_clase_sensor[1];
                            break;
                        }
                        default:
                        {
                            throw new Exception("Campo desconocido: '".$campo."'");
                        }
                    }
                    $existe_valor_clase_sensor = ($valor != "");
                }
                break;
            }
            // Clases sin valores de clase (se utilizan los valores periódicos)
            case CLASE_SENSOR_LUZ_INTERIOR:
            case CLASE_SENSOR_VIENTO:
            case CLASE_SENSOR_TEMPERATURA:
            case CLASE_SENSOR_HUMEDAD:
            {
                $existe_valor_clase_sensor = true;
                break;
            }
            // Clases sin procesado (sin valores de clase ni valores periódicos)
            case CLASE_SENSOR_CORTES_TENSION:
            {
                $existe_valor_clase_sensor = false;
                break;
            }
            default:
            {
                throw new Exception("Clase desconocida: '".$clase_sensor."'");
            }
        }
        return ($existe_valor_clase_sensor);
    }


    // Devuelve el código HTML con la cadena de valores de un sensor para un widget de tipo valor digital de sensor
    function dame_html_cadena_valores_sensor_widget_valor_digital_sensor(
        $id_ratio,
        $id_sensor,
        $hora_valores_sensor,
        $valores_sensor,
        $clase_sensor,
        $parametros_clase,
        $incrementos_tiempo_real_horarios,
        $granularidad_sensor,
        $intervalo_valores,
        $campo,
        $numero_columnas_fila_widget_clase_contenido_widget)
    {
        // Nota: Altura del valor y la unidad dependiente de la configuración de la cuadrícula
        $clase_tamanyo_fuente_texto_grande_widget = "tamanyo-fuente-texto-grande-widget-valor-digital-columnas-".$numero_columnas_fila_widget_clase_contenido_widget;
        $clase_css_texto_grande = "texto-grande-widget-valor-digital-sensor ".$clase_tamanyo_fuente_texto_grande_widget;
        $clase_css_texto_pequenyo = "texto-pequenyo-widget-valor-digital-sensor";

        // Campo de sensor
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_HUMEDAD:
            {
                $campo = CAMPO_TODOS;
                break;
            }
        }

        // Todos los campos
        if ($campo == CAMPO_TODOS)
        {
            // Se recuperan los valores del sensor (con valores de clase si aplica)
            $recuperar_valores_clase_sensor = NULL;
            $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
            if ($caracteristicas_clase_sensor["valores_clase"] == true)
            {
                switch ($granularidad_sensor)
                {
                    case GRANULARIDAD_TIEMPO_REAL:
                    {
                        $recuperar_valores_clase_sensor = false;
                        break;
                    }
                    default:
                    {
                        $recuperar_valores_clase_sensor = true;
                        break;
                    }
                }
            }
            else
            {
                $recuperar_valores_clase_sensor = false;
            }
            $clase_css_texto_pequenyo = "texto-pequenyo-widget-valor-digital-sensor";
            if ($recuperar_valores_clase_sensor == false)
            {
                $cadena_valores_sensor = NodoSensor::dame_cadena_valores_sensor(
                    $id_ratio,
                    $id_sensor,
                    $hora_valores_sensor,
                    $valores_sensor,
                    $clase_sensor,
                    $parametros_clase,
                    $incrementos_tiempo_real_horarios,
                    $granularidad_sensor,
                    SEPARADOR_VALOR_INCREMENTO_SENSOR,
                    FORMATO_CADENA_VALORES_SENSOR_REDUCIDO,
                    $clase_css_texto_pequenyo);
            }
            else
            {
                $cadena_valores_sensor = NodoSensor::dame_cadena_valores_clase_sensor(
                    $id_ratio,
                    $id_sensor,
                    $hora_valores_sensor,
                    $valores_sensor,
                    $clase_sensor,
                    $parametros_clase,
                    $granularidad_sensor,
                    $clase_css_texto_pequenyo);
            }

            // Código HTML
            $codigo_html = "<div class='".$clase_css_texto_grande."'>".$cadena_valores_sensor."</div>";
        }
        else
        {
            // Un solo campo
            $valor_unidad_sensor = dame_valor_unidad_sensor_widget(
                $id_ratio,
                $id_sensor,
                $hora_valores_sensor,
                $valores_sensor,
                $clase_sensor,
                $parametros_clase,
                $incrementos_tiempo_real_horarios,
                $granularidad_sensor,
                $intervalo_valores,
                $campo);
            if ($valor_unidad_sensor === NULL)
            {
                $codigo_html = NULL;
            }
            else
            {
                $codigo_html = "<div class='".$clase_css_texto_grande."'>".$valor_unidad_sensor["cadena_valor"];
                if ($valor_unidad_sensor["unidad"] != "")
                {
                    $codigo_html .= " "."<span class='".$clase_css_texto_pequenyo."'>".$valor_unidad_sensor["unidad"]."</span></div>";
                }
            }
        }
        return ($codigo_html);
    }


    // Devuelve el código HTML con la cadena de valor medio acumulado de un sensor para un widget de tipo valor digital de sensor
    function dame_html_cadena_valor_medio_acumulado_sensor_widget_valor_digital_sensor(
        $valor_medio_acumulado,
        $unidad_medida,
        $numero_decimales,
        $numero_columnas_fila_widget_clase_contenido_widget)
    {
        // Nota: Altura del valor y la unidad dependiente de la configuración de la cuadrícula
        $clase_tamanyo_fuente_texto_grande_widget = "tamanyo-fuente-texto-grande-widget-valor-digital-columnas-".$numero_columnas_fila_widget_clase_contenido_widget;
        $clase_css_texto_grande = "texto-grande-widget-valor-digital-sensor ".$clase_tamanyo_fuente_texto_grande_widget;

        $cadena_valor = formatea_numero($valor_medio_acumulado, $numero_decimales);
        $codigo_html = "<div class='".$clase_css_texto_grande."'>".$cadena_valor;
        $clase_css_texto_pequenyo = "texto-pequenyo-widget-valor-digital-sensor";
        if ($unidad_medida != "")
        {
            $codigo_html .= " "."<span class='".$clase_css_texto_pequenyo."'>".$unidad_medida."</span></div>";
        }

        return ($codigo_html);
    }


    // Devuelve un valor de sensor y su unidad según la clase de sensor
    function dame_valor_unidad_sensor_widget(
        $id_ratio,
        $id_sensor,
        $cadena_hora_valores_sensor_base_datos_utc,
        $valores_sensor,
        $clase_sensor,
        $parametros_clase,
        $incrementos_tiempo_real_horarios,
        $granularidad_sensor,
        $intervalo_valores,
        $campo)
    {
        $idiomas = new Idiomas();

        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);
        if ($aplicar_ratio == true)
        {
            $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                $id_ratio,
                $id_sensor,
                $cadena_hora_valores_sensor_base_datos_utc,
                $cadena_hora_valores_sensor_base_datos_utc,
                $intervalo_valores,
                NULL,
                NULL,
                NULL);
        }

        $valor = NULL;
        $numero_decimales_valor = NULL;
        $cadena_valor = NULL;
        $unidad_medida = NULL;
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_TEMPERATURA:
            {
                $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                $temperatura = $valores_clase_sensor[0];
                $grados_hora_calefaccion = $valores_clase_sensor[1];
                $grados_hora_refrigeracion = $valores_clase_sensor[2];
                $grados_dia_calefaccion = $valores_clase_sensor[3];
                $grados_dia_refrigeracion = $valores_clase_sensor[4];
                switch ($campo)
                {
                    case CAMPO_TEMPERATURA:
                    {
                        $valor = $temperatura;
                        $numero_decimales_valor = 2;
                        break;
                    }
                    case CAMPO_GRADOS_HORA_CALEFACCION:
                    {
                        $valor = $grados_hora_calefaccion;
                        $numero_decimales_valor = 2;
                        break;
                    }
                    case CAMPO_GRADOS_HORA_REFRIGERACION:
                    {
                        $valor = $grados_hora_refrigeracion;
                        $numero_decimales_valor = 2;
                        break;
                    }
                    case CAMPO_GRADOS_DIA_CALEFACCION:
                    {
                        $valor = $grados_dia_calefaccion;
                        $numero_decimales_valor = 2;
                        break;
                    }
                    case CAMPO_GRADOS_DIA_REFRIGERACION:
                    {
                        $valor = $grados_dia_refrigeracion;
                        $numero_decimales_valor = 2;
                        break;
                    }
                }
                $unidad_medida = $_SESSION["unidad_medida_temperatura"];
                break;
            }
            case CLASE_SENSOR_HUMEDAD:
            {
                $valor = $valores_sensor;
                $numero_decimales_valor = 2;
                $unidad_medida = "%";
                break;
            }
            case CLASE_SENSOR_LUZ_INTERIOR:
            {
                $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                $iluminacion = $valores_clase_sensor[0];
                $luz_artificial = $valores_clase_sensor[1];
                switch ($campo)
                {
                    case CAMPO_ILUMINACION:
                    {
                        if ($iluminacion == 1)
                        {
                            $unidad_medida = $idiomas->_("lux");
                        }
                        else
                        {
                            $unidad_medida = $idiomas->_("luxes");
                        }
                        $valor = $iluminacion;
                        $numero_decimales_valor = 2;
                        break;
                    }
                    case CAMPO_LUZ_ARTIFICIAL:
                    {
                        // Nota: Si el valor no es 0 o 1, se asume que es un porcentaje (entre 0 y 1)
                        $valor = $luz_artificial;
                        switch ($luz_artificial)
                        {
                            case 0:
                            {
                                $numero_decimales_valor = 0;
                                $cadena_valor = $idiomas->_("No hay luz artificial");
                                $unidad_medida = "";
                                break;
                            }
                            case 1:
                            {
                                $numero_decimales_valor = 0;
                                $cadena_valor = $idiomas->_("Luz artificial");
                                $unidad_medida = "";
                                break;
                            }
                            default:
                            {
                                $numero_decimales_valor = 2;
                                $cadena_valor = formatea_numero($luz_artificial * 100, 2);
                                $unidad_medida = "%"." ".$idiomas->_("luz artificial");
                                break;
                            }
                        }
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_VIENTO:
            {
                $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                $velocidad = formatea_numero($valores_clase_sensor[0], 2);
                $direccion = formatea_numero($valores_clase_sensor[1], 2);
                switch ($campo)
                {
                    case CAMPO_VELOCIDAD:
                    {
                        $valor = $velocidad;
                        $numero_decimales_valor = 2;
                        $unidad_medida = $_SESSION["unidad_medida_velocidad"];
                        break;
                    }
                    case CAMPO_DIRECCION:
                    {
                        $valor = $direccion;
                        $numero_decimales_valor = 2;
                        $unidad_medida = "º";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                // Nota: En la cadena de últimos valores (valor e incremento),
                // el incremento es horario (aunque sea granularidad cuartohoraria) (es la potencia)
                $unidad_medida_absoluto = $idiomas->_("kWh");
                $unidad_medida_incremento = $idiomas->_("kWh");
                $unidad_medida_incremento_horario = $idiomas->_("kW");
                switch ($granularidad_sensor)
                {
                    case GRANULARIDAD_TIEMPO_REAL:
                    {
                        $valor_incremento_sensor = explode(SEPARADOR_VALOR_INCREMENTO_SENSOR, $valores_sensor);
                        $absoluto = $valor_incremento_sensor[0];
                        $incremento = $valor_incremento_sensor[1];
                        switch ($campo)
                        {
                            case CAMPO_ABSOLUTO:
                            {
                                if (($absoluto != "") && ($absoluto != "?"))
                                {
                                    $valor = $absoluto;
                                    $numero_decimales_valor = 2;
                                    $unidad_medida = $unidad_medida_absoluto;
                                }
                                break;
                            }
                            case CAMPO_INCREMENTO:
                            {
                                if (($incremento != "") && ($incremento != "?"))
                                {
                                    $segundos_incremento = $valor_incremento_sensor[2];
                                    $valor = $incremento * (3600 / $segundos_incremento);
                                    $numero_decimales_valor = 2;
                                    $unidad_medida = $unidad_medida_incremento_horario;
                                }
                                break;
                            }
                        }
                        break;
                    }
                    case GRANULARIDAD_CUARTOHORARIA:
                    case GRANULARIDAD_HORARIA:
                    {
                        $valores_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                        $unidad_medida_coste = $_SESSION["moneda"];
                        $unidad_medida_sobrepotencia = $idiomas->_("kW");
                        $incremento = $valores_sensor[0];
                        $tramo = $valores_sensor[1];
                        $coste = $valores_sensor[2];
                        $sobrepotencia = $valores_sensor[3];
                        switch ($campo)
                        {
                            case CAMPO_INCREMENTO:
                            {
                                $valor = $incremento;
                                $numero_decimales_valor = 2;
                                $unidad_medida = $unidad_medida_incremento;
                                break;
                            }
                            case CAMPO_INCREMENTO_POTENCIA:
                            {
                                switch ($granularidad_sensor)
                                {
                                    case GRANULARIDAD_CUARTOHORARIA:
                                    {
                                        $valor = $incremento * 4;
                                        break;
                                    }
                                    case GRANULARIDAD_HORARIA:
                                    {
                                        $valor = $incremento;
                                        break;
                                    }
                                }
                                $numero_decimales_valor = 2;
                                $unidad_medida = $unidad_medida_incremento_horario;
                                break;
                            }
                            case CAMPO_TRAMO:
                            {
                                $valor = $tramo;
                                if ($tramo == round($tramo))
                                {
                                    $numero_decimales_valor = 0;
                                }
                                else
                                {
                                    $numero_decimales_valor = 2;
                                }
                                $unidad_medida = "";
                                break;
                            }
                            case CAMPO_COSTE:
                            {
                                $valor = $coste;
                                $numero_decimales_valor = 2;
                                $unidad_medida = $unidad_medida_coste;
                                break;
                            }
                            case CAMPO_SOBREPOTENCIA:
                            {
                                $valor = $sobrepotencia;
                                $numero_decimales_valor = 2;
                                $unidad_medida = $unidad_medida_sobrepotencia;
                                break;
                            }
                        }
                        break;
                    }
                    break;
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                // Nota: En la cadena de últimos valores (valor e incremento),
                // el incremento es horario (aunque sea granularidad cuartohoraria) (es la potencia)
                $unidad_medida_absoluto = $idiomas->_("kVArh");
                $unidad_medida_incremento = $idiomas->_("kVArh");
                $unidad_medida_incremento_horario = $idiomas->_("kVAr");
                switch ($granularidad_sensor)
                {
                    case GRANULARIDAD_TIEMPO_REAL:
                    {
                        $valor_incremento_sensor = explode(SEPARADOR_VALOR_INCREMENTO_SENSOR, $valores_sensor);
                        $absoluto = $valor_incremento_sensor[0];
                        $incremento = $valor_incremento_sensor[1];
                        switch ($campo)
                        {
                            case CAMPO_ABSOLUTO:
                            {
                                if (($absoluto != "") && ($absoluto != "?"))
                                {
                                    $valor = $absoluto;
                                    $numero_decimales_valor = 2;
                                    $unidad_medida = $unidad_medida_absoluto;
                                }
                                break;
                            }
                            case CAMPO_INCREMENTO:
                            {
                                if (($incremento != "") && ($incremento != "?"))
                                {
                                    $segundos_incremento = $valor_incremento_sensor[2];
                                    $valor = $incremento * (3600 / $segundos_incremento);
                                    $numero_decimales_valor = 2;
                                    $unidad_medida = $unidad_medida_incremento_horario;
                                }
                                break;
                            }
                        }
                        break;
                    }
                    case GRANULARIDAD_CUARTOHORARIA:
                    case GRANULARIDAD_HORARIA:
                    {
                        $valores_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                        $incremento = $valores_sensor[0];
                        $tramo = $valores_sensor[1];
                        $coseno_phi = $valores_sensor[2];
                        $penalizable = $valores_sensor[3];
                        switch ($campo)
                        {
                            case CAMPO_INCREMENTO:
                            {
                                $valor = $incremento;
                                $numero_decimales_valor = 2;
                                $unidad_medida = $unidad_medida_incremento;
                                break;
                            }
                            case CAMPO_INCREMENTO_POTENCIA:
                            {
                                switch ($granularidad_sensor)
                                {
                                    case GRANULARIDAD_CUARTOHORARIA:
                                    {
                                        $valor = $incremento * 4;
                                        break;
                                    }
                                    case GRANULARIDAD_HORARIA:
                                    {
                                        $valor = $incremento;
                                        break;
                                    }
                                }
                                $numero_decimales_valor = 2;
                                $unidad_medida = $unidad_medida_incremento_horario;
                                break;
                            }
                            case CAMPO_TRAMO:
                            {
                                $valor = $tramo;
                                if ($tramo == round($tramo))
                                {
                                    $numero_decimales_valor = 0;
                                }
                                else
                                {
                                    $numero_decimales_valor = 2;
                                }
                                $unidad_medida = "";
                                break;
                            }
                            case CAMPO_COSENO_PHI:
                            {
                                $valor = $coseno_phi;
                                $numero_decimales_valor = 2;
                                $unidad_medida = "";
                                break;
                            }
                            case CAMPO_PENALIZABLE:
                            {
                                // Nota: Si el valor no es 0 o 1, se asume que es un porcentaje (entre 0 y 1)
                                $valor = $penalizable;
                                switch ($luz_artificial)
                                {
                                    case 0:
                                    {
                                        $cadena_valor = $idiomas->_("No penalizable");
                                        $unidad_medida = "";
                                        break;
                                    }
                                    case 1:
                                    {
                                        $cadena_valor = $idiomas->_("Penalizable");
                                        $unidad_medida = "";
                                        break;
                                    }
                                    default:
                                    {
                                        $cadena_valor = formatea_numero($penalizable * 100, 2);
                                        $unidad_medida = "%"." ".$idiomas->_("penalizable");
                                        break;
                                    }
                                }
                                break;
                            }
                        }
                        break;
                    }
                    break;
                }
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION:
            {
                $valor_incremento_sensor = explode(SEPARADOR_VALOR_INCREMENTO_SENSOR, $valores_sensor);
                $incremento = $valor_incremento_sensor[1];
                $numero_decimales_valor = 0;
                if (($incremento != "") && ($incremento != "?"))
                {
                    $valor = $incremento;
                    if ($incremento == VALOR_SI)
                    {
                        $cadena_valor = $idiomas->_("Cortes de tensión");
                    }
                    else
                    {
                        $cadena_valor = $idiomas->_("Tensión correcta");
                    }
                    $unidad_medida = "";
                }
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                $unidad_medida_consumo = $idiomas->_("kWh");
                $unidad_medida_coste = $_SESSION["moneda"];
                switch ($granularidad_sensor)
                {
                    case GRANULARIDAD_TIEMPO_REAL:
                    {
                        $valor = $valores_sensor;
                        $numero_decimales_valor = 2;
                        $unidad_medida = $idiomas->_("kWh");
                        break;
                    }
                    case GRANULARIDAD_CUARTOHORARIA:
                    case GRANULARIDAD_HORARIA:
                    {
                        $valores_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                        $consumo_estimado = $valores_sensor[0];
                        $consumo_real = $valores_sensor[1];
                        $desvio_consumo = $valores_sensor[2];
                        $coste_desvio = $valores_sensor[3];
                        $penalizable = $valores_sensor[4];
                        switch ($campo)
                        {
                            case CAMPO_CONSUMO_ESTIMADO:
                            {
                                $valor = $consumo_estimado;
                                $numero_decimales_valor = 2;
                                $unidad_medida = $unidad_medida_consumo;
                                break;
                            }
                            case CAMPO_CONSUMO_REAL:
                            {
                                $valor = $consumo_real;
                                $numero_decimales_valor = 2;
                                $unidad_medida = $unidad_medida_consumo;
                                break;
                            }
                            case CAMPO_DESVIO_CONSUMO:
                            {
                                $valor = $desvio_consumo;
                                $numero_decimales_valor = 2;
                                $unidad_medida = $unidad_medida_consumo;
                                break;
                            }
                            case CAMPO_COSTE_DESVIO:
                            {
                                $valor = $coste_desvio;
                                $numero_decimales_valor = 2;
                                $unidad_medida = $unidad_medida_coste;
                                break;
                            }
                            case CAMPO_PENALIZABLE:
                            {
                                // Nota: Si el valor no es 0 o 1, se asume que es un porcentaje (entre 0 y 1)
                                $valor = $penalizable;
                                switch ($penalizable)
                                {
                                    case 0:
                                    {
                                        $cadena_valor = $idiomas->_("No penalizable");
                                        $unidad_medida = "";
                                        break;
                                    }
                                    case 1:
                                    {
                                        $cadena_valor = $idiomas->_("Penalizable");
                                        $unidad_medida = "";
                                        break;
                                    }
                                    default:
                                    {
                                        $cadena_valor = formatea_numero($penalizable * 100, 2);
                                        $unidad_medida = "%"." ".$idiomas->_("penalizable");
                                        break;
                                    }
                                }
                                break;
                            }
                        }
                        break;
                    }
                    break;
                }
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                $unidad_medida_absoluto = $idiomas->_("m3");
                $unidad_medida_incremento = $idiomas->_("m3");
                switch ($granularidad_sensor)
                {
                    case GRANULARIDAD_TIEMPO_REAL:
                    {
                        $valor_incremento_sensor = explode(SEPARADOR_VALOR_INCREMENTO_SENSOR, $valores_sensor);
                        $absoluto = $valor_incremento_sensor[0];
                        $incremento = $valor_incremento_sensor[1];
                        switch ($campo)
                        {
                            case CAMPO_ABSOLUTO:
                            {
                                if (($absoluto != "") && ($absoluto != "?"))
                                {
                                    $valor = $absoluto;
                                    $numero_decimales_valor = 2;
                                    $unidad_medida = $unidad_medida_absoluto;
                                }
                                break;
                            }
                            case CAMPO_INCREMENTO:
                            {
                                if (($incremento != "") && ($incremento != "?"))
                                {
                                    $segundos_incremento = $valor_incremento_sensor[2];
                                    $valor = $incremento * (3600 / $segundos_incremento);
                                    $numero_decimales_valor = 2;
                                    $unidad_medida = $unidad_medida_incremento."/".$idiomas->_("h");
                                }
                                break;
                            }
                        }
                        break;
                    }
                    case GRANULARIDAD_CUARTOHORARIA:
                    case GRANULARIDAD_HORARIA:
                    {
                        $valores_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                        $unidad_medida_consumo = $idiomas->_("kWh");
                        $unidad_medida_coste = $_SESSION["moneda"];
                        $incremento = $valores_sensor[0];
                        $consumo = $valores_sensor[1];
                        $coste = $valores_sensor[2];
                        switch ($campo)
                        {
                            case CAMPO_INCREMENTO:
                            {
                                $valor = $incremento;
                                $numero_decimales_valor = 2;
                                $unidad_medida = $unidad_medida_incremento;
                                break;
                            }
                            case CAMPO_CONSUMO:
                            {
                                $valor = $consumo;
                                $numero_decimales_valor = 2;
                                $unidad_medida = $unidad_medida_consumo;
                                break;
                            }
                            case CAMPO_COSTE:
                            {
                                $valor = $coste;
                                $numero_decimales_valor = 2;
                                $unidad_medida = $unidad_medida_coste;
                                break;
                            }
                        }
                        break;
                    }
                    break;
                }
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                $unidad_medida_absoluto = $idiomas->_("m3");
                $unidad_medida_incremento = $idiomas->_("m3");
                switch ($granularidad_sensor)
                {
                    case GRANULARIDAD_TIEMPO_REAL:
                    {
                        $valor_incremento_sensor = explode(SEPARADOR_VALOR_INCREMENTO_SENSOR, $valores_sensor);
                        $absoluto = $valor_incremento_sensor[0];
                        $incremento = $valor_incremento_sensor[1];
                        switch ($campo)
                        {
                            case CAMPO_ABSOLUTO:
                            {
                                if (($absoluto != "") && ($absoluto != "?"))
                                {
                                    $valor = $absoluto;
                                    $numero_decimales_valor = 2;
                                    $unidad_medida = $unidad_medida_absoluto;
                                }
                                break;
                            }
                            case CAMPO_INCREMENTO:
                            {
                                if (($incremento != "") && ($incremento != "?"))
                                {
                                    $segundos_incremento = $valor_incremento_sensor[2];
                                    $valor = $incremento * (3600 / $segundos_incremento);
                                    $numero_decimales_valor = 2;
                                    $unidad_medida = $unidad_medida_incremento."/".$idiomas->_("h");
                                }
                                break;
                            }
                        }
                        break;
                    }
                    case GRANULARIDAD_CUARTOHORARIA:
                    case GRANULARIDAD_HORARIA:
                    {
                        $valores_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                        $incremento = $valores_sensor[0];
                        switch ($campo)
                        {
                            case CAMPO_INCREMENTO:
                            {
                                $valor = $incremento;
                                $numero_decimales_valor = 2;
                                $unidad_medida = $unidad_medida_incremento;
                                break;
                            }
                        }
                        break;
                    }
                    break;
                }
                break;
            }
            case CLASE_SENSOR_GENERICA:
            {
                // Nota: En la cadena de últimos valores (valor e incremento),
                // el incremento es horario (aunque sea granularidad cuartohoraria)
                $unidad_medida_generica = NodoSensor::dame_parametro_clase_generica($parametros_clase, INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_UNIDAD_MEDIDA);
                $unidad_medida = $unidad_medida_generica;
                $numero_decimales_valor = 2;
                switch ($granularidad_sensor)
                {
                    case GRANULARIDAD_TIEMPO_REAL:
                    {
                        $valor_incremento_sensor = explode(SEPARADOR_VALOR_INCREMENTO_SENSOR, $valores_sensor);
                        $valor_generica = $valor_incremento_sensor[0];
                        $incremento = $valor_incremento_sensor[1];
                        switch ($campo)
                        {
                            case CAMPO_VALOR:
                            {
                                if (($valor_generica != "") && ($valor_generica != "?"))
                                {
                                    $valor = $valor_generica;
                                }
                                break;
                            }
                            case CAMPO_INCREMENTO:
                            {
                                if (($incremento != "") && ($incremento != "?"))
                                {
                                    if ($incrementos_tiempo_real_horarios == true)
                                    {
                                        $segundos_incremento = $valor_incremento_sensor[2];
                                        $valor = $incremento * (3600 / $segundos_incremento);
                                    }
                                    else
                                    {
                                        $valor = $incremento;
                                    }
                                }
                                break;
                            }
                        }
                        break;
                    }
                    case GRANULARIDAD_CUARTOHORARIA:
                    case GRANULARIDAD_HORARIA:
                    {
                        $valores_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                        $numero_valores_sensor = count($valores_sensor);
                        switch ($numero_valores_sensor)
                        {
                            case 2:
                            {
                                $valor_generica = $valores_sensor[0];
                                $incremento = $valores_sensor[1];
                                switch ($campo)
                                {
                                    case CAMPO_VALOR:
                                    {
                                        if (($valor_generica != "") && ($valor_generica != "?"))
                                        {
                                            $valor = $valor_generica;
                                        }
                                        break;
                                    }
                                    case CAMPO_INCREMENTO:
                                    {
                                        if (($incremento != "") && ($incremento != "?"))
                                        {
                                            $valor = $incremento;
                                        }
                                        break;
                                    }
                                }
                                break;
                            }
                            case 4:
                            {
                                $valor_media = $valores_sensor[0];
                                $valor_suma = $valores_sensor[1];
                                $incremento_suma = $valores_sensor[2];
                                $incremento_media = $valores_sensor[3];
                                switch ($campo)
                                {
                                    case CAMPO_VALOR_MEDIA:
                                    {
                                        if (($valor_media != "") && ($valor_media != "?"))
                                        {
                                            $valor = $valor_media;
                                        }
                                        break;
                                    }
                                    case CAMPO_VALOR_SUMA:
                                    {
                                        if (($valor_suma != "") && ($valor_suma != "?"))
                                        {
                                            $valor = $valor_suma;
                                        }
                                        break;
                                    }
                                    case CAMPO_INCREMENTO_SUMA:
                                    {
                                        if (($incremento_suma != "") && ($incremento_suma != "?"))
                                        {
                                            $valor = $incremento_suma;
                                        }
                                        break;
                                    }
                                    case CAMPO_INCREMENTO_MEDIA:
                                    {
                                        if (($incremento_media != "") && ($incremento_media != "?"))
                                        {
                                            $valor = $incremento_media;
                                        }
                                        break;
                                    }
                                }
                                break;
                            }
                        }
                        break;
                    }
                }

                // Se añade el sufijo 'por hora' si es necesario
                // (si es granularidad en tiempo real, se añade el sufijo 'por hora')
                switch ($campo)
                {
                    case CAMPO_INCREMENTO:
                    case CAMPO_INCREMENTO_SUMA:
                    case CAMPO_INCREMENTO_MEDIA:
                    {
                        $unidad_medida = $unidad_medida_generica;
                        if (($granularidad_sensor == GRANULARIDAD_TIEMPO_REAL) && ($incrementos_tiempo_real_horarios == true))
                        {
                            if ($unidad_medida != "")
                            {
                                $unidad_medida .= "/".$idiomas->_("h");
                            }
                            else
                            {
                                $unidad_medida = $idiomas->_("por hora");
                            }
                        }
                        break;
                    }
                }
                break;
            }
        }

        // Si no hay valor
        if ($valor === NULL)
        {
            $valor_unidad = NULL;
        }
        else
        {
            // Cast a float
            $valor = (float) $valor;

            // Se aplica el ratio y formatea el valor si es necesario
            if ($aplicar_ratio == true)
            {
                aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_hora_valores_sensor_base_datos_utc, $valor);
                modifica_unidad_medida_ratio($info_ratio_sensor, $unidad_medida);
            }
            if ($cadena_valor === NULL)
            {
                $cadena_valor = formatea_numero($valor, $numero_decimales_valor);
            }
            $valor_unidad = array(
                "valor" => $valor,
                "cadena_valor" => $cadena_valor,
                "unidad" => $unidad_medida);
        }
        return ($valor_unidad);
    }


    // Devuelve el valor numérico de un campo de un sensor de un widget según la clase de sensor
    function dame_valor_numerico_sensor_widget(
        $id_ratio,
        $id_sensor,
        $cadena_fecha_hora_valores_sensor_base_datos_utc,
        $valores_sensor,
        $clase_sensor,
        $incrementos_tiempo_real_horarios,
        $granularidad_sensor,
        $campo)
    {
        $valor_numerico = NULL;
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_TEMPERATURA:
            {
                $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                $temperatura = $valores_clase_sensor[0];
                $grados_hora_calefaccion = $valores_clase_sensor[1];
                $grados_hora_refrigeracion = $valores_clase_sensor[2];
                $grados_dia_calefaccion = $valores_clase_sensor[3];
                $grados_dia_refrigeracion = $valores_clase_sensor[4];
                switch ($campo)
                {
                    case CAMPO_TEMPERATURA:
                    {
                        $valor_numerico = $temperatura;
                        break;
                    }
                    case CAMPO_GRADOS_HORA_CALEFACCION:
                    {
                        $valor_numerico = $grados_hora_calefaccion;
                        break;
                    }
                    case CAMPO_GRADOS_HORA_REFRIGERACION:
                    {
                        $valor_numerico = $grados_hora_refrigeracion;
                        break;
                    }
                    case CAMPO_GRADOS_DIA_CALEFACCION:
                    {
                        $valor_numerico = $grados_dia_calefaccion;
                        break;
                    }
                    case CAMPO_GRADOS_DIA_REFRIGERACION:
                    {
                        $valor_numerico = $grados_dia_refrigeracion;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_HUMEDAD:
            {
                $valor_numerico = $valores_sensor;
                break;
            }
            case CLASE_SENSOR_LUZ_INTERIOR:
            {
                $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                $iluminacion = $valores_clase_sensor[0];
                $luz_artificial = $valores_clase_sensor[1];
                switch ($campo)
                {
                    case CAMPO_ILUMINACION:
                    {
                        $valor_numerico = $iluminacion;
                        break;
                    }
                    case CAMPO_LUZ_ARTIFICIAL:
                    {
                        $valor_numerico = $luz_artificial;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_VIENTO:
            {
                $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                $velocidad = $valores_clase_sensor[0];
                $direccion = $valores_clase_sensor[1];
                switch ($campo)
                {
                    case CAMPO_VELOCIDAD:
                    {
                        $valor_numerico = $velocidad;
                        break;
                    }
                    case CAMPO_DIRECCION:
                    {
                        $valor_numerico = $direccion;
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                switch ($granularidad_sensor)
                {
                    case GRANULARIDAD_TIEMPO_REAL:
                    {
                        $valor_incremento_sensor = explode(SEPARADOR_VALOR_INCREMENTO_SENSOR, $valores_sensor);
                        $absoluto = $valor_incremento_sensor[0];
                        $incremento = $valor_incremento_sensor[1];
                        switch ($campo)
                        {
                            case CAMPO_ABSOLUTO:
                            {
                                if (($absoluto != "") && ($absoluto != "?"))
                                {
                                    $valor_numerico = $absoluto;
                                }
                                break;
                            }
                            case CAMPO_INCREMENTO:
                            {
                                if (($incremento != "") && ($incremento != "?"))
                                {
                                    $segundos_incremento = $valor_incremento_sensor[2];
                                    $valor_numerico = $incremento * (3600 / $segundos_incremento);
                                }
                                break;
                            }
                        }
                        break;
                    }
                    case GRANULARIDAD_CUARTOHORARIA:
                    case GRANULARIDAD_HORARIA:
                    {
                        $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                        $incremento = $valores_clase_sensor[0];
                        $tramo = $valores_clase_sensor[1];
                        $coste = $valores_clase_sensor[2];
                        $sobrepotencia = $valores_clase_sensor[3];
                        switch ($campo)
                        {
                            case CAMPO_INCREMENTO:
                            {
                                $valor_numerico = $incremento;
                                break;
                            }
                            case CAMPO_INCREMENTO_POTENCIA:
                            {
                                switch ($granularidad_sensor)
                                {
                                    case GRANULARIDAD_CUARTOHORARIA:
                                    {
                                        $valor_numerico = $incremento * 4;
                                        break;
                                    }
                                    case GRANULARIDAD_HORARIA:
                                    {
                                        $valor_numerico = $incremento;
                                        break;
                                    }
                                }
                                break;
                            }
                            case CAMPO_TRAMO:
                            {
                                $valor_numerico = $tramo;
                                break;
                            }
                            case CAMPO_COSTE:
                            {
                                $valor_numerico = $coste;
                                break;
                            }
                            case CAMPO_SOBREPOTENCIA:
                            {
                                $valor_numerico = $sobrepotencia;
                                break;
                            }
                        }
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                switch ($granularidad_sensor)
                {
                    case GRANULARIDAD_TIEMPO_REAL:
                    {
                        $valor_incremento_sensor = explode(SEPARADOR_VALOR_INCREMENTO_SENSOR, $valores_sensor);
                        $absoluto = $valor_incremento_sensor[0];
                        $incremento = $valor_incremento_sensor[1];
                        switch ($campo)
                        {
                            case CAMPO_ABSOLUTO:
                            {
                                if (($absoluto != "") && ($absoluto != "?"))
                                {
                                    $valor_numerico = $absoluto;
                                }
                                break;
                            }
                            case CAMPO_INCREMENTO:
                            {
                                if (($incremento != "") && ($incremento != "?"))
                                {
                                    $segundos_incremento = $valor_incremento_sensor[2];
                                    $valor_numerico = $incremento * (3600 / $segundos_incremento);
                                }
                                break;
                            }
                        }
                        break;
                    }
                    case GRANULARIDAD_CUARTOHORARIA:
                    case GRANULARIDAD_HORARIA:
                    {
                        $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                        $incremento = $valores_clase_sensor[0];
                        $tramo = $valores_clase_sensor[1];
                        $coseno_phi = $valores_clase_sensor[2];
                        $penalizable = $valores_clase_sensor[3];
                        switch ($campo)
                        {
                            case CAMPO_INCREMENTO:
                            {
                                $valor_numerico = $incremento;
                                break;
                            }
                            case CAMPO_TRAMO:
                            {
                                $valor_numerico = $tramo;
                                break;
                            }
                            case CAMPO_COSENO_PHI:
                            {
                                $valor_numerico = $coseno_phi;
                                break;
                            }
                            case CAMPO_PENALIZABLE:
                            {
                                $valor_numerico = $penalizable;
                                break;
                            }
                        }
                        break;
                    }
                    break;
                }
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION:
            {
                $valor_incremento_sensor = explode(SEPARADOR_VALOR_INCREMENTO_SENSOR, $valores_sensor);
                $incremento = $valor_incremento_sensor[1];
                if (($incremento != "") && ($incremento != "?"))
                {
                    $valor_numerico = $incremento;
                }
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                switch ($granularidad_sensor)
                {
                    case GRANULARIDAD_TIEMPO_REAL:
                    {
                        $valor_numerico = $valores_sensor;
                        break;
                    }
                    case GRANULARIDAD_CUARTOHORARIA:
                    case GRANULARIDAD_HORARIA:
                    {
                        $valores_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                        $consumo_estimado = $valores_sensor[0];
                        $consumo_real = $valores_sensor[1];
                        $desvio_consumo = $valores_sensor[2];
                        $coste_desvio = $valores_sensor[3];
                        $penalizable = $valores_sensor[4];
                        switch ($campo)
                        {
                            case CAMPO_CONSUMO_ESTIMADO:
                            {
                                $valor_numerico = $consumo_estimado;
                                break;
                            }
                            case CAMPO_CONSUMO_REAL:
                            {
                                $valor_numerico = $consumo_real;
                                break;
                            }
                            case CAMPO_DESVIO_CONSUMO:
                            {
                                $valor_numerico = $desvio_consumo;
                                break;
                            }
                            case CAMPO_COSTE_DESVIO:
                            {
                                $valor_numerico = $coste_desvio;
                                break;
                            }
                            case CAMPO_PENALIZABLE:
                            {
                                $valor_numerico = $penalizable;
                                break;
                            }
                        }
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                switch ($granularidad_sensor)
                {
                    case GRANULARIDAD_TIEMPO_REAL:
                    {
                        $valor_incremento_sensor = explode(SEPARADOR_VALOR_INCREMENTO_SENSOR, $valores_sensor);
                        $absoluto = $valor_incremento_sensor[0];
                        $incremento = $valor_incremento_sensor[1];
                        switch ($campo)
                        {
                            case CAMPO_ABSOLUTO:
                            {
                                if (($absoluto != "") && ($absoluto != "?"))
                                {
                                    $valor_numerico = $absoluto;
                                }
                                break;
                            }
                            case CAMPO_INCREMENTO:
                            {
                                if (($incremento != "") && ($incremento != "?"))
                                {
                                    $segundos_incremento = $valor_incremento_sensor[2];
                                    $valor_numerico = $incremento * (3600 / $segundos_incremento);
                                }
                                break;
                            }
                        }
                        break;
                    }
                    case GRANULARIDAD_CUARTOHORARIA:
                    case GRANULARIDAD_HORARIA:
                    {
                        $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                        $incremento = $valores_clase_sensor[0];
                        $consumo = $valores_clase_sensor[1];
                        $coste = $valores_clase_sensor[2];
                        switch ($campo)
                        {
                            case CAMPO_INCREMENTO:
                            {
                                $valor_numerico = $incremento;
                                break;
                            }
                            case CAMPO_CONSUMO:
                            {
                                $valor_numerico = $consumo;
                                break;
                            }
                            case CAMPO_COSTE:
                            {
                                $valor_numerico = $coste;
                                break;
                            }
                        }
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                switch ($granularidad_sensor)
                {
                    case GRANULARIDAD_TIEMPO_REAL:
                    {
                        $valor_incremento_sensor = explode(SEPARADOR_VALOR_INCREMENTO_SENSOR, $valores_sensor);
                        $absoluto = $valor_incremento_sensor[0];
                        $incremento = $valor_incremento_sensor[1];
                        switch ($campo)
                        {
                            case CAMPO_ABSOLUTO:
                            {
                                if (($absoluto != "") && ($absoluto != "?"))
                                {
                                    $valor_numerico = $absoluto;
                                }
                                break;
                            }
                            case CAMPO_INCREMENTO:
                            {
                                if (($incremento != "") && ($incremento != "?"))
                                {
                                    $segundos_incremento = $valor_incremento_sensor[2];
                                    $valor_numerico = $incremento * (3600 / $segundos_incremento);
                                }
                                break;
                            }
                        }
                        break;
                    }
                    case GRANULARIDAD_HORARIA:
                    {
                        $valores_clase_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                        $incremento = $valores_clase_sensor[0];
                        switch ($campo)
                        {
                            case CAMPO_INCREMENTO:
                            {
                                $valor_numerico = $incremento;
                                break;
                            }
                        }
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_GENERICA:
            {
                switch ($granularidad_sensor)
                {
                    case GRANULARIDAD_TIEMPO_REAL:
                    {
                        $valores_sensor = explode(SEPARADOR_VALOR_INCREMENTO_SENSOR, $valores_sensor);
                        break;
                    }
                    case GRANULARIDAD_HORARIA:
                    {
                        $valores_sensor = explode(SEPARADOR_VALORES_SENSOR, $valores_sensor);
                        break;
                    }
                }

                $numero_valores_sensor = count($valores_sensor);
                switch ($numero_valores_sensor)
                {
                    // Nota: El 3er valor es el tiempo del incremento (en tiempo real)
                    case 2:
                    case 3:
                    {
                        $valor = $valores_sensor[0];
                        $incremento = $valores_sensor[1];
                        switch ($campo)
                        {
                            case CAMPO_VALOR:
                            {
                                if (($valor != "") && ($valor != "?"))
                                {
                                    $valor_numerico = $valor;
                                }
                                break;
                            }
                            case CAMPO_INCREMENTO:
                            {
                                if (($incremento != "") && ($incremento != "?"))
                                {
                                    if ($incrementos_tiempo_real_horarios == true)
                                    {
                                        $segundos_incremento = $valor_incremento_sensor[2];
                                        $valor_numerico = $incremento * (3600 / $segundos_incremento);
                                    }
                                    else
                                    {
                                        $valor_numerico = $incremento;
                                    }
                                }
                                break;
                            }
                        }
                        break;
                    }
                    case 4:
                    {
                        $valor_media = $valores_sensor[0];
                        $valor_suma = $valores_sensor[1];
                        $incremento_suma = $valores_sensor[2];
                        $incremento_media = $valores_sensor[3];
                        switch ($campo)
                        {
                            case CAMPO_VALOR_MEDIA:
                            {
                                if (($valor_media != "") && ($valor_media != "?"))
                                {
                                    $valor_numerico = $valor_media;
                                }
                                break;
                            }
                            case CAMPO_VALOR_SUMA:
                            {
                                if (($valor_suma != "") && ($valor_suma != "?"))
                                {
                                    $valor_numerico = $valor_suma;
                                }
                                break;
                            }
                            case CAMPO_INCREMENTO_SUMA:
                            {
                                if (($incremento_suma != "") && ($incremento_suma != "?"))
                                {
                                    $valor_numerico = $incremento_suma;
                                }
                                break;
                            }
                            case CAMPO_INCREMENTO_MEDIA:
                            {
                                if (($incremento_media != "") && ($incremento_media != "?"))
                                {
                                    $valor_numerico = $incremento_media;
                                }
                                break;
                            }
                        }
                        break;
                    }
                }
                break;
            }
        }

        // Se aplica el ratio (si es necesario)
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);
        if ($aplicar_ratio == true)
        {
            switch ($granularidad_sensor)
            {
                case GRANULARIDAD_TIEMPO_REAL:
                {
                    $intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
                    break;
                }
                case GRANULARIDAD_CUARTOHORARIA:
                {
                    $intervalo_valores = INTERVALO_VALORES_CUARTOHORA;
                    break;
                }
                case GRANULARIDAD_HORARIA:
                {
                    $intervalo_valores = INTERVALO_VALORES_HORA;
                    break;
                }
            }
            $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_valores_sensor_base_datos_utc,
                $cadena_fecha_hora_valores_sensor_base_datos_utc,
                $intervalo_valores,
                NULL,
                NULL,
                NULL);
            aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_valores_sensor_base_datos_utc, $valor_numerico);
        }
        return ($valor_numerico);
    }


    // Devuelve el índice de color de fondo de un widget de tipo sensor
    function dame_indice_color_fondo_widget_sensor(
        $id_ratio,
        $id_sensor,
        $cadena_fecha_hora_ultimos_valores_sensor_base_datos_utc,
        $valores_sensor,
        $clase_sensor,
        $incrementos_tiempo_real_horarios,
        $granularidad_sensor,
        $campo,
        $utilizar_colores_fondo,
        $valor_limite_colores_fondo_1,
        $valor_limite_colores_fondo_2)
    {
        $sin_color_fondo_widget = (
           ($utilizar_colores_fondo == VALOR_NO) ||
           (($valores_sensor === NULL) || ($valores_sensor == "")));
        if ($sin_color_fondo_widget == true)
        {
            $indice_color_fondo = ID_NINGUNO;
        }
        else
        {
            if ($campo == CAMPO_TODOS)
            {
                switch ($clase_sensor)
                {
                    case CLASE_SENSOR_LUZ_INTERIOR:
                    {
                        $campo = CAMPO_ILUMINACION;
                        break;
                    }
                    case CLASE_SENSOR_VIENTO:
                    {
                        $campo = CAMPO_VELOCIDAD;
                        break;
                    }
                    case CLASE_SENSOR_ENERGIA_ACTIVA:
                    case CLASE_SENSOR_ENERGIA_REACTIVA:
                    {
                        $campo = CAMPO_INCREMENTO;
                        break;
                    }
                    case CLASE_SENSOR_COMPRA_ENERGIA:
                    {
                        $campo = CAMPO_CONSUMO_ESTIMADO;
                        break;
                    }
                    case CLASE_SENSOR_GAS:
                    {
                        $campo = CAMPO_INCREMENTO;
                        break;
                    }
                    case CLASE_SENSOR_AGUA:
                    {
                        $campo = CAMPO_INCREMENTO;
                        break;
                    }
                    case CLASE_SENSOR_GENERICA:
                    {
                        $campo = CAMPO_INCREMENTO;
                        break;
                    }
                    default:
                    {
                        throw new Exception("Clase de sensor desconocida: '".$clase_sensor."'");
                    }
                }
            }
            $valor_sensor = dame_valor_numerico_sensor_widget(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_ultimos_valores_sensor_base_datos_utc,
                $valores_sensor,
                $clase_sensor,
                $incrementos_tiempo_real_horarios,
                $granularidad_sensor,
                $campo);
            if ($valor_sensor !== NULL)
            {
                $indice_color_fondo = dame_indice_color_fondo_widget(
                    $valor_sensor,
                    $utilizar_colores_fondo,
                    $valor_limite_colores_fondo_1,
                    $valor_limite_colores_fondo_2);
            }
        }
        return ($indice_color_fondo);
    }


    // Devuelve información del valor medio / acumulado del campo del sensor del widget
    function dame_valor_medio_acumulado_campo_sensor_widget(
        $id_ratio,
        $clase_sensor,
        $id_sensor,
        $campo,
        $cadena_fecha_hora_inicio_base_datos_utc,
        $cadena_fecha_hora_fin_base_datos_utc,
        $horario_semanal,
        $exclusion_fechas,
        $inclusion_fechas,
        $parametros_extra_campo)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Intervalo de valores
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor);
        if ($caracteristicas_clase_sensor["procesado_valores"] == false)
        {
            $intervalo_valores = INTERVALO_VALORES_TIEMPO_REAL;
        }
        else
        {
            $intervalo_valores = INTERVALO_VALORES_HORA;
        }

        // Se recuperan los valores del sensor
        $consulta_valores_sensor = dame_consulta_valores_sensor(
            $id_sensor,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            $intervalo_valores,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas,
            $parametros_extra_campo);
        $res_valores_sensor = $bd_datos->ejecuta_consulta($consulta_valores_sensor);
        if ($res_valores_sensor == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_sensor."'");
        }

        // Datos del widget
        if ($res_valores_sensor->dame_numero_filas() == false)
        {
            $res_valor_medio_acumulado = array("sin_valores" => true);
        }
        else
        {
            // Se recupera la información del ratio (si aplica)
            $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);
            if ($aplicar_ratio == true)
            {
                $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                    $id_ratio,
                    $id_sensor,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
                    $intervalo_valores,
                    $horario_semanal,
                    $exclusion_fechas,
                    $inclusion_fechas);
            }

            // Campo sin agrupación de valores
            // (el intervalo de valores es por hora si hay agrupación de valores y no se agrupa en la propia consulta)
            $campo_sin_agrupaciones_valores = elimina_tipo_agrupacion_valores_campo_sensor($campo);

            // Se recorren las filas de valores del sensor
            $cadena_fecha_hora_inicio_valores_base_datos_utc = NULL;
            $cadena_fecha_hora_fin_valores_base_datos_utc = NULL;
            $suma_valores = 0;
            $numero_valores = 0;
            while ($fila_valor_sensor = $res_valores_sensor->dame_siguiente_fila())
            {
                // Fecha y valor
                $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
                $valor = $fila_valor_sensor[$campo_sin_agrupaciones_valores];
                if ($valor !== NULL)
                {
                    $valor = (float) $valor;
                    if ($aplicar_ratio == true)
                    {
                        aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $valor);
                    }
                }
                if ($valor === NULL)
                {
                    continue;
                }

                // Suma de valores y fechas de inicio y fin de valores
                $suma_valores += $valor;
                if ($cadena_fecha_hora_inicio_valores_base_datos_utc === NULL)
                {
                    $cadena_fecha_hora_inicio_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
                }
                $cadena_fecha_hora_fin_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;

                // Número de valores
                $numero_valores += 1;
            }

            // Si no hay valores
            if ($numero_valores == 0)
            {
                $res_valor_medio_acumulado = array("sin_valores" => true);
            }
            else
            {
                // Valor medio o acumulado
                $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);
                switch ($tipo_valores_campo)
                {
                    case TIPO_VALORES_SENSOR_PUNTUALES:
                    {
                        $valor_medio_acumulado = $suma_valores / $numero_valores;
                        break;
                    }
                    case TIPO_VALORES_SENSOR_INCREMENTALES:
                    {
                        $valor_medio_acumulado = $suma_valores;
                        break;
                    }
                }

                // Unidad de medida y número de decimales
                // (Nota: Si el número de decimales es 0 y es media de valores, se pone 2 porque hay una división)
                $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
                if ($aplicar_ratio == true)
                {
                    modifica_unidad_medida_ratio($info_ratio_sensor, $unidad_medida);
                }
                $numero_decimales = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);
                if (($numero_decimales == 0) && ($tipo_valores_campo == TIPO_VALORES_SENSOR_PUNTUALES))
                {
                    $numero_decimales = 2;
                }

                // Resultado
                $res_valor_medio_acumulado = array(
                    "sin_valores" => false,
                    "cadena_fecha_hora_inicio_valores_base_datos_utc" => $cadena_fecha_hora_inicio_valores_base_datos_utc,
                    "cadena_fecha_hora_fin_valores_base_datos_utc" => $cadena_fecha_hora_fin_valores_base_datos_utc,
                    "valor_medio_acumulado" => $valor_medio_acumulado,
                    "unidad_medida" => $unidad_medida,
                    "numero_decimales" => $numero_decimales);
            }
        }
        return ($res_valor_medio_acumulado);
    }


    // Devuelve los últimos valores (añade los valores de campos calculados si es necesario) de un widget
    function dame_ultimos_valores_calculados_widget($clase_sensor, $parametros_extra_clase_sensor, $ultimos_valores)
    {
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_TEMPERATURA:
            {
                $temperatura = explode(SEPARADOR_VALORES_SENSOR, $ultimos_valores)[0];
                if ($parametros_extra_clase_sensor == "")
                {
                    $temperatura_referencia = 0;
                }
                else
                {
                    $temperatura_referencia = $parametros_extra_clase_sensor;
                }
                if ($temperatura - $temperatura_referencia > 0)
                {
                    $grados_hora_calefaccion = 0;
                }
                else
                {
                    $grados_hora_calefaccion = $temperatura_referencia - $temperatura;
                }
                if ($temperatura - $temperatura_referencia < 0)
                {
                    $grados_hora_refrigeracion = 0;
                }
                else
                {
                    $grados_hora_refrigeracion = $temperatura - $temperatura_referencia;
                }
                if ($temperatura - $temperatura_referencia > 0)
                {
                    $grados_dia_calefaccion = 0;
                }
                else
                {
                    $grados_dia_calefaccion = ($temperatura_referencia - $temperatura) / 24;
                }
                if ($temperatura - $temperatura_referencia < 0)
                {
                    $grados_dia_refrigeracion = 0;
                }
                else
                {
                    $grados_dia_refrigeracion = ($temperatura - $temperatura_referencia) / 24;
                }
                $ultimos_valores = implode(SEPARADOR_PARAMETROS_VALORES, array(
                    $temperatura,
                    $grados_hora_calefaccion,
                    $grados_hora_refrigeracion,
                    $grados_dia_calefaccion,
                    $grados_dia_refrigeracion));
                break;
            }
        }
        return ($ultimos_valores);
    }
?>
