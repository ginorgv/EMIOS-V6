<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_eventos.php');


    // Constantes

    // Indices de parámetros de tipo de informes automáticos
	define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_CLASE_SENSOR", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_ORIGEN_EVENTO", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_ID_ORIGEN_EVENTO", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_GRANULARIDAD_EVENTO", 3);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_IDS_EVENTOS", 4);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_CAMPO", 5);


    //
    // Funciones que devuelven el código HTML de los parámetros de los informes automáticos
    //


    // Parámetros del informe de activaciones de eventos
    function dame_html_parametros_tipo_informe_automatico_sensores_activaciones_eventos($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_CLASE_SENSOR];
        $origen_evento = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_ORIGEN_EVENTO];
        $id_origen_evento = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_ID_ORIGEN_EVENTO];
        $granularidad_evento = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_GRANULARIDAD_EVENTO];
        $ids_eventos = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_IDS_EVENTOS]);
        $campo = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SENSORES_ACTIVACIONES_EVENTOS_CAMPO];

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Clase de sensor").": ".NodoSensor::dame_descripcion_clase_sensor($clase_sensor)."</li>";
        $html .= "<li>".$idiomas->_("Tipo de origen").": ".Evento::dame_descripcion_origen_evento($origen_evento)."</li>";
        switch ($origen_evento)
        {
            case ORIGEN_EVENTO_SENSOR:
            {
                $nombre_origen_evento = dame_nombre_sensor($id_origen_evento);
                break;
            }
            case ORIGEN_EVENTO_GRUPO_SENSORES:
            {
                $nombre_origen_evento = dame_nombre_grupo_sensores($id_origen_evento);
                break;
            }
        }
        $html .= "<li>".$idiomas->_("Origen").": ".$nombre_origen_evento."</li>";
        $html .= "<li>".$idiomas->_("Granularidad").": ".dame_descripcion_granularidad($granularidad_evento)."</li>";
        $html .= "<li>".$idiomas->_("Eventos").":";
        $nombres_eventos = dame_nombres_eventos($ids_eventos);
        $lista_nombres_eventos = "<ul>";
        foreach ($nombres_eventos AS $nombre_evento)
        {
            $lista_nombres_eventos .= "<li>".htmlspecialchars($nombre_evento, ENT_QUOTES)."</li>";
        }
        $lista_nombres_eventos .= "</ul>";
        $html .= $lista_nombres_eventos;
        $html .= "</li>";
        $html .= "<li>".$idiomas->_("Campo").": ".dame_descripcion_campo_clase_sensor($clase_sensor, $campo)."</li>";

        return ($html);
    }
?>