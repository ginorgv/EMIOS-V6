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
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_INSTALACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_instalacion = $_POST['id_instalacion'];

    // Se recupera la fila de la instalación
    $fila_instalacion = dame_fila_instalacion($id_instalacion);

    // Se elimina la instalación
    $operacion_borrado = "
        DELETE
        FROM instalaciones
        WHERE
            id = '".$bd_red->_($id_instalacion)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Acciones a realizar al eliminar una instalación
        realiza_acciones_instalacion_eliminada($id_instalacion);

        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_instalacion($fila_instalacion);

        $res = "OK";
        $msg = $idiomas->_("Instalación eliminada correctamente");
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


    // Realiza acciones al eliminar una instalación
    function realiza_acciones_instalacion_eliminada($id_instalacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se eliminan los equipos de la instalación
        $operacion_borrado_equipos_instalacion = "
            DELETE
            FROM equipos_instalaciones
            WHERE
                instalacion = '".$bd_red->_($id_instalacion)."'";
        $res_borrado_equipos_instalacion = $bd_red->ejecuta_operacion($operacion_borrado_equipos_instalacion);
        if ($res_borrado_equipos_instalacion == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_equipos_instalacion."'");
        }

        // Se eliminan las anotaciones de los equipos de la instalación
        $operacion_borrado_anotaciones_equipos_instalacion = "
            DELETE
            FROM anotaciones_equipos_instalaciones
            WHERE
                instalacion = '".$bd_red->_($id_instalacion)."'";
        $res_borrado_anotaciones_equipos_instalacion = $bd_red->ejecuta_operacion($operacion_borrado_anotaciones_equipos_instalacion);
        if ($res_borrado_anotaciones_equipos_instalacion == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_anotaciones_equipos_instalacion."'");
        }

        // Se elimina la imagen de la instalación
        elimina_imagen_base_datos(ORIGEN_IMAGEN_INSTALACION_IMAGEN, $id_instalacion);

        // Se eliminan las posiciones de mapa de la instalación
        elimina_info_posiciones_mapa_elemento_base_datos(TIPO_ELEMENTO_MAPA_INSTALACION, $id_instalacion);
        elimina_info_posiciones_mapa_origen_base_datos(ORIGEN_MAPA_INSTALACION, $id_instalacion);
    }


    // Añade la acción de usuario de eliminación de la instalación
    function anyade_accion_usuario_eliminar_instalacion($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_INSTALACION;
        $objeto_accion_usuario = $fila["nombre"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            NULL,
            NULL,
            NULL);
    }
?>
