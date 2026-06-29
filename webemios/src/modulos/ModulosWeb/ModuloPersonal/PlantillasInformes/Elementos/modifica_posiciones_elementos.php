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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_POSICIONES_ELEMENTOS_PLANTILLA_INFORME, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_plantilla_informe = $_POST["id"];
    $ids_elementos = $_POST["ids_elementos"];

    // Actualización de las posiciones de los elementos
    if ($_POST["ids_elementos"] != "")
    {
        $posicion_elemento = 1;
        foreach ($ids_elementos as $id_elemento)
        {
            $operacion_modificacion = "
                UPDATE elementos_plantillas_informes
                SET
                    posicion = '".$bd_red->_($posicion_elemento)."'
                WHERE
                    id = '".$bd_red->_($id_elemento)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }

            $posicion_elemento++;
        }
    }

    $res = "OK";
    $msg = $idiomas->_("Posiciones de los elementos modificadas correctamente");

    // Se actualiza el usuario de la plantilla de informe (si es necesario)
    actualiza_usuario_plantilla_informe($id_plantilla_informe);

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );
?>
