<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_herramientas_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');


    // Parámetros
    $clase_nodo = $_POST["clase_nodo"];
    $id_grupo = $_POST["id_grupo"];
    $tipo_nodo = $_POST["tipo_nodo"];

    // Se recuperan los grupos de nodos
    switch ($tipo_nodo)
    {
        case TIPO_NODO_SENSOR:
        {
            $html = dame_lista_grupos_sensores($clase_nodo, array($id_grupo), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
            break;
        }
        case TIPO_NODO_ACTUADOR:
        {
            $html = dame_lista_grupos_actuadores($clase_nodo, array($id_grupo), OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
            break;
        }
    }
    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>
