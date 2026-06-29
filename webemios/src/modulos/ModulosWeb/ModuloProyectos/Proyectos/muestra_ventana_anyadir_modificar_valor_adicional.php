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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/util_proyectos.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_VALOR_ADICIONAL_PROYECTO, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Proyectos
    $id_proyecto = $_POST["id_proyecto"];
    $id_valor_adicional = $_POST["id_valor_adicional"];
    if ($id_valor_adicional === NULL)
    {
        $id_valor_adicional = ID_NINGUNO;
    }

    // Añadir o modificar valor_adicional
    $anyadir_valor_adicional = ($id_valor_adicional == ID_NINGUNO);
    if ($anyadir_valor_adicional == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_proyectos_anyadir_modificar_valor_adicional_proyecto">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Se muestra el contenido de la ventana
    $titulo .= " ".$idiomas->_("valor adicional");
    $error = rellena_contenido_ventana_anyadir_modificar_valor_adicional_proyecto(
        $anyadir_valor_adicional,
        $id_valor_adicional,
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
        <div id="parametros_ventana_anyadir_modificar_valor_adicional_proyecto"
            anyadir_valor_adicional="'.$anyadir_valor_adicional.'"
            id_proyecto="'.$id_proyecto.'"
            id_valor_adicional="'.$id_valor_adicional.'"
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
	// Función para mostrar el contenido de la ventana de anyadir/modificar valor adicional
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar valor adicional de línea base
	function rellena_contenido_ventana_anyadir_modificar_valor_adicional_proyecto(
        $anyadir_valor_adicional,
        $id_valor_adicional,
        &$contenido)
	{
        $idiomas = new Idiomas();

		// Si hay que modificar el valor adicional
		if ($anyadir_valor_adicional == false)
		{
            // Se recupera la información actual de la base de datos
			$fila_valor_adicional_proyecto = dame_fila_valor_adicional_proyecto($id_valor_adicional);

            $nombre = $fila_valor_adicional_proyecto["nombre"];
            $destino = $fila_valor_adicional_proyecto["destino"];
            $valor = $fila_valor_adicional_proyecto["valor"];
            $periodicidad = $fila_valor_adicional_proyecto["periodicidad"];
            $cadena_fecha_inicio_base_datos_local = $fila_valor_adicional_proyecto["fecha_inicio"];
            $cadena_fecha_fin_base_datos_local = $fila_valor_adicional_proyecto["fecha_fin"];
            $aplicar_intervalos_sin_valores = $fila_valor_adicional_proyecto["aplicar_intervalos_sin_valores"];

            // Conversión de fechas
            if ($cadena_fecha_inicio_base_datos_local !== NULL)
            {
                $cadena_fecha_inicio_local_local = convierte_formato_fecha($cadena_fecha_inicio_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            }
            if ($cadena_fecha_fin_base_datos_local !== NULL)
            {
                $cadena_fecha_fin_local_local = convierte_formato_fecha($cadena_fecha_fin_base_datos_local, FORMATO_FECHA_BASE_DATOS, $_SESSION["formato_fecha_local"]);
            }
        }
        else
        {
            // Valores por defecto al añadir un valor adicional
            $destino = DESTINO_VALOR_ADICIONAL_PROYECTO_VALORES_REALES;
            $periodicidad = PERIODICIDAD_VALOR_ADICIONAL_PROYECTO_NINGUNA;
            $aplicar_intervalos_sin_valores = VALOR_SI;
        }

        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Nombre').": "."</span><br/>
					<input type='text' id='nombre_valor_adicional_proyecto'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Destino").": "."</span><br/>
                    <select id='destino_valor_adicional_proyecto' class='select-administracion'>";
        $contenido .= dame_lista_destinos_valor_adicional_proyecto($destino, false);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Valor').": "."</span><br/>
					<input type='text' id='valor_valor_adicional_proyecto'
						class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$valor."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Periodicidad").": "."</span><br/>
                    <select id='periodicidad_valor_adicional_proyecto' class='select-administracion'>";
        $contenido .= dame_lista_periodicidades_valor_adicional_proyecto($periodicidad, false);
        $contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid' id='control_fecha_inicio_valor_adicional_proyecto'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Fecha de inicio').": "."</span><br/>
					<input type='text' id='fecha_inicio_valor_adicional_proyecto'
						class='TLNT_input_valid_characters input-administracion' value='".$cadena_fecha_inicio_local_local."'>
                    <span id='boton_proyectos_ayuda_fecha_inicio_valor_adicional_proyecto' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
				</div>
			</div>

            <div class='row-fluid' id='control_fecha_fin_valor_adicional_proyecto'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Fecha de fin').": "."</span><br/>
					<input type='text' id='fecha_fin_valor_adicional_proyecto'
						class='TLNT_input_valid_characters input-administracion' value='".$cadena_fecha_fin_local_local."'>
                    <span id='boton_proyectos_ayuda_fecha_fin_valor_adicional_proyecto' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Aplicar en intervalos sin valores").": "."</span><br/>
                    <select id='aplicar_intervalos_sin_valores_valor_adicional_proyecto' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($aplicar_intervalos_sin_valores);
        $contenido .= "
                    </select>
                </div>
            </div>";

        return ("OK");
    }
?>
