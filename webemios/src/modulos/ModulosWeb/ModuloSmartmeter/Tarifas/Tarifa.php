<?php
	session_start();

	include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    // Clase que representa una tarifa
	class Tarifa
	{
		// Miembros de tarifa


		public $idiomas;

		public $id;
        public $params;


		// Funciones de tarifa


		function __construct($params = array())
		{
			$this->idiomas = new Idiomas();

            $this->id = $params["id"];
            $this->params = $params;
		}


        function dame_herramientas_detalles_tabla()
		{
            // Herramientas de detalles de tarifa
            $administracion_tarifas = Tarifa::dame_administracion_tarifas();
            $herramientas = "";
            $herramientas .= "
                <span class='boton-herramientas-detalle-tabla-datos'>
                    <button id='boton_refrescar__".$this->id."' class='btn-mini btn btn-success boton_smartmeter_refrescar_tabla_tarifa'>
                        <i class='icon-refresh color-blanco'></i>
                    </button>
                </span>";

            // Asignar tarifa a sensores
            if ($administracion_tarifas == true)
            {
                $herramientas .= "
                    <span class='boton-herramientas-detalle-tabla-datos'>
                        <button id='boton_asignar_tarifa__".$this->id."' class='btn-mini btn btn-success boton_smartmeter_mostrar_ventana_asignacion_tarifa_sensores'>".
                            $this->idiomas->_("Asignar tarifa")."
                        </button>
                    </span>";
            }
			return ($herramientas);
		}


        //
        // Expiraciones de tarifa
        //


        static function dame_expiraciones_tarifa()
        {
            $expiraciones_tarifa = array();
            array_push($expiraciones_tarifa, EXPIRACION_TARIFA_NINGUNO);
            array_push($expiraciones_tarifa, EXPIRACION_TARIFA_SI);
            array_push($expiraciones_tarifa, EXPIRACION_TARIFA_NO);
            return ($expiraciones_tarifa);
        }

        static function dame_prorrateos_tarifa()
        {
            $prorrateos_tarifa = array();            
            array_push($prorrateos_tarifa, PRORRATEO_TARIFA_SI);
            array_push($prorrateos_tarifa, PRORRATEO_TARIFA_NO);
            return ($prorrateos_tarifa);
        }


        static function dame_descripcion_expiracion_tarifa($expiracion_tarifa)
        {
            switch ($expiracion_tarifa)
            {
                case EXPIRACION_TARIFA_NINGUNO:
                {
                    $descripcion_expiracion_tarifa = "Ninguno";
                    break;
                }
                case EXPIRACION_TARIFA_SI:
                {
                    $descripcion_expiracion_tarifa = "Sí";
                    break;
                }
                case EXPIRACION_TARIFA_NO:
                {
                    $descripcion_expiracion_tarifa = "No";
                    break;
                }
                default:
                {
                    $descripcion_expiracion_tarifa = "Desconocida";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_expiracion_tarifa));
        }

        static function dame_descripcion_prorrateo_tarifa($prorrateo_tarifa)
        {
            switch ($prorrateo_tarifa)
            {
                case PRORRATEO_TARIFA_SI:
                {
                    $descripcion_prorrateo_tarifa = "Sí";
                    break;
                }
                case PRORRATEO_TARIFA_NO:
                {
                    $descripcion_prorrateo_tarifa = "No";
                    break;
                }
                default:
                {
                    $descripcion_prorrateo_tarifa = "Desconocida";
                    break;
                }
            }
            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion_prorrateo_tarifa));
        }

        //
        // Funciones auxiliares
        //


        static function dame_administracion_tarifas()
        {
            $administracion_tarifas = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR);
            return ($administracion_tarifas);
        }


        function dame_hay_tarifa_grupo_mas_reciente($tabla_tarifas)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $hay_tarifa_grupo_mas_reciente = false;
            if ($this->params["grupo"] != ID_NINGUNO)
            {
                $consulta_tarifas = "
                    SELECT id
                    FROM ".$tabla_tarifas."
                    WHERE
                        grupo = '".$bd_red->_($this->params["grupo"])."'
                    ORDER BY fecha_expiracion DESC
                    LIMIT 1";
                $res_tarifas = $bd_red->ejecuta_consulta($consulta_tarifas);
                if (($res_tarifas == false) || ($res_tarifas->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_tarifas."'");
                }
                $fila_tarifa = $res_tarifas->dame_siguiente_fila();
                if ($fila_tarifa["id"] != $this->id)
                {
                    $hay_tarifa_grupo_mas_reciente = true;
                }
            }
            return ($hay_tarifa_grupo_mas_reciente);
        }


        function dame_aviso_expiracion($tabla_tarifas)
        {
            $aviso_expiracion = "";

            $expiracion = $this->params["expiracion"];
            if ($expiracion == EXPIRACION_TARIFA_SI)
            {
                $numero_dias_restantes_expiracion = dame_numero_dias_restantes_expiracion_tarifa($this->params["fecha_expiracion"]);
                if (($numero_dias_restantes_expiracion <= $this->params["numero_dias_preaviso_expiracion"]) || ($numero_dias_restantes_expiracion <= 0))
                {
                    // Nota: Diferentes iconos en la fecha de expiración dependiendo de si hay alguna tarifa en el grupo (si existe) más reciente
                    $hay_tarifa_grupo_mas_reciente = $this->dame_hay_tarifa_grupo_mas_reciente($tabla_tarifas);
                    if ($hay_tarifa_grupo_mas_reciente == false)
                    {
                        $clases_icono_aviso_expiracion = "icon-warning-sign color-rojo";
                    }
                    else
                    {
                        $clases_icono_aviso_expiracion = "icon-info-sign color-azul";
                    }

                    $aviso_expiracion .= "<i class='".$clases_icono_aviso_expiracion."'></i> ";
                    if ($numero_dias_restantes_expiracion > 0)
                    {
                        $aviso_expiracion .= $this->idiomas->_("La tarifa expirará dentro de")." ".$numero_dias_restantes_expiracion." ";
                        if ($numero_dias_restantes_expiracion == 1)
                        {
                            $aviso_expiracion .= $this->idiomas->_("día");
                        }
                        else
                        {
                            $aviso_expiracion .= $this->idiomas->_("días");
                        }
                    }
                    else
                    {
                        if ($numero_dias_restantes_expiracion == 0)
                        {
                            $aviso_expiracion .= $this->idiomas->_("La tarifa expira hoy");
                        }
                        else
                        {
                            $numero_dias_tarifa_expirada = -$numero_dias_restantes_expiracion;
                            $aviso_expiracion .= $this->idiomas->_("La tarifa expiró hace")." ".$numero_dias_tarifa_expirada." ";
                            if ($numero_dias_tarifa_expirada == 1)
                            {
                                $aviso_expiracion .= $this->idiomas->_("día");
                            }
                            else
                            {
                                $aviso_expiracion .= $this->idiomas->_("días");
                            }
                        }
                    }
                    $aviso_expiracion .= "<br/>";
                }
            }
            return ($aviso_expiracion);
        }


        static function dame_ids_tarifas_usuario_actual($medicion, $ids_sensores_usuario = NULL)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se recuperan los ids de sensores visibles por el usuario actual
            if ($ids_sensores_usuario === NULL)
            {
                $ids_sensores_usuario = dame_ids_sensores_usuario_actual(true);
            }

            // Identificadores de tarifas
            $ids_tarifas = array();

            // Parámetros dependientes de la medición
            $clase_sensor = dame_clase_sensor_medicion($medicion);
            $indice_parametros_clase_sensor_id_tarifa = dame_indice_parametro_clase_sensor_tarifa($medicion);
            $indice_parametros_clase_sensor_id_grupo_tarifas = dame_indice_parametro_clase_sensor_grupo_tarifas($medicion);
            $tabla_tarifas = dame_nombre_tabla_tarifas($medicion);
            $tabla_grupos_tarifas = dame_nombre_tabla_grupos_tarifas($medicion);

            // Se recuperan los identificadores de las tarifas eléctricas de los sensores
            $cadena_ids_sensores_consulta = dame_cadena_ids_consulta($ids_sensores_usuario);
            $consulta_sensores_tarifas = "
                SELECT
                    DISTINCT(SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".($indice_parametros_clase_sensor_id_tarifa + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1)) AS id_tarifa
                FROM sensores
                WHERE
                    (clase = '".$clase_sensor."')
                    AND (id IN (".$cadena_ids_sensores_consulta."))
                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".($indice_parametros_clase_sensor_id_tarifa + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) <> ".ID_NINGUNO.")
                    AND (red = '".$_SESSION["id_red"]."')";
            $res_sensores_tarifas = $bd_red->ejecuta_consulta($consulta_sensores_tarifas);
            if ($res_sensores_tarifas == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_sensores_tarifas."'");
            }
            while ($fila_sensor_tarifas = $res_sensores_tarifas->dame_siguiente_fila())
            {
                if (in_array($fila_sensor_tarifas["id_tarifa"], $ids_tarifas) == false)
                {
                    array_push($ids_tarifas, $fila_sensor_tarifas["id_tarifa"]);
                }
            }

            // Se recuperan los identificadores de las tarifas de los grupos asignados a los sensores
            // - Se recuperan los identificadores de los grupos asignados a los sensores
            // - Se recuperan los identificadores de las tarifas de los grupos recuperados
            $ids_grupos_tarifas = array();
            $consulta_sensores_grupos_tarifas = "
                SELECT
                    DISTINCT(SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".($indice_parametros_clase_sensor_id_grupo_tarifas + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1)) AS id_grupo_tarifas
                FROM sensores
                WHERE
                    (clase = '".$clase_sensor."')
                    AND (id IN (".$cadena_ids_sensores_consulta."))
                    AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".($indice_parametros_clase_sensor_id_grupo_tarifas + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) <> ".ID_NINGUNO.")
                    AND (red = '".$_SESSION["id_red"]."')";
            $res_sensores_grupos_tarifas = $bd_red->ejecuta_consulta($consulta_sensores_grupos_tarifas);
            if ($res_sensores_grupos_tarifas == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_sensores_grupos_tarifas."'");
            }
            while ($fila_sensor_grupos_tarifas = $res_sensores_grupos_tarifas->dame_siguiente_fila())
            {
                array_push($ids_grupos_tarifas, $fila_sensor_grupos_tarifas["id_grupo_tarifas"]);
            }

            if (count($ids_grupos_tarifas) > 0)
            {
                $cadena_ids_grupos_tarifas_consulta = dame_cadena_ids_consulta($ids_grupos_tarifas);
                $consulta_tarifas_grupos = "
                    SELECT id
                    FROM ".$tabla_tarifas."
                    WHERE
                        (grupo IN (".$cadena_ids_grupos_tarifas_consulta."))
                        AND (red = '".$_SESSION["id_red"]."')";
                $res_tarifas_grupos = $bd_red->ejecuta_consulta($consulta_tarifas_grupos);
                if ($res_tarifas_grupos == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_tarifas_grupos."'");
                }
                while ($fila_tarifa_grupos = $res_tarifas_grupos->dame_siguiente_fila())
                {
                    array_push($ids_tarifas, $fila_tarifa_grupos["id"]);
                }
            }

            // Si hay administración de sensores, se devuelven las tarifas que no están asignadas a ningún sensor
            if (NodoSensor::dame_administracion_sensores() == true)
            {
                // Se recuperan los identificadores de las tarifas sin grupo que no están asignadas a ningún sensor
                // - Se recuperan los identificadores de las tarifas sin grupo que están asignadas a algún sensor
                // - Se recuperan los identificadores de las tarifas sin grupo que no están en las tarifas sin grupos asignadas a algún sensor

                // Se recuperan los identificadores de las tarifas sin grupo que están asignadas a algún sensor
                $consulta_tarifas_sin_grupo_asignadas = "
                    SELECT DISTINCT(SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".($indice_parametros_clase_sensor_id_tarifa + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1)) AS id_tarifa
                    FROM sensores
                    WHERE
                        (clase = '".$clase_sensor."')
                        AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".($indice_parametros_clase_sensor_id_tarifa + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) <> ".ID_NINGUNO.")
                        AND (red = '".$_SESSION["id_red"]."')";
                $res_tarifas_sin_grupo_asignadas = $bd_red->ejecuta_consulta($consulta_tarifas_sin_grupo_asignadas);
                if ($res_tarifas_sin_grupo_asignadas == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_tarifas_sin_grupo_asignadas."'");
                }
                $ids_tarifas_sin_grupo_asignadas = array();
                while ($fila_tarifas_sin_grupo_asignadas = $res_tarifas_sin_grupo_asignadas->dame_siguiente_fila())
                {
                    array_push($ids_tarifas_sin_grupo_asignadas, $fila_tarifas_sin_grupo_asignadas["id_tarifa"]);
                }

                // Se recuperan los identificadores de las tarifas sin grupo que no están en las tarifas sin grupos asignadas a algún sensor
                $cadena_ids_tarifas_sin_grupo_asignadas_consulta = dame_cadena_ids_consulta($ids_tarifas_sin_grupo_asignadas);
                $consulta_tarifas_sin_grupo_sin_asignar = "
                    SELECT id
                    FROM ".$tabla_tarifas."
                    WHERE
                        (grupo = '".ID_NINGUNO."')
                        AND (id NOT IN (".$cadena_ids_tarifas_sin_grupo_asignadas_consulta."))
                        AND (red = '".$_SESSION["id_red"]."')";
                $res_tarifas_sin_grupo_sin_asignar = $bd_red->ejecuta_consulta($consulta_tarifas_sin_grupo_sin_asignar);
                if ($res_tarifas_sin_grupo_sin_asignar == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_tarifas_sin_grupo_sin_asignar."'");
                }
                $ids_tarifas_sin_grupo_sin_asignar = array();
                while ($fila_tarifa_sin_grupo_sin_asignar = $res_tarifas_sin_grupo_sin_asignar->dame_siguiente_fila())
                {
                    array_push($ids_tarifas_sin_grupo_sin_asignar, $fila_tarifa_sin_grupo_sin_asignar["id"]);
                }

                // Se recuperan los identificadores de las tarifas de los grupos que no están asignados a ningún sensor
                // - Se recuperan los identificadores de grupos que están asignados a algún sensor
                // - Se recuperan los identificadores de los grupos no asignados a ningún sensor
                // - Se recuperan los identificadores de las tarifas de los grupos recuperados

                // Se recuperan los identificadores de los grupos que están asignadas a algún sensor
                $consulta_grupos_tarifas_asignados = "
                    SELECT DISTINCT(SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".($indice_parametros_clase_sensor_id_grupo_tarifas + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1)) AS id_grupo
                    FROM sensores
                    WHERE
                        (clase = '".$clase_sensor."')
                        AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".($indice_parametros_clase_sensor_id_grupo_tarifas + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) <> ".ID_NINGUNO.")
                        AND (red = '".$_SESSION["id_red"]."')";
                $res_grupos_tarifas_asignados = $bd_red->ejecuta_consulta($consulta_grupos_tarifas_asignados);
                if ($res_grupos_tarifas_asignados == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_grupos_tarifas_asignados."'");
                }
                $ids_grupos_tarifas_asignados = array();
                while ($fila_grupo_tarifas_asignado = $res_grupos_tarifas_asignados->dame_siguiente_fila())
                {
                    array_push($ids_grupos_tarifas_asignados, $fila_grupo_tarifas_asignado["id_grupo"]);
                }

                // Se recuperan los identificadores de los grupos no asignados a ningún sensor
                $ids_grupos_tarifas_sin_asignar = array();
                $cadena_ids_grupos_tarifas_asignados_consulta = dame_cadena_ids_consulta($ids_grupos_tarifas_asignados);
                $consulta_grupos_tarifas_sin_asignar = "
                    SELECT id
                    FROM ".$tabla_grupos_tarifas."
                    WHERE
                        (id NOT IN (".$cadena_ids_grupos_tarifas_asignados_consulta."))
                        AND (red = '".$_SESSION["id_red"]."')";
                $res_grupos_tarifas_sin_asignar = $bd_red->ejecuta_consulta($consulta_grupos_tarifas_sin_asignar);
                if ($res_grupos_tarifas_sin_asignar == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_grupos_tarifas_sin_asignar."'");
                }
                while ($fila_grupos_tarifas_sin_asignar = $res_grupos_tarifas_sin_asignar->dame_siguiente_fila())
                {
                    array_push($ids_grupos_tarifas_sin_asignar, $fila_grupos_tarifas_sin_asignar["id"]);
                }

                // Se recuperan los identificadores de las tarifas de los grupos recuperados
                $ids_tarifas_con_grupo_sin_asignar = array();
                if (count($ids_grupos_tarifas_sin_asignar) > 0)
                {
                    $cadena_ids_grupos_tarifas_sin_asignar_consulta = dame_cadena_ids_consulta($ids_grupos_tarifas_sin_asignar);
                    $consulta_tarifas_con_grupo_sin_asignar = "
                        SELECT id
                        FROM ".$tabla_tarifas."
                        WHERE
                            (grupo IN (".$cadena_ids_grupos_tarifas_sin_asignar_consulta."))
                            AND (red = '".$_SESSION["id_red"]."')";
                    $res_tarifas_con_grupo_sin_asignar = $bd_red->ejecuta_consulta($consulta_tarifas_con_grupo_sin_asignar);
                    if ($res_tarifas_con_grupo_sin_asignar == false)
                    {
                        throw new Exception("Error en la consulta: '".$consulta_tarifas_con_grupo_sin_asignar."'");
                    }
                    while ($fila_tarifa_con_grupo_sin_asignar = $res_tarifas_con_grupo_sin_asignar->dame_siguiente_fila())
                    {
                        array_push($ids_tarifas_con_grupo_sin_asignar, $fila_tarifa_con_grupo_sin_asignar["id"]);
                    }
                }

                // Se añaden las tarifas sin grupo y con grupo sin asignar
                $ids_tarifas = array_merge($ids_tarifas, $ids_tarifas_sin_grupo_sin_asignar);
                $ids_tarifas = array_merge($ids_tarifas, $ids_tarifas_con_grupo_sin_asignar);
                $ids_tarifas = array_unique($ids_tarifas);
            }

            return ($ids_tarifas);
        }
	}
?>
