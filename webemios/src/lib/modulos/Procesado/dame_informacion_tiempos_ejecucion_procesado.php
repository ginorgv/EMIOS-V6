<?php
    session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/util_informes_procesado.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_INFORMACION_TIEMPOS_EJECUCION_PROCESADO, $_POST);

    // Se recupera y devuelve la información de tiempos de ejecución de procesado
    $parametros = $_POST;
    $resultado = dame_informacion_tiempos_ejecucion_procesado($parametros);
    print(json_encode($resultado));
?>
