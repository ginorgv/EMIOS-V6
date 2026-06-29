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
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_PERIODO_CALCULO_COSTES_PASS_POOL_TARIFA_ELECTRICA, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_tarifa_electrica = $_POST["id_tarifa_electrica"];
    $id_periodo_calculo_costes = $_POST["id_periodo_calculo_costes"];
    if ($id_periodo_calculo_costes === NULL)
    {
        $id_periodo_calculo_costes = ID_NINGUNO;
    }

    // Añadir o modificar periodo de cálculo de costes
    $anyadir_periodo_calculo_costes = ($id_periodo_calculo_costes == ID_NINGUNO);
    if ($anyadir_periodo_calculo_costes == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_smartmeter_anyadir_modificar_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= " ".$idiomas->_("periodo de cálculo de costes");
    $error = rellena_contenido_ventana_anyadir_modificar_periodo_calculo_costes($anyadir_periodo_calculo_costes, $id_periodo_calculo_costes, $contenido);
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
        <div id="parametros_ventana_anyadir_modificar_periodo_calculo_costes_pass_pool_tarifa_electrica"
            anyadir_periodo_calculo_costes="'.$anyadir_periodo_calculo_costes.'"
            id_tarifa_electrica="'.$id_tarifa_electrica.'"
            id_periodo_calculo_costes="'.$id_periodo_calculo_costes.'"
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
	// Funciones para mostrar el contenido de la ventana de anyadir/modificar periodo de cálculo de costes
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar periodo de cálculo de costes
	function rellena_contenido_ventana_anyadir_modificar_periodo_calculo_costes($anyadir_periodo_calculo_costes, $id_periodo_calculo_costes, &$contenido)
	{
        $idiomas = new Idiomas();

		// Si hay que modificar el periodo de cálculo de costes, se recupera la información actual de la base de datos
		if ($anyadir_periodo_calculo_costes == false)
		{
			$fila_periodo_calculo_costes = dame_fila_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya($id_periodo_calculo_costes);

            $cadena_fecha_inicio_local_local = convierte_formato_fecha($fila_periodo_calculo_costes["fecha_inicio"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            $cadena_fecha_fin_local_local = convierte_formato_fecha($fila_periodo_calculo_costes["fecha_fin"], FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
		}
        else
        {
            $fecha_hora_fin_local = dame_fecha_hora_actual_local();
            $fecha_hora_inicio_local = clone $fecha_hora_fin_local;
            date_sub($fecha_hora_inicio_local, date_interval_create_from_date_string("1 month"));
            $cadena_fecha_inicio_local_local = convierte_fecha_a_cadena($fecha_hora_inicio_local, $_SESSION["formato_fecha_local"]);
            $cadena_fecha_fin_local_local = convierte_fecha_a_cadena($fecha_hora_fin_local, $_SESSION["formato_fecha_local"]);
        }

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de inicio").": "."</span><br/>
                    <input size='10' type='text' id='fecha_inicio_periodo_calculo_costes_pass_pool_tarifa_electrica' class='datepicker selector-fechas-administracion'
                        readonly='readonly' value='".$cadena_fecha_inicio_local_local."'>
                </div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de fin").": "."</span><br/>
                    <input size='10' type='text' id='fecha_fin_periodo_calculo_costes_pass_pool_tarifa_electrica' class='datepicker selector-fechas-administracion'
                        readonly='readonly' value='".$cadena_fecha_fin_local_local."'>
                </div>
			</div>";

        return ("OK");
	}
?>
