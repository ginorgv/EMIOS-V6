<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/util_informes_informes_personalizados.php');


    // Constantes

    // Indices de parámetros de tipo de informes automáticos
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_ESTUDIO_GENERAL_MEDICION", 0);
	define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_ESTUDIO_GENERAL_ID_RATIO", 1);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_ESTUDIO_GENERAL_ID_SENSOR", 2);
    define("INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_ESTUDIO_GENERAL_APARTADOS", 3);


    //
    // Funciones que devuelven el código HTML de los parámetros de los informes automáticos
    //


    // Parámetros del informe de estudio general
    function dame_html_parametros_tipo_informe_automatico_smartmeter_estudio_general($cadena_parametros_tipo, $parametros_tipo_json)
    {
        $idiomas = new Idiomas();

        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
        $medicion = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_ESTUDIO_GENERAL_MEDICION];
        $id_ratio = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_ESTUDIO_GENERAL_ID_RATIO];
        $id_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_ESTUDIO_GENERAL_ID_SENSOR];
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $apartados = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_SMARTMETER_ESTUDIO_GENERAL_APARTADOS]);

        $html = "";
        $html .= "<i class='icon-info-sign color-azul'></i> ";
        $html .= $idiomas->_("Parámetros de tipo").":";
        $html .= "<ul>";
        $html .= "<li>".$idiomas->_("Medición").": ".dame_descripcion_medicion($medicion)."</li>";
        if ($id_ratio != ID_NINGUNO)
        {
            $info_ratio = dame_info_ratio($id_ratio);
            $nombre_ratio = $info_ratio["nombre"];
            $html .= "<li>".$idiomas->_("Ratio").": ".$nombre_ratio."</li>";
        }
        $html .= "<li>".$idiomas->_("Sensor").": ".$nombre_sensor."</li>";
        $html .= "<li>".$idiomas->_("Apartados").": ";
        $lista_descripciones_apartados = "<ul>";
        foreach ($apartados AS $apartado)
        {
            $lista_descripciones_apartados .= "<li>".dame_descripcion_apartado_estudio_general($medicion, $apartado)."</li>";
        }
        $lista_descripciones_apartados .= "</ul>";
        $html .= $lista_descripciones_apartados;
        $html .= "</li>";
        $html_textos = "";
        if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_INTRODUCCION_ELECTRICIDAD_ESPANYA, $apartados))
        {
            $parametros_json = json_decode_caracteres_especiales($parametros_tipo_json);
            $texto_introduccion = $parametros_json["texto_introduccion"];
            $html_textos .= "
                <ul>
                    <li>".$idiomas->_("Texto de introducción").":"."</li>";
            $html_textos .= "
                    <div class='contenedor-textarea-detalle-tabla-datos'>
                        <textarea class='area-entrada-texto-detalles-informe' rows='1' disabled>".htmlspecialchars($texto_introduccion, ENT_QUOTES)."</textarea>
                    </div>
                </ul>";
        }
        if ($html_textos != "")
        {
            $html .= "<li>".$idiomas->_("Textos").": ";
            $html .= $html_textos;
            $html .= "</li>";
        }
        $html .= "</ul>";

        return ($html);
    }
?>
