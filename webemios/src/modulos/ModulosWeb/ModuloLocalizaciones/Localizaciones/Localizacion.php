<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');


	// Clase que representa una localización
	class Localizacion
	{
        // Funciones estáticas de localización


        // Devuelve la cabecera para la tabla de localizaciones
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
				$idiomas->_("Nombre"),
                $idiomas->_("Hijas")." (".$idiomas->_("nivel").")",
                $idiomas->_("Sensores"),
                $idiomas->_("Actuadores")
			));
        }


        // Devuelve la consulta para la tabla de localizaciones
        static function dame_consulta_localizaciones($filtro)
        {
            // Nota: Se leen todos los campos porque se pueden actualizar también los detalles en la tabla
            $consulta = "
                SELECT *
                FROM localizaciones
                WHERE
                    (red = '".$_SESSION["id_red"]."')";
            if ($filtro != "")
            {
                $campos = array("nombre");
                $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
            }
            $consulta .= "
                ORDER BY nombre ASC";
            return ($consulta);
        }


        // Devuelve la tabla de localizaciones
        static function dame_tabla_localizaciones($filtro, $id_localizacion_detalles)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $administracion_localizaciones = Localizacion::dame_administracion_localizaciones();
            if ($administracion_localizaciones == true)
            {
                $boton_anyadir_localizacion = "<i id='anyade_modifica_localizacion' class='icon-plus color-blanco boton_localizaciones_mostrar_ventana_anyadir_modificar_localizacion boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_localizacion);
            }
            $boton_actualizar_tabla_localizaciones = "<i id='actualiza_localizaciones' class='icon-refresh color-blanco boton_localizaciones_actualizar_tabla_localizaciones boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_localizaciones);
            $boton_ayuda_tabla_localizaciones = "<i id='ayuda_localizaciones' class='icon-question-sign color-blanco boton_localizaciones_ayuda_tabla_localizaciones boton-tabla-datos'></i>";
            array_push($opciones, $boton_ayuda_tabla_localizaciones);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_LOCALIZACIONES,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_LOCALIZACIONES),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-localizaciones-localizaciones",
                $idiomas->_("Localizaciones"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = Localizacion::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Filas de las localizaciones
            $consulta_localizaciones = Localizacion::dame_consulta_localizaciones($filtro);
            $res_localizaciones = $bd_red->ejecuta_consulta($consulta_localizaciones);
            if ($res_localizaciones == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_localizaciones."'");
            }
            $filas_localizaciones = array();
            while ($fila_localizacion = $res_localizaciones->dame_siguiente_fila())
            {
                array_push($filas_localizaciones, $fila_localizacion);
            }

            // Filas de las hijas de localizaciones
            $consulta_hijas_localizaciones = "
                SELECT *
                FROM hijas_localizaciones";
            $res_hijas_localizaciones = $bd_red->ejecuta_consulta($consulta_hijas_localizaciones);
            if ($res_hijas_localizaciones == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_hijas_localizaciones."'");
            }
            $filas_hijas_localizaciones = array();
            while ($fila_hija_localizacion = $res_hijas_localizaciones->dame_siguiente_fila())
            {
                array_push($filas_hijas_localizaciones, $fila_hija_localizacion);
            }

            // Filas de los sensores y de los actuadores
            $consulta_sensores = "
                SELECT *
                FROM sensores
                WHERE
                    (red = '".$_SESSION["id_red"]."')";
            $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
            if ($res_sensores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_sensores."'");
            }
            $filas_sensores = array();
            while ($fila_sensor = $res_sensores->dame_siguiente_fila())
            {
                array_push($filas_sensores, $fila_sensor);
            }
            $consulta_actuadores = "
                SELECT *
                FROM actuadores
                WHERE
                    (red = '".$_SESSION["id_red"]."')";
            $res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
            if ($res_actuadores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_actuadores."'");
            }
            $filas_actuadores = array();
            while ($fila_actuador = $res_actuadores->dame_siguiente_fila())
            {
                array_push($filas_actuadores, $fila_actuador);
            }

            // Identificadores de localizaciones del usuario actual
            $mostrar_todas_localizaciones = dame_mostrar_todas_localizaciones();
            if ($mostrar_todas_localizaciones == false)
            {
                $ids_localizaciones_usuario = dame_ids_localizaciones_usuario_actual(true);
            }

            // Se añade cada una de las localizaciones a la tabla y el pie de tabla
            $numero_localizaciones = 0;
            foreach ($filas_localizaciones as $fila_localizacion)
            {
                $localizacion = new Localizacion($fila_localizacion);

                $anyadir_localizacion = true;
                if ($mostrar_todas_localizaciones == false)
                {
                    if (in_array($localizacion->id, $ids_localizaciones_usuario) == false)
                    {
                        $anyadir_localizacion = false;
                    }
                }

                if ($anyadir_localizacion == true)
                {
                    $params_fila = array(
                        "opciones" => $localizacion->dame_opciones_tabla()
                    );
                    if (($id_localizacion_detalles !== NULL) && ($localizacion->id == $id_localizacion_detalles))
                    {
                        $params_fila["herramientas_detalles_tabla"] = $localizacion->dame_herramientas_detalles_tabla();
                        $params_fila["detalles_tabla"] = $localizacion->dame_detalles_tabla();
                    }
                    $tabla->anyade_fila(
                        "datosLocalizacion__".$fila_localizacion['id'],
                        $localizacion->dame_datos_tabla(
                            $filas_hijas_localizaciones,
                            $filas_sensores,
                            $filas_actuadores),
                        $params_fila
                    );
                    $numero_localizaciones += 1;
                }
            }
            $tabla->anyade_pie($idiomas->_("Localizaciones").": ".$numero_localizaciones);

            return ($tabla->dame_tabla());
        }


        // Miembros de localización


		public $idiomas;

		public $id;
        public $params;


		function __construct($params = array())
		{
			$this->idiomas = new Idiomas();

			$this->id = $params["id"];
            $this->params = $params;
		}


        function dame_datos_tabla(
            $filas_hijas_localizaciones,
            $filas_sensores,
            $filas_actuadores)
		{
            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $nombre = $icono_dato_erroneo;
            $cadena_numero_localizaciones_hijas = $icono_dato_erroneo;
            $numero_sensores = $icono_dato_erroneo;
            $numero_actuadores = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Número de localizaciones hijas
                $numero_localizaciones_hijas = Localizacion::dame_numero_localizaciones_hijas($this->id, $filas_hijas_localizaciones);
                $cadena_numero_localizaciones_hijas = $numero_localizaciones_hijas." (".$this->params["orden"].")";

                // Número de sensores y actuadores
                $numero_sensores = Localizacion::dame_numero_nodos($this->id, TIPO_NODO_SENSOR, $filas_sensores);
                $numero_actuadores = Localizacion::dame_numero_nodos($this->id, TIPO_NODO_ACTUADOR, $filas_actuadores);
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
                $cadena_numero_localizaciones_hijas,
                $numero_sensores,
                $numero_actuadores
			));
		}


        function dame_opciones_tabla()
		{
            $nombre = htmlspecialchars($this->params['nombre'], ENT_QUOTES);

            $opciones = array();
            $administracion_localizaciones = Localizacion::dame_administracion_localizaciones();
            if ($administracion_localizaciones == true)
            {
                $editar = "<i id='anyade_modifica_localizacion__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' ".
                    "class='icon-pencil color-gris boton_localizaciones_mostrar_ventana_anyadir_modificar_localizacion boton-tabla-datos'></i>";
                $duplicar = "<i id='anyade_modifica_localizacion__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' ".
                    "class='icon-copy color-gris boton_localizaciones_mostrar_ventana_anyadir_modificar_localizacion boton-tabla-datos'></i>";
                $borrar = "<i id='elimina_localizacion__".$this->id."' nombre_localizacion='".$nombre."' ".
                    "class='icon-remove color-gris boton_localizaciones_eliminar_localizacion boton-tabla-datos'></i>";
                $opciones = array($borrar, $duplicar, $editar);
            }

			return ($opciones);
		}


        function dame_herramientas_detalles_tabla()
		{
            $herramientas = "";
            $herramientas .= "
                <span class='boton-herramientas-detalle-tabla-datos'>
                    <button id='boton_refrescar__".$this->id."' class='btn-mini btn btn-success boton_localizaciones_refrescar_tabla_localizacion'>
                        <i class='icon-refresh color-blanco'></i>
                    </button>
                </span>";

			return ($herramientas);
		}


		function dame_detalles_tabla()
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            $info = "";
            $administracion_localizaciones = Localizacion::dame_administracion_localizaciones();

            // Identificador
            if ($administracion_localizaciones == true)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Identificador").": ".$this->id."<br/>";
                $info .= "<br/>";
            }

            // Descripción
            if ($this->params["descripcion"] != "")
			{
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Descripción").": ".htmlspecialchars($this->params["descripcion"], ENT_QUOTES)."<br/>";
                $info .= "<br/>";
			}

            // Nombres de sensores y valores de ratios de la localización
            $info_nombres_valores_sensores_ratios = $this->dame_info_nombres_valores_sensores_ratios();
            if ($info_nombres_valores_sensores_ratios !== NULL)
            {
                $info .= $info_nombres_valores_sensores_ratios;
                $info .= "<br/>";
            }

            // Instalaciones de la localización
            $info_instalaciones = dame_info_instalaciones_localizacion($this->id);
            $numero_instalaciones = count($info_instalaciones);
            if ($numero_instalaciones > 0)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Esta localización tiene")." ".$numero_instalaciones." ";
                if ($numero_instalaciones == 1)
                {
                    $info .= $this->idiomas->_("instalación").":";
                }
                else
                {
                    $info .= $this->idiomas->_("instalaciones").":";
                }
                $lista_nombres_instalaciones = "<ul>";
                foreach ($info_instalaciones as $info_instalacion)
                {
                    $nombre_instalacion = $info_instalacion["nombre"];
                    $lista_nombres_instalaciones .= "<li>".htmlspecialchars($nombre_instalacion, ENT_QUOTES);
                    $lista_nombres_instalaciones .= "</li>";
                }
                $lista_nombres_instalaciones .= "</ul>";
                $info .= $lista_nombres_instalaciones;
                $info .= "<br/>";
            }

            // Sensores y grupos de sensores de la localización
            $info_sensores = dame_info_nodos_localizaciones(
                array($this->id),
                TIPO_NODO_SENSOR,
                CLASE_TODAS,
                false);
            $info_grupos_sensores = dame_info_grupos_nodos_localizaciones(
                array($this->id),
                TIPO_NODO_SENSOR,
                CLASE_TODAS);
            $numero_sensores = count($info_sensores);
            if ($numero_sensores > 0)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Esta localización tiene")." ".$numero_sensores." ";
                if ($numero_sensores == 1)
                {
                    $info .= $this->idiomas->_("sensor").":";
                }
                else
                {
                    $info .= $this->idiomas->_("sensores").":";
                }
                $lista_nombres_sensores = "<ul>";
                foreach ($info_sensores as $info_sensor)
                {
                    $nombre_sensor = $info_sensor["nombre"];
                    $visible_localizaciones_hijas = $info_sensor["visible_localizaciones_hijas"];
                    $lista_nombres_sensores .= "<li>".htmlspecialchars($nombre_sensor, ENT_QUOTES);
                    if ($visible_localizaciones_hijas == VALOR_SI)
                    {
                        $lista_nombres_sensores .= " (".$this->idiomas->_("visible en localizaciones hijas").": ".
                            "<i class='icon-circle color-nodo-visible-localizaciones-hijas'></i>)";
                    }
                    if ($numero_instalaciones > 0)
                    {
                        foreach ($info_instalaciones as $info_instalacion)
                        {
                            if (in_array($info_sensor["id"], $info_instalacion["ids_sensores"]) == true)
                            {
                                $lista_nombres_sensores .= " (".$this->idiomas->_("instalación").": ".
                                    htmlspecialchars($info_instalacion["nombre"], ENT_QUOTES).")";
                                break;
                            }
                        }
                    }
                    $lista_nombres_sensores .= "</li>";
                }
                $lista_nombres_sensores .= "</ul>";
                $info .= $lista_nombres_sensores;
                $info .= "<br/>";
            }
            $numero_grupos_sensores = count($info_grupos_sensores);
            if ($numero_grupos_sensores > 0)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Esta localización tiene")." ".$numero_grupos_sensores." ";
                if ($numero_grupos_sensores == 1)
                {
                    $info .= $this->idiomas->_("grupo de sensores").":";
                }
                else
                {
                    $info .= $this->idiomas->_("grupos de sensores").":";
                }
                $lista_nombres_grupos_sensores = "<ul>";
                foreach ($info_grupos_sensores as $info_grupo_sensores)
                {
                    $nombre_grupo_sensores = $info_grupo_sensores["nombre"];
                    $lista_nombres_grupos_sensores .= "<li>".htmlspecialchars($nombre_grupo_sensores, ENT_QUOTES)."</li>";
                }
                $lista_nombres_grupos_sensores .= "</ul>";
                $info .= $lista_nombres_grupos_sensores;
                $info .= "<br/>";
            }

            // Actuadores y grupos de actuadores de la localización
            $info_actuadores = dame_info_nodos_localizaciones(
                array($this->id),
                TIPO_NODO_ACTUADOR,
                CLASE_TODAS,
                false);
            $info_grupos_actuadores = dame_info_grupos_nodos_localizaciones(
                array($this->id),
                TIPO_NODO_ACTUADOR,
                CLASE_TODAS);
            $numero_actuadores = count($info_actuadores);
            if ($numero_actuadores > 0)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Esta localización tiene")." ".$numero_actuadores." ";
                if ($numero_actuadores == 1)
                {
                    $info .= $this->idiomas->_("actuador").":";
                }
                else
                {
                    $info .= $this->idiomas->_("actuadores").":";
                }
                $lista_nombres_actuadores = "<ul>";
                foreach ($info_actuadores as $info_actuador)
                {
                    $nombre_actuador = $info_actuador["nombre"];
                    $visible_localizaciones_hijas = $info_actuador["visible_localizaciones_hijas"];
                    $lista_nombres_actuadores .= "<li>".htmlspecialchars($nombre_actuador, ENT_QUOTES);
                    if ($visible_localizaciones_hijas == VALOR_SI)
                    {
                        $lista_nombres_actuadores .= " (".$icono_estado = "<i class='icon-circle color-nodo-visible-localizaciones-hijas'></i>"." ".
                            $this->idiomas->_("visible en localizaciones hijas").")";
                    }
                    if ($numero_instalaciones > 0)
                    {
                        foreach ($info_instalaciones as $info_instalacion)
                        {
                            if (in_array($info_actuador["id"], $info_instalacion["ids_actuadores"]) == true)
                            {
                                $lista_nombres_actuadores .= " (".$this->idiomas->_("instalación").": ".
                                    htmlspecialchars($info_instalacion["nombre"], ENT_QUOTES).")";
                                break;
                            }
                        }
                    }
                    $lista_nombres_actuadores .= "</li>";
                }
                $lista_nombres_actuadores .= "</ul>";
                $info .= $lista_nombres_actuadores;
                $info .= "<br/>";
            }
            $numero_grupos_actuadores = count($info_grupos_actuadores);
            if ($numero_grupos_actuadores > 0)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Esta localización tiene")." ".$numero_grupos_actuadores." ";
                if ($numero_grupos_actuadores == 1)
                {
                    $info .= $this->idiomas->_("grupo de actuadores").":";
                }
                else
                {
                    $info .= $this->idiomas->_("grupos de actuadores").":";
                }
                $lista_nombres_grupos_actuadores = "<ul>";
                foreach ($info_grupos_actuadores as $info_grupo_actuadores)
                {
                    $nombre_grupo_actuadores = $info_grupo_actuadores["nombre"];
                    $lista_nombres_grupos_actuadores .= "<li>".htmlspecialchars($nombre_grupo_actuadores, ENT_QUOTES)."</li>";
                }
                $lista_nombres_grupos_actuadores .= "</ul>";
                $info .= $lista_nombres_grupos_actuadores;
                $info .= "<br/>";
            }

            // Lista de localizaciones padres
            $consulta_localizaciones_padres = "
                SELECT localizaciones.nombre
                FROM
                    localizaciones,
                    hijas_localizaciones
                WHERE
                    (hijas_localizaciones.localizacion_hija = '".$bd_red->_($this->id)."')
                    AND (hijas_localizaciones.localizacion_padre = localizaciones.id)
                ORDER BY localizaciones.nombre ASC";
            $res_localizaciones_padres = $bd_red->ejecuta_consulta($consulta_localizaciones_padres);
            if ($res_localizaciones_padres == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_localizaciones_padres."'");
            }
            $numero_localizaciones_padres = $res_localizaciones_padres->dame_numero_filas();
            if ($numero_localizaciones_padres > 0)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Esta localización tiene")." ".$numero_localizaciones_padres." ";
                if ($numero_localizaciones_padres == 1)
                {
                    $info .= $this->idiomas->_("padre").":";
                }
                else
                {
                    $info .= $this->idiomas->_("padres").":";
                }
                $lista_nombres_localizaciones_padres = "<ul>";
                while ($fila_localizacion_padre = $res_localizaciones_padres->dame_siguiente_fila())
                {
                    $nombre_localizacion_padre = $fila_localizacion_padre["nombre"];
                    $lista_nombres_localizaciones_padres .= "<li>".htmlspecialchars($nombre_localizacion_padre, ENT_QUOTES)."</li>";
                }
                $lista_nombres_localizaciones_padres .= "</ul>";
                $info .= $lista_nombres_localizaciones_padres;
                $info .= "<br/>";
            }

            // Se muestra la tabla de las localizaciones hijas
            if (($administracion_localizaciones == true) || ($this->params["orden"] > 0))
            {
                $mostrar_tabla_localizaciones_hijas = true;
            }
            else
            {
                $mostrar_tabla_localizaciones_hijas = false;
            }
            if ($mostrar_tabla_localizaciones_hijas == true)
            {
                $id_elemento_hijas_localizacion = 'hijas-localizacion'.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
                $info .= "<div id='".$id_elemento_hijas_localizacion."' class='contenedor-detalle-tabla-datos'>".
                    $this->dame_tabla_hijas()."</div>";
                $info .= "<br/>";
            }

            // Se elimina el último salto de línea (si es necesario)
            if (endsWith($info, "<br/>") == true)
            {
                $info = substr($info, 0, -strlen("<br/>"));
            }

            return ($info);
		}


        //
        // Funciones para las hijas
        //


        function dame_tabla_hijas()
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se crean las opciones de la tabla
            $administracion_localizaciones = Localizacion::dame_administracion_localizaciones();
            $opciones = array();
            if ($administracion_localizaciones == true)
            {
                $boton_anyadir_hija_localizacion = "<i id='anyade_modifica_hija_localizacion__".$this->id."' class='icon-plus color-blanco boton_localizaciones_mostrar_ventana_anyadir_modificar_hija_localizacion boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_hija_localizacion);
            }
            $boton_actualizar_tabla_hijas_localizacion = "<i id='actualiza_tabla_hijas_localizacion__".$this->id."' class='icon-refresh color-blanco boton_actualizar_tabla_hijas_localizacion boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_hijas_localizacion);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_HIJAS_LOCALIZACIONES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-hijas-localizacion",
                $this->idiomas->_("Hijas"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = array(
                $this->idiomas->_("Nombre"),
                $this->idiomas->_("Nivel"));
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las localizaciones hijas a la tabla y el pie de tabla
            $consulta = "
                SELECT
                    hijas_localizaciones.id AS id,
                    hijas_localizaciones.localizacion_hija AS localizacion_hija,
                    localizaciones.nombre AS nombre,
                    localizaciones.orden AS orden
                FROM
                    hijas_localizaciones, localizaciones
                WHERE
                    (hijas_localizaciones.localizacion_padre = '".$bd_red->_($this->id)."')
                    AND (hijas_localizaciones.localizacion_hija = localizaciones.id)
                ORDER BY localizaciones.nombre ASC";
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_hijas = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $datos = array(
                    $fila['nombre'],
                    $fila['orden']
                );

                $opciones = array();
                if ($administracion_localizaciones == true)
                {
                    $editar = "<i id='anyade_modifica_hija_localizacion__".$this->id."__".$fila['localizacion_hija']."__".$fila['id']."' class='icon-pencil color-gris boton_localizaciones_mostrar_ventana_anyadir_modificar_hija_localizacion boton-tabla-datos'></i>";
                    $borrar = "<i id='elimina_hija_localizacion__".$this->id."__".$fila['localizacion_hija']."__".$fila['id']."' class='icon-remove color-gris boton_localizaciones_eliminar_hija_localizacion boton-tabla-datos'></i>";
                    $opciones = array($borrar, $editar);
                }
                $params_fila = array(
                    "opciones" => $opciones
                );
                $tabla->anyade_fila(
                    "datosHijaLocalizacion__".$this->id."__".$fila['id'],
                    $datos,
                    $params_fila
                );
            }
            $tabla->anyade_pie($this->idiomas->_("Hijas").": ".$numero_hijas);

            return ($tabla->dame_tabla(false));
		}


        //
        // Funciones de topología de localizaciones
        //


        function dame_info_topologia_localizacion()
		{
            // Hijos y número de nodos finales
            $hijos = array();
            $numero_nodos_finales = 0;

            // Se recuperan los identificadores de las localizaciones del usuario
            $ids_localizaciones_usuario = dame_ids_localizaciones_usuario_actual(true);

            // Topología de localizaciones padres e hijas
            $topologia_localizaciones_padres_hijas = $this->dame_info_topologia_localizaciones_padres_hijas($ids_localizaciones_usuario);
            array_push($hijos, $topologia_localizaciones_padres_hijas);
            $numero_nodos_finales += $topologia_localizaciones_padres_hijas["numero_nodos_finales"];

            // Topología de sensores y actuadores (propios, de localizaciones padres e hijas)
            $topologia_sensores_actuadores = $this->dame_info_topologia_sensores_actuadores();
            array_push($hijos, $topologia_sensores_actuadores);
            $numero_nodos_finales += $topologia_sensores_actuadores["numero_nodos_finales"];

            $info = array(
				"nombre" => $this->params["nombre"],
				"info_nodo" => NULL,
				"color_nodo" => NULL,
				"children" => $hijos,
                "numero_nodos_finales" => $numero_nodos_finales
			);
            return ($info);
		}


        function dame_info_topologia_localizaciones_padres_hijas($ids_localizaciones_usuario_actual)
		{
            // Se recuperan los nombres de las localizaciones
            $nombres_localizaciones_usuario_actual = dame_nombres_localizaciones($ids_localizaciones_usuario_actual);

            // Se recupera la topología de las localizaciones padres e hijas
            $info_topologia_localizaciones_padres = $this->dame_info_topologia_localizaciones_padres(
                $ids_localizaciones_usuario_actual,
                $nombres_localizaciones_usuario_actual);
            $info_topologia_localizaciones_hijas = $this->dame_info_topologia_localizaciones_hijas(
                $ids_localizaciones_usuario_actual,
                $nombres_localizaciones_usuario_actual);

            // Se devuelve la topología de las localizaciones padres e hijas
            $numero_nodos_finales = 0;
            $hijos_localizaciones_padres_hijas = array();
            if ($info_topologia_localizaciones_padres != NULL)
            {
                array_push($hijos_localizaciones_padres_hijas, $info_topologia_localizaciones_padres);
                $numero_nodos_finales += $info_topologia_localizaciones_padres["numero_nodos_finales"];
            }
            if ($info_topologia_localizaciones_hijas != NULL)
            {
                array_push($hijos_localizaciones_padres_hijas, $info_topologia_localizaciones_hijas);
                $numero_nodos_finales += $info_topologia_localizaciones_hijas["numero_nodos_finales"];
            }
            if ($numero_nodos_finales == 0)
            {
                $numero_nodos_finales = 1;
            }
            $topologia_localizaciones_padres_hijas = array(
                "nombre" => $this->idiomas->_("Localizaciones"),
                "info_nodo" => NULL,
                "color_nodo" => NULL,
                "children" => $hijos_localizaciones_padres_hijas,
                "numero_nodos_finales" => $numero_nodos_finales
            );
            return ($topologia_localizaciones_padres_hijas);
        }


        function dame_info_topologia_localizaciones_padres($ids_localizaciones_usuario_actual, $nombres_localizaciones_usuario_actual)
		{
            // Localizaciones ascendientes ordenadas por grado y por orden alfabético
            $nombres_localizaciones_ascendientes_grados = array();
            $ids_grados_localizaciones_ascendientes = dame_ids_grados_localizaciones_ascendientes($this->id);
            foreach ($ids_grados_localizaciones_ascendientes as $id_localizacion => $grado_localizacion)
            {
                if (in_array($id_localizacion, $ids_localizaciones_usuario_actual) == true)
                {
                    if (array_key_exists($grado_localizacion, $nombres_localizaciones_ascendientes_grados) == false)
                    {
                        $nombres_localizaciones_ascendientes_grados[$grado_localizacion] = array();
                    }
                    array_push($nombres_localizaciones_ascendientes_grados[$grado_localizacion], $nombres_localizaciones_usuario_actual[$id_localizacion]);
                }
            }
            foreach ($nombres_localizaciones_ascendientes_grados as $grado_localizaciones => $nombres_localizaciones)
            {
                sort($nombres_localizaciones_ascendientes_grados[$grado_localizaciones]);
            }
            ksort($nombres_localizaciones_ascendientes_grados);

            // Se añaden los nodos de topología de árbol de los padres por grados
            if (count($nombres_localizaciones_ascendientes_grados) > 0)
            {
                $numero_nodos_finales = 0;
                $hijos_localizaciones_padres = array();
                foreach ($nombres_localizaciones_ascendientes_grados as $grado_localizaciones => $nombres_localizaciones)
                {
                    $hijos_localizaciones_padres_grado = array();
                    foreach ($nombres_localizaciones as $nombre_localizacion)
                    {
                        $topologia_localizacion_padre = array(
                            "nombre" => $nombre_localizacion,
                            "info_nodo" => NULL,
                            "color_nodo" => NULL,
                            "children" => NULL
                        );
                        array_push($hijos_localizaciones_padres_grado, $topologia_localizacion_padre);
                    }
                    $topologia_localizaciones_padres_grado = array(
                        "nombre" => $this->idiomas->_("Grado")." ".$grado_localizaciones,
                        "info_nodo" => NULL,
                        "color_nodo" => NULL,
                        "children" => $hijos_localizaciones_padres_grado,
                    );
                    array_push($hijos_localizaciones_padres, $topologia_localizaciones_padres_grado);
                    $numero_nodos_finales += count($nombres_localizaciones);
                }

                $topologia_localizaciones_padres = array(
                    "nombre" => $this->idiomas->_("Padres"),
                    "info_nodo" => NULL,
                    "color_nodo" => NULL,
                    "children" => $hijos_localizaciones_padres,
                    "numero_nodos_finales" => $numero_nodos_finales
                );
                return ($topologia_localizaciones_padres);
            }
            else
            {
                return (NULL);
            }
		}


        function dame_info_topologia_localizaciones_hijas($ids_localizaciones_usuario_actual, $nombres_localizaciones_usuario_actual)
		{
            // Localizaciones descendientes ordenadas por grado y por orden alfabético
            $nombres_localizaciones_descendientes_grados = array();
            $ids_grados_localizaciones_descendientes = dame_ids_grados_localizaciones_descendientes($this->id);
            foreach ($ids_grados_localizaciones_descendientes as $id_localizacion => $grado_localizacion)
            {
                if (in_array($id_localizacion, $ids_localizaciones_usuario_actual) == true)
                {
                    if (array_key_exists($grado_localizacion, $nombres_localizaciones_descendientes_grados) == false)
                    {
                        $nombres_localizaciones_descendientes_grados[$grado_localizacion] = array();
                    }
                    array_push($nombres_localizaciones_descendientes_grados[$grado_localizacion], $nombres_localizaciones_usuario_actual[$id_localizacion]);
                }
            }
            foreach ($nombres_localizaciones_descendientes_grados as $grado_localizaciones => $nombres_localizaciones)
            {
                sort($nombres_localizaciones_descendientes_grados[$grado_localizaciones]);
            }
            ksort($nombres_localizaciones_descendientes_grados);

            // Se añaden los nodos de topología de árbol de los hijos por grados
            if (count($nombres_localizaciones_descendientes_grados) > 0)
            {
                $numero_nodos_finales = 0;
                $hijos_localizaciones_hijas = array();
                foreach ($nombres_localizaciones_descendientes_grados as $grado_localizaciones => $nombres_localizaciones)
                {
                    $hijos_localizaciones_hijas_grado = array();
                    foreach ($nombres_localizaciones as $nombre_localizacion)
                    {
                        $topologia_localizacion_hija = array(
                            "nombre" => $nombre_localizacion,
                            "info_nodo" => NULL,
                            "color_nodo" => NULL,
                            "children" => NULL
                        );
                        array_push($hijos_localizaciones_hijas_grado, $topologia_localizacion_hija);
                    }
                    $topologia_localizaciones_hijas_grado = array(
                        "nombre" => $this->idiomas->_("Grado")." ".$grado_localizaciones,
                        "info_nodo" => NULL,
                        "color_nodo" => NULL,
                        "children" => $hijos_localizaciones_hijas_grado,
                    );
                    array_push($hijos_localizaciones_hijas, $topologia_localizaciones_hijas_grado);
                    $numero_nodos_finales += count($nombres_localizaciones);
                }

                $topologia_hijos = array(
                    "nombre" => $this->idiomas->_("Hijas"),
                    "info_nodo" => NULL,
                    "color_nodo" => NULL,
                    "children" => $hijos_localizaciones_hijas,
                    "numero_nodos_finales" => $numero_nodos_finales
                );
                return ($topologia_hijos);
            }
            else
            {
                return (NULL);
            }
		}


        function dame_info_topologia_sensores_actuadores()
		{
            // Localizaciones ascendientes y descendientes
            $ids_localizaciones_ascendientes = dame_ids_localizaciones_ascendientes(array($this->id));
            $ids_localizaciones_descendientes = dame_ids_localizaciones_descendientes(array($this->id));

            // Se recupera la topología de los sensores y de los grupos de sensores
            $info_topologia_sensores = $this->dame_info_topologia_nodos(
                $ids_localizaciones_ascendientes,
                $ids_localizaciones_descendientes,
                TIPO_NODO_SENSOR);
            $info_topologia_grupos_sensores = $this->dame_info_topologia_grupos_nodos(
                $ids_localizaciones_descendientes,
                TIPO_NODO_SENSOR);

            // Se recupera la topología de los actuadores y de los grupos de actuadores
            $info_topologia_actuadores = $this->dame_info_topologia_nodos(
                $ids_localizaciones_ascendientes,
                $ids_localizaciones_descendientes,
                TIPO_NODO_ACTUADOR);
            $info_topologia_grupos_actuadores = $this->dame_info_topologia_grupos_nodos(
                $ids_localizaciones_descendientes,
                TIPO_NODO_ACTUADOR);

            // Se devuelve la topología de los nodos
            $numero_nodos_finales = 0;
            $hijos_sensores_actuadores = array();
            if ($info_topologia_sensores != NULL)
            {
                array_push($hijos_sensores_actuadores, $info_topologia_sensores);
                $numero_nodos_finales += $info_topologia_sensores["numero_nodos_finales"];
            }
            if ($info_topologia_grupos_sensores != NULL)
            {
                array_push($hijos_sensores_actuadores, $info_topologia_grupos_sensores);
                $numero_nodos_finales += $info_topologia_grupos_sensores["numero_nodos_finales"];
            }
            if ($info_topologia_actuadores != NULL)
            {
                array_push($hijos_sensores_actuadores, $info_topologia_actuadores);
                $numero_nodos_finales += $info_topologia_actuadores["numero_nodos_finales"];
            }
            if ($info_topologia_grupos_actuadores != NULL)
            {
                array_push($hijos_sensores_actuadores, $info_topologia_grupos_actuadores);
                $numero_nodos_finales += $info_topologia_grupos_actuadores["numero_nodos_finales"];
            }
            if ($numero_nodos_finales == 0)
            {
                $numero_nodos_finales = 1;
            }

            $topologia_sensores_actuadores = array(
                "nombre" => $this->idiomas->_("Sensores y actuadores"),
                "info_nodo" => NULL,
                "color_nodo" => NULL,
                "children" => $hijos_sensores_actuadores,
                "numero_nodos_finales" => $numero_nodos_finales
            );
            return ($topologia_sensores_actuadores);
        }


        function dame_info_topologia_nodos(
            $ids_localizaciones_ascendientes_usuario_actual,
            $ids_localizaciones_descendientes_usuario_actual,
            $tipo_nodo)
        {
            // Nombre de los nodos
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    $nombre_nodos = $this->idiomas->_("Sensores");
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    $nombre_nodos = $this->idiomas->_("Actuadores");
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
                }
            }

            // Se recuperan los nodos de la localización
            $info_nodos_localizacion = dame_info_nodos_localizaciones(
                array($this->id),
                $tipo_nodo,
                CLASE_TODAS,
                false);

            // Se recuperan los nodos de las localizaciones ascendientes y descendientes
            $info_nodos_localizaciones_ascendientes = dame_info_nodos_localizaciones(
                $ids_localizaciones_ascendientes_usuario_actual,
                $tipo_nodo,
                CLASE_TODAS,
                true);
            $info_nodos_localizaciones_descendientes = dame_info_nodos_localizaciones(
                $ids_localizaciones_descendientes_usuario_actual,
                $tipo_nodo,
                CLASE_TODAS,
                false);

            // Número de nodos finales e hijos de nodos
            $numero_nodos_finales = 0;
            $hijos_nodos = array();

            // Se añaden los nodos de topología de árbol de los nodos de la localización
            if (count($info_nodos_localizacion) > 0)
            {
                $hijos_nodos_localizacion = array();
                foreach ($info_nodos_localizacion as $info_nodo_localizacion)
                {
                    $nombre_nodo = htmlspecialchars($info_nodo_localizacion["nombre"], ENT_QUOTES);
                    $info_nodo = $nombre_nodo;
                    $visible_localizaciones_hijas = $info_nodo_localizacion["visible_localizaciones_hijas"];
                    switch ($visible_localizaciones_hijas)
                    {
                        case VALOR_SI:
                        {
                            $info_nodo .= " (".$this->idiomas->_("visible en localizaciones hijas").")";
                            $color_nodo = TOPOLOGIA_ARBOL_COLOR_NODO_AZUL;
                            break;
                        }
                        case VALOR_NO:
                        {
                            $color_nodo = TOPOLOGIA_ARBOL_COLOR_NODO_GRIS;
                            break;
                        }
                    }
                    $topologia_nodo_localizacion = array(
                        "nombre" => $nombre_nodo,
                        "info_nodo" => $info_nodo,
                        "color_nodo" => $color_nodo,
                        "children" => NULL
                    );
                    array_push($hijos_nodos_localizacion, $topologia_nodo_localizacion);
                }
                $topologia_nodos_localizacion = array(
                    "nombre" => $this->idiomas->_("Propios"),
                    "info_nodo" => NULL,
                    "color_nodo" => NULL,
                    "children" => $hijos_nodos_localizacion,
                );
                array_push($hijos_nodos, $topologia_nodos_localizacion);
                $numero_nodos_finales += count($info_nodos_localizacion);
            }

            // Se añaden los nodos de topología de árbol de los nodos de las localizaciones padres
            if (count($info_nodos_localizaciones_ascendientes) > 0)
            {
                $hijos_nodos_localizaciones_padres = array();
                foreach ($info_nodos_localizaciones_ascendientes as $info_nodo_localizaciones_ascendientes)
                {
                    $nombre_nodo = htmlspecialchars($info_nodo_localizaciones_ascendientes["nombre"], ENT_QUOTES);
                    $nombre_localizacion = htmlspecialchars($info_nodo_localizaciones_ascendientes["nombre_localizacion"], ENT_QUOTES);
                    $info_nodo = $nombre_nodo." (".$this->idiomas->_("localización").": ".$nombre_localizacion.")";
                    $visible_localizaciones_hijas = $info_nodo_localizaciones_ascendientes["visible_localizaciones_hijas"];
                    switch ($visible_localizaciones_hijas)
                    {
                        case VALOR_SI:
                        {
                            $info_nodo .= " (".$this->idiomas->_("visible en localizaciones hijas").")";
                            $color_nodo = TOPOLOGIA_ARBOL_COLOR_NODO_AZUL;
                            break;
                        }
                        case VALOR_NO:
                        {
                            $color_nodo = NULL;
                            break;
                        }
                    }
                    $topologia_nodo_localizaciones_ascendientes = array(
                        "nombre" => $nombre_nodo,
                        "info_nodo" => $info_nodo,
                        "color_nodo" => $color_nodo,
                        "children" => NULL
                    );
                    array_push($hijos_nodos_localizaciones_padres, $topologia_nodo_localizaciones_ascendientes);
                }
                $topologia_nodos_localizaciones_padres = array(
                    "nombre" => $this->idiomas->_("Padres"),
                    "info_nodo" => NULL,
                    "color_nodo" => NULL,
                    "children" => $hijos_nodos_localizaciones_padres,
                );
                array_push($hijos_nodos, $topologia_nodos_localizaciones_padres);
                $numero_nodos_finales += count($info_nodos_localizaciones_ascendientes);
            }

            // Se añaden los nodos de topología de árbol de los nodos de las localizaciones hijas
            if (count($info_nodos_localizaciones_descendientes) > 0)
            {
                $hijos_nodos_localizaciones_hijas = array();
                foreach ($info_nodos_localizaciones_descendientes as $info_nodo_localizaciones_descendientes)
                {
                    $nombre_nodo = htmlspecialchars($info_nodo_localizaciones_descendientes["nombre"], ENT_QUOTES);
                    $nombre_localizacion = htmlspecialchars($info_nodo_localizaciones_descendientes["nombre_localizacion"], ENT_QUOTES);
                    $info_nodo = $nombre_nodo." (".$this->idiomas->_("localización").": ".$nombre_localizacion.")";
                    $visible_localizaciones_hijas = $info_nodo_localizaciones_descendientes["visible_localizaciones_hijas"];
                    switch ($visible_localizaciones_hijas)
                    {
                        case VALOR_SI:
                        {
                            $info_nodo .= " (".$this->idiomas->_("visible en localizaciones hijas").")";
                            $color_nodo = TOPOLOGIA_ARBOL_COLOR_NODO_AZUL;
                            break;
                        }
                        case VALOR_NO:
                        {
                            $color_nodo = NULL;
                            break;
                        }
                    }
                    $topologia_nodo_localizaciones_descendientes = array(
                        "nombre" => $nombre_nodo,
                        "info_nodo" => $info_nodo,
                        "color_nodo" => $color_nodo,
                        "children" => NULL
                    );
                    array_push($hijos_nodos_localizaciones_hijas, $topologia_nodo_localizaciones_descendientes);
                }
                $topologia_nodos_localizaciones_hijas = array(
                    "nombre" => $this->idiomas->_("Hijas"),
                    "info_nodo" => NULL,
                    "color_nodo" => NULL,
                    "children" => $hijos_nodos_localizaciones_hijas,
                );
                array_push($hijos_nodos, $topologia_nodos_localizaciones_hijas);
                $numero_nodos_finales += count($info_nodos_localizaciones_descendientes);
            }

            // Si se han añadido nodos se devuelven los hijos
            if (count($hijos_nodos) > 0)
            {
                $topologia_nodos = array(
                    "nombre" => $nombre_nodos,
                    "info_nodo" => NULL,
                    "color_nodo" => NULL,
                    "children" => $hijos_nodos,
                    "numero_nodos_finales" => $numero_nodos_finales
                );
                return ($topologia_nodos);
            }
            else
            {
                return (NULL);
            }
        }


        function dame_info_topologia_grupos_nodos($ids_localizaciones_descendientes_usuario_actual, $tipo_nodo)
        {
            // Nombre de los nodos
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    $nombre_grupos_nodos = $this->idiomas->_("Grupos de sensores");
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    $nombre_grupos_nodos = $this->idiomas->_("Grupos de actuadores");
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
                }
            }

            // Se recuperan los grupos de nodos de la localización
            $info_grupos_nodos_localizacion = dame_info_grupos_nodos_localizaciones(
                array($this->id),
                $tipo_nodo,
                CLASE_TODAS);

            // Se recuperan los grupos de nodos de las localizaciones descendientes
            // (no se muestran los grupos de nodos de las localizaciones ascendientes)
            $info_grupos_nodos_localizaciones_descendientes = dame_info_grupos_nodos_localizaciones(
                $ids_localizaciones_descendientes_usuario_actual,
                $tipo_nodo,
                CLASE_TODAS);

            // Número de nodos finales e hijos de grupos de nodos
            $numero_nodos_finales = 0;
            $hijos_grupos_nodos = array();

            // Se añaden los nodos de topología de árbol de los grupos de nodos de la localización
            if (count($info_grupos_nodos_localizacion) > 0)
            {
                $hijos_grupos_nodos_localizacion = array();
                foreach ($info_grupos_nodos_localizacion as $info_grupo_nodos_localizacion)
                {
                    $nombre_grupo_nodos_localizacion = htmlspecialchars($info_grupo_nodos_localizacion["nombre"], ENT_QUOTES);
                    $topologia_grupo_nodos_localizacion = array(
                        "nombre" => $nombre_grupo_nodos_localizacion,
                        "info_nodo" => NULL,
                        "color_nodo" => NULL,
                        "children" => NULL
                    );
                    array_push($hijos_grupos_nodos_localizacion, $topologia_grupo_nodos_localizacion);
                }
                $topologia_grupos_nodos_localizacion = array(
                    "nombre" => $this->idiomas->_("Propios"),
                    "info_nodo" => NULL,
                    "color_nodo" => NULL,
                    "children" => $hijos_grupos_nodos_localizacion,
                );
                array_push($hijos_grupos_nodos, $topologia_grupos_nodos_localizacion);
                $numero_nodos_finales += count($info_grupos_nodos_localizacion);
            }

            // Se añaden los nodos de topología de árbol de los grupos de nodos de las localizaciones hijas
            if (count($info_grupos_nodos_localizaciones_descendientes) > 0)
            {
                $hijos_grupos_nodos_localizaciones_hijas = array();
                foreach ($info_grupos_nodos_localizaciones_descendientes as $info_grupo_nodos_localizaciones_descendientes)
                {
                    $nombre_grupo_nodos_localizaciones_descendientes = htmlspecialchars($info_grupo_nodos_localizaciones_descendientes["nombre"], ENT_QUOTES);
                    $topologia_grupo_nodos_localizaciones_descendientes = array(
                        "nombre" => $nombre_grupo_nodos_localizaciones_descendientes,
                        "info_nodo" => NULL,
                        "color_nodo" => NULL,
                        "children" => NULL
                    );
                    array_push($hijos_grupos_nodos_localizaciones_hijas, $topologia_grupo_nodos_localizaciones_descendientes);
                }
                $topologia_grupos_nodos_localizaciones_hijas = array(
                    "nombre" => $this->idiomas->_("Hijas"),
                    "info_nodo" => NULL,
                    "color_nodo" => NULL,
                    "children" => $hijos_grupos_nodos_localizaciones_hijas,
                );
                array_push($hijos_grupos_nodos, $topologia_grupos_nodos_localizaciones_hijas);
                $numero_nodos_finales += count($info_grupos_nodos_localizaciones_descendientes);
            }

            // Si se han añadido nodos se devuelven los hijos
            if (count($hijos_grupos_nodos) > 0)
            {
                $topologia_nodos = array(
                    "nombre" => $nombre_grupos_nodos,
                    "info_nodo" => NULL,
                    "color_nodo" => NULL,
                    "children" => $hijos_grupos_nodos,
                    "numero_nodos_finales" => $numero_nodos_finales
                );
                return ($topologia_nodos);
            }
            else
            {
                return (NULL);
            }
        }


        //
        // Funciones del interfaz 'ObjetoMapa'
        //


        // Devuelve la imagen para el mapa
        function dame_datos_imagen_mapa()
		{
            // Ruta de imagen base
            $ruta_imagen_base = $_SESSION["directorio"]."/rsc/imagenes/marker-icon-localizacion.png";

            // Se recupera la imagen del mapa
            $datos_imagen_mapa = dame_imagen_mapa_objeto(
                $this,
                $this->id,
                $this->params["nombre"],
                "localizacion",
                $ruta_imagen_base);
            return ($datos_imagen_mapa);
		}


        // Crea la imagen del texto auxiliar
        function crea_imagen_texto_auxiliar(&$ruta_imagen_texto_auxiliar)
		{
        }


        // Añade las rutas de las imágenes satélite
        function anyade_rutas_imagenes_satelite(&$rutas_imagenes_satelite)
		{
        }


		// Devuelve la información para tooltip del mapa
		function dame_tooltip_mapa($id_mapa)
		{
            $info = "";
			$info .= "<b>".$this->idiomas->_("Localización")."</b><br/>";
			$info .= $this->idiomas->_("Nombre").": ".$this->params["nombre"]."<br/>";
            $info .= $this->idiomas->_("Nivel").": ".$this->params["orden"]."<br/>";

            // Nombres de sensores y valores de ratios de la localización
            $info_nombres_valores_sensores_ratios = $this->dame_info_nombres_valores_sensores_ratios();
            if ($info_nombres_valores_sensores_ratios !== NULL)
            {
                $info .= $info_nombres_valores_sensores_ratios;
            }

			return ($info);
		}


        //
        // Funciones auxiliares
        //


        static function dame_administracion_localizaciones()
        {
            $administracion_localizaciones = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_localizaciones"]["administracion_localizaciones"] == VALOR_SI);
            return ($administracion_localizaciones);
        }


        function dame_info_nombres_valores_sensores_ratios()
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Ratios de la localización
			$consulta_ratios = "
				SELECT
                    ratios.nombre AS nombre,
                    ratios.unidad_medida AS unidad_medida,
                    ratios.tipo AS tipo,
                    ratios_localizaciones.valor AS valor,
                    ratios_localizaciones.sensor AS sensor
				FROM
                    ratios_localizaciones,
                    ratios
				WHERE
                    (ratios_localizaciones.localizacion = '".$bd_red->_($this->id)."')
                    AND (ratios.id = ratios_localizaciones.ratio)
                ORDER BY ratios.nombre ASC";
			$res_ratios = $bd_red->ejecuta_consulta($consulta_ratios);
            if ($res_ratios == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_ratios."'");
            }
            $numero_ratios = $res_ratios->dame_numero_filas();
            $nombres_valores_sensores_ratios = "<ul>";
            while ($fila_ratio = $res_ratios->dame_siguiente_fila())
            {
                $nombre_ratio = $fila_ratio["nombre"];
                $tipo_ratio = $fila_ratio["tipo"];
                $unidad_medida_ratio = $fila_ratio["unidad_medida"];
                $valor_ratio = $fila_ratio["valor"];
                $id_sensor_ratio = $fila_ratio["sensor"];

                switch ($tipo_ratio)
                {
                    case TIPO_RATIO_FIJO:
                    {
                        $nombres_valores_sensores_ratios .= "
                            <li>".
                                htmlspecialchars($nombre_ratio, ENT_QUOTES)." (".htmlspecialchars($unidad_medida_ratio, ENT_QUOTES)."): ".
                                formatea_numero($valor_ratio, 2)."
                            </li>";
                        break;
                    }
                    case TIPO_RATIO_VARIABLE:
                    {
                        $nombre_sensor_ratio = dame_nombre_sensor($id_sensor_ratio);
                        $nombres_valores_sensores_ratios .= "
                            <li>".
                                htmlspecialchars($nombre_ratio, ENT_QUOTES)." (".htmlspecialchars($unidad_medida_ratio, ENT_QUOTES)."): ".
                                htmlspecialchars($nombre_sensor_ratio, ENT_QUOTES)."
                            </li>";
                        break;
                    }
                }
            }
            $nombres_valores_sensores_ratios .= "</ul>";
            if ($numero_ratios > 0)
            {
                $info_nombres_valores_sensores_ratios .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Valores y sensores de ratios").": ".$nombres_valores_sensores_ratios;
            }
            else
            {
                $info_nombres_valores_sensores_ratios = NULL;
            }
            return ($info_nombres_valores_sensores_ratios);
        }


        static function dame_numero_localizaciones_hijas($id_localizacion, $filas_hijas_localizaciones)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            if ($filas_hijas_localizaciones === NULL)
            {
                $consulta_hijas_localizaciones = "
				SELECT
                    COUNT(*) AS localizaciones_hijas
				FROM hijas_localizaciones
				WHERE
                    localizacion_padre = '".$bd_red->_($id_localizacion)."'";
                $res_hijas_localizaciones = $bd_red->ejecuta_consulta($consulta_hijas_localizaciones);
                if (($res_hijas_localizaciones == false) || ($res_hijas_localizaciones->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_hijas_localizaciones."'");
                }
                $fila_hijas_localizaciones = $res_hijas_localizaciones->dame_siguiente_fila();
                $numero_localizaciones_hijas = $fila_hijas_localizaciones['localizaciones_hijas'];
            }
            else
            {
                $numero_localizaciones_hijas = 0;
                foreach ($filas_hijas_localizaciones as $fila_hija_localizacion)
                {
                    $id_localizacion_padre = $fila_hija_localizacion["localizacion_padre"];
                    if ($id_localizacion == $id_localizacion_padre)
                    {
                        $numero_localizaciones_hijas += 1;
                    }
                }
            }
            return ($numero_localizaciones_hijas);
        }


        static function dame_numero_nodos($id_localizacion, $tipo_nodo, $filas_nodos)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se recupera el número de nodos de la localización
            if ($filas_nodos === NULL)
            {
                switch ($tipo_nodo)
                {
                    case TIPO_NODO_SENSOR:
                    {
                        $nombre_tabla_nodos = "sensores";
                        break;
                    }
                    case TIPO_NODO_GRUPO_SENSORES:
                    {
                        $nombre_tabla_nodos = "grupos_sensores";
                        break;
                    }
                    case TIPO_NODO_ACTUADOR:
                    {
                        $nombre_tabla_nodos = "actuadores";
                        break;
                    }
                    case TIPO_NODO_GRUPO_ACTUADORES:
                    {
                        $nombre_tabla_nodos = "grupos_actuadores";
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
                    }
                }
                $consulta_nodos = "
                    SELECT
                        COUNT(*) AS numero_nodos
                    FROM ".$nombre_tabla_nodos."
                    WHERE
                        localizacion = '".$bd_red->_($id_localizacion)."'";
                $res_nodos = $bd_red->ejecuta_consulta($consulta_nodos);
                if (($res_nodos == false) || ($res_nodos->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_nodos."'");
                }
                $fila_nodos = $res_nodos->dame_siguiente_fila();
                $numero_nodos = $fila_nodos['numero_nodos'];
            }
            else
            {
                $numero_nodos = 0;
                foreach ($filas_nodos as $fila_nodo)
                {
                    $id_localizacion_nodo = $fila_nodo["localizacion"];
                    if ($id_localizacion == $id_localizacion_nodo)
                    {
                        $numero_nodos += 1;
                    }
                }
            }
            return ($numero_nodos);
        }


        static function dame_nombres_nodos($id_localizacion, $tipo_nodo)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se recupera el número de nodos de la localización
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    $nombre_tabla_nodos = "sensores";
                    break;
                }
                case TIPO_NODO_GRUPO_SENSORES:
                {
                    $nombre_tabla_nodos = "grupos_sensores";
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    $nombre_tabla_nodos = "actuadores";
                    break;
                }
                case TIPO_NODO_GRUPO_ACTUADORES:
                {
                    $nombre_tabla_nodos = "grupos_actuadores";
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
                }
            }
			$consulta_nodos = "
				SELECT nombre
				FROM ".$nombre_tabla_nodos."
				WHERE
                    localizacion = '".$bd_red->_($id_localizacion)."'
                ORDER BY nombre ASC";
			$res_nodos = $bd_red->ejecuta_consulta($consulta_nodos);
            if ($res_nodos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_nodos."'");
            }
            $nombres_nodos = array();
            while ($fila_nodo = $res_nodos->dame_siguiente_fila())
            {
                array_push($nombres_nodos, $fila_nodo["nombre"]);
            }
            return ($nombres_nodos);
        }
	}
?>
