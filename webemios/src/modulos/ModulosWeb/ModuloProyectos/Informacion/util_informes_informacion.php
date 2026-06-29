<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/Proyecto.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/util_modulo_sensores.php');


    //
    // Funciones de información de información
    //


    // Devuelve la información de un proyecto
    function dame_informacion_proyecto($parametros)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $id_proyecto = $parametros["id_proyecto"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];

        // Se comprueba si el proyecto es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        $ids_proyectos_usuario_actual = Proyecto::dame_ids_proyectos_usuario_actual($ids_sensores_usuario_actual);
        if (in_array($id_proyecto, $ids_proyectos_usuario_actual) == false)
        {
            throw new Exception("Proyecto no visible por el usuario actual (id: '".$id_proyecto."')");
        }

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Se recupera la información de proyecto
        $fila_proyecto = dame_fila_proyecto($id_proyecto);

        // Si el estado de avance del proyecto no es válido, se devuelve que no hay datos disponibles
        $estado_avance_proyecto = $fila_proyecto["estado_avance"];
        switch ($estado_avance_proyecto)
        {
            case ESTADO_AVANCE_PROYECTO_SIN_VALOR_OBJETIVO:
            case ESTADO_AVANCE_PROYECTO_NEGATIVO:
            case ESTADO_AVANCE_PROYECTO_POSITIVO:
            {
                break;
            }
            default:
            {
                $resultado = array(
                    "res" => "ERROR",
                    "msg" => $idiomas->_("No hay datos disponibles"));
                return ($resultado);
            }
        }

        // Conversión de fechas
        $zona_horaria_local = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_base_datos_local = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_local = convierte_formato_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $fecha_hora_inicio_local = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria_local);
        $fecha_hora_fin_local = convierte_cadena_a_fecha($cadena_fecha_hora_fin_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria_local);

        // Fechas y horas de inicio y fin de proyecto
        $cadena_fecha_hora_inicio_proyecto_base_datos_local = $fila_proyecto["fecha_inicio"]." 00:00:00";
        $cadena_fecha_hora_fin_proyecto_base_datos_local = $fila_proyecto["fecha_fin"]." 23:59:59";
        $fecha_hora_inicio_proyecto_local = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_proyecto_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria_local);
        $fecha_hora_fin_proyecto_local = convierte_cadena_a_fecha($cadena_fecha_hora_fin_proyecto_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria_local);

        // Si la fecha de inicio o fecha de fin está fuera de rango de fechas de proyecto, se establecen a las fechas del proyecto
        if ($fecha_hora_inicio_local < $fecha_hora_inicio_proyecto_local)
        {
            $fecha_hora_inicio_proyecto_local = $fecha_hora_inicio_local;
            $cadena_fecha_hora_inicio_base_datos_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, FORMATO_FECHA_HORA_BASE_DATOS);
        }
        if ($fecha_hora_fin_local > $fecha_hora_fin_proyecto_local)
        {
            $fecha_hora_fin_proyecto_local = $fecha_hora_fin_local;
            $cadena_fecha_hora_fin_base_datos_local = convierte_fecha_a_cadena($fecha_hora_fin_local, FORMATO_FECHA_HORA_BASE_DATOS);
        }

        // Se recupera la información de la línea base del proyecto
        $id_linea_base = $fila_proyecto["linea_base"];
        $fila_linea_base = dame_fila_linea_base($id_linea_base);
        $filas_lineas_base = array();
        $filas_lineas_base[$id_linea_base] = $fila_linea_base;

        // Intervalo de valores
        $intervalo_valores = $fila_proyecto["intervalo_valores"];

        // Unidad de medida y número de decimales
        $clase_sensor = $fila_proyecto["clase_sensor"];
        $id_sensor = $fila_proyecto["sensor"];
        $campo = $fila_proyecto["campo"];
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
        $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);

        // Flag de campo puntual e incremental
        $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);
        $campo_puntual = ($tipo_valores_campo == TIPO_VALORES_SENSOR_PUNTUALES);
        $campo_incremental = ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES);

        // Segundos máximos entre consumos y costes (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, NULL);

        // Valores mínimo y máximo
        $min_valor = INF;
        $max_valor = -INF;
        $min_diferencia = INF;
        $max_diferencia = -INF;
        $min_diferencia_acumulada = INF;
        $max_diferencia_acumulada = -INF;
        $min_fecha = NULL;
        $max_fecha = NULL;

        // Se realiza la consulta de valores reales y simulados del proyecto
        $consulta_valores_reales_simulados = "
            SELECT *
            FROM valores_reales_simulados_proyectos
            WHERE
                (id_proyecto = '".$bd_datos->_($id_proyecto)."')
                AND (fecha_hora_local >= '".$cadena_fecha_hora_inicio_base_datos_local."')
                AND (fecha_hora_local <= '".$cadena_fecha_hora_fin_base_datos_local."')
            ORDER BY
                fecha_hora_utc ASC";
        $res_valores_reales_simulados = $bd_datos->ejecuta_consulta($consulta_valores_reales_simulados);
        if ($res_valores_reales_simulados == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_reales_simulados."'");
        }

        // Si no hay datos no se hace nada
        $numero_valores_reales_simulados = $res_valores_reales_simulados->dame_numero_filas();
        if ($numero_valores_reales_simulados == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se recupera la información de valores adicionales del proyecto (para los tooltips)
        $info_valores_adicionales_proyecto = dame_info_valores_adicionales_proyecto($id_proyecto);

        // Variables para la información del proyecto
        $valor_real_avance = 0;
    	$valor_simulado_avance = 0;

        // Gráficas de proyecto
        $datos_valores_reales_proyecto = new VectorDatos();
        $datos_valores_simulados_proyecto = new VectorDatos();
        $datos_banda_valores_reales_proyecto = new VectorDatos();
        $datos_banda_valores_simulados_proyecto = new VectorDatos();
        $datos_diferencias_valores_reales_simulados_proyecto = new VectorDatos();
        $datos_diferencias_acumuladas_valores_reales_simulados_proyecto = new VectorDatos();
        $suma_diferencias_valores_reales_simulados_proyecto = 0;
        $timestamp_fecha_hora_valores_proyecto_anterior = NULL;
        $numero_puntos_seguidos_grafica = 0;
        while ($fila_valores_reales_simulados = $res_valores_reales_simulados->dame_siguiente_fila())
        {
            $cadena_fecha_valor_real_simulado_proyecto_base_datos_utc = $fila_valores_reales_simulados["fecha_hora_utc"];
            $cadena_fecha_valor_real_simulado_proyecto_base_datos_local = $fila_valores_reales_simulados["fecha_hora_local"];
            $valor_real_proyecto = (float) $fila_valores_reales_simulados["valor_real"];
            $valor_simulado_proyecto = (float) $fila_valores_reales_simulados["valor_simulado"];
            $id_linea_base_valor_real_simulado = (int) $fila_valores_reales_simulados["id_linea_base"];
            $error_estandar_valor_simulado = (float) $fila_valores_reales_simulados["error_estandar_linea_base"];

            // Valores reales y simulados de avance
            $valor_real_avance += $valor_real_proyecto;
            $valor_simulado_avance += $valor_simulado_proyecto;

            // Nota: Puede no haber línea base en el valor si se ha añadido valor adicional en un intervalo sin valores
            if ($id_linea_base_valor_real_simulado != ID_NINGUNO)
            {
                if (array_key_exists($id_linea_base_valor_real_simulado, $filas_lineas_base) == true)
                {
                    $fila_linea_base_valor_simulado = $filas_lineas_base[$id_linea_base_valor_real_simulado];
                }
                else
                {
                    $fila_linea_base_valor_simulado = dame_fila_linea_base($id_linea_base_valor_real_simulado);
                    $filas_lineas_base[$id_linea_base_valor_real_simulado] = $fila_linea_base_valor_simulado;
                }
            }

            if ($valor_real_proyecto > $max_valor)
            {
                $max_valor = $valor_real_proyecto;
            }
            if ($valor_simulado_proyecto > $max_valor)
            {
                $max_valor = $valor_simulado_proyecto;
            }
            if ($valor_real_proyecto < $min_valor)
            {
                $min_valor = $valor_real_proyecto;
            }
            if ($valor_simulado_proyecto < $min_valor)
            {
                $min_valor = $valor_simulado_proyecto;
            }

            if ($min_fecha === NULL)
            {
                $min_fecha = $cadena_fecha_valor_real_simulado_proyecto_base_datos_local;
            }
            $max_fecha = $cadena_fecha_valor_real_simulado_proyecto_base_datos_local;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_valores_proyecto = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_valor_real_simulado_proyecto_base_datos_utc, FORMATO_FECHA_HORA_FUNCIONES, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_valores_proyecto -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_valores_proyecto_anterior !== NULL))
            {
                $segundos_entre_valores_proyecto = ($timestamp_fecha_hora_valores_proyecto - $timestamp_fecha_hora_valores_proyecto_anterior) / 1000;
                if ($segundos_entre_valores_proyecto > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_valores_reales_proyecto->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_proyecto_anterior + 1, NULL);
                    $datos_valores_simulados_proyecto->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_proyecto_anterior + 1, NULL);
                    $datos_banda_valores_simulados_proyecto->anyade_tupla_pareja_datos(NULL, NULL);
                    $datos_diferencias_valores_reales_simulados_proyecto->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_proyecto_anterior + 1, NULL);
                }
            }
            $timestamp_fecha_hora_valores_proyecto_anterior = $timestamp_fecha_hora_valores_proyecto;
            $numero_puntos_seguidos_grafica += 1;

            // Sólo se añaden las horas si el intervalo es por hora en los tooltips
            if ($intervalo_valores == INTERVALO_VALORES_HORA)
            {
                $cadena_fecha_valor_valores_proyecto_local_local = convierte_formato_fecha($cadena_fecha_valor_real_simulado_proyecto_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local_sin_segundos"]);
            }
            else
            {
                $cadena_fecha_valor_valores_proyecto_local_local = convierte_formato_fecha($cadena_fecha_valor_real_simulado_proyecto_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            }

            // Datos de valores reales
            $tooltip_valor_real = $idiomas->_("Valor real").": ".formatea_numero($valor_real_proyecto, $numero_decimales_valores);
            if ($unidad_medida != "")
            {
                $tooltip_valor_real .= " ".$unidad_medida;
            }
            $tooltip_valor_real .= " (".$cadena_fecha_valor_valores_proyecto_local_local.")";
            anyade_informacion_valores_adicionales_tooltip(
                $tooltip_valor_real,
                DESTINO_VALOR_ADICIONAL_PROYECTO_VALORES_REALES,
                $fila_valores_reales_simulados,
                $info_valores_adicionales_proyecto,
                $unidad_medida,
                $numero_decimales_valores);

            $datos_valores_reales_proyecto->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_valores_proyecto,
                $valor_real_proyecto,
                $tooltip_valor_real);

            // Datos de valores simulados
            $tooltip_valor_simulado = $idiomas->_("Valor simulado").": ".formatea_numero($valor_simulado_proyecto, $numero_decimales_valores);
            if ($unidad_medida != "")
            {
                $tooltip_valor_simulado .= " ".$unidad_medida;
            }
            $tooltip_valor_simulado .= " (".$cadena_fecha_valor_valores_proyecto_local_local.")"."<br/>";
            anyade_informacion_valores_adicionales_tooltip(
                $tooltip_valor_simulado,
                DESTINO_VALOR_ADICIONAL_PROYECTO_VALORES_SIMULADOS,
                $fila_valores_reales_simulados,
                $info_valores_adicionales_proyecto,
                $unidad_medida,
                $numero_decimales_valores);
            if ($id_linea_base_valor_real_simulado != ID_NINGUNO)
            {
                // Nota: Si el campo es coste, el error estándar es de consumo, se multiplica por el precio (valor 'extra')
                if ((($clase_sensor == CLASE_SENSOR_ENERGIA_ACTIVA) && ($campo == CAMPO_COSTE)) ||
                    (($clase_sensor == CLASE_SENSOR_GAS) && ($campo == CAMPO_COSTE)))
                {
                    $valor_extra = $fila_valores_reales_simulados["valor_extra"];
                    if ($valor_extra !== NULL)
                    {
                        $error_estandar_valor_simulado = $error_estandar_valor_simulado * $valor_extra;
                    }
                }

                $tooltip_valor_simulado .= $idiomas->_("Línea base").": ".$fila_linea_base_valor_simulado["nombre"]."<br/>";
                $cadena_error_estandar = formatea_numero($error_estandar_valor_simulado, NUMERO_DECIMALES_ERROR_ESTANDAR_LINEA_BASE);
                $tooltip_valor_simulado .= $idiomas->_("Error estándar")." (".$idiomas->_("RMSE").")".": ".$cadena_error_estandar;
            }
            else
            {
                $tooltip_valor_simulado .= $idiomas->_("Línea base").": ".$idiomas->_("Ninguna");
                $error_estandar_valor_simulado = 0;
            }

            $datos_valores_simulados_proyecto->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_valores_proyecto,
                $valor_simulado_proyecto,
                $tooltip_valor_simulado);

            // Banda de error en valores simulados
            $datos_banda_valores_simulados_proyecto->anyade_tupla_pareja_datos(
                $valor_simulado_proyecto - $error_estandar_valor_simulado,
                $valor_simulado_proyecto + $error_estandar_valor_simulado);

            // Datos de diferencias
            $diferencia_valor_real_simulado_proyecto = $valor_real_proyecto - $valor_simulado_proyecto;
            if ($diferencia_valor_real_simulado_proyecto > $max_diferencia)
            {
                $max_diferencia = $diferencia_valor_real_simulado_proyecto;
            }
            if ($diferencia_valor_real_simulado_proyecto < $min_diferencia)
            {
                $min_diferencia = $diferencia_valor_real_simulado_proyecto;
            }
            $datos_diferencias_valores_reales_simulados_proyecto->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_proyecto, $diferencia_valor_real_simulado_proyecto);

            // Datos de diferencias acumuladas
            if ($campo_incremental == true)
            {
                $suma_diferencias_valores_reales_simulados_proyecto += $diferencia_valor_real_simulado_proyecto;
                if ($suma_diferencias_valores_reales_simulados_proyecto > $max_diferencia_acumulada)
                {
                    $max_diferencia_acumulada = $suma_diferencias_valores_reales_simulados_proyecto;
                }
                if ($suma_diferencias_valores_reales_simulados_proyecto < $min_diferencia_acumulada)
                {
                    $min_diferencia_acumulada = $suma_diferencias_valores_reales_simulados_proyecto;
                }
                $datos_diferencias_acumuladas_valores_reales_simulados_proyecto->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_proyecto, $suma_diferencias_valores_reales_simulados_proyecto);
            }
        }

        // Valores reales y simulados de avance
        if ($campo_puntual == true)
        {
            $valor_real_avance /= $numero_valores_reales_simulados;
            $valor_simulado_avance /= $numero_valores_reales_simulados;
        }

        // Fechas de inicio y fin
        if (($min_fecha !== NULL) && ($max_fecha !== NULL))
        {
            $min_fecha = convierte_formato_fecha($min_fecha, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_FECHA_HORA_JQPLOT);
            $max_fecha = convierte_formato_fecha($max_fecha, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_FECHA_HORA_JQPLOT);

            // Fechas mínima y máxima
            // (si son la misma, se elimina y se añade una hora al mínimo y al máximo para que la gráfica se muestre correctamente)
            if ($min_fecha == $max_fecha)
            {
                $intervalo_fecha = dame_intervalo_fecha_intervalo_valores_fechas_iguales($intervalo_valores);
                $min_fecha_valores_proyecto = convierte_cadena_a_fecha($min_fecha, FORMATO_FECHA_HORA_JQPLOT, ZONA_HORARIA_UTC);
                $max_fecha_valores_proyecto = convierte_cadena_a_fecha($max_fecha, FORMATO_FECHA_HORA_JQPLOT, ZONA_HORARIA_UTC);
                $min_fecha_valores_proyecto->sub($intervalo_fecha);
                $max_fecha_valores_proyecto->add($intervalo_fecha);

                $min_fecha = convierte_fecha_a_cadena($min_fecha_valores_proyecto, FORMATO_FECHA_HORA_JQPLOT);
                $max_fecha = convierte_fecha_a_cadena($max_fecha_valores_proyecto, FORMATO_FECHA_HORA_JQPLOT);
            }
        }

        // Gráfica de valores
        $grafica_valores = new VectorDatos();
        $grafica_valores->anyade_dato($datos_valores_simulados_proyecto->dame_datos());
        $grafica_valores->anyade_dato($datos_valores_reales_proyecto->dame_datos());

        // Bandas de valores
        $bandas_valores = new VectorDatos();
        $bandas_valores->anyade_dato($datos_banda_valores_simulados_proyecto->dame_datos());
        $bandas_valores->anyade_dato($datos_banda_valores_reales_proyecto->dame_datos());

        // Nombres de gráfica de valores
        $nombres_grafica_valores = new VectorDatos();
        $nombres_grafica_valores->anyade_etiqueta($idiomas->_("Valores simulados"));
        $nombres_grafica_valores->anyade_etiqueta($idiomas->_("Valores reales"));

        // Gráfica de diferencias
        $grafica_diferencias = new VectorDatos();
        $grafica_diferencias->anyade_dato($datos_diferencias_valores_reales_simulados_proyecto->dame_datos());

        // Gráfica de diferencias acumuladas
        $grafica_diferencias_acumuladas = new VectorDatos();
        $grafica_diferencias_acumuladas->anyade_dato($datos_diferencias_acumuladas_valores_reales_simulados_proyecto->dame_datos());

        // Proyecto
        $proyecto = new Proyecto($fila_proyecto);

        // Tabla de parámetros del proyecto
        $params_tabla_parametros_proyecto = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_PARAMETROS_PROYECTO,
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_PARAMETROS_PROYECTO),
            "generar_valores_xml" => true
        );
        $cabecera_tabla_parametros_proyecto = array(
            $idiomas->_("Sensor"),
            $idiomas->_("Intervalo de valores"),
            $idiomas->_("Línea base"),
            $idiomas->_("Fecha de inicio"),
            $idiomas->_("Fecha de fin"),
            $idiomas->_("Objetivo"));
        $titulo_tabla_parametros_proyecto = $idiomas->_("Parámetros de proyecto");
        $tabla_parametros_proyecto = new TablaDatos(
            "tabla-parametros-proyecto",
            $titulo_tabla_parametros_proyecto,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_parametros_proyecto
        );
        $tabla_parametros_proyecto->anyade_cabecera("", $cabecera_tabla_parametros_proyecto);
        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $descripcion_sensor = $nombre_sensor." (".strtolower(dame_descripcion_campo_clase_sensor($clase_sensor, $campo).")");
        $cadena_intervalo_valores = dame_descripcion_intervalo_valores($intervalo_valores);
        $linea_base = dame_nombre_linea_base($id_linea_base);
        $cadena_fecha_inicio = convierte_formato_fecha($fila_proyecto["fecha_inicio"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
        $cadena_fecha_fin = convierte_formato_fecha($fila_proyecto["fecha_fin"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
        $descripcion_objetivo_proyecto = $proyecto->dame_descripcion_objetivo_proyecto();
        $datos_parametros_proyecto = array(
            $descripcion_sensor,
            $cadena_intervalo_valores,
            $linea_base,
            $cadena_fecha_inicio,
            $cadena_fecha_fin,
            $descripcion_objetivo_proyecto);
        $tabla_parametros_proyecto->anyade_fila("fila-parametros-proyecto", $datos_parametros_proyecto);
        $datos_tabla_parametros_proyecto = $tabla_parametros_proyecto->dame_tabla();

        // Tabla de información del proyecto
        $params_tabla_informacion_proyecto = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_INFORMACION_PROYECTO,
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_INFORMACION_PROYECTO),
            "generar_valores_xml" => true
        );
        $cabecera_tabla_informacion_proyecto = array(
            $idiomas->_("Hora de fin de consulta de valores"),
            $idiomas->_("Hora de últimos valores"),
            $idiomas->_("Valor real"),
            $idiomas->_("Valor simulado"),
            $idiomas->_("Avance"),
            $idiomas->_("Estado"));
        $titulo_tabla_informacion_proyecto = $idiomas->_("Información de proyecto");
        $tabla_informacion_proyecto = new TablaDatos(
            "tabla-informacion-proyecto",
            $titulo_tabla_informacion_proyecto,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_informacion_proyecto
        );

        // Se calculan los datos para la información del proyecto
        $cadena_hora_fin_valores_avance_local_local = $cadena_fecha_hora_fin_local_local;
        $cadena_hora_ultimos_valores_avance_local_local = convierte_formato_fecha($cadena_fecha_valor_real_simulado_proyecto_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
        $cadena_valor_real_avance = formatea_numero($valor_real_avance, $numero_decimales_valores);
        if ($unidad_medida != "")
        {
            $cadena_valor_real_avance .= " ".$unidad_medida;
        }
        $cadena_valor_simulado_avance = formatea_numero($valor_simulado_avance, $numero_decimales_valores);
        if ($unidad_medida != "")
        {
            $cadena_valor_simulado_avance .= " ".$unidad_medida;
        }
        $estado_proyecto = $fila_proyecto["estado"];

        // Porcentaje de finalización del proyecto
		// (no se tiene en cuenta el día actual - sólo se cuentan días completos)
        switch ($estado_proyecto)
        {
            case ESTADO_PROYECTO_ACTIVO:
            case ESTADO_PROYECTO_FINALIZADO:
            {
                $fecha_hora_fin_valores_avance_local = convierte_cadena_a_fecha($cadena_hora_fin_valores_avance_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria_local);
                $numero_dias_duracion_proyecto = $fecha_hora_fin_proyecto_local->diff($fecha_hora_inicio_proyecto_local)->days + 1;
                $numero_dias_actuales_proyecto = $fecha_hora_fin_valores_avance_local->diff($fecha_hora_inicio_local)->days + 1;
                $porcentaje_finalizacion = ($numero_dias_actuales_proyecto * 100) / $numero_dias_duracion_proyecto;
                if ($porcentaje_finalizacion > 100)
                {
                    $porcentaje_finalizacion = 100;
                }
                switch ($estado_proyecto)
                {
                    case ESTADO_PROYECTO_FINALIZADO:
                    {
                        if ($porcentaje_finalizacion < 100)
                        {
                            $estado_proyecto = ESTADO_PROYECTO_ACTIVO;
                        }
                        break;
                    }
                }
                break;
            }
            default:
            {
                $porcentaje_finalizacion = NULL;
                break;
            }
        }
        $descripcion_avance_proyecto = Proyecto::dame_descripcion_avance_proyecto(
            $fila_proyecto,
            $valor_real_avance,
            $valor_simulado_avance,
            $porcentaje_finalizacion,
            NULL);
        $descripcion_estado_proyecto = Proyecto::dame_descripcion_estado_proyecto_porcentaje_finalizacion(
            $estado_proyecto,
            $porcentaje_finalizacion,
            NULL);

        // Datos de información del proyecto
        $datos_informacion_proyecto = array(
            $cadena_hora_fin_valores_avance_local_local,
            $cadena_hora_ultimos_valores_avance_local_local,
            $cadena_valor_real_avance,
            $cadena_valor_simulado_avance,
            $descripcion_avance_proyecto,
            $descripcion_estado_proyecto);
        $tabla_informacion_proyecto->anyade_cabecera("", $cabecera_tabla_informacion_proyecto);
        $tabla_informacion_proyecto->anyade_fila("", $datos_informacion_proyecto);
        $datos_tabla_informacion_proyecto = $tabla_informacion_proyecto->dame_tabla();

        // Tabla de valores adicionales de proyecto
        if (dame_numero_valores_adicionales_proyecto($id_proyecto) == 0)
        {
            $datos_tabla_valores_adicionales_proyecto = NULL;
        }
        else
        {
            $datos_tabla_valores_adicionales_proyecto = $proyecto->dame_tabla_valores_adicionales(false, true);
        }

        // Tablas de errores y coeficientes de línea base y de excepciones
        $params_tabla_errores_coeficientes_lineas_base = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLAS_ERRORES_COEFICIENTES_LINEAS_BASE,
            "generar_valores_xml" => true
        );
        $cabecera_tabla_errores_coeficientes_lineas_base = array(
            $idiomas->_("Nombre"),
            $idiomas->_("Error estándar")." (".$idiomas->_("RMSE").")",
            $idiomas->_("Coeficiente de variación"),
            $idiomas->_("Coeficiente de correlación")." (".$idiomas->_("R2").")"
        );

        // Flag para mostrar las tablas de errores de líneas base
        if ((($clase_sensor == CLASE_SENSOR_ENERGIA_ACTIVA) && ($campo == CAMPO_COSTE)) ||
            (($clase_sensor == CLASE_SENSOR_GAS) && ($campo == CAMPO_COSTE)))
        {
            $mostrar_tablas_errores_coeficientes_lineas_base = false;
        }
        else
        {
            $mostrar_tablas_errores_coeficientes_lineas_base = true;
        }

        // Se crean las tablas de errores y coeficientes de líneas base (si es necesario)
        if ($mostrar_tablas_errores_coeficientes_lineas_base == true)
        {
            // Tabla de error y coeficientes de línea base
            $titulo_tabla_error_coeficientes_linea_base = $idiomas->_("Error y coeficientes de línea base");
            $tabla_error_coeficientes_linea_base = new TablaDatos(
                "tabla-error-coeficientes-linea-base",
                $titulo_tabla_error_coeficientes_linea_base,
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla_errores_coeficientes_lineas_base
            );
            $error_estandar_linea_base = $fila_linea_base["error_estandar"];
            if ($error_estandar_linea_base == -1)
            {
                $cadena_error_estandar_linea_base = $idiomas->_("ND");
            }
            else
            {
                $cadena_error_estandar_linea_base = formatea_numero($error_estandar_linea_base, NUMERO_DECIMALES_ERROR_ESTANDAR_LINEA_BASE);
            }
            $coeficiente_variacion_linea_base = $fila_linea_base["coeficiente_variacion"];
            if ($coeficiente_variacion_linea_base == "")
            {
                $cadena_coeficiente_variacion_linea_base = $idiomas->_("ND");
            }
            else
            {
                $cadena_coeficiente_variacion_linea_base = formatea_numero($coeficiente_variacion_linea_base, NUMERO_DECIMALES_COEFICIENTE_VARIACION_LINEA_BASE);
            }
            $coeficiente_correlacion_linea_base = $fila_linea_base["coeficiente_correlacion"];
            if ($coeficiente_correlacion_linea_base == "")
            {
                $cadena_coeficiente_correlacion_linea_base = $idiomas->_("ND");
            }
            else
            {
                $cadena_coeficiente_correlacion_linea_base = formatea_numero($coeficiente_correlacion_linea_base, NUMERO_DECIMALES_COEFICIENTE_VARIACION_LINEA_BASE);
            }
            $tabla_error_coeficientes_linea_base->anyade_cabecera("", $cabecera_tabla_errores_coeficientes_lineas_base);
            $datos_error_coeficientes_linea_base = array(
                $fila_linea_base["nombre"],
                $cadena_error_estandar_linea_base,
                $cadena_coeficiente_variacion_linea_base,
                $cadena_coeficiente_correlacion_linea_base);
            $tabla_error_coeficientes_linea_base->anyade_fila("fila-error-coeficientes-linea-base", $datos_error_coeficientes_linea_base);
            $datos_tabla_error_coeficientes_linea_base = $tabla_error_coeficientes_linea_base->dame_tabla();

            // Tabla de errores y coeficientes de líneas base de excepciones
            $hay_lineas_base_excepciones = (count($filas_lineas_base) > 1);
            if ($hay_lineas_base_excepciones == false)
            {
                $datos_tabla_errores_coeficientes_lineas_base_excepciones = NULL;
            }
            else
            {
                $titulo_tabla_errores_coeficientes_lineas_base_excepciones = $idiomas->_("Errores y coeficientes de líneas base de excepciones");
                $tabla_errores_coeficientes_lineas_base_excepciones = new TablaDatos(
                    "tabla-errores-coeficientes-lineas-base-excepciones",
                    $titulo_tabla_errores_coeficientes_lineas_base_excepciones,
                    TIPO_TABLA_DATOS_LISTA,
                    $params_tabla_errores_coeficientes_lineas_base
                );
                $tabla_errores_coeficientes_lineas_base_excepciones->anyade_cabecera("", $cabecera_tabla_errores_coeficientes_lineas_base);

                // Se añade la información de líneas base ordenadas por nombre
                $errores_coeficientes_lineas_base_excepciones = array();
                foreach ($filas_lineas_base as $fila_linea_base_bucle)
                {
                    if ($fila_linea_base_bucle["id"] == $id_linea_base)
                    {
                        continue;
                    }
                    $errores_coeficientes_lineas_base_excepciones[$fila_linea_base_bucle["nombre"]] = array(
                        "error_estandar" => $fila_linea_base_bucle["error_estandar"],
                        "coeficiente_variacion" => $fila_linea_base_bucle["coeficiente_variacion"],
                        "coeficiente_correlacion" => $fila_linea_base_bucle["coeficiente_correlacion"]);
                }
                ksort($errores_coeficientes_lineas_base_excepciones);
                foreach ($errores_coeficientes_lineas_base_excepciones as $nombre_linea_base_excepcion => $error_coeficientes_linea_base_excepcion)
                {
                    $error_estandar_linea_base_excepcion = $error_coeficientes_linea_base_excepcion["error_estandar"];
                    if ($error_estandar_linea_base_excepcion == -1)
                    {
                        $cadena_error_estandar_linea_base_excepcion = $idiomas->_("ND");
                    }
                    else
                    {
                        $cadena_error_estandar_linea_base_excepcion = formatea_numero($error_estandar_linea_base_excepcion, NUMERO_DECIMALES_ERROR_ESTANDAR_LINEA_BASE);
                    }
                    $coeficiente_variacion_linea_base_excepcion = $error_coeficientes_linea_base_excepcion["coeficiente_variacion"];
                    if ($coeficiente_variacion_linea_base_excepcion == "")
                    {
                        $cadena_coeficiente_variacion_linea_base_excepcion = $idiomas->_("ND");
                    }
                    else
                    {
                        $cadena_coeficiente_variacion_linea_base_excepcion = formatea_numero($coeficiente_variacion_linea_base_excepcion, NUMERO_DECIMALES_COEFICIENTE_VARIACION_LINEA_BASE);
                    }
                    $coeficiente_correlacion_linea_base_excepcion = $error_coeficientes_linea_base_excepcion["coeficiente_correlacion"];
                    if ($coeficiente_correlacion_linea_base_excepcion == "")
                    {
                        $cadena_coeficiente_correlacion_linea_base_excepcion = $idiomas->_("ND");
                    }
                    else
                    {
                        $cadena_coeficiente_correlacion_linea_base_excepcion = formatea_numero($coeficiente_correlacion_linea_base_excepcion, NUMERO_DECIMALES_COEFICIENTE_VARIACION_LINEA_BASE);
                    }
                    $datos_error_coeficientes_linea_base_excepcion = array(
                        $nombre_linea_base_excepcion,
                        $cadena_error_estandar_linea_base_excepcion,
                        $cadena_coeficiente_variacion_linea_base_excepcion,
                        $cadena_coeficiente_correlacion_linea_base_excepcion);
                    $tabla_errores_coeficientes_lineas_base_excepciones->anyade_fila("fila-error-coeficientes-linea-base-excepcion", $datos_error_coeficientes_linea_base_excepcion);
                }
                $datos_tabla_errores_coeficientes_lineas_base_excepciones = $tabla_errores_coeficientes_lineas_base_excepciones->dame_tabla();
            }
        }
        else
        {
            $datos_tabla_error_coeficientes_linea_base = NULL;
            $datos_tabla_errores_coeficientes_lineas_base_excepciones = NULL;
        }

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($min_valor == INF)
        {
            $min_valor = "ND";
        }
        if ($max_valor == -INF)
        {
            $max_valor = "ND";
        }
        if ($min_diferencia == INF)
        {
            $min_diferencia = "ND";
        }
        if ($max_diferencia == -INF)
        {
            $max_diferencia = "ND";
        }
        if ($min_diferencia_acumulada == INF)
        {
            $min_diferencia_acumulada = "ND";
        }
        if ($max_diferencia_acumulada == -INF)
        {
            $max_diferencia_acumulada = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "tabla_parametros_proyecto" => $datos_tabla_parametros_proyecto,
            "tabla_informacion_proyecto" => $datos_tabla_informacion_proyecto,
            "min_fecha" => $min_fecha,
            "max_fecha" => $max_fecha,
            "grafica_valores" => $grafica_valores->dame_datos(),
            "bandas_valores" => $bandas_valores->dame_datos(),
            "min_valor" => $min_valor,
            "max_valor" => $max_valor,
            "etiquetas_valores" => $nombres_grafica_valores->dame_datos(),
            "grafica_diferencias" => $grafica_diferencias->dame_datos(),
            "min_diferencia" => $min_diferencia,
            "max_diferencia" => $max_diferencia,
            "campo_incremental" => $campo_incremental,
            "grafica_diferencias_acumuladas" => $grafica_diferencias_acumuladas->dame_datos(),
            "min_diferencia_acumulada" => $min_diferencia_acumulada,
            "max_diferencia_acumulada" => $max_diferencia_acumulada,
            "numero_decimales_valores" => $numero_decimales_valores,
            "unidad_medida" => $unidad_medida,
            "intervalo_valores" => $intervalo_valores,
            "tabla_valores_adicionales_proyecto" => $datos_tabla_valores_adicionales_proyecto,
            "tabla_error_coeficientes_linea_base" => $datos_tabla_error_coeficientes_linea_base,
            "tabla_errores_coeficientes_lineas_base_excepciones" => $datos_tabla_errores_coeficientes_lineas_base_excepciones);
        return ($resultado);
    }


    // Devuelve información del estado de un proyecto
    function dame_informacion_estado_proyecto($parametros)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $id_proyecto = $parametros["id_proyecto"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $clase_unidad_medida = $parametros["clase_unidad_medida"];

        // Se comprueba si el proyecto es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        $ids_proyectos_usuario_actual = Proyecto::dame_ids_proyectos_usuario_actual($ids_sensores_usuario_actual);
        if (in_array($id_proyecto, $ids_proyectos_usuario_actual) == false)
        {
            throw new Exception("Proyecto no visible por el usuario actual (id: '".$id_proyecto."')");
        }

        // Se recupera la información de proyecto
        $fila_proyecto = dame_fila_proyecto($id_proyecto);

        // Si el estado de avance del proyecto no es válido, se devuelve que no hay datos disponibles
        $estado_avance_proyecto = $fila_proyecto["estado_avance"];
        switch ($estado_avance_proyecto)
        {
            case ESTADO_AVANCE_PROYECTO_SIN_VALOR_OBJETIVO:
            case ESTADO_AVANCE_PROYECTO_NEGATIVO:
            case ESTADO_AVANCE_PROYECTO_POSITIVO:
            {
                break;
            }
            default:
            {
                $resultado = array(
                    "res" => "OK",
                    "hay_datos" => false);
                return ($resultado);
            }
        }

        // Conversión de fechas
        $zona_horaria_local = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_base_datos_local = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_local = convierte_formato_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $fecha_hora_inicio_local = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria_local);
        $fecha_hora_fin_local = convierte_cadena_a_fecha($cadena_fecha_hora_fin_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria_local);

        // Fechas y horas de inicio y fin de proyecto
        $cadena_fecha_hora_inicio_proyecto_base_datos_local = $fila_proyecto["fecha_inicio"]." 00:00:00";
        $cadena_fecha_hora_fin_proyecto_base_datos_local = $fila_proyecto["fecha_fin"]." 23:59:59";
        $fecha_hora_inicio_proyecto_local = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_proyecto_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria_local);
        $fecha_hora_fin_proyecto_local = convierte_cadena_a_fecha($cadena_fecha_hora_fin_proyecto_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria_local);

        // Si la fecha de inicio o fecha de fin está fuera de rango de fechas de proyecto, se establecen a las fechas del proyecto
        if ($fecha_hora_inicio_local < $fecha_hora_inicio_proyecto_local)
        {
            $fecha_hora_inicio_proyecto_local = $fecha_hora_inicio_local;
            $cadena_fecha_hora_inicio_base_datos_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, FORMATO_FECHA_HORA_BASE_DATOS);
        }
        if ($fecha_hora_fin_local > $fecha_hora_fin_proyecto_local)
        {
            $fecha_hora_fin_proyecto_local = $fecha_hora_fin_local;
            $cadena_fecha_hora_fin_base_datos_local = convierte_fecha_a_cadena($fecha_hora_fin_local, FORMATO_FECHA_HORA_BASE_DATOS);
        }

        // Unidad de medida y número de decimales
        $clase_sensor = $fila_proyecto["clase_sensor"];
        $id_sensor = $fila_proyecto["sensor"];
        $campo = $fila_proyecto["campo"];
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
        $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);

        // Se realiza la consulta de valores reales y simulados del proyecto
        $consulta_valores_reales_simulados = "
            SELECT *
            FROM valores_reales_simulados_proyectos
            WHERE
                (id_proyecto = '".$bd_datos->_($id_proyecto)."')
                AND (fecha_hora_local >= '".$cadena_fecha_hora_inicio_base_datos_local."')
                AND (fecha_hora_local <= '".$cadena_fecha_hora_fin_base_datos_local."')
            ORDER BY
                fecha_hora_utc ASC";
        $res_valores_reales_simulados = $bd_datos->ejecuta_consulta($consulta_valores_reales_simulados);
        if ($res_valores_reales_simulados == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_reales_simulados."'");
        }

        // Si no hay datos no se hace nada
        $numero_valores_reales_simulados = $res_valores_reales_simulados->dame_numero_filas();
        if ($numero_valores_reales_simulados == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se recorren las filas de valores reales y simulados del proyecto
        $valor_real_avance = 0;
    	$valor_simulado_avance = 0;
        while ($fila_valores_reales_simulados = $res_valores_reales_simulados->dame_siguiente_fila())
        {
            $cadena_fecha_valor_real_simulado_proyecto_base_datos_local = $fila_valores_reales_simulados["fecha_hora_local"];

            $valor_real_proyecto = (float) $fila_valores_reales_simulados["valor_real"];
            $valor_simulado_proyecto = (float) $fila_valores_reales_simulados["valor_simulado"];

            $valor_real_avance += $valor_real_proyecto;
            $valor_simulado_avance += $valor_simulado_proyecto;
        }

        // Valores reales y simulados de avance
        $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);
        if ($tipo_valores_campo == TIPO_VALORES_SENSOR_PUNTUALES)
        {
            $valor_real_avance /= $numero_valores_reales_simulados;
            $valor_simulado_avance /= $numero_valores_reales_simulados;
        }

        // Se calculan los datos para la información del proyecto
        $cadena_hora_fin_valores_avance_local_local = $cadena_fecha_hora_fin_local_local;
        $cadena_hora_ultimos_valores_avance_local_local = convierte_formato_fecha($cadena_fecha_valor_real_simulado_proyecto_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
        $cadena_valor_real_avance = formatea_numero($valor_real_avance, $numero_decimales_valores);
        if ($unidad_medida != "")
        {
            $cadena_valor_real_avance .= " ".$unidad_medida;
        }
        $cadena_valor_simulado_avance = formatea_numero($valor_simulado_avance, $numero_decimales_valores);
        if ($unidad_medida != "")
        {
            $cadena_valor_simulado_avance .= " ".$unidad_medida;
        }
        $estado_proyecto = $fila_proyecto["estado"];

        // Porcentaje de finalización del proyecto
		// (no se tiene en cuenta el día actual - sólo se cuentan días completos)
        switch ($estado_proyecto)
        {
            case ESTADO_PROYECTO_ACTIVO:
            case ESTADO_PROYECTO_FINALIZADO:
            {
                $fecha_hora_fin_valores_avance_local = convierte_cadena_a_fecha($cadena_hora_fin_valores_avance_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria_local);
                $numero_dias_duracion_proyecto = $fecha_hora_fin_proyecto_local->diff($fecha_hora_inicio_proyecto_local)->days + 1;
                $numero_dias_actuales_proyecto = $fecha_hora_fin_valores_avance_local->diff($fecha_hora_inicio_local)->days + 1;
                $porcentaje_finalizacion = ($numero_dias_actuales_proyecto * 100) / $numero_dias_duracion_proyecto;
                if ($porcentaje_finalizacion > 100)
                {
                    $porcentaje_finalizacion = 100;
                }
                switch ($estado_proyecto)
                {
                    case ESTADO_PROYECTO_FINALIZADO:
                    {
                        if ($porcentaje_finalizacion < 100)
                        {
                            $estado_proyecto = ESTADO_PROYECTO_ACTIVO;
                        }
                        break;
                    }
                }
                break;
            }
            default:
            {
                $porcentaje_finalizacion = NULL;
                break;
            }
        }
        $descripcion_avance_proyecto = Proyecto::dame_descripcion_avance_proyecto(
            $fila_proyecto,
            $valor_real_avance,
            $valor_simulado_avance,
            $porcentaje_finalizacion,
            $clase_unidad_medida);
        $descripcion_estado_proyecto = Proyecto::dame_descripcion_estado_proyecto_porcentaje_finalizacion(
            $estado_proyecto,
            $porcentaje_finalizacion,
            $clase_unidad_medida);

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "estado_avance_proyecto" => $estado_avance_proyecto,
            "estado_proyecto" => $estado_proyecto,
            "cadena_hora_fin_valores_avance_local_local" => $cadena_hora_fin_valores_avance_local_local,
            "cadena_hora_ultimos_valores_avance_local_local" => $cadena_hora_ultimos_valores_avance_local_local,
            "descripcion_avance_proyecto" => $descripcion_avance_proyecto,
            "descripcion_estado_proyecto" => $descripcion_estado_proyecto);
        return ($resultado);
    }


    //
    // Funciones auxiliares
    //


    // Añade la información de valores adicionales al tooltip
    function anyade_informacion_valores_adicionales_tooltip(
        &$tooltip_valor,
        $destino_valor_adicional,
        $fila_valores_reales_simulados,
        $info_valores_adicionales_proyecto,
        $unidad_medida,
        $numero_decimales_valores)
    {
        $idiomas = new Idiomas();

        switch ($destino_valor_adicional)
        {
            case DESTINO_VALOR_ADICIONAL_PROYECTO_VALORES_REALES:
            {
                if ($fila_valores_reales_simulados["ids_valores_adicionales_reales"] == "")
                {
                    return;
                }
                $valor_sensor = $fila_valores_reales_simulados["valor_real_sensor"];
                $ids_valores_adicionales = explode(",", $fila_valores_reales_simulados["ids_valores_adicionales_reales"]);
                $valores_adicionales = explode(",", $fila_valores_reales_simulados["valores_adicionales_reales"]);
                break;
            }
            case DESTINO_VALOR_ADICIONAL_PROYECTO_VALORES_SIMULADOS:
            {
                if ($fila_valores_reales_simulados["ids_valores_adicionales_simulados"] == "")
                {
                    return;
                }
                $valor_sensor = $fila_valores_reales_simulados["valor_simulado_sensor"];
                $ids_valores_adicionales = explode(",", $fila_valores_reales_simulados["ids_valores_adicionales_simulados"]);
                $valores_adicionales = explode(",", $fila_valores_reales_simulados["valores_adicionales_simulados"]);
                break;
            }
        }

        // Se añaden el valor de sensor y los nombres y valores de cada uno de los valores adicionales
        if (count($ids_valores_adicionales) > 0)
        {
            $tooltip_valor .= "<ul>";

            // Valor de sensor
            if ($valor_sensor !== NULL)
            {
                $cadena_valor_sensor = formatea_numero($valor_sensor, $numero_decimales_valores);
                if ($unidad_medida != "")
                {
                    $cadena_valor_sensor .= " ".$unidad_medida;
                }
            }
            else
            {
                $cadena_valor_sensor = $idiomas->_("ND");
            }
            $tooltip_valor .= "<li>".$idiomas->_("Valor de sensor").": ".$cadena_valor_sensor."</li>";

            // Nombres y valores de los valores adicionales
            for ($i = 0; $i < count($ids_valores_adicionales); $i++)
            {
                $id_valor_adicional = $ids_valores_adicionales[$i];
                $valor_adicional = $valores_adicionales[$i];

                $info_valor_adicional = $info_valores_adicionales_proyecto[$id_valor_adicional];
                $nombre_valor_adicional = $info_valor_adicional["nombre"];
                $cadena_valor_adicional = formatea_numero($valor_adicional, $numero_decimales_valores);
                if ($unidad_medida != "")
                {
                    $cadena_valor_adicional .= " ".$unidad_medida;
                }
                $tooltip_valor .= "<li>".htmlspecialchars($nombre_valor_adicional, ENT_QUOTES).": ".$cadena_valor_adicional."</li>";
            }

            $tooltip_valor .= "</ul>";
        }
    }


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_proyectos_informacion_proyecto()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_PARAMETROS_PROYECTO);
        array_push($elementos_informe, ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_INFORMACION_PROYECTO);
        array_push($elementos_informe, ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_GRAFICA_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_GRAFICA_DIFERENCIAS);
        array_push($elementos_informe, ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_GRAFICA_DIFERENCIAS_ACUMULADAS);
        array_push($elementos_informe, ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_VALORES_ADICIONALES_PROYECTO);
        array_push($elementos_informe, ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_ERROR_COEFICIENTES_LINEA_BASE);
        array_push($elementos_informe, ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_ERRORES_COEFICIENTES_LINEAS_BASE_EXCEPCIONES);
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_proyectos_informacion_proyecto($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_PARAMETROS_PROYECTO:
            {
                $descripcion = "Tabla de parámetros de proyecto";
                break;
            }
            case ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_INFORMACION_PROYECTO:
            {
                $descripcion = "Tabla de información de proyecto";
                break;
            }
            case ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_GRAFICA_VALORES:
            {
                $descripcion = "Gráfica de valores";
                break;
            }
            case ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_GRAFICA_DIFERENCIAS:
            {
                $descripcion = "Gráfica de diferencias";
                break;
            }
            case ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_GRAFICA_DIFERENCIAS_ACUMULADAS:
            {
                $descripcion = "Gráfica de diferencias acumuladas";
                break;
            }
            case ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_VALORES_ADICIONALES_PROYECTO:
            {
                $descripcion = "Tabla de valores adicionales";
                break;
            }
            case ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_ERROR_COEFICIENTES_LINEA_BASE:
            {
                $descripcion = "Tabla de error y coeficientes de línea base";
                break;
            }
            case ELEMENTO_INFORME_PROYECTOS_INFORMACION_PROYECTO_TABLA_ERRORES_COEFICIENTES_LINEAS_BASE_EXCEPCIONES:
            {
                $descripcion = "Tabla de errores y coeficientes de líneas base de excepciones";
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
    // Funciones de informes
    //


    function dame_html_informe_tipo_proyectos_informacion_proyecto($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-proyectos-informacion-proyecto'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-proyectos-informacion-proyecto' hidden>
                        <div class='tabla-datos100' id='contenedor-tabla-parametros-proyecto-informacion-proyecto'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-informacion-proyecto-informacion-proyecto'></div>
                        <div class='grafica100' id='grafica-valores-informacion-proyecto'></div>
                        <div class='grafica100' id='grafica-diferencias-informacion-proyecto'></div>
                        <div class='grafica100' id='grafica-diferencias-acumuladas-informacion-proyecto'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-valores-adicionales-proyecto-informacion-proyecto'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-error-coeficientes-linea-base-informacion-proyecto'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-errores-coeficientes-lineas-base-excepciones-informacion-proyecto'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de información de proyecto (1)
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-proyecto-1'>";
                $html_informe .= dame_html_cabecera_informe_fichero_proyectos_informacion(TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-proyecto-1'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-parametros-proyecto-informacion-proyecto'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-informacion-proyecto-informacion-proyecto'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-informacion-proyecto'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-diferencias-informacion-proyecto'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-diferencias-acumuladas-informacion-proyecto'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de información de proyecto (2)
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-proyecto-2'>";
                $html_informe .= dame_html_cabecera_informe_fichero_proyectos_informacion(TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-proyecto-2'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-valores-adicionales-proyecto-informacion-proyecto'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-error-coeficientes-linea-base-informacion-proyecto'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-errores-coeficientes-lineas-base-excepciones-informacion-proyecto'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    //
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_proyectos_informacion_proyecto(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_elemento = "";
        $prefijo_elemento = "elemento".$numero_elemento."-";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-proyecto-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay proyecto seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-parametros-proyecto-informacion-proyecto'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-informacion-proyecto-informacion-proyecto'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-informacion-proyecto'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-informacion-proyecto'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-acumuladas-informacion-proyecto'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-valores-adicionales-proyecto-informacion-proyecto'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-error-coeficientes-linea-base-informacion-proyecto'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-errores-coeficientes-lineas-base-excepciones-informacion-proyecto'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-proyecto-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay proyecto seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-parametros-proyecto-informacion-proyecto'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-informacion-proyecto-informacion-proyecto'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-informacion-proyecto'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-informacion-proyecto'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-acumuladas-informacion-proyecto'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-valores-adicionales-proyecto-informacion-proyecto'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-error-coeficientes-linea-base-informacion-proyecto'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-errores-coeficientes-lineas-base-excepciones-informacion-proyecto'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_proyectos_informacion_proyecto(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Si no hay proyecto seleccionado, se devuelve sin proyecto
        if ($parametros_tipo_elemento["id_proyecto"] == ID_NINGUNO)
        {
            $resultado = array(
                "res" => "OK",
                "sin_proyecto_seleccionado" => true);
            return ($resultado);
        }

        // Parámetros de tipo de elemento
        $id_proyecto = $parametros_tipo_elemento["id_proyecto"];
        $modificar_periodo_tiempo = $parametros_tipo_elemento["modificar_periodo_tiempo"];

        // Si no se modifica el periodo de tiempo, la fecha de inicio es la fecha de inicio del proyecto
        if ($modificar_periodo_tiempo == VALOR_NO)
        {
            // Se recupera la fila del proyecto
            $fila_proyecto = dame_fila_proyecto($id_proyecto);

            // Fecha y hora de inicio (de proyecto)
            $cadena_fecha_hora_inicio_base_datos_local = $fila_proyecto["fecha_inicio"]." 00:00:00";
            $cadena_fecha_hora_inicio_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
            $parametros_informe["fecha_hora_inicio"] = $cadena_fecha_hora_inicio_local_local;
        }

        // Parámetros del informe
        $parametros_informe["id_proyecto"] = $id_proyecto;
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_informacion_proyecto($parametros_informe);
        return ($datos_elemento);
    }
?>



