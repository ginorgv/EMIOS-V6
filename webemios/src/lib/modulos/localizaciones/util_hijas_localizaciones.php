<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_arboles.php');


    // Carga la información de las localizaciones padres e hijas
	function carga_informacion_localizaciones_padres_hijas(&$info_localizaciones_padres, &$info_localizaciones_hijas, $recargar_informacion = false)
	{
        if ($recargar_informacion == true)
        {
            unset($GLOBALS['info_localizaciones_padres_hijas']);
        }
        if (!isset($GLOBALS['info_localizaciones_padres_hijas']))
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta_hijas_localizaciones = "
                SELECT
                    hijas_localizaciones.localizacion_padre,
                    hijas_localizaciones.localizacion_hija
                FROM
                    hijas_localizaciones,
                    localizaciones
                WHERE
                    (hijas_localizaciones.localizacion_padre = localizaciones.id)
                    AND (localizaciones.red = '".$_SESSION["id_red"]."')
                ORDER BY
                    hijas_localizaciones.localizacion_padre ASC,
                    hijas_localizaciones.localizacion_hija ASC";
            $res_hijas_localizaciones = $bd_red->ejecuta_consulta($consulta_hijas_localizaciones);
            if ($res_hijas_localizaciones == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_hijas_localizaciones."'");
            }

            $info_localizaciones_padres = array();
            $info_localizaciones_hijas = array();
            while ($fila_hija_localizacion = $res_hijas_localizaciones->dame_siguiente_fila())
            {
                $id_localizacion_padre = $fila_hija_localizacion["localizacion_padre"];
                $id_localizacion_hija = $fila_hija_localizacion["localizacion_hija"];

                // Se añade la información de localizaciones padres y de localizaciones hijas
                anyade_nodo_hijo($info_localizaciones_padres, $id_localizacion_hija, $id_localizacion_padre);
                anyade_nodo_hijo($info_localizaciones_hijas, $id_localizacion_padre, $id_localizacion_hija);
            }

            $GLOBALS['info_localizaciones_padres_hijas'] = array(
                "info_localizaciones_padres" => $info_localizaciones_padres,
                "info_localizaciones_hijas" => $info_localizaciones_hijas);
        }
        else
        {
            $info_localizaciones_padres = $GLOBALS['info_localizaciones_padres_hijas']["info_localizaciones_padres"];
            $info_localizaciones_hijas = $GLOBALS['info_localizaciones_padres_hijas']["info_localizaciones_hijas"];
        }
    }


    //
    // Funciones de administración de localizaciones hijas
    //


    function anyade_localizacion_padre(&$info_localizaciones_padres, $id_localizacion_padre, $id_localizacion_hija)
	{
        anyade_nodo_hijo($info_localizaciones_padres, $id_localizacion_hija, $id_localizacion_padre);
    }


    function elimina_localizacion_padre(&$info_localizaciones_padres, $id_localizacion_padre, $id_localizacion_hija)
	{
        elimina_nodo_hijo($info_localizaciones_padres, $id_localizacion_hija, $id_localizacion_padre);
    }


    function anyade_localizacion_hija(&$info_localizaciones_hijas, $id_localizacion_padre, $id_localizacion_hija)
	{
        anyade_nodo_hijo($info_localizaciones_hijas, $id_localizacion_padre, $id_localizacion_hija);
    }


    function elimina_localizacion_hija(&$info_localizaciones_hijas, $id_localizacion_padre, $id_localizacion_hija)
	{
        elimina_nodo_hijo($info_localizaciones_hijas, $id_localizacion_padre, $id_localizacion_hija);
    }


    function existe_bucle_localizaciones_hijas($info_localizaciones_hijas)
	{
        return (existe_bucle_nodos_hijos($info_localizaciones_hijas));
    }


    function carga_ordenes_localizaciones_padres_hijas(&$ordenes_localizaciones)
	{
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_ordenes_localizaciones = "
            SELECT
                id,
                orden
            FROM localizaciones
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_ordenes_localizaciones = $bd_red->ejecuta_consulta($consulta_ordenes_localizaciones);
        if ($res_ordenes_localizaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_ordenes_localizaciones."'");
        }

        $ordenes_localizaciones = array();
        while ($fila_orden_localizacion = $res_ordenes_localizaciones->dame_siguiente_fila())
        {
            $id_localizacion = $fila_orden_localizacion["id"];
            $orden = $fila_orden_localizacion["orden"];

            anyade_orden_nodo($ordenes_localizaciones, $id_localizacion, $orden);
        }
    }


    function actualiza_orden_localizaciones_ascendientes(
        $info_localizaciones_padres,
        $info_localizaciones_hijas,
        &$ordenes_localizaciones,
        $id_localizacion)
	{
        $bd_red = BaseDatosRed::dame_base_datos();

        // Órdenes anteriores
        $ordenes_localizaciones_anteriores = $ordenes_localizaciones;

        // Se actualiza el orden de la localización y recursivamente el de sus padres
        actualiza_orden_nodos_ascendientes(
            $info_localizaciones_padres,
            $info_localizaciones_hijas,
            $ordenes_localizaciones,
            $id_localizacion);

        // Se recorren los órdenes anteriores y actuales, se modifican en base de datos los que hayan cambiado
        $numero_localizaciones_actualizadas = 0;
        foreach ($ordenes_localizaciones as $id_localizacion => $orden)
        {
            $orden_anterior = $ordenes_localizaciones_anteriores[$id_localizacion];
            if ($orden_anterior <> $orden)
            {
                $operacion_modificacion = "
                    UPDATE localizaciones
                    SET
                        orden = '".$bd_red->_($orden)."'
                    WHERE
                        id = '".$bd_red->_($id_localizacion)."'";
                $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
                if ($res_modificacion == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_modificacion."'");
                }

                $numero_localizaciones_actualizadas += 1;
            }
        }

        // Se devuelve el número de localizaciones actualizadas
        return ($numero_localizaciones_actualizadas);
    }


    //
    // Funciones de obtención de localizaciones ascendientes y descendientes
    //


    // Devuelve los identificadores de las localizaciones ascendientes de las localizaciones especificadas
    function dame_ids_localizaciones_ascendientes_localizaciones($info_localizaciones_padres, $ids_localizaciones_hijas, $incluir_localizaciones_hijas)
	{
        $nodos_visitados = array();
        $ids_localizaciones_ascendientes = dame_nodos_descendientes_nodos($info_localizaciones_padres, $ids_localizaciones_hijas, $nodos_visitados);
        if ($incluir_localizaciones_hijas == false)
        {
            $ids_localizaciones_ascendientes = array_diff($ids_localizaciones_ascendientes, $ids_localizaciones_hijas);
        }
        return ($ids_localizaciones_ascendientes);
    }


    // Devuelve los identificadores de las localizaciones descendientes de las localizaciones especificadas
    function dame_ids_localizaciones_descendientes_localizaciones($info_localizaciones_hijas, $ids_localizaciones_padres, $incluir_localizaciones_padres)
	{
        $nodos_visitados = array();
        $ids_localizaciones_descendientes = dame_nodos_descendientes_nodos($info_localizaciones_hijas, $ids_localizaciones_padres, $nodos_visitados);
        if ($incluir_localizaciones_padres == false)
        {
            $ids_localizaciones_descendientes = array_diff($ids_localizaciones_descendientes, $ids_localizaciones_padres);
        }
        return ($ids_localizaciones_descendientes);
    }


    // Devuelve los identificadores de las las localizaciones y los grados ascendientes de las localizaciones especificadas
    function dame_ids_localizaciones_grados_ascendientes_localizaciones($info_localizaciones_padres, $ids_localizaciones_hijas, $incluir_localizaciones_hijas)
	{
        $nodos_visitados = array();
        $ids_localizaciones_grados_ascendientes = dame_nodos_grados_descendientes_nodos($info_localizaciones_padres, $ids_localizaciones_hijas, $nodos_visitados, 0);
        if ($incluir_localizaciones_hijas == false)
        {
            foreach ($ids_localizaciones_hijas as $id_localizacion_hija)
            {
                unset($ids_localizaciones_grados_ascendientes[$id_localizacion_hija]);
            }
        }
        return ($ids_localizaciones_grados_ascendientes);
    }


    // Devuelve los identificadores de las las localizaciones y los grados descendientes de las localizaciones especificadas
    function dame_ids_localizaciones_grados_descendientes_localizaciones($info_localizaciones_hijas, $ids_localizaciones_padres, $incluir_localizaciones_padres)
	{
        $nodos_visitados = array();
        $ids_localizaciones_grados_descendientes = dame_nodos_grados_descendientes_nodos($info_localizaciones_hijas, $ids_localizaciones_padres, $nodos_visitados, 0);
        if ($incluir_localizaciones_padres == false)
        {
            foreach ($ids_localizaciones_padres as $id_localizacion_padre)
            {
                unset($ids_localizaciones_grados_descendientes[$id_localizacion_padre]);
            }
        }
        return ($ids_localizaciones_grados_descendientes);
    }


    // Devuelve si existe la localización en las localizaciones ascendientes de la localización hija
    function existe_localizacion_ascendiente($info_localizaciones_padres, $id_localizacion, $id_localizacion_hija)
	{
        $ids_localizaciones_padres_visitadas = array();
        $existe_localizacion_ascendiente = existe_nodo_hijo(
            $info_localizaciones_padres,
            $id_localizacion,
            $info_localizaciones_padres[$id_localizacion_hija],
            $ids_localizaciones_padres_visitadas);
        return ($existe_localizacion_ascendiente);
    }


    // Devuelve si existe la localización en las localizaciones descendientes de la localización padre
    function existe_localizacion_descendiente($info_localizaciones_hijas, $id_localizacion, $id_localizacion_padre)
	{
        $ids_localizaciones_hijas_visitadas = array();
        $existe_localizacion_descendiente = existe_nodo_hijo(
            $info_localizaciones_hijas,
            $id_localizacion,
            $info_localizaciones_hijas[$id_localizacion_padre],
            $ids_localizaciones_hijas_visitadas);
        return ($existe_localizacion_descendiente);
    }


    //
    // Funciones de listas de localizaciones hijas
    //


    // Devuelve la lista de localizaciones hijas
    function dame_lista_localizaciones_hijas($id_localizacion_padre, $id_localizacion_hija)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_localizaciones = "
            SELECT
                id,
                nombre
            FROM localizaciones
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (id <> '".$bd_red->_($id_localizacion_padre)."')";
        $mostrar_todas_localizaciones = dame_mostrar_todas_localizaciones();
        if ($mostrar_todas_localizaciones == false)
        {
            $consulta_localizaciones .= "
                AND (".dame_condicion_consulta_localizaciones_usuario_actual();
            if ($id_localizacion_hija != "")
            {
                $consulta_localizaciones .= " OR (localizaciones.id = '".$bd_red->_($id_localizacion_hija)."')";
            }
            $consulta_localizaciones .= ")";
        }
        $consulta_localizaciones .= "
            ORDER BY nombre ASC";
        $res_localizaciones = $bd_red->ejecuta_consulta($consulta_localizaciones);
        if ($res_localizaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_localizaciones."'");
        }

        $lista_localizaciones = "";
        $lista_localizaciones .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        while ($fila_localizacion = $res_localizaciones->dame_siguiente_fila())
        {
            $lista_localizaciones .= "<option value='".$fila_localizacion['id']."'";
			if ($fila_localizacion['id'] == $id_localizacion_hija)
			{
				$lista_localizaciones .= " selected";
			}
			$lista_localizaciones .= ">".htmlspecialchars($fila_localizacion['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_localizaciones);
    }


    //
    // Funciones de obtención de información de hijas de localizaciones
    //


    function dame_fila_hija_localizacion($id_hija_localizacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_hija_localizacion = "
            SELECT *
            FROM hijas_localizaciones
            WHERE
                id = '".$bd_red->_($id_hija_localizacion)."'";
        $res_hija_localizacion = $bd_red->ejecuta_consulta($consulta_hija_localizacion);
        if (($res_hija_localizacion == false) || ($res_hija_localizacion->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_hija_localizacion."'");
        }
        $fila_hija_localizacion = $res_hija_localizacion->dame_siguiente_fila();
        return ($fila_hija_localizacion);
    }
?>
