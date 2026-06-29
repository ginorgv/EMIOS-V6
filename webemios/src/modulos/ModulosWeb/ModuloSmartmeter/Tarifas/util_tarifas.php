<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');

	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/util_tarifas_agua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/TarifaAgua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');
		include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/util_tarifas_electricidad_Portugal.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/util_tarifas_gas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/GrupoTarifas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/Tarifa.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/util_modulo_smartmeter.php');


    //
    // Funciones de listas de tarifas
    //


    // Crea una lista desplegable para la selección de una tarifa
    function dame_control_lista_tarifas(
        $id_controles,
        $medicion,
        $id_tarifa,
        $mostrar_etiquetas,
        $opciones_extra)
    {
        $idiomas = new Idiomas();

        $control_lista_tarifas = "";
        if ($mostrar_etiquetas == true)
        {
            $control_lista_tarifas .= "<div id='etiqueta_tarifa_".$id_controles."'>".$idiomas->_("Tarifa").": "."</div>";
        }
        $control_lista_tarifas .= "
            <select id='id_tarifa_".$id_controles."'";
        $control_lista_tarifas .= "
                class='chosen-select' hidden>";
        $control_lista_tarifas .= dame_lista_tarifas($medicion, array($id_tarifa), $opciones_extra);
        $control_lista_tarifas .= "
            </select>";

        return ($control_lista_tarifas);
    }


    // Devuelve la lista de tarifas
    function dame_lista_tarifas(
        $medicion,
        $ids_tarifas_seleccionadas,
        $opciones_extra)
    {
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $lista_tarifas .= dame_lista_tarifas_electricidad_Espanya($ids_tarifas_seleccionadas, $opciones_extra);
                        break;
                    }

                    case PAIS_PORTUGAL:
                    {
                        $lista_tarifas .= dame_lista_tarifas_electricidad_Portugal($ids_tarifas_seleccionadas, $opciones_extra);
                        break;
                    }

                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case MEDICION_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        $lista_tarifas .= dame_lista_tarifas_gas_Espanya($ids_tarifas_seleccionadas, $opciones_extra);
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
            case MEDICION_AGUA:
            {
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $lista_tarifas .= dame_lista_tarifas_agua_Espanya($ids_tarifas_seleccionadas, $opciones_extra);
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
        }
        return ($lista_tarifas);
    }


    // Devuelve la lista de expiraciones de tarifa
    function dame_lista_expiraciones_tarifa($expiracion_seleccionada)
    {
        $expiraciones_tarifa = Tarifa::dame_expiraciones_tarifa();
        foreach ($expiraciones_tarifa as $expiracion_tarifa)
        {
            $nombre_expiracion_tarifa = Tarifa::dame_descripcion_expiracion_tarifa($expiracion_tarifa);
            $lista .= "<option value='".$expiracion_tarifa."'";
			if ($expiracion_tarifa == $expiracion_seleccionada)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_expiracion_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }

    function dame_lista_prorrateo($prorrateo_seleccionado)
    {
        $log = dame_log();
        $log -> debug("3 INICIO DEL MÉTODO DAME LISTA PRORRATEO");
        $log -> debug("4 La variable prorrateo seleccionado que llega a la función: ");
        $log -> debug($prorrateo_seleccionado);
        $prorrateos_tarifa = Tarifa::dame_prorrateos_tarifa();
        $lista = "";
        foreach ($prorrateos_tarifa as $prorrateo_tarifa)
        {
            $log = dame_log();
            $log -> debug("5 La variable prorrateo tarifa es: ");
            $log -> debug($prorrateo_tarifa);
            $nombre_prorrateo_tarifa = Tarifa::dame_descripcion_prorrateo_tarifa($prorrateo_tarifa);
            //$prorrateo_seleccionado = Tarifa::dame_descripcion_prorrateo_tarifa($prorrateo_seleccionado);
            $lista .= "<option value='".$prorrateo_tarifa."'";
            $log = dame_log();
            $log -> debug("6 El prorrateo seleccionado: ");
            $log -> debug($prorrateo_seleccionado);
            $log -> debug("7 El prorrateo tarifa: ");
            $log -> debug($prorrateo_tarifa);
			if ($prorrateo_tarifa == $prorrateo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_prorrateo_tarifa, ENT_QUOTES)."</option>";
        }
        $log = dame_log();
        $log -> debug("8 La lista es: ");
        $log -> debug($lista);
        return ($lista);
    }

    //
    // Funciones de listas de grupos de tarifas
    //


    // Crea una lista desplegable para la selección de un grupo de tarifas
    function dame_control_lista_doble_tarifas(
        $id_controles,
        $medicion,
        $max_tarifas)
    {
        $idiomas = new Idiomas();

        // Nota: En las listas dobles es necesario el atributo 'name'
        $control_lista_doble_tarifas = "<span>".$idiomas->_("Tarifas").": "."</span><br/>";
        $control_lista_doble_tarifas .= "
            <div id='select_tarifas_no_visible_".$id_controles."' hidden></div>
            <select id='ids_tarifas_".$id_controles."'
                name='ids_tarifas_".$id_controles."'
                max_selected='".$max_tarifas."' multiple='multiple'
                class='select100' hidden>";
        $control_lista_doble_tarifas .= dame_lista_tarifas($medicion, array(), OPCIONES_EXTRA_LISTA_TARIFAS_SIN_NINGUNA);
        $control_lista_doble_tarifas .= "
            </select>";
        return ($control_lista_doble_tarifas);
    }


    // Devuelve la lista de grupos de tarifas
    function dame_lista_grupos_tarifas(
        $medicion,
        $id_grupo_seleccionado,
        $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $tabla_grupos_tarifas = dame_nombre_tabla_grupos_tarifas($medicion);
        $consulta_grupos = "
            SELECT
                id,
                nombre
            FROM ".$tabla_grupos_tarifas."
            WHERE
                red = '".$_SESSION["id_red"]."'
            ORDER BY nombre ASC";
        $res_grupos = $bd_red->ejecuta_consulta($consulta_grupos);
        if ($res_grupos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_grupos."'");
        }

        $lista = "";
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_TODOS) || ($opciones_extra == OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_TODOS_NINGUNO))
        {
            $lista .= "<option value='".ID_TODOS."'>".$idiomas->_("Todos")."</option>";
        }
        if (($opciones_extra == OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_NINGUNO) || ($opciones_extra == OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_TODOS_NINGUNO))
        {
            $lista .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        }
        while ($fila_grupo = $res_grupos->dame_siguiente_fila())
        {
            $lista .= "<option value='".$fila_grupo['id']."'";
			if ($fila_grupo['id'] == $id_grupo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($fila_grupo['nombre'], ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    //
    // Funciones de identificadores de tarifas
    //


    // Devuelve el identificador de tarifa (actual) de un sensor
    function dame_id_tarifa_id_sensor($id_sensor)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta = "
            SELECT
                clase,
                parametros_clase
            FROM sensores
            WHERE
                id = '".$bd_red->_($id_sensor)."'";
        $res = $bd_red->ejecuta_consulta($consulta);
        if (($res == false) || ($res->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
        }
        $fila = $res->dame_siguiente_fila();
        $clase_sensor = $fila["clase"];
        $parametros_clase_sensor = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila["parametros_clase"]);

        $id_tarifa = dame_id_tarifa_parametros_clase_sensor($clase_sensor, $parametros_clase_sensor);
        return ($id_tarifa);
    }


    // Devuelve el identificador de tarifa (actual) de los parámetros de clase
    function dame_id_tarifa_parametros_clase_sensor($clase_sensor, $parametros_clase_sensor)
    {
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $medicion = MEDICION_ELECTRICIDAD;
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $id_tarifa = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_TARIFA_ELECTRICA];
                        $id_grupo_tarifas = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_GRUPO_TARIFAS_ELECTRICAS];
                        break;
                    }

                    case PAIS_PORTUGAL:
                    {
                        $id_tarifa = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_TARIFA_ELECTRICA];
                        $id_grupo_tarifas = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_GRUPO_TARIFAS_ELECTRICAS];
                        break;
                    }

                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                $medicion = MEDICION_GAS;
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        $id_tarifa = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_TARIFA_GAS];
                        $id_grupo_tarifas = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_GRUPO_TARIFAS_GAS];
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                $medicion = MEDICION_AGUA;
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $id_tarifa = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_TARIFA_AGUA];
                        $id_grupo_tarifas = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_GRUPO_TARIFAS_AGUA];
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor sin tarifas: '".$clase_sensor."'");
            }
        }

        // Si hay grupo de tarifas:
        // - Si la última tarifa está 'caducada' se devuelve esa tarifa
        // - Si la última tarifa no está 'caducada' se devuelve la última tarifa no caducada 'actual'
        //   (puede haber varias tarifas que caduquen en el futuro)
        if ($id_grupo_tarifas != ID_NINGUNO)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $tabla_tarifas = dame_nombre_tabla_tarifas($medicion);
            $consulta_tarifas = "
				SELECT
					id,
                    fecha_expiracion
				FROM ".$tabla_tarifas."
				WHERE
					grupo = '".$bd_red->_($id_grupo_tarifas)."'
				ORDER BY fecha_expiracion DESC";
            $res_tarifas = $bd_red->ejecuta_consulta($consulta_tarifas);
            if ($res_tarifas == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tarifas."'");
            }
            $numero_tarifa = 1;
            while ($fila_tarifa = $res_tarifas->dame_siguiente_fila())
            {
                $numero_dias_restantes_expiracion = dame_numero_dias_restantes_expiracion_tarifa($fila_tarifa["fecha_expiracion"]);
                if ($numero_dias_restantes_expiracion <= 0)
                {
                    if ($numero_tarifa == 1)
                    {
                        $id_tarifa = $fila_tarifa["id"];
                    }
                    break;
                }
                else
                {
                    $id_tarifa = $fila_tarifa["id"];
                }
                $numero_tarifa += 1;
            }
        }
        return ($id_tarifa);
    }


    // Devuelve el identificador de tarifa de un sensor y fecha
    function dame_id_tarifa_id_sensor_fecha($id_sensor, $cadena_fecha_hora_local_local)
    {
        $fila_sensor = dame_fila_sensor($id_sensor);
        $clase_sensor = $fila_sensor["clase"];
        $parametros_clase_sensor = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor["parametros_clase"]);

        $id_tarifa = dame_id_tarifa_parametros_clase_sensor_fecha($clase_sensor, $parametros_clase_sensor, $cadena_fecha_hora_local_local);
        return ($id_tarifa);
    }


    // Devuelve el identificador de tarifa eléctrica de los parámetros de clase y la fecha
    function dame_id_tarifa_parametros_clase_sensor_fecha($clase_sensor, $parametros_clase_sensor, $cadena_fecha_hora_local_local)
    {
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $medicion = MEDICION_ELECTRICIDAD;
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $id_tarifa = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_TARIFA_ELECTRICA];
                        $id_grupo_tarifas = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_GRUPO_TARIFAS_ELECTRICAS];
                        break;
                    }

                    case PAIS_PORTUGAL:
                    {
                        $id_tarifa = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_TARIFA_ELECTRICA];
                        $id_grupo_tarifas = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_GRUPO_TARIFAS_ELECTRICAS];
                        break;
                    }

                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                $medicion = MEDICION_GAS;
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        $id_tarifa = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_TARIFA_GAS];
                        $id_grupo_tarifas = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_GRUPO_TARIFAS_GAS];
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                $medicion = MEDICION_AGUA;
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $id_tarifa = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_TARIFA_AGUA];
                        $id_grupo_tarifas = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_GRUPO_TARIFAS_AGUA];
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor sin tarifas: '".$clase_sensor."'");
            }
        }

        // Si hay grupo de tarifas:
        // - Se devuelve la tarifa correspondiente a la fecha especificada
        //   (si no hay ninguna, se devuelve la tarifa más reciente)
        if ($id_grupo_tarifas != ID_NINGUNO)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $zona_horaria = dame_zona_horaria_local();
            $cadena_fecha_local_local = convierte_formato_fecha($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);
            $fecha_local = convierte_cadena_a_fecha($cadena_fecha_local_local, $_SESSION["formato_fecha_local"], $zona_horaria);
            $fecha_local->setTime(0, 0, 0);

            $tabla_tarifas = dame_nombre_tabla_tarifas($medicion);
            $consulta_tarifas = "
				SELECT
					id,
                    fecha_expiracion
				FROM ".$tabla_tarifas."
				WHERE
					grupo = '".$bd_red->_($id_grupo_tarifas)."'
				ORDER BY fecha_expiracion ASC";
            $res_tarifas = $bd_red->ejecuta_consulta($consulta_tarifas);
            if ($res_tarifas == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tarifas."'");
            }
            $id_ultima_tarifa = ID_NINGUNO;
            while ($fila_tarifa = $res_tarifas->dame_siguiente_fila())
            {
                $fecha_expiracion_local = convierte_cadena_a_fecha($fila_tarifa["fecha_expiracion"], FORMATO_FECHA_BASE_DATOS, $zona_horaria);
                $fecha_expiracion_local->setTime(0, 0, 0);
                if ($fecha_expiracion_local > $fecha_local)
                {
                    $id_tarifa = $fila_tarifa["id"];
                    break;
                }
                else
                {
                    $id_ultima_tarifa = $fila_tarifa["id"];
                }
            }
            if ($id_tarifa == ID_NINGUNO)
            {
                $id_tarifa = $id_ultima_tarifa;
            }
        }
        return ($id_tarifa);
    }


    //
    // Funciones de obtención de información de tarifas
    //


    // Devuelve la fila de tarifa
    function dame_fila_tarifa($tabla_tarifas, $id_tarifa)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_tarifa = "
            SELECT *
            FROM ".$tabla_tarifas."
            WHERE
                id = '".$bd_red->_($id_tarifa)."'";
        $res_tarifa = $bd_red->ejecuta_consulta($consulta_tarifa);
        if (($res_tarifa == false) || ($res_tarifa->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_tarifa."'");
        }
        $fila_tarifa = $res_tarifa->dame_siguiente_fila();
        return ($fila_tarifa);
    }


    // Devuelve el nombre de la tarifa
    function dame_nombre_tarifa($tabla_tarifas, $id_tarifa)
    {
        $ids_tarifas = array($id_tarifa);
        $nombres_tarifas = dame_nombres_tarifas($tabla_tarifas, $ids_tarifas);
        $nombre_tarifa = $nombres_tarifas[0];
        return ($nombre_tarifa);
    }


    // Devuelve los nombres de las tarifas
    function dame_nombres_tarifas($tabla_tarifas, $ids_tarifas)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $nombres_tarifas = array();
        foreach ($ids_tarifas AS $id_tarifa)
        {
            if ($id_tarifa == ID_NINGUNO)
            {
                $nombre_tarifa = $idiomas->_("Ninguna");
            }
            else
            {
                $consulta_tarifa = "
                    SELECT nombre
                    FROM ".$tabla_tarifas."
                    WHERE
                        id = '".$bd_red->_($id_tarifa)."'";
                $res_tarifa = $bd_red->ejecuta_consulta($consulta_tarifa);
                if (($res_tarifa == false) || ($res_tarifa->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_tarifa."'");
                }
                $fila_tarifa = $res_tarifa->dame_siguiente_fila();
                $nombre_tarifa = $fila_tarifa["nombre"];
            }
            array_push($nombres_tarifas, $nombre_tarifa);
        }
        return ($nombres_tarifas);
    }


    // Devuelve la fila del grupo de tarifas
    function dame_fila_grupo_tarifas($tabla_grupos_tarifas, $id_grupo_tarifas)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

         $consulta_grupo_tarifas = "
            SELECT *
            FROM ".$tabla_grupos_tarifas."
            WHERE
                id = '".$bd_red->_($id_grupo_tarifas)."'";
        $res_grupo_tarifas = $bd_red->ejecuta_consulta($consulta_grupo_tarifas);
        if (($res_grupo_tarifas == false) || ($res_grupo_tarifas->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_grupo_tarifas."'");
        }
        $fila_grupo_tarifas = $res_grupo_tarifas->dame_siguiente_fila();
        return ($fila_grupo_tarifas);
    }


    // Devuelve el nombre del grupo de tarifas
    function dame_nombre_grupo_tarifas($tabla_grupos_tarifas, $id_grupo_tarifas)
    {
        $ids_grupos_tarifas = array($id_grupo_tarifas);
        $nombres_grupos_tarifas = dame_nombres_grupos_tarifas($tabla_grupos_tarifas, $ids_grupos_tarifas);
        $nombre_grupo_tarifas = $nombres_grupos_tarifas[0];
        return ($nombre_grupo_tarifas);
    }


    // Devuelve los nombres de los grupos de tarifas
    function dame_nombres_grupos_tarifas($tabla_grupos_tarifas, $ids_grupos_tarifas)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $nombres_grupos_tarifas = array();
        foreach ($ids_grupos_tarifas AS $id_grupo_tarifas)
        {
            if ($id_grupo_tarifas == ID_NINGUNO)
            {
                $nombre_grupo_tarifas = $idiomas->_("Ninguno");
            }
            else
            {
                $consulta_grupo_tarifas = "
                    SELECT nombre
                    FROM ".$tabla_grupos_tarifas."
                    WHERE
                        id = '".$bd_red->_($id_grupo_tarifas)."'";
                $res_grupo_tarifas = $bd_red->ejecuta_consulta($consulta_grupo_tarifas);
                if (($res_grupo_tarifas == false) || ($res_grupo_tarifas->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_grupo_tarifas."'");
                }
                $fila_grupo_tarifas = $res_grupo_tarifas->dame_siguiente_fila();
                $nombre_grupo_tarifas = $fila_grupo_tarifas["nombre"];
            }
            array_push($nombres_grupos_tarifas, $nombre_grupo_tarifas);
        }
        return ($nombres_grupos_tarifas);
    }


    // Devuelve la fila del concepto adicional de factura de una tarifa
    function dame_fila_concepto_adicional_factura_tarifa($tabla_conceptos_adicionales_facturas_tarifas, $id_concepto_adicional_factura_tarifa)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_concepto_adicional_factura_tarifa = "
            SELECT *
            FROM ".$tabla_conceptos_adicionales_facturas_tarifas."
            WHERE
                id = '".$bd_red->_($id_concepto_adicional_factura_tarifa)."'";
        $res_concepto_adicional_factura_tarifa = $bd_red->ejecuta_consulta($consulta_concepto_adicional_factura_tarifa);
        if (($res_concepto_adicional_factura_tarifa == false) || ($res_concepto_adicional_factura_tarifa->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_concepto_adicional_factura_tarifa."'");
        }
        $fila_concepto_adicional_factura_tarifa = $res_concepto_adicional_factura_tarifa->dame_siguiente_fila();
        return ($fila_concepto_adicional_factura_tarifa);
    }


    //
    // Funciones de expiración de tarifas
    //


    // Devuelve el número de días restantes para la expiración de la tarifa
    function dame_numero_dias_restantes_expiracion_tarifa($cadena_fecha_expiracion_base_datos_local)
    {
        $zona_horaria = dame_zona_horaria_local();
        $fecha_expiracion_local = convierte_cadena_a_fecha($cadena_fecha_expiracion_base_datos_local, FORMATO_FECHA_BASE_DATOS, $zona_horaria);
        $fecha_expiracion_local->setTime(0, 0, 0);
        $fecha_actual_local = dame_fecha_hora_actual_local();
        $fecha_actual_local->setTime(0, 0, 0);

        // Nota: Devuelve los días siempre en positivo, por eso hay que hacer la comprobación
        $numero_dias_restantes_expiracion = $fecha_expiracion_local->diff($fecha_actual_local)->days;
        if ($fecha_actual_local > $fecha_expiracion_local)
        {
            $numero_dias_restantes_expiracion = -$numero_dias_restantes_expiracion;
        }
        return ($numero_dias_restantes_expiracion);
    }


    //
    // Funciones de controles para el filtrado de tarifas
    //


    // Crea una lista desplegable para la selección de un tarifa
    function dame_control_lista_estados_tarifa($id_controles, $etiqueta)
    {
        $idiomas = new Idiomas();

        $control_lista_estados .= "<div id='etiqueta_estado_tarifa_".$id_controles."'>".$etiqueta.": "."</div>";
        $control_lista_estados .= "<select id='estado_tarifa_".$id_controles."' class='filtro-desplegable'>";

        $control_lista_estados .= "<option value=".ESTADO_TARIFA_TODOS.">".$idiomas->_("Todos")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_TARIFA_OK.">".$idiomas->_("Ok")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_TARIFA_AVISO_EXPIRACION.">".$idiomas->_("Aviso de expiración")."</option>";
        $control_lista_estados .= "<option value=".ESTADO_TARIFA_EXPIRADA.">".$idiomas->_("Expirada")."</option>";

        $control_lista_estados .= "</select>";

        return ($control_lista_estados);
    }


    //
    // Funciones de tipos de tarifas
    //


    // Devuelve el nombre de la tabla de las tarifas
    function dame_nombre_tabla_tarifas($medicion)
    {
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $nombre_tabla = TABLA_TARIFAS_ELECTRICAS_ESPANYA;
                        break;
                    }

                    case PAIS_PORTUGAL:
                    {
                        $nombre_tabla = TABLA_TARIFAS_ELECTRICAS_PORTUGAL;
                        break;
                    }

                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case MEDICION_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        $nombre_tabla = TABLA_TARIFAS_GAS_ESPANYA;
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
            case MEDICION_AGUA:
            {
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $nombre_tabla = TABLA_TARIFAS_AGUA_ESPANYA;
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($nombre_tabla);
    }


    // Devuelve el nombre de la tabla de los conceptos adicionales de facturas de tarifas
    function dame_nombre_tabla_conceptos_adicionales_facturas_tarifas($medicion)
    {
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $nombre_tabla = TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_ELECTRICAS_ESPANYA;
                        break;
                    }

                    case PAIS_PORTUGAL:
                    {
                        $nombre_tabla = TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_ELECTRICAS_PORTUGAL;
                        break;
                    }

                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case MEDICION_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        $nombre_tabla = TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_GAS_ESPANYA;
                        break;
                    }

                    default:
                    {
                        throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
            case MEDICION_AGUA:
            {
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $nombre_tabla = TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_AGUA_ESPANYA;
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($nombre_tabla);
    }


    // Devuelve el nombre de la tabla de los grupos de tarifas
    function dame_nombre_tabla_grupos_tarifas($medicion)
    {
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $nombre_tabla = TABLA_GRUPOS_TARIFAS_ELECTRICAS_ESPANYA;
                        break;
                    }

                    case PAIS_PORTUGAL:
                    {
                        $nombre_tabla = TABLA_GRUPOS_TARIFAS_ELECTRICAS_PORTUGAL;
                        break;
                    }

                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case MEDICION_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        $nombre_tabla = TABLA_GRUPOS_TARIFAS_GAS_ESPANYA;
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
            case MEDICION_AGUA:
            {
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $nombre_tabla = TABLA_GRUPOS_TARIFAS_AGUA_ESPANYA;
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($nombre_tabla);
    }


    // Devuelve el índice del parámetro de clase de sensor correspondiente al identificador de tarifa
    function dame_indice_parametro_clase_sensor_tarifa($medicion)
    {
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $indice_parametro_clase_sensor_tarifa = INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_TARIFA_ELECTRICA;
                        break;
                    }

                    case PAIS_PORTUGAL:
                    {
                        $indice_parametro_clase_sensor_tarifa = INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_TARIFA_ELECTRICA;
                        break;
                    }

                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case MEDICION_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        $indice_parametro_clase_sensor_tarifa = INDICE_PARAMETRO_CLASE_SENSOR_GAS_ID_TARIFA_GAS;
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
            case MEDICION_AGUA:
            {
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $indice_parametro_clase_sensor_tarifa = INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ID_TARIFA_AGUA;
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($indice_parametro_clase_sensor_tarifa);
    }


    // Devuelve el índice del parámetro de clase de sensor correspondiente al identificador de grupo de tarifas
    function dame_indice_parametro_clase_sensor_grupo_tarifas($medicion)
    {
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $indice_parametro_clase_sensor_grupo_tarifas = INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_GRUPO_TARIFAS_ELECTRICAS;
                        break;
                    }

                    // EMG : Qué es esto
                    case PAIS_PORTUGAL:
                    {
                        $indice_parametro_clase_sensor_grupo_tarifas = INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_GRUPO_TARIFAS_ELECTRICAS;
                        break;
                    }

                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case MEDICION_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        $indice_parametro_clase_sensor_grupo_tarifas = INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_GRUPO_TARIFAS_GAS;
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
            case MEDICION_AGUA:
            {
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $indice_parametro_clase_sensor_grupo_tarifas = INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_GRUPO_TARIFAS_AGUA;
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($indice_parametro_clase_sensor_grupo_tarifas);
    }


    // Asigna el grupo de tarifas a los sensores de la tarifa especificada
    function asigna_grupo_tarifas_sensores_tarifa($medicion, $id_grupo_tarifas, $id_tarifa)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros dependientes de la medición
        $clase_sensor = dame_clase_sensor_medicion($medicion);
        $indice_parametros_clase_sensor_id_tarifa = dame_indice_parametro_clase_sensor_tarifa($medicion);
        $indice_parametros_clase_sensor_id_grupo_tarifas = dame_indice_parametro_clase_sensor_grupo_tarifas($medicion);

        // Sensores a los que está asignado la tarifa eléctrica
        $consulta_sensores = "
            SELECT
                id,
                parametros_clase
            FROM sensores
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (clase = '".$clase_sensor."')
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".($indice_parametros_clase_sensor_id_tarifa + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($id_tarifa)."')";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensores."'");
        }

        while ($fila_sensor = $res_sensores->dame_siguiente_fila())
        {
            $id_sensor = $fila_sensor["id"];
            $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor['parametros_clase']);

            // Se asigna el grupo de tarifas al sensor
            $parametros_clase[$indice_parametros_clase_sensor_id_tarifa] = ID_NINGUNO;
            $parametros_clase[$indice_parametros_clase_sensor_id_grupo_tarifas] = $id_grupo_tarifas;

            $cadena_parametros_clase = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_clase);
            $operacion_modificacion_parametros_clase = "
                UPDATE sensores
                SET
                    parametros_clase = '".$bd_red->_($cadena_parametros_clase)."'
                WHERE
                    id = '".$bd_red->_($id_sensor)."'";
            $res_modificacion_parametros_clase = $bd_red->ejecuta_operacion($operacion_modificacion_parametros_clase);
            if ($res_modificacion_parametros_clase == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion_parametros_clase."'");
            }
        }
    }


    //
    // Funciones de conceptos adicionales de factura
    //


    // Devuelve la lista de tipos de conceptos adicionales de factura de tarifa
    function dame_lista_tipos_conceptos_adicionales_factura_tarifa($medicion, $tipo_concepto_adicional_seleccionado)
    {
        $lista_tipos_conceptos_adicionales = "";
        $lista_tipos_conceptos_adicionales .= dame_opcion_valor_lista_simple(
            dame_descripcion_tipo_concepto_adicional_factura_tarifa(TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_FIJO),
            TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_FIJO,
            $tipo_concepto_adicional_seleccionado);
        $lista_tipos_conceptos_adicionales .= dame_opcion_valor_lista_simple(
            dame_descripcion_tipo_concepto_adicional_factura_tarifa(TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_DIARIO),
            TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_DIARIO,
            $tipo_concepto_adicional_seleccionado);
        $caracteristicas_tarifas = dame_caracteristicas_tarifas_pais_medicion($medicion);
        if ($caracteristicas_tarifas["conceptos_adicionales_factura_consumo"] == true)
        {
            $lista_tipos_conceptos_adicionales .= dame_opcion_valor_lista_simple(
                dame_descripcion_tipo_concepto_adicional_factura_tarifa(TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_ABSOLUTO),
                TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_ABSOLUTO,
                $tipo_concepto_adicional_seleccionado);
            $lista_tipos_conceptos_adicionales .= dame_opcion_valor_lista_simple(
                dame_descripcion_tipo_concepto_adicional_factura_tarifa(TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_DIARIO),
                TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_DIARIO,
                $tipo_concepto_adicional_seleccionado);
        }
        return ($lista_tipos_conceptos_adicionales);
    }


    // Devuelve la descripción del concepto adicional de factura de tarifa
    function dame_descripcion_tipo_concepto_adicional_factura_tarifa($tipo_concepto_adicional)
    {
        switch ($tipo_concepto_adicional)
        {
            case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_FIJO:
            {
                $descripcion_tipo_concepto_adicional = "Fijo";
                break;
            }
            case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_DIARIO:
            {
                $descripcion_tipo_concepto_adicional = "Diario";
                break;
            }
            case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_ABSOLUTO:
            {
                $descripcion_tipo_concepto_adicional = "Consumo (absoluto)";
                break;
            }
            case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_DIARIO:
            {
                $descripcion_tipo_concepto_adicional = "Consumo (diario)";
                break;
            }
            default:
            {
                $descripcion_tipo_concepto_adicional = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_tipo_concepto_adicional));
    }


    // Devuelve la tabla de conceptos adicionales de factura de tarifa
    function dame_tabla_conceptos_adicionales_factura_tarifa($medicion, $id_tarifa)
    {
        $bd_red = BaseDatosRed::dame_base_datos();
        $idiomas = new Idiomas();

        // Consulta de conceptos adicionales de factura
        $nombre_tabla_conceptos_adicionales_facturas_tarifas = dame_nombre_tabla_conceptos_adicionales_facturas_tarifas($medicion);
        $consulta_conceptos = "
            SELECT *
            FROM ".$nombre_tabla_conceptos_adicionales_facturas_tarifas."
            WHERE
                tarifa = '".$bd_red->_($id_tarifa)."'
            ORDER BY nombre ASC";
        $res_conceptos = $bd_red->ejecuta_consulta($consulta_conceptos);
        if ($res_conceptos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_conceptos."'");
        }
        $numero_conceptos_adicionales_factura = $res_conceptos->dame_numero_filas();

        // Comprobación de visualización de tabla
        $administracion_tarifas = Tarifa::dame_administracion_tarifas();
        if (($administracion_tarifas == false) && ($numero_conceptos_adicionales_factura == 0))
        {
            return (NULL);
        }

        // Se crean las opciones de la tabla
        $opciones = array();
        if ($administracion_tarifas == true)
        {
            $boton_anyadir_concepto_adicional_factura = "<i id='anyade_modifica_concepto_adicional_factura__".$id_tarifa."' class='icon-plus color-blanco boton_smartmeter_mostrar_ventana_anyadir_modificar_concepto_adicional_factura_tarifa boton-tabla-datos'></i>";
            array_push($opciones, $boton_anyadir_concepto_adicional_factura);
        }
        $boton_actualizar_tabla_conceptos_adicionales_factura = "<i id='actualiza_tabla_conceptos_adicional_factura__".$id_tarifa."' class='icon-refresh color-blanco boton_smartmeter_actualizar_tabla_conceptos_adicionales_factura_tarifa boton-tabla-datos'></i>";
        array_push($opciones, $boton_actualizar_tabla_conceptos_adicionales_factura);

        // Se crea la tabla
        $caracteristicas_tarifas_pais = dame_caracteristicas_tarifas_pais_medicion($medicion);
        if ($caracteristicas_tarifas_pais["impuesto_conceptos_adicionales_factura"] == false)
        {
            $numero_columnas = NUMERO_COLUMNAS_TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_SIN_IMPUESTO;
            $anchuras_columnas = ANCHURAS_COLUMNAS_TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_SIN_IMPUESTO;
        }
        else
        {
            $numero_columnas = NUMERO_COLUMNAS_TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_CON_IMPUESTO;
            $anchuras_columnas = ANCHURAS_COLUMNAS_TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_CON_IMPUESTO;
        }
        $params_tabla = array(
            "opciones" => $opciones,
            "numero_columnas" => $numero_columnas,
            "anchuras_columnas" => unserialize($anchuras_columnas),
            "generar_valores_xml" => true
        );
        $tabla = new TablaDatos(
            "tabla-conceptos-adicionales-factura",
            $idiomas->_("Conceptos adicionales de factura"),
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        if ($caracteristicas_tarifas_pais["impuesto_conceptos_adicionales_factura"] == false)
        {
            $cabecera = array(
                $idiomas->_("Nombre"),
                $idiomas->_("Tipo"),
                $idiomas->_("Coste"));
        }
        else
        {
            $cabecera = array(
                $idiomas->_("Nombre"),
                $idiomas->_("Tipo"),
                $idiomas->_("Coste"),
                $idiomas->_("Impuesto"));
        }
        $tabla->anyade_cabecera("", $cabecera);

        // Unidades de medida
        $clase_sensor = dame_clase_sensor_medicion($medicion);
        $unidad_medida_consumo = dame_unidad_medida_consumo_clase_sensor($clase_sensor);
        $unidad_medida_coste = $_SESSION["moneda"];

        // Se añade cada uno de conceptos adicionales de factura a la tabla y el pie de tabla
        while ($fila_concepto = $res_conceptos->dame_siguiente_fila())
        {
            $id_concepto = $fila_concepto['id'];
            $nombre = $fila_concepto['nombre'];
            $tipo = $fila_concepto['tipo'];
            $cadena_coste = $fila_concepto['coste'];
            $cadena_limites_consumo_tramos = $fila_concepto['limites_consumo_tramos'];
            $impuesto = $fila_concepto['impuesto'];

            switch ($tipo)
            {
                case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_FIJO:
                case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_DIARIO:
                {
                    $cadena_coste_fila_tabla = formatea_numero($cadena_coste, 6, true)." ".$unidad_medida_coste;
                    break;
                }
                case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_ABSOLUTO:
                case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_DIARIO:
                {
                    $cadenas_costes = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_coste);
                    $cadena_coste_fila_tabla = "";
                    foreach ($cadenas_costes as $cadena_coste)
                    {
                        if ($cadena_coste_fila_tabla != "")
                        {
                            $cadena_coste_fila_tabla .= ", ";
                        }
                        $cadena_coste_fila_tabla .= formatea_numero($cadena_coste, 6, true);
                    }
                    $cadena_coste_fila_tabla .= " ".$unidad_medida_coste."/".$unidad_medida_consumo;
                    if ($cadena_limites_consumo_tramos != "")
                    {
                        $cadenas_limites_consumo_tramos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_limites_consumo_tramos);
                        $cadena_limites_consumo_tramos_fila_tabla = "";
                        foreach ($cadenas_limites_consumo_tramos as $cadena_limite_consumo_tramo)
                        {
                            if ($cadena_limites_consumo_tramos_fila_tabla != "")
                            {
                                $cadena_limites_consumo_tramos_fila_tabla .= ", ";
                            }
                            $cadena_limites_consumo_tramos_fila_tabla .= formatea_numero($cadena_limite_consumo_tramo, 2, true);
                        }
                        $cadena_limites_consumo_tramos_fila_tabla .= " ".$unidad_medida_consumo;
                        switch ($tipo)
                        {
                            case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_DIARIO:
                            {
                                $cadena_limites_consumo_tramos_fila_tabla .= "/".$idiomas->_("día");
                                break;
                            }
                        }
                        $cadena_coste_fila_tabla .= " (".$idiomas->_("límites de consumo por tramo").": ".$cadena_limites_consumo_tramos_fila_tabla.")";
                    }
                    break;
                }
            }

            // Datos de la fila de la tabla
            $descripcion_tipo = dame_descripcion_tipo_concepto_adicional_factura_tarifa($tipo);
            if ($caracteristicas_tarifas_pais["impuesto_conceptos_adicionales_factura"] == false)
            {
                $datos_fila = array(
                    htmlspecialchars($nombre, ENT_QUOTES),
                    $descripcion_tipo,
                    $cadena_coste_fila_tabla
                );
            }
            else
            {
                $cadena_impuesto = formatea_numero($impuesto, 2, true)." %";
                $datos_fila = array(
                    htmlspecialchars($nombre, ENT_QUOTES),
                    $descripcion_tipo,
                    $cadena_coste_fila_tabla,
                    $cadena_impuesto
                );
            }

            $opciones = array();
            if ($administracion_tarifas == true)
            {
                $editar = "<i id='anyade_modifica_concepto_adicional_factura__".$id_tarifa."__".$id_concepto."' class='icon-pencil color-gris boton_smartmeter_mostrar_ventana_anyadir_modificar_concepto_adicional_factura_tarifa boton-tabla-datos'></i>";
                $borrar = "<i id='elimina_concepto_adicional_factura__".$id_tarifa."__".$id_concepto."' class='icon-remove color-gris boton_smartmeter_eliminar_concepto_adicional_factura_tarifa boton-tabla-datos'></i>";
                $opciones = array($borrar, $editar);
            }
            $params_fila = array(
                "opciones" => $opciones
            );
            $tabla->anyade_fila(
                "datosConceptoAdicionalFactura__".$id_tarifa."__".$id_concepto,
                $datos_fila,
                $params_fila
            );
        }
        $tabla->anyade_pie($idiomas->_("Conceptos adicionales de factura").": ".$numero_conceptos_adicionales_factura);

        return ($tabla->dame_tabla(false));
    }


    // Devuelve la información de conceptos adicionales de factura de tarifa
    function dame_info_conceptos_adicionales_factura_tarifa($medicion, $id_tarifa)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Consulta de conceptos adicionales de factura
        $nombre_tabla_conceptos_adicionales_facturas_tarifas = dame_nombre_tabla_conceptos_adicionales_facturas_tarifas($medicion);
        $consulta_conceptos = "
            SELECT *
            FROM ".$nombre_tabla_conceptos_adicionales_facturas_tarifas."
            WHERE
                tarifa = '".$bd_red->_($id_tarifa)."'
            ORDER BY nombre ASC";
        $res_conceptos = $bd_red->ejecuta_consulta($consulta_conceptos);
        if ($res_conceptos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_conceptos."'");
        }

        $info_conceptos = array();
        while ($fila_concepto = $res_conceptos->dame_siguiente_fila())
        {
            $info_concepto = $fila_concepto;
            array_push($info_conceptos, $info_concepto);
        }
        return ($info_conceptos);
    }


    // Devuelve si hay impuesto en los conceptos adicionales de factura de tarifa (y el nombre si lo hay)
    function dame_hay_impuesto_conceptos_adicionales_factura_tarifa($medicion, $id_tarifa, &$nombre_impuesto)
    {
        $idiomas = new Idiomas();

        // Se recupera si hay impuesto
        $caracteristicas_tarifas_pais = dame_caracteristicas_tarifas_pais_medicion($medicion);
        if ($caracteristicas_tarifas_pais["impuesto_conceptos_adicionales_factura"] == false)
        {
            return (false);
        }

        // Se recupera el nombre del impuesto
        $nombre_impuesto = NULL;
        switch ($medicion)
        {
            case MEDICION_AGUA:
            {
                switch ($_SESSION["pais_tarifas_agua"])
                {
                    case PAIS_ESPANYA:
                    {
                        $fila_tarifa_agua = dame_fila_tarifa(TABLA_TARIFAS_AGUA_ESPANYA, $id_tarifa);
                        $caracteristicas_tipo_tarifa_agua = TarifaAgua_Espanya::dame_caracteristicas_tipo_tarifa_agua($fila_tarifa_agua["tipo"]);
                        $tipo_tarifa_canarias = $caracteristicas_tipo_tarifa_agua["tipo_tarifa_canarias"];
                        if ($tipo_tarifa_canarias == false)
                        {
                            $nombre_impuesto = $idiomas->_("IVA");
                        }
                        else
                        {
                            $nombre_impuesto = $idiomas->_("IGIC");
                        }
                        break;
                    }
                }
                break;
            }
        }
        return (true);
    }


    //
    // Funciones de instalaciones
    //


    // Devuelve información de la instalación del sensor especificado
    function dame_datos_instalacion_sensor($medicion, $id_sensor, $id_tarifa)
    {
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $datos_instalacion = dame_datos_instalacion_sensor_electricidad_Espanya($id_sensor, $id_tarifa);
                        break;
                    }

                    case PAIS_PORTUGAL:
                    {
                        $datos_instalacion = dame_datos_instalacion_sensor_electricidad_Espanya($id_sensor, $id_tarifa);
                        break;
                    }

                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case MEDICION_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        $datos_instalacion = dame_datos_instalacion_sensor_gas_Espanya($id_sensor, $id_tarifa);
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
            case MEDICION_AGUA:
            {
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $datos_instalacion = dame_datos_instalacion_sensor_agua_Espanya($id_sensor, $id_tarifa);
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($datos_instalacion);
    }


    //
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_instalacion(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        // Selección de medición y de país
        $medicion = $parametros_tipo_elemento["medicion"];
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $html_elemento = dame_html_elemento_plantilla_informe_tipo_smartmeter_instalacion_electricidad_Espanya(
                            $numero_elemento,
                            $nombre_elemento,
                            $parametros_tipo_elemento,
                            $tipo_informe);
                        break;
                    }

                    // EMG: Qué es esto
                    case PAIS_PORTUGAL:
                    {
                        $html_elemento = dame_html_elemento_plantilla_informe_tipo_smartmeter_instalacion_electricidad_Espanya(
                            $numero_elemento,
                            $nombre_elemento,
                            $parametros_tipo_elemento,
                            $tipo_informe);
                        break;
                    }

                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case MEDICION_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        $html_elemento = dame_html_elemento_plantilla_informe_tipo_smartmeter_instalacion_gas_Espanya(
                            $numero_elemento,
                            $nombre_elemento,
                            $parametros_tipo_elemento,
                            $tipo_informe);
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
            case MEDICION_AGUA:
            {
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $html_elemento = dame_html_elemento_plantilla_informe_tipo_smartmeter_instalacion_agua_Espanya(
                            $numero_elemento,
                            $nombre_elemento,
                            $parametros_tipo_elemento,
                            $tipo_informe);
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_instalacion(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Selección de medición y de país
        $medicion = $parametros_tipo_elemento["medicion"];
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_instalacion_electricidad_Espanya(
                            $numero_elemento,
                            $parametros_tipo_elemento,
                            $parametros_informe);
                        break;
                    }

                    // EMG : Qué es esto
                    case PAIS_PORTUGAL:
                    {
                        $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_instalacion_electricidad_Espanya(
                            $numero_elemento,
                            $parametros_tipo_elemento,
                            $parametros_informe);
                        break;
                    }

                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            case MEDICION_GAS:
            {
                $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                switch ($pais_tarifas_gas)
                {
                    case PAIS_ESPANYA:
                    {
                        $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_instalacion_gas_Espanya(
                            $numero_elemento,
                            $parametros_tipo_elemento,
                            $parametros_informe);
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                    }
                }
                break;
            }
            case MEDICION_AGUA:
            {
                $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                switch ($pais_tarifas_agua)
                {
                    case PAIS_ESPANYA:
                    {
                        $datos_elemento = dame_datos_elemento_plantilla_informe_tipo_smartmeter_instalacion_agua_Espanya(
                            $numero_elemento,
                            $parametros_tipo_elemento,
                            $parametros_informe);
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($datos_elemento);
    }


    //
    // Funciones de administración
    //


    // Realiza acciones al eliminar una tarifa
    function realiza_acciones_tarifa_eliminada($medicion, $id_tarifa)
    {
        // Se modifican los elementos de plantillas de informes que contengan esta tarifa (se establece a ninguna)
        modifica_elementos_plantillas_informes_tarifa_eliminada($medicion, $id_tarifa);

        // Se eliminan los informes automáticos correspondientes
        elimina_informes_automaticos_tarifa_eliminada($medicion, $id_tarifa);
    }


    //
    // Funciones auxiliares
    //


    // Duplica los conceptos adicionales de factura de la la tarifa anterior
    function duplica_conceptos_adicionales_factura_tarifa_anterior(
        $tabla_conceptos_adicionales_factura_tarifas,
        $id_tarifa_anterior,
        $id_tarifa)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren los conceptos adicionales de factura de la tarifa anterior (origen de la actual), se cambia el id de origen y se añaden
        $consulta_conceptos_adicionales = "
            SELECT *
            FROM ".$tabla_conceptos_adicionales_factura_tarifas."
            WHERE
                tarifa = '".$bd_red->_($id_tarifa_anterior)."'";
        $res_conceptos_adicionales = $bd_red->ejecuta_consulta($consulta_conceptos_adicionales);
        if ($res_conceptos_adicionales == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_conceptos_adicionales."'");
        }

        while ($fila_concepto_adicional = $res_conceptos_adicionales->dame_siguiente_fila())
        {
            $operacion_insercion_concepto_adicional = "
                INSERT INTO ".$tabla_conceptos_adicionales_factura_tarifas." (
                    nombre,
                    red,
                    tarifa,
                    tipo,
                    coste,
                    limites_consumo_tramos,
                    impuesto
                ) VALUES (
                    '".$bd_red->_($fila_concepto_adicional["nombre"])."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_tarifa)."',
                    '".$bd_red->_($fila_concepto_adicional["tipo"])."',
                    '".$bd_red->_($fila_concepto_adicional["coste"])."',
                    '".$bd_red->_($fila_concepto_adicional["limites_consumo_tramos"])."',
                    '".$bd_red->_($fila_concepto_adicional["impuesto"])."'
                )";
            $res_insercion_concepto_adicional = $bd_red->ejecuta_operacion($operacion_insercion_concepto_adicional);
            if ($res_insercion_concepto_adicional == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_concepto_adicional."'");
            }
        }
    }

    // Duplica los conceptos de coste pass_through de la la tarifa anterior
    function duplica_conceptos_coste_pass_through_factura_tarifa_anterior(
        $id_tarifa_anterior,
        $id_tarifa)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren los conceptos adicionales de factura de la tarifa anterior (origen de la actual), se cambia el id de origen y se añaden
        $consulta_conceptos_coste = "
            SELECT *
            FROM ".TABLA_CONCEPTOS_COSTE_PASS_THROUGH_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                tarifa_electrica = '".$bd_red->_($id_tarifa_anterior)."'";
        $res_conceptos_coste = $bd_red->ejecuta_consulta($consulta_conceptos_coste);
        if ($res_conceptos_coste == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_conceptos_coste."'");
        }

        while ($fila_concepto_coste = $res_conceptos_coste->dame_siguiente_fila())
        {
            $operacion_insercion_concepto_coste = "
                INSERT INTO ".TABLA_CONCEPTOS_COSTE_PASS_THROUGH_TARIFAS_ELECTRICAS_ESPANYA." (
                    nombre,
                    red,
                    tarifa_electrica,
                    formula_precio_consumo
                ) VALUES (
                    '".$bd_red->_($fila_concepto_coste["nombre"])."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_tarifa)."',
                    '".$bd_red->_($fila_concepto_coste["formula_precio_consumo"])."'
                )";
            $res_insercion_concepto_coste = $bd_red->ejecuta_operacion($operacion_insercion_concepto_coste);
            if ($res_insercion_concepto_coste == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_concepto_coste."'");
            }
        }
    }

?>
