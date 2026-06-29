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
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/ElementoPlantillaInforme.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Parametros/util_administracion_parametros.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_PARAMETRO_PLANTILLA_INFORME, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_plantilla_informe = $_POST["id_plantilla_informe"];
    $id_parametro = $_POST["id_parametro"];
    if ($id_parametro === NULL)
    {
        $id_parametro = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Añadir o modificar parámetro
    $anyadir_parametro = (($id_parametro == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
    if ($anyadir_parametro == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_personal_anyadir_modificar_parametro_plantilla_informe">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= " ".$idiomas->_("parámetro");
    if (($anyadir_parametro == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
    {
        $titulo .= " (".$idiomas->_("duplicar").")";
    }

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_parametro($anyadir_parametro, $id_plantilla_informe, $id_parametro, $contenido);
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
	// Funciones para mostrar el contenido de la ventana de anyadir/modificar parámetro
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar parámetro
	function rellena_contenido_ventana_anyadir_modificar_parametro($anyadir_parametro, $id_plantilla_informe, $id_parametro, &$contenido)
	{
		$idiomas = new Idiomas();
		$bd_red = BaseDatosRed::dame_base_datos();

        // Inicialización de variables necesarias
        $clase_sensor = CLASE_NINGUNA;
        $clase_actuador = CLASE_NINGUNA;

        // Si hay que modificar el parámetro (o es un duplicado), se recupera la información actual de la base de datos
        if ($id_parametro != ID_NINGUNO)
		{
			$consulta_parametro = "
				SELECT *
				FROM parametros_plantillas_informes
				WHERE
					id = '".$bd_red->_($id_parametro)."'";
			$res_parametro = $bd_red->ejecuta_consulta($consulta_parametro);
			if (($res_parametro == false) || ($res_parametro->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_parametro."'");
            }
			$fila_parametro = $res_parametro->dame_siguiente_fila();

            $nombre = $fila_parametro["nombre"];
            $tipo = $fila_parametro["tipo"];
            $cadena_parametros_tipo = $fila_parametro["parametros_tipo"];

            // Se recuperan los parámetros de tipo del elemento
            $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_tipo);
            switch ($tipo)
            {
                case TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR:
                {
                    $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR_CLASE_SENSOR];
                    $id_parametro_sensor_asociado = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_SENSOR_ID_PARAMETRO_SENSOR_ASOCIADO];
                    break;
                }
                case TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES:
                {
                    $clase_sensor = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_SENSORES_CLASE_SENSOR];
                    break;
                }
                case TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR:
                {
                    $clase_actuador = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_ACTUADOR_CLASE_ACTUADOR];
                    break;
                }
                case TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES:
                {
                    $clase_actuador = $parametros_tipo[INDICE_PARAMETRO_TIPO_PARAMETRO_PLANTILLA_INFORME_GRUPO_ACTUADORES_CLASE_ACTUADOR];
                    break;
                }
            }
		}
        else
        {
            $nombre = "";
            $tipo = TIPO_NINGUNO;
            $clase_sensor = CLASE_NINGUNA;
            $id_parametro_sensor_asociado = ID_NINGUNO;
            $clase_actuador = CLASE_NINGUNA;
        }

        // Se muestran las pestañas
        $contenido = "
            <div id='tabs-administracion-parametros-plantillas-informes' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-sensor' id='titulo-tab-tipo-sensor'>".$idiomas->_("Sensor")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grupo-sensores' id='titulo-tab-tipo-grupo-sensores'>".$idiomas->_("Grupo de sensores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-actuador' id='titulo-tab-tipo-actuador'>".$idiomas->_("Actuador")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-grupo-actuadores' id='titulo-tab-tipo-grupo-actuadores'>".$idiomas->_("Grupo de actuadores")."</a></li>
                </ul>
                <div id='tabs-content-administracion-parametro-plantillas-informes' class='tab-content'>";

        // Contenido de pestaña principal
        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

        // Contenido de pestaña principal
        $mostrar_identificador = false;
        if ($anyadir_parametro == false)
        {
            switch ($_SESSION["perfil"])
            {
                case PERFIL_USUARIO_ESTANDAR:
                {
                    if ($_SESSION["utilizada_contrasenya_admin_superadmin"] == true)
                    {
                        $mostrar_identificador = true;
                    }
                    break;
                }
                case PERFIL_USUARIO_ADMINISTRADOR:
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    $mostrar_identificador = true;
                    break;
                }
            }
        }
        if ($mostrar_identificador == true)
        {
            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Identificador").": "."</span><br/>
                        <input type='text' id='id_parametro_plantilla_informe'
                            class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".$id_parametro."' disabled>
                    </div>
                </div>";
        }

        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
					<input type='text' id='nombre_parametro_plantilla_informe'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
                    <select id='tipo_parametro_plantilla_informe' class='select-administracion'";

        // No se puede modificar el tipo de parámetro
        if ($anyadir_parametro == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";

        $tipos_parametro_disponibles = dame_tipos_parametro_plantillas_informes_disponibles();
        $contenido .= dame_lista_valores($tipos_parametro_disponibles, array($tipo));
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de tipo 'Sensor'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-sensor'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_parametro_plantilla_informe_sensor' class='select-administracion'";

        // No se puede modificar la clase de sensor
        if ($anyadir_parametro == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";

        $contenido .= dame_lista_clases_sensor($clase_sensor, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid' id='control_id_parametro_sensor_asociado_parametro_plantilla_informe_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Parámetro asociado").": "."</span><br/>
                    <select id='id_parametro_sensor_asociado_parametro_plantilla_informe_sensor' class='select-administracion'>";
        $contenido .= dame_lista_parametros_sensores_asociados_parametro_plantilla_informe(
            $id_plantilla_informe,
            $clase_sensor,
            $id_parametro_sensor_asociado);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de tipo 'Grupo de sensores'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-grupo-sensores'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de sensor").": "."</span><br/>
                    <select id='clase_sensor_parametro_plantilla_informe_grupo_sensores' class='select-administracion'";

        // No se puede modificar la clase de sensor
        if ($anyadir_parametro == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";

        $contenido .= dame_lista_clases_sensor($clase_sensor, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de tipo 'Actuador'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-actuador'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de actuador").": "."</span><br/>
                    <select id='clase_actuador_parametro_plantilla_informe_actuador' class='select-administracion'";

        // No se puede modificar la clase de sensor
        if ($anyadir_parametro == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";

        $contenido .= dame_lista_clases_actuador($clase_actuador, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de tipo 'Grupo de actuadores'
        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-grupo-actuadores'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase de actuador").": "."</span><br/>
                    <select id='clase_actuador_parametro_plantilla_informe_grupo_actuadores' class='select-administracion'";

        // No se puede modificar la clase de sensor
        if ($anyadir_parametro == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";

        $contenido .= dame_lista_clases_actuador($clase_actuador, false, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
		$contenido .= "
                    </select>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Se añaden los parámetros (no visibles) en un 'div' oculto
        $contenido .= '
            <div id="parametros_ventana_anyadir_modificar_parametro_plantilla_informe"
                anyadir_parametro="'.$anyadir_parametro.'"
                id_plantilla_informe="'.$id_plantilla_informe.'"
                id_parametro="'.$id_parametro.'"
                tipo="'.$tipo.'"
                parametros_tipo="'.$cadena_parametros_tipo.'"
                hidden>
            </div>';

        return ("OK");
	}
?>
