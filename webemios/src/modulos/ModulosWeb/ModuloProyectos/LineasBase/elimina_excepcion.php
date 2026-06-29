<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_EXCEPCION_LINEA_BASE, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_excepcion = $_POST['id_excepcion'];
    $id_linea_base_padre = $_POST['id_linea_base_padre'];

    // Se recupera la información de la excepción de la línea base
    $fila_excepcion_linea_base = dame_fila_excepcion_linea_base($id_excepcion);

    // Se elimina la excepción de la línea base
	$operacion_borrado = "
        DELETE
        FROM excepciones_lineas_base
        WHERE
            id = '".$bd_red->_($id_excepcion)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Se invalidan los avances y el estado de los proyectos dependientes de esta línea base
        invalida_avance_estado_proyectos_dependientes_linea_base($id_linea_base_padre);

        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_excepcion_linea_base($fila_excepcion_linea_base);

        $res = "OK";
        $msg = $idiomas->_("Excepción eliminada correctamente");
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


    // Añade la acción de usuario de eliminación de la excepción de la línea base
    function anyade_accion_usuario_eliminar_excepcion_linea_base($fila)
    {
        // Nombre de la línea base
        $nombre_linea_base = dame_nombre_linea_base($fila["linea_base_padre"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_EXCEPCION_LINEA_BASE;
        $objeto_accion_usuario = $fila["nombre"]." (".$nombre_linea_base.")";

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            NULL,
            NULL,
            NULL);
    }
?>
