<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Regla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/util_reglas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/util_sucesos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/util_eventos.php');


    // Clase que representa un suceso para la activación de reglas
    class SucesoRegla
    {
        // Funciones estáticas de suceso


        // Devuelve la cabecera para la tabla de sucesos
        static function dame_cabecera_tabla()
        {
            $idiomas = new Idiomas();

            return (array(
                $idiomas->_("Nombre"),
                $idiomas->_("Causa"),
                $idiomas->_("Origen"),
                $idiomas->_("Activaciones"),
                $idiomas->_("Estado")
            ));
        }


        // Devuelve la consulta para la tabla de sucesos
        static function dame_consulta_sucesos($id_regla)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta = "
                SELECT *
                FROM sucesos_reglas
                WHERE
                    regla = '".$bd_red->_($id_regla)."'
                ORDER BY nombre ASC";
            return ($consulta);
        }


        // Devuelve la tabla de sucesos
        static function dame_tabla_sucesos($id_regla)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $administracion_reglas = Regla::dame_administracion_reglas();
            if ($administracion_reglas == true)
            {
                $boton_anyadir_suceso = "<i id='anyade_modifica_suceso__".$id_regla."' class='icon-plus color-blanco boton_actuadores_mostrar_ventana_anyadir_modificar_suceso_regla boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_suceso);
            }
            $boton_actualizar_tabla_sucesos = "<i id='actualiza_tabla_sucesos__".$id_regla."' class='icon-refresh color-blanco boton_actuadores_actualizar_tabla_sucesos_regla boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_sucesos);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_SUCESOS_REGLAS,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_SUCESOS_REGLAS),
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-sucesos-reglas",
                $idiomas->_("Sucesos"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = SucesoRegla::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada una de las acciones a la tabla y el pie de tabla
            $consulta = SucesoRegla::dame_consulta_sucesos($id_regla);
            $res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_sucesos = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $suceso = new SucesoRegla($fila);
                $params_fila = array(
                    "opciones" => $suceso->dame_opciones_tabla(),
                );
                $tabla->anyade_fila(
                    "datosSucesoRegla__".$id_regla."__".$fila['id'], $suceso->dame_datos_tabla(), $params_fila
                );
            }
            $tabla->anyade_pie($idiomas->_("Sucesos").": ".$numero_sucesos);

            return ($tabla->dame_tabla(false));
        }


        // Miembros de suceso


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
            $bd_red = BaseDatosRed::dame_base_datos();

            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $nombre = $icono_dato_erroneo;
            $descripcion_causa = $icono_dato_erroneo;
            $descripcion_origen = $icono_dato_erroneo;
            $descripcion_activaciones = $icono_dato_erroneo;
            $estado = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $nombre_correcto = false;
            try
            {
                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);
                $nombre_correcto = true;

                // En la columna 'causa' se muestra el nombre del evento/timeout/regla y el tipo (evento/timeout(regla)
                switch ($this->params["causa"])
                {
                    case CAUSA_SUCESO_EVENTO:
                    {
                        $nombre_evento = dame_nombre_evento($this->params["id_causa"]);
                        $descripcion_causa = $nombre_evento." (".$this->idiomas->_("evento").")";
                        break;
                    }
                    case CAUSA_SUCESO_TIMEOUT_ENVIO_SENSOR:
                    {
                        $descripcion_causa = SucesoRegla::dame_descripcion_causa_suceso(CAUSA_SUCESO_TIMEOUT_ENVIO_SENSOR);
                        break;
                    }
                    case CAUSA_SUCESO_REGLA:
                    {
                        $nombre_regla = dame_nombre_regla($this->params["id_causa"]);
                        $descripcion_causa = $nombre_regla." (".$this->idiomas->_("regla").")";
                        break;
                    }
                    default:
                    {
                        throw new Exception("Causa de suceso desconocida: '".$this->params["causa"]."'");
                    }
                }
                $descripcion_causa = htmlspecialchars($descripcion_causa, ENT_QUOTES);

                // En la columna 'origen' se muestra el nombre del sensor/grupo y el tipo (sensor/grupo)
                switch ($this->params["origen"])
                {
                    case ORIGEN_SUCESO_SENSOR:
                    {
                        $nombre_sensor = dame_nombre_sensor($this->params["id_origen"]);
                        $descripcion_origen = $nombre_sensor;
                        break;
                    }
                    case ORIGEN_SUCESO_GRUPO_SENSORES:
                    {
                        $nombre_grupo = dame_nombre_grupo_sensores($this->params["id_origen"]);
                        $descripcion_origen = $nombre_grupo." (".$this->idiomas->_("grupo").")";
                        break;
                    }
                    default:
                    {
                        $descripcion_origen = $this->idiomas->_("Ninguno");
                        break;
                    }
                }
                $descripcion_origen = htmlspecialchars($descripcion_origen, ENT_QUOTES);

                // Activaciones
                if ($this->params["numero_activaciones"] == NUMERO_ACTIVACIONES_SUCESO_TODOS_SENSORES_GRUPO)
                {
                    $descripcion_activaciones = $this->idiomas->_("Todos");
                }
                else
                {
                    $descripcion_activaciones = $this->params["numero_activaciones"];
                }

                // Estado
                if ($this->params["numero_activaciones"] == NUMERO_ACTIVACIONES_SUCESO_TODOS_SENSORES_GRUPO)
                {
                    // Se recupera el número de sensores del grupo y se establece el estado
                    $consulta_sensores_grupo = "
                        SELECT
                            COUNT(*) AS numero_sensores
                        FROM sensores
                        WHERE
                            grupo = ".$bd_red->_($this->params["id_origen"]);
                    $res_sensores_grupo = $bd_red->ejecuta_consulta($consulta_sensores_grupo);
                    if (($res_sensores_grupo == false) || ($res_sensores_grupo->dame_numero_filas() == 0))
                    {
                        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensores_grupo."'");
                    }
                    $fila_sensores_grupo = $res_sensores_grupo->dame_siguiente_fila();
                    $numero_sensores_grupo = $fila_sensores_grupo["numero_sensores"];
                    if (($this->params["activaciones"] > 0) && ($this->params["activaciones"] == $numero_sensores_grupo))
                    {
                        $estado = "<i class='icon-circle color-verde'>".
                            "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Activado"), ENT_QUOTES)."</texto></i>";
                        $estado .= " (".$this->params["activaciones"].")";
                    }
                    else
                    {
                        $estado = "<i class='icon-circle color-rojo'>".
                            "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Desactivado"), ENT_QUOTES)."</texto></i>";
                    }
                }
                else
                {
                    if ($this->params["activaciones"] >= $this->params["numero_activaciones"])
                    {
                        $estado = "<i class='icon-circle color-verde'>".
                            "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Activado"), ENT_QUOTES)."</texto></i>";
                        if ($this->params["numero_activaciones"] > 1)
                        {
                            $estado .= " (".$this->params["activaciones"].")";
                        }
                    }
                    else
                    {
                        $estado = "<i class='icon-circle color-rojo'>".
                            "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("Desactivado"), ENT_QUOTES)."</texto></i>";
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
                $descripcion_causa,
                $descripcion_origen,
                $descripcion_activaciones,
                $estado,
            ));
        }


        function dame_opciones_tabla()
        {
            $nombre = htmlspecialchars($this->params['nombre'], ENT_QUOTES);

            $opciones = array();
            $administracion_reglas = Regla::dame_administracion_reglas();
            if ($administracion_reglas == true)
            {
                $botones_administracion_habilitados = $this->dame_administracion_suceso_usuario_actual();
                if ($botones_administracion_habilitados == true)
                {
                    $editar = "<i id='anyade_modifica_suceso__".$this->params["regla"]."__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' ".
                        "class='icon-pencil color-gris boton_actuadores_mostrar_ventana_anyadir_modificar_suceso_regla boton-tabla-datos'></i>";
                    $duplicar = "<i id='anyade_modifica_suceso__".$this->params["regla"]."__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' ".
                        "class='icon-copy color-gris boton_actuadores_mostrar_ventana_anyadir_modificar_suceso_regla boton-tabla-datos'></i>";
                    $borrar = "<i id='elimina_suceso__".$this->params["regla"]."__".$this->id."' nombre_suceso='".$nombre."' ".
                        "class='icon-remove color-gris boton_actuadores_eliminar_suceso_regla boton-tabla-datos'></i>";
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
                $info .= "<br/>";
            }

            // Modo de activación
            $modo_activacion = $this->params["modo_activacion"];
            $parametros_modo_activacion = explode(SEPARADOR_PARAMETROS_SIMPLES, $this->params["parametros_modo_activacion"]);
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $this->idiomas->_("Modo de activación").": ".SucesoRegla::dame_descripcion_modo_activacion_suceso($modo_activacion)."<br/>";
            switch ($modo_activacion)
            {
                case MODO_ACTIVACION_SUCESO_TIEMPO_MINIMO:
                {
                    $numero_horas_activacion = $parametros_modo_activacion[0];
                    $info .= "<ul>";
                    $info .= "<li>".$this->idiomas->_("Horas de activación").": ".formatea_numero($numero_horas_activacion, 4)."</li>";
                    $info .= "</ul>";
                    break;
                }
                case MODO_ACTIVACION_SUCESO_REPETICIONES_MINIMAS_PERIODO_TIEMPO:
                {
                    $periodo_tiempo_activacion = $parametros_modo_activacion[0];
                    $numero_repeticiones_activacion = $parametros_modo_activacion[1];
                    $info .= "<ul>";
                    $info .= "<li>".$this->idiomas->_("Periodo de tiempo de activación").": ".dame_descripcion_periodo_tiempo($periodo_tiempo_activacion)."</li>";
                    $info .= "<li>".$this->idiomas->_("Repeticiones de activación").": ".$numero_repeticiones_activacion."</li>";
                    $info .= "</ul>";
                    break;
                }
            }

            return ($info);
		}


        //
        // Funciones de parámetros de sucesos
        //


        // Devuelve las causas del suceso
        static function dame_causas_suceso()
        {
            $causas_suceso = array();
            array_push($causas_suceso, CAUSA_SUCESO_EVENTO);
            array_push($causas_suceso, CAUSA_SUCESO_TIMEOUT_ENVIO_SENSOR);
            array_push($causas_suceso, CAUSA_SUCESO_REGLA);
            return ($causas_suceso);
        }


        // Devuelve la descripción de la causa del suceso
        static function dame_descripcion_causa_suceso($causa_suceso)
        {
            switch ($causa_suceso)
            {
                case CAUSA_SUCESO_EVENTO:
                {
                    $descripcion = "Evento";
                    break;
                }
                case CAUSA_SUCESO_TIMEOUT_ENVIO_SENSOR:
                {
                    $descripcion = "Timeout de envío";
                    break;
                }
                case CAUSA_SUCESO_REGLA:
                {
                    $descripcion = "Regla";
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


        // Devuelve la descripción del origen del suceso
        static function dame_descripcion_origen_suceso($origen_suceso)
        {
            switch ($origen_suceso)
            {
                case ORIGEN_SUCESO_SENSOR:
                {
                    $descripcion = "Sensor";
                    break;
                }
                case ORIGEN_SUCESO_GRUPO_SENSORES:
                {
                    $descripcion = "Grupo";
                    break;
                }
                case ID_NINGUNO:
                {
                    $descripcion = "Ninguno";
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


        // Devuelve los modos de activación del suceso
        static function dame_modos_activacion_suceso()
        {
            $modos_activacion_suceso = array();
            array_push($modos_activacion_suceso, MODO_ACTIVACION_SUCESO_NORMAL);
            array_push($modos_activacion_suceso, MODO_ACTIVACION_SUCESO_TIEMPO_MINIMO);
            array_push($modos_activacion_suceso, MODO_ACTIVACION_SUCESO_REPETICIONES_MINIMAS_PERIODO_TIEMPO);
            return ($modos_activacion_suceso);
        }


        // Devuelve la descripción del modo de activación del suceso
        static function dame_descripcion_modo_activacion_suceso($modo_activacion_suceso)
        {
            switch ($modo_activacion_suceso)
            {
                case MODO_ACTIVACION_SUCESO_NORMAL:
                {
                    $descripcion = "Normal";
                    break;
                }
                case MODO_ACTIVACION_SUCESO_TIEMPO_MINIMO:
                {
                    $descripcion = "Tiempo mínimo";
                    break;
                }
                case MODO_ACTIVACION_SUCESO_REPETICIONES_MINIMAS_PERIODO_TIEMPO:
                {
                    $descripcion = "Repeticiones mínimas en un periodo de tiempo";
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


        // Devuelve los periodos de tiempo de activación del suceso
        // (para el modo de activación de repeticiones mínimas en periodo de tiempo)
        static function dame_periodos_tiempo_activacion_suceso()
        {
            $periodos_tiempo_activacion_suceso = array();
            array_push($periodos_tiempo_activacion_suceso, PERIODO_TIEMPO_HORA);
            array_push($periodos_tiempo_activacion_suceso, PERIODO_TIEMPO_DIA);
            array_push($periodos_tiempo_activacion_suceso, PERIODO_TIEMPO_SEMANA);
            array_push($periodos_tiempo_activacion_suceso, PERIODO_TIEMPO_MES);
            return ($periodos_tiempo_activacion_suceso);
        }


        // Devuelve la descripción del número de activaciones
        function dame_descripcion_numero_activaciones_suceso($numero_activaciones_suceso)
        {
            $idiomas = new Idiomas();

            if ($numero_activaciones_suceso == NUMERO_ACTIVACIONES_SUCESO_TODOS_SENSORES_GRUPO)
            {
                $descripcion = $idiomas->_("Todos");
            }
            else
            {
                $descripcion = $numero_activaciones_suceso;
            }
            return ($descripcion);
        }


        //
        // Funciones auxiliares
        //


        function dame_administracion_suceso_usuario_actual()
        {
            $administracion_suceso = true;
            switch ($this->params["causa"])
            {
                case CAUSA_SUCESO_EVENTO:
                {
                    $mostrar_todos_sensores = dame_mostrar_todos_sensores();
                    if ($mostrar_todos_sensores == false)
                    {
                        $ids_eventos_usuario = Evento::dame_ids_eventos_usuario_actual();
                        if (in_array($this->params["id_causa"], $ids_eventos_usuario) == false)
                        {
                            $administracion_suceso = false;
                        }
                    }
                    break;
                }
                case CAUSA_SUCESO_REGLA:
                {
                    $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
                    if ($mostrar_todos_actuadores == false)
                    {
                        $ids_reglas_usuario = Regla::dame_ids_reglas_usuario_actual();
                        if (in_array($this->params["id_causa"], $ids_reglas_usuario) == false)
                        {
                            $administracion_suceso = false;
                        }
                    }
                    break;
                }
                case CAUSA_SUCESO_TIMEOUT_ENVIO_SENSOR:
                {
                    $mostrar_todos_sensores = dame_mostrar_todos_sensores();
                    if ($mostrar_todos_sensores == false)
                    {
                        switch ($this->params["origen"])
                        {
                            case ORIGEN_SUCESO_SENSOR:
                            {
                                $ids_sensores_usuario = dame_ids_sensores_usuario_actual(true);
                                if (in_array($this->params["id_origen"], $ids_sensores_usuario) == false)
                                {
                                    $administracion_suceso = false;
                                }
                                break;
                            }
                            case ORIGEN_SUCESO_GRUPO_SENSORES:
                            {
                                $ids_grupos_sensores_usuario = dame_ids_grupos_sensores_usuario_actual(false);
                                if (in_array($this->params["id_origen"], $ids_grupos_sensores_usuario) == false)
                                {
                                    $administracion_suceso = false;
                                }
                                break;
                            }
                        }
                    }
                    break;
                }
            }
            return ($administracion_suceso);
        }


        static function dame_nombre_causa_suceso_regla($causa_suceso, $id_causa_suceso)
        {
            $idiomas = new Idiomas();

            switch ($causa_suceso)
            {
                case CAUSA_SUCESO_EVENTO:
                {
                    $nombre_causa = dame_nombre_evento($id_causa_suceso);
                    break;
                }
                case CAUSA_SUCESO_REGLA:
                {
                    $nombre_causa = dame_nombre_regla($id_causa_suceso);
                    break;
                }
                default:
                {
                    $nombre_causa = $idiomas->_("Ninguna");
                    break;
                }
            }
            return ($nombre_causa);
        }


        static function dame_nombre_origen_suceso_regla($origen_suceso, $id_origen_suceso)
        {
            $idiomas = new Idiomas();

            switch ($origen_suceso)
            {
                case ORIGEN_SUCESO_SENSOR:
                {
                    $nombre_origen = dame_nombre_sensor($id_origen_suceso);
                    break;
                }
                case ORIGEN_SUCESO_GRUPO_SENSORES:
                {
                    $nombre_origen = dame_nombre_grupo_sensores($id_origen_suceso);
                    break;
                }
                default:
                {
                    $nombre_origen = $idiomas->_("Ninguno");
                    break;
                }
            }
            return ($nombre_origen);
        }
    }
?>
