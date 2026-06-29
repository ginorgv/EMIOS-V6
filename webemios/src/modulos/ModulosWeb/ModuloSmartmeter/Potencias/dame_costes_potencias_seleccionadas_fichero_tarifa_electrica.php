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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/util_informes_potencias.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_COSTES_POTENCIAS_SELECCIONADAS_FICHERO_TARIFA_ELECTRICA, $_POST);

    // Se recuperan los costes y potencias seleccionadas de un sensor
    $parametros = $_POST;
    $ficheros = $_FILES;
    $resultado = dame_costes_potencias_seleccionadas_fichero_tarifa_electricidad($parametros, $ficheros);
    print(json_encode($resultado));
?>
