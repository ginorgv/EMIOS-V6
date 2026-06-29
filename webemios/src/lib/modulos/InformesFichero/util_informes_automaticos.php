<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/InformeAutomatico.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/util_modulo_actuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/util_modulo_proyectos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/util_modulo_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/util_modulo_smartmeter.php');


    // Devuelve el número de informes automáticos (del usuario actual)
    function dame_numero_informes_automaticos()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_numero_informes_automaticos = "
            SELECT
                COUNT(*) AS numero_informes_automaticos
            FROM informes_automaticos
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (usuario = '".$bd_red->_($_SESSION["id_usuario"])."')
            ORDER BY nombre ASC";
        $res_numero_informes_automaticos = $bd_red->ejecuta_consulta($consulta_numero_informes_automaticos);
        if (($res_numero_informes_automaticos == false) || ($res_numero_informes_automaticos->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_numero_informes_automaticos."'");
        }
        $fila_numero_informes_automaticos = $res_numero_informes_automaticos->dame_siguiente_fila();
        $numero_informes_automaticos = $fila_numero_informes_automaticos["numero_informes_automaticos"];
        return ($numero_informes_automaticos);
    }


    // Devuelve el número máximo de informes automáticos (del usuario actual)
    function dame_numero_maximo_informes_automaticos()
    {
        switch ($_SESSION["perfil"])
        {
            case PERFIL_USUARIO_ESTANDAR:
            {
                $numero_maximo_informes_automaticos = $_SESSION["parametros_modulo_personal"]["numero_maximo_informes_automaticos"];
                break;
            }
            default:
            {
                $numero_maximo_informes_automaticos = -1;
                break;
            }
        }
        return ($numero_maximo_informes_automaticos);
    }


    // Devuelve si es posible añadir un informe automático (con el usuario actual)
    function dame_posible_anyadir_informe_automatico($numero_informes_automaticos)
    {
        $modulo_personal_disponible = dame_modulo_disponible_sesion(MODULO_PERSONAL);
        if ($modulo_personal_disponible == false)
        {
            return (false);
        }

        if (InformeAutomatico::dame_administracion_informes_automaticos() == false)
        {
            return (false);
        }

        $numero_maximo_informes_automaticos = dame_numero_maximo_informes_automaticos();
        if (($numero_maximo_informes_automaticos == -1) OR
            (($numero_maximo_informes_automaticos != 0) AND
             ($numero_maximo_informes_automaticos > $numero_informes_automaticos)))
        {
            return (true);
        }
        else
        {
            return (false);
        }
    }


    //
    // Funciones para eliminar informes automáticos automáticamente
    //


    // Elimina los informes automáticos no visibles de un usuario (con perfil estándar)
    function elimina_informes_automaticos_no_visibles_usuario(
        $id_usuario,
        $perfil,
        $id_red,
        $parametros_usuario)
    {
        // Se eliminan los informes automáticos que el usuario ya no puede visualizar:
        // - 1. No tiene el módulo correspondiente.
        // - 2. Se eliminar el informe automático si el usuario ya no tiene permisos para visualizar el nodo correspondiente (sensor, actuador o grupo de actuadores)
        // (Nota: Se comprueban los permisos del sensor o actuador tanto en el módulo Sensores y Actuadores como en el módulo Localizaciones)

        $bd_red = BaseDatosRed::dame_base_datos();

        // Módulos y secciones del usuario
        $modulos_usuario = dame_modulos_usuario($id_usuario, $perfil, $id_red);
        $secciones_usuario = dame_secciones_usuario($id_usuario, $id_red);

        // Parámetros del usuario
        $parametros_modulo_localizaciones = $parametros_usuario["parametros_modulo_localizaciones"];
        $parametros_modulo_sensores = $parametros_usuario["parametros_modulo_sensores"];
        $parametros_modulo_actuadores = $parametros_usuario["parametros_modulo_actuadores"];

        // Localizaciones
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

        // Sensores
        $permiso_todos_sensores = $parametros_modulo_sensores["permiso_todos_sensores"];
        $ids_sensores = $parametros_modulo_sensores["ids_sensores"];
        $ids_grupos_sensores = $parametros_modulo_sensores["ids_grupos_sensores"];

        // Actuadores
        $permiso_todos_actuadores = $parametros_modulo_actuadores["permiso_todos_actuadores"];
        $ids_actuadores = $parametros_modulo_actuadores["ids_actuadores"];
        $ids_grupos_actuadores = $parametros_modulo_actuadores["ids_grupos_actuadores"];

        // Comprobación de eliminación de informes automáticos por permisos del módulo Sensores (por secciones)
        if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) or
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_EVENTOS, $secciones_usuario[MODULO_SENSORES]) == false)))
        {
            $operacion_borrado_informes_automaticos_sensores = "
                DELETE
                FROM informes_automaticos
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND (tipo = '".TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS."')";
            $res_borrado_informes_automaticos_sensores = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_sensores);
            if ($res_borrado_informes_automaticos_sensores == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_sensores."'");
            }
        }
        if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) or
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_INFORMACION, $secciones_usuario[MODULO_SENSORES]) == false)))
        {
            $operacion_borrado_informes_automaticos_sensores = "
                DELETE
                FROM informes_automaticos
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND ((tipo = '".TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_INFORMACION_VIENTO."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_INFORMACION_GAS."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_INFORMACION_AGUA."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_INFORMACION_GENERICA."'))";
            $res_borrado_informes_automaticos_sensores = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_sensores);
            if ($res_borrado_informes_automaticos_sensores == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_sensores."'");
            }
        }
        if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) or
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_ANALISIS, $secciones_usuario[MODULO_SENSORES]) == false)))
        {
            $operacion_borrado_informes_automaticos_sensores = "
                DELETE
                FROM informes_automaticos
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND ((tipo = '".TIPO_INFORME_SENSORES_ANALISIS_HORARIO."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_ANALISIS_DIARIO."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO."'))";
            $res_borrado_informes_automaticos_sensores = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_sensores);
            if ($res_borrado_informes_automaticos_sensores == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_sensores."'");
            }
        }
        if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) or
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_COMPARACION, $secciones_usuario[MODULO_SENSORES]) == false)))
        {
            $operacion_borrado_informes_automaticos_sensores = "
                DELETE
                FROM informes_automaticos
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND ((tipo = '".TIPO_INFORME_SENSORES_COMPARACION_PERIODOS."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_VALORES_GENERALES."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES."'))";
            $res_borrado_informes_automaticos_sensores = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_sensores);
            if ($res_borrado_informes_automaticos_sensores == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_sensores."'");
            }
        }
        if ((in_array(MODULO_SENSORES, $modulos_usuario) == false) or
            ((count($secciones_usuario[MODULO_SENSORES]) > 0) && (in_array(SECCION_SENSORES_ESTADISTICA, $secciones_usuario[MODULO_SENSORES]) == false)))
        {
            $operacion_borrado_informes_automaticos_sensores = "
                DELETE
                FROM informes_automaticos
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND ((tipo = '".TIPO_INFORME_SENSORES_HISTOGRAMA."')
                        OR (tipo = '".TIPO_INFORME_SENSORES_CORRELACION."'))";
            $res_borrado_informes_automaticos_sensores = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_sensores);
            if ($res_borrado_informes_automaticos_sensores == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_sensores."'");
            }
        }

        // Comprobación de eliminación de informes automáticos por permisos del módulo Actuadores
        if ((in_array(MODULO_ACTUADORES, $modulos_usuario) == false) or
            ((count($secciones_usuario[MODULO_ACTUADORES]) > 0) && (in_array(SECCION_ACTUADORES_INFORMACION, $secciones_usuario[MODULO_ACTUADORES]) == false)))
        {
            $operacion_borrado_informes_automaticos_actuadores = "
                DELETE
                FROM informes_automaticos
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND (tipo = '".TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS."')";
            $res_borrado_informes_automaticos_actuadores = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_actuadores);
            if ($res_borrado_informes_automaticos_actuadores == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_actuadores."'");
            }
        }

        // Comprobación de eliminación de informes automáticos por permisos del módulo Smartmeter (por secciones)
        if ((in_array(MODULO_SMARTMETER, $modulos_usuario) == false) or
            ((count($secciones_usuario[MODULO_SMARTMETER]) > 0) && (in_array(SECCION_SMARTMETER_CONSUMOS_COSTES, $secciones_usuario[MODULO_SMARTMETER]) == false)))
        {
            $operacion_borrado_informes_automaticos_smartmeter = "
                DELETE
                FROM informes_automaticos
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND ((tipo = '".TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES."')
                        OR (tipo = '".TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES."')
                        OR (tipo = '".TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS."')
                        OR (tipo = '".TIPO_INFORME_SMARTMETER_EXCESOS_POTENCIA."')
                        OR (tipo = '".TIPO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA."')
                        OR (tipo = '".TIPO_INFORME_SMARTMETER_CORTES_TENSION."')
                        OR (tipo = '".TIPO_INFORME_SMARTMETER_EXCESOS_CAUDAL."')
                        OR (tipo = '".TIPO_INFORME_SMARTMETER_COMPARACION_PERIODOS."'))";
            $res_borrado_informes_automaticos_smartmeter = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_smartmeter);
            if ($res_borrado_informes_automaticos_smartmeter == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_smartmeter."'");
            }
        }
        if ((in_array(MODULO_SMARTMETER, $modulos_usuario) == false) or
            ((count($secciones_usuario[MODULO_SMARTMETER]) > 0) && (in_array(SECCION_SMARTMETER_FACTURAS, $secciones_usuario[MODULO_SMARTMETER]) == false)))
        {
            $operacion_borrado_informes_automaticos_smartmeter = "
                DELETE
                FROM informes_automaticos
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND ((tipo = '".TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA."'))";
            $res_borrado_informes_automaticos_smartmeter = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_smartmeter);
            if ($res_borrado_informes_automaticos_smartmeter == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_smartmeter."'");
            }
        }
        if ((in_array(MODULO_SMARTMETER, $modulos_usuario) == false) or
            ((count($secciones_usuario[MODULO_SMARTMETER]) > 0) && (in_array(SECCION_SMARTMETER_INFORMES_PERSONALIZADOS, $secciones_usuario[MODULO_SMARTMETER]) == false)))
        {
            $operacion_borrado_informes_automaticos_smartmeter = "
                DELETE
                FROM informes_automaticos
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND ((tipo = '".TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL."'))";
            $res_borrado_informes_automaticos_smartmeter = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_smartmeter);
            if ($res_borrado_informes_automaticos_smartmeter == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_smartmeter."'");
            }
        }

        // Comprobación de eliminación de informes automáticos por permisos del módulo Proyectos
        if ((in_array(MODULO_PROYECTOS, $modulos_usuario) == false) or
            ((count($secciones_usuario[MODULO_PROYECTOS]) > 0) && (in_array(SECCION_PROYECTOS_LINEAS_BASE, $secciones_usuario[MODULO_PROYECTOS]) == false)))
        {
            $operacion_borrado_informes_automaticos_proyectos = "
                DELETE
                FROM informes_automaticos
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND (tipo = '".TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE."')";
            $res_borrado_informes_automaticos_proyectos = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_proyectos);
            if ($res_borrado_informes_automaticos_proyectos == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_proyectos."'");
            }
        }
        if ((in_array(MODULO_PROYECTOS, $modulos_usuario) == false) or
            ((count($secciones_usuario[MODULO_PROYECTOS]) > 0) && (in_array(SECCION_PROYECTOS_INFORMACION, $secciones_usuario[MODULO_PROYECTOS]) == false)))
        {
            $operacion_borrado_informes_automaticos_proyectos = "
                DELETE
                FROM informes_automaticos
                WHERE
                    (usuario = '".$bd_red->_($id_usuario)."')
                    AND (red = '".$bd_red->_($id_red)."')
                    AND (tipo = '".TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO."')";
            $res_borrado_informes_automaticos_proyectos = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_proyectos);
            if ($res_borrado_informes_automaticos_proyectos == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_proyectos."'");
            }
        }

        // Comprobación de eliminación de informes automáticos por eliminación de permisos de nodos individuales
        // - 1. Se recorren cada uno de los informes automáticos del usuario
        // - 2. Se recuperan los identificadores de los sensores, actuadores y grupos de actuadores del informe automático
        // - 3. Si no son visibles por el usuario, se elimina el informe automático
        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                (usuario = '".$bd_red->_($id_usuario)."')
                AND (red = '".$bd_red->_($id_red)."')";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        $ids_informes_automaticos_pendientes_borrado = array();
        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            $ids_sensores_informe_automatico = array();
            $ids_actuadores_informe_automatico = array();
            $ids_grupos_actuadores_informe_automatico = array();
            switch ($fila_informe_automatico["tipo"])
            {
                // Informes automáticos del módulo Sensores
                case TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                {
                    $ids_sensores_informe_automatico = dame_ids_sensores_informe_automatico_sensores(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    $ids_grupos_sensores_informe_automatico = dame_ids_grupos_sensores_informe_automatico_sensores(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    break;
                }
                case TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA:
                case TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD:
                case TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR:
                case TIPO_INFORME_SENSORES_INFORMACION_VIENTO:
                case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA:
                case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA:
                case TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION:
                case TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA:
                case TIPO_INFORME_SENSORES_INFORMACION_GAS:
                case TIPO_INFORME_SENSORES_INFORMACION_AGUA:
                case TIPO_INFORME_SENSORES_INFORMACION_GENERICA:
                case TIPO_INFORME_SENSORES_ANALISIS_HORARIO:
                case TIPO_INFORME_SENSORES_ANALISIS_DIARIO:
                case TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
                case TIPO_INFORME_SENSORES_COMPARACION_PERIODOS:
                case TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
                case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
                case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
                case TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO:
                case TIPO_INFORME_SENSORES_VALORES_GENERALES:
                case TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES:
                case TIPO_INFORME_SENSORES_HISTOGRAMA:
                case TIPO_INFORME_SENSORES_CORRELACION:
                {
                    $ids_sensores_informe_automatico = dame_ids_sensores_informe_automatico_sensores(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    break;
                }
                // Informes automáticos del módulo Actuadores
                case TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                {
                    $ids_actuadores_informe_automatico = dame_ids_actuadores_informe_automatico_actuadores(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    $ids_grupos_actuadores_informe_automatico = dame_ids_grupos_actuadores_informe_automatico_actuadores(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    $ids_sensores_informe_automatico = dame_ids_sensores_informe_automatico_actuadores(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    break;
                }
                // Informes automáticos del módulo Smartmeter
                case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
                case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS:
                case TIPO_INFORME_SMARTMETER_EXCESOS_POTENCIA:
                case TIPO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA:
                case TIPO_INFORME_SMARTMETER_CORTES_TENSION:
                case TIPO_INFORME_SMARTMETER_EXCESOS_CAUDAL:
                case TIPO_INFORME_SMARTMETER_COMPARACION_PERIODOS:
                case TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA:
                case TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL:
                {
                    $ids_sensores_informe_automatico = dame_ids_sensores_informe_automatico_smartmeter(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    break;
                }
                // Informes automáticos del módulos Proyectos
                case TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                case TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO:
                {
                    $ids_sensores_informe_automatico = dame_ids_sensores_informe_automatico_proyectos(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    break;
                }
            }

            // Comprobación de sensores visibles por el usuario
            foreach ($ids_sensores_informe_automatico AS $id_sensor_informe_automatico)
            {
                if (($id_sensor_informe_automatico == "") || ($id_sensor_informe_automatico == ID_NINGUNO))
                {
                    continue;
                }
                $sensor_visible_usuario = false;
                if ($sensor_visible_usuario == false)
                {
                    if (($permiso_todos_sensores == true) ||
                        (dame_sensor_sensores_grupos($id_sensor_informe_automatico, $ids_sensores, $ids_grupos_sensores) == true))
                    {
                        $sensor_visible_usuario = true;
                    }
                }
                if ($sensor_visible_usuario == false)
                {
                    if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                    {
                        if (in_array($id_sensor_informe_automatico, $ids_sensores_visibles_localizaciones) == true)
                        {
                            $sensor_visible_usuario = true;
                        }
                    }
                }
                if ($sensor_visible_usuario == false)
                {
                    array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
                    break;
                }
            }

            // Comprobación de grupos de sensores visibles por el usuario
            foreach ($ids_grupos_sensores_informe_automatico AS $id_grupo_sensores_informe_automatico)
            {
                $grupo_sensores_visible_usuario = false;
                if ($grupo_sensores_visible_usuario == false)
                {
                    if (($permiso_todos_sensores == true) ||
                        (in_array($id_grupo_sensores_informe_automatico, $ids_grupos_sensores) == true))
                    {
                        $grupo_sensores_visible_usuario = true;
                    }
                }
                if ($grupo_sensores_visible_usuario == false)
                {
                    if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                    {
                        if (in_array($id_grupo_sensores_informe_automatico, $ids_grupos_sensores_visibles_localizaciones) == true)
                        {
                            $grupo_sensores_visible_usuario = true;
                        }
                    }
                }
                if ($grupo_sensores_visible_usuario == false)
                {
                    array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
                    break;
                }
            }

            // Comprobación de actuadores visibles por el usuario
            foreach ($ids_actuadores_informe_automatico AS $id_actuador_informe_automatico)
            {
                if (($id_actuador_informe_automatico == "") || ($id_actuador_informe_automatico == ID_NINGUNO))
                {
                    continue;
                }
                $actuador_visible_usuario = false;
                if ($actuador_visible_usuario == false)
                {
                    if (($permiso_todos_actuadores == true) ||
                        (dame_actuador_actuadores_grupos($id_actuador_informe_automatico, $ids_actuadores, $ids_grupos_actuadores) == true))
                    {
                        $actuador_visible_usuario = true;
                    }
                }
                if ($actuador_visible_usuario == false)
                {
                    if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                    {
                        if (in_array($id_actuador_informe_automatico, $ids_actuadores_visibles_localizaciones) == true)
                        {
                            $actuador_visible_usuario = true;
                        }
                    }
                }
                if ($actuador_visible_usuario == false)
                {
                    array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
                    break;
                }
            }

            // Comprobación de grupos de actuadores visibles por el usuario
            foreach ($ids_grupos_actuadores_informe_automatico AS $id_grupo_actuadores_informe_automatico)
            {
                $grupo_actuadores_visible_usuario = false;
                if ($grupo_actuadores_visible_usuario == false)
                {
                    if (($permiso_todos_actuadores == true) ||
                        (in_array($id_grupo_actuadores_informe_automatico, $ids_grupos_actuadores) == true))
                    {
                        $grupo_actuadores_visible_usuario = true;
                    }
                }
                if ($grupo_actuadores_visible_usuario == false)
                {
                    if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                    {
                        if (in_array($id_grupo_actuadores_informe_automatico, $ids_grupos_actuadores_visibles_localizaciones) == true)
                        {
                            $grupo_actuadores_visible_usuario = true;
                        }
                    }
                }
                if ($grupo_actuadores_visible_usuario == false)
                {
                    array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
                    break;
                }
            }
        }

        // Se borran los informes automáticos pendientes de borrado
        if (count($ids_informes_automaticos_pendientes_borrado) > 0)
        {
            $cadena_ids_informes_automaticos_pendientes_borrado = dame_cadena_ids_consulta($ids_informes_automaticos_pendientes_borrado);
            $operacion_borrado_informes_automaticos_pendientes = "
                DELETE
                FROM informes_automaticos
                WHERE
                    id IN (".$cadena_ids_informes_automaticos_pendientes_borrado.")";
            $res_borrado_informes_automaticos_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_pendientes);
            if ($res_borrado_informes_automaticos_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_pendientes."'");
            }
        }
    }


    // Elimina los informes automáticos correspondientes al eliminar un ratio
    function elimina_informes_automaticos_ratio_eliminado($id_ratio)
    {
        // - 1. Se recorren cada uno de los informes automáticos de todos los usuarios
        // - 2. Se recupera el identificador de ratio del informe automático
        // - 3. Si el ratio es el mismo ratio del informe, se elimina el informe automático

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        $ids_informes_automaticos_pendientes_borrado = array();
        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            // Nota: Sólo se recupera el ratio de los módulos que tienen informes automáticos con ratios
            $modulo = dame_modulo_tipo_informe_automatico($fila_informe_automatico["tipo"]);
            switch ($modulo)
            {
                case MODULO_SENSORES:
                {
                    $id_ratio_informe_automatico = dame_id_ratio_informe_automatico_sensores(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    if (($id_ratio !== NULL) && ($id_ratio_informe_automatico == $id_ratio))
                    {
                        array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
                    }
                    break;
                }
                case MODULO_SMARTMETER:
                {
                    $id_ratio_informe_automatico = dame_id_ratio_informe_automatico_smartmeter(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    if (($id_ratio !== NULL) && ($id_ratio_informe_automatico == $id_ratio))
                    {
                        array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
                    }
                }
            }
        }
        if (count($ids_informes_automaticos_pendientes_borrado) > 0)
        {
            $cadena_ids_informes_automaticos_pendientes_borrado = dame_cadena_ids_consulta($ids_informes_automaticos_pendientes_borrado);
            $operacion_borrado_informes_automaticos_pendientes = "
                DELETE
                FROM informes_automaticos
                WHERE
                    id IN (".$cadena_ids_informes_automaticos_pendientes_borrado.")";
            $res_borrado_informes_automaticos_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_pendientes);
            if ($res_borrado_informes_automaticos_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_pendientes."'");
            }
        }
    }


    // Elimina los informes automáticos correspondientes al eliminar una plantilla de informe
    function elimina_informes_automaticos_plantilla_informe_eliminada($id_plantilla_informe)
    {
        // - 1. Se recorren cada uno de los informes automáticos de todos los usuarios
        // - 2. Se recupera el identificador de plantilla de informe
        // - 3. Si la plantilla de informe es la misma plantilla de informe, se elimina el informe automático

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (tipo = '".TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME."')";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        $ids_informes_automaticos_pendientes_borrado = array();
        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_informe_automatico["parametros_tipo"]);
            $id_plantilla_informe_informe_automatico = $parametros_tipo[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_ID_PLANTILLA_INFORME];
            if ($id_plantilla_informe == $id_plantilla_informe_informe_automatico)
            {
                array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
            }
        }
        if (count($ids_informes_automaticos_pendientes_borrado) > 0)
        {
            $cadena_ids_informes_automaticos_pendientes_borrado = dame_cadena_ids_consulta($ids_informes_automaticos_pendientes_borrado);
            $operacion_borrado_informes_automaticos_pendientes = "
                DELETE
                FROM informes_automaticos
                WHERE
                    id IN (".$cadena_ids_informes_automaticos_pendientes_borrado.")";
            $res_borrado_informes_automaticos_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_pendientes);
            if ($res_borrado_informes_automaticos_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_pendientes."'");
            }
        }
    }


    // Elimina los informes automáticos correspondientes al eliminar un sensor
    function elimina_informes_automaticos_sensor_eliminado($id_sensor)
    {
        // - 1. Se recorren cada uno de los informes automáticos de todos los usuarios
        // - 2. Se recuperan los identificadores de los sensores del informe automático
        // - 3. Si el sensor está en los sensores del informe, se elimina el informe automático

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        $ids_informes_automaticos_pendientes_borrado = array();
        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            // Nota: Sólo se recuperan los sensores de los módulos que tienen informes automáticos con sensores
            $modulo = dame_modulo_tipo_informe_automatico($fila_informe_automatico["tipo"]);
            switch ($modulo)
            {
                case MODULO_SENSORES:
                {
                    $ids_sensores_informe_automatico = dame_ids_sensores_informe_automatico_sensores(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    if (dame_sensor_sensores_grupos($id_sensor, $ids_sensores_informe_automatico, array()) == true)
                    {
                        array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
                    }
                    break;
                }
                case MODULO_ACTUADORES:
                {
                    $ids_sensores_informe_automatico = dame_ids_sensores_informe_automatico_actuadores(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    if (dame_sensor_sensores_grupos($id_sensor, $ids_sensores_informe_automatico, array()) == true)
                    {
                        array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
                    }
                    break;
                }
                case MODULO_SMARTMETER:
                {
                    $ids_sensores_informe_automatico = dame_ids_sensores_informe_automatico_smartmeter(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    if (dame_sensor_sensores_grupos($id_sensor, $ids_sensores_informe_automatico, array()) == true)
                    {
                        array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
                    }
                    break;
                }
            }
        }
        if (count($ids_informes_automaticos_pendientes_borrado) > 0)
        {
            $cadena_ids_informes_automaticos_pendientes_borrado = dame_cadena_ids_consulta($ids_informes_automaticos_pendientes_borrado);
            $operacion_borrado_informes_automaticos_pendientes = "
                DELETE
                FROM informes_automaticos
                WHERE
                    id IN (".$cadena_ids_informes_automaticos_pendientes_borrado.")";
            $res_borrado_informes_automaticos_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_pendientes);
            if ($res_borrado_informes_automaticos_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_pendientes."'");
            }
        }
    }


    // Elimina los informes automáticos correspondientes al eliminar un grupo de sensores
    function elimina_informes_automaticos_grupo_sensores_eliminado($id_grupo_sensores)
    {
        // - 1. Se recorren cada uno de los informes automáticos de todos los usuarios
        // - 2. Se recuperan los identificadores de los grupos de sensores del informe automático
        // - 3. Si el grupo de sensores está en los grupos de sensores del informe, se elimina el informe automático

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        $ids_informes_automaticos_pendientes_borrado = array();
        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            // Nota: Sólo se recuperan los grupos de sensores de los módulos que tienen informes automáticos con grupos de sensores
            $modulo = dame_modulo_tipo_informe_automatico($fila_informe_automatico["tipo"]);
            switch ($modulo)
            {
                case MODULO_SENSORES:
                {
                    $ids_grupos_sensores_informe_automatico = dame_ids_grupos_sensores_informe_automatico_sensores(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    if (in_array($id_grupo_sensores, $ids_grupos_sensores_informe_automatico) == true)
                    {
                        array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
                    }
                    break;
                }
            }
        }
        if (count($ids_informes_automaticos_pendientes_borrado) > 0)
        {
            $cadena_ids_informes_automaticos_pendientes_borrado = dame_cadena_ids_consulta($ids_informes_automaticos_pendientes_borrado);
            $operacion_borrado_informes_automaticos_pendientes = "
                DELETE
                FROM informes_automaticos
                WHERE
                    id IN (".$cadena_ids_informes_automaticos_pendientes_borrado.")";
            $res_borrado_informes_automaticos_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_pendientes);
            if ($res_borrado_informes_automaticos_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_pendientes."'");
            }
        }
    }


    // Elimina los informes automáticos correspondientes al eliminar un evento
    function elimina_informes_automaticos_evento_eliminado($id_evento)
    {
        // - 1. Se recorren cada uno de los informes automáticos de todos los usuarios
        // - 2. Se recuperan los identificadores de los eventos del informe automático
        // - 3. Si el evento está en los eventos del informe, se elimina el informe automático

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        $ids_informes_automaticos_pendientes_borrado = array();
        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            // Nota: Sólo se recuperan los grupos de sensores de los módulos que tienen informes automáticos con eventos
            $modulo = dame_modulo_tipo_informe_automatico($fila_informe_automatico["tipo"]);
            switch ($modulo)
            {
                case MODULO_SENSORES:
                {
                    $ids_eventos_informe_automatico = dame_ids_eventos_informe_automatico_sensores(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    if (in_array($id_evento, $ids_eventos_informe_automatico) == true)
                    {
                        array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
                    }
                    break;
                }
            }
        }
        if (count($ids_informes_automaticos_pendientes_borrado) > 0)
        {
            $cadena_ids_informes_automaticos_pendientes_borrado = dame_cadena_ids_consulta($ids_informes_automaticos_pendientes_borrado);
            $operacion_borrado_informes_automaticos_pendientes = "
                DELETE
                FROM informes_automaticos
                WHERE
                    id IN (".$cadena_ids_informes_automaticos_pendientes_borrado.")";
            $res_borrado_informes_automaticos_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_pendientes);
            if ($res_borrado_informes_automaticos_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_pendientes."'");
            }
        }
    }


    // Elimina los informes automáticos correspondientes al eliminar un actuador
    function elimina_informes_automaticos_actuador_eliminado($id_actuador)
    {
        // - 1. Se recorren cada uno de los informes automáticos de todos los usuarios
        // - 2. Se recuperan los identificadores de los actuadores del informe automático
        // - 3. Si el actuador está en los actuadores del informe, se elimina el informe automático

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        $ids_informes_automaticos_pendientes_borrado = array();
        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            // Nota: Sólo se recuperan los actuadores de los módulos que tienen informes automáticos con actuadores
            $modulo = dame_modulo_tipo_informe_automatico($fila_informe_automatico["tipo"]);
            switch ($modulo)
            {
                case MODULO_ACTUADORES:
                {
                    $ids_actuadores_informe_automatico = dame_ids_actuadores_informe_automatico_actuadores(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    if (dame_actuador_actuadores_grupos($id_actuador, $ids_actuadores_informe_automatico, array()) == true)
                    {
                        array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
                    }
                    break;
                }
            }
        }
        if (count($ids_informes_automaticos_pendientes_borrado) > 0)
        {
            $cadena_ids_informes_automaticos_pendientes_borrado = dame_cadena_ids_consulta($ids_informes_automaticos_pendientes_borrado);
            $operacion_borrado_informes_automaticos_pendientes = "
                DELETE
                FROM informes_automaticos
                WHERE
                    id IN (".$cadena_ids_informes_automaticos_pendientes_borrado.")";
            $res_borrado_informes_automaticos_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_pendientes);
            if ($res_borrado_informes_automaticos_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_pendientes."'");
            }
        }
    }


    // Elimina los informes automáticos correspondientes al eliminar un grupo de actuadores
    function elimina_informes_automaticos_grupo_actuadores_eliminado($id_grupo_actuadores)
    {
        // - 1. Se recorren cada uno de los informes automáticos de todos los usuarios
        // - 2. Se recuperan los identificadores de los grupos de actuadores del informe automático
        // - 3. Si el grupo de actuadores está en los grupos de actuadores del informe, se elimina el informe automático

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        $ids_informes_automaticos_pendientes_borrado = array();
        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            // Nota: Sólo se recuperan los grupos de actuadores de los módulos que tienen informes automáticos con grupos de actuadores
            $modulo = dame_modulo_tipo_informe_automatico($fila_informe_automatico["tipo"]);
            switch ($modulo)
            {
                case MODULO_ACTUADORES:
                {
                    $ids_grupos_actuadores_informe_automatico = dame_ids_grupos_actuadores_informe_automatico_actuadores(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    if (in_array($id_grupo_actuadores, $ids_grupos_actuadores_informe_automatico) == true)
                    {
                        array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
                    }
                    break;
                }
            }
        }
        if (count($ids_informes_automaticos_pendientes_borrado) > 0)
        {
            $cadena_ids_informes_automaticos_pendientes_borrado = dame_cadena_ids_consulta($ids_informes_automaticos_pendientes_borrado);
            $operacion_borrado_informes_automaticos_pendientes = "
                DELETE
                FROM informes_automaticos
                WHERE
                    id IN (".$cadena_ids_informes_automaticos_pendientes_borrado.")";
            $res_borrado_informes_automaticos_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_pendientes);
            if ($res_borrado_informes_automaticos_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_pendientes."'");
            }
        }
    }


    // Elimina los informes automáticos correspondientes al eliminar una tarifa
    function elimina_informes_automaticos_tarifa_eliminada($medicion, $id_tarifa)
    {
        // - 1. Se recorren cada uno de los informes automáticos de todos los usuarios
        // - 2. Se recuperan los identificadores de las tarifas del informe automático
        // - 3. Si la tarifa está en las tarifas del informe, se elimina el informe automático

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        $ids_informes_automaticos_pendientes_borrado = array();
        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            // Nota: Sólo se recuperan los sensores de los módulos que tienen informes automáticos con sensores
            $modulo = dame_modulo_tipo_informe_automatico($fila_informe_automatico["tipo"]);
            switch ($modulo)
            {
                case MODULO_SMARTMETER:
                {
                    $ids_tarifas_informe_automatico = dame_ids_tarifas_informe_automatico_smartmeter(
                        $medicion,
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    if (in_array($id_tarifa, $ids_tarifas_informe_automatico) == true)
                    {
                        array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
                    }
                    break;
                }
            }
        }
        if (count($ids_informes_automaticos_pendientes_borrado) > 0)
        {
            $cadena_ids_informes_automaticos_pendientes_borrado = dame_cadena_ids_consulta($ids_informes_automaticos_pendientes_borrado);
            $operacion_borrado_informes_automaticos_pendientes = "
                DELETE
                FROM informes_automaticos
                WHERE
                    id IN (".$cadena_ids_informes_automaticos_pendientes_borrado.")";
            $res_borrado_informes_automaticos_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_pendientes);
            if ($res_borrado_informes_automaticos_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_pendientes."'");
            }
        }
    }


    // Elimina los informes automáticos correspondientes al eliminar una línea base
    function elimina_informes_automaticos_linea_base_eliminada($id_linea_base)
    {
        // - 1. Se recorren cada uno de los informes automáticos de todos los usuarios
        // - 2. Se recuperan los identificadores de las líneas base del informe automático
        // - 3. Si la línea base está en las líneas base del informe, se elimina el informe automático

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        $ids_informes_automaticos_pendientes_borrado = array();
        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            // Nota: Sólo se recuperan las líneas base de los módulos que tienen informes automáticos con líneas base
            $modulo = dame_modulo_tipo_informe_automatico($fila_informe_automatico["tipo"]);
            switch ($modulo)
            {
                case MODULO_PROYECTOS:
                {
                    $ids_lineas_base_informe_automatico = dame_ids_lineas_base_informe_automatico_proyectos(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    if (in_array($id_linea_base, $ids_lineas_base_informe_automatico) == true)
                    {
                        array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
                    }
                    break;
                }
            }
        }
        if (count($ids_informes_automaticos_pendientes_borrado) > 0)
        {
            $cadena_ids_informes_automaticos_pendientes_borrado = dame_cadena_ids_consulta($ids_informes_automaticos_pendientes_borrado);
            $operacion_borrado_informes_automaticos_pendientes = "
                DELETE
                FROM informes_automaticos
                WHERE
                    id IN (".$cadena_ids_informes_automaticos_pendientes_borrado.")";
            $res_borrado_informes_automaticos_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_pendientes);
            if ($res_borrado_informes_automaticos_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_pendientes."'");
            }
        }
    }


    // Elimina los informes automáticos correspondientes al eliminar un proyecto
    function elimina_informes_automaticos_proyecto_eliminado($id_proyecto)
    {
        // - 1. Se recorren cada uno de los informes automáticos de todos los usuarios
        // - 2. Se recuperan los identificadores de los proyectos del informe automático
        // - 3. Si el proyecto está en los proyectos del informe, se elimina el informe automático

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                red = '".$_SESSION["id_red"]."'";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        $ids_informes_automaticos_pendientes_borrado = array();
        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            // Nota: Sólo se recuperan los proyectos de los módulos que tienen informes automáticos con proyectos
            $modulo = dame_modulo_tipo_informe_automatico($fila_informe_automatico["tipo"]);
            switch ($modulo)
            {
                case MODULO_PROYECTOS:
                {
                    $ids_proyectos_informe_automatico = dame_ids_proyectos_informe_automatico_proyectos(
                        $fila_informe_automatico["tipo"],
                        $fila_informe_automatico["parametros_tipo"]);
                    if (in_array($id_proyecto, $ids_proyectos_informe_automatico) == true)
                    {
                        array_push($ids_informes_automaticos_pendientes_borrado, $fila_informe_automatico["id"]);
                    }
                    break;
                }
            }
        }
        if (count($ids_informes_automaticos_pendientes_borrado) > 0)
        {
            $cadena_ids_informes_automaticos_pendientes_borrado = dame_cadena_ids_consulta($ids_informes_automaticos_pendientes_borrado);
            $operacion_borrado_informes_automaticos_pendientes = "
                DELETE
                FROM informes_automaticos
                WHERE
                    id IN (".$cadena_ids_informes_automaticos_pendientes_borrado.")";
            $res_borrado_informes_automaticos_pendientes = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_pendientes);
            if ($res_borrado_informes_automaticos_pendientes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_pendientes."'");
            }
        }
    }


    //
    // Funciones de parámetros de plantillas de informes de informes automáticos
    //


    // Añade el parámetro (con valor 'ninguno') a los informes automáticos de plantillas de informes (configurables) correspondientes
    function anyade_parametro_informes_automaticos_plantilla_informe($id_plantilla_informe, $id_parametro)
    {
        // - 1. Se recorren cada uno de los informes automáticos de plantillas de informes de todos los usuarios
        // - 2. Se recupera el identificador de plantilla de informe
        // - 3. Si la plantilla de informe es la misma plantilla de informe, se añade el parámetro al informe automático correspondiente

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (tipo = '".TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME."')";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            $id_informe_automatico = $fila_informe_automatico["id"];
            $parametros_tipo_informe_automatico = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_informe_automatico["parametros_tipo"]);

            $id_plantilla_informe_informe_automatico = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_ID_PLANTILLA_INFORME];
            if ($id_plantilla_informe == $id_plantilla_informe_informe_automatico)
            {
                // Parámetros de tipo del informe automático
                $cadena_ids_parametros = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_PARAMETROS];
                $cadena_ids_elementos_texto = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TEXTO];
                $cadena_ids_elementos_imagen = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_IMAGEN];
                $cadena_imagenes_personalizadas_elementos_texto = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IMAGENES_PERSONALIZADAS_ELEMENTOS_IMAGEN];
                $cadena_horario_semanal = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_INCLUSION_FECHAS];
                $cadena_valores_parametros = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_VALORES_PARAMETROS];

                // Conversiones
                if ($cadena_ids_parametros == "")
                {
                    $ids_parametros = array();
                }
                else
                {
                    $ids_parametros = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros);
                }
                if ($cadena_valores_parametros == "")
                {
                    $valores_parametros = array();
                }
                else
                {
                    $valores_parametros = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_valores_parametros);
                }

                // Se añade el nuevo parámetro a los parámetros de tipo (el identificador y el valor - ninguno)
                array_push($ids_parametros, $id_parametro);
                array_push($valores_parametros, ID_NINGUNO);
                $cadena_ids_parametros_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros);
                $cadena_valores_parametros_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $valores_parametros);

                // Se modifican los parámetros de tipo
                $cadena_parametros_tipo_modificada = implode(SEPARADOR_PARAMETROS_COMPUESTOS, array(
                    $id_plantilla_informe,
                    $cadena_ids_parametros_modificada,
                    $cadena_valores_parametros_modificada,
                    $cadena_ids_elementos_texto,
                    $cadena_ids_elementos_imagen,
                    $cadena_imagenes_personalizadas_elementos_texto,
                    $cadena_horario_semanal,
                    $cadena_exclusion_fechas,
                    $cadena_inclusion_fechas));
                $operacion_modificacion_informes_automaticos = "
                    UPDATE informes_automaticos
                    SET
                        parametros_tipo = '".$bd_red->_($cadena_parametros_tipo_modificada)."'
                    WHERE
                        id = '".$bd_red->_($id_informe_automatico)."'";
                $res_modificacion_informes_automaticos = $bd_red->ejecuta_operacion($operacion_modificacion_informes_automaticos);
                if ($res_modificacion_informes_automaticos == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_modificacion_informes_automaticos."'");
                }
            }
        }
    }


    // Elimina el parámetro de plantilla de informe configurable de los informes automáticos correspondientes
    function elimina_parametro_informes_automaticos_plantilla_informe($id_plantilla_informe, $id_parametro)
    {
        // - 1. Se recorren cada uno de los informes automáticos de plantillas de informes de todos los usuarios
        // - 2. Se recupera el identificador de plantilla de informe
        // - 3. Si la plantilla de informe es la misma plantilla de informe, se elimina el parámetro del informe automático correspondiente

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (tipo = '".TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME."')";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            $id_informe_automatico = $fila_informe_automatico["id"];
            $parametros_tipo_informe_automatico = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_informe_automatico["parametros_tipo"]);

            $id_plantilla_informe_informe_automatico = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_ID_PLANTILLA_INFORME];
            if ($id_plantilla_informe == $id_plantilla_informe_informe_automatico)
            {
                // Parámetros de tipo del informe automático
                $cadena_ids_parametros = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_PARAMETROS];
                $cadena_valores_parametros = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_VALORES_PARAMETROS];
                $cadena_ids_elementos_portada = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_PORTADA];
                $cadena_ids_elementos_titulo = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TITULO];
                $cadena_ids_elementos_texto = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TEXTO];
                $cadena_ids_elementos_imagen = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_IMAGEN];
                $cadena_imagenes_personalizadas_elementos_texto = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IMAGENES_PERSONALIZADAS_ELEMENTOS_IMAGEN];
                $cadena_horario_semanal = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_INCLUSION_FECHAS];

                // Conversiones
                if ($cadena_ids_parametros == "")
                {
                    $ids_parametros = array();
                }
                else
                {
                    $ids_parametros = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros);
                }
                if ($cadena_valores_parametros == "")
                {
                    $valores_parametros = array();
                }
                else
                {
                    $valores_parametros = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_valores_parametros);
                }

                // Si existe el parámetro, se elimina de los parámetros de tipo
                $indice_parametro = array_search($id_parametro, $ids_parametros);
                if (($indice_parametro !== false) && ($indice_parametro !== NULL))
                {
                    unset($ids_parametros[$indice_parametro]);
                    unset($valores_parametros[$indice_parametro]);
                    $cadena_ids_parametros_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros);
                    $cadena_valores_parametros_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $valores_parametros);

                    // Se modifican los parámetros de tipo
                    $cadena_parametros_tipo_modificada = implode(SEPARADOR_PARAMETROS_COMPUESTOS, array(
                        $id_plantilla_informe,
                        $cadena_ids_parametros_modificada,
                        $cadena_valores_parametros_modificada,
                        $cadena_ids_elementos_portada,
                        $cadena_ids_elementos_titulo,
                        $cadena_ids_elementos_texto,
                        $cadena_ids_elementos_imagen,
                        $cadena_imagenes_personalizadas_elementos_texto,
                        $cadena_horario_semanal,
                        $cadena_exclusion_fechas,
                        $cadena_inclusion_fechas));
                    $operacion_modificacion_informes_automaticos = "
                        UPDATE informes_automaticos
                        SET
                            parametros_tipo = '".$bd_red->_($cadena_parametros_tipo_modificada)."'
                        WHERE
                            id = '".$bd_red->_($id_informe_automatico)."'";
                    $res_modificacion_informes_automaticos = $bd_red->ejecuta_operacion($operacion_modificacion_informes_automaticos);
                    if ($res_modificacion_informes_automaticos == false)
                    {
                        throw new Exception("Error en la operación: '".$operacion_modificacion_informes_automaticos."'");
                    }
                }
            }
        }
    }


    // Se modifican los informes automáticos de plantillas de informes (configurables) que tengan elementos no visibles seleccionados en algún parámetro
    function modifica_informes_automaticos_plantillas_informes_no_visibles_usuario(
        $id_usuario,
        $perfil,
        $id_red,
        $parametros_usuario)
    {
        // Se modifican (establece los valores de parámetros a ninguno) los parámetros con objetos no visibles

        $bd_red = BaseDatosRed::dame_base_datos();

        // Módulos del usuario
        $modulos_usuario = dame_modulos_usuario($id_usuario, $perfil, $id_red);

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

        // 1. Se guardan los parámetros de plantillas de informes del usuario
        // 2. Se recorren los informes automáticos de plantillas de informes del usuario
        // 3. Si algún valor de parámetro del informe no es visible por el usuario, se establece a ninguno
        $consulta_parametros_plantillas_informes = "
            SELECT
                parametros_plantillas_informes.id,
                parametros_plantillas_informes.tipo
            FROM
                parametros_plantillas_informes,
                plantillas_informes
            WHERE
                (parametros_plantillas_informes.plantilla_informe = plantillas_informes.id)
                AND (plantillas_informes.red = '".$_SESSION["id_red"]."')
                AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')";
        $res_parametros_plantillas_informes = $bd_red->ejecuta_consulta($consulta_parametros_plantillas_informes);
        if ($res_parametros_plantillas_informes == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros_plantillas_informes."'");
        }
        $ids_parametros_plantillas_informes = array();
        $tipos_parametros_plantillas_informes = array();
        while ($fila_parametro_plantilla_informe = $res_parametros_plantillas_informes->dame_siguiente_fila())
        {
            array_push($ids_parametros_plantillas_informes, $fila_parametro_plantilla_informe["id"]);
            array_push($tipos_parametros_plantillas_informes, $fila_parametro_plantilla_informe["tipo"]);
        }

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (tipo = '".TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME."')
                AND (usuario = '".$bd_red->_($id_usuario)."')";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }
        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            $id_informe_automatico = $fila_informe_automatico["id"];
            $parametros_tipo_informe_automatico = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_informe_automatico["parametros_tipo"]);

            // Identificadores y valores de parámetros del informe automático de plantilla de informe
            $cadena_ids_parametros = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_PARAMETROS];
            $cadena_valores_parametros = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_VALORES_PARAMETROS];
            if ($cadena_ids_parametros == "")
            {
                $ids_parametros = array();
            }
            else
            {
                $ids_parametros = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros);
            }
            if ($cadena_valores_parametros == "")
            {
                $valores_parametros = array();
            }
            else
            {
                $valores_parametros = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_valores_parametros);
            }

            // Se recorren cada uno de los parámetros del informe automático
            // - Si el valor del parámetro no es visible por el usuario (el identificador correspondiente), se establece el valor del parámetro a ninguno
            $parametros_tipo_informe_automatico_modificados = false;
            for ($i = 0; $i < count($ids_parametros); $i++)
            {
                $id_parametro = $ids_parametros[$i];
                $valor_parametro = $valores_parametros[$i];
                if ($valor_parametro == ID_NINGUNO)
                {
                    continue;
                }

                $indice_parametro = array_search($id_parametro, $ids_parametros_plantillas_informes);
                if (($indice_parametro !== false) && ($indice_parametro !== NULL))
                {
                    $tipo_parametro_plantilla_informe = $tipos_parametros_plantillas_informes[$indice_parametro];
                    switch ($tipo_parametro_plantilla_informe)
                    {
                        case TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR:
                        {
                            $sensor_visible_usuario = false;
                            if ($sensor_visible_usuario == false)
                            {
                                if (($permiso_todos_sensores == true) ||
                                    (dame_sensor_sensores_grupos($valor_parametro, $ids_sensores, $ids_grupos_sensores) == true))
                                {
                                    $sensor_visible_usuario = true;
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($valor_parametro, $ids_sensores_visibles_localizaciones) == true)
                                    {
                                        $sensor_visible_usuario = true;
                                    }
                                }
                            }
                            if ($sensor_visible_usuario == false)
                            {
                                $valores_parametros[$i] = ID_NINGUNO;
                                $parametros_tipo_informe_automatico_modificados = true;
                            }
                            break;
                        }
                        case TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES:
                        {
                            $grupo_sensores_visible_usuario = false;
                            if ($grupo_sensores_visible_usuario == false)
                            {
                                if (($permiso_todos_sensores == true) ||
                                    (in_array($valor_parametro, $ids_grupos_sensores) == true))
                                {
                                    $grupo_sensores_visible_usuario = true;
                                }
                            }
                            if ($grupo_sensores_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($valor_parametro, $ids_grupos_sensores_visibles_localizaciones) == true)
                                    {
                                        $grupo_sensores_visible_usuario = true;
                                    }
                                }
                            }
                            if ($grupo_sensores_visible_usuario == false)
                            {
                                $valores_parametros[$i] = ID_NINGUNO;
                                $parametros_tipo_informe_automatico_modificados = true;
                            }
                            break;
                        }
                        case TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR:
                        {
                            $actuador_visible_usuario = false;
                            if ($actuador_visible_usuario == false)
                            {
                                if (($permiso_todos_actuadores == true) ||
                                    (dame_actuador_actuadores_grupos($valor_parametro, $ids_actuadores, $ids_grupos_actuadores) == true))
                                {
                                    $actuador_visible_usuario = true;
                                }
                            }
                            if ($actuador_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($valor_parametro, $ids_actuadores_visibles_localizaciones) == true)
                                    {
                                        $actuador_visible_usuario = true;
                                    }
                                }
                            }
                            if ($actuador_visible_usuario == false)
                            {
                                $valores_parametros[$i] = ID_NINGUNO;
                                $parametros_tipo_informe_automatico_modificados = true;
                            }
                            break;
                        }
                        case TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
                        {
                            $grupo_actuadores_visible_usuario = false;
                            if ($grupo_actuadores_visible_usuario == false)
                            {
                                if (($permiso_todos_actuadores == true) ||
                                    (in_array($valor_parametro, $ids_grupos_actuadores) == true))
                                {
                                    $grupo_actuadores_visible_usuario = true;
                                }
                            }
                            if ($grupo_actuadores_visible_usuario == false)
                            {
                                if (in_array(MODULO_LOCALIZACIONES, $modulos_usuario) == true)
                                {
                                    if (in_array($valor_parametro, $ids_grupos_actuadores_visibles_localizaciones) == true)
                                    {
                                        $grupo_actuadores_visible_usuario = true;
                                    }
                                }
                            }
                            if ($grupo_actuadores_visible_usuario == false)
                            {
                                $valores_parametros[$i] = ID_NINGUNO;
                                $parametros_tipo_informe_automatico_modificados = true;
                            }
                            break;
                        }
                        case TIPO_PARAMETRO_PLANTILLA_INFORME_LINEA_BASE:
                        {
                            $linea_base_visible_usuario = false;
                            if (in_array($valor_parametro, $ids_todas_lineas_base_visibles_usuario) == true)
                            {
                                $linea_base_visible_usuario = true;
                            }
                            if ($linea_base_visible_usuario == false)
                            {
                                $valores_parametros[$i] = ID_NINGUNO;
                                $parametros_tipo_informe_automatico_modificados = true;
                            }
                            break;
                        }
                        case TIPO_PARAMETRO_PLANTILLA_INFORME_PROYECTO:
                        {
                            $proyecto_visible_usuario = false;
                            if (in_array($valor_parametro, $ids_todos_proyectos_visibles_usuario) == true)
                            {
                                $proyecto_visible_usuario = true;
                            }
                            if ($proyecto_visible_usuario == false)
                            {
                                $valores_parametros[$i] = ID_NINGUNO;
                                $parametros_tipo_informe_automatico_modificados = true;
                            }
                            break;
                        }
                        default:
                        {
                            throw new Exception("Tipo de parámetro desconocido: '".$tipo_parametro_plantilla_informe."'");
                        }
                    }
                }
            }

            // Se actualizan los parámetros de tipo del informe automático (si se ha modificado el valor de algún parámetro)
            if ($parametros_tipo_informe_automatico_modificados == true)
            {
                $cadena_valores_parametros_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $valores_parametros);
                $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_VALORES_PARAMETROS] = $cadena_valores_parametros_modificada;
                $cadena_parametros_tipo_modificada = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo_informe_automatico);
                $operacion_modificacion_informes_automaticos = "
                    UPDATE informes_automaticos
                    SET
                        parametros_tipo = '".$bd_red->_($cadena_parametros_tipo_modificada)."'
                    WHERE
                        id = '".$bd_red->_($id_informe_automatico)."'";
                $res_modificacion_informes_automaticos = $bd_red->ejecuta_operacion($operacion_modificacion_informes_automaticos);
                if ($res_modificacion_informes_automaticos == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_modificacion_informes_automaticos."'");
                }
            }
        }
    }


    // Se modifican los informes automáticos de plantillas de informes (configurables) que tengan este sensor seleccionado en algún parámetro
    function modifica_informes_automaticos_plantillas_informes_sensor_eliminado($id_sensor)
    {
        modifica_informes_automaticos_plantillas_informes_valor_parametro_eliminado(TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR, $id_sensor);
    }


    // Se modifican los informes automáticos de plantillas de informes (configurables) que tengan este grupo de sensores seleccionado en algún parámetro
    function modifica_informes_automaticos_plantillas_informes_grupo_sensores_eliminado($id_grupo_sensores)
    {
        modifica_informes_automaticos_plantillas_informes_valor_parametro_eliminado(TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES, $id_grupo_sensores);
    }


    // Se modifican los informes automáticos de plantillas de informes (configurables) que tengan este actuador seleccionado en algún parámetro
    function modifica_informes_automaticos_plantillas_informes_actuador_eliminado($id_actuador)
    {
        modifica_informes_automaticos_plantillas_informes_valor_parametro_eliminado(TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR, $id_actuador);
    }


    // Se modifican los informes automáticos de plantillas de informes (configurables) que tengan este grupo de actuadores seleccionado en algún parámetro
    function modifica_informes_automaticos_plantillas_informes_grupo_actuadores_eliminado($id_grupo_actuadores)
    {
        modifica_informes_automaticos_plantillas_informes_valor_parametro_eliminado(TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES, $id_grupo_actuadores);
    }


    // Se modifican los informes automáticos de plantillas de informes (configurables) que tengan esta línea base seleccionada en algún parámetro
    function modifica_informes_automaticos_plantillas_informes_linea_base_eliminada($id_linea_base)
    {
        modifica_informes_automaticos_plantillas_informes_valor_parametro_eliminado(TIPO_PARAMETRO_PLANTILLA_INFORME_LINEA_BASE, $id_linea_base);
    }


    // Se modifican los informes automáticos de plantillas de informes (configurables) que tengan este proyecto seleccionado en algún parámetro
    function modifica_informes_automaticos_plantillas_informes_proyecto_eliminado($id_proyecto)
    {
        modifica_informes_automaticos_plantillas_informes_valor_parametro_eliminado(TIPO_PARAMETRO_PLANTILLA_INFORME_PROYECTO, $id_proyecto);
    }


    // Se modifican los informes automáticos de plantillas de informes (configurables) que tengan este identificador seleccionado en algún parámetro
    function modifica_informes_automaticos_plantillas_informes_valor_parametro_eliminado($tipo_parametro, $id_eliminado)
    {
        // - 1. Se recuperan los parámetros de las plantillas de informes del tipo especificado
        // - 2. Se recorren cada uno de los informes automáticos de plantillas de informes de todos los usuarios
        // - 3. Se recorren cada uno de los parámetros del informe automático (de parámetros tipo):
        //   - Si el valor del parámetro coincide con el valor del parámetro eliminado (el identificador correspondiente), se establece el valor del parámetro a ninguno
        //   - Se actualizan los parámetros de tipo del informe automático (si se ha modificado el valor de algún parámetro)

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_parametros_plantillas_informes = "
            SELECT
                parametros_plantillas_informes.id
            FROM
                parametros_plantillas_informes,
                plantillas_informes
            WHERE
                (parametros_plantillas_informes.plantilla_informe = plantillas_informes.id)
                AND (plantillas_informes.red = '".$_SESSION["id_red"]."')
                AND (parametros_plantillas_informes.tipo = '".$bd_red->_($tipo_parametro)."')";
        $res_parametros_plantillas_informes = $bd_red->ejecuta_consulta($consulta_parametros_plantillas_informes);
        if ($res_parametros_plantillas_informes == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros_plantillas_informes."'");
        }
        $ids_parametros_plantillas_informes_tipo = array();
        while ($fila_parametro_plantilla_informe = $res_parametros_plantillas_informes->dame_siguiente_fila())
        {
            array_push($ids_parametros_plantillas_informes_tipo, $fila_parametro_plantilla_informe["id"]);
        }

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (tipo = '".TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME."')";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }
        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            $id_informe_automatico = $fila_informe_automatico["id"];
            $parametros_tipo_informe_automatico = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_informe_automatico["parametros_tipo"]);

            // Identificadores y valores de parámetros del informe automático de plantilla de informe
            $cadena_ids_parametros = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_PARAMETROS];
            $cadena_valores_parametros = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_VALORES_PARAMETROS];
            if ($cadena_ids_parametros == "")
            {
                $ids_parametros = array();
            }
            else
            {
                $ids_parametros = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros);
            }
            if ($cadena_valores_parametros == "")
            {
                $valores_parametros = array();
            }
            else
            {
                $valores_parametros = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_valores_parametros);
            }

            // Se recorren cada uno de los parámetros del informe automático
            // - Si el valor del parámetro coincide con el valor del parámetro eliminado (el identificador correspondiente), se establece el valor del parámetro a ninguno
            $parametros_tipo_informe_automatico_modificados = false;
            foreach ($ids_parametros_plantillas_informes_tipo as $id_parametro_plantilla_informe_tipo)
            {
                $indice_parametro = array_search($id_parametro_plantilla_informe_tipo, $ids_parametros);
                if (($indice_parametro !== false) && ($indice_parametro !== NULL))
                {
                    $valor_parametro = $valores_parametros[$indice_parametro];
                    if ($valor_parametro == $id_eliminado)
                    {
                        $valores_parametros[$indice_parametro] = ID_NINGUNO;
                        $parametros_tipo_informe_automatico_modificados = true;
                    }
                }
            }

            // Se actualizan los parámetros de tipo del informe automático (si se ha modificado el valor de algún parámetro)
            if ($parametros_tipo_informe_automatico_modificados == true)
            {
                $cadena_valores_parametros_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $valores_parametros);
                $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_VALORES_PARAMETROS] = $cadena_valores_parametros_modificada;
                $cadena_parametros_tipo_modificada = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo_informe_automatico);
                $operacion_modificacion_informes_automaticos = "
                    UPDATE informes_automaticos
                    SET
                        parametros_tipo = '".$bd_red->_($cadena_parametros_tipo_modificada)."'
                    WHERE
                        id = '".$bd_red->_($id_informe_automatico)."'";
                $res_modificacion_informes_automaticos = $bd_red->ejecuta_operacion($operacion_modificacion_informes_automaticos);
                if ($res_modificacion_informes_automaticos == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_modificacion_informes_automaticos."'");
                }
            }
        }
    }


    //
    // Funciones de elementos de plantillas de informes de informes automáticos
    //


    // Añade el texto del elemento de plantilla de informe a los informes automáticos correspondientes
    function anyade_texto_elemento_informes_automaticos_plantilla_informe(
        $id_plantilla_informe,
        $tipo_elemento,
        $id_elemento,
        $texto_elemento)
    {
        // - 1. Se recorren cada uno de los informes automáticos de plantillas de informes de todos los usuarios
        // - 2. Se recupera el identificador de plantilla de informe
        // - 3. Si la plantilla de informe es la misma plantilla de informe, se añade el elemento al informe automático correspondiente

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (tipo = '".TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME."')";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            $id_informe_automatico = $fila_informe_automatico["id"];
            $parametros_tipo_informe_automatico = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_informe_automatico["parametros_tipo"]);
            $cadena_parametros_tipo_json_informe_automatico = $fila_informe_automatico["parametros_tipo_json"];
            $parametros_tipo_json_informe_automatico = json_decode_caracteres_especiales($cadena_parametros_tipo_json_informe_automatico);

            $id_plantilla_informe_informe_automatico = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_ID_PLANTILLA_INFORME];
            if ($id_plantilla_informe == $id_plantilla_informe_informe_automatico)
            {
                // Parámetros de tipo del informe automático
                $cadena_ids_parametros = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_PARAMETROS];
                $cadena_valores_parametros = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_VALORES_PARAMETROS];
                $cadena_ids_elementos_portada = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_PORTADA];
                $cadena_ids_elementos_titulo = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TITULO];
                $cadena_ids_elementos_texto = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TEXTO];
                $cadena_ids_elementos_imagen = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_IMAGEN];
                $cadena_imagenes_personalizadas_elementos_texto = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IMAGENES_PERSONALIZADAS_ELEMENTOS_IMAGEN];
                $cadena_horario_semanal = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_INCLUSION_FECHAS];

                // Se añade el elemento (el identificador y el texto)
                $cadena_ids_elementos_portada_modificada = $cadena_ids_elementos_portada;
                $cadena_ids_elementos_titulo_modificada = $cadena_ids_elementos_titulo;
                $cadena_ids_elementos_texto_modificada = $cadena_ids_elementos_texto;
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
                    {
                        if ($cadena_ids_elementos_portada == "")
                        {
                            $ids_elementos_portada = array();
                        }
                        else
                        {
                            $ids_elementos_portada = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_portada);
                        }
                        array_push($ids_elementos_portada, $id_elemento);
                        $parametros_tipo_json_informe_automatico["subtitulo_elemento_portada_".$id_elemento] = $texto_elemento;
                        $cadena_ids_elementos_portada_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_elementos_portada);
                        break;
                    }
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO:
                    {
                        if ($cadena_ids_elementos_titulo == "")
                        {
                            $ids_elementos_titulo = array();
                        }
                        else
                        {
                            $ids_elementos_titulo = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_titulo);
                        }
                        array_push($ids_elementos_titulo, $id_elemento);
                        $parametros_tipo_json_informe_automatico["titulo_elemento_titulo_".$id_elemento] = $texto_elemento;
                        $cadena_ids_elementos_titulo_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_elementos_titulo);
                        break;
                    }
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO:
                    {
                        if ($cadena_ids_elementos_texto == "")
                        {
                            $ids_elementos_texto = array();
                        }
                        else
                        {
                            $ids_elementos_texto = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_texto);
                        }
                        array_push($ids_elementos_texto, $id_elemento);
                        $parametros_tipo_json_informe_automatico["texto_elemento_texto_".$id_elemento] = $texto_elemento;
                        $cadena_ids_elementos_texto_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_elementos_texto);
                        break;
                    }
                }

                // Se modifican los parámetros de tipo
                $cadena_parametros_tipo_modificada = implode(SEPARADOR_PARAMETROS_COMPUESTOS, array(
                    $id_plantilla_informe,
                    $cadena_ids_parametros,
                    $cadena_valores_parametros,
                    $cadena_ids_elementos_portada_modificada,
                    $cadena_ids_elementos_titulo_modificada,
                    $cadena_ids_elementos_texto_modificada,
                    $cadena_ids_elementos_imagen,
                    $cadena_imagenes_personalizadas_elementos_texto,
                    $cadena_horario_semanal,
                    $cadena_exclusion_fechas,
                    $cadena_inclusion_fechas));
                $cadena_parametros_tipo_json_modificada = json_encode($parametros_tipo_json_informe_automatico);
                $operacion_modificacion_informes_automaticos = "
                    UPDATE informes_automaticos
                    SET
                        parametros_tipo = '".$bd_red->_($cadena_parametros_tipo_modificada)."',
                        parametros_tipo_json = '".$bd_red->_($cadena_parametros_tipo_json_modificada)."'
                    WHERE
                        id = '".$bd_red->_($id_informe_automatico)."'";
                $res_modificacion_informes_automaticos = $bd_red->ejecuta_operacion($operacion_modificacion_informes_automaticos);
                if ($res_modificacion_informes_automaticos == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_modificacion_informes_automaticos."'");
                }
            }
        }
    }


    // Elimina el texto del elemento de plantilla de informe de los informes automáticos correspondientes
    function elimina_texto_elemento_informes_automaticos_plantilla_informe(
        $id_plantilla_informe,
        $tipo_elemento,
        $id_elemento)
    {
        // - 1. Se recorren cada uno de los informes automáticos de plantillas de informes de todos los usuarios
        // - 2. Se recupera el identificador de plantilla de informe
        // - 3. Si la plantilla de informe es la misma plantilla de informe, se elimina el texto del informe automático correspondiente

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (tipo = '".TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME."')";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            $id_informe_automatico = $fila_informe_automatico["id"];
            $parametros_tipo_informe_automatico = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_informe_automatico["parametros_tipo"]);
            $cadena_parametros_tipo_json_informe_automatico = $fila_informe_automatico["parametros_tipo_json"];
            $parametros_tipo_json_informe_automatico = json_decode_caracteres_especiales($cadena_parametros_tipo_json_informe_automatico);

            $id_plantilla_informe_informe_automatico = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_ID_PLANTILLA_INFORME];
            if ($id_plantilla_informe == $id_plantilla_informe_informe_automatico)
            {
                // Parámetros de tipo del informe automático
                $cadena_ids_parametros = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_PARAMETROS];
                $cadena_valores_parametros = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_VALORES_PARAMETROS];
                $cadena_ids_elementos_portada = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_PORTADA];
                $cadena_ids_elementos_titulo = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TITULO];
                $cadena_ids_elementos_texto = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TEXTO];
                $cadena_ids_elementos_imagen = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_IMAGEN];
                $cadena_imagenes_personalizadas_elementos_texto = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IMAGENES_PERSONALIZADAS_ELEMENTOS_IMAGEN];
                $cadena_horario_semanal = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_INCLUSION_FECHAS];

                // Se elimina el elemento (el identificador y el texto)
                $existe_elemento = false;
                $cadena_ids_elementos_portada_modificada = $cadena_ids_elementos_portada;
                $cadena_ids_elementos_titulo_modificada = $cadena_ids_elementos_titulo;
                $cadena_ids_elementos_texto_modificada = $cadena_ids_elementos_texto;
                switch ($tipo_elemento)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
                    {
                        if ($cadena_ids_elementos_portada == "")
                        {
                            $ids_elementos_portada = array();
                        }
                        else
                        {
                            $ids_elementos_portada = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_portada);
                        }
                        $indice_elemento_portada = array_search($id_elemento, $ids_elementos_portada);
                        if (($indice_elemento_portada !== false) && ($indice_elemento_portada !== NULL))
                        {
                            unset($ids_elementos_portada[$indice_elemento_portada]);
                            unset($parametros_tipo_json_informe_automatico["subtitulo_elemento_portada_".$id_elemento]);
                            $cadena_ids_elementos_portada_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_elementos_portada);
                            $existe_elemento = true;
                        }
                        break;
                    }
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO:
                    {
                        if ($cadena_ids_elementos_titulo == "")
                        {
                            $ids_elementos_titulo = array();
                        }
                        else
                        {
                            $ids_elementos_titulo = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_titulo);
                        }
                        $indice_elemento_titulo = array_search($id_elemento, $ids_elementos_titulo);
                        if (($indice_elemento_titulo !== false) && ($indice_elemento_titulo !== NULL))
                        {
                            unset($ids_elementos_titulo[$indice_elemento_titulo]);
                            unset($parametros_tipo_json_informe_automatico["titulo_elemento_titulo_".$id_elemento]);
                            $cadena_ids_elementos_titulo_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_elementos_titulo);
                            $existe_elemento = true;
                        }
                        break;
                    }
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO:
                    {
                        if ($cadena_ids_elementos_texto == "")
                        {
                            $ids_elementos_texto = array();
                        }
                        else
                        {
                            $ids_elementos_texto = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_texto);
                        }
                        $indice_elemento_texto = array_search($id_elemento, $ids_elementos_texto);
                        if (($indice_elemento_texto !== false) && ($indice_elemento_texto !== NULL))
                        {
                            unset($ids_elementos_texto[$indice_elemento_texto]);
                            unset($parametros_tipo_json_informe_automatico["texto_elemento_texto_".$id_elemento]);
                            $cadena_ids_elementos_texto_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_elementos_texto);
                            $existe_elemento = true;
                        }
                        break;
                    }
                }

                // Si existe el elemento, se elimina de los parámetros de tipo
                if ($existe_elemento == true)
                {
                    // Se modifican los parámetros de tipo
                    $cadena_parametros_tipo_modificada = implode(SEPARADOR_PARAMETROS_COMPUESTOS, array(
                        $id_plantilla_informe,
                        $cadena_ids_parametros,
                        $cadena_valores_parametros,
                        $cadena_ids_elementos_portada_modificada,
                        $cadena_ids_elementos_titulo_modificada,
                        $cadena_ids_elementos_texto_modificada,
                        $cadena_ids_elementos_imagen,
                        $cadena_imagenes_personalizadas_elementos_texto,
                        $cadena_horario_semanal,
                        $cadena_exclusion_fechas,
                        $cadena_inclusion_fechas));
                    $cadena_parametros_tipo_json_modificada = json_encode($parametros_tipo_json_informe_automatico);
                    $operacion_modificacion_informes_automaticos = "
                        UPDATE informes_automaticos
                        SET
                            parametros_tipo = '".$bd_red->_($cadena_parametros_tipo_modificada)."',
                            parametros_tipo_json = '".$bd_red->_($cadena_parametros_tipo_json_modificada)."'
                        WHERE
                            id = '".$bd_red->_($id_informe_automatico)."'";
                    $res_modificacion_informes_automaticos = $bd_red->ejecuta_operacion($operacion_modificacion_informes_automaticos);
                    if ($res_modificacion_informes_automaticos == false)
                    {
                        throw new Exception("Error en la operación: '".$operacion_modificacion_informes_automaticos."'");
                    }
                }
            }
        }
    }


    //
    // Funciones de imágenes de plantillas de informes de informes automáticos
    //


    // Añade la imagen a los informes automáticos correspondientes
    function anyade_imagen_informes_automaticos_plantilla_informe($id_plantilla_informe, $id_elemento_imagen)
    {
        // - 1. Se recorren cada uno de los informes automáticos de plantillas de informes de todos los usuarios
        // - 2. Se recupera el identificador de plantilla de informe
        // - 3. Si la plantilla de informe es la misma plantilla de informe, se añade la imagen al informe automático correspondiente

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (tipo = '".TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME."')";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            $id_informe_automatico = $fila_informe_automatico["id"];
            $parametros_tipo_informe_automatico = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_informe_automatico["parametros_tipo"]);
            $cadena_parametros_tipo_json_informe_automatico = $fila_informe_automatico["parametros_tipo_json"];
            $parametros_tipo_json_informe_automatico = json_decode_caracteres_especiales($cadena_parametros_tipo_json_informe_automatico);

            $id_plantilla_informe_informe_automatico = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_ID_PLANTILLA_INFORME];
            if ($id_plantilla_informe == $id_plantilla_informe_informe_automatico)
            {
                // Parámetros de tipo del informe automático
                $cadena_ids_parametros = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_PARAMETROS];
                $cadena_valores_parametros = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_VALORES_PARAMETROS];
                $cadena_ids_elementos_texto = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TEXTO];
                $cadena_ids_elementos_imagen = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_IMAGEN];
                $cadena_imagenes_personalizadas_elementos_imagen = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IMAGENES_PERSONALIZADAS_ELEMENTOS_IMAGEN];
                $cadena_horario_semanal = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_INCLUSION_FECHAS];

                // Conversiones
                if ($cadena_ids_elementos_imagen == "")
                {
                    $ids_elementos_imagen = array();
                }
                else
                {
                    $ids_elementos_imagen = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_imagen);
                }
                if ($cadena_imagenes_personalizadas_elementos_imagen == "")
                {
                    $imagenes_personalizadas_elementos_imagen = array();
                }
                else
                {
                    $imagenes_personalizadas_elementos_imagen = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_imagenes_personalizadas_elementos_imagen);
                }

                // Se añade la imagen a los parámetros de tipo
                array_push($ids_elementos_imagen, $id_elemento_imagen);
                array_push($imagenes_personalizadas_elementos_imagen, VALOR_NO);
                $cadena_ids_elementos_imagen_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_elementos_imagen);
                $cadena_imagenes_personalizadas_elementos_imagen_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $imagenes_personalizadas_elementos_imagen);

                // Se modifican los parámetros de tipo
                $cadena_parametros_tipo_modificada = implode(SEPARADOR_PARAMETROS_COMPUESTOS, array(
                    $id_plantilla_informe,
                    $cadena_ids_parametros,
                    $cadena_valores_parametros,
                    $cadena_ids_elementos_texto,
                    $cadena_ids_elementos_imagen_modificada,
                    $cadena_imagenes_personalizadas_elementos_imagen_modificada,
                    $cadena_horario_semanal,
                    $cadena_exclusion_fechas,
                    $cadena_inclusion_fechas));
                $cadena_parametros_tipo_json_modificada = json_encode($parametros_tipo_json_informe_automatico);
                $operacion_modificacion_informes_automaticos = "
                    UPDATE informes_automaticos
                    SET
                        parametros_tipo = '".$bd_red->_($cadena_parametros_tipo_modificada)."',
                        parametros_tipo_json = '".$bd_red->_($cadena_parametros_tipo_json_modificada)."'
                    WHERE
                        id = '".$bd_red->_($id_informe_automatico)."'";
                $res_modificacion_informes_automaticos = $bd_red->ejecuta_operacion($operacion_modificacion_informes_automaticos);
                if ($res_modificacion_informes_automaticos == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_modificacion_informes_automaticos."'");
                }
            }
        }
    }


    // Elimina la imagen de plantilla de informe de los informes automáticos correspondientes
    function elimina_imagen_informes_automaticos_plantilla_informe($id_plantilla_informe, $id_elemento_imagen)
    {
        // - 1. Se recorren cada uno de los informes automáticos de plantillas de informes de todos los usuarios
        // - 2. Se recupera el identificador de plantilla de informe
        // - 3. Si la plantilla de informe es la misma plantilla de informe, se elimina el texto del informe automático correspondiente

        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (tipo = '".TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME."')";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            $id_informe_automatico = $fila_informe_automatico["id"];
            $parametros_tipo_informe_automatico = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_informe_automatico["parametros_tipo"]);
            $cadena_parametros_tipo_json_informe_automatico = $fila_informe_automatico["parametros_tipo_json"];
            $parametros_tipo_json_informe_automatico = json_decode_caracteres_especiales($cadena_parametros_tipo_json_informe_automatico);

            $id_plantilla_informe_informe_automatico = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_ID_PLANTILLA_INFORME];
            if ($id_plantilla_informe == $id_plantilla_informe_informe_automatico)
            {
                // Parámetros de tipo del informe automático
                $cadena_ids_parametros = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_PARAMETROS];
                $cadena_valores_parametros = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_VALORES_PARAMETROS];
                $cadena_ids_elementos_texto = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TEXTO];
                $cadena_ids_elementos_imagen = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_IMAGEN];
                $cadena_imagenes_personalizadas_elementos_imagen = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IMAGENES_PERSONALIZADAS_ELEMENTOS_IMAGEN];
                $cadena_horario_semanal = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_HORARIO_SEMANAL];
                $cadena_exclusion_fechas = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_EXCLUSION_FECHAS];
                $cadena_inclusion_fechas = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_INCLUSION_FECHAS];

                // Conversiones
                if ($cadena_ids_elementos_imagen == "")
                {
                    $ids_elementos_imagen = array();
                }
                else
                {
                    $ids_elementos_imagen = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_imagen);
                }
                if ($cadena_imagenes_personalizadas_elementos_imagen == "")
                {
                    $imagenes_personalizadas_elementos_imagen = array();
                }
                else
                {
                    $imagenes_personalizadas_elementos_imagen = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_imagenes_personalizadas_elementos_imagen);
                }

                // Si existe la imagen, se elimina de los parámetros de tipo
                $indice_elemento_imagen = array_search($id_elemento_imagen, $ids_elementos_imagen);
                if (($indice_elemento_imagen !== false) && ($indice_elemento_imagen !== NULL))
                {
                    $imagen_personalizada_elemento_imagen = $imagenes_personalizadas_elementos_imagen[$indice_elemento_imagen];

                    unset($ids_elementos_imagen[$indice_elemento_imagen]);
                    unset($imagenes_personalizadas_elementos_imagen[$indice_elemento_imagen]);
                    $cadena_ids_elementos_imagen_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_elementos_imagen);
                    $cadena_imagenes_personalizadas_elementos_imagen_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $imagenes_personalizadas_elementos_imagen);

                    // Se modifican los parámetros de tipo
                    $cadena_parametros_tipo_modificada = implode(SEPARADOR_PARAMETROS_COMPUESTOS, array(
                        $id_plantilla_informe,
                        $cadena_ids_parametros,
                        $cadena_valores_parametros,
                        $cadena_ids_elementos_texto,
                        $cadena_ids_elementos_imagen_modificada,
                        $cadena_imagenes_personalizadas_elementos_imagen_modificada,
                        $cadena_horario_semanal,
                        $cadena_exclusion_fechas,
                        $cadena_inclusion_fechas));
                    $cadena_parametros_tipo_json_modificada = json_encode($parametros_tipo_json_informe_automatico);
                    $operacion_modificacion_informes_automaticos = "
                        UPDATE informes_automaticos
                        SET
                            parametros_tipo = '".$bd_red->_($cadena_parametros_tipo_modificada)."',
                            parametros_tipo_json = '".$bd_red->_($cadena_parametros_tipo_json_modificada)."'
                        WHERE
                            id = '".$bd_red->_($id_informe_automatico)."'";
                    $res_modificacion_informes_automaticos = $bd_red->ejecuta_operacion($operacion_modificacion_informes_automaticos);
                    if ($res_modificacion_informes_automaticos == false)
                    {
                        throw new Exception("Error en la operación: '".$operacion_modificacion_informes_automaticos."'");
                    }

                    // Se elimina la imagen si era personalizada
                    if ($imagen_personalizada_elemento_imagen == VALOR_SI)
                    {
                        $id_origen = implode(SEPARADOR_PARAMETROS_SIMPLES, array($id_informe_automatico, $id_elemento_imagen));
                        elimina_imagen_base_datos(ORIGEN_IMAGEN_PLANTILLA_INFORME_INFORME_AUTOMATICO_IMAGEN, $id_origen);
                    }
                }
            }
        }
    }


    //
    // Funciones de parámetros de los informes
    //


    // Devuelve el HTML del horario semanal para los detalles de un informe automático
    function dame_html_parametro_horario_semanal_informe_automatico($titulo, $cadena_horario_semanal)
    {
        $codigo_html = "";
        $html_horario_semanal = dame_descripcion_horario_semanal($cadena_horario_semanal, true, TIPO_DESCRIPCION_HTML);
        if ($html_horario_semanal <> "")
        {
            $codigo_html .= "<li>".$titulo.": ";
            $codigo_html .= $html_horario_semanal;
            $codigo_html .= "</li>";
        }
        return ($codigo_html);
    }


    // Devuelve el HTML de fechas para los detalles de un informe automático
    function dame_html_parametro_fechas_informe_automatico($titulo, $cadena_fechas)
    {
        $codigo_html = "";
        $html_fechas = dame_descripcion_fechas($cadena_fechas, TIPO_DESCRIPCION_HTML);
        if ($html_fechas <> "")
        {
            $codigo_html .= "<li>".$titulo.": ";
            $codigo_html .= $html_fechas;
            $codigo_html .= "</li>";
        }
        return ($codigo_html);
    }


    // Devuelve el HTML de agrpaciones de días de la semana para los detalles de un informe automático
    function dame_html_parametro_agrupaciones_dias_semana_informe_automatico($titulo, $cadena_agrupaciones_dias_semana)
    {
        $codigo_html = "";
        if ($cadena_agrupaciones_dias_semana != "")
        {
            $cadena_agrupaciones_dias_semana = str_replace(" ", "", $cadena_agrupaciones_dias_semana);
            $cadena_agrupaciones_dias_semana = str_replace(SEPARADOR_PARAMETROS_SIMPLES, SEPARADOR_PARAMETROS_SIMPLES." ", $cadena_agrupaciones_dias_semana);
            $codigo_html .= "<li>".$titulo.": ";
            $codigo_html .= $cadena_agrupaciones_dias_semana;
            $codigo_html .= "</li>";
        }
        return ($codigo_html);
    }


    //
    // Funciones auxiliares
    //


    // Devuelve el módulo del tipo del informe automático
    function dame_modulo_tipo_informe_automatico($tipo_informe_automatico)
    {
        switch ($tipo_informe_automatico)
        {
            // Informes automáticos del módulo Personal
            case TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME:
            {
                $modulo = MODULO_PERSONAL;
                break;
            }
            // Informes automáticos del módulo Sensores
            case TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
            case TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA:
            case TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD:
            case TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR:
            case TIPO_INFORME_SENSORES_INFORMACION_VIENTO:
            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA:
            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA:
            case TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION:
            case TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA:
            case TIPO_INFORME_SENSORES_INFORMACION_GAS:
            case TIPO_INFORME_SENSORES_INFORMACION_AGUA:
            case TIPO_INFORME_SENSORES_INFORMACION_GENERICA:
            case TIPO_INFORME_SENSORES_ANALISIS_HORARIO:
            case TIPO_INFORME_SENSORES_ANALISIS_DIARIO:
            case TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
            case TIPO_INFORME_SENSORES_COMPARACION_PERIODOS:
            case TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
            case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
            case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
            case TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO:
            case TIPO_INFORME_SENSORES_VALORES_GENERALES:
            case TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES:
            case TIPO_INFORME_SENSORES_HISTOGRAMA:
            case TIPO_INFORME_SENSORES_CORRELACION:
            {
                $modulo = MODULO_SENSORES;
                break;
            }
            // Informes automáticos del módulo Actuadores
            case TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
            {
                $modulo = MODULO_ACTUADORES;
                break;
            }
            // Informes automáticos del módulo Smartmeter
            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS:
            case TIPO_INFORME_SMARTMETER_EXCESOS_POTENCIA:
            case TIPO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA:
            case TIPO_INFORME_SMARTMETER_CORTES_TENSION:
            case TIPO_INFORME_SMARTMETER_EXCESOS_CAUDAL:
            case TIPO_INFORME_SMARTMETER_COMPARACION_PERIODOS:
            case TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA:
            case TIPO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA:
            case TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA:
            case TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL:
            {
                $modulo = MODULO_SMARTMETER;
                break;
            }
            // Informes automáticos del módulo Proyectos
            case TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
            case TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO:
            {
                $modulo = MODULO_PROYECTOS;
                break;
            }
            default:
            {
                throw new Exception("Tipo de informe automático desconocido: '".$tipo_informe_automatico."'");
            }
        }
        return ($modulo);
    }


    //
    // Funciones de permisos de usuario
    //


    // Devuelve los identificadores de los informes automáticos visibles para el usuario actual
    function dame_ids_informes_automaticos_usuario_actual()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Identificadores de informes automáticos
        $ids_informes_automaticos = array();
        $consulta = InformeAutomatico::dame_consulta_informes_automaticos("");
        $res = $bd_red->ejecuta_consulta($consulta);
        if ($res == false)
        {
            throw new Exception("Error en la consulta: '".$consulta."'");
        }
        while ($fila = $res->dame_siguiente_fila())
        {
            $id_informe_automatico = $fila["id"];
            array_push($ids_informes_automaticos, $id_informe_automatico);
        }
        return ($ids_informes_automaticos);
    }


    //
    // Funciones para obtener el contenido de las secciones
    //


    // Devuelve la tabla que contiene el filtro para la tabla de informes automáticos
    function dame_tabla_filtro_informes_automaticos_tabla()
    {
        $idiomas = new Idiomas();

        // Se recuperan los controles a mostrar
        $id_controles = "filtro_informes_automaticos_tabla";
        $filtro_informes_automaticos = dame_filtro_texto_controles_extra($id_controles, $idiomas->_("Nombre"), array());

        // Se crea la tabla contenedora
        $tabla = new TablaDatos(
            "tabla-filtro-informes-automaticos-tabla",
            $idiomas->_("Filtro de informes automáticos"),
            TIPO_TABLA_DATOS_CONTENEDOR
        );

        $params_fila = array(
            "clase_dato" => "filtro-informes",
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_INFORMES_AUTOMATICOS),
        );
        $tabla->anyade_fila("filtro-informes-automaticos-tabla", $filtro_informes_automaticos, $params_fila);

        return ($tabla->dame_tabla());
    }
?>