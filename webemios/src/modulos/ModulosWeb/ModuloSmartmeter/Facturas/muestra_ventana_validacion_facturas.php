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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Espanya/util_facturas_electricidad_Espanya.php');


    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    $medicion = $_POST["medicion"];

    // Botones de la ventana
    $pie .= '<button class="btn btn-success boton_smartmeter_validar_facturas">'.$idiomas->_("Validar").'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= $idiomas->_("Validar facturas y cierres");
    $error = rellena_contenido_ventana_validacion_facturas($medicion, $contenido);
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
	// Funciones para mostrar el contenido de la ventana de validación de facturas
	//


	// Función que rellena el contenido de la ventana de validación de facturas
	function rellena_contenido_ventana_validacion_facturas($medicion, &$contenido)
	{
        $idiomas = new Idiomas();

		$contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ficheros de facturas y cierres").": "."</span><br/>
                    <input type='file' id='ficheros_validacion_facturas_files' multiple>
                    <textarea id='ficheros_validacion_facturas_text'
						class='TLNT_input_mandatory input-administracion' rows='10' readonly></textarea>
                    <button id='boton_validacion_facturas_seleccionar_ficheros' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de ficheros").": "."</span><br/>
                    <select id='tipo_ficheros_validacion_facturas' class='select-administracion'>";
        $contenido .= dame_lista_tipos_fichero_validacion_facturas($medicion);
        $contenido .= "
                    </select>
                </div>
            </div>";

        return ("OK");
	}


    //
    // Funciones auxiliares
    //


    // Devuelve la lista de tipos de fichero de validación de facturas
    function dame_lista_tipos_fichero_validacion_facturas($medicion)
    {
        switch ($medicion)
        {
            case MEDICION_ELECTRICIDAD:
            {
                $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                switch ($pais_tarifas_electricas)
                {
                    case PAIS_ESPANYA:
                    {
                        $lista = dame_lista_tipos_fichero_validacion_facturas_electricidad_Espanya(TIPO_NINGUNO);
                        break;
                    }
                    default:
                    {
                        throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                    }
                }
                break;
            }
            default:
            {
                throw new Exception("Medición desconocida: '".$medicion."'");
            }
        }
        return ($lista);
    }
?>
