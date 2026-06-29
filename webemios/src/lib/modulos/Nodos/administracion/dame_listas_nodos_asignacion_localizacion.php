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


    // Parámetros
    $id_localizacion = $_POST["id_localizacion"];
    $tipo_nodo = $_POST["tipo_nodo"];
    $clase_nodo = $_POST["clase_nodo"];

    // Se recuperan los nodos de la localización
    // Nota: Si la localización es ninguna, se comprueba si los nodos son visibles por el usuario
    $ids_nodos_localizacion = dame_ids_nodos_localizacion($tipo_nodo, $clase_nodo, $id_localizacion);
    $ids_grupos_nodos_localizacion = dame_ids_grupos_nodos_localizacion($tipo_nodo, $clase_nodo, $id_localizacion);
    if ($id_localizacion == ID_NINGUNO)
    {
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                $ids_nodos_usuario_actual = dame_ids_sensores_usuario_actual(true);
                $ids_grupos_nodos_usuario_actual = dame_ids_grupos_sensores_usuario_actual(true);
                break;
            }
            case TIPO_NODO_ACTUADOR:
            {
                $ids_nodos_usuario_actual = dame_ids_actuadores_usuario_actual(true);
                $ids_grupos_nodos_usuario_actual = dame_ids_grupos_actuadores_usuario_actual(true);
                break;
            }
        }
        $ids_nodos_localizacion = array_intersect($ids_nodos_localizacion, $ids_nodos_usuario_actual);
        $ids_grupos_nodos_localizacion = array_intersect($ids_grupos_nodos_localizacion, $ids_grupos_nodos_usuario_actual);
    }
    $html_lista_nodos = dame_lista_nodos(
        $tipo_nodo,
        $clase_nodo,
        $ids_nodos_localizacion,
        OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);
    $html_lista_grupos_nodos = dame_lista_grupos_nodos(
        $tipo_nodo,
        $clase_nodo,
        $ids_grupos_nodos_localizacion,
        OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA);

    print(json_encode(array(
        "res" => "OK",
        "html_lista_nodos" => $html_lista_nodos,
        "html_lista_grupos_nodos" => $html_lista_grupos_nodos))
    );
?>
