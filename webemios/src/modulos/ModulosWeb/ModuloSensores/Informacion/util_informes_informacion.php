<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/ValoresMapaCalor.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/Comentario.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/util_comentarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_informes_plantillas_informes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Informacion/InformesFichero/util_informacion_informes_fichero.php');


    //
    // Funciones de información de sensores
    //


    function dame_informacion_sensor_temperatura($parametros, $filas_valores_sensor)
    {
        // Si no hay datos no se hace nada
        if (count($filas_valores_sensor) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        $idiomas = new Idiomas();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $campo = $parametros["campo"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $tipo_mapa_calor = $parametros["tipo_mapa_calor"];
        $comentarios = $parametros["comentarios"];
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $tipo_informe = $parametros["tipo_informe"];
        $numero_elemento_plantilla_informe = $parametros["numero_elemento_plantilla_informe"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Zona horaria
        $zona_horaria = dame_zona_horaria_local();

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Variables del informe
        $mostrar_mapas_calor = dame_mostrar_mapa_calor_sensor_informacion($intervalo_valores, $tipo_mapa_calor);
        $valores_mapa_calor_temperatura = new ValoresMapaCalor($tipo_mapa_calor);
        $numero_ocurrencias_temperatura = 0;
        $suma_temperaturas = 0;
        $datos_grafica_temperatura = new VectorDatos();
        $min_temperatura = INF;
        $max_temperatura = -INF;
        $cadena_fecha_hora_inicio_valores_base_datos_utc = NULL;
        $cadena_fecha_hora_fin_valores_base_datos_utc = NULL;

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Se recorren los valores del sensor
        $timestamp_fecha_hora_temperatura_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        foreach ($filas_valores_sensor as $fila_valor_sensor)
        {
            // Fecha y temperatura
            $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
            $temperatura = (float) $fila_valor_sensor[$campo];
            if ($temperatura > $max_temperatura)
            {
                $max_temperatura = $temperatura;
            }
            if ($temperatura < $min_temperatura)
            {
                $min_temperatura = $temperatura;
            }
            if ($cadena_fecha_hora_inicio_valores_base_datos_utc === NULL)
            {
                $cadena_fecha_hora_inicio_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
            }
            $cadena_fecha_hora_fin_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_temperatura_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_temperatura_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_temperatura_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_temperatura_utc - $timestamp_fecha_hora_temperatura_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_grafica_temperatura->anyade_tupla_pareja_datos($timestamp_fecha_hora_temperatura_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_temperatura_anterior_utc = $timestamp_fecha_hora_temperatura_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade la temperatura
            $datos_grafica_temperatura->anyade_tupla_pareja_datos($timestamp_fecha_hora_temperatura_utc, $temperatura);

            // Número de ocurrencias y suma de valores
            $numero_ocurrencias_temperatura += 1;
            $suma_temperaturas += $temperatura;

            // Datos para el mapa de calor de temperatura
            if ($mostrar_mapas_calor == true)
            {
                $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);
                $valores_mapa_calor_temperatura->anyade_valor_fecha_hora($fecha_hora_local, $temperatura);
            }
        }

        // Descripción del campo, número de decimales de valores y unidad de medida
        $descripcion_campo = dame_descripcion_campo_clase_sensor(CLASE_SENSOR_TEMPERATURA, $campo);
        $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor(CLASE_SENSOR_TEMPERATURA, $campo);
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor(CLASE_SENSOR_TEMPERATURA, ID_NINGUNO, $campo);

        // Variables para dibujar las gráficas
        $etiquetas_grafica_temperatura = new VectorDatos();
        $etiquetas_grafica_temperatura->anyade_etiqueta($nombre_sensor);
        $grafica_temperatura = new VectorDatos();
        $grafica_temperatura->anyade_dato($datos_grafica_temperatura->dame_datos());

        // Variables de comentarios
        $filas_comentarios = array();
        $lineas_verticales_comentarios = array();
        $tabla_comentarios = NULL;

        // Comentarios y texto de información de los datos
        if ($numero_ocurrencias_temperatura > 0)
        {
            $cadena_fecha_hora_inicio_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);

            $cadena_fecha_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);
            $cadena_fecha_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);

            // Se recuperan los comentarios y las líneas verticales para la gráfica
            // - Nota: No se utiliza el horario semanal ni las fechas para mostrar todos los comentarios entre la fecha de inicio y fin del informe
            //   (puede ser que haya comentarios en periodos que no se visualicen en la gráfica pero que puedan ser relevantes)
            switch ($comentarios)
            {
                case COMENTARIOS_GRAFICA:
                case COMENTARIOS_GRAFICA_TABLA:
                {
                    $nombres_sensores_comentarios = dame_nombres_sensores_descendientes_comentarios(array($id_sensor));
                    $filas_comentarios = Comentario::dame_filas_comentarios_objetos(
                        ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION,
                        $nombres_sensores_comentarios,
                        $cadena_fecha_hora_inicio_valores_base_datos_utc,
                        $cadena_fecha_hora_fin_valores_base_datos_utc,
                        NULL,
                        NULL,
                        NULL);
                    $lineas_verticales_comentarios = Comentario::dame_lineas_verticales_comentarios_informe(
                        $filas_comentarios,
                        false,
                        $milisegundos_desfase_zonas_horarias_cliente_local);
                    break;
                }
            }

            $min_temperatura = round($min_temperatura, $numero_decimales_valores);
            $max_temperatura = round($max_temperatura, $numero_decimales_valores);

            $cadena_min_temperatura = formatea_numero($min_temperatura, $numero_decimales_valores);
            $cadena_max_temperatura = formatea_numero($max_temperatura, $numero_decimales_valores);
            $temperatura_media = formatea_numero($suma_temperaturas / $numero_ocurrencias_temperatura, $numero_decimales_valores);

            // Campo
            switch ($campo)
            {
                case CAMPO_TEMPERATURA:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de temperaturas del periodo").": ".$cadena_min_temperatura." ".$unidad_medida." / ".$cadena_max_temperatura." ".$unidad_medida.
                        " (".$idiomas->_("número de muestras").": ".formatea_numero($numero_ocurrencias_temperatura, 0).")"."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Temperatura media del periodo").": ".$temperatura_media." ".$unidad_medida."<br/>";
                    break;
                }
                case CAMPO_GRADOS_HORA_CALEFACCION:
                case CAMPO_GRADOS_HORA_REFRIGERACION:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de grados hora del periodo").": ".$cadena_min_temperatura." ".$unidad_medida." / ".$cadena_max_temperatura." ".$unidad_medida.
                        " (".$idiomas->_("número de muestras").": ".formatea_numero($numero_ocurrencias_temperatura, 0).")"."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Grados hora medios del periodo").": ".$temperatura_media." ".$unidad_medida."<br/>";

                    switch ($intervalo_valores)
                    {
                        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
                        case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
                        {
                            $temperatura_acumulada = $idiomas->_("ND");
                            break;
                        }
                        default:
                        {
                            $temperatura_acumulada = formatea_numero($suma_temperaturas, $numero_decimales_valores);
                            break;
                        }
                    }
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Grados hora totales del periodo").": ".$temperatura_acumulada." ".$unidad_medida."<br/>";
                    break;
                }
                case CAMPO_GRADOS_DIA_CALEFACCION:
                case CAMPO_GRADOS_DIA_REFRIGERACION:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de grados día del periodo").": ".$cadena_min_temperatura." ".$unidad_medida." / ".$cadena_max_temperatura." ".$unidad_medida.
                        " (".$idiomas->_("número de muestras").": ".formatea_numero($numero_ocurrencias_temperatura, 0).")"."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Grados día medios del periodo").": ".$temperatura_media." ".$unidad_medida."<br/>";

                    switch ($intervalo_valores)
                    {
                        case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
                        case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
                        {
                            $temperatura_acumulada = $idiomas->_("ND");
                            break;
                        }
                        default:
                        {
                            $temperatura_acumulada = formatea_numero($suma_temperaturas, $numero_decimales_valores);
                            break;
                        }
                    }
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Grados día totales del periodo").": ".$temperatura_acumulada." ".$unidad_medida."<br/>";
                    break;
                }
            }
        }

        // Tabla y número de comentarios
        if ($comentarios == COMENTARIOS_GRAFICA_TABLA)
        {
            if ((count($filas_comentarios) > 0) ||
                (($tipo_informe == TIPO_INFORME_WEB_EMIOS) && (NodoSensor::dame_administracion_comentarios_sensores() == true)))
            {
                if ($numero_elemento_plantilla_informe === NULL)
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA;
                }
                else
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA.",".$numero_elemento_plantilla_informe;
                }
                $tabla_comentarios = Comentario::dame_tabla_comentarios_objetos_informe(
                    $origen_comentarios,
                    $parametros_origen_comentarios,
                    "tabla-comentarios-sensor-informacion-temperatura",
                    $filas_comentarios,
                    NULL,
                    array($nombre_sensor),
                    $tipo_informe);
            }
        }
        $numero_comentarios = count($filas_comentarios);

        // Descripción del sensor
        $descripcion_sensor = dame_descripcion_sensor_informe($id_sensor);

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($min_temperatura == INF)
        {
            $min_temperatura = "ND";
        }
        if ($max_temperatura == -INF)
        {
            $max_temperatura = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "min_temperatura" => $min_temperatura,
            "max_temperatura" => $max_temperatura,
            "etiquetas_grafica_temperatura" => $etiquetas_grafica_temperatura->dame_datos(),
            "grafica_temperatura" => $grafica_temperatura->dame_datos(),
            "lineas_verticales_comentarios" => $lineas_verticales_comentarios,
            "tabla_comentarios" => $tabla_comentarios,
            "numero_comentarios" => $numero_comentarios,
            "fecha_inicio_valores" => $cadena_fecha_inicio_valores_local_local,
            "hora_inicio_valores" => $cadena_hora_inicio_valores_local_local,
            "fecha_fin_valores" => $cadena_fecha_fin_valores_local_local,
            "hora_fin_valores" => $cadena_hora_fin_valores_local_local,
            "dias_mapa_calor_temperatura" => $valores_mapa_calor_temperatura->dame_dias(),
            "datos_mapa_calor_temperatura" => $valores_mapa_calor_temperatura->dame_datos(),
            "texto_informacion_datos" => $texto_informacion_datos,
            "descripcion_campo" => $descripcion_campo,
            "numero_decimales_valores" => $numero_decimales_valores,
            "unidad_medida" => $unidad_medida,
            "descripcion_sensor" => $descripcion_sensor);
        return ($resultado);
    }


    function dame_informacion_sensor_humedad($parametros, $filas_valores_sensor)
    {
        // Si no hay datos no se hace nada
        if (count($filas_valores_sensor) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        $idiomas = new Idiomas();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $tipo_mapa_calor = $parametros["tipo_mapa_calor"];
        $comentarios = $parametros["comentarios"];
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $tipo_informe = $parametros["tipo_informe"];
        $numero_elemento_plantilla_informe = $parametros["numero_elemento_plantilla_informe"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Zona horaria
        $zona_horaria = dame_zona_horaria_local();

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Variables del informe
        $mostrar_mapas_calor = dame_mostrar_mapa_calor_sensor_informacion($intervalo_valores, $tipo_mapa_calor);
        $valores_mapa_calor_humedad = new ValoresMapaCalor($tipo_mapa_calor);
        $numero_ocurrencias_humedad = 0;
        $suma_humedades = 0;
        $datos_grafica_humedad = new VectorDatos();
        $min_humedad = INF;
        $max_humedad = -INF;
        $cadena_fecha_hora_inicio_valores_base_datos_utc = NULL;
        $cadena_fecha_hora_fin_valores_base_datos_utc = NULL;

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Se recorren las filas de valores del sensor
        $timestamp_fecha_hora_humedad_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        foreach ($filas_valores_sensor as $fila_valor_sensor)
        {
            // Fecha y humedad
            $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
            $humedad = (float) $fila_valor_sensor[CAMPO_HUMEDAD];
            if ($humedad > $max_humedad)
            {
                $max_humedad = $humedad;
            }
            if ($humedad < $min_humedad)
            {
                $min_humedad = $humedad;
            }
            if ($cadena_fecha_hora_inicio_valores_base_datos_utc === NULL)
            {
                $cadena_fecha_hora_inicio_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
            }
            $cadena_fecha_hora_fin_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_humedad_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_humedad_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_humedad_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_humedad_utc - $timestamp_fecha_hora_humedad_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_grafica_humedad->anyade_tupla_pareja_datos($timestamp_fecha_hora_humedad_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_humedad_anterior_utc = $timestamp_fecha_hora_humedad_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade la humedad
            $datos_grafica_humedad->anyade_tupla_pareja_datos($timestamp_fecha_hora_humedad_utc, $humedad);

            $numero_ocurrencias_humedad += 1;
            $suma_humedades += $humedad;

            // Datos para el mapa de calor de humedad
            if ($mostrar_mapas_calor == true)
            {
                $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);
                $valores_mapa_calor_humedad->anyade_valor_fecha_hora($fecha_hora_local, $humedad);
            }
        }

        // Número de decimales de valores
        $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor(CLASE_SENSOR_HUMEDAD, CAMPO_HUMEDAD);

        // Variables para dibujar las gráficas
        $etiquetas_grafica_humedad = new VectorDatos();
        $etiquetas_grafica_humedad->anyade_etiqueta($nombre_sensor);
        $grafica_humedad = new VectorDatos();
        $grafica_humedad->anyade_dato($datos_grafica_humedad->dame_datos());

        // Variables de comentarios
        $filas_comentarios = array();
        $lineas_verticales_comentarios = array();
        $tabla_comentarios = NULL;

        // Comentarios y texto de información de los datos
        if ($numero_ocurrencias_humedad > 0)
        {
            $cadena_fecha_hora_inicio_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);

            $cadena_fecha_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);
            $cadena_fecha_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);

            // Se recuperan los comentarios y las líneas verticales para la gráfica
            switch ($comentarios)
            {
                case COMENTARIOS_GRAFICA:
                case COMENTARIOS_GRAFICA_TABLA:
                {
                    $nombres_sensores_comentarios = dame_nombres_sensores_descendientes_comentarios(array($id_sensor));
                    $filas_comentarios = Comentario::dame_filas_comentarios_objetos(
                        ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION,
                        $nombres_sensores_comentarios,
                        $cadena_fecha_hora_inicio_valores_base_datos_utc,
                        $cadena_fecha_hora_fin_valores_base_datos_utc,
                        NULL,
                        NULL,
                        NULL);
                    $lineas_verticales_comentarios = Comentario::dame_lineas_verticales_comentarios_informe(
                        $filas_comentarios,
                        false,
                        $milisegundos_desfase_zonas_horarias_cliente_local);
                    break;
                }
            }

            // Texto de información de los datos
            $min_humedad = round($min_humedad, $numero_decimales_valores);
            $max_humedad = round($max_humedad, $numero_decimales_valores);

            $cadena_min_humedad = formatea_numero($min_humedad, $numero_decimales_valores);
            $cadena_max_humedad = formatea_numero($max_humedad, $numero_decimales_valores);
            $humedad_media = formatea_numero($suma_humedades / $numero_ocurrencias_humedad, $numero_decimales_valores);

            $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Rango de humedad del periodo").": ".$cadena_min_humedad." "."%"." / ".$cadena_max_humedad." "."%".
                " (".$idiomas->_("número de muestras").": ".formatea_numero($numero_ocurrencias_humedad, 0).")"."<br/>";
            $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Humedad media del periodo").": ".$humedad_media." "."%"."<br/>";
        }

        // Tabla y número de comentarios
        if ($comentarios == COMENTARIOS_GRAFICA_TABLA)
        {
            if ((count($filas_comentarios) > 0) ||
                (($tipo_informe == TIPO_INFORME_WEB_EMIOS) && (NodoSensor::dame_administracion_comentarios_sensores() == true)))
            {
                if ($numero_elemento_plantilla_informe === NULL)
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD;
                }
                else
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD.",".$numero_elemento_plantilla_informe;
                }
                $tabla_comentarios = Comentario::dame_tabla_comentarios_objetos_informe(
                    $origen_comentarios,
                    $parametros_origen_comentarios,
                    "tabla-comentarios-sensor-informacion-humedad",
                    $filas_comentarios,
                    NULL,
                    array($nombre_sensor),
                    $tipo_informe);
            }
        }
        $numero_comentarios = count($filas_comentarios);

        // Descripción del sensor
        $descripcion_sensor = dame_descripcion_sensor_informe($id_sensor);

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($min_humedad == INF)
        {
            $min_humedad = "ND";
        }
        if ($max_humedad == -INF)
        {
            $max_humedad = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "min_humedad" => $min_humedad,
            "max_humedad" => $max_humedad,
            "etiquetas_grafica_humedad" => $etiquetas_grafica_humedad->dame_datos(),
            "grafica_humedad" => $grafica_humedad->dame_datos(),
            "lineas_verticales_comentarios" => $lineas_verticales_comentarios,
            "tabla_comentarios" => $tabla_comentarios,
            "numero_comentarios" => $numero_comentarios,
            "fecha_inicio_valores" => $cadena_fecha_inicio_valores_local_local,
            "hora_inicio_valores" => $cadena_hora_inicio_valores_local_local,
            "fecha_fin_valores" => $cadena_fecha_fin_valores_local_local,
            "hora_fin_valores" => $cadena_hora_fin_valores_local_local,
            "dias_mapa_calor_humedad" => $valores_mapa_calor_humedad->dame_dias(),
            "datos_mapa_calor_humedad" => $valores_mapa_calor_humedad->dame_datos(),
            "texto_informacion_datos" => $texto_informacion_datos,
            "numero_decimales_valores" => $numero_decimales_valores,
            "descripcion_sensor" => $descripcion_sensor);
        return ($resultado);
    }


    function dame_informacion_sensor_luz_interior($parametros, $filas_valores_sensor)
    {
        // Si no hay datos no se hace nada
        if (count($filas_valores_sensor) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        $idiomas = new Idiomas();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $tipo_mapa_calor = $parametros["tipo_mapa_calor"];
        $comentarios = $parametros["comentarios"];
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $tipo_informe = $parametros["tipo_informe"];
        $numero_elemento_plantilla_informe = $parametros["numero_elemento_plantilla_informe"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Zona horaria
        $zona_horaria = dame_zona_horaria_local();

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Variables del informe
        $mostrar_mapas_calor = dame_mostrar_mapa_calor_sensor_informacion($intervalo_valores, $tipo_mapa_calor);
        $valores_mapa_calor_luz = new ValoresMapaCalor($tipo_mapa_calor);
        $valores_mapa_calor_luz_artificial = new ValoresMapaCalor($tipo_mapa_calor);
        $numero_ocurrencias_luz = 0;
        $suma_luces = 0;
        $datos_grafica_luz = new VectorDatos();
        $datos_grafica_luz_artificial = new VectorDatos();
        $min_luz = INF;
        $max_luz = -INF;
        $cadena_fecha_hora_inicio_valores_base_datos_utc = NULL;
        $cadena_fecha_hora_fin_valores_base_datos_utc = NULL;

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Se recorren las filas de valores del sensor
        $timestamp_fecha_hora_luz_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        foreach ($filas_valores_sensor as $fila_valores_sensor)
        {
            // Fecha y valor
            $cadena_fecha_hora_base_datos_utc = $fila_valores_sensor['fecha_hora'];
            $luz = (int) $fila_valores_sensor[CAMPO_ILUMINACION];
            $luz_artificial = (float) $fila_valores_sensor[CAMPO_LUZ_ARTIFICIAL];
            if ($luz > $max_luz)
            {
                $max_luz = $luz;
            }
            if ($luz < $min_luz)
            {
                $min_luz = $luz;
            }
            if ($luz_artificial > VALOR_SI)
            {
                $luz_artificial = VALOR_SI;
            }
            if ($luz_artificial < VALOR_NO)
            {
                $luz_artificial = VALOR_NO;
            }
            if ($cadena_fecha_hora_inicio_valores_base_datos_utc === NULL)
            {
                $cadena_fecha_hora_inicio_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
            }
            $cadena_fecha_hora_fin_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_luz_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_luz_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_luz_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_luz_utc - $timestamp_fecha_hora_luz_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_grafica_luz->anyade_tupla_pareja_datos($timestamp_fecha_hora_luz_anterior_utc + 1, NULL);
                    $datos_grafica_luz_artificial->anyade_tupla_pareja_datos($timestamp_fecha_hora_luz_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_luz_anterior_utc = $timestamp_fecha_hora_luz_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade la luz
            $datos_grafica_luz->anyade_tupla_pareja_datos($timestamp_fecha_hora_luz_utc, $luz);
            $datos_grafica_luz_artificial->anyade_tupla_pareja_datos($timestamp_fecha_hora_luz_utc, $luz_artificial);

            $numero_ocurrencias_luz += 1;
            $suma_luces += $luz;

            // Datos para los mapas de calor de luces
            if ($mostrar_mapas_calor == true)
            {
                $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);
                $valores_mapa_calor_luz->anyade_valor_fecha_hora($fecha_hora_local, $luz);
                $valores_mapa_calor_luz_artificial->anyade_valor_fecha_hora($fecha_hora_local, $luz_artificial);
            }
        }

        // Número de decimales de valores de luz
        $numero_decimales_valores_luz = dame_numero_decimales_valores_campo_clase_sensor(CLASE_SENSOR_LUZ_INTERIOR, CAMPO_ILUMINACION);

        // Variables para dibujar las gráficas
        $etiquetas_graficas = new VectorDatos();
        $etiquetas_graficas->anyade_etiqueta($nombre_sensor);
        $grafica_luz = new VectorDatos();
        $grafica_luz->anyade_dato($datos_grafica_luz->dame_datos());
        $grafica_luz_artificial = new VectorDatos();
        $grafica_luz_artificial->anyade_dato($datos_grafica_luz_artificial->dame_datos());

        // Variables de comentarios
        $filas_comentarios = array();
        $lineas_verticales_comentarios = array();
        $tabla_comentarios = NULL;

        // Comentarios y texto de información de los datos
        if ($numero_ocurrencias_luz > 0)
        {
            $cadena_fecha_hora_inicio_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);

            $cadena_fecha_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);
            $cadena_fecha_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);

            // Se recuperan los comentarios y las líneas verticales para la gráfica
            switch ($comentarios)
            {
                case COMENTARIOS_GRAFICA:
                case COMENTARIOS_GRAFICA_TABLA:
                {
                    $nombres_sensores_comentarios = dame_nombres_sensores_descendientes_comentarios(array($id_sensor));
                    $filas_comentarios = Comentario::dame_filas_comentarios_objetos(
                        ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION,
                        $nombres_sensores_comentarios,
                        $cadena_fecha_hora_inicio_valores_base_datos_utc,
                        $cadena_fecha_hora_fin_valores_base_datos_utc,
                        NULL,
                        NULL,
                        NULL);
                    $lineas_verticales_comentarios = Comentario::dame_lineas_verticales_comentarios_informe(
                        $filas_comentarios,
                        false,
                        $milisegundos_desfase_zonas_horarias_cliente_local);
                    break;
                }
            }

            $min_luz = round($min_luz, $numero_decimales_valores_luz);
            $max_luz = round($max_luz, $numero_decimales_valores_luz);

            $cadena_min_luz = formatea_numero($min_luz, $numero_decimales_valores_luz);
            $cadena_max_luz = formatea_numero($max_luz, $numero_decimales_valores_luz);
            $luz_media = formatea_numero($suma_luces / $numero_ocurrencias_luz, $numero_decimales_valores_luz);

            $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Rango de luz del periodo").": ".$cadena_min_luz." ".$idiomas->_("luxes")." / ".$cadena_max_luz." ".$idiomas->_("luxes").
                " (".$idiomas->_("número de muestras").": ".formatea_numero($numero_ocurrencias_luz, 0).")"."<br/>";
            $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Media de luz del periodo").": ".$luz_media." ".$idiomas->_("luxes")."<br/>";
        }

        // Tabla y número de comentarios
        if ($comentarios == COMENTARIOS_GRAFICA_TABLA)
        {
            if ((count($filas_comentarios) > 0) ||
                (($tipo_informe == TIPO_INFORME_WEB_EMIOS) && (NodoSensor::dame_administracion_comentarios_sensores() == true)))
            {
                if ($numero_elemento_plantilla_informe === NULL)
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR;
                }
                else
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR.",".$numero_elemento_plantilla_informe;
                }
                $tabla_comentarios = Comentario::dame_tabla_comentarios_objetos_informe(
                    $origen_comentarios,
                    $parametros_origen_comentarios,
                    "tabla-comentarios-sensor-informacion-luz-interior",
                    $filas_comentarios,
                    NULL,
                    array($nombre_sensor),
                    $tipo_informe);
            }
        }
        $numero_comentarios = count($filas_comentarios);

        // Descripción del sensor
        $descripcion_sensor = dame_descripcion_sensor_informe($id_sensor);

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($min_luz == INF)
        {
            $min_luz = "ND";
        }
        if ($max_luz == -INF)
        {
            $max_luz = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "min_luz" => $min_luz,
            "max_luz" => $max_luz,
            "etiquetas_graficas" => $etiquetas_graficas->dame_datos(),
            "grafica_luz" => $grafica_luz->dame_datos(),
            "grafica_luz_artificial" => $grafica_luz_artificial->dame_datos(),
            "lineas_verticales_comentarios" => $lineas_verticales_comentarios,
            "tabla_comentarios" => $tabla_comentarios,
            "numero_comentarios" => $numero_comentarios,
            "fecha_inicio_valores" => $cadena_fecha_inicio_valores_local_local,
            "hora_inicio_valores" => $cadena_hora_inicio_valores_local_local,
            "fecha_fin_valores" => $cadena_fecha_fin_valores_local_local,
            "hora_fin_valores" => $cadena_hora_fin_valores_local_local,
            "dias_mapas_calor" => $valores_mapa_calor_luz->dame_dias(),
            "datos_mapa_calor_luz" => $valores_mapa_calor_luz->dame_datos(),
            "datos_mapa_calor_luz_artificial" => $valores_mapa_calor_luz_artificial->dame_datos(),
            "texto_informacion_datos" => $texto_informacion_datos,
            "numero_decimales_valores_luz" => $numero_decimales_valores_luz,
            "descripcion_sensor" => $descripcion_sensor);
        return ($resultado);
    }


    function dame_informacion_sensor_viento($parametros, $filas_valores_sensor)
    {
        // Si no hay datos no se hace nada
        if (count($filas_valores_sensor) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        $idiomas = new Idiomas();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $tipo_mapa_calor = $parametros["tipo_mapa_calor"];
        $comentarios = $parametros["comentarios"];
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $tipo_informe = $parametros["tipo_informe"];
        $numero_elemento_plantilla_informe = $parametros["numero_elemento_plantilla_informe"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Zona horaria
        $zona_horaria = dame_zona_horaria_local();

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Variables del informe
        $mostrar_mapas_calor = dame_mostrar_mapa_calor_sensor_informacion($intervalo_valores, $tipo_mapa_calor);
        $valores_mapa_calor_velocidad = new ValoresMapaCalor($tipo_mapa_calor);
        $valores_mapa_calor_direccion = new ValoresMapaCalor($tipo_mapa_calor);
        $numero_ocurrencias_viento = 0;
        $suma_velocidades = 0;
        $datos_grafica_velocidad = new VectorDatos();
        $datos_grafica_direccion = new VectorDatos();
        $min_velocidad = INF;
        $max_velocidad = -INF;
        $datos_direccion = array();
        $cadena_fecha_hora_inicio_valores_base_datos_utc = NULL;
        $cadena_fecha_hora_fin_valores_base_datos_utc = NULL;

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Se recorren las filas de valores del sensor
        $timestamp_fecha_hora_viento_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        foreach ($filas_valores_sensor as $fila_valores_sensor)
        {
            // Fecha y valores
            $cadena_fecha_hora_base_datos_utc = $fila_valores_sensor['fecha_hora'];
            $velocidad = (float) $fila_valores_sensor[CAMPO_VELOCIDAD];
            $direccion = (float) $fila_valores_sensor[CAMPO_DIRECCION];
            if ($velocidad > $max_velocidad)
            {
                $max_velocidad = $velocidad;
            }
            if ($velocidad < $min_velocidad)
            {
                $min_velocidad = $velocidad;
            }
            if ($cadena_fecha_hora_inicio_valores_base_datos_utc === NULL)
            {
                $cadena_fecha_hora_inicio_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
            }
            $cadena_fecha_hora_fin_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_viento_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_viento_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_viento_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_viento_utc - $timestamp_fecha_hora_viento_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_grafica_velocidad->anyade_tupla_pareja_datos($timestamp_fecha_hora_viento_anterior_utc + 1, NULL);
                    $datos_grafica_direccion->anyade_tupla_pareja_datos($timestamp_fecha_hora_viento_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_viento_anterior_utc = $timestamp_fecha_hora_viento_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade el viento
            $datos_grafica_velocidad->anyade_tupla_pareja_datos($timestamp_fecha_hora_viento_utc, $velocidad);
            $datos_grafica_direccion->anyade_tupla_pareja_datos($timestamp_fecha_hora_viento_utc, $direccion);

            $numero_ocurrencias_viento += 1;
            $suma_velocidades += $velocidad;

            // Datos para los gráficos de velocidad y dirección de viento
            if ($velocidad < MAXIMA_VELOCIDAD_VIENTO_CALMA)
            {
                $direccion_multiplo_10 = "null";
            }
            else
            {
                // Nota: Se hace el módulo con 360 porque en ocasiones llegan orientaciones mayores que 360 º (aunque no tenga sentido)
                $direccion_multiplo_10 = (formatea_numero(floor($direccion / 10)) * 10) % 360;
                if ($direccion_multiplo_10 == 0)
                {
                    $direccion_multiplo_10 = 360;
                }
            }
            if (array_key_exists($direccion_multiplo_10, $datos_direccion) == False)
            {
                $ocurrencias = 1;
                $velocidades = $velocidad;
            }
            else
            {
                $ocurrencias = $datos_direccion[$direccion_multiplo_10]["ocurrencias"] + 1;
                $velocidades = $datos_direccion[$direccion_multiplo_10]["velocidades"] + $velocidad;
            }
            $datos_direccion[$direccion_multiplo_10] = array(
                "ocurrencias" => $ocurrencias,
                "velocidades" => $velocidades);

            // Datos para los mapas de calor
            if ($mostrar_mapas_calor == true)
            {
                $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);
                $valores_mapa_calor_velocidad->anyade_valor_fecha_hora($fecha_hora_local, $velocidad);
                $valores_mapa_calor_direccion->anyade_valor_fecha_hora($fecha_hora_local, $direccion);
            }
        }

        // Datos de viento
        $datos_viento = array();
        $max_frecuencia = 0;
        $max_velocidad_media = 0;
        foreach ($datos_direccion as $direccion => $datos)
        {
            $ocurrencias = $datos["ocurrencias"];
            $velocidad_media = $datos["velocidades"] / $datos["ocurrencias"];

            $datos_viento[$direccion] = array($ocurrencias, $velocidad_media);

            $frecuencia = formatea_numero($ocurrencias / $numero_ocurrencias_viento, 2);
            if (((float) $frecuencia > $max_frecuencia) && ($direccion != "null"))
            {
                $max_frecuencia = (float) $frecuencia;
            }
            if ((int) $velocidad_media > $max_velocidad_media)
            {
                $max_velocidad_media = (int) $velocidad_media;
            }
        }

        // Números de decimales de valores
        $numeros_decimales_valores_velocidad = dame_numero_decimales_valores_campo_clase_sensor(CLASE_SENSOR_VIENTO, CAMPO_VELOCIDAD);
        $numeros_decimales_valores_direccion = dame_numero_decimales_valores_campo_clase_sensor(CLASE_SENSOR_VIENTO, CAMPO_DIRECCION);

        // Variables para dibujar las gráficas
        $etiquetas_graficas = new VectorDatos();
        $etiquetas_graficas->anyade_etiqueta($nombre_sensor);
        $grafica_velocidad = new VectorDatos();
        $grafica_velocidad->anyade_dato($datos_grafica_velocidad->dame_datos());
        $grafica_direccion = new VectorDatos();
        $grafica_direccion->anyade_dato($datos_grafica_direccion->dame_datos());

        // Variables de comentarios
        $filas_comentarios = array();
        $lineas_verticales_comentarios = array();
        $tabla_comentarios = NULL;

        // Comentarios y texto de información de los datos
        if ($numero_ocurrencias_viento > 0)
        {
            $cadena_fecha_hora_inicio_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);

            $cadena_fecha_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);
            $cadena_fecha_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);

            // Se recuperan los comentarios y las líneas verticales para la gráfica
            switch ($comentarios)
            {
                case COMENTARIOS_GRAFICA:
                case COMENTARIOS_GRAFICA_TABLA:
                {
                    $nombres_sensores_comentarios = dame_nombres_sensores_descendientes_comentarios(array($id_sensor));
                    $filas_comentarios = Comentario::dame_filas_comentarios_objetos(
                        ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION,
                        $nombres_sensores_comentarios,
                        $cadena_fecha_hora_inicio_valores_base_datos_utc,
                        $cadena_fecha_hora_fin_valores_base_datos_utc,
                        NULL,
                        NULL,
                        NULL);
                    $lineas_verticales_comentarios = Comentario::dame_lineas_verticales_comentarios_informe(
                        $filas_comentarios,
                        false,
                        $milisegundos_desfase_zonas_horarias_cliente_local);
                    break;
                }
            }

            $min_velocidad = round($min_velocidad, $numeros_decimales_valores_velocidad);
            $max_velocidad = round($max_velocidad, $numeros_decimales_valores_velocidad);

            $cadena_min_velocidad = formatea_numero($min_velocidad, $numeros_decimales_valores_velocidad);
            $cadena_max_velocidad = formatea_numero($max_velocidad, $numeros_decimales_valores_velocidad);
            $velocidad_media_total = formatea_numero($suma_velocidades / $numero_ocurrencias_viento, $numeros_decimales_valores_velocidad);

            $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Rango de velocidades del periodo").": ".$cadena_min_velocidad." ".$_SESSION["unidad_medida_velocidad"]." / ".$cadena_max_velocidad." ".$_SESSION["unidad_medida_velocidad"].
                " (".$idiomas->_("número de muestras").": ".formatea_numero($numero_ocurrencias_viento).")"."<br/>";
            $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Velocidad media del periodo").": ".$velocidad_media_total." ".$_SESSION["unidad_medida_velocidad"]."<br/>";
        }

        // Tabla y número de comentarios
        if ($comentarios == COMENTARIOS_GRAFICA_TABLA)
        {
            if ((count($filas_comentarios) > 0) ||
                (($tipo_informe == TIPO_INFORME_WEB_EMIOS) && (NodoSensor::dame_administracion_comentarios_sensores() == true)))
            {
                if ($numero_elemento_plantilla_informe === NULL)
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_VIENTO;
                }
                else
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_VIENTO.",".$numero_elemento_plantilla_informe;
                }
                $tabla_comentarios = Comentario::dame_tabla_comentarios_objetos_informe(
                    $origen_comentarios,
                    $parametros_origen_comentarios,
                    "tabla-comentarios-sensor-informacion-viento",
                    $filas_comentarios,
                    NULL,
                    array($nombre_sensor),
                    $tipo_informe);
            }
        }
        $numero_comentarios = count($filas_comentarios);

        // Descripción del sensor
        $descripcion_sensor = dame_descripcion_sensor_informe($id_sensor);

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($max_velocidad_media == -INF)
        {
            $max_velocidad_media = "ND";
        }
        if ($max_frecuencia == -INF)
        {
            $max_frecuencia = "ND";
        }
        if ($max_velocidad == -INF)
        {
            $max_velocidad = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "max_velocidad_media" => $max_velocidad_media,
            "max_frecuencia" => $max_frecuencia,
            "max_velocidad" => $max_velocidad,
            "datos_viento" => $datos_viento,
            "etiquetas_graficas" => $etiquetas_graficas->dame_datos(),
            "grafica_velocidad" => $grafica_velocidad->dame_datos(),
            "grafica_direccion" => $grafica_direccion->dame_datos(),
            "lineas_verticales_comentarios" => $lineas_verticales_comentarios,
            "tabla_comentarios" => $tabla_comentarios,
            "numero_comentarios" => $numero_comentarios,
            "fecha_inicio_valores" => $cadena_fecha_inicio_valores_local_local,
            "hora_inicio_valores" => $cadena_hora_inicio_valores_local_local,
            "fecha_fin_valores" => $cadena_fecha_fin_valores_local_local,
            "hora_fin_valores" => $cadena_hora_fin_valores_local_local,
            "dias_mapas_calor" => $valores_mapa_calor_velocidad->dame_dias(),
            "datos_mapa_calor_velocidad" => $valores_mapa_calor_velocidad->dame_datos(),
            "datos_mapa_calor_direccion" => $valores_mapa_calor_direccion->dame_datos(),
            "texto_informacion_datos" => $texto_informacion_datos,
            "numeros_decimales_valores_velocidad" => $numeros_decimales_valores_velocidad,
            "numeros_decimales_valores_direccion" => $numeros_decimales_valores_direccion,
            "descripcion_sensor" => $descripcion_sensor);
        return ($resultado);
    }


    function dame_informacion_sensor_energia($parametros, $filas_valores_sensor)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $clase_sensor = $parametros["clase_sensor"];
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $campo = $parametros["campo"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $tipo_mapa_calor = $parametros["tipo_mapa_calor"];
        $comentarios = $parametros["comentarios"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $tipo_informe = $parametros["tipo_informe"];
        $numero_elemento_plantilla_informe = $parametros["numero_elemento_plantilla_informe"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

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

        // Se recupera la información del ratio (si aplica)
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);
        if ($aplicar_ratio == true)
        {
            $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                $intervalo_valores,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas);
        }

        // Descripción del campo, número de decimales de valores y unidad de medida
        $descripcion_campo = dame_descripcion_campo_clase_sensor($clase_sensor, $campo);
        $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, ID_NINGUNO, $campo);
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio_sensor, $unidad_medida);
        }

        // Variables del informe
        $mostrar_mapas_calor = dame_mostrar_mapa_calor_sensor_informacion($intervalo_valores, $tipo_mapa_calor);
        $valores_mapa_calor_valores = new ValoresMapaCalor($tipo_mapa_calor);
        $numero_ocurrencias_valores = 0;
        $suma_valores = 0;
        $suma_horas = 0;
        $datos_grafica_valores = new VectorDatos();
        $datos_grafica_valores_acumulados = new VectorDatos();
        $min_valor = INF;
        $max_valor = -INF;
        $valor_inicial = NULL;
        $valor_final = NULL;
        $cadena_fecha_hora_inicio_valores_base_datos_utc = NULL;
        $cadena_fecha_hora_fin_valores_base_datos_utc = NULL;

        // Flag de campo incremental
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $tipo_informe_sensores = TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA;
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                $tipo_informe_sensores = TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA;
                break;
            }
        }
        $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor_informe($clase_sensor, $campo, $tipo_informe_sensores);
        $campo_incremental = ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES);

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Se recorren las filas de valores del sensor
        $timestamp_fecha_hora_valor_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        foreach ($filas_valores_sensor as $fila_valor_sensor)
        {
            // Fecha y valor
            $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
            $valor = $fila_valor_sensor[$campo];
            if ($valor !== NULL)
            {
                $valor = (float) $valor;
                if ($aplicar_ratio == true)
                {
                    aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $valor);
                }
            }
            if ($valor === NULL)
            {
                continue;
            }

            // Valores máximos y mínimos y adición de los datos a las gráficas
            if ($valor > $max_valor)
            {
                $max_valor = $valor;
            }
            if ($valor < $min_valor)
            {
                $min_valor = $valor;
            }
            if ($valor_inicial === NULL)
            {
                $valor_inicial = $valor;
            }
            $valor_final = $valor;
            if ($cadena_fecha_hora_inicio_valores_base_datos_utc === NULL)
            {
                $cadena_fecha_hora_inicio_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
            }
            $cadena_fecha_hora_fin_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_valor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_valor_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_valor_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_valor_utc - $timestamp_fecha_hora_valor_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_grafica_valores->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                    if ($campo_incremental == true)
                    {
                        $datos_grafica_valores_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                    }
                }
            }
            $timestamp_fecha_hora_valor_anterior_utc = $timestamp_fecha_hora_valor_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade el valor (con tooltip personalizado si es campo incremental)
            anyade_dato_grafica_valores_tooltip_personalizado_campo_incremental(
                $datos_grafica_valores,
                $timestamp_fecha_hora_valor_utc,
                $valor,
                $campo_incremental,
                $intervalo_valores,
                $zona_horaria,
                2,
                $unidad_medida,
                $fila_valor_sensor);

            // Número de ocurrencias de valores
            $numero_ocurrencias_valores += 1;

            // Suma de valores
            $valor_modificado = modifica_valor_campo_clase_sensor_informe(
                $clase_sensor,
                $campo,
                $valor,
                $tipo_informe_sensores);
            $suma_valores += $valor_modificado;

            // Si el campo es incremental
            if ($campo_incremental == true)
            {
                // Horas
                $suma_horas += $fila_valor_sensor["horas"];

                // Se añade el valor
                $datos_grafica_valores_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_utc, $suma_valores);
            }

            // Datos para el mapa de calor del campo seleccionado
            if ($mostrar_mapas_calor == true)
            {
                $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);
                $valores_mapa_calor_valores->anyade_valor_fecha_hora($fecha_hora_local, $valor);
            }
        }

        // Si no hay datos no se hace nada
        if ($numero_ocurrencias_valores == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Colores de mapa de calor
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $colores_mapa_calor_valores = COLORES_VERDE_ROJO;
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                switch ($campo)
                {
                    case CAMPO_COSENO_PHI:
                    {
                        $colores_mapa_calor_valores = COLORES_ROJO_VERDE;
                        break;
                    }
                    default:
                    {
                        $colores_mapa_calor_valores = COLORES_VERDE_ROJO;
                        break;
                    }
                }
                break;
            }
        }

        // Variables para dibujar las gráficas
        $etiquetas_graficas = new VectorDatos();
        $etiquetas_graficas->anyade_etiqueta($nombre_sensor);

        $grafica_valores = new VectorDatos();
        $grafica_valores->anyade_dato($datos_grafica_valores->dame_datos());
        $grafica_valores_acumulados = new VectorDatos();
        if ($campo_incremental == true)
        {
            $grafica_valores_acumulados->anyade_dato($datos_grafica_valores_acumulados->dame_datos());
        }

        // Variables de comentarios
        $filas_comentarios = array();
        $lineas_verticales_comentarios = array();
        $tabla_comentarios = NULL;

        // Comentarios y texto de información de los datos
        if ($numero_ocurrencias_valores > 0)
        {
            $cadena_fecha_hora_inicio_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);

            $cadena_fecha_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);
            $cadena_fecha_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);

            // Se recuperan los comentarios y las líneas verticales para la gráfica
            switch ($comentarios)
            {
                case COMENTARIOS_GRAFICA:
                case COMENTARIOS_GRAFICA_TABLA:
                {
                    $nombres_sensores_comentarios = dame_nombres_sensores_descendientes_comentarios(array($id_sensor));
                    $filas_comentarios = Comentario::dame_filas_comentarios_objetos(
                        ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION,
                        $nombres_sensores_comentarios,
                        $cadena_fecha_hora_inicio_valores_base_datos_utc,
                        $cadena_fecha_hora_fin_valores_base_datos_utc,
                        NULL,
                        NULL,
                        NULL);
                    $lineas_verticales_comentarios = Comentario::dame_lineas_verticales_comentarios_informe(
                        $filas_comentarios,
                        false,
                        $milisegundos_desfase_zonas_horarias_cliente_local);
                    break;
                }
            }

            // Texto de información de los datos
            $min_valor = round($min_valor, $numero_decimales_valores);
            $max_valor = round($max_valor, $numero_decimales_valores);

            $cadena_min_valor = formatea_numero($min_valor, $numero_decimales_valores);
            $cadena_max_valor = formatea_numero($max_valor, $numero_decimales_valores);
            $cadena_diferencia_valores = formatea_numero($valor_final - $valor_inicial, $numero_decimales_valores);
            $cadena_media_valores = formatea_numero(($suma_valores / $numero_ocurrencias_valores), $numero_decimales_valores);
            $cadena_suma_valores = formatea_numero($suma_valores, $numero_decimales_valores);

            $texto_informacion_muestras = " (".$idiomas->_("número de muestras").": ".formatea_numero($numero_ocurrencias_valores, 0);
            if ($campo_incremental == true)
            {
                $texto_informacion_muestras .= ", ".$idiomas->_("periodo de tiempo de muestras").": ".dame_texto_periodo($suma_horas * 3600);
            }
            $texto_informacion_muestras .= ")";
            switch ($campo)
            {
                case CAMPO_ABSOLUTO:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de consumos absolutos del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                        $texto_informacion_muestras."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Consumo total del periodo").": ".$cadena_diferencia_valores." ".$unidad_medida." "."<br/>";
                    break;
                }
                case CAMPO_INCREMENTO:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de consumos del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                        $texto_informacion_muestras."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Consumo medio del periodo").": ".$cadena_media_valores." ".$unidad_medida."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Consumo total del periodo").": ".$cadena_suma_valores." ".$unidad_medida."<br/>";
                    break;
                }
                case CAMPO_INCREMENTO_POTENCIA:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de potencias del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                        $texto_informacion_muestras."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Potencia media del periodo").": ".$cadena_media_valores." ".$unidad_medida." "."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Potencia total del periodo").": ".$cadena_suma_valores." ".$unidad_medida." "."<br/>";
                    break;
                }
                case CAMPO_TRAMO:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de tramos del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                        $texto_informacion_muestras."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Tramo medio del periodo").": ".$cadena_media_valores." ".$unidad_medida."<br/>";
                    break;
                }
            }
            switch ($clase_sensor)
            {
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                {
                    switch ($campo)
                    {
                        case CAMPO_COSTE:
                        {
                            $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                                $idiomas->_("Rango de costes del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                                $texto_informacion_muestras."<br/>";
                            $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                                $idiomas->_("Coste medio del periodo").": ".$cadena_media_valores." ".$unidad_medida."<br/>";
                            $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                                $idiomas->_("Coste total del periodo").": ".$cadena_suma_valores." ".$unidad_medida."<br/>";
                            break;
                        }
                        case CAMPO_SOBREPOTENCIA:
                        {
                            $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                                $idiomas->_("Rango de sobrepotencias del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                                $texto_informacion_muestras."<br/>";
                            $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                                $idiomas->_("Sobrepotencia total del periodo (excesos)").": ".$cadena_suma_valores." ".$unidad_medida." "."<br/>";
                            break;
                        }
                    }
                    break;
                }
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                {
                    switch ($campo)
                    {
                        case CAMPO_COSENO_PHI:
                        {
                            $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                                $idiomas->_("Rango de coseno de phi del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                                $texto_informacion_muestras."<br/>";
                            $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                                $idiomas->_("Coseno de phi medio del periodo").": ".$cadena_media_valores." ".$unidad_medida."<br/>";
                            break;
                        }
                        case CAMPO_PENALIZABLE:
                        {
                            $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                                $idiomas->_("Rango de valores del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                                $texto_informacion_muestras."<br/>";
                            $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                                $idiomas->_("Valor medio del periodo").": ".$cadena_media_valores." ".$unidad_medida."<br/>";
                            break;
                        }
                    }
                    break;
                }
            }
        }

        // Tabla y número de comentarios
        if ($comentarios == COMENTARIOS_GRAFICA_TABLA)
        {
            switch ($clase_sensor)
            {
                case CLASE_SENSOR_ENERGIA_ACTIVA:
                {
                    $tipo_informe_informacion_energia = TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA;
                    $id_tabla_comentarios_energia = "tabla-comentarios-sensor-informacion-energia-activa";
                    break;
                }
                case CLASE_SENSOR_ENERGIA_REACTIVA:
                {
                    $tipo_informe_informacion_energia = TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA;
                    $id_tabla_comentarios_energia = "tabla-comentarios-sensor-informacion-energia-reactiva";
                    break;
                }
            }
            if ((count($filas_comentarios) > 0) ||
                (($tipo_informe == TIPO_INFORME_WEB_EMIOS) && (NodoSensor::dame_administracion_comentarios_sensores() == true)))
            {
                if ($numero_elemento_plantilla_informe === NULL)
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION;
                    $parametros_origen_comentarios = $tipo_informe_informacion_energia;
                }
                else
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME;
                    $parametros_origen_comentarios = $tipo_informe_informacion_energia.",".$numero_elemento_plantilla_informe;
                }
                $tabla_comentarios = Comentario::dame_tabla_comentarios_objetos_informe(
                    $origen_comentarios,
                    $parametros_origen_comentarios,
                    $id_tabla_comentarios_energia,
                    $filas_comentarios,
                    NULL,
                    array($nombre_sensor),
                    $tipo_informe);
            }
        }
        $numero_comentarios = count($filas_comentarios);

        // Descripción del sensor
        $descripcion_sensor = dame_descripcion_sensor_informe($id_sensor);

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($min_valor == INF)
        {
            $min_valor = "ND";
        }
        if ($max_valor == -INF)
        {
            $max_valor = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "min_valor" => $min_valor,
            "max_valor" => $max_valor,
            "suma_valores" => $suma_valores,
            "etiquetas_graficas" => $etiquetas_graficas->dame_datos(),
            "grafica_valores" => $grafica_valores->dame_datos(),
            "campo_incremental" => $campo_incremental,
            "grafica_valores_acumulados" => $grafica_valores_acumulados->dame_datos(),
            "lineas_verticales_comentarios" => $lineas_verticales_comentarios,
            "tabla_comentarios" => $tabla_comentarios,
            "numero_comentarios" => $numero_comentarios,
            "fecha_inicio_valores" => $cadena_fecha_inicio_valores_local_local,
            "hora_inicio_valores" => $cadena_hora_inicio_valores_local_local,
            "fecha_fin_valores" => $cadena_fecha_fin_valores_local_local,
            "hora_fin_valores" => $cadena_hora_fin_valores_local_local,
            "colores_mapa_calor_valores" => $colores_mapa_calor_valores,
            "dias_mapa_calor_valores" => $valores_mapa_calor_valores->dame_dias(),
            "datos_mapa_calor_valores" => $valores_mapa_calor_valores->dame_datos(),
            "texto_informacion_datos" => $texto_informacion_datos,
            "descripcion_campo" => $descripcion_campo,
            "numero_decimales_valores" => $numero_decimales_valores,
            "unidad_medida" => $unidad_medida,
            "descripcion_sensor" => $descripcion_sensor);
        return ($resultado);
    }


    function dame_informacion_sensor_cortes_tension($parametros, $filas_valores_sensor)
    {
        // Si no hay datos no se hace nada
        if (count($filas_valores_sensor) == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        $idiomas = new Idiomas();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $tipo_mapa_calor = $parametros["tipo_mapa_calor"];
        $comentarios = $parametros["comentarios"];
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $tipo_informe = $parametros["tipo_informe"];
        $numero_elemento_plantilla_informe = $parametros["numero_elemento_plantilla_informe"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Zona horaria
        $zona_horaria = dame_zona_horaria_local();

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Variables del informe
        $mostrar_mapas_calor = dame_mostrar_mapa_calor_sensor_informacion($intervalo_valores, $tipo_mapa_calor);
        $valores_mapa_calor_cortes_tension = new ValoresMapaCalor($tipo_mapa_calor);
        $numero_ocurrencias_cortes_tension = 0;
        $suma_cortes_tension = 0;
        $datos_grafica_cortes_tension = new VectorDatos();
        $datos_grafica_cortes_tension_acumulados = new VectorDatos();
        $cadena_fecha_hora_inicio_valores_base_datos_utc = NULL;
        $cadena_fecha_hora_fin_valores_base_datos_utc = NULL;

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Se recorren las filas de valores del sensor
        $timestamp_fecha_hora_corte_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        foreach ($filas_valores_sensor as $fila_valor_sensor)
        {
            // Fecha y valor
            $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
            $corte = (int) $fila_valor_sensor[CAMPO_CORTES];
            if ($cadena_fecha_hora_inicio_valores_base_datos_utc === NULL)
            {
                $cadena_fecha_hora_inicio_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
            }
            $cadena_fecha_hora_fin_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_corte_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_corte_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_corte_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_corte_utc - $timestamp_fecha_hora_corte_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_grafica_cortes_tension->anyade_tupla_pareja_datos($timestamp_fecha_hora_corte_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_corte_anterior_utc = $timestamp_fecha_hora_corte_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Número de ocurrencias y suma de cortes de tensión
            $numero_ocurrencias_cortes_tension += 1;
            $suma_cortes_tension += $corte;

            // Se añade el corte
            $datos_grafica_cortes_tension->anyade_tupla_pareja_datos($timestamp_fecha_hora_corte_utc, $corte);
            $datos_grafica_cortes_tension_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_corte_utc, $suma_cortes_tension);

            // Datos para el mapa de calor de cortes
            if ($mostrar_mapas_calor == true)
            {
                $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);
                $valores_mapa_calor_cortes_tension->anyade_valor_fecha_hora($fecha_hora_local, $corte);
            }
        }

        // Variables para dibujar las gráficas
        $etiquetas_graficas = new VectorDatos();
        $etiquetas_graficas->anyade_etiqueta($nombre_sensor);
        $grafica_cortes_tension = new VectorDatos();
        $grafica_cortes_tension->anyade_dato($datos_grafica_cortes_tension->dame_datos());
        $grafica_cortes_tension_acumulados = new VectorDatos();
        $grafica_cortes_tension_acumulados->anyade_dato($datos_grafica_cortes_tension_acumulados->dame_datos());

        // Variables de comentarios
        $filas_comentarios = array();
        $lineas_verticales_comentarios = array();
        $tabla_comentarios = NULL;

        // Si hay datos
        if ($numero_ocurrencias_cortes_tension > 0)
        {
            $cadena_fecha_hora_inicio_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);

            $cadena_fecha_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);
            $cadena_fecha_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);

            // Se recuperan los comentarios y las líneas verticales para la gráfica
            switch ($comentarios)
            {
                case COMENTARIOS_GRAFICA:
                case COMENTARIOS_GRAFICA_TABLA:
                {
                    $nombres_sensores_comentarios = dame_nombres_sensores_descendientes_comentarios(array($id_sensor));
                    $filas_comentarios = Comentario::dame_filas_comentarios_objetos(
                        ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION,
                        $nombres_sensores_comentarios,
                        $cadena_fecha_hora_inicio_valores_base_datos_utc,
                        $cadena_fecha_hora_fin_valores_base_datos_utc,
                        NULL,
                        NULL,
                        NULL);
                    $lineas_verticales_comentarios = Comentario::dame_lineas_verticales_comentarios_informe(
                        $filas_comentarios,
                        false,
                        $milisegundos_desfase_zonas_horarias_cliente_local);
                    break;
                }
            }

            // Texto de información de los datos
            $cadena_suma_cortes_tension = formatea_numero($suma_cortes_tension, 0);
            $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Número de cortes de tensión del periodo").": ".$cadena_suma_cortes_tension.
                " (".$idiomas->_("número de muestras").": ".formatea_numero($numero_ocurrencias_cortes_tension, 0).")"."<br/>";
        }

        // Tabla y número de comentarios
        if ($comentarios == COMENTARIOS_GRAFICA_TABLA)
        {
            if ((count($filas_comentarios) > 0) ||
                (($tipo_informe == TIPO_INFORME_WEB_EMIOS) && (NodoSensor::dame_administracion_comentarios_sensores() == true)))
            {
                if ($numero_elemento_plantilla_informe === NULL)
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION;
                }
                else
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION.",".$numero_elemento_plantilla_informe;
                }
                $tabla_comentarios = Comentario::dame_tabla_comentarios_objetos_informe(
                    $origen_comentarios,
                    $parametros_origen_comentarios,
                    "tabla-comentarios-sensor-informacion-cortes-tension",
                    $filas_comentarios,
                    NULL,
                    array($nombre_sensor),
                    $tipo_informe);
            }
        }
        $numero_comentarios = count($filas_comentarios);

        // Descripción del sensor
        $descripcion_sensor = dame_descripcion_sensor_informe($id_sensor);

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "suma_cortes_tension" => $suma_cortes_tension,
            "etiquetas_graficas" => $etiquetas_graficas->dame_datos(),
            "grafica_cortes_tension" => $grafica_cortes_tension->dame_datos(),
            "grafica_cortes_tension_acumulados" => $grafica_cortes_tension_acumulados->dame_datos(),
            "lineas_verticales_comentarios" => $lineas_verticales_comentarios,
            "tabla_comentarios" => $tabla_comentarios,
            "numero_comentarios" => $numero_comentarios,
            "fecha_inicio_valores" => $cadena_fecha_inicio_valores_local_local,
            "hora_inicio_valores" => $cadena_hora_inicio_valores_local_local,
            "fecha_fin_valores" => $cadena_fecha_fin_valores_local_local,
            "hora_fin_valores" => $cadena_hora_fin_valores_local_local,
            "dias_mapa_calor_cortes_tension" => $valores_mapa_calor_cortes_tension->dame_dias(),
            "datos_mapa_calor_cortes_tension" => $valores_mapa_calor_cortes_tension->dame_datos(),
            "texto_informacion_datos" => $texto_informacion_datos,
            "descripcion_sensor" => $descripcion_sensor);
        return ($resultado);
    }


    function dame_informacion_sensor_compra_energia($parametros, $filas_valores_sensor)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $campo = $parametros["campo"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $tipo_mapa_calor = $parametros["tipo_mapa_calor"];
        $comentarios = $parametros["comentarios"];
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $tipo_informe = $parametros["tipo_informe"];
        $numero_elemento_plantilla_informe = $parametros["numero_elemento_plantilla_informe"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Descripción del campo, número de decimales de valores y unidad de medida
        $descripcion_campo = dame_descripcion_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, $campo);
        $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, $campo);
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor(CLASE_SENSOR_COMPRA_ENERGIA, ID_NINGUNO, $campo);

        // Variables del informe
        $mostrar_mapas_calor = dame_mostrar_mapa_calor_sensor_informacion($intervalo_valores, $tipo_mapa_calor);
        $valores_mapa_calor_valores = new ValoresMapaCalor($tipo_mapa_calor);
        $valores_mapa_calor_valores_visibles = NULL;
        $numero_ocurrencias_valores = 0;
        $suma_valores = 0;
        $suma_horas = 0;
        $datos_grafica_valores = new VectorDatos();
        $datos_grafica_valores_acumulados = new VectorDatos();
        $min_valor = INF;
        $max_valor = -INF;
        $min_valor_acumulado = INF;
        $max_valor_acumulado = -INF;
        $valor_inicial = NULL;
        $valor_final = NULL;
        $cadena_fecha_hora_inicio_valores_base_datos_utc = NULL;
        $cadena_fecha_hora_fin_valores_base_datos_utc = NULL;

        // Flag de campo incremental
        $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor(CLASE_SENSOR_COMPRA_ENERGIA, $campo);
        $campo_incremental = ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES);

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Se recorren las filas de valores del sensor
        $timestamp_fecha_hora_valor_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        foreach ($filas_valores_sensor as $fila_valor_sensor)
        {
            // Fecha y valor
            $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
            $valor = $fila_valor_sensor[$campo];
            if ($valor !== NULL)
            {
                $valor = (float) $valor;
            }
            if ($valor === NULL)
            {
                continue;
            }

            // Valores máximos y mínimos y adición de los datos a las gráficas
            if ($valor > $max_valor)
            {
                $max_valor = $valor;
            }
            if ($valor < $min_valor)
            {
                $min_valor = $valor;
            }
            if ($valor_inicial === NULL)
            {
                $valor_inicial = $valor;
            }
            $valor_final = $valor;
            if ($cadena_fecha_hora_inicio_valores_base_datos_utc === NULL)
            {
                $cadena_fecha_hora_inicio_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
            }
            $cadena_fecha_hora_fin_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_valor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_valor_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_valor_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_valor_utc - $timestamp_fecha_hora_valor_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_grafica_valores->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                    if ($campo_incremental == true)
                    {
                        $datos_grafica_valores_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                    }
                }
            }
            $timestamp_fecha_hora_valor_anterior_utc = $timestamp_fecha_hora_valor_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade el valor (con tooltip personalizado si es campo incremental)
            anyade_dato_grafica_valores_tooltip_personalizado_campo_incremental(
                $datos_grafica_valores,
                $timestamp_fecha_hora_valor_utc,
                $valor,
                $campo_incremental,
                $intervalo_valores,
                $zona_horaria,
                2,
                $unidad_medida,
                $fila_valor_sensor);

            $numero_ocurrencias_valores += 1;
            $suma_valores += $valor;
            if ($suma_valores < $min_valor_acumulado)
            {
                $min_valor_acumulado = $suma_valores;
            }
            if ($suma_valores > $max_valor_acumulado)
            {
                $max_valor_acumulado = $suma_valores;
            }

            // Si el campo es incremental
            if ($campo_incremental == true)
            {
                // Horas
                $suma_horas += $fila_valor_sensor["horas"];

                // Se añade el valor
                $datos_grafica_valores_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_utc, $suma_valores);
            }

            // Datos para el mapa de calor del campo seleccionado
            if ($mostrar_mapas_calor == true)
            {
                $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);

                // Valores de mapa de calor
                switch ($campo)
                {
                    // Nota: Si el campo es desvío de consumo,
                    // en verde son los valores absolutos más bajos y en rojo los más altos
                    // (aunque luego se muestran los valores "reales" en el mapa de calor)
                    case CAMPO_DESVIO_CONSUMO:
                    {
                        if ($valores_mapa_calor_valores_visibles === NULL)
                        {
                            $valores_mapa_calor_valores_visibles = new ValoresMapaCalor($tipo_mapa_calor);
                        }
                        $valores_mapa_calor_valores->anyade_valor_fecha_hora($fecha_hora_local, abs($valor));
                        $valores_mapa_calor_valores_visibles->anyade_valor_fecha_hora($fecha_hora_local, $valor);
                        break;
                    }
                    default:
                    {
                        $valores_mapa_calor_valores->anyade_valor_fecha_hora($fecha_hora_local, $valor);
                        break;
                    }
                }
            }
        }

        // Si no hay datos no se hace nada
        if ($numero_ocurrencias_valores == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Colores de mapa de calor
        $colores_mapa_calor_valores = COLORES_VERDE_ROJO;

        // Variables para dibujar las gráficas
        $etiquetas_graficas = new VectorDatos();
        $etiquetas_graficas->anyade_etiqueta($nombre_sensor);

        $grafica_valores = new VectorDatos();
        $grafica_valores->anyade_dato($datos_grafica_valores->dame_datos());
        $grafica_valores_acumulados = new VectorDatos();
        if ($campo_incremental == true)
        {
            $grafica_valores_acumulados->anyade_dato($datos_grafica_valores_acumulados->dame_datos());
        }

        // Variables de comentarios
        $filas_comentarios = array();
        $lineas_verticales_comentarios = array();
        $tabla_comentarios = NULL;

        // Comentarios y texto de información de los datos
        if ($numero_ocurrencias_valores > 0)
        {
            $cadena_fecha_hora_inicio_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);

            $cadena_fecha_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);
            $cadena_fecha_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);

            // Se recuperan los comentarios y las líneas verticales para la gráfica
            switch ($comentarios)
            {
                case COMENTARIOS_GRAFICA:
                case COMENTARIOS_GRAFICA_TABLA:
                {
                    $nombres_sensores_comentarios = dame_nombres_sensores_descendientes_comentarios(array($id_sensor));
                    $filas_comentarios = Comentario::dame_filas_comentarios_objetos(
                        ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION,
                        $nombres_sensores_comentarios,
                        $cadena_fecha_hora_inicio_valores_base_datos_utc,
                        $cadena_fecha_hora_fin_valores_base_datos_utc,
                        NULL,
                        NULL,
                        NULL);
                    $lineas_verticales_comentarios = Comentario::dame_lineas_verticales_comentarios_informe(
                        $filas_comentarios,
                        false,
                        $milisegundos_desfase_zonas_horarias_cliente_local);
                    break;
                }
            }

            // Texto de información de los datos
            $min_valor = round($min_valor, $numero_decimales_valores);
            $max_valor = round($max_valor, $numero_decimales_valores);

            $cadena_min_valor = formatea_numero($min_valor, $numero_decimales_valores);
            $cadena_max_valor = formatea_numero($max_valor, $numero_decimales_valores);
            $cadena_media_valores = formatea_numero(($suma_valores / $numero_ocurrencias_valores), $numero_decimales_valores);
            $cadena_suma_valores = formatea_numero($suma_valores, $numero_decimales_valores);

            $texto_informacion_muestras = " (".$idiomas->_("número de muestras").": ".formatea_numero($numero_ocurrencias_valores, 0);
            if ($campo_incremental == true)
            {
                $texto_informacion_muestras .= ", ".$idiomas->_("periodo de tiempo de muestras").": ".dame_texto_periodo($suma_horas * 3600);
            }
            $texto_informacion_muestras .= ")";
            switch ($campo)
            {
                case CAMPO_CONSUMO_ESTIMADO:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de consumos estimados del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                        $texto_informacion_muestras."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Consumo estimado medio del periodo").": ".$cadena_media_valores." ".$unidad_medida."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Consumo estimado total del periodo").": ".$cadena_suma_valores." ".$unidad_medida."<br/>";
                    break;
                }
                case CAMPO_CONSUMO_REAL:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de consumos reales del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                        $texto_informacion_muestras."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Consumo real medio del periodo").": ".$cadena_media_valores." ".$unidad_medida."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Consumo real total del periodo").": ".$cadena_suma_valores." ".$unidad_medida."<br/>";
                    break;
                }
                case CAMPO_DESVIO_CONSUMO:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de desvíos de consumo del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                        $texto_informacion_muestras."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Desvío de consumo medio del periodo").": ".$cadena_media_valores." ".$unidad_medida."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Desvío de consumo total del periodo").": ".$cadena_suma_valores." ".$unidad_medida."<br/>";
                    break;
                }
                case CAMPO_COSTE_DESVIO:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de costes de desvíos del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                        $texto_informacion_muestras."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Coste de desvíos medio del periodo").": ".$cadena_media_valores." ".$unidad_medida."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Coste de desvíos total del periodo").": ".$cadena_suma_valores." ".$unidad_medida."<br/>";
                    break;
                }
                case CAMPO_PENALIZABLE:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de valores del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                        $texto_informacion_muestras."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Valor medio del periodo").": ".$cadena_media_valores." ".$unidad_medida."<br/>";
                    break;
                }
            }
        }

        // Tabla y número de comentarios
        if ($comentarios == COMENTARIOS_GRAFICA_TABLA)
        {
            if ((count($filas_comentarios) > 0) ||
                (($tipo_informe == TIPO_INFORME_WEB_EMIOS) && (NodoSensor::dame_administracion_comentarios_sensores() == true)))
            {
                if ($numero_elemento_plantilla_informe === NULL)
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA;
                }
                else
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA.",".$numero_elemento_plantilla_informe;
                }
                $tabla_comentarios = Comentario::dame_tabla_comentarios_objetos_informe(
                    $origen_comentarios,
                    $parametros_origen_comentarios,
                    "tabla-comentarios-sensor-informacion-gas",
                    $filas_comentarios,
                    NULL,
                    array($nombre_sensor),
                    $tipo_informe);
            }
        }
        $numero_comentarios = count($filas_comentarios);

        // Descripción del sensor
        $descripcion_sensor = dame_descripcion_sensor_informe($id_sensor);

        // Días y datos de mapa de calor
        $dias_mapa_calor_valores = $valores_mapa_calor_valores->dame_dias();
        $datos_mapa_calor_valores = $valores_mapa_calor_valores->dame_datos();
        $datos_mapa_calor_valores_visibles = NULL;
        if ($valores_mapa_calor_valores_visibles !== NULL)
        {
            $datos_mapa_calor_valores_visibles = $valores_mapa_calor_valores_visibles->dame_datos();
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

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "min_valor" => $min_valor,
            "max_valor" => $max_valor,
            "min_valor_acumulado" => $min_valor_acumulado,
            "max_valor_acumulado" => $max_valor_acumulado,
            "etiquetas_graficas" => $etiquetas_graficas->dame_datos(),
            "grafica_valores" => $grafica_valores->dame_datos(),
            "campo_incremental" => $campo_incremental,
            "grafica_valores_acumulados" => $grafica_valores_acumulados->dame_datos(),
            "lineas_verticales_comentarios" => $lineas_verticales_comentarios,
            "tabla_comentarios" => $tabla_comentarios,
            "numero_comentarios" => $numero_comentarios,
            "fecha_inicio_valores" => $cadena_fecha_inicio_valores_local_local,
            "hora_inicio_valores" => $cadena_hora_inicio_valores_local_local,
            "fecha_fin_valores" => $cadena_fecha_fin_valores_local_local,
            "hora_fin_valores" => $cadena_hora_fin_valores_local_local,
            "colores_mapa_calor_valores" => $colores_mapa_calor_valores,
            "dias_mapa_calor_valores" => $dias_mapa_calor_valores,
            "datos_mapa_calor_valores" => $datos_mapa_calor_valores,
            "datos_mapa_calor_valores_visibles" => $datos_mapa_calor_valores_visibles,
            "texto_informacion_datos" => $texto_informacion_datos,
            "descripcion_campo" => $descripcion_campo,
            "numero_decimales_valores" => $numero_decimales_valores,
            "unidad_medida" => $unidad_medida,
            "descripcion_sensor" => $descripcion_sensor);
        return ($resultado);
    }


    function dame_informacion_sensor_gas($parametros, $filas_valores_sensor)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $campo = $parametros["campo"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $tipo_mapa_calor = $parametros["tipo_mapa_calor"];
        $comentarios = $parametros["comentarios"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $tipo_informe = $parametros["tipo_informe"];
        $numero_elemento_plantilla_informe = $parametros["numero_elemento_plantilla_informe"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

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

        // Se recupera la información del ratio (si aplica)
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, CLASE_SENSOR_GAS, $campo);
        if ($aplicar_ratio == true)
        {
            $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                $intervalo_valores,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas);
        }

        // Descripción del campo, número de decimales de valores y unidad de medida
        $descripcion_campo = dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GAS, $campo);
        $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor(CLASE_SENSOR_GAS, $campo);
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor(CLASE_SENSOR_GAS, ID_NINGUNO, $campo);
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio_sensor, $unidad_medida);
        }

        // Variables del informe
        $mostrar_mapas_calor = dame_mostrar_mapa_calor_sensor_informacion($intervalo_valores, $tipo_mapa_calor);
        $valores_mapa_calor_valores = new ValoresMapaCalor($tipo_mapa_calor);
        $numero_ocurrencias_valores = 0;
        $suma_valores = 0;
        $suma_horas = 0;
        $datos_grafica_valores = new VectorDatos();
        $datos_grafica_valores_acumulados = new VectorDatos();
        $min_valor = INF;
        $max_valor = -INF;
        $valor_inicial = NULL;
        $valor_final = NULL;
        $cadena_fecha_hora_inicio_valores_base_datos_utc = NULL;
        $cadena_fecha_hora_fin_valores_base_datos_utc = NULL;

        // Flag de campo incremental
        $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor(CLASE_SENSOR_GAS, $campo);
        $campo_incremental = ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES);

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Se recorren las filas de valores del sensor
        $timestamp_fecha_hora_valor_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        foreach ($filas_valores_sensor as $fila_valor_sensor)
        {
            // Fecha y valor
            $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
            $valor = $fila_valor_sensor[$campo];
            if ($valor !== NULL)
            {
                $valor = (float) $valor;
                if ($aplicar_ratio == true)
                {
                    aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $valor);
                }
            }
            if ($valor === NULL)
            {
                continue;
            }

            // Valores máximos y mínimos y adición de los datos a las gráficas
            if ($valor > $max_valor)
            {
                $max_valor = $valor;
            }
            if ($valor < $min_valor)
            {
                $min_valor = $valor;
            }
            if ($valor_inicial === NULL)
            {
                $valor_inicial = $valor;
            }
            $valor_final = $valor;
            if ($cadena_fecha_hora_inicio_valores_base_datos_utc === NULL)
            {
                $cadena_fecha_hora_inicio_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
            }
            $cadena_fecha_hora_fin_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_valor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_valor_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_valor_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_valor_utc - $timestamp_fecha_hora_valor_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_grafica_valores->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                    if ($campo_incremental == true)
                    {
                        $datos_grafica_valores_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                    }
                }
            }
            $timestamp_fecha_hora_valor_anterior_utc = $timestamp_fecha_hora_valor_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade el valor (con tooltip personalizado si es campo incremental)
            anyade_dato_grafica_valores_tooltip_personalizado_campo_incremental(
                $datos_grafica_valores,
                $timestamp_fecha_hora_valor_utc,
                $valor,
                $campo_incremental,
                $intervalo_valores,
                $zona_horaria,
                2,
                $unidad_medida,
                $fila_valor_sensor);

            $numero_ocurrencias_valores += 1;
            $suma_valores += $valor;

            // Si el campo es incremental
            if ($campo_incremental == true)
            {
                // Horas
                $suma_horas += $fila_valor_sensor["horas"];

                // Se añade el valor
                $datos_grafica_valores_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_utc, $suma_valores);
            }

            // Datos para el mapa de calor del campo seleccionado
            if ($mostrar_mapas_calor == true)
            {
                $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);
                $valores_mapa_calor_valores->anyade_valor_fecha_hora($fecha_hora_local, $valor);
            }
        }

        // Si no hay datos no se hace nada
        if ($numero_ocurrencias_valores == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Colores de mapa de calor
        $colores_mapa_calor_valores = COLORES_VERDE_ROJO;

        // Variables para dibujar las gráficas
        $etiquetas_graficas = new VectorDatos();
        $etiquetas_graficas->anyade_etiqueta($nombre_sensor);

        $grafica_valores = new VectorDatos();
        $grafica_valores->anyade_dato($datos_grafica_valores->dame_datos());
        $grafica_valores_acumulados = new VectorDatos();
        if ($campo_incremental == true)
        {
            $grafica_valores_acumulados->anyade_dato($datos_grafica_valores_acumulados->dame_datos());
        }

        // Variables de comentarios
        $filas_comentarios = array();
        $lineas_verticales_comentarios = array();
        $tabla_comentarios = NULL;

        // Comentarios y texto de información de los datos
        if ($numero_ocurrencias_valores > 0)
        {
            $cadena_fecha_hora_inicio_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);

            $cadena_fecha_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);
            $cadena_fecha_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);

            // Se recuperan los comentarios y las líneas verticales para la gráfica
            switch ($comentarios)
            {
                case COMENTARIOS_GRAFICA:
                case COMENTARIOS_GRAFICA_TABLA:
                {
                    $nombres_sensores_comentarios = dame_nombres_sensores_descendientes_comentarios(array($id_sensor));
                    $filas_comentarios = Comentario::dame_filas_comentarios_objetos(
                        ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION,
                        $nombres_sensores_comentarios,
                        $cadena_fecha_hora_inicio_valores_base_datos_utc,
                        $cadena_fecha_hora_fin_valores_base_datos_utc,
                        NULL,
                        NULL,
                        NULL);
                    $lineas_verticales_comentarios = Comentario::dame_lineas_verticales_comentarios_informe(
                        $filas_comentarios,
                        false,
                        $milisegundos_desfase_zonas_horarias_cliente_local);
                    break;
                }
            }

            // Texto de información de los datos
            $min_valor = round($min_valor, $numero_decimales_valores);
            $max_valor = round($max_valor, $numero_decimales_valores);

            $cadena_min_valor = formatea_numero($min_valor, $numero_decimales_valores);
            $cadena_max_valor = formatea_numero($max_valor, $numero_decimales_valores);
            $cadena_diferencia_valores = formatea_numero($valor_final - $valor_inicial, $numero_decimales_valores);
            $cadena_media_valores = formatea_numero(($suma_valores / $numero_ocurrencias_valores), $numero_decimales_valores);
            $cadena_suma_valores = formatea_numero($suma_valores, $numero_decimales_valores);

            $texto_informacion_muestras = " (".$idiomas->_("número de muestras").": ".formatea_numero($numero_ocurrencias_valores, 0);
            if ($campo_incremental == true)
            {
                $texto_informacion_muestras .= ", ".$idiomas->_("periodo de tiempo de muestras").": ".dame_texto_periodo($suma_horas * 3600);
            }
            $texto_informacion_muestras .= ")";
            switch ($campo)
            {
                case CAMPO_ABSOLUTO:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de volúmenes absolutos del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                        $texto_informacion_muestras."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Volumen total del periodo").": ".$cadena_diferencia_valores." ".$unidad_medida." "."<br/>";
                    break;
                }
                case CAMPO_INCREMENTO:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de volúmenes del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                        $texto_informacion_muestras."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Volumen medio del periodo").": ".$cadena_media_valores." ".$unidad_medida."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Volumen total del periodo").": ".$cadena_suma_valores." ".$unidad_medida."<br/>";
                    break;
                }
                case CAMPO_CONSUMO:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de consumos del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                        $texto_informacion_muestras."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Consumo medio del periodo").": ".$cadena_media_valores." ".$unidad_medida."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Consumo total del periodo").": ".$cadena_suma_valores." ".$unidad_medida."<br/>";
                    break;
                }
                case CAMPO_COSTE:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de costes del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                        $texto_informacion_muestras."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Coste medio del periodo").": ".$cadena_media_valores." ".$unidad_medida."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Coste total del periodo").": ".$cadena_suma_valores." ".$unidad_medida."<br/>";
                    break;
                }
            }
        }

        // Tabla y número de comentarios
        if ($comentarios == COMENTARIOS_GRAFICA_TABLA)
        {
            if ((count($filas_comentarios) > 0) ||
                (($tipo_informe == TIPO_INFORME_WEB_EMIOS) && (NodoSensor::dame_administracion_comentarios_sensores() == true)))
            {
                if ($numero_elemento_plantilla_informe === NULL)
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_GAS;
                }
                else
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_GAS.",".$numero_elemento_plantilla_informe;
                }
                $tabla_comentarios = Comentario::dame_tabla_comentarios_objetos_informe(
                    $origen_comentarios,
                    $parametros_origen_comentarios,
                    "tabla-comentarios-sensor-informacion-gas",
                    $filas_comentarios,
                    NULL,
                    array($nombre_sensor),
                    $tipo_informe);
            }
        }
        $numero_comentarios = count($filas_comentarios);

        // Descripción del sensor
        $descripcion_sensor = dame_descripcion_sensor_informe($id_sensor);

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($min_valor == INF)
        {
            $min_valor = "ND";
        }
        if ($max_valor == -INF)
        {
            $max_valor = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "min_valor" => $min_valor,
            "max_valor" => $max_valor,
            "suma_valores" => $suma_valores,
            "etiquetas_graficas" => $etiquetas_graficas->dame_datos(),
            "grafica_valores" => $grafica_valores->dame_datos(),
            "campo_incremental" => $campo_incremental,
            "grafica_valores_acumulados" => $grafica_valores_acumulados->dame_datos(),
            "lineas_verticales_comentarios" => $lineas_verticales_comentarios,
            "tabla_comentarios" => $tabla_comentarios,
            "numero_comentarios" => $numero_comentarios,
            "fecha_inicio_valores" => $cadena_fecha_inicio_valores_local_local,
            "hora_inicio_valores" => $cadena_hora_inicio_valores_local_local,
            "fecha_fin_valores" => $cadena_fecha_fin_valores_local_local,
            "hora_fin_valores" => $cadena_hora_fin_valores_local_local,
            "colores_mapa_calor_valores" => $colores_mapa_calor_valores,
            "dias_mapa_calor_valores" => $valores_mapa_calor_valores->dame_dias(),
            "datos_mapa_calor_valores" => $valores_mapa_calor_valores->dame_datos(),
            "texto_informacion_datos" => $texto_informacion_datos,
            "descripcion_campo" => $descripcion_campo,
            "numero_decimales_valores" => $numero_decimales_valores,
            "unidad_medida" => $unidad_medida,
            "descripcion_sensor" => $descripcion_sensor);
        return ($resultado);
    }


    function dame_informacion_sensor_agua($parametros, $filas_valores_sensor)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $campo = $parametros["campo"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $tipo_mapa_calor = $parametros["tipo_mapa_calor"];
        $comentarios = $parametros["comentarios"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $tipo_informe = $parametros["tipo_informe"];
        $numero_elemento_plantilla_informe = $parametros["numero_elemento_plantilla_informe"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

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

        // Se recupera la información del ratio (si aplica)
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, CLASE_SENSOR_AGUA, $campo);
        if ($aplicar_ratio == true)
        {
            $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                $intervalo_valores,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas);
        }

        // Descripción del campo, número de decimales de valores y unidad de medida
        $descripcion_campo = dame_descripcion_campo_clase_sensor(CLASE_SENSOR_AGUA, $campo);
        $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor(CLASE_SENSOR_AGUA, $campo);
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor(CLASE_SENSOR_AGUA, ID_NINGUNO, $campo);
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio_sensor, $unidad_medida);
        }

        // Variables del informe
        $mostrar_mapas_calor = dame_mostrar_mapa_calor_sensor_informacion($intervalo_valores, $tipo_mapa_calor);
        $valores_mapa_calor_valores = new ValoresMapaCalor($tipo_mapa_calor);
        $numero_ocurrencias_valores = 0;
        $suma_valores = 0;
        $suma_horas = 0;
        $datos_grafica_valores = new VectorDatos();
        $datos_grafica_valores_acumulados = new VectorDatos();
        $min_valor = INF;
        $max_valor = -INF;
        $valor_inicial = NULL;
        $valor_final = NULL;
        $cadena_fecha_hora_inicio_valores_base_datos_utc = NULL;
        $cadena_fecha_hora_fin_valores_base_datos_utc = NULL;

        // Flag de campo incremental
        $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor(CLASE_SENSOR_AGUA, $campo);
        $campo_incremental = ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES);

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Se recorren las filas de valores del sensor
        $timestamp_fecha_hora_valor_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        foreach ($filas_valores_sensor as $fila_valor_sensor)
        {
            // Fecha y valor
            $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
            $valor = $fila_valor_sensor[$campo];
            if ($valor !== NULL)
            {
                $valor = (float) $valor;
                if ($aplicar_ratio == true)
                {
                    aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $valor);
                }
            }
            if ($valor === NULL)
            {
                continue;
            }

            // Valores máximos y mínimos y adición de los datos a las gráficas
            if ($valor > $max_valor)
            {
                $max_valor = $valor;
            }
            if ($valor < $min_valor)
            {
                $min_valor = $valor;
            }
            if ($valor_inicial === NULL)
            {
                $valor_inicial = $valor;
            }
            $valor_final = $valor;
            if ($cadena_fecha_hora_inicio_valores_base_datos_utc === NULL)
            {
                $cadena_fecha_hora_inicio_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
            }
            $cadena_fecha_hora_fin_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_valor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_valor_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_valor_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_valor_utc - $timestamp_fecha_hora_valor_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_grafica_valores->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                    if ($campo_incremental == true)
                    {
                        $datos_grafica_valores_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                    }
                }
            }
            $timestamp_fecha_hora_valor_anterior_utc = $timestamp_fecha_hora_valor_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade el valor (con tooltip personalizado si es campo incremental)
            anyade_dato_grafica_valores_tooltip_personalizado_campo_incremental(
                $datos_grafica_valores,
                $timestamp_fecha_hora_valor_utc,
                $valor,
                $campo_incremental,
                $intervalo_valores,
                $zona_horaria,
                2,
                $unidad_medida,
                $fila_valor_sensor);

            $numero_ocurrencias_valores += 1;
            $suma_valores += $valor;

            // Si el campo es incremental
            if ($campo_incremental == true)
            {
                // Horas
                $suma_horas += $fila_valor_sensor["horas"];

                // Se añade el valor
                $datos_grafica_valores_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_utc, $suma_valores);
            }

            // Datos para el mapa de calor del campo seleccionado
            if ($mostrar_mapas_calor == true)
            {
                $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);
                $valores_mapa_calor_valores->anyade_valor_fecha_hora($fecha_hora_local, $valor);
            }
        }

        // Si no hay datos no se hace nada
        if ($numero_ocurrencias_valores == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Colores de mapa de calor
        $colores_mapa_calor_valores = COLORES_VERDE_ROJO;

        // Variables para dibujar las gráficas
        $etiquetas_graficas = new VectorDatos();
        $etiquetas_graficas->anyade_etiqueta($nombre_sensor);

        $grafica_valores = new VectorDatos();
        $grafica_valores->anyade_dato($datos_grafica_valores->dame_datos());
        $grafica_valores_acumulados = new VectorDatos();
        if ($campo_incremental == true)
        {
            $grafica_valores_acumulados->anyade_dato($datos_grafica_valores_acumulados->dame_datos());
        }

        // Variables de comentarios
        $filas_comentarios = array();
        $lineas_verticales_comentarios = array();
        $tabla_comentarios = NULL;

        // Comentarios y texto de información de los datos
        if ($numero_ocurrencias_valores > 0)
        {
            $cadena_fecha_hora_inicio_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);

            $cadena_fecha_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);
            $cadena_fecha_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);

            // Se recuperan los comentarios y las líneas verticales para la gráfica
            switch ($comentarios)
            {
                case COMENTARIOS_GRAFICA:
                case COMENTARIOS_GRAFICA_TABLA:
                {
                    $nombres_sensores_comentarios = dame_nombres_sensores_descendientes_comentarios(array($id_sensor));
                    $filas_comentarios = Comentario::dame_filas_comentarios_objetos(
                        ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION,
                        $nombres_sensores_comentarios,
                        $cadena_fecha_hora_inicio_valores_base_datos_utc,
                        $cadena_fecha_hora_fin_valores_base_datos_utc,
                        NULL,
                        NULL,
                        NULL);
                    $lineas_verticales_comentarios = Comentario::dame_lineas_verticales_comentarios_informe(
                        $filas_comentarios,
                        false,
                        $milisegundos_desfase_zonas_horarias_cliente_local);
                    break;
                }
            }

            // Texto de información de los datos
            $min_valor = round($min_valor, $numero_decimales_valores);
            $max_valor = round($max_valor, $numero_decimales_valores);

            $cadena_min_valor = formatea_numero($min_valor, $numero_decimales_valores);
            $cadena_max_valor = formatea_numero($max_valor, $numero_decimales_valores);
            $cadena_diferencia_valores = formatea_numero($valor_final - $valor_inicial, $numero_decimales_valores);
            $cadena_media_valores = formatea_numero(($suma_valores / $numero_ocurrencias_valores), $numero_decimales_valores);
            $cadena_suma_valores = formatea_numero($suma_valores, $numero_decimales_valores);

            $texto_informacion_muestras = " (".$idiomas->_("número de muestras").": ".formatea_numero($numero_ocurrencias_valores, 0);
            if ($campo_incremental == true)
            {
                $texto_informacion_muestras .= ", ".$idiomas->_("periodo de tiempo de muestras").": ".dame_texto_periodo($suma_horas * 3600);
            }
            $texto_informacion_muestras .= ")";
            switch ($campo)
            {
                case CAMPO_ABSOLUTO:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de consumos absolutos del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                        $texto_informacion_muestras."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Consumo total del periodo").": ".$cadena_diferencia_valores." ".$unidad_medida." "."<br/>";
                    break;
                }
                case CAMPO_INCREMENTO:
                {
                    $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Rango de consumos del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor." ".$unidad_medida.
                        $texto_informacion_muestras."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Consumo medio del periodo").": ".$cadena_media_valores." ".$unidad_medida."<br/>";
                    $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                        $idiomas->_("Consumo total del periodo").": ".$cadena_suma_valores." ".$unidad_medida."<br/>";
                    break;
                }
            }
        }

        // Tabla y número de comentarios
        if ($comentarios == COMENTARIOS_GRAFICA_TABLA)
        {
            if ((count($filas_comentarios) > 0) ||
                (($tipo_informe == TIPO_INFORME_WEB_EMIOS) && (NodoSensor::dame_administracion_comentarios_sensores() == true)))
            {
                if ($numero_elemento_plantilla_informe === NULL)
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_AGUA;
                }
                else
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_AGUA.",".$numero_elemento_plantilla_informe;
                }
                $tabla_comentarios = Comentario::dame_tabla_comentarios_objetos_informe(
                    $origen_comentarios,
                    $parametros_origen_comentarios,
                    "tabla-comentarios-sensor-informacion-gas",
                    $filas_comentarios,
                    NULL,
                    array($nombre_sensor),
                    $tipo_informe);
            }
        }
        $numero_comentarios = count($filas_comentarios);

        // Descripción del sensor
        $descripcion_sensor = dame_descripcion_sensor_informe($id_sensor);

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($min_valor == INF)
        {
            $min_valor = "ND";
        }
        if ($max_valor == -INF)
        {
            $max_valor = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "min_valor" => $min_valor,
            "max_valor" => $max_valor,
            "suma_valores" => $suma_valores,
            "etiquetas_graficas" => $etiquetas_graficas->dame_datos(),
            "grafica_valores" => $grafica_valores->dame_datos(),
            "campo_incremental" => $campo_incremental,
            "grafica_valores_acumulados" => $grafica_valores_acumulados->dame_datos(),
            "lineas_verticales_comentarios" => $lineas_verticales_comentarios,
            "tabla_comentarios" => $tabla_comentarios,
            "numero_comentarios" => $numero_comentarios,
            "fecha_inicio_valores" => $cadena_fecha_inicio_valores_local_local,
            "hora_inicio_valores" => $cadena_hora_inicio_valores_local_local,
            "fecha_fin_valores" => $cadena_fecha_fin_valores_local_local,
            "hora_fin_valores" => $cadena_hora_fin_valores_local_local,
            "colores_mapa_calor_valores" => $colores_mapa_calor_valores,
            "dias_mapa_calor_valores" => $valores_mapa_calor_valores->dame_dias(),
            "datos_mapa_calor_valores" => $valores_mapa_calor_valores->dame_datos(),
            "texto_informacion_datos" => $texto_informacion_datos,
            "descripcion_campo" => $descripcion_campo,
            "numero_decimales_valores" => $numero_decimales_valores,
            "unidad_medida" => $unidad_medida,
            "descripcion_sensor" => $descripcion_sensor);
        return ($resultado);
    }


    function dame_informacion_sensor_generica($parametros, $filas_valores_sensor)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $campo = $parametros["campo"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $tipo_mapa_calor = $parametros["tipo_mapa_calor"];
        $comentarios = $parametros["comentarios"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $tipo_informe = $parametros["tipo_informe"];
        $numero_elemento_plantilla_informe = $parametros["numero_elemento_plantilla_informe"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

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

        // Se recupera la información del ratio (si aplica)
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, CLASE_SENSOR_GENERICA, $campo);
        if ($aplicar_ratio == true)
        {
            $info_ratio_sensor = dame_info_ratio_sensor_fechas(
                $id_ratio,
                $id_sensor,
                $cadena_fecha_hora_inicio_base_datos_utc,
                $cadena_fecha_hora_fin_base_datos_utc,
                $intervalo_valores,
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas);
        }

        // Se recuperan los parámetros de la clase genérica del sensor
        $consulta_sensor = "
            SELECT
                parametros_clase
            FROM sensores
            WHERE
                (nombre = '".$bd_red->_($nombre_sensor)."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_sensor = $bd_red->ejecuta_consulta($consulta_sensor);
        if (($res_sensor == false) || ($res_sensor->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensor."'");
        }
        $fila_sensor = $res_sensor->dame_siguiente_fila();
        $cadena_parametros_clase_sensor = $fila_sensor["parametros_clase"];

        // Parámetros de clase genérica
        $parametros_clase_generica = NodoSensor::dame_parametros_clase_generica($cadena_parametros_clase_sensor);
        $nombre_medida = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_NOMBRE_MEDIDA];
        $unidad_medida = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_UNIDAD_MEDIDA];
        $colores_mapa_calor_valor = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_COLORES_MAPA_CALOR_VALOR];
        $colores_mapa_calor_incremento = $parametros_clase_generica[INDICE_PARAMETRO_CLASE_SENSOR_GENERICA_COLORES_MAPA_CALOR_INCREMENTO];

        // Tipo de líneas de valores y número de decimales de valores
        $tipo_lineas_valores = dame_tipo_lineas_valores_intervalo_valores_campo_clase_sensor(
            $intervalo_valores,
            CLASE_SENSOR_GENERICA,
            $id_sensor,
            $campo);

        // Descripción del campo, número de decimales de valores y unidad de medida
        $campo_sin_agrupaciones_valores = elimina_tipo_agrupacion_valores_campo_sensor($campo);
        $descripcion_campo = dame_descripcion_campo_clase_sensor(CLASE_SENSOR_GENERICA, $campo_sin_agrupaciones_valores);
        $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor(CLASE_SENSOR_GENERICA, $campo);
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio_sensor, $unidad_medida);
        }
        if ($unidad_medida != "")
        {
            $cadena_unidad_medida = " ".$unidad_medida;
        }
        else
        {
            $cadena_unidad_medida = "";
        }

        // Variables del informe
        $mostrar_mapas_calor = dame_mostrar_mapa_calor_sensor_informacion($intervalo_valores, $tipo_mapa_calor);
        $valores_mapa_calor_valores = new ValoresMapaCalor($tipo_mapa_calor);
        $numero_ocurrencias_valores = 0;
        $suma_valores = 0;
        $suma_horas = 0;
        $datos_grafica_valores = new VectorDatos();
        $datos_grafica_valores_acumulados = new VectorDatos();
        $min_valor = INF;
        $max_valor = -INF;
        $valor_inicial = NULL;
        $valor_final = NULL;
        $min_suma_valores = INF;
        $max_suma_valores = -INF;
        $cadena_fecha_hora_inicio_valores_base_datos_utc = NULL;
        $cadena_fecha_hora_fin_valores_base_datos_utc = NULL;

        // Flag de campo incremental
        $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor(CLASE_SENSOR_GENERICA, $campo);
        $campo_incremental = ($tipo_valores_campo == TIPO_VALORES_SENSOR_INCREMENTALES);

        // Segundos máximos entre valores (para separar las líneas de las gráficas)
        $segundos_maximos_entre_valores_grafica = dame_segundos_maximos_entre_valores_grafica($intervalo_valores, $id_sensor);

        // Se recorren los valores del sensor
        $timestamp_fecha_hora_valor_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        foreach ($filas_valores_sensor as $fila_valor_sensor)
        {
            // Fecha y valor
            $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor['fecha_hora'];
            $valor = $fila_valor_sensor[$campo];
            if ($valor !== NULL)
            {
                $valor = (float) $valor;
                if ($aplicar_ratio == true)
                {
                    aplica_ratio_fecha_valor($info_ratio_sensor, $cadena_fecha_hora_base_datos_utc, $valor);
                }
            }
            if ($valor === NULL)
            {
                continue;
            }

            // Valores máximos y mínimos y adición de los datos a las gráficas
            if ($valor > $max_valor)
            {
                $max_valor = $valor;
            }
            if ($valor < $min_valor)
            {
                $min_valor = $valor;
            }
            if ($valor_inicial === NULL)
            {
                $valor_inicial = $valor;
            }
            $valor_final = $valor;
            if ($cadena_fecha_hora_inicio_valores_base_datos_utc === NULL)
            {
                $cadena_fecha_hora_inicio_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;
            }
            $cadena_fecha_hora_fin_valores_base_datos_utc = $cadena_fecha_hora_base_datos_utc;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_valor_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_valor_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_valor_anterior_utc !== NULL))
            {
                $segundos_entre_valores = ($timestamp_fecha_hora_valor_utc - $timestamp_fecha_hora_valor_anterior_utc) / 1000;
                if ($segundos_entre_valores > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_grafica_valores->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                    if ($campo_incremental == true)
                    {
                        $datos_grafica_valores_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_anterior_utc + 1, NULL);
                    }
                }
            }
            $timestamp_fecha_hora_valor_anterior_utc = $timestamp_fecha_hora_valor_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Se añade el valor (con tooltip personalizado si es campo incremental)
            anyade_dato_grafica_valores_tooltip_personalizado_campo_incremental(
                $datos_grafica_valores,
                $timestamp_fecha_hora_valor_utc,
                $valor,
                $campo_incremental,
                $intervalo_valores,
                $zona_horaria,
                $numero_decimales_valores,
                $unidad_medida,
                $fila_valor_sensor);

            // Número de ocurrencias y suma de valores
            $numero_ocurrencias_valores += 1;
            $suma_valores += $valor;

            // Si el campo es incremental
            if ($campo_incremental == true)
            {
                // Horas
                $suma_horas += $fila_valor_sensor["horas"];

                // Suma de valores
                if ($suma_valores > $max_suma_valores)
                {
                    $max_suma_valores = $suma_valores;
                }
                if ($suma_valores < $min_suma_valores)
                {
                    $min_suma_valores = $suma_valores;
                }

                // Se añade el valor
                $datos_grafica_valores_acumulados->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_utc, $suma_valores);
            }

            // Datos para los mapas de calor de valores
            if ($mostrar_mapas_calor == true)
            {
                $fecha_hora_utc = convierte_cadena_a_fecha($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $fecha_hora_local = dame_fecha_hora_local($fecha_hora_utc);
                $valores_mapa_calor_valores->anyade_valor_fecha_hora($fecha_hora_local, $valor);
            }
        }

        // Si no hay datos no se hace nada
        if ($numero_ocurrencias_valores == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Variables para dibujar las gráficas
        $etiquetas_graficas = new VectorDatos();
        $etiquetas_graficas->anyade_etiqueta($nombre_sensor);

        $grafica_valores = new VectorDatos();
        $grafica_valores->anyade_dato($datos_grafica_valores->dame_datos());
        $grafica_valores_acumulados = new VectorDatos();
        $grafica_valores_acumulados->anyade_dato($datos_grafica_valores_acumulados->dame_datos());

        switch ($campo_sin_agrupaciones_valores)
        {
            case CAMPO_VALOR:
            {
                $colores_mapa_calor_valores = $colores_mapa_calor_valor;
                break;
            }
            case CAMPO_INCREMENTO:
            {
                $colores_mapa_calor_valores = $colores_mapa_calor_incremento;
                break;
            }
        }

        // Variables de comentarios
        $filas_comentarios = array();
        $lineas_verticales_comentarios = array();
        $tabla_comentarios = NULL;

        // Si hay valores
        if ($numero_ocurrencias_valores > 0)
        {
            $cadena_fecha_hora_inicio_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_valores_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_valores_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);

            $cadena_fecha_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);
            $cadena_fecha_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_hora_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_HORA);

            // Se recuperan los comentarios y las líneas verticales para la gráfica
            switch ($comentarios)
            {
                case COMENTARIOS_GRAFICA:
                case COMENTARIOS_GRAFICA_TABLA:
                {
                    $nombres_sensores_comentarios = dame_nombres_sensores_descendientes_comentarios(array($id_sensor));
                    $filas_comentarios = Comentario::dame_filas_comentarios_objetos(
                        ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION,
                        $nombres_sensores_comentarios,
                        $cadena_fecha_hora_inicio_valores_base_datos_utc,
                        $cadena_fecha_hora_fin_valores_base_datos_utc,
                        NULL,
                        NULL,
                        NULL);
                    $lineas_verticales_comentarios = Comentario::dame_lineas_verticales_comentarios_informe(
                        $filas_comentarios,
                        false,
                        $milisegundos_desfase_zonas_horarias_cliente_local);
                    break;
                }
            }

            // Texto de información de los datos
            $min_valor = round($min_valor, $numero_decimales_valores);
            $max_valor = round($max_valor, $numero_decimales_valores);

            $cadena_min_valor = formatea_numero($min_valor, $numero_decimales_valores);
            $cadena_max_valor = formatea_numero($max_valor, $numero_decimales_valores);
            $cadena_diferencia_valores = formatea_numero($valor_final - $valor_inicial, $numero_decimales_valores);
            if ($cadena_diferencia_valores == "-0.00")
            {
                $cadena_diferencia_valores = "0.00";
            }
            $cadena_media_valores = formatea_numero(($suma_valores / $numero_ocurrencias_valores), $numero_decimales_valores);
            if ($cadena_media_valores == "-0.00")
            {
                $cadena_media_valores = "0.00";
            }
            $cadena_suma_valores = formatea_numero($suma_valores, $numero_decimales_valores);

            $texto_informacion_muestras = " (".$idiomas->_("número de muestras").": ".formatea_numero($numero_ocurrencias_valores, 0);
            if ($campo_incremental == true)
            {
                $texto_informacion_muestras .= ", ".$idiomas->_("periodo de tiempo de muestras").": ".dame_texto_periodo($suma_horas * 3600);
            }
            $texto_informacion_muestras .= ")";
            switch ($campo_sin_agrupaciones_valores)
            {
                case CAMPO_VALOR:
                {
                    if ($nombre_medida == "")
                    {
                        $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                            $idiomas->_("Rango de valores del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor.
                            $texto_informacion_muestras."<br/>";
                        $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                            $idiomas->_("Valor medio del periodo").": ".$cadena_media_valores."<br/>";
                        $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                            $idiomas->_("Diferencia de valores del periodo").": ".$cadena_diferencia_valores."<br/>";
                    }
                    else
                    {
                        $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                            $idiomas->_("Rango de")." ".$nombre_medida." ".$idiomas->_("del periodo").": ".$cadena_min_valor.$cadena_unidad_medida." / ".$cadena_max_valor.$cadena_unidad_medida.
                            " (".$idiomas->_("número de muestras").": ".formatea_numero($numero_ocurrencias_valores, 0).")"."<br/>";
                        $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                            $idiomas->_("Valor medio de")." ".$nombre_medida." ".$idiomas->_("del periodo").": ".$cadena_media_valores.$cadena_unidad_medida."<br/>";
                        $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                            $idiomas->_("Diferencia de")." ".$nombre_medida." ".$idiomas->_("del periodo").": ".$cadena_diferencia_valores.$cadena_unidad_medida."<br/>";
                    }
                    break;
                }
                case CAMPO_INCREMENTO:
                {
                    if ($nombre_medida == "")
                    {
                        $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                            $idiomas->_("Rango de incrementos de valores del periodo").": ".$cadena_min_valor." / ".$cadena_max_valor.
                            $texto_informacion_muestras."<br/>";
                        $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                            $idiomas->_("Incremento medio de valores del periodo").": ".$cadena_media_valores."<br/>";
                        $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                            $idiomas->_("Incremento total de valores del periodo").": ".$cadena_suma_valores."<br/>";
                    }
                    else
                    {
                        $texto_informacion_datos = "<i class='icon-info-sign color-azul'></i> ".
                            $idiomas->_("Rango de incrementos de")." ".$nombre_medida." ".$idiomas->_("del periodo").": ".$cadena_min_valor.$cadena_unidad_medida." / ".$cadena_max_valor.$cadena_unidad_medida.
                            $texto_informacion_muestras."<br/>";
                        $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                            $idiomas->_("Incremento medio de")." ".$nombre_medida." ".$idiomas->_("del periodo").": ".$cadena_media_valores.$cadena_unidad_medida."<br/>";
                        $texto_informacion_datos .= "<i class='icon-info-sign color-azul'></i> ".
                            $idiomas->_("Incremento total de")." ".$nombre_medida." ".$idiomas->_("del periodo").": ".$cadena_suma_valores.$cadena_unidad_medida."<br/>";
                    }
                    break;
                }
            }
        }

        // Tabla y número de comentarios
        if ($comentarios == COMENTARIOS_GRAFICA_TABLA)
        {
            if ((count($filas_comentarios) > 0) ||
                (($tipo_informe == TIPO_INFORME_WEB_EMIOS) && (NodoSensor::dame_administracion_comentarios_sensores() == true)))
            {
                if ($numero_elemento_plantilla_informe === NULL)
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_GENERICA;
                }
                else
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME;
                    $parametros_origen_comentarios = TIPO_INFORME_SENSORES_INFORMACION_GENERICA.",".$numero_elemento_plantilla_informe;
                }
                $tabla_comentarios = Comentario::dame_tabla_comentarios_objetos_informe(
                    $origen_comentarios,
                    $parametros_origen_comentarios,
                    "tabla-comentarios-sensor-informacion-generica",
                    $filas_comentarios,
                    NULL,
                    array($nombre_sensor),
                    $tipo_informe);
            }
        }
        $numero_comentarios = count($filas_comentarios);

        // Descripción del sensor
        $descripcion_sensor = dame_descripcion_sensor_informe($id_sensor);

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($min_valor == INF)
        {
            $min_valor = "ND";
        }
        if ($max_valor == -INF)
        {
            $max_valor = "ND";
        }
        if ($min_suma_valores == INF)
        {
            $min_suma_valores = "ND";
        }
        if ($max_suma_valores == -INF)
        {
            $max_suma_valores = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "min_valor" => $min_valor,
            "max_valor" => $max_valor,
            "min_suma_valores" => $min_suma_valores,
            "max_suma_valores" => $max_suma_valores,
            "etiquetas_graficas" => $etiquetas_graficas->dame_datos(),
            "tipo_lineas_valores" => $tipo_lineas_valores,
            "grafica_valores" => $grafica_valores->dame_datos(),
            "campo_incremental" => $campo_incremental,
            "grafica_valores_acumulados" => $grafica_valores_acumulados->dame_datos(),
            "lineas_verticales_comentarios" => $lineas_verticales_comentarios,
            "tabla_comentarios" => $tabla_comentarios,
            "numero_comentarios" => $numero_comentarios,
            "fecha_inicio_valores" => $cadena_fecha_inicio_valores_local_local,
            "hora_inicio_valores" => $cadena_hora_inicio_valores_local_local,
            "fecha_fin_valores" => $cadena_fecha_fin_valores_local_local,
            "hora_fin_valores" => $cadena_hora_fin_valores_local_local,
            "colores_mapa_calor_valores" => $colores_mapa_calor_valores,
            "dias_mapa_calor_valores" => $valores_mapa_calor_valores->dame_dias(),
            "datos_mapa_calor_valores" => $valores_mapa_calor_valores->dame_datos(),
            "texto_informacion_datos" => $texto_informacion_datos,
            "nombre_medida" => $nombre_medida,
            "descripcion_campo" => $descripcion_campo,
            "numero_decimales_valores" => $numero_decimales_valores,
            "unidad_medida" => $unidad_medida,
            "descripcion_sensor" => $descripcion_sensor);
        return ($resultado);
    }


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_sensores_informacion($clase_sensor)
    {
        $elementos_informe = array();
        switch ($clase_sensor)
        {
            case CLASE_NINGUNA:
            {
                break;
            }
            case CLASE_SENSOR_TEMPERATURA:
            {
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_TEMPERATURA);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_TEMPERATURA);
                break;
            }
            case CLASE_SENSOR_HUMEDAD:
            {
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_HUMEDAD);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_HUMEDAD);
                break;
            }
            case CLASE_SENSOR_LUZ_INTERIOR:
            {
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_LUZ);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_LUZ_ARTIFICIAL);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_LUZ);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_LUZ_ARTIFICIAL);
                break;
            }
            case CLASE_SENSOR_VIENTO:
            {
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VELOCIDAD_VIENTO);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_DIRECCION_VIENTO);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICOS_VIENTO);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VELOCIDAD_VIENTO);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_DIRECCION_VIENTO);
                break;
            }
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES_ACUMULADOS);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VALORES);
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION:
            {
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_CORTES_TENSION);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_CORTES_TENSION_ACUMULADOS);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_CORTES_TENSION);
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES_ACUMULADOS);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VALORES);
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES_ACUMULADOS);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VALORES);
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES_ACUMULADOS);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VALORES);
                break;
            }
            case CLASE_SENSOR_GENERICA:
            {
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES_ACUMULADOS);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS);
                array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VALORES);
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor desconocida: '".$clase_sensor."'");
            }
        }
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_sensores_informacion($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_TEMPERATURA:
            {
                $descripcion = "Gráfica de temperatura";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_HUMEDAD:
            {
                $descripcion = "Gráfica de humedad";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_LUZ:
            {
                $descripcion = "Gráfica de iluminación";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_LUZ_ARTIFICIAL:
            {
                $descripcion = "Gráfica de luz artificial";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VELOCIDAD_VIENTO:
            {
                $descripcion = "Gráfica de velocidad de viento";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_DIRECCION_VIENTO:
            {
                $descripcion = "Gráfica de direccion de viento";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES:
            {
                $descripcion = "Gráfica de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_VALORES_ACUMULADOS:
            {
                $descripcion = "Gráfica de valores (acumulado)";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_CORTES_TENSION:
            {
                $descripcion = "Gráfica de cortes de tensión";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_CORTES_TENSION_ACUMULADOS:
            {
                $descripcion = "Gráfica de cortes de tensión (acumulado)";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_DIA_NOCHE:
            {
                $descripcion = "Gráfica de salidas y puestas de sol";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_DURACION_DIAS:
            {
                $descripcion = "Gráfica de duración de días";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_ACELERACION:
            {
                $descripcion = "Gráfica de aceleración";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_ORIENTACION:
            {
                $descripcion = "Gráfica de orientación";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_NIVEL_BATERIA:
            {
                $descripcion = "Gráfica de nivel de batería";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_FLAGS:
            {
                $descripcion = "Gráfica de flags";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICA_TIPO_MENSAJE:
            {
                $descripcion = "Gráfica de tipo de mensaje";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_DESCRIPCION_SENSOR:
            {
                $descripcion = "Descripción de sensor";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_TEXTO_INFORMACION:
            {
                $descripcion = "Texto de información";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_TABLA_COMENTARIOS:
            {
                $descripcion = "Tabla de comentarios";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_GRAFICOS_VIENTO:
            {
                $descripcion = "Gráficos de viento";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_TEMPERATURA:
            {
                $descripcion = "Mapa de calor de temperatura";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_HUMEDAD:
            {
                $descripcion = "Mapa de calor de humedad";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_LUZ:
            {
                $descripcion = "Mapa de calor de iluminación";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_LUZ_ARTIFICIAL:
            {
                $descripcion = "Mapa de calor de luz artificial";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VELOCIDAD_VIENTO:
            {
                $descripcion = "Mapa de calor de velocidad de viento";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_DIRECCION_VIENTO:
            {
                $descripcion = "Mapa de calor de dirección de viento";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_VALORES:
            {
                $descripcion = "Mapa de calor de valores";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_INFORMACION_MAPA_CALOR_CORTES_TENSION:
            {
                $descripcion = "Mapa de calor de cortes de tensión";
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


    function dame_html_informe_tipo_sensores_informacion($clase_sensor, $tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_TEMPERATURA:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_informe .= "
                            <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-informacion-temperatura'>
                                <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                            </div>
                            <div id='informe-sensores-informacion-temperatura' hidden>
                                <div class='grafica100' id='grafica-informacion-temperatura'></div>
                                <div class='texto100' id='descripcion-sensor-informacion-temperatura'></div>
                                <div class='texto100' id='texto-informacion-datos-informacion-temperatura'></div>
                                <div class='tabla-datos100' id='contenedor-tabla-comentarios-informacion-temperatura'></div>
                                <div class='mapa-calor100' id='mapa-calor-informacion-temperatura'></div>
                                <div id='parametros-resultado-informe-informacion-temperatura' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        // Página 'Temperatura (1)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-temperatura-1'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-temperatura-1'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-informacion-temperatura'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='descripcion-sensor-informacion-temperatura'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='texto-informacion-datos-informacion-temperatura'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-comentarios-informacion-temperatura'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-informacion-temperatura-1'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";

                        // Página 'Temperatura (2)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-temperatura-2'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-temperatura-2'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-informacion-temperatura-2'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_HUMEDAD:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_informe .= "
                            <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-informacion-humedad'>
                                <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                            </div>
                            <div id='informe-sensores-informacion-humedad' hidden>
                                <div class='grafica100' id='grafica-informacion-humedad'></div>
                                <div class='texto100' id='descripcion-sensor-informacion-humedad'></div>
                                <div class='texto100' id='texto-informacion-datos-informacion-humedad'></div>
                                <div class='tabla-datos100' id='contenedor-tabla-comentarios-informacion-humedad'></div>
                                <div class='mapa-calor100' id='mapa-calor-informacion-humedad'></div>
                                <div id='parametros-resultado-informe-informacion-humedad' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        // Página 'Humedad (1)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-humedad-1'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-humedad-1'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-informacion-humedad'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='descripcion-sensor-informacion-humedad'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='texto-informacion-datos-informacion-humedad'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-comentarios-informacion-humedad'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-informacion-humedad-1'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";

                        // Página 'Humedad (2)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-humedad-2'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-humedad-2'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-informacion-humedad-2'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_LUZ_INTERIOR:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_informe .= "
                            <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-informacion-luz-interior'>
                                <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                            </div>
                            <div id='informe-sensores-informacion-luz-interior' hidden>
                                <div class='grafica100' id='grafica-luz-informacion-luz-interior'></div>
                                <div class='grafica100' id='grafica-luz-artificial-informacion-luz-interior'></div>
                                <div class='texto100' id='descripcion-sensor-informacion-luz-interior'></div>
                                <div class='texto100' id='texto-informacion-datos-informacion-luz-interior'></div>
                                <div class='tabla-datos100' id='contenedor-tabla-comentarios-informacion-luz-interior'></div>
                                <div class='mapa-calor100' id='mapa-calor-luz-informacion-luz-interior'></div>
                                <div class='mapa-calor100' id='mapa-calor-luz-artificial-informacion-luz-interior'></div>
                                <div id='parametros-resultado-informe-informacion-luz-interior' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        // Página 'Luz interior (1)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-luz-interior-1'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-luz-interior-1'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-luz-informacion-luz-interior'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-luz-artificial-informacion-luz-interior'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='descripcion-sensor-informacion-luz-interior'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='texto-informacion-datos-informacion-luz-interior'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-comentarios-informacion-luz-interior'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";

                        // Página 'Luz interior (2)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-luz-interior-2'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-luz-interior-2'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-luz-informacion-luz-interior'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";

                        // Página 'Luz interior (3)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-luz-interior-3'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-luz-interior-3'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-luz-artificial-informacion-luz-interior'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_VIENTO:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_informe .= "
                            <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-informacion-viento'>
                                <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                            </div>
                            <div id='informe-sensores-informacion-viento' hidden>
                                <div class='grafica100' id='grafica-velocidad-informacion-viento'></div>
                                <div class='grafica100' id='grafica-direccion-informacion-viento'></div>
                                <div class='texto100' id='descripcion-sensor-informacion-viento'></div>
                                <div class='texto100' id='texto-informacion-datos-informacion-viento'></div>
                                <div class='tabla-datos100' id='contenedor-tabla-comentarios-informacion-viento'></div>
                                <div class='grafico50 centrado' id='grafico-frecuencia-informacion-viento'></div>
                                <div class='grafico50 centrado' id='grafico-velocidad-informacion-viento'></div>
                                <div class='fin-graficos50'></div>
                                <div class='mapa-calor100' id='mapa-calor-velocidad-informacion-viento'></div>
                                <div class='mapa-calor100' id='mapa-calor-direccion-informacion-viento'></div>
                                <div id='parametros-resultado-informe-informacion-viento' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        // Página 'Viento (1)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-viento-1'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_VIENTO);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-viento-1'></div>
                                <div class='grafica100-informe-fichero centrado separacion-superior-elementos-informe-fichero' id='grafica-velocidad-informacion-viento'></div>
                                <div class='grafica100-informe-fichero centrado separacion-superior-elementos-informe-fichero' id='grafica-direccion-informacion-viento'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='descripcion-sensor-informacion-viento'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='texto-informacion-datos-informacion-viento'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-comentarios-informacion-viento'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";

                        // Página 'Viento (2)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-viento-2'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_VIENTO);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-viento-2'></div>
                                <div class='grafico50-informe-fichero separacion-superior-elementos-informe-fichero' id='grafico-frecuencia-informacion-viento'></div>
                                <div class='grafico50-informe-fichero separacion-superior-elementos-informe-fichero' id='grafico-velocidad-informacion-viento'></div>
                                <div class='fin-graficos50-informe-fichero'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";

                        // Página 'Viento (3)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-viento-3'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_VIENTO);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-viento-3'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-velocidad-informacion-viento'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";

                        // Página 'Viento (4)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-viento-4'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_VIENTO);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-viento-4'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-direccion-informacion-viento'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                // Tipo de informe de información y sufijo de controles
                $tipo_informe_sensores_informacion = NULL;
                $sufijo_tipo_energia = NULL;
                switch ($clase_sensor)
                {
                    case CLASE_SENSOR_ENERGIA_ACTIVA:
                    {
                        $tipo_informe_sensores_informacion = TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA;
                        $sufijo_tipo_energia = "activa";
                        break;
                    }
                    case CLASE_SENSOR_ENERGIA_REACTIVA:
                    {
                        $tipo_informe_sensores_informacion = TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA;
                        $sufijo_tipo_energia = "reactiva";
                        break;
                    }
                }

                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_informe .= "
                            <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-informacion-energia-".$sufijo_tipo_energia."'>
                                <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                            </div>
                            <div id='informe-sensores-informacion-energia-".$sufijo_tipo_energia."' hidden>
                                <div class='grafica100' id='grafica-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div class='grafica100' id='grafica-informacion-energia-".$sufijo_tipo_energia."-acumulado'></div>
                                <div class='texto100' id='descripcion-sensor-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div class='texto100' id='texto-informacion-datos-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div class='tabla-datos100' id='contenedor-tabla-comentarios-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div class='mapa-calor100' id='mapa-calor-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div id='parametros-resultado-informe-informacion-energia-".$sufijo_tipo_energia."' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        // Página 'Energía (1)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-energia-".$sufijo_tipo_energia."-1'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion($tipo_informe_sensores_informacion);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-energia-".$sufijo_tipo_energia."-1'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-informacion-energia-".$sufijo_tipo_energia."-acumulado'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='descripcion-sensor-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='texto-informacion-datos-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-comentarios-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-informacion-energia-".$sufijo_tipo_energia."-1'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";

                        // Página 'Energía (2)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-energia-".$sufijo_tipo_energia."-2'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion($tipo_informe_sensores_informacion);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-energia-".$sufijo_tipo_energia."-2'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-informacion-energia-".$sufijo_tipo_energia."-2'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_informe .= "
                            <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-informacion-cortes-tension'>
                                <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                            </div>
                            <div id='informe-sensores-informacion-cortes-tension' hidden>
                                <div class='grafica100' id='grafica-informacion-cortes-tension-cortes'></div>
                                <div class='grafica100' id='grafica-informacion-cortes-tension-cortes-acumulados'></div>
                                <div class='texto100' id='descripcion-sensor-informacion-cortes-tension'></div>
                                <div class='texto100' id='texto-informacion-datos-informacion-cortes-tension'></div>
                                <div class='tabla-datos100' id='contenedor-tabla-comentarios-informacion-cortes-tension'></div>
                                <div class='mapa-calor100' id='mapa-calor-informacion-cortes-tension-cortes'></div>
                                <div id='parametros-resultado-informe-informacion-cortes-tension' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        // Página 'Cortes de tensión (1)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-cortes-tension-1'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-cortes-tension-1'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-informacion-cortes-tension-cortes'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-informacion-cortes-tension-cortes-acumulados'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='descripcion-sensor-informacion-cortes-tension'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='texto-informacion-datos-informacion-cortes-tension'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-comentarios-informacion-cortes-tension'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-informacion-cortes-tension-cortes-1'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";

                        // Página 'Cortes de tensión (2)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-cortes-tension-2'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-cortes-tension-2'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-informacion-cortes-tension-cortes-2'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_informe .= "
                            <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-informacion-compra-energia'>
                                <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                            </div>
                            <div id='informe-sensores-informacion-compra-energia' hidden>
                                <div class='grafica100' id='grafica-informacion-compra-energia'></div>
                                <div class='grafica100' id='grafica-informacion-compra-energia-acumulado'></div>
                                <div class='texto100' id='descripcion-sensor-informacion-compra-energia'></div>
                                <div class='texto100' id='texto-informacion-datos-informacion-compra-energia'></div>
                                <div class='tabla-datos100' id='contenedor-tabla-comentarios-informacion-compra-energia'></div>
                                <div class='mapa-calor100' id='mapa-calor-informacion-compra-energia'></div>
                                <div id='parametros-resultado-informe-informacion-compra-energia' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        // Página 'Compra de energía (1)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-compra-energia-1'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-compra-energia-1'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-informacion-compra-energia'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-informacion-compra-energia-acumulado'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='descripcion-sensor-informacion-compra-energia'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='texto-informacion-datos-informacion-compra-energia'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-comentarios-informacion-compra-energia'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-informacion-compra-energia-1'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";

                        // Página 'Compra de energía (2)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-compra-energia-2'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-compra-energia-2'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-informacion-compra-energia-2'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_informe .= "
                            <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-informacion-gas'>
                                <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                            </div>
                            <div id='informe-sensores-informacion-gas' hidden>
                                <div class='grafica100' id='grafica-informacion-gas'></div>
                                <div class='grafica100' id='grafica-informacion-gas-acumulado'></div>
                                <div class='texto100' id='descripcion-sensor-informacion-gas'></div>
                                <div class='texto100' id='texto-informacion-datos-informacion-gas'></div>
                                <div class='tabla-datos100' id='contenedor-tabla-comentarios-informacion-gas'></div>
                                <div class='mapa-calor100' id='mapa-calor-informacion-gas'></div>
                                <div id='parametros-resultado-informe-informacion-gas' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        // Página 'Gas (1)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-gas-1'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_GAS);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-gas-1'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-informacion-gas'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-informacion-gas-acumulado'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='descripcion-sensor-informacion-gas'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='texto-informacion-datos-informacion-gas'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-comentarios-informacion-gas'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-informacion-gas-1'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";

                        // Página 'Gas (2)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-gas-2'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_GAS);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-gas-2'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-informacion-gas-2'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_informe .= "
                            <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-informacion-agua'>
                                <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                            </div>
                            <div id='informe-sensores-informacion-agua' hidden>
                                <div class='grafica100' id='grafica-informacion-agua'></div>
                                <div class='grafica100' id='grafica-informacion-agua-acumulado'></div>
                                <div class='texto100' id='descripcion-sensor-informacion-agua'></div>
                                <div class='texto100' id='texto-informacion-datos-informacion-agua'></div>
                                <div class='tabla-datos100' id='contenedor-tabla-comentarios-informacion-agua'></div>
                                <div class='mapa-calor100' id='mapa-calor-informacion-agua'></div>
                                <div id='parametros-resultado-informe-informacion-agua' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        // Página 'Agua (1)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-agua-1'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_AGUA);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-agua-1'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-informacion-agua'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-informacion-agua-acumulado'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='descripcion-sensor-informacion-agua'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='texto-informacion-datos-informacion-agua'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-comentarios-informacion-agua'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-informacion-agua-1'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";

                        // Página 'Gas (2)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-agua-2'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_AGUA);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-agua-2'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-informacion-agua-2'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_GENERICA:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_informe .= "
                            <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-informacion-generica'>
                                <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                            </div>
                            <div id='informe-sensores-informacion-generica' hidden>
                                <div class='grafica100' id='grafica-informacion-generica'></div>
                                <div class='grafica100' id='grafica-informacion-generica-acumulado'></div>
                                <div class='texto100' id='descripcion-sensor-informacion-generica'></div>
                                <div class='texto100' id='texto-informacion-datos-informacion-generica'></div>
                                <div class='tabla-datos100' id='contenedor-tabla-comentarios-informacion-generica'></div>
                                <div class='mapa-calor100' id='mapa-calor-informacion-generica'></div>
                                <div id='parametros-resultado-informe-informacion-generica' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        // Página 'Genérica (1)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-generica-1'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_GENERICA);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-generica-1'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-informacion-generica'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-informacion-generica-acumulado'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='descripcion-sensor-informacion-generica'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='texto-informacion-datos-informacion-generica'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-comentarios-informacion-generica'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-informacion-generica-1'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";

                        // Página 'Genérica (2)'
                        $html_informe .= "
                            <div class='pagina-informe-fichero' id='pagina-informe-fichero-informacion-generica-2'>";
                        $html_informe .= dame_html_cabecera_informe_fichero_sensores_informacion(TIPO_INFORME_SENSORES_INFORMACION_GENERICA);
                        $html_informe .= "
                                <div class='titulo-informe-fichero' id='titulo-informe-fichero-informacion-generica-2'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-informacion-generica-2'></div>
                                <div class='fin-pagina-informe-fichero'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor desconocida: '".$clase_sensor."'");
            }
        }
        return ($html_informe);
    }


    //
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_sensores_informacion(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_elemento = "";
        $prefijo_elemento = "elemento".$numero_elemento."-";
        $clase_sensor = $parametros_tipo_elemento["clase_sensor"];
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_TEMPERATURA:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-informacion-temperatura'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-temperatura'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-temperatura'></div>
                                <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-temperatura'></div>
                                <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-informacion-temperatura'></div>
                                <div id='".$prefijo_elemento."parametros-resultado-informe-informacion-temperatura' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-informacion-temperatura'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-temperatura'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-temperatura'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-temperatura'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-informacion-temperatura'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_HUMEDAD:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-informacion-humedad'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-humedad'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-humedad'></div>
                                <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-humedad'></div>
                                <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-informacion-humedad'></div>
                                <div id='".$prefijo_elemento."parametros-resultado-informe-informacion-humedad' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-informacion-humedad'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-humedad'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-humedad'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-humedad'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-informacion-humedad'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_LUZ_INTERIOR:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-luz-informacion-luz-interior'></div>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-luz-artificial-informacion-luz-interior'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-luz-interior'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-luz-interior'></div>
                                <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-luz-interior'></div>
                                <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-luz-informacion-luz-interior'></div>
                                <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-luz-artificial-informacion-luz-interior'></div>
                                <div id='".$prefijo_elemento."parametros-resultado-informe-informacion-luz-interior' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-luz-informacion-luz-interior'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-luz-artificial-informacion-luz-interior'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-luz-interior'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-luz-interior'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-luz-interior'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-luz-informacion-luz-interior'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-luz-artificial-informacion-luz-interior'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_VIENTO:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-velocidad-informacion-viento'></div>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-direccion-informacion-viento'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-viento'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-viento'></div>
                                <div class='grafico50 elemento-oculto' id='".$prefijo_elemento."grafico-frecuencia-informacion-viento'></div>
                                <div class='grafico50 elemento-oculto' id='".$prefijo_elemento."grafico-velocidad-informacion-viento'></div>
                                <div class='fin-graficos50'></div>
                                <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-viento'></div>
                                <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-velocidad-informacion-viento'></div>
                                <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-direccion-informacion-viento'></div>
                                <div id='".$prefijo_elemento."parametros-resultado-informe-informacion-viento' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-velocidad-informacion-viento'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-direccion-informacion-viento'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-viento'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-viento'></div>
                                <div class='grafico50-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafico-frecuencia-informacion-viento'></div>
                                <div class='grafico50-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafico-velocidad-informacion-viento'></div>
                                <div class='fin-graficos50-informe-fichero'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-viento'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-velocidad-informacion-viento'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-direccion-informacion-viento'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                // Sufijo de controles y títulos de tipo de energía
                $sufijo_tipo_energia = NULL;
                switch ($clase_sensor)
                {
                    case CLASE_SENSOR_ENERGIA_ACTIVA:
                    {
                        $sufijo_tipo_energia = "activa";
                        break;
                    }
                    case CLASE_SENSOR_ENERGIA_REACTIVA:
                    {
                        $sufijo_tipo_energia = "reactiva";
                        break;
                    }
                }

                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-informacion-energia-".$sufijo_tipo_energia."-acumulado'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div id='".$prefijo_elemento."parametros-resultado-informe-informacion-energia-activa' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-informacion-energia-".$sufijo_tipo_energia."-acumulado'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-energia-".$sufijo_tipo_energia."'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-informacion-energia-".$sufijo_tipo_energia."'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-informacion-cortes-tension'></div>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-informacion-cortes-tension-acumulado'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-cortes-tension'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-cortes-tension'></div>
                                <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-cortes-tension'></div>
                                <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-informacion-cortes-tension'></div>
                                <div id='".$prefijo_elemento."parametros-resultado-informe-informacion-cortes-tension' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-informacion-cortes-tension'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-informacion-cortes-tension-acumulado'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-cortes-tension'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-cortes-tension'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-cortes-tension'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-informacion-cortes-tension'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-informacion-compra-energia'></div>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-informacion-compra-energia-acumulado'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-compra-energia'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-compra-energia'></div>
                                <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-compra-energia'></div>
                                <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-informacion-compra-energia'></div>
                                <div id='".$prefijo_elemento."parametros-resultado-informe-informacion-compra-energia' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-informacion-compra-energia'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-informacion-compra-energia-acumulado'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-compra-energia'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-compra-energia'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-compra-energia'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-informacion-compra-energia'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-informacion-gas'></div>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-informacion-gas-acumulado'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-gas'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-gas'></div>
                                <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-gas'></div>
                                <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-informacion-gas'></div>
                                <div id='".$prefijo_elemento."parametros-resultado-informe-informacion-gas' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-informacion-gas'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-informacion-gas-acumulado'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-gas'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-gas'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-gas'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-informacion-gas'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-informacion-agua'></div>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-informacion-agua-acumulado'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-agua'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-agua'></div>
                                <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-agua'></div>
                                <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-informacion-agua'></div>
                                <div id='".$prefijo_elemento."parametros-resultado-informe-informacion-agua' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-informacion-agua'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-informacion-agua-acumulado'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-agua'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-agua'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-agua'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-informacion-agua'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            case CLASE_SENSOR_GENERICA:
            {
                switch ($tipo_informe)
                {
                    case TIPO_INFORME_WEB_EMIOS:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-informacion-generica'></div>
                                <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-informacion-generica-acumulado'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-generica'></div>
                                <div class='texto100 elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-generica'></div>
                                <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-generica'></div>
                                <div class='mapa-calor100 elemento-oculto' id='".$prefijo_elemento."mapa-calor-informacion-generica'></div>
                                <div id='".$prefijo_elemento."parametros-resultado-informe-informacion-generica' hidden></div>
                            </div>";
                        break;
                    }
                    case TIPO_INFORME_FICHERO:
                    {
                        $html_elemento .= "
                            <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                                <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                            </div>
                            <div id='contenido-elemento".$numero_elemento."'>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-informacion-generica'></div>
                                <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-informacion-generica-acumulado'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-informacion-generica'></div>
                                <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."texto-informacion-datos-informacion-generica'></div>
                                <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-informacion-generica'></div>
                                <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."mapa-calor-informacion-generica'></div>
                            </div>";
                        break;
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor desconocida: '".$clase_sensor."'");
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_sensores_informacion(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe,
        &$filas_valores_sensores)
    {
        // Si no hay sensor seleccionado, se devuelve sin sensor
        if ($parametros_tipo_elemento["id_sensor"] == ID_NINGUNO)
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensor_seleccionado" => true);
            return ($resultado);
        }

        $parametros_informe["id_ratio"] = $parametros_tipo_elemento["id_ratio"];
        $parametros_informe["clase_sensor"] = $parametros_tipo_elemento["clase_sensor"];
        $parametros_informe["id_sensor"] = $parametros_tipo_elemento["id_sensor"];
        $nombre_sensor = dame_nombre_sensor($parametros_tipo_elemento["id_sensor"]);
        $parametros_informe["nombre_sensor"] = $nombre_sensor;
        $parametros_informe["campo"] = $parametros_tipo_elemento["campo"];
        $parametros_informe["parametros_extra_campo"] = $parametros_tipo_elemento["parametros_extra_campo"];
        $parametros_informe["intervalo_valores"] = $parametros_tipo_elemento["intervalo_valores"];
        $parametros_informe["tipo_mapa_calor"] = $parametros_tipo_elemento["tipo_mapa_calor"];
        $parametros_informe["comentarios"] = $parametros_tipo_elemento["comentarios"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $filas_valores_sensor = dame_filas_valores_sensores_elemento_plantilla_informe($parametros_informe, $filas_valores_sensores);
        $clase_sensor = $parametros_informe["clase_sensor"];
        switch ($clase_sensor)
        {
            case CLASE_SENSOR_TEMPERATURA:
            {
                $datos_elemento = dame_informacion_sensor_temperatura($parametros_informe, $filas_valores_sensor);
                break;
            }
            case CLASE_SENSOR_HUMEDAD:
            {
                $datos_elemento = dame_informacion_sensor_humedad($parametros_informe, $filas_valores_sensor);
                break;
            }
            case CLASE_SENSOR_LUZ_INTERIOR:
            {
                $datos_elemento = dame_informacion_sensor_luz_interior($parametros_informe, $filas_valores_sensor);
                break;
            }
            case CLASE_SENSOR_VIENTO:
            {
                $datos_elemento = dame_informacion_sensor_viento($parametros_informe, $filas_valores_sensor);
                break;
            }
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                $datos_elemento = dame_informacion_sensor_energia($parametros_informe, $filas_valores_sensor);
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION:
            {
                $datos_elemento = dame_informacion_sensor_cortes_tension($parametros_informe, $filas_valores_sensor);
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                $datos_elemento = dame_informacion_sensor_compra_energia($parametros_informe, $filas_valores_sensor);
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                $datos_elemento = dame_informacion_sensor_gas($parametros_informe, $filas_valores_sensor);
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                $datos_elemento = dame_informacion_sensor_agua($parametros_informe, $filas_valores_sensor);
                break;
            }
            case CLASE_SENSOR_GENERICA:
            {
                $datos_elemento = dame_informacion_sensor_generica($parametros_informe, $filas_valores_sensor);
                break;
            }
            default:
            {
                throw new Exception("Clase de sensor desconocida: '".$clase_sensor."'");
            }
        }
        return ($datos_elemento);
    }


    //
    // Funciones auxiliares de informes
    //


    function anyade_dato_grafica_valores_tooltip_personalizado_campo_incremental(
        $datos_grafica_valores,
        $timestamp_fecha_hora_valor_utc,
        $valor,
        $campo_incremental,
        $intervalo_valores,
        $zona_horaria_local,
        $numero_decimales,
        $unidad_medida,
        $fila_valor_sensor)
    {
        if ($campo_incremental == false)
        {
            $datos_grafica_valores->anyade_tupla_pareja_datos($timestamp_fecha_hora_valor_utc, $valor);
        }
        else
        {
            $idiomas = new Idiomas();

            $cadena_fecha_hora_base_datos_utc = $fila_valor_sensor["fecha_hora"];
            $cadena_fecha_hora_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria_local);
            switch ($intervalo_valores)
            {
                case INTERVALO_VALORES_TIEMPO_REAL_LINEAS:
                case INTERVALO_VALORES_TIEMPO_REAL_PUNTOS:
                {
                    $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local"];
                    break;
                }
                case INTERVALO_VALORES_CUARTOHORA:
                case INTERVALO_VALORES_HORA:
                {
                    $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local_sin_segundos"];
                    break;
                }
                default:
                {
                    $formato_fecha_hora_local = $_SESSION["formato_fecha_local"];
                    break;
                }
            }
            $cadena_fecha_hora_local = convierte_formato_fecha($cadena_fecha_hora_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $formato_fecha_hora_local);
            $tooltip_valor_incremental = formatea_numero($valor, $numero_decimales)." ".$unidad_medida." (".$cadena_fecha_hora_local.")";
            $tooltip_valor_incremental .= "<br/>"." (".$idiomas->_("periodo de tiempo").": ".dame_texto_periodo($fila_valor_sensor["horas"] * 3600).")";

            $datos_grafica_valores->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_valor_utc,
                $valor,
                $tooltip_valor_incremental);
        }
    }
?>
