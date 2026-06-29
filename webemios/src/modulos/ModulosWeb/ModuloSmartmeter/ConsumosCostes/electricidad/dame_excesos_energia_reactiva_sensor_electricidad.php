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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/util_informes_consumos_costes_electricidad.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_EXCESOS_ENERGIA_REACTIVA_SENSOR, $_POST);

    // Se recuperan los excesos de energía reactiva
    $parametros = $_POST;
    $resultado = dame_excesos_energia_reactiva_sensor_electricidad($parametros);
    print(json_encode($resultado));
?>
