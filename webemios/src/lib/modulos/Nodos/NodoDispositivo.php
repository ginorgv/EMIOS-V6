<?php
	session_start();

    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');


	class NodoDispositivo extends Nodo
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
					FROM dispositivos
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
				$idiomas->_("Arquitectura"),
				$idiomas->_("IP pública"),
                $idiomas->_("Latencia")." (".$idiomas->_("s").")",
                $idiomas->_("Último estado")
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
            $arquitectura = $icono_dato_erroneo;
            $ip_publica = $icono_dato_erroneo;
            $latencia = $icono_dato_erroneo;
            $ultimo_estado = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Icono de conexión
                $icono_conexion = $this->icono_conexion;

                // Arquitectura
                $arquitectura = NodoDispositivo::dame_descripcion_arquitectura_dispositivo($this->params["arquitectura"]);

                // IP pública
                $ip_publica = $this->params["ip_publica"];
                if (($ip_publica === NULL) || ($ip_publica == ""))
                {
                    $ip_publica = $this->idiomas->_("Ninguna");
                }
                $nombre_dispositivo = htmlspecialchars($ip_publica, ENT_QUOTES);

                // Latencia
                $latencia = $this->params['latencia'];
                if ($latencia === NULL)
                {
                   $latencia = $this->idiomas->_("Sin datos");
                }

                // Iconos de alarma
                $iconos_alarma = "";
                $timeout_envio_estado_activado = $this->dame_timeout_envio_estado_activado();
                if ($timeout_envio_estado_activado == true)
                {
                    $iconos_alarma .= "<i class='icon-bell-alt color-rojo'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("timeout"), ENT_QUOTES)."</texto></i>";
                }

                // Último estado
                if ($this->params["hora_ultimo_estado"] === NULL)
                {
                    $ultimo_estado = $this->idiomas->_("Sin estado recibido");
                }
                else
                {
                    $zona_horaria = dame_zona_horaria_local();
                    $cadena_fecha_hora_ultimo_estado_base_local_utc = convierte_formato_fecha($this->params["hora_ultimo_estado"], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                    $cadena_fecha_hora_ultimo_estado_base_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_ultimo_estado_base_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                    $ultimo_estado = $cadena_fecha_hora_ultimo_estado_base_local_local;
                    if ($iconos_alarma != "")
                    {
                        $ultimo_estado .= " [".$iconos_alarma."]";
                    }
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
				$arquitectura,
				$ip_publica,
                $latencia,
                $ultimo_estado,
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
            $id_red = $_SESSION["id_red"];
            $bd_red = BaseDatosRed::dame_base_datos();
            $consulta = "SELECT clientes.nombre FROM clientes INNER JOIN redes ON clientes.id=redes.cliente WHERE redes.id = ".$id_red;
            $res = $bd_red->ejecuta_consulta($consulta);
            $fila = $res->dame_siguiente_fila();
            $nombre_cliente = $fila["nombre"];
            switch ($_SESSION["perfil"])
            {
                case PERFIL_USUARIO_ESTANDAR:
                {
                    break;
                }
                case PERFIL_USUARIO_ADMINISTRADOR:
                {
                    
                    if ($nombre_cliente == 'Future Sense')
                    {
                        $herramientas .= "
                        <span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_reiniciar__".$this->id."' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_dispositivo'>".
                                $this->idiomas->_("Reiniciar")."
                            </button>
                        </span>
                        <span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_actualiza__".$this->id."__actualiza' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_dispositivo'>".
                                $this->idiomas->_("Actualizar dispositivo")."
                            </button>
                        </span>
                        <span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_actualiza__".$this->id."__anyade' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_dispositivo'>".
                                $this->idiomas->_("Añadir versión")."
                            </button>
                        </span>
                        <span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_actualiza__".$this->id."__elimina' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_dispositivo'>".
                                $this->idiomas->_("Eliminar versión")."
                            </button>
                        </span>";
                    }
                    else
                    {
                        $herramientas .= "
                        <span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_leer_estado__".$this->id."' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_dispositivo'>".
                                $this->idiomas->_("Leer estado")."
                            </button>
                        </span>";
                    }
                    break;
                }
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    if ($nombre_cliente == 'Future Sense')
                    {
                        $herramientas .= "
                        <span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_reiniciar__".$this->id."' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_dispositivo'>".
                                $this->idiomas->_("Reiniciar")."
                            </button>
                        </span>
                        <span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_actualiza__".$this->id."__actualiza' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_dispositivo'>".
                                $this->idiomas->_("Actualizar dispositivo")."
                            </button>
                        </span>
                        <span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_actualiza__".$this->id."__anyade' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_dispositivo'>".
                                $this->idiomas->_("Añadir versión")."
                            </button>
                        </span>
                        <span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_actualiza__".$this->id."__elimina' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_dispositivo'>".
                                $this->idiomas->_("Eliminar versión")."
                            </button>
                        </span>";
                    }
                    else
                    {
                        $herramientas .= "
                        <span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_reiniciar__".$this->id."' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_dispositivo'>".
                                $this->idiomas->_("Reiniciar")."
                            </button>
                        </span>
                        <span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_recargar_configuracion__".$this->id."' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_dispositivo'>".
                                $this->idiomas->_("Recargar configuración")."
                            </button>
                        </span>
                        <span class='boton-herramientas-detalle-tabla-datos'>
                            <button id='boton_leer_estado__".$this->id."' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_dispositivo'>".
                                $this->idiomas->_("Leer estado")."
                            </button>
                        </span>";
                    }
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

            // Se recupera la información del dispositivo
			$consulta_dispositivo = "
				SELECT *
				FROM dispositivos
				WHERE
                    id = '".$bd_red->_($this->id)."'";
			$res_dispositivo = $bd_red->ejecuta_consulta($consulta_dispositivo);
            if (($res_dispositivo == false) || ($res_dispositivo->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_dispositivo."'");
            }
			$fila_dispositivo = $res_dispositivo->dame_siguiente_fila();

            if ($fila_dispositivo['descripcion'] != "")
			{
				$info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Descripción").": ".htmlspecialchars($fila_dispositivo['descripcion'], ENT_QUOTES)."<br/>";
			}


            if ($fila_dispositivo["arquitectura"]== ARQUITECTURA_DISPOSITIVO_BYE_RADON)
            {
                // Se muestra el IMEI BYE RADON
            $info .= "<i class='icon-info-sign color-azul'></i> ".
            $this->idiomas->_("IMEI").": ".$fila_dispositivo["imei"]."<br/>";
            
            // Se obtiene el listado de sensores que contengan en el nombre el IMEI
            $consulta_sensores = "
				SELECT *
				FROM sensores
				WHERE
                    nombre LIKE '".$bd_red->_($fila_dispositivo['imei'])."%'";
			$res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
            if (($res_sensores == false) || ($res_sensores->dame_numero_filas() == 0))
            {
                $info .= "<br/><i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("No tiene sensores asociados").":<br/>";
            }
            else
            {
                $fila_sensores = $res_sensores->dame_siguiente_fila();
                // crear consulta de sensores para mostrarlos como un listado dentro del dispositivo de RADON
                $info .= "<br/><i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Sensores").":<br/>";

                // Se muestra una lista con los sensores correspondientes
                $info .= "<ul><li>".$fila_sensores['nombre']."</li>";
                $fila_sensores = $res_sensores->dame_siguiente_fila();
                $info .= "<li>".$fila_sensores['nombre']."</li>";
                $fila_sensores = $res_sensores->dame_siguiente_fila();
                $info .= "<li>".$fila_sensores['nombre']."</li></ul><br/>";
            }

            if ($fila_dispositivo['descripcion'] != "")
			{
				$info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Descripción").": ".htmlspecialchars($fila_dispositivo['descripcion'], ENT_QUOTES)."<br/>";
			}

            
            }
            else
            {
            // Se muestra la dirección MAC
            $info .= "<i class='icon-info-sign color-azul'></i> ".
            $this->idiomas->_("Dirección MAC").": ".$fila_dispositivo["direccion_mac"]."<br/>";

            // Se muestra la ip local
            $info .= "<i class='icon-info-sign color-azul'></i> ".
            $this->idiomas->_("Dirección IP local").": ".$fila_dispositivo["ip_local"]."<br/>";

            }
            
            // Se muestra la frecuencia de actualización
            if ($fila_dispositivo['frecuencia_actualizacion'] != 0)
            {
                $texto_periodo_frecuencia_actualizacion = dame_texto_periodo($fila_dispositivo['frecuencia_actualizacion']);
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Frecuencia de actualización").": ".$texto_periodo_frecuencia_actualizacion."<br/>";
            }

            // Se muestra la frecuencia de envío de estado
            if ($fila_dispositivo['frecuencia_envio_estado'] != 0)
            {
                $texto_periodo_frecuencia_envio_estado = dame_texto_periodo($fila_dispositivo['frecuencia_envio_estado']);
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Frecuencia de envío de estado").": ".$texto_periodo_frecuencia_envio_estado."<br/>";
            }

            $id_red = $_SESSION["id_red"];
            $bd_red = BaseDatosRed::dame_base_datos();
            $consulta = "SELECT clientes.nombre FROM clientes INNER JOIN redes ON clientes.id=redes.cliente WHERE redes.id = ".$id_red;
            $res = $bd_red->ejecuta_consulta($consulta);
            $fila = $res->dame_siguiente_fila();
            $nombre_cliente = $fila["nombre"];
            // Si hay timeout de envío de estado, se muestra un aviso
            if ($nombre_cliente != 'Future Sense') {
                $timeout_envio_estado = ($fila_dispositivo['timeout_envio_estado'] == VALOR_SI);
                if ($timeout_envio_estado == true)
                {
                    if ($fila_dispositivo['hora_ultimo_estado'] !== NULL)
                    {
                        $fecha_actual_utc = dame_fecha_hora_actual_utc();
                        $fecha_ultimo_estado_utc = convierte_cadena_a_fecha($fila_dispositivo['hora_ultimo_estado'], FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                        $segundos_transcurridos = $fecha_actual_utc->getTimestamp() - $fecha_ultimo_estado_utc->getTimestamp();
                        $info .= "<i class='icon-bell-alt color-rojo'></i> ".
                            $this->idiomas->_("No se ha recibido el estado del dispositivo en")." ".dame_texto_periodo($segundos_transcurridos)."<br/>";
                    }
                    else
                    {
                        $info .= "<i class='icon-bell-alt color-rojo'></i> ".
                            $this->idiomas->_("No se ha recibido ningún estado del dispositivo")."<br/>";
                    }
                }
    
            }
            
			// Se recupera la información del axón en el dispositivo (si lo hay)
			$consulta_axones = "
				SELECT
                    id,
                    nombre
				FROM axones
				WHERE
					(dispositivo = '".$bd_red->_($this->id)."')";
			$res_axones = $bd_red->ejecuta_consulta($consulta_axones);
            if ($res_axones == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_axones."'");
            }
            $numero_axones = $res_axones->dame_numero_filas();

            if ($nombre_cliente != 'Future Sense')
            {
			    if ($numero_axones > 0)
			    {
                    $fila_axon = $res_axones->dame_siguiente_fila();
                    $nombre_axon = htmlspecialchars($fila_axon['nombre'], ENT_QUOTES);
                    $id_axon = $fila_axon['id'];

                    // Se crea un nodo axón para recuperar su información detallada
                    $info .= "<br/>";
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Este dispositivo contiene el axón").": ".$nombre_axon."<br/>";
                    $nodo = Nodo::crea_nodo($id_axon, TIPO_NODO_AXON);
                    $info .= $nodo->dame_detalles_tabla();
                    $info .= "<br/>";
                }
                else
                {
                    $info .= "<br/>";
			    	$info .= "<i class='icon-info-sign color-azul'></i> ".
			    		$this->idiomas->_("Este dispositivo no tiene axón")."<br/>";
                    $info .= "<br/>";
			    }
            }
            // Último estado recibido del dispositivo
            if ($fila_dispositivo["ultimo_estado"] === NULL)
            {
                if ($nombre_cliente != 'Future Sense')
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Este dispositivo no tiene información de estado");
                }
                else
                {
                    /* TEMPORAL
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("ICCID").": 8934071400006151466F<br/>";
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("BG96 Firmware").": BG96MAR03A06M1G_01.007.01.007<br/>";
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Registration status").": 0,1<br/><br/>";
                    $info .= "<div class='contenido-detalle-tabla-datos' id='contenedor_control_datos_test__".$this->id."' style='display:none'>";
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Set band search").": +QCFG: 'band',0xf,0x80084,0x80084<br/>";
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Set automatic scan mode").": +QCFG: 'nwscanmode',0<br/>";
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Network Search Sequence (3-1-2)").": +QCFG: 'nwscanseq',030201<br/>";
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Set Network Category to IoT Mode").": +QCFG: 'iotopmode',2<br/>";
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Set Service Domain to PS").": +QCFG: 'servicedomain',2<br/>";
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Set the APN automatically from the network").": +CGDCONT: 1,'IP','','0.0.0.0',0,0,0,0<br/>";
                    $info .= "</div>";   */

                }
			}
            else
            {
                $parametros_ultimo_estado = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_dispositivo["ultimo_estado"]);
                $memoria_libre = $parametros_ultimo_estado[0];
                $utilizacion_disco = $parametros_ultimo_estado[1];
                $tiempo_funcionamiento = $parametros_ultimo_estado[2];
                $uso_cpu = $parametros_ultimo_estado[3];
                $temperatura_cpu = $parametros_ultimo_estado[4];
                $version_fuentes = $parametros_ultimo_estado[5];
                $version_fuentes_web = $parametros_ultimo_estado[6];

                $zona_horaria = dame_zona_horaria_local();
                $fecha_hora_utc = convierte_formato_fecha($fila_dispositivo["hora_ultimo_estado"], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $fecha_hora_local = cambia_zona_horaria_cadena_fecha_hora($fecha_hora_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Último estado del dispositivo")." (".$fecha_hora_local.")"."<br/>";

                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Memoria libre").": ".$memoria_libre." ".$this->idiomas->_("MB")."<br/>";
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Porcentaje de uso de disco").": ".$utilizacion_disco." "."%"."<br/>";
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Tiempo de funcionamiento").": ".dame_texto_periodo($tiempo_funcionamiento)."<br/>";
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Porcentaje de uso de CPU (últimos 5 minutos)").": ".$uso_cpu." "."%"."<br/>";
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Temperatura de CPU").": ".$temperatura_cpu." ".$this->idiomas->_("ºC")."<br/>";

                if ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR)
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Versión de fuentes").": ".$version_fuentes."<br/>";
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Versión de fuentes web").": ".$version_fuentes_web."<br/>";
                }
            }

			return ($info);
		}


        function dame_info_topologia_red($clase_sensor = NULL, $clase_actuador = NULL)
		{
            $bd_red = BaseDatosRed::dame_base_datos();

			$hijos = array();
            $numero_nodos_finales = 0;

			$consulta = "
				SELECT
                    id,
                    nombre,
                    latencia
				FROM axones
				WHERE
					dispositivo = '".$bd_red->_($this->id)."'
                ORDER BY nombre ASC";
			$res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
			while ($fila = $res->dame_siguiente_fila())
			{
				$hijo = Nodo::crea_nodo($fila["id"], TIPO_NODO_AXON, $fila);
                $topologia_red_hijo = $hijo->dame_info_topologia_red($clase_sensor, $clase_actuador);
				array_push($hijos, $topologia_red_hijo);

                if ($topologia_red_hijo["children"] == 0)
                {
                    $numero_nodos_finales = $numero_nodos_finales + 1;
                }
                else
                {
                    $numero_nodos_finales = $numero_nodos_finales + count($topologia_red_hijo["children"]);
                }
			}
            if ($numero_nodos_finales == 0)
            {
                $numero_nodos_finales = 1;
            }

			$info = array(
				"nombre" => $this->params["nombre"],
				"info_nodo" => $this->dame_tooltip_topologia_red(),
				"color_nodo" => $this->dame_color_nodo_topologia_red(),
				"children" => $hijos,
                "numero_nodos_finales" => $numero_nodos_finales
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


		function dame_info_tooltip_mapa($id_mapa)
		{
            // Arquitectura y conexión
            $info = "";
			$info .= $this->idiomas->_("Arquitectura").": ".NodoDispositivo::dame_descripcion_arquitectura_dispositivo($this->params["arquitectura"])."<br/>";
            if ($this->conexion != "NA")
            {
                $descripcion_conexion = Nodo::dame_descripcion_conexion($this->conexion);
                $info .= $this->icono_conexion.": ".$descripcion_conexion."<br/>";
            }

            return ($info);
		}


		function dame_conf()
		{
            $entradas_ini = dame_entradas_ini();
            $ip_servidor_emios = $entradas_ini[INI_IP_SERVIDOR_EMIOS];
            $web_emios = $entradas_ini[INI_WEB_EMIOS];

            // Si se quiere cambiar una red de servidor, se utiliza esta clave para que sus dispositivos recuperen
            // la configuración del nuevo servidor web emios
            $clave_web_emios_red = INI_WEB_EMIOS."_red_".$this->params['red'];
            if (array_key_exists($clave_web_emios_red, $entradas_ini))
            {
                $web_emios = $entradas_ini[$clave_web_emios_red];
            }

            // Si se quiere cambiar un dispositivo de servidor, se utiliza esta clave para que recupere
            // la configuración del nuevo servidor web emios
            $clave_web_emios_dispositivo = INI_WEB_EMIOS."_dispositivo_".$this->params['id'];
            if (array_key_exists($clave_web_emios_dispositivo, $entradas_ini))
            {
                $web_emios = $entradas_ini[$clave_web_emios_dispositivo];
            }

            $conf = array(
				"ID" => $this->params['id'],
				"RED" => $this->params['red'],
                "IP_SERVIDOR_EMIOS" => $ip_servidor_emios,
                "WEB_EMIOS" => $web_emios,
				"FREC_ACTUALIZACION" => $this->params['frecuencia_actualizacion'],
				"ARQUITECTURA" => $this->params["arquitectura"],
                "FREC_ENVIO_ESTADO" => $this->params["frecuencia_envio_estado"]
			);

            return ($conf);
		}


        //
        // Funciones de iconos del mapa
        //


        function anyade_rutas_imagenes_satelite(&$rutas_fila_imagenes_satelite_1, &$rutas_fila_imagenes_satelite_2)
		{
            $timeout_envio_estado_activado = $this->dame_timeout_envio_estado_activado();
            if ($timeout_envio_estado_activado == true)
            {
                $ruta_imagen_satelite = $_SESSION["directorio"]."/rsc/imagenes/reloj.png";
                array_push($rutas_fila_imagenes_satelite_1, $ruta_imagen_satelite);
            }
        }


        //
        //  Funciones auxiliares
        //


        function dame_timeout_envio_estado_activado()
        {
            $timeout_envio_estado_activado = ($this->params['timeout_envio_estado'] == VALOR_SI);
            return ($timeout_envio_estado_activado);
        }


        //
        // Arquitecturas de dispositivo
        //


        static function dame_arquitecturas_dispositivo()
        {
            $arquitecturas_dispositivo = array();
            array_push($arquitecturas_dispositivo, ARQUITECTURA_DISPOSITIVO_RPI);
            array_push($arquitecturas_dispositivo, ARQUITECTURA_DISPOSITIVO_BABELBOX);
            array_push($arquitecturas_dispositivo, ARQUITECTURA_DISPOSITIVO_BYE_RADON);
            return ($arquitecturas_dispositivo);
        }


        static function dame_descripcion_arquitectura_dispositivo($arquitectura_dispositivo)
        {
            switch ($arquitectura_dispositivo)
            {
                case ARQUITECTURA_DISPOSITIVO_RPI:
                {
                    $descripcion_arquitectura_dispositivo = "Raspberry Pi";
                    break;
                }
                case ARQUITECTURA_DISPOSITIVO_BABELBOX:
                {
                    $descripcion_arquitectura_dispositivo = "BabelBox";
                    break;
                }
                case ARQUITECTURA_DISPOSITIVO_BYE_RADON:
                {
                    $descripcion_arquitectura_dispositivo = "Bye Radon";
                    break;
                }
                default:
                {
                    $descripcion_arquitectura_dispositivo = "Desconocida";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_arquitectura_dispositivo));
        }
	}
?>
