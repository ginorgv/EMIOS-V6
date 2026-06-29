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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_LICENCIA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_licencia = $_POST["id_licencia"];
    $modulo = $_POST["modulo"];
    $activada = $_POST["activada"];
    $numero_maximo_elementos = $_POST["numero_maximo_elementos"];

    // Se modifica la licencia
    if ($numero_maximo_elementos < 0)
    {
        $numero_maximo_elementos = 0;
    }
    $operacion_modificacion = "
        UPDATE licencias
        SET
            activada = '".$bd_red->_($activada)."',
            numero_maximo_elementos = '".$bd_red->_($numero_maximo_elementos)."'
        WHERE
            id = '".$bd_red->_($id_licencia)."'";
    $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
    if ($res_modificacion == true)
    {
        $res = "OK";
        $msg = $idiomas->_("Licencia modificada correctamente");
    }
    else
    {
        throw new Exception("Error en la operación: '".$operacion_modificacion."'");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
