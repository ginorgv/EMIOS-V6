<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_CONCEPTO_COSTE_PASS_THROUGH_TARIFA_ELECTRICA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_tarifa_electrica = $_POST["id_tarifa_electrica"];
    $nombre = $_POST["nombre"];
    $formula_precio_consumo = $_POST["formula_precio_consumo"];

    // Se comprueba si existe un concepto de coste con el mismo nombre en la misma tarifa eléctrica
    $consulta_existe = "
        SELECT nombre
        FROM ".TABLA_CONCEPTOS_COSTE_PASS_THROUGH_TARIFAS_ELECTRICAS_ESPANYA."
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (tarifa_electrica = '".$bd_red->_($id_tarifa_electrica)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un concepto de coste con el mismo nombre");
    }
    else
    {
        $res = anyade_concepto_coste($id_tarifa_electrica, $nombre, $formula_precio_consumo);
        
        if($res == "OK")
        {
            $msg = $idiomas->_("Concepto de coste añadido correctamente");
        }
        else if($res == "ERROR")
        {
                $msg = $idiomas->_("Ha ocurrido un error al evaluar la fórmula de precio de consumo")."\n(".
                    $descripcion_error.")";
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }

        print(json_encode(array(
    "res" => $res,
    "msg" => $msg))
);
    

    //
    // Funciones auxiliares
    //

?>
