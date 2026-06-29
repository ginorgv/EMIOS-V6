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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_MODIFICAR_TARIFAS_ELECTRICAS, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Botones de la ventana
    $pie .= '<button class="btn btn-success boton_smartmeter_modificar_tarifas_electricidad_Espanya">'.$idiomas->_("Modificar").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= $idiomas->_("Modificar tarifas");
    $error = rellena_contenido_ventana_modificar_tarifas_electricas($contenido);
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
	// Funcion para mostrar el contenido de la ventana de modificar tarifas eléctricas
	//


	// Función que rellena el contenido de la ventana de modificar tarifas eléctricas
	function rellena_contenido_ventana_modificar_tarifas_electricas(&$contenido)
	{
        $tipo = TIPO_TARIFA_TODOS;
        $contrato = CONTRATO_TARIFA_ELECTRICA_TODOS;
        $expiracion = EXPIRACION_TARIFA_NINGUNO;
        $fecha_hora_expiracion_local = dame_fecha_hora_actual_local();
        date_add($fecha_hora_expiracion_local, date_interval_create_from_date_string("1 year"));
        $cadena_fecha_expiracion_local_local = convierte_fecha_a_cadena($fecha_hora_expiracion_local, $_SESSION["formato_fecha_local"]);
        $numero_dias_preaviso_expiracion = 30;
        $bonificacion_85 = BONIFICACION_85_TARIFA_ELECTRICA_NINGUNA;
        $tipo_medida = TIPO_MEDIDA_TARIFA_ELECTRICA_NINGUNA;
        $potencia_nominal_transformador = "";
        $formula_precio_consumo_pass_through = "";
        $id_indicador_omie_pass_pool = ID_INDICADOR_OMIE_TARIFA_ELECTRICA_NINGUNO;
        $tipo_calculo_coste_pass_pool = TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_NINGUNO;
        $dia_calculo_coste_automatico_pass_pool = "";
        $fecha_inicio_contrato_cierre = convierte_fecha_a_cadena(dame_fecha_hora_actual_local(), $_SESSION["formato_fecha_local"]);
        $impuesto_electrico = "";
        $tipo_alquiler_contador = TIPO_ALQUILER_CONTADOR_NINGUNO;
        $alquiler_contador = "";
        $iva = "";
        $igic_reducido = "";
        $igic_normal = "";
        $prorrateo = PRORRATEO_TARIFA_SI;

        $contenido = dame_contenido_pestanyas_ventana_administracion_tarifas_electricidad_Espanya(
            TIPO_ADMINISTRACION_TARIFAS_MULTIPLE,
            ID_NINGUNO,
            NULL,
            NULL,
            $tipo,
            $contrato,
            NULL,
            $expiracion,
            $cadena_fecha_expiracion_local_local,
            $numero_dias_preaviso_expiracion,
            $bonificacion_85,
            $tipo_medida,
            $potencia_nominal_transformador,
            $formula_precio_consumo_pass_through,
            $id_indicador_omie_pass_pool,
            $tipo_calculo_coste_pass_pool,
            $dia_calculo_coste_automatico_pass_pool,
            $fecha_inicio_contrato_cierre,
            $impuesto_electrico,
            $tipo_alquiler_contador,
            $alquiler_contador,
            $iva,
            $igic_reducido,
            $igic_normal,
            $prorrateo
        );

        return ("OK");
	}
?>


