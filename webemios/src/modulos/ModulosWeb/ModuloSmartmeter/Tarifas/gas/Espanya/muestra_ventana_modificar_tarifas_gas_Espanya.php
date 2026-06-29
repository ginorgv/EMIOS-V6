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

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/gas/Espanya/util_tarifas_gas_Espanya.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_MODIFICAR_TARIFAS_GAS, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Botones de la ventana
    $pie .= '<button class="btn btn-success boton_smartmeter_modificar_tarifas_gas_Espanya">'.$idiomas->_("Modificar").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= $idiomas->_("Modificar tarifas");
    $error = rellena_contenido_ventana_modificar_tarifas_gas($contenido);
    if ($error == "OK")
    {
        $res = "OK";
    }
    else
    {
        $res = "ERROR";
        $msg = $error;
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "titulo" => $titulo,
        "contenido" => $contenido,
        "pie" => $pie))
    );


	//
	// Funcion para mostrar el contenido de la ventana de modificar tarifas de gas
	//


	// Función que rellena el contenido de la ventana de modificar tarifas de gas
	function rellena_contenido_ventana_modificar_tarifas_gas(&$contenido)
	{
        $tipo = TIPO_TARIFA_TODOS;
        $expiracion = EXPIRACION_TARIFA_NINGUNO;
        $fecha_hora_expiracion_local = dame_fecha_hora_actual_local();
        date_add($fecha_hora_expiracion_local, date_interval_create_from_date_string("1 year"));
        $cadena_fecha_expiracion_local_local = convierte_fecha_a_cadena($fecha_hora_expiracion_local, $_SESSION["formato_fecha_local"]);
        $numero_dias_preaviso_expiracion = 30;
        $factor_conversion = "";
        $precio_consumo = "";
        $precio_caudal_diario = "";
        $caudal_diario = "";
        $precio_termino_fijo_diario = "";
        $impuesto_gas = "";
        $tipo_alquiler_contador = TIPO_ALQUILER_CONTADOR_NINGUNO;
        $alquiler_contador = "";
        $iva = "";

        $contenido = dame_contenido_pestanyas_ventana_administracion_tarifas_gas_Espanya(
            TIPO_ADMINISTRACION_TARIFAS_MULTIPLE,
            ID_NINGUNO,
            NULL,
            NULL,
            $tipo,
            NULL,
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


