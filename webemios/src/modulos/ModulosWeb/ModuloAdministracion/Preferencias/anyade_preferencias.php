<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_PREFERENCIAS, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $url = $_POST["url"];
    $logo_personalizado = $_POST["logo_personalizado"];
    $nombre_logo = $_POST["nombre_logo"];
    $url_logo = $_POST["url_logo"];
    $titulo_web = $_POST["titulo_web"];
    $tema = $_POST["tema"];
    $paleta_colores_graficas = $_POST["paleta_colores_graficas"];

    // Se comprueba si existen unas preferencias con la misma URL
    $consulta_existe = "
        SELECT
            url
        FROM preferencias
        WHERE
            url = '".$bd_red->_($url)."'";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existen unas preferencias con la misma URL");
    }
    else
    {
        // Se añaden las preferencias
        $operacion_insercion = "
            INSERT INTO preferencias (
                url,
                logo_personalizado,
                nombre_logo,
                url_logo,
                titulo_web,
                tema,
                paleta_colores_graficas
            ) VALUES (
                '".$bd_red->_($url)."',
                '".$bd_red->_($logo_personalizado)."',
                '".$bd_red->_($nombre_logo)."',
                '".$bd_red->_($url_logo)."',
                '".$bd_red->_($titulo_web)."',
                '".$bd_red->_($tema)."',
                '".$bd_red->_($paleta_colores_graficas)."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Se recupera el id de las preferencias añadidas
            $id_preferencias = $bd_red->dame_id_autoincremental_ultima_insercion();

            $res = "OK";
            $msg = $idiomas->_("Preferencias añadidas correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "id_preferencias" => $id_preferencias))
    );
?>
