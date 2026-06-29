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
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_CONCEPTO_ADICIONAL_FACTURA_TARIFA, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $medicion = $_POST["medicion"];
    $id_tarifa = $_POST["id_tarifa"];
    $id_concepto_adicional = $_POST["id_concepto_adicional"];
    if ($id_concepto_adicional === NULL)
    {
        $id_concepto_adicional = ID_NINGUNO;
    }

    // Añadir o modificar concepto adicional
    $anyadir_concepto_adicional = ($id_concepto_adicional == ID_NINGUNO);
    if ($anyadir_concepto_adicional == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_smartmeter_anyadir_modificar_concepto_adicional_factura_tarifa">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= " ".$idiomas->_("concepto adicional de factura");

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_concepto_adicional(
        $anyadir_concepto_adicional,
        $medicion,
        $id_tarifa,
        $id_concepto_adicional,
        $contenido);
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
        <div id="parametros_ventana_anyadir_modificar_concepto_adicional_factura_tarifa"
            anyadir_concepto_adicional="'.$anyadir_concepto_adicional.'"
            id_tarifa="'.$id_tarifa.'"
            id_concepto_adicional="'.$id_concepto_adicional.'"
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
	// Función para mostrar el contenido de la ventana de anyadir/modificar concepto adicional
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar concepto adicional
	function rellena_contenido_ventana_anyadir_modificar_concepto_adicional(
        $anyadir_concepto_adicional,
        $medicion,
        $id_tarifa,
        $id_concepto_adicional,
        &$contenido)
	{
        $idiomas = new Idiomas();

        // Unidad de medida de coste
        $unidad_medida_coste = $_SESSION["moneda"];

		// Si hay que modificar el concepto adicional, se recupera la información actual de la base de datos
		if ($anyadir_concepto_adicional == false)
		{
            $tabla_conceptos_adicionales = dame_nombre_tabla_conceptos_adicionales_facturas_tarifas($medicion);
            $fila_concepto_adicional = dame_fila_concepto_adicional_factura_tarifa($tabla_conceptos_adicionales, $id_concepto_adicional);

			$nombre = $fila_concepto_adicional["nombre"];
            $tipo = $fila_concepto_adicional["tipo"];
            $coste = $fila_concepto_adicional["coste"];
            $limites_consumo_tramos = $fila_concepto_adicional["limites_consumo_tramos"];
            $impuesto = $fila_concepto_adicional["impuesto"];

            $cadena_coste = str_replace(SEPARADOR_PARAMETROS_SIMPLES, ", ", $coste);
            $cadena_limites_consumo_tramos = str_replace(SEPARADOR_PARAMETROS_SIMPLES, ", ", $limites_consumo_tramos);
		}
        else
        {
            $impuesto = 0;
        }

        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
					<input type='text' id='nombre_concepto_adicional_factura_tarifa'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
                    <select id='tipo_concepto_adicional_factura_tarifa' class='chosen-select-administracion'>";
        $contenido .= dame_lista_tipos_conceptos_adicionales_factura_tarifa($medicion, $tipo);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Coste")." (".$unidad_medida_coste.")".": "."</span><br/>
					<input type='text' id='coste_concepto_adicional_factura_tarifa'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$cadena_coste."'>
				</div>
			</div>";

        $clase_sensor = dame_clase_sensor_medicion($medicion);
        $unidad_medida_consumo = dame_unidad_medida_consumo_clase_sensor($clase_sensor);
        $contenido .= "
			<div class='row-fluid' id='control_limites_consumo_tramos_concepto_adicional_factura_tarifa'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Límites de consumo por tramo")." (".$unidad_medida_consumo."): "."</span><br/>
					<input type='text' id='limites_consumo_tramos_concepto_adicional_factura_tarifa'
						class='TLNT_input_valid_characters input-administracion' value='".$cadena_limites_consumo_tramos."'>
                    <span id='boton_smartmeter_ayuda_limites_consumo_tramos_concepto_adicional_factura_tarifa' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
				</div>
			</div>";

        $nombre_impuesto = "";
        $hay_impuesto = dame_hay_impuesto_conceptos_adicionales_factura_tarifa($medicion, $id_tarifa, $nombre_impuesto);
        $contenido .= "
            <div class='row-fluid'";
        if ($hay_impuesto == false)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">";
        $contenido .= "
				<div class='span12'><span class='titulo-campo-administracion'>".$nombre_impuesto." (%)".": "."</span><br/>
					<input type='text' id='impuesto_concepto_adicional_factura_tarifa'
						class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$impuesto."'>
				</div>
			</div>";

        return ("OK");
	}
?>


