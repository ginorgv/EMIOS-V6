<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_INFORME_AUTOMATICO, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_informe_automatico = $_POST['id_informe_automatico'];

    // Se borra el informe automático
    $operacion_borrado = "
        DELETE
        FROM informes_automaticos
        WHERE
            id = '".$bd_red->_($id_informe_automatico)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Acciones a realizar al eliminar un informe automático
        realiza_acciones_informe_automatico_eliminado($id_informe_automatico);

        $res = "OK";
        $msg = $idiomas->_("Informe automático eliminado correctamente");
    }
    else
    {
        throw new Exception("Error en la operación: '".$operacion_borrado."'");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Realiza acciones al eliminar un informe automático
    function realiza_acciones_informe_automatico_eliminado($id_informe_automatico)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se eliminan las imágenes personalizadas del informe automático
        $operacion_borrado_imagenes = "
            DELETE
            FROM imagenes
            WHERE
                (origen = '".ORIGEN_IMAGEN_INFORME_AUTOMATICO_PLANTILLA_INFORME_IMAGEN."')
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(id_origen, '".SEPARADOR_PARAMETROS_SIMPLES."', ".(0 + 1)."), '".SEPARADOR_PARAMETROS_SIMPLES."', -1) = '".$bd_red->_($id_informe_automatico)."')";
        $res_borrado_imagenes = $bd_red->ejecuta_operacion($operacion_borrado_imagenes);
        if ($res_borrado_imagenes == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_imagenes."'");
        }
    }
?>
