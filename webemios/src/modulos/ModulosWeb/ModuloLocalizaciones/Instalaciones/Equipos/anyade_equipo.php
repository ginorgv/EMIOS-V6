<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/EquipoInstalacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/util_equipos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_EQUIPO_INSTALACION, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
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

    // Se comprueba si existe un equipo con el mismo nombre en la misma instalación
    $consulta_existe = "
        SELECT *
        FROM equipos_instalaciones
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (instalacion = '".$bd_red->_($id_instalacion)."')";
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
        // Comprobaciones antes de añadir el equipo de la instalación:
        // - Comprobación de bucle en los equipos hijos
        // - Comprobación de que los sensores y actuadores no están ya en otro equipo
        $anyadir_equipo = true;

        // Comprobación de bucle en las localizaciones hijas
        if ($anyadir_equipo == true)
        {
            if ($id_equipo_padre != ID_NINGUNO)
            {
                $info_equipos_padres = NULL;
                $info_equipos_hijos = NULL;
                $id_equipo_auxiliar = ID_NINGUNO;
                carga_informacion_equipos_instalaciones_padres_hijos(
                    $id_instalacion,
                    $info_equipos_padres,
                    $info_equipos_hijos);
                anyade_equipo_instalacion_padre($info_equipos_padres, $id_equipo_padre, $id_equipo_auxiliar);
                anyade_equipo_instalacion_hijo($info_equipos_hijos, $id_equipo_padre, $id_equipo_auxiliar);

                $existe_bucle = existe_bucle_equipos_instalaciones_hijos($info_equipos_hijos);
                if ($existe_bucle == true)
                {
                    $anyadir_equipo = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Hay un bucle en los equipos de la instalación");
                }
            }
        }

        // Comprobación de que los sensores y actuadores no están ya en otro equipo
        if ($anyadir_equipo == true)
        {
            if (count($ids_sensores) > 0)
            {
                $ids_sensores_equipos_instalacion = dame_ids_nodos_otros_equipos_instalacion($id_instalacion, TIPO_NODO_SENSOR, NULL);
                if (count(array_intersect($ids_sensores, $ids_sensores_equipos_instalacion)) > 0)
                {
                    $anyadir_equipo = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Hay sensores ya asignados a otros equipos de la instalación");
                }
            }
        }
        if ($anyadir_equipo == true)
        {
            if (count($ids_actuadores) > 0)
            {
                $ids_actuadores_equipos_instalacion = dame_ids_nodos_otros_equipos_instalacion($id_instalacion, TIPO_NODO_ACTUADOR, NULL);
                if (count(array_intersect($ids_actuadores, $ids_actuadores_equipos_instalacion)) > 0)
                {
                    $anyadir_equipo = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("Hay actuadores ya asignados a otros equipos de la instalación");
                }
            }
        }

        // Se añade el equipo de la instalación
        if ($anyadir_equipo == true)
        {
            // Se añade el equipo de la instalación
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
            $operacion_insercion = "
                INSERT INTO equipos_instalaciones (
                    nombre,
                    descripcion,
                    red,
                    instalacion,
                    equipo_padre,
                    orden,
                    sensores,
                    actuadores,
                    estado,
                    observaciones,
                    icono_imagen
                ) VALUES (
                    '".$bd_red->_($nombre)."',
                    '".$bd_red->_($descripcion)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_instalacion)."',
                    '".$bd_red->_($id_equipo_padre)."',
                    0,
                    '".$bd_red->_($cadena_ids_sensores)."',
                    '".$bd_red->_($cadena_ids_actuadores)."',
                    '".$bd_red->_($estado)."',
                    '".$bd_red->_($observaciones)."',
                    '".$bd_red->_($icono_imagen)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recuperan el id y la fila del equipo de instalación añadido
                $id_equipo = $bd_red->dame_id_autoincremental_ultima_insercion();
                $fila_equipo = dame_fila_equipo_instalacion($id_equipo);

                // Si hay equipo padre
                if ($id_equipo_padre != ID_NINGUNO)
                {
                    // Recarga la información de los equipos padres e hijos
                    $info_equipos_padres = NULL;
                    $info_equipos_hijos = NULL;
                    carga_informacion_equipos_instalaciones_padres_hijos(
                        $id_instalacion,
                        $info_equipos_padres,
                        $info_equipos_hijos);

                    // Se actualiza el orden del equipo padre (y sus padres recursivamente)
                    $ordenes_equipos = NULL;
                    carga_ordenes_equipos_instalaciones_padres_hijos($id_instalacion, $ordenes_equipos);
                    $numero_equipos_actualizados = actualiza_orden_equipos_instalaciones_ascendientes(
                        $info_equipos_padres,
                        $info_equipos_hijos,
                        $ordenes_equipos,
                        $id_equipo_padre);
                }

                // Se guarda la información de la posición en la imagen (mapa)
                if ($mostrar_en_imagen == VALOR_SI)
                {
                    $info_posicion_mapa = array(
                        "tipo_elemento" => TIPO_ELEMENTO_MAPA_EQUIPO_INSTALACION,
                        "id_elemento" => $id_equipo,
                        "origen" => ORIGEN_MAPA_INSTALACION,
                        "id_origen" => $id_instalacion,
                        "latitud" => $latitud_imagen,
                        "longitud" => $longitud_imagen,
                        "zoom" => $zoom_imagen);
                    guarda_info_posicion_mapa_base_datos($info_posicion_mapa);
                }

                // Se añade la acción de usuario
                anyade_accion_usuario_anyadir_equipo_instalacion($fila_equipo, $info_posicion_mapa);

                $res = "OK";
                $msg = $idiomas->_("Equipo añadido correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
            }
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "numero_equipos_actualizados" => $numero_equipos_actualizados))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de adición del equipo
    function anyade_accion_usuario_anyadir_equipo_instalacion($fila, $info_posicion_mapa)
    {
        // Nombre de la instalación
        $nombre_instalacion = dame_nombre_instalacion($fila["instalacion"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_EQUIPO_INSTALACION;
        $objeto_accion_usuario = $fila["nombre"]." (".$nombre_instalacion.")";

        // Nombre del equipo padre
        $nombre_equipo_padre = dame_nombre_equipo_instalacion($fila["equipo_padre"]);

        // Ids de sensores y de actuadores
        $ids_sensores = array();
        if ($fila["sensores"] != "")
        {
            $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila["sensores"]);
        }
        $ids_actuadores = array();
        if ($fila["actuadores"] != "")
        {
            $ids_actuadores = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila["actuadores"]);
        }

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DESCRIPCION] = $fila["descripcion"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_EQUIPO_PADRE] = $nombre_equipo_padre;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_SENSORES] = dame_nombres_sensores($ids_sensores);
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRES_ACTUADORES] = dame_nombres_actuadores($ids_actuadores);
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ESTADO_EQUIPO_INSTALACION] = $fila["estado"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_OBSERVACIONES] = $fila["observaciones"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_ICONO_IMAGEN] = $fila["icono_imagen"];

        // Información de posición en mapa
        anyade_parametros_accion_usuario_parametros_info_posicion_mapa($info_posicion_mapa, $parametros_accion_usuario);

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
