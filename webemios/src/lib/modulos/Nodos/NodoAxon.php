<?php
	session_start();


    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');

    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');


    class NodoAxon extends Nodo
	{
		function calcula_conexion()
		{
			// Se recupera el estado de conexión de la base de datos si no existe en los parámetros
			if (array_key_exists("conexion", $this->params))
			{
				$this->conexion = $this->params["conexion"];
			}
			else
			{
				$bd_red = BaseDatosRed::dame_base_datos();

				$consulta = "
					SELECT
                        conexion
					FROM axones
					WHERE
                        id = '".$bd_red->_($this->id)."'";
				$res = $bd_red->ejecuta_consulta($consulta);
                if (($res == false) || ($res->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
                }

				$fila = $res->dame_siguiente_fila();
				$this->conexion = $fila["conexion"];
			}
		}


		static function dame_cabecera_tabla()
		{
			$idiomas = new Idiomas();

			return (array(
				$idiomas->_("Nombre"),
				$idiomas->_("Conexión"),
                $idiomas->_("Dispositivo"),
                $idiomas->_("Latencia")." (".$idiomas->_("s").")",
                $idiomas->_("Última comunicación")
			));
		}


		function dame_datos_tabla()
		{
            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $nombre = $icono_dato_erroneo;
            $icono_conexion = $icono_dato_erroneo;
            $nombre_dispositivo = $icono_dato_erroneo;
            $latencia = $icono_dato_erroneo;
            $cadena_hora_ultimo_mensaje_local_local = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Icono de conexión
                $icono_conexion = $this->icono_conexion;

                // Nombre de dispositivo
                $id_dispositivo = $this->params["dispositivo"];
                $fila_dispositivo = dame_fila_dispositivo($id_dispositivo);
                $nombre_dispositivo = $fila_dispositivo["nombre"];
                $nombre_dispositivo = htmlspecialchars($nombre_dispositivo, ENT_QUOTES);

                // Latencia
                $latencia = $this->params['latencia'];
                if ($latencia === NULL)
                {
                   $latencia = $this->idiomas->_("Sin datos");
                }

                // Hora de último mensaje
                $cadena_fecha_hora_ultimo_mensaje_base_datos_utc = $this->params['hora_ultimo_mensaje'];
                if ($cadena_fecha_hora_ultimo_mensaje_base_datos_utc === NULL)
                {
                    $cadena_hora_ultimo_mensaje_local_local = $this->idiomas->_("Sin datos");
                }
                else
                {
                    $zona_horaria = dame_zona_horaria_local();
                    $cadena_hora_ultimo_mensaje_local_utc = convierte_formato_fecha($cadena_fecha_hora_ultimo_mensaje_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                    $cadena_hora_ultimo_mensaje_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_ultimo_mensaje_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                }
            }
            catch (Exception $e)
            {
                // Se añade información de la excepción en el log
                $log = dame_log();
                $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $e);

                // Se añade icono de error en el nombre
                if ($nombre_correcto == true)
                {
                    $nombre = "[".$icono_fila_con_errores."] ".$nombre;
                }
            }

            // Se devuelven los datos de la tabla
            return (array(
				$nombre,
				$icono_conexion,
                $nombre_dispositivo,
				$latencia,
                $cadena_hora_ultimo_mensaje_local_local
			));
		}


		function dame_herramientas_detalles_tabla()
		{
            // Diferentes herramientas según el perfil del usuario
            $herramientas = "";
            $herramientas .= "
                <span class='boton-herramientas-detalle-tabla-datos'>
                    <button id='boton_refrescar__".$this->tipo."__".$this->id."' class='btn-mini btn btn-success boton_refrescar_tabla_nodo'>
                        <i class='icon-refresh color-blanco'></i>
                    </button>
                </span>";
            switch ($_SESSION["perfil"])
            {
                case PERFIL_USUARIO_ESTANDAR:
                {
                    break;
                }
                case PERFIL_USUARIO_ADMINISTRADOR:
                {
                    $herramientas .=
                        "<span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_ping__".$this->id."' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_axon'>".$this->idiomas->_("Ping")."</button>
                        </span>";
                    break;
                }
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    $herramientas .=
                        "<span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_ping__".$this->id."' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_axon'>".$this->idiomas->_("Ping")."</button>
                        </span>
                        <span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_leer_sensores__".$this->id."' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_axon'>".$this->idiomas->_("Leer sensores")."</button>
                        </span>";
                    break;
                }
            }

			return ($herramientas);
		}


        function dame_detalles_tabla($mostrar_numeros_sensores_actuadores = null)
		{
        	$bd_red = BaseDatosRed::dame_base_datos();

            $info = "";

            if ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Identificador").": ".$this->id."<br/>";
                $info .= "<br/>";
            }

          	// Se recupera el número de sensores del axón
			$consulta_sensores = "
				SELECT
                    COUNT(*) AS sensores
				FROM sensores
				WHERE
                    (tipo = '".TIPO_SENSOR_REAL."')
                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_REAL_ID_AXON + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($this->id)."')";
			$res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
            if (($res_sensores == false) || ($res_sensores->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensores."'");
            }
			$fila_sensores = $res_sensores->dame_siguiente_fila();
            $numero_sensores = $fila_sensores['sensores'];
            if ($numero_sensores > 0)
            {
                if ($numero_sensores == 1)
                {
                    $texto_sensores_actuadores = $numero_sensores." ".$this->idiomas->_("sensor");
                }
                else
                {
                    $texto_sensores_actuadores = $numero_sensores." ".$this->idiomas->_("sensores");
                }
            }

			// Se recupera el número de actuadores del axón
			$consulta_actuadores = "
				SELECT
                    COUNT(*) AS actuadores
				FROM actuadores
				WHERE
                    (tipo = '".TIPO_ACTUADOR_HARDWARE."')
                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_ID_AXON + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($this->id)."')";
			$res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
            if (($res_actuadores == false) || ($res_actuadores->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_actuadores."'");
            }
			$fila_actuadores = $res_actuadores->dame_siguiente_fila();
            $numero_actuadores = $fila_actuadores['actuadores'];
            if ($numero_actuadores > 0)
            {
                if ($numero_sensores > 0)
                {
                    $texto_sensores_actuadores .= " ".$this->idiomas->_("y")." ";
                }
                if ($numero_actuadores == 1)
                {
                    $texto_sensores_actuadores .= $numero_actuadores." ".$this->idiomas->_("actuador");
                }
                else
                {
                    $texto_sensores_actuadores .= $numero_actuadores." ".$this->idiomas->_("actuadores");
                }
            }
            $id_red = $_SESSION["id_red"];
            $bd_red = BaseDatosRed::dame_base_datos();
            $consulta = "SELECT clientes.nombre FROM clientes INNER JOIN redes ON clientes.id=redes.cliente WHERE redes.id = ".$id_red;
            $res = $bd_red->ejecuta_consulta($consulta);
            $fila = $res->dame_siguiente_fila();
            $nombre_cliente = $fila["nombre"];
            if ($nombre_cliente != 'Future Sense')
                {
                $info .= "<i class='icon-info-sign color-azul'></i> ";
                }
            if ($numero_sensores + $numero_actuadores > 0)
            {
                $info .= $this->idiomas->_("Este axón tiene")." ".$texto_sensores_actuadores;
            }
            else
            {
                if ($nombre_cliente != 'Future Sense')
                {
                $info .= $this->idiomas->_("Este axón no tiene sensores ni actuadores");
                }
            }
            $info .= "<br/>";

            return ($info);
		}


		function dame_info_topologia_red($clase_sensor = NULL, $clase_actuador = NULL)
		{
			$bd_red = BaseDatosRed::dame_base_datos();

            $hijos = array();

			$consulta_sensores = "
				SELECT
                    id,
                    nombre
				FROM sensores
				WHERE
                    (tipo = '".TIPO_SENSOR_REAL."')
                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_REAL_ID_AXON + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($this->id)."')";
            if ($clase_sensor != CLASE_TODAS)
            {
                $consulta_sensores .= "
                    AND (clase = '".$bd_red->_($clase_sensor)."')";
            }
            $consulta_sensores .= "
                ORDER BY nombre ASC";
			$res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
            if ($res_sensores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_sensores."'");
            }
            while ($fila_sensor = $res_sensores->dame_siguiente_fila())
            {
                $hijo = Nodo::crea_nodo($fila_sensor["id"], TIPO_NODO_SENSOR, array("nombre" => $fila_sensor["nombre"], "conexion" => $this->conexion));
                array_push($hijos, $hijo->dame_info_topologia_red());
            }

			$consulta_actuadores = "
				SELECT
                    id,
                    nombre
				FROM actuadores
				WHERE
                    (tipo = '".TIPO_ACTUADOR_HARDWARE."')
                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_ID_AXON + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($this->id)."')";
            if ($clase_actuador != CLASE_TODAS)
            {
                $consulta_actuadores .= "
                    AND (clase = '".$bd_red->_($clase_actuador)."')";
            }
            $consulta_actuadores .= "
                ORDER BY nombre ASC";
			$res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
            if ($res_actuadores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_actuadores."'");
            }
            while ($fila_actuador = $res_actuadores->dame_siguiente_fila())
            {
                $hijo = Nodo::crea_nodo($fila_actuador["id"], TIPO_NODO_ACTUADOR, array("nombre" => $fila_actuador['nombre'], "conexion" => $this->conexion));
                array_push($hijos, $hijo->dame_info_topologia_red());
            }

            $info = array(
				"nombre" => $this->params["nombre"],
				"info_nodo" => $this->dame_tooltip_topologia_red(),
				"color_nodo" => $this->dame_color_nodo_topologia_red(),
				"children" => $hijos
			);
            return ($info);
		}


        function dame_tooltip_topologia_red()
		{
			$tooltip = Nodo::dame_tooltip_topologia_red();
			if ($this->params["latencia"] !== NULL)
			{
                $tooltip .= ", ".$this->idiomas->_("latencia").": ".$this->params["latencia"]." ".$this->idiomas->_("s");
			}

			return ($tooltip);
		}


        function dame_conf()
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta_redes = "
                SELECT
                    zona_horaria
                FROM redes
                WHERE
                    id = '".$bd_red->_($this->params['red'])."'";
            $res_redes = $bd_red->ejecuta_consulta($consulta_redes);
            if (($res_redes == false) || ($res_redes->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_redes."'");
            }
            $fila_red = $res_redes->dame_siguiente_fila();
            $zona_horaria = $fila_red["zona_horaria"];

            $conf = array(
				"ID" => $this->params['id'],
				"RED" => $this->params['red'],
                "ZONA_HORARIA" => $zona_horaria,
                "WEB_EMIOS" => dame_valor_entrada_ini(INI_WEB_EMIOS)
			);

            // Se añade la configuración de cada uno de los sensores
			$sensores = array();
			$consulta_sensores = "
				SELECT *
				FROM sensores
				WHERE
                    (tipo = '".TIPO_SENSOR_REAL."')
                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_SENSOR_REAL_ID_AXON + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($this->id)."')";
			$res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
            if ($res_sensores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_sensores."'");
            }
            while ($fila_sensores = $res_sensores->dame_siguiente_fila())
            {
                $sensor = Nodo::crea_nodo($fila_sensores["id"], TIPO_NODO_SENSOR, $fila_sensores);
                array_push($sensores, $sensor->dame_conf());
            }
			$conf["SENSORES"] = $sensores;

            // Se añade la configuración de cada uno de los actuadores
			$actuadores = array();
			$consulta_actuadores = "
				SELECT *
				FROM actuadores
				WHERE
                    (tipo = '".TIPO_ACTUADOR_HARDWARE."')
                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_ID_AXON + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$bd_red->_($this->id)."')";
			$res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
            if ($res_actuadores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_actuadores."'");
            }
            while ($fila_actuador = $res_actuadores->dame_siguiente_fila())
            {
                $actuador = Nodo::crea_nodo($fila_actuador["id"], TIPO_NODO_ACTUADOR, $fila_actuador);
                array_push($actuadores, $actuador->dame_conf());
            }
			$conf["ACTUADORES"] = $actuadores;

            return ($conf);
		}
	}
?>
