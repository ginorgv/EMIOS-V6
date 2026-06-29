<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/util_parametros.php');


    $tipo = $_POST["tipo"];
    $cadena_parametros_tipo = $_POST["cadena_parametros_tipo"];
    $id_valor_parametro = $_POST["id_valor_parametro"];
    $cadena_ids_parametros_asociados = $_POST["cadena_ids_parametros_asociados"];

    $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
    $ids_parametros_asociados = explode(",", $cadena_ids_parametros_asociados);
    $ids_valores_parametros_asociados = dame_ids_valores_parametros_asociados_valor_parametro(
        $tipo,
        $parametros_tipo,
        $id_valor_parametro,
        $ids_parametros_asociados);

    print(json_encode(array(
        "res" => "OK",
        "ids_valores_parametros_asociados" => $ids_valores_parametros_asociados))
    );
?>