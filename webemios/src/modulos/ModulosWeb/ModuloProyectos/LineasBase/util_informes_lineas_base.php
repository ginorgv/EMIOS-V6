<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/VectorDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/Comentario.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/util_comentarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_campos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/LineaBase.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/util_modulo_sensores.php');


    //
    // Funciones de información de líneas base
    //


    // Devuelve la información de simulación de línea base
    function dame_simulacion_linea_base($parametros)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $id_linea_base = $parametros["id_linea_base"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $comentarios = $parametros["comentarios"];
        $tipo_informe = $parametros["tipo_informe"];
        $numero_elemento_plantilla_informe = $parametros["numero_elemento_plantilla_informe"];

        // Se comprueba si la línea base es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        $ids_lineas_base_usuario_actual = LineaBase::dame_ids_lineas_base_usuario_actual($ids_sensores_usuario_actual);
        if (in_array($id_linea_base, $ids_lineas_base_usuario_actual) == false)
        {
            throw new Exception("Línea base no visible por el usuario actual (id: '".$id_linea_base."')");
        }

        // Si la línea base es funcional se comprueba la función de valores
        // (Nota: Después de la simulación de la línea base, en la fila se actualiza el error estándar)
        $fila_linea_base_antes_simulacion = dame_fila_linea_base($id_linea_base);
        if ($fila_linea_base_antes_simulacion["tipo"] == TIPO_LINEA_BASE_FUNCIONAL)
        {
            $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_linea_base_antes_simulacion["parametros_tipo"]);
            $funcion_valores = $parametros_tipo[INDICE_PARAMETRO_TIPO_LINEA_BASE_FUNCIONAL_FUNCION_VALORES];

            // Se recuperan las variables de la línea base (para evaluar la función)
            $consulta_variables_linea_base = "
                SELECT nombre
                FROM variables_lineas_base
                WHERE
                    linea_base = '".$bd_red->_($id_linea_base)."'
                ORDER BY nombre ASC";
            $res_variables_linea_base = $bd_red->ejecuta_consulta($consulta_variables_linea_base);
            if ($res_variables_linea_base == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_variables_linea_base."'");
            }
            $numero_variables_linea_base = $res_variables_linea_base->dame_numero_filas();
            $nombres_variables_linea_base = array();
            while ($fila_variable_linea_base = $res_variables_linea_base->dame_siguiente_fila())
            {
                array_push($nombres_variables_linea_base, $fila_variable_linea_base["nombre"]);
            }

            // Valores de prueba de la función de valores
            if ($numero_variables_linea_base > 0)
            {
                $valores_variables_linea_base = array_fill(0, $numero_variables_linea_base, VALOR_PRUEBA_DEFECTO_FUNCION_LINEA_BASE);
            }
            else
            {
                $valores_variables_linea_base = array();
            }

            // Parámetros de la función a llamar
            $parametros_funcion_externa =
                array(
                    "llamante" => "web_emios",
                    "nombre" => NOMBRE_FUNCION_EVALUA_FUNCION_VALORES,
                    "funcion_valores" => $funcion_valores,
                    "nombres_variables" => $nombres_variables_linea_base,
                    "valores_variables" => $valores_variables_linea_base
                );

            // Llamada a función 'externa'
            $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
            $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

            // Si la función de valores es incorrecta se devuelve un error
            if ($resultado_funcion_externa["funcion_correcta"] == 0)
            {
                $error = $resultado_funcion_externa["error"];
                $descripcion_error = dame_descripcion_error_funcion_variables($error);

                $msg = $idiomas->_("Ha ocurrido un error al evaluar la función de valores")."\n(".
                    $descripcion_error.")";

                $resultado = array(
                    "res" => "ERROR",
                    "msg" => $msg);
                return ($resultado);
            }
        }

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
        $cadena_fecha_hora_fin_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Parámetros de la función a llamar
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_CALCULA_VALORES_REALES_SIMULADOS_LINEA_BASE,
                "id_linea_base" => $id_linea_base,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_funciones_utc,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_funciones_utc
            );

        // Llamada a función 'externa'
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

        // Recuperación de valores reales y simulados
        $valores_reales_simulados_linea_base = $resultado_funcion_externa["valores_reales_simulados_linea_base"];
        $numero_valores_reales_simulados_linea_base = count($valores_reales_simulados_linea_base);

        // Si no hay datos no se hace nada
        if ($numero_valores_reales_simulados_linea_base == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Desfase UTC entre la zona horaria del cliente (parámetros) y local
        $minutos_desfase_utc_zona_horaria_local = dame_minutos_desfase_utc_zona_horaria_local();
        $minutos_desfase_zonas_horarias_cliente_local = $minutos_desfase_utc - $minutos_desfase_utc_zona_horaria_local;
        $milisegundos_desfase_zonas_horarias_cliente_local = $minutos_desfase_zonas_horarias_cliente_local * 60 * 1000;

        // Filas de líneas base (la propia y las de las excepciones si las hay)
        // (Nota: Se vuelve a recuperar la fila de la línea base porque se ha actualizado el error estándar)
        $filas_lineas_base = array();
        $fila_linea_base = dame_fila_linea_base($id_linea_base);
        $filas_lineas_base[$id_linea_base] = $fila_linea_base;

        // Intervalo de valores
        $intervalo_valores = $fila_linea_base["intervalo_valores"];

        // Unidad de medida y número de decimales
        $clase_sensor = $fila_linea_base["clase_sensor"];
        $id_sensor = $fila_linea_base["sensor"];
        $campo_parametros_extra = explode(SEPARADOR_CAMPO_PARAMETROS_EXTRA, $fila_linea_base["campo_parametros_extra"]);
        $campo = $campo_parametros_extra[0];
        $unidad_medida = NodoSensor::dame_unidad_medida_sensor($clase_sensor, $id_sensor, $campo);
        $numero_decimales_valores = dame_numero_decimales_valores_campo_clase_sensor($clase_sensor, $campo);

        // Flag de campo incremental
        $tipo_valores_campo = dame_tipo_valores_campo_clase_sensor($clase_sensor, $campo);
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

        // Gráficas de línea base
        $datos_valores_reales_linea_base = new VectorDatos();
        $datos_valores_simulados_linea_base = new VectorDatos();
        $datos_banda_valores_reales_linea_base = new VectorDatos();
        $datos_banda_valores_simulados_linea_base = new VectorDatos();
        $datos_diferencias_valores_reales_simulados_linea_base = new VectorDatos();
        $datos_diferencias_acumuladas_valores_reales_simulados_linea_base = new VectorDatos();
        $suma_diferencias_valores_reales_simulados_linea_base = 0;
        $cadena_fecha_hora_inicio_valores_funciones_local = NULL;
        $cadena_fecha_hora_fin_valores_funciones_local = NULL;
        $timestamp_fecha_hora_valores_linea_base_anterior_utc = NULL;
        $numero_puntos_seguidos_grafica = 0;
        for ($i = 0; $i < $numero_valores_reales_simulados_linea_base; $i++)
        {
            // Valores
            $valor_real_simulado_linea_base = $valores_reales_simulados_linea_base[$i];
            $cadena_fecha_valor_real_simulado_linea_base_funciones_utc = $valor_real_simulado_linea_base["fecha_hora_utc"];
            $cadena_fecha_valor_real_simulado_linea_base_funciones_local = $valor_real_simulado_linea_base["fecha_hora_local"];
            $valor_real_linea_base = $valor_real_simulado_linea_base["valor_real"];
            $valor_simulado_linea_base = $valor_real_simulado_linea_base["valor_simulado"];
            $id_linea_base_valor_real_simulado = $valor_real_simulado_linea_base["id_linea_base"];
            $error_estandar_valor_simulado = $valor_real_simulado_linea_base["error_estandar_linea_base"];

            // Fila de línea base
            if (array_key_exists($id_linea_base_valor_real_simulado, $filas_lineas_base) == true)
            {
                $fila_linea_base_valor_simulado = $filas_lineas_base[$id_linea_base_valor_real_simulado];
            }
            else
            {
                $fila_linea_base_valor_simulado = dame_fila_linea_base($id_linea_base_valor_real_simulado);
                $filas_lineas_base[$id_linea_base_valor_real_simulado] = $fila_linea_base_valor_simulado;
            }

            // Máximos y mínimos
            if ($valor_real_linea_base > $max_valor)
            {
                $max_valor = $valor_real_linea_base;
            }
            if ($valor_simulado_linea_base > $max_valor)
            {
                $max_valor = $valor_simulado_linea_base;
            }
            if ($valor_real_linea_base < $min_valor)
            {
                $min_valor = $valor_real_linea_base;
            }
            if ($valor_simulado_linea_base < $min_valor)
            {
                $min_valor = $valor_simulado_linea_base;
            }

            // Fechas de inicio y fin de valores
            if ($cadena_fecha_hora_inicio_valores_funciones_local === NULL)
            {
                $cadena_fecha_hora_inicio_valores_funciones_local = $cadena_fecha_valor_real_simulado_linea_base_funciones_local;
            }
            $cadena_fecha_hora_fin_valores_funciones_local = $cadena_fecha_valor_real_simulado_linea_base_funciones_local;

            // Se añade un valor nulo para separar los valores si la diferencia de tiempo entre valores es demasiado grande
            $timestamp_fecha_hora_valores_linea_base_utc = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_valor_real_simulado_linea_base_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, ZONA_HORARIA_UTC);
            $timestamp_fecha_hora_valores_linea_base_utc -= $milisegundos_desfase_zonas_horarias_cliente_local;
            if (($numero_puntos_seguidos_grafica > 1) &&
                ($segundos_maximos_entre_valores_grafica !== NULL) && ($timestamp_fecha_hora_valores_linea_base_anterior_utc !== NULL))
            {
                $segundos_entre_valores_linea_base = ($timestamp_fecha_hora_valores_linea_base_utc - $timestamp_fecha_hora_valores_linea_base_anterior_utc) / 1000;
                if ($segundos_entre_valores_linea_base > $segundos_maximos_entre_valores_grafica)
                {
                    $numero_puntos_seguidos_grafica = 0;
                    $datos_valores_reales_linea_base->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_linea_base_anterior_utc + 1, NULL);
                    $datos_valores_simulados_linea_base->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_linea_base_anterior_utc + 1, NULL);
                    $datos_banda_valores_simulados_linea_base->anyade_tupla_pareja_datos(NULL, NULL);
                    $datos_diferencias_valores_reales_simulados_linea_base->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_linea_base_anterior_utc + 1, NULL);
                }
            }
            $timestamp_fecha_hora_valores_linea_base_anterior_utc = $timestamp_fecha_hora_valores_linea_base_utc;
            $numero_puntos_seguidos_grafica += 1;

            // Sólo se añaden las horas si el intervalo es por hora en los tooltips
            if ($intervalo_valores == INTERVALO_VALORES_HORA)
            {
                $formato_fecha_hora_local = $_SESSION["formato_fecha_hora_local_sin_segundos"];
            }
            else
            {
                $formato_fecha_hora_local = $_SESSION["formato_fecha_local"];
            }
            $cadena_fecha_valor_valores_linea_base_local_local = convierte_formato_fecha($cadena_fecha_valor_real_simulado_linea_base_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, $formato_fecha_hora_local);

            // Datos de valores reales
            $tooltip_valor_real = $idiomas->_("Valor real").": ".formatea_numero($valor_real_linea_base, $numero_decimales_valores);
            if ($unidad_medida != "")
            {
                $tooltip_valor_real .= " ".$unidad_medida;
            }
            $tooltip_valor_real .= " (".$cadena_fecha_valor_valores_linea_base_local_local.")";
            $datos_valores_reales_linea_base->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_valores_linea_base_utc,
                $valor_real_linea_base,
                $tooltip_valor_real);

            // Datos de valores simulados
            $tooltip_valor_simulado = $idiomas->_("Valor simulado").": ".formatea_numero($valor_simulado_linea_base, $numero_decimales_valores);
            if ($unidad_medida != "")
            {
                $tooltip_valor_simulado .= " ".$unidad_medida;
            }
            $tooltip_valor_simulado .= " (".$cadena_fecha_valor_valores_linea_base_local_local.")"."<br/>";
            $tooltip_valor_simulado .= $idiomas->_("Línea base").": ".$fila_linea_base_valor_simulado["nombre"]."<br/>";
            $cadena_error_estandar_valor_simulado = formatea_numero($error_estandar_valor_simulado, NUMERO_DECIMALES_ERROR_ESTANDAR_LINEA_BASE);
            $tooltip_valor_simulado .= $idiomas->_("Error estándar")." (".$idiomas->_("RMSE").")".": ".$cadena_error_estandar_valor_simulado."<br/>";
            $datos_valores_simulados_linea_base->anyade_tupla_pareja_datos_etiqueta(
                $timestamp_fecha_hora_valores_linea_base_utc,
                $valor_simulado_linea_base,
                $tooltip_valor_simulado);
            $datos_banda_valores_simulados_linea_base->anyade_tupla_pareja_datos(
                $valor_simulado_linea_base - $error_estandar_valor_simulado,
                $valor_simulado_linea_base + $error_estandar_valor_simulado);

            // Datos de diferencias
            $diferencia_valor_real_simulado_linea_base = $valor_real_linea_base - $valor_simulado_linea_base;
            if ($diferencia_valor_real_simulado_linea_base > $max_diferencia)
            {
                $max_diferencia = $diferencia_valor_real_simulado_linea_base;
            }
            if ($diferencia_valor_real_simulado_linea_base < $min_diferencia)
            {
                $min_diferencia = $diferencia_valor_real_simulado_linea_base;
            }
            $datos_diferencias_valores_reales_simulados_linea_base->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_linea_base_utc, $diferencia_valor_real_simulado_linea_base);

            // Datos de diferencias acumuladas
            if ($campo_incremental == true)
            {
                $suma_diferencias_valores_reales_simulados_linea_base += $diferencia_valor_real_simulado_linea_base;
                if ($suma_diferencias_valores_reales_simulados_linea_base > $max_diferencia_acumulada)
                {
                    $max_diferencia_acumulada = $suma_diferencias_valores_reales_simulados_linea_base;
                }
                if ($suma_diferencias_valores_reales_simulados_linea_base < $min_diferencia_acumulada)
                {
                    $min_diferencia_acumulada = $suma_diferencias_valores_reales_simulados_linea_base;
                }
                $datos_diferencias_acumuladas_valores_reales_simulados_linea_base->anyade_tupla_pareja_datos($timestamp_fecha_hora_valores_linea_base_utc, $suma_diferencias_valores_reales_simulados_linea_base);
            }
        }

        // Fechas de inicio y fin
        if ($numero_valores_reales_simulados_linea_base > 0)
        {
            $cadena_min_fecha_jqplot_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, FORMATO_FECHA_HORA_JQPLOT);
            $cadena_max_fecha_jqplot_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, FORMATO_FECHA_HORA_JQPLOT);

            // Fechas mínima y máxima
            // (si son la misma, se elimina y se añade una hora al mínimo y al máximo para que la gráfica se muestre correctamente)
            if ($cadena_min_fecha_jqplot_local == $cadena_max_fecha_jqplot_local)
            {
                $intervalo_fecha = dame_intervalo_fecha_intervalo_valores_fechas_iguales($intervalo_valores);
                $min_fecha_local = convierte_cadena_a_fecha($cadena_min_fecha_jqplot_local, FORMATO_FECHA_HORA_JQPLOT, $zona_horaria);
                $max_fecha_local = convierte_cadena_a_fecha($cadena_max_fecha_jqplot_local, FORMATO_FECHA_HORA_JQPLOT, $zona_horaria);
                $min_fecha_local->sub($intervalo_fecha);
                $max_fecha_local->add($intervalo_fecha);

                $cadena_min_fecha_jqplot_local = convierte_fecha_a_cadena($min_fecha_local, FORMATO_FECHA_HORA_JQPLOT);
                $cadena_max_fecha_jqplot_local = convierte_fecha_a_cadena($max_fecha_local, FORMATO_FECHA_HORA_JQPLOT);
            }
        }

        // Gráfica de valores
        $grafica_valores = new VectorDatos();
        $grafica_valores->anyade_dato($datos_valores_simulados_linea_base->dame_datos());
        $grafica_valores->anyade_dato($datos_valores_reales_linea_base->dame_datos());

        // Bandas de valores
        $bandas_valores = new VectorDatos();
        $bandas_valores->anyade_dato($datos_banda_valores_simulados_linea_base->dame_datos());
        $bandas_valores->anyade_dato($datos_banda_valores_reales_linea_base->dame_datos());

        // Nombres de gráfica de valores
        $nombres_grafica_valores = new VectorDatos();
        $nombres_grafica_valores->anyade_etiqueta($idiomas->_("Valores simulados"));
        $nombres_grafica_valores->anyade_etiqueta($idiomas->_("Valores reales"));

        // Gráfica de diferencias
        $grafica_diferencias = new VectorDatos();
        $grafica_diferencias->anyade_dato($datos_diferencias_valores_reales_simulados_linea_base->dame_datos());

        // Gráfica de diferencias acumuladas
        $grafica_diferencias_acumuladas = new VectorDatos();
        $grafica_diferencias_acumuladas->anyade_dato($datos_diferencias_acumuladas_valores_reales_simulados_linea_base->dame_datos());

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

        // Tabla de error y coeficientes de línea base
        $titulo_tabla_error_coeficientes_linea_base = $idiomas->_("Error y coeficientes de línea base");
        $tabla_error_coeficientes_linea_base = new TablaDatos(
            "tabla-error-coeficientes-linea-base",
            $titulo_tabla_error_coeficientes_linea_base,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla_errores_coeficientes_lineas_base
        );
        $error_estandar_linea_base = $fila_linea_base["error_estandar"];
        if ($error_estandar_linea_base == ID_NINGUNO)
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
        if (count($filas_lineas_base) == 1)
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

        // Variables de comentarios
        $filas_comentarios = array();
        $lineas_verticales_comentarios = array();
        $tabla_comentarios = NULL;

        // Fechas de inicio y fin de valores
        $cadena_fecha_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_local"]);
        $cadena_hora_inicio_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_valores_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, FORMATO_HORA);
        $cadena_fecha_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_local"]);
        $cadena_hora_fin_valores_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_valores_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, FORMATO_HORA);

        // Se recuperan los comentarios y las líneas verticales para la gráfica
        // - Nota: No se utiliza el horario semanal ni las fechas para mostrar todos los comentarios entre la fecha de inicio y fin del informe
        //   (puede ser que haya comentarios en periodos que no se visualicen en la gráfica pero que puedan ser relevantes)
        switch ($comentarios)
        {
            case COMENTARIOS_GRAFICA:
            case COMENTARIOS_GRAFICA_TABLA:
            {
                $nombre_sensor = dame_nombre_sensor($id_sensor);
                $nombres_sensores_comentarios = dame_nombres_sensores_descendientes_comentarios(array($id_sensor));
                $filas_comentarios = Comentario::dame_filas_comentarios_objetos(
                    ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE,
                    $nombres_sensores_comentarios,
                    $cadena_fecha_hora_inicio_base_datos_utc,
                    $cadena_fecha_hora_fin_base_datos_utc,
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

        // Tabla y número de comentarios
        if ($comentarios == COMENTARIOS_GRAFICA_TABLA)
        {
            if ((count($filas_comentarios) > 0) ||
                (($tipo_informe == TIPO_INFORME_WEB_EMIOS) && (NodoActuador::dame_administracion_comentarios_actuadores() == true)))
            {
                if ($numero_elemento_plantilla_informe === NULL)
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE;
                    $parametros_origen_comentarios = NULL;
                }
                else
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME;
                    $parametros_origen_comentarios = $numero_elemento_plantilla_informe;
                }
                $tabla_comentarios = Comentario::dame_tabla_comentarios_objetos_informe(
                    $origen_comentarios,
                    $parametros_origen_comentarios,
                    "tabla-comentarios-proyectos-simulador-linea-base",
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
            "min_fecha" => $cadena_min_fecha_jqplot_local,
            "max_fecha" => $cadena_max_fecha_jqplot_local,
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
            "tabla_error_coeficientes_linea_base" => $datos_tabla_error_coeficientes_linea_base,
            "tabla_errores_coeficientes_lineas_base_excepciones" => $datos_tabla_errores_coeficientes_lineas_base_excepciones,
            "lineas_verticales_comentarios" => $lineas_verticales_comentarios,
            "tabla_comentarios" => $tabla_comentarios,
            "numero_comentarios" => $numero_comentarios,
            "fecha_inicio_valores" => $cadena_fecha_inicio_valores_local_local,
            "hora_inicio_valores" => $cadena_hora_inicio_valores_local_local,
            "fecha_fin_valores" => $cadena_fecha_fin_valores_local_local,
            "hora_fin_valores" => $cadena_hora_fin_valores_local_local,
            "nombre_sensor" => $nombre_sensor,
            "descripcion_sensor" => $descripcion_sensor);
        return ($resultado);
    }


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_proyectos_simulador_linea_base()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_GRAFICA_VALORES);
        array_push($elementos_informe, ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_GRAFICA_DIFERENCIAS);
        array_push($elementos_informe, ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_GRAFICA_DIFERENCIAS_ACUMULADAS);
        array_push($elementos_informe, ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_DESCRIPCION_SENSOR);
        array_push($elementos_informe, ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TABLA_ERROR_COEFICIENTES_LINEA_BASE);
        array_push($elementos_informe, ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TABLA_ERRORES_COEFICIENTES_LINEAS_BASE_EXCEPCIONES);
        array_push($elementos_informe, ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TABLA_COMENTARIOS);
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_proyectos_simulador_linea_base($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_GRAFICA_VALORES:
            {
                $descripcion = "Gráfica de valores";
                break;
            }
            case ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_GRAFICA_DIFERENCIAS:
            {
                $descripcion = "Gráfica de diferencias";
                break;
            }
            case ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_GRAFICA_DIFERENCIAS_ACUMULADAS:
            {
                $descripcion = "Gráfica de diferencias acumuladas";
                break;
            }
            case ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_DESCRIPCION_SENSOR:
            {
                $descripcion = "Descripción de sensor";
                break;
            }
            case ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TABLA_ERROR_COEFICIENTES_LINEA_BASE:
            {
                $descripcion = "Tabla de error y coeficientes de línea base";
                break;
            }
            case ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TABLA_ERRORES_COEFICIENTES_LINEAS_BASE_EXCEPCIONES:
            {
                $descripcion = "Tabla de errores y coeficientes de líneas base de excepciones";
                break;
            }
            case ELEMENTO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TABLA_COMENTARIOS:
            {
                $descripcion = "Tabla de comentarios";
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


    function dame_html_informe_tipo_proyectos_simulador_linea_base($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-proyectos-simulador-linea-base'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-proyectos-simulador-linea-base' hidden>
                        <div class='grafica100' id='grafica-valores-simulador-linea-base'></div>
                        <div class='grafica100' id='grafica-diferencias-simulador-linea-base'></div>
                        <div class='grafica100' id='grafica-diferencias-acumuladas-simulador-linea-base'></div>
                        <div class='texto100' id='descripcion-sensor-simulador-linea-base'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-error-coeficientes-linea-base-simulador-linea-base'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-errores-coeficientes-lineas-base-excepciones-simulador-linea-base'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-comentarios-simulador-linea-base'></div>
                        <div id='parametros-resultado-informe-simulador-linea-base' hidden></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página simulación de línea base
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-simulador-linea-base'>";
                $html_informe .= dame_html_cabecera_informe_fichero_proyectos_lineas_base(TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-simulador-linea-base'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-valores-simulador-linea-base'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-diferencias-simulador-linea-base'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-diferencias-acumuladas-simulador-linea-base'></div>
                        <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='descripcion-sensor-simulador-linea-base'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-error-coeficientes-linea-base-simulador-linea-base'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-errores-coeficientes-lineas-base-excepciones-simulador-linea-base'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-comentarios-simulador-linea-base'></div>
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


    function dame_html_elemento_plantilla_informe_tipo_proyectos_simulador_linea_base(
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
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-linea-base-seleccionada-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay línea base seleccionada")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-valores-simulador-linea-base'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-simulador-linea-base'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-acumuladas-simulador-linea-base'></div>
                        <div class='texto100 elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-simulador-linea-base'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-error-coeficientes-linea-base-simulador-linea-base'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-errores-coeficientes-lineas-base-excepciones-simulador-linea-base'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-simulador-linea-base'></div>
                        <div id='".$prefijo_elemento."parametros-resultado-informe-simulador-linea-base' hidden></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-linea-base-seleccionada-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay línea base seleccionada")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-valores-simulador-linea-base'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-simulador-linea-base'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-diferencias-acumuladas-simulador-linea-base'></div>
                        <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."descripcion-sensor-simulador-linea-base'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-error-coeficientes-linea-base-simulador-linea-base'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-errores-coeficientes-lineas-base-excepciones-simulador-linea-base'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios-simulador-linea-base'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_proyectos_simulador_linea_base(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Si no hay línea base seleccionada, se devuelve sin línea base
        if ($parametros_tipo_elemento["id_linea_base"] == ID_NINGUNO)
        {
            $resultado = array(
                "res" => "OK",
                "sin_linea_base_seleccionada" => true);
            return ($resultado);
        }

        $parametros_informe["id_linea_base"] = $parametros_tipo_elemento["id_linea_base"];
        $parametros_informe["comentarios"] = $parametros_tipo_elemento["comentarios"];
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_simulacion_linea_base($parametros_informe);
        return ($datos_elemento);
    }
?>
