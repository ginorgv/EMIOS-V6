<?php
	session_start();

    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');


    // Constantes

    // Índices de parámetros de caducidad de valores de las redes
    define("INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_NUMERO_MESES_VALORES_TIEMPO_REAL", 0);
    define("INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_NUMERO_MESES_VALORES_CUARTOSHORA", 1);
    define("INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_NUMERO_MESES_VALORES_HORAS", 2);
    define("INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_NUMERO_MESES_VALORES_DIAS", 3);
    define("INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_NUMERO_MESES_VALORES_MESES", 4);
    define("INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_ENVIAR_VALORES_CADUCADOS_TIEMPO_REAL", 5);
    define("INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_ENVIAR_VALORES_CADUCADOS_CUARTOSHORA", 6);
    define("INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_ENVIAR_VALORES_CADUCADOS_HORAS", 7);
    define("INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_DIRECCION_EMAIL_ENVIO_VALORES_CADUCADOS", 8);
    define("INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_NUMERO_MESES_ACCIONES_USUARIO", 9);
    define("INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_NUMERO_MESES_ACTIVACIONES", 10);

    // Índices de unidades_de medida
    define("INDICE_UNIDADES_MEDIDA_RED_MONEDA", 0);
    define("INDICE_UNIDADES_MEDIDA_RED_TEMPERATURA", 1);
    define("INDICE_UNIDADES_MEDIDA_RED_VELOCIDAD", 2);

    // Índices de países de tarifas
    define("INDICE_PAISES_TARIFAS_RED_PAIS_TARIFAS_ELECTRICAS", 0);
    define("INDICE_PAISES_TARIFAS_RED_PAIS_TARIFAS_GAS", 1);
    define("INDICE_PAISES_TARIFAS_RED_PAIS_TARIFAS_AGUA", 2);

    // Índices de direcciones e-mail de notificaciones
    define("INDICE_DIRECCION_EMAIL_ENVIO_NOTIFICACIONES_RED_VALIDACIONES_FACTURAS", 0);
    define("INDICE_DIRECCION_EMAIL_ENVIO_NOTIFICACIONES_RED_AVISOS_EXPIRACIONES_TARIFAS", 1);
    define("INDICE_DIRECCION_EMAIL_ENVIO_NOTIFICACIONES_RED_AVISOS_TIMEOUTS_ENVIO_SENSORES_ERROR_VALORES", 2);
    define("INDICE_DIRECCION_EMAIL_ENVIO_NOTIFICACIONES_RED_AVISOS_EVENTOS_ACTIVADOS", 3);
    define("INDICE_DIRECCION_EMAIL_ENVIO_NOTIFICACIONES_RED_AVISOS_ACTUADORES_ERROR_REGLAS_ACTIVADAS", 4);

    // Tipos de elementos de detalles de la tabla
    // (Nota: Primero sensores y actuadores y luego el resto ordenado por módulo y sección)
    define("TIPO_ELEMENTO_RED_DETALLES_TABLA_SENSORES", "SENSORES");
    define("TIPO_ELEMENTO_RED_DETALLES_TABLA_ACTUADORES", "ACTUADORES");
    define("TIPO_ELEMENTO_RED_DETALLES_TABLA_PLANTILLAS_INFORMES", "PLANTILLAS_INFORMES");
    define("TIPO_ELEMENTO_RED_DETALLES_TABLA_INFORMES_AUTOMATICOS", "INFORMES_AUTOMATICOS");
    define("TIPO_ELEMENTO_RED_DETALLES_TABLA_WIDGETS", "WIDGETS");
    define("TIPO_ELEMENTO_RED_DETALLES_TABLA_LOCALIZACIONES", "LOCALIZACIONES");
    define("TIPO_ELEMENTO_RED_DETALLES_TABLA_INSTALACIONES", "INSTALACIONES");
    define("TIPO_ELEMENTO_RED_DETALLES_TABLA_EVENTOS", "EVENTOS");
    define("TIPO_ELEMENTO_RED_DETALLES_TABLA_PROGRAMACIONES", "PROGRAMACIONES");
    define("TIPO_ELEMENTO_RED_DETALLES_TABLA_REGLAS", "REGLAS");
    define("TIPO_ELEMENTO_RED_DETALLES_TABLA_LINEAS_BASE", "LINEAS_BASE");
    define("TIPO_ELEMENTO_RED_DETALLES_TABLA_PROYECTOS", "PROYECTOS");


	// Nodo red
	class NodoRed extends Nodo
	{
        static function dame_cabecera_tabla()
		{
			$idiomas = new Idiomas();

			return (array(
				$idiomas->_("Cliente"),
                $idiomas->_("Nombre"),
                $idiomas->_("Sensores"),
                $idiomas->_("Actuadores"),
                $idiomas->_("Pasarelas")
			));
		}


		function dame_datos_tabla()
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $nombre_cliente = $icono_dato_erroneo;
            $nombre = $icono_dato_erroneo;
            $numero_sensores = $icono_dato_erroneo;
            $numero_actuadores = $icono_dato_erroneo;
            $texto_dispositivos = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_cliente_correcto = false;
            try
            {
                // Nombre de cliente
                if (array_key_exists("nombre_cliente", $this->params) == true)
                {
                    $nombre_cliente = $this->params["nombre_cliente"];
                }
                else
                {
                    $consulta_cliente = "
                        SELECT nombre
                        FROM clientes
                        WHERE
                            id = '".$bd_red->_($this->params["cliente"])."'";
                    $res_cliente = $bd_red->ejecuta_consulta($consulta_cliente);
                    if (($res_cliente == false) || ($res_cliente->dame_numero_filas() == 0))
                    {
                        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_cliente."'");
                    }
                    $fila_cliente = $res_cliente->dame_siguiente_fila();
                    $nombre_cliente = $fila_cliente["nombre"];
                }
                $nombre_cliente_correcto = true;

                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);

                // Número de sensores y actuadores
                if (array_key_exists("numero_sensores", $this->params) == true)
                {
                    $numero_sensores = $this->params["numero_sensores"];
                }
                else
                {
                    $consulta_sensores = "
                        SELECT
                            COUNT(*) AS numero_sensores
                        FROM sensores
                        WHERE
                            red = '".$bd_red->_($this->id)."'";
                    $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
                    if (($res_sensores == false) || ($res_sensores->dame_numero_filas() == 0))
                    {
                        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensores."'");
                    }
                    $fila_sensores = $res_sensores->dame_siguiente_fila();
                    $numero_sensores = $fila_sensores["numero_sensores"];
                }
                if (array_key_exists("numero_actuadores", $this->params) == true)
                {
                    $numero_actuadores = $this->params["numero_actuadores"];
                }
                else
                {
                    $consulta_actuadores = "
                        SELECT
                            COUNT(*) AS numero_actuadores
                        FROM actuadores
                        WHERE
                            red = '".$bd_red->_($this->id)."'";
                    $res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
                    if (($res_actuadores == false) || ($res_actuadores->dame_numero_filas() == 0))
                    {
                        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_actuadores."'");
                    }
                    $fila_actuadores = $res_actuadores->dame_siguiente_fila();
                    $numero_actuadores = $fila_actuadores["numero_actuadores"];
                }

                // Texto de los dispositivos (pasarelas) (totales: conectados y desconectados)
                if (array_key_exists("numero_dispositivos_totales", $this->params) == true)
                {
                    $numero_dispositivos_totales = $this->params["numero_dispositivos_totales"];
                    $numero_dispositivos_conectados = $this->params["numero_dispositivos_conectados"];
                    $numero_dispositivos_desconectados = $this->params["numero_dispositivos_desconectados"];
                }
                else
                {
                    $consulta_dispositivos = "
                        SELECT conexion
                        FROM dispositivos
                        WHERE
                            red = '".$bd_red->_($this->id)."'";
                    $res_dispositivos = $bd_red->ejecuta_consulta($consulta_dispositivos);
                    $numero_dispositivos_totales = 0;
                    $numero_dispositivos_conectados = 0;
                    $numero_dispositivos_desconectados = 0;
                    while ($fila_dispositivo = $res_dispositivos->dame_siguiente_fila())
                    {
                        $conexion = $fila_dispositivo["conexion"];
                        $numero_dispositivos_totales += 1;
                        switch ($conexion)
                        {
                            case "ON":
                            {
                                $numero_dispositivos_conectados += 1;
                                break;
                            }
                            case "OFF":
                            case "FINISHED":
                            case "TIMEOUT":
                            {
                                $numero_dispositivos_desconectados += 1;
                                break;
                            }
                        }
                    }
                }
                $texto_dispositivos = "";
                if ($numero_dispositivos_totales == 0)
                {
                    $texto_dispositivos .= $this->idiomas->_("No hay pasarelas");
                }
                else
                {
                    $texto_dispositivos = $numero_dispositivos_totales;
                    if (($numero_dispositivos_conectados > 0) || ($numero_dispositivos_desconectados > 0))
                    {
                        $texto_dispositivos .= " (";
                        if ($numero_dispositivos_conectados > 0)
                        {
                            if ($numero_dispositivos_conectados == 1)
                            {
                                $texto_conectados = $this->idiomas->_("conectada");
                            }
                            else
                            {
                                $texto_conectados = $this->idiomas->_("conectadas");
                            }
                            $texto_dispositivos .= $numero_dispositivos_conectados." ".$texto_conectados;
                            if ($numero_dispositivos_desconectados > 0)
                            {
                                $texto_dispositivos .= ", ";
                            }
                        }
                        if ($numero_dispositivos_desconectados > 0)
                        {
                            if ($numero_dispositivos_desconectados == 1)
                            {
                                $texto_desconectados = $this->idiomas->_("no conectada");
                            }
                            else
                            {
                                $texto_desconectados = $this->idiomas->_("no conectadas");
                            }
                            $texto_dispositivos .= $numero_dispositivos_desconectados." ".$texto_desconectados;
                        }
                        $texto_dispositivos .= ")";
                    }
                }
                $texto_dispositivos = htmlspecialchars($texto_dispositivos, ENT_QUOTES);
            }
            catch (Exception $e)
            {
                // Se añade información de la excepción en el log
                $log = dame_log();
                $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $e);

                // Se añade icono de error en el nombre de cliente
                if ($nombre_cliente_correcto == true)
                {
                    $nombre_cliente = "[".$icono_fila_con_errores."] ".$nombre_cliente;
                }
            }

            // Se devuelven los datos de la tabla
            return (array(
				$nombre_cliente,
                $nombre,
                $numero_sensores,
                $numero_actuadores,
                $texto_dispositivos
			));
		}


        function dame_detalles_tabla($mostrar_numeros_sensores_actuadores = null)
		{
        	$bd_red = BaseDatosRed::dame_base_datos();

            $info = "";

            //if ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR)
            //{
                // Identificador de la red
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Identificador").": ".$this->id."<br/>";
                $info .= "<br/>";
            //}

            // Información de sensores y actuadores de la red
            if ($mostrar_numeros_sensores_actuadores == true)
            {
                $hay_sensores_actuadores_red = false;
                $info_sensores_actuadores_red .= "<i class='icon-info-sign color-azul'></i> ";
                $info_sensores_actuadores_red .= $this->idiomas->_("Esta red tiene").":";
                $info_sensores_actuadores_red .= "<ul>";
                $info_sensores_actuadores_red .= $this->dame_info_elementos_red_detalles_tabla(TIPO_ELEMENTO_RED_DETALLES_TABLA_SENSORES, $hay_sensores_actuadores_red);
                $info_sensores_actuadores_red .= $this->dame_info_elementos_red_detalles_tabla(TIPO_ELEMENTO_RED_DETALLES_TABLA_ACTUADORES, $hay_sensores_actuadores_red);
                $info_elementos_red .= "</ul>";
                if ($hay_sensores_actuadores_red == true)
                {
                    $info .= $info_sensores_actuadores_red."<br/>";
                }
            }

            // Información de elementos de la red
            $hay_elementos_red = false;
            $info_elementos_red .= "<i class='icon-info-sign color-azul'></i> ";
            $info_elementos_red .= $this->idiomas->_("Esta red tiene").":";
            $info_elementos_red .= "<ul>";
            $info_elementos_red .= $this->dame_info_elementos_red_detalles_tabla(TIPO_ELEMENTO_RED_DETALLES_TABLA_LOCALIZACIONES, $hay_elementos_red);
            $info_elementos_red .= $this->dame_info_elementos_red_detalles_tabla(TIPO_ELEMENTO_RED_DETALLES_TABLA_INSTALACIONES, $hay_elementos_red);
            $info_elementos_red .= $this->dame_info_elementos_red_detalles_tabla(TIPO_ELEMENTO_RED_DETALLES_TABLA_EVENTOS, $hay_elementos_red);
            $info_elementos_red .= $this->dame_info_elementos_red_detalles_tabla(TIPO_ELEMENTO_RED_DETALLES_TABLA_PROGRAMACIONES, $hay_elementos_red);
            $info_elementos_red .= $this->dame_info_elementos_red_detalles_tabla(TIPO_ELEMENTO_RED_DETALLES_TABLA_REGLAS, $hay_elementos_red);
            $info_elementos_red .= $this->dame_info_elementos_red_detalles_tabla(TIPO_ELEMENTO_RED_DETALLES_TABLA_LINEAS_BASE, $hay_elementos_red);
            $info_elementos_red .= $this->dame_info_elementos_red_detalles_tabla(TIPO_ELEMENTO_RED_DETALLES_TABLA_PROYECTOS, $hay_elementos_red);
            $info_elementos_red .= "</ul>";
            if ($hay_elementos_red == true)
            {
                $info .= $info_elementos_red."<br/>";
            }

            if ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR)
            {
                // Usuarios de la red
                $consulta_usuarios = "
                    SELECT
                        usuarios.id AS id,
                        usuarios.nombre AS nombre,
                        usuarios.perfil AS perfil
                    FROM
                        usuarios,
                        redes_usuarios
                    WHERE
                        (redes_usuarios.red = ".$bd_red->_($this->id).")
                        AND (usuarios.id = redes_usuarios.usuario)
                    ORDER BY usuarios.nombre ASC";
                $res_usuarios = $bd_red->ejecuta_consulta($consulta_usuarios);
                if ($res_usuarios == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_usuarios."'");
                }
                $info .= "<i class='icon-info-sign color-azul'></i> ";
                $numero_usuarios = $res_usuarios->dame_numero_filas();
                if ($numero_usuarios > 0)
                {
                    $texto_usuarios = $numero_usuarios;
                    if ($numero_usuarios == 1)
                    {
                        $texto_usuarios .= " ".$this->idiomas->_("usuario asignado");
                    }
                    else
                    {
                        $texto_usuarios .= " ".$this->idiomas->_("usuarios asignados");
                    }
                    $info .= $this->idiomas->_("Esta red tiene")." ".$texto_usuarios.":";
                    $info .= "<ul>";
                    while ($fila_usuario = $res_usuarios->dame_siguiente_fila())
                    {
                        $info .= "<li>".htmlspecialchars($fila_usuario["nombre"]." (".$fila_usuario["id"].")", ENT_QUOTES);
                        if ($fila_usuario["perfil"] == PERFIL_USUARIO_ADMINISTRADOR)
                        {
                            $info .= " [".$this->idiomas->_("administrador")."]";
                        }
                        $info .= "</li>";
                    }
                    $info .= "</ul>";
                }
                else
                {
                    $info .= $this->idiomas->_("Esta red no tiene usuarios asignados")."<br/>";
                }
                $info .= "<br/>";

                // Información de elementos de usuarios
                $hay_elementos_usuarios = false;
                $info_elementos_usuarios .= "<i class='icon-info-sign color-azul'></i> ";
                $info_elementos_usuarios .= $this->idiomas->_("Los usuarios de esta red tienen").":";
                $info_elementos_usuarios .= "<ul>";
                $info_elementos_usuarios .= $this->dame_info_elementos_red_detalles_tabla(TIPO_ELEMENTO_RED_DETALLES_TABLA_PLANTILLAS_INFORMES, $hay_elementos_usuarios);
                $info_elementos_usuarios .= $this->dame_info_elementos_red_detalles_tabla(TIPO_ELEMENTO_RED_DETALLES_TABLA_INFORMES_AUTOMATICOS, $hay_elementos_usuarios);
                $info_elementos_usuarios .= $this->dame_info_elementos_red_detalles_tabla(TIPO_ELEMENTO_RED_DETALLES_TABLA_WIDGETS, $hay_elementos_usuarios);
                $info_elementos_usuarios .= "</ul>";
                if ($hay_elementos_usuarios == true)
                {
                    $info .= $info_elementos_usuarios."<br/>";
                }
            }

            // Se elimina el último salto de línea (si es necesario)
            if (endsWith($info, "<br/>") == true)
            {
                $info = substr($info, 0, -strlen("<br/>"));
            }

			return ($info);
		}


        function dame_info_elementos_red_detalles_tabla($tipo_elemento, &$hay_elementos_red)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            switch ($tipo_elemento)
            {
                case TIPO_ELEMENTO_RED_DETALLES_TABLA_SENSORES:
                {
                    $nombre_tabla_elementos = "sensores";
                    $nombre_elemento = $this->idiomas->_("sensor");
                    $nombre_elementos = $this->idiomas->_("sensores");
                    break;
                }
                case TIPO_ELEMENTO_RED_DETALLES_TABLA_ACTUADORES:
                {
                    $nombre_tabla_elementos = "actuadores";
                    $nombre_elemento = $this->idiomas->_("actuador");
                    $nombre_elementos = $this->idiomas->_("actuadores");
                    break;
                }
                case TIPO_ELEMENTO_RED_DETALLES_TABLA_PLANTILLAS_INFORMES:
                {
                    $nombre_tabla_elementos = "plantillas_informes";
                    $nombre_elemento = $this->idiomas->_("plantilla de informe");
                    $nombre_elementos = $this->idiomas->_("plantillas de informe");
                    break;
                }
                case TIPO_ELEMENTO_RED_DETALLES_TABLA_INFORMES_AUTOMATICOS:
                {
                    $nombre_tabla_elementos = "informes_automaticos";
                    $nombre_elemento = $this->idiomas->_("informe automático");
                    $nombre_elementos = $this->idiomas->_("informes automáticos");
                    break;
                }
                case TIPO_ELEMENTO_RED_DETALLES_TABLA_WIDGETS:
                {
                    $nombre_tabla_elementos = "widgets";
                    $nombre_elemento = $this->idiomas->_("widget");
                    $nombre_elementos = $this->idiomas->_("widgets");
                    break;
                }
                case TIPO_ELEMENTO_RED_DETALLES_TABLA_LOCALIZACIONES:
                {
                    $nombre_tabla_elementos = "localizaciones";
                    $nombre_elemento = $this->idiomas->_("localización");
                    $nombre_elementos = $this->idiomas->_("localizaciones");
                    break;
                }
                case TIPO_ELEMENTO_RED_DETALLES_TABLA_INSTALACIONES:
                {
                    $nombre_tabla_elementos = "instalaciones";
                    $nombre_elemento = $this->idiomas->_("instalación");
                    $nombre_elementos = $this->idiomas->_("instalaciones");
                    break;
                }
                case TIPO_ELEMENTO_RED_DETALLES_TABLA_EVENTOS:
                {
                    $nombre_tabla_elementos = "eventos";
                    $nombre_elemento = $this->idiomas->_("evento");
                    $nombre_elementos = $this->idiomas->_("eventos");
                    break;
                }
                case TIPO_ELEMENTO_RED_DETALLES_TABLA_PROGRAMACIONES:
                {
                    $nombre_tabla_elementos = "programaciones";
                    $nombre_elemento = $this->idiomas->_("programación");
                    $nombre_elementos = $this->idiomas->_("programaciones");
                    break;
                }
                case TIPO_ELEMENTO_RED_DETALLES_TABLA_REGLAS:
                {
                    $nombre_tabla_elementos = "reglas";
                    $nombre_elemento = $this->idiomas->_("regla");
                    $nombre_elementos = $this->idiomas->_("reglas");
                    break;
                }
                case TIPO_ELEMENTO_RED_DETALLES_TABLA_LINEAS_BASE:
                {
                    $nombre_tabla_elementos = "lineas_base";
                    $nombre_elemento = $this->idiomas->_("línea base");
                    $nombre_elementos = $this->idiomas->_("líneas base");
                    break;
                }
                case TIPO_ELEMENTO_RED_DETALLES_TABLA_PROYECTOS:
                {
                    $nombre_tabla_elementos = "proyectos";
                    $nombre_elemento = $this->idiomas->_("proyecto");
                    $nombre_elementos = $this->idiomas->_("proyectos");
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de elemento desconocido: '".$tipo_elemento."'");
                }
            }

            // Se recupera el número de elementos de la red
			$consulta_elementos = "
				SELECT
                    COUNT(*) AS numero_elementos
				FROM ".$nombre_tabla_elementos."
				WHERE
                    red = '".$bd_red->_($this->id)."'";
			$res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
            if (($res_elementos == false) || ($res_elementos->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_elementos."'");
            }
			$fila_elementos = $res_elementos->dame_siguiente_fila();
            $numero_elementos = $fila_elementos['numero_elementos'];
            if ($numero_elementos > 0)
            {
                if ($numero_elementos == 1)
                {
                    $texto_elementos = $numero_elementos." ".$nombre_elemento;
                }
                else
                {
                    $texto_elementos = $numero_elementos." ".$nombre_elementos;
                }
            }
            if ($numero_elementos > 0)
            {
                $info .= "<li>".$texto_elementos."</li>";
                $hay_elementos_red = true;
            }
            else
            {
                $info = NULL;
            }
            return ($info);
        }


        function dame_duplicacion_tabla()
        {
            return (true);
        }


		function dame_info_topologia_red($clase_sensor = NULL, $clase_actuador = NULL)
		{
            $bd_red = BaseDatosRed::dame_base_datos();

			$hijos = array();
            $numero_nodos_finales = 0;

            // Topología de red de los dispositivos
			$consulta = "
				SELECT
                    id,
                    nombre,
                    latencia
				FROM dispositivos
				WHERE
                    red = '".$_SESSION["id_red"]."'
                ORDER BY nombre ASC";
			$res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
			while ($fila = $res->dame_siguiente_fila())
			{
				$hijo = Nodo::crea_nodo($fila["id"], TIPO_NODO_DISPOSITIVO, $fila);
                $topologia_red_hijo = $hijo->dame_info_topologia_red($clase_sensor, $clase_actuador);
				array_push($hijos, $topologia_red_hijo);
                $numero_nodos_finales += $topologia_red_hijo["numero_nodos_finales"];
            }

            // Topología de red del servidor
            $topologia_red_servidor = $this->dame_info_topologia_red_servidor($clase_sensor, $clase_actuador);
            array_push($hijos, $topologia_red_servidor);
            $numero_nodos_finales += $topologia_red_servidor["numero_nodos_finales"];

            $info = array(
				"nombre" => $this->params["nombre"],
				"info_nodo" => $this->dame_tooltip_topologia_red(),
				"color_nodo" => $this->dame_color_nodo_topologia_red(),
				"children" => $hijos,
                "numero_nodos_finales" => $numero_nodos_finales
			);
            return ($info);
		}


        function dame_info_topologia_red_servidor($clase_sensor, $clase_actuador)
		{
            $hijos = array();

            // Sensores del servidor
            $info_topologia_red_sensores_virtuales = $this->dame_info_topologia_red_sensores_tipo_clase(TIPO_SENSOR_VIRTUAL, $clase_sensor);
            $info_topologia_red_sensores_procesado = $this->dame_info_topologia_red_sensores_tipo_clase(TIPO_SENSOR_PROCESADO, $clase_sensor);
            $info_topologia_red_sensores_externos = $this->dame_info_topologia_red_sensores_tipo_clase(TIPO_SENSOR_EXTERNO, $clase_sensor);
            if (count($info_topologia_red_sensores_virtuales["children"]) > 0)
            {
                array_push($hijos, $info_topologia_red_sensores_virtuales);
            }
            if (count($info_topologia_red_sensores_procesado["children"]) > 0)
            {
                array_push($hijos, $info_topologia_red_sensores_procesado);
            }
            if (count($info_topologia_red_sensores_externos["children"]) > 0)
            {
                array_push($hijos, $info_topologia_red_sensores_externos);
            }

            // Actuadores del servidor
            $info_topologia_red_actuadores_software = $this->dame_info_topologia_red_actuadores_tipo_clase(TIPO_ACTUADOR_SOFTWARE, $clase_actuador);
            if (count($info_topologia_red_actuadores_software["children"]) > 0)
            {
                array_push($hijos, $info_topologia_red_actuadores_software);
            }

            $numero_nodos_finales =
                count($info_topologia_red_sensores_virtuales["children"]) +
                count($info_topologia_red_sensores_procesado["children"]) +
                count($info_topologia_red_sensores_externos["children"]) +
                count($info_topologia_red_actuadores_software["children"]);
            if ($numero_nodos_finales == 0)
            {
                $numero_nodos_finales = count($hijos);
            }

			$info = array(
				"nombre" => "[".$this->idiomas->_("Servidor")."]",
				"info_nodo" => $this->idiomas->_("Servidor"),
				"color_nodo" => NULL,
				"children" => $hijos,
                "numero_nodos_finales" => $numero_nodos_finales
			);
            return ($info);
		}


        function dame_info_topologia_red_sensores_tipo_clase($tipo_sensor, $clase_sensor)
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            $hijos = array();
			$consulta_sensores = "
				SELECT
                    id,
                    nombre
				FROM sensores
				WHERE
                    (tipo = '".$bd_red->_($tipo_sensor)."')
                    AND (red = '".$_SESSION["id_red"]."')";
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
                $sensor = Nodo::crea_nodo($fila_sensor["id"], TIPO_NODO_SENSOR, array("nombre" => $fila_sensor["nombre"], "conexion" => "NA"));
                array_push($hijos, $sensor->dame_info_topologia_red());
            }

            switch ($tipo_sensor)
            {
                case TIPO_SENSOR_VIRTUAL:
                {
                    $descripcion = $this->idiomas->_("Sensores virtuales");
                    break;
                }
                case TIPO_SENSOR_PROCESADO:
                {
                    $descripcion = $this->idiomas->_("Sensores de procesado");
                    break;
                }
                case TIPO_SENSOR_EXTERNO:
                {
                    $descripcion = $this->idiomas->_("Sensores externos");
                    break;
                }
            }
            $nombre = "[".$descripcion."]";
            $info = array(
				"nombre" => $nombre,
				"info_nodo" => $descripcion,
				"color_nodo" => NULL,
				"children" => $hijos
			);
            return ($info);
        }


        function dame_info_topologia_red_actuadores_tipo_clase($tipo_actuador, $clase_actuador)
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            $hijos = array();
			$consulta_actuadores = "
				SELECT
                    id,
                    nombre
				FROM actuadores
                WHERE
                    (tipo = '".$bd_red->_($tipo_actuador)."')
                    AND (red = '".$_SESSION["id_red"]."')";
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
                $actuador = Nodo::crea_nodo($fila_actuador["id"], TIPO_NODO_ACTUADOR, array("nombre" => $fila_actuador["nombre"], "conexion" => "NA"));
                array_push($hijos, $actuador->dame_info_topologia_red());
            }

            switch ($tipo_actuador)
            {
                case TIPO_ACTUADOR_SOFTWARE:
                {
                    $descripcion = $this->idiomas->_("Actuadores software");
                    break;
                }
            }
            $nombre = "[".$descripcion."]";
            $info = array(
				"nombre" => $nombre,
				"info_nodo" => $descripcion,
				"color_nodo" => NULL,
				"children" => $hijos
			);
            return ($info);
        }
	}
?>
