<?php
	include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/NodoActuador.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/util_acciones.php');


    // Constantes

    // Indices de parámetros de tipo de widgets
    define("INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_ACTUADOR_CLASE_ACTUADOR", 0);
	define("INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_ACTUADOR_ID_ACTUADOR", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_ACTUADOR_ICONO", 2);

    define("INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES_CLASE_ACTUADOR", 0);
	define("INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES_ID_GRUPO_ACTUADORES", 1);
    define("INDICE_PARAMETRO_TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES_ICONO", 2);


    // Devuelve los datos de un widget de tipo 'Información de actuador' o 'Informacion de grupo de actuadores'
    function dame_datos_widget_informacion_actuador_grupo_actuadores(
        $tipo_widget,
        $id_widget,
        $parametros_tipo,
        $numero_columnas_fila_widget_clase_contenido_widget,
        $minutos_desfase_utc)
    {
        $idiomas = new Idiomas();

        // Parámetros de tipo de widget
        switch ($tipo_widget)
        {
            case TIPO_WIDGET_INFORMACION_ACTUADOR:
            {
                $clase_actuador = $parametros_tipo["clase_actuador"];
                $id_actuador = $parametros_tipo["id_actuador"];

                // Se recupera la fila del actuador
                $fila_actuador_grupo_actuadores = dame_fila_actuador($id_actuador);
                break;
            }
            case TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES:
            {
                $clase_actuador = $parametros_tipo["clase_actuador"];
                $id_grupo_actuadores = $parametros_tipo["id_grupo_actuadores"];

                // Se recupera la fila del grupo de actuadores
                $fila_actuador_grupo_actuadores = dame_fila_grupo_actuadores($id_grupo_actuadores);
                break;
            }
        }
        $icono = $parametros_tipo["icono"];

        // Contenido y fecha de última acción
        $contenido_ultima_accion = $fila_actuador_grupo_actuadores["contenido_ultima_accion"];
        $cadena_hora_ultima_accion_base_datos_utc = $fila_actuador_grupo_actuadores["hora_ultima_accion"];

        // Se recuperan la imagen y el nombre del estado de la acción del grupo de actuadores (si tiene acciones predefinidas)
        if ($contenido_ultima_accion !== NULL)
        {
            // Imagen de última acción
            $sin_ultima_accion = false;
            $caracteristicas_clase_actuador = NodoActuador::dame_caracteristicas_clase_actuador($clase_actuador);
            $clase_acciones_predefinidas = $caracteristicas_clase_actuador["acciones_predefinidas"];
            $imagen_ultima_accion_estandar = NodoActuador::dame_imagen_estado_actual_clase(
                $clase_actuador,
                $contenido_ultima_accion,
                $cadena_hora_ultima_accion_base_datos_utc);

            // Nota: Se utilizan los iconos 'grandes' (para que no se vean pixelados los '.png')
            $imagen_ultima_accion = str_replace(".png", "-grande.png", $imagen_ultima_accion_estandar);
            if ($clase_acciones_predefinidas == true)
            {
                $nombre_estado_ultima_accion = dame_nombre_estado_accion_predefinida($clase_actuador, $contenido_ultima_accion);
            }
            else
            {
                $nombre_estado_ultima_accion = "";
            }

            // Hora de última acción
            $zona_horaria = dame_zona_horaria_local();
            $cadena_hora_ultima_accion_loca_utc = convierte_formato_fecha($cadena_hora_ultima_accion_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
            $cadena_hora_ultima_accion_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_ultima_accion_loca_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_hora_ultima_accion = "(".$cadena_hora_ultima_accion_local_local.")";

            // Icono de estado de última ejecución de acción
            switch ($tipo_widget)
            {
                case TIPO_WIDGET_INFORMACION_ACTUADOR:
                {
                    $icono_estado_ultima_ejecucion_accion = "";
                    $estado_ejecucion_ultima_accion = $fila_actuador_grupo_actuadores["estado_ejecucion_ultima_accion"];
                    switch ($estado_ejecucion_ultima_accion)
                    {
                        case ESTADO_EJECUCION_ACCION_NO_CONECTADO:
                        {
                            $icono_estado_ultima_ejecucion_accion .= "<i class='icon-remove-sign color-gris-claro'></i>";
                            break;
                        }
                        case ESTADO_EJECUCION_ACCION_EN_EJECUCION:
                        {
                            $icono_estado_ultima_ejecucion_accion .= "<i class='icon-spinner color-gris'></i>";
                            break;
                        }
                        case ESTADO_EJECUCION_ACCION_ERROR:
                        {
                            $icono_estado_ultima_ejecucion_accion .= "<i class='icon-warning-sign color-rojo'></i>";
                            break;
                        }
                    }
                    break;
                }
                case TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES:
                {
                    $icono_estado_ultima_ejecucion_accion = "";
                    break;
                }
            }
        }
        else
        {
            $sin_ultima_accion = true;
            $imagen_ultima_accion = NULL;
            $nombre_estado_ultima_accion = NULL;
        }

        // Nota: Tamaño de la fuente dependiente de la configuración de la cuadrícula de widgets
        $clase_tamanyo_fuente_imagen_accion_widget = "tamanyo-fuente-imagen-accion-widget-informacion-actuador-columnas-".$numero_columnas_fila_widget_clase_contenido_widget;
        $clase_tamanyo_fuente_nombre_estado_widget = "tamanyo-fuente-nombre-estado-widget-informacion-actuador-columnas-".$numero_columnas_fila_widget_clase_contenido_widget;

        // Botón de envío de acción
        $envio_acciones_actuadores = NodoActuador::dame_envio_acciones_actuadores();
        $mostrar_boton_envio_accion = ($envio_acciones_actuadores == true);
        if ($mostrar_boton_envio_accion == true)
        {
            $fila_widget = dame_fila_widget($id_widget);
            $id_pestanya_widgets = $fila_widget["pestanya"];
            switch ($tipo_widget)
            {
                case TIPO_WIDGET_INFORMACION_ACTUADOR:
                {
                    $html_boton_envio_accion = "
                        <div class='contenedor-botones-widget'>
                            <button id='boton_mostrar_ventana_envio_accion__".$id_actuador."__".$id_pestanya_widgets."'
                                class='btn-mini btn btn-success boton-widget boton_actuadores_mostrar_ventana_envio_accion_actuador_widget'>".
                                $idiomas->_("Enviar acción")."
                            </button>
                        </div>";
                    break;
                }
                case TIPO_WIDGET_INFORMACION_GRUPO_ACTUADORES:
                {
                    $html_boton_envio_accion = "
                        <div class='contenedor-botones-widget'>
                            <button id='boton_mostrar_ventana_envio_accion__".$id_grupo_actuadores."__".$id_pestanya_widgets."'
                                class='btn-mini btn btn-success boton-widget boton_actuadores_mostrar_ventana_envio_accion_grupo_actuadores_widget'>".
                                $idiomas->_("Enviar acción")."
                            </button>
                        </div>";
                    break;
                }
            }
        }
        else
        {
            $html_boton_envio_accion = "";
        }

        // Datos del widget
        $datos_widget_informacion_actuador_grupo_actuadores = array(
            "res" => "OK",
            "sin_ultima_accion" => $sin_ultima_accion,
            "imagen_ultima_accion" => $imagen_ultima_accion,
            "nombre_estado_ultima_accion" => $nombre_estado_ultima_accion,
            "cadena_hora_ultima_accion" => $cadena_hora_ultima_accion,
            "icono_estado_ultima_ejecucion_accion" => $icono_estado_ultima_ejecucion_accion,
            "clase_tamanyo_fuente_imagen_accion_widget" => $clase_tamanyo_fuente_imagen_accion_widget,
            "clase_tamanyo_fuente_nombre_estado_widget" => $clase_tamanyo_fuente_nombre_estado_widget,
            "html_boton_envio_accion" => $html_boton_envio_accion);

        // Icono
        $datos_widget_informacion_actuador_grupo_actuadores["icono"] = $icono;

        // Se devuelven los datos del widget
        return ($datos_widget_informacion_actuador_grupo_actuadores);
    }
?>

