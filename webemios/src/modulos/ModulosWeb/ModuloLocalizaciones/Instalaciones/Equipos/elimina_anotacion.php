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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_ANOTACION_EQUIPO_INSTALACION, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_instalacion = $_POST['id_instalacion'];
    $id_equipo = $_POST['id_equipo'];
    $id_anotacion = $_POST['id_anotacion'];

    // Se recupera la información de la fila de la anotración del equipo de la instalación
    $fila_anotacion = dame_fila_anotacion_equipo_instalacion($id_anotacion);

    // Se elimina la anotación del equipo de la instalación
	$operacion_borrado = "
        DELETE
        FROM anotaciones_equipos_instalaciones
        WHERE
            id = '".$bd_red->_($id_anotacion)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Acciones a realizar al eliminar una anotación de un equipo de una instalación
        realiza_acciones_anotacion_equipo_instalacion_eliminado($id_instalacion, $id_equipo, $id_anotacion);

        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_anotacion_equipo_instalacion($fila_anotacion);

        $res = "OK";
        $msg = $idiomas->_("Anotación eliminada correctamente");
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


    // Realiza acciones al eliminar una anotación de un equipo de una instalación
    function realiza_acciones_anotacion_equipo_instalacion_eliminado($id_instalacion, $id_equipo, $id_anotacion)
    {
        // Se elimina la imagen de la anotación
        $id_origen = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
            $id_instalacion,
            $id_equipo,
            $id_anotacion));
        elimina_imagen_base_datos(ORIGEN_IMAGEN_ANOTACION_EQUIPO_INSTALACION_FOTO, $id_origen);
    }


    // Añade la acción de usuario de eliminación de la anotación del equipo de una instalación
    function anyade_accion_usuario_eliminar_anotacion_equipo_instalacion($fila)
    {
        // Nombres de la instalación y del equipo
        $nombre_instalacion = dame_nombre_instalacion($fila["instalacion"]);
        $nombre_equipo = dame_nombre_equipo_instalacion($fila["equipo"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_ANOTACION_EQUIPO_INSTALACION;
        $objeto_accion_usuario = $nombre_equipo." (".$nombre_instalacion.")";

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            NULL,
            NULL,
            NULL);
    }
?>
