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
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/util_equipos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_EQUIPO_INSTALACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_instalacion = $_POST['id_instalacion'];
    $id_equipo = $_POST['id_equipo'];

    // Se recupera la información de la fila del equipo de la instalación
    $fila_equipo = dame_fila_equipo_instalacion($id_equipo);

    // Se elimina el equipo de la instalación
	$operacion_borrado = "
        DELETE
        FROM equipos_instalaciones
        WHERE
            id = '".$bd_red->_($id_equipo)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Acciones a realizar al eliminar un equipo de una instalación
        realiza_acciones_equipo_instalacion_eliminado($id_instalacion, $id_equipo, $fila_equipo);

        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_equipo_instalacion($fila_equipo);

        $res = "OK";
        $msg = $idiomas->_("Equipo eliminado correctamente");
    }
    else
    {
        throw new Exception("Error en la operación: '".$operacion_borrado."'");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Realiza acciones al eliminar un equipo de una instalación
    function realiza_acciones_equipo_instalacion_eliminado($id_instalacion, $id_equipo, $fila_equipo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se eliminan las anotaciones
        $operacion_borrado_anotaciones = "
            DELETE
            FROM anotaciones_equipos_instalaciones
            WHERE
                equipo = '".$bd_red->_($id_equipo)."'";
        $res_borrado_anotaciones = $bd_red->ejecuta_operacion($operacion_borrado_anotaciones);
        if ($res_borrado_anotaciones == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_anotaciones."'");
        }

        // Se eliminan las imágenes de las anotaciones
        $operacion_borrado_imagenes_anotaciones = "
            DELETE
            FROM imagenes
            WHERE
                (origen = '".ORIGEN_IMAGEN_ANOTACION_EQUIPO_INSTALACION_FOTO."')
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(id_origen, '".SEPARADOR_PARAMETROS_SIMPLES."', ".(1 + 1)."), '".SEPARADOR_PARAMETROS_SIMPLES."', -1) = '".$bd_red->_($id_equipo)."')";
        $res_borrado_imagenes_anotaciones = $bd_red->ejecuta_operacion($operacion_borrado_imagenes_anotaciones);
        if ($res_borrado_imagenes_anotaciones == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_imagenes_anotaciones."'");
        }

        // Se establece el equipo padre de los equipos hijos de este equipo a ninguno
        $operacion_modificacion_equipos_hijos = "
            UPDATE equipos_instalaciones
            SET
                equipo_padre = '".ID_NINGUNO."'
            WHERE
                equipo_padre = '".$bd_red->_($id_equipo)."'";
        $res_modificacion_equipos_hijos = $bd_red->ejecuta_operacion($operacion_modificacion_equipos_hijos);
        if ($res_modificacion_equipos_hijos == false)
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion_equipos_hijos."'");
        }

        // Si había equipo padre
        $id_equipo_padre = $fila_equipo["equipo_padre"];
        if ($id_equipo_padre != ID_NINGUNO)
        {
            // Recarga la información de los equipos padres e hijos
            $info_equipos_padres = NULL;
            $info_equipos_hijos = NULL;
            carga_informacion_equipos_instalaciones_padres_hijos(
                $id_instalacion,
                $info_equipos_padres,
                $info_equipos_hijos, true);

            // Se actualiza el orden del equipo padre (y sus padres recursivamente)
            $ordenes_equipos = NULL;
            carga_ordenes_equipos_instalaciones_padres_hijos($id_instalacion, $ordenes_equipos);
            actualiza_orden_equipos_instalaciones_ascendientes(
                $info_equipos_padres,
                $info_equipos_hijos,
                $ordenes_equipos,
                $id_equipo_padre);
        }

        // Se eliminan las posiciones de mapa del equipo
        elimina_info_posiciones_mapa_elemento_base_datos(TIPO_ELEMENTO_MAPA_EQUIPO_INSTALACION, $id_equipo);
    }


    // Añade la acción de usuario de eliminación del equipo de una instalación
    function anyade_accion_usuario_eliminar_equipo_instalacion($fila)
    {
        // Nombre de la instalación
        $nombre_instalacion = dame_nombre_instalacion($fila["instalacion"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_EQUIPO_INSTALACION;
        $objeto_accion_usuario = $fila["nombre"]." (".$nombre_instalacion.")";

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            NULL,
            NULL,
            NULL);
    }
?>
