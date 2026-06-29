<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_hijas_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/Proyecto.php');


    //
    // Funciones de identificadores de proyectos
    //


    // Devuelve los identificadores de los proyectos (de la red actual)
    function dame_ids_proyectos()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_proyectos = "
            SELECT id
            FROM proyectos
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        $res_proyectos = $bd_red->ejecuta_consulta($consulta_proyectos);
        if ($res_proyectos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_proyectos."'");
        }
        $ids_proyectos = array();
        while ($fila_proyecto = $res_proyectos->dame_siguiente_fila())
        {
            array_push($ids_proyectos, $fila_proyecto["id"]);
        }
        return ($ids_proyectos);
    }


    //
    // Funciones de permisos de proyectos
    //


    // Devuelve si se muestran todos los proyectos
    function dame_mostrar_todos_proyectos()
    {
        $mostrar_todos_proyectos = dame_mostrar_todos_sensores();
        return ($mostrar_todos_proyectos);
    }


    //
    // Funciones de listas de proyectos
    //


    // Devuelve la lista de proyectos
    function dame_lista_proyectos($id_proyecto_seleccionado)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_proyectos = "
            SELECT
                id,
                nombre
            FROM proyectos
            WHERE
                red = '".$_SESSION["id_red"]."'
            ORDER BY nombre ASC";
        $res_proyectos = $bd_red->ejecuta_consulta($consulta_proyectos);
        if ($res_proyectos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_proyectos."'");
        }

        // Identificadores de proyectos del usuario actual
        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        if ($mostrar_todos_sensores == false)
        {
            $ids_proyectos_usuario = Proyecto::dame_ids_proyectos_usuario_actual();
        }

        $lista_proyectos = "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        while ($fila_proyecto = $res_proyectos->dame_siguiente_fila())
        {
            $anyadir_proyecto = true;
            if ($mostrar_todos_sensores == false)
            {
                // Nota: En algunas listas el proyecto seleccionado puede no estar visible en el usuario actual
                // (se muestra también ese proyecto en la lista)
                // (p.e. un proyecto en un widget de un sensor de una localización diferente a la actual)
                if ((in_array($fila_proyecto["id"], $ids_proyectos_usuario) == false) &&
                    ($fila_proyecto["id"] != $id_proyecto_seleccionado))
                {
                    $anyadir_proyecto = false;
                }
            }

            if ($anyadir_proyecto == true)
            {
                $lista_proyectos .= "<option value='".$fila_proyecto['id']."'";
                if ($fila_proyecto['id'] == $id_proyecto_seleccionado)
                {
                    $lista_proyectos .= " selected";
                }
                $lista_proyectos .= ">".htmlspecialchars($fila_proyecto['nombre'], ENT_QUOTES)."</option>";
            }
        }
        return ($lista_proyectos);
    }


    // Devuelve la lista de intervalos de valores de proyecto
    function dame_lista_intervalos_valores_proyecto($opciones_extra, $intervalo_valores_seleccionado)
    {
        $intervalos_valores_proyecto = array(
            INTERVALO_VALORES_HORA,
            INTERVALO_VALORES_DIA,
            INTERVALO_VALORES_SEMANA,
            INTERVALO_VALORES_MES);

        $idiomas = new Idiomas();
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_NINGUNO:
            {
                $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), INTERVALO_VALORES_NINGUNO, $intervalo_valores_seleccionado);
                break;
            }
            case OPCIONES_EXTRA_LISTA_INTERVALOS_VALORES_TODOS:
            {
                $lista = dame_opcion_valor_lista_simple($idiomas->_("Todos"), INTERVALO_VALORES_TODOS, $intervalo_valores_seleccionado);
                break;
            }
        }
        foreach ($intervalos_valores_proyecto as $intervalo_valores_proyecto)
        {
            $nombre_intervalo_valores_proyecto = dame_descripcion_intervalo_valores($intervalo_valores_proyecto);
            $lista .= "<option value='".$intervalo_valores_proyecto."'";
			if ($intervalo_valores_proyecto == $intervalo_valores_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_intervalo_valores_proyecto, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de tipos de objetivo de proyectos
    function dame_lista_tipos_objetivo_proyecto($tipo_objetivo_seleccionado)
    {
        $tipos_objetivo_proyecto = Proyecto::dame_tipos_objetivo_proyecto();

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), TIPO_NINGUNO, $tipo_objetivo_seleccionado);
        foreach ($tipos_objetivo_proyecto as $tipo_objetivo_proyecto)
        {
            $nombre_tipo_objetivo_proyecto = Proyecto::dame_descripcion_tipo_objetivo_proyecto($tipo_objetivo_proyecto);
            $lista .= "<option value='".$tipo_objetivo_proyecto."'";
			if ($tipo_objetivo_proyecto == $tipo_objetivo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_tipo_objetivo_proyecto, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de tipos de valor de objetivo de proyectos
    function dame_lista_tipos_valor_objetivo_proyecto($tipo_valor_objetivo_seleccionado)
    {
        $tipos_valor_objetivo_proyecto = Proyecto::dame_tipos_valor_objetivo_proyecto();

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), TIPO_NINGUNO, $tipo_valor_objetivo_seleccionado);
        foreach ($tipos_valor_objetivo_proyecto as $tipo_valor_objetivo_proyecto)
        {
            $nombre_tipo_valor_objetivo_proyecto = Proyecto::dame_descripcion_tipo_valor_objetivo_proyecto($tipo_valor_objetivo_proyecto);
            $lista .= "<option value='".$tipo_valor_objetivo_proyecto."'";
			if ($tipo_valor_objetivo_proyecto == $tipo_valor_objetivo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_tipo_valor_objetivo_proyecto, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de estados de avance de proyecto
    function dame_lista_estados_avance_proyecto($opciones_extra, $estado_avance_seleccionado)
    {
        $estados_avance_proyecto = Proyecto::dame_estados_avance_proyecto();

        $idiomas = new Idiomas();
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_ESTADOS_AVANCE_PROYECTO_TODOS:
            {
                $lista = dame_opcion_valor_lista_simple($idiomas->_("Todos"), ESTADO_AVANCE_PROYECTO_TODOS, $estado_avance_seleccionado);
                break;
            }
        }
        foreach ($estados_avance_proyecto as $estado_avance_proyecto)
        {
            $nombre_estado_avance_proyecto = Proyecto::dame_descripcion_estado_avance_proyecto($estado_avance_proyecto);
            $lista .= "<option value='".$estado_avance_proyecto."'";
			if ($estado_avance_proyecto == $estado_avance_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_estado_avance_proyecto, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de estados de proyecto
    function dame_lista_estados_proyecto($opciones_extra, $estado_seleccionado)
    {
        $estados_proyecto = Proyecto::dame_estados_proyecto();

        $idiomas = new Idiomas();
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_ESTADOS_PROYECTO_TODOS:
            {
                $lista = dame_opcion_valor_lista_simple($idiomas->_("Todos"), ESTADO_PROYECTO_TODOS, $estado_seleccionado);
                break;
            }
        }
        foreach ($estados_proyecto as $estado_proyecto)
        {
            $nombre_estado_proyecto = Proyecto::dame_descripcion_estado_proyecto($estado_proyecto);
            $lista .= "<option value='".$estado_proyecto."'";
			if ($estado_proyecto == $estado_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_estado_proyecto, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de destinos de valor adicional de proyecto
    function dame_lista_destinos_valor_adicional_proyecto($destino_seleccionada)
    {
        $destinos_valor_adicional_proyecto = Proyecto::dame_destinos_valor_adicional_proyecto();
        foreach ($destinos_valor_adicional_proyecto as $destino_valor_adicional_proyecto)
        {
            $nombre_destino_valor_adicional_proyecto = Proyecto::dame_descripcion_destino_valor_adicional_proyecto($destino_valor_adicional_proyecto);
            $lista .= "<option value='".$destino_valor_adicional_proyecto."'";
			if ($destino_valor_adicional_proyecto == $destino_seleccionada)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_destino_valor_adicional_proyecto, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de periodicidades de valor adicional de proyecto
    function dame_lista_periodicidades_valor_adicional_proyecto($periodicidad_seleccionada)
    {
        $periodicidades_valor_adicional_proyecto = Proyecto::dame_periodicidades_valor_adicional_proyecto();
        foreach ($periodicidades_valor_adicional_proyecto as $periodicidad_valor_adicional_proyecto)
        {
            $nombre_periodicidad_valor_adicional_proyecto = Proyecto::dame_descripcion_periodicidad_valor_adicional_proyecto($periodicidad_valor_adicional_proyecto);
            $lista .= "<option value='".$periodicidad_valor_adicional_proyecto."'";
			if ($periodicidad_valor_adicional_proyecto == $periodicidad_seleccionada)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_periodicidad_valor_adicional_proyecto, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Crea una lista desplegable para la selección de un proyecto
    function dame_control_lista_proyectos($id_controles)
    {
        $idiomas = new Idiomas();

        $control_lista_proyectos = "";
        $control_lista_proyectos .= "<div id='etiqueta_proyecto_".$id_controles."'>".$idiomas->_("Proyecto").": "."</div>";
        $control_lista_proyectos .= "
            <select id='id_proyecto_".$id_controles."' class='chosen-select' hidden>";
        $control_lista_proyectos .= dame_lista_proyectos(NULL);
        $control_lista_proyectos .= "
            </select>";

        return ($control_lista_proyectos);
    }


    // Crea una lista desplegable para la selección de un intervalo de valores de proyecto
    function dame_control_lista_intervalos_valores_proyecto($id_controles, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $control_lista_intervalos_valores_proyecto = "";
        $control_lista_intervalos_valores_proyecto .= "<div id='etiqueta_intervalo_valores_proyecto_".$id_controles."'>".$idiomas->_("Intervalo de valores").": "."</div>";
        $control_lista_intervalos_valores_proyecto .= "
            <select id='intervalo_valores_proyecto_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_intervalos_valores_proyecto .= dame_lista_intervalos_valores_proyecto($opciones_extra, NULL);
        $control_lista_intervalos_valores_proyecto .= "
            </select>";

        return ($control_lista_intervalos_valores_proyecto);
    }


    // Crea una lista desplegable para la selección de un estado de avance de proyecto
    function dame_control_lista_estados_avance_proyecto($id_controles, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $control_lista_estados_avance_proyecto = "";
        $control_lista_estados_avance_proyecto .= "<div id='etiqueta_estado_avance_proyecto_".$id_controles."'>".$idiomas->_("Avance").": "."</div>";
        $control_lista_estados_avance_proyecto .= "
            <select id='estado_avance_proyecto_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_estados_avance_proyecto .= dame_lista_estados_avance_proyecto($opciones_extra, NULL);
        $control_lista_estados_avance_proyecto .= "
            </select>";

        return ($control_lista_estados_avance_proyecto);
    }


    // Crea una lista desplegable para la selección de un estado de proyecto
    function dame_control_lista_estados_proyecto($id_controles, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $control_lista_estados_proyecto = "";
        $control_lista_estados_proyecto .= "<div id='etiqueta_estado_proyecto_".$id_controles."'>".$idiomas->_("Estado").": "."</div>";
        $control_lista_estados_proyecto .= "
            <select id='estado_proyecto_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_estados_proyecto .= dame_lista_estados_proyecto($opciones_extra, NULL);
        $control_lista_estados_proyecto .= "
            </select>";

        return ($control_lista_estados_proyecto);
    }


    //
    // Funciones de proyectos
    //


    // Se invalida el avance y el estado del proyecto
    function invalida_avance_estado_proyecto($id_proyecto)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $operacion_modificacion = "
            UPDATE proyectos
            SET
                hora_ultimo_calculo_avance = NULL,
                hora_fin_valores_avance = NULL,
                hora_ultimos_valores_avance = NULL,
                valor_real_avance = NULL,
                valor_simulado_avance = NULL,
                porcentaje_finalizacion = NULL,
                estado_avance = '".ESTADO_AVANCE_PROYECTO_NINGUNO."',
                estado = '".ESTADO_PROYECTO_NINGUNO."'
            WHERE
                id = '".$bd_red->_($id_proyecto)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == false)
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion."'");
        }
    }


    // Se invalidan los avances y el estado de los proyectos dependientes de la línea base especificada
    function invalida_avance_estado_proyectos_dependientes_linea_base($id_linea_base)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan las líneas base ascendientes
        $info_lineas_base_padres = NULL;
        $info_lineas_base_hijas = NULL;
        carga_informacion_lineas_base_padres_hijas_excepciones($info_lineas_base_padres, $info_lineas_base_hijas);
        $ids_lineas_base_ascendientes = dame_ids_lineas_base_ascendientes_lineas_base($info_lineas_base_padres, array($id_linea_base), true);

        // Se recuperan los proyectos con las líneas base especificadas
        $cadena_ids_lineas_base_consulta = dame_cadena_ids_consulta($ids_lineas_base_ascendientes);
        $consulta_proyectos = "
            SELECT
                id
            FROM proyectos
            WHERE
                linea_base IN (".$cadena_ids_lineas_base_consulta.")";
        $res_proyectos = $bd_red->ejecuta_consulta($consulta_proyectos);
        if ($res_proyectos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_proyectos."'");
        }

        // Se invalidan el avance y el estado de los proyectos
        while ($fila_proyecto = $res_proyectos->dame_siguiente_fila())
        {
            invalida_avance_estado_proyecto($fila_proyecto["id"]);
        }
    }


    //
    // Funciones de obtención de información de proyectos
    //


    // Devuelve la fila del proyecto
    function dame_fila_proyecto($id_proyecto)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_proyecto = "
            SELECT *
            FROM proyectos
            WHERE
                id = '".$bd_red->_($id_proyecto)."'";
        $res_proyecto = $bd_red->ejecuta_consulta($consulta_proyecto);
        if (($res_proyecto == false) || ($res_proyecto->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_proyecto."'");
        }
        $fila_proyecto = $res_proyecto->dame_siguiente_fila();
        return ($fila_proyecto);
    }


    // Devuelve el nombre del proyecto
    function dame_nombre_proyecto($id_proyecto)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_proyecto = "
            SELECT nombre
            FROM proyectos
            WHERE
                id = '".$bd_red->_($id_proyecto)."'";
        $res_proyecto = $bd_red->ejecuta_consulta($consulta_proyecto);
        if (($res_proyecto == false) || ($res_proyecto->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_proyecto."'");
        }
        $fila_proyecto = $res_proyecto->dame_siguiente_fila();
        $nombre_proyecto = $fila_proyecto["nombre"];
        return ($nombre_proyecto);
    }


    // Devuelve el número de valores adicionales del proyecto
    function dame_numero_valores_adicionales_proyecto($id_proyecto)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_valores_adicionales = "
            SELECT
                COUNT(*) AS numero_valores_adicionales
            FROM valores_adicionales_proyectos
            WHERE
                proyecto = '".$bd_red->_($id_proyecto)."'";
        $res_valores_adicionales = $bd_red->ejecuta_consulta($consulta_valores_adicionales);
        if (($res_valores_adicionales == false) || ($res_valores_adicionales->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_valores_adicionales."'");
        }
        $fila_valores_adicionales = $res_valores_adicionales->dame_siguiente_fila();
        $numero_valores_adicionales = $fila_valores_adicionales["numero_valores_adicionales"];
        return ($numero_valores_adicionales);
    }


    // Devuelve la información de valores adicionales de un proyecto
    function dame_info_valores_adicionales_proyecto($id_proyecto)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_valores_adicionales = "
            SELECT *
            FROM valores_adicionales_proyectos
            WHERE
                proyecto = '".$bd_red->_($id_proyecto)."'";
        $res_valores_adicionales = $bd_red->ejecuta_consulta($consulta_valores_adicionales);
        if ($res_valores_adicionales == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_adicionales."'");
        }

        $info_valores_adicionales = array();
        while ($fila_valor_adicional = $res_valores_adicionales->dame_siguiente_fila())
        {
            $id_valor_adicional = $fila_valor_adicional["id"];
            $info_valores_adicionales[$id_valor_adicional] = $fila_valor_adicional;
        }
        return ($info_valores_adicionales);
    }


    // Devuelve el número de proyectos (de la red actual)
    function dame_numero_proyectos()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_proyectos = "
            SELECT
                COUNT(*) AS numero_proyectos
            FROM proyectos
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_proyectos = $bd_red->ejecuta_consulta($consulta_proyectos);
        if (($res_proyectos == false) || ($res_proyectos->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_proyectos."'");
        }
        $fila_proyectos = $res_proyectos->dame_siguiente_fila();
        $numero_proyectos = $fila_proyectos["numero_proyectos"];
        return ($numero_proyectos);
    }


    // Devuelve el identificador del sensor de un proyecto
    function dame_id_sensor_proyecto($id_proyecto)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_proyecto = "
            SELECT
                sensor
            FROM proyectos
            WHERE
                id = '".$bd_red->_($id_proyecto)."'";
        $res_proyecto = $bd_red->ejecuta_consulta($consulta_proyecto);
        if (($res_proyecto == false) || ($res_proyecto->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_proyecto."'");
        }
        $fila_proyecto = $res_proyecto->dame_siguiente_fila();
        $id_sensor_proyecto = $fila_proyecto["sensor"];
        return ($id_sensor_proyecto);
    }


    // Devuelve la fila del valor adicional del proyecto
    function dame_fila_valor_adicional_proyecto($id_valor_adicional_proyecto)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_valor_adicional_proyecto = "
            SELECT *
            FROM valores_adicionales_proyectos
            WHERE
                id = '".$bd_red->_($id_valor_adicional_proyecto)."'";
        $res_valor_adicional_proyecto = $bd_red->ejecuta_consulta($consulta_valor_adicional_proyecto);
        if (($res_valor_adicional_proyecto == false) || ($res_valor_adicional_proyecto->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_valor_adicional_proyecto."'");
        }
        $fila_valor_adicional_proyecto = $res_valor_adicional_proyecto->dame_siguiente_fila();
        return ($fila_valor_adicional_proyecto);
    }


    //
    // Funciones auxiliares
    //


    // Devuelve los identificadores de todos los proyectos visibles por el usuario
    // (aquellos cuyo sensor sea visible en el usuario, bien por permisos del módulo Sensores o del módulo Localizaciones)
    function dame_ids_todos_proyectos_visibles_usuario(
        $permiso_todos_sensores,
        $ids_sensores,
        $ids_grupos_sensores,
        $modulos_usuario,
        $ids_sensores_visibles_localizaciones)
    {
        // Identificadores de proyectos
        $ids_proyectos = array();

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_proyectos = "
            SELECT
                id,
                sensor
            FROM proyectos
            WHERE
                red = '".$_SESSION["id_red"]."'
            ORDER BY nombre ASC";
        $res_proyectos = $bd_red->ejecuta_consulta($consulta_proyectos);
        if ($res_proyectos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_proyectos."'");
        }
        while ($fila_proyecto = $res_proyectos->dame_siguiente_fila())
        {
            $id_proyecto = $fila_proyecto["id"];
            $id_sensor_proyecto = $fila_proyecto["sensor"];

            $sensor_visible_usuario = false;
            if ($sensor_visible_usuario == false)
            {
                if (($permiso_todos_sensores == true) ||
                    (dame_sensor_sensores_grupos($id_sensor_proyecto, $ids_sensores, $ids_grupos_sensores) == true))
                {
                    $sensor_visible_usuario = true;
                }
            }
            if ($sensor_visible_usuario == false)
            {
                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                {
                    if (in_array($id_sensor_proyecto, $ids_sensores_visibles_localizaciones) == true)
                    {
                        $sensor_visible_usuario = true;
                    }
                }
            }

            if ($sensor_visible_usuario == true)
            {
                array_push($ids_proyectos, $id_proyecto);
            }
        }

        return ($ids_proyectos);
    }
?>