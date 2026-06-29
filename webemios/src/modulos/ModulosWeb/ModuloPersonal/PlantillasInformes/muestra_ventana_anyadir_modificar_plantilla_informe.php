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
	include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/PlantillaInforme.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_PLANTILLA_INFORME, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_plantilla_informe = $_POST["id_plantilla_informe"];
    if ($id_plantilla_informe === NULL)
    {
        $id_plantilla_informe = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Añadir o modificar plantilla de informe
    $anyadir_plantilla_informe = (($id_plantilla_informe == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
    if ($anyadir_plantilla_informe == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_personal_anyadir_modificar_plantilla_informe">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= " ".$idiomas->_("plantilla de informe");
    if (($anyadir_plantilla_informe == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
    {
        $titulo .= " (".$idiomas->_("duplicar").")";
    }

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_plantilla_informe($anyadir_plantilla_informe, $id_plantilla_informe, $contenido);
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
    if ($id_plantilla_informe != ID_NINGUNO)
    {
        $fila_plantilla_informe = dame_fila_plantilla_informe($id_plantilla_informe);
        $periodo_tiempo_defecto = $fila_plantilla_informe["periodo_tiempo_defecto"];
        $logo_personalizado = $fila_plantilla_informe["logo_personalizado"];
    }
    $contenido .= '
        <div id="parametros_ventana_anyadir_modificar_plantilla_informe"
            anyadir_plantilla_informe="'.$anyadir_plantilla_informe.'"
            id_plantilla_informe="'.$id_plantilla_informe.'"
            periodo_tiempo_defecto="'.$periodo_tiempo_defecto.'"
            logo_personalizado="'.$logo_personalizado.'"
            perfil_usuario="'.$_SESSION["perfil"].'"
            id_red="'.$_SESSION["id_red"].'"
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
	// Funciones para mostrar el contenido de la ventana de anyadir/modificar plantilla de informe
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar plantilla de informe
	function rellena_contenido_ventana_anyadir_modificar_plantilla_informe($anyadir_plantilla_informe, $id_plantilla_informe, &$contenido)
	{
		$idiomas = new Idiomas();

		// Si hay que modificar la plantilla de informe (o es un duplicado), se recupera la información actual de la base de datos
		if ($id_plantilla_informe != ID_NINGUNO)
		{
            $fila_plantilla_informe = dame_fila_plantilla_informe($id_plantilla_informe);

			$nombre = $fila_plantilla_informe["nombre"];
			$descripcion = $fila_plantilla_informe["descripcion"];
            $tipo = $fila_plantilla_informe["tipo"];
            $titulo_informe = $fila_plantilla_informe["titulo_informe"];
            $periodo_tiempo_defecto = $fila_plantilla_informe["periodo_tiempo_defecto"];
            $iniciar_comienzo_periodo_tiempo_defecto = $fila_plantilla_informe["iniciar_comienzo_periodo_tiempo_defecto"];
            $tipo_seleccion_horario_semanal_fechas = $fila_plantilla_informe["tipo_seleccion_horario_semanal_fechas"];
            $logo_personalizado = $fila_plantilla_informe["logo_personalizado"];
            $nombre_logo = $fila_plantilla_informe["nombre_logo"];
            $tema = $fila_plantilla_informe["tema"];
		}
        else
        {
            $nombre = "";
            $descripcion = "";
            $tipo = TIPO_PLANTILLA_INFORME_FIJO;
            $titulo_informe = "";
            $periodo_tiempo_defecto = PERIODO_TIEMPO_DEFECTO_PLANTILLA_INFORME_SEMANA;
            $iniciar_comienzo_periodo_tiempo_defecto = VALOR_NO;
            $tipo_seleccion_horario_semanal_fechas = TIPO_SELECCION_HORARIO_SEMANAL_FECHAS_FIJO;
            $logo_personalizado = VALOR_NO;
            $tema = TEMA_DEFECTO;
        }

		$contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Nombre').": "."</span><br/>
					<input type='text' id='nombre_plantilla_informe'
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
                    <textarea id='descripcion_plantilla_informe'
						class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
					<select id='tipo_plantilla_informe' class='select-administracion'";

        // Se comprueba si se puede modificar el tipo de plantilla de informe
        if ($id_plantilla_informe != ID_NINGUNO)
        {
            $tipo_modificable_plantilla_informe = dame_tipo_modificable_plantilla_informe($id_plantilla_informe);
            if ($tipo_modificable_plantilla_informe == false)
            {
                $contenido .= " disabled";
            }
        }
        $contenido .= ">";

        $tipos_plantilla_informe = PlantillaInforme::dame_tipos_plantilla_informe();
        foreach ($tipos_plantilla_informe as $tipo_plantilla_informe)
        {
            $nombre_tipo_plantilla_informe = PlantillaInforme::dame_descripcion_tipo_plantilla_informe($tipo_plantilla_informe);
            $contenido .= "<option value='".$tipo_plantilla_informe."'";
            if ($tipo_plantilla_informe == $tipo)
            {
                $contenido .= " selected";
            }
            $contenido .= ">".htmlspecialchars($nombre_tipo_plantilla_informe, ENT_QUOTES)."</option>";
        }
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Título de informe').": "."</span><br/>
					<input type='text' id='titulo_informe_plantilla_informe'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($titulo_informe, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodo de tiempo por defecto").": "."</span><br/>
                    <select id='periodo_tiempo_defecto_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_periodos_tiempo_defecto_plantilla_informe($periodo_tiempo_defecto);
		$contenido .= "
                    </select>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Iniciar desde el comienzo del periodo de tiempo por defecto").": "."</span><br/>
                    <select id='iniciar_comienzo_periodo_tiempo_defecto_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($iniciar_comienzo_periodo_tiempo_defecto);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de selección de horario semanal y fechas").": "."</span><br/>
					<select id='tipo_seleccion_horario_semanal_fechas_plantilla_informe' class='select-administracion'>";

        $tipos_seleccion_horario_semanal_fechas_plantilla_informe = PlantillaInforme::dame_tipos_seleccion_horario_semanal_fechas();
        foreach ($tipos_seleccion_horario_semanal_fechas_plantilla_informe as $tipo_seleccion_horario_semanal_fechas_plantilla_informe)
        {
            $nombre_tipo_seleccion_horario_semanal_fechas_plantilla_informe =
                PlantillaInforme::dame_descripcion_tipo_seleccion_horario_semanal_fechas($tipo_seleccion_horario_semanal_fechas_plantilla_informe);
            $contenido .= "<option value='".$tipo_seleccion_horario_semanal_fechas_plantilla_informe."'";
            if ($tipo_seleccion_horario_semanal_fechas_plantilla_informe == $tipo_seleccion_horario_semanal_fechas)
            {
                $contenido .= " selected";
            }
            $contenido .= ">".htmlspecialchars($nombre_tipo_seleccion_horario_semanal_fechas_plantilla_informe, ENT_QUOTES)."</option>";
        }
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Logo personalizado").": "."</span><br/>
					<select id='logo_personalizado_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($logo_personalizado);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_nombre_logo_plantilla_informe'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre de logo").": "."</span><br/>
					<input type='text' id='nombre_logo_plantilla_informe'
						class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre_logo, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid' id='control_fichero_logo_pdf_plantilla_informe'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fichero de logo PDF").": "."</span><br/>
                    <input type='file' id='fichero_logo_pdf_plantilla_informe_file'>
                    <input type='text' id='fichero_logo_pdf_plantilla_informe_text'
                        class='TLNT_input_valid_characters input-administracion' readonly>
                    <button id='boton_anyadir_modificar_plantilla_informe_seleccionar_fichero_logo_pdf' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>";
        if (($anyadir_plantilla_informe == false) && ($logo_personalizado == VALOR_SI))
        {
            $origen = ORIGEN_IMAGEN_PLANTILLA_INFORME_LOGO_PDF;
            $id_origen = $id_plantilla_informe;
            $nombre_ventana = $idiomas->_("Logo PDF");
            $contenido .= "
                <button class='btn-mini btn btn-success boton-mostrar-imagen-fichero-administracion boton_mostrar_imagen_base_datos_ventana' ".
                    "origen='".$origen."' id_origen='".$id_origen."' nombre_ventana='".$nombre_ventana."'>".
                    "<i class='icon-picture color-blanco'></i></button>";
        }
        $contenido .= "
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tema").": "."</span><br/>
					<select id='tema_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_temas($tema);
		$contenido .= "
					</select>
				</div>
			</div>";

        $ocultar_id_red_destino = ((($anyadir_plantilla_informe == true) && ($id_plantilla_informe == ID_NINGUNO)) ||
            ($anyadir_plantilla_informe == false));
        $id_control_id_red_destino_plantilla_informe = "control_id_red_destino_plantilla_informe";
        if ($ocultar_id_red_destino == true)
        {
            $id_control_id_red_destino_plantilla_informe .= "-oculto";
        }
        $contenido .= "
            <div class='row-fluid' id='".$id_control_id_red_destino_plantilla_informe."'";
        if ($ocultar_id_red_destino == true)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Red destino").": "."</span><br/>
                    <select id='id_red_destino_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_redes(array($_SESSION["id_red"]));
		$contenido .= "
                    </select>
                </div>
            </div>";

        $ocultar_usuario_destino = ((($anyadir_plantilla_informe == true) && ($id_plantilla_informe == ID_NINGUNO)) ||
            ($anyadir_plantilla_informe == false) || ($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR));
        $id_control_id_usuario_destino_plantilla_informe = "control_id_usuario_destino_plantilla_informe";
        if ($ocultar_usuario_destino == true)
        {
            $id_control_id_usuario_destino_plantilla_informe .= "-oculto";
        }
        $contenido .= "
            <div class='row-fluid' id='".$id_control_id_usuario_destino_plantilla_informe."'";
        if ($ocultar_usuario_destino == true)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Usuario destino").": "."</span><br/>
                    <select id='id_usuario_destino_plantilla_informe' class='select-administracion'>";
        $contenido .= dame_lista_usuarios(PERFIL_USUARIO_ESTANDAR, OPCIONES_EXTRA_LISTA_USUARIOS_ACTUAL);
		$contenido .= "
                    </select>
                </div>
            </div>";

        return ("OK");
	}
?>
