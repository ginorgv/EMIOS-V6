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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_CONCEPTO_COSTE_PASS_THROUGH_TARIFA_ELECTRICA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_concepto_coste = $_POST["id_concepto_coste"];
    $id_tarifa_electrica = $_POST["id_tarifa_electrica"];
    $nombre = $_POST["nombre"];
    $formula_precio_consumo = $_POST["formula_precio_consumo"];

    // Se comprueba si existe un concepto de coste con el mismo nombre en la misma tarifa eléctrica
    $consulta_existe = "
        SELECT nombre
        FROM ".TABLA_CONCEPTOS_COSTE_PASS_THROUGH_TARIFAS_ELECTRICAS_ESPANYA."
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (tarifa_electrica = '".$bd_red->_($id_tarifa_electrica)."')
            AND (id <> '".$bd_red->_($id_concepto_coste)."')";
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
        // Comprobaciones antes de modificar el concepto de coste:
        // - Se valida la fórmula de cálculo de precio de consumo
        $modificar_concepto_coste = true;

        // Si los datos son correctos se evalua la función de cálculo de precio de consumo
        if ($modificar_concepto_coste == true)
        {
            // Parámetros de la función a llamar
            $parametros_funcion_externa =
                array(
                    "llamante" => "web_emios",
                    "nombre" => NOMBRE_FUNCION_EVALUA_FORMULA_PRECIO_CONSUMO_PASS_THROUGH_ESPANYA,
                    "formula_precio_consumo_pass_through" => $formula_precio_consumo
                );

            // Llamada a función 'externa'
            $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
            $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

            // Si la fórmula de valores es incorrecta se devuelve un error
            if ($resultado_funcion_externa["formula_correcta"] == 0)
            {
                $modificar_concepto_coste = False;

                $error = $resultado_funcion_externa["error"];
                $descripcion_error = dame_descripcion_error_funcion_variables($error);

                $res = "ERROR";
                $msg = $idiomas->_("Ha ocurrido un error al evaluar la fórmula de precio de consumo")."\n(".
                    $descripcion_error.")";
            }
        }

        // Se modifica el concepto de coste de la tarifa eléctrica
        if ($modificar_concepto_coste == true)
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_concepto_coste_anterior = dame_fila_concepto_coste_pass_through_tarifa_electricidad_Espanya($id_concepto_coste);

            // Se modifica el concepto de coste
            $operacion_modificacion = "
                UPDATE ".TABLA_CONCEPTOS_COSTE_PASS_THROUGH_TARIFAS_ELECTRICAS_ESPANYA."
                SET
                    nombre = '".$bd_red->_($nombre)."',
                    formula_precio_consumo = '".$bd_red->_($formula_precio_consumo)."'
                WHERE
                    id = '".$bd_red->_($id_concepto_coste)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
                // Se recupera la fila actual
                $fila_concepto_coste_actual = dame_fila_concepto_coste_pass_through_tarifa_electricidad_Espanya($id_concepto_coste);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_concepto_coste_pass_through_tarifa_electricidad_Espanya(
                    $fila_concepto_coste_actual,
                    $fila_concepto_coste_anterior);

                $res = "OK";
                $msg = $idiomas->_("Concepto de coste modificado correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación del concepto de coste de una tarifa eléctrica
    function anyade_accion_usuario_modificar_concepto_coste_pass_through_tarifa_electricidad_Espanya(
        $fila_actual,
        $fila_anterior)
    {
        // Nombre de tarifa eléctrica
        $nombre_tarifa_electrica = dame_nombre_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $fila_actual["tarifa_electrica"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_CONCEPTO_COSTE_PASS_THROUGH_TARIFA_ELECTRICIDAD_ESPANYA;
        $objeto_accion_usuario = $nombre_tarifa_electrica;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["formula_precio_consumo"] != $fila_anterior["formula_precio_consumo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FORMULA_PRECIO_CONSUMO_PASS_THROUGH] = $fila_actual["formula_precio_consumo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_FORMULA_PRECIO_CONSUMO_PASS_THROUGH] = $fila_anterior["formula_precio_consumo"];
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            $parametros_accion_usuario_anteriores,
            NULL);
    }
?>
