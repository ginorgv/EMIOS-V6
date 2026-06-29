<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_ficheros.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/util_facturas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Espanya/util_facturas_electricidad_Espanya.php');


    // Constantes

    // Estados de validación de factura eléctrica
    define("ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_CORRECTA", "CORRECTA");
    define("ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_INCORRECTA", "INCORRECTA");
    define("ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_RECUPERACION_SENSOR_ENERGIA_ACTIVA", "ERROR_RECUPERACION_SENSOR_ENERGIA_ACTIVA");
    define("ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_RECUPERACION_INFO_MONITORIZACION", "ERROR_RECUPERACION_INFO_MONITORIZACION");
    define("ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_MODIFICACION_DATOS_FACTURA_INFO_MONITORIZACION", "ERROR_MODIFICACION_DATOS_FACTURA_INFO_MONITORIZACION");
    define("ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_RECUPERACION_DATOS_MONITORIZACION", "ERROR_RECUPERACION_DATOS_MONITORIZACION");
    define("ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_SIN_DATOS_CONSUMO_MONITORIZACION", "ERROR_SIN_DATOS_CONSUMO_MONITORIZACION");
    define("ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_DATOS_CONSUMO_MONITORIZACION_INSUFICIENTES", "ERROR_DATOS_CONSUMO_MONITORIZACION_INSUFICIENTES");
    define("ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_VALIDACION_DATOS_FACTURA_MONITORIZACION", "ERROR_VALIDACION_DATOS_FACTURA_MONITORIZACION");

    // Índices de sensores
    define("INDICE_SENSORES_SENSOR_ENERGIA_ACTIVA", 0);
    define("INDICE_SENSORES_SENSOR_ENERGIA_REACTIVA", 1);

    // Índices de resultado de validación
    define("INDICE_RESULTADO_VALIDACION_DATO_FACTURA", 0);
    define("INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION", 1);
    define("INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR", 2);
    define("INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA", 3);


    // Clase validación factura eléctrica de España
	class ValidacionFacturaElectrica_Espanya
	{
        // Devuelve la cabecera para la validación
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
                $idiomas->_("Fecha"),
                $idiomas->_("Sensor de energía activa"),
                $idiomas->_("Fecha de inicio"),
                $idiomas->_("Fecha de fin"),
                $idiomas->_("Estado"),
			));
        }


        // Devuelve la consulta para las validaciones
        static function dame_consulta_validaciones_facturas_electricas($cadena_fecha_hora_inicio_base_datos_utc, $cadena_fecha_hora_fin_base_datos_utc)
        {
            $bd_datos = BaseDatosDatos::dame_base_datos();

            $consulta = "
                SELECT
                    id,
                    hora,
                    SUBSTRING_INDEX(SUBSTRING_INDEX(sensores, '".SEPARADOR_PARAMETROS_SIMPLES."', ".(INDICE_SENSORES_SENSOR_ENERGIA_ACTIVA + 1)."), '".SEPARADOR_PARAMETROS_SIMPLES."', -1) AS sensor_energia_activa,
                    fecha_inicio,
                    fecha_fin,
                    estado
                FROM ".TABLA_VALIDACIONES_FACTURAS_ELECTRICAS_ESPANYA."
                WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                    AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')
                ORDER BY
                    hora DESC,
                    fecha_inicio DESC";
			return ($consulta);
        }


        // Devuelve la tabla de validaciones
        static function dame_tabla_validaciones_facturas_electricas(
            $filtro = null,
            $cadena_fecha_hora_inicio_base_datos_utc = null,
            $cadena_fecha_hora_fin_base_datos_utc = null,
            &$limite_elementos_tabla_historico_superado = null)
		{
            $idiomas = new Idiomas();
            $bd_datos = BaseDatosDatos::dame_base_datos();

            // Se crea la tabla
            $params_tabla = array(
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_VALIDACIONES_FACTURAS_ELECTRICAS_ESPANYA,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_VALIDACIONES_FACTURAS_ELECTRICAS_ESPANYA),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tablaValidacionesFacturas",
                $idiomas->_("Validaciones de facturas y cierres"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = ValidacionFacturaElectrica_Espanya::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las validaciones a la tabla y el pie de tabla
            // (si no hay fechas, se devuelve la tabla vacía)
            $numero_validaciones = 0;
            if (($cadena_fecha_hora_inicio_base_datos_utc !== NULL) && ($cadena_fecha_hora_fin_base_datos_utc !== NULL))
            {
                $consulta_validaciones = ValidacionFacturaElectrica_Espanya::dame_consulta_validaciones_facturas_electricas(
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc);
                $res_validaciones = $bd_datos->ejecuta_consulta($consulta_validaciones);
                if ($res_validaciones == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_validaciones."'");
                }

                // Identificadores de sensores del usuario actual
                $mostrar_todos_sensores = dame_mostrar_todos_sensores();
                if ($mostrar_todos_sensores == false)
                {
                    $ids_sensores_usuario = dame_ids_sensores_usuario_actual(true);
                    $nombres_sensores_usuario = dame_nombres_sensores($ids_sensores_usuario);
                }

                $limite_elementos_tabla_historico_superado = false;
                while (($fila_validacion = $res_validaciones->dame_siguiente_fila()) && ($limite_elementos_tabla_historico_superado == false))
                {
                    // Se realiza el filtrado (no se hace en la consulta porque se filtra por la descripción del estado, no por el estado en la base de datos)
                    $nombre_sensor_energia_activa = $fila_validacion["sensor_energia_activa"];
                    $descripcion_estado = ValidacionFacturaElectrica_Espanya::dame_descripcion_estado_validacion_factura_electrica_tabla($fila_validacion["estado"]);
                    if (($filtro != "") &&
                        (stripos($nombre_sensor_energia_activa, $filtro) === false) &&
                        (strtolower($descripcion_estado) != strtolower($filtro)))
                    {
                        continue;
                    }

                    if ($numero_validaciones == NUMERO_MAXIMO_ELEMENTOS_TABLAS_HISTORICOS)
                    {
                        $limite_elementos_tabla_historico_superado = true;
                        break;
                    }
                    else
                    {
                        $anyadir_validacion = true;
                        if ($mostrar_todos_sensores == false)
                        {
                            if (in_array($fila_validacion['sensor_energia_activa'], $nombres_sensores_usuario) == false)
                            {
                                $anyadir_validacion = false;
                            }
                        }

                        if ($anyadir_validacion == true)
                        {
                            $validacion = new ValidacionFacturaElectrica_Espanya($fila_validacion);
                            $params_fila = array(
                                "opciones" => $validacion->dame_opciones_tabla()
                            );
                            $tabla->anyade_fila(
                                "datosValidacionFacturaElectrica_Espanya__".$fila_validacion['id'],
                                $validacion->dame_datos_tabla(),
                                $params_fila
                            );
                            $numero_validaciones += 1;
                        }
                    }
                }
            }
            $texto_pie = $idiomas->_("Número de validaciones").": ".$numero_validaciones;
            if ($limite_elementos_tabla_historico_superado == true)
            {
                $texto_pie .= " (".$idiomas->_("límite máximo superado").")";
            }
            $tabla->anyade_pie($texto_pie);

            return ($tabla->dame_tabla());
		}


		// Miembros de validación


        public $idiomas;

        public $id;
        public $params;


        // Funciones de acción


		function __construct($params)
		{
			$this->idiomas = new Idiomas();

			$this->id = $params['id'];
            $this->params = $params;
		}


        // Datos para la tabla
		function dame_datos_tabla()
		{
            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $cadena_fecha_hora_local_local = $icono_dato_erroneo;
            $nombre_sensor_energia_activa = $icono_dato_erroneo;
            $cadena_fecha_inicio_local_local = $icono_dato_erroneo;
            $cadena_fecha_fin_local_local = $icono_dato_erroneo;
            $estado = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $fecha_hora_correcta = false;
            try
            {
                // Conversión de fechas
                $zona_horaria = dame_zona_horaria_local();
                $cadena_fecha_hora_local_utc = convierte_formato_fecha($this->params['hora'], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_fecha_hora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                $fecha_hora_correcta = true;

                // Nombre de sensor de energía activa
                $nombre_sensor_energia_activa = $this->params['sensor_energia_activa'];
                if ($nombre_sensor_energia_activa == "")
                {
                    $nombre_sensor_energia_activa = $this->idiomas->_("Ninguno");
                }
                $nombre_sensor_energia_activa = htmlspecialchars($nombre_sensor_energia_activa, ENT_QUOTES);

                // Conversión de fechas
                $cadena_fecha_inicio_local_local = convierte_formato_fecha($this->params['fecha_inicio'], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
                $cadena_fecha_fin_local_local = convierte_formato_fecha($this->params['fecha_fin'], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);

                // Estado
                $icono_estado = ValidacionFacturaElectrica_Espanya::dame_icono_estado_validacion_factura_electrica($this->params['estado']);
                $descripcion_estado = ValidacionFacturaElectrica_Espanya::dame_descripcion_estado_validacion_factura_electrica_tabla($this->params['estado']);
                $estado = $icono_estado." (".$descripcion_estado.")";
            }
            catch (Exception $e)
            {
                // Se añade información de la excepción en el log
                $log = dame_log();
                $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $e);

                // Se añade icono de error en la fecha
                if ($fecha_hora_correcta == true)
                {
                    $cadena_fecha_hora_local_local = "[".$icono_fila_con_errores."] ".$cadena_fecha_hora_local_local;
                }
            }

            // Se devuelven los datos de la tabla
			return (array(
				$cadena_fecha_hora_local_local,
                $nombre_sensor_energia_activa,
                $cadena_fecha_inicio_local_local,
                $cadena_fecha_fin_local_local,
                $estado
			));
		}


        // Devuelve las opciones para mostrar en la tabla
		function dame_opciones_tabla()
		{
            $nombre_sensor_energia_activa = htmlspecialchars($this->params['sensor_energia_activa'], ENT_QUOTES);

            $opciones = array();
            $administracion_validaciones_facturas = dame_administracion_validaciones_facturas();
            if ($administracion_validaciones_facturas == true)
            {
                // Conversión de fechas
                $zona_horaria = dame_zona_horaria_local();
                $cadena_fecha_hora_local_utc = convierte_formato_fecha($this->params['hora'], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_fecha_hora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                $borrar = "<i id='elimina__".$this->id."' hora='".$cadena_fecha_hora_local_local."' nombre_sensor='".$nombre_sensor_energia_activa."' ".
                    "class='icon-remove color-gris boton_smartmeter_eliminar_validacion_factura boton-tabla-datos'></i>";
                $opciones = array($borrar);
            }

			return ($opciones);
		}


        // Devuelve las herramientas de los detalles de la tabla
        function dame_herramientas_detalles_tabla()
		{
            // Modificación de observaciones de validación de factura eléctrica
            $administracion_validaciones_facturas = dame_administracion_validaciones_facturas();
            if ($administracion_validaciones_facturas == true)
            {
                // Texto del botón
                if ($this->params['observaciones'] == "")
                {
                    $texto_boton_modificar_observaciones = $this->idiomas->_("Añadir observaciones");
                }
                else
                {
                    $texto_boton_modificar_observaciones = $this->idiomas->_("Modificar observaciones");
                }

                $herramientas .= "
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button id='boton_modificar_observaciones_validacion_factura__".$this->id."' class='btn-mini btn btn-success boton_smartmeter_mostrar_ventana_modificar_observaciones_validacion_factura'>".
                            $texto_boton_modificar_observaciones."
                        </button>
                    </span>";
            }

            return ($herramientas);
		}


        // Devuelve los detalles de la tabla
        function dame_detalles_tabla()
		{
            $info = "";

            // Observaciones
            if ($this->params['observaciones'] != "")
			{
				$info .= "<i class='icon-info-sign color-azul'></i> ".
                    $this->idiomas->_("Observaciones").": ".htmlspecialchars($this->params['observaciones'], ENT_QUOTES)."<br/>";
                $info .= "<br/>";
			}

            // Parámetros y resultado de la acción
            if ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR)
            {
                // Estado de validación
                $estado_validacion = $this->params["estado"];

                // Si se ha podido recuperar la información de monitorización
                if (($estado_validacion != ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_RECUPERACION_SENSOR_ENERGIA_ACTIVA) &&
                    ($estado_validacion != ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_RECUPERACION_INFO_MONITORIZACION))
                {
                    // Tarifa eléctrica
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Tarifa eléctrica").": ".htmlspecialchars($this->params["tarifa_electrica"], ENT_QUOTES)."<br/>";

                    // CUPS (si existe)
                    if ($this->params["cups"] != "")
                    {
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("CUPS").": ".htmlspecialchars($this->params["cups"], ENT_QUOTES)."<br/>";
                    }

                    // Errores máximos permitido en la validación
                    $errores_maximos_validacion = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["errores_maximos"]);
                    $error_maximo_energia = $errores_maximos_validacion[0];
                    $error_maximo_potencia = $errores_maximos_validacion[1];
                    $error_maximo_otros_conceptos_coste_total = $errores_maximos_validacion[2];
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Errores máximos permitidos").":";
                    $info .= "<ul>";
                    $info .= "<li>".$this->idiomas->_("Energía").": ".formatea_numero($error_maximo_energia, 2)." "."%"."</li>";
                    $info .= "<li>".$this->idiomas->_("Potencia").": ".formatea_numero($error_maximo_potencia, 2)." "."%"."</li>";
                    if ($this->params["tipo_fichero"] == TIPO_FICHERO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_GEMWEB)
                    {
                        $info .= "<li>".$this->idiomas->_("Otros conceptos y coste total").": ".formatea_numero($error_maximo_otros_conceptos_coste_total, 2)." "."%"."</li>";
                    }
                    $info .= "</ul>";

                    // Sensores asociados al sensor de energía activa
                    $nombres_sensores_validacion = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["sensores"]);
                    if (count($nombres_sensores_validacion) == 1)
                    {
                        $info .= "<i class='icon-warning-sign color-rojo'></i> ".
                            $this->idiomas->_("No hay sensor de energía reactiva asociado")."<br/>";
                    }
                    else
                    {
                        $nombre_sensor_energia_reactiva = $nombres_sensores_validacion[1];
                        $info .= "<i class='icon-info-sign color-azul'></i> ".
                            $this->idiomas->_("Sensor de energía reactiva").": ".htmlspecialchars($nombre_sensor_energia_reactiva, ENT_QUOTES)."<br/>";
                    }
                    $info .= "<br/>";
                }

                // Información de la validación
                switch ($estado_validacion)
                {
                    case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_CORRECTA:
                    case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_INCORRECTA:
                    {
                        $descripcion_resultado_validacion = ValidacionFacturaElectrica_Espanya::dame_descripcion_resultado_validacion_factura_electrica($this->params["resultado"]);
                        $info .= $descripcion_resultado_validacion;
                        $info .= "<br/>";
                        break;
                    }
                    default:
                    {
                        $descripcion_error_validacion = ValidacionFacturaElectrica_Espanya::dame_descripcion_estado_validacion_factura_electrica_detalles_tabla($estado_validacion);
                        $info .= "<i class='icon-warning-sign color-rojo'></i> ".$descripcion_error_validacion."<br/>";
                        $info .= "<br/>";
                        break;
                    }
                }

                // Información de la validación
                if ($this->params["usuario"] !== NULL)
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Usuario").": ".htmlspecialchars($this->params["usuario"], ENT_QUOTES)."<br/>";
                }
                else
                {
                    $info .= "<i class='icon-info-sign color-azul'></i> ".
                        $this->idiomas->_("Validación automática")."<br/>";
                }
                $tipo_fichero = $this->params["tipo_fichero"];
                $info .= "<i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("Tipo de fichero").": ".
                    dame_descripcion_tipo_fichero_validacion_facturas_electricidad_Espanya($tipo_fichero)."<br/>";
                switch ($tipo_fichero)
                {
                    case TIPO_FICHERO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_CIERRE_FACTURACION_ENERGY_MINUS:
                    {
                        $info .= "<ul>";
                        $info .= "<li>".$this->idiomas->_("Nombre de fichero").": ".$this->params["identificador_factura"]."</li>";
                        $info .= "</ul>";
                        break;
                    }
                    case TIPO_FICHERO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ATR_DISTRIBUIDORA_XML:
                    {
                        $info .= "<ul>";
                        $info .= "<li>".$this->idiomas->_("CUPS").": ".$this->params["identificador_factura"]."</li>";
                        $info .= "</ul>";
                        break;
                    }
                    case TIPO_FICHERO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_GEMWEB:
                    {
                        $info .= "<ul>";
                        $info .= "<li>".$this->idiomas->_("Identificador de factura").": ".$this->params["identificador_factura"]."</li>";
                        $info .= "</ul>";
                        break;
                    }
                }
            }
            else
            {
                $info .= "<i class='icon-warning-sign color-rojo'></i> ".
                    $this->idiomas->_("Sin información disponible");
            }

            return ($info);
		}


        //
        // Funciones auxiliares
        //


        // Devuelve la descripción del estado de la validación de la factura eléctrica (para la tabla)
        static function dame_descripcion_estado_validacion_factura_electrica_tabla($estado_validacion)
        {
            switch ($estado_validacion)
            {
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_CORRECTA:
                {
                    $descripcion = "correcta";
                    break;
                }
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_INCORRECTA:
                {
                    $descripcion = "incorrecta";
                    break;
                }
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_RECUPERACION_INFO_MONITORIZACION_CUPS:
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_RECUPERACION_DATOS_MONITORIZACION:
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_SIN_DATOS_CONSUMO_MONITORIZACION:
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_DATOS_CONSUMO_MONITORIZACION_INSUFICIENTES:
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_VALIDACION_DATOS_FACTURA_MONITORIZACION:
                {
                    $descripcion = "error";
                    break;
                }
                default:
                {
                    $descripcion = "desconocido";
                    break;
                }
            }

            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion));
        }


        // Devuelve el icono del estado de la validación de la factura eléctrica
        static function dame_icono_estado_validacion_factura_electrica($estado_validacion)
        {
            $descripcion_estado_validacion = ValidacionFacturaElectrica_Espanya::dame_descripcion_estado_validacion_factura_electrica_tabla($estado_validacion);
            switch ($estado_validacion)
            {
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_CORRECTA:
                {
                    $icono = "<i class='icon-thumbs-up-alt color-verde'></i>";
                    break;
                }
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_INCORRECTA:
                {
                    $icono = "<i class='icon-thumbs-down-alt color-rojo'></i>";
                    break;
                }
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_RECUPERACION_INFO_MONITORIZACION_CUPS:
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_RECUPERACION_DATOS_MONITORIZACION:
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_SIN_DATOS_CONSUMO_MONITORIZACION:
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_DATOS_CONSUMO_MONITORIZACION_INSUFICIENTES:
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_VALIDACION_DATOS_FACTURA_MONITORIZACION:
                {
                    $icono = "<i class='icon-remove-sign color-gris-claro'></i>";
                    break;
                }
                default:
                {
                    $icono = "<i class='icon-question-sign color-gris-claro'></i>";
                    break;
                }
            }
            $icono .= "<texto class='elemento-oculto'>".htmlspecialchars($descripcion_estado_validacion)."</texto></i>";
            return ($icono);
        }


        // Devuelve la descripción del estado de la validación de la factura eléctrica (para los detalles de la tabla)
        static function dame_descripcion_estado_validacion_factura_electrica_detalles_tabla($estado_validacion)
        {
            switch ($estado_validacion)
            {
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_CORRECTA:
                {
                    $descripcion = "Correcta";
                    break;
                }
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_INCORRECTA:
                {
                    $descripcion = "Incorrecta";
                    break;
                }
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_RECUPERACION_SENSOR_ENERGIA_ACTIVA:
                {
                    $descripcion = "Error en la recuperación del sensor de energía activa";
                    break;
                }
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_RECUPERACION_INFO_MONITORIZACION:
                {
                    $descripcion = "Error en la recuperación de información de monitorización del sensor de energía activa";
                    break;
                }
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_MODIFICACION_DATOS_FACTURA_INFO_MONITORIZACION:
                {
                    $descripcion = "Error al modificar los datos de factura con la información de monitorización";
                    break;
                }
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_RECUPERACION_DATOS_MONITORIZACION:
                {
                    $descripcion = "Error en la recuperación de datos de monitorización";
                    break;
                }
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_SIN_DATOS_CONSUMO_MONITORIZACION:
                {
                    $descripcion = "No hay datos de consumo en la monitorización";
                    break;
                }
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_DATOS_CONSUMO_MONITORIZACION_INSUFICIENTES:
                {
                    $descripcion = "Los datos de consumo en la monitorización son insuficientes";
                    break;
                }
                case ESTADO_VALIDACION_FACTURA_ELECTRICA_ESPANYA_ERROR_VALIDACION_DATOS_FACTURA_MONITORIZACION:
                {
                    $descripcion = "Error en la validación de datos de factura con datos de monitorización";
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


        // Devuelve la descripcion del resultado de la validación de la factura eléctrica
        static function dame_descripcion_resultado_validacion_factura_electrica($resultado_validacion_json)
        {
            $idiomas = new Idiomas();

            // Resultado de la validación:
            // - Energía activa
            // - Potencia
            // - Excesos de potencia
            // - Energía reactiva
            // - Otros conceptos y coste total
            $descripcion_resultado_validacion = "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Resultado de la validación")." (".$idiomas->_("datos factura")." / ".$idiomas->_("datos monitorización")."): "."<br/>";
            $resultado_validacion = json_decode($resultado_validacion_json, true);
            $descripcion_resultado_validacion .= "<ul>";
            if (array_key_exists("validacion_correcta_energia_activa", $resultado_validacion) == true)
            {
                $descripcion_resultado_validacion .= "<li>".ValidacionFacturaElectrica_Espanya::dame_descripcion_resultado_validacion_factura_electrica_energia_activa($resultado_validacion)."</li>";
            }
            if (array_key_exists("validacion_correcta_potencia", $resultado_validacion) == true)
            {
                $descripcion_resultado_validacion .= "<li>".ValidacionFacturaElectrica_Espanya::dame_descripcion_resultado_validacion_factura_electrica_potencia($resultado_validacion)."</li>";
            }
            if (array_key_exists("validacion_correcta_excesos_potencia", $resultado_validacion) == true)
            {
                $descripcion_resultado_validacion .= "<li>".ValidacionFacturaElectrica_Espanya::dame_descripcion_resultado_validacion_factura_electrica_excesos_potencia($resultado_validacion)."</li>";
            }
            if (array_key_exists("validacion_correcta_energia_reactiva", $resultado_validacion) == true)
            {
                $descripcion_resultado_validacion .= "<li>".ValidacionFacturaElectrica_Espanya::dame_descripcion_resultado_validacion_factura_electrica_energia_reactiva($resultado_validacion)."</li>";
            }
            if (array_key_exists("validacion_correcta_otros_conceptos_coste_total", $resultado_validacion) == true)
            {
                $descripcion_resultado_validacion .= "<li>".ValidacionFacturaElectrica_Espanya::dame_descripcion_resultado_validacion_factura_electrica_otros_conceptos_coste_total($resultado_validacion)."</li>";
            }
            $descripcion_resultado_validacion .= "</ul>";
            return ($descripcion_resultado_validacion);
        }


        // Devuelve la descripcion del resultado de la validación de la factura eléctrica (energía activa)
        static function dame_descripcion_resultado_validacion_factura_electrica_energia_activa($resultado_validacion)
        {
            $idiomas = new Idiomas();
            $descripcion_resultado_validacion = "";

            // Unidad de medida de coste
            $unidad_medida_coste = $_SESSION["moneda"];

            // Energía activa
            if ($resultado_validacion["validacion_correcta_energia_activa"] == false)
            {
                $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
            }
            $descripcion_resultado_validacion .= $idiomas->_("Energía activa").": ";
            $descripcion_resultado_validacion .= "<ul>";

            // Comprobación de tramos diferentes en consumo de energía activa
            if (array_key_exists("tramos_diferentes_consumo_energia_activa", $resultado_validacion) == true)
            {
                $descripcion_resultado_validacion .= "<li>";
                $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ".
                    $idiomas->_("Los tramos no coinciden en los datos de factura y monitorización")." (".$idiomas->_("consumo").")";
                $descripcion_resultado_validacion .= "</li>";
            }

            // Consumo (por tramo)
            if (array_key_exists("info_consumo_energia_activa", $resultado_validacion) == true)
            {
                $descripcion_resultado_validacion .= "<li>";
                $descripcion_resultado_validacion .= $idiomas->_("Consumo")." (".$idiomas->_("por tramo")."): ";
                $descripcion_resultado_validacion .= "<ul>";
                foreach ($resultado_validacion["info_consumo_energia_activa"] as $numero_tramo => $info_consumo_energia_activa_tramo)
                {
                    $descripcion_resultado_validacion .= "<li>";
                    if ($info_consumo_energia_activa_tramo[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
                    {
                        $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                    }
                    $descripcion_resultado_validacion .= $idiomas->_("Tramo")." ".$numero_tramo.": ".
                        formatea_numero($info_consumo_energia_activa_tramo[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 2)." ".$idiomas->_("kWh")." / ".
                        formatea_numero($info_consumo_energia_activa_tramo[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 2)." ".$idiomas->_("kWh").
                        " (".$idiomas->_("error").": ".formatea_numero($info_consumo_energia_activa_tramo[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
                    $descripcion_resultado_validacion .= "</li>";
                }
                $descripcion_resultado_validacion .= "</ul>";
                $descripcion_resultado_validacion .= "</li>";
            }

            // Coste total
            if (array_key_exists("info_coste_total_energia_activa", $resultado_validacion) == true)
            {
                $info_coste_total_energia_activa = $resultado_validacion["info_coste_total_energia_activa"];
                $descripcion_resultado_validacion .= "<li>";
                if ($info_coste_total_energia_activa[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
                {
                    $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                }
                $descripcion_resultado_validacion .= $idiomas->_("Coste total").": ".
                    $info_coste_total_energia_activa[INDICE_RESULTADO_VALIDACION_DATO_FACTURA]." ".$unidad_medida_coste." / ".
                    $info_coste_total_energia_activa[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION]." ".$unidad_medida_coste.
                    " (".$idiomas->_("error").": ".formatea_numero($info_coste_total_energia_activa[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
                $descripcion_resultado_validacion .= "</li>";
            }

            // Coste total de energía activa (tarifa de acceso - ATR)
            if (array_key_exists("info_coste_total_tarifa_acceso_energia_activa", $resultado_validacion) == true)
            {
                $info_coste_total_energia_activa_tarifa_acceso = $resultado_validacion["info_coste_total_tarifa_acceso_energia_activa"];
                $descripcion_resultado_validacion .= "<li>";
                if ($info_coste_total_energia_activa_tarifa_acceso[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
                {
                    $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                }
                $descripcion_resultado_validacion .= $idiomas->_("Coste total")." (".$idiomas->_("tarifa de acceso")."): ".
                    $info_coste_total_energia_activa_tarifa_acceso[INDICE_RESULTADO_VALIDACION_DATO_FACTURA]." ".$unidad_medida_coste." / ".
                    $info_coste_total_energia_activa_tarifa_acceso[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION]." ".$unidad_medida_coste.
                    " (".$idiomas->_("error").": ".formatea_numero($info_coste_total_energia_activa_tarifa_acceso[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
                $descripcion_resultado_validacion .= "</li>";
            }

            $descripcion_resultado_validacion .= "</ul>";
            return ($descripcion_resultado_validacion);
        }


        // Devuelve la descripcion del resultado de la validación de la factura eléctrica (potencia)
        static function dame_descripcion_resultado_validacion_factura_electrica_potencia($resultado_validacion)
        {
            $idiomas = new Idiomas();
            $descripcion_resultado_validacion = "";

            // Potencia
            if ($resultado_validacion["validacion_correcta_potencia"] == false)
            {
                $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
            }
            $descripcion_resultado_validacion .= $idiomas->_("Potencia").": ";
            $descripcion_resultado_validacion .= "<ul>";

            // Comprobación de tramos diferentes en potencia facturada
            if (array_key_exists("tramos_diferentes_potencia_facturada", $resultado_validacion) == true)
            {
                $descripcion_resultado_validacion .= "<li>";
                $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ".
                    $idiomas->_("Los tramos no coinciden en los datos de factura y monitorización")." (".$idiomas->_("potencia facturada").")";
                $descripcion_resultado_validacion .= "</li>";
            }

            // Potencia facturada (por tramo)
            if (array_key_exists("info_potencia_facturada", $resultado_validacion) == true)
            {
                $descripcion_resultado_validacion .= "<li>";
                $descripcion_resultado_validacion .= $idiomas->_("Potencia facturada")." (".$idiomas->_("por tramo")."): ";
                $descripcion_resultado_validacion .= "<ul>";
                foreach ($resultado_validacion["info_potencia_facturada"] as $numero_tramo => $info_potencia_facturada_tramo)
                {
                    $descripcion_resultado_validacion .= "<li>";
                    if ($info_potencia_facturada_tramo[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
                    {
                        $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                    }
                    $descripcion_resultado_validacion .= $idiomas->_("Tramo")." ".$numero_tramo.": ".
                        formatea_numero($info_potencia_facturada_tramo[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 2)." ".$idiomas->_("kW")." / ".
                        formatea_numero($info_potencia_facturada_tramo[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 2)." ".$idiomas->_("kW").
                        " (".$idiomas->_("error").": ".formatea_numero($info_potencia_facturada_tramo[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
                    $descripcion_resultado_validacion .= "</li>";
                }
                $descripcion_resultado_validacion .= "</ul>";
                $descripcion_resultado_validacion .= "</li>";
            }

            // Comprobación de tramos diferentes en potencia máxima
            if (array_key_exists("tramos_diferentes_potencia_maxima", $resultado_validacion) == true)
            {
                $descripcion_resultado_validacion .= "<li>";
                $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ".
                    $idiomas->_("Los tramos no coinciden en los datos de factura y monitorización")." (".$idiomas->_("potencia máxima").")";
                $descripcion_resultado_validacion .= "</li>";
            }

            // Potencia máxima (por tramo)
            if (array_key_exists("info_potencia_maxima", $resultado_validacion) == true)
            {
                $descripcion_resultado_validacion .= "<li>";
                $descripcion_resultado_validacion .= $idiomas->_("Potencia máxima")." (".$idiomas->_("por tramo")."): ";
                $descripcion_resultado_validacion .= "<ul>";
                foreach ($resultado_validacion["info_potencia_maxima"] as $numero_tramo => $info_potencia_maxima_tramo)
                {
                    $descripcion_resultado_validacion .= "<li>";
                    if ($info_potencia_maxima_tramo[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
                    {
                        $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                    }
                    $descripcion_resultado_validacion .= $idiomas->_("Tramo")." ".$numero_tramo.": ".
                        formatea_numero($info_potencia_maxima_tramo[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 2)." ".$idiomas->_("kW")." / ".
                        formatea_numero($info_potencia_maxima_tramo[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 2)." ".$idiomas->_("kW").
                        " (".$idiomas->_("error").": ".formatea_numero($info_potencia_maxima_tramo[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
                    $descripcion_resultado_validacion .= "</li>";
                }
                $descripcion_resultado_validacion .= "</ul>";
                $descripcion_resultado_validacion .= "</li>";
            }

            // Coste total de potencia
            if (array_key_exists("info_coste_total_potencia", $resultado_validacion) == true)
            {
                $info_coste_total_potencia = $resultado_validacion["info_coste_total_potencia"];
                $descripcion_resultado_validacion .= "<li>";
                if ($info_coste_total_potencia[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
                {
                    $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                }
                $descripcion_resultado_validacion .= $idiomas->_("Coste total").": ".
                    formatea_numero($info_coste_total_potencia[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 2, false)." ".$unidad_medida_coste." / ".
                    formatea_numero($info_coste_total_potencia[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 2, false)." ".$unidad_medida_coste.
                    " (".$idiomas->_("error").": ".formatea_numero($info_coste_total_potencia[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
                $descripcion_resultado_validacion .= "</li>";
            }

            $descripcion_resultado_validacion .= "</ul>";
            return ($descripcion_resultado_validacion);
        }


        // Devuelve la descripcion del resultado de la validación de la factura eléctrica (excesos de potencia)
        static function dame_descripcion_resultado_validacion_factura_electrica_excesos_potencia($resultado_validacion)
        {
            $idiomas = new Idiomas();
            $descripcion_resultado_validacion = "";

            // Excesos de potencia
            if ($resultado_validacion["validacion_correcta_excesos_potencia"] == false)
            {
                $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
            }
            $descripcion_resultado_validacion .= $idiomas->_("Excesos de potencia").": ";
            $descripcion_resultado_validacion .= "<ul>";

            // Comprobación de tramos diferentes en excesos de potencia
            if (array_key_exists("tramos_diferentes_excesos_potencia", $resultado_validacion) == true)
            {
                $descripcion_resultado_validacion .= "<li>";
                $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ".
                    $idiomas->_("Los tramos no coinciden en los datos de factura y monitorización")." (".$idiomas->_("excesos").")";
                $descripcion_resultado_validacion .= "</li>";
            }

            // Excesos de potencia (por tramo)
            if (array_key_exists("info_excesos_potencia", $resultado_validacion) == true)
            {
                if (count($resultado_validacion["info_excesos_potencia"]) == 0)
                {
                    $descripcion_resultado_validacion .= "<li>";
                    $descripcion_resultado_validacion .= $idiomas->_("Sin excesos");
                    $descripcion_resultado_validacion .= "</li>";
                }
                else
                {
                    $descripcion_resultado_validacion .= "<li>";
                    $descripcion_resultado_validacion .= $idiomas->_("Excesos")." (".$idiomas->_("por tramo")."): ";
                    $descripcion_resultado_validacion .= "<ul>";
                    foreach ($resultado_validacion["info_excesos_potencia"] as $numero_tramo => $info_exceso_potencia_tramo)
                    {
                        $descripcion_resultado_validacion .= "<li>";
                        if ($info_exceso_potencia_tramo[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
                        {
                            $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                        }
                        $descripcion_resultado_validacion .= $idiomas->_("Tramo")." ".$numero_tramo.": ".
                            formatea_numero($info_exceso_potencia_tramo[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 3)." ".$idiomas->_("kW")." / ".
                            formatea_numero($info_exceso_potencia_tramo[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 3)." ".$idiomas->_("kW").
                            " (".$idiomas->_("error").": ".formatea_numero($info_exceso_potencia_tramo[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
                        $descripcion_resultado_validacion .= "</li>";
                    }
                    $descripcion_resultado_validacion .= "</ul>";
                    $descripcion_resultado_validacion .= "</li>";
                }
            }

            // Coste total de excesos de potencia (si existe)
            if (array_key_exists("info_coste_total_excesos_potencia", $resultado_validacion) == true)
            {
                $info_coste_total_excesos_potencia = $resultado_validacion["info_coste_total_excesos_potencia"];
                $descripcion_resultado_validacion .= "<li>";
                if ($info_coste_total_excesos_potencia[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
                {
                    $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                }
                $descripcion_resultado_validacion .= $idiomas->_("Coste total").": ".
                    formatea_numero($info_coste_total_excesos_potencia[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 2, false)." ".$unidad_medida_coste." / ".
                    formatea_numero($info_coste_total_excesos_potencia[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 2, false)." ".$unidad_medida_coste.
                    " (".$idiomas->_("error").": ".formatea_numero($info_coste_total_excesos_potencia[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
                $descripcion_resultado_validacion .= "</li>";
            }

            $descripcion_resultado_validacion .= "</ul>";
            return ($descripcion_resultado_validacion);
        }


        // Devuelve la descripcion del resultado de la validación de la factura eléctrica (energía reactiva)
        static function dame_descripcion_resultado_validacion_factura_electrica_energia_reactiva($resultado_validacion)
        {
            $idiomas = new Idiomas();
            $descripcion_resultado_validacion = "";

            // Energía reactiva
            if ($resultado_validacion["validacion_correcta_energia_reactiva"] == false)
            {
                $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
            }
            $descripcion_resultado_validacion .= $idiomas->_("Energía reactiva").": ";
            $descripcion_resultado_validacion .= "<ul>";

            // Comprobación de tramos diferentes en excesos de energía reactiva
            if (array_key_exists("tramos_diferentes_energia_reactiva", $resultado_validacion) == true)
            {
                $descripcion_resultado_validacion .= "<li>";
                $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ".
                    $idiomas->_("Los tramos no coinciden en los datos de factura y monitorización")." (".$idiomas->_("excesos").")";
                $descripcion_resultado_validacion .= "</li>";
            }

						// Consumo de de energía reactiva (por tramo)
            if (array_key_exists("info_consumo_energia_reactiva_inductiva", $resultado_validacion) == true)
            {
                if (count($resultado_validacion["info_consumo_energia_reactiva_inductiva"]) == 0)
                {
                    $descripcion_resultado_validacion .= "<li>";
                    $descripcion_resultado_validacion .= $idiomas->_("Sin consumo");
                    $descripcion_resultado_validacion .= "</li>";
                }
                else
                {
                    $descripcion_resultado_validacion .= "<li>";
                    $descripcion_resultado_validacion .= $idiomas->_("Energía Reactiva Inductiva")." (".$idiomas->_("por tramo")."): ";
                    $descripcion_resultado_validacion .= "<ul>";
                    foreach ($resultado_validacion["info_consumo_energia_reactiva_inductiva"] as $numero_tramo => $info_consumo_energia_reactiva_tramo)
                    {
                        $descripcion_resultado_validacion .= "<li>";
                        if ($info_consumo_energia_reactiva_tramo[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
                        {
                            $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                        }
                        $descripcion_resultado_validacion .= $idiomas->_("Tramo")." ".$numero_tramo.": ".
                            formatea_numero($info_consumo_energia_reactiva_tramo[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 2)." ".$idiomas->_("kVArh")." / ".
                            formatea_numero($info_consumo_energia_reactiva_tramo[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 2)." ".$idiomas->_("kVArh").
                            " (".$idiomas->_("error").": ".formatea_numero($info_consumo_energia_reactiva_tramo[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
                        $descripcion_resultado_validacion .= "</li>";
                    }
                    $descripcion_resultado_validacion .= "</ul>";
                    $descripcion_resultado_validacion .= "</li>";
                }
            }

            // Excesos de de energía reactiva (por tramo)
            if (array_key_exists("info_excesos_energia_reactiva_inductiva", $resultado_validacion) == true)
            {
                if (count($resultado_validacion["info_excesos_energia_reactiva_inductiva"]) == 0)
                {
                    $descripcion_resultado_validacion .= "<li>";
                    $descripcion_resultado_validacion .= $idiomas->_("Sin excesos");
                    $descripcion_resultado_validacion .= "</li>";
                }
                else
                {
                    $descripcion_resultado_validacion .= "<li>";
                    $descripcion_resultado_validacion .= $idiomas->_("Excesos")." (".$idiomas->_("por tramo")."): ";
                    $descripcion_resultado_validacion .= "<ul>";
                    foreach ($resultado_validacion["info_excesos_energia_reactiva_inductiva"] as $numero_tramo => $info_exceso_energia_reactiva_tramo)
                    {
                        $descripcion_resultado_validacion .= "<li>";
                        if ($info_exceso_energia_reactiva_tramo[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
                        {
                            $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                        }
                        $descripcion_resultado_validacion .= $idiomas->_("Tramo")." ".$numero_tramo.": ".
                            formatea_numero($info_exceso_energia_reactiva_tramo[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 2)." ".$idiomas->_("kVArh")." / ".
                            formatea_numero($info_exceso_energia_reactiva_tramo[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 2)." ".$idiomas->_("kVArh").
                            " (".$idiomas->_("error").": ".formatea_numero($info_exceso_energia_reactiva_tramo[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
                        $descripcion_resultado_validacion .= "</li>";
                    }
                    $descripcion_resultado_validacion .= "</ul>";
                    $descripcion_resultado_validacion .= "</li>";
                }
            }

            // Coste total de excesos de energía reactiva (si existe)
            if (array_key_exists("info_coste_total_energia_reactiva_inductiva", $resultado_validacion) == true)
            {
                $info_coste_total_energia_reactiva = $resultado_validacion["info_coste_total_energia_reactiva_inductiva"];
                $descripcion_resultado_validacion .= "<li>";
                if ($info_coste_total_energia_reactiva[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
                {
                    $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                }
                $descripcion_resultado_validacion .= $idiomas->_("Coste total").": ".
                    formatea_numero($info_coste_total_energia_reactiva[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 2, false)." ".$unidad_medida_coste." / ".
                    formatea_numero($info_coste_total_energia_reactiva[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 2, false)." ".$unidad_medida_coste.
                    " (".$idiomas->_("error").": ".formatea_numero($info_coste_total_energia_reactiva[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
                $descripcion_resultado_validacion .= "</li>";
            }

          

						// Consumo de de energía reactiva (por tramo)
						if (array_key_exists("info_consumo_energia_reactiva_capacitiva", $resultado_validacion) == true)
						{
								if (count($resultado_validacion["info_consumo_energia_reactiva_capacitiva"]) == 0)
								{
										$descripcion_resultado_validacion .= "<li>";
										$descripcion_resultado_validacion .= $idiomas->_("Sin consumo");
										$descripcion_resultado_validacion .= "</li>";
								}
								else
								{
										$descripcion_resultado_validacion .= "<li>";
										$descripcion_resultado_validacion .= $idiomas->_("Energía Reactiva Capacitiva")." (".$idiomas->_("por tramo")."): ";
										$descripcion_resultado_validacion .= "<ul>";
										foreach ($resultado_validacion["info_consumo_energia_reactiva_capacitiva"] as $numero_tramo => $info_consumo_energia_reactiva_tramo)
										{
												$descripcion_resultado_validacion .= "<li>";
												if ($info_consumo_energia_reactiva_tramo[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
												{
														$descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
												}
												$descripcion_resultado_validacion .= $idiomas->_("Tramo")." ".$numero_tramo.": ".
														formatea_numero($info_consumo_energia_reactiva_tramo[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 2)." ".$idiomas->_("kVArh")." / ".
														formatea_numero($info_consumo_energia_reactiva_tramo[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 2)." ".$idiomas->_("kVArh").
														" (".$idiomas->_("error").": ".formatea_numero($info_consumo_energia_reactiva_tramo[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
												$descripcion_resultado_validacion .= "</li>";
										}
										$descripcion_resultado_validacion .= "</ul>";
										$descripcion_resultado_validacion .= "</li>";
								}
						}

						// Excesos de de energía reactiva (por tramo)
						// No se pueden comprobar los excesos de energía capacitiva porque no aparecen en el informe de cierre
						/*if (array_key_exists("info_excesos_energia_reactiva_capacitiva", $resultado_validacion) == true)
						{
								if (count($resultado_validacion["info_excesos_energia_reactiva_capacitiva"]) == 0)
								{
										$descripcion_resultado_validacion .= "<li>";
										$descripcion_resultado_validacion .= $idiomas->_("Sin excesos");
										$descripcion_resultado_validacion .= "</li>";
								}
								else
								{
										$descripcion_resultado_validacion .= "<li>";
										$descripcion_resultado_validacion .= $idiomas->_("Excesos")." (".$idiomas->_("por tramo")."): ";
										$descripcion_resultado_validacion .= "<ul>";
										foreach ($resultado_validacion["info_excesos_energia_reactiva_capacitiva"] as $numero_tramo => $info_exceso_energia_reactiva_tramo)
										{
												$descripcion_resultado_validacion .= "<li>";
												if ($info_exceso_energia_reactiva_tramo[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
												{
														$descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
												}
												$descripcion_resultado_validacion .= $idiomas->_("Tramo")." ".$numero_tramo.": ".
														formatea_numero($info_exceso_energia_reactiva_tramo[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 2)." ".$idiomas->_("kVArh")." / ".
														formatea_numero($info_exceso_energia_reactiva_tramo[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 2)." ".$idiomas->_("kVArh").
														" (".$idiomas->_("error").": ".formatea_numero($info_exceso_energia_reactiva_tramo[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
												$descripcion_resultado_validacion .= "</li>";
										}
										$descripcion_resultado_validacion .= "</ul>";
										$descripcion_resultado_validacion .= "</li>";
								}
						}*/

						// Coste total de excesos de energía reactiva (si existe)
						if (array_key_exists("info_coste_total_energia_reactiva_capacitiva", $resultado_validacion) == true)
						{
								$info_coste_total_energia_reactiva = $resultado_validacion["info_coste_total_energia_reactiva_capacitiva"];
								$descripcion_resultado_validacion .= "<li>";
								if ($info_coste_total_energia_reactiva[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
								{
										$descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
								}
								$descripcion_resultado_validacion .= $idiomas->_("Coste total").": ".
										formatea_numero($info_coste_total_energia_reactiva[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 2, false)." ".$unidad_medida_coste." / ".
										formatea_numero($info_coste_total_energia_reactiva[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 2, false)." ".$unidad_medida_coste.
										" (".$idiomas->_("error").": ".formatea_numero($info_coste_total_energia_reactiva[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
								$descripcion_resultado_validacion .= "</li>";
						}

						$descripcion_resultado_validacion .= "</ul>";
						return ($descripcion_resultado_validacion);
				}


        // Devuelve la descripcion del resultado de la validación de la factura eléctrica (otros conceptos y coste total)
        static function dame_descripcion_resultado_validacion_factura_electrica_otros_conceptos_coste_total($resultado_validacion)
        {
            $idiomas = new Idiomas();
            $descripcion_resultado_validacion = "";

            if ($resultado_validacion["validacion_correcta_otros_conceptos_coste_total"] == false)
            {
                $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
            }
            $descripcion_resultado_validacion .= $idiomas->_("Otros conceptos y coste total").": ";
            $descripcion_resultado_validacion .= "<ul>";

            // Coste de impuesto eléctrico
            if (array_key_exists("info_coste_impuesto_electrico", $resultado_validacion) == true)
            {
                $info_coste_impuesto_electrico = $resultado_validacion["info_coste_impuesto_electrico"];
                $descripcion_resultado_validacion .= "<li>";
                if ($info_coste_impuesto_electrico[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
                {
                    $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                }
                $descripcion_resultado_validacion .= $idiomas->_("Coste de impuesto eléctrico").": ".
                    formatea_numero($info_coste_impuesto_electrico[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 2, false)." ".$unidad_medida_coste." / ".
                    formatea_numero($info_coste_impuesto_electrico[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 2, false)." ".$unidad_medida_coste.
                    " (".$idiomas->_("error").": ".formatea_numero($info_coste_impuesto_electrico[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
                $descripcion_resultado_validacion .= "</li>";
            }

            // Coste de alquiler de contador
            if (array_key_exists("info_coste_alquiler_contador", $resultado_validacion) == true)
            {
                $info_coste_alquiler_contador = $resultado_validacion["info_coste_alquiler_contador"];
                $descripcion_resultado_validacion .= "<li>";
                if ($info_coste_alquiler_contador[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
                {
                    $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                }
                $descripcion_resultado_validacion .= $idiomas->_("Coste de alquiler de contador").": ".
                    formatea_numero($info_coste_alquiler_contador[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 2, false)." ".$unidad_medida_coste." / ".
                    formatea_numero($info_coste_alquiler_contador[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 2, false)." ".$unidad_medida_coste.
                    " (".$idiomas->_("error").": ".formatea_numero($info_coste_alquiler_contador[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
                $descripcion_resultado_validacion .= "</li>";
            }

            // IVA y coste de IVA
            if (array_key_exists("info_coste_iva", $resultado_validacion) == true)
            {
                $info_iva = $resultado_validacion["info_iva"];
                $descripcion_resultado_validacion .= "<li>";
                if ($info_iva[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
                {
                    $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                }
                $descripcion_resultado_validacion .= $idiomas->_("IVA").": ".
                    formatea_numero($info_iva[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 2)." "."%"." / ".
                    formatea_numero($info_iva[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 2)." "."%".
                    " (".$idiomas->_("error").": ".formatea_numero($info_iva[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
                $descripcion_resultado_validacion .= "</li>";
                $info_coste_iva = $resultado_validacion["info_coste_iva"];
                $descripcion_resultado_validacion .= "<li>";
                if ($info_coste_impuesto_electrico[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
                {
                    $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                }
                $descripcion_resultado_validacion .= $idiomas->_("Coste de IVA").": ".
                    formatea_numero($info_coste_iva[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 2, false)." ".$unidad_medida_coste." / ".
                    formatea_numero($info_coste_iva[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 2, false)." ".$unidad_medida_coste.
                    " (".$idiomas->_("error").": ".formatea_numero($info_coste_iva[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
                $descripcion_resultado_validacion .= "</li>";
            }

            // Coste total
            if (array_key_exists("info_coste_total", $resultado_validacion) == true)
            {
                $info_coste_total = $resultado_validacion["info_coste_total"];
                $descripcion_resultado_validacion .= "<li>";
                if ($info_coste_total[INDICE_RESULTADO_VALIDACION_VALIDACION_CORRECTA] == false)
                {
                    $descripcion_resultado_validacion .= "<i class='icon-thumbs-down-alt color-rojo'></i> ";
                }
                $descripcion_resultado_validacion .= $idiomas->_("Coste total").": ".
                    formatea_numero($info_coste_total[INDICE_RESULTADO_VALIDACION_DATO_FACTURA], 2, false)." ".$unidad_medida_coste." / ".
                    formatea_numero($info_coste_total[INDICE_RESULTADO_VALIDACION_DATO_MONITORIZACION], 2, false)." ".$unidad_medida_coste.
                    " (".$idiomas->_("error").": ".formatea_numero($info_coste_total[INDICE_RESULTADO_VALIDACION_PORCENTAJE_ERROR], 2)." %".")";
                $descripcion_resultado_validacion .= "</li>";
            }

            $descripcion_resultado_validacion .= "</ul>";
            return ($descripcion_resultado_validacion);
        }
	}
?>
