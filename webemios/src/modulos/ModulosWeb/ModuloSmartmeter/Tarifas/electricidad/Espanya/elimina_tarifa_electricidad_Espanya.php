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
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_TARIFA_ELECTRICA, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_tarifa_electrica = $_POST['id_tarifa_electrica'];

    // Comprobaciones antes de eliminar la tarifa eléctrica:
    // - Se comprueba si se está utilizando la tarifa eléctrica en algún sensor
    $eliminar_tarifa = true;

    // Se comprueba si se está utilizando la tarifa eléctrica en algún sensor
    if ($eliminar_tarifa == true)
    {
        $consulta_sensores = "
            SELECT nombre
            FROM sensores
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (clase = '".CLASE_SENSOR_ENERGIA_ACTIVA."')
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_TARIFA_ELECTRICA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1)  = '".$bd_red->_($id_tarifa_electrica)."')
            ORDER BY nombre ASC";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }
        if ($res_sensores->dame_numero_filas() > 0)
        {
            $eliminar_tarifa = false;

            $fila_sensor = $res_sensores->dame_siguiente_fila();
            $nombre_sensor = $fila_sensor["nombre"];

            $res = "ERROR";
            $msg = $idiomas->_("No se puede eliminar la tarifa porque está asignada a algún sensor")."\n(".
                $nombre_sensor.")";
        }
    }

    // Se elimina la tarifa eléctrica
    if ($eliminar_tarifa == true)
    {
        // Se recupera la fila de la tarifa eléctrica
        $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa_electrica);

        // Se elimina la tarifa eléctrica
        $operacion_borrado = "
            DELETE
            FROM ".TABLA_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                id = '".$bd_red->_($id_tarifa_electrica)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            // Se elimina la información de los tramos de la tarifa eléctrica
            $operacion_borrado_tramos_tarifa_electrica = "
                DELETE
                FROM ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_ESPANYA."
                WHERE
                    tarifa_electrica = '".$bd_red->_($id_tarifa_electrica)."'";
            $res_borrado_tramos_tarifa_electrica = $bd_red->ejecuta_operacion($operacion_borrado_tramos_tarifa_electrica);
            if ($res_borrado_tramos_tarifa_electrica == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_tramos_tarifa_electrica."'");
            }

            // Se eliminan los periodos de cálculo de costes de pass-pool de la tarifa eléctrica
            $operacion_borrado_periodos_calculo_costes_pass_pool_tarifa_electrica = "
                DELETE
                FROM ".TABLA_PERIODOS_CALCULO_COSTES_PASS_POOL_TARIFAS_ELECTRICAS_ESPANYA."
                WHERE
                    tarifa_electrica = '".$bd_red->_($id_tarifa_electrica)."'";
            $res_borrado_periodos_calculo_costes_pass_pool_tarifa_electrica = $bd_red->ejecuta_operacion($operacion_borrado_periodos_calculo_costes_pass_pool_tarifa_electrica);
            if ($res_borrado_periodos_calculo_costes_pass_pool_tarifa_electrica == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_periodos_calculo_costes_pass_pool_tarifa_electrica."'");
            }

            // Se eliminan los conceptos de coste de de pass-through de la tarifa eléctrica
            $operacion_borrado_conceptos_coste_pass_through_tarifa_electrica = "
                DELETE
                FROM ".TABLA_CONCEPTOS_COSTE_PASS_THROUGH_TARIFAS_ELECTRICAS_ESPANYA."
                WHERE
                    tarifa_electrica = '".$bd_red->_($id_tarifa_electrica)."'";
            $res_borrado_conceptos_coste_pass_through_tarifa_electrica = $bd_red->ejecuta_operacion($operacion_borrado_conceptos_coste_pass_through_tarifa_electrica);
            if ($res_borrado_conceptos_coste_pass_through_tarifa_electrica == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_conceptos_coste_pass_through_tarifa_electrica."'");
            }

            // Se eliminan los conceptos adicionales de factura de la tarifa eléctrica
            $operacion_borrado_conceptos_adicionales_tarifa_electrica = "
                DELETE
                FROM ".TABLA_CONCEPTOS_ADICIONALES_FACTURA_TARIFAS_ELECTRICAS_ESPANYA."
                WHERE
                    tarifa = '".$bd_red->_($id_tarifa_electrica)."'";
            $res_borrado_conceptos_adicionales_tarifa_electrica = $bd_red->ejecuta_operacion($operacion_borrado_conceptos_adicionales_tarifa_electrica);
            if ($res_borrado_conceptos_adicionales_tarifa_electrica == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_conceptos_adicionales_tarifa_electrica."'");
            }

            // Acciones a realizar al eliminar la tarifa
            realiza_acciones_tarifa_eliminada(MEDICION_ELECTRICIDAD, $id_tarifa_electrica);

            // Se añade la acción de usuario
            anyade_accion_usuario_eliminar_tarifa_electricidad_Espanya($fila_tarifa_electrica);

            $res = "OK";
            $msg = $idiomas->_("Tarifa eliminada correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_borrado."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de eliminación de la tarifa eléctrica
    function anyade_accion_usuario_eliminar_tarifa_electricidad_Espanya($fila)
    {
        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_TARIFA;
        $objeto_accion_usuario = $fila["nombre"];

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = MEDICION_ELECTRICIDAD;

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
