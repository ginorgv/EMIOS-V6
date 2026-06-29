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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/util_informes_caudales.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_COSTES_CAUDALES_OPTIMOS_FICHERO_TARIFA_GAS, $_POST);

    // Se recuperan los costes y caudales óptimos a partir de los datos de un fichero
    $parametros = $_POST;
    $ficheros = $_FILES;
    $resultado = dame_coste_caudal_diario_optimo_fichero_tarifa_gas($parametros, $ficheros);
    print(json_encode($resultado));
?>
