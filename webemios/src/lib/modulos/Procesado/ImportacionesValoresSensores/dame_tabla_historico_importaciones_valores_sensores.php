<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/ImportacionesValoresSensores/HistoricoImportacionValoresSensor.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_HISTORICO_IMPORTACIONES_VALORES_SENSORES, $_POST);

    // Parámetros
    $modulo = $_POST["modulo"];
    $filtro = $_POST["filtro"];
    $clase_sensor = $_POST["clase_sensor"];
    $cadena_fecha_hora_inicio_local_local = $_POST["fecha_hora_inicio"];
    $cadena_fecha_hora_fin_local_local = $_POST["fecha_hora_fin"];
    $resultado_ejecucion = $_POST["resultado_ejecucion"];

    // Conversión de fechas
    $zona_horaria = dame_zona_horaria_local();
    $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
    $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
    $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
    $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

    $limite_elementos_tabla_superado = false;
	$contenido = HistoricoImportacionValoresSensor::dame_tabla_historico_importaciones_valores_sensores(
        $modulo,
        $filtro,
        $clase_sensor,
        $cadena_fecha_hora_inicio_base_datos_utc,
        $cadena_fecha_hora_fin_base_datos_utc,
        $resultado_ejecucion,
        $limite_elementos_tabla_superado);

	print(json_encode(array(
        "res" => "OK",
        "html" => $contenido,
        "limite_elementos_tabla_superado" => $limite_elementos_tabla_superado))
    );
?>
