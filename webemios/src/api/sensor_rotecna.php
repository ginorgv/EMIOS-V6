<?php
    include_once('comprueba_tipo_peticion_http_api.php');

	include_once('directorio_raiz_api.php');
    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/api/activa_captura_excepciones_api.php');


    // Actualiza los órdenes de los sensores (virtuales y externos)
    $log = dame_log("api_externo");
    $log->info("Petición API HTTP: datos sensor rotecna");
    $res = "DATOS DE SENSOR RECIBIDO";
    $datos_recibidos = json_decode(file_get_contents('php://input'), true);
    $log -> info($datos_recibidos);

    // Guardamos los datos recibidos en un fichero JSON.
    $fecha = new DateTime('NOW');
    $nombre_fichero_json = "/var/ftp/rotecna/Rotecna_".$fecha->format("Ymd_His").".json";
    if(!$fichero_json = fopen($nombre_fichero_json, 'w')){
        $log -> error("No se puede abrir el fichero json");
    }
    if(fwrite($fichero_json, file_get_contents('php://input')) === FALSE)
    {
        $log -> error("No se puede escribir en el fichero json");
    }
    fclose($fichero_json);

    // Leemos los datos
    $serial = $datos_recibidos["sn"];
    $timestamp = $datos_recibidos["timestamp"];
    $fecha = date('d-m-Y, H:i:s', $timestamp);
    $dist = $datos_recibidos["dist"];
    $qual = $datos_recibidos["qual"];
    $vbatt = $datos_recibidos["vbatt"];
    $temp = $datos_recibidos["temp"];
    $datos = "Fecha;dist;qual;vbatt;temp\n".
        $fecha .";".$dist.";".$qual.";".$vbatt.";".$temp;

    // Guardamos los datos recibidos en un fichero csv.
    $nombre_fichero_csv = "/var/ftp/uploads/Rotecna_".$serial."_".$timestamp.".csv";
    $log -> info("Guardamos el fichero " .$nombre_fichero_csv);
    if(!$fichero_csv = fopen($nombre_fichero_csv, 'w')){
        $log -> error("No se puede abrir el fichero csv");
    }
    if(fwrite($fichero_csv, $datos) === FALSE)
    {
        $log -> error("No se puede escribir en el fichero csv");
    }
    $log -> info("Datos guardados");
    fclose($fichero_csv);

    print(json_encode($res));
?>
