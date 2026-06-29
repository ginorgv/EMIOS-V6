<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');

	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');


    // Parámetros
    $id = $_POST["id"];
    $titulo = $_POST["titulo"];
    $tipo = $_POST["tipo"];
    $numero_columnas = $_POST["numero_columnas"];
    $generar_valores_xml = $_POST["generar_valores_xml"];
    $cabecera = $_POST["cabecera"];
    $filas = $_POST["filas"];
    $pie = $_POST["pie"];

    // Tabla genérica
    $params_tabla = array(
        "numero_columnas" => $numero_columnas,
        "generar_valores_xml" => $generar_valores_xml
    );
    $tabla_generica = new TablaDatos(
        $id,
        $titulo,
        $tipo,
        $params_tabla
    );
    $tabla_generica->anyade_cabecera("", $cabecera);
    foreach ($filas as $fila)
    {
        $tabla_generica->anyade_fila("fila-tabla-generica", $fila);
    }
    if ($pie !== NULL)
    {
        $tabla_generica->anyade_pie($pie);
    }

	print(json_encode
	(
		array(
            "res" => "OK",
            "tabla" => $tabla_generica->dame_tabla()
		)
	));
?>
