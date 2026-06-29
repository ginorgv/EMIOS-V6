<?php
	if (session_status() === PHP_SESSION_NONE) { session_start(); }

	include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');


	// Includes de las clases derivadas de nodo
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/NodoRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/NodoDispositivo.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/NodoAxon.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/NodoSensor.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/NodoGrupoSensores.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/NodoActuador.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/NodoGrupoActuadores.php');


    // Clase base de nodo de la que heredan todos los tipos de nodos
	class Nodo
	{
        // Funciones estáticas de nodo


		// Crea un nodo del tipo especificado
		static function crea_nodo($id, $tipo, $params = array())
		{
			$idiomas = new Idiomas();

			switch ($tipo)
			{
				case TIPO_NODO_RED:
                {
					$nodo = new NodoRed($tipo, $idiomas->_("Red"), $id, $params);
                    break;
                }
                case TIPO_NODO_DISPOSITIVO:
                {
					$nodo = new NodoDispositivo($tipo, $idiomas->_("Dispositivo"), $id, $params);
                    break;
                }
				case TIPO_NODO_AXON:
                {
					$nodo = new NodoAxon($tipo, $idiomas->_("Axón"), $id, $params);
                    break;
                }
				case TIPO_NODO_SENSOR:
                {
					$nodo = new NodoSensor($tipo, $idiomas->_("Sensor"), $id, $params);
                    break;
                }
                case TIPO_NODO_GRUPO_SENSORES:
                {
					$nodo = new NodoGrupoSensores($tipo, $idiomas->_("Sensor"), $id, $params);
                    break;
                }
				case TIPO_NODO_ACTUADOR:
                {
					$nodo = new NodoActuador($tipo, $idiomas->_("Actuador"), $id, $params);
                    break;
                }
				case TIPO_NODO_GRUPO_ACTUADORES:
                {
					$nodo = new NodoGrupoActuadores($tipo, $idiomas->_("Grupo de actuadores"), $id, $params);
                    break;
                }
				default:
                {
					throw new Exception("Tipo de nodo desconocido");
                }
			}
            return ($nodo);
		}


		// Devuelve la cabecera para la tabla para el tipo de nodo especificado
		// (se evita tener que crear un nodo auxiliar sólo para recuperar esta cabecera)
		static function dame_cabecera_tabla_tipo_nodo($tipo_nodo)
		{
			switch ($tipo_nodo)
			{
				case TIPO_NODO_RED:
                {
					$cabecera_tabla = NodoRed::dame_cabecera_tabla();
                    break;
                }
                case TIPO_NODO_DISPOSITIVO:
                {
					$cabecera_tabla = NodoDispositivo::dame_cabecera_tabla();
                    break;
                }
				case TIPO_NODO_AXON:
                {
					$cabecera_tabla = NodoAxon::dame_cabecera_tabla();
                    break;
                }
				case TIPO_NODO_SENSOR:
                {
					$cabecera_tabla = NodoSensor::dame_cabecera_tabla();
                    break;
                }
                case TIPO_NODO_GRUPO_SENSORES:
                {
					$cabecera_tabla = NodoGrupoSensores::dame_cabecera_tabla();
                    break;
                }
				case TIPO_NODO_ACTUADOR:
                {
					$cabecera_tabla = NodoActuador::dame_cabecera_tabla();
                    break;
                }
				case TIPO_NODO_GRUPO_ACTUADORES:
                {
					$cabecera_tabla = NodoGrupoActuadores::dame_cabecera_tabla();
                    break;
                }
				default:
                {
					throw new Exception("Tipo de nodo desconocido");
                }
			}
            return ($cabecera_tabla);
		}


		// Devuelve el nodo raiz de la red actual
		static function dame_nodo_red()
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta = "
				SELECT nombre
				FROM redes
				WHERE
                    id = '".$_SESSION["id_red"]."'";
            $res = $bd_red->ejecuta_consulta($consulta);
            if (($res == false) || ($res->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
            }
            $fila = $res->dame_siguiente_fila();
			$nodo = Nodo::crea_nodo($_SESSION["id_red"], TIPO_NODO_RED, $fila);
			return ($nodo);
		}


		// Devuelve la configuración para el axón con id
		static function dame_configuracion_axon($id)
		{
			$bd_red = BaseDatosRed::dame_base_datos();

			$consulta = "
				SELECT *
				FROM axones
				WHERE
                    id = '".$bd_red->_($id)."'";
			$res = $bd_red->ejecuta_consulta($consulta);
            if (($res == false) || ($res->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
            }
			$fila = $res->dame_siguiente_fila();
			$nodo = Nodo::crea_nodo($id, TIPO_NODO_AXON, $fila);
            $conf = $nodo->dame_conf();
			return (json_encode($conf));
		}


		// Devuelve la configuración para el dispositivo con mac
		static function dame_configuracion_dispositivo_mac($mac)
		{
			$bd_red = BaseDatosRed::dame_base_datos();

			$consulta = "
				SELECT
					id,
					direccion_mac,
					red,
					frecuencia_actualizacion,
					arquitectura,
                    frecuencia_envio_estado
				FROM dispositivos
				WHERE
					dispositivos.direccion_mac = '".$bd_red->_($mac)."'";
			$res = $bd_red->ejecuta_consulta($consulta);
            if (($res == false) || ($res->dame_numero_filas() == 0))
            {
                if ($res == true)
                {
                    $log = dame_log();
                    $ip_remota = $_SERVER['REMOTE_ADDR'];
                    $log->warn("(IP remota: '".$ip_remota."') No se ha encontrado el dispositivo con MAC: '".$mac."'");
                }

                throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
            }
			$fila = $res->dame_siguiente_fila();
			$nodo = Nodo::crea_nodo($fila["id"], TIPO_NODO_DISPOSITIVO, $fila);
            $conf = $nodo->dame_conf();
			return (json_encode($conf));
		}


		// Devuelve la versión de las fuentes de la red del dispositivo con esa mac
		static function dame_version_fuentes_dispositivo_mac($mac, $ips)
		{
			$bd_red = BaseDatosRed::dame_base_datos();

			$consulta = "
				SELECT
                    redes.version_fuentes AS version
				FROM dispositivos, redes
				WHERE
					(dispositivos.direccion_mac = '".$bd_red->_($mac)."')
					AND (dispositivos.red = redes.id)";
			$res = $bd_red->ejecuta_consulta($consulta);
            if (($res == false) || ($res->dame_numero_filas() == 0))
            {
                if ($res == true)
                {
                    $log = dame_log();
                    $ip_remota = $_SERVER['REMOTE_ADDR'];
                    $ips_locales = str_replace("_", ", ", $ips);
                    $log->warn("(IP remota: '".$ip_remota."', IPs locales: '".$ips_locales."') No se ha encontrado el dispositivo con MAC: '".$mac."'");
                }

                throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
            }
			$fila = $res->dame_siguiente_fila();
            $version = $fila["version"];
			return ($version);
		}


        // Devuelve la versión de las fuentes de la red de la web del dispositivo con esa mac
		static function dame_version_fuentes_web_dispositivo_mac($mac)
		{
			$bd_red = BaseDatosRed::dame_base_datos();

			$consulta = "
				SELECT
                    redes.version_fuentes_web AS version
				FROM dispositivos, redes
				WHERE
					(dispositivos.direccion_mac = '".$bd_red->_($mac)."')
					AND (dispositivos.red = redes.id)";
			$res = $bd_red->ejecuta_consulta($consulta);
            if (($res == false) || ($res->dame_numero_filas() == 0))
            {
                if ($res == true)
                {
                    $log = dame_log();
                    $ip_remota = $_SERVER['REMOTE_ADDR'];
                    $log->warn("(IP remota: '".$ip_remota."') No se ha encontrado el dispositivo con MAC: '".$mac."'");
                }

                throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
            }
			$fila = $res->dame_siguiente_fila();
            $version = $fila["version"];
			return ($version);
		}


		// Miembros de nodo
		public $idiomas;

		public $tipo;
		public $tipo_legible;

		public $id;
		public $params;

        public $texto_auxiliar_mapa;

		public $conexion;
		public $icono_conexion;


		// Funciones de nodo


        // Constructor
		function __construct($tipo, $tipo_legible, $id, $params)
		{
			$this->log = dame_log();
			$this->idiomas = new Idiomas();

			$this->tipo = $tipo;
			$this->tipo_legible = $tipo_legible;

			$this->id = $id;
			$this->params = $params;

            $this->texto_auxiliar_mapa = "";

			$this->calcula_conexion();
			$this->genera_icono_conexion();
		}


		// Calcula el estado de conexión del nodo
		function calcula_conexion()
		{
			// Si no está en los parámetros, se considera la conexión NA
			if (array_key_exists("conexion", $this->params))
			{
				$this->conexion = $this->params["conexion"];
			}
			else
			{
				$this->conexion = "NA";
			}
		}


		// Genera el html del icono de la conexión
		function genera_icono_conexion()
		{
            switch ($this->conexion)
            {
                // Generales
                case "ON":
                {
                    switch ($this->tipo)
                    {
                        case TIPO_NODO_SENSOR:
                        {
                            $texto_icono = $this->idiomas->_("Real")." (".$this->idiomas->_("conectado").")";
                            break;
                        }
                        case TIPO_NODO_ACTUADOR:
                        {
                            $texto_icono = $this->idiomas->_("Hardware")." (".$this->idiomas->_("conectado").")";
                            break;
                        }
                        default:
                        {
                            $texto_icono = $this->idiomas->_("Conectado");
                            break;
                        }
                    }
                    $this->icono_conexion = "<i class='icon-off color-verde'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($texto_icono, ENT_QUOTES)."</texto></i>";
                    break;
                }
                case "OFF":
                {
                    switch ($this->tipo)
                    {
                        case TIPO_NODO_SENSOR:
                        {
                            $texto_icono = $this->idiomas->_("Real")." (".$this->idiomas->_("desconectado").")";
                            break;
                        }
                        case TIPO_NODO_ACTUADOR:
                        {
                            $texto_icono = $this->idiomas->_("Hardware")." (".$this->idiomas->_("desconectado").")";
                            break;
                        }
                        default:
                        {
                            $texto_icono = $this->idiomas->_("Desconectado");
                            break;
                        }
                    }
                    $this->icono_conexion = "<i class='icon-off color-rojo'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($texto_icono, ENT_QUOTES)."</texto></i>";
                    break;
                }
                case "FINISHED":
                {
                    $this->icono_conexion = "<i class='icon-remove-sign color-gris-claro'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Finalizado"), ENT_QUOTES)."</texto></i>";
                    break;
                }
                // Sensores
                case TIPO_SENSOR_VIRTUAL:
                {
                    $this->icono_conexion = "<i class='icon-cloud color-azul'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Virtual"), ENT_QUOTES)."</texto></i>";
                    break;
                }
                case TIPO_SENSOR_PROCESADO:
                {
                    $this->icono_conexion = "<i class='icon-beaker color-azul'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Procesado"), ENT_QUOTES)."</texto></i>";
                    break;
                }
                case TIPO_SENSOR_EXTERNO:
                {
                    $this->icono_conexion = "<i class='icon-signin color-gris-claro'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Externo"), ENT_QUOTES)."</texto></i>";
                    break;
                }
                // Actuadores
                case TIPO_ACTUADOR_SOFTWARE:
                {
                    $this->icono_conexion = "<i class='icon-cloud color-azul'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Software"), ENT_QUOTES)."</texto></i>";
                    break;
                }

                default:
                {
                    $id_red = $_SESSION["id_red"];
                    $bd_red = BaseDatosRed::dame_base_datos();
                    $consulta = "SELECT clientes.nombre FROM clientes INNER JOIN redes ON clientes.id=redes.cliente WHERE redes.id = ".$id_red;
                    $res = $bd_red->ejecuta_consulta($consulta);
                    $fila = $res->dame_siguiente_fila();
                    $nombre_cliente = $fila["nombre"];
                    // Si la red es de un cliente BYE RADON se muestra el dispositivo como conectedo.
                    if ($nombre_cliente == 'Future Sense')
                    {
                        $this->icono_conexion = "<i class='icon-off color-verde'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Desconocido"), ENT_QUOTES)."</texto></i>";
                    }
                    else
                    {
                        $this->icono_conexion = "<i class='icon-question color-gris'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Desconocido"), ENT_QUOTES)."</texto></i>";
                    }
                    
                }
			}
		}


		// Cabecera de la tabla del nodo
		static function dame_cabecera_tabla()
		{
			return (array());
		}


		// Datos para la tabla del nodo
		function dame_datos_tabla()
		{
			return (array());
		}


		// Devuelve las opciones para mostrar en la tabla
		function dame_opciones_tabla($nodo_administrable, $permitir_adicion_nodos)
		{
            $nombre = htmlspecialchars($this->params['nombre'], ENT_QUOTES);

            $opciones = array();
            $administracion_nodos = Nodo::dame_administracion_nodos($this->tipo);
            if ($administracion_nodos == true)
            {
                if ($nodo_administrable == true)
                {
                    $editar = "<i id='anyade_modifica_nodo__".$this->tipo."__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' ".
                        "class='icon-pencil color-gris boton_mostrar_ventana_anyadir_modificar_nodo boton-tabla-datos'></i>";
                    $borrar = "<i id='elimina_nodo__".$this->tipo."__".$this->id."' nombre_nodo='".$nombre."' ".
                        "class='icon-remove color-gris boton_eliminar_nodo boton-tabla-datos'></i>";
                }
                else
                {
                    $editar = "<i class='icon-pencil color-gris-muy-claro'></i>";
                    $borrar = "<i class='icon-remove color-gris-muy-claro'></i>";
                }
                if (($this->dame_duplicacion_tabla() == true) && ($permitir_adicion_nodos == true))
                {
                    if ($nodo_administrable == true)
                    {
                        $duplicar = "<i id='anyade_modifica_nodo__".$this->tipo."__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' ".
                            "class='icon-copy color-gris boton_mostrar_ventana_anyadir_modificar_nodo boton-tabla-datos'></i>";
                    }
                    else
                    {
                        $duplicar = "<i class='icon-copy color-gris-muy-claro'></i>";
                    }
                    $opciones = array($borrar, $duplicar, $editar);
                }
                else
                {
                    $opciones = array($borrar, $editar);
                }
            }

			return ($opciones);
		}


        function dame_duplicacion_tabla()
        {
            return (false);
        }


        // Devuelve las herramientas para mostrar en los detalles de la tabla
		function dame_herramientas_detalles_tabla()
		{
			return ("");
		}


		// Devuelve los detalles de la tabla
		function dame_detalles_tabla($mostrar_numeros_sensores_actuadores = null)
		{
			return ("");
		}


		// Devuelve la información del nodo para el grafo de topología de red
		function dame_info_topologia_red($clase_sensor = NULL, $clase_actuador = NULL)
		{
			return (array());
		}


		// Devuelve el texto del tooltip para el grafo de topología de red
		function dame_tooltip_topologia_red()
		{
			$tooltip = $this->idiomas->_("Nombre").": ".$this->params["nombre"];
			$tooltip .= ", ".$this->idiomas->_("tipo").": ".$this->tipo_legible;
            if ($this->conexion != "NA")
            {
                $tooltip .= ", ".$this->idiomas->_("conexión").": ".$this->idiomas->_($this->conexion);
            }

			return ($tooltip);
		}


		// Devuelve una cadena que representa el archivo de configuración del nodo
		function dame_conf()
		{
			return ("");
		}


        //
        // Funciones para mostrar en el mapa
        //


        // Devuelve la imagen del mapa
        function dame_datos_imagen_mapa()
		{
            // Ruta de imagen base
            $icono_base = $this->dame_nombre_icono_base();
            $conexion = $this->conexion;
            $ruta_imagen_base = $_SESSION["directorio"]."/rsc/imagenes/marker-icon-".$icono_base."-".$conexion.".png";

            // Se recupera la imagen del mapa actual
            $datos_imagen_mapa_actual = dame_imagen_mapa_objeto(
                $this,
                $this->id,
                $this->params["nombre"],
                $this->tipo,
                $ruta_imagen_base);
            return ($datos_imagen_mapa_actual);
		}


        // Crea la imagen del texto auxiliar
        function crea_imagen_texto_auxiliar(&$ruta_imagen_texto_auxiliar)
		{
        }


        // Añade las rutas de las imágenes satélite
        function anyade_rutas_imagenes_satelite(&$rutas_imagenes_satelite_1, &$rutas_imagenes_satelite_2)
		{
        }


        // Devuelve la información de tooltip para el mapa
		function dame_tooltip_mapa($id_mapa)
		{
			$info = "";
			$info .= "<b>".$this->tipo_legible."</b><br/>";
			$info .= $this->idiomas->_("Nombre").": ".$this->params["nombre"]."<br/>";
            $info .= $this->dame_info_tooltip_mapa($id_mapa);

            return ($info);
		}


        //
        // Funciones auxiliares para mostrar en el mapa
        //


        // Devuelve el nombre del icono base
        function dame_nombre_icono_base()
		{
            return ($this->tipo);
        }


        // Establece el texto auxiliar del mapa
        function establece_texto_auxiliar_mapa($texto_auxiliar_mapa)
        {
            $this->texto_auxiliar_mapa = $texto_auxiliar_mapa;
        }


        // Devuelve la información específica para el mapa
		function dame_info_tooltip_mapa($id_mapa)
		{
			return ("");
		}


        //
        //  Funciones auxiliares
        //


        static function dame_descripcion_conexion($conexion)
        {
            switch ($conexion)
            {
                case "NA":
                {
                    $descripcion_conexion = "No disponible";
                    break;
                }
                case "OFF":
                {
                    $descripcion_conexion = "Desconectado";
                    break;
                }
                case "ON":
                {
                    $descripcion_conexion = "Conectado";
                    break;
                }
                case "FINISHED":
                {
                    $descripcion_conexion = "Finalizado";
                    break;
                }
                case TIPO_SENSOR_VIRTUAL:
                {
                    $descripcion_conexion = "Virtual";
                    break;
                }
                case TIPO_SENSOR_PROCESADO:
                {
                    $descripcion_conexion = "Procesado";
                    break;
                }
                case TIPO_SENSOR_EXTERNO:
                {
                    $descripcion_conexion = "Externo";
                    break;
                }
                case TIPO_ACTUADOR_SOFTWARE:
                {
                    $descripcion_conexion = "Software";
                    break;
                }
                default:
                {
                    $descripcion_conexion = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_conexion));
        }


        static function dame_administracion_nodos($tipo)
        {
            switch ($tipo)
            {
                case TIPO_NODO_SENSOR:
                case TIPO_NODO_GRUPO_SENSORES:
                {
                    $administracion_nodos = NodoSensor::dame_administracion_sensores();
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                case TIPO_NODO_GRUPO_ACTUADORES:
                {
                    $administracion_nodos = NodoActuador::dame_administracion_actuadores();
                    break;
                }
                default:
                {
                    $id_red = $_SESSION["id_red"];
                    $bd_red = BaseDatosRed::dame_base_datos();
                    $consulta = "SELECT clientes.nombre FROM clientes INNER JOIN redes ON clientes.id=redes.cliente WHERE redes.id = ".$id_red;
                    $res = $bd_red->ejecuta_consulta($consulta);
                    $fila = $res->dame_siguiente_fila();
                    $nombre_cliente = $fila["nombre"];
                    if ($nombre_cliente == 'Future Sense' AND (($_SESSION["perfil"] == PERFIL_USUARIO_SUPERADMINISTRADOR) OR ($_SESSION["perfil"] == PERFIL_USUARIO_ADMINISTRADOR)))
                    {
                        $administracion_nodos = true;
                    }
                    else
                    {
                        $administracion_nodos = $_SESSION["perfil"] == PERFIL_USUARIO_SUPERADMINISTRADOR;
                    }
                    //$administracion_nodos = ($_SESSION["perfil"] == PERFIL_USUARIO_SUPERADMINISTRADOR) OR ($_SESSION["perfil"] == PERFIL_USUARIO_ADMINISTRADOR);
                    break;
                }
            }
            return ($administracion_nodos);
        }


        function dame_administracion_nodo($ids_nodos_administrables)
        {
            return (true);
        }


        function dame_color_nodo_topologia_red()
        {
            switch ($this->conexion)
            {
                case "ON":
                {
                    $color_nodo = TOPOLOGIA_ARBOL_COLOR_NODO_VERDE;
                    break;
                }
                case "OFF":
                {
                    $color_nodo = TOPOLOGIA_ARBOL_COLOR_NODO_ROJO;
                    break;
                }
                default:
                {
                    $color_nodo = NULL;
                    break;
                }
            }
            return ($color_nodo);
        }
    }
?>
