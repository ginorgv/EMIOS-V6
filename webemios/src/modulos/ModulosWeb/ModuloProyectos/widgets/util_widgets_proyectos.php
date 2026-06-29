<?php
	include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_informes_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloProyectos/Proyectos/Proyecto.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    // Constantes

    // Indices de parámetros de tipo de widgets
	define("INDICE_PARAMETRO_TIPO_WIDGET_SIMULADOR_LINEA_BASE_ID_LINEA_BASE", 0);
    define("INDICE_PARAMETRO_TIPO_WIDGET_SIMULADOR_LINEA_BASE_PERIODO_TIEMPO", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_SIMULADOR_LINEA_BASE_INICIAR_COMIENZO_PERIODO_TIEMPO", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_SIMULADOR_LINEA_BASE_FECHA_INICIO_PERIODO_TIEMPO", 3);

    define("INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_PROYECTO_ID_PROYECTO", 0);
    define("INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_PROYECTO_PERIODO_TIEMPO", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_PROYECTO_INICIAR_COMIENZO_PERIODO_TIEMPO", 2);
    define("INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_PROYECTO_FECHA_INICIO_PERIODO_TIEMPO", 3);
    define("INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_PROYECTO_ICONO", 4);


    // Devuelve los datos de un widget de tipo 'Simulador de línea base'
    function dame_datos_widget_simulador_linea_base(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        // Parámetros de tipo de widget
        $id_linea_base = $parametros_tipo["id_linea_base"];
        $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];

        // Fechas de inicio y fin (local)
        $fechas_horas_inicio_fin = dame_fechas_inicio_fin_periodo_tiempo_widget(
            $periodo_tiempo,
            $iniciar_comienzo_periodo_tiempo,
            $cadena_fecha_inicio_periodo_tiempo_base_datos_local);
        $fecha_hora_inicio_local = $fechas_horas_inicio_fin["fecha_hora_inicio_local"];
        $fecha_hora_fin_local = $fechas_horas_inicio_fin["fecha_hora_fin_local"];

        // Conversión a cadena
        $cadena_fecha_hora_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_hora_local"]);
        $cadena_fecha_hora_fin_local_local = convierte_fecha_a_cadena($fecha_hora_fin_local, $_SESSION["formato_fecha_hora_local"]);

        // Se recuperan los datos de simulación de línea base
        $parametros_simulacion_linea_base = array(
            "id_linea_base" => $id_linea_base,
            "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
            "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
            "minutos_desfase_utc" => $minutos_desfase_utc);
        $res_simulacion_linea_base = dame_simulacion_linea_base($parametros_simulacion_linea_base);
        if ($res_simulacion_linea_base["res"] == "OK")
        {
            // Cadenas con las horas inicial y final de la consulta
            $res_simulacion_linea_base["fecha_hora_inicio_consulta"] = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_JQPLOT);
            $res_simulacion_linea_base["fecha_hora_fin_consulta"] = convierte_formato_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_JQPLOT);

            // Formatos de fechas
            $cadena_fecha_hora_inicio_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_hora_local_sin_segundos"]);
            $cadena_fecha_hora_fin_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_hora_local_sin_segundos"]);

            // Cadena de horas de valores
            $html_cadena_horas_valores = "";
            $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-valores'>(".$cadena_fecha_hora_inicio_local_local."),</span>";
            $html_cadena_horas_valores .= "<span class='fecha-hora-widgets-valores'>(".$cadena_fecha_hora_fin_local_local.")</span>";

            // Parámetros 'extra' para el dibujado del widget
            $res_simulacion_linea_base["html_cadena_horas_valores"] = $html_cadena_horas_valores;
        }

        // Datos del widget
        $datos_widget_simulador_linea_base = $res_simulacion_linea_base;

        // Se devuelven los datos del widget
        return ($datos_widget_simulador_linea_base);
    }


    // Devuelve los datos de un widget de tipo 'Información de proyecto'
    function dame_datos_widget_informacion_proyecto(
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        // Parámetros de tipo de widget
        $id_proyecto = $parametros_tipo["id_proyecto"];
        $periodo_tiempo = $parametros_tipo["periodo_tiempo"];
        $iniciar_comienzo_periodo_tiempo = $parametros_tipo["iniciar_comienzo_periodo_tiempo"];
        $cadena_fecha_inicio_periodo_tiempo_base_datos_local = $parametros_tipo["fecha_inicio_periodo_tiempo"];
        $icono = $parametros_tipo["icono"];

        // - Si no hay periodo de tiempo se recupera la información actual de la tabla de proyectos,
        // - Si hay periodo de tiempo hay que calcular el avance y el estado del proyecto
        if ($periodo_tiempo == ID_NINGUNO)
        {
            // Se recupera la fila del proyecto
            $fila_proyecto = dame_fila_proyecto($id_proyecto);

            // Fecha y hora de inicio (de proyecto)
            $cadena_fecha_hora_inicio_base_datos_local = $fila_proyecto["fecha_inicio"]." 00:00:00";
            $cadena_fecha_hora_inicio_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);

            // Estado de avance, estado y hora de cálculo de último avance y fin de valores del avance
            $estado_avance_proyecto = $fila_proyecto["estado_avance"];
            $estado_proyecto = $fila_proyecto["estado"];
            $cadena_hora_ultimo_calculo_avance_base_datos_local = $fila_proyecto["hora_ultimo_calculo_avance"];
            if ($cadena_hora_ultimo_calculo_avance_base_datos_local !== NULL)
            {
                $cadena_hora_ultimo_calculo_avance_local_local = convierte_formato_fecha($cadena_hora_ultimo_calculo_avance_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
            }
            else
            {
                $cadena_hora_ultimo_calculo_avance_local_local = NULL;
            }
            $cadena_hora_fin_valores_avance_base_datos_local = $fila_proyecto["hora_fin_valores_avance"];
            if ($cadena_hora_fin_valores_avance_base_datos_local !== NULL)
            {
                $cadena_hora_fin_valores_avance_local_local = convierte_formato_fecha($cadena_hora_fin_valores_avance_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
            }
            else
            {
                $cadena_hora_fin_valores_avance_local_local = NULL;
            }

            // Descripción de avance y estado del proyecto
            $descripcion_avance_proyecto = Proyecto::dame_descripcion_avance_proyecto(
                $fila_proyecto,
                $fila_proyecto["valor_real_avance"],
                $fila_proyecto["valor_simulado_avance"],
                $fila_proyecto["porcentaje_finalizacion"],
                "texto-pequenyo-widget-valor-digital-sensor");
            $descripcion_estado_proyecto = Proyecto::dame_descripcion_estado_proyecto_porcentaje_finalizacion(
                $fila_proyecto["estado"],
                $fila_proyecto["porcentaje_finalizacion"],
                "texto-pequenyo-widget-valor-digital-sensor");
        }
        else
        {
            // Fechas de inicio y fin (local)
            $fechas_horas_inicio_fin = dame_fechas_inicio_fin_periodo_tiempo_widget(
                $periodo_tiempo,
                $iniciar_comienzo_periodo_tiempo,
                $cadena_fecha_inicio_periodo_tiempo_base_datos_local);
            $fecha_hora_inicio_local = $fechas_horas_inicio_fin["fecha_hora_inicio_local"];
            $fecha_hora_fin_local = $fechas_horas_inicio_fin["fecha_hora_fin_local"];

            // Las fechas de inicio y fin de información de proyectos son sin horas (días enteros)
            $fecha_hora_inicio_local->setTime(0, 0, 0);
            $fecha_hora_fin_local->setTime(23, 59, 59);

            // La fecha de fin es de un día anterior
            // (porque los datos del proyecto están calculados hasta el día anterior)
            $fecha_hora_fin_local->modify('-1 day');

            // Conversión a cadena
            $cadena_fecha_hora_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_hora_local"]);
            $cadena_fecha_hora_fin_local_local = convierte_fecha_a_cadena($fecha_hora_fin_local, $_SESSION["formato_fecha_hora_local"]);

            // Se recuperan los datos de información de proyecto
            $parametros_informacion_proyecto = array(
                "id_proyecto" => $id_proyecto,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
                "clase_unidad_medida" =>  "texto-pequenyo-widget-valor-digital-sensor");
            $res_informacion_proyecto = dame_informacion_estado_proyecto($parametros_informacion_proyecto);
            if ($res_informacion_proyecto["res"] == "OK")
            {
                // Información para el dibujado del widget
                if ($res_informacion_proyecto["hay_datos"] == true)
                {
                    $estado_avance_proyecto = $res_informacion_proyecto["estado_avance_proyecto"];
                    $estado_proyecto = $res_informacion_proyecto["estado_proyecto"];
                    $cadena_hora_ultimo_calculo_avance_local_local = $res_informacion_proyecto["cadena_hora_ultimos_valores_avance_local_local"];
                    $cadena_hora_fin_valores_avance_local_local = $res_informacion_proyecto["cadena_hora_fin_valores_avance_local_local"];
                    $descripcion_avance_proyecto = $res_informacion_proyecto["descripcion_avance_proyecto"];
                    $descripcion_estado_proyecto = $res_informacion_proyecto["descripcion_estado_proyecto"];
                }
                else
                {
                    $estado_proyecto = ESTADO_PROYECTO_NINGUNO;
                }
            }
            else
            {
                // Si hay error se devuelve el resultado y el mensaje del error
                return ($res_informacion_proyecto);
            }
        }

        // Nota: Tamaño de la fuente dependiente de la configuración de la cuadrícula de widgets
        $clase_tamanyo_fuente_avance_proyecto_widget = "tamanyo-fuente-avance-proyecto-widget-informacion-proyecto-columnas-".$numero_columnas_fila_widget_clase_contenido_widget;
        $clase_tamanyo_fuente_estado_proyecto_widget = "tamanyo-fuente-estado-proyecto-widget-informacion-proyecto-columnas-".$numero_columnas_fila_widget_clase_contenido_widget;

        // Datos del widget
        $datos_widget_informacion_proyecto = array(
            "estado_avance_proyecto" => $estado_avance_proyecto,
            "estado_proyecto" => $estado_proyecto,
            "cadena_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
            "cadena_hora_ultimo_calculo_avance" => $cadena_hora_ultimo_calculo_avance_local_local,
            "cadena_hora_fin_valores_avance" => $cadena_hora_fin_valores_avance_local_local,
            "descripcion_avance_proyecto" => $descripcion_avance_proyecto,
            "descripcion_estado_proyecto" => $descripcion_estado_proyecto,
            "clase_tamanyo_fuente_avance_proyecto_widget" => $clase_tamanyo_fuente_avance_proyecto_widget,
            "clase_tamanyo_fuente_estado_proyecto_widget" => $clase_tamanyo_fuente_estado_proyecto_widget);

        // Icono
        $datos_widget_informacion_proyecto["icono"] = $icono;

        // Se devuelven los datos del widget
        $datos_widget_informacion_proyecto["res"] = "OK";
        return ($datos_widget_informacion_proyecto);
    }
?>

