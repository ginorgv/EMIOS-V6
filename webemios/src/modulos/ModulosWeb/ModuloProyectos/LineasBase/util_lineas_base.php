<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/LineaBase.php');


    //
    // Funciones de identificadores de líneas base
    //


    // Devuelve los identificadores de las líneas base (de la red actual)
    function dame_ids_lineas_base()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_lineas_base = "
            SELECT id
            FROM lineas_base
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        $res_lineas_base = $bd_red->ejecuta_consulta($consulta_lineas_base);
        if ($res_lineas_base == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_lineas_base."'");
        }
        $ids_lineas_base = array();
        while ($fila_linea_base = $res_lineas_base->dame_siguiente_fila())
        {
            array_push($ids_lineas_base, $fila_linea_base["id"]);
        }
        return ($ids_lineas_base);
    }


    //
    // Funciones de permisos de líneas base
    //


    // Devuelve si se muestran todas las líneas base
    function dame_mostrar_todas_lineas_base()
    {
        $mostrar_todas_lineas_base = dame_mostrar_todos_sensores();
        return ($mostrar_todas_lineas_base);
    }


    //
    // Funciones de listas de líneas base
    //


    // Devuelve la lista de líneas base
    function dame_lista_lineas_base($id_linea_base_seleccionada)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_lineas_base = "
            SELECT
                id,
                nombre
            FROM lineas_base
            WHERE
                red = '".$_SESSION["id_red"]."'
            ORDER BY nombre ASC";
        $res_lineas_base = $bd_red->ejecuta_consulta($consulta_lineas_base);
        if ($res_lineas_base == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_lineas_base."'");
        }

        // Identificadores de líneas base del usuario actual
        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        if ($mostrar_todos_sensores == false)
        {
            $ids_lineas_base_usuario = LineaBase::dame_ids_lineas_base_usuario_actual();
        }

        $lista_lineas_base = "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguna")."</option>";
        while ($fila_linea_base = $res_lineas_base->dame_siguiente_fila())
        {
            $anyadir_linea_base = true;
            if ($mostrar_todos_sensores == false)
            {
                if (in_array($fila_linea_base["id"], $ids_lineas_base_usuario) == false)
                {
                    $anyadir_linea_base = false;
                }
            }

            if ($anyadir_linea_base == true)
            {
                $lista_lineas_base .= "<option value='".$fila_linea_base['id']."'";
                if ($fila_linea_base['id'] == $id_linea_base_seleccionada)
                {
                    $lista_lineas_base .= " selected";
                }
                $lista_lineas_base .= ">".htmlspecialchars($fila_linea_base['nombre'], ENT_QUOTES)."</option>";
            }
        }
        return ($lista_lineas_base);
    }


    // Devuelve la lista de líneas base con el intervalo de valores especificado
    function dame_lista_lineas_base_intervalo_valores($intervalo_valores, $linea_base_seleccionada)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_lineas_base = "
            SELECT
                id,
                nombre
            FROM lineas_base
            WHERE
                (intervalo_valores = '".$bd_red->_($intervalo_valores)."')
                AND (red = '".$_SESSION["id_red"]."')
            ORDER BY nombre ASC";
        $res_lineas_base = $bd_red->ejecuta_consulta($consulta_lineas_base);
        if ($res_lineas_base == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_lineas_base."'");
        }

        // Identificadores de líneas base del usuario actual
        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        if ($mostrar_todos_sensores == false)
        {
            $ids_lineas_base_usuario = LineaBase::dame_ids_lineas_base_usuario_actual();
        }

        $lista_lineas_base = "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguna")."</option>";
        while ($fila_linea_base = $res_lineas_base->dame_siguiente_fila())
        {
            $anyadir_linea_base = true;
            if ($mostrar_todos_sensores == false)
            {
                if (in_array($fila_linea_base["id"], $ids_lineas_base_usuario) == false)
                {
                    $anyadir_linea_base = false;
                }
            }

            if ($anyadir_linea_base == true)
            {
                $lista_lineas_base .= "<option value='".$fila_linea_base['id']."'";
                if ($fila_linea_base['id'] == $linea_base_seleccionada)
                {
                    $lista_lineas_base .= " selected";
                }
                $lista_lineas_base .= ">".htmlspecialchars($fila_linea_base['nombre'], ENT_QUOTES)."</option>";
            }
        }
        return ($lista_lineas_base);
    }


    // Devuelve la lista de líneas base con el sensor especificado
    function dame_lista_lineas_base_sensor($id_sensor, $linea_base_seleccionada)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_lineas_base = "
            SELECT
                id,
                nombre
            FROM lineas_base
            WHERE
                sensor = '".$bd_red->_($id_sensor)."'
            ORDER BY nombre ASC";
        $res_lineas_base = $bd_red->ejecuta_consulta($consulta_lineas_base);
        if ($res_lineas_base == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_lineas_base."'");
        }

        $lista_lineas_base = "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguna")."</option>";
        while ($fila_linea_base = $res_lineas_base->dame_siguiente_fila())
        {
            $lista_lineas_base .= "<option value='".$fila_linea_base['id']."'";
            if ($fila_linea_base['id'] == $linea_base_seleccionada)
            {
                $lista_lineas_base .= " selected";
            }
            $lista_lineas_base .= ">".htmlspecialchars($fila_linea_base['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_lineas_base);
    }


    // Devuelve la lista de tipos de línea base
    function dame_lista_tipos_linea_base($opciones_extra, $tipo_seleccionado)
    {
        $idiomas = new Idiomas();
        $tipos_linea_base = LineaBase::dame_tipos_linea_base();

        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_TIPOS_NINGUNO:
            {
                $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), TIPO_NINGUNO, $tipo_seleccionado);
                break;
            }
            case OPCIONES_EXTRA_LISTA_TIPOS_TODOS:
            {
                $lista = dame_opcion_valor_lista_simple($idiomas->_("Todos"), TIPO_TODOS, $tipo_seleccionado);
                break;
            }
        }
        foreach ($tipos_linea_base as $tipo_linea_base)
        {
            $nombre_tipo_linea_base = LineaBase::dame_descripcion_tipo_linea_base($tipo_linea_base);
            $lista .= "<option value='".$tipo_linea_base."'";
			if ($tipo_linea_base == $tipo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_tipo_linea_base, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de intervalos de valores de líneas base
    function dame_lista_intervalos_valores_linea_base($tipo, $opciones_extra, $intervalo_valores_seleccionado)
    {
        switch ($tipo)
        {
            case TIPO_LINEA_BASE_PERIODICA:
            {
                $intervalos_valores_linea_base = array(
                    INTERVALO_VALORES_HORA,
                    INTERVALO_VALORES_DIA);
                break;
            }
            case TIPO_LINEA_BASE_FUNCIONAL:
            {
                $intervalos_valores_linea_base = array(
                    INTERVALO_VALORES_HORA,
                    INTERVALO_VALORES_DIA,
                    INTERVALO_VALORES_SEMANA,
                    INTERVALO_VALORES_MES);
                break;
            }
        }

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
        foreach ($intervalos_valores_linea_base as $intervalo_valores_linea_base)
        {
            $nombre_intervalo_valores_linea_base = dame_descripcion_intervalo_valores($intervalo_valores_linea_base);
            $lista .= "<option value='".$intervalo_valores_linea_base."'";
			if ($intervalo_valores_linea_base == $intervalo_valores_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_intervalo_valores_linea_base, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de periodicidades de valores de líneas base periódicas
    function dame_lista_periodicidades_valores_linea_base_periodica($periodicidad_valores_seleccionada)
    {
        $periodicidades_valores_linea_base = LineaBase::dame_periodicidades_valores_linea_base_periodica();

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), ID_NINGUNO, $periodicidad_valores_seleccionada);
        foreach ($periodicidades_valores_linea_base as $periodicidad_valores_linea_base)
        {
            $nombre_periodicidad_valores_linea_base = LineaBase::dame_descripcion_periodicidad_valores_linea_base_periodica($periodicidad_valores_linea_base);
            $lista .= "<option value='".$periodicidad_valores_linea_base."'";
			if ($periodicidad_valores_linea_base == $periodicidad_valores_seleccionada)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_periodicidad_valores_linea_base, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de tipos de cálculo de valores de líneas base periódicas
    function dame_lista_tipos_calculo_valores_linea_base_periodica($tipo_calculo_valores_seleccionado)
    {
        $tipos_calculo_valores_linea_base = LineaBase::dame_tipos_calculo_valores_linea_base_periodica();

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), TIPO_NINGUNO, $tipo_calculo_valores_seleccionado);
        foreach ($tipos_calculo_valores_linea_base as $tipo_calculo_valores_linea_base)
        {
            $nombre_tipo_calculo_valores_linea_base = LineaBase::dame_descripcion_tipo_calculo_valores_linea_base_periodica($tipo_calculo_valores_linea_base);
            $lista .= "<option value='".$tipo_calculo_valores_linea_base."'";
			if ($tipo_calculo_valores_linea_base == $tipo_calculo_valores_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_tipo_calculo_valores_linea_base, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Crea una lista desplegable para la selección de una línea base
    function dame_control_lista_lineas_base($id_controles)
    {
        $idiomas = new Idiomas();

        $control_lista_lineas_base = "";
        $control_lista_lineas_base .= "<div id='etiqueta_linea_base_".$id_controles."'>".$idiomas->_("Línea base").": "."</div>";
        $control_lista_lineas_base .= "
            <select id='id_linea_base_".$id_controles."' class='chosen-select' hidden>";
        $control_lista_lineas_base .= dame_lista_lineas_base(NULL);
        $control_lista_lineas_base .= "
            </select>";

        return ($control_lista_lineas_base);
    }


    // Crea una lista desplegable para la selección de un tipo de línea base
    function dame_control_lista_tipos_linea_base($id_controles, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $control_lista_tipos_linea_base = "";
        $control_lista_tipos_linea_base .= "<div id='etiqueta_tipo_linea_base_".$id_controles."'>".$idiomas->_("Tipo").": "."</div>";
        $control_lista_tipos_linea_base .= "
            <select id='tipo_linea_base_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_tipos_linea_base .= dame_lista_tipos_linea_base($opciones_extra, NULL);
        $control_lista_tipos_linea_base .= "
            </select>";

        return ($control_lista_tipos_linea_base);
    }


    // Crea una lista desplegable para la selección de un intervalo de valores de línea base
    function dame_control_lista_intervalos_valores_linea_base($id_controles, $tipo, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $control_lista_intervalos_valores_linea_base = "";
        $control_lista_intervalos_valores_linea_base .= "<div id='etiqueta_intervalo_valores_linea_base_".$id_controles."'>".$idiomas->_("Intervalo de valores").": "."</div>";
        $control_lista_intervalos_valores_linea_base .= "
            <select id='intervalo_valores_linea_base_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_intervalos_valores_linea_base .= dame_lista_intervalos_valores_linea_base($tipo, $opciones_extra, NULL);
        $control_lista_intervalos_valores_linea_base .= "
            </select>";

        return ($control_lista_intervalos_valores_linea_base);
    }


    //
    // Funciones de obtención de información de líneas base
    //


    // Devuelve la fila de la línea base
    function dame_fila_linea_base($id_linea_base)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_linea_base = "
            SELECT *
            FROM lineas_base
            WHERE
                id = '".$bd_red->_($id_linea_base)."'";
        $res_linea_base = $bd_red->ejecuta_consulta($consulta_linea_base);
        if (($res_linea_base == false) || ($res_linea_base->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_linea_base."'");
        }
        $fila_linea_base = $res_linea_base->dame_siguiente_fila();
        return ($fila_linea_base);
    }


    // Devuelve la fila de una línea base a partir del nombre
    function dame_fila_linea_base_nombre($nombre_linea_base)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_linea_base = "
            SELECT *
            FROM lineas_base
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (nombre = '".$bd_red->_($nombre_linea_base)."')";
        $res_linea_base = $bd_red->ejecuta_consulta($consulta_linea_base);
        if (($res_linea_base == false) || ($res_linea_base->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_linea_base."'");
        }
        $fila_linea_base = $res_linea_base->dame_siguiente_fila();
        return ($fila_linea_base);
    }


    // Devuelve el nombre de la línea base
    function dame_nombre_linea_base($id_linea_base)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        switch ($id_linea_base)
        {
            case ID_NINGUNO:
            {
                $nombre_linea_base = $idiomas->_("Ninguna");
                break;
            }
            default:
            {
                $consulta_linea_base = "
                    SELECT nombre
                    FROM lineas_base
                    WHERE
                        id = '".$bd_red->_($id_linea_base)."'";
                $res_linea_base = $bd_red->ejecuta_consulta($consulta_linea_base);
                if (($res_linea_base == false) || ($res_linea_base->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_linea_base."'");
                }
                $fila_linea_base = $res_linea_base->dame_siguiente_fila();
                $nombre_linea_base = $fila_linea_base["nombre"];
                break;
            }
        }
        return ($nombre_linea_base);
    }


    // Devuelve el intervalo de valores de la línea base
    function dame_intervalo_valores_linea_base($id_linea_base)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_linea_base = "
            SELECT
                intervalo_valores
            FROM lineas_base
            WHERE
                id = '".$bd_red->_($id_linea_base)."'";
        $res_linea_base = $bd_red->ejecuta_consulta($consulta_linea_base);
        if (($res_linea_base == false) || ($res_linea_base->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_linea_base."'");
        }
        $fila_linea_base = $res_linea_base->dame_siguiente_fila();
        $intervalo_valores = $fila_linea_base["intervalo_valores"];
        return ($intervalo_valores);
    }


    // Devuelve el número de variables de la línea base
    function dame_numero_variables_linea_base($id_linea_base)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_variables = "
            SELECT
                COUNT(*) AS numero_variables
            FROM variables_lineas_base
            WHERE
                linea_base = '".$bd_red->_($id_linea_base)."'";
        $res_variables = $bd_red->ejecuta_consulta($consulta_variables);
        if (($res_variables == false) || ($res_variables->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_variables."'");
        }
        $fila_variables = $res_variables->dame_siguiente_fila();
        $numero_variables = $fila_variables["numero_variables"];
        return ($numero_variables);
    }


    // Devuelve el número de excepciones de la línea base
    function dame_numero_excepciones_linea_base($id_linea_base)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_excepciones = "
            SELECT
                COUNT(*) AS numero_excepciones
            FROM excepciones_lineas_base
            WHERE
                linea_base_padre = '".$bd_red->_($id_linea_base)."'";
        $res_excepciones = $bd_red->ejecuta_consulta($consulta_excepciones);
        if (($res_excepciones == false) || ($res_excepciones->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_excepciones."'");
        }
        $fila_excepciones = $res_excepciones->dame_siguiente_fila();
        $numero_excepciones = $fila_excepciones["numero_excepciones"];
        return ($numero_excepciones);
    }


    // Devuelve el identificador del sensor de una línea base
    function dame_id_sensor_linea_base($id_linea_base)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_linea_base = "
            SELECT
                sensor
            FROM lineas_base
            WHERE
                id = '".$bd_red->_($id_linea_base)."'";
        $res_linea_base = $bd_red->ejecuta_consulta($consulta_linea_base);
        if (($res_linea_base == false) || ($res_linea_base->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_linea_base."'");
        }
        $fila_linea_base = $res_linea_base->dame_siguiente_fila();
        $id_sensor_linea_base = $fila_linea_base["sensor"];
        return ($id_sensor_linea_base);
    }


    // Devuelve la fila de la excepción de la línea bas
    function dame_fila_excepcion_linea_base($id_excepcion_linea_base)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_excepcion_linea_base = "
            SELECT *
            FROM excepciones_lineas_base
            WHERE
                id = '".$bd_red->_($id_excepcion_linea_base)."'";
        $res_excepcion_linea_base = $bd_red->ejecuta_consulta($consulta_excepcion_linea_base);
        if (($res_excepcion_linea_base == false) || ($res_excepcion_linea_base->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_excepcion_linea_base."'");
        }
        $fila_excepcion_linea_base = $res_excepcion_linea_base->dame_siguiente_fila();
        return ($fila_excepcion_linea_base);
    }


    // Devuelve la fila de la variable de la línea base
    function dame_fila_variable_linea_base($id_variable_linea_base)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_variable_linea_base = "
            SELECT *
            FROM variables_lineas_base
            WHERE
                id = '".$bd_red->_($id_variable_linea_base)."'";
        $res_variable_linea_base = $bd_red->ejecuta_consulta($consulta_variable_linea_base);
        if (($res_variable_linea_base == false) || ($res_variable_linea_base->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_variable_linea_base."'");
        }
        $fila_variable_linea_base = $res_variable_linea_base->dame_siguiente_fila();
        return ($fila_variable_linea_base);
    }


    //
    // Funciones auxiliares
    //


    // Devuelve los identificadores de todas las líneas base visibles por el usuario
    // (aquellas cuyo sensor sea visible en el usuario, bien por permisos del módulo Sensores o del módulo Localizaciones)
    function dame_ids_todas_lineas_base_visibles_usuario(
        $permiso_todos_sensores,
        $ids_sensores,
        $ids_grupos_sensores,
        $modulos_usuario,
        $ids_sensores_visibles_localizaciones)
    {
        // Identificadores de líneas base
        $ids_lineas_base = array();

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_lineas_base = "
            SELECT
                id,
                sensor
            FROM lineas_base
            WHERE
                red = '".$_SESSION["id_red"]."'
            ORDER BY nombre ASC";
        $res_lineas_base = $bd_red->ejecuta_consulta($consulta_lineas_base);
        if ($res_lineas_base == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_lineas_base."'");
        }
        while ($fila_linea_base = $res_lineas_base->dame_siguiente_fila())
        {
            $id_linea_base = $fila_linea_base["id"];
            $id_sensor_linea_base = $fila_linea_base["sensor"];

            $sensor_visible_usuario = false;
            if ($sensor_visible_usuario == false)
            {
                if (($permiso_todos_sensores == true) ||
                    (dame_sensor_sensores_grupos($id_sensor_linea_base, $ids_sensores, $ids_grupos_sensores) == true))
                {
                    $sensor_visible_usuario = true;
                }
            }
            if ($sensor_visible_usuario == false)
            {
                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                {
                    if (in_array($id_sensor_linea_base, $ids_sensores_visibles_localizaciones) == true)
                    {
                        $sensor_visible_usuario = true;
                    }
                }
            }

            if ($sensor_visible_usuario == true)
            {
                array_push($ids_lineas_base, $id_linea_base);
            }
        }

        return ($ids_lineas_base);
    }


    //
    // Funciones de acciones de usuario
    //


    // Añade la acción de usuario de adición de la línea base
    function anyade_accion_usuario_anyadir_linea_base($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_LINEA_BASE;
        $objeto_accion_usuario = $fila["nombre"];

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_SENSOR] = $fila["clase_sensor"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_SENSOR] = dame_nombre_sensor($fila["sensor"]);
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CAMPO_PARAMETROS_EXTRA] = $fila["campo_parametros_extra"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_LINEA_BASE] = $fila["tipo"];
        $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila["parametros_tipo"]);
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PARAMETROS_TIPO_LINEA_BASE] = array(
            "tipo" => $fila["tipo"],
            "parametros_tipo" => $parametros_tipo);
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_INTERVALO_VALORES] = $fila["intervalo_valores"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_INICIO_PERIODO_REFERENCIA_LINEA_BASE] = $fila["fecha_inicio_periodo_referencia"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_FIN_PERIODO_REFERENCIA_LINEA_BASE] = $fila["fecha_fin_periodo_referencia"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ERROR_ESTANDAR_LINEA_BASE] = $fila["error_estandar"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_COEFICIENTE_VARIACION_LINEA_BASE] = $fila["coeficiente_variacion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_COEFICIENTE_CORRELACION_LINEA_BASE] = $fila["coeficiente_correlacion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_HORARIO_SEMANAL] = array(
            "cadena_horario_semanal" => $fila["horario_semanal"],
            "mostrar_horas" => true);
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_EXCLUSION_FECHAS] = $fila["exclusion_fechas"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>