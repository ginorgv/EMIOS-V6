<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/util_modulo_actuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/PlantillaInforme.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/util_modulo_proyectos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/util_modulo_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/util_modulo_smartmeter.php');


    //
    // Funciones de listas de plantillas de informes
    //


    // Devuelve la lista de plantillas de informes
    function dame_lista_plantillas_informes($id_plantilla_informe_seleccionada, $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_plantillas_informes = PlantillaInforme::dame_consulta_plantillas_informes("");
        $res_plantillas_informes = $bd_red->ejecuta_consulta($consulta_plantillas_informes);
        if ($res_plantillas_informes == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_plantillas_informes."'");
        }

        if ($opciones_extra == OPCIONES_EXTRA_LISTA_PLANTILLAS_INFORMES_ACTUAL)
        {
            $lista_plantillas_informes .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Actual")."</option>";
        }
        else
        {
            $lista_plantillas_informes .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguna")."</option>";
        }
        while ($fila_plantilla_informe = $res_plantillas_informes->dame_siguiente_fila())
        {
            $lista_plantillas_informes .= "<option value='".$fila_plantilla_informe['id']."'";
            if ($fila_plantilla_informe['id'] == $id_plantilla_informe_seleccionada)
            {
                $lista_plantillas_informes .= " selected";
            }
            $lista_plantillas_informes .= ">".htmlspecialchars($fila_plantilla_informe['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_plantillas_informes);
    }


    // Crea una lista desplegable para la selección de una plantilla de informe
    function dame_control_lista_plantillas_informes($id_controles, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $control_lista_plantillas_informes = "";
        $control_lista_plantillas_informes .= "<div id='etiqueta_plantilla_informe_".$id_controles."'>".$idiomas->_("Plantilla de informe").": "."</div>";
        $control_lista_plantillas_informes .= "
            <select id='id_plantilla_informe_".$id_controles."' class='chosen-select' hidden>";
        $control_lista_plantillas_informes .= dame_lista_plantillas_informes(ID_NINGUNO, $opciones_extra);
        $control_lista_plantillas_informes .= "
            </select>";

        return ($control_lista_plantillas_informes);
    }


    // Devuelve la lista de periodos de tiempo por defecto de plantillas de informe
    function dame_lista_periodos_tiempo_defecto_plantilla_informe($periodo_tiempo_seleccionado)
    {
        $periodos_tiempo_defecto_plantilla_informe = array(
            PERIODO_TIEMPO_DEFECTO_PLANTILLA_INFORME_DIA,
            PERIODO_TIEMPO_DEFECTO_PLANTILLA_INFORME_SEMANA,
            PERIODO_TIEMPO_DEFECTO_PLANTILLA_INFORME_MES,
            PERIODO_TIEMPO_DEFECTO_PLANTILLA_INFORME_ANYO);

        $lista = "";
        foreach ($periodos_tiempo_defecto_plantilla_informe as $periodo_tiempo_defecto_plantilla_informe)
        {
            $lista .= "<option value='".$periodo_tiempo_defecto_plantilla_informe."'";
            if ($periodo_tiempo_defecto_plantilla_informe == $periodo_tiempo_seleccionado)
            {
                $lista .= " selected";
            }
            $lista .= ">".PlantillaInforme::dame_descripcion_periodo_tiempo_defecto_plantilla_informe($periodo_tiempo_defecto_plantilla_informe)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de parámetros de una plantilla de informe
    function dame_lista_parametros_plantilla_informe($id_plantilla_informe, $ids_parametros_seleccionados)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_parametros = "
            SELECT
                id,
                nombre
            FROM parametros_plantillas_informes
            WHERE
                plantilla_informe = '".$bd_red->_($id_plantilla_informe)."'
            ORDER BY posicion ASC";
        $res_parametros = $bd_red->ejecuta_consulta($consulta_parametros);
        if ($res_parametros == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros."'");
        }

        $lista_parametros = "";
        $numero_parametro = 1;
        while ($fila_parametro = $res_parametros->dame_siguiente_fila())
        {
            $cadena_identificador_ordenacion = $numero_parametro;
            if ($numero_parametro < 10)
            {
                $cadena_identificador_ordenacion = "0".$cadena_identificador_ordenacion;
            }
            $lista_parametros .= "<option value='".$fila_parametro['id']."' sort_id='".$cadena_identificador_ordenacion."'";
			if (in_array($fila_parametro['id'], $ids_parametros_seleccionados) == true)
			{
				$lista_parametros .= " selected";
			}
			$lista_parametros .= ">".htmlspecialchars($fila_parametro['nombre'], ENT_QUOTES)."</option>";
        }
        return ($lista_parametros);
    }


    //
    // Funciones de información de plantillas de informes
    //


    // Devuelve el nombre de la plantilla de informe
    function dame_nombre_plantilla_informe($id_plantilla_informe)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_plantilla_informe = "
            SELECT nombre
            FROM plantillas_informes
            WHERE
                id = '".$bd_red->_($id_plantilla_informe)."'";
        $res_plantilla_informe = $bd_red->ejecuta_consulta($consulta_plantilla_informe);
        if (($res_plantilla_informe == false) || ($res_plantilla_informe->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_plantilla_informe."'");
        }
        $fila_plantilla_informe = $res_plantilla_informe->dame_siguiente_fila();
        $nombre_plantilla_informe = $fila_plantilla_informe["nombre"];
        return ($nombre_plantilla_informe);
    }


    // Devuelve la fila de la plantilla de informe
    function dame_fila_plantilla_informe($id_plantilla_informe)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_plantilla_informe = "
            SELECT *
            FROM plantillas_informes
            WHERE
                id = '".$bd_red->_($id_plantilla_informe)."'";
        $res_plantilla_informe = $bd_red->ejecuta_consulta($consulta_plantilla_informe);
        if (($res_plantilla_informe == false) || ($res_plantilla_informe->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_plantilla_informe."'");
        }
        $fila_plantilla_informe = $res_plantilla_informe->dame_siguiente_fila();
        return ($fila_plantilla_informe);
    }


    //
    // Funciones para eliminar y modificar elementos de plantillas de informes automáticamente
    //


    // Elimina y modifica los elementos de plantillas de informes no visibles de un usuario (con perfil estándar)
    function elimina_modifica_elementos_plantillas_informes_no_visibles_usuario(
        $id_usuario,
        $perfil,
        $id_red,
        $parametros_usuario)
    {
        // Se eliminan los elementos que el usuario ya no puede visualizar si no tiene el módulo correspondiente
        // Se modifican (establece los identificadores de objetos no visibles a ninguno) los elementos con objetos no visibles

        $bd_red = BaseDatosRed::dame_base_datos();

        // Módulos y secciones del usuario
        $modulos_usuario = dame_modulos_usuario($id_usuario, $perfil, $id_red);
        $secciones_usuario = dame_secciones_usuario($id_usuario, $id_red);

        // Parámetros del usuario
        $parametros_modulo_localizaciones = $parametros_usuario["parametros_modulo_localizaciones"];
        $parametros_modulo_sensores = $parametros_usuario["parametros_modulo_sensores"];
        $parametros_modulo_actuadores = $parametros_usuario["parametros_modulo_actuadores"];

        // Identificadores de elementos visibles por el usuario
        if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
        {
            $permiso_todas_localizaciones = $parametros_modulo_localizaciones["permiso_todas_localizaciones"];
            if ($permiso_todas_localizaciones == true)
            {
                $ids_localizaciones = dame_ids_localizaciones();
            }
            else
            {
                $ids_localizaciones = $parametros_modulo_localizaciones["ids_localizaciones"];
            }
            $ids_sensores_visibles_localizaciones = dame_ids_nodos_visibles_localizaciones(
                $ids_localizaciones,
                TIPO_NODO_SENSOR);
            $ids_grupos_sensores_visibles_localizaciones = dame_ids_grupos_nodos_visibles_localizaciones(
                $ids_localizaciones,
                TIPO_NODO_SENSOR);
            $ids_actuadores_visibles_localizaciones = dame_ids_nodos_visibles_localizaciones(
                $ids_localizaciones,
                TIPO_NODO_ACTUADOR);
            $ids_grupos_actuadores_visibles_localizaciones = dame_ids_grupos_nodos_visibles_localizaciones(
                $ids_localizaciones,
                TIPO_NODO_ACTUADOR);
        }
        else
        {
            $ids_sensores_visibles_localizaciones = array();
            $ids_grupos_sensores_visibles_localizaciones = array();
            $ids_actuadores_visibles_localizaciones = array();
            $ids_grupos_actuadores_visibles_localizaciones = array();
        }
        $permiso_todos_sensores = $parametros_modulo_sensores["permiso_todos_sensores"];
        $ids_sensores = $parametros_modulo_sensores["ids_sensores"];
        $ids_grupos_sensores = $parametros_modulo_sensores["ids_grupos_sensores"];
        $permiso_todos_actuadores = $parametros_modulo_actuadores["permiso_todos_actuadores"];
        $ids_actuadores = $parametros_modulo_actuadores["ids_actuadores"];
        $ids_grupos_actuadores = $parametros_modulo_actuadores["ids_grupos_actuadores"];
        $ids_todas_lineas_base_visibles_usuario = dame_ids_todas_lineas_base_visibles_usuario(
            $permiso_todos_sensores,
            $ids_sensores,
            $ids_grupos_sensores,
            $modulos_usuario,
            $ids_sensores_visibles_localizaciones);
        $ids_todos_proyectos_visibles_usuario = dame_ids_todos_proyectos_visibles_usuario(
            $permiso_todos_sensores,
            $ids_sensores,
            $ids_grupos_sensores,
            $modulos_usuario,
            $ids_sensores_visibles_localizaciones);

        // Se eliminan los elementos de plantillas de informes de varios módulos
        if (((in_array(MODULO_SENSORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_INFORMACION, $secciones_usuario[MODULO_SENSORES]) == false))) &&
            ((in_array(MODULO_ACTUADORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_ACTUADORES]) > 0) && (in_array(SECCION_ACTUADORES_INFORMACION, $secciones_usuario[MODULO_ACTUADORES]) == false))))
        {
            $operacion_borrado_elementos_comentarios = "
                DELETE elementos_plantillas_informes
                FROM
                    elementos_plantillas_informes,
                    plantillas_informes
                WHERE
                    (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                    AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')
                    AND (plantillas_informes.red = '".$bd_red->_($id_red)."')
                    AND (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS."')";
            $res_borrado_elementos_comentarios = $bd_red->ejecuta_operacion($operacion_borrado_elementos_comentarios);
            if ($res_borrado_elementos_comentarios == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_elementos_comentarios."'");
            }
        }

        // Si se tiene el módulo Sensores
        if (in_array(MODULO_SENSORES, $modulos_usuario) == true)
        {
            // 1. Se recorren cada uno de los elementos de plantillas de informes del usuario que muestran información de sensores
            // 2. Se recuperan los identificadores de sensores del elemento de plantilla de informe
            // 3. Si algún sensor no es visible por el usuario, se establece a ninguno
            $consulta_elementos_sensores = "
                SELECT elementos_plantillas_informes.*
                FROM
                    elementos_plantillas_informes,
                    plantillas_informes
                WHERE
                    (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                    AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')
                    AND (plantillas_informes.red = '".$bd_red->_($id_red)."')";
            $res_elementos_sensores = $bd_red->ejecuta_consulta($consulta_elementos_sensores);
            if ($res_elementos_sensores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_elementos_sensores."'");
            }

            while ($fila_elemento = $res_elementos_sensores->dame_siguiente_fila())
            {
                $id_elemento = $fila_elemento["id"];
                $tipo_elemento = $fila_elemento["tipo"];
                $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

                // Comprobaciones de identificadores de sensores
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
                    {
                        $ids_sensores_no_visibles_elemento = array();
                        $ids_sensores_elemento = dame_ids_sensores_elemento_plantilla_informe_sensores(
                            $tipo_elemento,
                            $parametros_tipo_elemento);
                        foreach ($ids_sensores_elemento as $id_sensor_elemento)
                        {
                            if (($id_sensor_elemento == "") || ($id_sensor_elemento == ID_NINGUNO))
                            {
                                continue;
                            }
                            $sensor_visible_usuario = false;
                            if ($sensor_visible_usuario == false)
                            {
                                if (($permiso_todos_sensores == true) ||
                                    (dame_sensor_sensores_grupos($id_sensor_elemento, $ids_sensores, $ids_grupos_sensores) == true))
                                {
                                    $sensor_visible_usuario = true;
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($id_sensor_elemento, $ids_sensores_visibles_localizaciones) == true)
                                    {
                                        $sensor_visible_usuario = true;
                                    }
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                array_push($ids_sensores_no_visibles_elemento, $id_sensor_elemento);
                            }
                        }
                        if (count($ids_sensores_no_visibles_elemento) > 0)
                        {
                            elimina_ids_sensores_elemento_plantilla_informe_sensores(
                                $id_elemento,
                                $tipo_elemento,
                                $parametros_tipo_elemento,
                                $ids_sensores_no_visibles_elemento);
                        }
                        break;
                    }
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                    {
                        $ids_sensores_no_visibles_elemento = array();
                        $ids_sensores_elemento = dame_ids_sensores_elemento_plantilla_informe_actuadores(
                            $tipo_elemento,
                            $parametros_tipo_elemento);
                        foreach ($ids_sensores_elemento as $id_sensor_elemento)
                        {
                            if (($id_sensor_elemento == "") || ($id_sensor_elemento == ID_NINGUNO))
                            {
                                continue;
                            }
                            $sensor_visible_usuario = false;
                            if ($sensor_visible_usuario == false)
                            {
                                if (($permiso_todos_sensores == true) ||
                                    (dame_sensor_sensores_grupos($id_sensor_elemento, $ids_sensores, $ids_grupos_sensores) == true))
                                {
                                    $sensor_visible_usuario = true;
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($id_sensor_elemento, $ids_sensores_visibles_localizaciones) == true)
                                    {
                                        $sensor_visible_usuario = true;
                                    }
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                array_push($ids_sensores_no_visibles_elemento, $id_sensor_elemento);
                            }
                        }
                        if (count($ids_sensores_no_visibles_elemento) > 0)
                        {
                            elimina_ids_sensores_elemento_plantilla_informe_actuadores(
                                $id_elemento,
                                $tipo_elemento,
                                $parametros_tipo_elemento,
                                $ids_sensores_no_visibles_elemento);
                        }
                        break;
                    }
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION:
                    {
                        $ids_sensores_no_visibles_elemento = array();
                        $ids_sensores_elemento = dame_ids_sensores_elemento_plantilla_informe_smartmeter(
                            $tipo_elemento,
                            $parametros_tipo_elemento);
                        foreach ($ids_sensores_elemento as $id_sensor_elemento)
                        {
                            if (($id_sensor_elemento == "") || ($id_sensor_elemento == ID_NINGUNO))
                            {
                                continue;
                            }
                            $sensor_visible_usuario = false;
                            if ($sensor_visible_usuario == false)
                            {
                                if (($permiso_todos_sensores == true) ||
                                    (dame_sensor_sensores_grupos($id_sensor_elemento, $ids_sensores, $ids_grupos_sensores) == true))
                                {
                                    $sensor_visible_usuario = true;
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($id_sensor_elemento, $ids_sensores_visibles_localizaciones) == true)
                                    {
                                        $sensor_visible_usuario = true;
                                    }
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                array_push($ids_sensores_no_visibles_elemento, $id_sensor_elemento);
                            }
                        }
                        if (count($ids_sensores_no_visibles_elemento) > 0)
                        {
                            elimina_ids_sensores_elemento_plantilla_informe_smartmeter(
                                $id_elemento,
                                $tipo_elemento,
                                $parametros_tipo_elemento,
                                $ids_sensores_no_visibles_elemento);
                        }
                        break;
                    }
                }
            }

            // 1. Se recorren cada uno de los elementos de plantillas de informes del usuario que muestran información de grupos de sensores
            // 2. Se recuperan los identificadores de grupos de sensores del elemento de plantilla de informe
            // 3. Si algún grupo de sensores no es visible por el usuario, se establece a ninguno
            $consulta_elementos_grupos_sensores = "
                SELECT elementos_plantillas_informes.*
                FROM
                    elementos_plantillas_informes,
                    plantillas_informes
                WHERE
                    (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                    AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')
                    AND (plantillas_informes.red = '".$bd_red->_($id_red)."')";
            $res_elementos_grupos_sensores = $bd_red->ejecuta_consulta($consulta_elementos_grupos_sensores);
            if ($res_elementos_grupos_sensores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_elementos_grupos_sensores."'");
            }

            while ($fila_elemento = $res_elementos_grupos_sensores->dame_siguiente_fila())
            {
                $id_elemento = $fila_elemento["id"];
                $tipo_elemento = $fila_elemento["tipo"];
                $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

                // Comprobaciones de identificadores de grupos de sensores
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                    {
                        $ids_grupos_sensores_no_visibles_elemento = array();
                        $ids_grupos_sensores_elemento = dame_ids_grupos_sensores_elemento_plantilla_informe_sensores(
                            $tipo_elemento,
                            $parametros_tipo_elemento);
                        foreach ($ids_grupos_sensores_elemento as $id_grupo_sensores_elemento)
                        {
                            if (($id_grupo_sensores_elemento != "") || ($id_grupo_sensores_elemento == ID_NINGUNO))
                            {
                                continue;
                            }
                            $grupo_sensores_visible_usuario = false;
                            if ($grupo_sensores_visible_usuario == false)
                            {
                                if (($permiso_todos_sensores == true) ||
                                    (in_array($id_grupo_sensores_elemento, $ids_grupos_sensores) == true))
                                {
                                    $grupo_sensores_visible_usuario = true;
                                }
                            }
                            if ($grupo_sensores_visible_usuario == false)
                            {
                                if (in_array($id_grupo_sensores_elemento, $ids_grupos_sensores_visibles_localizaciones) == true)
                                {
                                    $grupo_sensores_visible_usuario = true;
                                }
                            }
                            if ($grupo_sensores_visible_usuario == false)
                            {
                                array_push($ids_grupos_sensores_no_visibles_elemento, $id_grupo_sensores_elemento);
                            }
                        }
                        if (count($ids_grupos_sensores_no_visibles_elemento) > 0)
                        {
                            elimina_ids_grupos_sensores_elemento_plantilla_informe_sensores(
                                $id_elemento,
                                $tipo_elemento,
                                $parametros_tipo_elemento,
                                $ids_grupos_sensores_no_visibles_elemento);
                        }
                        break;
                    }
                }
            }
        }

        // Se eliminan los elementos de plantillas de informes de la sección eventos del módulo Sensores (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_EVENTOS, $secciones_usuario[MODULO_SENSORES]) == false)))
        {
            $operacion_borrado_elementos_sensores = "
                DELETE elementos_plantillas_informes
                FROM
                    elementos_plantillas_informes,
                    plantillas_informes
                WHERE
                    (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                    AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')
                    AND (plantillas_informes.red = '".$bd_red->_($id_red)."')
                    AND (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS."')";
            $res_borrado_elementos_sensores = $bd_red->ejecuta_operacion($operacion_borrado_elementos_sensores);
            if ($res_borrado_elementos_sensores == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_elementos_sensores."'");
            }
        }

        // Se eliminan los elementos de plantillas de informes de la sección información del módulo Sensores (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_INFORMACION, $secciones_usuario[MODULO_SENSORES]) == false)))
        {
            $operacion_borrado_elementos_sensores = "
                DELETE elementos_plantillas_informes
                FROM
                    elementos_plantillas_informes,
                    plantillas_informes
                WHERE
                    (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                    AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')
                    AND (plantillas_informes.red = '".$bd_red->_($id_red)."')
                    AND (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION."')";
            $res_borrado_elementos_sensores = $bd_red->ejecuta_operacion($operacion_borrado_elementos_sensores);
            if ($res_borrado_elementos_sensores == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_elementos_sensores."'");
            }
        }

        // Se eliminan los elementos de plantillas de informes de la sección análisis del módulo Sensores (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_ANALISIS, $secciones_usuario[MODULO_SENSORES]) == false)))
        {
            $operacion_borrado_elementos_sensores = "
                DELETE elementos_plantillas_informes
                FROM
                    elementos_plantillas_informes,
                    plantillas_informes
                WHERE
                    (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                    AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')
                    AND (plantillas_informes.red = '".$bd_red->_($id_red)."')
                    AND (
                        (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO."')
                        OR (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO."')
                        OR (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO."')
                    )";
            $res_borrado_elementos_sensores = $bd_red->ejecuta_operacion($operacion_borrado_elementos_sensores);
            if ($res_borrado_elementos_sensores == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_elementos_sensores."'");
            }
        }

        // Se eliminan los elementos de plantillas de informes de la sección comparación del módulo Sensores (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_COMPARACION, $secciones_usuario[MODULO_SENSORES]) == false)))
        {
            $operacion_borrado_elementos_sensores = "
                DELETE elementos_plantillas_informes
                FROM
                    elementos_plantillas_informes,
                    plantillas_informes
                WHERE
                    (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                    AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')
                    AND (plantillas_informes.red = '".$bd_red->_($id_red)."')
                    AND (
                        (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS."')
                        OR (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO."')
                        OR (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES."')
                        OR (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES."')
                        OR (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO."')
                        OR (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES."')
                        OR (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES."')
                    )";
            $res_borrado_elementos_sensores = $bd_red->ejecuta_operacion($operacion_borrado_elementos_sensores);
            if ($res_borrado_elementos_sensores == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_elementos_sensores."'");
            }
        }

        // Si se tiene el módulo Actuadores
        if (in_array(MODULO_ACTUADORES, $modulos_usuario) == true)
        {
            // 1. Se recorren cada uno de los elementos de plantillas de informes del usuario que muestran información de actuadores
            // 2. Se recuperan los identificadores de actuadores del elemento de plantilla de informe
            // 3. Si algún actuador no es visible por el usuario, se establece a ninguno
            $consulta_elementos_actuadores = "
                SELECT elementos_plantillas_informes.*
                FROM
                    elementos_plantillas_informes,
                    plantillas_informes
                WHERE
                    (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                    AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')
                    AND (plantillas_informes.red = '".$bd_red->_($id_red)."')";
            $res_elementos_actuadores = $bd_red->ejecuta_consulta($consulta_elementos_actuadores);
            if ($res_elementos_actuadores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_elementos_actuadores."'");
            }

            while ($fila_elemento = $res_elementos_actuadores->dame_siguiente_fila())
            {
                $id_elemento = $fila_elemento["id"];
                $tipo_elemento = $fila_elemento["tipo"];
                $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

                // Comprobaciones de identificadores de actuadores
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                    {
                        $ids_actuadores_no_visibles_elemento = array();
                        $ids_actuadores_elemento = dame_ids_actuadores_elemento_plantilla_informe_actuadores(
                            $tipo_elemento,
                            $parametros_tipo_elemento);
                        foreach ($ids_actuadores_elemento as $id_actuador_elemento)
                        {
                            if (($id_actuador_elemento == "") || ($id_actuador_elemento == ID_NINGUNO))
                            {
                                continue;
                            }
                            $actuador_visible_usuario = false;
                            if ($actuador_visible_usuario == false)
                            {
                                if (($permiso_todos_actuadores == true) ||
                                    (dame_actuador_actuadores_grupos($id_actuador_elemento, $ids_actuadores, $ids_grupos_actuadores) == true))
                                {
                                    $actuador_visible_usuario = true;
                                }
                            }
                            if ($actuador_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($id_actuador_elemento, $ids_actuadores_visibles_localizaciones) == true)
                                    {
                                        $actuador_visible_usuario = true;
                                    }
                                }
                            }
                            if ($actuador_visible_usuario == false)
                            {
                                array_push($ids_actuadores_no_visibles_elemento, $id_actuador_elemento);
                            }
                        }
                        if (count($ids_actuadores_no_visibles_elemento) > 0)
                        {
                            elimina_ids_actuadores_elemento_plantilla_informe_actuadores(
                                $id_elemento,
                                $tipo_elemento,
                                $parametros_tipo_elemento,
                                $ids_actuadores_no_visibles_elemento);
                        }
                        break;
                    }
                }
            }

            // 1. Se recorren cada uno de los elementos de plantillas de informes del usuario que muestran información de grupos de actuadores
            // 2. Se recuperan los identificadores de grupos de actuadores del elemento de plantilla de informe
            // 3. Si algún grupo de actuadores no es visible por el usuario, se establece a ninguno
            $consulta_elementos_grupos_actuadores = "
                SELECT elementos_plantillas_informes.*
                FROM
                    elementos_plantillas_informes,
                    plantillas_informes
                WHERE
                    (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                    AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')
                    AND (plantillas_informes.red = '".$bd_red->_($id_red)."')";
            $res_elementos_grupos_actuadores = $bd_red->ejecuta_consulta($consulta_elementos_grupos_actuadores);
            if ($res_elementos_grupos_actuadores == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_elementos_grupos_actuadores."'");
            }

            while ($fila_elemento = $res_elementos_grupos_actuadores->dame_siguiente_fila())
            {
                $id_elemento = $fila_elemento["id"];
                $tipo_elemento = $fila_elemento["tipo"];
                $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

                // Comprobaciones de identificadores de grupos de actuadores
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                    {
                        $ids_grupos_actuadores_no_visibles_elemento = array();
                        $ids_grupos_actuadores_elemento = dame_ids_grupos_actuadores_elemento_plantilla_informe_actuadores(
                            $tipo_elemento,
                            $parametros_tipo_elemento);
                        foreach ($ids_grupos_actuadores_elemento as $id_grupo_actuadores_elemento)
                        {
                            if (($id_grupo_actuadores_elemento != "") || ($id_grupo_actuadores_elemento == ID_NINGUNO))
                            {
                                continue;
                            }
                            $grupo_actuadores_visible_usuario = false;
                            if ($grupo_actuadores_visible_usuario == false)
                            {
                                if (($permiso_todos_actuadores == true) ||
                                    (in_array($id_grupo_actuadores_elemento, $ids_grupos_actuadores) == true))
                                {
                                    $grupo_actuadores_visible_usuario = true;
                                }
                            }
                            if ($grupo_actuadores_visible_usuario == false)
                            {
                                if (in_array($id_grupo_actuadores_elemento, $ids_grupos_actuadores_visibles_localizaciones) == true)
                                {
                                    $grupo_actuadores_visible_usuario = true;
                                }
                            }
                            if ($grupo_actuadores_visible_usuario == false)
                            {
                                array_push($ids_grupos_actuadores_no_visibles_elemento, $id_grupo_actuadores_elemento);
                            }
                        }
                        if (count($ids_grupos_sensores_no_visibles_elemento) > 0)
                        {
                            elimina_ids_grupos_actuadores_elemento_plantilla_informe_actuadores(
                                $id_elemento,
                                $tipo_elemento,
                                $parametros_tipo_elemento,
                                $ids_grupos_actuadores_no_visibles_elemento);
                        }
                        break;
                    }
                }
            }
        }

        // Se eliminan los elementos de plantillas de informes de la sección información del módulo Actuadores (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_ACTUADORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_ACTUADORES]) > 0) && (in_array(SECCION_ACTUADORES_INFORMACION, $secciones_usuario[MODULO_ACTUADORES]) == false)))
        {
            $operacion_borrado_elementos_actuadores = "
                DELETE elementos_plantillas_informes
                FROM
                    elementos_plantillas_informes,
                    plantillas_informes
                WHERE
                    (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                    AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')
                    AND (plantillas_informes.red = '".$bd_red->_($id_red)."')
                    AND (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS."')";
            $res_borrado_elementos_actuadores = $bd_red->ejecuta_operacion($operacion_borrado_elementos_actuadores);
            if ($res_borrado_elementos_actuadores == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_elementos_actuadores."'");
            }
        }

        // Se eliminan los elementos de plantillas de informes de la sección consumos y Costes del módulo SmartMeter (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_SMARTMETER, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SMARTMETER]) > 0) && (in_array(SECCION_SMARTMETER_CONSUMOS_COSTES, $secciones_usuario[MODULO_SMARTMETER]) == false)))
        {
            $operacion_borrado_elementos_smartmeter = "
                DELETE elementos_plantillas_informes
                FROM
                    elementos_plantillas_informes,
                    plantillas_informes
                WHERE
                    (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                    AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')
                    AND (plantillas_informes.red = '".$bd_red->_($id_red)."')
                    AND (
                        (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES."')
                        OR (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES."')
                        OR (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS."')
                        OR (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS."')
                        OR (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD."')
                        OR (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD."')
                        OR (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD."')
                        OR (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD."')
                        OR (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS."')
                    )";
            $res_borrado_elementos_smartmeter = $bd_red->ejecuta_operacion($operacion_borrado_elementos_smartmeter);
            if ($res_borrado_elementos_smartmeter == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_elementos_smartmeter."'");
            }
        }

        // Se eliminan los elementos de plantillas de informes de la sección facturas del módulo SmartMeter (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_SMARTMETER, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SMARTMETER]) > 0) && (in_array(SECCION_SMARTMETER_FACTURAS, $secciones_usuario[MODULO_SMARTMETER]) == false)))
        {
            $operacion_borrado_elementos_smartmeter = "
                DELETE elementos_plantillas_informes
                FROM
                    elementos_plantillas_informes,
                    plantillas_informes
                WHERE
                    (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                    AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')
                    AND (plantillas_informes.red = '".$bd_red->_($id_red)."')
                    AND (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA."')";
            $res_borrado_elementos_smartmeter = $bd_red->ejecuta_operacion($operacion_borrado_elementos_smartmeter);
            if ($res_borrado_elementos_smartmeter == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_elementos_smartmeter."'");
            }
        }

        // Se eliminan los elementos de plantillas de informes de la sección tarifas del módulo SmartMeter (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_SMARTMETER, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SMARTMETER]) > 0) && (in_array(SECCION_SMARTMETER_TARIFAS, $secciones_usuario[MODULO_SMARTMETER]) == false)))
        {
            $operacion_borrado_elementos_smartmeter = "
                DELETE elementos_plantillas_informes
                FROM
                    elementos_plantillas_informes,
                    plantillas_informes
                WHERE
                    (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                    AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')
                    AND (plantillas_informes.red = '".$bd_red->_($id_red)."')
                    AND (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION."')";
            $res_borrado_elementos_smartmeter = $bd_red->ejecuta_operacion($operacion_borrado_elementos_smartmeter);
            if ($res_borrado_elementos_smartmeter == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_elementos_smartmeter."'");
            }
        }

        // Si se tiene el módulo Proyectos
        if (in_array(MODULO_PROYECTOS, $modulos_usuario) == true)
        {
            // 1. Se recorren cada uno de los elementos de plantillas de informes del usuario que muestran información de proyectos
            // 2. Se recuperan los identificadores de proyectos del elemento de plantilla de informe
            // 3. Si algún proyecto no es visible por el usuario, se establece a ninguno
            $consulta_elementos_proyectos = "
                SELECT elementos_plantillas_informes.*
                FROM
                    elementos_plantillas_informes,
                    plantillas_informes
                WHERE
                    (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                    AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')
                    AND (plantillas_informes.red = '".$bd_red->_($id_red)."')";
            $res_elementos_proyectos = $bd_red->ejecuta_consulta($consulta_elementos_proyectos);
            if ($res_elementos_proyectos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_elementos_proyectos."'");
            }

            while ($fila_elemento = $res_elementos_proyectos->dame_siguiente_fila())
            {
                $id_elemento = $fila_elemento["id"];
                $tipo_elemento = $fila_elemento["tipo"];
                $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

                // Comprobaciones de identificadores de líneas base
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                    {
                        $ids_lineas_base_no_visibles_elemento = array();
                        $ids_lineas_base_elemento = dame_ids_lineas_base_elemento_plantilla_informe_proyectos(
                            $tipo_elemento,
                            $parametros_tipo_elemento);
                        foreach ($ids_lineas_base_elemento as $id_linea_base_elemento)
                        {
                            if (($id_linea_base_elemento != "") || ($id_linea_base_elemento == ID_NINGUNO))
                            {
                                continue;
                            }
                            $linea_base_visible_usuario = false;
                            if (in_array($id_linea_base_elemento, $ids_todas_lineas_base_visibles_usuario) == true)
                            {
                                $linea_base_visible_usuario = true;
                            }
                            if ($linea_base_visible_usuario == false)
                            {
                                array_push($ids_lineas_base_no_visibles_elemento, $id_linea_base_elemento);
                            }
                        }
                        if (count($ids_lineas_base_no_visibles_elemento) > 0)
                        {
                            elimina_ids_lineas_base_elemento_plantilla_informe_proyectos(
                                $id_elemento,
                                $tipo_elemento,
                                $parametros_tipo_elemento,
                                $ids_lineas_base_no_visibles_elemento);
                        }
                        break;
                    }
                }

                // Comprobaciones de identificadores de proyectos
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
                    {
                        $ids_proyectos_no_visibles_elemento = array();
                        $ids_proyectos_elemento = dame_ids_proyectos_elemento_plantilla_informe_proyectos(
                            $tipo_elemento,
                            $parametros_tipo_elemento);
                        foreach ($ids_proyectos_elemento as $id_proyecto_elemento)
                        {
                            if (($id_proyecto_elemento != "") || ($id_proyecto_elemento == ID_NINGUNO))
                            {
                                continue;
                            }
                            $proyecto_visible_usuario = false;
                            if (in_array($id_proyecto_elemento, $ids_todos_proyectos_visibles_usuario) == true)
                            {
                                $proyecto_visible_usuario = true;
                            }
                            if ($proyecto_visible_usuario == false)
                            {
                                array_push($ids_proyectos_no_visibles_elemento, $id_proyecto_elemento);
                            }
                        }
                        if (count($ids_proyectos_no_visibles_elemento) > 0)
                        {
                            elimina_ids_proyectos_elemento_plantilla_informe_proyectos(
                                $id_elemento,
                                $tipo_elemento,
                                $parametros_tipo_elemento,
                                $ids_proyectos_no_visibles_elemento);
                        }
                        break;
                    }
                }
            }
        }

        // Se eliminan los elementos de plantillas de informes de la sección líneas base del módulo Proyectos (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_PROYECTOS, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_PROYECTOS]) > 0) && (in_array(SECCION_PROYECTOS_LINEAS_BASE, $secciones_usuario[MODULO_PROYECTOS]) == false)))
        {
            $operacion_borrado_elementos_proyectos = "
                DELETE elementos_plantillas_informes
                FROM
                    elementos_plantillas_informes,
                    plantillas_informes
                WHERE
                    (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                    AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')
                    AND (plantillas_informes.red = '".$bd_red->_($id_red)."')
                    AND (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE."')";
            $res_borrado_elementos_proyectos = $bd_red->ejecuta_operacion($operacion_borrado_elementos_proyectos);
            if ($res_borrado_elementos_proyectos == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_elementos_proyectos."'");
            }
        }

        // Se eliminan los elementos de plantillas de informes de la sección información del módulo Proyectos (si no se tiene el módulo o la sección)
        if ((in_array(MODULO_PROYECTOS, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_PROYECTOS]) > 0) && (in_array(SECCION_PROYECTOS_INFORMACION, $secciones_usuario[MODULO_PROYECTOS]) == false)))
        {
            $operacion_borrado_elementos_proyectos = "
                DELETE elementos_plantillas_informes
                FROM
                    elementos_plantillas_informes,
                    plantillas_informes
                WHERE
                    (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                    AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')
                    AND (plantillas_informes.red = '".$bd_red->_($id_red)."')
                    AND (elementos_plantillas_informes.tipo = '".TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO."')";
            $res_borrado_elementos_proyectos = $bd_red->ejecuta_operacion($operacion_borrado_elementos_proyectos);
            if ($res_borrado_elementos_proyectos == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_elementos_proyectos."'");
            }
        }
    }


    // Modifica los elementos de plantillas de informes correspondientes al eliminar un ratio
    function modifica_elementos_plantillas_informes_ratio_eliminado($id_ratio)
    {
        // Se modifican (establece los identificadores de sensores a ninguno) los elementos con el sensor especificado
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los elementos de plantillas de informes del usuario que tienen ratio
        // 2. Se recupera el identificador de ratio del elemento de plantilla de informe
        // 3. Si el ratio coincide con el ratio especificado, se elimina de los parámetros del elemento especificado (se establece a ninguno)
        $consulta_elementos = "
            SELECT elementos_plantillas_informes.*
            FROM
                elementos_plantillas_informes,
                plantillas_informes
            WHERE
                (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                AND (plantillas_informes.red = '".$_SESSION["id_red"]."')";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if ($res_elementos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }

        while ($fila_elemento = $res_elementos->dame_siguiente_fila())
        {
            $id_elemento = $fila_elemento["id"];
            $tipo_elemento = $fila_elemento["tipo"];
            $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

            switch ($tipo_elemento)
            {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
                {
                    $id_ratio_elemento = dame_id_ratio_elemento_plantilla_informe_sensores(
                        $tipo_elemento,
                        $parametros_tipo_elemento);
                    if ($id_ratio == $id_ratio_elemento)
                    {
                        elimina_id_ratio_elemento_plantilla_informe_sensores(
                            $id_elemento,
                            $tipo_elemento,
                            $parametros_tipo_elemento,
                            $id_ratio);
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
                {
                    $id_ratio_elemento = dame_id_ratio_elemento_plantilla_informe_smartmeter(
                        $tipo_elemento,
                        $parametros_tipo_elemento);
                    if ($id_ratio == $id_ratio_elemento)
                    {
                        elimina_id_ratio_elemento_plantilla_informe_smartmeter(
                            $id_elemento,
                            $tipo_elemento,
                            $parametros_tipo_elemento,
                            $id_ratio);
                    }
                    break;
                }
            }
        }
    }


    // Modifica los elementos de plantillas de informes correspondientes al eliminar un sensor
    function modifica_elementos_plantillas_informes_sensor_eliminado($id_sensor)
    {
        // Se modifican (establece los identificadores de sensores a ninguno) los elementos con el sensor especificado
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los elementos de plantillas de informes que muestran información de sensores
        // 2. Se recuperan los identificadores de sensores del elemento de plantilla de informe
        // 3. Si algún sensor coincide con el sensor especificado, se elimina de los parámetros del elemento especificado (se establece a ninguno)
        $consulta_elementos = "
            SELECT elementos_plantillas_informes.*
            FROM
                elementos_plantillas_informes,
                plantillas_informes
            WHERE
                (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                AND (plantillas_informes.red = '".$_SESSION["id_red"]."')";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if ($res_elementos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }

        while ($fila_elemento = $res_elementos->dame_siguiente_fila())
        {
            $id_elemento = $fila_elemento["id"];
            $tipo_elemento = $fila_elemento["tipo"];
            $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

            switch ($tipo_elemento)
            {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
                {
                    $ids_sensores_elemento = dame_ids_sensores_elemento_plantilla_informe_sensores(
                        $tipo_elemento,
                        $parametros_tipo_elemento);
                    if (in_array($id_sensor, $ids_sensores_elemento) == true)
                    {
                        elimina_ids_sensores_elemento_plantilla_informe_sensores(
                            $id_elemento,
                            $tipo_elemento,
                            $parametros_tipo_elemento,
                            array($id_sensor));
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                {
                    $ids_sensores_elemento = dame_ids_sensores_elemento_plantilla_informe_actuadores(
                        $tipo_elemento,
                        $parametros_tipo_elemento);
                    if (in_array($id_sensor, $ids_sensores_elemento) == true)
                    {
                        elimina_ids_sensores_elemento_plantilla_informe_actuadores(
                            $id_elemento,
                            $tipo_elemento,
                            $parametros_tipo_elemento,
                            array($id_sensor));
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION:
                {
                    $ids_sensores_elemento = dame_ids_sensores_elemento_plantilla_informe_smartmeter(
                        $tipo_elemento,
                        $parametros_tipo_elemento);
                    if (in_array($id_sensor, $ids_sensores_elemento) == true)
                    {
                        elimina_ids_sensores_elemento_plantilla_informe_smartmeter(
                            $id_elemento,
                            $tipo_elemento,
                            $parametros_tipo_elemento,
                            array($id_sensor));
                    }
                    break;
                }
            }
        }
    }


    // Modifica los elementos de plantillas de informes correspondientes al eliminar un grupo de sensores
    function modifica_elementos_plantillas_informes_grupo_sensores_eliminado($id_grupo_sensores)
    {
        // Se modifican (establece los identificadores de grupos de sensores a ninguno) los elementos con el grupo de sensores especificado
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los elementos de plantillas de informes que muestran información de grupos de sensores
        // 2. Se recuperan los identificadores de grupos de sensores del elemento de plantilla de informe
        // 3. Si algún sensor coincide con el grupo de sensores especificado, se elimina de los parámetros del elemento especificado (se establece a ninguno)
        $consulta_elementos = "
            SELECT elementos_plantillas_informes.*
            FROM
                elementos_plantillas_informes,
                plantillas_informes
            WHERE
                (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                AND (plantillas_informes.red = '".$_SESSION["id_red"]."')";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if ($res_elementos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }

        while ($fila_elemento = $res_elementos->dame_siguiente_fila())
        {
            $id_elemento = $fila_elemento["id"];
            $tipo_elemento = $fila_elemento["tipo"];
            $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

            switch ($tipo_elemento)
            {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                {
                    $ids_grupos_sensores_elemento = dame_ids_grupos_sensores_elemento_plantilla_informe_sensores(
                        $tipo_elemento,
                        $parametros_tipo_elemento);
                    if (in_array($id_grupo_sensores, $ids_grupos_sensores_elemento) == true)
                    {
                        elimina_ids_grupos_sensores_elemento_plantilla_informe_sensores(
                            $id_elemento,
                            $tipo_elemento,
                            $parametros_tipo_elemento,
                            array($id_grupo_sensores));
                    }
                    break;
                }
            }
        }
    }


    // Modifica los elementos de plantillas de informes correspondientes al eliminar un evento
    function modifica_elementos_plantillas_informes_evento_eliminado($id_evento)
    {
        // Se modifican (establece los identificadores de sensores a ninguno) los elementos con el sensor especificado
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los elementos de plantillas de informes que muestran información de eventos
        // 2. Se recuperan los identificadores de eventos del elemento de plantilla de informe
        // 3. Si algún evento coincide con el evento especificado, se elimina de los parámetros del elemento especificado (se establece a ninguno)
        $consulta_elementos = "
            SELECT elementos_plantillas_informes.*
            FROM
                elementos_plantillas_informes,
                plantillas_informes
            WHERE
                (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                AND (plantillas_informes.red = '".$_SESSION["id_red"]."')";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if ($res_elementos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }

        while ($fila_elemento = $res_elementos->dame_siguiente_fila())
        {
            $id_elemento = $fila_elemento["id"];
            $tipo_elemento = $fila_elemento["tipo"];
            $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

            switch ($tipo_elemento)
            {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                {
                    $ids_eventos_elemento = dame_ids_eventos_elemento_plantilla_informe_sensores(
                        $tipo_elemento,
                        $parametros_tipo_elemento);
                    if (in_array($id_evento, $ids_eventos_elemento) == true)
                    {
                        elimina_ids_eventos_elemento_plantilla_informe_sensores(
                            $id_elemento,
                            $tipo_elemento,
                            $parametros_tipo_elemento,
                            array($id_evento));
                    }
                    break;
                }
            }
        }
    }


    // Modifica los elementos de plantillas de informes correspondientes al eliminar un actuador
    function modifica_elementos_plantillas_informes_actuador_eliminado($id_actuador)
    {
        // Se modifican (establece los identificadores de actuadores a ninguno) los elementos con el actuador especificado
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los elementos de plantillas de informes que muestran información de actuadores
        // 2. Se recuperan los identificadores de actuadores del elemento de plantilla de informe
        // 3. Si algún actuador coincide con el actuador especificado, se elimina de los parámetros del elemento especificado (se establece a ninguno)
        $consulta_elementos = "
            SELECT elementos_plantillas_informes.*
            FROM
                elementos_plantillas_informes,
                plantillas_informes
            WHERE
                (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                AND (plantillas_informes.red = '".$_SESSION["id_red"]."')";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if ($res_elementos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }

        while ($fila_elemento = $res_elementos->dame_siguiente_fila())
        {
            $id_elemento = $fila_elemento["id"];
            $tipo_elemento = $fila_elemento["tipo"];
            $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

            switch ($tipo_elemento)
            {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                {
                    $ids_actuadores_elemento = dame_ids_actuadores_elemento_plantilla_informe_actuadores(
                        $tipo_elemento,
                        $parametros_tipo_elemento);
                    if (in_array($id_actuador, $ids_actuadores_elemento) == true)
                    {
                        elimina_ids_actuadores_elemento_plantilla_informe_actuadores(
                            $id_elemento,
                            $tipo_elemento,
                            $parametros_tipo_elemento,
                            array($id_actuador));
                    }
                    break;
                }
            }
        }
    }


    // Modifica los elementos de plantillas de informes correspondientes al eliminar un grupo de actuadores
    function modifica_elementos_plantillas_informes_grupo_actuadores_eliminado($id_grupo_actuadores)
    {
        // Se modifican (establece los identificadores de sensores a ninguno) los elementos con el grupo de actuadores especificado
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los elementos de plantillas de informes que muestran información de grupos de actuadores
        // 2. Se recuperan los identificadores de grupos de actuadores del elemento de plantilla de informe
        // 3. Si algún grupo de actuadores coincide con el grupo especificado, se elimina de los parámetros del elemento especificado (se establece a ninguno)
        $consulta_elementos = "
            SELECT elementos_plantillas_informes.*
            FROM
                elementos_plantillas_informes,
                plantillas_informes
            WHERE
                (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                AND (plantillas_informes.red = '".$_SESSION["id_red"]."')";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if ($res_elementos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }

        while ($fila_elemento = $res_elementos->dame_siguiente_fila())
        {
            $id_elemento = $fila_elemento["id"];
            $tipo_elemento = $fila_elemento["tipo"];
            $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

            switch ($tipo_elemento)
            {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                {
                    $ids_grupos_actuadores_elemento = dame_ids_grupos_actuadores_elemento_plantilla_informe_actuadores(
                        $tipo_elemento,
                        $parametros_tipo_elemento);
                    if (in_array($id_grupo_actuadores, $ids_grupos_actuadores_elemento) == true)
                    {
                        elimina_ids_grupos_actuadores_elemento_plantilla_informe_actuadores(
                            $id_elemento,
                            $tipo_elemento,
                            $parametros_tipo_elemento,
                            array($id_grupo_actuadores));
                    }
                    break;
                }
            }
        }
    }


    // Modifica los elementos de plantillas de informes correspondientes al eliminar una tarifa
    function modifica_elementos_plantillas_informes_tarifa_eliminada($medicion, $id_tarifa)
    {
        // Se modifican (establece los identificadores de proyectos a ninguno) los elementos con el proyecto especificado
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los elementos de plantillas de informes que muestran información de tarifas
        // 2. Se recuperan los identificadores de tarifas del elemento de plantilla de informe
        // 3. Si alguna tarifa coincide con la tarifa especificada, se elimina de los parámetros del elemento especificado (se establece a ninguna)
        $consulta_elementos = "
            SELECT elementos_plantillas_informes.*
            FROM
                elementos_plantillas_informes,
                plantillas_informes
            WHERE
                (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                AND (plantillas_informes.red = '".$_SESSION["id_red"]."')";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if ($res_elementos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }

        while ($fila_elemento = $res_elementos->dame_siguiente_fila())
        {
            $id_elemento = $fila_elemento["id"];
            $tipo_elemento = $fila_elemento["tipo"];
            $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

            switch ($tipo_elemento)
            {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
                {
                    $ids_tarifas_elemento = dame_ids_tarifas_elemento_plantilla_informe_smartmeter(
                        $medicion,
                        $tipo_elemento,
                        $parametros_tipo_elemento);
                    if (in_array($id_tarifa, $ids_tarifas_elemento) == true)
                    {
                        elimina_ids_tarifas_elemento_plantilla_informe_smartmeter(
                            $id_elemento,
                            $tipo_elemento,
                            $parametros_tipo_elemento,
                            array($id_tarifa));
                    }
                    break;
                }
            }
        }
    }


    // Modifica los elementos de plantillas de informes correspondientes al eliminar una línea base
    function modifica_elementos_plantillas_informes_linea_base_eliminada($id_linea_base)
    {
        // Se modifican (establece los identificadores de líneas base a ninguno) los elementos con la línea base especificada
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los elementos de plantillas de informes que muestran información de líneas base
        // 2. Se recuperan los identificadores de líneas base del elemento de plantilla de informe
        // 3. Si alguna línea base coincide con la línea base especificada, se elimina de los parámetros del elemento especificado (se establece a ninguno)
        $consulta_elementos = "
            SELECT elementos_plantillas_informes.*
            FROM
                elementos_plantillas_informes,
                plantillas_informes
            WHERE
                (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                AND (plantillas_informes.red = '".$_SESSION["id_red"]."')";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if ($res_elementos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }

        while ($fila_elemento = $res_elementos->dame_siguiente_fila())
        {
            $id_elemento = $fila_elemento["id"];
            $tipo_elemento = $fila_elemento["tipo"];
            $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

            switch ($tipo_elemento)
            {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                {
                    $ids_lineas_base_elemento = dame_ids_lineas_base_elemento_plantilla_informe_proyectos(
                        $tipo_elemento,
                        $parametros_tipo_elemento);
                    if (in_array($id_linea_base, $ids_lineas_base_elemento) == true)
                    {
                        elimina_ids_lineas_base_elemento_plantilla_informe_proyectos(
                            $id_elemento,
                            $tipo_elemento,
                            $parametros_tipo_elemento,
                            array($id_linea_base));
                    }
                    break;
                }
            }
        }
    }


    // Modifica los elementos de plantillas de informes correspondientes al eliminar un proyecto
    function modifica_elementos_plantillas_informes_proyecto_eliminado($id_proyecto)
    {
        // Se modifican (establece los identificadores de proyectos a ninguno) los elementos con el proyecto especificado
        $bd_red = BaseDatosRed::dame_base_datos();

        // 1. Se recorren cada uno de los elementos de plantillas de informes que muestran información de proyectos
        // 2. Se recuperan los identificadores de proyectos del elemento de plantilla de informe
        // 3. Si algún proyecto coincide con el proyecto especificado, se elimina de los parámetros del elemento especificado (se establece a ninguno)
        $consulta_elementos = "
            SELECT elementos_plantillas_informes.*
            FROM
                elementos_plantillas_informes,
                plantillas_informes
            WHERE
                (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                AND (plantillas_informes.red = '".$_SESSION["id_red"]."')";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if ($res_elementos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }

        while ($fila_elemento = $res_elementos->dame_siguiente_fila())
        {
            $id_elemento = $fila_elemento["id"];
            $tipo_elemento = $fila_elemento["tipo"];
            $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

            switch ($tipo_elemento)
            {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
                {
                    $ids_proyectos_elemento = dame_ids_proyectos_elemento_plantilla_informe_proyectos(
                        $tipo_elemento,
                        $parametros_tipo_elemento);
                    if (in_array($id_proyecto, $ids_proyectos_elemento) == true)
                    {
                        elimina_ids_proyectos_elemento_plantilla_informe_proyectos(
                            $id_elemento,
                            $tipo_elemento,
                            $parametros_tipo_elemento,
                            array($id_proyecto));
                    }
                    break;
                }
            }
        }
    }


    //
    // Funciones para duplicados de plantillas de informes
    //


    // Duplica los parámetros de la plantilla de informe anterior
    function duplica_parametros_plantilla_informe_anterior(
        $id_red_plantilla_informe,
        $id_plantilla_informe_anterior,
        $id_plantilla_informe,
        &$ids_parametros_plantilla_informe_anterior,
        &$ids_parametros_plantilla_informe)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se guardan los identificadores de los parámetros
        $ids_parametros_plantilla_informe_anterior = array();
        $ids_parametros_plantilla_informe = array();

        // Se recorren los parametros de la plantilla informe anterior (origen de la actual), se cambia la plantilla de informe y se añaden
        $consulta_parametros = "
            SELECT *
            FROM parametros_plantillas_informes
            WHERE
                plantilla_informe = '".$bd_red->_($id_plantilla_informe_anterior)."'";
        $res_parametros = $bd_red->ejecuta_consulta($consulta_parametros);
        if ($res_parametros == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros."'");
        }

        while ($fila_parametro = $res_parametros->dame_siguiente_fila())
        {
            $operacion_insercion_parametro = "
                INSERT INTO parametros_plantillas_informes (
                    nombre,
                    red,
                    plantilla_informe,
                    posicion,
                    tipo,
                    parametros_tipo
                ) VALUES (
                    '".$bd_red->_($fila_parametro["nombre"])."',
                    '".$bd_red->_($fila_parametro["$id_red_plantilla_informe"])."',
                    '".$bd_red->_($id_plantilla_informe)."',
                    '".$bd_red->_($fila_parametro["posicion"])."',
                    '".$bd_red->_($fila_parametro["tipo"])."',
                    '".$bd_red->_($fila_parametro["parametros_tipo"])."'
                )";
            $res_insercion_parametro = $bd_red->ejecuta_operacion($operacion_insercion_parametro);
            if ($res_insercion_parametro == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_parametro."'");
            }

            // Identificadores de parámetros
            array_push($ids_parametros_plantilla_informe_anterior, $fila_parametro["id"]);
            $id_parametro = $bd_red->dame_id_autoincremental_ultima_insercion();
            array_push($ids_parametros_plantilla_informe, $id_parametro);
        }
    }


    // Duplica los elementos de la plantilla de informe anterior
    function duplica_elementos_plantilla_informe_anterior(
        $id_red_plantilla_informe,
        $id_plantilla_informe_anterior,
        $id_plantilla_informe,
        $ids_parametros_plantilla_informe_anterior,
        $ids_parametros_plantilla_informe,
        &$ids_elementos_plantilla_informe_anterior,
        &$ids_elementos_plantilla_informe)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se guardan los identificadores de los elementos
        $ids_elementos_plantilla_informe_anterior = array();
        $ids_elementos_plantilla_informe = array();

        // Se recorren los elementos de la plantilla informe anterior (origen de la actual), se cambia la plantilla de informe y se añaden
        $consulta_elementos = "
            SELECT *
            FROM elementos_plantillas_informes
            WHERE
                plantilla_informe = '".$bd_red->_($id_plantilla_informe_anterior)."'";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if ($res_elementos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }

        while ($fila_elemento = $res_elementos->dame_siguiente_fila())
        {
            // Tipo del elemento y parámetros
            $tipo_elemento = $fila_elemento["tipo"];
            $cadena_parametros_tipo_elemento = $fila_elemento["parametros_tipo"];
            $cadena_parametros_requeridos_elemento = $fila_elemento["parametros_requeridos"];

            // Se sustituyen los identificadores de los parámetros de la plantilla anterior por los de la nueva plantilla
            sustituye_identificadores_parametros_elemento_plantilla_anterior(
                $tipo_elemento,
                $cadena_parametros_tipo_elemento,
                $cadena_parametros_requeridos_elemento,
                $ids_parametros_plantilla_informe_anterior,
                $ids_parametros_plantilla_informe);

            $operacion_insercion_elemento = "
                INSERT INTO elementos_plantillas_informes (
                    nombre,
                    red,
                    plantilla_informe,
                    posicion,
                    tipo,
                    parametros_tipo,
                    parametros_tipo_json,
                    elementos_informe,
                    modo_visibilidad,
                    parametros_requeridos
                ) VALUES (
                    '".$bd_red->_($fila_elemento["nombre"])."',
                    '".$bd_red->_($id_red_plantilla_informe)."',
                    '".$bd_red->_($id_plantilla_informe)."',
                    '".$bd_red->_($fila_elemento["posicion"])."',
                    '".$bd_red->_($fila_elemento["tipo"])."',
                    '".$bd_red->_($cadena_parametros_tipo_elemento)."',
                    '".$bd_red->_($fila_elemento["parametros_tipo_json"])."',
                    '".$bd_red->_($fila_elemento["elementos_informe"])."',
                    '".$bd_red->_($fila_elemento["modo_visibilidad"])."',
                    '".$bd_red->_($cadena_parametros_requeridos_elemento)."'
                )";
            $res_insercion_elemento = $bd_red->ejecuta_operacion($operacion_insercion_elemento);
            if ($res_insercion_elemento == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_elemento."'");
            }

            // Identificadores de elementos
            $id_elemento_anterior = $fila_elemento["id"];
            $id_elemento = $bd_red->dame_id_autoincremental_ultima_insercion();

            // Se duplican las imágenes de los elementos con imágenes
            switch ($tipo_elemento)
            {
                case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN:
                {
                    $id_origen_anterior = implode(SEPARADOR_PARAMETROS_SIMPLES, array($id_plantilla_informe_anterior, $id_elemento_anterior));
                    $id_origen = implode(SEPARADOR_PARAMETROS_SIMPLES, array($id_plantilla_informe, $id_elemento));
                    $res_duplicar_imagen = duplica_imagen_base_datos(ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN, $id_origen_anterior, $id_origen);
                    if ($res_duplicar_imagen["res"] == "ERROR")
                    {
                        throw new Exception($res_duplicar_imagen["msg"]);
                    }
                    break;
                }
            }

            // Identificadores de elementos
            array_push($ids_elementos_plantilla_informe_anterior, $id_elemento_anterior);
            array_push($ids_elementos_plantilla_informe, $id_elemento);
        }
    }


    // Sustituye los identificadores de los parámetros de la plantilla anterior por los de la nueva plantilla
    function sustituye_identificadores_parametros_elemento_plantilla_anterior(
        $tipo_elemento,
        &$cadena_parametros_tipo_elemento,
        &$cadena_parametros_requeridos_elemento,
        $ids_parametros_plantilla_informe_anterior,
        $ids_parametros_plantilla_informe)
    {
        // Se guardan los identificadores de parámetros
        $ids_parametros_plantillas_informes = array();
        for ($i = 0; $i < count($ids_parametros_plantilla_informe_anterior); $i++)
        {
            $id_parametro_plantilla_informe_anterior = $ids_parametros_plantilla_informe_anterior[$i];
            $id_parametro_plantilla_informe = $ids_parametros_plantilla_informe[$i];
            $ids_parametros_plantillas_informes[$id_parametro_plantilla_informe_anterior] = $id_parametro_plantilla_informe;
        }

        // Se modifican los parámetros de tipo del elemento
        $parametros_tipo_elemento = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo_elemento);
        switch ($tipo_elemento)
        {
            // Elementos de varios módulos
            case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
            {
                $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $cadena_ids_parametros_anteriores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_SENSORES];
                    $ids_parametros_anteriores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros_anteriores);
                    $ids_parametros = array();
                    foreach ($ids_parametros_anteriores as $id_parametro_anterior)
                    {
                        $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                        array_push($ids_parametros, $id_parametro);
                    }
                    $cadena_ids_parametros = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros);
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_SENSORES] = $cadena_ids_parametros;
                }
                $tipo_seleccion_actuadores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_ACTUADORES];
                if ($tipo_seleccion_actuadores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $cadena_ids_parametros_anteriores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_ACTUADORES];
                    $ids_parametros_anteriores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros_anteriores);
                    $ids_parametros = array();
                    foreach ($ids_parametros_anteriores as $id_parametro_anterior)
                    {
                        $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                        array_push($ids_parametros, $id_parametro);
                    }
                    $cadena_ids_parametros = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros);
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_ACTUADORES] = $cadena_ids_parametros;
                }
                $tipo_seleccion_grupos_actuadores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_GRUPOS_ACTUADORES];
                if ($tipo_seleccion_grupos_actuadores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $cadena_ids_parametros_anteriores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_GRUPOS_ACTUADORES];
                    $ids_parametros_anteriores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros_anteriores);
                    $ids_parametros = array();
                    foreach ($ids_parametros_anteriores as $id_parametro_anterior)
                    {
                        $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                        array_push($ids_parametros, $id_parametro);
                    }
                    $cadena_ids_parametros = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros);
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_GRUPOS_ACTUADORES] = $cadena_ids_parametros;
                }
                break;
            }
            // Elementos de sensores (Eventos)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            {
                $tipo_seleccion_origen_evento = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_TIPO_SELECCION_ORIGEN_EVENTO];
                if ($tipo_seleccion_origen_evento == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_ID_ORIGEN_EVENTO];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_ID_ORIGEN_EVENTO] = $id_parametro;
                }
                break;
            }
            // Elementos de sensores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_ID_SENSOR];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_ID_SENSOR] = $id_parametro;
                }
                break;
            }
            // Elementos de sensores (Análisis)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_ID_SENSOR];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_ID_SENSOR] = $id_parametro;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_ID_SENSOR];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_ID_SENSOR] = $id_parametro;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
            {
                $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $cadena_ids_parametros_anteriores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_IDS_SENSORES];
                    $ids_parametros_anteriores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros_anteriores);
                    $ids_parametros = array();
                    foreach ($ids_parametros_anteriores as $id_parametro_anterior)
                    {
                        $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                        array_push($ids_parametros, $id_parametro);
                    }
                    $cadena_ids_parametros = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros);
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_IDS_SENSORES] = $cadena_ids_parametros;
                }
                break;
            }
            // Elementos de sensores (Comparación)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_ID_SENSOR];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_ID_SENSOR] = $id_parametro;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_ID_SENSOR];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_ID_SENSOR] = $id_parametro;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            {
                $tipo_seleccion_sensor_principal = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TIPO_SELECCION_SENSOR_PRINCIPAL];
                if ($tipo_seleccion_sensor_principal == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_ID_SENSOR_PRINCIPAL];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_ID_SENSOR_PRINCIPAL] = $id_parametro;
                }
                $tipo_seleccion_sensores_secundarios = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TIPO_SELECCION_SENSORES_SECUNDARIOS];
                if ($tipo_seleccion_sensores_secundarios == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $cadena_ids_parametros_anteriores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_IDS_SENSORES_SECUNDARIOS];
                    $ids_parametros_anteriores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros_anteriores);
                    $ids_parametros = array();
                    foreach ($ids_parametros_anteriores as $id_parametro_anterior)
                    {
                        $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                        array_push($ids_parametros, $id_parametro);
                    }
                    $cadena_ids_parametros = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros);
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_IDS_SENSORES_SECUNDARIOS] = $cadena_ids_parametros;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
            {
                $cadena_tipos_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_TIPOS_SELECCION_SENSORES];
                $tipos_seleccion_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_tipos_seleccion_sensores);
                $cadena_ids_parametros_anteriores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_IDS_SENSORES];
                $ids_parametros_anteriores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros_anteriores);
                $ids_parametros = array();
                for ($i = 0; $i < count($tipos_seleccion_sensores); $i++)
                {
                    $tipo_seleccion_sensor = $tipos_seleccion_sensores[$i];
                    switch ($tipo_seleccion_sensor)
                    {
                        case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO:
                        {
                            $id_parametro = $ids_parametros_anteriores[$i];
                            break;
                        }
                        case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE:
                        {
                            $id_parametro_anterior = $ids_parametros_anteriores[$i];
                            $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                            break;
                        }
                    }
                    array_push($ids_parametros, $id_parametro);
                }
                $cadena_ids_parametros = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros);
                $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_IDS_SENSORES] = $cadena_ids_parametros;
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
            {
                $tipo_seleccion_sensores_agregados = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_TIPO_SELECCION_SENSORES_AGREGADOS];
                if ($tipo_seleccion_sensores_agregados == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $cadena_ids_parametros_anteriores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_IDS_SENSORES_AGREGADOS];
                    $ids_parametros_anteriores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros_anteriores);
                    $ids_parametros = array();
                    foreach ($ids_parametros_anteriores as $id_parametro_anterior)
                    {
                        $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                        array_push($ids_parametros, $id_parametro);
                    }
                    $cadena_ids_parametros = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros);
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_IDS_SENSORES_AGREGADOS] = $cadena_ids_parametros;
                }
                $tipo_seleccion_sensor_destacado = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_TIPO_SELECCION_SENSOR_DESTACADO];
                if ($tipo_seleccion_sensor_destacado == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_ID_SENSOR_DESTACADO];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_ID_SENSOR_DESTACADO] = $id_parametro;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
            {
                $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $cadena_ids_parametros_anteriores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_IDS_SENSORES];
                    $ids_parametros_anteriores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros_anteriores);
                    $ids_parametros = array();
                    foreach ($ids_parametros_anteriores as $id_parametro_anterior)
                    {
                        $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                        array_push($ids_parametros, $id_parametro);
                    }
                    $cadena_ids_parametros = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros);
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_IDS_SENSORES] = $cadena_ids_parametros;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
            {
                $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $cadena_ids_parametros_anteriores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_IDS_SENSORES];
                    $ids_parametros_anteriores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros_anteriores);
                    $ids_parametros = array();
                    foreach ($ids_parametros_anteriores as $id_parametro_anterior)
                    {
                        $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                        array_push($ids_parametros, $id_parametro);
                    }
                    $cadena_ids_parametros = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros);
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_IDS_SENSORES] = $cadena_ids_parametros;
                }
                break;
            }
            // Elementos de sensores (Estadística)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_ID_SENSOR];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_ID_SENSOR] = $id_parametro;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
            {
                $tipo_seleccion_sensor_dependiente = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_TIPO_SELECCION_SENSOR_DEPENDIENTE];
                if ($tipo_seleccion_sensor_dependiente == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_ID_SENSOR_DEPENDIENTE];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_ID_SENSOR_DEPENDIENTE] = $id_parametro;
                }
                $cadena_tipos_seleccion_sensores_independientes = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_TIPOS_SELECCION_SENSORES_INDEPENDIENTES];
                $tipos_seleccion_sensores_independientes = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_tipos_seleccion_sensores_independientes);
                $cadena_ids_parametros_anteriores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_IDS_SENSORES_INDEPENDIENTES];
                $ids_parametros_anteriores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros_anteriores);
                $ids_parametros = array();
                for ($i = 0; $i < count($tipos_seleccion_sensores_independientes); $i++)
                {
                    $tipo_seleccion_sensor_independiente = $tipos_seleccion_sensores_independientes[$i];
                    switch ($tipo_seleccion_sensor_independiente)
                    {
                        case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO:
                        {
                            $id_parametro = $ids_parametros_anteriores[$i];
                            break;
                        }
                        case TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE:
                        {
                            $id_parametro_anterior = $ids_parametros_anteriores[$i];
                            $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                            break;
                        }
                    }
                    array_push($ids_parametros, $id_parametro);
                }
                $cadena_ids_parametros = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros);
                $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_IDS_SENSORES_INDEPENDIENTES] = $cadena_ids_parametros;
                break;
            }
            // Elementos de actuadores (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                $tipo_seleccion_destino_accion = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TIPO_SELECCION_DESTINO_ACCION];
                if ($tipo_seleccion_destino_accion == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_DESTINO_ACCION];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_DESTINO_ACCION] = $id_parametro;
                }
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_SENSOR];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_ID_SENSOR] = $id_parametro;
                }
                break;
            }
            // Elementos de SmartMeter (Consumos y costes)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            {
                $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $cadena_ids_parametros_anteriores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_IDS_SENSORES];
                    $ids_parametros_anteriores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros_anteriores);
                    $ids_parametros = array();
                    foreach ($ids_parametros_anteriores as $id_parametro_anterior)
                    {
                        $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                        array_push($ids_parametros, $id_parametro);
                    }
                    $cadena_ids_parametros = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros);
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_IDS_SENSORES] = $cadena_ids_parametros;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
            {
                $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_TIPO_SELECCION_SENSORES];
                if ($tipo_seleccion_sensores == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $cadena_ids_parametros_anteriores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_IDS_SENSORES];
                    $ids_parametros_anteriores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros_anteriores);
                    $ids_parametros = array();
                    foreach ($ids_parametros_anteriores as $id_parametro_anterior)
                    {
                        $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                        array_push($ids_parametros, $id_parametro);
                    }
                    $cadena_ids_parametros = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros);
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_IDS_SENSORES] = $cadena_ids_parametros;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_ID_SENSOR];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_ID_SENSOR] = $id_parametro;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_ID_SENSOR];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS_ID_SENSOR] = $id_parametro;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_ID_SENSOR];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_ID_SENSOR] = $id_parametro;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_ID_SENSOR];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_ID_SENSOR] = $id_parametro;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ID_SENSOR];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_ID_SENSOR] = $id_parametro;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ID_SENSOR];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD_ID_SENSOR] = $id_parametro;
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_ID_SENSOR];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_ID_SENSOR] = $id_parametro;
                }
                break;
            }
            // Elementos de SmartMeter (Facturas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_ID_SENSOR];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_ID_SENSOR] = $id_parametro;
                }
                $tipo_seleccion_sensores_reparto_costes = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_TIPO_SELECCION_SENSORES_REPARTO_COSTES];
                if ($tipo_seleccion_sensores_reparto_costes == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $cadena_ids_parametros_anteriores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_IDS_SENSORES_REPARTO_COSTES];
                    $ids_parametros_anteriores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros_anteriores);
                    $ids_parametros = array();
                    foreach ($ids_parametros_anteriores as $id_parametro_anterior)
                    {
                        $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                        array_push($ids_parametros, $id_parametro);
                    }
                    $cadena_ids_parametros = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros);
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_IDS_SENSORES_REPARTO_COSTES] = $cadena_ids_parametros;
                }
                break;
            }
            // Elementos de SmartMeter (Tarifas)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION:
            {
                $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION_TIPO_SELECCION_SENSOR];
                if ($tipo_seleccion_sensor == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION_ID_SENSOR];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION_ID_SENSOR] = $id_parametro;
                }
                break;
            }
            // Elementos de proyectos (Líneas base)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
            {
                $tipo_seleccion_linea_base = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TIPO_SELECCION_LINEA_BASE];
                if ($tipo_seleccion_linea_base == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ID_LINEA_BASE];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_ID_LINEA_BASE] = $id_parametro;
                }
                break;
            }
            // Elementos de proyectos (Información)
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
            {
                $tipo_seleccion_proyecto = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO_TIPO_SELECCION_PROYECTO];
                if ($tipo_seleccion_proyecto == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_CONFIGURABLE)
                {
                    $id_parametro_anterior = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO_ID_PROYECTO];
                    $id_parametro = $ids_parametros_plantillas_informes[$id_parametro_anterior];
                    $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO_ID_PROYECTO] = $id_parametro;
                }
                break;
            }
        }
        $cadena_parametros_tipo_elemento = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo_elemento);

        // Se modifican los parámetros requeridos del elemento
        $ids_parametros_requeridos_elemento = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_parametros_requeridos_elemento);
        for ($i = 0; $i < count($ids_parametros_requeridos_elemento); $i++)
        {
            $id_parametro_requerido_anterior = $ids_parametros_requeridos_elemento[$i];
            $id_parametro_requerido = $ids_parametros_plantillas_informes[$id_parametro_requerido_anterior];
            $ids_parametros_requeridos_elemento[$i] = $id_parametro_requerido;
        }
        $cadena_parametros_requeridos_elemento = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros_requeridos_elemento);
    }


    // Devuelve si todos los elementos de la plantilla de informe son visibles por el usuario
    function dame_elementos_plantilla_informe_visibles_red_usuario(
        $id_plantilla_informe,
        $id_red,
        $id_usuario,
        $perfil,
        &$nombre_primer_elemento_no_visible)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Módulos y secciones del usuario
        $modulos_usuario = dame_modulos_usuario($id_usuario, $perfil, $id_red);
        if ($perfil == PERFIL_USUARIO_ESTANDAR)
        {
            $secciones_usuario = dame_secciones_usuario($id_usuario, $id_red);
        }
        else
        {
            $secciones_usuario = NULL;
        }

        // Se recupera la información de los elementos
        $consulta_elementos = "
            SELECT
                nombre,
                tipo
            FROM elementos_plantillas_informes
            WHERE
                plantilla_informe = '".$bd_red->_($id_plantilla_informe)."'";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if ($res_elementos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }

        // Se recorren los elementos
        $elementos_plantilla_informe_visibles = true;
        while (($elementos_plantilla_informe_visibles == true) && ($fila_elemento = $res_elementos->dame_siguiente_fila()))
        {
            $nombre_elemento = $fila_elemento["nombre"];
            $tipo_elemento = $fila_elemento["tipo"];

            $elemento_varios_modulos = false;
            switch ($tipo_elemento)
            {
                // Elementos generales
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_PAGINA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_LINEA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_NOTAS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN:
                {
                    $modulo_elemento = NULL;
                }
                // Elementos de varios módulos
                case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
                {
                    $modulos_elemento = array(MODULO_SENSORES, MODULO_ACTUADORES);
                    $secciones_elemento = array(SECCION_SENSORES_INFORMACION, SECCION_ACTUADORES_INFORMACION);
                    break;
                }
                // Elementos de sensores (Eventos)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                {
                    $modulo_elemento = MODULO_SENSORES;
                    $seccion_elemento = SECCION_SENSORES_EVENTOS;
                    break;
                }
                // Elementos de sensores (Información)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
                {
                    $modulo_elemento = MODULO_SENSORES;
                    $seccion_elemento = SECCION_SENSORES_INFORMACION;
                    break;
                }
                // Elementos de sensores (Análisis)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
                {
                    $modulo_elemento = MODULO_SENSORES;
                    $seccion_elemento = SECCION_SENSORES_ANALISIS;
                    break;
                }
                // Elementos de sensores (Comparación)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
                {
                    $modulo_elemento = MODULO_SENSORES;
                    $seccion_elemento = SECCION_SENSORES_COMPARACION;
                    break;
                }
                // Elementos de sensores (Estadística)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
                {
                    $modulo_elemento = MODULO_SENSORES;
                    $seccion_elemento = SECCION_SENSORES_ESTADISTICA;
                    break;
                }
                // Elementos de actuadores (Información)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                {
                    $modulo_elemento = MODULO_ACTUADORES;
                    $seccion_elemento = SECCION_ACTUADORES_INFORMACION;
                    break;
                }
                // Elementos de SmartMeter (Consumos y costes)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
                {
                    $modulo_elemento = MODULO_SMARTMETER;
                    $seccion_elemento = SECCION_SMARTMETER_CONSUMOS_COSTES;
                    break;
                }
                // Elementos de SmartMeter (Facturas)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
                {
                    $modulo_elemento = MODULO_SMARTMETER;
                    $seccion_elemento = SECCION_SMARTMETER_FACTURAS;
                    break;
                }
                // Elementos de SmartMeter (Tarifas)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION:
                {
                    $modulo_elemento = MODULO_SMARTMETER;
                    $seccion_elemento = SECCION_SMARTMETER_TARIFAS;
                    break;
                }
                // Elementos de proyectos (Líneas base)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                {
                    $modulo_elemento = MODULO_PROYECTOS;
                    $seccion_elemento = SECCION_PROYECTOS_LINEAS_BASE;
                    break;
                }
                // Elementos de proyectos (Información)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
                {
                    $modulo_elemento = MODULO_PROYECTOS;
                    $seccion_elemento = SECCION_PROYECTOS_INFORMACION;
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de elemento desconocido: '".$tipo_elemento."'");
                }
            }

            // Se comprueba si es visible el elemento
            // (si es de varios módulos, con que este visible un módulo es suficiente)
            if ($elemento_varios_modulos == false)
            {
                if ($modulo_elemento === NULL)
                {
                    continue;
                }
                if (in_array($modulo_elemento, $modulos_usuario) == true)
                {
                    if ($perfil == PERFIL_USUARIO_ESTANDAR)
                    {
                        if (array_key_exists($modulo_elemento, $secciones_usuario) == true)
                        {
                            if (in_array($seccion_elemento, $secciones_usuario) == false)
                            {
                                $elementos_plantilla_informe_visibles = false;
                            }
                        }
                    }
                }
                else
                {
                    $elementos_plantilla_informe_visibles = false;
                }
            }
            else
            {
                $algun_modulo_visible = false;
                for ($i = 0; $i < count($modulos_elemento); $i++)
                {
                    $modulo_elemento = $modulos_elemento[$i];
                    $seccion_elemento = $secciones_elemento[$i];
                    $modulo_visible = true;
                    if (in_array($modulo_elemento, $modulos_usuario) == true)
                    {
                        if ($perfil == PERFIL_USUARIO_ESTANDAR)
                        {
                            if (array_key_exists($modulo_elemento, $secciones_usuario) == true)
                            {
                                if (in_array($seccion_elemento, $secciones_usuario) == false)
                                {
                                    $modulo_visible = false;
                                }
                            }
                        }
                    }
                    else
                    {
                        $modulo_visible = false;
                    }
                    if ($modulo_visible == true)
                    {
                        $algun_modulo_visible = true;
                    }
                }
                if ($algun_modulo_visible == false)
                {
                    $elementos_plantilla_informe_visibles = false;
                }
            }
            if ($elementos_plantilla_informe_visibles == false)
            {
                $nombre_primer_elemento_no_visible = $nombre_elemento;
            }
        }
        return ($elementos_plantilla_informe_visibles);
    }


    // Devuelve si todos los elementos de la plantilla de informe son configurables (no hay valores "fijos")
    function dame_elementos_plantilla_informe_configurables($id_plantilla_informe, &$nombre_primer_elemento_no_configurable)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recupera la información de los elementos
        $consulta_elementos = "
            SELECT
                nombre,
                tipo,
                parametros_tipo
            FROM elementos_plantillas_informes
            WHERE
                plantilla_informe = '".$bd_red->_($id_plantilla_informe)."'";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if ($res_elementos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }

        // Se recorren los elementos
        $elementos_plantilla_informe_configurables = true;
        while (($elementos_plantilla_informe_configurables == true) && ($fila_elemento = $res_elementos->dame_siguiente_fila()))
        {
            $nombre_elemento = $fila_elemento["nombre"];
            $tipo_elemento = $fila_elemento["tipo"];
            $cadena_parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

            $parametros_tipo_elemento = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo_elemento);
            $tipos_seleccion_elementos_elemento = array();
            switch ($tipo_elemento)
            {
                // Elementos generales
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_PAGINA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_LINEA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_NOTAS:
                case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN:
                {
                    break;
                }
                // Elementos de varios módulos
                case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
                {
                    $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_SENSORES];
                    $ids_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_SENSORES];
                    if (count($ids_sensores) > 0)
                    {
                        array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensores);
                    }
                    $tipo_seleccion_actuadores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_SENSORES];
                    $ids_actuadores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_ACTUADORES];
                    if (count($ids_actuadores) > 0)
                    {
                        array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_actuadores);
                    }
                    $tipo_seleccion_grupos_actuadores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_TIPO_SELECCION_SENSORES];
                    $ids_grupos_actuadores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS_IDS_ACTUADORES];
                    if (count($ids_grupos_actuadores) > 0)
                    {
                        array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_grupos_actuadores);
                    }
                    break;
                }
                // Elementos de sensores (Eventos)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                {
                    $tipo_seleccion_origen_evento = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS_TIPO_SELECCION_ORIGEN_EVENTO];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_origen_evento);
                    break;
                }
                // Elementos de sensores (Información)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION_TIPO_SELECCION_SENSOR];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor);
                    break;
                }
                // Elementos de sensores (Análisis)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO_TIPO_SELECCION_SENSOR];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO_TIPO_SELECCION_SENSOR];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
                {
                    $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO_TIPO_SELECCION_SENSORES];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensores);
                    break;
                }
                // Elementos de sensores (Comparación)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS_TIPO_SELECCION_SENSOR];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO_TIPO_SELECCION_SENSOR];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
                {
                    $tipo_seleccion_sensor_principal = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TIPO_SELECCION_SENSOR_PRINCIPAL];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor_principal);
                    $tipo_seleccion_sensores_secundarios = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES_TIPO_SELECCION_SENSORES_SECUNDARIOS];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensores_secundarios);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
                {
                    $cadena_tipos_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES_TIPOS_SELECCION_SENSORES];
                    $tipos_seleccion_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_tipos_seleccion_sensores);
                    for ($i = 0; $i < count($tipos_seleccion_sensores); $i++)
                    {
                        $tipo_seleccion_sensor = $tipos_seleccion_sensores[$i];
                        array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor);
                    }
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
                {
                    $tipo_seleccion_sensores_agregados = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_TIPO_SELECCION_SENSORES_AGREGADOS];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensores_agregados);
                    $tipo_seleccion_sensor_destacado = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO_TIPO_SELECCION_SENSOR_DESTACADO];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor_destacado);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
                {
                    $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES_TIPO_SELECCION_SENSORES];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensores);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
                {
                    $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES_TIPO_SELECCION_SENSORES];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensores);
                    break;
                }
                // Elementos de sensores (Estadística)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_HISTOGRAMA_TIPO_SELECCION_SENSOR];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION:
                {
                    $tipo_seleccion_sensor_dependiente = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_TIPO_SELECCION_SENSOR_DEPENDIENTE];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor_dependiente);
                    $cadena_tipos_seleccion_sensores_independientes = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_CORRELACION_TIPOS_SELECCION_SENSORES_INDEPENDIENTES];
                    $tipos_seleccion_sensores_independientes = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_tipos_seleccion_sensores_independientes);
                    for ($i = 0; $i < count($tipos_seleccion_sensores_independientes); $i++)
                    {
                        $tipo_seleccion_sensor_independiente = $tipos_seleccion_sensores_independientes[$i];
                        array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor_independiente);
                    }
                    break;
                }
                // Elementos de actuadores (Información)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                {
                    $tipo_seleccion_destino_accion = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TIPO_SELECCION_DESTINO_ACCION];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_destino_accion);
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS_TIPO_SELECCION_SENSOR];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor);
                    break;
                }
                // Elementos de SmartMeter (Consumos y costes)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                {
                    $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES_TIPO_SELECCION_SENSORES];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensores);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
                {
                    $tipo_seleccion_sensores = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES_TIPO_SELECCION_SENSORES];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensores);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS_TIPO_SELECCION_SENSOR];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
                {
                    // Nota: Este elemento no se puede copiar entre redes (las tarifas no son configurables)
                    $elementos_plantilla_informe_configurables = false;
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD_TIPO_SELECCION_SENSOR];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor);
                    break;
                }
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS_TIPO_SELECCION_SENSOR];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor);
                    break;
                }
                // Elementos de SmartMeter (Facturas)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_TIPO_SELECCION_SENSOR];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor);
                    $tipo_seleccion_sensores_reparto_costes = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA_TIPO_SELECCION_SENSORES_REPARTO_COSTES];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensores_reparto_costes);
                    break;
                }
                // Elementos de SmartMeter (Tarifas)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION:
                {
                    $tipo_seleccion_sensor = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION_TIPO_SELECCION_SENSOR];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_sensor);
                    break;
                }
                // Elementos de proyectos (Líneas base)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                {
                    $tipo_seleccion_linea_base = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE_TIPO_SELECCION_LINEA_BASE];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_linea_base);
                    break;
                }
                // Elementos de proyectos (Información)
                case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
                {
                    $tipo_seleccion_proyecto = $parametros_tipo_elemento[INDICE_PARAMETRO_TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO_TIPO_SELECCION_PROYECTO];
                    array_push($tipos_seleccion_elementos_elemento, $tipo_seleccion_proyecto);
                    break;
                }
                default:
                {
                    throw new Exception("Tipo de elemento desconocido: '".$tipo_elemento."'");
                }
            }
            foreach ($tipos_seleccion_elementos_elemento as $tipo_seleccion_elemento_elemento)
            {
                if ($tipo_seleccion_elemento_elemento == TIPO_SELECCION_ELEMENTO_PLANTILLA_INFORME_FIJO)
                {
                    $elementos_plantilla_informe_configurables = false;
                    break;
                }
            }
            if ($elementos_plantilla_informe_configurables == false)
            {
                $nombre_primer_elemento_no_configurable = $nombre_elemento;
            }
        }
        return ($elementos_plantilla_informe_configurables);
    }


    //
    // Funciones auxiliares
    //


    // Actualiza el usuario de una plantilla de informe
    function actualiza_usuario_plantilla_informe($id_plantilla_informe)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        if ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR)
        {
            $operacion_modificacion = "
                UPDATE plantillas_informes
                SET
                    usuario = '".$_SESSION["id_usuario"]."'
                WHERE
                    id = '".$bd_red->_($id_plantilla_informe)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }
        }
    }


    // Devuelve si el tipo de una plantilla de informe es modificable
    function dame_tipo_modificable_plantilla_informe($id_plantilla_informe)
    {
        $tipo = dame_tipo_plantilla_informe($id_plantilla_informe);
        $tipo_modificable = ($tipo == TIPO_PLANTILLA_INFORME_FIJO);
        return ($tipo_modificable);
    }


    // Devuelve el tipo de una plantilla de informe
    function dame_tipo_plantilla_informe($id_plantilla_informe)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta = "
            SELECT tipo
            FROM plantillas_informes
            WHERE
                id = '".$bd_red->_($id_plantilla_informe)."'";
        $res = $bd_red->ejecuta_consulta($consulta);
        if (($res == false) || ($res->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
        }
        $fila = $res->dame_siguiente_fila();
        $tipo = $fila["tipo"];

        return ($tipo);
    }


    // Devuelve el tipo de selección de horario semanal y fechas de una plantilla de informe
    function dame_tipo_seleccion_horario_semanal_fechas_plantilla_informe($id_plantilla_informe)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        if ($id_plantilla_informe == ID_NINGUNO)
        {
            $tipo_seleccion_horario_semanal_fechas = TIPO_SELECCION_HORARIO_SEMANAL_FECHAS_FIJO;
        }
        else
        {
            $consulta = "
                SELECT
                    tipo_seleccion_horario_semanal_fechas
                FROM plantillas_informes
                WHERE
                    id = '".$bd_red->_($id_plantilla_informe)."'";
            $res = $bd_red->ejecuta_consulta($consulta);
            if (($res == false) || ($res->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
            }
            $fila = $res->dame_siguiente_fila();
            $tipo_seleccion_horario_semanal_fechas = $fila["tipo_seleccion_horario_semanal_fechas"];
        }
        return ($tipo_seleccion_horario_semanal_fechas);
    }


    // Devuelve las fechas de inicio y fin según el periodo de tiempo por defecto de una plantilla de informe
    function dame_fechas_inicio_fin_defecto_informe_plantilla_informe($id_plantilla_informe)
    {
        $zona_horaria = dame_zona_horaria_local();
        if ($id_plantilla_informe == ID_NINGUNO)
        {
            $fecha_hora_actual_local = dame_fecha_hora_actual_local();
            $cadena_fecha_inicio_date_javascript_local = convierte_fecha_a_cadena($fecha_hora_actual_local, FORMATO_FECHA_DATE_JAVASCRIPT, $zona_horaria);
            $cadena_fecha_fin_date_javascript_local = $cadena_fecha_inicio_date_javascript_local;
        }
        else
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            // Se recupera el periodo de tiempo por defecto
            $consulta = "
                SELECT
                    periodo_tiempo_defecto,
                    iniciar_comienzo_periodo_tiempo_defecto
                FROM plantillas_informes
                WHERE
                    id = '".$bd_red->_($id_plantilla_informe)."'";
            $res = $bd_red->ejecuta_consulta($consulta);
            if (($res == false) || ($res->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
            }
            $fila = $res->dame_siguiente_fila();
            $periodo_tiempo = $fila["periodo_tiempo_defecto"];
            $iniciar_comienzo_periodo_tiempo= $fila["iniciar_comienzo_periodo_tiempo_defecto"];

            // Se calculan las fechas de inicio y fin del informe
            $fecha_hora_fin_local = dame_fecha_hora_actual_local();
            $fecha_hora_inicio_local = clone $fecha_hora_fin_local;
            if ($iniciar_comienzo_periodo_tiempo == true)
            {
                switch ($periodo_tiempo)
                {
                    case PERIODO_TIEMPO_HORA:
                    {
                        $fecha_hora_inicio_local->setTime($fecha_hora_inicio_local->format("H"), 0, 0);
                        break;
                    }
                    case PERIODO_TIEMPO_DIA:
                    {
                        $fecha_hora_inicio_local->setTime(0, 0, 0);
                        break;
                    }
                    case PERIODO_TIEMPO_SEMANA:
                    {
                        $fecha_hora_inicio_local->setTime(0, 0, 0);
                        $numero_dia_semana = $fecha_hora_inicio_local->format('w');
                        if ($numero_dia_semana == 0)
                        {
                            $numero_dia_semana = 7;
                        }
                        date_modify($fecha_hora_inicio_local, '-'.($numero_dia_semana - 1).' day');
                        break;
                    }
                    case PERIODO_TIEMPO_MES:
                    {
                        $fecha_hora_inicio_local->setTime(0, 0, 0);
                        $fecha_hora_inicio_local->setDate($fecha_hora_inicio_local->format("Y"), $fecha_hora_inicio_local->format("m"), 1);
                        break;
                    }
                    case PERIODO_TIEMPO_ANYO:
                    {
                        $fecha_hora_inicio_local->setTime(0, 0, 0);
                        $fecha_hora_inicio_local->setDate($fecha_hora_inicio_local->format("Y"), 1, 1);
                        break;
                    }
                    default:
                    {
                        throw new Exception("Periodo desconocido");
                    }
                }
            }
            else
            {
                switch ($periodo_tiempo)
                {
                    case PERIODO_TIEMPO_HORA:
                    {
                        $cadena_periodo = "PT1H";
                        break;
                    }
                    case PERIODO_TIEMPO_DIA:
                    {
                        $cadena_periodo = "P1D";
                        break;
                    }
                    case PERIODO_TIEMPO_SEMANA:
                    {
                        $cadena_periodo = "P7D";
                        break;
                    }
                    case PERIODO_TIEMPO_MES:
                    {
                        $cadena_periodo = "P1M";
                        break;
                    }
                    case PERIODO_TIEMPO_ANYO:
                    {
                        $cadena_periodo = "P1Y";
                        break;
                    }
                    default:
                    {
                        throw new Exception("Periodo desconocido");
                    }
                }
                $periodo_tiempo = new DateInterval($cadena_periodo);
                $fecha_hora_inicio_local->sub($periodo_tiempo);
            }

            $cadena_fecha_inicio_date_javascript_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, FORMATO_FECHA_DATE_JAVASCRIPT, $zona_horaria);
            $cadena_fecha_fin_date_javascript_local = convierte_fecha_a_cadena($fecha_hora_fin_local, FORMATO_FECHA_DATE_JAVASCRIPT, $zona_horaria);
        }

        $fechas = array(
            "fecha_inicio" => $cadena_fecha_inicio_date_javascript_local,
            "fecha_fin" => $cadena_fecha_fin_date_javascript_local);
        return ($fechas);
    }


    // Devuelve si todos los elementos de la plantilla de informe son visibles por el usuario
    function dame_elementos_plantilla_informe_visibles_usuario(
        $id_plantilla_informe,
        $id_usuario,
        &$nombre_primer_elemento_no_visible)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Módulos y secciones del usuario
        $modulos_usuario = dame_modulos_usuario($id_usuario, PERFIL_USUARIO_ESTANDAR, $_SESSION["id_red"]);
        $secciones_usuario = dame_secciones_usuario($id_usuario, $_SESSION["id_red"]);

        // Se recuperan los parámetros de los módulos del usuario:
        // - Localizaciones
        // - Sensores
        // - Actuadores
        $parametros_modulo_localizaciones = NULL;
        $parametros_modulo_sensores = NULL;
        $parametros_modulo_actuadores = NULL;
        $consulta_parametros_modulos_usuario = "
            SELECT
                modulo,
                parametros
            FROM modulos_usuarios
            WHERE
                (usuario = '".$bd_red->_($id_usuario)."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_parametros_modulos_usuario = $bd_red->ejecuta_consulta($consulta_parametros_modulos_usuario);
        if ($res_parametros_modulos_usuario == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros_modulos_usuario."'");
        }
        while ($fila_parametros_modulo_usuario = $res_parametros_modulos_usuario->dame_siguiente_fila())
        {
            $modulo = $fila_parametros_modulo_usuario["modulo"];
            $cadena_parametros = $fila_parametros_modulo_usuario["parametros"];

            switch ($modulo)
            {
                case MODULO_LOCALIZACIONES:
                {
                    $parametros_modulo_localizaciones = dame_parametros_modulo_localizaciones_usuario($cadena_parametros);
                    break;
                }
                case MODULO_SENSORES:
                {
                    $parametros_modulo_sensores = dame_parametros_modulo_sensores_usuario($cadena_parametros);
                    break;
                }
                case MODULO_ACTUADORES:
                {
                    $parametros_modulo_actuadores = dame_parametros_modulo_actuadores_usuario($cadena_parametros);
                    break;
                }
            }
        }
        if (($parametros_modulo_localizaciones === NULL) ||
            ($parametros_modulo_sensores === NULL) ||
            ($parametros_modulo_actuadores === NULL))
        {
            throw new Exception("Error al recuperar los parámetros de módulos del usuario: '".$id_usuario."'");
        }

        // Identificadores de elementos visibles por el usuario
        if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
        {
            $permiso_todas_localizaciones = $parametros_modulo_localizaciones["permiso_todas_localizaciones"];
            if ($permiso_todas_localizaciones == true)
            {
                $ids_localizaciones = dame_ids_localizaciones();
            }
            else
            {
                $ids_localizaciones = $parametros_modulo_localizaciones["ids_localizaciones"];
            }
            $ids_sensores_visibles_localizaciones = dame_ids_nodos_visibles_localizaciones(
                $ids_localizaciones,
                TIPO_NODO_SENSOR);
            $ids_grupos_sensores_visibles_localizaciones = dame_ids_grupos_nodos_visibles_localizaciones(
                $ids_localizaciones,
                TIPO_NODO_SENSOR);
            $ids_actuadores_visibles_localizaciones = dame_ids_nodos_visibles_localizaciones(
                $ids_localizaciones,
                TIPO_NODO_ACTUADOR);
            $ids_grupos_actuadores_visibles_localizaciones = dame_ids_grupos_nodos_visibles_localizaciones(
                $ids_localizaciones,
                TIPO_NODO_ACTUADOR);
        }
        else
        {
            $ids_sensores_visibles_localizaciones = array();
            $ids_grupos_sensores_visibles_localizaciones = array();
            $ids_actuadores_visibles_localizaciones = array();
            $ids_grupos_actuadores_visibles_localizaciones = array();
        }
        $permiso_todos_sensores = $parametros_modulo_sensores["permiso_todos_sensores"];
        $ids_sensores = $parametros_modulo_sensores["ids_sensores"];
        $ids_grupos_sensores = $parametros_modulo_sensores["ids_grupos_sensores"];
        $permiso_todos_actuadores = $parametros_modulo_actuadores["permiso_todos_actuadores"];
        $ids_actuadores = $parametros_modulo_actuadores["ids_actuadores"];
        $ids_grupos_actuadores = $parametros_modulo_actuadores["ids_grupos_actuadores"];
        $ids_todas_lineas_base_visibles_usuario = dame_ids_todas_lineas_base_visibles_usuario(
            $permiso_todos_sensores,
            $ids_sensores,
            $ids_grupos_sensores,
            $modulos_usuario,
            $ids_sensores_visibles_localizaciones);
        $ids_todos_proyectos_visibles_usuario = dame_ids_todos_proyectos_visibles_usuario(
            $permiso_todos_sensores,
            $ids_sensores,
            $ids_grupos_sensores,
            $modulos_usuario,
            $ids_sensores_visibles_localizaciones);

        // Se recuperan las filas de los elementos de la plantilla de informe
        $consulta_elementos = "
            SELECT *
            FROM
                elementos_plantillas_informes
            WHERE
                plantilla_informe = '".$bd_red->_($id_plantilla_informe)."'";
        $res_elementos = $bd_red->ejecuta_consulta($consulta_elementos);
        if ($res_elementos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_elementos."'");
        }
        $filas_elementos = array();
        while ($fila_elemento = $res_elementos->dame_siguiente_fila())
        {
            array_push($filas_elementos, $fila_elemento);
        }

        // Si hay elementos de secciones de varios módulos no visibles por el usuario, se devuelve false
        if (((in_array(MODULO_SENSORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_INFORMACION, $secciones_usuario[MODULO_SENSORES]) == false))) &&
            ((in_array(MODULO_ACTUADORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_ACTUADORES]) > 0) && (in_array(SECCION_ACTUADORES_INFORMACION, $secciones_usuario[MODULO_ACTUADORES]) == false))))
        {
            foreach ($filas_elementos as $fila_elemento)
            {
                $nombre_elemento = $fila_elemento["nombre"];
                $tipo_elemento = $fila_elemento["tipo"];
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
                    {
                        $nombre_primer_elemento_no_visible = $nombre_elemento;
                        return (false);
                    }
                }
            }
        }

        // Si se tiene el módulo Sensores
        if (in_array(MODULO_SENSORES, $modulos_usuario) == true)
        {
            // 1. Se recorren cada uno de los elementos de plantillas de informes del usuario que muestran información de sensores
            // 2. Se recuperan los identificadores de sensores del elemento de plantilla de informe
            // 3. Si algún sensor no es visible por el usuario, se devuelve false
            foreach ($filas_elementos as $fila_elemento)
            {
                $nombre_elemento = $fila_elemento["nombre"];
                $tipo_elemento = $fila_elemento["tipo"];
                $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

                // Comprobaciones de identificadores de sensores
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
                    {
                        $ids_sensores_elemento = dame_ids_sensores_elemento_plantilla_informe_sensores(
                            $tipo_elemento,
                            $parametros_tipo_elemento);
                        foreach ($ids_sensores_elemento as $id_sensor_elemento)
                        {
                            if (($id_sensor_elemento == "") || ($id_sensor_elemento == ID_NINGUNO))
                            {
                                continue;
                            }
                            $sensor_visible_usuario = false;
                            if ($sensor_visible_usuario == false)
                            {
                                if (($permiso_todos_sensores == true) ||
                                    (dame_sensor_sensores_grupos($id_sensor_elemento, $ids_sensores, $ids_grupos_sensores) == true))
                                {
                                    $sensor_visible_usuario = true;
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($id_sensor_elemento, $ids_sensores_visibles_localizaciones) == true)
                                    {
                                        $sensor_visible_usuario = true;
                                    }
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                $nombre_primer_elemento_no_visible = $nombre_elemento;
                                return (false);
                            }
                        }
                        break;
                    }
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                    {
                        $ids_sensores_elemento = dame_ids_sensores_elemento_plantilla_informe_actuadores(
                            $tipo_elemento,
                            $parametros_tipo_elemento);
                        foreach ($ids_sensores_elemento as $id_sensor_elemento)
                        {
                            if (($id_sensor_elemento == "") || ($id_sensor_elemento == ID_NINGUNO))
                            {
                                continue;
                            }
                            $sensor_visible_usuario = false;
                            if ($sensor_visible_usuario == false)
                            {
                                if (($permiso_todos_sensores == true) ||
                                    (dame_sensor_sensores_grupos($id_sensor_elemento, $ids_sensores, $ids_grupos_sensores) == true))
                                {
                                    $sensor_visible_usuario = true;
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($id_sensor_elemento, $ids_sensores_visibles_localizaciones) == true)
                                    {
                                        $sensor_visible_usuario = true;
                                    }
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                $nombre_primer_elemento_no_visible = $nombre_elemento;
                                return (false);
                            }
                        }
                        break;
                    }
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION:
                    {
                        $ids_sensores_elemento = dame_ids_sensores_elemento_plantilla_informe_smartmeter(
                            $tipo_elemento,
                            $parametros_tipo_elemento);
                        foreach ($ids_sensores_elemento as $id_sensor_elemento)
                        {
                            if (($id_sensor_elemento == "") || ($id_sensor_elemento == ID_NINGUNO))
                            {
                                continue;
                            }
                            $sensor_visible_usuario = false;
                            if ($sensor_visible_usuario == false)
                            {
                                if (($permiso_todos_sensores == true) ||
                                    (dame_sensor_sensores_grupos($id_sensor_elemento, $ids_sensores, $ids_grupos_sensores) == true))
                                {
                                    $sensor_visible_usuario = true;
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($id_sensor_elemento, $ids_sensores_visibles_localizaciones) == true)
                                    {
                                        $sensor_visible_usuario = true;
                                    }
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                $nombre_primer_elemento_no_visible = $nombre_elemento;
                                return (false);
                            }
                        }
                        break;
                    }
                }
            }

            // 1. Se recorren cada uno de los elementos de plantillas de informes del usuario que muestran información de grupos de sensores
            // 2. Se recuperan los identificadores de grupos de sensores del elemento de plantilla de informe
            // 3. Si algún grupo de sensores no es visible por el usuario, se devuelve false
            foreach ($filas_elementos as $fila_elemento)
            {
                $nombre_elemento = $fila_elemento["nombre"];
                $tipo_elemento = $fila_elemento["tipo"];
                $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

                // Comprobaciones de identificadores de grupos de sensores
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                    {
                        $ids_grupos_sensores_elemento = dame_ids_grupos_sensores_elemento_plantilla_informe_sensores(
                            $tipo_elemento,
                            $parametros_tipo_elemento);
                        foreach ($ids_grupos_sensores_elemento as $id_grupo_sensores_elemento)
                        {
                            $grupo_sensores_visible_usuario = false;
                            if ($grupo_sensores_visible_usuario == false)
                            {
                                if (($permiso_todos_sensores == true) ||
                                    (in_array($id_grupo_sensores_elemento, $ids_grupos_sensores) == true))
                                {
                                    $grupo_sensores_visible_usuario = true;
                                }
                            }
                            if ($grupo_sensores_visible_usuario == false)
                            {
                                if (in_array($id_grupo_sensores_elemento, $ids_grupos_sensores_visibles_localizaciones) == true)
                                {
                                    $grupo_sensores_visible_usuario = true;
                                }
                            }
                            if ($grupo_sensores_visible_usuario == false)
                            {
                                $nombre_primer_elemento_no_visible = $nombre_elemento;
                                return (false);
                            }
                        }
                        break;
                    }
                }
            }
        }

        // Si hay elementos de secciones del módulo Sensores no visibles por el usuario, se devuelve false
        if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_EVENTOS, $secciones_usuario[MODULO_SENSORES]) == false)))
        {
            foreach ($filas_elementos as $fila_elemento)
            {
                $nombre_elemento = $fila_elemento["nombre"];
                $tipo_elemento = $fila_elemento["tipo"];
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                    {
                        $nombre_primer_elemento_no_visible = $nombre_elemento;
                        return (false);
                    }
                }
            }
        }
        if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_INFORMACION, $secciones_usuario[MODULO_SENSORES]) == false)))
        {
            foreach ($filas_elementos as $fila_elemento)
            {
                $nombre_elemento = $fila_elemento["nombre"];
                $tipo_elemento = $fila_elemento["tipo"];
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INFORMACION:
                    {
                        $nombre_primer_elemento_no_visible = $nombre_elemento;
                        return (false);
                    }
                }
            }
        }
        if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_ANALISIS, $secciones_usuario[MODULO_SENSORES]) == false)))
        {
            foreach ($filas_elementos as $fila_elemento)
            {
                $nombre_elemento = $fila_elemento["nombre"];
                $tipo_elemento = $fila_elemento["tipo"];
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_HORARIO:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_DIARIO:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
                    {
                        $nombre_primer_elemento_no_visible = $nombre_elemento;
                        return (false);
                    }
                }
            }
        }
        if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_COMPARACION, $secciones_usuario[MODULO_SENSORES]) == false)))
        {
            foreach ($filas_elementos as $fila_elemento)
            {
                $nombre_elemento = $fila_elemento["nombre"];
                $tipo_elemento = $fila_elemento["tipo"];
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERIODOS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_ANALISIS_COMPARATIVO:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_VALORES_GENERALES:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SENSORES_INCREMENTOS_TOTALES:
                    {
                        $nombre_primer_elemento_no_visible = $nombre_elemento;
                        return (false);
                    }
                }
            }
        }

        // Si se tiene el módulo Actuadores
        if (in_array(MODULO_ACTUADORES, $modulos_usuario) == true)
        {
            // 1. Se recorren cada uno de los elementos de plantillas de informes del usuario que muestran información de actuadores
            // 2. Se recuperan los identificadores de actuadores del elemento de plantilla de informe
            // 3. Si algún actuador no es visible por el usuario, se devuelve false
            foreach ($filas_elementos as $fila_elemento)
            {
                $nombre_elemento = $fila_elemento["nombre"];
                $tipo_elemento = $fila_elemento["tipo"];
                $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

                // Comprobaciones de identificadores de actuadores
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                    {
                        $ids_actuadores_elemento = dame_ids_actuadores_elemento_plantilla_informe_actuadores(
                            $tipo_elemento,
                            $parametros_tipo_elemento);
                        foreach ($ids_actuadores_elemento as $id_actuador_elemento)
                        {
                            if (($id_actuador_elemento == "") || ($id_actuador_elemento == ID_NINGUNO))
                            {
                                continue;
                            }
                            $actuador_visible_usuario = false;
                            if ($actuador_visible_usuario == false)
                            {
                                if (($permiso_todos_actuadores == true) ||
                                    (dame_actuador_actuadores_grupos($id_actuador_elemento, $ids_actuadores, $ids_grupos_actuadores) == true))
                                {
                                    $actuador_visible_usuario = true;
                                }
                            }
                            if ($actuador_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($id_actuador_elemento, $ids_actuadores_visibles_localizaciones) == true)
                                    {
                                        $actuador_visible_usuario = true;
                                    }
                                }
                            }
                            if ($actuador_visible_usuario == false)
                            {
                                $nombre_primer_elemento_no_visible = $nombre_elemento;
                                return (false);
                            }
                        }
                        break;
                    }
                }
            }

            // 1. Se recorren cada uno de los elementos de plantillas de informes del usuario que muestran información de grupos de actuadores
            // 2. Se recuperan los identificadores de grupos de actuadores del elemento de plantilla de informe
            // 3. Si algún grupo de actuadores no es visible por el usuario, se devuelve false
            foreach ($filas_elementos as $fila_elemento)
            {
                $nombre_elemento = $fila_elemento["nombre"];
                $tipo_elemento = $fila_elemento["tipo"];
                $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

                // Comprobaciones de identificadores de grupos de actuadores
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_COMENTARIOS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                    {
                        $ids_grupos_actuadores_elemento = dame_ids_grupos_actuadores_elemento_plantilla_informe_actuadores(
                            $tipo_elemento,
                            $parametros_tipo_elemento);
                        foreach ($ids_grupos_actuadores_elemento as $id_grupo_actuadores_elemento)
                        {
                            $grupo_actuadores_visible_usuario = false;
                            if ($grupo_actuadores_visible_usuario == false)
                            {
                                if (($permiso_todos_actuadores == true) ||
                                    (in_array($id_grupo_actuadores_elemento, $ids_grupos_actuadores) == true))
                                {
                                    $grupo_actuadores_visible_usuario = true;
                                }
                            }
                            if ($grupo_actuadores_visible_usuario == false)
                            {
                                if (in_array($id_grupo_actuadores_elemento, $ids_grupos_actuadores_visibles_localizaciones) == true)
                                {
                                    $grupo_actuadores_visible_usuario = true;
                                }
                            }
                            if ($grupo_actuadores_visible_usuario == false)
                            {
                                $nombre_primer_elemento_no_visible = $nombre_elemento;
                                return (false);
                            }
                        }
                        break;
                    }
                }
            }
        }

        // Si hay elementos de secciones del módulo Actuadores no visibles por el usuario, se devuelve false
        if ((in_array(MODULO_ACTUADORES, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_ACTUADORES]) > 0) && (in_array(SECCION_ACTUADORES_INFORMACION, $secciones_usuario[MODULO_ACTUADORES]) == false)))
        {
            foreach ($filas_elementos as $fila_elemento)
            {
                $nombre_elemento = $fila_elemento["nombre"];
                $tipo_elemento = $fila_elemento["tipo"];
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                    {
                        $nombre_primer_elemento_no_visible = $nombre_elemento;
                        return (false);
                    }
                }
            }
        }

        // Si hay elementos de secciones del módulo SmartMeter no visibles por el usuario, se devuelve false
        if ((in_array(MODULO_SMARTMETER, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SMARTMETER]) > 0) && (in_array(SECCION_SMARTMETER_CONSUMOS_COSTES, $secciones_usuario[MODULO_SMARTMETER]) == false)))
        {
            foreach ($filas_elementos as $fila_elemento)
            {
                $nombre_elemento = $fila_elemento["nombre"];
                $tipo_elemento = $fila_elemento["tipo"];
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_COMPARACION_PERIODOS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS_ELECTRICIDAD:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_CORTES_TENSION_ELECTRICIDAD:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_POTENCIA_ELECTRICIDAD:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA_ELECTRICIDAD:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_EXCESOS_CAUDAL_GAS:
                    {
                        $nombre_primer_elemento_no_visible = $nombre_elemento;
                        return (false);
                    }
                }
            }
        }
        if ((in_array(MODULO_SMARTMETER, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SMARTMETER]) > 0) && (in_array(SECCION_SMARTMETER_FACTURAS, $secciones_usuario[MODULO_SMARTMETER]) == false)))
        {
            foreach ($filas_elementos as $fila_elemento)
            {
                $nombre_elemento = $fila_elemento["nombre"];
                $tipo_elemento = $fila_elemento["tipo"];
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_SIMULADOR_FACTURA:
                    {
                        $nombre_primer_elemento_no_visible = $nombre_elemento;
                        return (false);
                    }
                }
            }
        }
        if ((in_array(MODULO_SMARTMETER, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_SMARTMETER]) > 0) && (in_array(SECCION_SMARTMETER_TARIFAS, $secciones_usuario[MODULO_SMARTMETER]) == false)))
        {
            foreach ($filas_elementos as $fila_elemento)
            {
                $nombre_elemento = $fila_elemento["nombre"];
                $tipo_elemento = $fila_elemento["tipo"];
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_SMARTMETER_INSTALACION:
                    {
                        $nombre_primer_elemento_no_visible = $nombre_elemento;
                        return (false);
                    }
                }
            }
        }

        // Si se tiene el módulo Proyectos
        if (in_array(MODULO_PROYECTOS, $modulos_usuario) == true)
        {
            // 1. Se recorren cada uno de los elementos de plantillas de informes del usuario que muestran información de proyectos
            // 2. Se recuperan los identificadores de proyectos del elemento de plantilla de informe
            // 3. Si algún proyecto no es visible por el usuario, se devuelve false
            foreach ($filas_elementos as $fila_elemento)
            {
                $nombre_elemento = $fila_elemento["nombre"];
                $tipo_elemento = $fila_elemento["tipo"];
                $parametros_tipo_elemento = $fila_elemento["parametros_tipo"];

                // Comprobaciones de identificadores de líneas base
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                    {
                        $ids_lineas_base_elemento = dame_ids_lineas_base_elemento_plantilla_informe_proyectos(
                            $tipo_elemento,
                            $parametros_tipo_elemento);
                        foreach ($ids_lineas_base_elemento as $id_linea_base_elemento)
                        {
                            $linea_base_visible_usuario = false;
                            if (in_array($id_linea_base_elemento, $ids_todas_lineas_base_visibles_usuario) == true)
                            {
                                $linea_base_visible_usuario = true;
                            }
                            if ($linea_base_visible_usuario == false)
                            {
                                $nombre_primer_elemento_no_visible = $nombre_elemento;
                                return (false);
                            }
                        }
                        break;
                    }
                }

                // Comprobaciones de identificadores de proyectos
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
                    {
                        $ids_proyectos_elemento = dame_ids_proyectos_elemento_plantilla_informe_proyectos(
                            $tipo_elemento,
                            $parametros_tipo_elemento);
                        foreach ($ids_proyectos_elemento as $id_proyecto_elemento)
                        {
                            $proyecto_visible_usuario = false;
                            if (in_array($id_proyecto_elemento, $ids_todos_proyectos_visibles_usuario) == true)
                            {
                                $proyecto_visible_usuario = true;
                            }
                            if ($proyecto_visible_usuario == false)
                            {
                                $nombre_primer_elemento_no_visible = $nombre_elemento;
                                return (false);
                            }
                        }
                        break;
                    }
                }
            }
        }

        // Si hay elementos de secciones del módulo SmartMeter no visibles por el usuario, se devuelve false
        if ((in_array(MODULO_PROYECTOS, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_PROYECTOS]) > 0) && (in_array(SECCION_PROYECTOS_LINEAS_BASE, $secciones_usuario[MODULO_PROYECTOS]) == false)))
        {
            foreach ($filas_elementos as $fila_elemento)
            {
                $nombre_elemento = $fila_elemento["nombre"];
                $tipo_elemento = $fila_elemento["tipo"];
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                    {
                        $nombre_primer_elemento_no_visible = $nombre_elemento;
                        return (false);
                    }
                }
            }
        }
        if ((in_array(MODULO_PROYECTOS, $modulos_usuario) == false) ||
            ((count($secciones_usuario[MODULO_PROYECTOS]) > 0) && (in_array(SECCION_PROYECTOS_INFORMACION, $secciones_usuario[MODULO_PROYECTOS]) == false)))
        {
            foreach ($filas_elementos as $fila_elemento)
            {
                $nombre_elemento = $fila_elemento["nombre"];
                $tipo_elemento = $fila_elemento["tipo"];
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_PROYECTOS_INFORMACION_PROYECTO:
                    {
                        $nombre_primer_elemento_no_visible = $nombre_elemento;
                        return (false);
                    }
                }
            }
        }

        // Si se llega aquí todos los elementos son visibles por el usuario actual
        return (true);
    }


    //
    // Funciones de permisos de usuario
    //


    // Devuelve los identificadores de las plantillas de informes visibles para el usuario actual
    function dame_ids_plantillas_informes_usuario_actual()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Identificadores de plantillas de informes
        $ids_plantillas_informes = array();
        $consulta = PlantillaInforme::dame_consulta_plantillas_informes("");
        $res = $bd_red->ejecuta_consulta($consulta);
        if ($res == false)
        {
            throw new Exception("Error en la consulta: '".$consulta."'");
        }
        while ($fila = $res->dame_siguiente_fila())
        {
            $id_plantilla_informe = $fila["id"];
            array_push($ids_plantillas_informes, $id_plantilla_informe);
        }
        return ($ids_plantillas_informes);
    }
?>