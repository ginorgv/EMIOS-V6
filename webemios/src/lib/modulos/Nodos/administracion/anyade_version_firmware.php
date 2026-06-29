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
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_dispositivos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Se obtienen los parámetros de la petición interna
    $id_version = $_POST["id_version"];
    $nombre_fichero = $_POST["nombre_fichero"];
    $servidor = $_POST["servidor"];

    // Se comprueba si existe una version con el mismo nombre y en el mismo servidor
    $consulta_existe = "
        SELECT id
        FROM versiones_firmware_radon
        WHERE
            (nombre_fichero = '".$bd_red->_($nombre_fichero)."')
            AND (servidor = '".$bd_red->_($servidor)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe una version con el mismo nombre en el mismo servidor");
    }
    else
    {
        // Se añade la version de firmware
        $operacion_insercion = "
            INSERT INTO versiones_firmware_radon (
                id_version,
                nombre_fichero,
                servidor
            ) VALUES (
                '".$bd_red->_($id_version)."',
                '".$bd_red->_($nombre_fichero)."',
                '".$bd_red->_($servidor)."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            $res = "OK";
            $msg = "Version registrada correctamente";
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
