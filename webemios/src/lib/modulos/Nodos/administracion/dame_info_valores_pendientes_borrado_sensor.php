<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    $idiomas = new Idiomas();
	$bd_datos = BaseDatosDatos::dame_base_datos();

    // Parámetros
    $nombre_sensor = $_POST['nombre_sensor'];

    // Se devuelve si hay valores pendientes de borrado de un sensor (y si es de la misma clase)
    $fila_informacion_valores_pendientes_borrado_sensor = dame_fila_informacion_valores_pendientes_borrado_sensor($_SESSION["id_red"], $nombre_sensor);
    if ($fila_informacion_valores_pendientes_borrado_sensor === NULL)
    {
        $hay_valores_pendientes_borrado = false;
    }
    else
    {
        $hay_valores_pendientes_borrado = true;
        $clase_sensor_valores_pendientes_borrado = $fila_informacion_valores_pendientes_borrado_sensor["clase"];
        $tipo_valores_sensor_valores_pendientes_borrado = $fila_informacion_valores_pendientes_borrado_sensor["tipo_valores"];
        $incrementos_tiempo_real_horarios_sensor_valores_pendientes_borrado = $fila_informacion_valores_pendientes_borrado_sensor["incrementos_tiempo_real_horarios"];
        $cadena_fecha_hora_utc_base_datos_valores_pendientes_borrado = $fila_informacion_valores_pendientes_borrado_sensor["hora"];

        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_local_base_datos_valores_pendientes_borrado = cambia_zona_horaria_cadena_fecha_hora(
            $cadena_fecha_hora_utc_base_datos_valores_pendientes_borrado,
            FORMATO_FECHA_HORA_BASE_DATOS,
            ZONA_HORARIA_UTC,
            $zona_horaria);
        $cadena_fecha_hora_local_local_valores_pendientes_borrado = convierte_formato_fecha(
            $cadena_fecha_hora_local_base_datos_valores_pendientes_borrado,
            FORMATO_FECHA_HORA_BASE_DATOS,
            $_SESSION["formato_fecha_hora_local"]);
    }

    print(json_encode(array(
        "res" => OK,
        "hay_valores_pendientes_borrado" => $hay_valores_pendientes_borrado,
        "clase_sensor_valores_pendientes_borrado" => $clase_sensor_valores_pendientes_borrado,
        "tipo_valores_sensor_valores_pendientes_borrado" => $tipo_valores_sensor_valores_pendientes_borrado,
        "incrementos_tiempo_real_horarios_sensor_valores_pendientes_borrado" => $incrementos_tiempo_real_horarios_sensor_valores_pendientes_borrado,
        "hora_valores_pendientes_borrado" => $cadena_fecha_hora_local_local_valores_pendientes_borrado))
    );
?>
