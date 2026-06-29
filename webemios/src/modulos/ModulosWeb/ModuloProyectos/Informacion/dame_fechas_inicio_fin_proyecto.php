<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    $id_proyecto = $_POST["id_proyecto"];

    $fecha_hora_actual_local = dame_fecha_hora_actual_local();
    $fecha_hora_ayer_local = dame_fecha_hora_actual_local();
    $fecha_hora_ayer_local->modify('-1 day');
    $zona_horaria = dame_zona_horaria_local();
    if ($id_proyecto == ID_NINGUNO)
    {
        $fecha_hora_inicio_local = clone $fecha_hora_actual_local;
        $fecha_hora_inicio_local->modify('-1 '.PERIODO_DEFECTO_PROYECTOS_INFORMACION_PROYECTO);
        $fecha_hora_fin_local = clone $fecha_hora_ayer_local;
        $cadena_fecha_inicio_date_javascript_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, FORMATO_FECHA_DATE_JAVASCRIPT, $zona_horaria);
        $cadena_fecha_fin_date_javascript_local = convierte_fecha_a_cadena($fecha_hora_fin_local, FORMATO_FECHA_DATE_JAVASCRIPT, $zona_horaria);
    }
    else
    {
        $fila_proyecto = dame_fila_proyecto($id_proyecto);
        $cadena_fecha_inicio_date_javascript_local = convierte_formato_fecha($fila_proyecto["fecha_inicio"], FORMATO_FECHA_BASE_DATOS, FORMATO_FECHA_DATE_JAVASCRIPT);
        $cadena_fecha_fin_date_javascript_local = convierte_formato_fecha($fila_proyecto["fecha_fin"], FORMATO_FECHA_BASE_DATOS, FORMATO_FECHA_DATE_JAVASCRIPT);

        // Si la fecha de fin de proyecto es mayor que la fecha del día anterior (ayer), se establece a la fecha del día anterior (ayer)
        // (porque los datos del proyecto están calculados hasta el día anterior)
        $fecha_fin_proyecto_local = convierte_cadena_a_fecha($cadena_fecha_fin_date_javascript_local, FORMATO_FECHA_BASE_DATOS, $zona_horaria);
        if ($fecha_fin_proyecto_local > $fecha_hora_ayer_local)
        {
            $cadena_fecha_fin_date_javascript_local = convierte_fecha_a_cadena($fecha_hora_ayer_local, FORMATO_FECHA_DATE_JAVASCRIPT, $zona_horaria);
        }
    }

    print(json_encode(array(
        "res" => "OK",
        "fecha_inicio" => $cadena_fecha_inicio_date_javascript_local,
        "fecha_fin" => $cadena_fecha_fin_date_javascript_local))
    );
?>

