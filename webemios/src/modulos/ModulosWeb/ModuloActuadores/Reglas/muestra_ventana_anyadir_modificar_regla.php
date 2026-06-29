<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/AccionRegla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/SucesoRegla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/util_reglas.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_REGLA, $_POST);

    $bd_red = BaseDatosRed::dame_base_datos();
    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_regla = $_POST["id_regla"];
    if ($id_regla === NULL)
    {
        $id_regla = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Flag que indica si se puede realizar la operación
    $operacion_permitida = True;

    // Si es una duplicación de regla se comprueba si se puede duplicar
    // (si hay sucesos o acciones no administrables por el usuario actual no se puede duplicar)
    if ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO)
    {
        $posible_duplicar_regla = true;

        // Se comprueban los sucesos de la regla
        if ($posible_duplicar_regla == true)
        {
            $consulta_sucesos = SucesoRegla::dame_consulta_sucesos($id_regla);
            $res_sucesos = $bd_red->ejecuta_consulta($consulta_sucesos);
            if ($res_sucesos == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_sucesos."'");
            }
            while ($fila_suceso = $res_sucesos->dame_siguiente_fila())
            {
                $suceso = new SucesoRegla($fila_suceso);
                if ($suceso->dame_administracion_suceso_usuario_actual() == false)
                {
                    $posible_duplicar_regla = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("No se puede duplicar la regla porque tiene sucesos no administrables por el usuario actual");
                    break;
                }
            }
        }

        // Se comprueban las acciones de la regla
        if ($posible_duplicar_regla == true)
        {
            $consulta_acciones = AccionRegla::dame_consulta_acciones($id_regla, ID_NINGUNO);
            $res_acciones = $bd_red->ejecuta_consulta($consulta_acciones);
            if ($res_acciones == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_acciones."'");
            }
            while ($fila_accion = $res_acciones->dame_siguiente_fila())
            {
                $accion = new AccionRegla($fila_accion);
                if ($accion->dame_administracion_accion_usuario_actual() == false)
                {
                    $posible_duplicar_regla = false;

                    $res = "ERROR";
                    $msg = $idiomas->_("No se puede duplicar la regla porque tiene acciones no administrables por el usuario actual");
                    break;
                }
            }
        }

        // Flag de operación permitida
        $operacion_permitida = $posible_duplicar_regla;
    }

    // Añadir o modificar regla
    if ($operacion_permitida == true)
    {
        $anyadir_regla = (($id_regla == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
        if ($anyadir_regla == true)
        {
            $titulo .= $idiomas->_("Añadir");
        }
        else
        {
            $titulo .= $idiomas->_("Modificar");
        }
        $pie .= '<button class="btn btn-success boton_actuadores_anyadir_modificar_regla">'.$titulo.'</button>';
        $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

        // Título
        $titulo .= " ".$idiomas->_("regla");
        if (($anyadir_regla == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
        {
            $titulo .= " (".$idiomas->_("duplicar").")";
        }

        // Se recupera el contenido de la ventana
        $error = rellena_contenido_ventana_anyadir_modificar_regla($anyadir_regla, $id_regla, $contenido);
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
            <div id="parametros_ventana_anyadir_modificar_regla"
                anyadir_regla="'.$anyadir_regla.'"
                id_regla="'.$id_regla.'"
                hidden>
            </div>';
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "titulo" => $titulo,
        "contenido" => $contenido,
        "pie" => $pie))
    );


	//
	// Funcion para mostrar el contenido de la ventana de anyadir/modificar regla
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar regla
	function rellena_contenido_ventana_anyadir_modificar_regla($anyadir_regla, $id_regla, &$contenido)
	{
		$idiomas = new Idiomas();

		// Si hay que modificar la regla (o es un duplicado), se recupera la información actual de la base de datos
        if ($id_regla != ID_NINGUNO)
		{
			$fila_regla = dame_fila_regla($id_regla);

			$nombre = $fila_regla["nombre"];
            $descripcion = $fila_regla["descripcion"];
            $tipo = $fila_regla["tipo"];
            $modo_activacion = $fila_regla["modo_activacion"];
            $numero_dias_caducidad_acciones = $fila_regla["numero_dias_caducidad_acciones"];
		}
        else
        {
            $nombre = "";
            $descripcion = "";
            $tipo = ID_NINGUNO;
            $modo_activacion = ID_NINGUNO;
            $numero_dias_caducidad_acciones = 7;
        }

		$contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Nombre').": "."</span><br/>
					<input type='text' id='nombre_regla'
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
                    <textarea id='descripcion_regla'
						class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
					<select id='tipo_regla' class='select-administracion'>";
        $contenido .= dame_lista_tipos_regla($tipo);
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Modo de activación").": "."</span><br/>
					<select id='modo_activacion_regla' class='select-administracion'>";
        $contenido .= dame_lista_modos_activacion_regla($modo_activacion);
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de días de caducidad de acciones").": "."</span><br/>
                    <input type='text' id='numero_dias_caducidad_acciones_regla'
                        class='TLNT_input_mandatory TLNT_input_integer input-administracion' value='".$numero_dias_caducidad_acciones."'>
                    <span id='boton_actuadores_ayuda_numero_dias_caducidad_acciones_regla' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
                </div>
            </div>";

        return ("OK");
	}
?>
