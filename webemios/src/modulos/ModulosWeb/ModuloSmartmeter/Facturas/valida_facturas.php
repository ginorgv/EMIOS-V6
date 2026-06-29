<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/util_informes_facturas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_VALIDAR_FACTURAS, $parametros);

    // Se validan las facturas
    $parametros = $_POST;
    $ficheros = $_FILES;
    $resultado = valida_facturas($parametros, $ficheros);
    print(json_encode($resultado));
?>
