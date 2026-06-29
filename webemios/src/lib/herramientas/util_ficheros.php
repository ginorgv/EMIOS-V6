<?php
    session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');


    // Función que escribe un fichero de valores CSV con el nombre y los valores especificados
	function escribe_fichero_valores_csv($ruta_fichero_csv, $filas_valores_csv)
	{
        unlink($ruta_fichero_csv);
        $fichero_csv = fopen($ruta_fichero_csv, 'w');
        foreach ($filas_valores_csv as $fila_valores_csv)
        {
            fputcsv($fichero_csv, $fila_valores_csv, ";");
        }
        fclose($fichero_csv);
    }

    // Comprime los ficheros en un 'zip'
    function crea_fichero_zip($rutas_ficheros, $nombres_ficheros, $ruta_fichero_zip)
    {
        $log = dame_log();

        $zip = new ZipArchive();

        //http://us3.php.net/manual/en/ziparchive.open.php#88765
        $res_open = $zip->open($ruta_fichero_zip, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
        if ($res_open !== true)
        {
            $log->error("Error al abrir el fichero ZIP: '".$ruta_fichero_zip."' (res: '".$res_open."')");
            return (false);
        }
        for ($i = 0; $i < count($rutas_ficheros); $i++)
        {
            $res_add_file = $zip->addFile($rutas_ficheros[$i], $nombres_ficheros[$i]);
            if ($res_add_file !== true)
            {
                $log->error("Error al añadir el fichero: '".$rutas_ficheros[$i]."' (res: '".$res_add_file."')");
                return (false);
            }
        }
        $res_close = $zip->close();
        if ($res_close == false)
        {
            $log->info("Error al cerrar el fichero ZIP: '".$ruta_fichero_zip."' (res: '".$res_close."')");
            return (false);
        }

        return (true);
    }
?>
