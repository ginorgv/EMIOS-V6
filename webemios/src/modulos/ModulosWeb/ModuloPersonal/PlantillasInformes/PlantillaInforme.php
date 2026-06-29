<?php
	session_start();

	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/ElementoPlantillaInforme.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/ParametroPlantillaInforme.php');


	// Clase que representa una plantilla de informe (con elementos personalizados)
	class PlantillaInforme
	{
        // Funciones estáticas de plantilla de informe


        // Devuelve la cabecera para la tabla de plantillas de informes
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            $cabecera_tabla = array(
                $idiomas->_("Nombre"),
                $idiomas->_("Tipo"));
            if ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR)
            {
                array_push($cabecera_tabla, $idiomas->_("Usuario"));
            }
            return ($cabecera_tabla);
        }


        // Devuelve la consulta para la tabla de plantillas de informes
        static function dame_consulta_plantillas_informes($filtro)
        {
            switch ($_SESSION["perfil"])
            {
                case PERFIL_USUARIO_ESTANDAR:
                {
                    $consulta = "
                        SELECT *
                        FROM plantillas_informes
                        WHERE
                            (red = '".$_SESSION["id_red"]."')
                            AND (usuario = '".$_SESSION["id_usuario"]."')";
                    if ($filtro != "")
                    {
                        $campos = array("nombre");
                        $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                        $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
                    }
                    $consulta .= "
                        ORDER BY nombre ASC";
                    break;
                }
                default:
                {
                    // Nota: Se añade 'usuarios.id = '".$_SESSION["id_usuario"]."'' en la condición para que no se muestren filas repetidas
                    // (se mostrarían las plantillas de informes sin usuario tantas veces repetidas como usuarios hay en el sistema)
                    $consulta = "
                        SELECT
                            plantillas_informes.*
                        FROM
                            plantillas_informes,
                            usuarios
                        WHERE
                            (plantillas_informes.red = '".$_SESSION["id_red"]."')
                            AND
                                (((plantillas_informes.usuario = usuarios.id) AND (usuarios.perfil <> '".PERFIL_USUARIO_ESTANDAR."'))
                                OR ((plantillas_informes.usuario = '') AND (usuarios.id = '".$_SESSION["id_usuario"]."')))";
                    if ($filtro != "")
                    {
                        $campos = array("plantillas_informes.nombre");
                        $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                        $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
                    }
                    $consulta .= "
                        ORDER BY
                            plantillas_informes.nombre ASC,
                            plantillas_informes.usuario ASC";
                    break;
                }
            }
            return ($consulta);
        }


        // Devuelve la tabla de plantillas de informes
        static function dame_tabla_plantillas_informes($filtro)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $administracion_plantillas_informes = PlantillaInforme::dame_administracion_plantillas_informes();
            if ($administracion_plantillas_informes == true)
            {
                $boton_anyadir_plantilla_informe = "<i id='anyade_modifica_plantilla_informe' class='icon-plus color-blanco boton_personal_mostrar_ventana_anyadir_modificar_plantilla_informe boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_plantilla_informe);
            }
            $boton_actualizar_tabla_plantillas_informes = "<i id='actualiza_plantillas_informes' class='icon-refresh color-blanco boton_personal_actualizar_tabla_plantillas_informes boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_plantillas_informes);
            if ($administracion_plantillas_informes == true)
            {
                $boton_ayuda_tabla_plantillas_informes = "<i id='ayuda_plantillas_informes' class='icon-question-sign color-blanco boton_personal_ayuda_tabla_plantillas_informes boton-tabla-datos'></i>";
                array_push($opciones, $boton_ayuda_tabla_plantillas_informes);
            }

            // Se crea la tabla
            switch ($_SESSION["perfil"])
            {
                case PERFIL_USUARIO_ESTANDAR:
                {
                    $numero_columnas_tabla_plantillas_informes = NUMERO_COLUMNAS_TABLA_PLANTILLAS_INFORMES_SIN_USUARIO;
                    $anchuras_columnas_tabla_plantillas_informes = ANCHURAS_COLUMNAS_TABLA_PLANTILLAS_INFORMES_SIN_USUARIO;
                    break;
                }
                default:
                {
                    $numero_columnas_tabla_plantillas_informes = NUMERO_COLUMNAS_TABLA_PLANTILLAS_INFORMES_CON_USUARIO;
                    $anchuras_columnas_tabla_plantillas_informes = ANCHURAS_COLUMNAS_TABLA_PLANTILLAS_INFORMES_CON_USUARIO;
                    break;
                }
            }
            if ($administracion_plantillas_informes == true)
            {
                $tipo_fila = TIPO_FILA_TABLA_DATOS_DETALLES;
            }
            else
            {
                $tipo_fila = TIPO_FILA_TABLA_DATOS_NORMAL;
            }
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => $numero_columnas_tabla_plantillas_informes,
                "anchuras_columnas" => unserialize($anchuras_columnas_tabla_plantillas_informes),
                "tipo_fila" => $tipo_fila,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-personal-plantillas_informes",
                $idiomas->_("Plantillas de informes"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = PlantillaInforme::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las plantillas de informes a la tabla y el pie de tabla
            $consulta = PlantillaInforme::dame_consulta_plantillas_informes($filtro);
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }

            // Se añaden las plantillas de informes
            $numero_plantillas_informes = 0;
            while ($fila = $res->dame_siguiente_fila())
            {
                $plantilla_informe = new PlantillaInforme($fila);
                $params_fila = array(
                    "opciones" => $plantilla_informe->dame_opciones_tabla()
                );
                $tabla->anyade_fila(
                    "datosPlantillaInforme__".$fila['id'],
                    $plantilla_informe->dame_datos_tabla(),
                    $params_fila
                );
                $numero_plantillas_informes += 1;
            }
            $tabla->anyade_pie($idiomas->_("Plantillas de informes").": ".$numero_plantillas_informes);

            return ($tabla->dame_tabla());
        }


        // Miembros de plantilla de informe


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
            $id_usuario = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Descripción de tipo
                $descripcion_tipo = PlantillaInforme::dame_descripcion_tipo_plantilla_informe($this->params["tipo"]);

                // Usuario
                if ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR)
                {
                    // Nota: Puede haberse eliminado el usuario (no estándar) que ha creado un informe (y al eliminarse no se borran esos informes)
                    $id_usuario = $this->params["usuario"];
                    if ($id_usuario == "")
                    {
                        $id_usuario = $this->idiomas->_("ND");
                    }
                    $id_usuario = htmlspecialchars($id_usuario, ENT_QUOTES);
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
            array_push($datos_tabla, $descripcion_tipo);
            if ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR)
            {
                array_push($datos_tabla, $id_usuario);
            }
            return ($datos_tabla);
        }


        function dame_opciones_tabla()
		{
            $opciones = array();
            $administracion_plantillas_informes = PlantillaInforme::dame_administracion_plantillas_informes();
            if ($administracion_plantillas_informes == true)
            {
                $editar = "<i id='anyade_modifica_plantilla_informe__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' ".
                    "class='icon-pencil color-gris boton_personal_mostrar_ventana_anyadir_modificar_plantilla_informe boton-tabla-datos'></i>";
                $duplicar = "<i id='anyade_modifica_plantilla_informe__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' ".
                    "class='icon-copy color-gris boton_personal_mostrar_ventana_anyadir_modificar_plantilla_informe boton-tabla-datos'></i>";
                $borrar = "<i id='elimina_plantilla_informe__".$this->id."' nombre_plantilla_informe='".$this->params["nombre"]."' ".
                    "class='icon-remove color-gris boton_personal_eliminar_plantilla_informe boton-tabla-datos'></i>";
                $opciones = array($borrar, $duplicar, $editar);
            }

			return ($opciones);
        }


        function dame_detalles_tabla()
		{
			$info = "";
            $administracion_plantillas_informes = PlantillaInforme::dame_administracion_plantillas_informes();

            // Identificador
            $mostrar_identificador =
                ($administracion_plantillas_informes == true) ||
                ($_SESSION["utilizada_contrasenya_admin_superadmin"] == true);
            if ($mostrar_identificador == true)
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

            // Título del informe
            if ($this->params["titulo_informe"] != "")
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Título de informe").": ".htmlspecialchars($this->params["titulo_informe"], ENT_QUOTES)."<br/>";
            }

            // Periodo de tiempo por defecto
            $descripcion_periodo_tiempo_defecto = PlantillaInforme::dame_descripcion_periodo_tiempo_defecto_plantilla_informe($this->params["periodo_tiempo_defecto"]);
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Periodo de tiempo por defecto").": ".$descripcion_periodo_tiempo_defecto."<br/>";
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Iniciar desde el comienzo del periodo de tiempo por defecto").": ".
                dame_descripcion_valores_si_no($this->params["iniciar_comienzo_periodo_tiempo_defecto"])."<br/>";

            // Tipo de selección de horario semanal, exclusión e inclusión de fechas
            $descripcion_tipo_seleccion_horario_semanal_fechas =
                PlantillaInforme::dame_descripcion_tipo_seleccion_horario_semanal_fechas($this->params["tipo_seleccion_horario_semanal_fechas"]);
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Tipo de selección de horario semanal y fechas").": ".$descripcion_tipo_seleccion_horario_semanal_fechas."<br/>";

            // Logo personalizado
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Logo personalizado").": ".dame_descripcion_valores_si_no($this->params["logo_personalizado"]);
            if ($this->params["logo_personalizado"] == VALOR_SI)
            {
                $info .= " (".htmlspecialchars($this->params["nombre_logo"], ENT_QUOTES).")";
            }
            $info .= "<br/>";

            // Tema
            $descripcion_tema = dame_descripcion_tema($this->params["tema"]);
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Tema").": ".$descripcion_tema."<br/>";

            // Si la plantilla es configurable, se muestra la tabla de parámetros
            if ($this->params["tipo"] == TIPO_PLANTILLA_INFORME_CONFIGURABLE)
            {
                $info .= "<br/>";
                $info .= $this->dame_tabla_parametros();
            }

            // Tabla de elementos
            $info .= "<br/>";
            $info .= $this->dame_tabla_elementos();

            return ($info);
		}


        //
        // Funciones auxiliares
        //


        static function dame_administracion_plantillas_informes()
        {
            $administracion_plantillas_informes = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_personal"]["administracion_plantillas_informes"] == VALOR_SI);
            return ($administracion_plantillas_informes);
        }


        function dame_tabla_parametros()
		{
            $id_elemento_parametros_plantilla_informe = "parametros-plantilla-informe".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
            $tabla_parametros = "<div id='".$id_elemento_parametros_plantilla_informe."' class='contenedor-detalle-tabla-datos'>".
                ParametroPlantillaInforme::dame_tabla_parametros($this->id)."</div>";
            return ($tabla_parametros);
		}


        function dame_tabla_elementos()
		{
            $id_elemento_elementos_plantilla_informe = "elementos-plantilla-informe".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
            $tabla_elementos = "<div id='".$id_elemento_elementos_plantilla_informe."' class='contenedor-detalle-tabla-datos'>".
                ElementoPlantillaInforme::dame_tabla_elementos($this->id)."</div>";
            return ($tabla_elementos);
		}


        //
        // Funciones de tipos de plantilla de informe
        //


        static function dame_tipos_plantilla_informe()
        {
            $tipos_plantilla_informe = array();
            array_push($tipos_plantilla_informe, TIPO_PLANTILLA_INFORME_FIJO);
            array_push($tipos_plantilla_informe, TIPO_PLANTILLA_INFORME_CONFIGURABLE);
            return ($tipos_plantilla_informe);
        }


        static function dame_descripcion_tipo_plantilla_informe($tipo_plantilla_informe)
        {
            switch ($tipo_plantilla_informe)
            {
                case TIPO_PLANTILLA_INFORME_FIJO:
                {
                    $descripcion = "Fijo";
                    break;
                }
                case TIPO_PLANTILLA_INFORME_CONFIGURABLE:
                {
                    $descripcion = "Configurable";
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
        // Funciones de tipos de selección de horario semanal y fechas de plantilla de informe
        //


        static function dame_tipos_seleccion_horario_semanal_fechas()
        {
            $tipos_seleccion_horario_semanal_fechas = array();
            array_push($tipos_seleccion_horario_semanal_fechas, TIPO_SELECCION_HORARIO_SEMANAL_FECHAS_FIJO);
            array_push($tipos_seleccion_horario_semanal_fechas, TIPO_SELECCION_HORARIO_SEMANAL_FECHAS_CONFIGURABLE);
            return ($tipos_seleccion_horario_semanal_fechas);
        }


        static function dame_descripcion_tipo_seleccion_horario_semanal_fechas($tipo_seleccion_horario_semanal_fechas)
        {
            switch ($tipo_seleccion_horario_semanal_fechas)
            {
                case TIPO_SELECCION_HORARIO_SEMANAL_FECHAS_FIJO:
                {
                    $descripcion = "Fijo";
                    break;
                }
                case TIPO_SELECCION_HORARIO_SEMANAL_FECHAS_CONFIGURABLE:
                {
                    $descripcion = "Configurable";
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
        // Funciones de periodos de tiempos por defecto de plantilla de informe
        //


        // Devuelve la descripción del periodo de tiempo por defecto de una plantilla de informe
        static function dame_descripcion_periodo_tiempo_defecto_plantilla_informe($periodo_tiempo_defecto_plantilla_informe)
        {
            switch ($periodo_tiempo_defecto_plantilla_informe)
            {
                case PERIODO_TIEMPO_DEFECTO_PLANTILLA_INFORME_DIA:
                {
                    $descripcion = "Día";
                    break;
                }
                case PERIODO_TIEMPO_DEFECTO_PLANTILLA_INFORME_SEMANA:
                {
                    $descripcion = "Semana";
                    break;
                }
                case PERIODO_TIEMPO_DEFECTO_PLANTILLA_INFORME_MES:
                {
                    $descripcion = "Mes";
                    break;
                }
                case PERIODO_TIEMPO_DEFECTO_PLANTILLA_INFORME_ANYO:
                {
                    $descripcion = "Año";
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
	}
?>
