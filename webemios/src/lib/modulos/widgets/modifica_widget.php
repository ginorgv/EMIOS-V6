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
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/widgets/util_widgets.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_WIDGET, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_widget = $_POST['id_widget'];
    $nombre = $_POST['nombre'];
    $tipo = $_POST["tipo"];
    $parametros_tipo = $_POST['parametros_tipo'];
    $id_pestanya = $_POST['id_pestanya'];
    $numero_columnas = $_POST['numero_columnas'];
    $id_pestanya_anterior = $_POST['id_pestanya_anterior'];

    // Se comprueba si existe otro widget con el mismo nombre en la misma pestaña
    $consulta_existe = "
        SELECT *
        FROM widgets
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (pestanya = '".$bd_red->_($id_pestanya)."')
            AND (id <> '".$bd_red->_($id_widget)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un widget con el mismo nombre");
    }
    else
    {
        // Si la pestaña cambia se actualiza la posición del widget a la última de la nueva pestaña
        if ($id_pestanya != $id_pestanya_anterior)
        {
            // Se añade el widget en la última posición
            $consulta_posicion_maxima = "
                SELECT
                    MAX(posicion) AS max_posicion
                FROM widgets
                WHERE
                    pestanya = '".$bd_red->_($id_pestanya)."'";
            $res_posicion_maxima = $bd_red->ejecuta_consulta($consulta_posicion_maxima);
            if ($res_posicion_maxima == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_posicion_maxima."'");
            }
            $fila_posicion_maxima = $res_posicion_maxima->dame_siguiente_fila();

            $posicion_maxima = $fila_posicion_maxima["max_posicion"];
            if ($posicion_maxima === NULL)
            {
                $nueva_posicion_widget = 1;
            }
            else
            {
                $nueva_posicion_widget = $posicion_maxima + 1;
            }
        }

        // Modificación
        $operacion_modificacion = "
            UPDATE widgets
            SET
                nombre = '".$bd_red->_($nombre)."',
                tipo = '".$bd_red->_($tipo)."',
                parametros_tipo = '".$bd_red->_($parametros_tipo)."',
                pestanya = '".$bd_red->_($id_pestanya)."',
                numero_columnas = '".$bd_red->_($numero_columnas)."'";
        if ($nueva_posicion_widget != $antigua_posicion_widget)
        {
            $operacion_modificacion .= ",
                posicion = '".$nueva_posicion_widget."'";
        }
        $operacion_modificacion .= "
            WHERE
                id = '".$id_widget."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            $res = "OK";
            $msg = $idiomas->_("Widget modificado correctamente");
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
