<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');


    // Parámetros
    $tipo = $_POST["tipo"];
    $tipo_administracion = $_POST["tipo_administracion"];
	$id_tarifa = $_POST["id_tarifa"];
    $prorrateo = $_POST['prorrateo'];
    
	$log = dame_log();
	$log-> info("ID TARIFA " . $id_tarifa);
    $log -> debug("SMR prorrateo en dame controles tramos tipo tarifa");
    $log -> debug($prorrateo);
	// Comprobamos si el tipo de contrato se mantiene aunque cambie el año.
	if ($id_tarifa and $id_tarifa != ID_NINGUNO)
	{
		$fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa);
		$tipo_tarifa_inicial = $fila_tarifa_electrica["tipo"];
		if (strncmp($tipo_tarifa_inicial,$tipo,8 ) !== 0)
		{
			$id_tarifa = ID_NINGUNO;
		}
	}else {
		$id_tarifa = ID_NINGUNO;
	}


	// Se recuperan los controles
    $controles_precios_consumo = dame_controles_precios_consumo_tramos_tarifa_tipo_electricidad_Espanya($id_tarifa, $tipo, $tipo_administracion);
    $controles_coeficientes_precio_consumo_pass_pool = dame_controles_coeficientes_precio_consumo_pass_pool_tramos_tarifa_tipo_electricidad_Espanya($id_tarifa, $tipo, $tipo_administracion);
    $controles_precios_consumo_tarifa_acceso = dame_controles_precios_consumo_tarifa_acceso_tramos_tarifa_tipo_electricidad_Espanya(ID_NINGUNO, $tipo, $tipo_administracion);
    $controles_precios_potencias = dame_controles_precios_potencias_tramos_tarifa_tipo_electricidad_Espanya(ID_NINGUNO, $tipo, $tipo_administracion);
    //TODO: revisar el parámetro del prorrateo
    $controles_potencias = dame_controles_potencias_tramos_tarifa_tipo_electricidad_Espanya($id_tarifa, $tipo, $tipo_administracion, $prorrateo);

    $html = array(
        "controles_precios_consumo" => $controles_precios_consumo,
        "controles_coeficientes_precio_consumo_pass_pool" => $controles_coeficientes_precio_consumo_pass_pool,
        "controles_precios_consumo_tarifa_acceso" => $controles_precios_consumo_tarifa_acceso,
        "controles_precios_potencias" => $controles_precios_potencias,
        "controles_potencias" => $controles_potencias);

    print(json_encode(array(
        "res" => "OK",
        "html" => $html))   

    );
    $log = dame_log();
    $log -> debug("El resultado de los controles es: ");
    $log -> debug($html);
?>
