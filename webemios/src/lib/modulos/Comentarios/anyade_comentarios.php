<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');

    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_COMENTARIOS, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();
	$bd_datos = BaseDatosDatos::dame_base_datos();

    // Parámetros
    $cadena_fecha_hora_local_local = $_POST["fecha_hora"];
    $tipo = $_POST["tipo"];
    $visibilidad = $_POST["visibilidad"];
    $objetos = $_POST["objetos"];
    $descripcion = $_POST["descripcion"];

    // Conversión de fechas
    $zona_horaria = dame_zona_horaria_local();
    $cadena_fecha_hora_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
    $cadena_fecha_hora_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

    // Se añaden los comentarios
    foreach ($objetos as $objeto)
    {
        $operacion_insercion = "
            INSERT INTO comentarios (
                hora,
                usuario,
                tipo,
                visibilidad,
                objeto,
                descripcion,
                red
            ) VALUES (
                '".$bd_datos->_($cadena_fecha_hora_base_datos_utc)."',
                '".$bd_datos->_($_SESSION["id_usuario"])."',
                '".$bd_datos->_($tipo)."',
                '".$bd_datos->_($visibilidad)."',
                '".$bd_datos->_($objeto)."',
                '".$bd_datos->_($descripcion)."',
                '".$_SESSION["id_red"]."'
            )";
        $res_insercion = $bd_datos->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == false)
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }
    $res = "OK";
    $msg = $idiomas->_("Comentarios añadidos correctamente");

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>