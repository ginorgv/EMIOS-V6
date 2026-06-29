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
    $nombre_fichero = $_POST["nombre_fichero"];
    $servidor = $_POST["servidor"];

    // Se elimina el registro
    $consulta_elimina = "
        DELETE
        FROM versiones_firmware_radon
        WHERE
            (nombre_fichero = '".$bd_red->_($nombre_fichero)."')
            AND (servidor = '".$bd_red->_($servidor)."')";
    $res_elimina = $bd_red->ejecuta_consulta($consulta_elimina);
    if ($res_elimina == true)
        {
            $res = "OK";
            $msg = $idiomas->_("Version eliminada del registro correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_borrado."'");
        }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
