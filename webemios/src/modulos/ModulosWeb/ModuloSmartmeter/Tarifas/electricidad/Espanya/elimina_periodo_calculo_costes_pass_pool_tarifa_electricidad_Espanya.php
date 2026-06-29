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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_PERIODO_CALCULO_COSTES_PASS_POOL_TARIFA_ELECTRICA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_periodo_calculo_costes = $_POST['id_periodo_calculo_costes'];

    // Se recupera la fila del periodo de cálculo de costes
    $fila_periodo_calculo_costes = dame_fila_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya($id_periodo_calculo_costes);

    // Se elimina el periodo de cálculo de costes
	$operacion_borrado = "
        DELETE
        FROM ".TABLA_PERIODOS_CALCULO_COSTES_PASS_POOL_TARIFAS_ELECTRICAS_ESPANYA."
        WHERE
            id = '".$bd_red->_($id_periodo_calculo_costes)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya($fila_periodo_calculo_costes);

        $res = "OK";
        $msg = $idiomas->_("Periodo de cálculo de costes eliminado correctamente");
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


    // Añade la acción de usuario de eliminación del periodo
    function anyade_accion_usuario_eliminar_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya($fila)
    {
        // Nombre de la tarifa eléctrica
        $nombre_tarifa =  dame_nombre_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $fila["tarifa_electrica"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_PERIODO_CALCULO_COSTES_PASS_POOL_TARIFA_ELECTRICIDAD_ESPANYA;
        $objeto_accion_usuario = $nombre_tarifa;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_INICIO] = $fila["fecha_inicio"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FECHA_FIN] = $fila["fecha_fin"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
