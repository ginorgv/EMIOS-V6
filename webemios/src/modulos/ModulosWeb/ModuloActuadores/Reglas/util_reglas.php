<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/herramientas/ClienteMqtt.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Regla.php');


    //
    // Funciones de envío de mensajes MQTT de administración de reglas
    //


    function notifica_operacion_administracion_regla($operacion_administracion, $id_regla)
    {
        $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
        $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
        if ($mqtt->conecta() == true)
        {
            switch ($operacion_administracion)
            {
                // Operaciones de administración
                case OPERACION_ADICION:
                {
                    $mqtt->publica("RULES/RULE/".$id_regla."/ADDED", "", 1);
                    break;
                }
                case OPERACION_MODIFICACION:
                {
                    $mqtt->publica("RULES/RULE/".$id_regla."/MODIFIED", "", 1);
                    break;
                }
                case OPERACION_BORRADO:
                {
                    $mqtt->publica("RULES/RULE/".$id_regla."/DELETED", "", 1);
                    break;
                }
            }
            $mqtt->desconecta();
        }
        else
        {
            throw new Exception("No se ha podido conectar al servidor MQTT");
        }
    }


    //
    // Funciones de listas de reglas
    //


    function dame_lista_tipos_regla($tipo_regla_seleccionado)
    {
        $tipos_regla = Regla::dame_tipos_regla();

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), TIPO_NINGUNO, $tipo_regla_seleccionado);
        foreach ($tipos_regla as $tipo_regla)
        {
            $nombre_tipo_regla = Regla::dame_descripcion_tipo_regla($tipo_regla);
            $lista .= "<option value='".$tipo_regla."'";
            if ($tipo_regla == $tipo_regla_seleccionado)
            {
                $lista .= " selected";
            }
            $lista .= ">".htmlspecialchars($nombre_tipo_regla, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    function dame_lista_modos_activacion_regla($modo_activacion_regla_seleccionado)
    {
        $modos_activacion_regla = Regla::dame_modos_activacion_regla();

        foreach ($modos_activacion_regla as $modo_activacion_regla)
        {
            $nombre_modo_activacion_regla = Regla::dame_descripcion_modo_activacion_regla($modo_activacion_regla);
            $lista .= "<option value='".$modo_activacion_regla."'";
            if ($modo_activacion_regla == $modo_activacion_regla_seleccionado)
            {
                $lista .= " selected";
            }
            $lista .= ">".htmlspecialchars($nombre_modo_activacion_regla, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    //
    // Funciones de controles para el filtrado de reglas
    //


    // Crea una lista desplegable para la selección de la habilitación de una regla
    function dame_control_lista_habilitaciones_regla($id_controles, $etiqueta)
    {
        $idiomas = new Idiomas();

        $control_lista_habilitaciones .= "<div id='etiqueta_habilitacion_regla_".$id_controles."'>".$etiqueta.": "."</div>";
        $control_lista_habilitaciones .= "<select id='habilitacion_regla_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_habilitaciones .= "<option value=".HABILITACION_REGLA_TODAS.">".$idiomas->_("Todas")."</option>";
        $control_lista_habilitaciones .= "<option value=".HABILITACION_REGLA_HABILITADA.">".$idiomas->_("Sí")."</option>";
        $control_lista_habilitaciones .= "<option value=".HABILITACION_REGLA_DESHABILITADA.">".$idiomas->_("No")."</option>";
        $control_lista_habilitaciones .= "</select>";
        return ($control_lista_habilitaciones);
    }


    // Crea una lista desplegable para la selección de la activación de una regla
    function dame_control_lista_activaciones_regla($id_controles, $etiqueta)
    {
        $idiomas = new Idiomas();

        $control_lista_activaciones .= "<div id='etiqueta_activacion_regla_".$id_controles."'>".$etiqueta.": "."</div>";
        $control_lista_activaciones .= "<select id='activacion_regla_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_activaciones .= "<option value=".ACTIVACION_REGLA_TODAS.">".$idiomas->_("Todas")."</option>";
        $control_lista_activaciones .= "<option value=".ACTIVACION_REGLA_ACTIVADA.">".$idiomas->_("Sí")."</option>";
        $control_lista_activaciones .= "<option value=".ACTIVACION_REGLA_DESACTIVADA.">".$idiomas->_("No")."</option>";
        $control_lista_activaciones .= "</select>";
        return ($control_lista_activaciones);
    }


    //
    // Funciones de obtención de información de reglas
    //


    function dame_fila_regla($id_regla)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_regla = "
            SELECT *
            FROM reglas
            WHERE
                id = '".$bd_red->_($id_regla)."'";
        $res_regla = $bd_red->ejecuta_consulta($consulta_regla);
        if (($res_regla == false) || ($res_regla->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_regla."'");
        }
        $fila_regla = $res_regla->dame_siguiente_fila();
        return ($fila_regla);
    }


    function dame_nombre_regla($id_regla)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_regla = "
            SELECT nombre
            FROM reglas
            WHERE
                id = '".$bd_red->_($id_regla)."'";
        $res_regla = $bd_red->ejecuta_consulta($consulta_regla);
        if (($res_regla == false) || ($res_regla->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_regla."'");
        }
        $fila_regla = $res_regla->dame_siguiente_fila();
        $nombre_regla = $fila_regla["nombre"];
        return ($nombre_regla);
    }
?>
