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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/agua/Espanya/util_tarifas_agua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_TARIFA_AGUA, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_tarifa_agua = $_POST["id_tarifa_agua"];
    if ($id_tarifa_agua === NULL)
    {
        $id_tarifa_agua = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Añadir o modificar tarifa de agua
    $anyadir_tarifa_agua = (($id_tarifa_agua == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
    if ($anyadir_tarifa_agua == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_smartmeter_anyadir_modificar_tarifa_agua_Espanya">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= " ".$idiomas->_("tarifa");
    if (($anyadir_tarifa_agua == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
    {
        $titulo .= " (".$idiomas->_("duplicar").")";
    }

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_tarifa_agua($anyadir_tarifa_agua, $id_tarifa_agua, $contenido);
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
        <div id="parametros_ventana_anyadir_modificar_tarifa_agua"
            anyadir_tarifa_agua="'.$anyadir_tarifa_agua.'"
            id_tarifa_agua="'.$id_tarifa_agua.'"
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
	// Funcion para mostrar el contenido de la ventana de anyadir/modificar tarifa de agua
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar tarifa de agua
	function rellena_contenido_ventana_anyadir_modificar_tarifa_agua($anyadir_tarifa_agua, $id_tarifa_agua, &$contenido)
	{
        // Si hay que modificar la tarifa de agua (o es un duplicado), se recupera la información actual de la base de datos
		if ($id_tarifa_agua != ID_NINGUNO)
		{
            $fila_tarifa_agua = dame_fila_tarifa(TABLA_TARIFAS_AGUA_ESPANYA, $id_tarifa_agua);

			$nombre = $fila_tarifa_agua["nombre"];
            $descripcion = $fila_tarifa_agua["descripcion"];
            $tipo = $fila_tarifa_agua["tipo"];
            $id_grupo = $fila_tarifa_agua["grupo"];
            $expiracion = $fila_tarifa_agua["expiracion"];
            $cadena_fecha_expiracion_local_local = convierte_formato_fecha($fila_tarifa_agua["fecha_expiracion"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $numero_dias_preaviso_expiracion = $fila_tarifa_agua["numero_dias_preaviso_expiracion"];
            $tipo_limites_consumo_tramos = $fila_tarifa_agua["tipo_limites_consumo_tramos"];
            $cadena_limites_consumo_tramos = $fila_tarifa_agua["limites_consumo_tramos"];
            $cadena_precios_consumo_tramos = $fila_tarifa_agua["precios_consumo_tramos"];
            $tipo_alquiler_contador = $fila_tarifa_agua["tipo_alquiler_contador"];
            $alquiler_contador = $fila_tarifa_agua["alquiler_contador"];
            $iva_consumo = $fila_tarifa_agua["iva_consumo"];
            $igic_consumo = $fila_tarifa_agua["igic_consumo"];
            $iva_alquiler_contador = $fila_tarifa_agua["iva_alquiler_contador"];
            $igic_alquiler_contador = $fila_tarifa_agua["igic_alquiler_contador"];
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
            $tipo_limites_consumo_tramos = TIPO_LIMITES_CONSUMO_TRAMOS_ABSOLUTO;
            $cadena_limites_consumo_tramos = "";
            $cadena_precios_consumo_tramos = 0;
            $tipo_alquiler_contador = TIPO_ALQUILER_CONTADOR_NINGUNO;
            $alquiler_contador = 0;
            $iva_consumo = VALOR_DEFECTO_IVA_CONSUMO_FACTURAS_AGUA_ESPANYA;
            $igic_consumo = VALOR_DEFECTO_IGIC_CONSUMO_FACTURAS_AGUA_ESPANYA;
            $iva_alquiler_contador = VALOR_DEFECTO_IVA_ALQUILER_CONTADOR_FACTURAS_AGUA_ESPANYA;
            $igic_alquiler_contador = VALOR_DEFECTO_IGIC_ALQUILER_CONTADOR_FACTURAS_AGUA_ESPANYA;
        }

        $contenido = dame_contenido_pestanyas_ventana_administracion_tarifas_agua_Espanya(
            TIPO_ADMINISTRACION_TARIFAS_UNICA,
            $id_tarifa_agua,
            $nombre,
            $descripcion,
            $tipo,
            $id_grupo,
            $expiracion,
            $cadena_fecha_expiracion_local_local,
            $numero_dias_preaviso_expiracion,
            $tipo_limites_consumo_tramos,
            $cadena_limites_consumo_tramos,
            $cadena_precios_consumo_tramos,
            $tipo_alquiler_contador,
            $alquiler_contador,
            $iva_consumo,
            $igic_consumo,
            $iva_alquiler_contador,
            $igic_alquiler_contador);

        return ("OK");
	}
?>


