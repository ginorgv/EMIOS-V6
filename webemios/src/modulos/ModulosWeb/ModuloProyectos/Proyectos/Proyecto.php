<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


	// Clase que representa un proyecto
	class Proyecto
	{
        // Funciones estáticas de proyecto


        // Devuelve la cabecera para la tabla de proyectos
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
				$idiomas->_("Nombre"),
                $idiomas->_("Sensor"),
                $idiomas->_("Intervalo de valores"),
                $idiomas->_("Avance"),
                $idiomas->_("Estado")
			));
        }


        // Devuelve la consulta para la tabla de proyectos
        static function dame_consulta_proyectos(
            $filtro,
            $intervalo_valores,
            $estado_avance,
            $estado)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Nota: Se leen todos los campos porque se pueden actualizar también los detalles en la tabla
            $consulta = "
                SELECT *
                FROM proyectos
                WHERE
                    (red = '".$_SESSION["id_red"]."')";
            if ($filtro != "")
            {
                $campos = array("nombre");
                $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
            }
            if ($intervalo_valores != INTERVALO_VALORES_TODOS)
            {
                $consulta .= "
                    AND (intervalo_valores = '".$bd_red->_($intervalo_valores)."')";
            }
            if ($estado_avance != ESTADO_AVANCE_PROYECTO_TODOS)
            {
                $consulta .= "
                    AND (estado_avance = '".$bd_red->_($estado_avance)."')";
            }
            if ($estado != ESTADO_PROYECTO_TODOS)
            {
                $consulta .= "
                    AND (estado = '".$bd_red->_($estado)."')";
            }
            $consulta .= "
                ORDER BY nombre ASC";
            return ($consulta);
        }


        // Devuelve la tabla de proyectos
        static function dame_tabla_proyectos(
            $filtro,
            $intervalo_valores,
            $estado_avance,
            $estado)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $administracion_proyectos = Proyecto::dame_administracion_proyectos();
            if ($administracion_proyectos == true)
            {
                $numero_maximo_proyectos = dame_numero_maximo_elementos_modulo(MODULO_PROYECTOS);
                if ($numero_maximo_proyectos > 0)
                {
                    $numero_proyectos = dame_numero_proyectos();
                }

                if (($numero_maximo_proyectos <= 0) || ($numero_proyectos < $numero_maximo_proyectos))
                {
                    $boton_anyadir_proyecto = "<i id='anyade_modifica_proyecto' ".
                        "class='icon-plus color-blanco boton_proyectos_mostrar_ventana_anyadir_modificar_proyecto boton-tabla-datos'></i>";
                    array_push($opciones, $boton_anyadir_proyecto);
                }
            }
            $boton_actualizar_tabla_proyectos = "<i id='actualiza_proyectos' class='icon-refresh color-blanco boton_proyectos_actualizar_tabla_proyectos boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_proyectos);
            $boton_ayuda_tabla_proyectos = "<i id='ayuda_proyectos' class='icon-question-sign color-blanco boton_proyectos_ayuda_tabla_proyectos boton-tabla-datos'></i>";
            array_push($opciones, $boton_ayuda_tabla_proyectos);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_PROYECTOS,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_PROYECTOS),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-proyectos-proyectos",
                $idiomas->_("Proyectos"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = Proyecto::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada uno de los proyectos a la tabla y el pie de tabla
            $consulta_proyectos = Proyecto::dame_consulta_proyectos(
                $filtro,
                $intervalo_valores,
                $estado_avance,
                $estado);
            $res_proyectos = $bd_red->ejecuta_consulta($consulta_proyectos);
            if ($res_proyectos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_proyectos."'");
            }

            // Identificadores de proyectos del usuario actual
            $mostrar_todos_proyectos = dame_mostrar_todos_proyectos();
            if ($mostrar_todos_proyectos == false)
            {
                $ids_proyectos_usuario = Proyecto::dame_ids_proyectos_usuario_actual();
            }

            // Filas de los proyectos
            $filas_proyectos = array();
            while ($fila_proyecto = $res_proyectos->dame_siguiente_fila())
            {
                $anyadir_proyecto = true;
                if ($mostrar_todos_proyectos == false)
                {
                    if (in_array($fila_proyecto["id"], $ids_proyectos_usuario) == false)
                    {
                        $anyadir_proyecto = false;
                    }
                }
                if ($anyadir_proyecto == true)
                {
                    array_push($filas_proyectos, $fila_proyecto);
                }
            }

            // Se añaden los nombres de los sensores a los datos de las filas de los proyectos
            $consulta_sensores = "
                SELECT
                    id,
                    nombre
                FROM sensores
                WHERE
                    (red = '".$_SESSION["id_red"]."')";
            if ($mostrar_todos_proyectos == false)
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
            for ($i = 0; $i < count($filas_proyectos); $i++)
            {
                $id_sensor = $filas_proyectos[$i]["sensor"];
                if (array_key_exists($id_sensor, $nombres_sensores) == true)
                {
                    $filas_proyectos[$i]["nombre_sensor"] = $nombres_sensores[$id_sensor];
                }
                else
                {
                    continue;
                }
            }

            // Se añaden los proyectos
            $numero_proyectos = 0;
            foreach ($filas_proyectos as $fila_proyecto)
            {
                $proyecto = new Proyecto($fila_proyecto);
                $params_fila = array(
                    "opciones" => $proyecto->dame_opciones_tabla()
                );
                $tabla->anyade_fila(
                    "datosProyecto__".$fila_proyecto['id'],
                    $proyecto->dame_datos_tabla(),
                    $params_fila
                );
                $numero_proyectos += 1;
            }
            $texto_pie = $idiomas->_("Proyectos").": ".$numero_proyectos;
            if ($administracion_proyectos == true)
            {
                $numero_maximo_proyectos = dame_numero_maximo_elementos_modulo(MODULO_PROYECTOS);
                if ($numero_maximo_proyectos > 0)
                {
                    $texto_pie .= " (".$idiomas->_("máximo").": ".$numero_maximo_proyectos.")";
                }
            }
            $tabla->anyade_pie($texto_pie);

            return ($tabla->dame_tabla());
        }


        // Miembros de proyecto


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
            $nombre_sensor = $icono_dato_erroneo;
            $descripcion_intervalo_valores = $icono_dato_erroneo;
            $descripcion_avance_proyecto = $icono_dato_erroneo;
            $descripcion_estado_proyecto_porcentaje_finalizacion = $icono_dato_erroneo;

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
                $descripcion_campo = strtolower(dame_descripcion_campo_clase_sensor(
                    $this->params["clase_sensor"],
                    $this->params["campo"]));
                $nombre_sensor .= " (".$descripcion_campo.")";
                $nombre_sensor = htmlspecialchars($nombre_sensor, ENT_QUOTES);

                // Descripciones
                $descripcion_intervalo_valores = dame_descripcion_intervalo_valores($this->params["intervalo_valores"]);
                $descripcion_avance_proyecto = Proyecto::dame_descripcion_avance_proyecto(
                    $this->params,
                    $this->params["valor_real_avance"],
                    $this->params["valor_simulado_avance"],
                    $this->params["porcentaje_finalizacion"],
                    NULL);
                $descripcion_estado_proyecto_porcentaje_finalizacion = Proyecto::dame_descripcion_estado_proyecto_porcentaje_finalizacion(
                    $this->params["estado"],
                    $this->params["porcentaje_finalizacion"],
                    NULL);
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
                $descripcion_intervalo_valores,
                $descripcion_avance_proyecto,
                $descripcion_estado_proyecto_porcentaje_finalizacion
			));
		}


        function dame_opciones_tabla()
		{
            $nombre = htmlspecialchars($this->params['nombre'], ENT_QUOTES);

            $opciones = array();
            $administracion_proyectos = Proyecto::dame_administracion_proyectos();
            if ($administracion_proyectos == true)
            {
                $editar = "<i id='anyade_modifica_proyecto__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' ".
                    "class='icon-pencil color-gris boton_proyectos_mostrar_ventana_anyadir_modificar_proyecto boton-tabla-datos'></i>";
                $duplicar = "<i id='anyade_modifica_proyecto__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' ".
                    "class='icon-copy color-gris boton_proyectos_mostrar_ventana_anyadir_modificar_proyecto boton-tabla-datos'></i>";
                $borrar = "<i id='elimina_proyecto__".$this->id."' nombre_proyecto='".$nombre."' ".
                    "class='icon-remove color-gris boton_proyectos_eliminar_proyecto boton-tabla-datos'></i>";
                $opciones = array($borrar, $duplicar, $editar);
            }
			return ($opciones);
		}


        function dame_herramientas_detalles_tabla()
		{
            // Herramientas de detalles de proyecto
            $administracion_proyectos = Proyecto::dame_administracion_proyectos();
            $herramientas = "";
            $herramientas .= "
                <span class='boton-herramientas-detalle-tabla-datos'>
                    <button id='boton_refrescar__".$this->id."' class='btn-mini btn btn-success boton_proyectos_refrescar_tabla_proyecto'>
                        <i class='icon-refresh color-blanco'></i>
                    </button>
                </span>";

            // Actualizar proyecto
            if ($administracion_proyectos == true)
            {
                $herramientas .= "
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button id='boton_actualizar_proyecto__".$this->id."' class='btn-mini btn btn-success boton_proyectos_actualizar_proyecto'>".
                            $this->idiomas->_("Actualizar proyecto")."
                        </button>
                    </span>";
            }
			return ($herramientas);
		}


		function dame_detalles_tabla()
		{
            $info = "";
            $administracion_proyectos = Proyecto::dame_administracion_proyectos();

            // Identificador
            if ($administracion_proyectos == true)
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

            // Línea base del proyecto
            if ($this->params["linea_base"] == ID_NINGUNO)
            {
                $info .= "<i class='icon-warning-sign color-rojo'></i> ".
                    $this->idiomas->_("El proyecto no tiene línea base asignada")."<br/>";
            }
            else
            {
                $nombre_linea_base = dame_nombre_linea_base($this->params["linea_base"]);
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Línea base").": ".$nombre_linea_base."<br/>";
            }

            // Fechas de inicio y fin del proyecto
            $info .= "<br/>";
            $cadena_fecha_inicio_local_local = convierte_formato_fecha($this->params["fecha_inicio"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_fecha_fin_local_local = convierte_formato_fecha($this->params["fecha_fin"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Fecha de inicio").": ".$cadena_fecha_inicio_local_local."<br/>";
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Fecha de fin").": ".$cadena_fecha_fin_local_local."<br/>";

            // Hora de último cálculo de avance
            $hora_ultimo_calculo_avance = $this->params["hora_ultimo_calculo_avance"];
            if ($hora_ultimo_calculo_avance !== NULL)
            {
                $info .= "<br/>";
                $cadena_hora_ultimo_calculo_avance_local_local = convierte_formato_fecha($hora_ultimo_calculo_avance, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Hora de última actualización del proyecto").": ".$cadena_hora_ultimo_calculo_avance_local_local."<br/>";
            }

            // Días restantes para el inicio o la finalización del proyecto
            // - Nota: Puede suceder que el proyecto ya haya empezado (o finalizado) pero el estado aún no este activo (o finalizado) esperando a
            //   la finalización del intervalo actual para calcular el primer (o último) valor
            $zona_horaria = dame_zona_horaria_local();
            switch ($this->params["estado"])
            {
                case ESTADO_PROYECTO_PENDIENTE:
                {
                    $fecha_hora_actual_local = dame_fecha_hora_actual_local();
                    $fecha_inicio_local = convierte_cadena_a_fecha($cadena_fecha_inicio_local_local, $_SESSION["formato_fecha_local"], $zona_horaria);
                    $numero_dias_restantes_inicio_proyecto = $fecha_hora_actual_local->diff($fecha_inicio_local)->days;
                    if ($numero_dias_restantes_inicio_proyecto > 0)
                    {
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Número de días restantes para el inicio del proyecto").": ".$numero_dias_restantes_inicio_proyecto."<br/>";
                    }
                    break;
                }
                case ESTADO_PROYECTO_ACTIVO:
                {
                    $fecha_hora_actual_local = dame_fecha_hora_actual_local();
                    $fecha_inicio_local = convierte_cadena_a_fecha($cadena_fecha_inicio_local_local, $_SESSION["formato_fecha_local"], $zona_horaria);
                    $fecha_fin_local = convierte_cadena_a_fecha($cadena_fecha_fin_local_local, $_SESSION["formato_fecha_local"], $zona_horaria);
                    $fecha_hora_actual_local->setTime(0, 0, 0);
                    $fecha_inicio_local->setTime(0, 0, 0);
                    $fecha_fin_local->setTime(0, 0, 0);
                    if ($fecha_hora_actual_local > $fecha_inicio_local)
                    {
                        $numero_dias_desde_inicio_proyecto = $fecha_hora_actual_local->diff($fecha_inicio_local)->days;
                    }
                    else
                    {
                        $numero_dias_desde_inicio_proyecto = 0;
                    }
                    if ($fecha_fin_local > $fecha_hora_actual_local)
                    {
                        $numero_dias_restantes_fin_proyecto = $fecha_fin_local->diff($fecha_hora_actual_local)->days + 1;
                    }
                    else
                    {
                        $numero_dias_restantes_fin_proyecto = 0;
                    }
                    if ($numero_dias_desde_inicio_proyecto > 0)
                    {
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Número de días transcurridos desde el inicio del proyecto").": ".formatea_numero($numero_dias_desde_inicio_proyecto, 0)."<br/>";
                    }
                    if ($numero_dias_restantes_fin_proyecto > 0)
                    {
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Número de días restantes para la finalización del proyecto").": ".formatea_numero($numero_dias_restantes_fin_proyecto, 0)."<br/>";
                    }
                    break;
                }
            }

            // Unidad de medida y número de decimales
            $clase_sensor = $this->params["clase_sensor"];
            $id_sensor = $this->params["sensor"];
            $campo = $this->params["campo"];
            $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
            $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);

            // Horas de fin de valores y de últimos valores del avance y valores real y simulado
            $hora_fin_valores_avance = $this->params["hora_fin_valores_avance"];
            $hora_ultimos_valores_avance = $this->params["hora_ultimos_valores_avance"];
            $valor_real_avance = $this->params["valor_real_avance"];
            $valor_simulado_avance = $this->params["valor_simulado_avance"];
            if (($hora_fin_valores_avance !== NULL) && ($hora_ultimos_valores_avance !== NULL) &&
                ($valor_real_avance !== NULL) && ($valor_simulado_avance !== NULL))
            {
                $info .= "<br/>";
                $cadena_hora_fin_valores_avance_local_local = convierte_formato_fecha($hora_fin_valores_avance, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Hora de fin de consulta de valores").": ".$cadena_hora_fin_valores_avance_local_local."<br/>";
                $cadena_hora_ultimos_valores_avance_local_local = convierte_formato_fecha($hora_ultimos_valores_avance, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Hora de últimos valores").": ".$cadena_hora_ultimos_valores_avance_local_local."<br/>";

                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Valor real").": ".formatea_numero($valor_real_avance, $numero_decimales_valores);
                if ($unidad_medida != "")
                {
                    $info .= " ".$unidad_medida;
                }
                $info .= "<br/>";
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Valor simulado").": ".formatea_numero($valor_simulado_avance, $numero_decimales_valores);
                if ($unidad_medida != "")
                {
                    $info .= " ".$unidad_medida;
                }
                $info .= "<br/>";
            }

            // Objetivo del proyecto
            $descripcion_objetivo_proyecto = $this->dame_descripcion_objetivo_proyecto();
            $info .= "<br/>";
            $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Objetivo del proyecto").": ".$descripcion_objetivo_proyecto."<br/>";

            // Detalles del objetivo del proyecto (sólo objetivo absoluto)
            // - En campos de sensor incrementales se muestra el objetivo en los estados activo y finalizado
            // - En campos de sensor puntuales sólo se muestra si se ha alcanzado el objetivo en el estado finalizado
            $tipo_objetivo = $this->params["tipo_objetivo"];
            $tipo_valor_objetivo = $this->params["tipo_valor_objetivo"];
            $valor_objetivo = $this->params["valor_objetivo"];
            $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);
            if ($tipo_objetivo == TIPO_OBJETIVO_PROYECTO_ABSOLUTO)
            {
                switch ($this->params["estado_avance"])
                {
                    case ESTADO_AVANCE_PROYECTO_POSITIVO:
                    case ESTADO_AVANCE_PROYECTO_NEGATIVO:
                    {
                        switch ($this->params["estado"])
                        {
                            case ESTADO_PROYECTO_ACTIVO:
                            {
                                if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
                                {
                                    if (($numero_dias_desde_inicio_proyecto > 0) && ($numero_dias_restantes_fin_proyecto > 0))
                                    {
                                        // Diferencia de incremento
                                        $valor_real_avance = $this->params["valor_real_avance"];
                                        $valor_simulado_avance = $this->params["valor_simulado_avance"];
                                        $diferencia_incremento = $valor_real_avance - $valor_simulado_avance;

                                        // Si se ha alcanzado el objetivo
                                        if ((($tipo_valor_objetivo == TIPO_VALOR_OBJETIVO_PROYECTO_INFERIOR) && ($diferencia_incremento <= $valor_objetivo)) ||
                                            (($tipo_valor_objetivo == TIPO_VALOR_OBJETIVO_PROYECTO_SUPERIOR) && ($diferencia_incremento >= $valor_objetivo)))
                                        {
                                            $info .= "<i class='icon-thumbs-up-alt color-verde'></i> ".
                                                $this->idiomas->_("Se ha alcanzado el objetivo del proyecto");
                                        }
                                        else
                                        {
                                            if ((($tipo_valor_objetivo == TIPO_VALOR_OBJETIVO_PROYECTO_INFERIOR) && ($diferencia_incremento < 0)) ||
                                                (($tipo_valor_objetivo == TIPO_VALOR_OBJETIVO_PROYECTO_SUPERIOR) && ($diferencia_incremento > 0)))
                                            {
                                                $valor_objetivo = abs($valor_objetivo);
                                                $diferencia_incremento = abs($diferencia_incremento);

                                                $diferencia_incremento_dia = $diferencia_incremento / $numero_dias_desde_inicio_proyecto;
                                                $numero_dias_consecucion_objetivo = (int) ($valor_objetivo / $diferencia_incremento_dia);
                                                $numero_dias_restantes_consecucion_objetivo = $numero_dias_consecucion_objetivo - $numero_dias_desde_inicio_proyecto;
                                                if ($numero_dias_restantes_consecucion_objetivo <= $numero_dias_restantes_fin_proyecto)
                                                {
                                                    $info .= "<i class='icon-thumbs-up-alt color-verde'></i> ";
                                                }
                                                else
                                                {
                                                    $info .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                                                }
                                                $info .= $this->idiomas->_("Número de días restantes para alcanzar el objetivo del proyecto").": ".
                                                    formatea_numero($numero_dias_restantes_consecucion_objetivo, 0);
                                            }
                                            else
                                            {
                                                $info .= "<i class='icon-thumbs-down-alt color-rojo'></i> ".
                                                    $this->idiomas->_("No se va a alcanzar el objetivo del proyecto");
                                            }
                                        }
                                        $info .= "<br/>";
                                    }
                                }
                                break;
                            }
                            case ESTADO_PROYECTO_FINALIZADO:
                            {
                                // Diferencia de incremento
                                $valor_real_avance = $this->params["valor_real_avance"];
                                $valor_simulado_avance = $this->params["valor_simulado_avance"];
                                $diferencia_incremento = $valor_real_avance - $valor_simulado_avance;

                                // Si se ha alcanzado el objetivo
                                if ((($tipo_valor_objetivo == TIPO_VALOR_OBJETIVO_PROYECTO_INFERIOR) && ($diferencia_incremento <= $valor_objetivo)) ||
                                    (($tipo_valor_objetivo == TIPO_VALOR_OBJETIVO_PROYECTO_SUPERIOR) && ($diferencia_incremento >= $valor_objetivo)))
                                {
                                    $info .= "<i class='icon-thumbs-up-alt color-verde'></i> ".
                                        $this->idiomas->_("Se ha alcanzado el objetivo del proyecto");
                                }
                                else
                                {
                                    $info .= "<i class='icon-thumbs-down-alt color-rojo'></i> ".
                                        $this->idiomas->_("No se ha alcanzado el objetivo del proyecto");
                                }
                                $info .= "<br/>";
                                break;
                            }
                        }
                    }
                }
            }

            // Se muestra la tabla de los valores adicionales (si es necesario)
            if ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES)
            {
                if (($administracion_proyectos == true) || (dame_numero_valores_adicionales_proyecto($this->id) > 0))
                {
                    $info .= "<br/>";
                    $id_elemento_valores_adicionales_proyecto = 'valores-adicionales-proyecto'.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$this->id;
                    $info .= "<div id='".$id_elemento_valores_adicionales_proyecto."' class='contenedor-detalle-tabla-datos'>".
                        $this->dame_tabla_valores_adicionales(true)."</div>";
                }
            }

            return ($info);
		}


        //
        // Funciones de descripción de objetivo del proyecto
        //


        function dame_descripcion_objetivo_proyecto()
        {
            // Unidad de medida y número de decimales
            $clase_sensor = $this->params["clase_sensor"];
            $id_sensor = $this->params["sensor"];
            $campo = $this->params["campo"];
            $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
            $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);

            $tipo_objetivo = $this->params["tipo_objetivo"];
            $tipo_valor_objetivo = $this->params["tipo_valor_objetivo"];
            $valor_objetivo = $this->params["valor_objetivo"];
            if ($valor_objetivo === NULL)
            {
                $cadena_descripcion_tipo_objetivo = Proyecto::dame_descripcion_tipo_objetivo_proyecto($tipo_objetivo);
                $descripcion_objetivo_proyecto = $this->idiomas->_("Ninguno")." (".strtolower($cadena_descripcion_tipo_objetivo).")";
            }
            else
            {
                $descripcion_objetivo_proyecto = "";
                switch ($tipo_objetivo)
                {
                    case TIPO_OBJETIVO_PROYECTO_PORCENTUAL:
                    {
                        $descripcion_objetivo_proyecto .= formatea_numero($valor_objetivo, 2)." %";
                        break;
                    }
                    case TIPO_OBJETIVO_PROYECTO_ABSOLUTO:
                    {
                        $descripcion_objetivo_proyecto .= formatea_numero($valor_objetivo, $numero_decimales_valores);
                        if ($unidad_medida != "")
                        {
                            $descripcion_objetivo_proyecto .= " ".$unidad_medida;
                        }
                        break;
                    }
                }
                switch ($tipo_valor_objetivo)
                {
                    case TIPO_VALOR_OBJETIVO_PROYECTO_INFERIOR:
                    {
                        $descripcion_objetivo_proyecto .= " (".$this->idiomas->_("inferior").")";
                        break;
                    }
                    case TIPO_VALOR_OBJETIVO_PROYECTO_SUPERIOR:
                    {
                        $descripcion_objetivo_proyecto .= " (".$this->idiomas->_("superior").")";
                        break;
                    }
                }
            }
            return ($descripcion_objetivo_proyecto);
        }


        //
        // Funciones para las tablas de valores adicionales
        //


        function dame_tabla_valores_adicionales($mostrar_opciones)
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se crean las opciones de la tabla
            if ($mostrar_opciones == true)
            {
                $administracion_proyectos = Proyecto::dame_administracion_proyectos();
                $boton_actualizar_tabla_valores_adicionales_proyecto = "<i id='actualiza_tabla_valores_adicionales_proyecto__".$this->id."' class='icon-refresh color-blanco boton_proyectos_actualizar_tabla_valores_adicionales_proyecto boton-tabla-datos'></i>";
                $opciones = array($boton_actualizar_tabla_valores_adicionales_proyecto);
                if ($administracion_proyectos == true)
                {
                    $boton_anyadir_valor_adicional_proyecto = "<i id='anyade_modifica_valor_adicional_proyecto__".$this->id."' class='icon-plus color-blanco boton_proyectos_mostrar_ventana_anyadir_modificar_valor_adicional_proyecto boton-tabla-datos'></i>";
                    array_push($opciones, $boton_anyadir_valor_adicional_proyecto);
                }
            }
            else
            {
                $opciones = array();
            }

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_VALORES_ADICIONALES_PROYECTO,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_VALORES_ADICIONALES_PROYECTO),
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-valores-adicionales-proyecto",
                $this->idiomas->_("Valores adicionales"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = array(
                $this->idiomas->_("Nombre"),
                $this->idiomas->_("Destino"),
                $this->idiomas->_("Valor"),
                $this->idiomas->_("Periodicidad"),
                $this->idiomas->_("Fecha de inicio"),
                $this->idiomas->_("Fecha de fin"),
                $this->idiomas->_("Aplicar en intervalos sin valores"));
            $tabla->anyade_cabecera("", $cabecera);

            // Unidad de medida y número de decimales
            $clase_sensor = $this->params["clase_sensor"];
            $id_sensor = $this->params["sensor"];
            $campo = $this->params["campo"];
            $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
            $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);

            // Se añade cada uno de los valores adicionales a la tabla y el pie de tabla
            $consulta = "
                SELECT *
                FROM valores_adicionales_proyectos
                WHERE
                    proyecto = '".$bd_red->_($this->id)."'
                ORDER BY nombre ASC";
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_valores_adicionales = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $id_valor_adicional = $fila["id"];
                $nombre = $fila["nombre"];
                $destino = $fila["destino"];
                $valor = $fila["valor"];
                $periodicidad = $fila["periodicidad"];
                $cadena_fecha_inicio_base_datos = $fila["fecha_inicio"];
                $cadena_fecha_fin_base_datos = $fila["fecha_fin"];
                $aplicar_intervalos_sin_valores = $fila["aplicar_intervalos_sin_valores"];

                // Destino, valor y periodicidad
                $descripcion_destino = Proyecto::dame_descripcion_destino_valor_adicional_proyecto($destino);
                $cadena_valor = formatea_numero($valor, $numero_decimales_valores);
                if ($unidad_medida != "")
                {
                    $cadena_valor .= " ".$unidad_medida;
                }
                $descripcion_periodicidad = Proyecto::dame_descripcion_periodicidad_valor_adicional_proyecto($periodicidad);

                // Conversión de fechas
                if ($cadena_fecha_inicio_base_datos === NULL)
                {
                    $cadena_fecha_inicio_local = $this->idiomas->_("ND");
                }
                else
                {
                    $cadena_fecha_inicio_local = convierte_formato_fecha($cadena_fecha_inicio_base_datos, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
                }
                if ($cadena_fecha_fin_base_datos === NULL)
                {
                    $cadena_fecha_fin_local = $this->idiomas->_("ND");
                }
                else
                {
                    $cadena_fecha_fin_local = convierte_formato_fecha($cadena_fecha_inicio_base_datos, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
                }

                // Aplicar en intervalos sin valores
                $descripcion_aplicar_intervalos_sin_valores = dame_descripcion_valores_si_no($aplicar_intervalos_sin_valores);

                // Datos de la fila
                $datos = array(
                    $nombre,
                    $descripcion_destino,
                    $cadena_valor,
                    $descripcion_periodicidad,
                    $cadena_fecha_inicio_local,
                    $cadena_fecha_fin_local,
                    $descripcion_aplicar_intervalos_sin_valores
                );

                // Se añade la fila
                $opciones = array();
                if (($mostrar_opciones == true) && ($administracion_proyectos == true))
                {
                    $editar = "<i id='anyade_modifica_valor_adicional_proyecto__".$this->id."__".$id_valor_adicional."' class='icon-pencil color-gris boton_proyectos_mostrar_ventana_anyadir_modificar_valor_adicional_proyecto boton-tabla-datos'></i>";
                    $borrar = "<i id='elimina_valor_adicional_proyecto__".$this->id."__".$id_valor_adicional."' class='icon-remove color-gris boton_proyectos_eliminar_valor_adicional_proyecto boton-tabla-datos'></i>";
                    $opciones = array($borrar, $editar);
                }
                $params_fila = array(
                    "opciones" => $opciones
                );
                $tabla->anyade_fila(
                    "datosValorAdicionalProyecto__".$this->id."__".$fila['id'],
                    $datos,
                    $params_fila
                );
            }
            $tabla->anyade_pie($this->idiomas->_("Valores adicionales").": ".$numero_valores_adicionales);

            // Se devuelve el html de la tabla
            $incluir_salto_linea = ($mostrar_opciones == false);
            return ($tabla->dame_tabla($incluir_salto_linea));
		}


        //
        // Funciones auxiliares
        //


        static function dame_administracion_proyectos()
        {
            $administracion_proyectos = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_sensores"]["administracion_sensores"] == VALOR_SI);
            return ($administracion_proyectos);
        }


        static function dame_ids_proyectos_usuario_actual($ids_sensores_usuario = NULL)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se recuperan los ids de sensores y de grupos de sensores visibles por el usuario actual
            if ($ids_sensores_usuario === NULL)
            {
                $ids_sensores_usuario = dame_ids_sensores_usuario_actual(true);
            }

            // Identificadores de proyectos
            $ids_proyectos = array();

            // Consulta de proyectos
            $consulta_proyectos = "
                SELECT
                    id,
                    sensor
                FROM proyectos
                WHERE
                    red = '".$_SESSION["id_red"]."'
                ORDER BY nombre ASC";
            $res_proyectos = $bd_red->ejecuta_consulta($consulta_proyectos);
            if ($res_proyectos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_proyectos."'");
            }
            while ($fila_proyecto = $res_proyectos->dame_siguiente_fila())
            {
                $id = $fila_proyecto["id"];
                $id_sensor = $fila_proyecto["sensor"];

                $anyadir_proyecto = true;
                if (in_array($id_sensor, $ids_sensores_usuario) == false)
                {
                    $anyadir_proyecto = false;
                }

                if ($anyadir_proyecto == true)
                {
                    array_push($ids_proyectos, $id);
                }
            }

            return ($ids_proyectos);
        }


        //
        // Información de avance y estado de proyecto
        //


        static function dame_descripcion_avance_proyecto(
            $fila_proyecto,
            $valor_real_avance,
            $valor_simulado_avance,
            $porcentaje_finalizacion,
            $clase_unidad_medida)
        {
            $idiomas = new Idiomas();

            // Tipo de objetivo y valor de objetivo
            $tipo_objetivo = $fila_proyecto["tipo_objetivo"];
            $tipo_valor_objetivo = $fila_proyecto["tipo_valor_objetivo"];
            $valor_objetivo = $fila_proyecto["valor_objetivo"];

            // Tipo de valores
            $clase_sensor = $fila_proyecto["clase_sensor"];
            $campo = $fila_proyecto["campo"];
            $tipo_valores = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);

            // Estado de avance del proyecto:
            // - Si no hay valores reales o simulados no hay estado de avance de proyectos
            // - Si hay valores reales o simulados:
            //   - Si no hay tipo de valor de objetivo, el estado de avance es sin valor de objetivo (se muestra sólo la diferencia de valores)
            //   - Si hay tipo de valor de objetivo se muestra el estado de avance correspondiente (positivo o negativo)
            if (($valor_real_avance === NULL) || ($valor_simulado_avance === NULL))
            {
                $estado_avance_proyecto = ESTADO_AVANCE_PROYECTO_NINGUNO;
            }
            else
            {
                if ($tipo_valor_objetivo === TIPO_NINGUNO)
                {
                    $estado_avance_proyecto = ESTADO_AVANCE_PROYECTO_SIN_VALOR_OBJETIVO;
                }
                else
                {
                    switch ($tipo_objetivo)
                    {
                        case TIPO_OBJETIVO_PROYECTO_PORCENTUAL:
                        {
                            if ($valor_simulado_avance == 0)
                            {
                                if ($valor_real_avance == 0)
                                {
                                    $porcentaje_avance = 0;
                                }
                                else
                                {
                                    if ($valor_real_avance > 0)
                                    {
                                        $porcentaje_avance = PORCENTAJE_AVANCE_MAXIMO;
                                    }
                                    else
                                    {
                                        if ($valor_real_avance < 0)
                                        {
                                            // Nota: El valor real del avance no debería ser negativo porque es un valor incremental
                                            $porcentaje_avance = PORCENTAJE_AVANCE_MINIMO;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $porcentaje_avance = ($valor_real_avance * 100) / $valor_simulado_avance - 100;
                            }
                            if ((($tipo_valor_objetivo == TIPO_VALOR_OBJETIVO_PROYECTO_INFERIOR) && ($porcentaje_avance <= $valor_objetivo)) ||
                                (($tipo_valor_objetivo == TIPO_VALOR_OBJETIVO_PROYECTO_SUPERIOR) && ($porcentaje_avance >= $valor_objetivo)))
                            {
                                $estado_avance_proyecto = ESTADO_AVANCE_PROYECTO_POSITIVO;
                            }
                            else
                            {
                                $estado_avance_proyecto = ESTADO_AVANCE_PROYECTO_NEGATIVO;
                            }
                            break;
                        }
                        case TIPO_OBJETIVO_PROYECTO_ABSOLUTO:
                        {
                            $diferencia_incremento = $valor_real_avance - $valor_simulado_avance;
                            switch ($tipo_valores)
                            {
                                case TIPO_VALORES_SENSOR_PUNTUALES:
                                {
                                    $valor_objetivo_parcial = $valor_objetivo;
                                    break;
                                }
                                case TIPO_VALORES_SENSOR_INCREMENTALES:
                                {
                                    $valor_objetivo_parcial = $valor_objetivo * $porcentaje_finalizacion / 100;
                                    break;
                                }
                            }
                            if ((($tipo_valor_objetivo == TIPO_VALOR_OBJETIVO_PROYECTO_INFERIOR) && ($diferencia_incremento <= $valor_objetivo_parcial)) ||
                                (($tipo_valor_objetivo == TIPO_VALOR_OBJETIVO_PROYECTO_SUPERIOR) && ($diferencia_incremento >= $valor_objetivo_parcial)))
                            {
                                $estado_avance_proyecto = ESTADO_AVANCE_PROYECTO_POSITIVO;
                            }
                            else
                            {
                                $estado_avance_proyecto = ESTADO_AVANCE_PROYECTO_NEGATIVO;
                            }
                            break;
                        }
                        default:
                        {
                            throw new Exception("Tipo de objetivo incorrecto: '".$tipo_objetivo."'");
                        }
                    }
                }
            }

            // Imagen del estado
            switch ($estado_avance_proyecto)
            {
                case ESTADO_AVANCE_PROYECTO_NINGUNO:
                {
                    $imagen_avance = "<i class='icon-question-sign color-gris-claro'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("desconocido"), ENT_QUOTES)."}"."</texto></i>";
                    break;
                }
                case ESTADO_AVANCE_PROYECTO_SIN_DATOS:
                {
                    $imagen_avance = "<i class='icon-warning-sign color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("aviso"), ENT_QUOTES)."}"."</texto></i>";
                    break;
                }
                case ESTADO_AVANCE_PROYECTO_SIN_VALOR_OBJETIVO:
                {
                    $imagen_avance = "<i class='icon-info-sign color-azul'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("información"), ENT_QUOTES)."}"."</texto></i>";
                    break;
                }
                case ESTADO_AVANCE_PROYECTO_POSITIVO:
                {
                    $imagen_avance = "<i class='icon-thumbs-up-alt color-verde'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("positivo"), ENT_QUOTES)."}"."</texto></i>";
                    break;
                }
                case ESTADO_AVANCE_PROYECTO_NEGATIVO:
                {
                    $imagen_avance = "<i class='icon-thumbs-down-alt color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("negativo"), ENT_QUOTES)."}"."</texto></i>";
                    break;
                }
                default:
                {
                    $imagen_avance = "";
                    break;
                }
            }
            $descripcion_estado_avance_proyecto = $imagen_avance;

            // Texto adicional según el estado del avance
            switch ($estado_avance_proyecto)
            {
                case ESTADO_AVANCE_PROYECTO_NINGUNO:
                {
                    $texto_adicional_estado_avance_proyecto = $idiomas->_("Ninguno");
                    break;
                }
                case ESTADO_AVANCE_PROYECTO_ERROR:
                {
                    $texto_adicional_estado_avance_proyecto = $idiomas->_("Error");
                    break;
                }
                case ESTADO_AVANCE_PROYECTO_SIN_DATOS:
                {
                    $texto_adicional_estado_avance_proyecto = $idiomas->_("Sin datos");
                    break;
                }
                case ESTADO_AVANCE_PROYECTO_SIN_VALOR_OBJETIVO:
                {
                    // Tipo de objetivo
                    switch ($tipo_objetivo)
                    {
                        case TIPO_OBJETIVO_PROYECTO_PORCENTUAL:
                        {
                            // Ejemplo de cadena de avance: 5 %
                            if ($valor_simulado_avance == 0)
                            {
                                if ($valor_real_avance == 0)
                                {
                                    $cadena_porcentaje_avance = "0.00";
                                }
                                else
                                {
                                    if ($valor_real_avance > 0)
                                    {
                                        $cadena_porcentaje_avance = $idiomas->_("INF");
                                    }
                                    else
                                    {
                                        if ($valor_real_avance < 0)
                                        {
                                            $cadena_porcentaje_avance = "-".$idiomas->_("INF");
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $porcentaje_avance = ($valor_real_avance * 100) / $valor_simulado_avance - 100;
                                $cadena_porcentaje_avance = formatea_numero($porcentaje_avance, 2);
                            }
                            $texto_adicional_estado_avance_proyecto = $cadena_porcentaje_avance." %";
                            break;
                        }
                        case TIPO_OBJETIVO_PROYECTO_ABSOLUTO:
                        {
                            // Unidad de medida y número de decimales
                            $clase_sensor = $fila_proyecto["clase_sensor"];
                            $id_sensor = $fila_proyecto["sensor"];
                            $campo = $fila_proyecto["campo"];
                            $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
                            $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);

                            // Ejemplo de cadena de avance: 100 kWh
                            $diferencia_incremento = $valor_real_avance - $valor_simulado_avance;
                            $cadena_diferencia_incremento = formatea_numero($diferencia_incremento, $numero_decimales_valores);
                            $texto_adicional_estado_avance_proyecto .= $cadena_diferencia_incremento;
                            if ($unidad_medida != "")
                            {
                                if (($clase_unidad_medida == "") || ($clase_unidad_medida === NULL))
                                {
                                    $cadena_unidad_medida = $unidad_medida;
                                }
                                else
                                {
                                    $cadena_unidad_medida = "<span class='".$clase_unidad_medida."'>".$unidad_medida."</span>";
                                }
                                $texto_adicional_estado_avance_proyecto .= " ".$cadena_unidad_medida;
                            }
                            break;
                        }
                    }
                    break;
                }
                case ESTADO_AVANCE_PROYECTO_POSITIVO:
                case ESTADO_AVANCE_PROYECTO_NEGATIVO:
                {
                    // Tipo de objetivo y valor de objetivo
                    $tipo_objetivo = $fila_proyecto["tipo_objetivo"];
                    $tipo_valor_objetivo = $fila_proyecto["tipo_valor_objetivo"];
                    $valor_objetivo = $fila_proyecto["valor_objetivo"];

                    // Tipo de objetivo
                    switch ($tipo_objetivo)
                    {
                        case TIPO_OBJETIVO_PROYECTO_PORCENTUAL:
                        {
                            // Ejemplo de cadena de avance: 5 % (10 %)
                            if ($valor_simulado_avance == 0)
                            {
                                if ($valor_real_avance == 0)
                                {
                                    $cadena_porcentaje_avance = "0.00";
                                }
                                else
                                {
                                    if ($valor_real_avance > 0)
                                    {
                                        $cadena_porcentaje_avance = $idiomas->_("INF");
                                    }
                                    else
                                    {
                                        if ($valor_real_avance < 0)
                                        {
                                            $cadena_porcentaje_avance = "-".$idiomas->_("INF");
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $porcentaje_avance = ($valor_real_avance * 100) / $valor_simulado_avance - 100;
                                $cadena_porcentaje_avance = formatea_numero($porcentaje_avance, 2);
                            }
                            if (($clase_unidad_medida == "") && ($clase_unidad_medida !== NULL))
                            {
                                $cadena_tanto_por_ciento = "%";
                            }
                            else
                            {
                                $cadena_tanto_por_ciento = "<span class='".$clase_unidad_medida."'>"."%"."</span>";
                            }
                            $texto_adicional_estado_avance_proyecto = $cadena_porcentaje_avance." ".$cadena_tanto_por_ciento;
                            if ($valor_objetivo != 0)
                            {
                                $cadena_porcentaje_objetivo = formatea_numero($valor_objetivo, 2);
                                switch ($tipo_valor_objetivo)
                                {
                                    case TIPO_VALOR_OBJETIVO_PROYECTO_INFERIOR:
                                    {
                                        $cadena_porcentaje_objetivo = "< ".$cadena_porcentaje_objetivo;
                                        break;
                                    }
                                    case TIPO_VALOR_OBJETIVO_PROYECTO_SUPERIOR:
                                    {
                                        $cadena_porcentaje_objetivo = "> ".$cadena_porcentaje_objetivo;
                                        break;
                                    }
                                }
                                $texto_adicional_estado_avance_proyecto .= " (".$cadena_porcentaje_objetivo." ".$cadena_tanto_por_ciento.")";
                            }
                            break;
                        }
                        case TIPO_OBJETIVO_PROYECTO_ABSOLUTO:
                        {
                            // Unidad de medida y número de decimales
                            $clase_sensor = $fila_proyecto["clase_sensor"];
                            $id_sensor = $fila_proyecto["sensor"];
                            $campo = $fila_proyecto["campo"];
                            $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
                            $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);

                            // Ejemplo de cadena de avance: 100 kWh (300 kWh)
                            $diferencia_incremento = $valor_real_avance - $valor_simulado_avance;
                            $cadena_diferencia_incremento = formatea_numero($diferencia_incremento, $numero_decimales_valores);
                            $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);
                            switch ($tipo_valores_campo)
                            {
                                case TIPO_VALORES_SENSOR_PUNTUALES:
                                {
                                    $valor_objetivo_parcial = $valor_objetivo;
                                    break;
                                }
                                case TIPO_VALORES_SENSOR_INCREMENTALES:
                                {
                                    $valor_objetivo_parcial = $valor_objetivo * $porcentaje_finalizacion / 100;
                                    break;
                                }
                            }
                            if ($valor_objetivo != 0)
                            {
                                $cadena_objetivo_parcial = formatea_numero($valor_objetivo_parcial, $numero_decimales_valores);
                                switch ($tipo_valor_objetivo)
                                {
                                    case TIPO_VALOR_OBJETIVO_PROYECTO_INFERIOR:
                                    {
                                        $cadena_objetivo_parcial = "< ".$cadena_objetivo_parcial;
                                        break;
                                    }
                                    case TIPO_VALOR_OBJETIVO_PROYECTO_SUPERIOR:
                                    {
                                        $cadena_objetivo_parcial = "> ".$cadena_objetivo_parcial;
                                        break;
                                    }
                                }
                            }
                            if ($unidad_medida != "")
                            {
                                if (($clase_unidad_medida == "") && ($clase_unidad_medida !== NULL))
                                {
                                    $cadena_unidad_medida = $unidad_medida;
                                }
                                else
                                {
                                    $cadena_unidad_medida = "<span class='".$clase_unidad_medida."'>".$unidad_medida."</span>";
                                }
                                $texto_adicional_estado_avance_proyecto = $cadena_diferencia_incremento." ".$cadena_unidad_medida;
                                if ($valor_objetivo != 0)
                                {
                                    $texto_adicional_estado_avance_proyecto .= " (".$cadena_objetivo_parcial." ".$cadena_unidad_medida.")";
                                }
                            }
                            else
                            {
                                $texto_adicional_estado_avance_proyecto = $cadena_diferencia_incremento;
                                if ($valor_objetivo != 0)
                                {
                                   $texto_adicional_estado_avance_proyecto .= " (".$cadena_objetivo_parcial.")";
                                }
                            }
                            break;
                        }
                    }
                    break;
                }
                default:
                {
                    $texto_adicional_estado_avance_proyecto = $idiomas->_("Desconocido");
                    break;
                }
            }
            if ($descripcion_estado_avance_proyecto != "")
            {
                $descripcion_estado_avance_proyecto .= " ";
            }
            $descripcion_estado_avance_proyecto .= $texto_adicional_estado_avance_proyecto;

            // Se devuelve la descripción del estado de avance del proyecto
            return ($descripcion_estado_avance_proyecto);
        }


        static function dame_descripcion_estado_proyecto_porcentaje_finalizacion(
            $estado_proyecto,
            $porcentaje_finalizacion_proyecto,
            $clase_unidad_medida)
        {
            $idiomas = new Idiomas();
            
            // Imagen del estado
            switch ($estado_proyecto)
            {
                case ESTADO_PROYECTO_NINGUNO:
                {
                    $imagen_estado = "<i class='icon-question-sign color-gris-claro'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("desconocido"), ENT_QUOTES)."}"."</texto></i>";
                    break;
                }
                case ESTADO_PROYECTO_SIN_LINEA_BASE:
                {
                    $imagen_estado = "<i class='icon-warning-sign color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("error"), ENT_QUOTES)."}"."</texto></i>";
                    break;
                }
                case ESTADO_PROYECTO_ERROR:
                {
                    $imagen_estado = "<i class='icon-warning-sign color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("error"), ENT_QUOTES)."}"."</texto></i>";
                    break;
                }
                case ESTADO_PROYECTO_PENDIENTE:
                {
                    $imagen_estado = "<i class='icon-cogs color-gris-claro'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("pendiente"), ENT_QUOTES)."}"."</texto></i>";
                    break;
                }
                case ESTADO_PROYECTO_ACTIVO:
                {
                    $imagen_estado = "<i class='icon-circle color-verde'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("activo"), ENT_QUOTES)."}"."</texto></i>";
                    break;
                }
                case ESTADO_PROYECTO_FINALIZADO:
                {
                    $imagen_estado = "<i class='icon-circle color-rojo'>".
                        "<texto class='elemento-oculto'>"."{".htmlspecialchars($idiomas->_("finalizado"), ENT_QUOTES)."}"."</texto></i>";
                    break;
                }
                default:
                {
                    $imagen_estado = "";
                    break;
                }
            }
            $descripcion_estado_proyecto = $imagen_estado;

            // Descripción del estado
            if ($descripcion_estado_proyecto != "")
            {
                $descripcion_estado_proyecto .= " ";
            }
            $descripcion_estado_proyecto .= Proyecto::dame_descripcion_estado_proyecto($estado_proyecto);

            // Porcentaje de finalización del proyecto
            if ($porcentaje_finalizacion_proyecto !== NULL)
            {
                $cadena_porcentaje_finalizacion_proyecto = formatea_numero($porcentaje_finalizacion_proyecto, 2);
                if (($clase_unidad_medida == "") || ($clase_unidad_medida === NULL))
                {
                    $cadena_porcentaje = "%";
                }
                else
                {
                    $cadena_porcentaje = "<span class='".$clase_unidad_medida."'>"."%"."</span>";
                }
                $descripcion_estado_proyecto .= " (".$cadena_porcentaje_finalizacion_proyecto." ".$cadena_porcentaje.")";
            }

            // Se devuelve la descripción del estado del proyecto
            return ($descripcion_estado_proyecto);
        }


        //
        // Parámetros de proyecto
        //


        static function dame_tipos_objetivo_proyecto()
        {
            $tipos_objetivo_proyecto = array();
            array_push($tipos_objetivo_proyecto, TIPO_OBJETIVO_PROYECTO_ABSOLUTO);
            array_push($tipos_objetivo_proyecto, TIPO_OBJETIVO_PROYECTO_PORCENTUAL);
            return ($tipos_objetivo_proyecto);
        }


        static function dame_descripcion_tipo_objetivo_proyecto($tipo_objetivo_proyecto)
        {
            switch ($tipo_objetivo_proyecto)
            {
                case TIPO_OBJETIVO_PROYECTO_ABSOLUTO:
                {
                    $descripcion_tipo_objetivo_proyecto = "Absoluto";
                    break;
                }
                case TIPO_OBJETIVO_PROYECTO_PORCENTUAL:
                {
                    $descripcion_tipo_objetivo_proyecto = "Porcentual";
                    break;
                }
                default:
                {
                    $descripcion_tipo_objetivo_proyecto = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_tipo_objetivo_proyecto));
        }


        static function dame_tipos_valor_objetivo_proyecto()
        {
            $tipos_valor_objetivo_proyecto = array();
            array_push($tipos_valor_objetivo_proyecto, TIPO_VALOR_OBJETIVO_PROYECTO_INFERIOR);
            array_push($tipos_valor_objetivo_proyecto, TIPO_VALOR_OBJETIVO_PROYECTO_SUPERIOR);
            return ($tipos_valor_objetivo_proyecto);
        }


        static function dame_descripcion_tipo_valor_objetivo_proyecto($tipo_valor_objetivo_proyecto)
        {
            switch ($tipo_valor_objetivo_proyecto)
            {
                case TIPO_NINGUNO:
                {
                    $descripcion_tipo_valor_objetivo_proyecto = "Ninguno";
                    break;
                }
                case TIPO_VALOR_OBJETIVO_PROYECTO_INFERIOR:
                {
                    $descripcion_tipo_valor_objetivo_proyecto = "Inferior";
                    break;
                }
                case TIPO_VALOR_OBJETIVO_PROYECTO_SUPERIOR:
                {
                    $descripcion_tipo_valor_objetivo_proyecto = "Superior";
                    break;
                }
                default:
                {
                    $descripcion_tipo_valor_objetivo_proyecto = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_tipo_valor_objetivo_proyecto));
        }


        static function dame_estados_avance_proyecto()
        {
            $estados_avance_proyecto = array();
            array_push($estados_avance_proyecto, ESTADO_AVANCE_PROYECTO_NINGUNO);
            array_push($estados_avance_proyecto, ESTADO_AVANCE_PROYECTO_SIN_DATOS);
            array_push($estados_avance_proyecto, ESTADO_AVANCE_PROYECTO_POSITIVO);
            array_push($estados_avance_proyecto, ESTADO_AVANCE_PROYECTO_NEGATIVO);
            return ($estados_avance_proyecto);
        }


        static function dame_descripcion_estado_avance_proyecto($estado_avance_proyecto)
        {
            switch ($estado_avance_proyecto)
            {
                case ESTADO_AVANCE_PROYECTO_NINGUNO:
                {
                    $descripcion_estado_avance_proyecto = "Ninguno";
                    break;
                }
                case ESTADO_AVANCE_PROYECTO_SIN_DATOS:
                {
                    $descripcion_estado_avance_proyecto = "Sin datos";
                    break;
                }
                case ESTADO_AVANCE_PROYECTO_POSITIVO:
                {
                    $descripcion_estado_avance_proyecto = "Positivo";
                    break;
                }
                case ESTADO_AVANCE_PROYECTO_NEGATIVO:
                {
                    $descripcion_estado_avance_proyecto = "Negativo";
                    break;
                }
                default:
                {
                    $descripcion_estado_avance_proyecto = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_estado_avance_proyecto));
        }


        static function dame_estados_proyecto()
        {
            $estados_proyecto = array();
            array_push($estados_proyecto, ESTADO_PROYECTO_NINGUNO);
            array_push($estados_proyecto, ESTADO_PROYECTO_SIN_LINEA_BASE);
            array_push($estados_proyecto, ESTADO_PROYECTO_ERROR);
            array_push($estados_proyecto, ESTADO_PROYECTO_PENDIENTE);
            array_push($estados_proyecto, ESTADO_PROYECTO_ACTIVO);
            array_push($estados_proyecto, ESTADO_PROYECTO_FINALIZADO);
            return ($estados_proyecto);
        }


        static function dame_descripcion_estado_proyecto($estado_proyecto)
        {
            switch ($estado_proyecto)
            {
                case ESTADO_PROYECTO_NINGUNO:
                {
                    $descripcion_estado_proyecto = "Ninguno";
                    break;
                }
                case ESTADO_PROYECTO_SIN_LINEA_BASE:
                {
                    $descripcion_estado_proyecto = "Sin línea base";
                    break;
                }
                case ESTADO_PROYECTO_ERROR:
                {
                    $descripcion_estado_proyecto = "Error";
                    break;
                }
                case ESTADO_PROYECTO_PENDIENTE:
                {
                    $descripcion_estado_proyecto = "Pendiente";
                    break;
                }
                case ESTADO_PROYECTO_ACTIVO:
                {
                    $descripcion_estado_proyecto = "Activo";
                    break;
                }
                case ESTADO_PROYECTO_FINALIZADO:
                {
                    $descripcion_estado_proyecto = "Finalizado";
                    break;
                }
                default:
                {
                    $descripcion_estado_proyecto = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_estado_proyecto));
        }


        //
        // Parámetros de valores adicionales de proyecto
        //


        static function dame_destinos_valor_adicional_proyecto()
        {
            $destinos_valor_adicional_proyecto = array();
            array_push($destinos_valor_adicional_proyecto, DESTINO_VALOR_ADICIONAL_PROYECTO_VALORES_REALES);
            array_push($destinos_valor_adicional_proyecto, DESTINO_VALOR_ADICIONAL_PROYECTO_VALORES_SIMULADOS);
            return ($destinos_valor_adicional_proyecto);
        }


        static function dame_descripcion_destino_valor_adicional_proyecto($destino_valor_adicional_proyecto)
        {
            switch ($destino_valor_adicional_proyecto)
            {
                case DESTINO_VALOR_ADICIONAL_PROYECTO_VALORES_REALES:
                {
                    $descripcion_destino_valor_adicional_proyecto = "Valores reales";
                    break;
                }
                case DESTINO_VALOR_ADICIONAL_PROYECTO_VALORES_SIMULADOS:
                {
                    $descripcion_destino_valor_adicional_proyecto = "Valores simulados";
                    break;
                }
                default:
                {
                    $descripcion_destino_valor_adicional_proyecto = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_destino_valor_adicional_proyecto));
        }


        static function dame_periodicidades_valor_adicional_proyecto()
        {
            $periodicidades_valor_adicional_proyecto = array();
            array_push($periodicidades_valor_adicional_proyecto, PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_NINGUNA);
            array_push($periodicidades_valor_adicional_proyecto, PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_PUNTUAL);
            array_push($periodicidades_valor_adicional_proyecto, PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_HORARIA);
            array_push($periodicidades_valor_adicional_proyecto, PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_DIARIA);
            array_push($periodicidades_valor_adicional_proyecto, PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_SEMANAL);
            array_push($periodicidades_valor_adicional_proyecto, PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_MENSUAL);
            return ($periodicidades_valor_adicional_proyecto);
        }


        static function dame_descripcion_periodicidad_valor_adicional_proyecto($periodicidad_valor_adicional_proyecto)
        {
            switch ($periodicidad_valor_adicional_proyecto)
            {
                case PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_NINGUNA:
                {
                    $descripcion_periodicidad_valor_adicional_proyecto = "Ninguna";
                    break;
                }
                case PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_PUNTUAL:
                {
                    $descripcion_periodicidad_valor_adicional_proyecto = "Puntual";
                    break;
                }
                case PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_HORARIA:
                {
                    $descripcion_periodicidad_valor_adicional_proyecto = "Horaria";
                    break;
                }
                case PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_DIARIA:
                {
                    $descripcion_periodicidad_valor_adicional_proyecto = "Diaria";
                    break;
                }
                case PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_SEMANAL:
                {
                    $descripcion_periodicidad_valor_adicional_proyecto = "Semanal";
                    break;
                }
                case PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_MENSUAL:
                {
                    $descripcion_periodicidad_valor_adicional_proyecto = "Mensual";
                    break;
                }
                default:
                {
                    $descripcion_periodicidad_valor_adicional_proyecto = "Desconocida";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_periodicidad_valor_adicional_proyecto));
        }
	}
?>
