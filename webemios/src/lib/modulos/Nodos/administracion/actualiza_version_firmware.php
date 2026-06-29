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
    $id_dispositivo = $_POST["id_dispositivo"];
    $nombre_fichero = $_POST["nombre_fichero"];
    $servidor = $_POST["servidor"];

    // Se obtiene el IMEI a partir del id dispositivo
    $consulta_imei = "
        SELECT imei
        FROM dispositivos
        WHERE
            (id = ".$id_dispositivo.")";

    $res_consulta = $bd_red->ejecuta_consulta($consulta_imei);
    
    if ($res_consulta == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_consulta->dame_numero_filas() > 1)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Se detectan más de dos dispositivos con el mismo identificador");
    }
    else
    {
        $fila_dispositivo = $res_consulta->dame_siguiente_fila();
        $imei_dispositivo = $fila_dispositivo['imei'];

        // Mensaje de ejemplo
        // 516,867035049382185,quest_v0992.bin,0.992,ftp.drivehq.com
        notifica_servidor_remoto_actualizar_dispositivo($imei_dispositivo, array($nombre_fichero, $id_version, $servidor));
        $res = "OK";
        $msg = "Petición de actualización enviada correctamente";
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
