<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/util_acciones.php');


    $clase_actuador = $_POST["clase_actuador"];
    $destino = $_POST["destino"];
    $id_destino = $_POST["id_destino"];
    $origen_controles_accion = $_POST["origen_controles_accion"];

    // Se recupera la información de la última acción
    if (($id_destino != ID_NINGUNO) && ($origen_controles_accion != ORIGEN_CONTROLES_ACCION_ENVIO_ACCION))
    {
        switch ($destino)
        {
            case DESTINO_ACCION_ACTUADOR:
            {
                $fila_actuador_grupo = dame_fila_actuador($id_destino);
                break;
            }
            case DESTINO_ACCION_GRUPO_ACTUADORES:
            {
                $fila_actuador_grupo = dame_fila_grupo_actuadores($id_destino);
                break;
            }
        }
        $contenido_ultima_accion = $fila_actuador_grupo["contenido_ultima_accion"];
        $valor_ultima_accion = $fila_actuador_grupo["valor_ultima_accion"];
    }

    // Se devuelven los controles de la acción
    $html = dame_controles_accion(
        $clase_actuador,
        $contenido_ultima_accion,
        $valor_ultima_accion,
        $origen_controles_accion);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))
    );
?>

