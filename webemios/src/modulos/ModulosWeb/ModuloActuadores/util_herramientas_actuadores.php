<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/ClienteMqtt.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_ficheros.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/util_acciones.php');


    //
    // Funciones de herramientas de actuadores
    //


    // Envía una acción a un actuador
    function envia_accion_actuador($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_actuador = $_POST['id_actuador'];
        $id_accion_predefinida = $_POST['id_accion_predefinida'];
        $contenido_accion = $_POST['contenido_accion'];
        $valor_accion = $_POST['valor_accion'];
        $cadena_fecha_hora_accion_base_datos_utc = $_POST['fecha_hora_accion'];
        $origen_accion = $_POST['origen_accion'];

        // Se recupera la información del actuador
        $fila_actuador = dame_fila_actuador($id_actuador);
        $nombre_actuador = $fila_actuador['nombre'];
        $clase_actuador = $fila_actuador['clase'];
        $tipo_actuador = $fila_actuador['tipo'];

        // Si hay acción predefinida se recuperan el contenido y el valor de la acción
        if ($id_accion_predefinida != ID_NINGUNO)
        {
            $fila_accion_predefinida = dame_fila_accion_predefinida($id_accion_predefinida);
            $nombre_accion = $fila_accion_predefinida["nombre"];
            $contenido_accion = $fila_accion_predefinida["contenido"];
            $valor_accion = $fila_accion_predefinida["valor"];
        }
        else
        {
            $nombre_accion = NULL;
        }

        // Se modifica la acción si es necesario
        if ($cadena_fecha_hora_accion_base_datos_utc !== NULL)
        {
            // Características de clase de actuador
            $caracteristicas_clase_actuador = NodoActuador::dame_caracteristicas_clase_actuador($clase_actuador);
            $tipo_acciones = $caracteristicas_clase_actuador["tipo_acciones"];

            // Se actualiza el contenido de la acción dependiendo de la fecha (si es necesario)
            actualiza_contenido_accion_fecha($contenido_accion, $tipo_acciones, $cadena_fecha_hora_accion_base_datos_utc);
        }

        // Asunto de la acción (dependiendo del tipo de actuador)
        switch ($tipo_actuador)
        {
            case TIPO_ACTUADOR_HARDWARE:
            {
                // Se recupera el identificador del axón
                $parametros_tipo_actuador = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_actuador['parametros_tipo']);
                $id_axon = $parametros_tipo_actuador[INDICE_PARAMETRO_TIPO_ACTUADOR_HARDWARE_ID_AXON];

                $asunto = "AX/".$id_axon."/ACT/".$id_actuador."/UPDATE";
                break;
            }
            case TIPO_ACTUADOR_SOFTWARE:
            {
                $asunto = "SOFTWARE_ACT/ACT/".$id_actuador."/UPDATE";
                break;
            }
        }

        // Identificador de acción
        $timestamp_utc = dame_timestamp_ahora_milisegundos_utc();
        $id_accion = $origen_accion."-".$timestamp_utc;

        // Se envia la acción al servidor MQTT del servidor EMIOS
        $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
        $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
        if ($mqtt->conecta() == true)
        {
            // Se envía la acción por MQTT
            $contenido_accion = str_replace(SUSTITUTO_SEPARADOR, SUSTITUTO_SEPARADOR_EXTRA, $contenido_accion);
            $contenido_accion = str_replace(SEPARADOR_PARAMETROS_VALORES, SUSTITUTO_SEPARADOR, $contenido_accion);
            $datos = implode("#", array(
                $id_accion,
                $contenido_accion,
                $valor_accion,
                $origen_accion,
                $_SESSION["id_usuario"]));

            $mqtt->publica($asunto, $datos, 0);
            $mqtt->desconecta();

            // Se añade la acción de usuario
            anyade_accion_usuario_enviar_accion_actuador_grupo_actuadores(
                $clase_actuador,
                DESTINO_ACCION_ACTUADOR,
                $nombre_actuador,
                $nombre_accion,
                $contenido_accion);

            $res = "OK";
            $msg = $idiomas->_("Acción enviada correctamente");
        }
        else
        {
            $res = "ERROR";
            $msg = $idiomas->_("No se ha podido enviar la acción");
        }

        // Se devuelve el resultado
        return(array(
            "res" => $res,
            "msg" => $msg)
        );
    }


    // Envía una acción a un grupo de actuadores
    function envia_accion_grupo_actuadores($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_grupo_actuadores = $parametros['id_grupo_actuadores'];
        $id_accion_predefinida = $parametros['id_accion_predefinida'];
        $contenido_accion = $parametros['contenido_accion'];
        $valor_accion = $parametros['valor_accion'];
        $cadena_fecha_hora_accion_base_datos_utc = $parametros['fecha_hora_accion'];
        $origen_accion = $parametros['origen_accion'];

        // Se recuperala información del grupo de actuadores
        $fila_grupo_actuadores = dame_fila_grupo_actuadores($id_grupo_actuadores);
        $nombre_grupo_actuadores = $fila_grupo_actuadores['nombre'];
        $clase_grupo_actuadores = $fila_grupo_actuadores['clase'];

        // Si hay acción predefinida se recuperan el contenido y el valor de la acción
        if ($id_accion_predefinida != ID_NINGUNO)
        {
            $fila_accion_predefinida = dame_fila_accion_predefinida($id_accion_predefinida);
            $nombre_accion = $fila_accion_predefinida["nombre"];
            $contenido_accion = $fila_accion_predefinida["contenido"];
            $valor_accion = $fila_accion_predefinida["valor"];
        }
        else
        {
            $nombre_accion = NULL;
        }

        // Se modifica la acción si es necesario
        if ($cadena_fecha_hora_accion_base_datos_utc !== NULL)
        {
            // Características de clase de actuador
            $caracteristicas_clase_actuador = NodoActuador::dame_caracteristicas_clase_actuador($clase_grupo_actuadores);
            $tipo_acciones = $caracteristicas_clase_actuador["tipo_acciones"];

            // Se actualiza el contenido de la acción dependiendo de la fecha (si es necesario)
            actualiza_contenido_accion_fecha($contenido_accion, $tipo_acciones, $cadena_fecha_hora_accion_base_datos_utc);
        }

        // Identificador de acción
        $timestamp_utc = dame_timestamp_ahora_milisegundos_utc();
        $id_accion = $origen_accion."-".$timestamp_utc;

        // Se envia la acción al servidor MQTT del servidor EMIOS
        $ip_mqtt = dame_valor_entrada_ini("ip_servidor_emios");
        $mqtt = new ClienteMqtt($ip_mqtt, PUERTO_SERVIDOR_MQTT, "PHP client-".$_SESSION["id_usuario"]);
        if ($mqtt->conecta() == true)
        {
            // Se envía la acción por MQTT
            $asunto = "NET/".$_SESSION["id_red"]."/GRP_ACT/".$id_grupo_actuadores."/UPDATE";
            $contenido_accion = str_replace(SUSTITUTO_SEPARADOR, SUSTITUTO_SEPARADOR_EXTRA, $contenido_accion);
            $contenido_accion = str_replace(SEPARADOR_PARAMETROS_VALORES, SUSTITUTO_SEPARADOR, $contenido_accion);
            $datos = implode("#", array(
                $id_accion,
                $contenido_accion,
                $valor_accion,
                $origen_accion,
                $_SESSION["id_usuario"]));

            $mqtt->publica($asunto, $datos, 0);
            $mqtt->desconecta();

            // Se añade la acción de usuario
            anyade_accion_usuario_enviar_accion_actuador_grupo_actuadores(
                $clase_grupo_actuadores,
                DESTINO_ACCION_GRUPO_ACTUADORES,
                $nombre_grupo_actuadores,
                $nombre_accion,
                $contenido_accion);

            $res = "OK";
            $msg = $idiomas->_("Acción enviada correctamente");
        }
        else
        {
            $res = "ERROR";
            $msg = $idiomas->_("No se ha podido enviar la acción");
        }

        // Se devuelve el resultado
        return(array(
            "res" => $res,
            "msg" => $msg)
        );
    }


    // Borra las acciones enviadas
    function borra_acciones_enviadas($parametros)
    {
        $idiomas = new Idiomas();
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Parámetros
        $clase_actuador = $parametros["clase_actuador"];
        $destino_accion = $parametros["destino_accion"];
        $id_destino_accion = $parametros["id_destino_accion"];
        $nombre_destino_accion = $parametros["nombre_destino_accion"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];

        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_base_datos_local = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_local = convierte_formato_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_inicio_base_datos_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_base_datos_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_base_datos_local, FORMATO_FECHA_HORA_BASE_DATOS, $zona_horaria, ZONA_HORARIA_UTC);

        // Se recuperan las fechas de inicio y fin de borrado de las acciones enviadas
        $cadena_fecha_hora_inicio_borrado_acciones_enviadas_base_datos_utc = NULL;
        $cadena_fecha_hora_fin_borrado_acciones_enviadas_base_datos_utc = NULL;
        dame_fechas_inicio_fin_acciones_enviadas(
            $destino_accion,
            $nombre_destino_accion,
            $cadena_fecha_hora_inicio_base_datos_utc,
            $cadena_fecha_hora_fin_base_datos_utc,
            $cadena_fecha_hora_inicio_borrado_acciones_enviadas_base_datos_utc,
            $cadena_fecha_hora_fin_borrado_acciones_enviadas_base_datos_utc);

        // Borrado de acciones
        switch ($destino_accion)
        {
            case DESTINO_ACCION_ACTUADOR:
            {
                $operacion_borrado_acciones_enviadas = "
                    DELETE
                    FROM acciones_actuadores
                    WHERE
                        (actuador = '".$bd_datos->_($nombre_destino_accion)."')";
                break;
            }
            case DESTINO_ACCION_GRUPO_ACTUADORES:
            {
                $operacion_borrado_acciones_enviadas = "
                    DELETE
                    FROM acciones_grupos_actuadores
                    WHERE
                        (grupo_actuadores = '".$bd_datos->_($nombre_destino_accion)."')";
                break;
            }
        }
        $operacion_borrado_acciones_enviadas .= "
            AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
            AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";
        $res_borrado_acciones_enviadas = $bd_datos->ejecuta_operacion($operacion_borrado_acciones_enviadas);
        if ($res_borrado_acciones_enviadas == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_acciones_enviadas."'");
        }
        $numero_acciones_enviadas_borradas = $bd_datos->dame_numero_filas_afectadas_ultima_operacion();

        // Mensaje de resultado de borrado de acciones enviadas
        if ($numero_acciones_enviadas_borradas == 0)
        {
            $msg = $idiomas->_("No se han borrado acciones enviadas");
        }
        else
        {
            $cadena_fecha_hora_inicio_borrado_acciones_enviadas_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_borrado_acciones_enviadas_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_fin_borrado_acciones_enviadas_base_datos_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_borrado_acciones_enviadas_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_fecha_hora_inicio_borrado_acciones_enviadas_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_borrado_acciones_enviadas_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
            $cadena_fecha_hora_fin_borrado_acciones_enviadas_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_borrado_acciones_enviadas_base_datos_utc, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);

            $msg .= "- ".$idiomas->_("Número de acciones enviadas borradas").": ".$numero_acciones_enviadas_borradas."\n";
            $msg .= "(".$idiomas->_("hora de inicio").": ".$cadena_fecha_hora_inicio_borrado_acciones_enviadas_local_local.", ";
            $msg .= $idiomas->_("hora de fin").": ".$cadena_fecha_hora_fin_borrado_acciones_enviadas_local_local.")\n";

            // Se añade la acción de usuario
            anyade_accion_usuario_borrar_acciones_enviadas(
                $clase_actuador,
                $destino_accion,
                $nombre_destino_accion,
                $cadena_fecha_hora_inicio_base_datos_local,
                $cadena_fecha_hora_fin_base_datos_local,
                $numero_acciones_enviadas_borradas,
                $cadena_fecha_hora_inicio_borrado_acciones_enviadas_base_datos_local,
                $cadena_fecha_hora_fin_borrado_acciones_enviadas_base_datos_local);
        }

        // Se devuelve el resultado
        return(array(
            "res" => "OK",
            "msg" => $msg)
        );
    }


    //
    // Funciones de acciones de usuario
    //


    // Añade la acción de usuario de envío de acción al actuador o grupo de actuadores
    function anyade_accion_usuario_enviar_accion_actuador_grupo_actuadores(
        $clase_actuador,
        $destino_accion,
        $nombre_destino_accion,
        $nombre_accion,
        $contenido_accion)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ENVIAR_ACCION_ACTUADOR_GRUPO_ACTUADORES;
        $objeto_accion_usuario = $nombre_destino_accion;

        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CLASE_ACTUADOR] = $clase_actuador;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_DESTINO_ACCION] = $destino_accion;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_DESTINO] = $nombre_destino_accion;
        if ($nombre_accion !== NULL)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_ACCION] = $nombre_accion;
        }
        else
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_CONTENIDO_ACCION] = $contenido_accion;
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }


    // Añade la acción de usuario de borrado de acciones enviadas
    function anyade_accion_usuario_borrar_acciones_enviadas(
        $clase_actuador,
        $destino_accion,
        $nombre_destino_accion,
        $cadena_fecha_hora_inicio_base_datos_local,
        $cadena_fecha_hora_fin_base_datos_local,
        $numero_acciones_enviadas_borradas,
        $cadena_fecha_hora_inicio_borrado_acciones_enviadas_base_datos_local,
        $cadena_fecha_hora_fin_borrado_acciones_enviadas_base_datos_local)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_BORRAR_ACCIONES_ENVIADAS_ACTUADOR_GRUPO_ACTUADORES;
        $objeto_accion_usuario = $nombre_destino_accion;

        // Parámetros de la acción
        $parametros_accion_usuario = array(
            PARAMETRO_ACCION_USUARIO_CLASE_ACTUADOR => $clase_actuador,
            PARAMETRO_ACCION_USUARIO_TIPO_DESTINO_ACCION => $destino_accion,
            PARAMETRO_ACCION_USUARIO_NOMBRE_DESTINO => $nombre_destino_accion,
            PARAMETRO_ACCION_USUARIO_FECHA_HORA_INICIO => $cadena_fecha_hora_inicio_base_datos_local,
            PARAMETRO_ACCION_USUARIO_FECHA_HORA_FIN => $cadena_fecha_hora_fin_base_datos_local);

        // Resultado de la acción
        $resultado_accion_usuario[PARAMETRO_ACCION_USUARIO_NUMERO_ACCIONES_ENVIADAS_BORRADAS_FECHAS_HORAS] = array(
            "numero" => $numero_acciones_enviadas_borradas,
            "fecha_hora_inicio" => $cadena_fecha_hora_inicio_borrado_acciones_enviadas_base_datos_local,
            "fecha_hora_fin" => $cadena_fecha_hora_fin_borrado_acciones_enviadas_base_datos_local);

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            $resultado_accion_usuario);
    }


    //
    // Funciones auxiliares
    //


    // Recuperación de fechas mínimas y máximas de las acciones enviadas
    function dame_fechas_inicio_fin_acciones_enviadas(
        $destino_accion,
        $nombre_destino_accion,
        $cadena_fecha_hora_inicio_base_datos_utc,
        $cadena_fecha_hora_fin_base_datos_utc,
        &$cadena_fecha_hora_inicio_acciones_enviadas_base_datos_utc,
        &$cadena_fecha_hora_fin_acciones_enviadas_base_datos_utc)
    {
        $bd_datos = BaseDatosDatos::dame_base_datos();

        // Consulta de acciones enviadas
        $consulta_acciones_enviadas = "
            SELECT
                MIN(hora) AS min_fecha,
                MAX(hora) AS max_fecha";
        switch ($destino_accion)
        {
            case DESTINO_ACCION_ACTUADOR:
            {
                $consulta_acciones_enviadas .= "
                    FROM acciones_actuadores
                    WHERE
                        (actuador = '".$bd_datos->_($nombre_destino_accion)."')";
                break;
            }
            case DESTINO_ACCION_GRUPO_ACTUADORES:
            {
                $consulta_acciones_enviadas .= "
                    FROM acciones_grupos_actuadores
                    WHERE
                        (grupo_actuadores = '".$bd_datos->_($nombre_destino_accion)."')";
                break;
            }
        }
        $consulta_acciones_enviadas .= "
            AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
            AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')";
        $res_acciones_enviadas = $bd_datos->ejecuta_consulta($consulta_acciones_enviadas);
        if ($res_acciones_enviadas == false)
		{
		    throw new Exception("Error en la consulta: '".$consulta_acciones_enviadas."'");
		}
        $fila_acciones_enviadas = $res_acciones_enviadas->dame_siguiente_fila();
        $cadena_fecha_hora_inicio_acciones_enviadas_base_datos_utc = $fila_acciones_enviadas['min_fecha'];
        $cadena_fecha_hora_fin_acciones_enviadas_base_datos_utc = $fila_acciones_enviadas['max_fecha'];

        // Si no hay acciones que borrar se devuelve false
        if ($cadena_fecha_hora_inicio_acciones_enviadas_base_datos_utc === NULL)
        {
            return (false);
        }
        else
        {
            return (true);
        }
	}
?>
