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
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');


	AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_CONCEPTO_COSTE_PASS_THROUGH_TARIFA_ELECTRICA, $_POST);

    $idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_concepto_coste = $_POST["id_concepto_coste"];

    // Se recupera la fila del concepto de coste
    $fila_concepto_coste = dame_fila_concepto_coste_pass_through_tarifa_electricidad_Espanya($id_concepto_coste);

    // Se borra el concepto adicional
    $operacion_borrado = "
        DELETE
        FROM ".TABLA_CONCEPTOS_COSTE_PASS_THROUGH_TARIFAS_ELECTRICAS_ESPANYA."
        WHERE
            id = '".$bd_red->_($id_concepto_coste)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_concepto_coste_pass_through_tarifa_electricidad_Espanya($fila_concepto_coste);

        $res = "OK";
        $msg = $idiomas->_("Concepto de coste eliminado correctamente");
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


    // Añade la acción de usuario de eliminación del concepto de coste
    function anyade_accion_usuario_eliminar_concepto_coste_pass_through_tarifa_electricidad_Espanya($fila)
    {
        // Nombre de tarifa eléctrica
        $nombre_tarifa_electrica = dame_nombre_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $fila["tarifa_electrica"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_CONCEPTO_COSTE_PASS_THROUGH_TARIFA_ELECTRICIDAD_ESPANYA;
        $objeto_accion_usuario = $fila["nombre"]." (".$nombre_tarifa_electrica.")";

        // Parámetros de la acción
        $parametros_accion_usuario = array();

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
