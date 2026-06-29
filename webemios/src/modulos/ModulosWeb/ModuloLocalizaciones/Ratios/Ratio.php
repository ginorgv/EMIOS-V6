<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');


	// Clase que representa un ratio
	class Ratio
	{
        // Funciones estáticas de ratio


        // Devuelve la cabecera para la tabla de ratios
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
				$idiomas->_("Nombre"),
                $idiomas->_("Unidad de medida"),
                $idiomas->_("Tipo"),
                $idiomas->_("Valor o sensor por defecto")
			));
        }


        // Devuelve la consulta para la tabla de ratios
        static function dame_consulta_ratios($filtro)
        {
            $consulta = "
                SELECT *
                FROM ratios
                WHERE
                    (red = '".$_SESSION["id_red"]."')";
            if ($filtro != "")
            {
                $campos = array("nombre");
                $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
            }
            $consulta .= "
                ORDER BY nombre ASC";
            return ($consulta);
        }


        // Devuelve la tabla de ratios
        static function dame_tabla_ratios($filtro)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $administracion_ratios = Ratio::dame_administracion_ratios();
            if ($administracion_ratios == true)
            {
                $boton_anyadir_ratio = "<i id='anyade_modifica_ratio' class='icon-plus color-blanco boton_localizaciones_mostrar_ventana_anyadir_modificar_ratio boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_ratio);
            }
            $boton_actualizar_tabla_ratios = "<i id='actualiza_ratios' class='icon-refresh color-blanco boton_localizaciones_actualizar_tabla_ratios boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_ratios);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_RATIOS,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_RATIOS),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-localizaciones-ratios",
                $idiomas->_("Ratios"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = Ratio::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de los ratios a la tabla y el pie de tabla
            $consulta = Ratio::dame_consulta_ratios($filtro);
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }

            // Se añaden los ratios
            $numero_ratios = 0;
            while ($fila = $res->dame_siguiente_fila())
            {
                $ratio = new Ratio($fila);

                $params_fila = array(
                    "opciones" => $ratio->dame_opciones_tabla()
                );
                $tabla->anyade_fila(
                    "datosRatio__".$fila['id'],
                    $ratio->dame_datos_tabla(),
                    $params_fila
                );
                $numero_ratios += 1;
            }
            $tabla->anyade_pie($idiomas->_("Ratios").": ".$numero_ratios);

            return ($tabla->dame_tabla());
        }


        // Miembros de ratio


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
            $unidad_medida = $icono_dato_erroneo;
            $descripcion_tipo = $icono_dato_erroneo;
            $valor_sensor_defecto = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // Unidad de medida
                $unidad_medida = htmlspecialchars($this->params["unidad_medida"], ENT_QUOTES);

                // Descripción de tipo y valor o sensor por defecto
                $tipo = $this->params["tipo"];
                $descripcion_tipo = Ratio::dame_descripcion_tipo_ratio($tipo);
                switch ($tipo)
                {
                    case TIPO_RATIO_FIJO:
                    {
                        $valor_defecto = $this->params["valor_defecto"];
                        if ($valor_defecto == 0)
                        {
                            $valor_sensor_defecto = $this->idiomas->_("Ninguno");
                        }
                        else
                        {
                            $valor_sensor_defecto = formatea_numero($valor_defecto, 2);
                        }
                        break;
                    }
                    case TIPO_RATIO_VARIABLE:
                    {
                        $id_sensor_defecto = $this->params["sensor_defecto"];
                        $valor_sensor_defecto = dame_nombre_sensor($id_sensor_defecto);
                        break;
                    }
                }
                $valor_sensor_defecto = htmlspecialchars($valor_sensor_defecto, ENT_QUOTES);
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
                $unidad_medida,
                $descripcion_tipo,
                $valor_sensor_defecto
			));
		}


        function dame_opciones_tabla()
		{
            $nombre = htmlspecialchars($this->params['nombre'], ENT_QUOTES);

            $opciones = array();
            $administracion_ratios = Ratio::dame_administracion_ratios();
            if ($administracion_ratios == true)
            {
                $editar = "<i id='anyade_modifica_ratio__".$this->id."' ".
                    "class='icon-pencil color-gris boton_localizaciones_mostrar_ventana_anyadir_modificar_ratio boton-tabla-datos'></i>";
                $borrar = "<i id='elimina_ratio__".$this->id."' nombre_ratio='".$nombre."' ".
                    "class='icon-remove color-gris boton_localizaciones_eliminar_ratio boton-tabla-datos'></i>";
                $opciones = array($borrar, $editar);
            }

			return ($opciones);
		}


		function dame_detalles_tabla()
		{
            $info = "";
            $administracion_ratios = Ratio::dame_administracion_ratios();

            // Identificador
            if ($administracion_ratios == true)
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

            // Sustituir unidad de medida del sensor
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Sustituir unidad de medida del sensor").": ".dame_descripcion_valores_si_no($this->params["sustituir_unidad_medida_sensor"])."<br/>";

            // Clase y campo de sensor (si el tipo de ratio es variable)
            if ($this->params["tipo"] == TIPO_RATIO_VARIABLE)
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Clase de sensor").": ".NodoSensor::dame_descripcion_clase_sensor($this->params["clase_sensor"])."<br/>";
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Campo de sensor").": ".dame_descripcion_campo_clase_sensor($this->params["clase_sensor"], $this->params["campo_sensor"])."<br/>";
            }

            return ($info);
		}


        //
        // Funciones de tipos de ratio
        //


        // Devuelve los tipos de ratios
        static function dame_tipos_ratio()
        {
            $tipos_ratio = array();
            array_push($tipos_ratio, TIPO_RATIO_FIJO);
            array_push($tipos_ratio, TIPO_RATIO_VARIABLE);
            return ($tipos_ratio);
        }


        // Devuelve la descripción del tipo del ratio
        static function dame_descripcion_tipo_ratio($tipo_ratio)
        {
            switch ($tipo_ratio)
            {
                case TIPO_RATIO_FIJO:
                {
                    $descripcion = "Fijo";
                    break;
                }
                case TIPO_RATIO_VARIABLE:
                {
                    $descripcion = "Variable";
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


        static function dame_administracion_ratios()
        {
            $administracion_ratios = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
                ($_SESSION["parametros_modulo_localizaciones"]["administracion_localizaciones"] == VALOR_SI);
            return ($administracion_ratios);
        }
	}
?>
