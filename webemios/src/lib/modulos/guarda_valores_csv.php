<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_ficheros.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_GUARDAR_VALORES_CSV, $_POST);

    $idiomas = new Idiomas();

	// Parámetros
    $nombre_fichero_zip = rawurldecode($_POST['nombre_fichero_zip']);
    $nombres_ficheros_csv = $_POST['nombres_ficheros_csv'];
    $filas_valores_ficheros_csv = json_decode($_POST["filas_valores_ficheros_csv"]);

    // Directorios de usuario
    $directorio_usuario_servidor = $_SESSION["directorio"].'/rsc/ficheros/tmp/'.$_SESSION["id_usuario"];
    $directorio_usuario_cliente = './rsc/ficheros/tmp/'.$_SESSION["id_usuario"];

    // Ruta de fichero resultado
    $ruta_fichero_resultado = NULL;

    // Rutas de ficheros CSV
    $rutas_servidor_ficheros_csv = array();
    $rutas_cliente_ficheros_csv = array();

    // Se crean los ficheros de valores CSV
    $numero_ficheros_csv = count($nombres_ficheros_csv);
    for ($i = 0; $i < $numero_ficheros_csv; $i++)
    {
        $nombre_fichero_csv = $nombres_ficheros_csv[$i];
        $filas_valores_fichero_csv = $filas_valores_ficheros_csv[$i];

        $elementos_nombre_fichero_csv = explode(SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES, $nombre_fichero_csv);
        $nombre_fichero_csv = $elementos_nombre_fichero_csv[0];
        $nombre_fichero_csv = rawurldecode($nombre_fichero_csv);
        $nombre_fichero_csv = convierte_ascii_estandar($nombre_fichero_csv);
        $nombre_fichero_csv = reemplaza_caracteres_no_alfanumericos($nombre_fichero_csv, "_");
        $nombre_fichero_csv .= ".csv";
        $nombres_ficheros_csv[$i] = $nombre_fichero_csv;

        $ruta_servidor_fichero_csv = $directorio_usuario_servidor.'/'.$nombre_fichero_csv;
        $ruta_cliente_fichero_csv = $directorio_usuario_cliente.'/'.$nombre_fichero_csv;

        escribe_fichero_valores_csv($ruta_servidor_fichero_csv, $filas_valores_fichero_csv);

        array_push($rutas_servidor_ficheros_csv, $ruta_servidor_fichero_csv);
        array_push($rutas_cliente_ficheros_csv, $ruta_cliente_fichero_csv);
    }

    // Si hay nombre de fichero comprimido se comprimen los ficheros
    if ($nombre_fichero_zip == NULL)
    {
        $ruta_fichero_resultado = $rutas_cliente_ficheros_csv[0];
    }
    else
    {
        $nombre_fichero_zip = convierte_ascii_estandar($nombre_fichero_zip);
        $nombre_fichero_zip = reemplaza_caracteres_no_alfanumericos($nombre_fichero_zip, "_");
        $nombre_fichero_zip .= ".zip";
        $ruta_servidor_fichero_zip = $directorio_usuario_servidor.'/'.$nombre_fichero_zip;
        $ruta_cliente_fichero_zip = $directorio_usuario_cliente.'/'.$nombre_fichero_zip;

        if (crea_fichero_zip($rutas_servidor_ficheros_csv, $nombres_ficheros_csv, $ruta_servidor_fichero_zip) == false)
        {
            throw new Exception("Error al crear el fichero ZIP: '".$ruta_servidor_fichero_zip."'");
        }
        $ruta_fichero_resultado = $ruta_cliente_fichero_zip;
    }

    // Mensaje
    $msg = $idiomas->_("Valores exportados correctamente");

    print(json_encode(array(
        "res" => "OK",
        "msg" => $msg,
        "ruta_fichero_valores" => $ruta_fichero_resultado))
    );
?>
