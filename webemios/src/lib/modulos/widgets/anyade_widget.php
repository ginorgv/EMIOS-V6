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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_WIDGET, $_POST);

    $idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST["nombre"];
    $id_pestanya = $_POST["id_pestanya"];
    $tipo = $_POST["tipo"];
    $parametros_tipo = $_POST["parametros_tipo"];
    $numero_columnas = $_POST["numero_columnas"];

    // Se comprueba si existe un widget con el mismo nombre en la misma pestaña
    $consulta_existe = "
        SELECT *
        FROM widgets
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (pestanya = '".$bd_red->_($id_pestanya)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception ("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un widget con el mismo nombre");
    }
    else
    {
        // Se añade el widget en la última posición
        $consulta_maxima_posicion = "
            SELECT
                MAX(posicion) AS max_posicion
            FROM widgets
            WHERE
                pestanya = '".$id_pestanya."'";
        $res_maxima_posicion = $bd_red->ejecuta_consulta($consulta_maxima_posicion);
        if ($res_maxima_posicion == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_maxima_posicion."'");
        }
        $fila_maxima_posicion = $res_maxima_posicion->dame_siguiente_fila();

        $maxima_posicion = $fila_maxima_posicion["max_posicion"];
        if ($maxima_posicion === NULL)
        {
            $posicion = 1;
        }
        else
        {
            $posicion = $maxima_posicion + 1;
        }

        $operacion_insercion = "
            INSERT INTO widgets (
                nombre,
                usuario,                
                red,
                pestanya,
                posicion,
                tipo,
                parametros_tipo,
                numero_columnas
            ) VALUES (
                '".$bd_red->_($nombre)."',
                '".$bd_red->_($_SESSION["id_usuario"])."',
                '".$_SESSION["id_red"]."',
                '".$bd_red->_($id_pestanya)."',
                '".$bd_red->_($posicion)."',
                '".$bd_red->_($tipo)."',
                '".$bd_red->_($parametros_tipo)."',
                '".$bd_red->_($numero_columnas)."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Identificador del widget añadido
            $id_widget = $bd_red->dame_id_autoincremental_ultima_insercion();

            $res = "OK";
            $msg = $idiomas->_("Widget añadido correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "id_widget" => $id_widget))
    );
?>
