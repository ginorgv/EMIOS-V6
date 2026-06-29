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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_LICENCIA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $modulo = $_POST["modulo"];
    $activada = $_POST["activada"];
    $numero_maximo_elementos = $_POST["numero_maximo_elementos"];

    // Se comprueba si existe una licencia del mismo módulo y de la misma red
    $consulta_existe = "
        SELECT id
        FROM licencias
        WHERE
            (modulo = '".$bd_red->_($modulo)."')
            AND (red = '".$_SESSION["id_red"]."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe una licencia del mismo módulo en esta red");
    }
    else
    {
        // Se añade la licencia
        if ($numero_maximo_elementos < 0)
        {
            $numero_maximo_elementos = 0;
        }
        $operacion_insercion = "
            INSERT INTO licencias (
                modulo,
                red,
                activada,
                numero_maximo_elementos
            ) VALUES (
                '".$bd_red->_($modulo)."',
                '".$_SESSION["id_red"]."',
                '".$bd_red->_($activada)."',
                '".$bd_red->_($numero_maximo_elementos)."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Se actualiza el menu con la nueva licencia
            $html_menu_modulos = dame_menu_modulos(MODULO_ADMINISTRACION);

            $res = "OK";
            $msg = $idiomas->_("Licencia añadida correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "html_menu_modulos" => $html_menu_modulos))
    );
?>
