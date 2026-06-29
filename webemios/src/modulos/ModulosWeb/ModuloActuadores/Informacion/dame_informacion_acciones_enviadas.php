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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Informacion/util_informes_informacion.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_ACCIONES_ENVIADAS, $_POST);

    // Se recupera la información de acciones enviadas
    $parametros = $_POST;
    $resultado = dame_informacion_acciones_enviadas($parametros);
    print(json_encode($resultado));
?>
