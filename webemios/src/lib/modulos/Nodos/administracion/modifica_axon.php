<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_AXON, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_axon = $_POST["id_axon"];
    $nombre = $_POST["nombre"];
    $id_dispositivo = $_POST["id_dispositivo"];

	// Se comprueba si existe otro axón con el mismo nombre
    $consulta_existe = "
        SELECT nombre
        FROM axones
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (red = '".$_SESSION["id_red"]."')
            AND (id <> '".$bd_red->_($id_axon)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un axón con el mismo nombre");
    }
    else
    {
        // Comprobar si ya hay otro axón en el dispositivo (no se pueden tener más de 1 axón en un dispositivo)
        $consulta_axones = "
            SELECT nombre
            FROM axones
            WHERE
                (dispositivo = '".$bd_red->_($id_dispositivo)."')
                AND (id <> '".$bd_red->_($id_axon)."')";
        $res_axones = $bd_red->ejecuta_consulta($consulta_axones);
        if ($res_axones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_axones."'");
        }
        if ($res_axones->dame_numero_filas() > 0)
        {
            $res = "ERROR";
            $msg = $idiomas->_("Ya existe un axón en el dispositivo");
        }
        else
        {
            // Se modifica el axón
            $operacion_modificacion = "
                UPDATE axones
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    dispositivo = '".$bd_red->_($id_dispositivo)."'
                WHERE
                    id = '".$bd_red->_($id_axon)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                $res = "OK";
                $msg = $idiomas->_("Axón modificado correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }
        }
    }

	print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
