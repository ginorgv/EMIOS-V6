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

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_COMENTARIO, $_POST);

	$idiomas = new Idiomas();
	$bd_datos = BaseDatosDatos::dame_base_datos();

    // Parámetros
    $id_comentario = $_POST["id_comentario"];
    $cadena_fecha_hora_local_local = $_POST["fecha_hora"];
    $tipo = $_POST["tipo"];
    $visibilidad = $_POST["visibilidad"];
    $objeto = $_POST["objeto"];
    $descripcion = $_POST["descripcion"];

    // Conversiones de fechas a UTC
    $zona_horaria = dame_zona_horaria_local();
    $cadena_fecha_hora_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
    $cadena_fecha_hora_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

    // Se modifica el comentario
    $operacion_modificacion = "
        UPDATE comentarios
        SET
            hora = '".$bd_datos->_($cadena_fecha_hora_base_datos_utc)."',
            usuario = '".$bd_datos->_($_SESSION["id_usuario"])."',
            tipo = '".$bd_datos->_($tipo)."',
            visibilidad = '".$bd_datos->_($visibilidad)."',
            objeto = '".$bd_datos->_($objeto)."',
            descripcion = '".$bd_datos->_($descripcion)."'
        WHERE
            (id = '".$bd_datos->_($id_comentario)."')
            AND (red = '".$_SESSION["id_red"]."')";
    $res_modificacion = $bd_datos->ejecuta_operacion($operacion_modificacion);
    if ($res_modificacion == true)
    {
        $res = "OK";
        $msg = $idiomas->_("Comentario modificado correctamente");
    }
    else
    {
        throw new Exception("Error en la operación: '".$operacion_modificacion."'");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>