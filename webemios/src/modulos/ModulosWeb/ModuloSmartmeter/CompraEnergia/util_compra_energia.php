<?php
	session_start();

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/Espanya/util_compra_energia_Espanya.php');


    //
    // Funciones de compra de energía
    //


    // Importa los valores diarios de compra de energía de un sensor
    function importa_valores_diarios_compra_energia_sensor($parametros, $ficheros)
    {
        // Selección de país
        $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
        switch ($pais_tarifas_electricas)
        {
            case PAIS_ESPANYA:
            {
                $resultado = importa_valores_diarios_compra_energia_sensor_Espanya($parametros, $ficheros);
                break;
            }
            default:
            {
                throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
            }
        }
        return ($resultado);
    }


    //
    // Funciones de listas
    //


    // Devuelve la lista de sensores hijos del sensor de compra de energía
    function dame_consulta_sensores_hijos_sensor_compra_energia($id_sensor)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Identificadores de sensores hijos
        if ($id_sensor == ID_NINGUNO)
        {
            $ids_sensores_hijos = array();
        }
        else
        {
            $fila_sensor = dame_fila_sensor($id_sensor);
            $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor["parametros_clase"]);
            $cadena_ids_sensores_hijos = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_COMPRA_ENERGIA_IDS_SENSORES_HIJOS];
            $ids_sensores_hijos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_sensores_hijos);
        }

        // Se recuperan los identificadores y nombres de los sensores hijos ordenados por nombre
        $cadena_consulta_ids_sensores_hijos = dame_cadena_ids_consulta($ids_sensores_hijos);
        $consulta_sensores_hijos = "
            SELECT
                id,
                nombre
            FROM sensores
            WHERE
                (id IN (".$bd_red->_($cadena_consulta_ids_sensores_hijos)."))
            ORDER BY nombre ASC";
        return ($consulta_sensores_hijos);
    }
?>
