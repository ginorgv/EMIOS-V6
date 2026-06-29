<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_arboles.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/EquipoInstalacion.php');


    //
    // Funciones de listas
    //


    function dame_lista_estados_equipo_instalacion($estado_seleccionado)
    {
        $estados_equipo = EquipoInstalacion::dame_estados_equipo();

        foreach ($estados_equipo as $estado_equipo)
        {
            $lista .= "<option value='".$estado_equipo."'";
			if ($estado_equipo == $estado_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".EquipoInstalacion::dame_descripcion_estado_equipo($estado_equipo)."</option>";
        }

        return ($lista);
    }


    function dame_lista_equipos_instalacion_padres($id_instalacion, $id_equipo_hijo, $id_equipo_padre)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_equipos = "
            SELECT
                id,
                nombre
            FROM equipos_instalaciones
            WHERE
                (instalacion = '".$bd_red->_($id_instalacion)."')
                AND (id <> '".$bd_red->_($id_equipo_hijo)."')";
        $consulta_equipos .= "
            ORDER BY nombre ASC";
        $res_equipos = $bd_red->ejecuta_consulta($consulta_equipos);
        if ($res_equipos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_equipos."'");
        }

        $lista_equipos = "";
        $lista_equipos .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        while ($fila_equipo = $res_equipos->dame_siguiente_fila())
        {
            $lista_equipos .= "<option value='".$fila_equipo['id']."'";
			if ($fila_equipo['id'] == $id_equipo_padre)
			{
				$lista_equipos .= " selected";
			}
			$lista_equipos .= ">".htmlspecialchars($fila_equipo['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_equipos);
    }


    function dame_lista_iconos_mapa_equipo_instalacion($icono_seleccionado)
    {
        $lista = "";
        $lista .= dame_entrada_lista("Equipo", dame_descripcion_icono_mapa("Equipo"), $icono_seleccionado);
        $lista .= dame_entrada_lista("datalogger", dame_descripcion_icono_mapa("datalogger"), $icono_seleccionado);
        $lista .= dame_entrada_lista("modem", dame_descripcion_icono_mapa("modem"), $icono_seleccionado);
        $lista .= dame_entrada_lista("contador", dame_descripcion_icono_mapa("contador"), $icono_seleccionado);
        $lista .= dame_entrada_lista("electrico", dame_descripcion_icono_mapa("electrico"), $icono_seleccionado);
        $lista .= dame_entrada_lista("temperatura", dame_descripcion_icono_mapa("temperatura"), $icono_seleccionado);
        $lista .= dame_entrada_lista("viento", dame_descripcion_icono_mapa("viento"), $icono_seleccionado);
        $lista .= dame_entrada_lista("agua", dame_descripcion_icono_mapa("agua"), $icono_seleccionado);
        $lista .= dame_entrada_lista("gas", dame_descripcion_icono_mapa("gas"), $icono_seleccionado);
        $lista .= dame_entrada_lista("bombilla", dame_descripcion_icono_mapa("bombilla"), $icono_seleccionado);
        $lista .= dame_entrada_lista("interruptor", dame_descripcion_icono_mapa("interruptor"), $icono_seleccionado);
        return ($lista);
    }


    //
    // Funciones de equipos padres e hijos
    //


    // Carga la información de los equipos de instalaciones padres e hijos
	function carga_informacion_equipos_instalaciones_padres_hijos($id_instalacion, &$info_equipos_padres, &$info_equipos_hijos)
	{
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_equipos = "
            SELECT
                equipo_padre,
                id
            FROM equipos_instalaciones
            WHERE
                (instalacion = '".$bd_red->_($id_instalacion)."')
                AND (equipo_padre <> '".ID_NINGUNO."')
            ORDER BY
                equipo_padre ASC,
                id ASC";
        $res_equipos = $bd_red->ejecuta_consulta($consulta_equipos);
        if ($res_equipos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_equipos."'");
        }

        $info_equipos_padres = array();
        $info_equipos_hijos = array();
        while ($fila_equipo = $res_equipos->dame_siguiente_fila())
        {
            $id_equipo_padre = $fila_equipo["equipo_padre"];
            $id_equipo_hijo = $fila_equipo["id"];

            // Se añade la información de equipos padres y de equipos hijos
            anyade_nodo_hijo($info_equipos_padres, $id_equipo_hijo, $id_equipo_padre);
            anyade_nodo_hijo($info_equipos_hijos, $id_equipo_padre, $id_equipo_hijo);
        }
    }


    //
    // Funciones de administración de equipos hijos
    //


    function anyade_equipo_instalacion_padre(&$info_equipos_padres, $id_equipo_padre, $id_equipo_hijo)
	{
        anyade_nodo_hijo($info_equipos_padres, $id_equipo_padre, $id_equipo_hijo);
    }


    function elimina_equipo_instalacion_padre(&$info_equipos_padres, $id_equipo_padre, $id_equipo_hijo)
	{
        elimina_nodo_hijo($info_equipos_padres, $id_equipo_padre, $id_equipo_hijo);
    }


    function anyade_equipo_instalacion_hijo(&$info_equipos_hijos, $id_equipo_padre, $id_equipo_hijo)
	{
        anyade_nodo_hijo($info_equipos_hijos, $id_equipo_padre, $id_equipo_hijo);
    }


    function elimina_equipo_instalacion_hijo(&$info_equipos_hijos, $id_equipo_padre, $id_equipo_hijo)
	{
        elimina_nodo_hijo($info_equipos_hijos, $id_equipo_padre, $id_equipo_hijo);
    }


    function existe_bucle_equipos_instalaciones_hijos($info_equipos_hijos)
	{
        return (existe_bucle_nodos_hijos($info_equipos_hijos));
    }


    function carga_ordenes_equipos_instalaciones_padres_hijos($id_instalacion, &$ordenes_equipos)
	{
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_ordenes_equipos_instalaciones = "
            SELECT
                id,
                orden
            FROM equipos_instalaciones
            WHERE
                instalacion = '".$bd_red->_($id_instalacion)."'";
        $res_ordenes_equipos_instalaciones = $bd_red->ejecuta_consulta($consulta_ordenes_equipos_instalaciones);
        if ($res_ordenes_equipos_instalaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_ordenes_equipos_instalaciones."'");
        }

        $ordenes_equipos = array();
        while ($fila_orden_equipo_instalacion = $res_ordenes_equipos_instalaciones->dame_siguiente_fila())
        {
            $id_equipo = $fila_orden_equipo_instalacion["id"];
            $orden = $fila_orden_equipo_instalacion["orden"];

            anyade_orden_nodo($ordenes_equipos, $id_equipo, $orden);
        }
    }


    function actualiza_orden_equipos_instalaciones_ascendientes(
        $info_equipos_padres,
        $info_equipos_hijos,
        &$ordenes_equipos,
        $id_equipo)
	{
        $bd_red = BaseDatosRed::dame_base_datos();

        // Órdenes anteriores
        $ordenes_equipo_anteriores = $ordenes_equipos;

        // Se actualiza el orden del equipo y recursivamente el de sus padres
        actualiza_orden_nodos_ascendientes(
            $info_equipos_padres,
            $info_equipos_hijos,
            $ordenes_equipos,
            $id_equipo);

        // Se recorren los órdenes anteriores y actuales, se modifican en base de datos los que hayan cambiado
        $numero_equipos_actualizados = 0;
        foreach ($ordenes_equipos as $id_equipo => $orden)
        {
            $orden_anterior = $ordenes_equipo_anteriores[$id_equipo];
            if ($orden_anterior <> $orden)
            {
                $operacion_modificacion = "
                    UPDATE equipos_instalaciones
                    SET
                        orden = '".$bd_red->_($orden)."'
                    WHERE
                        id = '".$bd_red->_($id_equipo)."'";
                $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
                if ($res_modificacion == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_modificacion."'");
                }

                $numero_equipos_actualizados += 1;
            }
        }

        // Se devuelve el número de equipos actualizados
        return ($numero_equipos_actualizados);
    }


    //
    // Funciones de nodos de equipos
    //


    function dame_ids_nodos_otros_equipos_instalacion($id_instalacion, $tipo_nodo, $id_equipo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_equipos = "
            SELECT
                id,
                sensores,
                actuadores
            FROM equipos_instalaciones
            WHERE
                instalacion = '".$bd_red->_($id_instalacion)."'";
        $res_equipos = $bd_red->ejecuta_consulta($consulta_equipos);
        if ($res_equipos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_equipos."'");
        }

        $ids_nodos = array();
        while ($fila_equipo = $res_equipos->dame_siguiente_fila())
        {
            if ($fila_equipo["id"] == $id_equipo)
            {
                continue;
            }
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    $cadena_ids_nodos_equipo = $fila_equipo["sensores"];
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    $cadena_ids_nodos_equipo = $fila_equipo["actuadores"];
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
                }
            }
            if ($cadena_ids_nodos_equipo != "")
            {
                $ids_nodos_equipo = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_nodos_equipo);
                $ids_nodos = array_merge($ids_nodos, $ids_nodos_equipo);
            }
        }
        return ($ids_nodos);
    }


    //
    // Funciones de obtención de información de equipos
    //


    function dame_fila_equipo_instalacion($id_equipo_instalacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_equipo_instalacion = "
            SELECT *
            FROM equipos_instalaciones
            WHERE
                id = '".$bd_red->_($id_equipo_instalacion)."'";
        $res_equipo_instalacion = $bd_red->ejecuta_consulta($consulta_equipo_instalacion);
        if (($res_equipo_instalacion == false) || ($res_equipo_instalacion->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_equipo_instalacion."'");
        }
        $fila_equipo_instalacion = $res_equipo_instalacion->dame_siguiente_fila();
        return ($fila_equipo_instalacion);
    }


    function dame_nombre_equipo_instalacion($id_equipo_instalacion)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        switch ($id_equipo_instalacion)
        {
            case ID_NINGUNO:
            {
                $nombre_equipo_instalacion = $idiomas->_("Ninguno");
                break;
            }
            default:
            {
                $consulta_equipo_instalacion = "
                    SELECT nombre
                    FROM equipos_instalaciones
                    WHERE
                        id = '".$bd_red->_($id_equipo_instalacion)."'";
                $res_equipo_instalacion = $bd_red->ejecuta_consulta($consulta_equipo_instalacion);
                if (($res_equipo_instalacion == false) || ($res_equipo_instalacion->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_equipo_instalacion."'");
                }
                $fila_equipo_instalacion = $res_equipo_instalacion->dame_siguiente_fila();
                $nombre_equipo_instalacion = $fila_equipo_instalacion["nombre"];
                break;
            }
        }
        return ($nombre_equipo_instalacion);
    }


    function dame_fila_anotacion_equipo_instalacion($id_anotacion_equipo_instalacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_anotacion_equipo_instalacion = "
            SELECT *
            FROM anotaciones_equipos_instalaciones
            WHERE
                id = '".$bd_red->_($id_anotacion_equipo_instalacion)."'";
        $res_anotacion_equipo_instalacion = $bd_red->ejecuta_consulta($consulta_anotacion_equipo_instalacion);
        if (($res_anotacion_equipo_instalacion == false) || ($res_anotacion_equipo_instalacion->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_anotacion_equipo_instalacion."'");
        }
        $fila_anotacion_equipo_instalacion = $res_anotacion_equipo_instalacion->dame_siguiente_fila();
        return ($fila_anotacion_equipo_instalacion);
    }
?>