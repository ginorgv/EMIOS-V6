<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/HistoricoProcesado.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/ImportacionesValoresSensores/HistoricoImportacionValoresSensor.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/ImportacionesValoresSensores/ImportacionValoresSensorPendiente.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/OperacionDatosSensor.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/util_procesado.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    //
    // Funciones de listas
    //


    function dame_control_lista_tipos_ejecucion_procesado($id_controles, $opciones_extra, $etiqueta)
    {
        $control_lista_estados .= "<div id='etiqueta_tipo_ejecucion_procesado_".$id_controles."'>".$etiqueta.": "."</div>";
        $control_lista_estados .= "<select id='tipo_ejecucion_procesado_".$id_controles."' class='filtro-desplegable'>";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_EJECUCIONES_PROCESADO_TODAS)
        {
            $control_lista_estados .= "<option value=".TIPO_EJECUCION_PROCESADO_TODOS.">".dame_descripcion_tipo_ejecucion_procesado(TIPO_EJECUCION_PROCESADO_TODOS)."</option>";
        }
        $control_lista_estados .= "<option value=".TIPO_EJECUCION_PROCESADO_NORMAL." selected>".dame_descripcion_tipo_ejecucion_procesado(TIPO_EJECUCION_PROCESADO_NORMAL)."</option>";
        $control_lista_estados .= "<option value=".TIPO_EJECUCION_PROCESADO_RECALCULOS.">".dame_descripcion_tipo_ejecucion_procesado(TIPO_EJECUCION_PROCESADO_RECALCULOS)."</option>";
        $control_lista_estados .= "</select>";

        return ($control_lista_estados);
    }


    //
    // Funciones de tablas de procesado de datos de sensores
    //


    function dame_tabla_operaciones_datos_sensores_procesado($modulo)
    {
        $contenido .= "
            <div id='tablaOperacionesDatosSensores'>".
                OperacionDatosSensor::dame_tabla_operaciones_datos_sensores($modulo, false)."
            </div>";

        return ($contenido);
    }


    function dame_operaciones_datos_sensores_pendientes_procesado($modulo)
    {
        $idiomas = new Idiomas();

        $contenido = "
            <div id='tabs' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-importaciones-valores-sensores'>".$idiomas->_("Importaciones de valores de sensores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-procesados-valores-sensores-pendientes'>".$idiomas->_("Procesados de valores de sensores pendientes")."</a></li>
                </ul>
                <div id='tabs-operaciones-datos-sensores-pendientes' class='tab-content'>";

        $contenido .= "
                    <div class='tab-pane active' id='tab-importaciones-valores-sensores'>".
                        dame_importaciones_valores_sensores($modulo)."
                    </div>

                    <div class='tab-pane' id='tab-procesados-valores-sensores-pendientes'>".
                        dame_procesados_valores_sensores_pendientes($modulo)."
                    </div>";

        $contenido .= "
                </div>
            </div>";

        return ($contenido);
    }


    function dame_importaciones_valores_sensores($modulo)
    {
        $idiomas = new Idiomas();

        $contenido = "
            <div id='tabs' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-importaciones-valores-sensores-pendientes'>".$idiomas->_("Importaciones de valores de sensores pendientes")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-historico-importaciones-valores-sensores'>".$idiomas->_("Histórico")."</a></li>
                </ul>
                <div id='tabs-importaciones-valores-sensores' class='tab-content'>";

        $contenido .= "
                    <div class='tab-pane active' id='tab-importaciones-valores-sensores-pendientes'>
                        <div id='tablaImportacionesValoresSensoresPendientes'>".
                            ImportacionValoresSensorPendiente::dame_tabla_importaciones_pendientes($modulo)."
                        </div>
                    </div>

                    <div class='tab-pane' id='tab-historico-importaciones-valores-sensores'>".
                        dame_tabla_filtro_historico_importaciones_valores_sensores($modulo).
                        dame_tabla_historico_importaciones_valores_sensores($modulo)."
                    </div>";

        $contenido .= "
                </div>
            </div>";

        return ($contenido);
    }


    function dame_tabla_filtro_historico_importaciones_valores_sensores($modulo)
    {
        $idiomas = new Idiomas();

        // Se recuperan los controles a mostrar
        $filtro_historico_importaciones_valores_sensores = dame_filtro_historico_importaciones_valores_sensores($modulo);

        // Se crea la tabla contenedora
        $tabla = new TablaDatos(
            "tabla-filtro-historico-importaciones-valores-sensores",
            $idiomas->_("Filtro de histórico de importaciones de valores de sensores"),
            TIPO_TABLA_DATOS_CONTENEDOR
        );

        $params_fila = array(
            "clase_dato" => "filtro-informes",
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_HISTORICO_IMPORTACIONES_VALORES_SENSORES),
            "numero_columnas" => NUMEROS_COLUMNAS_FILTRO_HISTORICO_IMPORTACIONES_VALORES_SENSORES
        );
        $tabla->anyade_fila("filtro-historico-importaciones-valores-sensores", $filtro_historico_importaciones_valores_sensores, $params_fila);

        return ($tabla->dame_tabla());
    }


    function dame_filtro_historico_importaciones_valores_sensores($modulo)
    {
        $idiomas = new Idiomas();

        $id_controles = "filtro_historico_importaciones_valores_sensores";
        switch ($modulo)
        {
            case MODULO_MONITORIZACION:
            {
                $etiqueta_filtro = $idiomas->_("Sensor y red");
                break;
            }
            default:
            {
                $etiqueta_filtro = $idiomas->_("Sensor");
                break;
            }
        }
        $filtro = "<div id='etiqueta_filtro_".$id_controles."'>".$etiqueta_filtro.": "."</div>";
        $filtro .= "<input type='text' class='filtro-texto' id='filtro_".$id_controles."'>";
        $control_lista_clases = dame_control_lista_clases_nodo(
            $id_controles,
            TIPO_NODO_SENSOR,
            OPCIONES_EXTRA_LISTA_CLASES_TODAS,
            $idiomas->_("Clase"));
        $fecha_inicio = dame_control_fecha_inicio(
            $id_controles,
            $idiomas->_("Inicio"),
            "00:00",
            PERIODO_DEFECTO_HISTORICO_IMPORTACIONES_VALORES_SENSORES);
        $fecha_fin = dame_control_fecha_fin(
            $id_controles,
            $idiomas->_("Fin"),
            "23:59",
            "");
        $control_lista_resultados_ejecucion = dame_control_lista_resultados_ejecucion_historico_importaciones_valores_sensores($id_controles);
        $boton = dame_boton_formulario($id_controles, $idiomas->_("Filtrar"));

        $controles = array(
            $filtro,
            $control_lista_clases,
            $fecha_inicio,
            $fecha_fin,
            $control_lista_resultados_ejecucion,
            $boton
        );
        return ($controles);
    }


    function dame_control_lista_resultados_ejecucion_historico_importaciones_valores_sensores($id_controles)
    {
        $idiomas = new Idiomas();

        $control_lista_resultados_ejecucion = dame_control_lista_valores(
            $id_controles,
            "resultado_ejecucion",
            $idiomas->_("Correcta"),
            array(
                array(RESULTADO_EJECUCION_IMPORTACION_VALORES_SENSORES_TODOS, $idiomas->_("Todas")),
                array(RESULTADO_EJECUCION_IMPORTACION_VALORES_SENSORES_OK, $idiomas->_("Ok")),
                array(RESULTADO_EJECUCION_IMPORTACION_VALORES_SENSORES_OK_SIN_VALORES_ERRONEOS, $idiomas->_("Ok (sin valores erróneos)")),
                array(RESULTADO_EJECUCION_IMPORTACION_VALORES_SENSORES_OK_CON_VALORES_ERRONEOS, $idiomas->_("Ok (con valores erróneos)")),
                array(RESULTADO_EJECUCION_IMPORTACION_VALORES_SENSORES_ERROR, $idiomas->_("Error"))),
            RESULTADO_EJECUCION_IMPORTACION_VALORES_SENSORES_TODOS,
            "filtro-desplegable");
        return ($control_lista_resultados_ejecucion);
    }


    function dame_tabla_historico_importaciones_valores_sensores($modulo)
    {
        $limite_elementos_tabla_superado = false;
        $contenido = "<div id='tablaHistoricoImportacionesValoresSensores'>".
            HistoricoImportacionValoresSensor::dame_tabla_historico_importaciones_valores_sensores(
                $modulo,
                "",
                NULL,
                NULL,
                $limite_elementos_tabla_superado).
            "</div>";
        return ($contenido);
    }


    function dame_procesados_valores_sensores_pendientes($modulo)
    {
        $idiomas = new Idiomas();

        $contenido = "
            <div id='tabs' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-recalculos-valores-clase-horarios'>".$idiomas->_("Recálculos de valores de clase")." (".$idiomas->_("horarios").")"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-recalculos-valores-clase-cuartohorarios'>".$idiomas->_("Recálculos de valores de clase")." (".$idiomas->_("cuartohorarios").")"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-sensores-procesado-valores-antiguos-horarios'>".$idiomas->_("Sensores de procesado")." (".$idiomas->_("horarios").")"."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-sensores-procesado-valores-antiguos-cuartohorarios'>".$idiomas->_("Sensores de procesado")." (".$idiomas->_("cuartohorarios").")"."</a></li>
                </ul>
                <div id='tabs-procesados-valores-sensores-pendientes' class='tab-content'>";

        $contenido .= "
                    <div class='tab-pane active' id='tab-recalculos-valores-clase-horarios'>
                        <div id='tablaRecalculosValoresClaseHorarios'>".
                            dame_tabla_recalculos_valores_clase($modulo, GRANULARIDAD_HORARIA)."
                        </div>
                    </div>

                    <div class='tab-pane' id='tab-recalculos-valores-clase-cuartohorarios'>
                        <div id='tablaRecalculosValoresClaseCuartohorarios'>".
                            dame_tabla_recalculos_valores_clase($modulo, GRANULARIDAD_CUARTOHORARIA)."
                        </div>
                    </div>

                    <div class='tab-pane' id='tab-sensores-procesado-valores-antiguos-horarios'>
                        <div id='tablaSensoresProcesadoValoresAntiguosHorarios'>".
                            dame_tabla_sensores_procesado_valores_antiguos($modulo, GRANULARIDAD_HORARIA)."
                        </div>
                    </div>

                    <div class='tab-pane' id='tab-sensores-procesado-valores-antiguos-cuartohorarios'>
                        <div id='tablaSensoresProcesadoValoresAntiguosCuartohorarios'>".
                            dame_tabla_sensores_procesado_valores_antiguos($modulo, GRANULARIDAD_CUARTOHORARIA)."
                        </div>
                    </div>";

        $contenido .= "
                </div>
            </div>";

        return ($contenido);
    }


    function dame_tabla_recalculos_valores_clase($modulo, $granularidad)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Sufijo de tabla de base de datos y nombre de la tabla
        switch ($granularidad)
        {
            case GRANULARIDAD_CUARTOHORARIA:
            {
                $sufijo_tabla_granularidad = SUFIJO_TABLA_CUARTOSHORA;
                $sufijo_nombre_tabla = $idiomas->_("cuartohorarios");
                break;
            }
            case GRANULARIDAD_HORARIA:
            {
                $sufijo_tabla_granularidad = SUFIJO_TABLA_HORAS;
                $sufijo_nombre_tabla = $idiomas->_("horarios");
                break;
            }
            default:
            {
                throw new Exception("Granularidad incorrecta: '".$granularidad."'");
            }
        }

        // Se crea la tabla
        switch ($modulo)
        {
            case MODULO_MONITORIZACION:
            {
                $numero_columnas = NUMERO_COLUMNAS_TABLA_RECALCULOS_VALORES_CLASE_CON_RED;
                $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_RECALCULOS_VALORES_CLASE_CON_RED);
                $mostrar_red = true;
                break;
            }
            default:
            {
                $numero_columnas = NUMERO_COLUMNAS_TABLA_RECALCULOS_VALORES_CLASE_SIN_RED;
                $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_RECALCULOS_VALORES_CLASE_SIN_RED);
                $mostrar_red = false;
                break;
            }
        }
        $params_tabla = array(
            "numero_columnas" => $numero_columnas,
            "anchuras_columnas" => $anchuras_columnas,
            "generar_valores_xml" => true
        );
        $tabla = new TablaDatos(
            "tabla-recalculos-valores-clase-".$granularidad,
            $idiomas->_("Recálculos de valores de clase")." (".$sufijo_nombre_tabla.")",
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $cabecera = array();
        array_push($cabecera, $idiomas->_("Fecha"));
        array_push($cabecera, $idiomas->_("Sensor"));
        if ($mostrar_red == true)
        {
            array_push($cabecera, $idiomas->_("Red"));
        }
        array_push($cabecera, $idiomas->_("Clase"));
        $tabla->anyade_cabecera("", $cabecera);

        // Se recuperan los nombres de los sensores del usuario
        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        if ($mostrar_todos_sensores == false)
        {
            $ids_sensores_usuario = dame_ids_sensores_usuario_actual(true);
            $nombres_sensores_usuario = dame_nombres_sensores($ids_sensores_usuario);
        }

        // Se añade cada uno de los recálculos a la tabla y el pie de tabla
        $tabla_horas_recalculos = TABLA_HORAS_RECALCULOS_VALORES_CLASE_SENSOR.$sufijo_tabla_granularidad;
        $consulta_horas_recalculos = "
            SELECT *
            FROM ".$tabla_horas_recalculos;
        if ($mostrar_red == false)
        {
            $consulta_horas_recalculos .= "
                WHERE
                    red = '".$_SESSION["id_red"]."'";
        }
        $consulta_horas_recalculos .= "
            ORDER BY
                hora DESC,
                red DESC,
                sensor DESC";
        $res_horas_recalculos = $bd_datos->ejecuta_consulta($consulta_horas_recalculos);
        if ($res_horas_recalculos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_horas_recalculos."'");
        }

        // Filas de las horas de recálculos
        $filas_horas_recalculos = array();
        while ($fila_horas_recalculos = $res_horas_recalculos->dame_siguiente_fila())
        {
            array_push($filas_horas_recalculos, $fila_horas_recalculos);
        }

        // Se añaden los nombres de las redes a los datos de las filas de las horas de recálculos
        if ($mostrar_red == true)
        {
            $consulta_redes = "
                SELECT
                    id,
                    nombre
                FROM redes";
            $res_redes = $bd_red->ejecuta_consulta($consulta_redes);
            $nombres_redes = array();
            while ($fila_red = $res_redes->dame_siguiente_fila())
            {
                $nombres_redes[$fila_red["id"]] = $fila_red["nombre"];
            }
            for ($i = 0; $i < count($filas_horas_recalculos); $i++)
            {
                $id_red = $filas_horas_recalculos[$i]["red"];
                $filas_horas_recalculos[$i]["nombre_red"] = $nombres_redes[$id_red];
            }
        }

        // Se añaden las filas de horas de recálculos
        $zona_horaria = dame_zona_horaria_local();
        $numero_recalculos_valores_clase = 0;
        foreach ($filas_horas_recalculos as $fila_horas_recalculos)
        {
            $anyadir_hora_recalculos = true;
            if ($mostrar_todos_sensores == false)
            {
                if (in_array($fila_horas_recalculos['sensor'], $nombres_sensores_usuario) == false)
                {
                    $anyadir_hora_recalculos = false;
                }
            }

            if ($anyadir_hora_recalculos == true)
            {
                // Conversión de hora
                $cadena_hora_utc = convierte_formato_fecha($fila_horas_recalculos['hora'], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_hora_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);

                // Datos de la tabla
                $datos_tabla = array();
                array_push($datos_tabla, $cadena_hora_local);
                array_push($datos_tabla, htmlspecialchars($fila_horas_recalculos['sensor'], ENT_QUOTES));
                if ($mostrar_red == true)
                {
                    $nombre_red = $fila_horas_recalculos["nombre_red"];
                    array_push($datos_tabla, htmlspecialchars($nombre_red, ENT_QUOTES));
                }
                $descripcion_clase = NodoSensor::dame_descripcion_clase_sensor($fila_horas_recalculos['clase']);
                array_push($datos_tabla, $descripcion_clase);

                // Se añade la fila a la tabla
                $tabla->anyade_fila(
                    "datoRecalculoValoresClase__".$numero_recalculos_valores_clase,
                    $datos_tabla
                );
                $numero_recalculos_valores_clase += 1;
            }
        }
        $tabla->anyade_pie($idiomas->_("Número de recálculos de valores de clase").": ".$numero_recalculos_valores_clase);

        return ($tabla->dame_tabla());
    }


    function dame_tabla_sensores_procesado_valores_antiguos($modulo, $granularidad)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Campo de hora de últimos valores por periodos y sufijo de nombre de la tabla
        switch ($granularidad)
        {
            case GRANULARIDAD_CUARTOHORARIA:
            {
                $campo_hora_ultimos_valores_periodos = "hora_ultimos_valores_clase_cuartoshora";
                $sufijo_nombre_tabla = $idiomas->_("cuartohorarios");
                break;
            }
            case GRANULARIDAD_HORARIA:
            {
                $campo_hora_ultimos_valores_periodos = "hora_ultimos_valores_clase_horas";
                $sufijo_nombre_tabla = $idiomas->_("horarios");
                break;
            }
            default:
            {
                throw new Exception("Granularidad incorrecta: '".$granularidad."'");
            }
        }

        // Se crea la tabla
        switch ($modulo)
        {
            case MODULO_MONITORIZACION:
            {
                $numero_columnas = NUMERO_COLUMNAS_TABLA_SENSORES_PROCESADO_VALORES_ANTIGUOS_CON_RED;
                $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_SENSORES_PROCESADO_VALORES_ANTIGUOS_CON_RED);
                $mostrar_red = true;
                break;
            }
            default:
            {
                $numero_columnas = NUMERO_COLUMNAS_TABLA_SENSORES_PROCESADO_VALORES_ANTIGUOS_SIN_RED;
                $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_SENSORES_PROCESADO_VALORES_ANTIGUOS_SIN_RED);
                $mostrar_red = false;
                break;
            }
        }
        $params_tabla = array(
            "numero_columnas" => $numero_columnas,
            "anchuras_columnas" => $anchuras_columnas,
            "generar_valores_xml" => true
        );
        $tabla = new TablaDatos(
            "tabla-sensores-procesado-valores-antiguos-".$granularidad,
            $idiomas->_("Sensores de procesado")." (".$sufijo_nombre_tabla.")",
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $cabecera = array();
        array_push($cabecera, $idiomas->_("Fecha"));
        array_push($cabecera, $idiomas->_("Sensor"));
        if ($mostrar_red == true)
        {
            array_push($cabecera, $idiomas->_("Red"));
        }
        array_push($cabecera, $idiomas->_("Clase"));
        $tabla->anyade_cabecera("", $cabecera);

        // Se recuperan los nombres de los sensores del usuario
        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        if ($mostrar_todos_sensores == false)
        {
            $ids_sensores_usuario = dame_ids_sensores_usuario_actual(true);
        }

        // Se añade cada uno de los sensores de procesado a la tabla y el pie de tabla
        $consulta_sensores = "
            SELECT *
            FROM sensores
            WHERE
                (tipo = '".TIPO_SENSOR_PROCESADO."')";
        if ($mostrar_red == false)
        {
            $consulta_sensores .= "
                AND (red = '".$_SESSION["id_red"]."')";
        }
        if ($granularidad == GRANULARIDAD_CUARTOHORARIA)
        {
            $consulta_sensores .= "
                AND (granularidad_cuartohoraria = '".VALOR_SI."')";
        }
        $consulta_sensores .= "
            ORDER BY
                ".$campo_hora_ultimos_valores_periodos." DESC,
                red DESC,
                nombre DESC";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }

        // Filas de los sensores
        $filas_sensores = array();
        while ($fila_sensores = $res_sensores->dame_siguiente_fila())
        {
            array_push($filas_sensores, $fila_sensores);
        }

        // Se añaden los nombres de las redes a los datos de las filas de las horas de últimos cálculos
        if ($mostrar_red == true)
        {
            $consulta_redes = "
                SELECT
                    id,
                    nombre
                FROM redes";
            $res_redes = $bd_red->ejecuta_consulta($consulta_redes);
            $nombres_redes = array();
            while ($fila_red = $res_redes->dame_siguiente_fila())
            {
                $nombres_redes[$fila_red["id"]] = $fila_red["nombre"];
            }
            for ($i = 0; $i < count($filas_sensores); $i++)
            {
                $id_red = $filas_sensores[$i]["red"];
                $filas_sensores[$i]["nombre_red"] = $nombres_redes[$id_red];
            }
        }

        // Se añaden las filas de sensores de procesado con valores antiguos
        $zona_horaria = dame_zona_horaria_local();
        $numero_sensores_procesado = 0;
        foreach ($filas_sensores as $fila_sensor)
        {
            $anyadir_sensor = true;
            if ($mostrar_todos_sensores == false)
            {
                if (in_array($fila_sensor['id'], $ids_sensores_usuario) == false)
                {
                    $anyadir_sensor = false;
                }
            }

            if ($anyadir_sensor == true)
            {
                // Si los valores no son antiguos no se muestra el sensor
                $cadena_fecha_ultimos_valores_utc = $fila_sensor[$campo_hora_ultimos_valores_periodos];
                if ($cadena_fecha_ultimos_valores_utc === NULL)
                {
                    $cadena_hora_local = $idiomas->_("ND")." (".$idiomas->_("sin valores").")";
                }
                else
                {
                    $fecha_ultimos_valores_utc = convierte_cadena_a_fecha($cadena_fecha_ultimos_valores_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                    $fecha_hora_actual_utc = dame_fecha_hora_actual_utc();
                    $periodo_antiguedad_valores = $fecha_hora_actual_utc->diff($fecha_ultimos_valores_utc);
                    $numero_dias_antiguedad_valores = $periodo_antiguedad_valores->days;
                    if ($numero_dias_antiguedad_valores <= NUMERO_MAXIMO_DIAS_CALCULO_VALORES_SENSORES_PROCESADO_EJECUCION_SIN_RECALCULOS)
                    {
                        continue;
                    }

                    // Conversión de hora
                    $cadena_hora_utc = convierte_formato_fecha($cadena_fecha_ultimos_valores_utc, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                    $cadena_hora_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                }

                // Datos de la tabla
                $datos_tabla = array();
                array_push($datos_tabla, $cadena_hora_local);
                array_push($datos_tabla, htmlspecialchars($fila_sensor["nombre"], ENT_QUOTES));
                if ($mostrar_red == true)
                {
                    $nombre_red = $fila_sensor["nombre_red"];
                    array_push($datos_tabla, htmlspecialchars($nombre_red, ENT_QUOTES));
                }
                $descripcion_clase = NodoSensor::dame_descripcion_clase_sensor($fila_sensor["clase"]);
                array_push($datos_tabla, $descripcion_clase);

                // Se añade la fila a la tabla
                $tabla->anyade_fila(
                    "datoSensorProcesadoValoresAntiguos__".$numero_sensores_procesado,
                    $datos_tabla
                );
                $numero_sensores_procesado += 1;
            }
        }
        $tabla->anyade_pie($idiomas->_("Número de sensores de procesado").": ".$numero_sensores_procesado);

        return ($tabla->dame_tabla());
    }


    //
    // Funciones de descripciones
    //


    function dame_descripcion_tipo_tarea_procesado($tipo_tarea_procesado)
    {
        $idiomas = new Idiomas();

        switch ($tipo_tarea_procesado)
        {
            case TIPO_TAREA_AGRUPA_INCREMENTOS_VALORES_PERIODOS_SENSORES:
            {
                $descripcion_tipo_tarea_procesado = "agrupación de incrementos de valores por periodos de sensores";
                break;
            }
            case TIPO_TAREA_BORRA_VALORES_CADUCADOS_SENSORES:
            {
                $descripcion_tipo_tarea_procesado = "borrado de valores caducados de sensores";
                break;
            }
            case TIPO_TAREA_BORRA_VALORES_PENDIENTES_BORRADO_SENSORES:
            {
                $descripcion_tipo_tarea_procesado = "borrado de valores pendientes de borrado de sensores";
                break;
            }
            case TIPO_TAREA_CALCULA_VALORES_PERIODOS_SENSORES:
            {
                $descripcion_tipo_tarea_procesado = "cálculo de valores por periodos de sensores";
                break;
            }
            case TIPO_TAREA_CALCULA_VALORES_SENSORES_CLASE_SENSOR:
            {
                $descripcion_tipo_tarea_procesado = "cálculo de valores de sensores de clase de sensor";
                break;
            }
            case TIPO_TAREA_CALCULA_VALORES_SENSORES_PROCESADO:
            {
                $descripcion_tipo_tarea_procesado = "cálculo de valores de sensores de procesado";
                break;
            }
            case TIPO_TAREA_CALCULA_VALORES_SENSORES_VIRTUALES:
            {
                $descripcion_tipo_tarea_procesado = "cálculo de valores de sensores virtuales";
                break;
            }
            default:
            {
                $descripcion_tipo_tarea_procesado = "desconocido";
                break;
            }
        }
        $descripcion_tipo_tarea_procesado = $idiomas->_($descripcion_tipo_tarea_procesado);
        return ($descripcion_tipo_tarea_procesado);
    }


    function dame_descripcion_nombre_funcion_procesado($nombre_funcion_procesado)
    {
        $idiomas = new Idiomas();

        switch ($nombre_funcion_procesado)
        {
            case NOMBRE_FUNCION_IMPORTA_VALORES_SENSOR_FICHERO_CSV:
            {
                $descripcion_nombre_funcion_procesado = "importación de valores de sensor";
                break;
            }
            case NOMBRE_FUNCION_IMPORTA_INCREMENTOS_VALORES_SENSOR_FICHERO_CSV:
            {
                $descripcion_nombre_funcion_procesado = "importación de incrementos de valores de sensor";
                break;
            }
            case NOMBRE_FUNCION_BORRA_VALORES_SENSOR:
            {
                $descripcion_nombre_funcion_procesado = "borrado de valores de sensor";
                break;
            }
            case NOMBRE_FUNCION_MODIFICA_VALORES_SENSOR:
            {
                $descripcion_nombre_funcion_procesado = "modificación de valores de sensor";
                break;
            }
            case NOMBRE_FUNCION_BORRA_VALORES_SENSORES_RED:
            {
                $descripcion_nombre_funcion_procesado = "borrado de valores de sensores de una red";
                break;
            }
            default:
            {
                $descripcion_nombre_funcion_procesado = "desconocido";
                break;
            }
        }
        $descripcion_nombre_funcion_procesado = $idiomas->_($descripcion_nombre_funcion_procesado);
        return ($descripcion_nombre_funcion_procesado);
    }


    function dame_descripcion_tipo_ejecucion_procesado($tipo_ejecucion_procesado)
    {
        switch ($tipo_ejecucion_procesado)
        {
            case TIPO_EJECUCION_PROCESADO_TODOS:
            {
                $descripcion_tipo_ejecucion_procesado = "Todos";
                break;
            }
            case TIPO_EJECUCION_PROCESADO_NORMAL:
            {
                $descripcion_tipo_ejecucion_procesado = "Normal";
                break;
            }
            case TIPO_EJECUCION_PROCESADO_RECALCULOS:
            {
                $descripcion_tipo_ejecucion_procesado = "Recálculos";
                break;
            }
            default:
            {
                $descripcion_tipo_ejecucion_procesado = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_tipo_ejecucion_procesado));
    }


    function dame_descripcion_tipo_procesado($tipo_procesado)
    {
        switch ($tipo_procesado)
        {
            case TIPO_PROCESADO_CLASE_SENSOR:
            {
                $descripcion_tipo_procesado = "Clase de sensor";
                break;
            }
            case TIPO_PROCESADO_TIPO_SENSOR:
            {
                $descripcion_tipo_procesado = "Tipo de sensor";
                break;
            }
            default:
            {
                $descripcion_tipo_procesado = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_tipo_procesado));
    }


    //
    // Funciones de obtención de información de procesado
    //


    function dame_fila_historico_procesado($id_historico_procesado)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        $consulta_historico_procesado = "
            SELECT *
            FROM ejecuciones_procesado
            WHERE
                id = '".$bd_datos->_($id_historico_procesado)."'";
        $res_historico_procesado = $bd_datos->ejecuta_consulta($consulta_historico_procesado);
        if (($res_historico_procesado == false) || ($res_historico_procesado->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_historico_procesado."'");
        }
        $fila_historico_procesado = $res_historico_procesado->dame_siguiente_fila();
        return ($fila_historico_procesado);
    }


    function dame_fila_historico_importacion_valores_sensor($id_historico_importacion_valores_sensor)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        $consulta_historico_importaciones_valores_sensores = "
            SELECT *
            FROM importaciones_valores_sensores
            WHERE
                id = '".$bd_datos->_($id_historico_importacion_valores_sensor)."'";
        $res_historico_importaciones_valores_sensores = $bd_datos->ejecuta_consulta($consulta_historico_importaciones_valores_sensores);
        if (($res_historico_importaciones_valores_sensores == false) || ($res_historico_importaciones_valores_sensores->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_historico_importaciones_valores_sensores."'");
        }
        $fila_historico_importaciones_valores_sensores = $res_historico_importaciones_valores_sensores->dame_siguiente_fila();
        return ($fila_historico_importaciones_valores_sensores);
    }


    //
    // Funciones auxiliares
    //


    function dame_ids_sensores_importaciones_pendientes()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $ids_sensores_recalculos_pendientes = array();
        $consulta_importaciones_pendientes = "
            SELECT sensor
            FROM importaciones_valores_sensores_pendientes
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_importaciones_pendientes = $bd_red->ejecuta_consulta($consulta_importaciones_pendientes);
        if ($res_importaciones_pendientes == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_importaciones_pendientes."'");
        }
        while ($fila_importacion_pendiente = $res_importaciones_pendientes->dame_siguiente_fila())
        {
            array_push($ids_sensores_recalculos_pendientes, $fila_importacion_pendiente["sensor"]);
        }
        return ($ids_sensores_recalculos_pendientes);
    }


    function dame_nombres_sensores_recalculos_pendientes()
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        $nombres_sensores_recalculos_pendientes = array();
        $consulta_horas_recalculos = "
            SELECT sensor
            FROM ".TABLA_HORAS_RECALCULOS_VALORES_CLASE_SENSOR.SUFIJO_TABLA_CUARTOSHORA."
            WHERE
                red = '".$_SESSION["id_red"]."'
            UNION
            SELECT sensor
            FROM ".TABLA_HORAS_RECALCULOS_VALORES_CLASE_SENSOR.SUFIJO_TABLA_HORAS."
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_horas_recalculos = $bd_datos->ejecuta_consulta($consulta_horas_recalculos);
        if ($res_horas_recalculos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_horas_recalculos."'");
        }
        while ($fila_horas_recalculos = $res_horas_recalculos->dame_siguiente_fila())
        {
            array_push($nombres_sensores_recalculos_pendientes, $fila_horas_recalculos["sensor"]);
        }
        return ($nombres_sensores_recalculos_pendientes);
    }
?>
