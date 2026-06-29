<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/EquipoInstalacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/util_equipos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_EQUIPO_INSTALACION, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_equipo = $_POST['id_equipo'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_instalacion = $_POST['id_instalacion'];
    $id_equipo_padre = $_POST['id_equipo_padre'];
    $ids_sensores = $_POST['ids_sensores'];
    $ids_actuadores = $_POST['ids_actuadores'];
    $estado = $_POST['estado'];
    $observaciones = $_POST['observaciones'];
    $icono_imagen = $_POST['icono_imagen'];
    $mostrar_en_imagen = $_POST['mostrar_en_imagen'];
    $latitud_imagen = $_POST['latitud_imagen'];
    $longitud_imagen = $_POST['longitud_imagen'];
    $zoom_imagen = $_POST['zoom_imagen'];
    $id_equipo_padre_anterior = $_POST['id_equipo_padre_anterior'];

    // Se comprueba si existe otro equipo con el mismo nombre en la misma instalación
    $consulta_existe = "
        SELECT *
        FROM equipos_instalaciones
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (instalacion = '".$bd_red->_($id_instalacion)."')
            AND (id <> '".$bd_red->_($id_equipo)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un equipo con el mismo nombre");
    }
    else
    {
        // Comprobaciones antes de modificar el equipo de la instalación:
        // - Comprobación de bucle en los equipos padres (después de modificar el equipo padre)
        // - Comprobación de que los sensores y actuadores no están ya en otro equipo
        $modificar_equipo = true;

        // Comprobación de bucle en los equipos hijos (después de modificar el equipo padre)
        if ($modificar_equipo == true)
        {
            if ($id_equipo_padre != $id_equipo_padre_anterior)
            {
                // Carga de información de equipos padres e hijos
                $info_equipos_padres = NULL;
                $info_equipos_hijos = NULL;
                carga_informacion_equipos_instalaciones_padres_hijos(
                    $id_instalacion,
                    $info_equipos_padres,
                    $info_equipos_hijos);
                if ($id_equipo_padre_anterior != ID_NINGUNO)
                {
                    elimina_equipo_instalacion_padre($info_equipos_padres, $id_equipo_padre_anterior, $id_equipo);
                    elimina_equipo_instalacion_hijo($info_equipos_hijos, $id_equipo_padre_anterior, $id_equipo);
                }
                if ($id_equipo_padre != ID_NINGUNO)
                {
                    anyade_equipo_instalacion_padre($info_equipos_padres, $id_equipo_padre, $id_equipo);
                    anyade_equipo_instalacion_hijo($info_equipos_hijos, $id_equipo_padre, $id_equipo);
                }

                // Se comprueba se existe bucle en los equipos hijos
                $existe_bucle = existe_bucle_equipos_instalaciones_hijos($info_equipos_hijos);
                if ($existe_bucle == true)
                {
                    $modificar_equipo = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Hay un bucle en los equipos de la instalación");
                }
            }
        }

        // Comprobación de que los sensores y actuadores no están ya en otro equipo
        if ($modificar_equipo == true)
        {
            if (count($ids_sensores) > 0)
            {
                $ids_sensores_equipos_instalacion = dame_ids_nodos_otros_equipos_instalacion($id_instalacion, TIPO_NODO_SENSOR, $id_equipo);
                if (count(array_intersect($ids_sensores, $ids_sensores_equipos_instalacion)) > 0)
                {
                    $modificar_equipo = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Hay sensores ya asignados a otros equipos de la instalación");
                }
            }
        }
        if ($modificar_equipo == true)
        {
            if (count($ids_actuadores) > 0)
            {
                $ids_actuadores_equipos_instalacion = dame_ids_nodos_otros_equipos_instalacion($id_instalacion, TIPO_NODO_ACTUADOR, $id_equipo);
                if (count(array_intersect($ids_actuadores, $ids_actuadores_equipos_instalacion)) > 0)
                {
                    $modificar_equipo = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Hay actuadores ya asignados a otros equipos de la instalación");
                }
            }
        }

        // Se modifica el equipo
        if ($modificar_equipo == true)
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_equipo_anterior = dame_fila_equipo_instalacion($id_equipo);

            // Se recupera la información de mapa anterior (antes de la modificación)
            $info_posicion_mapa_anterior = dame_info_posicion_mapa_base_datos(
                TIPO_ELEMENTO_MAPA_EQUIPO_INSTALACION,
                $id_equipo,
                ORIGEN_MAPA_INSTALACION,
                $id_instalacion);

            // Se modifica el equipo de la instalación
            $cadena_ids_sensores = "";
            $cadena_ids_actuadores = "";
            if (count($ids_sensores) > 0)
            {
                $cadena_ids_sensores = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_sensores);
            }
            if (count($ids_actuadores) > 0)
            {
                $cadena_ids_actuadores = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_actuadores);
            }
            $operacion_modificacion = "
                UPDATE equipos_instalaciones
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    descripcion = '".$bd_red->_($descripcion)."',
                    instalacion = '".$bd_red->_($id_instalacion)."',
                    equipo_padre = '".$bd_red->_($id_equipo_padre)."',
                    sensores = '".$bd_red->_($cadena_ids_sensores)."',
                    actuadores = '".$bd_red->_($cadena_ids_actuadores)."',
                    estado = '".$bd_red->_($estado)."',
                    observaciones = '".$bd_red->_($observaciones)."',
                    icono_imagen = '".$bd_red->_($icono_imagen)."'
                WHERE
                    id = '".$bd_red->_($id_equipo)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                // Si se ha modificado el equipo padre
                if ($id_equipo_padre != $id_equipo_padre_anterior)
                {
                    // Recarga la información de los equipos padres e hijos
                    $info_equipos_padres = NULL;
                    $info_equipos_hijos = NULL;
                    carga_informacion_equipos_instalaciones_padres_hijos(
                        $id_instalacion,
                        $info_equipos_padres,
                        $info_equipos_hijos);

                    // Se actualizan los órdenes de los equipos padre anterior y actual (y sus padres recursivamente)
                    $ordenes_equipos = NULL;
                    carga_ordenes_equipos_instalaciones_padres_hijos($id_instalacion, $ordenes_equipos);
                    actualiza_orden_equipos_instalaciones_ascendientes(
                        $info_equipos_padres,
                        $info_equipos_hijos,
                        $ordenes_equipos,
                        $id_equipo_padre_anterior);
                    actualiza_orden_equipos_instalaciones_ascendientes(
                        $info_equipos_padres,
                        $info_equipos_hijos,
                        $ordenes_equipos,
                        $id_equipo_padre);
                }

                // Se guarda o elimina la información de la posición en el mapa
                if ($mostrar_en_imagen == VALOR_SI)
                {
                    $info_posicion_mapa_actual = array(
                        "tipo_elemento" => TIPO_ELEMENTO_MAPA_EQUIPO_INSTALACION,
                        "id_elemento" => $id_equipo,
                        "origen" => ORIGEN_MAPA_INSTALACION,
                        "id_origen" => $id_instalacion,
                        "latitud" => $latitud_imagen,
                        "longitud" => $longitud_imagen,
                        "zoom" => $zoom_imagen);
                    guarda_info_posicion_mapa_base_datos($info_posicion_mapa_actual);
                }
                else
                {
                    elimina_info_posicion_mapa_base_datos(
                        TIPO_ELEMENTO_MAPA_EQUIPO_INSTALACION,
                        $id_equipo,
                        ORIGEN_MAPA_INSTALACION,
                        $id_instalacion);
                }

                // Se recupera la fila actual
                $fila_equipo_actual = dame_fila_equipo_instalacion($id_equipo);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_equipo_instalacion(
                    $fila_equipo_actual,
                    $fila_equipo_anterior,
                    $info_posicion_mapa_actual,
                    $info_posicion_mapa_anterior);

                $res = "OK";
                $msg = $idiomas->_("Equipo modificado correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación del equipo
    function anyade_accion_usuario_modificar_equipo_instalacion(
        $fila_actual,
        $fila_anterior,
        $info_posicion_mapa_actual,
        $info_posicion_mapa_anterior)
    {
        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_EQUIPO_INSTALACION;

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["descripcion"] != $fila_anterior["descripcion"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila_actual["descripcion"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila_anterior["descripcion"];
        }
        if ($fila_actual["equipo_padre"] != $fila_anterior["equipo_padre"])
        {
            $nombre_equipo_padre = dame_nombre_equipo_instalacion($fila_actual["equipo_padre"]);
            $nombre_equipo_padre_anterior = dame_nombre_equipo_instalacion($fila_anterior["equipo_padre"]);
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_EQUIPO_PADRE] = $nombre_equipo_padre;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_EQUIPO_PADRE] = $nombre_equipo_padre_anterior;
        }
        if ($fila_actual["sensores"] != $fila_anterior["sensores"])
        {
            $ids_sensores = array();
            $ids_sensores_anteriores = array();
            if ($fila_actual["sensores"] != "")
            {
                $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_actual["sensores"]);
            }
            if ($fila_anterior["sensores"] != "")
            {
                $ids_sensores_anteriores = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_anterior["sensores"]);
            }
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_SENSORES] = dame_nombres_sensores($ids_sensores);
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRES_SENSORES] = dame_nombres_sensores($ids_sensores_anteriores);
        }
        if ($fila_actual["actuadores"] != $fila_anterior["actuadores"])
        {
            $ids_actuadores = array();
            $ids_actuadores_anteriores = array();
            if ($fila_actual["actuadores"] != "")
            {
                $ids_actuadores = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_actual["actuadores"]);
            }
            if ($fila_anterior["actuadores"] != "")
            {
                $ids_actuadores_anteriores = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_anterior["actuadores"]);
            }
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_ACTUADORES] = dame_nombres_actuadores($ids_actuadores);
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRES_ACTUADORES] = dame_nombres_actuadores($ids_actuadores_anteriores);
        }
        if ($fila_actual["estado"] != $fila_anterior["estado"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ESTADO_EQUIPO_INSTALACION] = $fila_actual["estado"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_ESTADO_EQUIPO_INSTALACION] = $fila_anterior["estado"];
        }
        if ($fila_actual["observaciones"] != $fila_anterior["observaciones"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_OBSERVACIONES] = $fila_actual["observaciones"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_OBSERVACIONES] = $fila_anterior["observaciones"];
        }
        if ($fila_actual["icono_imagen"] != $fila_anterior["icono_imagen"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ICONO_IMAGEN] = $fila_actual["icono_imagen"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_ICONO_IMAGEN] = $fila_anterior["icono_imagen"];
        }

        // Información de posición en mapa
        if ($info_posicion_mapa_actual !== $info_posicion_mapa_anterior)
        {
            anyade_parametros_accion_usuario_parametros_info_posicion_mapa($info_posicion_mapa_actual, $parametros_accion_usuario);
            anyade_parametros_accion_usuario_parametros_info_posicion_mapa($info_posicion_mapa_anterior, $parametros_accion_usuario_anteriores);
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        if (count($parametros_accion_usuario) == 0)
        {
            return;
        }

        // Objeto de la acción (se tiene en cuenta si se ha cambiado el nombre)
        if ($fila_actual["nombre"] == $fila_anterior["nombre"])
        {
            $objeto_accion_usuario = $fila_actual["nombre"];
        }
        else
        {
            $objeto_accion_usuario = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $fila_actual["nombre"],
                $fila_anterior["nombre"]));
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            $parametros_accion_usuario_anteriores,
            NULL);
    }
?>
