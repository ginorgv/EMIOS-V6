<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    // Constantes

    // Indices de parámetros de tipo de informes automáticos
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_SIMULADOR_FACTURA_MEDICION", 0);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_SIMULADOR_FACTURA_ID_SENSOR", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_SIMULADOR_FACTURA_ID_TARIFA", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_SIMULADOR_FACTURA_IDS_SENSORES_REPARTO_COSTES", 3);


    //
    // Funciones que devuelven el código HTML de los parámetros de los informes automáticos
    //


    // Parámetros del informe de simulación de factura
    function dame_html_parametros_tipo_informe_automatico_smartmeter_simulador_factura($cadena_parametros_tipo)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $medicion = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_SIMULADOR_FACTURA_MEDICION];
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_SIMULADOR_FACTURA_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $id_tarifa = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_SIMULADOR_FACTURA_ID_TARIFA];
        if ($id_tarifa == ID_NINGUNO)
        {
            $caracteristicas_tarifas = dame_caracteristicas_tarifas_pais_medicion($medicion);
            if ($caracteristicas_tarifas["curva_coste"] == true)
            {
                $nombre_tarifa = $idiomas->_("Tarifa vigente según fechas");
            }
            else
            {
                $nombre_tarifa = $idiomas->_("Actual");
            }
        }
        else
        {
            $tabla_tarifas = dame_nombre_tabla_tarifas($medicion);
            $nombre_tarifa = dame_nombre_tarifa($tabla_tarifas, $id_tarifa);
        }
        $cadena_ids_sensores_reparto_costes = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_SIMULADOR_FACTURA_IDS_SENSORES_REPARTO_COSTES];
        if ($cadena_ids_sensores_reparto_costes == "")
        {
            $ids_sensores_reparto_costes = array();
        }
        else
        {
            $ids_sensores_reparto_costes = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_reparto_costes);
        }
        $nombres_sensores_reparto_costes = dame_nombres_sensores($ids_sensores_reparto_costes);

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Medición").": ".dame_descripcion_medicion($medicion)."</li>";

        $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
        $html .= "<li>".$idiomas->_("Tarifa").": ".$nombre_tarifa."</li>";
        if (count($ids_sensores_reparto_costes) > 0)
        {
            $html .= "<li>".$idiomas->_("Sensores de reparto de costes").":";
            $lista_nombres_sensores_reparto_costes = "<ul>";
            foreach ($nombres_sensores_reparto_costes AS $nombre_sensor_reparto_costes)
            {
                $lista_nombres_sensores_reparto_costes .= "<li>".htmlspecialchars($nombre_sensor_reparto_costes, ENT_QUOTES)."</li>";
            }
            $lista_nombres_sensores_reparto_costes .= "</ul>";
            $html .= $lista_nombres_sensores_reparto_costes;
        }
        $html .= "</ul>";

        return ($html);
    }
?>
