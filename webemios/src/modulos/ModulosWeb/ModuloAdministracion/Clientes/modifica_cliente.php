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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_CLIENTE, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_cliente = $_POST["id_cliente"];
    $nombre = $_POST["nombre"];

    // Se comprueba si existe otro cliente con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM clientes
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (id <> '".$bd_red->_($id_cliente)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un cliente con el mismo nombre");
    }
    else
    {
        $operacion_modificacion = "
            UPDATE clientes
            SET
                nombre = '".$bd_red->_($nombre)."'
            WHERE
                id = '".$bd_red->_($id_cliente)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            $res = "OK";
            $msg = $idiomas->_("Cliente modificado correctamente");
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
