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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_VALOR_ADICIONAL_PROYECTO, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_valor_adicional = $_POST['id_valor_adicional'];
    $id_proyecto = $_POST['id_proyecto'];

    // Se recupera la fila del valor adicional
    $fila_valor_adicional = dame_fila_valor_adicional_proyecto($id_valor_adicional);

    // Se elimina el valor adicional del proyecto
	$operacion_borrado = "
        DELETE
        FROM valores_adicionales_proyectos
        WHERE
            id = '".$bd_red->_($id_valor_adicional)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Se invalida el avance y el estado del proyecto
        invalida_avance_estado_proyecto($id_proyecto);

        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_valor_adicional_proyecto($fila_valor_adicional);

        $res = "OK";
        $msg = $idiomas->_("Valor adicional eliminado correctamente");
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


    // Añade la acción de usuario de eliminación del valor adicional del proyecto
    function anyade_accion_usuario_eliminar_valor_adicional_proyecto($fila)
    {
        // Nombre del proyecto
        $nombre_proyecto = dame_nombre_proyecto($fila["proyecto"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_VALOR_ADICIONAL_PROYECTO;
        $objeto_accion_usuario = $fila["nombre"]." (".$nombre_proyecto.")";

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            NULL,
            NULL,
            NULL);
    }
?>
