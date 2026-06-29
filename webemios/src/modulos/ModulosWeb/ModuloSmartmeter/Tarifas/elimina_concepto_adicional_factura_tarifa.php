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
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


	AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_CONCEPTO_ADICIONAL_FACTURA_TARIFA, $_POST);

    $idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $medicion = $_POST["medicion"];
    $id_concepto_adicional = $_POST["id_concepto_adicional"];

    // Tabla de conceptos adicionales de facturas de tarifas
    $tabla_conceptos_adicionales = dame_nombre_tabla_conceptos_adicionales_facturas_tarifas($medicion);

    // Se recupera la fila del concepto adicional
    $fila_concepto_adicional = dame_fila_concepto_adicional_factura_tarifa($tabla_conceptos_adicionales, $id_concepto_adicional);

    // Se borra el concepto adicional
    $operacion_borrado = "
        DELETE
        FROM ".$tabla_conceptos_adicionales."
        WHERE
            id = '".$bd_red->_($id_concepto_adicional)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        // Se añade la acción de usuario
        anyade_accion_usuario_eliminar_concepto_adicional_factura_tarifa($medicion, $fila_concepto_adicional);

        $res = "OK";
        $msg = $idiomas->_("Concepto adicional de factura eliminado correctamente");
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
    function anyade_accion_usuario_eliminar_concepto_adicional_factura_tarifa($medicion, $fila)
    {
        // Nombre de tarifa
        $tabla_tarifas = dame_nombre_tabla_tarifas($medicion);
        $nombre_tarifa = dame_nombre_tarifa($tabla_tarifas, $fila["tarifa"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_CONCEPTO_ADICIONAL_FACTURA_TARIFA;
        $objeto_accion_usuario = $fila["nombre"]." (".$nombre_tarifa.")";

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = $medicion;

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
