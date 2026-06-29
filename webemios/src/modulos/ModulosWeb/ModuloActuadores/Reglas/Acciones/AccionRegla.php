<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Regla.php');


    // Clase que representa una acción a ejecutar en la activacion/desactivación de reglas
	class AccionRegla
	{
        // Funciones estáticas de acción


        // Devuelve la cabecera para la tabla de acciones
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
				$idiomas->_("Nombre"),
                $idiomas->_("Causa"),
                $idiomas->_("Destino")
			));
        }


        // Devuelve la consulta para la tabla de acciones
        static function dame_consulta_acciones($id_regla, $tipo)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta = "
                SELECT *
                FROM acciones_reglas
                WHERE
                    (regla = '".$bd_red->_($id_regla)."')";
            if ($tipo != ID_NINGUNO)
            {
                $consulta .= "
                    AND (tipo = '".$bd_red->_($tipo)."')";
            }
            $consulta .= "
                ORDER BY nombre ASC";
            return ($consulta);
        }


        // Devuelve la tabla de acciones
        static function dame_tabla_acciones($id_regla, $tipo)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $administracion_reglas = Regla::dame_administracion_reglas();
            if ($administracion_reglas == true)
            {
                $boton_anyadir_accion = "<i id='anyade_modifica_accion__".$id_regla."__".$tipo."' class='icon-plus color-blanco boton_actuadores_mostrar_ventana_anyadir_modificar_accion_regla boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_accion);
            }
            $boton_actualizar_tabla_acciones = "<i id='actualiza_tabla_acciones__".$id_regla."__".$tipo."' class='icon-refresh color-blanco boton_actuadores_actualizar_tabla_acciones_regla boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_acciones);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_ACCIONES_REGLAS,
                "generar_valores_xml" => true
            );
            if ($administracion_reglas == true)
            {
                $params_tabla["tipo_fila"] = TIPO_FILA_TABLA_DATOS_DETALLES;
            }
            switch ($tipo)
            {
                case TIPO_ACCION_ACTIVACION:
                {
                    $titulo_tabla = $idiomas->_("Acciones de activación");
                    break;
                }
                case TIPO_ACCION_DESACTIVACION:
                {
                    $titulo_tabla = $idiomas->_("Acciones de desactivación");
                    break;
                }
            }
            $tabla = new TablaDatos(
                "tabla-acciones-reglas-".$tipo,
                $titulo_tabla,
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = AccionRegla::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las acciones a la tabla y el pie de tabla
            $consulta = AccionRegla::dame_consulta_acciones($id_regla, $tipo);
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_acciones = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $accion = new AccionRegla($fila);
                $params_fila = array(
                    "opciones" => $accion->dame_opciones_tabla(),
                );
                $tabla->anyade_fila(
                    "datosAccionRegla__".$id_regla."__".$tipo."__".$fila['id'],
                    $accion->dame_datos_tabla(),
                    $params_fila
                );
            }
            $tabla->anyade_pie($idiomas->_("Acciones").": ".$numero_acciones);

            return ($tabla->dame_tabla(false));
        }


        // Miembros de acción


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
            $causa = $icono_dato_erroneo;
            $destino = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Causa de ejecución de la acción
                $causa = AccionRegla::dame_descripcion_causa_ejecucion_accion($this->params["causa"]);

                // En la columna 'destino' se muestra el nombre del actuador/grupo y el tipo (actuador/grupo)
                switch ($this->params["destino"])
                {
                    case DESTINO_ACCION_ACTUADOR:
                    {
                        $fila_actuador = dame_fila_actuador($this->params["id_destino"]);
                        $destino = $fila_actuador["nombre"]." (".$this->idiomas->_("actuador").")";
                        break;
                    }
                    case DESTINO_ACCION_GRUPO_ACTUADORES:
                    {
                        $fila_grupo = dame_fila_grupo_actuadores($this->params["id_destino"]);
                        $destino = $fila_grupo["nombre"]." (".$this->idiomas->_("grupo").")";
                        break;
                    }
                    default:
                    {
                        throw new Exception("Destino de acción desconocido: '".$this->params["destino"]."'");
                    }
                }
                $destino = htmlspecialchars($destino, ENT_QUOTES);
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
                $causa,
                $destino
            ));
		}


        function dame_opciones_tabla()
		{
            $nombre = htmlspecialchars($this->params['nombre'], ENT_QUOTES);

            $opciones = array();
            $administracion_reglas = Regla::dame_administracion_reglas();
            if ($administracion_reglas == true)
            {
                $botones_administracion_habilitados = $this->dame_administracion_accion_usuario_actual();
                if ($botones_administracion_habilitados == true)
                {
                    $editar = "<i id='anyade_modifica_accion__".$this->params["regla"]."__".$this->params["tipo"]."__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' ".
                        "class='icon-pencil color-gris boton_actuadores_mostrar_ventana_anyadir_modificar_accion_regla boton-tabla-datos'></i>";
                    $duplicar = "<i id='anyade_modifica_accion__".$this->params["regla"]."__".$this->params["tipo"]."__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' ".
                        "class='icon-copy color-gris boton_actuadores_mostrar_ventana_anyadir_modificar_accion_regla boton-tabla-datos'></i>";
                    $borrar = "<i id='elimina_accion__".$this->params["regla"]."__".$this->params["tipo"]."__".$this->id."' nombre_accion='".$nombre."' ".
                        "class='icon-remove color-gris boton_actuadores_eliminar_accion_regla boton-tabla-datos'></i>";
                }
                else
                {
                    $editar = "<i class='icon-pencil color-gris-muy-claro'></i>";
                    $duplicar = "<i class='icon-copy color-gris-muy-claro'></i>";
                    $borrar = "<i class='icon-remove color-gris-muy-claro'></i>";
                }
                $opciones = array($borrar, $duplicar, $editar);
            }

			return ($opciones);
		}


        function dame_detalles_tabla()
		{
            $info = "";
            $administracion_reglas = Regla::dame_administracion_reglas();

            // Identificador
            if ($administracion_reglas == true)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Identificador").": ".$this->id."<br/>";
            }

            return ($info);
		}


        //
        // Funciones de parámetros de acciones
        //


        // Devuelve la descripción del tipo de acción
        static function dame_descripcion_tipo_accion($tipo_accion)
        {
            switch ($tipo_accion)
            {
                case TIPO_ACCION_ACTIVACION:
                {
                    $descripcion = "Activación";
                    break;
                }
                case TIPO_ACCION_DESACTIVACION:
                {
                    $descripcion = "Desactivación";
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


        // Devuelve las causas de ejecución de la acción
        static function dame_causas_ejecucion_accion()
        {
            $causas_ejecucion_accion = array();
            array_push($causas_ejecucion_accion, CAUSA_EJECUCION_ACCION_TODAS);
            array_push($causas_ejecucion_accion, CAUSA_EJECUCION_ACCION_SUCESO);
            array_push($causas_ejecucion_accion, CAUSA_EJECUCION_ACCION_NO_SUCESO);
            return ($causas_ejecucion_accion);
        }


        // Devuelve la descripción de la causa de ejecución de la acción
        static function dame_descripcion_causa_ejecucion_accion($causa_ejecucion_accion)
        {
            switch ($causa_ejecucion_accion)
            {
                case CAUSA_EJECUCION_ACCION_TODAS:
                {
                    $descripcion = "Todas";
                    break;
                }
                case CAUSA_EJECUCION_ACCION_SUCESO:
                {
                    $descripcion = "Suceso";
                    break;
                }
                case CAUSA_EJECUCION_ACCION_NO_SUCESO:
                {
                    $descripcion = "No suceso";
                    break;
                }
                default:
                {
                    $descripcion = "Desconocida";
                    break;
                }
            }

            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion));
        }


        //
        // Funciones auxiliares
        //


        function dame_administracion_accion_usuario_actual()
        {
            $administracion_accion = true;
            $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
            if ($mostrar_todos_actuadores == false)
            {
                switch ($this->params["destino"])
                {
                    case DESTINO_ACCION_ACTUADOR:
                    {
                        $ids_actuadores_usuario = dame_ids_actuadores_usuario_actual(true);
                        if (in_array($this->params["id_destino"], $ids_actuadores_usuario) == false)
                        {
                            $administracion_accion = false;
                        }
                        break;
                    }
                    case DESTINO_ACCION_GRUPO_ACTUADORES:
                    {
                        $ids_grupos_actuadores_usuario = dame_ids_grupos_actuadores_usuario_actual(false);
                        if (in_array($this->params["id_destino"], $ids_grupos_actuadores_usuario) == false)
                        {
                            $administracion_accion = false;
                        }
                        break;
                    }
                }
            }
            return ($administracion_accion);
        }


        static function dame_nombre_destino_accion_regla($destino_accion, $id_destino_accion)
        {
            $idiomas = new Idiomas();

            switch ($destino_accion)
            {
                case DESTINO_ACCION_ACTUADOR:
                {
                    $nombre_destino = dame_nombre_actuador($id_destino_accion);
                    break;
                }
                case DESTINO_ACCION_GRUPO_ACTUADORES:
                {
                    $nombre_destino = dame_nombre_grupo_actuadores($id_destino_accion);
                    break;
                }
                default:
                {
                    $nombre_destino = $idiomas->_("Ninguno");
                    break;
                }
            }
            return ($nombre_destino);
        }
    }
?>
