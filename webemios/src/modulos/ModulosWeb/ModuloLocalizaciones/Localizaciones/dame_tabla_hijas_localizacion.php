<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/Localizacion.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_TABLA_HIJAS_LOCALIZACION, $_POST);

	$bd_red = BaseDatosRed::dame_base_datos();

    // Se recupera la información de la localización
    $id_localizacion = $_POST['id_localizacion'];
	$consulta_localizacion = "
		SELECT *
		FROM
            localizaciones
		WHERE
			id = '".$bd_red->_($id_localizacion)."'";
	$res_localizacion = $bd_red->ejecuta_consulta($consulta_localizacion);
    if (($res_localizacion == false) || ($res_localizacion->dame_numero_filas() == 0))
    {
        throw new Exception("Error o no existe la información en la base de datos: '".$consulta_localizacion."'");
    }

    $fila_localizacion = $res_localizacion->dame_siguiente_fila();
    $localizacion = new Localizacion($fila_localizacion);

    // Se crea la localización y se recupera la tabla de localizaciones hijas
	$res = "OK";
	$html = $localizacion->dame_tabla_hijas();

    print(json_encode(array(
        "res" => $res,
        "html" => $html))
    );
?>
