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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_GRUPO_TARIFAS, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $medicion = $_POST["medicion"];
    $id_grupo_tarifas = $_POST["id_grupo_tarifas"];
    if ($id_grupo_tarifas === NULL)
    {
        $id_grupo_tarifas = ID_NINGUNO;
    }

    // Añadir o modificar grupo de tarifas
    $anyadir_grupo_tarifas = ($id_grupo_tarifas == ID_NINGUNO);
    if ($anyadir_grupo_tarifas == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_smartmeter_anyadir_modificar_grupo_tarifas">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= " ".$idiomas->_("grupo de tarifas");

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_grupo_tarifas(
        $anyadir_grupo_tarifas,
        $medicion,
        $id_grupo_tarifas,
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
        <div id="parametros_ventana_anyadir_modificar_grupo_tarifas"
            anyadir_grupo_tarifas="'.$anyadir_grupo_tarifas.'"
            id_grupo_tarifas="'.$id_grupo_tarifas.'"
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
	// Funcion para mostrar el contenido de la ventana de anyadir/modificar grupo de tarifas
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar grupo de tarifas
	function rellena_contenido_ventana_anyadir_modificar_grupo_tarifas(
        $anyadir_grupo_tarifas,
        $medicion,
        $id_grupo_tarifas,
        &$contenido)
	{
        $idiomas = new Idiomas();

		// Si hay que modificar el grupo de tarifas, se recupera la información actual de la base de datos
		if ($anyadir_grupo_tarifas == false)
		{
            $tabla_grupos_tarifas = dame_nombre_tabla_grupos_tarifas($medicion);
            $fila_grupo_tarifas = dame_fila_grupo_tarifas($tabla_grupos_tarifas, $id_grupo_tarifas);

			$nombre = $fila_grupo_tarifas["nombre"];
            $descripcion = $fila_grupo_tarifas["descripcion"];
		}

        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
					<input type='text' id='nombre_grupo_tarifas'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>";

        // Descripción
        $numero_caracteres_actuales = dame_numero_caracteres($descripcion);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_DESCRIPCION;
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'>
                    <span class='titulo-campo-administracion'>".$idiomas->_('Descripción').": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                        "(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
                    <textarea id='descripcion_grupo_tarifas'
						class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        return ("OK");
	}
?>


