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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/electricidad/Portugal/util_tarifas_electricidad_Portugal.php');
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
    $pie .= '<button class="btn btn-success boton_smartmeter_anyadir_modificar_tarifa_electricidad_Portugal">'.$titulo.'</button>';
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
            $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_PORTUGAL, $id_tarifa_electrica);

						$nombre = $fila_tarifa_electrica["nombre"];
            $descripcion = $fila_tarifa_electrica["descripcion"];
            $tipo = $fila_tarifa_electrica["tipo"];
						$ciclo = $fila_tarifa_electrica["ciclo"];
						$region = $fila_tarifa_electrica["region"];
						$id_grupo = $fila_tarifa_electrica["grupo"];
            $expiracion = $fila_tarifa_electrica["expiracion"];
            $cadena_fecha_expiracion_local_local = convierte_formato_fecha($fila_tarifa_electrica["fecha_expiracion"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $numero_dias_preaviso_expiracion = $fila_tarifa_electrica["numero_dias_preaviso_expiracion"];
            $impuesto_electrico = $fila_tarifa_electrica["impuesto_electrico"];
            $iva = $fila_tarifa_electrica["iva"];
            $contribucion_audiovisual = $fila_tarifa_electrica["contribucion_audiovisual"];
						$iva_reducido = $fila_tarifa_electrica["iva_reducido"];

						$fila_tramo_tarifa_elecrica = dame_info_tramos_tarifa_electricidad_Portugal($id_tarifa_electrica);
						$precio_consumo_ponta = $fila_tramo_tarifa_elecrica["precio_consumo_ponta"];
            $precio_consumo_cheia = $fila_tramo_tarifa_elecrica["precio_consumo_cheia"];
            $precio_consumo_vazio_normal = $fila_tramo_tarifa_elecrica["precio_consumo_vazio"];
            $precio_consumo_super_vazio = $fila_tramo_tarifa_elecrica["precio_consumo_super_vazio"];
            $precio_acceso_ponta = $fila_tramo_tarifa_elecrica["precio_consumo_tarifa_acceso_ponta"];
            $precio_acceso_cheia = $fila_tramo_tarifa_elecrica["precio_consumo_tarifa_acceso_cheia"];
            $precio_acceso_vazio_normal = $fila_tramo_tarifa_elecrica["precio_consumo_tarifa_acceso_vazio"];
            $precio_acceso_super_vazio = $fila_tramo_tarifa_elecrica["precio_consumo_tarifa_acceso_super_vazio"];
            $potencia_contratada = $fila_tramo_tarifa_elecrica["potencia_contratada"];
            $precio_potencia_contratada = $fila_tramo_tarifa_elecrica["precio_potencia_contratada"];
            $precio_potencia_ponta = $fila_tramo_tarifa_elecrica["precio_potencia_ponta"];
            $energia_reactiva_inductiva  = $fila_tramo_tarifa_elecrica["precio_inductiva"];
            $energia_reactiva_capacitiva = $fila_tramo_tarifa_elecrica["precio_capacitiva"];

		}
        else
        {
            $tipo = TIPO_TARIFA_NINGUNO;
            $ciclo = CICLO_TARIFA_ELECTRICA_PORTUGAL_NINGUNO;
            $region = REGIONES_PORTUGAL_NINGUNO;
            $id_grupo = ID_NINGUNO;
            $expiracion = EXPIRACION_TARIFA_NINGUNO;
            $fecha_hora_expiracion_local = dame_fecha_hora_actual_local();
            date_add($fecha_hora_expiracion_local, date_interval_create_from_date_string("1 year"));
            $cadena_fecha_expiracion_local_local = convierte_fecha_a_cadena($fecha_hora_expiracion_local, $_SESSION["formato_fecha_local"]);
            $numero_dias_preaviso_expiracion = 30;
            $precio_consumo_ponta = 0;
            $precio_consumo_cheia = 0;
            $precio_consumo_vazio_normal = 0;
            $precio_consumo_super_vazio = 0;
            $precio_acceso_ponta = 0;
            $precio_acceso_cheia = 0;
            $precio_acceso_vazio_normal = 0;
            $precio_acceso_super_vazio = 0;
            $potencia_contratada = 0;
            $precio_potencia_contratada = 0;
            $precio_potencia_ponta = 0;
            $energia_reactiva_inductiva  = 0;
            $energia_reactiva_capacitiva = 0;
            $impuesto_electrico = VALOR_DEFECTO_IMPUESTO_ELECTRICO_FACTURAS_ELECTRICAS_PORTUGAL;
            $iva = VALOR_DEFECTO_IVA_FACTURAS_ELECTRICAS_PORTUGAL;
            $contribucion_audiovisual = VALOR_DEFECTO_CONTRIBUCION_AUDIOVISUAL_FACTURAS_ELECTRICAS_PORTUGAL;
            $iva_reducido = VALOR_DEFECTO_IVA_REDUCIDO_FACTURAS_ELECTRICAS_PORTUGAL;
        }

        $contenido = dame_contenido_pestanyas_ventana_administracion_tarifas_electricidad_Portugal(
            TIPO_ADMINISTRACION_TARIFAS_UNICA,
            $id_tarifa_electrica,
            $nombre,
            $descripcion,
            $tipo,
            $ciclo,
            $region,
            $id_grupo,
            $expiracion,
            $cadena_fecha_expiracion_local_local ,
            $numero_dias_preaviso_expiracion,
            $precio_consumo_ponta,
            $precio_consumo_cheia,
            $precio_consumo_vazio_normal,
            $precio_consumo_super_vazio,
            $precio_acceso_ponta,
            $precio_acceso_cheia,
            $precio_acceso_vazio_normal,
            $precio_acceso_super_vazio,
            $potencia_contratada,
            $precio_potencia_contratada,
            $precio_potencia_ponta,
            $energia_reactiva_inductiva,
            $energia_reactiva_capacitiva,
            $impuesto_electrico ,
            $iva,
            $contribucion_audiovisual ,
            $iva_reducido);

        return ("OK");
	}
?>
