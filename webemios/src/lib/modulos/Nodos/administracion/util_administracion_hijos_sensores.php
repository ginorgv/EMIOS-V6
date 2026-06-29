<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_arboles.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    // Carga la información de los sensores padres e hijos del tipo especificado
	function carga_informacion_sensores_padres_hijos($tipo_sensor, $id_red, &$info_sensores_padres, &$info_sensores_hijos)
	{
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan los sensores del tipo especificado
        $consulta_sensores_tipo = "
            SELECT id
            FROM sensores";
        $filtro_consulta_sensores_tipo = "";
        if ($tipo_sensor != TIPO_TODOS)
        {
            $filtro_consulta_sensores_tipo .= " WHERE (tipo = '".$bd_red->_($tipo_sensor)."')";
        }
        if ($id_red != ID_TODOS)
        {
            if ($filtro_consulta_sensores_tipo == "")
            {
                $filtro_consulta_sensores_tipo .= " WHERE (red = '".$bd_red->_($id_red)."')";
            }
            else
            {
                $filtro_consulta_sensores_tipo .= " AND (red = '".$bd_red->_($id_red)."')";
            }
        }
        $consulta_sensores_tipo .= $filtro_consulta_sensores_tipo;
        $consulta_sensores_tipo .= "
            ORDER BY
                id";
        $res_sensores_tipo = $bd_red->ejecuta_consulta($consulta_sensores_tipo);
        if ($res_sensores_tipo == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores_tipo."'");
        }

        $ids_sensores_tipo = array();
        while ($fila_sensor_tipo = $res_sensores_tipo->dame_siguiente_fila())
        {
            $id_sensor_tipo = $fila_sensor_tipo["id"];
            array_push($ids_sensores_tipo, $id_sensor_tipo);
        }

        // Se recupera y carga la información de sensores padres e hijos
        $consulta_hijos_sensores = "
            SELECT
                hijos_sensores.sensor_padre,
                hijos_sensores.sensor_hijo
            FROM
                hijos_sensores,
                sensores
            WHERE
                (hijos_sensores.sensor_padre = sensores.id)";
        if ($tipo_sensor != TIPO_TODOS)
        {
            $consulta_hijos_sensores .= "
                AND (sensores.tipo = '".$bd_red->_($tipo_sensor)."')";
        }
        if ($id_red != ID_TODOS)
        {
            $consulta_hijos_sensores .= "
                AND (sensores.red = '".$_SESSION["id_red"]."')";
        }
        $consulta_hijos_sensores .= "
            ORDER BY
                hijos_sensores.sensor_padre ASC,
                hijos_sensores.sensor_hijo ASC";
        $res_hijos_sensores = $bd_red->ejecuta_consulta($consulta_hijos_sensores);
        if ($res_hijos_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_hijos_sensores."'");
        }

        $info_sensores_padres = array();
        $info_sensores_hijos = array();
        while ($fila_hijo_sensor = $res_hijos_sensores->dame_siguiente_fila())
        {
            $id_sensor_padre = $fila_hijo_sensor["sensor_padre"];
            $id_sensor_hijo = $fila_hijo_sensor["sensor_hijo"];

            // Si el tipo del sensor hijo no es el mismo tipo que el sensor padre, no se tiene en cuenta para los órdenes
            if (in_array($id_sensor_hijo, $ids_sensores_tipo) == false)
            {
                continue;
            }

            // Se añade la información de sensores padres y de sensores hijos
            anyade_nodo_hijo($info_sensores_padres, $id_sensor_hijo, $id_sensor_padre);
            anyade_nodo_hijo($info_sensores_hijos, $id_sensor_padre, $id_sensor_hijo);
        }
    }


    //
    // Funciones de administración de sensores hijos
    //


    function anyade_sensor_padre(&$info_sensores_padres, $id_sensor_padre, $id_sensor_hijo)
	{
        anyade_nodo_hijo($info_sensores_padres, $id_sensor_hijo, $id_sensor_padre);
    }


    function elimina_sensor_padre(&$info_sensores_padres, $id_sensor_padre, $id_sensor_hijo)
	{
        elimina_nodo_hijo($info_sensores_padres, $id_sensor_hijo, $id_sensor_padre);
    }


    function anyade_sensor_hijo(&$info_sensores_hijos, $id_sensor_padre, $id_sensor_hijo)
	{
        anyade_nodo_hijo($info_sensores_hijos, $id_sensor_padre, $id_sensor_hijo);
    }


    function elimina_sensor_hijo(&$info_sensores_hijos, $id_sensor_padre, $id_sensor_hijo)
	{
        elimina_nodo_hijo($info_sensores_hijos, $id_sensor_padre, $id_sensor_hijo);
    }


    function existe_bucle_sensores_hijos($info_sensores_hijos)
	{
        return (existe_bucle_nodos_hijos($info_sensores_hijos));
    }


    function carga_ordenes_sensores_padres_hijos($tipo_sensor, $id_red, &$ordenes_sensores)
	{
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_ordenes_sensores = "
            SELECT
                id,
                orden
            FROM sensores
            WHERE
                (tipo = '".$bd_red->_($tipo_sensor)."')";
        if ($id_red != ID_TODOS)
        {
            $consulta_ordenes_sensores .= "
                AND (red = '".$bd_red->_($id_red)."')";
        }
        $res_ordenes_sensores = $bd_red->ejecuta_consulta($consulta_ordenes_sensores);
        if ($res_ordenes_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_ordenes_sensores."'");
        }

        $ordenes_sensores = array();
        while ($fila_orden_sensor = $res_ordenes_sensores->dame_siguiente_fila())
        {
            $id_sensor = $fila_orden_sensor["id"];
            $orden = $fila_orden_sensor["orden"];

            anyade_orden_nodo($ordenes_sensores, $id_sensor, $orden);
        }
    }


    function actualiza_orden_sensores_ascendientes(
        $info_sensores_padres,
        $info_sensores_hijos,
        &$ordenes_sensores,
        $id_sensor)
	{
        $bd_red = BaseDatosRed::dame_base_datos();

        // Órdenes anteriores
        $ordenes_sensores_anteriores = $ordenes_sensores;

        // Se actualiza el orden del sensor y recursivamente el de sus padres
        actualiza_orden_nodos_ascendientes(
            $info_sensores_padres,
            $info_sensores_hijos,
            $ordenes_sensores,
            $id_sensor);

        // Se recorren los órdenes anteriores y actuales, se modifican en base de datos los que hayan cambiado
        foreach ($ordenes_sensores as $id_sensor => $orden)
        {
            $orden_anterior = $ordenes_sensores_anteriores[$id_sensor];
            if ($orden_anterior <> $orden)
            {
                $operacion_modificacion = "
                    UPDATE sensores
                    SET
                        orden = '".$bd_red->_($orden)."'
                    WHERE
                        id = '".$bd_red->_($id_sensor)."'";
                $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
                if ($res_modificacion == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_modificacion."'");
                }
            }
        }
    }


    //
    // Funciones de obtención de sensores descendientes
    //


    // Devuelve los identificadores de los sensores descendientes de los sensores especificados especificadas
    function dame_ids_sensores_descendientes_sensores($info_sensores_hijos, $ids_sensores_padres, $incluir_sensores_padres)
	{
        $nodos_visitados = array();
        $ids_sensores_descendientes = dame_nodos_descendientes_nodos($info_sensores_hijos, $ids_sensores_padres, $nodos_visitados);
        if ($incluir_sensores_padres == false)
        {
            $ids_sensores_descendientes = array_diff($ids_sensores_descendientes, $ids_sensores_padres);
        }
        return ($ids_sensores_descendientes);
    }


    //
    // Funciones de controles de sensores hijos
    //


    // Devuelve los controles de parámetros de la función de hijo de sensor de procesado especificada
    function dame_controles_parametros_funcion_hijo_sensor_procesado($id_hijo_sensor, $funcion_hijo_sensor_procesado, $parametros_funcion)
    {
        switch ($funcion_hijo_sensor_procesado)
        {
            case FUNCION_HIJO_SENSOR_PROCESADO_IDENTIDAD:
            {
                $controles = dame_controles_parametros_funcion_hijo_sensor_procesado_identidad($id_hijo_sensor, $parametros_funcion);
                break;
            }
            case FUNCION_HIJO_SENSOR_PROCESADO_MEDIA:
            {
                $controles = dame_controles_parametros_funcion_hijo_sensor_procesado_media($id_hijo_sensor, $parametros_funcion);
                break;
            }
            case FUNCION_HIJO_SENSOR_PROCESADO_DESVIACION_ESTANDAR:
            {
                $controles = dame_controles_parametros_funcion_hijo_sensor_procesado_desviacion_estandar($id_hijo_sensor, $parametros_funcion);
                break;
            }
            case FUNCION_HIJO_SENSOR_PROCESADO_ACUMULADO:
            {
                $controles = dame_controles_parametros_funcion_hijo_sensor_procesado_acumulado($id_hijo_sensor, $parametros_funcion);
                break;
            }
            case FUNCION_HIJO_SENSOR_PROCESADO_INCREMENTO:
            {
                $controles = dame_controles_parametros_funcion_hijo_sensor_procesado_incremento($id_hijo_sensor, $parametros_funcion);
                break;
            }
            default:
            {
                break;
            }
        }
        return ($controles);
    }


    // Devuelve los controles de parámetros de la función de hijo de sensor de procesado 'Identidad'
    function dame_controles_parametros_funcion_hijo_sensor_procesado_identidad($id_hijo_sensor, $parametros_funcion)
    {
        return ("");
    }


    // Devuelve los controles de parámetros de la función de hijo de sensor de procesado 'Media'
    function dame_controles_parametros_funcion_hijo_sensor_procesado_media($id_hijo_sensor, $parametros_funcion)
    {
        $idiomas = new Idiomas();

        if ($id_hijo_sensor != ID_NINGUNO)
        {
            $parametros_funcion = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_funcion);
            $numero_horas_funcion = $parametros_funcion[0];
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de horas").": "."</span><br/>
                    <input type='text' id='numero_horas_funcion_hijo_sensor_procesado_media'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_horas_funcion."'>
                </div>
            </div>";

        return ($controles);
    }


    // Devuelve los controles de parámetros de la función de hijo de sensor de procesado 'Desviación estándar'
    function dame_controles_parametros_funcion_hijo_sensor_procesado_desviacion_estandar($id_hijo_sensor, $parametros_funcion)
    {
        $idiomas = new Idiomas();

        if ($id_hijo_sensor != ID_NINGUNO)
        {
            $parametros_funcion = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_funcion);
            $numero_horas_funcion = $parametros_funcion[0];
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de horas").": "."</span><br/>
                    <input type='text' id='numero_horas_funcion_hijo_sensor_procesado_desviacion_estandar'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_horas_funcion."'>
                </div>
            </div>";

        return ($controles);
    }


    // Devuelve los controles de parámetros de la función de hijo de sensor de procesado 'Acumulado'
    function dame_controles_parametros_funcion_hijo_sensor_procesado_acumulado($id_hijo_sensor, $parametros_funcion)
    {
        $idiomas = new Idiomas();

        if ($id_hijo_sensor != ID_NINGUNO)
        {
            $parametros_funcion = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_funcion);
            $numero_horas_funcion = $parametros_funcion[0];
        }

        $controles = "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de horas").": "."</span><br/>
                    <input type='text' id='numero_horas_funcion_hijo_sensor_procesado_acumulado'
                        class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_horas_funcion."'>
                </div>
            </div>";

        return ($controles);
    }


    // Devuelve los controles de parámetros de la función de hijo de sensor de procesado 'Incremento'
    function dame_controles_parametros_funcion_hijo_sensor_procesado_incremento($id_hijo_sensor, $parametros_funcion)
    {
        return ("");
    }


    // Devuelve la lista de sensores hijos (para administración)
    function dame_lista_sensores_hijos_administracion(
        $id_sensor_padre,
        $tipo_sensor_padre,
        $id_sensor_hijo,
        $clase_sensores_hijos)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_sensores = "
            SELECT
                id,
                nombre
            FROM sensores
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (id <> '".$bd_red->_($id_sensor_padre)."')";
        switch ($tipo_sensor_padre)
        {
            case TIPO_SENSOR_VIRTUAL:
            {
                $consulta_sensores .= "
                    AND ((tipo = '".$bd_red->_(TIPO_SENSOR_REAL)."') OR (tipo = '".$bd_red->_(TIPO_SENSOR_VIRTUAL)."'))";
                break;
            }
        }
        if ($clase_sensores_hijos != CLASE_TODAS)
        {
            $consulta_sensores .= "
                AND (clase = '".$bd_red->_($clase_sensores_hijos)."')";
        }
        if (dame_mostrar_todos_sensores() == false)
        {
            $consulta_sensores .= "
                AND ".dame_condicion_consulta_sensores_usuario_actual(true);
        }
        $consulta_sensores .= "
            ORDER BY nombre ASC";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }

        $lista_sensores = "";
        $lista_sensores .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        while ($fila_sensor = $res_sensores->dame_siguiente_fila())
        {
            $lista_sensores .= "<option value='".$fila_sensor['id']."'";
			if ($fila_sensor['id'] == $id_sensor_hijo)
			{
				$lista_sensores .= " selected";
			}
			$lista_sensores .= ">".htmlspecialchars($fila_sensor['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_sensores);
    }


    //
    // Funciones auxiliares
    //


    // Actualiza los órdenes de todos los sensores del tipo especificado
    function actualiza_ordenes_sensores_tipo($tipo_sensor)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_sensores = "
            SELECT
                id
            FROM sensores
            WHERE
                tipo = '".$tipo_sensor."'
            ORDER BY id ASC";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }
        while ($fila_sensor = $res_sensores->dame_siguiente_fila())
        {
            $id_sensor = $fila_sensor["id"];

            $info_sensores_padres = NULL;
            $info_sensores_hijos = NULL;
            carga_informacion_sensores_padres_hijos($tipo_sensor, ID_TODOS, $info_sensores_padres, $info_sensores_hijos);

            $ordenes_sensores = NULL;
            carga_ordenes_sensores_padres_hijos($tipo_sensor, ID_TODOS, $ordenes_sensores);
            actualiza_orden_sensores_ascendientes($info_sensores_padres, $info_sensores_hijos, $ordenes_sensores, $id_sensor);
        }
    }
?>
