<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/Comentario.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_hijos_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    // Devuelve la lista de tipos de comentarios (para las ventanas de administración de comentarios)
    function dame_lista_tipos_comentario(
        $origen_comentario,
        $parametros_ventana_administracion_comentarios,
        $tipo_comentario_seleccionado)
    {
        $tipos_comentario = Comentario::dame_tipos_comentario(
            $origen_comentario,
            $parametros_ventana_administracion_comentarios,
            $tipo_comentario_seleccionado);
        $multiple_tipos_nodos = Comentario::dame_multiples_tipos_nodos_tipos_comentario($tipos_comentario);

        $lista = "";
        foreach ($tipos_comentario as $tipo_comentario)
        {
            $nombre_tipo_comentario = Comentario::dame_descripcion_tipo_comentario($tipo_comentario, $multiple_tipos_nodos);
            $lista .= "<option value='".$tipo_comentario."'";
            if ($tipo_comentario == $tipo_comentario_seleccionado)
            {
                $lista .= " selected";
            }
            $lista .= ">".htmlspecialchars($nombre_tipo_comentario)."</option>";
        }
        return ($lista);
    }


    // Devuelve la lista de visibilidades de comentarios
    function dame_lista_visibilidades_comentario($visibilidad_seleccionada, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $valores = array();
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_VISIBILIDADES_COMENTARIOS_TODAS)
        {
            array_push($valores, array(VISIBILIDAD_TODAS, $idiomas->_("Todas")));
        }
        array_push($valores, array(VISIBILIDAD_PUBLICA, $idiomas->_("Pública")));
        array_push($valores, array(VISIBILIDAD_PRIVADA, $idiomas->_("Privada")));
        $lista = dame_lista_valores($valores, array($visibilidad_seleccionada));
        return ($lista);
    }


    // Devuelve la lista de objetos de comentarios
    function dame_lista_objetos_comentarios(
        $origen_comentarios,
        $tipo_comentarios,
        $clase_objetos,
        $cadena_ids_objetos,
        $objeto)
    {
        switch ($origen_comentarios)
        {
            case ORIGEN_COMENTARIOS_HERRAMIENTAS_SENSORES:
            case ORIGEN_COMENTARIOS_HERRAMIENTAS_ACTUADORES:
            {
                $cadena_ids_objetos = NULL;
                break;
            }
        }
        switch ($tipo_comentarios)
        {
            case TIPO_COMENTARIO_ANOTACION_SENSOR:
            case TIPO_COMENTARIO_INTERVENCION_SENSOR:
            {
                if ($cadena_ids_objetos === NULL)
                {
                    $lista = dame_lista_sensores(
                        $clase_objetos,
                        array(),
                        OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
                }
                else
                {
                    $ids_sensores_seleccionados = array();
                    if ($cadena_ids_objetos != "")
                    {
                        $ids_objetos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_objetos);
                        if ($objeto != "")
                        {
                            // Nota: Si es una agrupación no existe el nombre del sensor con el nombre de la agrupación ...
                            $fila_sensor = dame_fila_sensor_nombre($_SESSION["id_red"], $objeto);
                            if ($fila_sensor !== NULL)
                            {
                                $id_sensor = $fila_sensor["id"];
                                $ids_sensores_seleccionados = array($id_sensor);
                            }
                        }
                    }
                    else
                    {
                        $ids_objetos = array();
                    }
                    $lista = dame_lista_sensores_ids(
                        $clase_objetos,
                        $ids_objetos,
                        $ids_sensores_seleccionados,
                        OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
                }
                break;
            }
            case TIPO_COMENTARIO_ANOTACION_ACTUADOR:
            case TIPO_COMENTARIO_INTERVENCION_ACTUADOR:
            {
                if ($cadena_ids_objetos === NULL)
                {
                    $lista = dame_lista_actuadores(
                        $clase_objetos,
                        array(),
                        OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
                }
                else
                {
                    if ($cadena_ids_objetos != "")
                    {
                        $ids_objetos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_objetos);
                    }
                    else
                    {
                        $ids_objetos = array();
                    }
                    $lista = dame_lista_actuadores_ids(
                        $clase_objetos,
                        $ids_objetos,
                        array(),
                        OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
                }

                break;
            }
            case TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES:
            case TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES:
            {
                if ($cadena_ids_objetos === NULL)
                {
                    $lista = dame_lista_grupos_actuadores(
                        $clase_objetos,
                        array(),
                        OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
                }
                else
                {
                    if ($cadena_ids_objetos != "")
                    {
                        $ids_objetos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_objetos);
                    }
                    else
                    {
                        $ids_objetos = array();
                    }
                    $lista = dame_lista_grupos_actuadores_ids(
                        $clase_objetos,
                        $ids_objetos,
                        array(),
                        OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
                }
                break;
            }
            default:
            {
                throw new Exception("Tipo de comentarios desconocido: '".$tipo_comentarios."'");
            }
        }
        return ($lista);
    }


    // Devuelve la descripción de los comentarios
    function dame_descripcion_comentarios($comentarios)
    {
        switch ($comentarios)
        {
            case COMENTARIOS_NINGUNO:
            {
                $descripcion_comentarios = "Ninguno";
                break;
            }
            case COMENTARIOS_GRAFICA:
            {
                $descripcion_comentarios = "Gráfica";
                break;
            }
            case COMENTARIOS_GRAFICA_TABLA:
            {
                $descripcion_comentarios = "Gráfica y tabla";
                break;
            }
            default:
            {
                $descripcion_comentarios = "Desconocido";
                break;
            }
        }
        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion_comentarios));
    }


    // Modifica los comentarios de un nodo
    function modifica_comentarios_nodo($tipo_nodo, $nombre_anterior, $nombre_actual)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Se modifican los comentarios
        $operacion_modificacion = "
            UPDATE comentarios
            SET
                objeto = '".$bd_datos->_($nombre_actual)."'
            WHERE
                (objeto = '".$bd_datos->_($nombre_anterior)."')
                AND (";
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $operacion_modificacion .= "
                    (tipo = '".TIPO_COMENTARIO_ANOTACION_SENSOR."') OR (tipo = '".TIPO_COMENTARIO_INTERVENCION_SENSOR."')";
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $operacion_modificacion .= "
                    (tipo = '".TIPO_COMENTARIO_ANOTACION_ACTUADOR."') OR (tipo = '".TIPO_COMENTARIO_INTERVENCION_ACTUADOR."')";
                break;
            }
            case TIPO_NODO_GRUPO_ACTUADORES:
            {
                $operacion_modificacion .= "
                    (tipo = '".TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES."') OR (tipo = '".TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES."')";
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto (sin comentarios): '".$tipo_nodo."'");
            }
        }
        $operacion_modificacion .= ")";
        $res_modificacion = $bd_datos->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == false)
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion."'");
        }
    }


    // Elimina los comentarios de un nodo
    function elimina_comentarios_nodo($tipo_nodo, $nombre)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Se elimina los comentarios
        $operacion_borrado = "
            DELETE
            FROM comentarios
            WHERE
                (objeto = '".$bd_datos->_($nombre)."')
                AND (";
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $operacion_borrado .= "
                    (tipo = '".TIPO_COMENTARIO_ANOTACION_SENSOR."') OR (tipo = '".TIPO_COMENTARIO_INTERVENCION_SENSOR."')";
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $operacion_borrado .= "
                    (tipo = '".TIPO_COMENTARIO_ANOTACION_ACTUADOR."') OR (tipo = '".TIPO_COMENTARIO_INTERVENCION_ACTUADOR."')";
                break;
            }
            case TIPO_NODO_GRUPO_ACTUADORES:
            {
                $operacion_borrado .= "
                    (tipo = '".TIPO_COMENTARIO_ANOTACION_GRUPO_ACTUADORES."') OR (tipo = '".TIPO_COMENTARIO_INTERVENCION_GRUPO_ACTUADORES."')";
                break;
            }
            default:
            {
                throw new Exception("Tipo de nodo incorrecto (sin comentarios): '".$tipo_nodo."'");
            }
        }
        $operacion_borrado .= ")";
        $res_borrado = $bd_datos->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado."'");
        }
    }


    // Devuelve los nombres de sensores descendientes para mostrar en los comentarios
    // (para que al mostrar comentarios de un sensor, también se muestren comentarios de sus sensores 'hijos' si los hay)
    function dame_nombres_sensores_descendientes_comentarios($ids_sensores)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recupera si hay sensores con hijos
        $cadena_ids_sensores = dame_cadena_ids_consulta($ids_sensores);
        $consulta_sensores = "
            SELECT
                COUNT(*) AS numero_sensores_con_hijos
            FROM sensores
            WHERE
                (id IN (".$bd_red->_($cadena_ids_sensores)."))
                AND ((tipo = '".TIPO_SENSOR_VIRTUAL."') OR (tipo = '".TIPO_SENSOR_PROCESADO."'))";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if (($res_sensores == false) || ($res_sensores->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensores."'");
        }

        // - Si no hay sensores con hijos no hace falta buscar los sensores descendientes
        // - Si hay sensores con hijos se recuperan los sensores descendientes para mostrar también sus comentarios
        $fila_sensores = $res_sensores->dame_siguiente_fila();
        $numero_sensores_con_hijos = $fila_sensores["numero_sensores_con_hijos"];
        if ($numero_sensores_con_hijos == 0)
        {
            $ids_sensores_comentarios = $ids_sensores;
        }
        else
        {
            $info_sensores_padres = NULL;
            $info_sensores_hijos = NULL;
            carga_informacion_sensores_padres_hijos(TIPO_TODOS, $_SESSION["id_red"], $info_sensores_padres, $info_sensores_hijos);

            $ids_sensores_comentarios = dame_ids_sensores_descendientes_sensores($info_sensores_hijos, $ids_sensores, true);
        }

        // Se recuperan y devuelven los nombres de los sensores
        $nombres_sensores_comentarios = dame_nombres_sensores($ids_sensores_comentarios);
        return ($nombres_sensores_comentarios);
    }
?>
