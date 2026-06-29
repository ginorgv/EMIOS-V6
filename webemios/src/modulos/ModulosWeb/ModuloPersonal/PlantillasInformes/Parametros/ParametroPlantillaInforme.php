<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/PlantillaInforme.php');


    // Constantes

    // Indices de parámetros de tipo de parámetro
	define("INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR_CLASE_SENSOR", 0);
    define("INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR_ID_PARAMETRO_SENSOR_ASOCIADO", 1);

    define("INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES_CLASE_SENSOR", 0);

    define("INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR_CLASE_ACTUADOR", 0);

    define("INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES_CLASE_ACTUADOR", 0);


    // Clase que representa un parámetro de una plantilla de informe
	class ParametroPlantillaInforme
	{
        // Funciones estáticas de acción


        // Devuelve la cabecera para la tabla de parámetros
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
				$idiomas->_("Nombre"),
                $idiomas->_("Tipo"),
                $idiomas->_("Parámetros"),
			));
        }


        // Devuelve la consulta para la tabla de parámetros
        static function dame_consulta_parametros($id_plantilla_informe)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta = "
                SELECT *
                FROM parametros_plantillas_informes
                WHERE
                    plantilla_informe = '".$bd_red->_($id_plantilla_informe)."'
                ORDER BY posicion ASC";
            return ($consulta);
        }


        // Devuelve la tabla de parámetros
        static function dame_tabla_parametros($id_plantilla_informe)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se recuperan los parámetros
            $consulta = ParametroPlantillaInforme::dame_consulta_parametros($id_plantilla_informe);
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_parametros = $res->dame_numero_filas();

            // Opciones de la tabla
            $opciones = array();
            $administracion_plantillas_informes = PlantillaInforme::dame_administracion_plantillas_informes();
            $permitir_adicion_parametros = false;
            if ($administracion_plantillas_informes == true)
            {
                $boton_modificar_posiciones_parametros = "<i id='modifica_posiciones_parametros__".$id_plantilla_informe."' class='icon-pencil color-blanco boton_personal_mostrar_ventana_modificar_posiciones_parametros_plantilla_informe boton-tabla-datos'></i>";
                $boton_anyadir_parametro = "<i id='anyade_modifica_parametro__".$id_plantilla_informe."' class='icon-plus color-blanco boton_personal_mostrar_ventana_anyadir_modificar_parametro_plantilla_informe boton-tabla-datos'></i>";
                array_push($opciones, $boton_modificar_posiciones_parametros);
                array_push($opciones, $boton_anyadir_parametro);
            }
            $boton_actualizar_tabla_parametros = "<i id='actualiza_tabla_parametros__".$id_plantilla_informe."' class='icon-refresh color-blanco boton_personal_actualizar_tabla_parametros_plantilla_informe boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_parametros);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_PARAMETROS_PLANTILLAS_INFORMES,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_PARAMETROS_PLANTILLAS_INFORMES),
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-parametros-plantillas-informes",
                $idiomas->_("Parámetros"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = ParametroPlantillaInforme::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada uno de los parámetros a la tabla y el pie de tabla
            while ($fila = $res->dame_siguiente_fila())
            {
                $parametro_plantilla_informe = new ParametroPlantillaInforme($fila);
                $params_fila = array(
                    "opciones" => $parametro_plantilla_informe->dame_opciones_tabla($permitir_adicion_parametros),
                );
                $tabla->anyade_fila(
                    "datosParametroPlantillaInforme__".$id_plantilla_informe."__".$fila['id'],
                    $parametro_plantilla_informe->dame_datos_tabla(),
                    $params_fila
                );
            }
            $tabla->anyade_pie($idiomas->_("Parámetros").": ".$numero_parametros);

            return ($tabla->dame_tabla(false));
        }


        // Miembros de parámetro


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
            $descripcion_parametros_tipo = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Descripciones de tipo y de parámetros de tipo
                $tipo = $this->params["tipo"];
                $parametros_tipo = $this->params["parametros_tipo"];
                $descripcion_tipo = ParametroPlantillaInforme::dame_descripcion_tipo_parametro($tipo);
                $descripcion_parametros_tipo = htmlspecialchars(ParametroPlantillaInforme::dame_descripcion_parametros_tipo_parametro($tipo, $parametros_tipo), ENT_QUOTES);
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
                $descripcion_parametros_tipo
            ));
		}


        function dame_opciones_tabla($permitir_adicion_parametros)
		{
            $nombre = htmlspecialchars($this->params['nombre'], ENT_QUOTES);

            $opciones = array();
            $administracion_plantillas_informes = PlantillaInforme::dame_administracion_plantillas_informes();
            if ($administracion_plantillas_informes == true)
            {
                $editar = "<i id='anyade_modifica_parametro__".$this->params['plantilla_informe']."__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' ".
                    "class='icon-pencil color-gris boton_personal_mostrar_ventana_anyadir_modificar_parametro_plantilla_informe boton-tabla-datos'></i>";
                $duplicar = "<i id='anyade_modifica_parametro__".$this->params['plantilla_informe']."__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' ".
                        "class='icon-copy color-gris boton_personal_mostrar_ventana_anyadir_modificar_parametro_plantilla_informe boton-tabla-datos'></i>";
                $borrar = "<i id='elimina_parametro__".$this->params['plantilla_informe']."__".$this->id."' nombre_parametro='".$nombre."' ".
                    "class='icon-remove color-gris boton_personal_eliminar_parametro_plantilla_informe boton-tabla-datos'></i>";
                if ($permitir_adicion_parametros == true)
                {
                    $opciones = array($borrar, $duplicar, $editar);
                }
                else
                {
                    $opciones = array($borrar, $editar);
                }
            }
			return ($opciones);
		}


        //
        // Funciones de tipos de parámetro
        //


        static function dame_descripcion_tipo_parametro($tipo_parametro)
        {
            switch ($tipo_parametro)
            {
                case TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR:
                {
                    $descripcion_tipo_parametro = "Sensor";
                    break;
                }
                case TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES:
                {
                    $descripcion_tipo_parametro = "Grupo de sensores";
                    break;
                }
                case TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR:
                {
                    $descripcion_tipo_parametro = "Actuador";
                    break;
                }
                case TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
                {
                    $descripcion_tipo_parametro = "Grupo de actuadores";
                    break;
                }
                case TIPO_PARAMETRO_PLANTILLA_INFORME_LINEA_BASE:
                {
                    $descripcion_tipo_parametro = "Línea base";
                    break;
                }
                case TIPO_PARAMETRO_PLANTILLA_INFORME_PROYECTO:
                {
                    $descripcion_tipo_parametro = "Proyecto";
                    break;
                }
                default:
                {
                    $descripcion_tipo_parametro = "Desconocido";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_tipo_parametro));
        }


        static function dame_descripcion_parametros_tipo_parametro($tipo, $cadena_parametros_tipo)
        {
            $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
            switch ($tipo)
            {
                case TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR:
                {
                    $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR_CLASE_SENSOR];
                    $id_parametro_sensor_asociado = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR_ID_PARAMETRO_SENSOR_ASOCIADO];
                    $descripcion_clase_sensor = NodoSensor::dame_descripcion_clase_sensor($clase_sensor);
                    $descripcion_parametros_tipo = $descripcion_clase_sensor;
                    if ($id_parametro_sensor_asociado != ID_NINGUNO)
                    {
                        $fila_parametro_sensor_asociado = dame_fila_parametro_plantilla_informe($id_parametro_sensor_asociado);
                        $nombre_parametro_sensor_asociado = $fila_parametro_sensor_asociado["nombre"];
                        $descripcion_parametros_tipo .= " (".$nombre_parametro_sensor_asociado.")";
                    }
                    break;
                }
                case TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES:
                {
                    $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES_CLASE_SENSOR];
                    $descripcion_clase_sensor = NodoSensor::dame_descripcion_clase_sensor($clase_sensor);
                    $descripcion_parametros_tipo = $descripcion_clase_sensor;
                    break;
                }
                case TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR:
                {
                    $clase_actuador = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR_CLASE_ACTUADOR];
                    $descripcion_clase_actuador = NodoActuador::dame_descripcion_clase_actuador($clase_actuador);
                    $descripcion_parametros_tipo = $descripcion_clase_actuador;
                    break;
                }
                case TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
                {
                    $clase_actuador = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES_CLASE_ACTUADOR];
                    $descripcion_clase_actuador = NodoActuador::dame_descripcion_clase_actuador($clase_actuador);
                    $descripcion_parametros_tipo = $descripcion_clase_actuador;
                    break;
                }
                case TIPO_PARAMETRO_PLANTILLA_INFORME_LINEA_BASE:
                case TIPO_PARAMETRO_PLANTILLA_INFORME_PROYECTO:
                {
                    $idiomas = new Idiomas();
                    $descripcion_parametros_tipo = $idiomas->_("ND");
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de parámetro desconocido: '".$tipo."'");
                }
            }
            return ($descripcion_parametros_tipo);
        }
    }
?>
