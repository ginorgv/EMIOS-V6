<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_CONCEPTO_COSTE_PASS_THROUGH_TARIFA_ELECTRICA, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_tarifa_electrica = $_POST["id_tarifa_electrica"];
    $id_concepto_coste = $_POST["id_concepto_coste"];
    if ($id_concepto_coste === NULL)
    {
        $id_concepto_coste = ID_NINGUNO;
    }

    // Añadir o modificar concepto de coste
    $anyadir_concepto_coste = ($id_concepto_coste == ID_NINGUNO);
    if ($anyadir_concepto_coste == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_smartmeter_anyadir_modificar_concepto_coste_pass_through_tarifa_electricidad_Espanya">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= " ".$idiomas->_("concepto de coste");

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_concepto_coste(
        $anyadir_concepto_coste,
        $id_concepto_coste,
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
        <div id="parametros_ventana_anyadir_modificar_concepto_coste_pass_through_tarifa_electrica"
            anyadir_concepto_coste="'.$anyadir_concepto_coste.'"
            id_tarifa_electrica="'.$id_tarifa_electrica.'"
            id_concepto_coste="'.$id_concepto_coste.'"
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
	// Función para mostrar el contenido de la ventana de anyadir/modificar concepto de coste
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar concepto de coste
	function rellena_contenido_ventana_anyadir_modificar_concepto_coste(
        $anyadir_concepto_coste,
        $id_concepto_coste,
        &$contenido)
	{
        $idiomas = new Idiomas();

        // Si hay que modificar el concepto de coste, se recupera la información actual de la base de datos
		if ($anyadir_concepto_coste == false)
		{
            $fila_concepto_coste = dame_fila_concepto_coste_pass_through_tarifa_electricidad_Espanya($id_concepto_coste);

			$nombre = $fila_concepto_coste["nombre"];
            $formula_precio_consumo = $fila_concepto_coste["formula_precio_consumo"];
		}
        else
        {
        }

        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
					<input type='text' id='nombre_concepto_coste_pass_through_tarifa_electrica'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>";

        // Fórmula de precio de consumo
        $numero_caracteres_actuales = dame_numero_caracteres($formula_precio_consumo);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_FORMULA_PRECIO_CONSUMO;
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'>
                    <span class='titulo-campo-administracion'>".$idiomas->_("Fórmula de precio de consumo")." (€/".$idiomas->_("MWh").")".": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                         "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
                    <textarea id='formula_precio_consumo_concepto_coste_pass_through_tarifa_electrica'
                        class='TLNT_input_mandatory input-administracion' rows='5'>".htmlspecialchars($formula_precio_consumo, ENT_QUOTES)."</textarea>
                    <span id='boton_smartmeter_ayuda_formula_precio_consumo_pass_through_tarifa_electrica' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
                </div>
            </div>";

        return ("OK");
	}
?>


