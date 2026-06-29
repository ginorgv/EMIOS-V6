<?php
	session_start();


    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_interfaces_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Programaciones/util_programaciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');


    // Constantes

    // Indices de parámetros de tipo de actuador hardware
	define("INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_ID_AXON", 0);
    define("INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_CLASE_INTERFAZ", 1);
    define("INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_UBICACION_INTERFAZ", 2);
    define("INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_OPCIONES_INTERFAZ", 3);

    // Indices de parámetros de tipo de actuador software
	define("INDICE_PARAMETRO_TIPO_ACTUADOR_SOFTWARE_CLASE_INTERFAZ", 0);
    define("INDICE_PARAMETRO_TIPO_ACTUADOR_SOFTWARE_UBICACION_INTERFAZ", 1);
    define("INDICE_PARAMETRO_TIPO_ACTUADOR_SOFTWARE_OPCIONES_INTERFAZ", 2);

	// Indices de parámetros de clase genérica
	define("INDICE_PARAMETRO_CLASE_ACTUADOR_GENERICA_ICONO", 0);

    // Valor final intermitente
    define("VALOR_FINAL_INTERMITENTE", "intermitente");


	class NodoActuador extends Nodo
	{
		function calcula_conexion()
		{
            // Si no se ha pasado la conexión como parámetro, Se recupera el estado de conexión de la base de datos
            // (se recupera tambien el nombre del axón para evitar tener que volver a realizar la misma consulta)
            if (array_key_exists("conexion", $this->params))
            {
                $this->conexion = $this->params["conexion"];
            }
            else
            {
                switch ($this->params["tipo"])
                {
                    case TIPO_ACTUADOR_HARDWARE:
                    {
                        $parametros_tipo_actuador = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $this->params['parametros_tipo']);
                        $id_axon = $parametros_tipo_actuador[0];

                        $bd_red = BaseDatosRed::dame_base_datos();

                        $consulta = "
                            SELECT
                                conexion
                            FROM axones
                            WHERE
                                id = '".$bd_red->_($id_axon)."'";
                        $res = $bd_red->ejecuta_consulta($consulta);
                        if ($res == false)
                        {
                            throw new Exception("Error en la consulta: '".$consulta."'");
                        }
                        if ($res->dame_numero_filas() > 0)
                        {
                            $fila = $res->dame_siguiente_fila();

                            $this->conexion = $fila["conexion"];
                        }
                        else
                        {
                            $this->conexion = "OFF";
                        }
                        break;
                    }
                    default:
                    {
                        $this->nombre_axon = $this->idiomas->_("Ninguno");
                        $this->conexion = $this->params["tipo"];
                        break;
                    }
                }
            }
		}


		static function dame_cabecera_tabla()
		{
			$idiomas = new Idiomas();

            // Flag para mostrar la localización
            $mostrar_localizacion = (dame_mostrar_controles_localizaciones() == true);

            // Se devuelve la cabecera
            if ($mostrar_localizacion == true)
            {
                $cabecera_tabla = array(
                    $idiomas->_("Nombre"),
                    $idiomas->_("Localización"),
                    $idiomas->_("Tipo"),
                    $idiomas->_("Clase"),
                    $idiomas->_("Grupo"),
                    $idiomas->_("Programación"),
                    $idiomas->_("Última acción"));
            }
            else
            {
                $cabecera_tabla = array(
                    $idiomas->_("Nombre"),
                    $idiomas->_("Tipo"),
                    $idiomas->_("Clase"),
                    $idiomas->_("Grupo"),
                    $idiomas->_("Programación"),
                    $idiomas->_("Última acción"));
            }
            return ($cabecera_tabla);
		}


		function dame_datos_tabla()
		{
            // Flag para mostrar la localización
            if (array_key_exists("mostrar_localizacion", $this->params) == true)
            {
                $mostrar_localizacion = $this->params["mostrar_localizacion"];
            }
            else
            {
                $mostrar_localizacion = (dame_mostrar_controles_localizaciones() == true);
            }

            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $nombre = $icono_dato_erroneo;
            $nombre_localizacion = $icono_dato_erroneo;
            $icono_conexion = $icono_dato_erroneo;
            $nombre_clase = $icono_dato_erroneo;
            $nombre_grupo = $icono_dato_erroneo;
            $nombre_programacion = $icono_dato_erroneo;
            $ultima_accion = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Localización
                if ($mostrar_localizacion == true)
                {
                    if (array_key_exists("nombre_localizacion", $this->params) == true)
                    {
                        $nombre_localizacion = $this->params["nombre_localizacion"];
                    }
                    else
                    {
                        $nombre_localizacion = dame_nombre_localizacion($this->params["localizacion"]);
                    }
                    $nombre_localizacion = htmlspecialchars($nombre_localizacion, ENT_QUOTES);
                }

                // Icono de conexión
                $icono_conexion = $this->icono_conexion;

                // Nombre de clase
                $nombre_clase = NodoActuador::dame_descripcion_clase_actuador($this->params['clase']);

                // Grupo de actuadores
                if (array_key_exists("nombre_grupo", $this->params) == true)
                {
                    $nombre_grupo = $this->params["nombre_grupo"];
                }
                else
                {
                    $nombre_grupo = dame_nombre_grupo_actuadores($this->params["grupo"]);
                }
                $nombre_grupo = htmlspecialchars($nombre_grupo, ENT_QUOTES);

                // Programación
                if ($this->params["programacion"] != ID_NINGUNO)
                {
                    $nombre_programacion = dame_nombre_programacion($this->params["programacion"]);
                }
                else
                {
                    $nombre_programacion = $this->idiomas->_("Ninguna");
                }
                $nombre_programacion = htmlspecialchars($nombre_programacion, ENT_QUOTES);

                // Iconos de última ejecución de acción
                $iconos_ultima_ejecucion_accion = "";
                $estado_ejecucion_ultima_accion = $this->params["estado_ejecucion_ultima_accion"];
                switch ($estado_ejecucion_ultima_accion)
                {
                    case ESTADO_EJECUCION_ACCION_NO_CONECTADO:
                    {
                        $iconos_ultima_ejecucion_accion .= "<i class='icon-remove-sign color-gris-claro'>".
                            "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("no conectado"), ENT_QUOTES)."</texto></i>";
                        break;
                    }
                    case ESTADO_EJECUCION_ACCION_EN_EJECUCION:
                    {
                        $iconos_ultima_ejecucion_accion .= "<i class='icon-spinner color-gris'>".
                            "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("en ejecución"), ENT_QUOTES)."</texto></i>";
                        break;
                    }
                    case ESTADO_EJECUCION_ACCION_ERROR:
                    {
                        $iconos_ultima_ejecucion_accion .= "<i class='icon-warning-sign color-rojo'>".
                            "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("error"), ENT_QUOTES)."</texto></i>";
                        break;
                    }
                }
                $error_ejecucion_accion = ($this->params["ultimo_error_ejecucion_accion_json"] != "");
                if ($error_ejecucion_accion == true)
                {
                    if ($iconos_ultima_ejecucion_accion != "")
                    {
                        $iconos_ultima_ejecucion_accion .= " ";
                    }
                    $iconos_ultima_ejecucion_accion .= "<i class='icon-flag color-gris-claro'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("error en ejecución de acción"), ENT_QUOTES)."</texto></i>";
                }

                // Última acción
                $contenido_ultima_accion = $this->params["contenido_ultima_accion"];
                $hora_ultima_accion = $this->params["hora_ultima_accion"];
                if ($contenido_ultima_accion === NULL)
                {
                    $ultima_accion = $this->idiomas->_("Sin última acción");
                }
                else
                {
                    $ultima_accion = NodoActuador::dame_imagen_estado_actual_clase($this->params["clase"], $contenido_ultima_accion, $hora_ultima_accion);
                    $zona_horaria = dame_zona_horaria_local();
                    $cadena_hora_ultima_accion_local_utc = convierte_formato_fecha($this->params["hora_ultima_accion"], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                    $cadena_hora_ultima_accion_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_ultima_accion_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                    $ultima_accion .= " <cadena_fecha class='cadena-fecha'>(".$cadena_hora_ultima_accion_local_local.")</cadena_fecha>";
                    if ($iconos_ultima_ejecucion_accion != "")
                    {
                        $ultima_accion .= " <iconos-dato class='iconos-dato'>[".$iconos_ultima_ejecucion_accion."]</iconos-dato>";
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
            $datos_tabla = array();
            array_push($datos_tabla, $nombre);
            if ($mostrar_localizacion == true)
            {
                array_push($datos_tabla, $nombre_localizacion);
            }
            array_push($datos_tabla, $icono_conexion);
            array_push($datos_tabla, $nombre_clase);
            array_push($datos_tabla, $nombre_grupo);
            array_push($datos_tabla, $nombre_programacion);
            array_push($datos_tabla, $ultima_accion);
            return ($datos_tabla);
		}


        function dame_herramientas_detalles_tabla()
		{
            // Se recupera la fila del actuador
			$fila_actuador = dame_fila_actuador($this->id);

            // Herramientas de detalles de actuador
            $administracion_actuadores = NodoActuador::dame_administracion_actuadores();
            $administracion_comentarios_actuadores = NodoActuador::dame_administracion_comentarios_actuadores();
            $herramientas = "";

            // Recargar la fila del actuador
            $herramientas .= "
                <span class='boton-herramientas-detalle-tabla-datos'>
                    <button id='boton_refrescar__".$this->tipo."__".$this->id."' class='btn-mini btn btn-success boton_refrescar_tabla_nodo'>
                        <i class='icon-refresh color-blanco'></i>
                    </button>
                </span>";

            // Adición de comentario al actuador
            if ($administracion_comentarios_actuadores == true)
            {
                $herramientas .= "
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button objeto='".$fila_actuador["nombre"]."' origen_comentario='".ORIGEN_COMENTARIOS_DETALLES_TABLA_ACTUADORES."'
                            class='btn-mini btn btn-success boton_mostrar_ventana_anyadir_modificar_comentario'>".
                            $this->idiomas->_("Añadir comentario")."
                        </button>
                    </span>";
            }

            // Acciones del actuador
            $envio_acciones_actuadores = NodoActuador::dame_envio_acciones_actuadores();
            if ($envio_acciones_actuadores == true)
            {
                $herramientas .= "
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button id_actuador='".$this->id."' origen_envio_accion='".ORIGEN_ENVIO_ACCION_DETALLES_TABLA_ACTUADORES."'
                            class='btn-mini btn btn-success boton_actuadores_mostrar_ventana_envio_accion_actuador'>".
                            $this->idiomas->_("Enviar acción")."
                        </button>
                    </span>";
            }

            // Borrado de acciones enviadas del actuador
            if ($administracion_actuadores == true)
            {
                $herramientas .= "
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button id='boton_borrar_acciones_enviadas__".$this->id."__".$fila_actuador['clase']."' class='btn-mini btn btn-success boton_actuadores_mostrar_ventana_borrado_acciones_enviadas_actuador'>".
                            $this->idiomas->_("Borrar acciones enviadas")."
                        </button>
                    </span>";
            }

			return ($herramientas);
		}


		function dame_detalles_tabla($mostrar_numeros_sensores_actuadores = null)
		{
            $bd_datos = BaseDatosDatos::dame_base_datos();

            $info = "";
            $administracion_actuadores = NodoActuador::dame_administracion_actuadores();

			// Se recupera la fila del actuador
			$fila_actuador = dame_fila_actuador($this->id);

            // Información para administradores
            if ($administracion_actuadores == true)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Identificador").": ".$this->id."<br/>";
                $info .= "<br/>";
            }

            // Descripción
            if ($fila_actuador['descripcion'] != "")
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Descripción").": ".htmlspecialchars($fila_actuador['descripcion'], ENT_QUOTES)."<br/>";
                $info .= "<br/>";
            }

            // Localización
            $mostrar_controles_localizaciones = dame_mostrar_controles_localizaciones();
            if ($mostrar_controles_localizaciones == true)
            {
                $id_localizacion = $fila_actuador["localizacion"];
                if ($id_localizacion != ID_NINGUNO)
                {
                    // Visible en localizaciones hijas
                    $visible_localizaciones_hijas = $fila_actuador['visible_localizaciones_hijas'];
                    $descripcion_visible_localizaciones_hijas = dame_descripcion_valores_si_no($visible_localizaciones_hijas);
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Visible en localizaciones hijas").": ".$descripcion_visible_localizaciones_hijas."<br/>";

                    // Se muestra la instalación y el equipo de la instalación del actuador (si está asignado a alguno)
                    $fila_equipo_instalacion = dame_fila_equipo_instalacion_localizacion_nodo($id_localizacion, TIPO_NODO_ACTUADOR, $this->id);
                    if ($fila_equipo_instalacion !== NULL)
                    {
                        $nombre_instalacion = dame_nombre_instalacion($fila_equipo_instalacion["instalacion"]);
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Instalación").": ".htmlspecialchars($nombre_instalacion, ENT_QUOTES)."<br/>";
                        $info .= "<ul>";
                        $info .= "<li>".$this->idiomas->_("Equipo").": ".htmlspecialchars($fila_equipo_instalacion["nombre"], ENT_QUOTES)."</li>";
                        $info .= "</ul>";
                    }

                    // Salto de línea
                    $info .= "<br/>";
                }
            }

            // Fecha de primera acción enviada
            if ($fila_actuador["hora_ultima_accion"] !== NULL)
            {
                $consulta_hora_primera_accion = "
                    SELECT hora
                    FROM acciones_actuadores
                    WHERE
                        (actuador = '".$bd_datos->_($fila_actuador["nombre"])."')
                        AND (red = '".$bd_datos->_($fila_actuador["red"])."')
                    ORDER BY hora ASC
                    LIMIT 1";
                $res_hora_primera_accion = $bd_datos->ejecuta_consulta($consulta_hora_primera_accion);
                if ($res_hora_primera_accion == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_hora_primera_accion."'");
                }
                if ($res_hora_primera_accion->dame_numero_filas() > 0)
                {
                    $fila_hora_primera_accion = $res_hora_primera_accion->dame_siguiente_fila();
                    $hora_primera_accion = $fila_hora_primera_accion["hora"];

                    $zona_horaria = dame_zona_horaria_local();
                    $cadena_hora_primera_accion_base_datos_utc = convierte_formato_fecha($hora_primera_accion, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                    $cadena_hora_primera_accion_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_primera_accion_base_datos_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                    $info .= "<i class='icon-info-sign color-azul'></i> ";
                    $info .= $this->idiomas->_("Fecha de primera acción").": ".$cadena_hora_primera_accion_local_local."<br/>";
                    $info .= "<br/>";
                }
            }

            // Comentarios anterior y siguiente de actuador
            $filas_comentarios = Comentario::dame_filas_comentarios_anterior_posterior_objeto(
                ORIGEN_COMENTARIOS_DETALLES_TABLA_ACTUADORES,
                $fila_actuador["nombre"]);
            $fila_comentario_anterior = $filas_comentarios["anterior"];
            $fila_comentario_posterior = $filas_comentarios["posterior"];
            if (($fila_comentario_anterior !== NULL) || ($fila_comentario_posterior !== NULL))
            {
                // Comentario anterior
                if ($fila_comentario_anterior !== NULL)
                {
                    $cadenas_comentario_anterior = Comentario::dame_cadenas_hora_tipo_descripcion_fila_comentario($fila_comentario_anterior, false);
                    $cadena_hora_comentario_anterior_local_local = $cadenas_comentario_anterior["cadena_hora_comentario_local_local"];
                    $descripcion_tipo_comentario_anterior = $cadenas_comentario_anterior["descripcion_tipo_comentario"];
                    $descripcion_comentario_anterior = $cadenas_comentario_anterior["descripcion_comentario"];
                    $comentario_anterior = $descripcion_tipo_comentario_anterior." (".$cadena_hora_comentario_anterior_local_local.") ".
                        "[".$descripcion_comentario_anterior."]";

                    $info .= "<i class='icon-info-sign color-azul'></i> ";
                    $info .= $this->idiomas->_("Comentario anterior").": ".$comentario_anterior."<br/>";
                }

                // Comentario posterior
                if ($fila_comentario_posterior !== NULL)
                {
                    $cadenas_comentario_posterior = Comentario::dame_cadenas_hora_tipo_descripcion_fila_comentario($fila_comentario_posterior, false);
                    $cadena_hora_comentario_posterior_local_local = $cadenas_comentario_posterior["cadena_hora_comentario_local_local"];
                    $descripcion_tipo_comentario_posterior = $cadenas_comentario_posterior["descripcion_tipo_comentario"];
                    $descripcion_comentario_posterior = $cadenas_comentario_posterior["descripcion_comentario"];
                    $comentario_posterior = $descripcion_tipo_comentario_posterior." (".$cadena_hora_comentario_posterior_local_local.") ".
                        "[".$descripcion_comentario_posterior."]";

                    $info .= "<i class='icon-info-sign color-azul'></i> ";
                    $info .= $this->idiomas->_("Comentario siguiente").": ".$comentario_posterior."<br/>";
                }

                // Salto de línea
                $info .= "<br/>";
            }

            // Parámetros específicos de clase de actuador
            $clase_actuador = $fila_actuador['clase'];
            switch ($clase_actuador)
            {
                case CLASE_ACTUADOR_GENERICA:
                {
                    if ($administracion_actuadores == true)
                    {
                        $cadena_parametros_clase_generica = $fila_actuador["parametros_clase"];
                        $parametros_clase_generica = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase_generica);
                        $icono = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_ACTUADOR_GENERICA_ICONO];

                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Clase genérica").":"."<br/>";
                        $info .= "<ul>";
                        $info .= "<li>".$this->idiomas->_("Icono de mapa").": ".dame_descripcion_icono_mapa($icono)."</li>";
                        $info .= "</ul>";
                        $info .= "<br/>";
                    }
                    break;
                }
            }

            // Parámetros del tipo de actuador
            $tipo_actuador = $fila_actuador["tipo"];
            $parametros_tipo_actuador = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_actuador['parametros_tipo']);
            switch ($tipo_actuador)
            {
                case TIPO_ACTUADOR_HARDWARE:
                {
                    $id_axon = $parametros_tipo_actuador[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_ID_AXON];
                    $clase_interfaz = $parametros_tipo_actuador[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_CLASE_INTERFAZ];
                    $ubicacion_interfaz = $parametros_tipo_actuador[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_UBICACION_INTERFAZ];
                    $opciones_interfaz = $parametros_tipo_actuador[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_OPCIONES_INTERFAZ];
                    break;
                }
                case TIPO_ACTUADOR_SOFTWARE:
                {
                    $clase_interfaz = $parametros_tipo_actuador[INDICE_PARAMETRO_TIPO_ACTUADOR_SOFTWARE_CLASE_INTERFAZ];
                    $ubicacion_interfaz = $parametros_tipo_actuador[INDICE_PARAMETRO_TIPO_ACTUADOR_SOFTWARE_UBICACION_INTERFAZ];
                    $opciones_interfaz = $parametros_tipo_actuador[INDICE_PARAMETRO_TIPO_ACTUADOR_SOFTWARE_OPCIONES_INTERFAZ];
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de actuador desconocido: '".$tipo_actuador."'");
                }
            }
            $opciones_interfaz = str_replace(SEPARADOR_PARAMETROS_VALORES, " ".SEPARADOR_PARAMETROS_VALORES." ", $opciones_interfaz);

            // Axón sólo para administradores
            if ($administracion_actuadores == true)
            {
                if ($tipo_actuador == TIPO_ACTUADOR_HARDWARE)
                {
                    $fila_axon = dame_fila_axon($id_axon);
                    $nombre_axon = $fila_axon["nombre"];
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Axón").": ".$nombre_axon."<br/>";
                }
            }

            // Información del interfaz
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Clase de interfaz").": ".NodoActuador::dame_descripcion_clase_interfaz_actuador($clase_interfaz)."<br/>";
            $info .= "<ul>";
            if ($ubicacion_interfaz != "")
            {
                switch ($tipo_actuador)
                {
                    case TIPO_ACTUADOR_HARDWARE:
                    {
                        $descripcion_parametros_ubicacion_interfaz = dame_descripcion_parametros_ubicacion_interfaz_actuador_hardware(
                            $clase_interfaz,
                            $ubicacion_interfaz,
                            TIPO_DESCRIPCION_HTML);
                        break;
                    }
                    case TIPO_ACTUADOR_SOFTWARE:
                    {
                        $descripcion_parametros_ubicacion_interfaz = dame_descripcion_parametros_ubicacion_interfaz_actuador_software(
                            $clase_interfaz,
                            $ubicacion_interfaz,
                            TIPO_DESCRIPCION_HTML);
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de actuador desconocido: '".$tipo_actuador."'");
                    }
                }
                $info .= "<li>".$this->idiomas->_("Ubicación de interfaz").": ".$descripcion_parametros_ubicacion_interfaz."</li>";
            }
            if ($opciones_interfaz != "")
            {
                switch ($tipo_actuador)
                {
                    case TIPO_ACTUADOR_HARDWARE:
                    {
                        $descripcion_parametros_opciones_interfaz = dame_descripcion_parametros_opciones_interfaz_actuador_hardware(
                            $clase_interfaz,
                            $opciones_interfaz,
                            TIPO_DESCRIPCION_HTML);
                        break;
                    }
                    case TIPO_ACTUADOR_SOFTWARE:
                    {
                        $descripcion_parametros_opciones_interfaz = dame_descripcion_parametros_opciones_interfaz_actuador_software(
                            $clase_interfaz,
                            $opciones_interfaz,
                            TIPO_DESCRIPCION_HTML);
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de actuador desconocido: '".$tipo_actuador."'");
                    }
                }
                $info .= "<li>".$this->idiomas->_("Opciones de interfaz").": ".$descripcion_parametros_opciones_interfaz."</li>";
            }
            $info .= "</ul>";
            $info .= "<br/>";

            // Último error en ejecución de acción
            if ($fila_actuador['ultimo_error_ejecucion_accion_json'] <> "")
            {
                switch ($fila_actuador['tipo'])
                {
                    case TIPO_ACTUADOR_SOFTWARE:
                    {
                        $info .= $this->dame_info_ultimo_error_ejecucion_accion_tipo_actuador_software_detalles_tabla($fila_actuador);
                        $info .= "<br/>";
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de actuador incorrecto: '".$fila_actuador['tipo']."'");
                    }
                }
            }

            // Calibración solo para administradores
            if ($administracion_actuadores == true)
            {
                // Calibración
                if ($fila_actuador['calibracion'] != "")
                {
                    $calibracion = formatea_calibracion($fila_actuador['calibracion']);
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Calibración").": ".$calibracion."<br/>";
                }
            }

            // Se elimina el último salto de línea (si es necesario)
            if (endsWith($info, "<br/>") == true)
            {
                $info = substr($info, 0, -strlen("<br/>"));
            }

			return ($info);
		}


        function dame_duplicacion_tabla()
        {
            return (true);
        }


        function dame_info_topologia_red($clase_sensor = NULL, $clase_actuador = NULL)
		{
			return (array(
				"nombre" => $this->params["nombre"],
				"info_nodo" => $this->dame_tooltip_topologia_red(),
				"color_nodo" => $this->dame_color_nodo_topologia_red()
			));
		}


        function dame_info_tooltip_mapa($id_mapa)
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            // Info
            $info = "";

            // Clase
            $nombre_clase = NodoActuador::dame_descripcion_clase_actuador($this->params['clase']);
			$info .= $this->idiomas->_("Clase").": ".$nombre_clase."<br/>";

            // Localización
            if ($_SESSION["id_localizacion"] != ID_DESACTIVADO)
            {
                // Localización
                if ($this->params["localizacion"] != ID_NINGUNO)
                {
                    $consulta_localizacion = "
                        SELECT nombre
                        FROM localizaciones
                        WHERE
                            id = '".$bd_red->_($this->params["localizacion"])."'";
                    $res_localizacion = $bd_red->ejecuta_consulta($consulta_localizacion);
                    if (($res_localizacion == false) || ($res_localizacion->dame_numero_filas() == 0))
                    {
                        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_localizacion."'");
                    }
                    $fila_localizacion = $res_localizacion->dame_siguiente_fila();
                    $info .= $this->idiomas->_("Localización").": ".$fila_localizacion["nombre"];
                }
                else
                {
                    $info .= $this->idiomas->_("Sin localización");
                }
                $info .= "<br/>";
            }

            // Grupo de actuadores
            if ($this->params["grupo"] != ID_NINGUNO)
            {
                $consulta_grupo = "
                    SELECT nombre
                    FROM grupos_actuadores
                    WHERE
                        id = '".$bd_red->_($this->params["grupo"])."'";
                $res_grupo = $bd_red->ejecuta_consulta($consulta_grupo);
                if (($res_grupo == false) || ($res_grupo->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_grupo."'");
                }
                $fila_grupo = $res_grupo->dame_siguiente_fila();
                $info .= $this->idiomas->_("Grupo").": ".$fila_grupo["nombre"];
            }
            else
            {
                $info .= $this->idiomas->_("Sin grupo");
            }
            $info .= "<br/>";

            // Última acción del actuador
            if ($this->params["contenido_ultima_accion"] === NULL)
            {
                $info .= "<i class='icon-info-sign color-azul'></i>: ".
                    $this->idiomas->_("Sin última acción");
            }
            else
            {
                $zona_horaria = dame_zona_horaria_local();
                $cadena_hora_ultima_accion_utc = convierte_formato_fecha($this->params["hora_ultima_accion"], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_hora_ultima_accion = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_ultima_accion_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                $imagen_ultima_accion = NodoActuador::dame_imagen_estado_actual_clase(
                    $this->params["clase"],
                    $this->params["contenido_ultima_accion"],
                    $this->params["hora_ultima_accion"]);
                switch ($this->params["estado_ejecucion_ultima_accion"])
                {
                    case ESTADO_EJECUCION_ACCION_NO_CONECTADO:
                    {
                        $info .=  "<i class='icon-remove-sign color-gris-claro'></i>: ";
                        break;
                    }
                    case ESTADO_EJECUCION_ACCION_EN_EJECUCION:
                    {
                        $info .= "<i class='icon-question color-gris'></i>: ";
                        break;
                    }
                    case ESTADO_EJECUCION_ACCION_ERROR:
                    {
                        $info .= "<i class='icon-warning-sign color-rojo'></i> ";
                        break;
                    }
                    case ESTADO_EJECUCION_ACCION_OK:
                    {
                        $info .= "<i class='icon-info-sign color-azul'></i>: ";
                        break;
                    }
                }
                $info .= $this->idiomas->_("Última acción").": ".$imagen_ultima_accion." (".$cadena_hora_ultima_accion.")";
            }
            $info .= "<br/>";

            // Conexión
            if ($this->conexion != "NA")
            {
                $descripcion_conexion = Nodo::dame_descripcion_conexion($this->conexion);
                $info .= $this->icono_conexion.": ".$descripcion_conexion."<br/>";
            }

            // Botón de envío de acción
            $envio_acciones_actuadores = NodoActuador::dame_envio_acciones_actuadores();
            if ($envio_acciones_actuadores == true)
            {
                $info .= "
                    <div class='contenedor-botones-tooltip-mapa'>
                        <button id='boton_mostrar_ventana_envio_accion__".$this->id."__".$id_mapa."'
                            class='btn-mini btn btn-success boton-tooltip-mapa boton_actuadores_mostrar_ventana_envio_accion_actuador_mapa'>".
                            $this->idiomas->_("Enviar acción")."
                        </button>
                    </div>";
            }

			return ($info);
		}


		function dame_conf()
		{
            $parametros_tipo_actuador = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $this->params['parametros_tipo']);
            $clase_interfaz = $parametros_tipo_actuador[1];
            $ubicacion_interfaz = $parametros_tipo_actuador[2];
            $opciones_interfaz = $parametros_tipo_actuador[3];

            $caracteristicas_clase_actuador = NodoActuador::dame_caracteristicas_clase_actuador($this->params['clase']);
            $numero_valores = $caracteristicas_clase_actuador["numero_valores"];
            $tipo_acciones = $caracteristicas_clase_actuador["tipo_acciones"];

            $conf = array(
				"ID" => $this->params['id'],
                "GRUPO" => $this->params['grupo'],
                "CLASE" => $this->params['clase'],
				"NUM_VALORES" => $numero_valores,
				"TIPO_ACCIONES" => $tipo_acciones,
                "CLASE_INTERFAZ" => $clase_interfaz,
                "UBICACION_INTERFAZ" => $ubicacion_interfaz,
                "OPCIONES_INTERFAZ" => $opciones_interfaz,
                "CALIBRACION" => $this->params['calibracion']
			);

			return ($conf);
		}


        //
        // Funciones de imágenes de las acciones
        //


		// Devuelve la representación gráfica de una acción
		static function dame_imagen_accion_clase($clase, $accion)
		{
            $caracteristicas_clase_actuador = NodoActuador::dame_caracteristicas_clase_actuador($clase);
            $tipo_acciones = $caracteristicas_clase_actuador["tipo_acciones"];
            switch ($clase)
			{
				case CLASE_ACTUADOR_MENSAJE:
                {
                    return (NodoActuador::dame_imagen_accion_clase_mensaje());
                }
                case CLASE_ACTUADOR_INTERRUPTOR:
                {
                    return (NodoActuador::dame_imagen_accion_clases_interruptor($tipo_acciones, $accion));
                }
                case CLASE_ACTUADOR_TELEPOSTE:
                {
					return (NodoActuador::dame_imagen_accion_clase_teleposte($accion));
                }
                case CLASE_ACTUADOR_LUZ_GRADUAL_4:
                {
					return (NodoActuador::dame_imagen_accion_clases_luminaria($tipo_acciones, $accion));
                }
                case CLASE_ACTUADOR_GENERICA:
                {
					return ($accion);
                }
				default:
                {
					throw new Exception("Clase de actuador desconocida");
                }
			}
		}


        // Devuelve la representación gráfica de una acción de la clase de actuador mensaje
        static function dame_imagen_accion_clase_mensaje()
		{
            $idiomas = new idiomas();

            $nombre_icono = "icon-envelope";
            $texto_icono = "{".$idiomas->_("mensaje")."}";
            $imagen = "<i class='".$nombre_icono." color-azul'>".
                "<texto class='elemento-oculto'>".htmlspecialchars($texto_icono, ENT_QUOTES)."</texto></i>";
			return ($imagen);
		}


        // Devuelve la representación gráfica de una acción de las clases de actuador con acciones representadas por interruptores
        static function dame_imagen_accion_clases_interruptor($tipo_acciones, $accion)
		{
            $idiomas = new idiomas();

            $imagen = "";
			$valores_accion = explode(SEPARADOR_PARAMETROS_VALORES, $accion);
            foreach ($valores_accion as $valor_accion)
            {
                $valor_final = NodoActuador::dame_valor_final_tipo_acciones_valor_accion($tipo_acciones, $valor_accion);
                if ($valor_final == "")
                {
                    $nombre_icono = "interruptor-unknown";
                    $texto_icono = "{".$idiomas->_("desconocido")."}";
                }
                else if ($valor_final == 0)
                {
                    $nombre_icono = "interruptor-off";
                    $texto_icono = "{".$idiomas->_("apagado")."}";
                }
                else
                {
                    $nombre_icono = "interruptor-on";
                    $texto_icono = "{".$idiomas->_("encendido")."}";
                }
                $imagen .= "<img src='./rsc/imagenes/".$nombre_icono.".png' class='icono-escalable'>".
                    "<texto class='elemento-oculto'>".htmlspecialchars($texto_icono, ENT_QUOTES)."</texto></img>";
			}

			return ($imagen);
		}


        // Devuelve la representación gráfica de una acción de la clase de actuador teleposte
        static function dame_imagen_accion_clase_teleposte($accion)
		{
            // Se utilizan las mismas imágenes de la luminaria, pero sólo sobre los dos primeros valores
            // (son la intensidad de la luz, los siguientes son para encender y apagar los paneles, y estarán encendidos o apagados según haya o no intensidad de luz)
			$valores_accion = explode(SEPARADOR_PARAMETROS_VALORES, $accion);
            $accion_imagen = $valores_accion[0].SEPARADOR_PARAMETROS_VALORES.$valores_accion[1];
            $imagen = NodoActuador::dame_imagen_accion_clases_luminaria(TIPO_ACCIONES_VALORES_FIJOS_GRADUALES, $accion_imagen);

			return ($imagen);
		}


        // Devuelve la representación gráfica de una acción de las clases de actuador con acciones representadas por luminarias
        static function dame_imagen_accion_clases_luminaria($tipo_acciones, $accion)
		{
            $idiomas = new idiomas();

            $imagen = "";
			$valores_accion = explode(SEPARADOR_PARAMETROS_VALORES, $accion);
            foreach ($valores_accion as $valor_accion)
            {
                $valor_final = NodoActuador::dame_valor_final_tipo_acciones_valor_accion($tipo_acciones, $valor_accion);
                if ($valor_final == "")
                {
                    $nombre_icono = "luminaria-unknown";
                    $texto_icono = "{".$idiomas->_("desconocido")."}";
                }
                else if ($valor_final == VALOR_FINAL_INTERMITENTE)
                {
                    $nombre_icono = "luminaria-blink";
                    $texto_icono = "{".$idiomas->_("intermitente")."}";
                }
                else if ($valor_final == 0)
                {
                    $nombre_icono = "luminaria-0";
                    $texto_icono = "{0}";
                }
                else if ($valor_final <= 25)
                {
                    $nombre_icono = "luminaria-25";
                    $texto_icono = "{25}";
                }
                else if ($valor_final <= 50)
                {
                    $nombre_icono = "luminaria-50";
                    $texto_icono = "{50}";
                }
                else if ($valor_final <= 75)
                {
                    $nombre_icono = "luminaria-75";
                    $texto_icono = "{75}";
                }
                else
                {
                    $nombre_icono = "luminaria-100";
                    $texto_icono = "{100}";
                }
                $imagen .= "<img src='./rsc/imagenes/".$nombre_icono.".png' class='icono-escalable'>".
                    "<texto class='elemento-oculto'>".htmlspecialchars($texto_icono, ENT_QUOTES)."</texto></img>";
			}

			return ($imagen);
		}


        // Devuelve la representación gráfica del estado de un actuador
		static function dame_imagen_estado_actual_clase($clase, $accion, $hora_accion)
		{
            $caracteristicas_clase_actuador = NodoActuador::dame_caracteristicas_clase_actuador($clase);
            $tipo_acciones = $caracteristicas_clase_actuador["tipo_acciones"];
            switch ($clase)
			{
                case CLASE_ACTUADOR_MENSAJE:
                {
					return (NodoActuador::dame_imagen_estado_actual_clase_mensaje());
                }
                case CLASE_ACTUADOR_INTERRUPTOR:
                {
					return (NodoActuador::dame_imagen_estado_actual_clases_interruptor($tipo_acciones, $accion, $hora_accion));
                }
                case CLASE_ACTUADOR_TELEPOSTE:
                {
					return (NodoActuador::dame_imagen_estado_actual_clase_teleposte($accion, $hora_accion));
                }
                case CLASE_ACTUADOR_LUZ_GRADUAL_4:
                {
					return (NodoActuador::dame_imagen_estado_actual_clases_luminaria($tipo_acciones, $accion, $hora_accion));
                }
                case CLASE_ACTUADOR_GENERICA:
                {
					return ($accion);
                }
				default:
                {
					throw new Exception("Clase de actuador desconocida");
                }
			}
		}


        // Devuelve la representación gráfica del estado de un actuador de clase mensaje
        static function dame_imagen_estado_actual_clase_mensaje()
		{
            $idiomas = new idiomas();

            $nombre_icono = "icon-envelope";
            $texto_icono = "{".$idiomas->_("mensaje")."}";
            $imagen = "<i class='".$nombre_icono." color-azul'>".
                "<texto class='elemento-oculto'>".htmlspecialchars($texto_icono, ENT_QUOTES)."</texto></i>";
			return ($imagen);
		}


        // Devuelve la representación gráfica del estado de un actuador de las clases de actuador que representan interruptores
        static function dame_imagen_estado_actual_clases_interruptor($tipo_acciones, $accion, $hora_accion)
		{
            $idiomas = new idiomas();

            if ($accion === NULL)
            {
                return ($idiomas->_("Ninguno"));
            }

            $imagen = "";
			$valores_accion = explode(SEPARADOR_PARAMETROS_VALORES, $accion);
            foreach ($valores_accion as $valor_accion)
            {
                $valor_actual = NodoActuador::dame_valor_actual_tipo_acciones_valor_accion($tipo_acciones, $valor_accion, $hora_accion);
                if ($valor_actual == "")
                {
                    $nombre_icono = "interruptor-unknown";
                    $texto_icono = "{".$idiomas->_("desconocido")."}";
                }
                else if ($valor_actual == 0)
                {
                    $nombre_icono = "interruptor-off";
                    $texto_icono = "{".$idiomas->_("apagado")."}";
                }
                else
                {
                    $nombre_icono = "interruptor-on";
                    $texto_icono = "{".$idiomas->_("encendido")."}";
                }
                $imagen .= "<img src='./rsc/imagenes/".$nombre_icono.".png' class='icono-escalable'>".
                    "<texto class='elemento-oculto'>".htmlspecialchars($texto_icono, ENT_QUOTES)."</texto></img>";
			}

			return ($imagen);
		}


        // Devuelve la representación gráfica del estado de un actuador de la clase de actuador teleposte
        static function dame_imagen_estado_actual_clase_teleposte($accion, $hora_accion)
		{
            if ($accion === NULL)
            {
                $idiomas = new idiomas();
                return ($idiomas->_("Ninguno"));
            }

            // Se utilizan las mismas imágenes de la luminaria, pero sólo sobre los dos primeros valores
            // (son la intensidad de la luz, los siguientes son para encender y apagar los paneles, y estarán encendidos o apagados según haya o no intensidad de luz)
			$valores_accion = explode(SEPARADOR_PARAMETROS_VALORES, $accion);
            $accion_teleposte = $valores_accion[0].SEPARADOR_PARAMETROS_VALORES.$valores_accion[1];
            $imagen = NodoActuador::dame_imagen_estado_actual_clases_luminaria(TIPO_ACCIONES_VALORES_FIJOS_GRADUALES, $accion_teleposte, $hora_accion);

			return ($imagen);
		}


        // Devuelve la representación gráfica del estado de un actuador de las clases de actuador con acciones representadas por luminarias
        static function dame_imagen_estado_actual_clases_luminaria($tipo_acciones, $accion, $hora_accion)
		{
            $idiomas = new idiomas();

            if ($accion === NULL)
            {
                return ($idiomas->_("Ninguno"));
            }

            $imagen = "";
			$valores_accion = explode(SEPARADOR_PARAMETROS_VALORES, $accion);
            foreach ($valores_accion as $valor_accion)
            {
                $valor_actual = NodoActuador::dame_valor_actual_tipo_acciones_valor_accion($tipo_acciones, $valor_accion, $hora_accion);
                if ($valor_actual == "")
                {
                    $nombre_icono = "luminaria-unknown";
                    $texto_icono = "{".$idiomas->_("desconocido")."}";
                }
                else if ($valor_actual == VALOR_FINAL_INTERMITENTE)
                {
                    $nombre_icono = "luminaria-blink";
                    $texto_icono = "{".$idiomas->_("intermitente")."}";
                }
                else if ($valor_actual == 0)
                {
                    $nombre_icono = "luminaria-0";
                    $texto_icono = "{0}";
                }
                else if ($valor_actual <= 25)
                {
                    $nombre_icono = "luminaria-25";
                    $texto_icono = "{25}";
                }
                else if ($valor_actual <= 50)
                {
                    $nombre_icono = "luminaria-50";
                    $texto_icono = "{50}";
                }
                else if ($valor_actual <= 75)
                {
                    $nombre_icono = "luminaria-75";
                    $texto_icono = "{75}";
                }
                else
                {
                    $nombre_icono = "luminaria-100";
                    $texto_icono = "{100}";
                }
                $imagen .= "<img src='./rsc/imagenes/".$nombre_icono.".png' class='icono-escalable'>".
                    "<texto class='elemento-oculto'>".htmlspecialchars($texto_icono, ENT_QUOTES)."</texto></img>";
			}

			return ($imagen);
		}


        // Devuelve el valor final de una accion del tipo de acciones especificado
        static function dame_valor_final_tipo_acciones_valor_accion($tipo_acciones, $valor_accion)
		{
            switch ($tipo_acciones)
            {
                case TIPO_ACCIONES_VALORES_UNICOS:
                {
                    $valor_final = $valor_accion;
                    break;
                }
                case TIPO_ACCIONES_VALORES_INICIAL_FINAL:
                {
                    $parametros_valor_accion = explode(SEPARADOR_PARAMETROS_SIMPLES, $valor_accion);
                    $valor_final = $parametros_valor_accion[2];
                    break;
                }
                case TIPO_ACCIONES_VALORES_FIJOS_GRADUALES:
                {
                    $valor_accion = str_replace(" ", "", $valor_accion);
                    $parametros_valor_accion = explode(SEPARADOR_PARAMETROS_SIMPLES, $valor_accion);
                    if (count($parametros_valor_accion) > 0)
                    {
                        $tipo_valores = $parametros_valor_accion[0];
                        switch ($tipo_valores)
                        {
                            case TIPO_VALORES_FIJOS:
                            {
                                $numero_repeticiones = $parametros_valor_accion[1];
                                break;
                            }
                            case TIPO_VALORES_GRADUALES:
                            {
                                $numero_repeticiones = $parametros_valor_accion[2];
                                break;
                            }
                        }
                        if ($numero_repeticiones == -1)
                        {
                            $valor_final = VALOR_FINAL_INTERMITENTE;
                        }
                        else
                        {
                            $valor_final = $parametros_valor_accion[count($parametros_valor_accion) - 1];
                        }
                    }
                    else
                    {
                        $valor_final = "";
                    }
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de acciones desconocido: '".$tipo_acciones."'");
                }
            }

            return ($valor_final);
		}


        // Devuelve el valor actual de una accion de la clase de actuador determinada
        static function dame_valor_actual_tipo_acciones_valor_accion($tipo_acciones, $valor_accion, $hora_accion)
		{
            $fecha_actual_utc = dame_fecha_hora_actual_utc();
            $fecha_accion_utc = convierte_cadena_a_fecha($hora_accion, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $segundos_transcurridos = $fecha_actual_utc->getTimestamp() - $fecha_accion_utc->getTimestamp();
            if ($segundos_transcurridos < 0)
            {
                $segundos_transcurridos = 0;
            }

			switch ($tipo_acciones)
            {
                case TIPO_ACCIONES_VALORES_UNICOS:
                {
                    $valor_actual = $valor_accion;
                    break;
                }
                case TIPO_ACCIONES_VALORES_INICIAL_FINAL:
                {
                    $parametros_valor_accion = explode(SEPARADOR_PARAMETROS_SIMPLES, $valor_accion);
                    if ($segundos_transcurridos < $parametros_valor_accion[1])
                    {
                        $valor_actual = $parametros_valor_accion[0];
                    }
                    else
                    {
                        $valor_actual = $parametros_valor_accion[2];
                    }
                    break;
                }
                case TIPO_ACCIONES_VALORES_FIJOS_GRADUALES:
                {
                    $valor_accion = str_replace(" ", "", $valor_accion);
                    $parametros_valor_accion = explode(SEPARADOR_PARAMETROS_SIMPLES, $valor_accion);
                    if (count($parametros_valor_accion) > 0)
                    {
                        $tipo_valores = $parametros_valor_accion[0];
                        switch ($tipo_valores)
                        {
                            case TIPO_VALORES_FIJOS:
                            {
                                $numero_repeticiones = $parametros_valor_accion[1];
                                break;
                            }
                            case TIPO_VALORES_GRADUALES:
                            {
                                $numero_repeticiones = $parametros_valor_accion[2];
                                break;
                            }
                        }
                        if ($numero_repeticiones == -1)
                        {
                            $valor_actual = VALOR_FINAL_INTERMITENTE;
                        }
                        else
                        {
                            $valor_actual = $parametros_valor_accion[count($parametros_valor_accion) - 1];
                        }
                    }
                    else
                    {
                        $valor_actual = "";
                    }
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de acciones desconocido: '".$tipo_acciones."'");
                }
            }

            return ($valor_actual);
		}


        //
        // Funciones de iconos del mapa
        //


        function dame_nombre_icono_base()
        {
            switch ($this->params["clase"])
            {
                case CLASE_ACTUADOR_GENERICA:
                {
                    $icono = NodoActuador::dame_parametro_clase_generica(
                        $this->params["parametros_clase"],
                        INDICE_PARAMETRO_CLASE_ACTUADOR_GENERICA_ICONO);
                    break;
                }
                default:
                {
                    $caracteristicas_clase_actuador = NodoActuador::dame_caracteristicas_clase_actuador($this->params["clase"]);
                    $icono = $caracteristicas_clase_actuador["icono"];
                }
            }
            return ($icono);
        }


		function anyade_rutas_imagenes_satelite(&$rutas_fila_imagenes_satelite_1, &$rutas_fila_imagenes_satelite_2)
		{
            $accion = $this->params["contenido_ultima_accion"];
            $hora_accion = $this->params["hora_ultima_accion"];

			switch ($this->params['clase'])
			{
                case CLASE_ACTUADOR_MENSAJE:
                {
					break;
                }
                case CLASE_ACTUADOR_INTERRUPTOR:
                {
					$this->anyade_rutas_imagenes_satelite_clases_interruptor($rutas_fila_imagenes_satelite_1, $accion, $hora_accion);
                    break;
                }
                case CLASE_ACTUADOR_TELEPOSTE:
                {
                    $this->anyade_rutas_imagenes_satelite_clase_teleposte($rutas_fila_imagenes_satelite_1, $accion, $hora_accion);
                    break;
                }
                case CLASE_ACTUADOR_LUZ_GRADUAL_4:
                {
					$this->anyade_rutas_imagenes_satelite_clases_luminaria($rutas_fila_imagenes_satelite_1, $accion, $hora_accion);
                    break;
                }
                case CLASE_ACTUADOR_GENERICA:
                {
                    $this->anyade_rutas_imagenes_satelite_clase_generica($rutas_fila_imagenes_satelite_1, $accion, $hora_accion);
                    break;
                }
			}

            // Última acción
            if ($this->params["contenido_ultima_accion"] !== NULL)
            {
                $estado_ejecucion_ultima_accion = $this->params["estado_ejecucion_ultima_accion"];
                switch ($estado_ejecucion_ultima_accion)
                {
                    case ESTADO_EJECUCION_ACCION_NO_CONECTADO:
                    {
                        $ruta_imagen_satelite = $_SESSION["directorio"]."/rsc/imagenes/no_conectado.png";
                        array_push($rutas_fila_imagenes_satelite_2, $ruta_imagen_satelite);
                        break;
                    }
                    case ESTADO_EJECUCION_ACCION_EN_EJECUCION:
                    {
                        $ruta_imagen_satelite = $_SESSION["directorio"]."/rsc/imagenes/en_ejecucion.png";
                        array_push($rutas_fila_imagenes_satelite_2, $ruta_imagen_satelite);
                        break;
                    }
                    case ESTADO_EJECUCION_ACCION_ERROR:
                    {
                        $ruta_imagen_satelite = $_SESSION["directorio"]."/rsc/imagenes/exclamacion.png";
                        array_push($rutas_fila_imagenes_satelite_2, $ruta_imagen_satelite);
                        break;
                    }
                }
            }
		}


        // Crea las imágenes satelite para los iconos del mapa con acciones representadas por interruptor (rojo - apagado, verde - encendido)
        function anyade_rutas_imagenes_satelite_clases_interruptor(&$rutas_imagenes_satelite, $accion, $hora_accion)
        {
            if ($accion === NULL)
            {
                $ruta_imagen_satelite .= $_SESSION["directorio"]."/rsc/imagenes/desconocido.png";
                array_push($rutas_imagenes_satelite, $ruta_imagen_satelite);
            }
            else
            {
                $caracteristicas_clase_actuador = NodoActuador::dame_caracteristicas_clase_actuador($this->params["clase"]);
                $tipo_acciones = $caracteristicas_clase_actuador["tipo_acciones"];
                $valores_accion = explode(SEPARADOR_PARAMETROS_VALORES, $accion);
                foreach ($valores_accion as $valor_accion)
                {
                    $valor_actual = NodoActuador::dame_valor_actual_tipo_acciones_valor_accion($tipo_acciones, $valor_accion, $hora_accion);
                    if ($valor_actual == "")
                    {
                        $estado = "unknown";
                    }
                    else if ($valor_actual == 0)
                    {
                        $estado = "off";
                    }
                    else
                    {
                        $estado = "on";
                    }
                    $ruta_imagen_satelite = $_SESSION["directorio"]."/rsc/imagenes/interruptor-".$estado.".png";
                    array_push($rutas_imagenes_satelite, $ruta_imagen_satelite);
                }
            }
        }


        // Crea las imágenes satelite para los iconos del mapa con acciones representadas por luminarias (para un teleposte)
        function anyade_rutas_imagenes_satelite_clase_teleposte(&$rutas_imagenes_satelite, $accion, $hora_accion)
        {
            if ($accion === NULL)
            {
                $ruta_imagen_satelite .= $_SESSION["directorio"]."/rsc/imagenes/desconocido.png";
                array_push($rutas_imagenes_satelite, $ruta_imagen_satelite);
            }
            else
            {
                // Se utilizan las mismas imágenes de la luminaria, pero sólo sobre los dos primeros valores
                // (son la intensidad de la luz, los siguientes son para encender y apagar los paneles, y estarán encendidos o apagados según haya o no intensidad de luz)
                $valores_accion = explode(SEPARADOR_PARAMETROS_VALORES, $accion);
                $accion_teleposte = $valores_accion[0].SEPARADOR_PARAMETROS_VALORES.$valores_accion[1];
                $this->anyade_rutas_imagenes_satelite_clases_luminaria($rutas_imagenes_satelite, $accion_teleposte, $hora_accion);
            }
        }


        // Crea las imágenes satelite para los iconos del mapa con acciones representadas por luminarias
        function anyade_rutas_imagenes_satelite_clases_luminaria(&$rutas_imagenes_satelite, $accion, $hora_accion)
        {
            if ($accion === NULL)
            {
                $ruta_imagen_satelite .= $_SESSION["directorio"]."/rsc/imagenes/desconocido.png";
                array_push($rutas_imagenes_satelite, $ruta_imagen_satelite);
            }
            else
            {
                $caracteristicas_clase_actuador = NodoActuador::dame_caracteristicas_clase_actuador($this->params["clase"]);
                $tipo_acciones = $caracteristicas_clase_actuador["tipo_acciones"];
                $valores_accion = explode(SEPARADOR_PARAMETROS_VALORES, $accion);
                foreach ($valores_accion as $valor_accion)
                {
                    $valor_actual = NodoActuador::dame_valor_actual_tipo_acciones_valor_accion($tipo_acciones, $valor_accion, $hora_accion);
                    if ($valor_actual == "")
                    {
                        $estado = "unknown";
                    }
                    else if ($valor_actual == VALOR_FINAL_INTERMITENTE)
                    {
                        $estado = "blink";
                    }
                    else if ($valor_actual == 0)
                    {
                        $estado = 0;
                    }
                    else if ($valor_actual <= 25)
                    {
                        $estado = 25;
                    }
                    else if ($valor_actual <= 50)
                    {
                        $estado = 50;
                    }
                    else if ($valor_actual <= 75)
                    {
                        $estado = 75;
                    }
                    else
                    {
                        $estado = 100;
                    }
                    $ruta_imagen_satelite = $_SESSION["directorio"]."/rsc/imagenes/luminaria-".$estado.".png";
                    array_push($rutas_imagenes_satelite, $ruta_imagen_satelite);
                }
            }
        }


        // Crea las imágenes satelite para los iconos del mapa de la clase genérica
        function anyade_rutas_imagenes_satelite_clase_generica(&$rutas_imagenes_satelite, $accion, $hora_accion)
        {
            if ($accion === NULL)
            {
                $ruta_imagen_satelite .= $_SESSION["directorio"]."/rsc/imagenes/desconocido.png";
                array_push($rutas_imagenes_satelite, $ruta_imagen_satelite);
            }
            else
            {
                // Ruta de la imagen con el texto de la acción
                $directorio_absoluto_imagen_mapa_actual = dame_directorio_ficheros_temporales_usuario($_SESSION["id_usuario"]);
                $ruta_imagen_texto_accion = $directorio_absoluto_imagen_mapa_actual."/"."texto_accion_tmp.png";

                crea_imagen_texto("(".$accion.")", $ruta_imagen_texto_accion);
                array_push($rutas_imagenes_satelite, $ruta_imagen_texto_accion);
            }
        }


        //
        // Funciones auxiliares
        //


        function dame_administracion_nodo($ids_nodos_administrables)
        {
            // Si se trabaja con localizaciones y hay localización seleccionada, el actuador se puede administrar sólo si el usuario ve el actuador
            // por sus localizaciones asignadas y descendientes (no por las localizaciones ascendientes)
            switch ($_SESSION["id_localizacion"])
            {
                case ID_DESACTIVADO:
                case ID_NINGUNO:
                {
                    $administracion_nodo = true;
                    break;
                }
                default:
                {
                    $administracion_nodo = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                        (in_array($this->id, $ids_nodos_administrables) == true);
                    break;
                }
            }
            return ($administracion_nodo);
        }


        static function dame_administracion_actuadores()
        {
            $administracion_actuadores = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_actuadores"]["administracion_actuadores"] == VALOR_SI);
            return ($administracion_actuadores);
        }


        static function dame_administracion_comentarios_actuadores()
        {
            $administracion_comentarios_actuadores = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_actuadores"]["administracion_actuadores"] == VALOR_SI) ||
                ($_SESSION["parametros_modulo_actuadores"]["administracion_comentarios_actuadores"] == VALOR_SI);
            return ($administracion_comentarios_actuadores);
        }


        static function dame_envio_acciones_actuadores()
        {
            $envio_acciones_actuadores = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_actuadores"]["acciones_actuadores"] == VALOR_SI) ||
                ($_SESSION["parametros_modulo_actuadores"]["administracion_actuadores"] == VALOR_SI);
            return ($envio_acciones_actuadores);
        }


        static function dame_parametros_clase_generica($cadena_parametros_clase_generica)
        {
            $parametros_clase_generica = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase_generica);
            return ($parametros_clase_generica);
        }


        static function dame_parametro_clase_generica($cadena_parametros_clase_generica, $indice_parametro)
        {
            $parametros_clase_generica = NodoActuador::dame_parametros_clase_generica($cadena_parametros_clase_generica);
            if (($cadena_parametros_clase_generica == "") || (count($parametros_clase_generica) < ($indice_parametro + 1)))
            {
                switch ($indice_parametro)
                {
                    case INDICE_PARAMETRO_CLASE_ACTUADOR_GENERICA_ICONO:
                    {
                        $parametro = "Actuador";
                        break;
                    }
                    default:
                    {
                        throw new Exception("Indice de parámetro incorrecto: '".$indice_parametro."'");
                    }
                }
            }
            else
            {
                $parametro = $parametros_clase_generica[$indice_parametro];
            }
            return ($parametro);
        }


        //
        // Funciones de clases y tipos de actuador
        //


        static function dame_clases_actuador()
        {
            $clases_actuador = array();
            array_push($clases_actuador, CLASE_ACTUADOR_MENSAJE);
            array_push($clases_actuador, CLASE_ACTUADOR_INTERRUPTOR);
            array_push($clases_actuador, CLASE_ACTUADOR_TELEPOSTE);
            array_push($clases_actuador, CLASE_ACTUADOR_LUZ_GRADUAL_4);
            array_push($clases_actuador, CLASE_ACTUADOR_GENERICA);
            return ($clases_actuador);
        }


        static function dame_descripcion_clase_actuador($clase_actuador)
        {
            switch ($clase_actuador)
            {
                case CLASE_NINGUNA:
                {
                    $descripcion_clase_sensor = "Ninguna";
                    break;
                }
                case CLASE_TODAS:
                {
                    $descripcion_clase_sensor = "Todas";
                    break;
                }
                case CLASE_ACTUADOR_MENSAJE:
                {
                    $descripcion_clase_actuador = "Mensaje";
                    break;
                }
                case CLASE_ACTUADOR_INTERRUPTOR:
                {
                    $descripcion_clase_actuador = "Interruptor";
                    break;
                }
                case CLASE_ACTUADOR_TELEPOSTE:
                {
                    $descripcion_clase_actuador = "Teleposte";
                    break;
                }
                case CLASE_ACTUADOR_LUZ_GRADUAL_4:
                {
                    $descripcion_clase_actuador = "4 luces con regulación";
                    break;
                }
                case CLASE_ACTUADOR_GENERICA:
                {
                    $descripcion_clase_actuador = "Genérica";
                    break;
                }
                default:
                {
                    $descripcion_clase_actuador = "Desconocida";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_clase_actuador));
        }


        static function dame_caracteristicas_clase_actuador($clase_actuador)
        {
            $caracteristicas_clase_actuador = array();
            switch ($clase_actuador)
            {
                case CLASE_ACTUADOR_MENSAJE:
                {
                    $caracteristicas_clase_actuador["numero_valores"] = 1;
                    $caracteristicas_clase_actuador["icono"] = "sobre";
                    $caracteristicas_clase_actuador["tipo_acciones"] = TIPO_ACCIONES_VALORES_UNICOS;
                    $caracteristicas_clase_actuador["estado_persistente"] = false;
                    $caracteristicas_clase_actuador["acciones_predefinidas"] = false;
                    break;
                }
                case CLASE_ACTUADOR_INTERRUPTOR:
                {
                    $caracteristicas_clase_actuador["numero_valores"] = 1;
                    $caracteristicas_clase_actuador["icono"] = "interruptor";
                    $caracteristicas_clase_actuador["tipo_acciones"] = TIPO_ACCIONES_VALORES_INICIAL_FINAL;
                    $caracteristicas_clase_actuador["estado_persistente"] = true;
                    $caracteristicas_clase_actuador["acciones_predefinidas"] = true;
                    break;
                }
                case CLASE_ACTUADOR_TELEPOSTE:
                {
                    $caracteristicas_clase_actuador["numero_valores"] = 6;
                    $caracteristicas_clase_actuador["icono"] = "teleposte";
                    $caracteristicas_clase_actuador["tipo_acciones"] = TIPO_ACCIONES_VALORES_FIJOS_GRADUALES;
                    $caracteristicas_clase_actuador["estado_persistente"] = true;
                    $caracteristicas_clase_actuador["acciones_predefinidas"] = true;
                    break;
                }
                case CLASE_ACTUADOR_LUZ_GRADUAL_4:
                {
                    $caracteristicas_clase_actuador["numero_valores"] = 4;
                    $caracteristicas_clase_actuador["icono"] = "bombilla";
                    $caracteristicas_clase_actuador["tipo_acciones"] = TIPO_ACCIONES_VALORES_INICIAL_FINAL;
                    $caracteristicas_clase_actuador["estado_persistente"] = true;
                    $caracteristicas_clase_actuador["acciones_predefinidas"] = true;
                    break;
                }
                case CLASE_ACTUADOR_GENERICA:
                {
                    $caracteristicas_clase_actuador["numero_valores"] = 1;
                    $caracteristicas_clase_actuador["icono"] = "Actuador";
                    $caracteristicas_clase_actuador["tipo_acciones"] = TIPO_ACCIONES_VALORES_UNICOS;
                    $caracteristicas_clase_actuador["estado_persistente"] = true;
                    $caracteristicas_clase_actuador["acciones_predefinidas"] = false;
                    break;
                }
                // Ninguna
                case CLASE_NINGUNA:
                {
                    $caracteristicas_clase_actuador["numero_valores"] = 0;
                    $caracteristicas_clase_actuador["icono"] = "";
                    $caracteristicas_clase_actuador["tipo_acciones"] = TIPO_ACCIONES_VALORES_UNICOS;
                    $caracteristicas_clase_actuador["estado_persistente"] = false;
                    $caracteristicas_clase_actuador["acciones_predefinidas"] = false;
                    break;
                }
                default:
                {
                    throw new Exception("Clase de actuador desconocida: '".$clase_actuador."'");
                }
            }
            return ($caracteristicas_clase_actuador);
        }


        static function dame_tipos_actuador()
        {
            $tipos_actuador = array();
            array_push($tipos_actuador, TIPO_ACTUADOR_HARDWARE);
            array_push($tipos_actuador, TIPO_ACTUADOR_SOFTWARE);
            return ($tipos_actuador);
        }


        static function dame_descripcion_tipo_actuador($tipo_actuador)
        {
            switch ($tipo_actuador)
            {
                case TIPO_ACTUADOR_HARDWARE:
                {
                    $descripcion_tipo_actuador = "Hardware";
                    break;
                }
                case TIPO_ACTUADOR_SOFTWARE:
                {
                    $descripcion_tipo_actuador = "Software";
                    break;
                }
                default:
                {
                    $descripcion_tipo_actuador = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_tipo_actuador));
        }


        static function dame_clases_interfaz_actuador()
        {
            $clases_interfaz_actuador = array();
            array_push($clases_interfaz_actuador, CLASE_INTERFAZ_ACTUADOR_EMAIL);
            array_push($clases_interfaz_actuador, CLASE_INTERFAZ_ACTUADOR_MODBUS_IP);
            array_push($clases_interfaz_actuador, CLASE_INTERFAZ_ACTUADOR_MODBUS_SERIE);
            array_push($clases_interfaz_actuador, CLASE_INTERFAZ_ACTUADOR_PWM);
            array_push($clases_interfaz_actuador, CLASE_INTERFAZ_ACTUADOR_SIMULADO);
            return ($clases_interfaz_actuador);
        }


        static function dame_descripcion_clase_interfaz_actuador($clase_interfaz_actuador)
        {
            switch ($clase_interfaz_actuador)
            {
                case CLASE_INTERFAZ_ACTUADOR_EMAIL:
                {
                    $descripcion_clase_interfaz_actuador = "E-mail";
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP:
                {
                    $descripcion_clase_interfaz_actuador = "ModBus IP";
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_MODBUS_SERIE:
                {
                    $descripcion_clase_interfaz_actuador = "ModBus serie";
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_PWM:
                {
                    $descripcion_clase_interfaz_actuador = "PWM";
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_SIMULADO:
                {
                    $descripcion_clase_interfaz_actuador = "Simulado";
                    break;
                }
                default:
                {
                    $descripcion_clase_interfaz_actuador = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_clase_interfaz_actuador));
        }


        static function dame_caracteristicas_clase_interfaz_actuador($clase_interfaz_actuador)
        {
            $caracteristicas_clase_interfaz_actuador = array();
            switch ($clase_interfaz_actuador)
            {
                case CLASE_INTERFAZ_ACTUADOR_EMAIL:
                {
                    $caracteristicas_clase_interfaz_actuador["tipo_actuador"] = TIPO_ACTUADOR_SOFTWARE;
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP:
                {
                    $caracteristicas_clase_interfaz_actuador["tipo_actuador"] = TIPO_ACTUADOR_TODOS;
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_MODBUS_SERIE:
                {
                    $caracteristicas_clase_interfaz_actuador["tipo_actuador"] = TIPO_ACTUADOR_HARDWARE;
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_PWM:
                {
                    $caracteristicas_clase_interfaz_actuador["tipo_actuador"] = TIPO_ACTUADOR_HARDWARE;
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_SIMULADO:
                {
                    $caracteristicas_clase_interfaz_actuador["tipo_actuador"] = TIPO_ACTUADOR_TODOS;
                    break;
                }
                // Ninguna
                case CLASE_NINGUNA:
                {
                    $caracteristicas_clase_interfaz_actuador["tipo_actuador"] = TIPO_ACTUADOR_SOFTWARE;
                    break;
                }
                default:
                {
                    break;
                }
            }
            return ($caracteristicas_clase_interfaz_actuador);
        }


        //
        // Funciones para mostrar los detalles de la tabla (tipo de actuador)
        //


        function dame_info_ultimo_error_ejecucion_accion_tipo_actuador_software_detalles_tabla($fila)
        {
            $info = "";

            // Clase de interfaz
            $parametros_actuador_hardware = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila['parametros_tipo']);
            $clase_interfaz = $parametros_actuador_hardware[INDICE_PARAMETRO_TIPO_ACTUADOR_SOFTWARE_CLASE_INTERFAZ];

            // Último error de ejecución de acción
            $ultimo_error_ejecucion_accion = json_decode($fila['ultimo_error_ejecucion_accion_json'], true);

            // Título de error
            $info .= "<i class='icon-flag color-gris-claro'></i> ";
            $info .= $this->idiomas->_("Error en la ejecución de la acción").":";
            $info .= "<ul>";

            // Zona horaria
            $zona_horaria = dame_zona_horaria_local();

            // Fecha (en la que ha ocurrido el error)
            $cadena_fecha_hora_base_datos_utc = $ultimo_error_ejecucion_accion["fecha"];
            $cadena_fecha_hora_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_local_local = convierte_formato_fecha($cadena_fecha_hora_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
            $info .= "<li>".$this->idiomas->_("Fecha").": ".$cadena_fecha_hora_local_local."</li>";

            // Error y descripción del error según la clase de interfaz
            switch ($clase_interfaz)
            {
                case CLASE_INTERFAZ_ACTUADOR_EMAIL:
                {
                    $descripcion_error = dame_descripcion_error_ejecucion_accion_email($ultimo_error_ejecucion_accion["error"]);
                    $cadena_parametros_error = $ultimo_error_ejecucion_accion["cadena_parametros_error"];
                    break;
                }
                case CLASE_INTERFAZ_ACTUADOR_MODBUS_IP:
                {
                    $descripcion_error = dame_descripcion_error_ejecucion_accion_modbus_ip($ultimo_error_ejecucion_accion["error"]);
                    $cadena_parametros_error = $ultimo_error_ejecucion_accion["cadena_parametros_error"];
                    break;
                }
                default:
                {
                    throw new Exception("Clase de interfaz incorrecta: '".$clase_interfaz."'");
                }
            }
            $info .= "<li>".$descripcion_error;
            if ($cadena_parametros_error != "")
            {
                $info .= " (".$cadena_parametros_error.")";
            }
            $info .= "</li>";
            $info .= "</ul>";

            return ($info);
        }
	}
?>
