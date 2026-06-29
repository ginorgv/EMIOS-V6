<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/util_administracion_usuarios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_LICENCIA, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_licencia = $_POST["id_licencia"];
    if ($id_licencia === NULL)
    {
        $id_licencia = ID_NINGUNO;
    }

    // Añadir o modificar licencia
    $anyadir_licencia = ($id_licencia == ID_NINGUNO);
    if ($anyadir_licencia == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_administracion_anyadir_modificar_licencia">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= " ".$idiomas->_("licencia");
    $error = rellena_contenido_ventana_anyadir_modificar_licencia($anyadir_licencia, $id_licencia, $contenido);
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
        <div id="parametros_ventana_anyadir_modificar_licencia"
            anyadir_licencia="'.$anyadir_licencia.'"
            id_licencia="'.$id_licencia.'"
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
	// Funcion para mostrar el contenido de la ventana de anyadir/modificar licencia
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar licencia
	function rellena_contenido_ventana_anyadir_modificar_licencia($anyadir_licencia, $id_licencia, &$contenido)
	{
		$idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

		// Si hay que modificar la licencia, se recupera la información actual de la base de datos
		if ($anyadir_licencia == false)
		{
			$consulta = "
				SELECT *
				FROM licencias
				WHERE
					id = '".$bd_red->_($id_licencia)."'";
			$res = $bd_red->ejecuta_consulta($consulta);
			if (($res == false) || ($res->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
            }

			$fila = $res->dame_siguiente_fila();
			$modulo = $fila["modulo"];
            $activada = $fila["activada"];
            $numero_maximo_elementos = $fila["numero_maximo_elementos"];
		}
        else
        {
            $modulo = ID_NINGUNO;
            $activada = VALOR_SI;
            $numero_maximo_elementos = 0;
        }

        // No se permite cambiar el módulo de la licencia si se está modificando
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Módulo").": "."</span><br/>
					<select id='modulo_licencia' class='select-administracion'";
        if ($anyadir_licencia == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_modulos($modulo);
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Activada").": "."</span><br/>
                    <select id='activada_licencia' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($activada);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_numero_maximo_elementos_licencia'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Número máximo de elementos').": "."</span><br/>
					<input type='text' id='numero_maximo_elementos_licencia'
						class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_maximo_elementos."'>
				</div>
			</div>";

        return ("OK");
	}
?>
