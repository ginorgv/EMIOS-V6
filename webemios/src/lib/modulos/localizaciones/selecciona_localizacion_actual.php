<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_SELECCIONAR_LOCALIZACION, $_POST);

	$idiomas = new Idiomas();

    // Parámetros
    $id_localizacion = $_POST["id_localizacion"];
    $nombre_localizacion = $_POST["nombre_localizacion"];
    $ids_localizaciones_seleccionadas = $_POST["ids_localizaciones_seleccionadas"];

    // Descripción de la localización
    $descripcion_localizacion = $nombre_localizacion;
    switch ($id_localizacion)
    {
        case ID_DESACTIVADO:
        case ID_NINGUNO:
        case ID_TODOS:
        case ID_LOCALIZACIONES_SELECCIONADAS_AND:
        case ID_LOCALIZACIONES_SELECCIONADAS_OR:
        {
            $descripcion_localizacion = strtolower($descripcion_localizacion);
            break;
        }
    }

    // Se establecen la localización y las localizaciones seleccionadas
    $_SESSION["id_localizacion"] = $id_localizacion;
    $_SESSION["ids_localizaciones_seleccionadas"] = $ids_localizaciones_seleccionadas;

    $res = "OK";
    $msg = $idiomas->_("Localización actual establecida correctamente")." (".htmlspecialchars($descripcion_localizacion, ENT_QUOTES).")";

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
