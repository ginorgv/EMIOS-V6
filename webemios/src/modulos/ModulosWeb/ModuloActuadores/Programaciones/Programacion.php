<?php
	session_start();

	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


	// Clase que representa un conjunto de acciones programadas
	class Programacion
	{
        // Funciones estáticas de programación


        // Devuelve la cabecera para la tabla de programaciones
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
				$idiomas->_("Nombre"),
				$idiomas->_("Clase de actuador"),
			));
        }


        // Devuelve la consulta para la tabla de programaciones
        static function dame_consulta_programaciones($filtro, $clase_actuador)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta = "
                SELECT *
                FROM programaciones
                WHERE
                    red = '".$_SESSION["id_red"]."'";
            if ($filtro != "")
            {
                $campos = array("nombre");
                $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
            }
            if ($clase_actuador != CLASE_TODAS)
            {
                $consulta .= "
                    AND (clase = '".$bd_red->_($clase_actuador)."')";
            }
            $consulta .= "
                ORDER BY nombre ASC";
            return ($consulta);
        }


        // Devuelve la tabla de programaciones
        static function dame_tabla_programaciones($filtro, $clase_actuador)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $administracion_programaciones = Programacion::dame_administracion_programaciones();
            if ($administracion_programaciones == true)
            {
                $boton_anyadir_programacion = "<i id='anyade_modifica_programacion' class='icon-plus color-blanco boton_actuadores_mostrar_ventana_anyadir_modificar_programacion boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_programacion);
            }
            $boton_actualizar_tabla_programaciones = "<i id='actualiza_programaciones' class='icon-refresh color-blanco boton_actuadores_actualizar_tabla_programaciones boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_programaciones);
            $boton_ayuda_tabla_programaciones = "<i id='ayuda_programaciones' class='icon-question-sign color-blanco boton_actuadores_ayuda_tabla_programaciones boton-tabla-datos'></i>";
            array_push($opciones, $boton_ayuda_tabla_programaciones);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_PROGRAMACIONES,
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-actuadores-programaciones",
                $idiomas->_("Programaciones"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = Programacion::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las programaciones a la tabla y el pie de tabla
            $consulta = Programacion::dame_consulta_programaciones($filtro, $clase_actuador);
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }

            // Identificadores de programaciones del usuario actual
            $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
            if ($mostrar_todos_actuadores == false)
            {
                $ids_programaciones_usuario = Programacion::dame_ids_programaciones_usuario_actual();
            }

            // Se añaden las programaciones
            $numero_programaciones = 0;
            while ($fila = $res->dame_siguiente_fila())
            {
                $programacion = new Programacion($fila);

                $anyadir_programacion = true;
                if ($mostrar_todos_actuadores == false)
                {
                    if (in_array($programacion->id, $ids_programaciones_usuario) == false)
                    {
                        $anyadir_programacion = false;
                    }
                }

                if ($anyadir_programacion == true)
                {
                    $params_fila = array(
                        "opciones" => $programacion->dame_opciones_tabla()
                    );
                    $tabla->anyade_fila(
                        "datosProgramacion__".$fila['id'],
                        $programacion->dame_datos_tabla(),
                        $params_fila
                    );
                    $numero_programaciones += 1;
                }
            }
            $tabla->anyade_pie($idiomas->_("Programaciones").": ".$numero_programaciones);

            return ($tabla->dame_tabla());
        }


        // Miembros de programación


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
            $nombre_clase_actuador = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Nombre de clase de actuador
                $nombre_clase_actuador = NodoActuador::dame_descripcion_clase_actuador($this->params["clase"]);
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
				$nombre_clase_actuador,
			));
        }


        function dame_opciones_tabla()
		{
            $id = $this->id;
            $nombre = htmlspecialchars($this->params['nombre'], ENT_QUOTES);

            $opciones = array();
            $administracion_programaciones = Programacion::dame_administracion_programaciones();
            if ($administracion_programaciones == true)
            {
                $editar = "<i id='anyade_modifica_programacion__".$id."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' ".
                    "class='icon-pencil color-gris boton_actuadores_mostrar_ventana_anyadir_modificar_programacion boton-tabla-datos'></i>";
                $duplicar = "<i id='anyade_modifica_programacion__".$id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' ".
                    "class='icon-copy color-gris boton_actuadores_mostrar_ventana_anyadir_modificar_programacion boton-tabla-datos'></i>";
                $borrar = "<i id='elimina_programacion__".$id."' nombre_programacion='".$nombre."' ".
                    "class='icon-remove color-gris boton_actuadores_eliminar_programacion boton-tabla-datos'></i>";
                $opciones = array($borrar, $duplicar, $editar);
            }
			return ($opciones);
		}


        function dame_detalles_tabla()
		{
			$bd_red = BaseDatosRed::dame_base_datos();

            $info = "";

            $administracion_programaciones = Programacion::dame_administracion_programaciones();
            if ($administracion_programaciones == true)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Identificador").": ".$this->id."<br/>";
                $info .= "<br/>";
            }

            // Actuadores
            $consulta_actuadores = "
				SELECT
					nombre
				FROM actuadores
				WHERE
					programacion = '".$bd_red->_($this->id)."'";
			$res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
            if ($res_actuadores == false)
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_actuadores."'");
            }
            $numero_actuadores = $res_actuadores->dame_numero_filas();
            if ($numero_actuadores > 0)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ";
                if ($numero_actuadores == 1)
                {
                    $info .= $this->idiomas->_("Esta programación está asignada a")." ".$numero_actuadores." ".$this->idiomas->_("actuador").":";
                }
                else
                {
                    $info .= $this->idiomas->_("Esta programación está asignada a")." ".$numero_actuadores." ".$this->idiomas->_("actuadores").":";
                }
                $nombres_actuadores = "<ul>";
                while ($fila_actuador = $res_actuadores->dame_siguiente_fila())
                {
                    $nombres_actuadores .= "<li>".htmlspecialchars($fila_actuador['nombre'], ENT_QUOTES)."</li>";
                }
                $nombres_actuadores .= "</ul>";
                $info .= $nombres_actuadores;
            }

            // Grupos de actuadores
            $consulta_grupos = "
				SELECT
					nombre
				FROM grupos_actuadores
				WHERE
					programacion = '".$bd_red->_($this->id)."'";
			$res_grupos = $bd_red->ejecuta_consulta($consulta_grupos);
            if ($res_grupos == false)
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_grupos."'");
            }
            $numero_grupos = $res_grupos->dame_numero_filas();
            if ($numero_grupos > 0)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ";
                if ($numero_grupos == 1)
                {
                    $info .= $this->idiomas->_("Esta programación está asignada a")." ".$numero_grupos." ".$this->idiomas->_("grupo de actuadores").":";
                }
                else
                {
                    $info .= $this->idiomas->_("Esta programación está asignada a")." ".$numero_grupos." ".$this->idiomas->_("grupos de actuadores").":";
                }
                $nombres_grupos = "<ul>";
                while ($fila_grupo = $res_grupos->dame_siguiente_fila())
                {
                    $nombres_grupos .= "<li>".htmlspecialchars($fila_grupo['nombre'], ENT_QUOTES)."</li>";
                }
                $nombres_grupos .= "</ul>";
                $info .= $nombres_grupos;
            }

            if (($numero_actuadores == 0) && ($numero_grupos == 0))
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ";
                $info .= $this->idiomas->_("Esta programación no está asignada a ningún actuador o grupo de actuadores");
                $info .= "<br/>";
            }
            $info .= "<br/>";

            // Tabla de acciones
            $id_elemento_acciones_programacion = 'acciones-programacion'.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
            $info .= "<div id='".$id_elemento_acciones_programacion."' class='contenedor-detalle-tabla-datos'>".
                $this->dame_tabla_acciones()."</div>";

            // Tabla de excepciones
            $numero_excepciones_programacion = 0;
            $tabla_excepciones_programacion = $this->dame_tabla_excepciones($numero_excepciones_programacion);
            if (($administracion_programaciones == true) || ($numero_excepciones_programacion > 0))
            {
                $id_elemento_excepciones_programacion = 'excepciones-programacion'.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
                $info .= "<br/>";
                $info .= "<div id='".$id_elemento_excepciones_programacion."' class='contenedor-detalle-tabla-datos'>".
                    $tabla_excepciones_programacion."</div>";
            }

            return ($info);
		}


        //
        // Funciones auxiliares
        //


        static function dame_administracion_programaciones()
        {
            $administracion_programaciones = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_actuadores"]["administracion_actuadores"] == VALOR_SI) ||
                ($_SESSION["parametros_modulo_actuadores"]["administracion_programaciones"] == VALOR_SI);
            return ($administracion_programaciones);
        }


        static function dame_ids_programaciones_usuario_actual()
        {
            $ids_actuadores_usuario = dame_ids_actuadores_usuario_actual(true);
            $ids_grupos_actuadores_usuario = dame_ids_grupos_actuadores_usuario_actual(true);

            // Identificadores de programaciones
            $ids_programaciones = array();

            $bd_red = BaseDatosRed::dame_base_datos();

            // Se recuperan los identificadores de las programaciones de los actuadores
            $cadena_ids_actuadores_consulta = dame_cadena_ids_consulta($ids_actuadores_usuario);
            $consulta_actuadores = "
                SELECT
                    DISTINCT(programacion)
                FROM actuadores
                WHERE
                    (programacion <> ".ID_NINGUNO.")
                    AND (id IN (".$cadena_ids_actuadores_consulta."))";
            $res_actuadores = $bd_red->ejecuta_consulta($consulta_actuadores);
            if ($res_actuadores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_actuadores."'");
            }
            while ($fila_actuador = $res_actuadores->dame_siguiente_fila())
            {
                if (in_array($fila_actuador["programacion"], $ids_programaciones) == false)
                {
                    array_push($ids_programaciones, $fila_actuador["programacion"]);
                }
            }

            // Se recuperan los identificadores de las programaciones de los grupos de actuadores
            $cadena_ids_grupos_actuadores_consulta = dame_cadena_ids_consulta($ids_grupos_actuadores_usuario);
            $consulta_grupos_actuadores = "
                SELECT
                    DISTINCT(programacion)
                FROM grupos_actuadores
                WHERE
                    (programacion <> ".ID_NINGUNO.")
                    AND (id IN (".$cadena_ids_grupos_actuadores_consulta."))";
            $res_grupos_actuadores = $bd_red->ejecuta_consulta($consulta_grupos_actuadores);
            if ($res_grupos_actuadores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_grupos_actuadores."'");
            }
            while ($fila_grupo_actuadores = $res_grupos_actuadores->dame_siguiente_fila())
            {
                if (in_array($fila_grupo_actuadores["programacion"], $ids_programaciones) == false)
                {
                    array_push($ids_programaciones, $fila_grupo_actuadores["programacion"]);
                }
            }

            // Se recuperan los identificadores de las programaciones que no están asignadas a ningún actuador o grupo
            // (si no se tiene administración de programaciones sólo se pueden ver las programaciones de las clases de los
            //  actuadores del usuario actual)
            $consulta_programaciones = "
                SELECT
                    id
                FROM programaciones
                WHERE
                    (id NOT IN (SELECT programacion FROM actuadores))
                    AND (id NOT IN (SELECT programacion FROM grupos_actuadores))";
            $administracion_programaciones = Programacion::dame_administracion_programaciones();
            if ($administracion_programaciones == false)
            {
                $clases_actuador = dame_clases_actuador_usuario_actual(true);
                $cadena_nombres_clases_consulta = dame_cadena_nombres_consulta($clases_actuador, $bd_red);
                $consulta_programaciones .= "
                    AND (clase IN (".$cadena_nombres_clases_consulta."))";
            }
            $res_programaciones = $bd_red->ejecuta_consulta($consulta_programaciones);
            if ($res_programaciones == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_programaciones."'");
            }
            while ($fila_programacion = $res_programaciones->dame_siguiente_fila())
            {
                array_push($ids_programaciones, $fila_programacion["id"]);
            }

            return ($ids_programaciones);
        }


        function dame_tabla_acciones()
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $administracion_programaciones = Programacion::dame_administracion_programaciones();
            if ($administracion_programaciones == true)
            {
                $boton_anyadir_accion = "<i id='anyade_modifica_accion_programacion__".$this->id."' class='icon-plus color-blanco boton_actuadores_mostrar_ventana_anyadir_modificar_accion_programacion boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_accion);
            }
            $boton_actualizar_tabla_acciones = "<i id='actualiza_tabla_acciones_programacion__".$this->id."' class='icon-refresh color-blanco boton_actuadores_actualizar_tabla_acciones_programacion boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_acciones);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_ACCIONES_PROGRAMACIONES,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_ACCIONES_PROGRAMACIONES),
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-acciones-programacion",
                $this->idiomas->_("Acciones"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = array(
                $this->idiomas->_("Días"),
                $this->idiomas->_("Hora"),
                $this->idiomas->_("Nombre"),
                $this->idiomas->_("Estado"));
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las acciones a la tabla y el pie de tabla
            $consulta = "
                SELECT
                    acciones_programaciones.id AS id,
                    acciones_programaciones.dias_semana AS dias_semana,
                    TIME_FORMAT(acciones_programaciones.hora, '%H:%i') AS hora,
                    acciones_programaciones.nombre AS nombre,
                    acciones_programaciones.contenido AS contenido,
                    programaciones.clase AS clase
                FROM
                    acciones_programaciones,
                    programaciones
                WHERE
                    (acciones_programaciones.programacion = '".$bd_red->_($this->id)."')
                    AND (programaciones.id ='".$bd_red->_($this->id)."')
                ORDER BY acciones_programaciones.dias_semana, acciones_programaciones.hora";
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_acciones = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                // Los nombres de las acciones predefinidas se traducen (el resto no)
                switch ($fila['clase'])
                {
                    case CLASE_ACTUADOR_INTERRUPTOR:
                    case CLASE_ACTUADOR_TELEPOSTE:
                    case CLASE_ACTUADOR_LUZ_GRADUAL_4:
                    {
                        $nombre_accion = $this->idiomas->_($fila['nombre']);
                        break;
                    }
                    default:
                    {
                        $nombre_accion = $fila['nombre'];
                        break;
                    }
                }
                $cadena_dias_semana = $fila['dias_semana'];
                $dias_semana = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_dias_semana);
                if (count($dias_semana) == 1)
                {
                    $nombres_dias_semana = dame_nombre_dia_semana($dias_semana[0]);
                }
                else
                {
                    $nombres_dias_semana = dame_abreviaturas_nombres_dias_semana($dias_semana);
                }
                $datos = array(
                    $nombres_dias_semana,
                    $fila['hora'],
                    $nombre_accion,
                    NodoActuador::dame_imagen_accion_clase($fila['clase'], $fila['contenido']),
                );

                $opciones = array();
                if ($administracion_programaciones == true)
                {
                    $editar = "<i id='anyade_modifica_accion_programacion__".$this->id."__".$fila['id']."' class='icon-pencil color-gris boton_actuadores_mostrar_ventana_anyadir_modificar_accion_programacion boton-tabla-datos'></i>";
                    $borrar = "<i id='elimina_accion_programacion__".$this->id."__".$fila['id']."' class='icon-remove color-gris boton_actuadores_eliminar_accion_programacion boton-tabla-datos'></i>";
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
            $tabla->anyade_pie($this->idiomas->_("Acciones de la programación").": ".$numero_acciones);

            return ($tabla->dame_tabla(false));
		}


        function dame_tabla_excepciones(&$numero_excepciones)
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $administracion_programaciones = Programacion::dame_administracion_programaciones();
            if ($administracion_programaciones == true)
            {
                $boton_anyadir_excepcion = "<i id='anyade_modifica_excepcion_programacion__".$this->id."' class='icon-plus color-blanco boton_actuadores_mostrar_ventana_anyadir_modificar_excepcion_programacion boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_excepcion);
            }
            $boton_actualizar_tabla_excepciones = "<i id='actualiza_tabla_excepciones_programacion__".$this->id."' class='icon-refresh color-blanco boton_actuadores_actualizar_tabla_excepciones_programacion boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_excepciones);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_EXCEPCIONES_PROGRAMACIONES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-excepciones-programacion",
                $this->idiomas->_("Excepciones"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = array(
                $this->idiomas->_("Nombre"),
                $this->idiomas->_("Tipo"),
                $this->idiomas->_("Fechas")." / ".$this->idiomas->_("días"));

            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las excepciones a la tabla y el pie de tabla
            $consulta = "
                SELECT
                    excepciones_programaciones.id AS id,
                    excepciones_programaciones.nombre AS nombre,
                    excepciones_programaciones.tipo AS tipo,
                    excepciones_programaciones.fecha AS fecha,
                    excepciones_programaciones.fecha_inicio AS fecha_inicio,
                    excepciones_programaciones.fecha_fin AS fecha_fin,
                    excepciones_programaciones.dia_anyo AS dia_anyo,
                    excepciones_programaciones.dia_anyo_inicio AS dia_anyo_inicio,
                    excepciones_programaciones.dia_anyo_fin AS dia_anyo_fin,
                    excepciones_programaciones.dia_semana AS dia_semana
                FROM
                    excepciones_programaciones,
                    programaciones
                WHERE
                    (excepciones_programaciones.programacion = '".$bd_red->_($this->id)."')
                    AND (programaciones.id ='".$bd_red->_($this->id)."')
                ORDER BY
                    FIELD(excepciones_programaciones.tipo, '".TIPO_EXCEPCION_PROGRAMACION_FECHA."', '".TIPO_EXCEPCION_PROGRAMACION_DIA_ANYO."', '".TIPO_EXCEPCION_PROGRAMACION_DIA_SEMANA."'),
                    fecha, dia_anyo, dia_semana";
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_excepciones = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                switch ($fila['tipo'])
                {
                    case TIPO_EXCEPCION_PROGRAMACION_FECHA:
                    {
                        $tipo_excepcion = $this->idiomas->_("Fecha");
                        $cadena_fechas_dias = convierte_formato_fecha($fila['fecha'], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
                        break;
                    }
                    case TIPO_EXCEPCION_PROGRAMACION_RANGO_FECHAS:
                    {
                        $tipo_excepcion = $this->idiomas->_("Rango de fechas");
                        $cadena_fechas_dias = convierte_formato_fecha($fila['fecha_inicio'], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"])." - ".
                            convierte_formato_fecha($fila['fecha_fin'], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
                        break;
                    }
                    case TIPO_EXCEPCION_PROGRAMACION_DIA_ANYO:
                    {
                        $tipo_excepcion = $this->idiomas->_("Día anual");
                        $cadena_fechas_dias = convierte_formato_dia_anyo($fila['dia_anyo'], FORMATO_DIA_ANYO_BASE_DATOS, $_SESSION["formato_dia_anyo_local"]);
                        break;
                    }
                    case TIPO_EXCEPCION_PROGRAMACION_RANGO_DIAS_ANYO:
                    {
                        $tipo_excepcion = $this->idiomas->_("Rango de días anuales");
                        $cadena_fechas_dias = convierte_formato_dia_anyo($fila['dia_anyo_inicio'], FORMATO_DIA_ANYO_BASE_DATOS, $_SESSION["formato_dia_anyo_local"])." - ".
                            convierte_formato_dia_anyo($fila['dia_anyo_fin'], FORMATO_DIA_ANYO_BASE_DATOS, $_SESSION["formato_dia_anyo_local"]);
                        break;
                    }
                    case TIPO_EXCEPCION_PROGRAMACION_DIA_SEMANA:
                    {
                        $tipo_excepcion = $this->idiomas->_("Día de semana");
                        $cadena_fechas_dias = dame_nombre_dia_semana($fila['dia_semana']);
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de excepción de programación desconocido: '".$tipo_excepcion."'");
                    }
                }
                $datos = array(
                    $fila['nombre'],
                    $tipo_excepcion,
                    $cadena_fechas_dias
                );

                $opciones = array();
                if ($administracion_programaciones == true)
                {
                    $editar = "<i id='anyade_modifica_excepcion_programacion__".$this->id."__".$fila['id']."' class='icon-pencil color-gris boton_actuadores_mostrar_ventana_anyadir_modificar_excepcion_programacion boton-tabla-datos'></i>";
                    $borrar = "<i id='elimina_excepcion_programacion__".$this->id."__".$fila['id']."' class='icon-remove color-gris boton_actuadores_eliminar_excepcion_programacion boton-tabla-datos'></i>";
                    $opciones = array($borrar, $editar);
                }
                $params_fila = array(
                    "opciones" => $opciones
                );
                $tabla->anyade_fila(
                    "datosExcepcionProgramacion__".$this->id."__".$fila['id'],
                    $datos,
                    $params_fila
                );
            }
            $tabla->anyade_pie($this->idiomas->_("Excepciones de la programación").": ".$numero_excepciones);

            return ($tabla->dame_tabla(false));
		}
	}
?>
