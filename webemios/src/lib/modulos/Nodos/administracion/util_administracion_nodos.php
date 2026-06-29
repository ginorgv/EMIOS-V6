<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Espanya/util_facturas_electricidad_Espanya.php');


    //
    // Funciones de redes
    //


    function dame_lista_clientes($cliente_seleccionado)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_clientes = "
			SELECT
                id,
                nombre
			FROM clientes
			ORDER BY nombre ASC";
		$res_clientes = $bd_red->ejecuta_consulta($consulta_clientes);
        if ($res_clientes == false)
		{
		    throw new Exception("Error en la consulta: '".$consulta_clientes."'");
		}

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), ID_NINGUNO, $cliente_seleccionado);
		while ($fila_cliente = $res_clientes->dame_siguiente_fila())
		{
			$lista .= "<option value='".$fila_cliente['id']."'";
			if ($fila_cliente['id'] == $cliente_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($fila_cliente['nombre'], ENT_QUOTES)."</option>";
		}
        return ($lista);
    }


    //
    // Funciones de dispositivos
    //


    function dame_lista_arquitecturas_dispositivo($arquitectura_dispositivo_seleccionada)
    {
        $arquitecturas_dispositivo = NodoDispositivo::dame_arquitecturas_dispositivo();
        $lista = "";
        foreach ($arquitecturas_dispositivo as $arquitectura_dispositivo)
        {
            $nombre_arquitectura_dispositivo = NodoDispositivo::dame_descripcion_arquitectura_dispositivo($arquitectura_dispositivo);
            $lista .= "<option value='".$arquitectura_dispositivo."'";
            if ($arquitectura_dispositivo == $arquitectura_dispositivo_seleccionada)
            {
                $lista .= " selected";
            }
            $lista .= ">".htmlspecialchars($nombre_arquitectura_dispositivo, ENT_QUOTES)."</option>";
        }
        return ($lista);
    }


    function dame_lista_dispositivos($dispositivo_seleccionado)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_dispositivos = "
			SELECT
                id,
                nombre
			FROM dispositivos
			WHERE
				red = '".$_SESSION["id_red"]."'
			ORDER BY nombre ASC";
        $res_dispositivos = $bd_red->ejecuta_consulta($consulta_dispositivos);
        if ($res_dispositivos == false)
		{
		    throw new Exception("Error en la consulta: '".$consulta_dispositivos."'");
		}

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), ID_NINGUNO, $dispositivo_seleccionado);
		while ($fila_dispositivo = $res_dispositivos->dame_siguiente_fila())
		{
			$lista .= "<option value='".$fila_dispositivo['id']."'";
			if ($fila_dispositivo['id'] == $dispositivo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($fila_dispositivo['nombre'], ENT_QUOTES)."</option>";
		}
        return ($lista);
    }


    function dame_lista_axones($axon_seleccionado)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_axones = "
			SELECT
                id,
                nombre
			FROM axones
			WHERE
				red = '".$_SESSION["id_red"]."'
			ORDER BY nombre ASC";
		$res_axones = $bd_red->ejecuta_consulta($consulta_axones);
        if ($res_axones == false)
		{
		    throw new Exception("Error en la consulta: '".$consulta_axones."'");
		}

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), ID_NINGUNO, $axon_seleccionado);
		while ($fila_axon = $res_axones->dame_siguiente_fila())
		{
			$lista .= "<option value='".$fila_axon['id']."'";
			if ($fila_axon['id'] == $axon_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($fila_axon['nombre'], ENT_QUOTES)."</option>";
		}
        return ($lista);
    }


    //
    // Funciones de sensores
    //


    function dame_lista_clases_sensor(&$clase_seleccionada, $mostrar_todas_clases, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $lista = "";
        if ($mostrar_todas_clases == true)
        {
            $clases_sensor = NodoSensor::dame_clases_sensor();
        }
        else
        {
            $clases_sensor = dame_clases_sensor_usuario_actual(true);
        }
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_CLASES_NINGUNA:
            {
                $lista .= dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), CLASE_NINGUNA, $clase_seleccionada);
                break;
            }
            case OPCIONES_EXTRA_LISTA_CLASES_TODAS:
            {
                $lista .= dame_opcion_valor_lista_simple($idiomas->_("Todas"), CLASE_TODAS, $clase_seleccionada);
                break;
            }
            case OPCIONES_EXTRA_LISTA_CLASES_NINGUNA_TODAS:
            {
                $lista .= dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), CLASE_NINGUNA, $clase_seleccionada);
                $lista .= dame_opcion_valor_lista_simple($idiomas->_("Todas"), CLASE_TODAS, $clase_seleccionada);
                break;
            }
        }
        foreach ($clases_sensor as $clase_sensor)
        {
            $nombre_clase_sensor = NodoSensor::dame_descripcion_clase_sensor($clase_sensor);
            $lista .= "<option value='".$clase_sensor."'";
			if ($clase_sensor == $clase_seleccionada)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_clase_sensor, ENT_QUOTES)."</option>";
        }
        return ($lista);
    }


    function dame_lista_tipos_sensor($tipo_sensor_seleccionado)
    {
        $tipos_sensor = NodoSensor::dame_tipos_sensor();

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), TIPO_NINGUNO, $tipo_sensor_seleccionado);
        foreach ($tipos_sensor as $tipo_sensor)
        {
            $nombre_tipo_sensor = NodoSensor::dame_descripcion_tipo_sensor($tipo_sensor);
            $lista .= "<option value='".$tipo_sensor."'";
            if ($tipo_sensor == $tipo_sensor_seleccionado)
            {
                $lista .= " selected";
            }
            $lista .= ">".htmlspecialchars($nombre_tipo_sensor, ENT_QUOTES)."</option>";
        }
        return ($lista);
    }


    function dame_lista_tipos_valores_sensor($tipo_valores_sensor_seleccionado)
    {
        $tipos_valores = array();
        array_push($tipos_valores, array(
            "id" => TIPO_VALORES_SENSOR_PUNTUALES,
            "nombre" => NodoSensor::dame_descripcion_tipo_valores_sensor(TIPO_VALORES_SENSOR_PUNTUALES)));
        array_push($tipos_valores, array(
            "id" => TIPO_VALORES_SENSOR_INCREMENTALES,
            "nombre" => NodoSensor::dame_descripcion_tipo_valores_sensor(TIPO_VALORES_SENSOR_INCREMENTALES)));

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), TIPO_NINGUNO, $tipo_valores_sensor_seleccionado);
        foreach ($tipos_valores as $tipo_valores)
        {
            $lista .= "<option value='".$tipo_valores['id']."'";
			if ($tipo_valores['id'] == $tipo_valores_sensor_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$tipo_valores['nombre']."</option>";
        }
        return ($lista);
    }

    function dame_lista_apis($tipo_valores_sensor_seleccionado)
    {
        $tipos_valores = array();
        array_push($tipos_valores, array(
            "id" => API_AXONTIME,
            "nombre" => NodoSensor::dame_descripcion_api_seleccionada(AXONTIME)));
        array_push($tipos_valores, array(
            "id" => API_SGCLIMA,
            "nombre" => NodoSensor::dame_descripcion_api_seleccionada(SGCLIMA)));

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), TIPO_NINGUNO, $tipo_valores_sensor_seleccionado);
        foreach ($tipos_valores as $tipo_valores)
        {
            $lista .= "<option value='".$tipo_valores['id']."'";
			if ($tipo_valores['id'] == $tipo_valores_sensor_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$tipo_valores['nombre']."</option>";
        }
        return ($lista);
    }

    function dame_lista_tipos_measurement_type($measurement_type_seleccionado)
    {
        $measurement_types = array();
        array_push($measurement_types, array(
            "id" => 0,
            "nombre" => NodoSensor::dame_descripcion_tipo_curva_sensor(TIPO_CURVA_HORARIA)));
        array_push($measurement_types, array(
            "id" => 1,
            "nombre" => NodoSensor::dame_descripcion_tipo_curva_sensor(TIPO_CURVA_CUARTO_HORARIA)));

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), TIPO_NINGUNO, $measurement_type_seleccionado);
        foreach ($measurement_types as $measurement_type)
        {
            $lista .= "<option value='".$measurement_type['id']."'";
			if ($measurement_type['id'] == $measurement_type_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$measurement_type['nombre']."</option>";
        }
        return ($lista);
    }


    function dame_lista_tipos_curva_sensor($tipo_curva_sensor_seleccionado)
    {
        $tipos_curva = array();
        array_push($tipos_curva, array(
            "id" => TM1,
            "nombre" => NodoSensor::dame_descripcion_tipo_curva_sensor(TIPO_CURVA_HORARIA)));
        array_push($tipos_curva, array(
            "id" => TM2,
            "nombre" => NodoSensor::dame_descripcion_tipo_curva_sensor(TIPO_CURVA_CUARTO_HORARIA)));

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), TIPO_NINGUNO, $tipo_curva_sensor_seleccionado);
        foreach ($tipos_curva as $tipo_curva)
        {
            $lista .= "<option value='".$tipo_curva['id']."'";
			if ($tipo_curva['id'] == $tipo_curva_sensor_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$tipo_curva['nombre']."</option>";
        }
        return ($lista);
    }

    function dame_lista_tipos_energia_sensor($tipo_energia_sensor_seleccionado)
    {
        $tipos_energia = array();
        array_push($tipos_energia, array(
            "id" => energia,
            "nombre" => NodoSensor::dame_descripcion_tipo_energia_sensor(ENERGIA_ACTIVA)));
        array_push($tipos_energia, array(
            "id" => ie1q,
            "nombre" => NodoSensor::dame_descripcion_tipo_energia_sensor(ENERGIA_REACTIVA_INDUCTIVA)));
        array_push($tipos_energia, array(
            "id" => ce4q,
            "nombre" => NodoSensor::dame_descripcion_tipo_energia_sensor(ENERGIA_REACTIVA_CAPACITIVA)));
        // Descomentar cuando tengamos que medir energia exportada
        //array_push($tipos_energia, array(
        //    "id" => exportada,
        //    "nombre" => NodoSensor::dame_descripcion_tipo_energia_sensor(ENERGIA_EXPORTADA)));

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), TIPO_NINGUNO, $tipo_energia_sensor_seleccionado);
        foreach ($tipos_energia as $tipo_energia)
        {
            $lista .= "<option value='".$tipo_energia['id']."'";
			if ($tipo_energia['id'] == $tipo_energia_sensor_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$tipo_energia['nombre']."</option>";
        }
        return ($lista);
    }


    function dame_lista_tipo_energia_reactiva($tipo_energia_reactiva_seleccionado)
    {
        $tipos_energia_reactiva = array();
        array_push($tipos_energia_reactiva, array(
            "id" => TIPO_ENERGIA_REACTIVA_Q1,
            "nombre" => NodoSensor::dame_descripcion_tipo_energia_reactiva(TIPO_ENERGIA_REACTIVA_Q1)));

    //Preparado para Q2 y Q3 por si fuera necesario en el futuro
    //    array_push($tipos_energia_reactiva, array(
    //        "id" => TIPO_ENERGIA_REACTIVA_Q2,
    //        "nombre" => NodoSensor::dame_descripcion_tipo_energia_reactiva(TIPO_ENERGIA_REACTIVA_Q2)));
    //    array_push($tipos_energia_reactiva, array(
    //        "id" => TIPO_ENERGIA_REACTIVA_Q3,
    //        "nombre" => NodoSensor::dame_descripcion_tipo_energia_reactiva(TIPO_ENERGIA_REACTIVA_Q3)));

        array_push($tipos_energia_reactiva, array(
            "id" => TIPO_ENERGIA_REACTIVA_Q4,
            "nombre" => NodoSensor::dame_descripcion_tipo_energia_reactiva(TIPO_ENERGIA_REACTIVA_Q4)));
        $idiomas = new Idiomas();
        #$lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), TIPO_NINGUNO, $tipo_energia_reactiva_seleccionado);
        foreach ($tipos_energia_reactiva as $tipo_energia_reactiva)
        {
            $lista .= "<option value='".$tipo_energia_reactiva['id']."'";
			if ($tipo_energia_reactiva['id'] == $tipo_energia_reactiva_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$tipo_energia_reactiva['nombre']."</option>";
        }
        return ($lista);
    }

    function dame_lista_tipos_cambio_valores_puntuales_sensor($tipo_cambio_valores_seleccionado)
    {
        $tipos_cambio_valores = array();
        array_push($tipos_cambio_valores, array(
            "id" => CAMBIO_VALORES_PUNTUALES_SENSOR_GRADUAL,
            "nombre" => NodoSensor::dame_descripcion_cambio_valores_puntuales_sensor(CAMBIO_VALORES_PUNTUALES_SENSOR_GRADUAL)));
        array_push($tipos_cambio_valores, array(
            "id" => CAMBIO_VALORES_PUNTUALES_SENSOR_INSTANTANEO,
            "nombre" => NodoSensor::dame_descripcion_cambio_valores_puntuales_sensor(CAMBIO_VALORES_PUNTUALES_SENSOR_INSTANTANEO)));

        foreach ($tipos_cambio_valores as $tipo_cambio_valores)
        {
            $lista .= "<option value='".$tipo_cambio_valores['id']."'";
			if ($tipo_cambio_valores['id'] == $tipo_cambio_valores_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$tipo_cambio_valores['nombre']."</option>";
        }
        return ($lista);
    }


    function dame_lista_tipos_horas_incrementos_valores_sensor($tipo_horas_incrementos_valores_seleccionado)
    {
        $tipos_horas_incrementos_valores = array();
        array_push($tipos_horas_incrementos_valores, array(
            "id" => TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO,
            "nombre" => NodoSensor::dame_descripcion_tipo_horas_incrementos_valores_sensor(TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_FIJO)));
        array_push($tipos_horas_incrementos_valores, array(
            "id" => TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_VARIABLE,
            "nombre" => NodoSensor::dame_descripcion_tipo_horas_incrementos_valores_sensor(TIPO_HORAS_INCREMENTOS_VALORES_SENSOR_VARIABLE)));

        foreach ($tipos_horas_incrementos_valores as $tipo_horas_incrementos_valores)
        {
            $lista .= "<option value='".$tipo_horas_incrementos_valores['id']."'";
			if ($tipo_horas_incrementos_valores['id'] == $tipo_horas_incrementos_valores_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$tipo_horas_incrementos_valores['nombre']."</option>";
        }
        return ($lista);
    }


    function dame_lista_tipos_incrementos_valores_sensor($tipo_incrementos_valores_seleccionado)
    {
        $tipos_incrementos_valores = array();
        array_push($tipos_incrementos_valores, array(
            "id" => TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL,
            "nombre" => NodoSensor::dame_descripcion_tipo_incrementos_valores_sensor(TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL)));
        array_push($tipos_incrementos_valores, array(
            "id" => TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_FINAL,
            "nombre" => NodoSensor::dame_descripcion_tipo_incrementos_valores_sensor(TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_FINAL)));

        foreach ($tipos_incrementos_valores as $tipo_incrementos_valores)
        {
            $lista .= "<option value='".$tipo_incrementos_valores['id']."'";
			if ($tipo_incrementos_valores['id'] == $tipo_incrementos_valores_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$tipo_incrementos_valores['nombre']."</option>";
        }
        return ($lista);
    }


    function dame_lista_clases_interfaz_sensor(&$clase_interfaz_sensor_seleccionada)
    {
        $clases_interfaz_sensor = NodoSensor::dame_clases_interfaz_sensor();

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), CLASE_NINGUNA, $clase_interfaz_sensor_seleccionada);
        foreach ($clases_interfaz_sensor as $clase_interfaz_sensor)
        {
            if ($clase_interfaz_sensor_seleccionada == ID_NINGUNO)
            {
                $clase_interfaz_sensor_seleccionada = $clase_interfaz_sensor;
            }

            $nombre_clase_interfaz_sensor = NodoSensor::dame_descripcion_clase_interfaz_sensor($clase_interfaz_sensor);
            $lista .= "<option value='".$clase_interfaz_sensor."'";
            if ($clase_interfaz_sensor == $clase_interfaz_sensor_seleccionada)
            {
                $lista .= " selected";
            }
            $lista .= ">".htmlspecialchars($nombre_clase_interfaz_sensor, ENT_QUOTES)."</option>";
        }
        return ($lista);
    }


    function dame_lista_clases_sensor_virtual_clase_sensor($clase_sensor, $clase_sensor_virtual_seleccionada)
    {
        $clases_virtuales_clase = array();
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_TEMPERATURA:
            case CLASE_SENSOR_HUMEDAD:
            case CLASE_SENSOR_LUZ_INTERIOR:
            case CLASE_SENSOR_VIENTO:
            {
                array_push($clases_virtuales_clase, array(
                    "id" => CLASE_SENSOR_VIRTUAL_MEDIA_VALORES,
                    "nombre" => NodoSensor::dame_descripcion_clase_sensor_virtual(CLASE_SENSOR_VIRTUAL_MEDIA_VALORES)));
                array_push($clases_virtuales_clase, array(
                    "id" => CLASE_SENSOR_VIRTUAL_VALOR_MINIMO,
                    "nombre" => NodoSensor::dame_descripcion_clase_sensor_virtual(CLASE_SENSOR_VIRTUAL_VALOR_MINIMO)));
                array_push($clases_virtuales_clase, array(
                    "id" => CLASE_SENSOR_VIRTUAL_VALOR_MAXIMO,
                    "nombre" => NodoSensor::dame_descripcion_clase_sensor_virtual(CLASE_SENSOR_VIRTUAL_VALOR_MAXIMO)));
                break;
            }
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            case CLASE_SENSOR_AGUA:
            case CLASE_SENSOR_GAS:
            {
                array_push($clases_virtuales_clase, array(
                    "id" => CLASE_SENSOR_VIRTUAL_SUMA_VALORES,
                    "nombre" => NodoSensor::dame_descripcion_clase_sensor_virtual(CLASE_SENSOR_VIRTUAL_SUMA_VALORES)));
                break;
            }
            case CLASE_SENSOR_GENERICA:
            {
                array_push($clases_virtuales_clase, array(
                    "id" => CLASE_SENSOR_VIRTUAL_SUMA_VALORES,
                    "nombre" => NodoSensor::dame_descripcion_clase_sensor_virtual(CLASE_SENSOR_VIRTUAL_SUMA_VALORES)));
                array_push($clases_virtuales_clase, array(
                    "id" => CLASE_SENSOR_VIRTUAL_MEDIA_VALORES,
                    "nombre" => NodoSensor::dame_descripcion_clase_sensor_virtual(CLASE_SENSOR_VIRTUAL_MEDIA_VALORES)));
                array_push($clases_virtuales_clase, array(
                    "id" => CLASE_SENSOR_VIRTUAL_VALOR_MINIMO,
                    "nombre" => NodoSensor::dame_descripcion_clase_sensor_virtual(CLASE_SENSOR_VIRTUAL_VALOR_MINIMO)));
                array_push($clases_virtuales_clase, array(
                    "id" => CLASE_SENSOR_VIRTUAL_VALOR_MAXIMO,
                    "nombre" => NodoSensor::dame_descripcion_clase_sensor_virtual(CLASE_SENSOR_VIRTUAL_VALOR_MAXIMO)));
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION:
            case CLASE_SENSOR_COMPRA_ENERGIA:
            case CLASE_NINGUNA:
            {
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor desconocida: '".$clase_sensor."')");
            }
        }

        $idiomas = new Idiomas();
        $lista .= dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), CLASE_NINGUNA, $clase_sensor_virtual_seleccionada);
        foreach ($clases_virtuales_clase as $clase_virtual_clase)
        {
            $lista .= "<option value='".$clase_virtual_clase['id']."'";
			if ($clase_virtual_clase['id'] == $clase_sensor_virtual_seleccionada)
			{
				$lista .= " selected";
			}
			$lista .= ">".$clase_virtual_clase['nombre']."</option>";
        }
        return ($lista);
    }


    function dame_lista_clases_sensor_procesado($clase_sensor_procesado_seleccionada)
    {
        $clases_sensor_procesado = array();
        array_push($clases_sensor_procesado, array(
            "id" => CLASE_SENSOR_PROCESADO_FUNCION_VALORES,
            "nombre" => NodoSensor::dame_descripcion_clase_sensor_procesado(CLASE_SENSOR_PROCESADO_FUNCION_VALORES)));
        array_push($clases_sensor_procesado, array(
            "id" => CLASE_SENSOR_PROCESADO_MEDIA_VALORES,
            "nombre" => NodoSensor::dame_descripcion_clase_sensor_procesado(CLASE_SENSOR_PROCESADO_MEDIA_VALORES)));
        array_push($clases_sensor_procesado, array(
            "id" => CLASE_SENSOR_PROCESADO_SUMA_VALORES,
            "nombre" => NodoSensor::dame_descripcion_clase_sensor_procesado(CLASE_SENSOR_PROCESADO_SUMA_VALORES)));
        array_push($clases_sensor_procesado, array(
            "id" => CLASE_SENSOR_PROCESADO_VALOR_MINIMO,
            "nombre" => NodoSensor::dame_descripcion_clase_sensor_procesado(CLASE_SENSOR_PROCESADO_VALOR_MINIMO)));
        array_push($clases_sensor_procesado, array(
            "id" => CLASE_SENSOR_PROCESADO_VALOR_MAXIMO,
            "nombre" => NodoSensor::dame_descripcion_clase_sensor_procesado(CLASE_SENSOR_PROCESADO_VALOR_MAXIMO)));

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), CLASE_NINGUNA, $clase_sensor_procesado_seleccionada);
        foreach ($clases_sensor_procesado as $clase_sensor_procesado)
        {
            $lista .= "<option value='".$clase_sensor_procesado['id']."'";
			if ($clase_sensor_procesado['id'] == $clase_sensor_procesado_seleccionada)
			{
				$lista .= " selected";
			}
			$lista .= ">".$clase_sensor_procesado['nombre']."</option>";
        }
        return ($lista);
    }


    function dame_lista_clases_sensor_externo(&$clase_sensor_externo_seleccionada)
    {
        $clases_externo = array();
        array_push($clases_externo, array(
            "id" => CLASE_SENSOR_EXTERNO_FICHEROS_CSV,
            "nombre" => NodoSensor::dame_descripcion_clase_sensor_externo(CLASE_SENSOR_EXTERNO_FICHEROS_CSV)));
        array_push($clases_externo, array(
            "id" => CLASE_SENSOR_EXTERNO_HTTP_EMIOS,
            "nombre" => NodoSensor::dame_descripcion_clase_sensor_externo(CLASE_SENSOR_EXTERNO_HTTP_EMIOS)));
        array_push($clases_externo, array(
            "id" => CLASE_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO,
            "nombre" => NodoSensor::dame_descripcion_clase_sensor_externo(CLASE_SENSOR_EXTERNO_HTTP_XML_POWERSTUDIO)));
        array_push($clases_externo, array(
            "id" => CLASE_SENSOR_EXTERNO_MODBUS_IP,
            "nombre" => NodoSensor::dame_descripcion_clase_sensor_externo(CLASE_SENSOR_EXTERNO_MODBUS_IP)));
        array_push($clases_externo, array(
            "id" => CLASE_SENSOR_EXTERNO_WIBEEE,
            "nombre" => NodoSensor::dame_descripcion_clase_sensor_externo(CLASE_SENSOR_EXTERNO_WIBEEE)));
        array_push($clases_externo, array(
            "id" => CLASE_SENSOR_EXTERNO_API,
            "nombre" => NodoSensor::dame_descripcion_clase_sensor_externo(CLASE_SENSOR_EXTERNO_API)));

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), CLASE_NINGUNA, $clase_sensor_externo_seleccionada);
        foreach ($clases_externo as $clase_externo)
        {
            if ($clase_sensor_externo_seleccionada == ID_NINGUNO)
            {
                $clase_sensor_externo_seleccionada = $clase_externo['id'];
            }

            $lista .= "<option value='".$clase_externo['id']."'";
			if ($clase_externo['id'] == $clase_sensor_externo_seleccionada)
			{
				$lista .= " selected";
			}
			$lista .= ">".$clase_externo['nombre']."</option>";
        }
        return ($lista);
    }


    //
    // Funciones de actuadores
    //


    function dame_lista_clases_actuador(&$clase_seleccionada, $mostrar_todas_clases, $opciones_extra)
    {
        $idiomas = new Idiomas();

        if ($mostrar_todas_clases == true)
        {
            $clases_actuador = NodoActuador::dame_clases_actuador();
        }
        else
        {
            $clases_actuador = dame_clases_actuador_usuario_actual(true);
        }
        switch ($opciones_extra)
        {
            case OPCIONES_EXTRA_LISTA_CLASES_NINGUNA:
            {
                $lista .= dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), CLASE_NINGUNA, $clase_seleccionada);
                break;
            }
            case OPCIONES_EXTRA_LISTA_CLASES_TODAS:
            {
                $lista .= dame_opcion_valor_lista_simple($idiomas->_("Todas"), CLASE_TODAS, $clase_seleccionada);
                break;
            }
            case OPCIONES_EXTRA_LISTA_CLASES_NINGUNA_TODAS:
            {
                $lista .= dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), CLASE_NINGUNA, $clase_seleccionada);
                $lista .= dame_opcion_valor_lista_simple($idiomas->_("Todas"), CLASE_TODAS, $clase_seleccionada);
                break;
            }
        }
        foreach ($clases_actuador as $clase_actuador)
        {
            $nombre_clase_actuador = NodoActuador::dame_descripcion_clase_actuador($clase_actuador);
            $lista .= "<option value='".$clase_actuador."'";
			if ($clase_actuador == $clase_seleccionada)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_clase_actuador, ENT_QUOTES)."</option>";
        }
        return ($lista);
    }


    function dame_lista_tipos_actuador($tipo_actuador_seleccionado)
    {
        $tipos_actuador = NodoActuador::dame_tipos_actuador();

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), TIPO_NINGUNO, $tipo_actuador_seleccionado);
        foreach ($tipos_actuador as $tipo_actuador)
        {
            $nombre_tipo_actuador = NodoActuador::dame_descripcion_tipo_actuador($tipo_actuador);
            $lista .= "<option value='".$tipo_actuador."'";
            if ($tipo_actuador == $tipo_actuador_seleccionado)
            {
                $lista .= " selected";
            }
            $lista .= ">".htmlspecialchars($nombre_tipo_actuador, ENT_QUOTES)."</option>";
        }
        return ($lista);
    }


    function dame_lista_clases_interfaz_tipo_actuador($tipo_actuador, &$clase_interfaz_seleccionada)
    {
        $clases_interfaz_actuador = NodoActuador::dame_clases_interfaz_actuador();

        $idiomas = new Idiomas();
        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), CLASE_NINGUNA, $clase_interfaz_seleccionada);
        foreach ($clases_interfaz_actuador as $clase_interfaz_actuador)
        {
            $caracteristicas_clase_interfaz_actuador = NodoActuador::dame_caracteristicas_clase_interfaz_actuador($clase_interfaz_actuador);
            if (($caracteristicas_clase_interfaz_actuador["tipo_actuador"] == $tipo_actuador) ||
                ($caracteristicas_clase_interfaz_actuador["tipo_actuador"] == TIPO_ACTUADOR_TODOS))
            {
                if ($clase_interfaz_seleccionada == ID_NINGUNO)
                {
                    $clase_interfaz_seleccionada = $clase_interfaz_actuador;
                }

                $nombre_clase_interfaz_actuador = NodoActuador::dame_descripcion_clase_interfaz_actuador($clase_interfaz_actuador);
                $lista .= "<option value='".$clase_interfaz_actuador."'";
                if ($clase_interfaz_actuador == $clase_interfaz_seleccionada)
                {
                    $lista .= " selected";
                }
                $lista .= ">".htmlspecialchars($nombre_clase_interfaz_actuador, ENT_QUOTES)."</option>";
            }
        }
        return ($lista);
    }


    function dame_lista_programaciones_clase_actuador($clase_actuador, $id_programacion_seleccionada)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_programaciones = "
            SELECT
                id,
                nombre
            FROM programaciones
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (clase = '".$bd_red->_($clase_actuador)."')
            ORDER BY nombre ASC";
        $res_programaciones = $bd_red->ejecuta_consulta($consulta_programaciones);
        if ($res_programaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_programaciones."'");
        }

        $lista = "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguna")."</option>";
        while ($fila_programacion = $res_programaciones->dame_siguiente_fila())
        {
            $lista .= "<option value='".$fila_programacion['id']."'";
			if ($fila_programacion['id'] == $id_programacion_seleccionada)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($fila_programacion['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista);
    }


    //
    // Funciones comunes a varios tipos de nodo
    //


    function dame_lista_idiomas($idioma_seleccionado)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_idiomas = "
			SELECT
                id,
                nombre
			FROM idiomas
			ORDER BY nombre ASC";
		$res_idiomas = $bd_red->ejecuta_consulta($consulta_idiomas);
        if ($res_idiomas == false)
		{
		    throw new Exception("Error en la consulta: '".$consulta_idiomas."'");
		}

        $lista = "";
		while ($fila_idioma = $res_idiomas->dame_siguiente_fila())
		{
			$lista .= "<option value='".$fila_idioma['id']."'";
			if ($fila_idioma['id'] == $idioma_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$idiomas->_($fila_idioma['nombre'])."</option>";
		}
        return ($lista);
    }


    function dame_lista_encapsulados_modbus_serie($encapsulado_modbus_serie_seleccionado)
    {
        $encapsulados_modbus_serie = array();
        array_push($encapsulados_modbus_serie, array(
            "id" => ENCAPSULADO_MODBUS_ASCII,
            "nombre" => dame_descripcion_encapsulado_modbus(ENCAPSULADO_MODBUS_ASCII)));
        array_push($encapsulados_modbus_serie, array(
            "id" => ENCAPSULADO_MODBUS_RTU,
            "nombre" => dame_descripcion_encapsulado_modbus(ENCAPSULADO_MODBUS_RTU)));

        foreach ($encapsulados_modbus_serie as $encapsulado_modbus_serie)
        {
            $lista .= "<option value='".$encapsulado_modbus_serie['id']."'";
			if ($encapsulado_modbus_serie['id'] == $encapsulado_modbus_serie_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$encapsulado_modbus_serie['nombre']."</option>";
        }
        return ($lista);
    }


    function dame_lista_encapsulados_modbus_ip($encapsulado_modbus_ip_seleccionado)
    {
        $encapsulados_modbus_ip = array();
        array_push($encapsulados_modbus_ip, array(
            "id" => ENCAPSULADO_MODBUS_ASCII,
            "nombre" => dame_descripcion_encapsulado_modbus(ENCAPSULADO_MODBUS_ASCII)));
        array_push($encapsulados_modbus_ip, array(
            "id" => ENCAPSULADO_MODBUS_RTU,
            "nombre" => dame_descripcion_encapsulado_modbus(ENCAPSULADO_MODBUS_RTU)));
        array_push($encapsulados_modbus_ip, array(
            "id" => ENCAPSULADO_MODBUS_TCP,
            "nombre" => dame_descripcion_encapsulado_modbus(ENCAPSULADO_MODBUS_TCP)));

        foreach ($encapsulados_modbus_ip as $encapsulado_modbus_ip)
        {
            $lista .= "<option value='".$encapsulado_modbus_ip['id']."'";
			if ($encapsulado_modbus_ip['id'] == $encapsulado_modbus_ip_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".$encapsulado_modbus_ip['nombre']."</option>";
        }
        return ($lista);
    }


    function dame_descripcion_encapsulado_modbus($encapsulado_modbus)
    {
        switch ($encapsulado_modbus)
        {
            case ENCAPSULADO_MODBUS_ASCII:
            {
                $descripcion = "ASCII";
                break;
            }
            case ENCAPSULADO_MODBUS_BINARY:
            {
                $descripcion = "Binario";
                break;
            }
            case ENCAPSULADO_MODBUS_RTU:
            {
                $descripcion = "RTU";
                break;
            }
            case ENCAPSULADO_MODBUS_TCP:
            {
                $descripcion = "TCP";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    function dame_lista_iconos_mapa_tipo_nodo($tipo_nodo, $icono_seleccionado)
    {
        $lista = "";
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $lista .= dame_entrada_lista("Sensor", dame_descripcion_icono_mapa("Sensor"), $icono_seleccionado);
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $lista .= dame_entrada_lista("Actuador", dame_descripcion_icono_mapa("Actuador"), $icono_seleccionado);
                break;
            }
        }
        $lista .= dame_entrada_lista("electrico", dame_descripcion_icono_mapa("electrico"), $icono_seleccionado);
        $lista .= dame_entrada_lista("temperatura", dame_descripcion_icono_mapa("temperatura"), $icono_seleccionado);
        $lista .= dame_entrada_lista("vela", dame_descripcion_icono_mapa("vela"), $icono_seleccionado);
        $lista .= dame_entrada_lista("viento", dame_descripcion_icono_mapa("viento"), $icono_seleccionado);
        $lista .= dame_entrada_lista("agua", dame_descripcion_icono_mapa("agua"), $icono_seleccionado);
        $lista .= dame_entrada_lista("gas", dame_descripcion_icono_mapa("gas"), $icono_seleccionado);
        $lista .= dame_entrada_lista("contador", dame_descripcion_icono_mapa("contador"), $icono_seleccionado);
        $lista .= dame_entrada_lista("movimiento", dame_descripcion_icono_mapa("movimiento"), $icono_seleccionado);
        $lista .= dame_entrada_lista("bombilla", dame_descripcion_icono_mapa("bombilla"), $icono_seleccionado);
        $lista .= dame_entrada_lista("interruptor", dame_descripcion_icono_mapa("interruptor"), $icono_seleccionado);
        $lista .= dame_entrada_lista("sobre", dame_descripcion_icono_mapa("sobre"), $icono_seleccionado);
        return ($lista);
    }


    function dame_lista_colores_mapa_calor($colores_selecionados)
    {
        $lista = "";
        $lista .= dame_entrada_lista(COLORES_AZUL_ROJO, dame_descripcion_colores_mapa_calor(COLORES_AZUL_ROJO), $colores_selecionados);
        $lista .= dame_entrada_lista(COLORES_NEGRO_AMARILLO, dame_descripcion_colores_mapa_calor(COLORES_NEGRO_AMARILLO), $colores_selecionados);
        $lista .= dame_entrada_lista(COLORES_ROJO_AZUL, dame_descripcion_colores_mapa_calor(COLORES_ROJO_AZUL), $colores_selecionados);
        $lista .= dame_entrada_lista(COLORES_ROJO_VERDE, dame_descripcion_colores_mapa_calor(COLORES_ROJO_VERDE), $colores_selecionados);
        $lista .= dame_entrada_lista(COLORES_VERDE_ROJO, dame_descripcion_colores_mapa_calor(COLORES_VERDE_ROJO), $colores_selecionados);
        $lista .= dame_entrada_lista(COLORES_AMARILLO_AZUL, dame_descripcion_colores_mapa_calor(COLORES_AMARILLO_AZUL), $colores_selecionados);
        $lista .= dame_entrada_lista(COLORES_BLANCO_NEGRO, dame_descripcion_colores_mapa_calor(COLORES_BLANCO_NEGRO), $colores_selecionados);
        return ($lista);
    }


    function dame_descripcion_colores_mapa_calor($colores)
    {
        $idiomas = new Idiomas();
        switch ($colores)
        {
            case COLORES_AZUL_ROJO:
            {
                $descripcion = $idiomas->_("Azul")." - ".$idiomas->_("rojo");
                break;
            }
            case COLORES_NEGRO_AMARILLO:
            {
                $descripcion = $idiomas->_("Negro")." - ".$idiomas->_("amarillo");
                break;
            }
            case COLORES_ROJO_AZUL:
            {
                $descripcion = $idiomas->_("Rojo")." - ".$idiomas->_("azul");
                break;
            }
            case COLORES_ROJO_VERDE:
            {
                $descripcion = $idiomas->_("Rojo")." - ".$idiomas->_("verde");
                break;
            }
            case COLORES_VERDE_ROJO:
            {
                $descripcion = $idiomas->_("Verde")." - ".$idiomas->_("rojo");
                break;
            }
            case COLORES_AMARILLO_AZUL:
            {
                $descripcion = $idiomas->_("Amarillo")." - ".$idiomas->_("azul");
                break;
            }
            case COLORES_BLANCO_NEGRO:
            {
                $descripcion = $idiomas->_("Blanco")." - ".$idiomas->_("negro");
                break;
            }
            default:
            {
                $descripcion = $idiomas->_("Desconocido");
                break;
            }
        }
        return ($descripcion);
    }


    function dame_lista_numeros_bits_parada_puerto_serie($numero_bits_parada_seleccionado)
    {
        $numeros_bits_parada = array();
        array_push($numeros_bits_parada, array(
            "id" => 1,
            "nombre" => "1"));
        array_push($numeros_bits_parada, array(
            "id" => 2,
            "nombre" => "2"));

        foreach ($numeros_bits_parada as $numero_bits_parada)
        {
            $lista .= "<option value='".$numero_bits_parada['id']."'";
			if ($numero_bits_parada['id'] == $numero_bits_parada_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($numero_bits_parada['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista);
    }


    function dame_lista_paridades_puerto_serie($paridad_seleccionada)
    {
        $paridades = array();
        array_push($paridades, array(
            "id" => PARIDAD_PUERTO_SERIE_NINGUNA,
            "nombre" => dame_descripcion_paridad_puerto_serie(PARIDAD_PUERTO_SERIE_NINGUNA)));
        array_push($paridades, array(
            "id" => PARIDAD_PUERTO_SERIE_PAR,
            "nombre" => dame_descripcion_paridad_puerto_serie(PARIDAD_PUERTO_SERIE_PAR)));
        array_push($paridades, array(
            "id" => PARIDAD_PUERTO_SERIE_IMPAR,
            "nombre" => dame_descripcion_paridad_puerto_serie(PARIDAD_PUERTO_SERIE_IMPAR)));

        foreach ($paridades as $paridad)
        {
            $lista .= "<option value='".$paridad['id']."'";
			if ($paridad['id'] == $paridad_seleccionada)
			{
				$lista .= " selected";
			}
			$lista .= ">".$paridad['nombre']."</option>";
        }
        return ($lista);
    }


    function dame_descripcion_paridad_puerto_serie($paridad)
    {
        switch ($paridad)
        {
            case PARIDAD_PUERTO_SERIE_NINGUNA:
            {
                $descripcion = "Ninguna";
                break;
            }
            case PARIDAD_PUERTO_SERIE_PAR:
            {
                $descripcion = "Par";
                break;
            }
            case PARIDAD_PUERTO_SERIE_IMPAR:
            {
                $descripcion = "Impar";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    function dame_descripcion_protocolo_serie($protocolo)
    {
        switch ($protocolo)
        {
            case PROTOCOLO_EMIOS:
            {
                $descripcion = "Emios";
                break;
            }
            case PROTOCOLO_API_XBEE:
            {
                $descripcion = "Xbee";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    //
    // Funciones de controles de redes
    //


    function dame_controles_red_pestanya_preferencias(
        $anyadir_red,
        $id_red,
        $nombre,
        $logo_personalizado,
        $nombre_logo,
        $url_logo,
        $titulo_web,
        $tema,
        $paleta_colores_graficas,
        $periodo_completo_informes_defecto)
    {
        $idiomas = new Idiomas();

        $controles = "";
        $controles .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Logo personalizado").": "."</span><br/>
					<select id='logo_personalizado_red' class='select-administracion'>";
        $controles .= dame_lista_valores_si_no($logo_personalizado);
		$controles .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_nombre_logo_red'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre de logo").": "."</span><br/>
					<input type='text' id='nombre_logo_red'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre_logo, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid' id='control_fichero_logo_red'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fichero de logo").": "."</span><br/>
                    <input type='file' id='fichero_logo_red_file'>
                    <input type='text' id='fichero_logo_red_text'
                        class='TLNT_input_valid_characters input-administracion' readonly>
                    <button id='boton_anyadir_modificar_red_seleccionar_fichero_logo' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>";
        if ($anyadir_red == false)
        {
            $origen = ORIGEN_IMAGEN_RED_LOGO;
            $id_origen = $id_red;
            $nombre_ventana = htmlspecialchars($nombre, ENT_QUOTES)." (".$idiomas->_("logo").")";
            $controles .= "
                <button id='boton_mostrar_imagen_logo_red' class='btn-mini btn btn-success boton-mostrar-imagen-fichero-administracion boton_mostrar_imagen_base_datos_ventana' ".
                    "origen='".$origen."' id_origen='".$id_origen."' nombre_ventana='".$nombre_ventana."'";
            if ($logo_personalizado == VALOR_NO)
            {
                $controles .= "style='display: none;'";
            }
            $controles .= "><i class='icon-picture color-blanco'></i></button>";
        }
        $controles .= "
				</div>
			</div>

            <div class='row-fluid' id='control_fichero_logo_pdf_red'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fichero de logo PDF").": "."</span><br/>
                    <input type='file' id='fichero_logo_pdf_red_file'>
                    <input type='text' id='fichero_logo_pdf_red_text'
                        class='TLNT_input_valid_characters input-administracion' readonly>
                    <button id='boton_anyadir_modificar_red_seleccionar_fichero_logo_pdf' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>";
        if ($anyadir_red == false)
        {
            $origen = ORIGEN_IMAGEN_RED_LOGO_PDF;
            $id_origen = $id_red;
            $nombre_ventana = htmlspecialchars($nombre, ENT_QUOTES)." (".$idiomas->_("logo PDF").")";
            $controles .= "
                <button id='boton_mostrar_imagen_logo_pdf_red' class='btn-mini btn btn-success boton-mostrar-imagen-fichero-administracion boton_mostrar_imagen_base_datos_ventana' ".
                    "origen='".$origen."' id_origen='".$id_origen."' nombre_ventana='".$nombre_ventana."'";
            if ($logo_personalizado == VALOR_NO)
            {
                $controles .= "style='display: none;'";
            }
            $controles .= "><i class='icon-picture color-blanco'></i></button>";
        }
        $controles .= "
				</div>
			</div>

            <div class='row-fluid' id='control_url_logo_red'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('URL de logo').": "."</span><br/>
					<input type='text' id='url_logo_red'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($url_logo, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Título de Web').": "."</span><br/>
					<input type='text' id='titulo_web_red'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($titulo_web, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tema").": "."</span><br/>
					<select id='tema_red' class='select-administracion'>";
        $controles .= dame_lista_temas($tema);
		$controles .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Colores de gráficas").": "."</span><br/>
					<select id='paleta_colores_graficas_red' class='select-administracion'>";
        $controles .= dame_lista_paleta_colores_graficas($paleta_colores_graficas);
		$controles .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo completo en informes por defecto").": "."</span><br/>
					<select id='periodo_completo_informes_defecto_red' class='select-administracion'>";
        $controles .= dame_lista_valores_si_no($periodo_completo_informes_defecto);
		$controles .= "
					</select>
				</div>
			</div>";

        return ($controles);
    }


    function dame_controles_red_pestanya_opciones_mapa(
        $anyadir_red,
        $id_red,
        $nombre,
        $tipo_mapa,
        $nombre_mapa,
        $factor_reduccion_imagen_mapa_local,
        $etiquetas_mapa)
    {
        $idiomas = new Idiomas();

        $controles = "";
        $controles .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de mapa").": "."</span><br/>
					<select id='tipo_mapa_red' class='select-administracion'>";
        $controles .= dame_lista_tipos_mapa($tipo_mapa);
		$controles .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre de mapa").": "."</span><br/>
					<input type='text' id='nombre_mapa_red'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$nombre_mapa."'>
				</div>
			</div>

            <div class='row-fluid' id='control_fichero_imagen_mapa_red'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fichero de imagen de mapa").": "."</span><br/>
                    <input type='file' id='fichero_imagen_mapa_red_file'>
                    <input type='text' id='fichero_imagen_mapa_red_text'
                        class='TLNT_input_valid_characters input-administracion' readonly>
                    <button id='boton_anyadir_modificar_red_seleccionar_fichero_imagen_mapa' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>";
        if ($anyadir_red == false)
        {
            $origen = ORIGEN_IMAGEN_RED_MAPA;
            $id_origen = $id_red;
            $nombre_ventana = htmlspecialchars($nombre, ENT_QUOTES)." (".$idiomas->_("mapa").")";
            $controles .= "
                <button id='boton_mostrar_imagen_mapa_red' class='btn-mini btn btn-success boton-mostrar-imagen-fichero-administracion boton_mostrar_imagen_base_datos_ventana' ".
                    "origen='".$origen."' id_origen='".$id_origen."' nombre_ventana='".$nombre_ventana."'";
            if ($tipo_mapa == TIPO_MAPA_INTERNET)
            {
                $controles .= "style='display: none;'";
            }
            $controles .= "><i class='icon-picture color-blanco'></i></button>";
        }
        $controles .= "
				</div>
			</div>

            <div class='row-fluid' id='control_factor_reduccion_imagen_mapa_local_red'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Factor de reducción de imagen de mapa").": "."</span><br/>
					<input type='text' id='factor_reduccion_imagen_mapa_local_red'
						class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$factor_reduccion_imagen_mapa_local."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar etiquetas").": "."</span><br/>
					<select id='etiquetas_mapa_red' class='select-administracion'>";
        $controles .= dame_lista_valores_si_no($etiquetas_mapa);
		$controles .= "
					</select>
				</div>
			</div>";

        return ($controles);
    }


    //
    // Funciones de controles de parámetros de clase de sensor
    //


    function dame_controles_sensor_pestanya_clase_energia_activa($parametros_clase_energia_activa)
    {
        $idiomas = new Idiomas();

        $controles = "";
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        if ($parametros_clase_energia_activa === NULL)
        {
            $parametros_clase_energia_activa = dame_parametros_defecto_clase_energia_activa_pais_tarifas($pais_tarifas_electricas);
        }
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $id_tarifa = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_TARIFA_ELECTRICA];
                $id_grupo_tarifas = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_GRUPO_TARIFAS_ELECTRICAS];
                $cups = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_CUPS];
                $error_maximo_validacion_facturas_energia = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_ENERGIA];
                $error_maximo_validacion_facturas_potencia = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_POTENCIA];
                $error_maximo_validacion_facturas_otros_conceptos_coste_total = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_OTROS_CONCEPTOS_COSTE_TOTAL];
                $tipo_fichero_validacion_facturas = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_TIPO_FICHERO_VALIDACION_FACTURAS];
                $prefijo_fichero_validacion_facturas = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_PREFIJO_FICHERO_VALIDACION_FACTURAS];

                $controles = "";
                $controles .= "
                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tarifa eléctrica")." (".$idiomas->_("sin grupo")."): "."</span><br/>
                            <select id='clase_energia_activa_id_tarifa_sensor' class='chosen-select-administracion'>";
                $controles .= dame_lista_tarifas_electricidad_Espanya(array($id_tarifa), OPCIONES_EXTRA_LISTA_TARIFAS_SIN_GRUPO);
                $controles .= "
                            </select>
                        </div>
                    </div>

                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupo de tarifas eléctricas").": "."</span><br/>
                            <select id='clase_energia_activa_id_grupo_tarifas_sensor' class='chosen-select-administracion'>";
                $controles .= dame_lista_grupos_tarifas(MEDICION_ELECTRICIDAD, $id_grupo_tarifas, OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_NINGUNO);
                $controles .= "
                            </select>
                        </div>
                    </div>

                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("CUPS").": "."</span><br/>
                            <input type='text' id='clase_energia_activa_cups_sensor'
                                class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($cups, ENT_QUOTES)."'>
                        </div>
                    </div>

                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Error máximo en validación de facturas y cierres")." (".$idiomas->_("energía").") (%): "."</span><br/>
                            <input type='text' id='clase_energia_activa_error_maximo_validacion_facturas_sensor_energia_sensor'
                                class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$error_maximo_validacion_facturas_energia."'>
                        </div>
                    </div>

                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Error máximo en validación de facturas y cierres")." (".$idiomas->_("potencia").") (%): "."</span><br/>
                            <input type='text' id='clase_energia_activa_error_maximo_validacion_facturas_sensor_potencia_sensor'
                                class='TLNT_input_float input-administracion' value='".$error_maximo_validacion_facturas_potencia."'>
                        </div>
                    </div>

                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Error máximo en validación de facturas y cierres")." (".$idiomas->_("otros conceptos y coste total").") (%): "."</span><br/>
                            <input type='text' id='clase_energia_activa_error_maximo_validacion_facturas_sensor_otros_conceptos_coste_total_sensor'
                                class='TLNT_input_float input-administracion' value='".$error_maximo_validacion_facturas_otros_conceptos_coste_total."'>
                        </div>
                    </div>

                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de fichero de validación automática de facturas y cierres").": "."</span><br/>
                            <select id='clase_energia_activa_tipo_fichero_validacion_facturas_sensor' class='select-administracion'>";
                $controles .= dame_lista_tipos_fichero_validacion_facturas_electricidad_Espanya($tipo_fichero_validacion_facturas);
                $controles .= "
                            </select>
                        </div>
                    </div>

                    <div class='row-fluid' id='control_clase_energia_activa_prefijo_fichero_validacion_facturas_sensor'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Prefijo de fichero de validación de facturas y cierres").": "."</span><br/>
                            <input type='text' id='clase_energia_activa_prefijo_fichero_validacion_facturas_sensor'
                                class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($prefijo_fichero_validacion_facturas, ENT_QUOTES)."'>
                        </div>
                    </div>";
                break;
            }

            case PAIS_PORTUGAL:
            {
                $id_tarifa = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_TARIFA_ELECTRICA];
                $id_grupo_tarifas = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_GRUPO_TARIFAS_ELECTRICAS];
                $cups = $parametros_clase_energia_activa[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_CUPS];

                $controles = "";
                $controles .= "
                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tarifa eléctrica")." (".$idiomas->_("sin grupo")."): "."</span><br/>
                            <select id='clase_energia_activa_id_tarifa_sensor' class='chosen-select-administracion'>";
                $controles .= dame_lista_tarifas_electricidad_Portugal(array($id_tarifa), OPCIONES_EXTRA_LISTA_TARIFAS_SIN_GRUPO);
                $controles .= "
                            </select>
                        </div>
                    </div>

                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupo de tarifas eléctricas").": "."</span><br/>
                            <select id='clase_energia_activa_id_grupo_tarifas_sensor' class='chosen-select-administracion'>";
                $controles .= dame_lista_grupos_tarifas(MEDICION_ELECTRICIDAD, $id_grupo_tarifas, OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_NINGUNO);
                $controles .= "
                            </select>
                        </div>
                    </div>

                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("CUPS").": "."</span><br/>
                            <input type='text' id='clase_energia_activa_cups_sensor'
                                class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($cups, ENT_QUOTES)."'>
                        </div>
                    </div>";
                break;
            }

            case PAIS_NINGUNO:
            {
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }

        return ($controles);
    }


    function dame_controles_sensor_pestanya_clase_energia_reactiva($parametros_clase_energia_reactiva)
    {
        $idiomas = new Idiomas();

        if ($parametros_clase_energia_reactiva !== NULL)
        {
            $id_sensor_energia_activa = $parametros_clase_energia_reactiva[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_ID_SENSOR_ENERGIA_ACTIVA];
            $tipo_energia_reactiva = $parametros_clase_energia_reactiva[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_TIPO_REACTIVA];
        }
        else
        {
            $id_sensor_energia_activa = NULL;
        }

        $controles = "";
        $controles .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor de energía activa").": "."</span><br/>
                    <select id='clase_energia_reactiva_id_sensor_energia_activa_sensor' class='chosen-select-administracion'>";
        $controles .= dame_lista_sensores(CLASE_SENSOR_ENERGIA_ACTIVA, array($id_sensor_energia_activa), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$controles .= "
                    </select>
                </div>
            </div>
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de energía reactiva").": "."</span><br/>
                    <select id='clase_energia_reactiva_tipo_energia_reactiva' class='select-administracion'>";
        $controles .= dame_lista_tipo_energia_reactiva($tipo_energia_reactiva);
        $controles .= "
                    </select>
                </div>
			</div>";

        return ($controles);
    }


    function dame_controles_sensor_pestanya_clase_cortes_tension($parametros_clase_cortes_tension)
    {
        $idiomas = new Idiomas();

        if ($parametros_clase_cortes_tension !== NULL)
        {
            $id_sensor_energia_activa = $parametros_clase_cortes_tension[INDICE_PARAMETRO_CLASE_SENSOR_CORTES_TENSION_ID_SENSOR_ENERGIA_ACTIVA];
        }
        else
        {
            $id_sensor_energia_activa = NULL;
        }

        $controles = "";
        $controles .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensor de energía activa").": "."</span><br/>
                    <select id='clase_cortes_tension_id_sensor_energia_activa_sensor' class='chosen-select-administracion'>";
        $controles .= dame_lista_sensores(CLASE_SENSOR_ENERGIA_ACTIVA, array($id_sensor_energia_activa), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
		$controles .= "
                    </select>
				</div>
			</div>";

        return ($controles);
    }


    function dame_controles_sensor_pestanya_clase_compra_energia($parametros_clase_compra_energia)
    {
        $idiomas = new Idiomas();

        if ($parametros_clase_compra_energia !== NULL)
        {
            $id_sensor_asociado = $parametros_clase_compra_energia[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_ID_SENSOR_ASOCIADO];
            $cadena_ids_sensores_hijos = $parametros_clase_compra_energia[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_IDS_SENSORES_HIJOS];
            $ids_sensores_hijos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_hijos);
        }
        else
        {
            $id_sensor_asociado = ID_NINGUNO;
            $ids_sensores_hijos = array();
        }

        $controles = "";

        // Nota: El identificador se sensor asociado se oculta
        $controles = "<div id='clase_compra_energia_id_sensor_asociado_sensor_no_visible' hidden>".$id_sensor_asociado."</div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $controles .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores hijos").": "."</span><br/>
                    <div id='select_clase_compra_energia_ids_sensores_hijos_sensor_no_visible' hidden></div>
                    <select id='clase_compra_energia_ids_sensores_hijos_sensor'
                        name='clase_compra_energia_ids_sensores_hijos_sensor'
                        max_selected='".MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_HIJOS_COMPRA_ENERGIA."' multiple='multiple'
                        class='select-administracion' hidden>";
        $controles .= dame_lista_sensores(
            CLASE_SENSOR_ENERGIA_ACTIVA,
            $ids_sensores_hijos,
            OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
        $controles .= "
                    </select>
                </div>
            </div>";

        return ($controles);
    }


    function dame_controles_sensor_pestanya_clase_gas($parametros_clase_gas)
    {
        $idiomas = new Idiomas();

        $controles = "";
        $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
        if ($parametros_clase_gas === NULL)
        {
            $parametros_clase_gas = dame_parametros_defecto_clase_gas_pais_tarifas($pais_tarifas_gas);
        }
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $id_tarifa = $parametros_clase_gas[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_TARIFA_GAS];
                $id_grupo_tarifas = $parametros_clase_gas[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_GRUPO_TARIFAS_GAS];
                $cups = $parametros_clase_gas[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_CUPS];

                $controles = "";
                $controles .= "
                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tarifa de gas")." (".$idiomas->_("sin grupo")."): "."</span><br/>
                            <select id='clase_gas_id_tarifa_sensor' class='chosen-select-administracion'>";
                $controles .= dame_lista_tarifas_gas_Espanya(array($id_tarifa), OPCIONES_EXTRA_LISTA_TARIFAS_SIN_GRUPO);
                $controles .= "
                            </select>
                        </div>
                    </div>

                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupo de tarifas de gas").": "."</span><br/>
                            <select id='clase_gas_id_grupo_tarifas_sensor' class='chosen-select-administracion'>";
                $controles .= dame_lista_grupos_tarifas(MEDICION_GAS, $id_grupo_tarifas, OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_NINGUNO);
                $controles .= "
                            </select>
                        </div>
                    </div>

                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("CUPS").": "."</span><br/>
                            <input type='text' id='clase_gas_cups_sensor'
                                class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($cups, ENT_QUOTES)."'>
                        </div>
                    </div>";
                break;
            }
            case PAIS_NINGUNO:
            {
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
            }
        }

        return ($controles);
    }


    function dame_controles_sensor_pestanya_clase_agua($parametros_clase_agua)
    {
        $idiomas = new Idiomas();

        $controles = "";
        $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
        if ($parametros_clase_agua === NULL)
        {
            $parametros_clase_agua = dame_parametros_defecto_clase_agua_pais_tarifas($pais_tarifas_agua);
        }
        switch ($pais_tarifas_agua)
        {
            case PAIS_ESPANYA:
            {
                $id_tarifa = $parametros_clase_agua[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_TARIFA_AGUA];
                $id_grupo_tarifas = $parametros_clase_agua[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_GRUPO_TARIFAS_AGUA];
                $cups = $parametros_clase_agua[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_CUPS];

                $controles = "";
                $controles .= "
                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tarifa de agua")." (".$idiomas->_("sin grupo")."): "."</span><br/>
                            <select id='clase_agua_id_tarifa_sensor' class='chosen-select-administracion'>";
                $controles .= dame_lista_tarifas_agua_Espanya(array($id_tarifa), OPCIONES_EXTRA_LISTA_TARIFAS_SIN_GRUPO);
                $controles .= "
                            </select>
                        </div>
                    </div>

                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupo de tarifas de agua").": "."</span><br/>
                            <select id='clase_agua_id_grupo_tarifas_sensor' class='chosen-select-administracion'>";
                $controles .= dame_lista_grupos_tarifas(MEDICION_AGUA, $id_grupo_tarifas, OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_NINGUNO);
                $controles .= "
                            </select>
                        </div>
                    </div>

                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("CUPS").": "."</span><br/>
                            <input type='text' id='clase_agua_cups_sensor'
                                class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($cups, ENT_QUOTES)."'>
                        </div>
                    </div>";
                break;
            }
            case PAIS_NINGUNO:
            {
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
            }
        }

        return ($controles);
    }


    function dame_controles_sensor_pestanya_clase_generica($parametros_clase_generica)
    {
        $idiomas = new Idiomas();

        if ($parametros_clase_generica !== NULL)
        {
            $nombre_medida = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_NOMBRE_MEDIDA];
            $unidad_medida = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_UNIDAD_MEDIDA];
            $icono = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_ICONO];
            $colores_mapa_calor_valor = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_COLORES_MAPA_CALOR_VALOR];
            $colores_mapa_calor_incremento = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_COLORES_MAPA_CALOR_INCREMENTO];
            $mostrar_incrementos_calculados = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_MOSTRAR_INCREMENTOS_CALCULADOS];
        }
        else
        {
            $nombre_medida = "";
            $unidad_medida = "";
            $icono = "Sensor";
            $colores_mapa_calor_valor = COLORES_AZUL_ROJO;
            $colores_mapa_calor_incremento = COLORES_AZUL_ROJO;
            $mostrar_incrementos_calculados = VALOR_SI;
        }

        $controles = "";
        $controles .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre de medida").": "."</span><br/>
					<input type='text' id='clase_generica_nombre_medida_sensor'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre_medida, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Unidad de medida").": "."</span><br/>
					<input type='text' id='clase_generica_unidad_medida_sensor'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($unidad_medida, ENT_QUOTES)."'>
				</div>
			</div>";

        $controles .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Icono de mapa").": "."</span><br/>
                    <select id='clase_generica_icono_mapa_sensor' class='select-administracion'>";
        $controles .= dame_lista_iconos_mapa_tipo_nodo(TIPO_NODO_SENSOR, $icono);
		$controles .= "
                    </select>
				</div>
			</div>";

        $controles .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Colores de mapa de calor de valores").": "."</span><br/>
                    <select id='clase_generica_colores_mapa_calor_valor_sensor' class='select-administracion'>";
        $controles .= dame_lista_colores_mapa_calor($colores_mapa_calor_valor);
		$controles .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Colores de mapa de calor de incrementos de valores").": "."</span><br/>
                    <select id='clase_generica_colores_mapa_calor_incremento_sensor' class='select-administracion'>";
        $controles .= dame_lista_colores_mapa_calor($colores_mapa_calor_incremento);
		$controles .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar incrementos calculados").": "."</span><br/>
					<select id='clase_generica_mostrar_incrementos_calculados_sensor' class='select-administracion'>";
        $controles .= dame_lista_valores_si_no($mostrar_incrementos_calculados);
		$controles .= "
					</select>
				</div>
			</div>";

        return ($controles);
    }


    //
    // Funciones de controles de parámetros de tipo de sensor
    //


    function dame_controles_sensor_pestanya_tipo_real($parametros_tipo_real, $calibracion)
    {
        $idiomas = new Idiomas();

        if ($parametros_tipo_real !== NULL)
        {
            $id_axon = $parametros_tipo_real[INDICE_PARAMETRO_TIPO_SENSOR_REAL_ID_AXON];
            $clase_interfaz = $parametros_tipo_real[INDICE_PARAMETRO_TIPO_SENSOR_REAL_CLASE_INTERFAZ];
            $ubicacion_interfaz = $parametros_tipo_real[INDICE_PARAMETRO_TIPO_SENSOR_REAL_UBICACION_INTERFAZ];
            $opciones_interfaz = $parametros_tipo_real[INDICE_PARAMETRO_TIPO_SENSOR_REAL_OPCIONES_INTERFAZ];
        }
        else
        {
            $id_axon = ID_NINGUNO;
            $clase_interfaz = CLASE_NINGUNA;
            $ubicacion_interfaz = NULL;
            $opciones_interfaz = NULL;
        }

        $controles = "";
        $controles .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Axón").": "."</span><br/>
					<select id='id_axon_sensor' class='select-administracion'>";
        $controles .= dame_lista_axones($id_axon);
		$controles .= "
					</select>
				</div>
			</div>";

        $controles .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de interfaz").": "."</span><br/>
					<select id='clase_interfaz_sensor' class='select-administracion'>";
        $controles .= dame_lista_clases_interfaz_sensor($clase_interfaz);
		$controles .= "
					</select>
				</div>
			</div>";

        $controles .= "
            <div class='row-fluid' id='id_controles_clase_interfaz_sensor'>";
        $controles .= dame_controles_clase_interfaz_sensor($clase_interfaz, $ubicacion_interfaz, $opciones_interfaz);
        $controles .= "
            </div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Calibración").": "."</span><br/>
					<input type='text' id='calibracion_interfaz_sensor'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($calibracion, ENT_QUOTES)."'>
                    <span id='boton_sensores_ayuda_calibracion_interfaz_sensor' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
				</div>
			</div>";

        return ($controles);
    }


    function dame_controles_sensor_pestanya_tipo_virtual($parametros_tipo_virtual, $clase_sensor)
    {
        $idiomas = new Idiomas();

        if ($parametros_tipo_virtual !== NULL)
        {
            $clase_sensor_virtual = $parametros_tipo_virtual[INDICE_PARAMETRO_TIPO_SENSOR_VIRTUAL_CLASE_VIRTUAL];
        }
        else
        {
            $clase_sensor_virtual = CLASE_NINGUNA;
        }

        $controles = "";
        $controles .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor virtual").": "."</span><br/>
                    <select id='clase_virtual_sensor' class='select-administracion'>";
        $controles .= dame_lista_clases_sensor_virtual_clase_sensor($clase_sensor, $clase_sensor_virtual);
		$controles .= "
                    </select>
				</div>
			</div>";

        return ($controles);
    }


    function dame_controles_sensor_pestanya_tipo_procesado($parametros_tipo_procesado, $calibracion)
    {
        $idiomas = new Idiomas();

        if ($parametros_tipo_procesado !== NULL)
        {
            $clase_sensor_procesado = $parametros_tipo_procesado[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_CLASE_PROCESADO];
            switch ($clase_sensor_procesado)
            {
                case CLASE_SENSOR_PROCESADO_FUNCION_VALORES:
                {
                    $funcion_valores_horaria = $parametros_tipo_procesado[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_FUNCION_VALORES_HORARIA];
                    $misma_funcion_valores_cuartohoraria = $parametros_tipo_procesado[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_MISMA_FUNCION_VALORES_CUARTOHORARIA];
                    $funcion_valores_cuartohoraria = $parametros_tipo_procesado[INDICE_PARAMETRO_TIPO_SENSOR_PROCESADO_FUNCION_VALORES_CUARTOHORARIA];
                    break;
                }
            }
        }
        else
        {
            $clase_sensor_procesado = CLASE_NINGUNA;
            $funcion_valores_horaria = "";
            $misma_funcion_valores_cuartohoraria = VALOR_SI;
            $funcion_valores_cuartohoraria = "";
        }

        $controles = "";
        $controles .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor de procesado").": "."</span><br/>
                    <select id='clase_procesado_sensor' class='select-administracion'>";
        $controles .= dame_lista_clases_sensor_procesado($clase_sensor_procesado);
		$controles .= "
                    </select>
				</div>
			</div>";

        // Funciones de valores
        $numero_caracteres_actuales_funcion_valores_horaria = strlen($funcion_valores_horaria);
        $numero_caracteres_actuales_funcion_valores_cuartohoraria = strlen($funcion_valores_cuartohoraria);
        $numero_maximo_caracteres_funciones_valores = NUMERO_MAXIMO_CARACTERES_FUNCION_VALORES_SENSOR_PROCESADO;
        $controles .= "
            <div class='row-fluid' id='control_funcion_valores_horaria_procesado_sensor'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Función de valores").": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres_funciones_valores."'>".
                        "(".$numero_caracteres_actuales_funcion_valores_horaria. " / ".$numero_maximo_caracteres_funciones_valores.")"."</span><br/>
                    <textarea id='funcion_valores_horaria_procesado_sensor'
                        class='input-administracion' rows='5'>".htmlspecialchars($funcion_valores_horaria, ENT_QUOTES)."</textarea>
                    <span id='boton_sensores_ayuda_funcion_valores_horaria_procesado_sensor' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
				</div>
			</div>

            <div class='row-fluid' id='control_misma_funcion_valores_cuartohoraria_procesado_sensor'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Misma función de valores cuartohoraria").": "."</span><br/>
					<select id='misma_funcion_valores_cuartohoraria_procesado_sensor' class='select-administracion'>";
        $controles .= dame_lista_valores_si_no($misma_funcion_valores_cuartohoraria);
		$controles .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_funcion_valores_cuartohoraria_procesado_sensor'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Función de valores cuartohoraria").": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres_funciones_valores."'>".
                        "(".$numero_caracteres_actuales_funcion_valores_cuartohoraria. " / ".$numero_maximo_caracteres_funciones_valores.")"."</span><br/>
                    <textarea id='funcion_valores_cuartohoraria_procesado_sensor'
                        class='input-administracion' rows='5'>".htmlspecialchars($funcion_valores_cuartohoraria, ENT_QUOTES)."</textarea>
                    <span id='boton_sensores_ayuda_funcion_valores_cuartohoraria_procesado_sensor' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
				</div>
			</div>

            <div class='row-fluid' id='control_valores_prueba_funcion_valores_procesado_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Valores de prueba de la función de valores").": "."</span><br/>
                    <input type='text' id='valores_prueba_funcion_valores_procesado_sensor'
                        class='TLNT_input_valid_characters input-administracion' value=''>
                    <span id='boton_sensores_ayuda_valores_prueba_funcion_valores_procesado_sensor' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
                </div>
            </div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Calibración").": "."</span><br/>
					<input type='text' id='calibracion_procesado_sensor'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($calibracion, ENT_QUOTES)."'>
                    <span id='boton_sensores_ayuda_calibracion_procesado_sensor' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
				</div>
			</div>";

        return ($controles);
    }


    function dame_controles_sensor_pestanya_tipo_externo($parametros_tipo_externo, $anyadir_sensor, $calibracion)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $calcular_id_externo = false;
        if ($parametros_tipo_externo !== NULL)
        {
            if ($anyadir_sensor == true)
            {
                $calcular_id_externo = true;
            }
            if ($anyadir_sensor == false)
            {
                $id_externo = $parametros_tipo_externo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO];
            }
            $clase_sensor_externo = $parametros_tipo_externo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_CLASE_EXTERNO];
            $opciones_generales_externo = $parametros_tipo_externo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_GENERALES];
            $opciones_valores_externo = $parametros_tipo_externo[INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_OPCIONES_VALORES];
            $opciones_valores_datadis = $parametros_tipo_externo[4];
        }
        else
        {
            $calcular_id_externo = true;
            $clase_sensor_externo = CLASE_NINGUNA;
            $opciones_generales_externo = NULL;
            $opciones_valores_externo = NULL;
            $opciones_valores_datadis = NULL;
        }
        if ($calcular_id_externo == true)
        {
            $consulta_id_externo = "
                SELECT
                    MAX(CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_EXTERNO_ID_EXTERNO + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1), SIGNED INTEGER)) AS id_externo
                FROM sensores
                WHERE
                    tipo = '".TIPO_SENSOR_EXTERNO."'";
            $res_id_externo = $bd_red->ejecuta_consulta($consulta_id_externo);
            if ($res_id_externo == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_id_externo."'");
            }
            $fila_id_externo = $res_id_externo->dame_siguiente_fila();
            $id_externo = $fila_id_externo["id_externo"];
            if ($id_externo === NULL)
            {
                $id_externo = 1;
            }
            else
            {
                $id_externo += 1;
            }
        }

        $controles = "";
        $controles .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Identificador externo").": "."</span><br/>
					<input type='text' id='id_externo_sensor'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($id_externo, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor externo").": "."</span><br/>
                    <select id='clase_externo_sensor' class='select-administracion'>";
        $controles .= dame_lista_clases_sensor_externo($clase_sensor_externo);
		$controles .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid' id='id_controles_clase_externo_sensor'>";
        $controles .= dame_controles_clase_sensor_externo($clase_sensor_externo, $opciones_generales_externo, $opciones_valores_externo, $opciones_valores_datadis);
        $controles .= "
            </div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Calibración").": "."</span><br/>
					<input type='text' id='calibracion_externo_sensor'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($calibracion, ENT_QUOTES)."'>
                    <span id='boton_sensores_ayuda_calibracion_externo_sensor' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
				</div>
			</div>";

        return ($controles);
    }


    //
    // Funciones de controles de parámetros de clase de actuador
    //


    function dame_controles_actuador_pestanya_clase_generica($parametros_clase_generica)
    {
        $idiomas = new Idiomas();

        if ($parametros_clase_generica !== NULL)
        {
            $icono = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_ACTUADOR_GENERICA_ICONO];
        }
        else
        {
            $icono = "Actuador";
        }

        $controles = "";
        $controles .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Icono de mapa").": "."</span><br/>
                    <select id='icono_mapa_actuador' class='select-administracion'>";
        $controles .= dame_lista_iconos_mapa_tipo_nodo(TIPO_NODO_ACTUADOR, $icono);
		$controles .= "
                    </select>
				</div>
			</div>";

        return ($controles);
    }


    //
    // Funciones de controles de parámetros de tipo de actuador
    //


    function dame_controles_actuador_pestanya_tipo($tipo, $parametros_tipo, $calibracion)
    {
        $idiomas = new Idiomas();

        switch ($tipo)
        {
            case TIPO_ACTUADOR_HARDWARE:
            {
                if ($parametros_tipo !== NULL)
                {
                    $id_axon = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_ID_AXON];
                    $clase_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_CLASE_INTERFAZ];
                    $ubicacion_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_UBICACION_INTERFAZ];
                    $opciones_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_OPCIONES_INTERFAZ];
                }
                else
                {
                    $id_axon = ID_NINGUNO;
                    $clase_interfaz = CLASE_NINGUNA;
                    $ubicacion_interfaz = NULL;
                    $opciones_interfaz = NULL;
                }
                break;
            }
            case TIPO_ACTUADOR_SOFTWARE:
            {
                $id_axon = ID_NINGUNO;
                if ($parametros_tipo !== NULL)
                {
                    $clase_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_SOFTWARE_CLASE_INTERFAZ];
                    $ubicacion_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_SOFTWARE_UBICACION_INTERFAZ];
                    $opciones_interfaz = $parametros_tipo[INDICE_PARAMETRO_TIPO_ACTUADOR_SOFTWARE_OPCIONES_INTERFAZ];
                }
                else
                {
                    $clase_interfaz = CLASE_NINGUNA;
                    $ubicacion_interfaz = NULL;
                    $opciones_interfaz = NULL;
                }
                break;
            }
        }

        $controles = "";
        $controles .= "
            <div class='row-fluid' id='control_id_axon_actuador'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Axón").": "."</span><br/>
					<select id='id_axon_actuador' class='select-administracion'>";
        $controles .= dame_lista_axones($id_axon);
		$controles .= "
					</select>
				</div>
			</div>";

        $controles .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de interfaz").": "."</span><br/>
					<select id='clase_interfaz_actuador' class='select-administracion'>";
        $controles .= dame_lista_clases_interfaz_tipo_actuador($tipo, $clase_interfaz);
		$controles .= "
					</select>
				</div>
			</div>";

        $controles .= "
            <div class='row-fluid' id='id_controles_clase_interfaz_actuador'>";
        $controles .= dame_controles_clase_interfaz_actuador($tipo, $clase_interfaz, $ubicacion_interfaz, $opciones_interfaz);
        $controles .= "
            </div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Calibración").": "."</span><br/>
					<input type='text' id='calibracion_interfaz_actuador'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($calibracion, ENT_QUOTES)."'>
                    <span id='boton_actuadores_ayuda_calibracion_actuador' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
				</div>
			</div>";

        return ($controles);
    }


    //
    // Funciones de parámetros por defecto de clases de sensor
    //


    function dame_parametros_defecto_clase_energia_activa_pais_tarifas($pais_tarifas_electricas)
    {
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $id_tarifa = ID_NINGUNO;
                $id_grupo_tarifas = ID_NINGUNO;
                $cups = "";
                $error_maximo_validacion_facturas_energia = 0;
                $error_maximo_validacion_facturas_potencia = 0;
                $error_maximo_validacion_facturas_otros_conceptos_coste_total = 0;
                $tipo_fichero_validacion_facturas = TIPO_NINGUNO;
                $prefijo_fichero_validacion_facturas = "";

                $parametros_defecto_clase_energia_activa = array(
                    $id_tarifa,
                    $id_grupo_tarifas,
                    $cups,
                    $error_maximo_validacion_facturas_energia,
                    $error_maximo_validacion_facturas_potencia,
                    $error_maximo_validacion_facturas_otros_conceptos_coste_total,
                    $tipo_fichero_validacion_facturas,
                    $prefijo_fichero_validacion_facturas);
                break;
            }

            case PAIS_PORTUGAL:
                {
                $id_tarifa = ID_NINGUNO;
                $id_grupo_tarifas = ID_NINGUNO;
                $cups = "";
                $error_maximo_validacion_facturas_energia = 0;
                $error_maximo_validacion_facturas_potencia = 0;
                $error_maximo_validacion_facturas_otros_conceptos_coste_total = 0;
                $tipo_fichero_validacion_facturas = TIPO_NINGUNO;
                $prefijo_fichero_validacion_facturas = "";

                $parametros_defecto_clase_energia_activa = array(
                    $id_tarifa,
                    $id_grupo_tarifas,
                    $cups,
                    $error_maximo_validacion_facturas_energia,
                    $error_maximo_validacion_facturas_potencia,
                    $error_maximo_validacion_facturas_otros_conceptos_coste_total,
                    $tipo_fichero_validacion_facturas,
                    $prefijo_fichero_validacion_facturas);
                break;
            }

            case PAIS_NINGUNO:
            {
                $parametros_defecto_clase_energia_activa = array();
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($parametros_defecto_clase_energia_activa);
    }


    function dame_parametros_defecto_clase_gas_pais_tarifas($pais_tarifas_gas)
    {
        switch ($pais_tarifas_gas)
        {
            case PAIS_ESPANYA:
            {
                $id_tarifa = ID_NINGUNO;
                $id_grupo_tarifas = ID_NINGUNO;
                $cups = "";

                $parametros_defecto_clase_gas = array(
                    $id_tarifa,
                    $id_grupo_tarifas,
                    $cups
                );
                break;
            }
            case PAIS_NINGUNO:
            {
                $parametros_defecto_clase_gas = array();
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
            }
        }
        return ($parametros_defecto_clase_gas);
    }


    function dame_parametros_defecto_clase_agua_pais_tarifas($pais_tarifas_agua)
    {
        switch ($pais_tarifas_agua)
        {
            case PAIS_ESPANYA:
            {
                $id_tarifa = ID_NINGUNO;
                $id_grupo_tarifas = ID_NINGUNO;
                $cups = "";

                $parametros_defecto_clase_agua = array(
                    $id_tarifa,
                    $id_grupo_tarifas,
                    $cups);
                break;
            }
            case PAIS_NINGUNO:
            {
                $parametros_defecto_clase_agua = array();
                break;
            }
            default:
            {
                throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
            }
        }
        return ($parametros_defecto_clase_agua);
    }


    //
    // Funciones auxiliares
    //


    function dame_entrada_lista($id, $nombre, $id_seleccionado)
    {
        $entrada_lista = "<option value='".$id."'";
        if ($id == $id_seleccionado)
        {
            $entrada_lista .= " selected";
        }
        $entrada_lista .= ">".htmlspecialchars($nombre, ENT_QUOTES)."</option>";
        return ($entrada_lista);
    }
?>
