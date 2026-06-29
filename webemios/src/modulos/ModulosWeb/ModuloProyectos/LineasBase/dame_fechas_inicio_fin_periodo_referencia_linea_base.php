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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/util_lineas_base.php');


    $id_linea_base = $_POST["id_linea_base"];

    if ($id_linea_base == ID_NINGUNO)
    {
        $zona_horaria = dame_zona_horaria_local();
        $fecha_hora_actual_local = dame_fecha_hora_actual_local();
        $fecha_hora_inicio_local = clone $fecha_hora_actual_local;
        $fecha_hora_inicio_local->modify('-1 '.PERIODO_DEFECTO_PROYECTOS_SIMULADOR_LINEA_BASE);
        $fecha_hora_fin_local = clone $fecha_hora_actual_local;
        $cadena_fecha_inicio_periodo_referencia_date_javascript_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, FORMATO_FECHA_DATE_JAVASCRIPT, $zona_horaria);
        $cadena_fecha_fin_periodo_referencia_date_javascript_local = convierte_fecha_a_cadena($fecha_hora_fin_local, FORMATO_FECHA_DATE_JAVASCRIPT, $zona_horaria);
    }
    else
    {
        $fila_linea_base = dame_fila_linea_base($id_linea_base);
        $cadena_fecha_inicio_periodo_referencia_date_javascript_local = convierte_formato_fecha($fila_linea_base["fecha_inicio_periodo_referencia"], FORMATO_FECHA_BASE_DATOS, FORMATO_FECHA_DATE_JAVASCRIPT);
        $cadena_fecha_fin_periodo_referencia_date_javascript_local = convierte_formato_fecha($fila_linea_base["fecha_fin_periodo_referencia"], FORMATO_FECHA_BASE_DATOS, FORMATO_FECHA_DATE_JAVASCRIPT);
    }

    print(json_encode(array(
        "res" => "OK",
        "fecha_inicio_periodo_referencia" => $cadena_fecha_inicio_periodo_referencia_date_javascript_local,
        "fecha_fin_periodo_referencia" => $cadena_fecha_fin_periodo_referencia_date_javascript_local))
    );
?>

