<?php

    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Instalacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/util_equipos.php');


    // Clase que representa un equipo de una instalación
    class EquipoInstalacion
    {
        // Funciones estáticas de equipo


        // Devuelve la cabecera para la tabla de equipos
        static function dame_cabecera_tabla()
        {
            $idiomas = new Idiomas();

            return (array(
                $idiomas->_("Nombre"),
                $idiomas->_("Padre"),
                $idiomas->_("Hijos")." (".$idiomas->_("nivel").")",
                $idiomas->_("Sensores"),
                $idiomas->_("Actuadores"),
                $idiomas->_("Estado")
            ));
        }


        // Devuelve la consulta para la tabla de equipos
        static function dame_consulta_equipos($id_instalacion)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta = "
                SELECT *
                FROM equipos_instalaciones
                WHERE
                    instalacion = '".$bd_red->_($id_instalacion)."'
                ORDER BY nombre ASC";
            return ($consulta);
        }


        // Devuelve la tabla de equipos
        static function dame_tabla_equipos($id_instalacion)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $administracion_instalaciones = Instalacion::dame_administracion_instalaciones();
            if ($administracion_instalaciones == true)
            {
                $boton_anyadir_equipo = "<i id='anyade_modifica_equipo__".$id_instalacion."' ".
                    "class='icon-plus color-blanco boton_localizaciones_mostrar_ventana_anyadir_modificar_equipo_instalacion boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_equipo);
            }
            $boton_actualizar_tabla_equipos = "<i id='actualiza_tabla_equipos__".$id_instalacion."' ".
                "class='icon-refresh color-blanco boton_localizaciones_actualizar_tabla_equipos_instalacion boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_equipos);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_EQUIPOS_INSTALACIONES,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_EQUIPOS_INSTALACIONES),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-equipos-instalaciones",
                $idiomas->_("Equipos"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = EquipoInstalacion::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las acciones a la tabla y el pie de tabla
            $consulta = EquipoInstalacion::dame_consulta_equipos($id_instalacion);
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_equipos = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $equipo = new EquipoInstalacion($fila);
                $params_fila = array(
                    "opciones" => $equipo->dame_opciones_tabla(),
                );
                $tabla->anyade_fila(
                    "datosEquipoInstalacion__".$id_instalacion."__".$fila['id'], $equipo->dame_datos_tabla(), $params_fila
                );
            }
            $tabla->anyade_pie($idiomas->_("Equipos").": ".$numero_equipos);

            return ($tabla->dame_tabla(false));
        }


        // Miembros de equipo


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
            $nombre_equipo_padre = $icono_dato_erroneo;
            $cadena_numero_equipos_hijos = $icono_dato_erroneo;
            $numero_sensores = $icono_dato_erroneo;
            $numero_actuadores = $icono_dato_erroneo;
            $descripcion_estado = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Equipo padre
                $nombre_equipo_padre = dame_nombre_equipo_instalacion($this->params["equipo_padre"]);
                $nombre_equipo_padre = htmlspecialchars($nombre_equipo_padre, ENT_QUOTES);

                // Número de equipos hijos, sensores y actuadores
                $numero_equipos_hijos = EquipoInstalacion::dame_numero_equipos_hijos($this->id);
                $numero_sensores = EquipoInstalacion::dame_numero_nodos($this->params["sensores"]);
                $numero_actuadores = EquipoInstalacion::dame_numero_nodos($this->params["actuadores"]);

                // Se añade el nivel al número de equipos hijos
                $cadena_numero_equipos_hijos = $numero_equipos_hijos." (".$this->params["orden"].")";

                // Estado
                switch ($this->params["estado"])
                {
                    case ESTADO_EQUIPO_INSTALACION_OK:
                    {
                        $icono_estado = "<i class='icon-circle color-estado-equipo-ok'></i>";
                        break;
                    }
                    case ESTADO_EQUIPO_INSTALACION_ERROR:
                    {
                        $icono_estado = "<i class='icon-circle color-estado-equipo-error'></i>";
                        break;
                    }
                    case ESTADO_EQUIPO_INSTALACION_PENDIENTE:
                    {
                        $icono_estado = "<i class='icon-circle color-estado-equipo-pendiente'></i>";
                        break;
                    }
                    default:
                    {
                        throw new Exception("Estado de equipo desconocido: '".$this->params["estado"]."'");
                    }
                }
                $descripcion_estado = $icono_estado." (".strtolower(EquipoInstalacion::dame_descripcion_estado_equipo($this->params["estado"])).")";
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
                $nombre_equipo_padre,
                $cadena_numero_equipos_hijos,
                $numero_sensores,
                $numero_actuadores,
                $descripcion_estado,
            ));
        }


        function dame_opciones_tabla()
        {
            $nombre = htmlspecialchars($this->params['nombre'], ENT_QUOTES);

            $opciones = array();
            $administracion_instalaciones = Instalacion::dame_administracion_instalaciones();
            if ($administracion_instalaciones == true)
            {
                $editar = "<i id='anyade_modifica_equipo__".$this->params["instalacion"]."__".$this->id."' ".
                    "class='icon-pencil color-gris boton_localizaciones_mostrar_ventana_anyadir_modificar_equipo_instalacion boton-tabla-datos'></i>";
                $borrar = "<i id='elimina_equipo__".$this->params["instalacion"]."__".$this->id."' nombre_equipo='".$nombre."' ".
                    "class='icon-remove color-gris boton_localizaciones_eliminar_equipo_instalacion boton-tabla-datos'></i>";
                $opciones = array($borrar, $editar);
            }

            return ($opciones);
        }


        function dame_detalles_tabla()
		{
            $bd_red = BaseDatosRed::dame_base_datos();

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

            // Lista de equipos hijos
            $consulta_equipos_hijos = "
                SELECT nombre
                FROM equipos_instalaciones
                WHERE
                    equipo_padre = '".$bd_red->_($this->id)."'
                ORDER BY nombre ASC";
            $res_equipos_hijos = $bd_red->ejecuta_consulta($consulta_equipos_hijos);
            if ($res_equipos_hijos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_equipos_hijos."'");
            }
            $numero_equipos_hijos = $res_equipos_hijos->dame_numero_filas();
            if ($numero_equipos_hijos > 0)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Este equipo tiene")." ".$numero_equipos_hijos." ";
                if ($numero_equipos_hijos == 1)
                {
                    $info .= $this->idiomas->_("hijo").":";
                }
                else
                {
                    $info .= $this->idiomas->_("hijos").":";
                }
                $lista_nombres_equipos_hijos = "<ul>";
                while ($fila_equipo_hijo = $res_equipos_hijos->dame_siguiente_fila())
                {
                    $nombre_equipo_hijo = $fila_equipo_hijo["nombre"];
                    $lista_nombres_equipos_hijos .= "<li>".htmlspecialchars($nombre_equipo_hijo, ENT_QUOTES)."</li>";
                }
                $lista_nombres_equipos_hijos .= "</ul>";
                $info .= $lista_nombres_equipos_hijos;
                $info .= "<br/>";
            }

            // Lista de sensores
            if ($this->params["sensores"] != "")
            {
                $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["sensores"]);
                $numero_sensores = count($ids_sensores);
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Este equipo tiene")." ".$numero_sensores." ";
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
                foreach ($nombres_sensores as $nombre_sensor)
                {
                    $lista_nombres_sensores .= "<li>".htmlspecialchars($nombre_sensor, ENT_QUOTES)."</li>";
                }
                $lista_nombres_sensores .= "</ul>";
                $info .= $lista_nombres_sensores;
                $info .= "<br/>";
            }

            // Lista de actuadores
            if ($this->params["actuadores"] != "")
            {
                $ids_actuadores = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["actuadores"]);
                $numero_actuadores = count($ids_actuadores);
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Este equipo tiene")." ".$numero_actuadores." ";
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
                foreach ($nombres_actuadores as $nombre_actuador)
                {
                    $lista_nombres_actuadores .= "<li>".htmlspecialchars($nombre_actuador, ENT_QUOTES)."</li>";
                }
                $lista_nombres_actuadores .= "</ul>";
                $info .= $lista_nombres_actuadores;
                $info .= "<br/>";
            }

            // Observaciones
            if ($this->params["observaciones"] != "")
			{
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Observaciones").": ".htmlspecialchars($this->params["observaciones"], ENT_QUOTES)."<br/>";
                $info .= "<br/>";
			}

            // Icono de imagen (sólo si tiene posición en el mapa)
            $info_posicion_mapa = dame_info_posicion_mapa_base_datos(
                TIPO_ELEMENTO_MAPA_EQUIPO_INSTALACION,
                $this->id,
                ORIGEN_MAPA_INSTALACION,
                $this->params["instalacion"]);
            if ($info_posicion_mapa !== NULL)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Icono de imagen").": ".dame_descripcion_icono_mapa($this->params["icono_imagen"])."<br/>";
                $info .= "<br/>";
            }

            // Tabla de anotaciones
            $numero_anotaciones_equipo = 0;
            $tabla_anotaciones_equipo = $this->dame_tabla_anotaciones($numero_anotaciones_equipo);
            if (($administracion_instalaciones == true) || ($numero_anotaciones_equipo > 0))
            {
                $id_elemento_anotaciones_equipo = 'anotaciones-equipo'.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
                $info .= "<div id='".$id_elemento_anotaciones_equipo."' class='contenedor-detalle-tabla-datos'>".
                    $tabla_anotaciones_equipo."</div>";
                $info .= "<br/>";
            }

            // Se elimina el último salto de línea (si es necesario)
            if (endsWith($info, "<br/>") == true)
            {
                $info = substr($info, 0, -strlen("<br/>"));
            }

            return ($info);
		}


        function dame_tabla_anotaciones(&$numero_anotaciones)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $administracion_instalaciones = Instalacion::dame_administracion_instalaciones();
            if ($administracion_instalaciones == true)
            {
                $boton_anyadir_anotacion = "<i id='anyade_modifica_anotacion_equipo__".$this->params["instalacion"]."__".$this->id."' class='icon-plus color-blanco boton_localizaciones_mostrar_ventana_anyadir_modificar_anotacion_equipo_instalacion boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_anotacion);
            }
            $boton_actualizar_tabla_anotaciones = "<i id='actualiza_tabla_anotaciones_equipo__".$this->params["instalacion"]."__".$this->id."' class='icon-refresh color-blanco boton_localizaciones_actualizar_tabla_anotaciones_equipo_instalacion boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_anotaciones);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_ANOTACIONES_EQUIPOS_INSTALACIONES,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_ANOTACIONES_EQUIPOS_INSTALACIONES),
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-anotaciones-equipo",
                $this->idiomas->_("Anotaciones"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = array(
                $this->idiomas->_("Hora"),
                $this->idiomas->_("Texto"),
                $this->idiomas->_("Foto"));
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las anotaciones a la tabla y el pie de tabla
            $consulta = "
                SELECT *
                FROM anotaciones_equipos_instalaciones
                WHERE
                    equipo = '".$bd_red->_($this->id)."'
                ORDER BY hora";
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_anotaciones = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                // Información de la anotación
                $id_anotacion = $fila["id"];
                $cadena_fecha_hora_base_datos_utc = $fila["hora"];
                $texto = $fila["texto"];
                $foto = $fila["foto"];

                // Hora
                $zona_horaria = dame_zona_horaria_local();
                $cadena_fecha_hora_local_utc = convierte_formato_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_fecha_hora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_fecha_hora_local_local_sin_segundos = convierte_formato_fecha($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_hora_local_sin_segundos"]);

                // Foto (botón para ver la foto)
                switch ($foto)
                {
                    case VALOR_SI:
                    {
                        $origen = ORIGEN_IMAGEN_ANOTACION_EQUIPO_INSTALACION_FOTO;
                        $id_origen = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                            $this->params["instalacion"],
                            $this->id,
                            $id_anotacion));
                        $nombre_ventana = htmlspecialchars($this->params['nombre'], ENT_QUOTES)." (".$cadena_fecha_hora_local_local_sin_segundos.")";
                        $boton_mostrar_foto = "<i id='muestra_foto_anotacion_equipo__".$this->params["instalacion"]."__".$this->id."__".$id_anotacion."' ".
                            "class='icon-camera color-gris clickable boton_mostrar_imagen_base_datos_ventana' ".
                            "origen='".$origen."' id_origen='".$id_origen."' nombre_ventana='".$nombre_ventana."'></i>";
                        break;
                    }
                    case VALOR_NO:
                    {
                        $boton_mostrar_foto = "";
                    }
                }
                $datos = array(
                    $cadena_fecha_hora_local_local_sin_segundos,
                    htmlspecialchars($texto, ENT_QUOTES),
                    $boton_mostrar_foto
                );

                $opciones = array();
                if ($administracion_instalaciones == true)
                {
                    $editar = "<i id='anyade_modifica_anotacion_equipo__".$this->params["instalacion"]."__".$this->id."__".$fila['id']."' class='icon-pencil color-gris boton_localizaciones_mostrar_ventana_anyadir_modificar_anotacion_equipo_instalacion boton-tabla-datos'></i>";
                    $borrar = "<i id='elimina_anotacion_equipo__".$this->params["instalacion"]."__".$this->id."__".$fila['id']."' class='icon-remove color-gris boton_localizaciones_eliminar_anotacion_equipo_instalacion boton-tabla-datos' ".
                        "nombre_equipo='".$nombre."' hora_anotacion='".$cadena_fecha_hora_local_local_sin_segundos."'></i>";
                    $opciones = array($borrar, $editar);
                }
                $params_fila = array(
                    "opciones" => $opciones
                );
                $tabla->anyade_fila(
                    "datosAccionProgramacion__".$this->id."__".$fila['id'],
                    $datos,
                    $params_fila
                );
            }
            $tabla->anyade_pie($this->idiomas->_("Anotaciones").": ".$numero_anotaciones);

            return ($tabla->dame_tabla(false));
        }


        //
        // Funciones de parámetros de equipos
        //


        // Devuelve los estados del equipo
        static function dame_estados_equipo()
        {
            $estados_equipo = array();
            array_push($estados_equipo, ESTADO_EQUIPO_INSTALACION_OK);
            array_push($estados_equipo, ESTADO_EQUIPO_INSTALACION_ERROR);
            array_push($estados_equipo, ESTADO_EQUIPO_INSTALACION_PENDIENTE);
            return ($estados_equipo);
        }


        // Devuelve la descripción del estado del equipo
        static function dame_descripcion_estado_equipo($estado_equipo)
        {
            switch ($estado_equipo)
            {
                case ESTADO_EQUIPO_INSTALACION_OK:
                {
                    $descripcion = "Ok";
                    break;
                }
                case ESTADO_EQUIPO_INSTALACION_ERROR:
                {
                    $descripcion = "Error";
                    break;
                }
                case ESTADO_EQUIPO_INSTALACION_PENDIENTE:
                {
                    $descripcion = "Pendiente";
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
        // Funciones de topología de equipos
        //


        function dame_info_topologia_equipo($estado_nodos_equipo)
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            // Hijos, números de nodos finales y de niveles de nodos
            $hijos = array();
            $numero_nodos_finales = 0;
            $numero_niveles_nodos = 0;

            // Estado del equipo
            $estado_equipo = $this->params["estado"];

            // Si no hay estado de nodos de equipo, son el mismo que el del equipo (es el equipo 'inicial')
            if ($estado_nodos_equipo === NULL)
            {
                $estado_nodos_equipo = $estado_equipo;
            }

            // Se añade la topología de cada uno de los equipos hijos del equipo
            $consulta_equipos_hijos = "
                SELECT *
                FROM equipos_instalaciones
                WHERE
                    equipo_padre = '".$bd_red->_($this->id)."'
                ORDER BY nombre ASC";
            $res_equipos_hijos = $bd_red->ejecuta_consulta($consulta_equipos_hijos);
            if ($res_equipos_hijos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_equipos_hijos."'");
            }
            while ($fila_equipo_hijo = $res_equipos_hijos->dame_siguiente_fila())
            {
                $equipo_hijo = new EquipoInstalacion($fila_equipo_hijo);
                $estado_equipo_hijo = $fila_equipo_hijo["estado"];
                switch ($estado_equipo_hijo)
                {
                    case ESTADO_EQUIPO_INSTALACION_OK:
                    {
                        $estado_nodos_equipo_hijo = $estado_nodos_equipo;
                        break;
                    }
                    case ESTADO_EQUIPO_INSTALACION_ERROR:
                    {
                        $estado_nodos_equipo_hijo = $estado_equipo_hijo;
                        break;
                    }
                    case ESTADO_EQUIPO_INSTALACION_PENDIENTE:
                    {
                        if ($estado_nodos_equipo == ESTADO_EQUIPO_INSTALACION_ERROR)
                        {
                            $estado_nodos_equipo_hijo = $estado_nodos_equipo;
                        }
                        else
                        {
                            $estado_nodos_equipo_hijo = $estado_equipo_hijo;
                        }
                        break;
                    }
                    default:
                    {
                        throw new Exception("Estado desconocido: '".$estado_equipo_hijo."'");
                    }
                }
                $info_topologia_equipo_hijo = $equipo_hijo->dame_info_topologia_equipo($estado_nodos_equipo_hijo);
                array_push($hijos, $info_topologia_equipo_hijo);
                $numero_nodos_finales += $info_topologia_equipo_hijo["numero_nodos_finales"];
                if ($info_topologia_equipo_hijo["numero_niveles_nodos"] > $numero_niveles_nodos)
                {
                    $numero_niveles_nodos = $info_topologia_equipo_hijo["numero_niveles_nodos"];
                }
            }

            // Se añade la topología de los sensores y los actuadores del equipo
            $info_topologia_sensores_equipo = $this->dame_info_topologia_nodos_equipo(TIPO_NODO_SENSOR, $estado_nodos_equipo);
            if ($info_topologia_sensores_equipo !== NULL)
            {
                array_push($hijos, $info_topologia_sensores_equipo);
                $numero_nodos_finales += $info_topologia_sensores_equipo["numero_nodos_finales"];
                if ($numero_niveles_nodos < 2)
                {
                    $numero_niveles_nodos = 2;
                }
            }
            $info_topologia_actuadores_equipo = $this->dame_info_topologia_nodos_equipo(TIPO_NODO_ACTUADOR, $estado_nodos_equipo);
            if ($info_topologia_actuadores_equipo !== NULL)
            {
                array_push($hijos, $info_topologia_actuadores_equipo);
                $numero_nodos_finales += $info_topologia_actuadores_equipo["numero_nodos_finales"];
                if ($numero_niveles_nodos < 2)
                {
                    $numero_niveles_nodos = 2;
                }
            }

            // Se devuelve la topología del equipo
            if ($numero_nodos_finales == 0)
            {
                $numero_nodos_finales = 1;
            }
            $numero_niveles_nodos += 1;
            $info_nodo = htmlspecialchars($this->params["nombre"], ENT_QUOTES).
                " (".EquipoInstalacion::dame_descripcion_estado_equipo($this->params["estado"]).")";
            $info = array(
				"nombre" => $this->params["nombre"],
				"info_nodo" => $info_nodo,
				"color_nodo" => EquipoInstalacion::dame_color_nodo_topologia_equipo($this->params["estado"]),
				"children" => $hijos,
                "numero_nodos_finales" => $numero_nodos_finales,
                "numero_niveles_nodos" => $numero_niveles_nodos
			);
            return ($info);
		}


        function dame_info_topologia_nodos_equipo($tipo_nodo, $estado_nodos_equipo)
        {
            // Color y descripción del estado de los nodos (depende del estado de los equipos padre)
            $color_nodo_topologia_equipo = EquipoInstalacion::dame_color_nodo_topologia_equipo($estado_nodos_equipo);
            $descripcion_estado_nodo_equipo = EquipoInstalacion::dame_descripcion_estado_equipo($estado_nodos_equipo);

            // Tipo de nodo
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    $nombre_nodos = $this->idiomas->_("Sensores");
                    $cadena_ids_nodos = $this->params["sensores"];
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    $nombre_nodos = $this->idiomas->_("Actuadores");
                    $cadena_ids_nodos = $this->params["actuadores"];
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
                }
            }

            // Identificadores de nodos
            if ($cadena_ids_nodos == "")
            {
                $ids_nodos = array();
            }
            else
            {
                $ids_nodos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_nodos);
            }

            // Número de nodos finales e hijos de nodos
            $numero_nodos_finales = count($ids_nodos);
            $hijos_nodos_equipo = array();

            // Se recorren cada uno de los nodos
            foreach ($ids_nodos as $id_nodo)
            {
                switch ($tipo_nodo)
                {
                    case TIPO_NODO_SENSOR:
                    {
                        $nombre_nodo = dame_nombre_sensor($id_nodo);
                        break;
                    }
                    case TIPO_NODO_ACTUADOR:
                    {
                        $nombre_nodo = dame_nombre_actuador($id_nodo);
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
                    }
                }

                $info_nodo = htmlspecialchars($nombre_nodo, ENT_QUOTES)." (".$descripcion_estado_nodo_equipo.")";
                $topologia_nodo_equipo = array(
                    "nombre" => $nombre_nodo,
                    "info_nodo" => $info_nodo,
                    "color_nodo" => $color_nodo_topologia_equipo,
                    "children" => NULL
                );
                array_push($hijos_nodos_equipo, $topologia_nodo_equipo);
            }

            // Si se han añadido nodos se devuelven los hijos
            if (count($hijos_nodos_equipo) > 0)
            {
                $topologia_nodos = array(
                    "nombre" => $nombre_nodos,
                    "info_nodo" => NULL,
                    "color_nodo" => NULL,
                    "children" => $hijos_nodos_equipo,
                    "numero_nodos_finales" => $numero_nodos_finales
                );
                return ($topologia_nodos);
            }
            else
            {
                return (NULL);
            }
        }


        static function dame_color_nodo_topologia_equipo($estado_equipo)
        {
            switch ($estado_equipo)
            {
                case ESTADO_EQUIPO_INSTALACION_OK:
                {
                    $color_nodo = TOPOLOGIA_ARBOL_COLOR_NODO_VERDE;
                    break;
                }
                case ESTADO_EQUIPO_INSTALACION_ERROR:
                {
                    $color_nodo = TOPOLOGIA_ARBOL_COLOR_NODO_ROJO;
                    break;
                }
                case ESTADO_EQUIPO_INSTALACION_PENDIENTE:
                {
                    $color_nodo = TOPOLOGIA_ARBOL_COLOR_NODO_NARANJA;
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


        //
        // Funciones del interfaz 'ObjetoMapa'
        //


        // Devuelve la imagen para el mapa
        function dame_datos_imagen_mapa()
		{
            // Ruta de imagen base
            $icono_base = $this->params["icono_imagen"];
            switch ($this->params["estado"])
            {
                case ESTADO_EQUIPO_INSTALACION_OK:
                {
                    $sufijo_icono_base = "ON";
                    break;
                }
                case ESTADO_EQUIPO_INSTALACION_ERROR:
                {
                    $sufijo_icono_base = "OFF";
                    break;
                }
                case ESTADO_EQUIPO_INSTALACION_PENDIENTE:
                {
                    $sufijo_icono_base = "PENDING";
                    break;
                }
            }
            $ruta_imagen_base = $_SESSION["directorio"]."/rsc/imagenes/marker-icon-".$icono_base."-".$sufijo_icono_base.".png";

            // Se recupera la imagen del mapa
            $datos_imagen_mapa = dame_imagen_mapa_objeto(
                $this,
                $this->id,
                $this->params["nombre"],
                "equipo_instalacion",
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
			$info .= "<b>".$this->idiomas->_("Equipo")."</b><br/>";
			$info .= $this->idiomas->_("Nombre").": ".$this->params["nombre"]."<br/>";

            // Número de equipos hijos, sensores y actuadores
            $numero_equipos_hijos = EquipoInstalacion::dame_numero_equipos_hijos($this->id);
            $numero_sensores = EquipoInstalacion::dame_numero_nodos($this->params["sensores"]);
            $numero_actuadores = EquipoInstalacion::dame_numero_nodos($this->params["actuadores"]);
            if ($numero_equipos_hijos > 0)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Este equipo tiene")." ".$numero_equipos_hijos." ";
                if ($numero_equipos_hijos == 1)
                {
                    $info .= $this->idiomas->_("hijo");
                }
                else
                {
                    $info .= $this->idiomas->_("hijos");
                }
                $info .= "<br/>";
            }
            if ($numero_sensores > 0)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Este equipo tiene")." ".$numero_sensores." ";
                if ($numero_sensores == 1)
                {
                    $info .= $this->idiomas->_("sensor");
                }
                else
                {
                    $info .= $this->idiomas->_("sensores");
                }
                $info .= "<br/>";
            }
            if ($numero_actuadores > 0)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Este equipo tiene")." ".$numero_actuadores." ";
                if ($numero_actuadores == 1)
                {
                    $info .= $this->idiomas->_("actuador");
                }
                else
                {
                    $info .= $this->idiomas->_("actuadores");
                }
                $info .= "<br/>";
            }

            // Estado y observaciones
            switch ($this->params["estado"])
            {
                case ESTADO_EQUIPO_INSTALACION_OK:
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ";
                    break;
                }
                case ESTADO_EQUIPO_INSTALACION_ERROR:
                {
                    $info .= "<i class='icon-warning-sign color-rojo'></i> ";
                    break;
                }
                case ESTADO_EQUIPO_INSTALACION_PENDIENTE:
                {
                    $info .= "<i class='icon-question-sign color-gris'></i> ";
                    break;
                }
            }
            $info .= $this->idiomas->_("Estado").": ".EquipoInstalacion::dame_descripcion_estado_equipo($this->params["estado"])."<br/>";
            if ($this->params["observaciones"] != "")
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Observaciones").": ".$this->params["observaciones"];
            }

			return ($info);
		}


        //
        // Funciones auxiliares
        //


        static function dame_numero_equipos_hijos($id_equipo)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se recupera el número de equipos hijos de la localización
			$consulta_equipos_hijos = "
				SELECT
                    COUNT(*) AS equipos_hijos
				FROM equipos_instalaciones
				WHERE
                    equipo_padre = '".$bd_red->_($id_equipo)."'";
			$res_equipos_hijos = $bd_red->ejecuta_consulta($consulta_equipos_hijos);
            if (($res_equipos_hijos == false) || ($res_equipos_hijos->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_equipos_hijos."'");
            }
			$fila_equipos_hijos = $res_equipos_hijos->dame_siguiente_fila();
            $numero_equipos_hijos = $fila_equipos_hijos['equipos_hijos'];
            return ($numero_equipos_hijos);
        }


        static function dame_numero_nodos($cadena_ids_nodos)
        {
            $ids_nodos = array();
            if ($cadena_ids_nodos != "")
            {
                $ids_nodos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_nodos);
            }
            $numero_nodos = count($ids_nodos);
            return ($numero_nodos);
        }
    }
?>
