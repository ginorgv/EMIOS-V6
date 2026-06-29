<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/NodoSensor.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');


    // Comprueba parámetros de la sesión
    function comprueba_parametros_sesion()
    {
        $parametros_sesion = $_POST["parametros_sesion"];

        // Se comprueba que la red sea la misma que en la sesión
        $sesion_correcta = true;
        if (array_key_exists("id_red_actual", $parametros_sesion) == true)
        {
            $id_red_actual = $parametros_sesion["id_red_actual"];
            if ($_SESSION["id_red"] != $id_red_actual)
            {
                $idiomas = new Idiomas();

                $nombre_red_actual = dame_nombre_red($_SESSION["id_red"]);
                $msg = $idiomas->_("Red incorrecta")." (".$idiomas->_("red actual").": ".$nombre_red_actual.")";

                $sesion_correcta = false;
                $respuesta_script = json_encode(array(
                    "res" => "PARAMETROS_SESION_INCORRECTOS",
                    "msg" => $msg));
            }
        }

        return (array(
            "sesion_correcta" => $sesion_correcta,
            "respuesta_script" => $respuesta_script
        ));
    }


    // Devuelve la información 'extra' local (de la sesión)
    function dame_informacion_extra_local()
    {
        // Formatos de fecha local (JavaScript)
        $formato_fecha_local = $_SESSION["formato_fecha_local"] ?? FORMATO_FECHA_LOCAL_DIA_MES_ANYO;
        switch ($formato_fecha_local)
        {
            case FORMATO_FECHA_LOCAL_DIA_MES_ANYO:
            {
                $formato_fecha_local_jqplot = FORMATO_FECHA_LOCAL_DIA_MES_ANYO_JQPLOT_JAVASCRIPT;
                $formato_fecha_local_jquery_ui = FORMATO_FECHA_LOCAL_DIA_MES_ANYO_JQUERY_UI_JAVASCRIPT;
                $formato_dia_anyo_local_jqplot = FORMATO_DIA_ANYO_LOCAL_DIA_MES_JQPLOT_JAVASCRIPT;
                $formato_dia_anyo_local_jquery_ui = FORMATO_DIA_ANYO_LOCAL_DIA_MES_JQUERY_UI_JAVASCRIPT;
                break;
            }
            case FORMATO_FECHA_LOCAL_MES_DIA_ANYO:
            {
                $formato_fecha_local_jqplot = FORMATO_FECHA_LOCAL_MES_DIA_ANYO_JQPLOT_JAVASCRIPT;
                $formato_fecha_local_jquery_ui = FORMATO_FECHA_LOCAL_MES_DIA_ANYO_JQUERY_UI_JAVASCRIPT;
                $formato_dia_anyo_local_jqplot = FORMATO_DIA_ANYO_LOCAL_MES_DIA_JQPLOT_JAVASCRIPT;
                $formato_dia_anyo_local_jquery_ui = FORMATO_DIA_ANYO_LOCAL_MES_DIA_JQUERY_UI_JAVASCRIPT;
                break;
            }
            case FORMATO_FECHA_LOCAL_ANYO_MES_DIA:
            {
                $formato_fecha_local_jqplot = FORMATO_FECHA_LOCAL_ANYO_MES_DIA_JQPLOT_JAVASCRIPT;
                $formato_fecha_local_jquery_ui = FORMATO_FECHA_LOCAL_ANYO_MES_DIA_JQUERY_UI_JAVASCRIPT;
                $formato_dia_anyo_local_jqplot = FORMATO_DIA_ANYO_LOCAL_MES_DIA_JQPLOT_JAVASCRIPT;
                $formato_dia_anyo_local_jquery_ui = FORMATO_DIA_ANYO_LOCAL_MES_DIA_JQUERY_UI_JAVASCRIPT;
                break;
            }
            default:
            {
                throw new Exception("Formato de fecha local desconocido: '".$formato_fecha_local."'");
            }
        }

        // Unidades de medida
        $moneda = $_SESSION["moneda"] ?? 'EUR';
        $unidad_medida_temperatura = $_SESSION["unidad_medida_temperatura"] ?? 'C';
        $unidad_medida_velocidad = $_SESSION["unidad_medida_velocidad"] ?? 'km/h';

        // Paises de tarifas
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"] ?? 'ESPANYA';
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"] ?? 'ESPANYA';
        $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"] ?? 'ESPANYA';

        // Medición por defecto
        $medicion_defecto = $_SESSION["medicion_defecto"] ?? 'electricidad';

        // Información 'extra' local
        $informacion_extra_local = array();
        $informacion_extra_local["formato_fecha_local_jqplot"] = $formato_fecha_local_jqplot;
        $informacion_extra_local["formato_fecha_local_jquery_ui"] = $formato_fecha_local_jquery_ui;
        $informacion_extra_local["formato_dia_anyo_local_jqplot"] = $formato_dia_anyo_local_jqplot;
        $informacion_extra_local["formato_dia_anyo_local_jquery_ui"] = $formato_dia_anyo_local_jquery_ui;
        $informacion_extra_local["moneda"] = $moneda;
        $informacion_extra_local["unidad_medida_temperatura"] = $unidad_medida_temperatura;
        $informacion_extra_local["unidad_medida_velocidad"] = $unidad_medida_velocidad;
        $informacion_extra_local["pais_tarifas_electricas"] = $pais_tarifas_electricas;
        $informacion_extra_local["pais_tarifas_gas"] = $pais_tarifas_gas;
        $informacion_extra_local["pais_tarifas_agua"] = $pais_tarifas_agua;
        $informacion_extra_local["medicion_defecto"] = $medicion_defecto;
        return ($informacion_extra_local);
    }


    // Devuelve la información 'extra' de preferencias actuales (de la sesión)
    function dame_informacion_extra_preferencias_actuales()
    {
        // Información extra de preferencias
        if (isset($_SESSION["pantalla_completa_inicio"]))
        {
            $pantalla_completa_inicio = $_SESSION["pantalla_completa_inicio"];
        }
        else
        {
            $pantalla_completa_inicio = false;
        }

        $exportacion_valores_sensores = NodoSensor::dame_exportacion_sensores();
        $administracion_comentarios_sensores = NodoSensor::dame_administracion_comentarios_sensores();
        $administracion_comentarios_actuadores = NodoActuador::dame_administracion_comentarios_actuadores();

        // Información 'extra' de preferencias actuales
        $informacion_extra_preferencias_actuales = array();
        $informacion_extra_preferencias_actuales["titulo_web"] = $_SESSION["titulo_web"];
        $informacion_extra_preferencias_actuales["paleta_colores_graficas"] = $_SESSION["paleta_colores_graficas"];
        $informacion_extra_preferencias_actuales["pantalla_completa_inicio"] = $pantalla_completa_inicio;
        $informacion_extra_preferencias_actuales["exportacion_valores_sensores"] = $exportacion_valores_sensores;
        $informacion_extra_preferencias_actuales["administracion_comentarios_sensores"] = $administracion_comentarios_sensores;
        $informacion_extra_preferencias_actuales["administracion_comentarios_actuadores"] = $administracion_comentarios_actuadores;
        return ($informacion_extra_preferencias_actuales);
    }
?>