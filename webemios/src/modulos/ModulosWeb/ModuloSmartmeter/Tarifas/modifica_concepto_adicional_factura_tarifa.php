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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_CONCEPTO_ADICIONAL_FACTURA_TARIFA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $medicion = $_POST["medicion"];
    $id_concepto_adicional = $_POST["id_concepto_adicional"];
    $id_tarifa = $_POST["id_tarifa"];
    $nombre = $_POST["nombre"];
    $tipo = $_POST["tipo"];
    $coste = $_POST["coste"];
    $limites_consumo_tramos = $_POST["limites_consumo_tramos"];
    $impuesto = $_POST["impuesto"];

    // Tabla de conceptos adicionales de facturas de tarifas
    $tabla_conceptos_adicionales = dame_nombre_tabla_conceptos_adicionales_facturas_tarifas($medicion);

	// Se comprueba si existe un concepto adicional con el mismo nombre en la misma tarifa
    $consulta_existe = "
        SELECT nombre
        FROM ".$tabla_conceptos_adicionales."
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (tarifa = '".$bd_red->_($id_tarifa)."')
            AND (id <> '".$bd_red->_($id_concepto_adicional)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un concepto adicional de factura con el mismo nombre");
    }
    else
    {
        // Se recupera la fila anterior (antes de la modificación)
        $fila_concepto_adicional_anterior = dame_fila_concepto_adicional_factura_tarifa($tabla_conceptos_adicionales, $id_concepto_adicional);

        // Se modifica el concepto adicional
        $operacion_modificacion = "
            UPDATE ".$tabla_conceptos_adicionales."
            SET
                nombre = '".$bd_red->_($nombre)."',
                tipo = '".$bd_red->_($tipo)."',
                coste = '".$bd_red->_($coste)."',
                limites_consumo_tramos = '".$bd_red->_($limites_consumo_tramos)."',
                impuesto = '".$bd_red->_($impuesto)."'
            WHERE
                id = '".$bd_red->_($id_concepto_adicional)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            // Se recupera la fila actual
            $fila_concepto_adicional_actual = dame_fila_concepto_adicional_factura_tarifa($tabla_conceptos_adicionales, $id_concepto_adicional);

            // Se añade la acción de usuario
            anyade_accion_usuario_modificar_concepto_adicional_factura_tarifa(
                $medicion,
                $fila_concepto_adicional_actual,
                $fila_concepto_adicional_anterior);

            $res = "OK";
            $msg = $idiomas->_("Concepto adicional de factura modificado correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de modificación del concepto adicional de factura de una tarifa
    function anyade_accion_usuario_modificar_concepto_adicional_factura_tarifa(
        $medicion,
        $fila_actual,
        $fila_anterior)
    {
        // Nombre de tarifa
        $tabla_tarifas = dame_nombre_tabla_tarifas($medicion);
        $nombre_tarifa = dame_nombre_tarifa($tabla_tarifas, $fila_actual["tarifa"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_CONCEPTO_ADICIONAL_FACTURA_TARIFA;
        $objeto_accion_usuario = $nombre_tarifa;

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = $medicion;
        $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_MEDICION] = $medicion;
        if ($fila_actual["nombre"] != $fila_anterior["nombre"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_actual["nombre"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila_anterior["nombre"];
        }
        if ($fila_actual["tipo"] != $fila_anterior["tipo"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA] = $fila_actual["tipo"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA] = $fila_anterior["tipo"];
        }
        if ($fila_actual["coste"] != $fila_anterior["coste"])
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_COSTE_CONCEPTO_ADICIONAL_FACTURA_TARIFA] = $fila_actual["coste"];
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_COSTE_CONCEPTO_ADICIONAL_FACTURA_TARIFA] = $fila_anterior["coste"];
        }
        if ($fila_actual["limites_consumo_tramos"] != $fila_anterior["limites_consumo_tramos"])
        {
            switch ($fila_actual["tipo"])
            {
                case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_ABSOLUTO:
                case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_DIARIO:
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LIMITES_CONSUMO_TRAMOS_CONCEPTO_ADICIONAL_FACTURA_TARIFA] = $fila_actual["limites_consumo_tramos"];
                    break;
                }
            }
            switch ($fila_anterior["tipo"])
            {
                case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_ABSOLUTO:
                case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_DIARIO:
                {
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_LIMITES_CONSUMO_TRAMOS_CONCEPTO_ADICIONAL_FACTURA_TARIFA] = $fila_anterior["limites_consumo_tramos"];
                    break;
                }
            }
        }
        $caracteristicas_tarifas_pais = dame_caracteristicas_tarifas_pais_medicion($medicion);
        if ($caracteristicas_tarifas_pais["impuesto_conceptos_adicionales_factura"] == true)
        {
            if ($fila_actual["impuesto"] != $fila_anterior["impuesto"])
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IMPUESTO_CONCEPTO_ADICIONAL_FACTURA_TARIFA] = $fila_actual["impuesto"];
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_IMPUESTO_CONCEPTO_ADICIONAL_FACTURA_TARIFA] = $fila_anterior["impuesto"];
            }
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        // (siempre se añade el parámetro de medición)
        if (count($parametros_accion_usuario) == 1)
        {
            return;
        }

        // Objeto de la acción (se tiene en cuenta si se ha cambiado el nombre)
        if ($fila_actual["nombre"] == $fila_anterior["nombre"])
        {
            $objeto_accion_usuario = $fila_actual["nombre"]." (".$nombre_tarifa.")";
        }
        else
        {
            $objeto_accion_usuario = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $fila_actual["nombre"]." (".$nombre_tarifa.")",
                $fila_anterior["nombre"]));
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
