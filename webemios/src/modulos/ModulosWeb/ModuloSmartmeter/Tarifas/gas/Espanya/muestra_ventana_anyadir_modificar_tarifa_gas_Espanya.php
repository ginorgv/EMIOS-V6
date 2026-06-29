<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/gas/Espanya/util_tarifas_gas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_TARIFA_GAS, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_tarifa_gas = $_POST["id_tarifa_gas"];
    if ($id_tarifa_gas === NULL)
    {
        $id_tarifa_gas = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Añadir o modificar tarifa de gas
    $anyadir_tarifa_gas = (($id_tarifa_gas == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
    if ($anyadir_tarifa_gas == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_smartmeter_anyadir_modificar_tarifa_gas_Espanya">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= " ".$idiomas->_("tarifa");
    if (($anyadir_tarifa_gas == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
    {
        $titulo .= " (".$idiomas->_("duplicar").")";
    }

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_tarifa_gas($anyadir_tarifa_gas, $id_tarifa_gas, $contenido);
    if ($error == "OK")
    {
        $res = "OK";
    }
    else
    {
        $res = "ERROR";
        $msg = $error;
    }

    // Se añaden los parámetros (no visibles) en un 'div' oculto
    $contenido .= '
        <div id="parametros_ventana_anyadir_modificar_tarifa_gas"
            anyadir_tarifa_gas="'.$anyadir_tarifa_gas.'"
            id_tarifa_gas="'.$id_tarifa_gas.'"
            hidden>
        </div>';

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "titulo" => $titulo,
        "contenido" => $contenido,
        "pie" => $pie))
    );


	//
	// Funcion para mostrar el contenido de la ventana de anyadir/modificar tarifa de gas
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar tarifa de gas
	function rellena_contenido_ventana_anyadir_modificar_tarifa_gas($anyadir_tarifa_gas, $id_tarifa_gas, &$contenido)
	{
        // Si hay que modificar la tarifa de gas (o es un duplicado), se recupera la información actual de la base de datos
		if ($id_tarifa_gas != ID_NINGUNO)
		{
            $fila_tarifa_gas = dame_fila_tarifa(TABLA_TARIFAS_GAS_ESPANYA, $id_tarifa_gas);

			$nombre = $fila_tarifa_gas["nombre"];
            $descripcion = $fila_tarifa_gas["descripcion"];
            $tipo = $fila_tarifa_gas["tipo"];
            $id_grupo = $fila_tarifa_gas["grupo"];
            $expiracion = $fila_tarifa_gas["expiracion"];
            $cadena_fecha_expiracion_local_local = convierte_formato_fecha($fila_tarifa_gas["fecha_expiracion"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $numero_dias_preaviso_expiracion = $fila_tarifa_gas["numero_dias_preaviso_expiracion"];
            $factor_conversion = $fila_tarifa_gas["factor_conversion"];
            $precio_consumo = $fila_tarifa_gas["precio_consumo"];
            $precio_caudal_diario = $fila_tarifa_gas["precio_caudal_diario"];
            $caudal_diario = $fila_tarifa_gas["caudal_diario"];
            $precio_termino_fijo_diario = $fila_tarifa_gas["precio_termino_fijo_diario"];
            $impuesto_gas = $fila_tarifa_gas["impuesto_gas"];
            $tipo_alquiler_contador = $fila_tarifa_gas["tipo_alquiler_contador"];
            $alquiler_contador = $fila_tarifa_gas["alquiler_contador"];
            $iva = $fila_tarifa_gas["iva"];
		}
        else
        {
            $tipo = TIPO_TARIFA_NINGUNO;
            $id_grupo = ID_NINGUNO;
            $expiracion = EXPIRACION_TARIFA_NINGUNO;
            $fecha_hora_expiracion_local = dame_fecha_hora_actual_local();
            date_add($fecha_hora_expiracion_local, date_interval_create_from_date_string("1 year"));
            $cadena_fecha_expiracion_local_local = convierte_fecha_a_cadena($fecha_hora_expiracion_local, $_SESSION["formato_fecha_local"]);
            $numero_dias_preaviso_expiracion = 30;
            $factor_conversion = 1;
            $precio_consumo = 0;
            $precio_caudal_diario = 0;
            $caudal_diario = 0;
            $precio_termino_fijo_diario = 0;
            $impuesto_gas = VALOR_DEFECTO_IMPUESTO_GAS_FACTURAS_GAS_ESPANYA;
            $tipo_alquiler_contador = TIPO_ALQUILER_CONTADOR_NINGUNO;
            $alquiler_contador = 0;
            $iva = VALOR_DEFECTO_IVA_FACTURAS_GAS_ESPANYA;
        }

        $contenido = dame_contenido_pestanyas_ventana_administracion_tarifas_gas_Espanya(
            TIPO_ADMINISTRACION_TARIFAS_UNICA,
            $id_tarifa_gas,
            $nombre,
            $descripcion,
            $tipo,
            $id_grupo,
            $expiracion,
            $cadena_fecha_expiracion_local_local,
            $numero_dias_preaviso_expiracion,
            $factor_conversion,
            $precio_consumo,
            $precio_caudal_diario,
            $caudal_diario,
            $precio_termino_fijo_diario,
            $impuesto_gas,
            $tipo_alquiler_contador,
            $alquiler_contador,
            $iva);

        return ("OK");
	}
?>


