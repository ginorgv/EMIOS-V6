<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/util_tarifas_agua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');
		include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/util_tarifas_electricidad_Portugal.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/util_tarifas_gas_Espanya.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_FILA_TABLA_TARIFA, $_POST);

    $parametros = $_POST;
    $medicion = $parametros["medicion"];

    switch ($medicion)
    {
        case MEDICION_ELECTRICIDAD:
        {
            $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
            switch ($pais_tarifas_electricas)
            {
                case PAIS_ESPANYA:
                {
                    $resultado = dame_fila_tabla_tarifa_electricidad_Espanya($parametros);
                    break;
                }case PAIS_PORTUGAL:
                {
                    $resultado = dame_fila_tabla_tarifa_electricidad_Portugal($parametros);
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                }
            }
            break;
        }
        case MEDICION_GAS:
        {
            $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
            switch ($pais_tarifas_gas)
            {
                case PAIS_ESPANYA:
                {
                    $resultado = dame_fila_tabla_tarifa_gas_Espanya($parametros);
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                }
            }
            break;
        }
        case MEDICION_AGUA:
        {
            $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
            switch ($pais_tarifas_agua)
            {
                case PAIS_ESPANYA:
                {
                    $resultado = dame_fila_tabla_tarifa_agua_Espanya($parametros);
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                }
            }
            break;
        }
        default:
        {
            throw new Exception("Medición desconocida: '".$medicion."'");
        }
    }

    print(json_encode($resultado));
?>
