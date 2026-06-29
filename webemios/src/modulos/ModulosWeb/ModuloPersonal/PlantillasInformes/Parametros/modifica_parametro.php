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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/util_administracion_parametros.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_PARAMETRO_PLANTILLA_INFORME, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_parametro = $_POST['id_parametro'];
    $nombre = $_POST['nombre'];
    $id_plantilla_informe = $_POST['id_plantilla_informe'];
    $tipo = $_POST['tipo'];
    $parametros_tipo = $_POST['parametros_tipo'];

    // Parámetros auxiliares
    $tipo_anterior = $_POST['tipo_anterior'];
    $parametros_tipo_anteriores = $_POST['parametros_tipo_anteriores'];

    // Se comprueba si existe otro parámetro con el mismo nombre en la misma plantilla de informe
    $consulta_existe = "
        SELECT *
        FROM parametros_plantillas_informes
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')
            AND (id <> '".$bd_red->_($id_parametro)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un parámetro con el mismo nombre");
    }
    else
    {
        $operacion_modificacion = "
            UPDATE parametros_plantillas_informes
            SET
                nombre = '".$bd_red->_($nombre)."',
                plantilla_informe = '".$bd_red->_($id_plantilla_informe)."',
                tipo = '".$bd_red->_($tipo)."',
                parametros_tipo = '".$bd_red->_($parametros_tipo)."'
            WHERE
                id = '".$bd_red->_($id_parametro)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            $res = "OK";
            $msg = $idiomas->_("Parámetro modificado correctamente");

            // Se actualiza el usuario de la plantilla de informe (si es necesario)
            actualiza_usuario_plantilla_informe($id_plantilla_informe);
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
