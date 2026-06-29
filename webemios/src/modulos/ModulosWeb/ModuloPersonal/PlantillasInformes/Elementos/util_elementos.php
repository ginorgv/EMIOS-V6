<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/Comentario.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/util_comentarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    //
    // Funciones de obtención de información de elemento de plantilla de informe
    //


    // Devuelve la fila del elemento de plantilla de informe
    function dame_fila_elemento_plantilla_informe($id_elemento)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_elemento = "
            SELECT *
            FROM elementos_plantillas_informes
            WHERE
                id = '".$bd_red->_($id_elemento)."'";
        $res_elemento = $bd_red->ejecuta_consulta($consulta_elemento);
        if ($res_elemento == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elemento."'");
        }

        $fila_elemento = $res_elemento->dame_siguiente_fila();
        return ($fila_elemento);
    }


    //
    // Funciones de elementos de plantillas de informes de varios módulos
    //


    function dame_html_elemento_plantilla_informe_tipo_comentarios(
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
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-objetos-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay objetos seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-objetos-seleccionados-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay objetos seleccionados")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-comentarios'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_comentarios(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Parámetros del tipo de elemento
        $visibilidad_comentarios = $parametros_tipo_elemento["visibilidad_comentarios"];
        $ids_sensores = $parametros_tipo_elemento["ids_sensores"];
        $ids_actuadores = $parametros_tipo_elemento["ids_actuadores"];
        $ids_grupos_actuadores = $parametros_tipo_elemento["ids_grupos_actuadores"];
        $horario_semanal = $parametros_tipo_elemento["horario_semanal"];
        $exclusion_fechas = $parametros_tipo_elemento["exclusion_fechas"];
        $inclusion_fechas = $parametros_tipo_elemento["inclusion_fechas"];

        // Si no hay sensores, actuadores o grupos de actuadores, se devuelve sin objetos seleccionados
        // - Nota: En principio no debería haber ids de nodos a 'NINGUNO' porque no se añaden a la lista de ids en ese caso
        //   (nodos eliminados o parámetros sin seleccionar)
        $ids_sensores = array_values(array_diff($ids_sensores, array(ID_NINGUNO)));
        $ids_actuadores = array_values(array_diff($ids_actuadores, array(ID_NINGUNO)));
        $ids_grupos_actuadores = array_values(array_diff($ids_grupos_actuadores, array(ID_NINGUNO)));
        $hay_objetos_seleccionados = ((count($ids_sensores) > 0) ||
            (count($ids_actuadores) > 0) || (count($ids_grupos_actuadores) > 0));
        if ($hay_objetos_seleccionados == false)
        {
            $resultado = array(
                "res" => "OK",
                "sin_objetos_seleccionados" => true);
            return ($resultado);
        }

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_local = $parametros_informe["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros_informe["fecha_hora_fin"];
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Nombres de los objetos de los comentarios
        $nombres_sensores_comentarios = dame_nombres_sensores_descendientes_comentarios($ids_sensores);
        $ids_grupos_actuadores_comentarios_ascendientes = dame_ids_grupos_nodos(TIPO_NODO_ACTUADOR, $ids_actuadores);
        $ids_actuadores_comentarios_descendientes = dame_ids_nodos_grupos(TIPO_NODO_GRUPO_ACTUADORES, $ids_grupos_actuadores);
        $ids_actuadores_comentarios = array_unique(array_merge($ids_actuadores, $ids_actuadores_comentarios_descendientes));
        $ids_grupos_actuadores_comentarios = array_unique(array_merge($ids_grupos_actuadores, $ids_grupos_actuadores_comentarios_ascendientes));
        $nombres_actuadores_comentarios = dame_nombres_actuadores($ids_actuadores_comentarios);
        $nombres_grupos_actuadores_comentarios = dame_nombres_grupos_actuadores($ids_grupos_actuadores_comentarios);

        // Se recuperan las filas de los comentarios
        $filas_comentarios = Comentario::dame_filas_comentarios_nodos(
            $visibilidad_comentarios,
            $nombres_sensores_comentarios,
            $nombres_actuadores_comentarios,
            $nombres_grupos_actuadores_comentarios,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            $horario_semanal,
            $exclusion_fechas,
            $inclusion_fechas);
        $numero_comentarios = count($filas_comentarios);

        // Comprobación de datos disponibles
        if ($numero_comentarios == 0)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }

        // Se crea la tabla de comentarios
        $tabla_comentarios = Comentario::dame_tabla_comentarios_nodos_informe(
            ORIGEN_COMENTARIOS_TABLA_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS,
            NULL,
            "tabla-comentarios",
            $filas_comentarios,
            $ids_sensores,
            $ids_actuadores,
            $ids_grupos_actuadores,
            $parametros_informe["tipo_informe"]);

        // Se devuelven los datos del elementos
        $datos_elemento = array(
            "hay_datos" => true,
            "tabla_comentarios" => $tabla_comentarios,
            "numero_comentarios" => $numero_comentarios
        );
        return ($datos_elemento);
    }
?>