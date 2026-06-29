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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_CLIENTE, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_cliente = $_POST["id_cliente"];

    // Se comprueba si existe alguna red de este cliente
    $consulta_redes = "
        SELECT nombre
        FROM redes
        WHERE
            cliente = '".$bd_red->_($id_cliente)."'
        ORDER BY nombre ASC";
    $res_redes = $bd_red->ejecuta_consulta($consulta_redes);
    if ($res_redes == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_redes."'");
    }
    if ($res_redes->dame_numero_filas() > 0)
    {
        $fila_red = $res_redes->dame_siguiente_fila();
        $nombre_red = $fila_red["nombre"];

        $res = "ERROR";
        $msg = $idiomas->_("No se puede eliminar el cliente porque tiene redes asignadas")."\n(".
            $nombre_red.")";
    }
    else
    {
        $operacion_borrado = "
            DELETE
            FROM clientes
            WHERE
                id = '".$bd_red->_($id_cliente)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            $res = "OK";
            $msg = $idiomas->_("Cliente eliminado correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_borrado."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
