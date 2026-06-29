<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Periodos/Periodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/RangosDias/RangoDias.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/AccionRegla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/SucesoRegla.php');


	// Clase que representa una regla
	class Regla
	{
        // Funciones estáticas de regla


        // Devuelve la cabecera para la tabla de reglas
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
				$idiomas->_("Nombre"),
                $idiomas->_("Tipo"),
                $idiomas->_("Modo de activación"),
                $idiomas->_("Habilitada"),
                $idiomas->_("Activada")
			));
        }


        // Devuelve la consulta para la tabla de reglas
        static function dame_consulta_reglas($filtro, $habilitacion, $activacion)
        {
            $consulta = "
                SELECT *
                FROM reglas
                WHERE
                    (red = '".$_SESSION["id_red"]."')";
            if ($filtro != "")
            {
                $campos = array("nombre");
                $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
            }
            switch ($habilitacion)
            {
                case HABILITACION_REGLA_HABILITADA:
                {
                    $consulta .= "
                        AND (habilitada = '".VALOR_SI."')";
                    break;
                }
                case HABILITACION_REGLA_DESHABILITADA:
                {
                    $consulta .= "
                        AND (habilitada = '".VALOR_NO."')";
                    break;
                }
            }
            switch ($activacion)
            {
                case ACTIVACION_REGLA_ACTIVADA:
                {
                    $consulta .= "
                        AND (activaciones > 0)";
                    break;
                }
                case ACTIVACION_REGLA_DESACTIVADA:
                {
                    $consulta .= "
                        AND (activaciones = 0)";
                    break;
                }
            }
            $consulta .= "
                ORDER BY nombre ASC";
            return ($consulta);
        }


        // Devuelve la tabla de reglas
        static function dame_tabla_reglas(
            $filtro,
            $habilitacion,
            $activacion,
            $actualizacion_periodica_activada)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $administracion_reglas = Regla::dame_administracion_reglas();
            if ($administracion_reglas == true)
            {
                $boton_anyadir_regla = "<i id='anyade_modifica_regla' class='icon-plus color-blanco boton_actuadores_mostrar_ventana_anyadir_modificar_regla boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_regla);
            }

            // Nota: Se comprueba la actualización periódica también con la cadena 'false'
            // porque al llamar al script PHP desde JavaScript se convierten los booleanos a cadena
            if (($actualizacion_periodica_activada == false) || ($actualizacion_periodica_activada === "false"))
            {
                $icono_boton_actualizacion_periodica = "icon-play";
            }
            else
            {
                $icono_boton_actualizacion_periodica = "icon-pause";
            }
            $boton_actualizacion_periodica_tabla_reglas = "<i id='boton_actualizacion_periodica_tabla_reglas' class='".$icono_boton_actualizacion_periodica." color-blanco boton-tabla-datos boton_actuadores_actualizacion_periodica_tabla_reglas'></i>";
            array_push($opciones, $boton_actualizacion_periodica_tabla_reglas);
            $boton_actualizar_tabla_reglas = "<i id='actualiza_reglas' class='icon-refresh color-blanco boton_actuadores_actualizar_tabla_reglas boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_reglas);
            $boton_ayuda_tabla_reglas = "<i id='ayuda_reglas' class='icon-question-sign color-blanco boton_actuadores_ayuda_tabla_reglas boton-tabla-datos'></i>";
            array_push($opciones, $boton_ayuda_tabla_reglas);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_REGLAS,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_REGLAS),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "filas_con_opciones" => ($administracion_reglas == true),
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-actuadores-reglas",
                $idiomas->_("Reglas"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = Regla::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las reglas a la tabla y el pie de tabla
            $consulta = Regla::dame_consulta_reglas($filtro, $habilitacion, $activacion);
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }

            // Identificadores de reglas del usuario actual
            $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
            if ($mostrar_todos_actuadores == false)
            {
                $ids_reglas_usuario = Regla::dame_ids_reglas_usuario_actual();
            }

            // Se añaden las reglas
            $numero_reglas = 0;
            while ($fila = $res->dame_siguiente_fila())
            {
                $anyadir_regla = true;
                if ($mostrar_todos_actuadores == false)
                {
                    if (in_array($fila["id"], $ids_reglas_usuario) == false)
                    {
                        $anyadir_regla = false;
                    }
                }

                if ($anyadir_regla == true)
                {
                    $regla = new Regla($fila);
                    $params_fila = array(
                        "opciones" => $regla->dame_opciones_tabla()
                    );
                    $tabla->anyade_fila(
                        "datosRegla__".$fila['id'],
                        $regla->dame_datos_tabla(),
                        $params_fila
                    );
                    $numero_reglas += 1;
                }
            }
            $tabla->anyade_pie($idiomas->_("Reglas").": ".$numero_reglas);

            return ($tabla->dame_tabla());
        }


        // Miembros de regla


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
            $descripcion_tipo = $icono_dato_erroneo;
            $descripcion_modo_activacion = $icono_dato_erroneo;
            $descripcion_habilitada = $icono_dato_erroneo;
            $activada = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Datos de la tabla
                $descripcion_tipo = Regla::dame_descripcion_tipo_regla($this->params["tipo"]);
                $descripcion_modo_activacion = Regla::dame_descripcion_modo_activacion_regla($this->params["modo_activacion"]);
                $descripcion_habilitada = dame_descripcion_valores_si_no($this->params["habilitada"]);
                switch ($this->params["activaciones"])
                {
                    case 0:
                    {
                        $activada = "<i class='icon-circle color-rojo'>".
                            "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Desactivación"), ENT_QUOTES)."</texto></i>";
                        break;
                    }
                    default:
                    {
                        $activada = "<i class='icon-circle color-verde'>".
                            "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Activación"), ENT_QUOTES)."</texto></i>";
                        if ($this->tipo == TIPO_REGLA_MULTIPLE)
                        {
                            $activada .= " (".$this->params["activaciones"].")";
                        }
                        break;
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
                $descripcion_tipo,
                $descripcion_modo_activacion,
                $descripcion_habilitada,
                $activada
			));
		}


        function dame_opciones_tabla()
		{
            $nombre = htmlspecialchars($this->params['nombre'], ENT_QUOTES);

            $opciones = array();
            $administracion_reglas = Regla::dame_administracion_reglas();
            if ($administracion_reglas == true)
            {
                $editar = "<i id='anyade_modifica_regla__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' ".
                    "class='icon-pencil color-gris boton_actuadores_mostrar_ventana_anyadir_modificar_regla boton-tabla-datos'></i>";
                $duplicar = "<i id='anyade_modifica_regla__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' ".
                    "class='icon-copy color-gris boton_actuadores_mostrar_ventana_anyadir_modificar_regla boton-tabla-datos'></i>";
                $borrar = "<i id='elimina_regla__".$this->id."' nombre_regla='".$nombre."' ".
                    "class='icon-remove color-gris boton_actuadores_eliminar_regla boton-tabla-datos'></i>";
                $opciones = array($borrar, $duplicar, $editar);
            }

			return ($opciones);
		}


        function dame_herramientas_detalles_tabla()
		{
            // Herramientas de detalles de regla
            $herramientas = "";
            $herramientas .= "
                <span class='boton-herramientas-detalle-tabla-datos'>
                    <button id='boton_refrescar__".$this->id."' class='btn-mini btn btn-success boton_actuadores_refrescar_tabla_regla'>
                        <i class='icon-refresh color-blanco'></i>
                    </button>
                </span>";

            // Recargar configuración
            $administracion_reglas = Regla::dame_administracion_reglas();
            if ($administracion_reglas == true)
            {
                $herramientas .= "
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button id='boton_recargar_configuracion__".$this->id."' class='btn-mini btn btn-success boton_actuadores_envia_accion_herramientas_regla'>".
                            $this->idiomas->_("Recargar configuración")."
                        </button>
                    </span>";
            }
			return ($herramientas);
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
                $info .= "<br/>";
            }

            // Descripción
            if ($this->params["descripcion"] != "")
			{
				$info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Descripción").": ".htmlspecialchars($this->params["descripcion"], ENT_QUOTES)."<br/>";
			}

            // Número de días de caducidad de acciones
            if ($this->params["numero_dias_caducidad_acciones"] > 0)
			{
				$info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Número de días de caducidad de acciones").": ".$this->params["numero_dias_caducidad_acciones"]."<br/>";
			}
            else
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Sin caducidad de acciones")."<br/>";
            }

            // Tablas de rangos de días y de periodos
            $info .= $this->dame_tabla_rangos_dias();
            $info .= $this->dame_tabla_periodos();

            // Tabla de sucesos
            $info .= "<br/>";
            $info .= $this->dame_tabla_sucesos();

            // Pestañas de tablas de acciones
            $info .= "<br/>";
            $info .= $this->dame_pestanyas_tablas_acciones();

            return ($info);
		}


        //
        // Funciones de parámetros de reglas
        //


        // Devuelve los tipos de regla
        static function dame_tipos_regla()
        {
            $tipos_regla = array();
            array_push($tipos_regla, TIPO_REGLA_UNICA);
            array_push($tipos_regla, TIPO_REGLA_MULTIPLE);
            return ($tipos_regla);
        }


        // Devuelve la descripción del tipo de regla
        static function dame_descripcion_tipo_regla($tipo_regla)
        {
            switch ($tipo_regla)
            {
                case TIPO_REGLA_UNICA:
                {
                    $descripcion = "Única";
                    break;
                }
                case TIPO_REGLA_MULTIPLE:
                {
                    $descripcion = "Múltiple";
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


        // Devuelve los modos de activación de regla
        static function dame_modos_activacion_regla()
        {
            $modos_activacion_regla = array();
            array_push($modos_activacion_regla, MODO_ACTIVACION_REGLA_CUALQUIER_SUCESO);
            array_push($modos_activacion_regla, MODO_ACTIVACION_REGLA_TODOS_SUCESOS);
            return ($modos_activacion_regla);
        }


        // Devuelve la descripción del modo de activación de regla
        static function dame_descripcion_modo_activacion_regla($modo_activacion_regla)
        {
            switch ($modo_activacion_regla)
            {
                case MODO_ACTIVACION_REGLA_CUALQUIER_SUCESO:
                {
                    $descripcion = "Cualquier suceso";
                    break;
                }
                case MODO_ACTIVACION_REGLA_TODOS_SUCESOS:
                {
                    $descripcion = "Todos los sucesos";
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
        // Funciones auxiliares
        //


        static function dame_administracion_reglas()
        {
            $administracion_reglas = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_actuadores"]["administracion_actuadores"] == VALOR_SI) ||
                ($_SESSION["parametros_modulo_actuadores"]["administracion_reglas"] == VALOR_SI);
            return ($administracion_reglas);
        }


        static function dame_ids_reglas_usuario_actual()
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Ids de actuador y grupos de actuadores el usuario
            $ids_actuadores_usuario = dame_ids_actuadores_usuario_actual(true);
            $ids_grupos_actuadores_usuario = dame_ids_grupos_actuadores_usuario_actual(true);

            // Identificadores de reglas
            $ids_reglas = array();

            // Se recuperan los identificadores de las reglas de la red actual
            $consulta_reglas = "
                SELECT id
                FROM reglas
                WHERE
                    red = '".$_SESSION["id_red"]."'
                ORDER BY nombre ASC";
            $res_reglas = $bd_red->ejecuta_consulta($consulta_reglas);
            if ($res_reglas == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_reglas."'");
            }

            // Se recorren todas las reglas y se añaden a las reglas del usuario actual si corresponde
            $cadena_ids_actuadores_consulta = dame_cadena_ids_consulta($ids_actuadores_usuario);
            $cadena_ids_grupos_actuadores_consulta = dame_cadena_ids_consulta($ids_grupos_actuadores_usuario);
            while ($fila_regla = $res_reglas->dame_siguiente_fila())
            {
                $id = $fila_regla["id"];

                $anyadir_regla = true;
                $consulta_acciones_reglas = "
                    SELECT
                        COUNT(*) AS numero_acciones
                    FROM acciones_reglas
                    WHERE
                        (regla = ".$bd_red->_($id).")
                        AND (((destino = '".DESTINO_ACCION_ACTUADOR."') AND (id_destino IN (".$cadena_ids_actuadores_consulta.")))
                            OR ((destino = '".DESTINO_ACCION_GRUPO_ACTUADORES."') AND (id_destino IN (".$cadena_ids_grupos_actuadores_consulta."))))";
                $res_acciones_reglas = $bd_red->ejecuta_consulta($consulta_acciones_reglas);
                if (($res_acciones_reglas == false) || ($res_acciones_reglas->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_acciones_reglas."'");
                }
                $fila_acciones_reglas = $res_acciones_reglas->dame_siguiente_fila();
                if ($fila_acciones_reglas["numero_acciones"] == 0)
                {
                    // Si una regla no tiene acciones, es visible para todos los usuarios
                    $consulta_acciones_reglas = "
                        SELECT
                            COUNT(*) AS numero_acciones
                        FROM acciones_reglas
                        WHERE
                            regla = ".$bd_red->_($id)."";
                    $res_acciones_reglas = $bd_red->ejecuta_consulta($consulta_acciones_reglas);
                    if (($res_acciones_reglas == false) || ($res_acciones_reglas->dame_numero_filas() == 0))
                    {
                        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_acciones_reglas."'");
                    }
                    $fila_acciones_reglas = $res_acciones_reglas->dame_siguiente_fila();
                    if ($fila_acciones_reglas["numero_acciones"] == 0)
                    {
                        $anyadir_regla = true;
                    }
                    else
                    {
                        $anyadir_regla = false;
                    }
                }

                if ($anyadir_regla == true)
                {
                    array_push($ids_reglas, $id);
                }
            }

            return ($ids_reglas);
        }


        function dame_tabla_rangos_dias()
        {
            $tabla_rangos_dias = "";
            $administracion_reglas = Regla::dame_administracion_reglas();
            if (($administracion_reglas == true) || (RangoDias::dame_numero_rangos_dias(ORIGEN_RANGOS_DIAS_REGLA, $this->id) > 0))
            {
                $id_elemento_rangos_dias_regla = "rangos-dias-".ORIGEN_RANGOS_DIAS_REGLA.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
                $tabla_rangos_dias .= "<br/><div id='".$id_elemento_rangos_dias_regla."' class='contenedor-detalle-tabla-datos'>".
                    RangoDias::dame_tabla_rangos_dias(ORIGEN_RANGOS_DIAS_REGLA, $this->id)."</div>";
            }
            return ($tabla_rangos_dias);
        }


        function dame_tabla_periodos()
        {
            $tabla_periodos = "";
            $administracion_reglas = Regla::dame_administracion_reglas();
            if (($administracion_reglas == true) || (Periodo::dame_numero_periodos(ORIGEN_PERIODOS_REGLA, $this->id) > 0))
            {
                $id_elemento_periodos_regla = "periodos-".ORIGEN_PERIODOS_REGLA.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
                $tabla_periodos .= "<br/><div id='".$id_elemento_periodos_regla."' class='contenedor-detalle-tabla-datos'>".
                    Periodo::dame_tabla_periodos(ORIGEN_PERIODOS_REGLA, $this->id)."</div>";
            }
            return ($tabla_periodos);
        }


        function dame_tabla_sucesos()
        {
            $id_elemento_sucesos_regla = "sucesos-regla".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
            $tabla_sucesos = "<div id='".$id_elemento_sucesos_regla."' class='contenedor-detalle-tabla-datos'>".
                SucesoRegla::dame_tabla_sucesos($this->id)."</div>";
            return ($tabla_sucesos);
        }


        function dame_pestanyas_tablas_acciones()
        {
            $id_elemento_pestanyas_acciones_regla = "pestanyas_acciones_".$this->id;
            $pestanyas_tablas_acciones = "";

            $pestanyas_tablas_acciones = "
                <div id='".$id_elemento_pestanyas_acciones_regla."' class='tabbable pestanyas-embebidas'>";
			$pestanyas_tablas_acciones .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-acciones-activacion-regla-".$this->id."'>".$this->idiomas->_("Acciones de activación")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-acciones-desactivacion-regla-".$this->id."'>".$this->idiomas->_("Acciones de desactivación")."</a></li>
                    </ul>
                    <div id='tabs-acciones-regla' class='tab-content'>";

			$pestanyas_tablas_acciones .= "
                        <div class='tab-pane active' id='tab-acciones-activacion-regla-".$this->id."'>
                            <div id='acciones-regla-".TIPO_ACCION_ACTIVACION.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id."'>".
                                AccionRegla::dame_tabla_acciones($this->id, TIPO_ACCION_ACTIVACION)."
                            </div>
                        </div>";

            $pestanyas_tablas_acciones .= "
                        <div class='tab-pane' id='tab-acciones-desactivacion-regla-".$this->id."'>
                            <div id='acciones-regla-".TIPO_ACCION_DESACTIVACION.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id."'>".
                                AccionRegla::dame_tabla_acciones($this->id, TIPO_ACCION_DESACTIVACION)."
                            </div>
                        </div>";

            $pestanyas_tablas_acciones .= "
					</div>
				</div>";

			return ($pestanyas_tablas_acciones);
        }
	}
?>
