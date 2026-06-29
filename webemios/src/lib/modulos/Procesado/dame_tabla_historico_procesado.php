<?php
    session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');

    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/HistoricoProcesado.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_HISTORICO_PROCESADO, $_POST);

    // Parámetros
    $tipo_ejecucion_procesado = $_POST["tipo_ejecucion_procesado"];
    $clase_sensor = $_POST["clase_sensor"];
    $tipo_sensor = $_POST["tipo_sensor"];
    $granularidad = $_POST["granularidad"];
    $cadena_fecha_hora_inicio_local_local = $_POST["fecha_hora_inicio"];
    $cadena_fecha_hora_fin_local_local = $_POST["fecha_hora_fin"];

    // Conversión de fechas
    $zona_horaria = dame_zona_horaria_local();
    $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
    $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
    $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
    $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

    $limite_elementos_tabla_superado = false;
	$contenido = HistoricoProcesado::dame_tabla_historico_procesado(
        $tipo_ejecucion_procesado,
        $clase_sensor,
        $tipo_sensor,
        $granularidad,
        $cadena_fecha_hora_inicio_base_datos_utc,
        $cadena_fecha_hora_fin_base_datos_utc,
        $limite_elementos_tabla_superado);

	print(json_encode(array(
        "res" => "OK",
        "html" => $contenido,
        "limite_elementos_tabla_superado" => $limite_elementos_tabla_superado))
    );
?>
