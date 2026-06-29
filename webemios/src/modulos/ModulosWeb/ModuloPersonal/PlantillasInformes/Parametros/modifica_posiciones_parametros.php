<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_POSICIONES_PARAMETROS_PLANTILLA_INFORME, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_plantilla_informe = $_POST["id"];
    $ids_parametros = $_POST["ids_parametros"];

    // Actualización de las posiciones de los parámetros
    if ($_POST["ids_parametros"] != "")
    {
        $posicion_parametro = 1;
        foreach ($ids_parametros as $id_parametro)
        {
            $operacion_modificacion = "
                UPDATE parametros_plantillas_informes
                SET
                    posicion = '".$bd_red->_($posicion_parametro)."'
                WHERE
                    id = '".$bd_red->_($id_parametro)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }

            $posicion_parametro++;
        }
    }

    $res = "OK";
    $msg = $idiomas->_("Posiciones de los parámetros modificadas correctamente");

    // Se actualiza el usuario de la plantilla de informe (si es necesario)
    actualiza_usuario_plantilla_informe($id_plantilla_informe);

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
