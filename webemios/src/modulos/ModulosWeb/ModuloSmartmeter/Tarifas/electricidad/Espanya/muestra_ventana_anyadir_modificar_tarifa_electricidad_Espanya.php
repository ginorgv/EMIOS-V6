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
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_TARIFA_ELECTRICA, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_tarifa_electrica = $_POST["id_tarifa_electrica"];
    if ($id_tarifa_electrica === NULL)
    {
        $id_tarifa_electrica = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Añadir o modificar tarifa eléctrica
    $anyadir_tarifa_electrica = (($id_tarifa_electrica == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
    if ($anyadir_tarifa_electrica == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_smartmeter_anyadir_modificar_tarifa_electricidad_Espanya">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= " ".$idiomas->_("tarifa");
    if (($anyadir_tarifa_electrica == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
    {
        $titulo .= " (".$idiomas->_("duplicar").")";
    }

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_tarifa_electrica($anyadir_tarifa_electrica, $id_tarifa_electrica, $contenido);
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
        <div id="parametros_ventana_anyadir_modificar_tarifa_electrica"
            anyadir_tarifa_electrica="'.$anyadir_tarifa_electrica.'"
            id_tarifa_electrica="'.$id_tarifa_electrica.'"
            hidden>
        </div>';

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "titulo" => $titulo,
        "contenido" => $contenido,
        "pie" => $pie))
    );

    $log = dame_log();
    $log -> debug("El resultado es: ");
    $log -> debug(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "titulo" => $titulo,
        "contenido" => $contenido,
        "pie" => $pie)));


	//
	// Funcion para mostrar el contenido de la ventana de anyadir/modificar tarifa eléctrica
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar tarifa eléctrica
	function rellena_contenido_ventana_anyadir_modificar_tarifa_electrica(
        $anyadir_tarifa_electrica,
        $id_tarifa_electrica, 
        &$contenido)
	{
        // Si hay que modificar la tarifa eléctrica (o es un duplicado), se recupera la información actual de la base de datos
		if ($id_tarifa_electrica != ID_NINGUNO)
		{
            $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa_electrica);

			$nombre = $fila_tarifa_electrica["nombre"];
            $descripcion = $fila_tarifa_electrica["descripcion"];
            $tipo = $fila_tarifa_electrica["tipo"];
            $contrato = $fila_tarifa_electrica["contrato"];
            $id_grupo = $fila_tarifa_electrica["grupo"];
            $expiracion = $fila_tarifa_electrica["expiracion"];
            $cadena_fecha_expiracion_local_local = convierte_formato_fecha($fila_tarifa_electrica["fecha_expiracion"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $numero_dias_preaviso_expiracion = $fila_tarifa_electrica["numero_dias_preaviso_expiracion"];
            $bonificacion_85 = $fila_tarifa_electrica["bonificacion_85"];
            $tipo_medida = $fila_tarifa_electrica["tipo_medida"];
            $potencia_nominal_transformador = $fila_tarifa_electrica["potencia_nominal_transformador"];
            $formula_precio_consumo_pass_through = $fila_tarifa_electrica["formula_precio_consumo_pass_through"];
            $id_indicador_omie_pass_pool = $fila_tarifa_electrica["id_indicador_omie_pass_pool"];
            $tipo_calculo_coste_pass_pool = $fila_tarifa_electrica["tipo_calculo_coste_pass_pool"];
            $dia_calculo_coste_automatico_pass_pool = $fila_tarifa_electrica["dia_calculo_coste_automatico_pass_pool"];
            if ($fila_tarifa_electrica["fecha_inicio_contrato_cierre"])
                $fecha_inicio_contrato_cierre = convierte_formato_fecha($fila_tarifa_electrica["fecha_inicio_contrato_cierre"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            else
                $fecha_inicio_contrato_cierre = NULL;
            $impuesto_electrico = $fila_tarifa_electrica["impuesto_electrico"];
            $tipo_alquiler_contador = $fila_tarifa_electrica["tipo_alquiler_contador"];
            $alquiler_contador = $fila_tarifa_electrica["alquiler_contador"];
            $iva = $fila_tarifa_electrica["iva"];
            $igic_reducido = $fila_tarifa_electrica["igic_reducido"];
            $igic_normal = $fila_tarifa_electrica["igic_normal"];
            $prorrateo = $fila_tarifa_electrica["prorrateo"];
		}
        else
        {
            $tipo = TIPO_TARIFA_NINGUNO;
            $contrato = CONTRATO_TARIFA_ELECTRICA_ESPANYA_FIJO;
            $id_grupo = ID_NINGUNO;
            $expiracion = EXPIRACION_TARIFA_NINGUNO;
            $fecha_hora_expiracion_local = dame_fecha_hora_actual_local();
            date_add($fecha_hora_expiracion_local, date_interval_create_from_date_string("1 year"));
            $cadena_fecha_expiracion_local_local = convierte_fecha_a_cadena($fecha_hora_expiracion_local, $_SESSION["formato_fecha_local"]);
            $numero_dias_preaviso_expiracion = 30;
            $bonificacion_85 = BONIFICACION_85_TARIFA_ELECTRICA_SI;
            $tipo_medida = TIPO_MEDIDA_TARIFA_ELECTRICA_ALTA_TENSION;
            $potencia_nominal_transformador = "";
            $formula_precio_consumo_pass_through = "";
            $id_indicador_omie_pass_pool = ID_INDICADOR_OMIE_TARIFA_ELECTRICA_PENINSULA;
            $tipo_calculo_coste_pass_pool = TIPO_CALCULO_COSTE_TARIFA_ELECTRICA_PASS_POOL_NINGUNO;
            $dia_calculo_coste_automatico_pass_pool = 1;
            $fecha_inicio_contrato_cierre = convierte_fecha_a_cadena(dame_fecha_hora_actual_local(), $_SESSION["formato_fecha_local"]);
            $impuesto_electrico = VALOR_DEFECTO_IMPUESTO_ELECTRICO_FACTURAS_ELECTRICAS_ESPANYA;
            $tipo_alquiler_contador = TIPO_ALQUILER_CONTADOR_NINGUNO;
            $alquiler_contador = 0;
            $iva = VALOR_DEFECTO_IVA_FACTURAS_ELECTRICAS_ESPANYA;
            $igic_reducido = VALOR_DEFECTO_IGIC_REDUCIDO_FACTURAS_ELECTRICAS_ESPANYA;
            $igic_normal = VALOR_DEFECTO_IGIC_NORMAL_FACTURAS_ELECTRICAS_ESPANYA;
            $prorrateo = PRORRATEO_TARIFA_SI;
        }

        $contenido = dame_contenido_pestanyas_ventana_administracion_tarifas_electricidad_Espanya(
            TIPO_ADMINISTRACION_TARIFAS_UNICA,
            $id_tarifa_electrica,
            $nombre,
            $descripcion,
            $tipo,
            $contrato,
            $id_grupo,
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
            $prorrateo);

        return ("OK");
	}
?>


