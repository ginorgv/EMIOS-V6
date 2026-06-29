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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_PREFERENCIAS, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_preferencias = $_POST["id_preferencias"];
    $url = $_POST["url"];
    $logo_personalizado = $_POST["logo_personalizado"];
    $nombre_logo = $_POST["nombre_logo"];
    $url_logo = $_POST["url_logo"];
    $titulo_web = $_POST["titulo_web"];
    $tema = $_POST["tema"];
    $paleta_colores_graficas = $_POST["paleta_colores_graficas"];

    // Parámetros auxiliares
    $logo_personalizado_anterior = $_POST['logo_personalizado_anterior'];

    // Se comprueba si existen otras preferencias con la misma URL
    $consulta_existe = "
        SELECT url
        FROM preferencias
        WHERE
            (url = '".$bd_red->_($url)."')
            AND (id <> '".$bd_red->_($id_preferencias)."')";
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
        // Si antes había logo personalizado y ahora no, se eliminan los logos anteriores
        if (($logo_personalizado_anterior == VALOR_SI) && ($logo_personalizado == VALOR_NO))
        {
            elimina_imagen_base_datos(ORIGEN_IMAGEN_PREFERENCIAS_LOGO, $id_preferencias);
            elimina_imagen_base_datos(ORIGEN_IMAGEN_PREFERENCIAS_LOGO_PDF, $id_preferencias);
        }

        // Se modifican las preferencias
        $operacion_modificacion = "
            UPDATE preferencias
            SET
                url = '".$bd_red->_($url)."',
                logo_personalizado = '".$bd_red->_($logo_personalizado)."',
                nombre_logo = '".$bd_red->_($nombre_logo)."',
                url_logo = '".$bd_red->_($url_logo)."',
                titulo_web = '".$bd_red->_($titulo_web)."',
                tema = '".$bd_red->_($tema)."',
                paleta_colores_graficas = '".$bd_red->_($paleta_colores_graficas)."'
            WHERE
                id = '".$bd_red->_($id_preferencias)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            $res = "OK";
            $msg = $idiomas->_("Preferencias modificadas correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
