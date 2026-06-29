<?php
	session_start();

	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/EquipoInstalacion.php');


	// Clase que representa una instalación
	class Instalacion
	{
        // Funciones estáticas de instalación


        // Devuelve la cabecera para la tabla de instalaciones
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
				$idiomas->_("Nombre"),
                $idiomas->_("Localización"),
                $idiomas->_("Equipos"),
                $idiomas->_("Sensores"),
                $idiomas->_("Actuadores")
			));
        }


        // Devuelve la consulta para la tabla de instalaciones
        static function dame_consulta_instalaciones(
            $filtro,
            $id_localizacion,
            $incluir_localizaciones_descendientes)
        {
            $consulta = "
                SELECT *
                FROM instalaciones
                WHERE
                    (red = '".$_SESSION["id_red"]."')";
            if ($filtro != "")
            {
                $campos = array("nombre");
                $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
            }
            if ($id_localizacion != ID_TODOS)
            {
                if ($incluir_localizaciones_descendientes == VALOR_SI)
                {
                    $ids_localizaciones = dame_ids_localizaciones_descendientes(array($id_localizacion));
                    array_push($ids_localizaciones, $id_localizacion);
                }
                else
                {
                    $ids_localizaciones = array($id_localizacion);
                }
                $cadena_ids_localizaciones_consulta = dame_cadena_ids_consulta($ids_localizaciones);
                $consulta .= "
                    AND (localizacion IN (".$cadena_ids_localizaciones_consulta."))";
            }
            $mostrar_todas_localizaciones = dame_mostrar_todas_localizaciones();
            if ($mostrar_todas_localizaciones == false)
            {
                $ids_localizaciones_usuario_actual = dame_ids_localizaciones_usuario_actual(true);
                $cadena_ids_localizaciones_usuario_actual = dame_cadena_ids_consulta($ids_localizaciones_usuario_actual);
                $consulta .= "
                    AND (localizacion IN (".$cadena_ids_localizaciones_usuario_actual."))";
            }
            $consulta .= "
                ORDER BY nombre ASC";
            return ($consulta);
        }


        // Devuelve la tabla de instalaciones
        static function dame_tabla_instalaciones(
            $filtro,
            $id_localizacion,
            $incluir_localizaciones_descendientes)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $administracion_instalaciones = Instalacion::dame_administracion_instalaciones();
            if ($administracion_instalaciones == true)
            {
                $boton_anyadir_instalacion = "<i id='anyade_modifica_instalacion' ".
                    "class='icon-plus color-blanco boton_localizaciones_mostrar_ventana_anyadir_modificar_instalacion boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_instalacion);
            }
            $boton_actualizar_tabla_instalaciones = "<i id='actualiza_instalaciones' ".
                "class='icon-refresh color-blanco boton_localizaciones_actualizar_tabla_instalaciones boton-tabla-datos'></i>";
            $boton_ayuda_tabla_instalaciones = "<i id='ayuda_instalaciones' ".
                "class='icon-question-sign color-blanco boton_localizaciones_ayuda_tabla_instalaciones boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_instalaciones);
            array_push($opciones, $boton_ayuda_tabla_instalaciones);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_INSTALACIONES,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_INSTALACIONES),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "filas_con_opciones" => ($administracion_instalaciones == true),
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-localizaciones-instalaciones",
                $idiomas->_("Instalaciones"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = Instalacion::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las instalaciones a la tabla y el pie de tabla
            $consulta_instalaciones = Instalacion::dame_consulta_instalaciones(
                $filtro,
                $id_localizacion,
                $incluir_localizaciones_descendientes);
            $res_instalaciones = $bd_red->ejecuta_consulta($consulta_instalaciones);
            if ($res_instalaciones == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_instalaciones."'");
            }

            // Identificadores de instalaciones del usuario actual
            $mostrar_todas_localizaciones = dame_mostrar_todas_localizaciones();
            if ($mostrar_todas_localizaciones == false)
            {
                $ids_instalaciones_usuario = Instalacion::dame_ids_instalaciones_usuario_actual();
            }

            // Filas de las instalaciones
            $filas_instalaciones = array();
            while ($fila_instalacion = $res_instalaciones->dame_siguiente_fila())
            {
                $anyadir_instalacion = true;
                if ($mostrar_todas_localizaciones == false)
                {
                    if (in_array($fila_instalacion["id"], $ids_instalaciones_usuario) == false)
                    {
                        $anyadir_instalacion = false;
                    }
                }
                if ($anyadir_instalacion == true)
                {
                    array_push($filas_instalaciones, $fila_instalacion);
                }
            }

            // Se añaden las instalaciones
            $nombres_localizaciones = array();
            for ($i = 0; $i < count($filas_instalaciones); $i++)
            {
                $id_localizacion = $filas_instalaciones[$i]["localizacion"];
                if (array_key_exists($id_localizacion, $nombres_localizaciones) == true)
                {
                    $nombre_localizacion = $nombres_localizaciones[$id_localizacion];
                }
                else
                {
                    $nombre_localizacion = dame_nombre_localizacion($id_localizacion);
                    $nombres_localizaciones[$id_localizacion] = $nombre_localizacion;
                }
                $filas_instalaciones[$i]["nombre_localizacion"] = $nombre_localizacion;
            }

            // (https://stackoverflow.com/questions/832709/natural-sorting-algorithm-in-php-with-support-for-unicode)
            $locale_anterior = setlocale(LC_ALL, $_SESSION["idioma"].".utf8");
            if ($locale_anterior === false)
            {
                foreach ($filas_instalaciones as $indice => $fila_instalacion)
                {
                    $nombres_localizaciones_ordenacion[$indice] = convierte_ascii_estandar($fila_instalacion['nombre_localizacion']);
                    $nombres_ordenacion[$indice] = convierte_ascii_estandar($fila_instalacion['nombre']);
                }
            }
            else
            {
                foreach ($filas_instalaciones as $indice => $fila_instalacion)
                {
                    $nombres_localizaciones_ordenacion[$indice] = $fila_instalacion['nombre_localizacion'];
                    $nombres_ordenacion[$indice] = $fila_instalacion['nombre'];
                }
            }
            array_multisort(
                $nombres_localizaciones_ordenacion, SORT_ASC, SORT_LOCALE_STRING,
                $nombres_ordenacion, SORT_ASC, SORT_LOCALE_STRING,
                $filas_instalaciones);
            if ($locale_anterior !== false)
            {
                setlocale(LC_COLLATE, $locale_anterior);
            }

            // Se añade cada una de las instalaciones a la tabla
            $numero_instalaciones = 0;
            foreach ($filas_instalaciones as $fila_instalacion)
            {
                $instalacion = new Instalacion($fila_instalacion);
                $params_fila = array(
                    "opciones" => $instalacion->dame_opciones_tabla()
                );
                $tabla->anyade_fila(
                    "datosInstalacion__".$fila_instalacion['id'],
                    $instalacion->dame_datos_tabla(),
                    $params_fila
                );
                $numero_instalaciones += 1;
            }
            $tabla->anyade_pie($idiomas->_("Instalaciones").": ".$numero_instalaciones);

            // Se devuelve la tabla
            return ($tabla->dame_tabla());
        }


        // Miembros de instalación


		public $idiomas;

		public $id;
        public $params;


		function __construct($params = array())
		{
			$this->idiomas = new Idiomas();

			$this->id = $params["id"];
            $this->params = $params;
		}


        function dame_datos_tabla()
		{
            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $nombre = $icono_dato_erroneo;
            $nombre_localizacion = $icono_dato_erroneo;
            $numero_equipos = $icono_dato_erroneo;
            $numero_sensores = $icono_dato_erroneo;
            $numero_actuadores = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Localización
                if (array_key_exists("nombre_localizacion", $this->params) == true)
                {
                    $nombre_localizacion = $this->params["nombre_localizacion"];
                }
                else
                {
                    $nombre_localizacion = dame_nombre_localizacion($this->params["localizacion"]);
                }
                $nombre_localizacion = htmlspecialchars($nombre_localizacion, ENT_QUOTES);

                // Número de equipos, sensores y actuadores
                $numero_equipos = Instalacion::dame_numero_equipos($this->id);
                $numero_sensores = Instalacion::dame_numero_nodos($this->id, TIPO_NODO_SENSOR);
                $numero_actuadores = Instalacion::dame_numero_nodos($this->id, TIPO_NODO_ACTUADOR);
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
                $nombre_localizacion,
                $numero_equipos,
                $numero_sensores,
                $numero_actuadores
			));
		}


        function dame_opciones_tabla()
		{
            $nombre = htmlspecialchars($this->params['nombre'], ENT_QUOTES);

            $opciones = array();
            $administracion_instalaciones = Instalacion::dame_administracion_instalaciones();
            if ($administracion_instalaciones == true)
            {
                $editar = "<i id='anyade_modifica_instalacion__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' ".
                    "class='icon-pencil color-gris boton_localizaciones_mostrar_ventana_anyadir_modificar_instalacion boton-tabla-datos'></i>";
                $duplicar = "<i id='anyade_modifica_instalacion__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' ".
                    "class='icon-copy color-gris boton_localizaciones_mostrar_ventana_anyadir_modificar_instalacion boton-tabla-datos'></i>";
                $borrar = "<i id='elimina_instalacion__".$this->id."' nombre_instalacion='".$nombre."' ".
                    "class='icon-remove color-gris boton_localizaciones_eliminar_instalacion boton-tabla-datos'></i>";
                $opciones = array($borrar, $duplicar, $editar);
            }

			return ($opciones);
		}


        function dame_herramientas_detalles_tabla()
		{
            // Herramientas de detalles de instalación
            $herramientas = "";
            $herramientas .= "
                <span class='boton-herramientas-detalle-tabla-datos'>
                    <button id='boton_refrescar__".$this->id."' class='btn-mini btn btn-success boton_localizaciones_refrescar_tabla_instalacion'>
                        <i class='icon-refresh color-blanco'></i>
                    </button>
                 </span>";
            if ($this->params["imagen"] == VALOR_SI)
            {
                $origen = ORIGEN_IMAGEN_INSTALACION_IMAGEN;
                $id_origen = $this->id;
                $nombre_ventana = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $boton_mostrar_imagen = "
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button class='btn-mini btn btn-success boton_mostrar_imagen_base_datos_ventana' ".
                            "origen='".$origen."' id_origen='".$id_origen."' nombre_ventana='".$nombre_ventana."'>".
                            "<i class='icon-picture color-blanco'></i>
                        </button>
                    </span>";
                $herramientas .= $boton_mostrar_imagen;
            }

			return ($herramientas);
		}


		function dame_detalles_tabla()
		{
            $info = "";
            $administracion_instalaciones = Instalacion::dame_administracion_instalaciones();

            // Identificador
            if ($administracion_instalaciones == true)
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

            // Tabla de equipos
            $info .= $this->dame_tabla_equipos();
            $info .= "<br/>";

            // Información de equipos de la instalación (para asignar los nombres a cada uno de los sensores y actuadores)
            $info_equipos = dame_info_equipos_instalacion($this->id);

            // Lista de sensores
            $ids_sensores = Instalacion::dame_ids_nodos($this->id, TIPO_NODO_SENSOR);
            if (count($ids_sensores) > 0)
            {
                $numero_sensores = count($ids_sensores);
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Esta instalación tiene")." ".$numero_sensores." ";
                if ($numero_sensores == 1)
                {
                    $info .= $this->idiomas->_("sensor").":";
                }
                else
                {
                    $info .= $this->idiomas->_("sensores").":";
                }
                $nombres_sensores = dame_nombres_sensores($ids_sensores);
                $lista_nombres_sensores = "<ul>";
                for ($i = 0; $i < count($ids_sensores); $i++)
                {
                    $id_sensor = $ids_sensores[$i];
                    $nombre_sensor = $nombres_sensores[$i];

                    $lista_nombres_sensores .= "<li>".htmlspecialchars($nombre_sensor, ENT_QUOTES);
                    foreach ($info_equipos as $info_equipo)
                    {
                        if (in_array($id_sensor, $info_equipo["ids_sensores"]) == true)
                        {
                            $lista_nombres_sensores .= " (".$this->idiomas->_("equipo").": ".
                                htmlspecialchars($info_equipo["nombre"], ENT_QUOTES).")";
                            break;
                        }
                    }
                    $lista_nombres_sensores .= "</li>";
                }
                $lista_nombres_sensores .= "</ul>";
                $info .= $lista_nombres_sensores;
                $info .= "<br/>";
            }

            // Lista de actuadores
            $ids_actuadores = Instalacion::dame_ids_nodos($this->id, TIPO_NODO_ACTUADOR);
            if (count($ids_actuadores) > 0)
            {
                $numero_actuadores = count($ids_actuadores);
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Esta instalación tiene")." ".$numero_actuadores." ";
                if ($numero_actuadores == 1)
                {
                    $info .= $this->idiomas->_("actuador").":";
                }
                else
                {
                    $info .= $this->idiomas->_("actuadores").":";
                }
                $nombres_actuadores = dame_nombres_actuadores($ids_actuadores);
                $lista_nombres_actuadores = "<ul>";
                for ($i = 0; $i < count($ids_actuadores); $i++)
                {
                    $id_actuador = $ids_actuadores[$i];
                    $nombre_actuador = $nombres_actuadores[$i];

                    $lista_nombres_actuadores .= "<li>".htmlspecialchars($nombre_actuador, ENT_QUOTES);
                    foreach ($info_equipos as $info_equipo)
                    {
                        if (in_array($id_actuador, $info_equipo["ids_actuadores"]) == true)
                        {
                            $lista_nombres_actuadores .= " (".$this->idiomas->_("equipo").": ".
                                htmlspecialchars($info_equipo["nombre"], ENT_QUOTES).")";
                            break;
                        }
                    }
                    $lista_nombres_actuadores .= "</li>";
                }
                $lista_nombres_actuadores .= "</ul>";
                $info .= $lista_nombres_actuadores;
                $info .= "<br/>";
            }

            // Se elimina el último salto de línea (si es necesario)
            if (endsWith($info, "<br/>") == true)
            {
                $info = substr($info, 0, -strlen("<br/>"));
            }

            return ($info);
		}


        function dame_tabla_equipos()
        {
            $id_elemento_equipos_instalacion = "equipos-instalacion".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
            $tabla_equipos = "<div id='".$id_elemento_equipos_instalacion."' class='contenedor-detalle-tabla-datos'>".
                EquipoInstalacion::dame_tabla_equipos($this->id)."</div>";
            return ($tabla_equipos);
        }


        //
        // Funciones de topología de instalaciones
        //


        function dame_info_topologia_instalacion()
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            // Hijos y números de nodos finales y de niveles de nodos
            $hijos = array();
            $numero_nodos_finales = 0;
            $numero_niveles_nodos = 0;

            // Se añade la topología de cada uno de los equipos de la instalación
            $consulta_equipos = "
                SELECT *
                FROM equipos_instalaciones
                WHERE
                    (instalacion = '".$bd_red->_($this->id)."')
                    AND (equipo_padre = '".ID_NINGUNO."')
                ORDER BY nombre ASC";
            $res_equipos = $bd_red->ejecuta_consulta($consulta_equipos);
            if ($res_equipos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_equipos."'");
            }
            while ($fila_equipo = $res_equipos->dame_siguiente_fila())
            {
                $equipo = new EquipoInstalacion($fila_equipo);
                $info_topologia_equipo = $equipo->dame_info_topologia_equipo(NULL);
                array_push($hijos, $info_topologia_equipo);
                $numero_nodos_finales += $info_topologia_equipo["numero_nodos_finales"];
                if ($info_topologia_equipo["numero_niveles_nodos"] > $numero_niveles_nodos)
                {
                    $numero_niveles_nodos = $info_topologia_equipo["numero_niveles_nodos"];
                }
            }

            // Se devuelve la topología de la instalación
            if ($numero_nodos_finales == 0)
            {
                $numero_nodos_finales = 1;
            }
            $info = array(
				"nombre" => $this->params["nombre"],
				"info_nodo" => NULL,
				"color_nodo" => NULL,
				"children" => $hijos,
                "numero_nodos_finales" => $numero_nodos_finales,
                "numero_niveles_nodos" => $numero_niveles_nodos
			);
            return ($info);
		}


        //
        // Funciones del interfaz 'ObjetoMapa'
        //


        // Devuelve la imagen para el mapa
        function dame_datos_imagen_mapa()
		{
            // Ruta de imagen base
            $ruta_imagen_base = $_SESSION["directorio"]."/rsc/imagenes/marker-icon-instalacion.png";

            // Se recupera la imagen del mapa
            $datos_imagen_mapa = dame_imagen_mapa_objeto(
                $this,
                $this->id,
                $this->params["nombre"],
                "instalacion",
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
			$info .= "<b>".$this->idiomas->_("Instalación")."</b><br/>";
			$info .= $this->idiomas->_("Nombre").": ".$this->params["nombre"]."<br/>";

            // Número de equipos
            $numero_equipos = Instalacion::dame_numero_equipos($this->id);
            if ($numero_equipos > 0)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Esta instalación tiene")." ".$numero_equipos." ";
                if ($numero_equipos == 1)
                {
                    $info .= $this->idiomas->_("equipo");
                }
                else
                {
                    $info .= $this->idiomas->_("equipos");
                }
            }

			return ($info);
		}


        //
        // Funciones auxiliares
        //


        static function dame_administracion_instalaciones()
        {
            $administracion_instalaciones = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_localizaciones"]["administracion_localizaciones"] == VALOR_SI) ||
                ($_SESSION["parametros_modulo_localizaciones"]["administracion_instalaciones"] == VALOR_SI);
            return ($administracion_instalaciones);
        }


        static function dame_ids_instalaciones_usuario_actual()
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Ids de localizaciones del usuario
            $ids_localizaciones_usuario = dame_ids_localizaciones_usuario_actual(true);

            // Identificadores de instalaciones
            $ids_instalaciones = array();

            // Se recorren todas las instalaciones pertenecientes a localizaciones visibles por el usuario actual
            $cadena_ids_localizaciones_consulta = dame_cadena_ids_consulta($ids_localizaciones_usuario);
            $consulta_instalaciones = "
                SELECT id
                FROM instalaciones
                WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (localizacion IN (".$cadena_ids_localizaciones_consulta."))
                ORDER BY nombre ASC";
            $res_instalaciones = $bd_red->ejecuta_consulta($consulta_instalaciones);
            if ($res_instalaciones == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_instalaciones."'");
            }
            while ($fila_instalacion = $res_instalaciones->dame_siguiente_fila())
            {
                $id = $fila_instalacion["id"];
                array_push($ids_instalaciones, $id);
            }

            return ($ids_instalaciones);
        }


        static function dame_numero_equipos($id_instalacion)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se recupera el número de equipos de la instalación
			$consulta_equipos = "
				SELECT
                    COUNT(*) AS equipos
				FROM equipos_instalaciones
				WHERE
                    instalacion = '".$bd_red->_($id_instalacion)."'";
			$res_equipos = $bd_red->ejecuta_consulta($consulta_equipos);
            if (($res_equipos == false) || ($res_equipos->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_equipos."'");
            }
			$fila_equipos = $res_equipos->dame_siguiente_fila();
            $numero_equipos = $fila_equipos['equipos'];
            return ($numero_equipos);
        }


        static function dame_numero_nodos($id_instalacion, $tipo_nodo)
        {
            $ids_nodos = Instalacion::dame_ids_nodos($id_instalacion, $tipo_nodo);
            $numero_nodos = count($ids_nodos);
            return ($numero_nodos);
        }


        static function dame_ids_nodos($id_instalacion, $tipo_nodo)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se recuperan los sensores de los equipos de la instalación
			$consulta_equipos = "
				SELECT
                    sensores,
                    actuadores
				FROM equipos_instalaciones
				WHERE
                    instalacion = '".$bd_red->_($id_instalacion)."'";
			$res_equipos = $bd_red->ejecuta_consulta($consulta_equipos);
            if ($res_equipos == false)
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_equipos."'");
            }

            // Se recorren los equipos y se añaden los nodos de cada uno de ellos
            $ids_nodos = array();
            while ($fila_equipo = $res_equipos->dame_siguiente_fila())
            {
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
	}
?>
