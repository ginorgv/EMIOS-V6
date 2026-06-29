<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');


    // Constantes

    // Indices de parámetros de tipo de línea base periódica
	define("INDICE_PARAMETRO_TIPO_LINEA_BASE_PERIODICA_PERIODICIDAD_VALORES", 0);
    define("INDICE_PARAMETRO_TIPO_LINEA_BASE_PERIODICA_TIPO_CALCULO_VALORES", 1);

    // Indices de parámetros de tipo de línea base funcional
	define("INDICE_PARAMETRO_TIPO_LINEA_BASE_FUNCIONAL_FUNCION_VALORES", 0);


	// Clase que representa una línea base
	class LineaBase
	{
        // Funciones estáticas de línea base


        // Devuelve la cabecera para la tabla de líneas base
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
				$idiomas->_("Nombre"),
                $idiomas->_("Sensor"),
                $idiomas->_("Tipo"),
                $idiomas->_("Intervalo de valores"),
			));
        }


        // Devuelve la consulta para la tabla de líneas base
        static function dame_consulta_lineas_base(
            $filtro,
            $tipo,
            $intervalo_valores)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Nota: Se leen todos los campos porque se pueden actualizar también los detalles en la tabla
            $consulta = "
                SELECT *
                FROM lineas_base
                WHERE
                    (red = '".$_SESSION["id_red"]."')";
            if ($filtro != "")
            {
                $campos = array("nombre");
                $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
            }
            if ($tipo != TIPO_TODOS)
            {
                $consulta .= "
                    AND (tipo = '".$bd_red->_($tipo)."')";
            }
            if ($intervalo_valores != INTERVALO_VALORES_TODOS)
            {
                $consulta .= "
                    AND (intervalo_valores = '".$bd_red->_($intervalo_valores)."')";
            }
            $consulta .= "
                ORDER BY nombre ASC";
            return ($consulta);
        }


        // Devuelve la tabla de líneas base
        static function dame_tabla_lineas_base(
            $filtro,
            $tipo,
            $intervalo_valores)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $administracion_lineas_base = LineaBase::dame_administracion_lineas_base();
            if ($administracion_lineas_base == true)
            {
                $boton_anyadir_linea_base = "<i id='anyade_modifica_linea_base' class='icon-plus color-blanco boton_proyectos_mostrar_ventana_anyadir_modificar_linea_base boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_linea_base);
            }
            $boton_actualizar_tabla_lineas_base = "<i id='actualiza_lineas_base' class='icon-refresh color-blanco boton_proyectos_actualizar_tabla_lineas_base boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_lineas_base);
            $boton_ayuda_tabla_lineas_base = "<i id='ayuda_lineas_base' class='icon-question-sign color-blanco boton_proyectos_ayuda_tabla_lineas_base boton-tabla-datos'></i>";
            array_push($opciones, $boton_ayuda_tabla_lineas_base);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_LINEAS_BASE,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_LINEAS_BASE),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-proyectos-lineas-base",
                $idiomas->_("Líneas base"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = LineaBase::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada uno de los líneas base a la tabla y el pie de tabla
            $consulta_lineas_base = LineaBase::dame_consulta_lineas_base(
                $filtro,
                $tipo,
                $intervalo_valores);
            $res_lineas_base = $bd_red->ejecuta_consulta($consulta_lineas_base);
            if ($res_lineas_base == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_lineas_base."'");
            }

            // Identificadores de líneas base del usuario actual
            $mostrar_todas_lineas_base = dame_mostrar_todas_lineas_base();
            if ($mostrar_todas_lineas_base == false)
            {
                $ids_lineas_base_usuario = LineaBase::dame_ids_lineas_base_usuario_actual();
            }

            // Filas de las líneas base
            $filas_lineas_base = array();
            while ($fila_linea_base = $res_lineas_base->dame_siguiente_fila())
            {
                $anyadir_linea_base = true;
                if ($mostrar_todas_lineas_base == false)
                {
                    if (in_array($fila_linea_base["id"], $ids_lineas_base_usuario) == false)
                    {
                        $anyadir_linea_base = false;
                    }
                }
                if ($anyadir_linea_base == true)
                {
                    array_push($filas_lineas_base, $fila_linea_base);
                }
            }

            // Se añaden los nombres de los sensores a los datos de las filas de las líneas base
            $consulta_sensores = "
                SELECT
                    id,
                    nombre
                FROM sensores
                WHERE
                    (red = '".$_SESSION["id_red"]."')";
            if ($mostrar_todas_lineas_base == false)
            {
                $consulta_sensores .= "
                    AND ".dame_condicion_consulta_sensores_usuario_actual(true);
            }
            $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
            $nombres_sensores = array();
            while ($fila_sensor = $res_sensores->dame_siguiente_fila())
            {
                $nombres_sensores[$fila_sensor["id"]] = $fila_sensor["nombre"];
            }
            for ($i = 0; $i < count($filas_lineas_base); $i++)
            {
                $id_sensor = $filas_lineas_base[$i]["sensor"];
                if (array_key_exists($id_sensor, $nombres_sensores) == true)
                {
                    $filas_lineas_base[$i]["nombre_sensor"] = $nombres_sensores[$id_sensor];
                }
                else
                {
                    continue;
                }
            }

            // Se añaden las líneas base
            $numero_lineas_base = 0;
            foreach ($filas_lineas_base as $fila_linea_base)
            {
                $linea_base = new LineaBase($fila_linea_base);

                $anyadir_linea_base = true;
                if ($mostrar_todas_lineas_base == false)
                {
                    if (in_array($linea_base->id, $ids_lineas_base_usuario) == false)
                    {
                        $anyadir_linea_base = false;
                    }
                }

                if ($anyadir_linea_base == true)
                {
                    $params_fila = array(
                        "opciones" => $linea_base->dame_opciones_tabla()
                    );
                    $tabla->anyade_fila(
                        "datosLineaBase__".$fila_linea_base['id'],
                        $linea_base->dame_datos_tabla(),
                        $params_fila
                    );
                    $numero_lineas_base += 1;
                }
            }
            $tabla->anyade_pie($idiomas->_("Líneas base").": ".$numero_lineas_base);

            return ($tabla->dame_tabla());
        }


        // Miembros de línea base


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
            $nombre_sensor= $icono_dato_erroneo;
            $descripcion_tipo = $icono_dato_erroneo;
            $descripcion_intervalo_valores = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Nombre de sensor (y descripción de campo)
                if (array_key_exists("nombre_sensor", $this->params) == true)
                {
                    $nombre_sensor = $this->params["nombre_sensor"];
                }
                else
                {
                    $nombre_sensor = dame_nombre_sensor($this->params["sensor"]);
                }
                $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $this->params["campo_parametros_extra"]);
                $campo = $campo_parametros_extra[0];
                $descripcion_campo = strtolower(dame_descripcion_campo_clase_sensor(
                    $this->params["clase_sensor"],
                    $campo));
                $nombre_sensor .= " (".$descripcion_campo.")";
                $nombre_sensor = htmlspecialchars($nombre_sensor, ENT_QUOTES);

                // Descripciones
                $descripcion_tipo = LineaBase::dame_descripcion_tipo_linea_base($this->params["tipo"]);
                $descripcion_intervalo_valores = dame_descripcion_intervalo_valores($this->params["intervalo_valores"]);
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
                $nombre_sensor,
                $descripcion_tipo,
                $descripcion_intervalo_valores
			));
		}


        function dame_opciones_tabla()
		{
            $nombre = htmlspecialchars($this->params['nombre'], ENT_QUOTES);

            $opciones = array();
            $administracion_lineas_base = LineaBase::dame_administracion_lineas_base();
            if ($administracion_lineas_base == true)
            {
                $editar = "<i id='anyade_modifica_linea_base__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' ".
                    "class='icon-pencil color-gris boton_proyectos_mostrar_ventana_anyadir_modificar_linea_base boton-tabla-datos'></i>";
                $duplicar = "<i id='anyade_modifica_linea_base__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' ".
                    "class='icon-copy color-gris boton_proyectos_mostrar_ventana_anyadir_modificar_linea_base boton-tabla-datos'></i>";
                $borrar = "<i id='elimina_linea_base__".$this->id."' nombre_linea_base='".$nombre."' ".
                    "class='icon-remove color-gris boton_proyectos_eliminar_linea_base boton-tabla-datos'></i>";
                $opciones = array($borrar, $duplicar, $editar);
            }

			return ($opciones);
		}


        function dame_herramientas_detalles_tabla()
		{
            $herramientas = "";

            $herramientas .= "
                <span class='boton-herramientas-detalle-tabla-datos'>
                    <button id='boton_refrescar__".$this->id."' class='btn-mini btn btn-success boton_proyectos_refrescar_tabla_linea_base'>
                        <i class='icon-refresh color-blanco'></i>
                    </button>
                </span>";

			return ($herramientas);
		}


		function dame_detalles_tabla()
		{
            $info = "";
            $administracion_lineas_base = LineaBase::dame_administracion_lineas_base();

            // Identificador
            if ($administracion_lineas_base == true)
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

            // Fechas de inicio y fin de periodo de referencia
            $fecha_inicio_periodo_referencia_local_local = convierte_formato_fecha($this->params["fecha_inicio_periodo_referencia"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $fecha_fin_periodo_referencia_local_local = convierte_formato_fecha($this->params["fecha_fin_periodo_referencia"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $info .= "<i class='icon-info-sign color-azul'></i> ".
               $this->idiomas->_("Fecha de inicio del periodo de referencia").": ".$fecha_inicio_periodo_referencia_local_local."<br/>";
            $info .= "<i class='icon-info-sign color-azul'></i> ".
               $this->idiomas->_("Fecha de fin del periodo de referencia").": ".$fecha_fin_periodo_referencia_local_local."<br/>";
            $info .= "<br/>";

            // Parámetros del tipo de línea base
            switch ($this->params['tipo'])
            {
                case TIPO_LINEA_BASE_PERIODICA:
                {
                    $parametros_linea_base_periodica = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $this->params['parametros_tipo']);
                    $periodicidad_valores = $parametros_linea_base_periodica[INDICE_PARAMETRO_TIPO_LINEA_BASE_PERIODICA_PERIODICIDAD_VALORES];
                    $tipo_calculo_valores = $parametros_linea_base_periodica[INDICE_PARAMETRO_TIPO_LINEA_BASE_PERIODICA_TIPO_CALCULO_VALORES];
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Periodicidad de valores").": ".LineaBase::dame_descripcion_periodicidad_valores_linea_base_periodica($periodicidad_valores)."<br/>";
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Tipo de cálculo de valores").": ".LineaBase::dame_descripcion_tipo_calculo_valores_linea_base_periodica($tipo_calculo_valores)."<br/>";
                    break;
                }
                case TIPO_LINEA_BASE_FUNCIONAL:
                {
                    $parametros_linea_base_funcional = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $this->params['parametros_tipo']);
                    $funcion_valores = $parametros_linea_base_funcional[INDICE_PARAMETRO_TIPO_LINEA_BASE_FUNCIONAL_FUNCION_VALORES];
                    if ($funcion_valores == "")
                    {
                        $funcion_valores = $this->idiomas->_("ND");
                    }
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Función de valores").": ".$funcion_valores."<br/>";
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de línea base desconocido: '".$this->params['tipo']."'");
                }
                $info .= "<br/>";
            }

            // Horario semanal y exclusión de fechas
            $cadena_horario_semanal = $this->params["horario_semanal"];
            $cadena_exclusion_fechas = $this->params["exclusion_fechas"];
            $html_horario_semanal = dame_descripcion_horario_semanal($cadena_horario_semanal, true, TIPO_DESCRIPCION_HTML);
            $html_exclusion_fechas = dame_descripcion_fechas($cadena_exclusion_fechas, TIPO_DESCRIPCION_HTML);
            if (($html_horario_semanal != "") || ($html_exclusion_fechas != ""))
            {
                if ($html_horario_semanal != "")
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Horario semanal").": ".$html_horario_semanal;
                }
                if ($html_exclusion_fechas != "")
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Exclusión de fechas").": ".$html_exclusion_fechas."<br/>";
                }
                $info .= "<br/>";
            }

            // Error estándar
            $error_estandar = $this->params['error_estandar'];
            if ($error_estandar == ID_NINGUNO)
            {
                $cadena_error_estandar = $this->idiomas->_("ND");
            }
            else
            {
                $cadena_error_estandar = formatea_numero($error_estandar, NUMERO_DECIMALES_ERROR_ESTANDAR_LINEA_BASE);
            }
            $info .= "<i class='icon-info-sign color-azul'></i> ".
            $this->idiomas->_("Error estándar")." (".$this->idiomas->_("RMSE").")".": ".$cadena_error_estandar."<br/>";

            // Coeficientes de variación y correlación
            if (($this->params['coeficiente_variacion'] != "") && ($this->params['coeficiente_variacion'] != 0))
            {
                $cadena_coeficiente_variacion = formatea_numero($this->params['coeficiente_variacion'], NUMERO_DECIMALES_COEFICIENTE_VARIACION_LINEA_BASE);
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Coeficiente de variación").": ".$cadena_coeficiente_variacion."<br/>";
            }
            if (($this->params['coeficiente_correlacion'] != "") && ($this->params['coeficiente_correlacion'] != 0))
            {
                $cadena_coeficiente_correlacion = formatea_numero($this->params['coeficiente_correlacion'], NUMERO_DECIMALES_COEFICIENTE_CORRELACION_LINEA_BASE);
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Coeficiente de correlación")." (".$this->idiomas->_("R2").")".": ".$cadena_coeficiente_correlacion."<br/>";
            }
            $info .= "<br/>";

            // Variables de línea base
            switch ($this->params['tipo'])
            {
                case TIPO_LINEA_BASE_FUNCIONAL:
                {
                    // Se muestra la tabla de las variables (si es necesario)
                    if (($administracion_lineas_base == true) || (dame_numero_variables_linea_base($this->id) > 0))
                    {
                        $id_elemento_variables_linea_base = 'variables-linea-base'.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
                        $info .= "<div id='".$id_elemento_variables_linea_base."' class='contenedor-detalle-tabla-datos'>".
                            $this->dame_tabla_variables()."</div>";
                        $info .= "<br/>";
                    }
                    break;
                }
            }

            // Se muestra la tabla de las excepciones (si es necesario)
            if (($administracion_lineas_base == true) || (dame_numero_excepciones_linea_base($this->id) > 0))
            {
                $id_elemento_excepciones_linea_base = 'excepciones-linea-base'.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
                $info .= "<div id='".$id_elemento_excepciones_linea_base."' class='contenedor-detalle-tabla-datos'>".
                    $this->dame_tabla_excepciones()."</div>";
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
        // Funciones para las tablas de variables y excepciones
        //


        function dame_tabla_variables()
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se crean las opciones de la tabla
            $administracion_lineas_base = LineaBase::dame_administracion_lineas_base();
            $boton_actualizar_tabla_variables_linea_base = "<i id='actualiza_tabla_variables_linea_base__".$this->id."' class='icon-refresh color-blanco boton_proyectos_actualizar_tabla_variables_linea_base boton-tabla-datos'></i>";
            $opciones = array($boton_actualizar_tabla_variables_linea_base);
            if ($administracion_lineas_base == true)
            {
                $boton_anyadir_variable_linea_base = "<i id='anyade_modifica_variable_linea_base__".$this->id."' class='icon-plus color-blanco boton_proyectos_mostrar_ventana_anyadir_modificar_variable_linea_base boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_variable_linea_base);
            }

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_VARIABLES_LINEA_BASE,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-variables-linea-base",
                $this->idiomas->_("Variables"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = array(
                $this->idiomas->_("Nombre"),
                $this->idiomas->_("Sensor"),
                $this->idiomas->_("Campo"));
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las variables a la tabla y el pie de tabla
            $consulta = "
                SELECT *
                FROM variables_lineas_base
                WHERE
                    linea_base = '".$bd_red->_($this->id)."'
                ORDER BY nombre ASC";
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_variables = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $id_variable = $fila["id"];
                $nombre = $fila["nombre"];
                $clase_sensor = $fila["clase_sensor"];
                $id_sensor = $fila["sensor"];
                $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $fila["campo_parametros_extra"]);
                $campo = $campo_parametros_extra[0];
                $parametros_extra_campo = $campo_parametros_extra[1];

                $nombre_sensor = dame_nombre_sensor($id_sensor);
                $descripcion_campo = dame_descripcion_campo_clase_sensor($clase_sensor, $campo);
                if ($parametros_extra_campo != "")
                {
                    $descripcion_parametros_extra_campo = strtolower(dame_descripcion_parametros_extra_campo_clase_sensor($clase_sensor, $campo));
                    $descripcion_campo .= " (".$descripcion_parametros_extra_campo.": ".$parametros_extra_campo.")";
                }

                $datos = array(
                    $nombre,
                    $nombre_sensor,
                    $descripcion_campo
                );

                $opciones = array();
                if ($administracion_lineas_base == true)
                {
                    $editar = "<i id='anyade_modifica_variable_linea_base__".$this->id."__".$id_variable."' class='icon-pencil color-gris boton_proyectos_mostrar_ventana_anyadir_modificar_variable_linea_base boton-tabla-datos'></i>";
                    $borrar = "<i id='elimina_variable_linea_base__".$this->id."__".$id_variable."' class='icon-remove color-gris boton_proyectos_eliminar_variable_linea_base boton-tabla-datos'></i>";
                    $opciones = array($borrar, $editar);
                }
                $params_fila = array(
                    "opciones" => $opciones
                );
                $tabla->anyade_fila(
                    "datosVariableLineaBase__".$this->id."__".$fila['id'],
                    $datos,
                    $params_fila
                );
            }
            $tabla->anyade_pie($this->idiomas->_("Variables").": ".$numero_variables);

            return ($tabla->dame_tabla(false));
		}


        function dame_tabla_excepciones()
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se crean las opciones de la tabla
            $administracion_lineas_base = LineaBase::dame_administracion_lineas_base();
            $boton_actualizar_tabla_excepciones_linea_base = "<i id='actualiza_tabla_excepciones_linea_base__".$this->id."' class='icon-refresh color-blanco boton_proyectos_actualizar_tabla_excepciones_linea_base boton-tabla-datos'></i>";
            $opciones = array($boton_actualizar_tabla_excepciones_linea_base);
            if ($administracion_lineas_base == true)
            {
                $boton_anyadir_excepcion_linea_base = "<i id='anyade_modifica_excepcion_linea_base__".$this->id."' class='icon-plus color-blanco boton_proyectos_mostrar_ventana_anyadir_modificar_excepcion_linea_base boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_excepcion_linea_base);
            }

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_EXCEPCIONES_LINEA_BASE,
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-excepciones-linea-base",
                $this->idiomas->_("Excepciones"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = array(
                $this->idiomas->_("Nombre"),
                $this->idiomas->_("Línea base"));
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las excepciones a la tabla y el pie de tabla
            $consulta = "
                SELECT *
                FROM excepciones_lineas_base
                WHERE
                    linea_base_padre = '".$bd_red->_($this->id)."'
                ORDER BY nombre ASC";
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_excepciones = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $id_excepcion = $fila["id"];
                $nombre = $fila["nombre"];
                $id_linea_base_hija = $fila["linea_base_hija"];

                $nombre_linea_base_hija = dame_nombre_linea_base($id_linea_base_hija);
                $datos = array(
                    $nombre,
                    $nombre_linea_base_hija
                );

                $opciones = array();
                if ($administracion_lineas_base == true)
                {
                    $editar = "<i id='anyade_modifica_excepcion_linea_base__".$this->id."__".$id_linea_base_hija."__".$id_excepcion."' class='icon-pencil color-gris boton_proyectos_mostrar_ventana_anyadir_modificar_excepcion_linea_base boton-tabla-datos'></i>";
                    $borrar = "<i id='elimina_excepcion_linea_base__".$this->id."__".$id_linea_base_hija."__".$id_excepcion."' class='icon-remove color-gris boton_proyectos_eliminar_excepcion_linea_base boton-tabla-datos'></i>";
                    $opciones = array($borrar, $editar);
                }
                $params_fila = array(
                    "opciones" => $opciones
                );
                $tabla->anyade_fila(
                    "datosExcepcionLineaBase__".$this->id."__".$fila['id'],
                    $datos,
                    $params_fila
                );
            }
            $tabla->anyade_pie($this->idiomas->_("Excepciones").": ".$numero_excepciones);

            return ($tabla->dame_tabla(false));
		}


        static function dame_detalles_tabla_excepcion_linea_base($id_linea_base_padre, $id_excepcion_linea_base)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $info = "";
            $administracion_lineas_base = LineaBase::dame_administracion_lineas_base();

            // Se recupera la información de la excepción de la línea base
            $consulta = "
                SELECT *
                FROM excepciones_lineas_base
                WHERE
                    id = '".$bd_red->_($id_excepcion_linea_base)."'
                ORDER BY nombre ASC";
            $res = $bd_red->ejecuta_consulta($consulta);
            if (($res == false) || ($res->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
            }
			$fila = $res->dame_siguiente_fila();

            // Información para administradores:
            // - Identificador
            if ($administracion_lineas_base == true)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $idiomas->_("Identificador").": ".$fila["id"]."<br/>";
            }

            // Descripción
            if ($fila['descripcion'] != "")
			{
				$info .= "<i class='icon-info-sign color-azul'></i> ".
                    $idiomas->_("Descripción").": ".htmlspecialchars($fila['descripcion'], ENT_QUOTES)."<br/>";
			}

            // Intervalo de valores de la línea base padre
            $intervalo_valores_linea_base_padre = dame_intervalo_valores_linea_base($id_linea_base_padre);

            // Horario semanal y fechas
            switch ($intervalo_valores_linea_base_padre)
            {
                case INTERVALO_VALORES_HORA:
                {
                    $mostrar_horario_semanal = true;
                    $mostrar_horas_horario_semanal = true;
                    break;
                }
                case INTERVALO_VALORES_DIA:
                {
                    $mostrar_horario_semanal = true;
                    $mostrar_horas_horario_semanal = false;
                    break;
                }
                case INTERVALO_VALORES_SEMANA:
                case INTERVALO_VALORES_MES:
                {
                    $mostrar_horario_semanal = false;
                    $mostrar_horas_horario_semanal = false;
                    break;
                }
            }
            if ($mostrar_horario_semanal == true)
            {
                $html_horario_semanal = dame_descripcion_horario_semanal(
                    $fila["horario_semanal"],
                    $mostrar_horas_horario_semanal,
                    TIPO_DESCRIPCION_HTML);
            }
            else
            {
                $html_horario_semanal = "";
            }
            $html_inclusion_fechas = dame_descripcion_fechas($fila["inclusion_fechas"], TIPO_DESCRIPCION_HTML);

            $info .= "<br/>";
            if (($html_horario_semanal <> "") || ($html_inclusion_fechas <> ""))
            {
                if ($html_horario_semanal <> "")
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Horario semanal").": ".$html_horario_semanal;
                }
                if ($html_inclusion_fechas <> "")
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Inclusión de fechas").": ".$html_inclusion_fechas;
                }
            }
            else
            {
                $info .= "<i class='icon-warning-sign color-rojo'></i> ".
                    $idiomas->_("No hay horario semanal ni inclusión de fechas");
            }

            return ($info);
        }


        //
        // Funciones auxiliares
        //


        static function dame_administracion_lineas_base()
        {
            $administracion_lineas_base = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_sensores"]["administracion_sensores"] == VALOR_SI);
            return ($administracion_lineas_base);
        }


        static function dame_ids_lineas_base_usuario_actual($ids_sensores_usuario = NULL)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se recuperan los ids de sensores visibles por el usuario actual
            if ($ids_sensores_usuario === NULL)
            {
                $ids_sensores_usuario = dame_ids_sensores_usuario_actual(true);
            }

            // Identificadores de líneas base
            $ids_lineas_base = array();

            // Consulta de líneas base
            $consulta_lineas_base = "
                SELECT
                    id,
                    sensor
                FROM lineas_base
                WHERE
                    red = '".$_SESSION["id_red"]."'
                ORDER BY nombre ASC";
            $res_lineas_base = $bd_red->ejecuta_consulta($consulta_lineas_base);
            if ($res_lineas_base == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_lineas_base."'");
            }
            while ($fila_linea_base = $res_lineas_base->dame_siguiente_fila())
            {
                $id = $fila_linea_base["id"];
                $id_sensor = $fila_linea_base["sensor"];

                $anyadir_linea_base = true;
                if (in_array($id_sensor, $ids_sensores_usuario) == false)
                {
                    $anyadir_linea_base = false;
                }

                if ($anyadir_linea_base == true)
                {
                    array_push($ids_lineas_base, $id);
                }
            }

            return ($ids_lineas_base);
        }


        //
        // Parámetros de línea base
        //


        static function dame_tipos_linea_base()
        {
            $tipos_linea_base = array();
            array_push($tipos_linea_base, TIPO_LINEA_BASE_PERIODICA);
            array_push($tipos_linea_base, TIPO_LINEA_BASE_FUNCIONAL);
            return ($tipos_linea_base);
        }


        static function dame_descripcion_tipo_linea_base($tipo_linea_base)
        {
            switch ($tipo_linea_base)
            {
                case TIPO_LINEA_BASE_PERIODICA:
                {
                    $descripcion_tipo_linea_base = "Periódica";
                    break;
                }
                case TIPO_LINEA_BASE_FUNCIONAL:
                {
                    $descripcion_tipo_linea_base = "Funcional";
                    break;
                }
                default:
                {
                    $descripcion_tipo_linea_base = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_tipo_linea_base));
        }


        static function dame_periodicidades_valores_linea_base_periodica()
        {
            $periodicidades_valores_linea_base_periodica = array();
            array_push($periodicidades_valores_linea_base_periodica, PERIODICIDAD_VALORES_LINEA_BASE_DIARIA);
            array_push($periodicidades_valores_linea_base_periodica, PERIODICIDAD_VALORES_LINEA_BASE_SEMANAL);
            return ($periodicidades_valores_linea_base_periodica);
        }


        static function dame_descripcion_periodicidad_valores_linea_base_periodica($periodicidad_valores_linea_base_periodica)
        {
            switch ($periodicidad_valores_linea_base_periodica)
            {
                case PERIODICIDAD_VALORES_LINEA_BASE_DIARIA:
                {
                    $descripcion_periodicidad_valores_linea_base_periodica = "Diaria";
                    break;
                }
                case PERIODICIDAD_VALORES_LINEA_BASE_SEMANAL:
                {
                    $descripcion_periodicidad_valores_linea_base_periodica = "Semanal";
                    break;
                }
                default:
                {
                    $descripcion_periodicidad_valores_linea_base_periodica = "Desconocida";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_periodicidad_valores_linea_base_periodica));
        }


        static function dame_tipos_calculo_valores_linea_base_periodica()
        {
            $tipos_calculo_valores_linea_base_periodica = array();
            array_push($tipos_calculo_valores_linea_base_periodica, TIPO_CALCULO_VALORES_LINEA_BASE_MEDIA);
            array_push($tipos_calculo_valores_linea_base_periodica, TIPO_CALCULO_VALORES_LINEA_BASE_MEDIANA);
            return ($tipos_calculo_valores_linea_base_periodica);
        }


        static function dame_descripcion_tipo_calculo_valores_linea_base_periodica($tipo_calculo_valores_linea_base_periodica)
        {
            switch ($tipo_calculo_valores_linea_base_periodica)
            {
                case TIPO_CALCULO_VALORES_LINEA_BASE_MEDIA:
                {
                    $descripcion_tipo_calculo_valores_linea_base_periodica = "Media";
                    break;
                }
                case TIPO_CALCULO_VALORES_LINEA_BASE_MEDIANA:
                {
                    $descripcion_tipo_calculo_valores_linea_base_periodica = "Mediana";
                    break;
                }
                default:
                {
                    $descripcion_tipo_calculo_valores_linea_base_periodica = "Desconocida";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_tipo_calculo_valores_linea_base_periodica));
        }
	}
?>
