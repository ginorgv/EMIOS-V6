<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_ficheros.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tablas_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/util_comentarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');


    // Clase comentario
	class Comentario
	{
        // Funciones estáticas de acción


		// Devuelve la cabecera de la tabla de comentarios
        static function dame_cabecera_tabla()
		{
            $idiomas = new Idiomas();

            return (array(
                $idiomas->_("Fecha"),
                $idiomas->_("Usuario"),
                $idiomas->_("Tipo"),
                $idiomas->_("Objeto"),
                $idiomas->_("Descripción")
			));
        }


        // Devuelve la consulta de comentarios
        static function dame_consulta_comentarios($cadena_fecha_hora_inicio_base_datos_utc, $cadena_fecha_hora_fin_base_datos_utc)
        {
            $bd_datos = BaseDatosDatos::dame_base_datos();

            $consulta = "
                SELECT *
                FROM comentarios
                WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                    AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')
                ORDER BY hora DESC, id DESC";
			return ($consulta);
        }


        // Devuelve la tabla de comentarios
        static function dame_tabla_comentarios(
            $modulo,
            $filtro,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            &$limite_elementos_tabla_historico_superado)
		{
            $idiomas = new Idiomas();
            $bd_datos = BaseDatosDatos::dame_base_datos();

            $boton_ayuda_tabla_comentarios = "<i id='ayuda_comentarios' class='icon-question-sign color-blanco boton_ayuda_tabla_comentarios boton-tabla-datos'></i>";
            $opciones = array($boton_ayuda_tabla_comentarios);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_COMENTARIOS_CON_USUARIO,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_COMENTARIOS_CON_USUARIO),
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-red-comentarios",
                $idiomas->_("Comentarios"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = Comentario::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Tabla de origen de comentarios (por ahora sólo en el módulo de red)
            switch ($modulo)
            {
                case MODULO_RED:
                {
                    $origen_comentarios = ORIGEN_COMENTARIOS_TABLA_COMENTARIOS_RED;
                    break;
                }
                default:
                {
                    throw new Exception("Módulo incorrecto: '".$modulo."'");
                }
            }

            // Se añade cada una de los comentarios a la tabla y el pie de tabla
            // (si no hay fechas, se devuelve la tabla vacía)
            $numero_comentarios = 0;
            if (($cadena_fecha_hora_inicio_base_datos_utc !== NULL) && ($cadena_fecha_hora_fin_base_datos_utc !== NULL))
            {
                $consulta_comentarios = Comentario::dame_consulta_comentarios($cadena_fecha_hora_inicio_base_datos_utc, $cadena_fecha_hora_fin_base_datos_utc);
                $res_comentarios = $bd_datos->ejecuta_consulta($consulta_comentarios);
                if ($res_comentarios == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_comentarios."'");
                }

                // Se recorren las filas de los comentarios
                $limite_elementos_tabla_historico_superado = false;
                while (($fila_comentario = $res_comentarios->dame_siguiente_fila()) && ($limite_elementos_tabla_historico_superado == false))
                {
                    // Se realiza el filtrado (no se hace en la consulta porque se filtra por la descripción del tipo, no por el tipo en la base de datos)
                    $id_usuario = $fila_comentario['usuario'];
                    $descripcion_tipo_comentario = Comentario::dame_descripcion_tipo_comentario($fila_comentario['tipo'], true);
                    $objeto = $fila_comentario['objeto'];
                    if (($filtro != "") &&
                        (stripos($id_usuario, $filtro) === false) &&
                        (stripos($descripcion_tipo_comentario, $filtro) === false) &&
                        (stripos($objeto, $filtro) === false))
                    {
                        continue;
                    }

                    if ($numero_comentarios == NUMERO_MAXIMO_ELEMENTOS_TABLAS_HISTORICOS)
                    {
                        $limite_elementos_tabla_historico_superado = true;
                        break;
                    }
                    else
                    {
                        $comentario = new Comentario($fila_comentario);
                        $opciones = $comentario->dame_opciones_tabla($origen_comentarios, NULL);
                        $params_fila = array(
                            "opciones" => $opciones
                        );
                        $tabla->anyade_fila(
                            "datosComentario__".$fila_comentario['id'],
                            $comentario->dame_datos_tabla(true, true),
                            $params_fila
                        );
                        $numero_comentarios += 1;
                    }
                }
            }
            $texto_pie = $idiomas->_("Número de comentarios").": ".$numero_comentarios;
            if ($limite_elementos_tabla_historico_superado == true)
            {
                $texto_pie .= " (".$idiomas->_("límite máximo superado").")";
            }
            $tabla->anyade_pie($texto_pie);

            return ($tabla->dame_tabla());
		}


        // Devuelve las filas de comentarios de los objetos comprendidos entre la fecha de inicio y la fecha de fin
        static function dame_filas_comentarios_objetos(
            $origen_comentarios,
            $nombres_objetos,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas)
        {
            $bd_datos = BaseDatosDatos::dame_base_datos();

            // Devuelve los tipos de comentario visibles para un usuario
            $tipos_comentario = Comentario::dame_tipos_comentario_visibles_usuario($origen_comentarios);

            // Consulta de los comentarios
            $cadena_tipos_comentario_consulta = dame_cadena_nombres_consulta($tipos_comentario, $bd_datos);
            $cadena_objetos_consulta = dame_cadena_nombres_consulta($nombres_objetos, $bd_datos);
            $consulta_comentarios = "
                SELECT *
                FROM comentarios
                WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (tipo IN (".$cadena_tipos_comentario_consulta."))
                    AND (objeto IN (".$cadena_objetos_consulta."))
                    AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                    AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";

            // Se añaden el horario semanal y la exclusión e inclusión de fechas
            $consulta_comentarios .= dame_filtro_consulta_horario_semanal_fechas(
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas);

            // Se añade el orden y se ejecuta la consulta
            $consulta_comentarios .= "
                ORDER BY hora ASC";
            $res_comentarios = $bd_datos->ejecuta_consulta($consulta_comentarios);
            if ($res_comentarios == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_comentarios."'");
            }

            $filas_comentarios = array();
            while ($fila_comentario = $res_comentarios->dame_siguiente_fila())
            {
                array_push($filas_comentarios, $fila_comentario);
            }
            return ($filas_comentarios);
        }


        // Devuelve las filas de comentarios de los nodos comprendidos entre la fecha de inicio y la fecha de fin
        static function dame_filas_comentarios_nodos(
            $visibilidad_comentarios,
            $nombres_sensores,
            $nombres_actuadores,
            $nombres_grupos_actuadores,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas)
        {
            $bd_datos = BaseDatosDatos::dame_base_datos();

            // Consulta de los comentarios
            $consulta_comentarios = "
                SELECT *
                FROM comentarios
                WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                    AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";
            if ($visibilidad_comentarios != VISIBILIDAD_TODAS)
            {
                $consulta_comentarios .= "
                    AND (visibilidad = '".$bd_datos->_($visibilidad_comentarios)."')";
            }
            $consulta_comentarios .= "
                    AND (";
            $anyadir_operador_or = false;
            if (count($nombres_sensores) > 0)
            {
                $tipos_comentario_sensores = Comentario::dame_tipos_comentario_tipo_nodo(TIPO_NODO_SENSOR);
                $cadena_tipos_comentario_sensores_consulta = dame_cadena_nombres_consulta($tipos_comentario_sensores, $bd_datos);
                $cadena_nombres_sensores_consulta = dame_cadena_nombres_consulta($nombres_sensores, $bd_datos);
                if ($anyadir_operador_or == true)
                {
                    $consulta_comentarios .= " OR ";
                }
                $consulta_comentarios .= "((tipo IN (".$cadena_tipos_comentario_sensores_consulta.")) AND (objeto IN (".$cadena_nombres_sensores_consulta.")))";
                $anyadir_operador_or = true;
            }
            if (count($nombres_actuadores) > 0)
            {
                $tipos_comentario_actuadores = Comentario::dame_tipos_comentario_tipo_nodo(TIPO_NODO_ACTUADOR);
                $cadena_tipos_comentario_actuadores_consulta = dame_cadena_nombres_consulta($tipos_comentario_actuadores, $bd_datos);
                $cadena_nombres_actuadores_consulta = dame_cadena_nombres_consulta($nombres_actuadores, $bd_datos);
                if ($anyadir_operador_or == true)
                {
                    $consulta_comentarios .= " OR ";
                }
                $consulta_comentarios .= "((tipo IN (".$cadena_tipos_comentario_actuadores_consulta.")) AND (objeto IN (".$cadena_nombres_actuadores_consulta.")))";
                $anyadir_operador_or = true;
            }
            if (count($nombres_grupos_actuadores) > 0)
            {
                $tipos_comentario_grupos_actuadores = Comentario::dame_tipos_comentario_tipo_nodo(TIPO_NODO_GRUPO_ACTUADORES);
                $cadena_tipos_comentario_grupos_actuadores_consulta = dame_cadena_nombres_consulta($tipos_comentario_grupos_actuadores, $bd_datos);
                $cadena_nombres_grupos_actuadores_consulta = dame_cadena_nombres_consulta($nombres_grupos_actuadores, $bd_datos);
                if ($anyadir_operador_or == true)
                {
                    $consulta_comentarios .= " OR ";
                }
                $consulta_comentarios .= "((tipo IN (".$cadena_tipos_comentario_grupos_actuadores_consulta.")) AND (objeto IN (".$cadena_nombres_grupos_actuadores_consulta.")))";
                $anyadir_operador_or = true;
            }
            $consulta_comentarios .= ")";

            // Se añaden el horario semanal y la exclusión e inclusión de fechas
            $consulta_comentarios .= dame_filtro_consulta_horario_semanal_fechas(
                $horario_semanal,
                $exclusion_fechas,
                $inclusion_fechas);

            // Se añade el orden y se ejecuta la consulta
            $consulta_comentarios .= "
                ORDER BY
                    hora ASC,
                    objeto ASC,
                    tipo ASC";
            $res_comentarios = $bd_datos->ejecuta_consulta($consulta_comentarios);
            if ($res_comentarios == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_comentarios."'");
            }

            $filas_comentarios = array();
            while ($fila_comentario = $res_comentarios->dame_siguiente_fila())
            {
                array_push($filas_comentarios, $fila_comentario);
            }
            return ($filas_comentarios);
        }


        // Devuelve las filas del comentario anterior y posterior a la fecha actual de un objeto
        static function dame_filas_comentarios_anterior_posterior_objeto($origen_comentarios, $nombre_objeto)
        {
            $bd_datos = BaseDatosDatos::dame_base_datos();

            // Devuelve los tipos de comentario visibles para un usuario
            $tipos_comentario = Comentario::dame_tipos_comentario_visibles_usuario($origen_comentarios);

            // Hora actual
            $fecha_hora_actual_utc = dame_fecha_hora_actual_utc();
            $cadena_fecha_hora_actual_base_datos_utc = convierte_fecha_a_cadena($fecha_hora_actual_utc, FORMATO_FECHA_HORA_BASE_DATOS);

            // Consultas de los comentarios
            $cadena_tipos_comentario_consulta = dame_cadena_nombres_consulta($tipos_comentario, $bd_datos);
            $consulta_comentarios = "
                SELECT
                    id,
                    hora,
                    usuario,
                    tipo,
                    objeto,
                    descripcion
                FROM comentarios
                WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (tipo IN (".$cadena_tipos_comentario_consulta."))
                    AND (objeto = '".$bd_datos->_($nombre_objeto)."')";

            // Comentario anterior
            $consulta_comentario_anterior = $consulta_comentarios."
                    AND (hora <= '".$bd_datos->_($cadena_fecha_hora_actual_base_datos_utc)."')
                ORDER BY hora DESC
                LIMIT 1";
            $res_comentario_anterior = $bd_datos->ejecuta_consulta($consulta_comentario_anterior);
            if ($res_comentario_anterior == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_comentario_anterior."'");
            }
            $fila_comentario_anterior = NULL;
            if ($res_comentario_anterior->dame_numero_filas() > 0)
            {
                $fila_comentario_anterior = $res_comentario_anterior->dame_siguiente_fila();
            }

            // Comentario posterior
            $consulta_comentario_posterior = $consulta_comentarios."
                    AND (hora > '".$bd_datos->_($cadena_fecha_hora_actual_base_datos_utc)."')
                ORDER BY hora ASC
                LIMIT 1";
            $res_comentario_posterior = $bd_datos->ejecuta_consulta($consulta_comentario_posterior);
            if ($res_comentario_posterior == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_comentario_posterior."'");
            }
            $fila_comentario_posterior = NULL;
            if ($res_comentario_posterior->dame_numero_filas() > 0)
            {
                $fila_comentario_posterior = $res_comentario_posterior->dame_siguiente_fila();
            }

            // Se devuelven los comentarios
            $filas_comentarios = array(
                "anterior" => $fila_comentario_anterior,
                "posterior" => $fila_comentario_posterior);
            return ($filas_comentarios);
        }


        // Devuelve la tabla de comentarios de los objetos de un informe
        static function dame_tabla_comentarios_objetos_informe(
            $origen_comentarios,
            $parametros_origen_comentarios,
            $id_tabla,
            $filas_comentarios,
            $ids_objetos,
            $nombres_objetos,
            $tipo_informe)
        {
            $idiomas = new Idiomas();

            // Tipo de nodo de la tabla de comentarios y mostrado de nombre de objeto
            switch ($origen_comentarios)
            {
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
                {
                    $tipo_nodo = TIPO_NODO_SENSOR;
                    $origen_comentarios_unico = true;
                    $multiples_tipos_nodos = false;
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
                {
                    $tipo_nodo = TIPO_NODO_ACTUADOR;
                    $origen_comentarios_unico = true;
                    $multiples_tipos_nodos = true;
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
                {
                    $tipo_nodo = TIPO_NODO_GRUPO_ACTUADORES;
                    $origen_comentarios_unico = true;
                    $multiples_tipos_nodos = true;
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
                {
                    $tipo_nodo = TIPO_NODO_SENSOR;
                    $origen_comentarios_unico = false;
                    $multiples_tipos_nodos = false;
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME:
                {
                    $tipo_nodo = TIPO_NODO_SENSOR;
                    $origen_comentarios_unico = true;
                    $multiples_tipos_nodos = false;
                    break;
                }
                default:
                {
                    throw new Exception("Origen de comentarios incorrecto: '".$origen_comentarios."'");
                }
            }

            // Flag para mostrar los usuarios en la tabla:
            // - Se muestra si el usuario tiene la seccion comentarios del módulo Red
            //   o tiene los permisos de comentarios del módulo correspondiente
            $mostrar_usuario = false;
            if (dame_seccion_disponible_sesion(MODULO_RED, SECCION_RED_COMENTARIOS) == true)
            {
                $mostrar_usuario = true;
            }
            else
            {
                switch ($tipo_nodo)
                {
                    case TIPO_NODO_SENSOR:
                    {
                        $mostrar_usuario = NodoSensor::dame_administracion_comentarios_sensores();
                        break;
                    }
                    case TIPO_NODO_ACTUADOR:
                    case TIPO_NODO_GRUPO_ACTUADORES:
                    {
                        $mostrar_usuario = NodoActuador::dame_administracion_comentarios_actuadores();
                        break;
                    }
                }
            }

            // Opciones de la tabla dependiendo del tipo de informe
            switch ($tipo_informe)
            {
                case TIPO_INFORME_WEB_EMIOS:
                {
                    switch ($tipo_nodo)
                    {
                        case TIPO_NODO_SENSOR:
                        {
                            $mostrar_opciones = NodoSensor::dame_administracion_comentarios_sensores();
                            break;
                        }
                        case TIPO_NODO_ACTUADOR:
                        case TIPO_NODO_GRUPO_ACTUADORES:
                        {
                            $mostrar_opciones = NodoActuador::dame_administracion_comentarios_actuadores();
                            break;
                        }
                    }
                    break;
                }
                case TIPO_INFORME_FICHERO:
                {
                    $mostrar_opciones = false;
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de informe desconocido: '".$tipo_informe."'");
                }
            }

            // Se crea la tabla
            if ($mostrar_usuario == true)
            {
                $numero_columnas = NUMERO_COLUMNAS_TABLA_COMENTARIOS_CON_USUARIO;
                $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_COMENTARIOS_CON_USUARIO);

                $cabecera_tabla = array(
                    $idiomas->_("Fecha"),
                    $idiomas->_("Usuario"),
                    $idiomas->_("Tipo"),
                    $idiomas->_("Objeto"),
                    $idiomas->_("Descripción")
                );

                // Botón para mostrar la ventana de anyadir comentario o comentarios según si el origen del comentario es único (o múltiple)
                if ($origen_comentarios_unico == true)
                {
                    $opciones = array();
                    if ($mostrar_opciones == true)
                    {
                        $anyadir = "<i objeto='".$nombres_objetos[0]."' origen_comentario='".$origen_comentarios."' parametros_origen_comentario='".$parametros_origen_comentarios."' ".
                            "class='icon-plus color-blanco boton-tabla-datos boton_mostrar_ventana_anyadir_modificar_comentario'></i>";
                        array_push($opciones, $anyadir);
                    }
                }
                else
                {
                    $opciones = array();
                    if ($mostrar_opciones == true)
                    {
                        $cadena_ids_objetos = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_objetos);
                        $anyadir = "<i ids_objetos='".$cadena_ids_objetos."' origen_comentarios='".$origen_comentarios."' parametros_origen_comentarios='".$parametros_origen_comentarios."' ".
                            "class='icon-plus color-blanco boton-tabla-datos boton_mostrar_ventana_anyadir_comentarios'></i>";
                        array_push($opciones, $anyadir);
                    }
                }
            }
            else
            {
                $numero_columnas = NUMERO_COLUMNAS_TABLA_COMENTARIOS_SIN_USUARIO;
                $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_COMENTARIOS_SIN_USUARIO);
                $opciones = array();
                $cabecera_tabla = array(
                    $idiomas->_("Fecha"),
                    $idiomas->_("Tipo"),
                    $idiomas->_("Objeto"),
                    $idiomas->_("Descripción")
                );
            }

            $params_tabla = array(
                "numero_columnas" => $numero_columnas,
                "anchuras_columnas" => $anchuras_columnas,
                "generar_valores_xml" => true,
                "opciones" => $opciones
            );
            $tabla = new TablaDatos(
                $id_tabla,
                $idiomas->_("Comentarios"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $tabla->anyade_cabecera("", $cabecera_tabla);

            // Se añaden los comentarios a la tabla
            $numero_comentarios = 0;
            $numero_comentarios_totales = count($filas_comentarios);
            foreach ($filas_comentarios as $fila_comentario)
            {
                $comentario = new Comentario($fila_comentario);
                if ($mostrar_opciones == true)
                {
                    $opciones = $comentario->dame_opciones_tabla($origen_comentarios, $parametros_origen_comentarios);
                }
                else
                {
                    $opciones = array();
                }
                $params_fila = array(
                    "opciones" => $opciones
                );
                $tabla->anyade_fila(
                    "datosComentario__".$fila_comentario['id'],
                    $comentario->dame_datos_tabla($multiples_tipos_nodos, $mostrar_usuario),
                    $params_fila
                );
                $numero_comentarios += 1;
            }
            $pie = $idiomas->_("Número de comentarios").": ".$numero_comentarios;
            if ($numero_comentarios_totales > $numero_comentarios)
            {
                $pie .= " (".$idiomas->_("total").": ".$numero_comentarios_totales.")";
            }
            $tabla->anyade_pie($pie);

            return ($tabla->dame_tabla());
        }


        // Devuelve la tabla de comentarios de los nodos de un informe
        static function dame_tabla_comentarios_nodos_informe(
            $origen_comentarios,
            $parametros_origen_comentarios,
            $id_tabla,
            $filas_comentarios,
            $ids_sensores,
            $ids_actuadores,
            $ids_grupos_actuadores,
            $tipo_informe)
        {
            $idiomas = new Idiomas();

            // Flag que indica si hay diferentes tipos de nodos en los comentarios
            // - Nota: Si el tipo de nodo es actuador o grupo de actuadores, se suman 2 tipos de nodos porque se muestran los comentarios
            //   de los actuadores y de su grupo y viceversa
            $numero_tipos_nodos = 0;
            if (count($ids_sensores) > 0)
            {
                $numero_tipos_nodos += 1;
            }
            if (count($ids_actuadores) > 0)
            {
                $numero_tipos_nodos += 2;
            }
            if (count($ids_grupos_actuadores) > 0)
            {
                $numero_tipos_nodos += 2;
            }
            if ($numero_tipos_nodos > 1)
            {
                $multiples_tipos_nodos = true;
            }
            else
            {
                $multiples_tipos_nodos = false;
            }

            // Flag para mostrar los usuarios en la tabla:
            // - Se muestra si el usuario tiene la seccion comentarios del módulo Red
            //   o tiene los permisos de comentarios del módulo correspondiente
            $mostrar_usuario = false;
            $mostrar_usuario_sensores = false;
            $mostrar_usuario_actuadores = false;
            if (dame_seccion_disponible_sesion(MODULO_RED, SECCION_RED_COMENTARIOS) == true)
            {
                $mostrar_usuario = true;
                $mostrar_usuario_sensores = true;
                $mostrar_usuario_actuadores = true;
            }
            else
            {
                $mostrar_usuario_sensores = NodoSensor::dame_administracion_comentarios_sensores();
                $mostrar_usuario_actuadores = NodoActuador::dame_administracion_comentarios_actuadores();
                if (($mostrar_usuario_sensores == true) || ($mostrar_usuario_actuadores == true))
                {
                    $mostrar_usuario = true;
                }
            }

            // Opciones de la tabla dependiendo del tipo de informe
            switch ($tipo_informe)
            {
                case TIPO_INFORME_WEB_EMIOS:
                {
                    $mostrar_opciones = (NodoSensor::dame_administracion_comentarios_sensores() ||
                        NodoActuador::dame_administracion_comentarios_actuadores());
                    break;
                }
                case TIPO_INFORME_FICHERO:
                {
                    $mostrar_opciones = false;
                    break;
                }
            }

            // Se crea la tabla
            if ($mostrar_usuario)
            {
                $numero_columnas = NUMERO_COLUMNAS_TABLA_COMENTARIOS_CON_USUARIO;
                $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_COMENTARIOS_CON_USUARIO);

                $opciones = array();
                if ($mostrar_opciones == true)
                {
                    $cadena_ids_sensores = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_sensores);
                    $cadena_ids_actuadores = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_actuadores);
                    $cadena_ids_grupos_actuadores = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_grupos_actuadores);
                    $anyadir = "<i ids_sensores='".$cadena_ids_sensores."' ids_actuadores='".$cadena_ids_actuadores."' ids_grupos_actuadores='".$cadena_ids_grupos_actuadores."' ".
                        "origen_comentarios='".$origen_comentarios."' parametros_origen_comentarios='".$parametros_origen_comentarios."' ".
                        "class='icon-plus color-blanco boton-tabla-datos boton_mostrar_ventana_anyadir_comentarios'></i>";
                    array_push($opciones, $anyadir);
                }

                $cabecera_tabla = array(
                    $idiomas->_("Fecha"),
                    $idiomas->_("Usuario"),
                    $idiomas->_("Tipo"),
                    $idiomas->_("Objeto"),
                    $idiomas->_("Descripción")
                );
            }
            else
            {
                $numero_columnas = NUMERO_COLUMNAS_TABLA_COMENTARIOS_SIN_USUARIO;
                $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_COMENTARIOS_SIN_USUARIO);
                $opciones = array();
                $cabecera_tabla = array(
                    $idiomas->_("Fecha"),
                    $idiomas->_("Tipo"),
                    $idiomas->_("Objeto"),
                    $idiomas->_("Descripción")
                );
            }

            $params_tabla = array(
                "numero_columnas" => $numero_columnas,
                "anchuras_columnas" => $anchuras_columnas,
                "generar_valores_xml" => true,
                "opciones" => $opciones
            );
            $tabla = new TablaDatos(
                $id_tabla,
                $idiomas->_("Comentarios"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $tabla->anyade_cabecera("", $cabecera_tabla);

            // Se añaden los comentarios a la tabla
            $numero_comentarios = 0;
            $numero_comentarios_totales = count($filas_comentarios);
            foreach ($filas_comentarios as $fila_comentario)
            {
                $comentario = new Comentario($fila_comentario);
                if ($mostrar_opciones == true)
                {
                    $opciones = $comentario->dame_opciones_tabla($origen_comentarios, $parametros_origen_comentarios);
                }
                else
                {
                    $opciones = array();
                }
                $params_fila = array(
                    "opciones" => $opciones
                );
                $tipo_comentario = $fila_comentario["tipo"];
                switch ($tipo_comentario)
                {
                    case TIPO_COMENTARIO_ANOTACION_SENSOR:
                    case TIPO_COMENTARIO_INTERVENCION_SENSOR:
                    {
                        if ($mostrar_usuario_sensores == false)
                        {
                            $comentario->params["usuario"] = $idiomas->_("ND");
                        }
                        break;
                    }
                    case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
                    case TIPO_COMENTARIO_INTERVENCION_ACTUADOR:
                    case TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES:
                    case TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES:
                    {
                        if ($mostrar_usuario_actuadores == false)
                        {
                            $comentario->params["usuario"] = $idiomas->_("ND");
                        }
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de comentario desconocido: '".$tipo_comentario."'");
                    }
                }
                $tabla->anyade_fila(
                    "datosComentario__".$fila_comentario['id'],
                    $comentario->dame_datos_tabla($multiples_tipos_nodos, $mostrar_usuario),
                    $params_fila
                );
                $numero_comentarios += 1;
            }
            $pie = $idiomas->_("Número de comentarios").": ".$numero_comentarios;
            if ($numero_comentarios_totales > $numero_comentarios)
            {
                $pie .= " (".$idiomas->_("total").": ".$numero_comentarios_totales.")";
            }
            $tabla->anyade_pie($pie);

            return ($tabla->dame_tabla());
        }


        // Devuelve las líneas verticales que corresponden a los comentarios en las gráficas de los informes
        static function dame_lineas_verticales_comentarios_informe(
            $filas_comentarios,
            $multiples_tipos_nodos,
            $milisegundos_desfase_zonas_horarias)
        {
            $lineas_verticales_comentarios = array();
            foreach ($filas_comentarios as $fila_comentario)
            {
                $cadena_fecha_hora_comentario_base_datos_utc = $fila_comentario["hora"];
                $tipo_comentario = $fila_comentario["tipo"];
                $objeto_comentario = $fila_comentario["objeto"];

                $timestamp_linea_vertical = dame_timestamp_cadena_fecha_milisegundos($cadena_fecha_hora_comentario_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC);
                $timestamp_linea_vertical -= $milisegundos_desfase_zonas_horarias;
                $color_linea_vertical = Comentario::dame_color_linea_vertical_tipo_comentario($tipo_comentario);

                $cadenas_comentario = Comentario::dame_cadenas_hora_tipo_descripcion_fila_comentario($fila_comentario, $multiples_tipos_nodos);
                $cadena_hora_comentario_local_local = $cadenas_comentario["cadena_hora_comentario_local_local"];
                $descripcion_tipo_comentario = $cadenas_comentario["descripcion_tipo_comentario"];
                $descripcion_comentario = $cadenas_comentario["descripcion_comentario"];

                // http://stackoverflow.com/questions/11254787/php-split-a-long-string-without-breaking-words
                $descripcion_comentario = htmlspecialchars($descripcion_comentario, ENT_QUOTES);
                $lineas_descripcion_comentario_tooltip = explode("\n", wordwrap($descripcion_comentario, NUMERO_MAXIMO_CARACTERES_LINEA_TOOLTIP_COMENTARIOS_GRAFICA));
                $descripcion_comentario_tooltip = "";
                foreach ($lineas_descripcion_comentario_tooltip as $linea_descripcion_comentario_tooltip)
                {
                    if ($descripcion_comentario_tooltip != "")
                    {
                        $descripcion_comentario_tooltip .= "<br/>";
                    }
                    $descripcion_comentario_tooltip .= $linea_descripcion_comentario_tooltip;
                }
                $texto_tooltip = $descripcion_tipo_comentario." (".$cadena_hora_comentario_local_local.")"."<br/>";
                $texto_tooltip .= htmlspecialchars($objeto_comentario, ENT_QUOTES)."<br/>";
                $texto_tooltip .= "[".$descripcion_comentario_tooltip."]";

                $linea_vertical_comentario = array(
                    "valor" => $timestamp_linea_vertical,
                    "color" => $color_linea_vertical,
                    "texto_tooltip" => $texto_tooltip);
                array_push($lineas_verticales_comentarios, $linea_vertical_comentario);
            }

            return ($lineas_verticales_comentarios);
        }


        // Miembros de comentario


        public $idiomas;

        public $id;
        public $params;


        // Funciones de comentario


		function __construct($params)
		{
			$this->idiomas = new Idiomas();

			$this->id = $params['id'];
            $this->params = $params;
		}


        function dame_datos_tabla($multiples_tipos_nodos, $mostrar_usuario)
        {
            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $cadena_fecha_hora_local_local_sin_segundos = $icono_dato_erroneo;
            $id_usuario = $icono_dato_erroneo;
            $descripcion_tipo = $icono_dato_erroneo;
            $objeto = $icono_dato_erroneo;
            $descripcion = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $fecha_hora_correcta = false;
            try
            {
                // Conversión de fechas
                $zona_horaria = dame_zona_horaria_local();
                $cadena_fecha_hora_local_utc = convierte_formato_fecha($this->params['hora'], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
                $cadena_fecha_hora_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_fecha_hora_local_local_sin_segundos = convierte_formato_fecha($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_hora_local_sin_segundos"]);
                $fecha_hora_correcta = true;

                // Datos de la tabla
                $id_usuario = $this->params['usuario'];
                $tipo = $this->params['tipo'];
                $descripcion_tipo = Comentario::dame_descripcion_tipo_comentario($tipo, $multiples_tipos_nodos);
                $visibilidad = $this->params['visibilidad'];
                if ($visibilidad == VISIBILIDAD_PRIVADA)
                {
                    $icono_visibilidad_privada = "<i class='icon-eye-close color-gris'>".
                        "<texto class='elemento-oculto'>".htmlspecialchars($this->idiomas->_("privado"), ENT_QUOTES)."</texto></i>";
                    $descripcion_tipo .= " <iconos-dato class='iconos-dato'>[".$icono_visibilidad_privada."]</iconos-dato>";
                }
                $objeto = htmlspecialchars($this->params['objeto'], ENT_QUOTES);
                $descripcion = htmlspecialchars($this->params['descripcion'], ENT_QUOTES);
            }
            catch (Exception $e)
            {
                // Se añade información de la excepción en el log
                $log = dame_log();
                $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $e);

                // Se añade icono de error en la fecha
                if ($fecha_hora_correcta == true)
                {
                    $cadena_fecha_hora_local_local_sin_segundos = "[".$icono_fila_con_errores."] ".$cadena_fecha_hora_local_local_sin_segundos;
                }
            }

            // Datos de la tabla (según los parámetros)
            $datos_tabla = array();
            array_push($datos_tabla, $cadena_fecha_hora_local_local_sin_segundos);
            if ($mostrar_usuario == true)
            {
                array_push($datos_tabla, $id_usuario);
            }
            array_push($datos_tabla, $descripcion_tipo);
            array_push($datos_tabla, $objeto);
            array_push($datos_tabla, $descripcion);
            return ($datos_tabla);
        }


        function dame_opciones_tabla($origen_comentario, $parametros_origen_comentario)
        {
            $zona_horaria = dame_zona_horaria_local();

            $objeto = htmlspecialchars($this->params["objeto"], ENT_QUOTES);
            $cadena_fecha_hora_local = cambia_zona_horaria_cadena_fecha_hora($this->params["hora"], FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_local_sin_segundos = convierte_formato_fecha($cadena_fecha_hora_local, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local_sin_segundos"]);

            $opciones = array();
            $tipo_comentario = $this->params['tipo'];
            $visibilidad_comentario = $this->params['visibilidad'];
            switch ($tipo_comentario)
            {
                case TIPO_COMENTARIO_ANOTACION_SENSOR:
                case TIPO_COMENTARIO_INTERVENCION_SENSOR:
                {
                    $administracion_comentarios = NodoSensor::dame_administracion_comentarios_sensores();
                    break;
                }
                case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
                case TIPO_COMENTARIO_INTERVENCION_ACTUADOR:
                case TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES:
                case TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES:
                {
                    $administracion_comentarios = NodoActuador::dame_administracion_comentarios_actuadores();
                    break;
                }
            }
            if ($administracion_comentarios == true)
            {
                $editar = "<i id='anyade_modifica_comentario__".$this->id."' objeto='".$objeto."' ".
                    "tipo_comentario='".$tipo_comentario."' visibilidad_comentario='".$visibilidad_comentario."' ".
                    "origen_comentario='".$origen_comentario."' parametros_origen_comentario='".$parametros_origen_comentario."' ".
                    "class='icon-pencil color-gris boton_mostrar_ventana_anyadir_modificar_comentario boton-tabla-datos'></i>";
                $borrar = "<i id='elimina_comentario__".$this->id."' objeto='".$objeto."' fecha_hora='".$cadena_fecha_hora_local_sin_segundos."' ".
                    "origen_comentario='".$origen_comentario."' parametros_origen_comentario='".$parametros_origen_comentario."'".
                    "class='icon-remove color-gris boton_eliminar_comentario boton-tabla-datos'></i>";
                $opciones = array($borrar, $editar);
            }

            return ($opciones);
        }


        //
        // Funciones auxiliares
        //


        // Devuelve los tipos de comentario
        static function dame_tipos_comentario(
            $origen_comentario,
            $parametros_ventana_administracion_comentarios,
            $tipo_comentario)
        {
            $tipos_comentario = array();
            switch ($origen_comentario)
            {
                case ORIGEN_COMENTARIOS_TABLA_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
                {
                    $tipo_ventana = $parametros_ventana_administracion_comentarios["tipo_ventana"];
                    switch ($tipo_ventana)
                    {
                        case TIPO_VENTANA_ANYADIR_COMENTARIO:
                        {
                            switch ($tipo_comentario)
                            {
                                case TIPO_COMENTARIO_ANOTACION_SENSOR:
                                case TIPO_COMENTARIO_INTERVENCION_SENSOR:
                                {
                                    array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_SENSOR);
                                    array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_SENSOR);
                                    break;
                                }
                                case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
                                case TIPO_COMENTARIO_INTERVENCION_ACTUADOR:
                                {
                                    array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_ACTUADOR);
                                    array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_ACTUADOR);
                                    break;
                                }
                                case TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES:
                                case TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES:
                                {
                                    array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES);
                                    array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES);
                                    break;
                                }
                                default:
                                {
                                    throw new Exception("Tipo de comentario desconocido: '".$tipo_comentario."'");
                                }
                                break;
                            }
                            break;
                        }
                        case TIPO_VENTANA_ANYADIR_COMENTARIOS:
                        {
                            $administracion_comentarios_sensores = $parametros_ventana_administracion_comentarios["administracion_comentarios_sensores"];
                            $administracion_comentarios_actuadores = $parametros_ventana_administracion_comentarios["administracion_comentarios_actuadores"];
                            if ($administracion_comentarios_sensores == true)
                            {
                                array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_SENSOR);
                                array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_SENSOR);
                            }
                            if ($administracion_comentarios_actuadores == true)
                            {
                                array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_ACTUADOR);
                                array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_ACTUADOR);
                                array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES);
                                array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES);
                            }
                            break;
                        }
                    }
                    break;
                }
                case ORIGEN_COMENTARIOS_TABLA_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
                case ORIGEN_COMENTARIOS_TABLA_COMENTARIOS_RED:
                {
                    switch ($tipo_comentario)
                    {
                        case TIPO_COMENTARIO_ANOTACION_SENSOR:
                        case TIPO_COMENTARIO_INTERVENCION_SENSOR:
                        {
                            array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_SENSOR);
                            array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_SENSOR);
                            break;
                        }
                        case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
                        case TIPO_COMENTARIO_INTERVENCION_ACTUADOR:
                        {
                            array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_ACTUADOR);
                            array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_ACTUADOR);
                            break;
                        }
                        case TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES:
                        case TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES:
                        {
                            array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES);
                            array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES);
                            break;
                        }
                        default:
                        {
                            throw new Exception("Tipo de comentario desconocido: '".$tipo_comentario."'");
                        }
                        break;
                    }
                    break;
                }
                case ORIGEN_COMENTARIOS_HERRAMIENTAS_SENSORES:
                case ORIGEN_COMENTARIOS_DETALLES_TABLA_SENSORES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
                {
                    array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_SENSOR);
                    array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_SENSOR);
                    break;
                }
                case ORIGEN_COMENTARIOS_DETALLES_TABLA_ACTUADORES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
                {
                    array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_ACTUADOR);
                    array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_ACTUADOR);
                    break;
                }
                case ORIGEN_COMENTARIOS_DETALLES_TABLA_GRUPOS_ACTUADORES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
                {
                    array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES);
                    array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES);
                    break;
                }
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
                {
                    switch ($tipo_comentario)
                    {
                        case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
                        case TIPO_COMENTARIO_INTERVENCION_ACTUADOR:
                        {
                            array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_ACTUADOR);
                            array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_ACTUADOR);
                            break;
                        }
                        case TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES:
                        case TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES:
                        {
                            array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES);
                            array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES);
                            break;
                        }
                        default:
                        {
                            throw new Exception("Tipo de comentario desconocido o incorrecto: '".$tipo_comentario."'");
                        }
                        break;
                    }
                    break;
                }
                case ORIGEN_COMENTARIOS_HERRAMIENTAS_ACTUADORES:
                {
                    array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_ACTUADOR);
                    array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_ACTUADOR);
                    array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES);
                    array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES);
                    break;
                }
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME:
                {
                    array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_SENSOR);
                    array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_SENSOR);
                    break;
                }
                default:
                {
                    throw new Exception("Origen de comentario desconocido: '".$origen_comentario."'");
                }
            }
            return ($tipos_comentario);
        }


        // Devuelve si los tipos de comentario corresponden a múltiples tipos de nodos
        static function dame_multiples_tipos_nodos_tipos_comentario($tipos_comentario)
        {
            $tipos_nodos = array();
            foreach ($tipos_comentario as $tipo_comentario)
            {
                switch ($tipo_comentario)
                {
                    case TIPO_COMENTARIO_ANOTACION_SENSOR:
                    case TIPO_COMENTARIO_INTERVENCION_SENSOR:
                    {
                        if (in_array(TIPO_NODO_SENSOR, $tipos_nodos) == false)
                        {
                            array_push($tipos_nodos, TIPO_NODO_SENSOR);
                        }
                        break;
                    }
                    case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
                    case TIPO_COMENTARIO_INTERVENCION_ACTUADOR:
                    {
                        if (in_array(TIPO_NODO_ACTUADOR, $tipos_nodos) == false)
                        {
                            array_push($tipos_nodos, TIPO_NODO_ACTUADOR);
                        }
                        break;
                    }
                    case TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES:
                    case TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES:
                    {
                        if (in_array(TIPO_NODO_GRUPO_ACTUADORES, $tipos_nodos) == false)
                        {
                            array_push($tipos_nodos, TIPO_NODO_GRUPO_ACTUADORES);
                        }
                        break;
                    }
                    default:
                    {
                        throw new Exception("Tipo de comentario desconocido: '".$tipo_comentario."'");
                    }
                    break;
                }
            }
            $multiples_tipos_nodos = (count($tipos_nodos) > 1);
            return ($multiples_tipos_nodos);
        }


        // Devuelve la descripción del tipo del comentario
        static function dame_descripcion_tipo_comentario($tipo_comentario, $multiples_tipos_nodos)
        {
            $idiomas = new Idiomas();

            switch ($tipo_comentario)
            {
                case TIPO_COMENTARIO_ANOTACION_SENSOR:
                case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
                case TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES:
                {
                    $descripcion = "Anotación";
                    break;
                }
                case TIPO_COMENTARIO_INTERVENCION_SENSOR:
                case TIPO_COMENTARIO_INTERVENCION_ACTUADOR:
                case TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES:
                {
                    $descripcion = "Intervención";
                    break;
                }
                default:
                {
                    $descripcion = "Desconocido";
                    break;
                }
            }
            $descripcion = $idiomas->_($descripcion);

            if ($multiples_tipos_nodos == true)
            {
                switch ($tipo_comentario)
                {
                    case TIPO_COMENTARIO_ANOTACION_SENSOR:
                    case TIPO_COMENTARIO_INTERVENCION_SENSOR:
                    {
                        $descripcion_tipo_nodo = "sensor";
                        break;
                    }
                    case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
                    case TIPO_COMENTARIO_INTERVENCION_ACTUADOR:
                    {
                        $descripcion_tipo_nodo = "actuador";
                        break;
                    }
                    case TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES:
                    case TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES:
                    {
                        $descripcion_tipo_nodo = "grupo de actuadores";
                        break;
                    }
                    default:
                    {
                        $descripcion_tipo_nodo = "desconocido";
                        break;
                    }
                }
                $descripcion .= " (".$idiomas->_($descripcion_tipo_nodo).")";
            }
            return ($descripcion);
        }


        // Devuelve los tipos de comentario visibles para un usuario
        static function dame_tipos_comentario_visibles_usuario($origen_comentarios)
        {
            $tipos_comentario = array();
            switch ($origen_comentarios)
            {
                case ORIGEN_COMENTARIOS_HERRAMIENTAS_SENSORES:
                case ORIGEN_COMENTARIOS_DETALLES_TABLA_SENSORES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SENSORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ELEMENTO_PLANTILLA_INFORME:
                {
                    // Flag para incluir las intervenciones:
                    // - Se muestra si el usuario tiene administración de comentarios de sensores o ve la seccion comentarios del módulo Red
                    $incluir_intervenciones = false;
                    if (NodoSensor::dame_administracion_comentarios_sensores() == true)
                    {
                        $incluir_intervenciones = true;
                    }
                    if ($incluir_intervenciones == false)
                    {
                        $modulos_usuario = dame_modulos_usuario($_SESSION["id_usuario"], $_SESSION["perfil"], $_SESSION["id_red"]);
                        if (in_array(MODULO_RED, $modulos_usuario))
                        {
                            $secciones_usuario = dame_secciones_usuario($_SESSION["id_usuario"], $_SESSION["id_red"]);
                            if ((count($secciones_usuario[MODULO_RED]) == 0) || (in_array(SECCION_RED_ACCIONES_USUARIO, $secciones_usuario[MODULO_RED])))
                            {
                                $incluir_intervenciones = true;
                            }
                        }
                    }

                    array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_SENSOR);
                    if ($incluir_intervenciones == true)
                    {
                        array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_SENSOR);
                    }
                    break;
                }
                case ORIGEN_COMENTARIOS_HERRAMIENTAS_ACTUADORES:
                case ORIGEN_COMENTARIOS_DETALLES_TABLA_ACTUADORES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ACTUADOR:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_ACTUADOR:
                {
                    // Flag para incluir las intervenciones:
                    // - Se muestra si el usuario tiene administración de comentarios de actuadores o ve la seccion comentarios del módulo Red
                    $incluir_intervenciones = false;
                    if (NodoActuador::dame_administracion_comentarios_actuadores() == true)
                    {
                        $incluir_intervenciones = true;
                    }
                    if ($incluir_intervenciones == false)
                    {
                        $modulos_usuario = dame_modulos_usuario($_SESSION["id_usuario"], $_SESSION["perfil"], $_SESSION["id_red"]);
                        if (in_array(MODULO_RED, $modulos_usuario))
                        {
                            $secciones_usuario = dame_secciones_usuario($_SESSION["id_usuario"], $_SESSION["id_red"]);
                            if ((count($secciones_usuario[MODULO_RED]) == 0) || (in_array(SECCION_RED_ACCIONES_USUARIO, $secciones_usuario[MODULO_RED])))
                            {
                                $incluir_intervenciones = true;
                            }
                        }
                    }

                    array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_ACTUADOR);
                    if ($incluir_intervenciones == true)
                    {
                        array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_ACTUADOR);
                    }
                    break;
                }
                case ORIGEN_COMENTARIOS_COMENTARIOS_GRUPOS_ACTUADORES:
                case ORIGEN_COMENTARIOS_DETALLES_TABLA_GRUPOS_ACTUADORES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_GRUPO_ACTUADORES:
                case ORIGEN_COMENTARIOS_GRAFICA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
                case ORIGEN_COMENTARIOS_TABLA_INFORME_ACTUADORES_INFORMACION_ELEMENTO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
                {
                    // Flag para incluir las intervenciones:
                    // - Se muestra si el usuario tiene administración de comentarios de actuadores o ve la seccion comentarios del módulo Red
                    $incluir_intervenciones = false;
                    if (NodoActuador::dame_administracion_comentarios_actuadores() == true)
                    {
                        $incluir_intervenciones = true;
                    }
                    if ($incluir_intervenciones == false)
                    {
                        $modulos_usuario = dame_modulos_usuario($_SESSION["id_usuario"], $_SESSION["perfil"], $_SESSION["id_red"]);
                        if (in_array(MODULO_RED, $modulos_usuario))
                        {
                            $secciones_usuario = dame_secciones_usuario($_SESSION["id_usuario"], $_SESSION["id_red"]);
                            if ((count($secciones_usuario[MODULO_RED]) == 0) || (in_array(SECCION_RED_ACCIONES_USUARIO, $secciones_usuario[MODULO_RED])))
                            {
                                $incluir_intervenciones = true;
                            }
                        }
                    }

                    array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_ACTUADOR);
                    array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES);
                    if ($incluir_intervenciones == true)
                    {
                        array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_ACTUADOR);
                        array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES);
                    }
                    break;
                }
                default:
                {
                    throw new Exception("Origen de comentarios incorrecto o desconocido: '".$origen_comentarios."'");
                }
            }

            return ($tipos_comentario);
        }


        // Devuelve los tipos de comentario del nodo especificado
        static function dame_tipos_comentario_tipo_nodo($tipo_nodo)
        {
            $tipos_comentario = array();
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_SENSOR);
                    array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_SENSOR);
                    break;
                }
                case TIPO_NODO_ACTUADOR:
                {
                    array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_ACTUADOR);
                    array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_ACTUADOR);
                    break;
                }
                case TIPO_NODO_GRUPO_ACTUADORES:
                {
                    array_push($tipos_comentario, TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES);
                    array_push($tipos_comentario, TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES);
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de nodo incorrecto: '".$tipo_nodo."'");
                }
            }
            return ($tipos_comentario);
        }


        // Devuelve el color para la línea vertical del tipo de comentario
        static function dame_color_linea_vertical_tipo_comentario($tipo_comentario)
        {
            switch ($tipo_comentario)
            {
                case TIPO_COMENTARIO_ANOTACION_SENSOR:
                case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
                case TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES:
                {
                    $color_linea_vertical = COLOR_LINEA_GRAFICA_VERDE_OSCURO;
                    break;
                }
                case TIPO_COMENTARIO_INTERVENCION_SENSOR:
                case TIPO_COMENTARIO_INTERVENCION_ACTUADOR:
                case TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES:
                {
                    $color_linea_vertical = COLOR_LINEA_GRAFICA_NARANJA;
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de comentario desconocido: '".$tipo_comentario."'");
                }
            }

            return ($color_linea_vertical);
        }


        // Devuelve las cadenas de hora local, tipo y descripción de una fila del comentario
        static function dame_cadenas_hora_tipo_descripcion_fila_comentario($fila_comentario, $multiples_tipos_nodos)
        {
            $zona_horaria = dame_zona_horaria_local();
            $cadena_hora_comentario_local_utc = convierte_formato_fecha($fila_comentario["hora"], FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
            $cadena_hora_comentario_local_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_comentario_local_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
            $descripcion_tipo_comentario = Comentario::dame_descripcion_tipo_comentario($fila_comentario["tipo"], $multiples_tipos_nodos);
            $descripcion_comentario = $fila_comentario["descripcion"];

            return (array(
                "cadena_hora_comentario_local_local" => $cadena_hora_comentario_local_local,
                "descripcion_tipo_comentario" => $descripcion_tipo_comentario,
                "descripcion_comentario" => $descripcion_comentario
            ));
        }
	}
?>
