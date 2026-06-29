<?php
	session_start();

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloWebEmios.php');

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/Alarmas/Alarma.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/AccionUsuario.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/HistoricoProcesado.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/util_informes_procesado.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/util_procesado.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


	class ModuloMonitorizacion extends ModuloWebEmios
	{
        function __construct()
        {
            // Super
            parent::__construct(MODULO_MONITORIZACION, NOMBRE_MODULO_MONITORIZACION);
        }


        //
        // Funciones virtuales sobreescritas
        //


        static function dame_secciones($parametros_extra)
        {
            $secciones = array(

                SECCION_MONITORIZACION_PROCESADO,
                SECCION_MONITORIZACION_ALARMAS,
                SECCION_MONITORIZACION_ACCIONES_USUARIO
            );
            return ($secciones);
        }


        static function dame_descripcion_seccion($seccion)
        {
            switch ($seccion)
            {
                case SECCION_MONITORIZACION_PROCESADO:
                {
                    $descripcion = "Procesado de datos";
                    break;
                }
                case SECCION_MONITORIZACION_ALARMAS:
                {
                    $descripcion = "Alarmas";
                    break;
                }
                case SECCION_MONITORIZACION_ACCIONES_USUARIO:
                {
                    $descripcion = "Acciones";
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


        function dame_contenido_seccion($seccion, $parametros_extra)
		{
            // Módulo
            $html = "<div id='modulo' name='".MODULO_MONITORIZACION."' hidden></div>";

            // Se añade el contenido de la sección
            $GLOBALS["reutilizar_consultas_bases_datos"] = true;
            $res = "OK";
            switch ($seccion)
            {
                case SECCION_MONITORIZACION_PROCESADO:
                {
                    $html .= $this->dame_procesado();
                    break;
                }
                case SECCION_MONITORIZACION_ALARMAS:
                {
                    $html .= $this->dame_tabla_filtro_alarmas();
                    $html .= $this->dame_tabla_alarmas();
                    break;
                }
                case SECCION_MONITORIZACION_ACCIONES_USUARIO:
                {
                    $html .= $this->dame_tabla_filtro_acciones_usuario();
                    $html .= $this->dame_tabla_acciones_usuario();
                    break;
                }
                default:
                {
                    $res = "ERROR";
                    $msg = $this->idiomas->_("Sección desconocida");
                    break;
                }
            }

            print(json_encode(array(
                "res" => $res,
                "msg" => $msg,
                "html" => $html))
            );
		}


        //
        // Funciones sobreescritas
        //


        static function dame_seccion_defecto($secciones = NULL)
        {
            $seccion_defecto = SECCION_MONITORIZACION_PROCESADO;
            return ($seccion_defecto);
        }


		//
		// Funciones para obtener el contenido de las secciones
		//


		function dame_procesado()
		{
            $contenido = "
				<div id='tabs' class='tabbable'>";
			$contenido .= "
					<ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-historico-procesado-monitorizacion'>".$this->idiomas->_("Histórico")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-ejecucion-manual-procesado-monitorizacion'>".$this->idiomas->_("Ejecución manual")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-operaciones-datos-sensores-monitorizacion'>".$this->idiomas->_("Operaciones de datos")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tiempos-ejecucion-procesado-monitorizacion'>".$this->idiomas->_("Tiempos de ejecución")."</a></li>
					</ul>
					<div id='tabs-procesado' class='tab-content'>";

            $contenido .= "
                        <div class='tab-pane active' id='tab-historico-procesado-monitorizacion'>";
            $contenido .= $this->dame_tabla_filtro_historico_procesado();
            $contenido .= $this->dame_tabla_historico_procesado();
            $contenido .= "
                        </div>";

            $contenido .= "
						<div class='tab-pane' id='tab-ejecucion-manual-procesado-monitorizacion'>";
            $contenido .= $this->dame_ejecucion_manual_procesado();
            $contenido .= "
						</div>";

            $contenido .= "
						<div class='tab-pane' id='tab-operaciones-datos-sensores-monitorizacion'>";
            $contenido .= $this->dame_herramientas_operaciones_datos_sensores_procesado();
            $contenido .= "
                <span class='boton-contenido-seccion'>
                    <button class='btn-mini btn btn-success boton_actualizar_tabla_operaciones_datos_sensores'>
                        <i class='icon-refresh color-blanco'></i>
                    </button>
                </span>";
            $contenido .= dame_tabla_operaciones_datos_sensores_procesado(MODULO_MONITORIZACION);
            $contenido .= dame_operaciones_datos_sensores_pendientes_procesado(MODULO_MONITORIZACION);
            $contenido .= "
						</div>";

            $contenido .= "
						<div class='tab-pane' id='tab-tiempos-ejecucion-procesado-monitorizacion'>";
            $contenido .= $this->dame_tiempos_ejecucion_procesado();
            $contenido .= "
						</div>";

			$contenido .= "
					</div>
				</div>";

			return ($contenido);
		}


        function dame_tabla_historico_procesado()
		{
            $limite_elementos_tabla_superado = false;
            $contenido = "<div id='tablaHistoricoProcesado'>".
                HistoricoProcesado::dame_tabla_historico_procesado(
                    TIPO_EJECUCION_PROCESADO_NORMAL,
                    CLASE_TODAS,
                    TIPO_TODOS,
                    GRANULARIDAD_TODAS,
                    NULL,
                    NULL,
                    $limite_elementos_tabla_superado)."</div>";
			return ($contenido);
		}


        function dame_ejecucion_manual_procesado()
		{
            $idiomas = new Idiomas();

            // Se recuperan los controles a mostrar
            $sufijo_controles = "monitorizacion_ejecucion_manual_procesado";

            $control_lista_tipos_ejecucion_procesado = dame_control_lista_tipos_ejecucion_procesado(
                $sufijo_controles,
                OPCIONES_EXTRA_LISTA_TIPOS_EJECUCIONES_PROCESADO_SIN_OPCIONES_EXTRA,
                $idiomas->_("Tipo de ejecución"));
            $control_lista_clases_sensor = dame_control_lista_clases_sensor(
                $sufijo_controles,
                OPCIONES_EXTRA_LISTA_CLASES_TODAS_NINGUNA,
                false,
                true,
                $idiomas->_("Clase de sensor"));
            $control_lista_tipos_sensor = $this->dame_control_lista_tipos_sensor($sufijo_controles, OPCIONES_EXTRA_LISTA_TIPOS_TODOS_NINGUNO);
            $boton_ejecutar = dame_boton_formulario($sufijo_controles, $this->idiomas->_("Ejecutar"));

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-monitorizacion-ejecucion-manual-procesado",
                $this->idiomas->_("Ejecución manual de procesado de datos"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_dato" => "desplegable-simple margenes-verticales",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_EJECUCION_MANUAL_PROCESADO)
            );
			$tabla->anyade_fila("ejecucion-manual-procesado", array(
                $control_lista_tipos_ejecucion_procesado,
                $control_lista_clases_sensor,
                $control_lista_tipos_sensor,
                $boton_ejecutar), $params_fila);

            return ($tabla->dame_tabla());
		}


        function dame_herramientas_operaciones_datos_sensores_procesado()
		{
            // Se recuperan los controles a mostrar
            $boton_eliminar_operaciones_datos_sensores = "<br/><button id='boton_monitorizacion_eliminar_operaciones_datos_sensores' class='btn-mini btn btn-success'>".$this->idiomas->_("Eliminar operaciones de datos de sensores")."</button><br/><br/>";
            $boton_pausar_procesado = "<br/><button id='boton_monitorizacion_pausar_procesado' class='btn-mini btn btn-success'>".$this->idiomas->_("Pausar procesado de datos")."</button><br/><br/>";
            $boton_reiniciar_procesado = "<br/><button id='boton_monitorizacion_reanudar_procesado' class='btn-mini btn btn-success'>".$this->idiomas->_("Reanudar procesado de datos")."</button><br/><br/>";
            $botones = array(
                $boton_eliminar_operaciones_datos_sensores,
                $boton_pausar_procesado,
                $boton_reiniciar_procesado);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-monitorizacion-herramientas-operaciones-datos-sensores",
                $this->idiomas->_("Herramientas de operaciones de datos de sensores"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_fila" => "botones-herramientas",
                "clase_dato" => "boton-herramientas",
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_HERRAMIENTAS_OPERACIONES_DATOS_SENSORES
            );
			$tabla->anyade_fila("botones-herramientas-operaciones-datos-sensores", $botones, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_tiempos_ejecucion_procesado()
		{
            $idiomas = new Idiomas();

            // Se recuperan los controles a mostrar
            $sufijo_controles = "monitorizacion_tiempos_ejecucion_procesado";

            $control_lista_tipos_ejecucion_procesado = dame_control_lista_tipos_ejecucion_procesado(
                $sufijo_controles,
                OPCIONES_EXTRA_LISTA_TIPOS_EJECUCIONES_PROCESADO_SIN_OPCIONES_EXTRA,
                $idiomas->_("Tipo de ejecución"));
            $control_lista_clases_sensor = dame_control_lista_clases_sensor(
                $sufijo_controles,
                OPCIONES_EXTRA_LISTA_CLASES_NINGUNA,
                true,
                true,
                $idiomas->_("Clase de sensor"));
            $control_lista_tipos_sensor = $this->dame_control_lista_tipos_sensor($sufijo_controles, OPCIONES_EXTRA_LISTA_TIPOS_NINGUNO);
            $control_lista_granularidades = $this->dame_control_lista_granularidades_ejecucion_procesado($sufijo_controles, OPCIONES_EXTRA_LISTA_GRANULARIDADES_NINGUNA);
            $opciones = array(
                $control_lista_tipos_ejecucion_procesado,
                $control_lista_clases_sensor,
                $control_lista_tipos_sensor,
                $control_lista_granularidades);
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                PERIODO_DEFECTO_MONITORIZACION_TIEMPO_EJECUCION_PROCESADO,
                $opciones,
                array());

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-monitorizacion-tiempos-ejecucion-procesado",
                $this->idiomas->_("Tiempos de ejecución de procesado de datos"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_TIEMPOS_EJECUCION_PROCESADO),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_TIEMPOS_EJECUCION_PROCESADO
            );
			$tabla->anyade_fila("fechas-monitorizacion", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_informacion_tiempos_ejecucion_procesado();
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_tabla_alarmas()
		{
            $contenido = "<div id='tablaAlarmas'>".Alarma::dame_tabla_alarmas(
                MODULO_MONITORIZACION,
                "",
                NULL,
                NULL)."</div>";
			return ($contenido);
		}


        function dame_tabla_acciones_usuario()
		{
            $contenido = "<div id='tablaAccionesUsuario'>".AccionUsuario::dame_tabla_acciones(
                MODULO_MONITORIZACION,
                "",
                NULL,
                NULL)."</div>";
			return ($contenido);
		}


        //
        // Funciones auxiliares para obtener el contenido de las secciones
        //


        function dame_tabla_filtro_historico_procesado()
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "monitorizacion_filtro_historico_procesado";

            $filtro = $this->dame_filtro_tipo_ejecucion_procesado_clase_tipo_sensor_granularidad_fechas(
                $sufijo_controles,
                PERIODO_DEFECTO_MONITORIZACION_HISTORICO_PROCESADO);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-monitorizacion-filtro-historico-procesado",
                $this->idiomas->_("Filtro de histórico de procesado de datos"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "controles-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_HISTORICO_PROCESADO),
                "numero_columnas" => NUMERO_COLUMNAS_FILTRO_HISTORICO_PROCESADO,
            );
			$tabla->anyade_fila("filtro-historico-procesado", $filtro, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_filtro_tipo_ejecucion_procesado_clase_tipo_sensor_granularidad_fechas($id_controles, $periodo)
        {
            $idiomas = new Idiomas();

            $control_lista_tipos_ejecucion_procesado = dame_control_lista_tipos_ejecucion_procesado(
                $id_controles,
                OPCIONES_EXTRA_LISTA_TIPOS_EJECUCIONES_PROCESADO_TODAS,
                $idiomas->_("Tipo de ejecución"));
            $control_lista_clases_sensor = dame_control_lista_clases_sensor(
                $id_controles,
                OPCIONES_EXTRA_LISTA_CLASES_TODAS_NINGUNA,
                true,
                true,
                $idiomas->_("Clase de sensor"));
            $control_lista_tipos_sensor = $this->dame_control_lista_tipos_sensor($id_controles, OPCIONES_EXTRA_LISTA_TIPOS_TODOS_NINGUNO);
            $control_lista_granularidades = $this->dame_control_lista_granularidades_ejecucion_procesado($id_controles, OPCIONES_EXTRA_LISTA_GRANULARIDADES_TODAS);
            $control_fecha_inicio = dame_control_fecha_inicio(
                $id_controles,
                $idiomas->_("Inicio"),
                "00:00",
                $periodo);
            $control_fecha_fin = dame_control_fecha_fin(
                $id_controles,
                $idiomas->_("Fin"),
                "23:59",
                "");
            $boton = dame_boton_formulario($id_controles, $idiomas->_("Filtrar"));

            $controles = array(
                $control_lista_tipos_ejecucion_procesado,
                $control_lista_clases_sensor,
                $control_lista_tipos_sensor,
                $control_lista_granularidades,
                $control_fecha_inicio,
                $control_fecha_fin,
                $boton
            );
            return ($controles);
        }


        function dame_control_lista_tipos_sensor($id_controles, $opciones_extra)
        {
            $idiomas = new Idiomas();

            $control_lista_tipos .= "<div id='etiqueta_tipo_sensor_".$id_controles."'>".$idiomas->_("Tipo de sensor").": "."</div>";
            $control_lista_tipos .= "<select id='tipo_sensor_".$id_controles."' class='filtro-desplegable'>";
            if (($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_TODOS) || ($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_TODOS_NINGUNO))
            {
                $control_lista_tipos .= "<option value=".TIPO_TODOS.">".$idiomas->_("Todos")."</option>";
            }
            if (($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_NINGUNO) || ($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_TODOS_NINGUNO))
            {
                $control_lista_tipos .= "<option value=".TIPO_NINGUNO.">".$idiomas->_("Ninguno")."</option>";
            }
            $nombre_tipo_sensor = NodoSensor::dame_descripcion_tipo_sensor(TIPO_SENSOR_PROCESADO);
            $control_lista_tipos .= "<option value='".TIPO_SENSOR_PROCESADO."'>".htmlspecialchars($nombre_tipo_sensor, ENT_QUOTES)."</option>";
            $control_lista_tipos .= "</select>";

            return ($control_lista_tipos);
        }


        function dame_control_lista_granularidades_ejecucion_procesado($id_controles, $opciones_extra)
        {
            $idiomas = new Idiomas();

            $control_lista_granularidades = "<div id='etiqueta_granularidad_".$id_controles."'>".$idiomas->_("Granularidad").": "."</div>";
            $control_lista_granularidades .= "<select id='granularidad_".$id_controles."' class='filtro-desplegable'>";
            if ($opciones_extra == OPCIONES_EXTRA_LISTA_GRANULARIDADES_TODAS)
            {
                $control_lista_granularidades .= "<option value=".GRANULARIDAD_TODAS.">".$idiomas->_("Todas")."</option>";
            }
            if ($opciones_extra == OPCIONES_EXTRA_LISTA_GRANULARIDADES_NINGUNA)
            {
                $control_lista_granularidades .= "<option value=".GRANULARIDAD_NINGUNA.">".$idiomas->_("Ninguna")."</option>";
            }
            $granularidades = array(GRANULARIDAD_CUARTOHORARIA, GRANULARIDAD_HORARIA);
            foreach ($granularidades as $granularidad)
            {
                $descripcion_granularidad = dame_descripcion_granularidad($granularidad);
                $control_lista_granularidades .= "<option value='".$granularidad."'>".$descripcion_granularidad."</option>";
            }
            $control_lista_granularidades .= "
                </select>";

            return ($control_lista_granularidades);
        }


        function dame_tabla_filtro_alarmas()
		{
            // Se recuperan los controles a mostrar
            $filtro = dame_filtro_texto_fechas(
                "monitorizacion_filtro_alarmas",
                "00:00",
                "23:59",
                $this->idiomas->_("Red, origen y descripción"),
                PERIODO_DEFECTO_MONITORIZACION_ALARMAS,
                "");

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-monitorizacion-filtro-alarmas",
                $this->idiomas->_("Filtro de alarmas"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_ALARMAS_MONITORIZACION),
            );
			$tabla->anyade_fila("filtro-alarmas", $filtro, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_tabla_filtro_acciones_usuario()
		{
            // Se recuperan los controles a mostrar
            $filtro = dame_filtro_texto_fechas(
                "monitorizacion_filtro_acciones_usuario",
                "00:00",
                "23:59",
                $this->idiomas->_("Red, usuario, tipo y objeto"),
                PERIODO_DEFECTO_MONITORIZACION_ACCIONES_USUARIO,
                "");
            $boton_exportar = dame_boton_formulario("monitorizacion_exportar_acciones_usuario", $this->idiomas->_("Exportar acciones"));
            array_push($filtro, $boton_exportar);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-monitorizacion-filtro-acciones-usuario",
                $this->idiomas->_("Filtro de acciones"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_ACCIONES_USUARIO_MONITORIZACION),
                "numero_columnas" => NUMERO_COLUMNAS_FILTRO_ACCIONES_USUARIO_MONITORIZACION
            );
			$tabla->anyade_fila("filtro-acciones-usuario", $filtro, $params_fila);

			return ($tabla->dame_tabla());
		}
    }
?>
