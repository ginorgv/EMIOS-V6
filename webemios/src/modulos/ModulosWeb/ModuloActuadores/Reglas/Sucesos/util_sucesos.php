<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Regla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/SucesoRegla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/util_reglas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_eventos.php');


    //
    // Funciones de listas
    //


    function dame_lista_causas_suceso_regla($causa_seleccionada)
    {
        $causas_suceso = SucesoRegla::dame_causas_suceso();

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), ID_NINGUNO, $causa_seleccionada);
        foreach ($causas_suceso as $causa_suceso)
        {
            $lista .= "<option value='".$causa_suceso."'";
			if ($causa_suceso == $causa_seleccionada)
			{
				$lista .= " selected";
			}
			$lista .= ">".SucesoRegla::dame_descripcion_causa_suceso($causa_suceso)."</option>";
        }

        return ($lista);
    }


    function dame_lista_ids_causas_suceso_regla($causa, &$id_causa_seleccionada, $id_regla)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $lista = "";
        switch ($causa)
        {
            case ID_NINGUNO:
            {
                $consulta_causas = "";
                break;
            }
            case CAUSA_SUCESO_EVENTO:
            {
                $consulta_causas = "
                    SELECT
                        id,
                        nombre
                    FROM eventos
                    WHERE
                        red = '".$_SESSION["id_red"]."'";
                $mostrar_todos_sensores = dame_mostrar_todos_sensores();
                if ($mostrar_todos_sensores == false)
                {
                    $ids_eventos_usuario = Evento::dame_ids_eventos_usuario_actual();
                    $cadena_ids_eventos_consulta = dame_cadena_ids_consulta($ids_eventos_usuario);
                    $consulta_causas .= "
                        AND (id IN (".$cadena_ids_eventos_consulta."))";
                }
                $consulta_causas .= "
                    ORDER BY nombre ASC";
                break;
            }
            case CAUSA_SUCESO_REGLA:
            {
                $consulta_causas = "
                    SELECT
                        id,
                        nombre
                    FROM reglas
                    WHERE
                        red = '".$_SESSION["id_red"]."'";
                $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
                if ($mostrar_todos_actuadores == false)
                {
                    $ids_reglas_usuario = Regla::dame_ids_reglas_usuario_actual();
                    $cadena_ids_reglas_consulta = dame_cadena_ids_consulta($ids_reglas_usuario);
                    $consulta_causas .= "
                        AND (id IN (".$cadena_ids_reglas_consulta."))";
                }
                $consulta_causas .= "
                        AND (id <> '".$bd_red->_($id_regla)."')";
                $consulta_causas .= "
                    ORDER BY nombre ASC";
                break;
            }
            case CAUSA_SUCESO_TIMEOUT_ENVIO_SENSOR:
            {
                $consulta_causas = "";
                break;
            }
        }
        $lista .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        if ($consulta_causas != "")
        {
            $res_causas = $bd_red->ejecuta_consulta($consulta_causas);
            if ($res_causas == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_causas."'");
            }

            while ($fila_causa = $res_causas->dame_siguiente_fila())
            {
                $lista .= "<option value='".$fila_causa['id']."'";
                if ($fila_causa['id'] == $id_causa_seleccionada)
                {
                    $lista .= " selected";
                }
                $lista .= ">".$fila_causa['nombre']."</option>";
            }
        }

        return ($lista);
    }


    function dame_lista_origenes_suceso_regla($causa, $id_causa, &$origen_seleccionado)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        switch ($causa)
        {
            case ID_NINGUNO:
            {
                $origenes_suceso = array();
                break;
            }
            case CAUSA_SUCESO_EVENTO:
            {
                if ($id_causa == ID_NINGUNO)
                {
                    $origenes_suceso = array();
                }
                else
                {
                    $consulta_evento = "
                        SELECT origen
                        FROM eventos
                        WHERE
                            eventos.id = '".$bd_red->_($id_causa)."'";
                    $res_evento = $bd_red->ejecuta_consulta($consulta_evento);
                    if (($res_evento == false) || ($res_evento->dame_numero_filas() == 0))
                    {
                        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_evento."'");
                    }
                    $fila_evento = $res_evento->dame_siguiente_fila();
                    $origen_evento = $fila_evento['origen'];

                    // Tipos de origenes del suceso
                    switch ($origen_evento)
                    {
                        case ORIGEN_EVENTO_SENSOR:
                        {
                            $origenes_suceso = array(ORIGEN_SUCESO_SENSOR);
                            break;
                        }
                        case ORIGEN_EVENTO_GRUPO_SENSORES:
                        {
                            $origenes_suceso = array(ORIGEN_SUCESO_GRUPO_SENSORES);
                            break;
                        }
                    }
                }
                break;
            }
            case CAUSA_SUCESO_TIMEOUT_ENVIO_SENSOR:
            {
                $origenes_suceso = array(ORIGEN_SUCESO_SENSOR, ORIGEN_SUCESO_GRUPO_SENSORES);
                break;
            }
            case CAUSA_SUCESO_REGLA:
            {
                $origenes_suceso = array();
                break;
            }
        }

        if (count($origenes_suceso) == 0)
        {
            $lista = "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        }
        else
        {
            foreach ($origenes_suceso as $origen_suceso)
            {
                if ($origen_seleccionado == ID_NINGUNO)
                {
                    $origen_seleccionado = $origen_suceso;
                }
                $lista .= "<option value='".$origen_suceso."'";
                if ($origen_suceso == $origen_seleccionado)
                {
                    $lista .= " selected";
                }
                $lista .= ">".SucesoRegla::dame_descripcion_origen_suceso($origen_suceso)."</option>";
            }
        }

        return ($lista);
    }


    function dame_lista_ids_origenes_suceso_regla($causa, $id_causa, $origen, &$id_origen_seleccionado)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $lista = "";
        switch ($causa)
        {
            case ID_NINGUNO:
            {
                $consulta_origenes = "";
                break;
            }
            case CAUSA_SUCESO_EVENTO:
            {
                // Si no hay eventos, no hay orígenes
                if ($id_causa == ID_NINGUNO)
                {
                    $consulta_origenes = "";
                }
                else
                {
                    $consulta_evento = "
                        SELECT
                            origen,
                            id_origen
                        FROM eventos
                        WHERE
                            eventos.id = '".$bd_red->_($id_causa)."'";
                    $res_evento = $bd_red->ejecuta_consulta($consulta_evento);
                    if (($res_evento == false) || ($res_evento->dame_numero_filas() == 0))
                    {
                        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_evento."'");
                    }

                    $fila_evento = $res_evento->dame_siguiente_fila();
                    $origen_evento = $fila_evento['origen'];
                    $id_origen_evento = $fila_evento['id_origen'];

                    // El origen seleccionado es el origen del evento
                    $id_origen_seleccionado = $id_origen_evento;

                    // Tipos de origenes del evento
                    switch ($origen_evento)
                    {
                        case ORIGEN_EVENTO_SENSOR:
                        {
                            $consulta_origenes = "
                                SELECT
                                    sensores.id AS id,
                                    sensores.nombre AS nombre
                                FROM sensores
                                WHERE
                                    sensores.id = '".$bd_red->_($id_origen_evento)."'";
                            $mostrar_todos_sensores = dame_mostrar_todos_sensores();
                            if ($mostrar_todos_sensores == false)
                            {
                                $consulta_origenes .=
                                    "AND ".dame_condicion_consulta_sensores_usuario_actual(true);
                            }
                            $consulta_origenes .= "
                                ORDER BY nombre ASC";
                            break;
                        }
                        case ORIGEN_EVENTO_GRUPO_SENSORES:
                        {
                            switch ($origen)
                            {
                                case ORIGEN_SUCESO_SENSOR:
                                {
                                    $consulta_origenes = "
                                        SELECT
                                            sensores.id AS id,
                                            sensores.nombre AS nombre
                                        FROM sensores
                                        WHERE
                                            sensores.grupo = '".$bd_red->_($id_origen_evento)."'";
                                    $mostrar_todos_sensores = dame_mostrar_todos_sensores();
                                    if ($mostrar_todos_sensores == false)
                                    {
                                        $consulta_origenes .=
                                            "AND ".dame_condicion_consulta_sensores_usuario_actual(true);
                                    }
                                    $consulta_origenes .= "
                                        ORDER BY nombre ASC";
                                    break;
                                }
                                case ORIGEN_SUCESO_GRUPO_SENSORES:
                                {
                                    $consulta_origenes = "
                                        SELECT
                                            grupos_sensores.id AS id,
                                            grupos_sensores.nombre AS nombre
                                        FROM grupos_sensores
                                        WHERE
                                            grupos_sensores.id = '".$bd_red->_($id_origen_evento)."'";
                                    $mostrar_todos_sensores = dame_mostrar_todos_sensores();
                                    if ($mostrar_todos_sensores == false)
                                    {
                                        $consulta_origenes .=
                                            "AND ".dame_condicion_consulta_grupos_sensores_usuario_actual(false);
                                    }
                                    $consulta_origenes .= "
                                        ORDER BY nombre ASC";
                                    break;
                                }
                            }
                            break;
                        }
                    }
                }
                break;
            }
            case CAUSA_SUCESO_TIMEOUT_ENVIO_SENSOR:
            {
                switch ($origen)
                {
                    case ORIGEN_SUCESO_SENSOR:
                    {
                        $consulta_origenes = "
                            SELECT
                                id,
                                nombre
                            FROM sensores
                            WHERE
                                red = '".$_SESSION["id_red"]."'";
                        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
                        if ($mostrar_todos_sensores == false)
                        {
                            $consulta_origenes .=
                                "AND ".dame_condicion_consulta_sensores_usuario_actual(true);
                        }
                        $consulta_origenes .= "
                            ORDER BY nombre ASC";
                        break;
                    }
                    case ORIGEN_SUCESO_GRUPO_SENSORES:
                    {
                        $consulta_origenes = "
                            SELECT
                                id,
                                nombre
                            FROM grupos_sensores
                            WHERE
                                red = '".$_SESSION["id_red"]."'";
                        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
                        if ($mostrar_todos_sensores == false)
                        {
                            $consulta_origenes .=
                                "AND ".dame_condicion_consulta_grupos_sensores_usuario_actual(false);
                        }
                        $consulta_origenes .= "
                            ORDER BY nombre ASC";
                        break;
                    }
                }
                break;
            }
            case CAUSA_SUCESO_REGLA:
            {
                $consulta_origenes = "";
                break;
            }
        }

        $lista .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        if ($consulta_origenes != "")
        {
            $res_origenes = $bd_red->ejecuta_consulta($consulta_origenes);
            if ($res_origenes == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_origenes."'");
            }

            while ($fila_origen = $res_origenes->dame_siguiente_fila())
            {
                $lista .= "<option value='".$fila_origen['id']."'";
                if ($fila_origen['id'] == $id_origen_seleccionado)
                {
                    $lista .= " selected";
                }
                $lista .= ">".$fila_origen['nombre']."</option>";
            }
        }

        return ($lista);
    }


    function dame_lista_modos_activacion_suceso_regla($modo_activacion_seleccionado)
    {
        $modos_activacion_suceso = SucesoRegla::dame_modos_activacion_suceso();

        $lista = "";
        foreach ($modos_activacion_suceso as $modo_activacion_suceso)
        {
            $lista .= "<option value='".$modo_activacion_suceso."'";
			if ($modo_activacion_suceso == $modo_activacion_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".SucesoRegla::dame_descripcion_modo_activacion_suceso($modo_activacion_suceso)."</option>";
        }

        return ($lista);
    }


    function dame_lista_periodos_tiempo_activacion_suceso_regla($periodo_tiempo_activacion_seleccionado)
    {
        $periodos_tiempo_activacion_suceso = SucesoRegla::dame_periodos_tiempo_activacion_suceso();

        $lista = "";
        foreach ($periodos_tiempo_activacion_suceso as $periodo_tiempo_activacion_suceso)
        {
            $lista .= "<option value='".$periodo_tiempo_activacion_suceso."'";
			if ($periodo_tiempo_activacion_suceso == $periodo_tiempo_activacion_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".dame_descripcion_periodo_tiempo($periodo_tiempo_activacion_suceso)."</option>";
        }

        return ($lista);
    }


    function dame_lista_numero_activaciones_suceso_regla($origen, $id_origen, $numero_activaciones)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Tipos de origenes del suceso
        switch ($origen)
        {
            case ORIGEN_SUCESO_SENSOR:
            {
                $numero_maximo_activaciones = 1;
                break;
            }
            case ORIGEN_SUCESO_GRUPO_SENSORES:
            {
                // Si no hay grupo o el grupo no tiene sensores se añade la opción 1
                if ($id_origen == ID_NINGUNO)
                {
                    $numero_maximo_activaciones = 1;
                }
                else
                {
                    $consulta_sensores = "
                        SELECT
                            COUNT(*) AS numero_sensores
                        FROM sensores
                        WHERE
                            sensores.grupo = '".$bd_red->_($id_origen)."'";
                    $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
                    if (($res_sensores == false) || ($res_sensores->dame_numero_filas() == 0))
                    {
                        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensores."'");
                    }
                    $fila_sensores = $res_sensores->dame_siguiente_fila();
                    $numero_sensores = $fila_sensores['numero_sensores'];

                    $numero_maximo_activaciones = $numero_sensores;
                    if ($numero_maximo_activaciones == 0)
                    {
                        $numero_maximo_activaciones = 1;
                    }
                }
                break;
            }
            default:
            {
                $numero_maximo_activaciones = 1;
                break;
            }
        }

        $lista = "";
        for ($i = 1; $i <= $numero_maximo_activaciones; $i++)
        {
            $lista .= "<option value='".$i."'";
			if ($i == $numero_activaciones)
			{
				$lista .= " selected";
			}
			$lista .= ">".$i."</option>";
        }
        if ($origen == ORIGEN_SUCESO_GRUPO_SENSORES)
        {
            $lista .= "<option value='".NUMERO_ACTIVACIONES_SUCESO_TODOS_SENSORES_GRUPO."'";
            if ($numero_activaciones == NUMERO_ACTIVACIONES_SUCESO_TODOS_SENSORES_GRUPO)
            {
                $lista .= " selected";
            }
            $lista .= ">".$idiomas->_("Todos")."</option>";
        }

        return ($lista);
    }


    //
    // Funciones para detección de bucles en los sucesos de reglas con causa regla
    //


    // Carga la información de las reglas hijas (sucesos de reglas con causa regla)
	function carga_informacion_reglas_hijas(&$info_reglas_hijas)
	{
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_sucesos_reglas = "
            SELECT
                regla,
                id_causa
            FROM
                sucesos_reglas
            WHERE
                causa = '".CAUSA_SUCESO_REGLA."'";
        $res_sucesos_reglas = $bd_red->ejecuta_consulta($consulta_sucesos_reglas);
        if ($res_sucesos_reglas == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sucesos_reglas."'");
        }

        $info_reglas_hijas = array();
        while ($fila_suceso_regla = $res_sucesos_reglas->dame_siguiente_fila())
        {
            $id_regla_padre = $fila_suceso_regla["regla"];
            $id_regla_hija = $fila_suceso_regla["id_causa"];

            // Se añade la información de la regla hija
            anyade_regla_hija($info_reglas_hijas, $id_regla_padre, $id_regla_hija);
        }
    }


    function anyade_regla_hija(&$info_reglas_hijas, $id_regla_padre, $id_regla_hija)
	{
        if (array_key_exists($id_regla_padre, $info_reglas_hijas) == False)
        {
            $info_reglas_hijas[$id_regla_padre] = array();
        }
        array_push($info_reglas_hijas[$id_regla_padre], $id_regla_hija);
    }


    function elimina_regla_hija(&$info_reglas_hijas, $id_regla_padre, $id_regla_hija)
	{
        $key = array_search($id_regla_hija, $info_reglas_hijas[$id_regla_padre]);
        if (($key !== false) && ($key !== NULL))
        {
            unset($info_reglas_hijas[$id_regla_padre][$key]);
            if (count($info_reglas_hijas[$id_regla_padre]) == 0)
            {
                unset($info_reglas_hijas[$id_regla_padre]);
            }
        }
    }


    function existe_bucle_reglas_hijas($info_reglas_hijas)
	{
        // Se comprueba si existe un bucle en cada una de las reglas padres
        foreach ($info_reglas_hijas as $id_regla_padre => $ids_reglas_hijas)
        {
            if (existe_regla_hija($info_reglas_hijas, $id_regla_padre, $ids_reglas_hijas, array()) == True)
            {
                return (True);
            }
        }

        return False;
    }


    function existe_regla_hija($info_reglas_hijas, $id_regla, $ids_reglas_hijas, $ids_reglas_hijas_visitadas)
	{
        if (in_array($id_regla, $ids_reglas_hijas) == True)
        {
            return (True);
        }
        else
        {
            foreach ($ids_reglas_hijas as $id_regla_hija)
            {
                if (in_array($id_regla_hija, $ids_reglas_hijas_visitadas) == true)
                {
                    continue;
                }
                else
                {
                    array_push($ids_reglas_hijas_visitadas, $id_regla_hija);
                }
                if (existe_regla_hija($info_reglas_hijas, $id_regla, $info_reglas_hijas[$id_regla_hija], $ids_reglas_hijas_visitadas) == True)
                {
                    return (True);
                }
            }

            return (False);
        }
    }


    //
    // Funciones de obtención de información de sucesos
    //


    function dame_fila_suceso_regla($id_suceso_regla)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_suceso_regla = "
            SELECT *
            FROM sucesos_reglas
            WHERE
                id = '".$bd_red->_($id_suceso_regla)."'";
        $res_suceso_regla = $bd_red->ejecuta_consulta($consulta_suceso_regla);
        if (($res_suceso_regla == false) || ($res_suceso_regla->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_suceso_regla."'");
        }
        $fila_suceso_regla = $res_suceso_regla->dame_siguiente_fila();
        return ($fila_suceso_regla);
    }
?>