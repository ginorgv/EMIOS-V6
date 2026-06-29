<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');


    //
    // Funciones de árboles (nodos)
    //


    //
    // Creación de árboles
    //


    function anyade_nodo_hijo(&$info_nodos_hijos, $nodo_padre, $nodo_hijo)
	{
        if (array_key_exists($nodo_padre, $info_nodos_hijos) == False)
        {
            $info_nodos_hijos[$nodo_padre] = array();
        }
        array_push($info_nodos_hijos[$nodo_padre], $nodo_hijo);
    }


    function elimina_nodo_hijo(&$info_nodos_hijos, $nodo_padre, $nodo_hijo)
	{
        $key = array_search($nodo_hijo, $info_nodos_hijos[$nodo_padre]);
        if (($key !== false) && ($key !== NULL))
        {
            unset($info_nodos_hijos[$nodo_padre][$key]);
            if (count($info_nodos_hijos[$nodo_padre]) == 0)
            {
                unset($info_nodos_hijos[$nodo_padre]);
            }
        }
    }


    //
    // Comprobación de bucles en los nodos
    //


    function existe_bucle_nodos_hijos($info_nodos_hijos)
	{
        // Se comprueba si existe un bucle en cada uno de los nodos padres
        foreach ($info_nodos_hijos as $nodo_padre => $nodos_hijos)
        {
            $nodos_hijos_visitados = array();
            if (existe_nodo_hijo($info_nodos_hijos, $nodo_padre, $nodos_hijos, $nodos_hijos_visitados) == True)
            {
                return (True);
            }
        }

        return (False);
    }


    function existe_nodo_hijo(
        $info_nodos_hijos,
        $nodo,
        $nodos_hijos,
        $nodos_hijos_visitados)
	{
        if (in_array($nodo, $nodos_hijos) == true)
        {
            return (True);
        }
        else
        {
            foreach ($nodos_hijos as $nodo_hijo)
            {
                if (in_array($nodo_hijo, $nodos_hijos_visitados) == true)
                {
                    continue;
                }
                else
                {
                    array_push($nodos_hijos_visitados, $nodo_hijo);
                }
                if (existe_nodo_hijo($info_nodos_hijos, $nodo, $info_nodos_hijos[$nodo_hijo], $nodos_hijos_visitados) == True)
                {
                    return (True);
                }
            }

            return (False);
        }
    }


    //
    // Obtención de nodos ascendientes y descendientes
    //


    function dame_nodos_descendientes_nodos($info_nodos_hijos, $nodos, &$nodos_visitados)
	{
        $nodos_descendientes_nodos = array();
        foreach ($nodos as $nodo)
        {
            if (in_array($nodo, $nodos_visitados) == true)
            {
                continue;
            }
            else
            {
                array_push($nodos_visitados, $nodo);
            }

            if (array_key_exists($nodo, $info_nodos_hijos) == True)
            {
                $nodos_descendientes_nodo = dame_nodos_descendientes_nodos($info_nodos_hijos, $info_nodos_hijos[$nodo], $nodos_visitados);
            }
            else
            {
                $nodos_descendientes_nodo = array();
            }
            array_push($nodos_descendientes_nodo, $nodo);

            // Se añaden los nodos descendientes del nodo actual a los nodos descendientes de los nodos
            foreach ($nodos_descendientes_nodo as $nodo)
            {
                array_push($nodos_descendientes_nodos, $nodo);
            }
        }

        return ($nodos_descendientes_nodos);
    }


    function dame_nodos_grados_descendientes_nodos(
        $info_nodos_hijos,
        $nodos,
        &$nodos_visitados,
        $grado)
	{
        $nodos_descendientes_nodos = array();
        foreach ($nodos as $nodo)
        {
            if (array_key_exists($nodo, $nodos_visitados) == true)
            {
                $grado_actual = $nodos_visitados[$nodo];
                if ($grado >= $grado_actual)
                {
                    continue;
                }
            }
            else
            {
                $nodos_visitados[$nodo] = $grado;
            }

            if (array_key_exists($nodo, $info_nodos_hijos) == True)
            {
                $nodos_descendientes_nodo = dame_nodos_grados_descendientes_nodos($info_nodos_hijos, $info_nodos_hijos[$nodo], $nodos_visitados, $grado + 1);
            }
            else
            {
                $nodos_descendientes_nodo = array();
            }
            $nodos_descendientes_nodo[$nodo] = $grado;

            // Se añaden los nodos descendientes del nodo actual a los nodos descendientes de los nodos
            foreach ($nodos_descendientes_nodo as $nodo => $grado)
            {
                $nodos_descendientes_nodos[$nodo] = $grado;
            }
        }

        return ($nodos_descendientes_nodos);
    }


    //
    // Órdenes de los nodos
    //


    function anyade_orden_nodo(&$ordenes_nodos, $nodo, $orden)
	{
        $ordenes_nodos[$nodo] = $orden;
    }


    function actualiza_orden_nodos_ascendientes(
        $info_nodos_padres,
        $info_nodos_hijos,
        &$ordenes_nodos,
        $nodo)
	{
        // Se actualiza el orden del nodo (el máximo orden de sus hijos más 1)
        if (array_key_exists($nodo, $info_nodos_hijos) == True)
        {
            $nodos_hijos = $info_nodos_hijos[$nodo];
            $max_orden_hijo = -1;
            foreach ($nodos_hijos as $nodo_hijo)
            {
                // Nota: El nodo hijo siempre debería estar en los órdenes de los nodos
                if (array_key_exists($nodo_hijo, $ordenes_nodos) == True)
                {
                    $orden_nodo_hijo = $ordenes_nodos[$nodo_hijo];
                    if ($orden_nodo_hijo > $max_orden_hijo)
                    {
                        $max_orden_hijo = $orden_nodo_hijo;
                    }
                }
                else
                {
                    throw new Exception("El nodo hijo: ".$nodo_hijo." no tiene orden");
                }
            }
            $orden_nodo = $max_orden_hijo + 1;
        }
        else
        {
            $orden_nodo = 0;
        }
        $ordenes_nodos[$nodo] = $orden_nodo;

        // Se actualizan los órdenes de los nodos padres
        $nodos_padres = $info_nodos_padres[$nodo];
        foreach ($nodos_padres as $nodo_padre)
        {
            actualiza_orden_nodos_ascendientes(
                $info_nodos_padres,
                $info_nodos_hijos,
                $ordenes_nodos,
                $nodo_padre);
        }
    }
?>
