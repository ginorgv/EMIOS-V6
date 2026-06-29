<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');

	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');


    //
    // Funciones de información de procesado
    //


    // Se recupera y devuelve la información de tiempos de ejecución de procesado
    function dame_informacion_tiempos_ejecucion_procesado($parametros)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $tipo_ejecucion_procesado = $parametros["tipo_ejecucion_procesado"];
        $clase_sensor = $parametros["clase_sensor"];
        $tipo_sensor = $parametros["tipo_sensor"];
        $granularidad = $parametros["granularidad"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Se realiza la consulta de tiempos de ejecución
        $consulta_ejecuciones = "
            SELECT
                hora AS fecha_hora,
                segundos_ejecucion
            FROM ejecuciones_procesado
            WHERE
                (segundos_ejecucion IS NOT NULL)
                AND (tipo_ejecucion_procesado = '".$bd_datos->_($tipo_ejecucion_procesado)."')";
        if ($clase_sensor != CLASE_NINGUNA)
        {
            $consulta_ejecuciones .= "
                AND (tipo_procesado = '".TIPO_PROCESADO_CLASE_SENSOR."')
                AND (clase_tipo = '".$bd_datos->_($clase_sensor)."')";
        }
        if ($tipo_sensor != TIPO_NINGUNO)
        {
            $consulta_ejecuciones .= "
                AND (tipo_procesado = '".TIPO_PROCESADO_TIPO_SENSOR."')
                AND (clase_tipo = '".$bd_datos->_($tipo_sensor)."')";
        }
        $consulta_ejecuciones .= "
                AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";
        if ($granularidad != GRANULARIDAD_NINGUNA)
        {
            $consulta_ejecuciones .= "
                AND (granularidad = '".$bd_datos->_($granularidad)."')";
        }

        // Se añaden el horario semanal y la exclusión e inclusión de fechas
        $consulta_ejecuciones .= dame_filtro_consulta_horario_semanal_fechas(
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas);

        // Se ejecuta la consulta de tiempos de ejecución
        $res_ejecuciones = $bd_datos->ejecuta_consulta($consulta_ejecuciones);
        if ($res_ejecuciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_ejecuciones."'");
        }

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        switch ($granularidad)
        {
            case GRANULARIDAD_NINGUNA:
            {
                // Nota: El intervalo entre ejecuciones de procesado de recalculos es de 10 minutos
                $segundos_maximos_entre_valores_grafica = (600 * 2) - 1;
                break;
            }
            case GRANULARIDAD_CUARTOHORARIA:
            {
                $segundos_maximos_entre_valores_grafica = (900 * 2) - 1;
                break;
            }
            case GRANULARIDAD_HORARIA:
            {
                $segundos_maximos_entre_valores_grafica = (3600 * 2) - 1;
                break;
            }
            default:
            {
                throw new Exception("Granularidad incorrecta: '".$granularidad."'");
            }
        }

        // Se recorren las filas de las ejecuciones
        $numero_ocurrencias = 0;
        $suma_segundos_ejecucion = 0;
        $datos_grafica_tiempos_ejecucion = new VectorDatos();
        $min_segundos_ejecucion = INF;
        $max_segundos_ejecucion = -INF;
        $timestamp_fecha_hora_segundos_ejecucion_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        while ($fila_ejecucion = $res_ejecuciones->dame_siguiente_fila())
        {
            // Fecha y segundos de ejecución
            $cadena_fecha_hora_base_datos_utc = $fila_ejecucion['fecha_hora'];
            $segundos_ejecucion = (float) $fila_ejecucion['segundos_ejecucion'];
            if ($segundos_ejecucion > $max_segundos_ejecucion)
            {
                $max_segundos_ejecucion = $segundos_ejecucion;
            }
            if ($segundos_ejecucion < $min_segundos_ejecucion)
            {
                $min_segundos_ejecucion = $segundos_ejecucion;
            }

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_segundos_ejecucion_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_segundos_ejecucion_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_segundos_ejecucion_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_segundos_ejecucion_utc - $timestamp_fecha_hora_segundos_ejecucion_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_grafica_tiempos_ejecucion->anyade_tupla_pareja_datos($timestamp_fecha_hora_segundos_ejecucion_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_segundos_ejecucion_anterior_utc = $timestamp_fecha_hora_segundos_ejecucion_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Se añaden los segundos de ejecución
            $timestamp_fecha_hora_ejecucion = dame_timestamp_cadena_fecha_milisegundos($fila_ejecucion['fecha_hora'], FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_ejecucion -= $milisegundos_desfase_zonas_horarias_cliente_local;
            $datos_grafica_tiempos_ejecucion->anyade_tupla_pareja_datos($timestamp_fecha_hora_ejecucion, $segundos_ejecucion);

            // Número de ocurrencias y suma de valores
            $numero_ocurrencias += 1;
            $suma_segundos_ejecucion += $segundos_ejecucion;


        }

        // Si no hay datos no se hace nada
        if ($numero_ocurrencias == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se recupera el nombre de la clase o del tipo según corresponda
        if ($clase_sensor != CLASE_NINGUNA)
        {
            $nombre_grafica = NodoSensor::dame_descripcion_clase_sensor($clase_sensor);
        }
        if ($tipo_sensor != TIPO_NINGUNO)
        {
            $nombre_grafica = NodoSensor::dame_descripcion_tipo_sensor($tipo_sensor);
        }

        // Variables para dibujar las gráficas
        $grafica_tiempos_ejecucion = new VectorDatos();
        $grafica_tiempos_ejecucion->anyade_dato($datos_grafica_tiempos_ejecucion->dame_datos());
        $nombre_grafica_tiempos_ejecucion = new VectorDatos();
        $nombre_grafica_tiempos_ejecucion->anyade_etiqueta($nombre_grafica);

        // Texto de información de los datos
        if ($numero_ocurrencias > 0)
        {
            $min_segundos_ejecucion = round($min_segundos_ejecucion, 2);
            $max_segundos_ejecucion = round($max_segundos_ejecucion, 2);

            $cadena_min_segundos_ejecucion = formatea_numero($min_segundos_ejecucion, 2);
            $cadena_max_segundos_ejecucion = formatea_numero($max_segundos_ejecucion, 2);

            $media_segundos_ejecucion = formatea_numero($suma_segundos_ejecucion / $numero_ocurrencias, 2);

            $texto_informacion_datos_tiempos_ejecucion = "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Rango de tiempos de ejecución del periodo").": ".$cadena_min_segundos_ejecucion." / ".$cadena_max_segundos_ejecucion." ". $idiomas->_("segundos")."<br/>";
            $texto_informacion_datos_tiempos_ejecucion .= "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Tiempo de ejecución medio del periodo").": ".$media_segundos_ejecucion." ". $idiomas->_("segundos")."<br/>";
        }
        else
        {
            // Nota: Los valores -INF y INF no se convierten correctamente a cadena... hay que establecer una valor "válido"
            $max_segundos_ejecucion = "ND";
        }

        // Valores máximos y mínimos
        if ($max_segundos_ejecucion == -INF)
        {
            $max_segundos_ejecucion = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "max_segundos_ejecucion" => $max_segundos_ejecucion,
            "etiquetas_grafica_tiempos_ejecucion" => $nombre_grafica_tiempos_ejecucion->dame_datos(),
            "grafica_tiempos_ejecucion" => $grafica_tiempos_ejecucion->dame_datos(),
            "texto_informacion_datos_tiempos_ejecucion" => $texto_informacion_datos_tiempos_ejecucion);
        return ($resultado);
    }


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_informacion_tiempos_ejecucion_procesado()
    {
        $idiomas = new Idiomas();

        $html_informe = "
            <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-monitorizacion-tiempos-ejecucion-procesado'>
                <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
            </div>
            <div id='informe-monitorizacion-tiempos-ejecucion-procesado' hidden>
                <div class='grafica100' id='grafica-tiempos-ejecucion-procesado'></div>
                <div class='texto100' id='texto-informacion-tiempos-ejecucion-procesado'></div>
            </div>";
        return ($html_informe);
    }
?>
