<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModulosProyectos/Proyectos/Proyecto.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModulosProyectos/Proyectos/util_proyectos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_VALORES_ADICIONALES_PROYECTO, $_POST);

    // Parámetros
    $id_proyecto = $_POST['id_proyecto'];

    // Se crea el proyecto y se recupera la tabla de valores adicionales
    $params = dame_fila_proyecto($id_proyecto);
    $proyecto = new Proyecto($params);

	$res = "OK";
	$html = $proyecto->dame_tabla_valores_adicionales(true);

    print(json_encode(array(
        "res" => $res,
        "html" => $html))
    );
?>
