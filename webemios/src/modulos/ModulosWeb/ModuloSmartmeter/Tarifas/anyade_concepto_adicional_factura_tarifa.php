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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/util_modulo_smartmeter.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_CONCEPTO_ADICIONAL_FACTURA_TARIFA, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $medicion = $_POST["medicion"];
    $id_tarifa = $_POST["id_tarifa"];
    $nombre = $_POST["nombre"];
    $tipo = $_POST["tipo"];
    $coste = $_POST["coste"];
    $limites_consumo_tramos = $_POST["limites_consumo_tramos"];
    $impuesto = $_POST["impuesto"];

    // Tabla de conceptos adicionales de facturas de tarifas
    $tabla_conceptos_adicionales = dame_nombre_tabla_conceptos_adicionales_facturas_tarifas($medicion);

	// Se comprueba si existe un concepto con el mismo nombre en la misma tarifa
    $consulta_existe = "
        SELECT nombre
        FROM ".$tabla_conceptos_adicionales."
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (tarifa = '".$bd_red->_($id_tarifa)."')";
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
        // Se añade el concepto adicional de factura de la tarifa
        $operacion_insercion = "
			INSERT INTO ".$tabla_conceptos_adicionales." (
                nombre,
                red,
                tarifa,
                tipo,
                coste,
                limites_consumo_tramos,
                impuesto
			) VALUES (
                '".$bd_red->_($nombre)."',
                '".$_SESSION["id_red"]."',
				'".$bd_red->_($id_tarifa)."',
                '".$bd_red->_($tipo)."',
                '".$bd_red->_($coste)."',
                '".$bd_red->_($limites_consumo_tramos)."',
                '".$bd_red->_($impuesto)."'
			)";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Se recuperan el id y la fila del concepto adicional añadido
            $id_concepto_adicional = $bd_red->dame_id_autoincremental_ultima_insercion();
            $fila_concepto_adicional = dame_fila_concepto_adicional_factura_tarifa($tabla_conceptos_adicionales, $id_concepto_adicional);

            // Se añade la acción de usuario
            anyade_accion_usuario_anyadir_concepto_adicional_factura_tarifa($medicion, $fila_concepto_adicional);

            $res = "OK";
            $msg = $idiomas->_("Concepto adicional de factura añadido correctamente");
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


    // Añade la acción de usuario de adición del concepto adicional de factura de una tarifa
    function anyade_accion_usuario_anyadir_concepto_adicional_factura_tarifa($medicion, $fila)
    {
        // Nombre de tarifa
        $tabla_tarifas = dame_nombre_tabla_tarifas($medicion);
        $nombre_tarifa = dame_nombre_tarifa($tabla_tarifas, $fila["tarifa"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_CONCEPTO_ADICIONAL_FACTURA_TARIFA;
        $objeto_accion_usuario = $fila["nombre"]." (".$nombre_tarifa.")";

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_MEDICION] = $medicion;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA] = $fila["tipo"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_COSTE_CONCEPTO_ADICIONAL_FACTURA_TARIFA] = $fila["coste"];
        switch ($fila["tipo"])
        {
            case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_ABSOLUTO:
            case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_CONSUMO_DIARIO:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_LIMITES_CONSUMO_TRAMOS_CONCEPTO_ADICIONAL_FACTURA_TARIFA] = $fila["limites_consumo_tramos"];
                break;
            }
        }
        $caracteristicas_tarifas_pais = dame_caracteristicas_tarifas_pais_medicion($medicion);
        if ($caracteristicas_tarifas_pais["impuesto_conceptos_adicionales_factura"] == true)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_IMPUESTO_CONCEPTO_ADICIONAL_FACTURA_TARIFA] = $fila["impuesto"];
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
