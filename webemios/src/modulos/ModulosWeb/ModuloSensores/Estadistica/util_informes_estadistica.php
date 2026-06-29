<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Estadistica/InformesFichero/util_estadistica_informes_fichero.php');


    //
    // Funciones de información de estadística
    //


    // Devuelve la información de histograma de valores de un sensor
    function dame_histograma_valores_sensor($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $clase_sensor = $parametros["clase_sensor"];
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $campo = $parametros["campo"];
        $parametros_extra_campo = $parametros["parametros_extra_campo"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);
        $detalle = $parametros["detalle"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Se recupera el valor del ratio (si aplica)
        $aplicar_ratio = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor, $campo);
        if ($aplicar_ratio == true)
        {
            $info_ratio = dame_info_ratio($id_ratio);
        }

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
        $cadena_fecha_hora_fin_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);

        // Número máximo de valores del histograma
        switch ($detalle)
        {
            case DETALLE_MINIMO:
            {
                $numero_maximo_valores_histograma = NUMERO_MAXIMO_VALORES_HISTOGRAMA_DETALLE_MINIMO;
                break;
            }
            case DETALLE_MEDIO:
            {
                $numero_maximo_valores_histograma = NUMERO_MAXIMO_VALORES_HISTOGRAMA_DETALLE_MEDIO;
                break;
            }
            case DETALLE_MAXIMO:
            {
                $numero_maximo_valores_histograma = NUMERO_MAXIMO_VALORES_HISTOGRAMA_DETALLE_MAXIMO;
                break;
            }
        }

        // Horario semanal y fechas
        $cadena_horario_semanal = dame_cadena_horario_semanal($horario_semanal);
        $cadena_exclusion_fechas = dame_cadena_fechas($exclusion_fechas);
        $cadena_inclusion_fechas = dame_cadena_fechas($inclusion_fechas);

        // Parámetros de la función a llamar
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_CALCULA_HISTOGRAMA_VALOR_SENSOR,
                "id_ratio" => $id_ratio,
                "clase_sensor" => $clase_sensor,
                "id_sensor" => $id_sensor,
                "campo" => $campo,
                "parametros_extra_campo" => $parametros_extra_campo,
                "hora_inicio" => $cadena_fecha_hora_inicio_funciones_utc,
                "hora_fin" => $cadena_fecha_hora_fin_funciones_utc,
                "intervalo_valores" => $intervalo_valores,
                "horario_semanal" => $cadena_horario_semanal,
                "exclusion_fechas" => $cadena_exclusion_fechas,
                "inclusion_fechas" => $cadena_inclusion_fechas,
                "numero_maximo_valores" => $numero_maximo_valores_histograma
            );

        // Llamada a función 'externa'
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

        // Recuperación de valores del histograma
        $ocurrencias_histograma = $resultado_funcion_externa["ocurrencias"];
        $bordes_histograma = $resultado_funcion_externa["bordes"];

        // Si no hay datos no se hace nada
        $numero_ocurrencias_histograma = count($ocurrencias_histograma);
        if ($numero_ocurrencias_histograma == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Unidad de medida
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
        if ($aplicar_ratio == true)
        {
            modifica_unidad_medida_ratio($info_ratio, $unidad_medida);
        }
        if ($unidad_medida != "")
        {
            $cadena_unidad_medida = " ".$unidad_medida;
        }
        else
        {
            $cadena_unidad_medida = "";
        }

        // Recuperación de medidas estadísticas y percentiles
        $media = formatea_numero($resultado_funcion_externa["media"], 2).$cadena_unidad_medida;
        $mediana = formatea_numero($resultado_funcion_externa["mediana"], 2).$cadena_unidad_medida;
        $moda = formatea_numero($resultado_funcion_externa["moda"], 2).$cadena_unidad_medida;
        $desviacion_estandar = formatea_numero($resultado_funcion_externa["desviacion_estandar"], 2).$cadena_unidad_medida;
        $percentil_10 = formatea_numero($resultado_funcion_externa["percentil_10"], 2).$cadena_unidad_medida;
        $percentil_25 = formatea_numero($resultado_funcion_externa["percentil_25"], 2).$cadena_unidad_medida;
        $percentil_50 = formatea_numero($resultado_funcion_externa["percentil_50"], 2).$cadena_unidad_medida;
        $percentil_75 = formatea_numero($resultado_funcion_externa["percentil_75"], 2).$cadena_unidad_medida;

        // Cálculo de valores centrales y probabilidades del histograma
        $valores_histograma = array();
        for ($i = 0; $i < $numero_ocurrencias_histograma; $i++)
        {
            $valor_histograma = $bordes_histograma[$i] + (float) (($bordes_histograma[$i + 1] - $bordes_histograma[$i]) / 2);
            array_push($valores_histograma, $valor_histograma);
        }

        $suma_ocurrencias_histograma = array_sum($ocurrencias_histograma);
        $probabilidades_histograma = array();
        for ($i = 0; $i < $numero_ocurrencias_histograma; $i++)
        {
            $probabilidad_histograma = ($ocurrencias_histograma[$i] / $suma_ocurrencias_histograma) * 100.0;
            array_push($probabilidades_histograma, $probabilidad_histograma);
        }

        // Valores mínimo y máximo
        $min_valor = INF;
        $max_valor = -INF;

        // Probabilidad máxima
        $max_probabilidad = -INF;

        // Gráfica del histograma
        $datos_grafica_histograma = new VectorDatos();
        for ($i = 0; $i < count($valores_histograma); $i++)
        {
            if ($min_valor > $bordes_histograma[$i])
            {
                $min_valor = $bordes_histograma[$i];
            }
            if ($max_valor < $bordes_histograma[$i])
            {
                $max_valor = $bordes_histograma[$i];
            }
            if ($min_valor > $bordes_histograma[$i + 1])
            {
                $min_valor = $bordes_histograma[$i + 1];
            }
            if ($max_valor < $bordes_histograma[$i + 1])
            {
                $max_valor = $bordes_histograma[$i + 1];
            }

            if ($max_probabilidad < $probabilidades_histograma[$i])
            {
                $max_probabilidad = $probabilidades_histograma[$i];
            }

            $borde_inferior_valor = formatea_numero($bordes_histograma[$i], 2).$cadena_unidad_medida;
            $borde_superior_valor = formatea_numero($bordes_histograma[$i + 1], 2).$cadena_unidad_medida            ;
            $tooltip_valor_histograma = "[".$borde_inferior_valor." - ".$borde_superior_valor;
            if ($i == count($valores_histograma) - 1)
            {
                $tooltip_valor_histograma .= "]";
            }
            else
            {
                $tooltip_valor_histograma .= ")";
            }
            $tooltip_valor_histograma .= " - ".formatea_numero($probabilidades_histograma[$i], 2)." %";

            $datos_grafica_histograma->anyade_tupla_pareja_datos_etiqueta(
                $valores_histograma[$i],
                $probabilidades_histograma[$i],
                $tooltip_valor_histograma);
        }

        // Variables para dibujar las gráficas

        // Si solo hay un valor, se pasan los bordes del valor
        if ($min_valor == $max_valor)
        {
            $min_valor = $bordes_histograma[0];
            $max_valor = $bordes_histograma[1];
        }

        // Gráfica
        $grafica_histograma = new VectorDatos();
        $grafica_histograma->anyade_dato($datos_grafica_histograma->dame_datos());

        $etiquetas_grafica_histograma = new VectorDatos();
        $etiquetas_grafica_histograma->anyade_etiqueta($nombre_sensor);

        // Tabla de medidas estadísticas
        $params_tabla_medidas_estadisticas = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_MEDIDAS_ESTADISTICAS_HISTOGRAMA,
            "generar_valores_xml" => true
        );
        $titulo_tabla_medidas_estadisticas = $idiomas->_("Medidas estadísticas");
        $tabla_medidas_estadisticas = new TablaDatos(
            "tabla-medidas-estadisticas-histograma",
            $titulo_tabla_medidas_estadisticas,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_medidas_estadisticas
        );
        $cabecera_tabla_medidas_estadisticas = array(
            $idiomas->_("Nombre"),
            $idiomas->_("Valor")
        );
        $tabla_medidas_estadisticas->anyade_cabecera("", $cabecera_tabla_medidas_estadisticas);

        // Filas de la tabla de medidas estadísticas
        $params_filas_medidas_estadisticas = array("texto_eliminar_valores_xml" => $cadena_unidad_medida);
        $datos_media = array($idiomas->_("Media"), $media);
        $datos_mediana = array($idiomas->_("Mediana"), $mediana);
        $datos_moda = array($idiomas->_("Moda"), $moda);
        $datos_desviacion_estandar = array($idiomas->_("Desviación estándar"), $desviacion_estandar);
        $tabla_medidas_estadisticas->anyade_fila("fila-media", $datos_media, $params_filas_medidas_estadisticas);
        $tabla_medidas_estadisticas->anyade_fila("fila-mediana", $datos_mediana, $params_filas_medidas_estadisticas);
        $tabla_medidas_estadisticas->anyade_fila("fila-moda", $datos_moda, $params_filas_medidas_estadisticas);
        $tabla_medidas_estadisticas->anyade_fila("fila-desviacion-estandar", $datos_desviacion_estandar, $params_filas_medidas_estadisticas);

        // Tabla de percentiles
        $params_tabla_percentiles = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_PERCENTILES_HISTOGRAMA,
            "generar_valores_xml" => true
        );
        $titulo_tabla_percentiles = $idiomas->_("Percentiles");
        $tabla_percentiles = new TablaDatos(
            "tabla-percentiles-histograma",
            $titulo_tabla_percentiles,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_percentiles
        );
        $cabecera_tabla_percentiles = array(
            "10 "."%",
            "25 "."%",
            "50 "."%",
            "75 "."%");
        $tabla_percentiles->anyade_cabecera("", $cabecera_tabla_percentiles);

        // Fila de la tabla de percentiles
        $params_fila_percentiles = array("texto_eliminar_valores_xml" => $cadena_unidad_medida);
        $tabla_percentiles->anyade_fila("fila-percentiles", array(
            $percentil_10,
            $percentil_25,
            $percentil_50,
            $percentil_75),
            $params_fila_percentiles);

        // Los valores 'INF' y '-INF' no se pueden convertir a cadena, se cambian por NA (ocurre cuando no hay datos)
        if ($min_valor == INF)
        {
            $min_valor = "ND";
        }
        if ($max_valor == -INF)
        {
            $max_valor = "ND";
        }
        if ($max_probabilidad == -INF)
        {
            $max_probabilidad = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "etiquetas_grafica_histograma" => $etiquetas_grafica_histograma->dame_datos(),
            "grafica_histograma" => $grafica_histograma->dame_datos(),
            "min_valor" => $min_valor,
            "max_valor" => $max_valor,
            "max_probabilidad" => $max_probabilidad,
            "tabla_medidas_estadisticas" => $tabla_medidas_estadisticas->dame_tabla(),
            "tabla_percentiles" => $tabla_percentiles->dame_tabla(),
            "unidad_medida" => $unidad_medida);
        return ($resultado);
    }


    // Devuelve la información de correlación de valores de sensores
    function dame_correlacion_valores_sensores($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_ratio = $parametros["id_ratio"];
        $clases_sensores_independientes = $parametros["clases_sensores_independientes"];
        $ids_sensores_independientes = $parametros["ids_sensores_independientes"];
        $nombres_sensores_independientes = $parametros["nombres_sensores_independientes"];
        $campos_independientes = $parametros["campos_independientes"];
        $parametros_extra_campos_independientes = $parametros["parametros_extra_campos_independientes"];
        $clase_sensor_dependiente = $parametros["clase_sensor_dependiente"];
        $id_sensor_dependiente = $parametros["id_sensor_dependiente"];
        $nombre_sensor_dependiente = $parametros["nombre_sensor_dependiente"];
        $campo_dependiente = $parametros["campo_dependiente"];
        $parametros_extra_campo_dependiente = $parametros["parametros_extra_campo_dependiente"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $intervalo_valores = $parametros["intervalo_valores"];
        $funcion_correlacion = $parametros["funcion_correlacion"];
        $horario_semanal = json_decode($parametros["horario_semanal"], true);
        $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        $inclusion_fechas = json_decode($parametros["inclusion_fechas"], true);

        // Se comprueba si los sensores son visibles por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        foreach ($ids_sensores_independientes as $id_sensor_independiente)
        {
            if (in_array($id_sensor_independiente, $ids_sensores_usuario_actual) == false)
            {
                throw new Exception("Sensor independiente no visible por el usuario actual (id: '".$id_sensor_independiente."')");
            }
        }
        if (in_array($id_sensor_dependiente, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor dependiente no visible por el usuario actual (id: '".$id_sensor_dependiente."')");
        }

        // Si alguna de las clases de sensor independiente no tiene procesado de valores no se puede realizar el informe
        foreach ($clases_sensores_independientes as $clase_sensor_independiente)
        {
            $caracteristicas_clase_sensor_independiente = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor_independiente);
            if ($caracteristicas_clase_sensor_independiente["procesado_valores"] == false)
            {
                $res = "ERROR";
                $msg = $idiomas->_("Correlación no disponible en la clase").": ".NodoSensor::dame_descripcion_clase_sensor($clase_sensor_independiente);

                $resultado = array(
                    "res" => $res,
                    "msg" => $msg);
                return ($resultado);
            }
        }

        // Si la clase del sensor dependiente no tiene procesado de valores no se puede realizar el informe
        $caracteristicas_clase_sensor_dependiente = NodoSensor::dame_caracteristicas_clase_sensor($clase_sensor_dependiente);
        if ($caracteristicas_clase_sensor_dependiente["procesado_valores"] == false)
        {
            $res = "ERROR";
            $msg = $idiomas->_("Correlación no disponible en la clase").": ".NodoSensor::dame_descripcion_clase_sensor($clase_sensor_dependiente);

            $resultado = array(
                "res" => $res,
                "msg" => $msg);
            return ($resultado);
        }

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
        $cadena_fecha_hora_fin_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);

        // Horario semanal y fechas
        $cadena_horario_semanal = dame_cadena_horario_semanal($horario_semanal);
        $cadena_exclusion_fechas = dame_cadena_fechas($exclusion_fechas);
        $cadena_inclusion_fechas = dame_cadena_fechas($inclusion_fechas);

        // Parámetros de la función a llamar
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_CALCULA_CORRELACION_VALORES_SENSORES,
                "id_ratio" => $id_ratio,
                "clases_sensores_independientes" => $clases_sensores_independientes,
                "ids_sensores_independientes" => $ids_sensores_independientes,
                "campos_independientes" => $campos_independientes,
                "parametros_extra_campos_independientes" => $parametros_extra_campos_independientes,
                "clase_sensor_dependiente" => $clase_sensor_dependiente,
                "id_sensor_dependiente" => $id_sensor_dependiente,
                "campo_dependiente" => $campo_dependiente,
                "parametros_extra_campo_dependiente" => $parametros_extra_campo_dependiente,
                "hora_inicio" => $cadena_fecha_hora_inicio_funciones_utc,
                "hora_fin" => $cadena_fecha_hora_fin_funciones_utc,
                "intervalo_valores" => $intervalo_valores,
                "horario_semanal" => $cadena_horario_semanal,
                "exclusion_fechas" => $cadena_exclusion_fechas,
                "inclusion_fechas" => $cadena_inclusion_fechas,
                "funcion_correlacion" => $funcion_correlacion
            );

        // Llamada a función 'externa'
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

        // Si no hay datos no se hace nada
        $sin_datos = $resultado_funcion_externa["sin_datos"];
        if ($sin_datos == true)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se recupera si se ha podido realizar la correlación
        $numero_valores_insuficiente = $resultado_funcion_externa["numero_valores_insuficiente"];
        if ($numero_valores_insuficiente == true)
        {
            $mensaje_error = ($idiomas->_("Número de valores insuficiente"));
            $resultado = array(
                "res" => "ERROR",
                "msg" => $mensaje_error);
            return ($resultado);
        }

        // Recuperación del resultado de la correlación
        $funcion_correlacion_resultado = $resultado_funcion_externa["funcion_correlacion"];
        $coeficientes_funcion_correlacion = $resultado_funcion_externa["coeficientes_funcion_correlacion"];
        $error_estandar = $resultado_funcion_externa["error_estandar"];
        $coeficiente_variacion = $resultado_funcion_externa["coeficiente_variacion"];
        $coeficiente_correlacion = $resultado_funcion_externa["coeficiente_correlacion"];

        // Tabla de función de correlación
        $numero_columnas_tabla_funcion_correlacion = NUMERO_COLUMNAS_TABLA_FUNCION_CORRELACION;
        if ($coeficiente_correlacion !== NULL)
        {
            $numero_columnas_tabla_funcion_correlacion += 1;
        }
        $params_tabla_funcion_correlacion = array(
            "numero_columnas" => $numero_columnas_tabla_funcion_correlacion,
            "generar_valores_xml" => true
        );
        $titulo_tabla_funcion_correlacion = $idiomas->_("Función de correlación");
        $tabla_funcion_correlacion = new TablaDatos(
            "tabla-funcion-correlacion",
            $titulo_tabla_funcion_correlacion,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_funcion_correlacion
        );
        $cabecera_funcion_correlacion = array(
            $idiomas->_("Función"),
            $idiomas->_("Error estándar")." (".$idiomas->_("RMSE").")",
            $idiomas->_("Coeficiente de variación"));
        if ($coeficiente_correlacion !== NULL)
        {
            array_push($cabecera_funcion_correlacion, $idiomas->_("Coeficiente de correlación")." (".$idiomas->_("R2").")");
        }
        $tabla_funcion_correlacion->anyade_cabecera("", $cabecera_funcion_correlacion);

        // Fila de la tabla de función de correlación
        $cadena_funcion_correlacion = dame_cadena_funcion_correlacion($funcion_correlacion_resultado, $coeficientes_funcion_correlacion);
        if ($error_estandar !== NULL)
        {
            $texto_error_estandar = formatea_numero($error_estandar, NUMERO_DECIMALES_ERROR_ESTANDAR_CORRELACION);
        }
        else
        {
            $texto_error_estandar = $idiomas->_("ND");
        }
        if ($coeficiente_variacion !== NULL)
        {
            $texto_coeficiente_variacion = formatea_numero($coeficiente_variacion, NUMERO_DECIMALES_COEFICIENTE_VARIACION_CORRELACION);
        }
        else
        {
            $texto_coeficiente_variacion = $idiomas->_("ND");
        }
        $valores_fila_tabla_funcion_correlacion = array(
            $cadena_funcion_correlacion,
            $texto_error_estandar,
            $texto_coeficiente_variacion);
        if ($coeficiente_correlacion !== NULL)
        {
            $texto_coeficiente_correlacion = formatea_numero($coeficiente_correlacion, NUMERO_DECIMALES_COEFICIENTE_VARIACION_CORRELACION);
            array_push($valores_fila_tabla_funcion_correlacion, $texto_coeficiente_correlacion);
        }
        $tabla_funcion_correlacion->anyade_fila("fila-funcion-correlación", $valores_fila_tabla_funcion_correlacion);

        // Si sólo hay 1 sensor independiente se dibuja la gráfica
        if (count($ids_sensores_independientes) == 1)
        {
            // Ecuación de la función de correlación
            $ecuacion = dame_ecuacion_funcion_correlacion($funcion_correlacion_resultado, $coeficientes_funcion_correlacion);

            // Recuperación de datos para dibujar la gráfica
            $valores_sensor_independiente = $resultado_funcion_externa["valores_sensor_independiente"];
            $valores_sensor_dependiente = $resultado_funcion_externa["valores_sensor_dependiente"];
            $cadenas_fechas_valores_sensores_funciones_utc = $resultado_funcion_externa["fechas_valores_sensores"];

            // Sensor independiente
            $clase_sensor_independiente = $clases_sensores_independientes[0];
            $id_sensor_independiente = $ids_sensores_independientes[0];
            $nombre_sensor_independiente = $nombres_sensores_independientes[0];
            $campo_independiente = $campos_independientes[0];

            // Valores mínimos y máximos
            $min_valor_independiente = INF;
            $max_valor_independiente = -INF;
            $min_valor_dependiente = INF;
            $max_valor_dependiente = -INF;

            // Formato de fecha y hora local
            switch ($intervalo_valores)
            {
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

            // Número de valores utilizados para realizar la correlación
            $numero_valores_correlacion = count($cadenas_fechas_valores_sensores_funciones_utc);

            // Gráfica de correlación de los sensores (puntos)
            $datos_valores_sensores = new VectorDatos();
            for ($i = 0; $i < $numero_valores_correlacion; $i++)
            {
                if ($min_valor_independiente > $valores_sensor_independiente[$i])
                {
                    $min_valor_independiente = $valores_sensor_independiente[$i];
                }
                if ($max_valor_independiente < $valores_sensor_independiente[$i])
                {
                    $max_valor_independiente = $valores_sensor_independiente[$i];
                }

                if ($min_valor_dependiente > $valores_sensor_dependiente[$i])
                {
                    $min_valor_dependiente = $valores_sensor_dependiente[$i];
                }
                if ($max_valor_dependiente < $valores_sensor_dependiente[$i])
                {
                    $max_valor_dependiente = $valores_sensor_dependiente[$i];
                }

                $cadena_fecha_valores_sensores_funciones_local = cambia_zona_horaria_cadena_fecha_hora($cadenas_fechas_valores_sensores_funciones_utc[$i], FORMATO_FECHA_HORA_FUNCIONES, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_fecha_valores_sensores_local_local_local = convierte_formato_fecha($cadena_fecha_valores_sensores_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, $formato_fecha_hora_local);

                // Se añaden los valores
                $datos_valores_sensores->anyade_tupla_pareja_datos_etiqueta(
                    $valores_sensor_independiente[$i],
                    $valores_sensor_dependiente[$i],
                    $cadena_fecha_valores_sensores_local_local_local);
            }
            if ($numero_valores_correlacion == 1)
            {
                $min_valor_independiente -= 1;
                $max_valor_independiente += 1;
            }

            $datos_valores_curva_correlacion = new VectorDatos();
            if ($numero_valores_correlacion > 0)
            {
                $incremento_valor_independiente_curva_correlacion = ($max_valor_independiente - $min_valor_independiente) / (NUMERO_VALORES_CURVA_CORRELACION - 1);
                $valor_sensor_independiente_curva_correlacion = $min_valor_independiente;
                for ($i = 0; $i < NUMERO_VALORES_CURVA_CORRELACION; $i++)
                {
                    if ($funcion_correlacion_resultado == FUNCION_CORRELACION_LOGARITMICA)
                    {
                        if ($valor_sensor_independiente_curva_correlacion == 0.0)
                        {
                            $valor_sensor_independiente_curva_correlacion = VALOR_CERO_MINIMO;
                        }
                    }

                    $valor_sensor_dependiente_curva_correlacion = calcula_resultado_ecuacion($ecuacion, array($valor_sensor_independiente_curva_correlacion));
                    if ($min_valor_dependiente > $valor_sensor_dependiente_curva_correlacion)
                    {
                        $min_valor_dependiente = $valor_sensor_dependiente_curva_correlacion;
                    }
                    if ($max_valor_dependiente < $valor_sensor_dependiente_curva_correlacion)
                    {
                        $max_valor_dependiente = $valor_sensor_dependiente_curva_correlacion;
                    }

                    $datos_valores_curva_correlacion->anyade_tupla_pareja_datos(
                        $valor_sensor_independiente_curva_correlacion,
                        $valor_sensor_dependiente_curva_correlacion);

                    $valor_sensor_independiente_curva_correlacion += $incremento_valor_independiente_curva_correlacion;
                }
            }

            // Unidades de medida
            $unidad_medida_independiente = NodoSensor::dame_unidad_medida_sensor($clase_sensor_independiente, $id_sensor_independiente, $campo_independiente);
            $aplicar_ratio_independiente = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor_independiente, $campo_independiente);
            if ($aplicar_ratio_independiente == true)
            {
                $info_ratio_independiente = dame_info_ratio($id_ratio);
                modifica_unidad_medida_ratio($info_ratio_independiente, $unidad_medida_independiente);
            }
            $unidad_medida_dependiente = NodoSensor::dame_unidad_medida_sensor($clase_sensor_dependiente, $id_sensor_dependiente, $campo_dependiente);
            $aplicar_ratio_dependiente = dame_aplicar_ratio_campo_clase_sensor($id_ratio, $clase_sensor_dependiente, $campo_dependiente);
            if ($aplicar_ratio_dependiente == true)
            {
                $info_ratio_dependiente = dame_info_ratio($id_ratio);
                modifica_unidad_medida_ratio($info_ratio_dependiente, $unidad_medida_dependiente);
            }

            // Gráficas
            $grafica_correlacion = new VectorDatos();
            $grafica_correlacion->anyade_dato($datos_valores_sensores->dame_datos());
            $grafica_correlacion->anyade_dato($datos_valores_curva_correlacion->dame_datos());

            $nombres_grafica_correlacion = new VectorDatos();
            $nombres_grafica_correlacion->anyade_etiqueta($idiomas->_("Valores de sensores")." (".$nombre_sensor_independiente." - ".$nombre_sensor_dependiente.")");
            $nombres_grafica_correlacion->anyade_etiqueta($idiomas->_("Curva de correlación")." (".$nombre_sensor_independiente." - ".$nombre_sensor_dependiente.")");

            $datos_nombres_grafica_correlacion = $nombres_grafica_correlacion->dame_datos();
            $datos_grafica_correlacion = $grafica_correlacion->dame_datos();
        }
        else
        {
            $datos_nombres_grafica_correlacion = NULL;
            $datos_grafica_correlacion = NULL;
            $min_valor_independiente = NULL;
            $max_valor_independiente = NULL;
            $min_valor_dependiente = NULL;
            $max_valor_dependiente = NULL;
            $unidad_medida_independiente = NULL;
            $unidad_medida_dependiente = NULL;
        }

        // Si el valor mínimo independiente es igual al máximo se suma y se resta 1
        if ($min_valor_independiente == $max_valor_independiente)
        {
            $min_valor_independiente -= 1;
            $max_valor_independiente += 1;
        }

        // Nota: Los valores -INF y INF no se convierten correctamente a cadena
        if ($min_valor_independiente == INF)
        {
            $min_valor_independiente = "ND";
        }
        if ($max_valor_independiente == -INF)
        {
            $max_valor_independiente = "ND";
        }
        if ($min_valor_dependiente == INF)
        {
            $min_valor_dependiente = "ND";
        }
        if ($max_valor_dependiente == -INF)
        {
            $max_valor_dependiente = "ND";
        }

        // Resultado
        $resultado = array(
            "res" => "OK",
            "hay_datos" => true,
            "tabla_funcion_correlacion" => $tabla_funcion_correlacion->dame_tabla(),
            "etiquetas_grafica_correlacion" => $datos_nombres_grafica_correlacion,
            "grafica_correlacion" => $datos_grafica_correlacion,
            "min_valor_independiente" => $min_valor_independiente,
            "max_valor_independiente" => $max_valor_independiente,
            "min_valor_dependiente" => $min_valor_dependiente,
            "max_valor_dependiente" => $max_valor_dependiente,
            "unidad_medida_independiente" => $unidad_medida_independiente,
            "unidad_medida_dependiente" => $unidad_medida_dependiente,
            "cadena_funcion_correlacion" => $cadena_funcion_correlacion,
            "error_estandar" => $error_estandar,
            "coeficiente_variacion" => $coeficiente_variacion,
            "coeficiente_correlacion" => $coeficiente_correlacion);
        return ($resultado);
    }


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_sensores_histograma()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_HISTOGRAMA_GRAFICA_HISTOGRAMA);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_HISTOGRAMA_TABLA_MEDIDAS_ESTADISTICAS);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_HISTOGRAMA_TABLA_PERCENTILES);
        return ($elementos_informe);
    }


    function dame_elementos_informe_sensores_correlacion()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_CORRELACION_GRAFICA_CORRELACION);
        array_push($elementos_informe, ELEMENTO_INFORME_SENSORES_CORRELACION_TABLA_FUNCION_CORRELACION);
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_sensores_histograma($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SENSORES_HISTOGRAMA_GRAFICA_HISTOGRAMA:
            {
                $descripcion = "Gráfica de histograma";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_HISTOGRAMA_TABLA_MEDIDAS_ESTADISTICAS:
            {
                $descripcion = "Tabla de medidas estadísticas";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_HISTOGRAMA_TABLA_PERCENTILES:
            {
                $descripcion = "Tabla de percentiles";
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


    function dame_descripcion_elemento_informe_sensores_correlacion($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SENSORES_CORRELACION_GRAFICA_CORRELACION:
            {
                $descripcion = "Gráfica de correlación";
                break;
            }
            case ELEMENTO_INFORME_SENSORES_CORRELACION_TABLA_FUNCION_CORRELACION:
            {
                $descripcion = "Tabla de función de correlación";
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


    function dame_html_informe_tipo_sensores_histograma($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-histograma'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-sensores-histograma' hidden>
                        <div class='grafica90' id='grafica-histograma'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-medidas-estadisticas-histograma'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-percentiles-histograma'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de histograma
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-estadistica-informe-fichero-histograma'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_estadistica(TIPO_INFORME_SENSORES_HISTOGRAMA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-histograma'></div>
                        <div class='grafica90-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-histograma'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-medidas-estadisticas-histograma'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-percentiles-histograma'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    function dame_html_informe_tipo_sensores_correlacion($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-sensores-correlacion'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-sensores-correlacion' hidden>
                        <div class='grafica100' id='grafica-correlacion'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-funcion-correlacion'></div>
                        <div id='parametros_resultado_correlacion' hidden></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de correlación
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-estadistica-informe-fichero-correlacion'>";
                $html_informe .= dame_html_cabecera_informe_fichero_sensores_estadistica(TIPO_INFORME_SENSORES_CORRELACION);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-correlacion'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-correlacion'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-funcion-correlacion'></div>
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


    function dame_html_elemento_plantilla_informe_tipo_sensores_histograma(
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
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica90 elemento-oculto' id='".$prefijo_elemento."grafica-histograma'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-medidas-estadisticas-histograma'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-percentiles-histograma'></div>
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
                        <div class='grafica90-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-histograma'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-medidas-estadisticas-histograma'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-percentiles-histograma'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_html_elemento_plantilla_informe_tipo_sensores_correlacion(
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
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensores-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensores seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-correlacion'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-funcion-correlacion'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensores-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensores seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-correlacion'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-funcion-correlacion'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_sensores_histograma(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
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
        $parametros_informe["detalle"] = $parametros_tipo_elemento["detalle"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_histograma_valores_sensor($parametros_informe);
        return ($datos_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_sensores_correlacion(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Si no hay sensores seleccionados, se devuelve sin sensores
        $hay_sensores_independientes_seleccionados = false;
        if (count($parametros_tipo_elemento["ids_sensores_independientes"]) > 0)
        {
            // Nota: En principio no debería haber ids de sensores a 'NINGUNO' porque no se añaden a la lista de ids en ese caso
            // (sensores eliminados o parámetros sin seleccionar)
            foreach ($parametros_tipo_elemento["ids_sensores_independientes"] as $id_sensor)
            {
                if ($id_sensor != ID_NINGUNO)
                {
                    $hay_sensores_independientes_seleccionados = true;
                    break;
                }
            }
        }
        $hay_sensor_dependiente_seleccionado = false;
        if ($parametros_tipo_elemento["id_sensor_dependiente"] != ID_NINGUNO)
        {
            $hay_sensor_dependiente_seleccionado = true;
        }
        if (($hay_sensores_independientes_seleccionados == false) || ($hay_sensor_dependiente_seleccionado == false))
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensores_seleccionados" => true);
            return ($resultado);
        }

        $parametros_informe["id_ratio"] = $parametros_tipo_elemento["id_ratio"];
        $parametros_informe["clases_sensores_independientes"] = $parametros_tipo_elemento["clases_sensores_independientes"];
        $parametros_informe["ids_sensores_independientes"] = $parametros_tipo_elemento["ids_sensores_independientes"];
        $nombres_sensores_independientes = dame_nombres_sensores($parametros_tipo_elemento["ids_sensores_independientes"]);
        $parametros_informe["nombres_sensores_independientes"] = $nombres_sensores_independientes;
        $parametros_informe["campos_independientes"] = $parametros_tipo_elemento["campos_independientes"];
        $parametros_informe["parametros_extra_campos_independientes"] = $parametros_tipo_elemento["parametros_extra_campos_independientes"];
        $parametros_informe["clase_sensor_dependiente"] = $parametros_tipo_elemento["clase_sensor_dependiente"];
        $parametros_informe["id_sensor_dependiente"] = $parametros_tipo_elemento["id_sensor_dependiente"];
        $nombre_sensor_dependiente = dame_nombre_sensor($parametros_tipo_elemento["id_sensor_dependiente"]);
        $parametros_informe["nombre_sensor_dependiente"] = $nombre_sensor_dependiente;
        $parametros_informe["campo_dependiente"] = $parametros_tipo_elemento["campo_dependiente"];
        $parametros_informe["parametros_extra_campo_dependiente"] = $parametros_tipo_elemento["parametros_extra_campo_dependiente"];
        $parametros_informe["intervalo_valores"] = $parametros_tipo_elemento["intervalo_valores"];
        $parametros_informe["horario_semanal"] = json_encode($parametros_tipo_elemento["horario_semanal"]);
        $parametros_informe["exclusion_fechas"] = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        $parametros_informe["inclusion_fechas"] = json_encode($parametros_tipo_elemento["inclusion_fechas"]);
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        // Función de correlación
        $funcion_correlacion = $parametros_tipo_elemento["funcion_correlacion"];
        switch ($funcion_correlacion)
        {
            case FUNCION_CORRELACION_AUTOMATICA:
            {
                $funcion_correlacion = "";
                break;
            }
            case FUNCION_CORRELACION_LINEAL:
            {
                $numero_sensores_independientes = 0;
                $ids_sensores_independientes = $parametros_tipo_elemento["ids_sensores_independientes"] ;
                for ($i = 0; $i < count($ids_sensores_independientes); $i++)
                {
                    $id_sensor_independiente = $ids_sensores_independientes[$i];
                    if ($id_sensor_independiente != ID_NINGUNO)
                    {
                        $numero_sensores_independientes += 1;
                    }
                }
                if ($numero_sensores_independientes > 1)
                {
                    $funcion_correlacion = FUNCION_CORRELACION_MULTIVARIABLE_LINEAL;
                }
                break;
            }
        }
        $parametros_informe["funcion_correlacion"] = $funcion_correlacion;

        $datos_elemento = dame_correlacion_valores_sensores($parametros_informe);
        return ($datos_elemento);
    }
?>
