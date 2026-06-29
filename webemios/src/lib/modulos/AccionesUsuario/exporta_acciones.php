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

    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/AccionUsuario.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_EXPORTAR_ACCIONES_USUARIO, $_POST);

    $idiomas = new Idiomas();

    // Parámetros
    $modulo = $_POST["modulo"];
    $filtro = $_POST["filtro"];
    $cadena_fecha_hora_inicio_local_local = $_POST["fecha_hora_inicio"];
    $cadena_fecha_hora_fin_local_local = $_POST["fecha_hora_fin"];

    // Conversión de fechas
    $zona_horaria = dame_zona_horaria_local();
    $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
    $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
    $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
    $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

    // Se exportan las acciones
    $ruta_relativa_fichero_acciones_exportadas = "";
    $numero_acciones_exportadas = AccionUsuario::exporta_acciones(
        $modulo,
        $filtro,
        $cadena_fecha_hora_inicio_base_datos_utc,
        $cadena_fecha_hora_fin_base_datos_utc,
        $ruta_relativa_fichero_acciones_exportadas);
    if ($numero_acciones_exportadas == 0)
    {
        $msg = $idiomas->_("No hay acciones para exportar");
    }
    else
    {
        $msg = $idiomas->_("Acciones exportadas correctamente").":\n";
        $msg .= "- ".$idiomas->_("Número de acciones exportadas").": ".$numero_acciones_exportadas."\n";
    }

	print(json_encode(array(
        "res" => "OK",
        "msg" => $msg,
        "ruta_fichero_acciones_exportadas" => $ruta_relativa_fichero_acciones_exportadas))
    );
?>
