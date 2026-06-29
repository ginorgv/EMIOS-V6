<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_interfaces_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_sensores_externos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_interfaces_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/util_tarifas_gas_Espanya.php');


    $log = dame_log();
	$idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_NODO, $_POST);

    // Parámetros
    $tipo_nodo = $_POST["tipo_nodo"];
    $id_nodo = $_POST["id_nodo"];
    if ($id_nodo === NULL)
    {
        $id_nodo = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Añadir o modificar nodo
    $anyadir_nodo = (($id_nodo == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
    if ($anyadir_nodo == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_anyadir_modificar_nodo">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título y contenido de la ventana
    // (se muestra un contenido diferente en la ventana modal dependiendo del tipo de nodo)
    switch ($tipo_nodo)
    {
        case TIPO_NODO_RED:
        {
            $titulo .= " ".$idiomas->_("red");
            $error = rellena_contenido_ventana_anyadir_modificar_red($anyadir_nodo, $id_nodo, $contenido);
            break;
        }
        case TIPO_NODO_DISPOSITIVO:
        {
            $titulo .= " ".$idiomas->_("dispositivo");
            $error = rellena_contenido_ventana_anyadir_modificar_dispositivo($anyadir_nodo, $id_nodo, $contenido);
            break;
        }
        case TIPO_NODO_AXON:
        {
            $titulo .= " ".$idiomas->_("axón");
            $error = rellena_contenido_ventana_anyadir_modificar_axon($anyadir_nodo, $id_nodo, $contenido);
            break;
        }
        case TIPO_NODO_SENSOR:
        {
            $titulo .= " ".$idiomas->_("sensor");
            $error = rellena_contenido_ventana_anyadir_modificar_sensor($anyadir_nodo, $id_nodo, $contenido);
            break;
        }
        case TIPO_NODO_GRUPO_SENSORES:
        {
            $titulo .= " ".$idiomas->_("grupo de sensores");
            $error = rellena_contenido_ventana_anyadir_modificar_grupo_sensores($anyadir_nodo, $id_nodo, $contenido);
            break;
        }
        case TIPO_NODO_ACTUADOR:
        {
            $titulo .= " ".$idiomas->_("actuador");
            $error = rellena_contenido_ventana_anyadir_modificar_actuador($anyadir_nodo, $id_nodo, $contenido);
            break;
        }
        case TIPO_NODO_GRUPO_ACTUADORES:
        {
            $titulo .= " ".$idiomas->_("grupo de actuadores");
            $error = rellena_contenido_ventana_anyadir_modificar_grupo_actuadores($anyadir_nodo, $id_nodo, $contenido);
            break;
        }
        default:
        {
            $error .= "Tipo nodo: '".$tipo_nodo."' no implementado";
        }
    }
    if (($anyadir_nodo == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
    {
        $titulo .= " (".$idiomas->_("duplicar").")";
    }

    if ($error == "OK")
    {
        $res = "OK";
    }
    else
    {
        $res = "ERROR";
        $msg = $error;
    }

    // Flag para mostrar los controles de localización de los nodos
    switch ($tipo_nodo)
    {
        case TIPO_NODO_SENSOR:
        case TIPO_NODO_GRUPO_SENSORES:
        case TIPO_NODO_ACTUADOR:
        case TIPO_NODO_GRUPO_ACTUADORES:
        {
            $mostrar_controles_localizaciones = dame_mostrar_controles_localizaciones();
            break;
        }
        default:
        {
            $mostrar_controles_localizaciones = false;
            break;
        }
    }

    // Se añaden los parámetros (no visibles) en un 'div' oculto
    if ($mostrar_controles_localizaciones == true)
    {
        $valor_mostrar_controles_localizaciones = VALOR_SI;
    }
    else
    {
        $valor_mostrar_controles_localizaciones = VALOR_NO;
    }
    $contenido .= '
        <div id="parametros_ventana_anyadir_modificar_nodo"
            tipo_nodo="'.$tipo_nodo.'"
            anyadir_nodo="'.$anyadir_nodo.'"
            id_nodo="'.$id_nodo.'"
            mostrar_controles_localizaciones="'.$valor_mostrar_controles_localizaciones.'"
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
	// Funciones para mostrar el contenido de las ventanas de anyadir/modificar nodos
	//


    function rellena_contenido_ventana_anyadir_modificar_red($anyadir_nodo, $id_nodo, &$contenido)
	{
        $idiomas = new Idiomas();
		$bd_red = BaseDatosRed::dame_base_datos();

        // Si se añade la red, se recupera el siguiente identificador de red (el máximo más 1)
        if ($anyadir_nodo == true)
        {
            $consulta_id_red = "
                SELECT
                    MAX(id) AS id
                FROM redes";
            $res_id_red = $bd_red->ejecuta_consulta($consulta_id_red);
            if ($res_id_red == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_id_red."'");
            }
            $fila_id_red = $res_id_red->dame_siguiente_fila();
            $id_red = $fila_id_red["id"];
            if ($id_red === NULL)
            {
                $siguiente_id_red = 1;
            }
            else
            {
                $siguiente_id_red = $id_red + 1;
            }
        }

        // Si hay que modificar la red (o es un duplicado), se recupera la información actual de la base de datos
		if ($id_nodo != ID_NINGUNO)
		{
            $fila_red = dame_fila_red($id_nodo);

            // Principal
			$nombre = htmlspecialchars($fila_red["nombre"], ENT_QUOTES);
			$id_cliente = htmlspecialchars( $fila_red["cliente"], ENT_QUOTES);
            $zona_horaria = $fila_red["zona_horaria"];
            $idioma = $fila_red["idioma"];

            // Local
            $tipo_formato_fecha_local = $fila_red["tipo_formato_fecha_local"];
            $separador_miles = $fila_red["separador_miles"];
            $id_separador_miles = dame_id_separador_miles($separador_miles);
            $punto_decimal = $fila_red["punto_decimal"];
            $id_punto_decimal = dame_id_punto_decimal($punto_decimal);
            $unidades_medida = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_red["unidades_medida"]);
            $moneda = $unidades_medida[INDICE_UNIDADES_MEDIDA_RED_MONEDA];
            $unidad_medida_temperatura = $unidades_medida[INDICE_UNIDADES_MEDIDA_RED_TEMPERATURA];
            $unidad_medida_velocidad = $unidades_medida[INDICE_UNIDADES_MEDIDA_RED_VELOCIDAD];
            $paises_tarifas = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_red["paises_tarifas"]);
            $pais_tarifas_electricas = $paises_tarifas[INDICE_PAISES_TARIFAS_RED_PAIS_TARIFAS_ELECTRICAS];
            $pais_tarifas_gas = $paises_tarifas[INDICE_PAISES_TARIFAS_RED_PAIS_TARIFAS_GAS];
            $pais_tarifas_agua = $paises_tarifas[INDICE_PAISES_TARIFAS_RED_PAIS_TARIFAS_AGUA];
            $medicion_defecto = $fila_red["medicion_defecto"];

            // Procesado
            $procesado_cuartohorario = $fila_red["procesado_cuartohorario"];

            // Caducidad de valores
            $parametros_caducidad_valores = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_red["parametros_caducidad_valores"]);
            $numero_meses_valores_tiempo_real = $parametros_caducidad_valores[INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_NUMERO_MESES_VALORES_TIEMPO_REAL];
            $numero_meses_valores_cuartoshora = $parametros_caducidad_valores[INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_NUMERO_MESES_VALORES_CUARTOSHORA];
            $numero_meses_valores_horas = $parametros_caducidad_valores[INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_NUMERO_MESES_VALORES_HORAS];
            $numero_meses_valores_dias = $parametros_caducidad_valores[INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_NUMERO_MESES_VALORES_DIAS];
            $numero_meses_valores_meses = $parametros_caducidad_valores[INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_NUMERO_MESES_VALORES_MESES];
            $enviar_valores_caducados_tiempo_real = $parametros_caducidad_valores[INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_ENVIAR_VALORES_CADUCADOS_TIEMPO_REAL];
            $enviar_valores_caducados_cuartoshora = $parametros_caducidad_valores[INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_ENVIAR_VALORES_CADUCADOS_CUARTOSHORA];
            $enviar_valores_caducados_horas = $parametros_caducidad_valores[INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_ENVIAR_VALORES_CADUCADOS_HORAS];
            $direccion_email_envio_valores_caducados = $parametros_caducidad_valores[INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_DIRECCION_EMAIL_ENVIO_VALORES_CADUCADOS];
            $numero_meses_acciones_usuario = $parametros_caducidad_valores[INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_NUMERO_MESES_ACCIONES_USUARIO];
            $numero_meses_activaciones = $parametros_caducidad_valores[INDICE_PARAMETRO_CADUCIDAD_VALORES_RED_NUMERO_MESES_ACTIVACIONES];

            // Notificaciones
            $direcciones_email_envio_notificaciones = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_red["direcciones_email_envio_notificaciones"]);
            $direccion_email_envio_validaciones_facturas = $direcciones_email_envio_notificaciones[INDICE_DIRECCION_EMAIL_ENVIO_NOTIFICACIONES_RED_VALIDACIONES_FACTURAS];
            $direccion_email_envio_avisos_expiraciones_tarifas = $direcciones_email_envio_notificaciones[INDICE_DIRECCION_EMAIL_ENVIO_NOTIFICACIONES_RED_AVISOS_EXPIRACIONES_TARIFAS];
            $direccion_email_envio_avisos_timeouts_envio_sensores_error_valores = $direcciones_email_envio_notificaciones[INDICE_DIRECCION_EMAIL_ENVIO_NOTIFICACIONES_RED_AVISOS_TIMEOUTS_ENVIO_SENSORES_ERROR_VALORES];
            $direccion_email_envio_avisos_eventos_activados = $direcciones_email_envio_notificaciones[INDICE_DIRECCION_EMAIL_ENVIO_NOTIFICACIONES_RED_AVISOS_EVENTOS_ACTIVADOS];
            $direccion_email_envio_avisos_actuadores_error_reglas_activadas = $direcciones_email_envio_notificaciones[INDICE_DIRECCION_EMAIL_ENVIO_NOTIFICACIONES_RED_AVISOS_ACTUADORES_ERROR_REGLAS_ACTIVADAS];
            $direccion_email_envio_validaciones_facturas = str_replace(" ", "", $direccion_email_envio_validaciones_facturas);
            $direccion_email_envio_avisos_expiraciones_tarifas = str_replace(" ", "", $direccion_email_envio_avisos_expiraciones_tarifas);
            $direccion_email_envio_avisos_timeouts_envio_sensores_error_valores = str_replace(" ", "", $direccion_email_envio_avisos_timeouts_envio_sensores_error_valores);
            $direccion_email_envio_avisos_eventos_activados = str_replace(" ", "", $direccion_email_envio_avisos_eventos_activados);
            $direccion_email_envio_avisos_actuadores_error_reglas_activadas = str_replace(" ", "", $direccion_email_envio_avisos_actuadores_error_reglas_activadas);
            $direccion_email_envio_validaciones_facturas = str_replace(SEPARADOR_DIRECCIONES_EMAIL, SEPARADOR_DIRECCIONES_EMAIL." ", $direccion_email_envio_validaciones_facturas);
            $direccion_email_envio_avisos_expiraciones_tarifas = str_replace(SEPARADOR_DIRECCIONES_EMAIL, SEPARADOR_DIRECCIONES_EMAIL." ", $direccion_email_envio_avisos_expiraciones_tarifas);
            $direccion_email_envio_avisos_timeouts_envio_sensores_error_valores = str_replace(SEPARADOR_DIRECCIONES_EMAIL, SEPARADOR_DIRECCIONES_EMAIL." ", $direccion_email_envio_avisos_timeouts_envio_sensores_error_valores);
            $direccion_email_envio_avisos_eventos_activados = str_replace(SEPARADOR_DIRECCIONES_EMAIL, SEPARADOR_DIRECCIONES_EMAIL." ", $direccion_email_envio_avisos_eventos_activados);
            $direccion_email_envio_avisos_actuadores_error_reglas_activadas = str_replace(SEPARADOR_DIRECCIONES_EMAIL, SEPARADOR_DIRECCIONES_EMAIL." ", $direccion_email_envio_avisos_actuadores_error_reglas_activadas);

            // Dirección origen de 'e-mail' de informes automáticos
            $direccion_origen_email_informes_automaticos = $fila_red["direccion_origen_email_informes_automaticos"];

            // Dispositivos
            $version_fuentes = $fila_red["version_fuentes"];
            $version_fuentes_web = $fila_red["version_fuentes_web"];

            // Preferencias
            $logo_personalizado = $fila_red["logo_personalizado"];
            $nombre_logo = $fila_red["nombre_logo"];
            $url_logo = $fila_red["url_logo"];
            $titulo_web = $fila_red["titulo_web"];
            $tema = $fila_red["tema"];
            $paleta_colores_graficas = $fila_red["paleta_colores_graficas"];
            $periodo_completo_informes_defecto = $fila_red["periodo_completo_informes_defecto"];

            // Opciones de mapa
            $tipo_mapa = $fila_red["tipo_mapa"];
            $nombre_mapa = $fila_red["nombre_mapa"];
            $factor_reduccion_imagen_mapa_local = $fila_red["factor_reduccion_imagen_mapa_local"];
            $etiquetas_mapa = $fila_red["etiquetas_mapa"];

            // Mapa (posición y zoom por defecto)
            $latitud_mapa_defecto = $fila_red["latitud_mapa_defecto"];
            $longitud_mapa_defecto = $fila_red["longitud_mapa_defecto"];
            $zoom_mapa_defecto = $fila_red["zoom_mapa_defecto"];

            // Si es un duplicado, se establece el siguiente identificador de red
            if ($anyadir_nodo == true)
            {
                $id_nodo = $siguiente_id_red;
            }
		}
        else
        {
            // Principal
            $zona_horaria = "Europe/Madrid";
            $idioma = "en_ES";

            // Local
            $tipo_formato_fecha_local = TIPO_FORMATO_FECHA_LOCAL_DEFECTO;
            $id_separador_miles = ID_SEPARADOR_MILES_DEFECTO;
            $id_punto_decimal = ID_PUNTO_DECIMAL_DEFECTO;
            $moneda = MONEDA_DEFECTO;
            $unidad_medida_temperatura = UNIDAD_MEDIDA_TEMPERATURA_DEFECTO;
            $unidad_medida_velocidad = UNIDAD_MEDIDA_VELOCIDAD_DEFECTO;
            $pais_tarifas_electricas = PAIS_TARIFAS_ELECTRICAS_DEFECTO;
            $pais_tarifas_gas = PAIS_TARIFAS_GAS_DEFECTO;
            $pais_tarifas_agua = PAIS_TARIFAS_AGUA_DEFECTO;
            $medicion_defecto = MEDICION_DEFECTO;

            // Procesado
            $procesado_cuartohorario = VALOR_NO;

            // Caducidad de valores
            $numero_meses_valores_tiempo_real = NUMERO_MESES_VALORES_TIEMPO_REAL_DEFECTO;
            $numero_meses_valores_cuartoshora = NUMERO_MESES_VALORES_CUARTOSHORA_DEFECTO;
            $numero_meses_valores_horas = NUMERO_MESES_VALORES_HORAS_DEFECTO;
            $numero_meses_valores_dias = NUMERO_MESES_VALORES_DIAS_DEFECTO;
            $numero_meses_valores_meses = NUMERO_MESES_DEFECTO_VALORES_MESES;
            $enviar_valores_caducados_tiempo_real = ENVIAR_VALORES_CADUCADOS_TIEMPO_REAL_DEFECTO;
            $enviar_valores_caducados_cuartoshora = ENVIAR_VALORES_CADUCADOS_CUARTOSHORA_DEFECTO;
            $enviar_valores_caducados_horas = ENVIAR_VALORES_CADUCADOS_HORAS_DEFECTO;
            $direccion_email_envio_valores_caducados = DIRECCION_EMAIL_ENVIO_VALORES_CADUCADOS_DEFECTO;
            $numero_meses_acciones_usuario = NUMERO_MESES_ACCIONES_USUARIO_DEFECTO;
            $numero_meses_activaciones = NUMERO_MESES_ACTIVACIONES_DEFECTO;

            // Notificaciones
            $direccion_email_envio_validaciones_facturas = "";
            $direccion_email_envio_avisos_expiraciones_tarifas = "";
            $direccion_email_envio_avisos_timeouts_envio_sensores_error_valores = "";
            $direccion_email_envio_avisos_eventos_activados = "";
            $direccion_email_envio_avisos_actuadores_error_reglas_activadas = "";

            // Dirección origen de 'e-mail' de informes automáticos
            $direccion_origen_email_informes_automaticos = "";

            // Dispositivos
            $version_fuentes = VERSION_FUENTES_DISPOSITIVOS_DEFECTO;
            $version_fuentes_web = VERSION_FUENTES_WEB_DISPOSITIVOS_DEFECTO;

            // Preferencias
            $logo_personalizado = VALOR_NO;
            $nombre_logo = "";
            $url_logo = "";
            $titulo_web = "";
            $tema = TEMA_DEFECTO;
            $paleta_colores_graficas = PALETA_COLORES_GRAFICAS_DEFECTO;
            $periodo_completo_informes_defecto = VALOR_SI;

            // Opciones de mapa
            $tipo_mapa = TIPO_MAPA_INTERNET;
            $factor_reduccion_imagen_mapa_local = 1;
            $etiquetas_mapa = VALOR_SI;

            // Mapa (posición y zoom por defecto)
            $latitud_mapa_defecto = 0.0;
            $longitud_mapa_defecto = 0.0;
            $zoom_mapa_defecto = ZOOM_MAPA_DEFECTO;

            // Se establece el siguiente identificador de red
            $id_nodo = $siguiente_id_red;
        }

        // Se muestran las siguientes pestañas:
        // - Principal, procesado de datos, caducidad de valores, notificaciones, pasarelas, local, preferencias, opciones de mapa y mapa
        $contenido = "
            <div id='tabs-administracion-red' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-local' id='titulo-tab-local'>".$idiomas->_("Local")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-procesado' id='titulo-tab-procesado'>".$idiomas->_("Procesado de datos")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-caducidad-valores' id='titulo-tab-caducidad-valores'>".$idiomas->_("Caducidad de valores")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-notificaciones' id='titulo-tab-local'>".$idiomas->_("Notificaciones")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-pasarelas' id='titulo-tab-pasarelas'>".$idiomas->_("Pasarelas")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-preferencias' id='titulo-tab-preferencias'>".$idiomas->_("Preferencias")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-opciones-mapa' id='titulo-tab-opciones-mapa'>".$idiomas->_("Opciones de mapa")."</a></li>";

        // Si se está añadiendo, no se muestra la pestaña de mapa
        if ($anyadir_nodo == false)
        {
            $contenido .= "
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-mapa' id='titulo-tab-mapa'>".$idiomas->_("Mapa")."</a></li>";
        }

        $contenido .= "
                </ul>
                <div id='tabs-content-administracion-red' class='tab-content'>";

        // Contenido de pestaña principal
        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

		$contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Identificador").": "."</span><br/>
					<input type='text' id='id_red'
						class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$id_nodo."'";
        // No se permite cambiar el id de la red si se está modificando
        if ($anyadir_nodo == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">
				</div>
			</div>

			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
					<input type='text' id='nombre_red'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Cliente").": "."</span><br/>
					<select id='id_cliente_red' class='select-administracion'>";
        $contenido .= dame_lista_clientes($id_cliente);
		$contenido .= "
					</select>
				</div>
			</div>";

        // No se permite cambiar la zona horaria de la red si se está modificando
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Zona horaria").": "."</span><br/>
					<select id='zona_horaria_red' class='select-administracion'";
        if ($anyadir_nodo == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_zonas_horarias($zona_horaria);
        $contenido .= "
					</select>
				</div>
			</div>";

        // No se permite cambiar el idioma de la red si se está modificando
        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Idioma").": "."</span><br/>
					<select id='idioma_red' class='select-administracion'";
        if ($anyadir_nodo == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_idiomas($idioma);
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña local
        $contenido .= "
                    <div class='tab-pane' id='tab-local'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Formato de fecha").": "."</span><br/>
					<select id='tipo_formato_fecha_local_red' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(TIPO_FORMATO_FECHA_LOCAL_DIA_MES_ANYO, $idiomas->_("día/mes/año")),
                array(TIPO_FORMATO_FECHA_LOCAL_MES_DIA_ANYO, $idiomas->_("mes/día/año")),
                array(TIPO_FORMATO_FECHA_LOCAL_ANYO_MES_DIA, $idiomas->_("año/mes/día"))),
            array($tipo_formato_fecha_local));
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Separador de miles").": "."</span><br/>
					<select id='id_separador_miles_red' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(ID_SEPARADOR_MILES_PUNTO, dame_descripcion_id_separador_miles(ID_SEPARADOR_MILES_PUNTO)),
                array(ID_SEPARADOR_MILES_COMA, dame_descripcion_id_separador_miles(ID_SEPARADOR_MILES_COMA)),
                array(ID_SEPARADOR_MILES_ESPACIO, dame_descripcion_id_separador_miles(ID_SEPARADOR_MILES_ESPACIO))),
            array($id_separador_miles));
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Punto decimal").": "."</span><br/>
					<select id='id_punto_decimal_red' class='select-administracion'>";
        $contenido .= dame_lista_valores(
            array(
                array(ID_PUNTO_DECIMAL_COMA, dame_descripcion_id_punto_decimal(ID_PUNTO_DECIMAL_COMA)),
                array(ID_PUNTO_DECIMAL_PUNTO, dame_descripcion_id_punto_decimal(ID_PUNTO_DECIMAL_PUNTO))),
            array($id_punto_decimal));
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Moneda").": "."</span><br/>
					<input type='text' id='moneda_red'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($moneda, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Unidad de medida de temperatura").": "."</span><br/>
					<input type='text' id='unidad_medida_temperatura_red'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($unidad_medida_temperatura, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Unidad de medida de velocidad").": "."</span><br/>
					<input type='text' id='unidad_medida_velocidad_red'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($unidad_medida_velocidad, ENT_QUOTES)."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tarifas eléctricas").": "."</span><br/>
					<select id='pais_tarifas_electricas_red' class='select-administracion'";
        // No se permite cambiar el país de tarifas si se está modificando y es diferente de ninguno
        if (($anyadir_nodo == false) && ($pais_tarifas_electricas != PAIS_NINGUNO))
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_valores(
            array(
                array(PAIS_NINGUNO, $idiomas->_("Ninguno")),
                array(PAIS_ESPANYA, $idiomas->_("España")),
                array(PAIS_PORTUGAL, $idiomas->_("Portugal"))),
            array($pais_tarifas_electricas));
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tarifas de gas").": "."</span><br/>
					<select id='pais_tarifas_gas_red' class='select-administracion'";
        // No se permite cambiar el país de tarifas si se está modificando y es diferente de ninguno
        if (($anyadir_nodo == false) && ($pais_tarifas_electricas != PAIS_NINGUNO))
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_valores(
            array(
                array(PAIS_NINGUNO, $idiomas->_("Ninguno")),
                array(PAIS_ESPANYA, $idiomas->_("España"))),
            array($pais_tarifas_gas));
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tarifas de agua").": "."</span><br/>
					<select id='pais_tarifas_agua_red' class='select-administracion'";
        // No se permite cambiar el país de tarifas si se está modificando y es diferente de ninguno
        if (($anyadir_nodo == false) && ($pais_tarifas_electricas != PAIS_NINGUNO))
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_valores(
            array(
                array(PAIS_NINGUNO, $idiomas->_("Ninguno")),
                array(PAIS_ESPANYA, $idiomas->_("España"))),
            array($pais_tarifas_agua));
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Medición por defecto").": "."</span><br/>
					<select id='medicion_defecto_red' class='select-administracion'>";
        $contenido .= dame_lista_mediciones($medicion_defecto, OPCIONES_EXTRA_LISTA_MEDICIONES_TODAS);
		$contenido .= "
					</select>
				</div>
			</div>";

		$contenido .= "
                    </div>";

        // Contenido de pestaña de procesado
        $contenido .= "
                    <div class='tab-pane' id='tab-procesado'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Procesado cuartohorario").": "."</span><br/>
					<select id='procesado_cuartohorario_red' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($procesado_cuartohorario);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de caducidad de valores
        $contenido .= "
                    <div class='tab-pane' id='tab-caducidad-valores'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de meses de valores en tiempo real").": "."</span><br/>
					<input type='text' id='numero_meses_valores_tiempo_real_red'
						class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_meses_valores_tiempo_real."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de meses de valores cuartohorarios").": "."</span><br/>
					<input type='text' id='numero_meses_valores_cuartoshora_red'
						class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_meses_valores_cuartoshora."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de meses de valores horarios").": "."</span><br/>
					<input type='text' id='numero_meses_valores_horas_red'
						class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_meses_valores_horas."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de meses de valores diarios").": "."</span><br/>
					<input type='text' id='numero_meses_valores_dias_red'
						class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_meses_valores_dias."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de meses de valores mensuales").": "."</span><br/>
					<input type='text' id='numero_meses_valores_meses_red'
						class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_meses_valores_meses."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Enviar valores en tiempo real caducados").": "."</span><br/>
					<select id='enviar_valores_caducados_tiempo_real_red' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($enviar_valores_caducados_tiempo_real);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Enviar valores cuartohorarios caducados").": "."</span><br/>
					<select id='enviar_valores_caducados_cuartoshora_red' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($enviar_valores_caducados_cuartoshora);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Enviar valores horarios caducados").": "."</span><br/>
					<select id='enviar_valores_caducados_horas_red' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($enviar_valores_caducados_horas);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección e-mail de envío de valores caducados").": "."</span><br/>
                    <input type='text' id='direccion_email_envio_valores_caducados_red'
                        class='input-administracion' value='".$direccion_email_envio_valores_caducados."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de meses de acciones de usuario").": "."</span><br/>
					<input type='text' id='numero_meses_acciones_usuario_red'
						class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_meses_acciones_usuario."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de meses de activaciones").": "."</span><br/>
					<input type='text' id='numero_meses_activaciones_red'
						class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$numero_meses_activaciones."'>
				</div>
			</div>";

		$contenido .= "
                    </div>";

        // Contenido de pestaña de notificaciones
        $contenido .= "
                    <div class='tab-pane' id='tab-notificaciones'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección e-mail de envío de validaciones automáticas de facturas y cierres").": "."</span><br/>
                    <input type='text' id='direccion_email_envio_validaciones_automaticas_facturas_red'
                        class='input-administracion' value='".$direccion_email_envio_validaciones_facturas."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección e-mail de envío de avisos de expiraciones de tarifas").": "."</span><br/>
                    <input type='text' id='direccion_email_envio_avisos_expiraciones_tarifas_red'
                        class='input-administracion' value='".$direccion_email_envio_avisos_expiraciones_tarifas."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección e-mail de envío de avisos de timeouts de envío de sensores y sensores con error de valores").": "."</span><br/>
                    <input type='text' id='direccion_email_envio_avisos_timeouts_envio_sensores_error_valores_red'
                        class='input-administracion' value='".$direccion_email_envio_avisos_timeouts_envio_sensores_error_valores."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección e-mail de envío de avisos de eventos activados").": "."</span><br/>
                    <input type='text' id='direccion_email_envio_avisos_eventos_activados_red'
                        class='input-administracion' value='".$direccion_email_envio_avisos_eventos_activados."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección e-mail de envío de avisos de actuadores con error y reglas activadas").": "."</span><br/>
                    <input type='text' id='direccion_email_envio_avisos_actuadores_error_reglas_activadas_red'
                        class='input-administracion' value='".$direccion_email_envio_avisos_actuadores_error_reglas_activadas."'>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección origen e-mail de informes automáticos").": "."</span><br/>
                    <input type='text' id='direccion_origen_email_informes_automaticos_red'
                        class='input-administracion' value='".$direccion_origen_email_informes_automaticos."'>
                </div>
            </div>";

		$contenido .= "
                    </div>";

        // Contenido de pestaña de pasarelas
        $contenido .= "
                    <div class='tab-pane' id='tab-pasarelas'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Versión de fuentes").": "."</span><br/>
					<input type='text' id='version_fuentes_red'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($version_fuentes, ENT_QUOTES)."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Versión de fuentes web").": "."</span><br/>
					<input type='text' id='version_fuentes_web_red'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($version_fuentes_web, ENT_QUOTES)."'>
				</div>
			</div>";

		$contenido .= "
                    </div>";

        // Contenido de pestaña de preferencias
        $contenido .= "
                    <div class='tab-pane' id='tab-preferencias'>";
        $contenido .= dame_controles_red_pestanya_preferencias(
            $anyadir_nodo,
            $id_nodo,
            $nombre,
            $logo_personalizado,
            $nombre_logo,
            $url_logo,
            $titulo_web,
            $tema,
            $paleta_colores_graficas,
            $periodo_completo_informes_defecto);
        $contenido .= "
                    </div>";

        // Contenido de pestaña de opciones de mapa
        $contenido .= "
                    <div class='tab-pane' id='tab-opciones-mapa'>";
        $contenido .= dame_controles_red_pestanya_opciones_mapa(
            $anyadir_nodo,
            $id_nodo,
            $nombre,
            $tipo_mapa,
            $nombre_mapa,
            $factor_reduccion_imagen_mapa_local,
            $etiquetas_mapa);
        $contenido .= "
                    </div>";

        // Mapa (posición y zoom por defecto)
        $contenido .= "
                    <div class='tab-pane' id='tab-mapa'>";
		$contenido .= dame_localizador_mapa(
            "_defecto",
            ORIGEN_MAPA_RED,
            $id_nodo,
            $latitud_mapa_defecto,
            $longitud_mapa_defecto,
            $zoom_mapa_defecto);
        $contenido .= "
                    </div>";

        // Se añaden los parámetros (no visibles) específicos de la red en un 'div' oculto
        $contenido .= '
            <div id="parametros_ventana_anyadir_modificar_red"
                logo_personalizado="'.$logo_personalizado.'"
                pais_tarifas_electricas="'.$pais_tarifas_electricas.'"
                pais_tarifas_gas="'.$pais_tarifas_gas.'"
                pais_tarifas_agua="'.$pais_tarifas_agua.'"
                tipo_mapa="'.$tipo_mapa.'"
                nombre_mapa="'.$nombre_mapa.'"
                factor_reduccion_imagen_mapa_local="'.$factor_reduccion_imagen_mapa_local.'"
                hidden>
            </div>';

        return ("OK");
	}


    function rellena_contenido_ventana_anyadir_modificar_dispositivo($anyadir_nodo, $id_nodo, &$contenido)
	{
		$idiomas = new Idiomas();

		// Si hay que modificar el dispositivo, se recupera la información actual de la base de datos
		if ($anyadir_nodo == false)
		{
			$fila_dispositivo = dame_fila_dispositivo($id_nodo);

			$nombre = $fila_dispositivo["nombre"];
            $descripcion = $fila_dispositivo["descripcion"];
			$direccion_mac = $fila_dispositivo["direccion_mac"];
            $identificador_imei = $fila_dispositivo["imei"];
			$id_arquitectura = $fila_dispositivo["arquitectura"];
            $ip_local = $fila_dispositivo["ip_local"];
            $frecuencia_actualizacion = $fila_dispositivo["frecuencia_actualizacion"];
            $frecuencia_envio_estado = $fila_dispositivo["frecuencia_envio_estado"];
            $id_localizacion = $fila_dispositivo["localizacion"];

            // Posición en mapa
            $info_posicion_mapa = dame_info_posicion_mapa_base_datos(
                TIPO_ELEMENTO_MAPA_DISPOSITIVO,
                $id_nodo,
                ORIGEN_MAPA_RED,
                $_SESSION["id_red"]);
            if ($info_posicion_mapa === NULL)
            {
                // Se recupera la información del mapa de la red
                $fila_red = dame_fila_red($_SESSION["id_red"]);
                $mostrar_en_mapa = VALOR_NO;
                $latitud_mapa = $fila_red["latitud_mapa_defecto"];
                $longitud_mapa = $fila_red["longitud_mapa_defecto"];
                $zoom_mapa = $fila_red["zoom_mapa_defecto"];
            }
            else
            {
                $mostrar_en_mapa = VALOR_SI;
                $latitud_mapa = $info_posicion_mapa["latitud"];
                $longitud_mapa = $info_posicion_mapa["longitud"];
                $zoom_mapa = $info_posicion_mapa["zoom"];
            }
		}
        else
        {
            // Se recupera la información del mapa de la red
            $fila_red = dame_fila_red($_SESSION["id_red"]);
            $mostrar_en_mapa = VALOR_NO;
            $latitud_mapa = $fila_red["latitud_mapa_defecto"];
            $longitud_mapa = $fila_red["longitud_mapa_defecto"];
            $zoom_mapa = $fila_red["zoom_mapa_defecto"];
        }

        // Se muestran dos pestañas: Datos y posición en mapa
        $contenido = "
            <div id='tabs-administracion-dispositivo' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-datos'>".$idiomas->_("Datos")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-posicion-mapa' id='titulo-tab-posicion-mapa'>".$idiomas->_("Posición en mapa")."</a></li>
                </ul>
                <div id='tabs-content-administracion-dispositivo' class='tab-content'>";

        $contenido .= "
                    <div class='tab-pane active' id='tab-datos'>";

        // Contenido de pestaña de datos
		$contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
					<input type='text' id='nombre_dispositivo'
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
                    <textarea id='descripcion_dispositivo'
						class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Arquitectura").": "."</span><br/>
					<select id='id_arquitectura_dispositivo' class='select-administracion'>";
        $contenido .= dame_lista_arquitecturas_dispositivo($id_arquitectura);
        $contenido .= "
					</select>
				</div>
			</div>";
 // ANADIR CLASE TLNT_input_mandatory para que sea obligatorio rellenar el campo
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12' id='control_contenedor_direccion_mac'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección MAC").": "."</span><br/>
					<input type='text' id='direccion_mac_dispositivo'
						class='TLNT_input_mac input-administracion' value='".$direccion_mac."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12' id='control_contenedor_direccion_ip'><span class='titulo-campo-administracion'>".$idiomas->_("Dirección IP").": "."</span><br/>
					<input type='text' id='ip_local_dispositivo'
						class='TLNT_input_ip input-administracion' value='".$ip_local."'>
				</div>
			</div>
            <div class='row-fluid'>
				<div class='span12' id='control_contenedor_imei_dispositivo'><span class='titulo-campo-administracion'>".$idiomas->_("IMEI").": "."</span><br/>
					<input type='text' id='imei_dispositivo'
						class='input-administracion' value='".$identificador_imei."'>
				</div>
			</div>
            <div class='row-fluid'>
				<div class='span12' id='control_contenedor_localizacion_dispositivo'><span class='titulo-campo-administracion'>".$idiomas->_("Localización").": "."</span><br/>
					<select id='id_localizacion_dispositivo_radon' class='chosen-select-administracion'>";
        $contenido .= dame_lista_localizaciones(array($id_localizacion), OPCIONES_EXTRA_LISTA_LOCALIZACIONES_NINGUNA);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Frecuencia de actualización")." (".$idiomas->_("s")."): "."</span><br/>
					<input type='text' id='frecuencia_actualizacion_dispositivo'
						class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$frecuencia_actualizacion."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Frecuencia de envío de estado")." (".$idiomas->_("s")."): "."</span><br/>
					<input type='text' id='frecuencia_envio_estado'
						class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$frecuencia_envio_estado."'>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Posición en el mapa
        $contenido .= "
                    <div class='tab-pane' id='tab-posicion-mapa'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar en mapa").": "."</span><br/>
					<select id='mostrar_en_mapa' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($mostrar_en_mapa);
		$contenido .= "
					</select>
				</div>
			</div>";

        $parametros_origen_mapa = array("modulo" => MODULO_RED);
		$contenido .= dame_localizador_mapa(
            "",
            ORIGEN_MAPA_POSICION,
            $parametros_origen_mapa,
            $latitud_mapa,
            $longitud_mapa,
            $zoom_mapa);

        $contenido .=
                    "</div>";

        $contenido .= "
                </div>
            </div>";

        return ("OK");
	}


	function rellena_contenido_ventana_anyadir_modificar_axon($anyadir_nodo, $id_nodo, &$contenido)
	{
		$idiomas = new Idiomas();

		// Si hay que modificar el axon, se recupera la información actual de la base de datos
		if ($anyadir_nodo == false)
		{
			$fila_axon = dame_fila_axon($id_nodo);

			$nombre = $fila_axon["nombre"];
			$id_dispositivo = $fila_axon["dispositivo"];
		}

		$contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
					<input type='text' id='nombre_axon'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>";

		$contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Dispositivo").": "."</span><br/>
					<select id='id_dispositivo_axon' class='select-administracion'>";
        $contenido .= dame_lista_dispositivos($id_dispositivo);
		$contenido .= "
					</select>
				</div>
			</div>";

        return ("OK");
	}


    function rellena_contenido_ventana_anyadir_modificar_sensor($anyadir_nodo, $id_nodo, &$contenido)
	{
		$idiomas = new Idiomas();

        // Se recupera el origen del mapa 'final'
        $parametros_origen_mapa = array("modulo" => MODULO_SENSORES);
        $resultado_origen_mapa = dame_origen_mapa_final(ORIGEN_MAPA_POSICION, $parametros_origen_mapa);
        $origen_mapa = $resultado_origen_mapa["origen_mapa_final"];
        $id_origen_mapa = $resultado_origen_mapa["id_origen_mapa_final"];

		// Si hay que modificar el sensor (o es un duplicado), se recupera la información actual de la base de datos
		if ($id_nodo != ID_NINGUNO)
		{
            $fila_sensor = dame_fila_sensor($id_nodo);

			$nombre = $fila_sensor["nombre"];
            $descripcion = $fila_sensor["descripcion"];
            $id_localizacion = $fila_sensor["localizacion"];
            $visible_localizaciones_hijas = $fila_sensor["visible_localizaciones_hijas"];
			$clase = $fila_sensor["clase"];
            $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor["parametros_clase"]);
            $tipo = $fila_sensor["tipo"];
            $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor["parametros_tipo"]);
            $calibracion = formatea_calibracion($fila_sensor["calibracion"]);
            $tipo_valores = $fila_sensor["tipo_valores"];
            $cambio_valores_puntuales = $fila_sensor["cambio_valores_puntuales"];
            $incrementos_tiempo_real_horarios = $fila_sensor["incrementos_tiempo_real_horarios"];
            $incrementos_negativos_validos = $fila_sensor["incrementos_negativos_validos"];
            $granularidad_cuartohoraria = $fila_sensor["granularidad_cuartohoraria"];
            $guardar_valores_base_datos = $fila_sensor["guardar_valores_base_datos"];
            $notificar_todos_eventos = $fila_sensor["notificar_todos_eventos"];
            $id_grupo = $fila_sensor["grupo"];
            $frecuencia_muestreo = $fila_sensor["frecuencia_muestreo"];
            $frecuencia_envio = $fila_sensor["frecuencia_envio"];

            // Posición en mapa
            $info_posicion_mapa = dame_info_posicion_mapa_base_datos(
                TIPO_ELEMENTO_MAPA_SENSOR,
                $id_nodo,
                $origen_mapa,
                $id_origen_mapa);
            if ($info_posicion_mapa === NULL)
            {
                // Se recupera la información del mapa del origen correspondiente
                switch ($origen_mapa)
                {
                    case ORIGEN_MAPA_RED:
                    {
                        $fila_origen_mapa = dame_fila_red($id_origen_mapa);
                        break;
                    }
                    case ORIGEN_MAPA_LOCALIZACION:
                    {
                        $fila_origen_mapa = dame_fila_localizacion($id_origen_mapa);
                        break;
                    }
                    default:
                    {
                        throw new Exception("Origen de mapa desconocido: '".$origen_mapa."'");
                    }
                }
                $mostrar_en_mapa = VALOR_NO;
                $latitud_mapa = $fila_origen_mapa["latitud_mapa_defecto"];
                $longitud_mapa = $fila_origen_mapa["longitud_mapa_defecto"];
                $zoom_mapa = $fila_origen_mapa["zoom_mapa_defecto"];
            }
            else
            {
                $mostrar_en_mapa = VALOR_SI;
                $latitud_mapa = $info_posicion_mapa["latitud"];
                $longitud_mapa = $info_posicion_mapa["longitud"];
                $zoom_mapa = $info_posicion_mapa["zoom"];
            }
        }
        else
        {
            // Valores por defecto al añadir un sensor
            $id_localizacion = dame_localizacion_defecto_anyadir_nodo();
            $visible_localizaciones_hijas = VALOR_NO;
            $clase = CLASE_NINGUNA;
            $parametros_clase = NULL;
            $tipo = TIPO_NINGUNO;
            $parametros_tipo = NULL;
            $calibracion = "";
            $tipo_valores = TIPO_NINGUNO;
            $cambio_valores_puntuales = CAMBIO_VALORES_PUNTUALES_SENSOR_GRADUAL;
            $incrementos_tiempo_real_horarios = VALOR_SI;
            $incrementos_negativos_validos = VALOR_NO;
            $granularidad_cuartohoraria = VALOR_NO;
            $guardar_valores_base_datos = VALOR_SI;
            $notificar_todos_eventos = VALOR_SI;
            $id_grupo = ID_NINGUNO;

            // Se recupera la información del mapa del origen correspondiente
            switch ($origen_mapa)
            {
                case ORIGEN_MAPA_RED:
                {
                    $fila_origen_mapa = dame_fila_red($id_origen_mapa);
                    break;
                }
                case ORIGEN_MAPA_LOCALIZACION:
                {
                    $fila_origen_mapa = dame_fila_localizacion($id_origen_mapa);
                    break;
                }
                default:
                {
                    throw new Exception("Origen de mapa desconocido: '".$origen_mapa."'");
                }
            }
            $mostrar_en_mapa = VALOR_NO;
            $latitud_mapa = $fila_origen_mapa["latitud_mapa_defecto"];
            $longitud_mapa = $fila_origen_mapa["longitud_mapa_defecto"];
            $zoom_mapa = $fila_origen_mapa["zoom_mapa_defecto"];
        }

        // Se muestran las siguientes pestañas:
        // - Principal, consumo eléctrico, genérica, interfaz, virtual, procesado, externo, envío y localización en mapa
        $contenido = "
            <div id='tabs-administracion-sensor' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-clase-energia-activa' id='titulo-tab-clase-energia-activa'>".$idiomas->_("Energía activa")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-clase-energia-reactiva' id='titulo-tab-clase-energia-reactiva'>".$idiomas->_("Energía reactiva")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-clase-cortes-tension' id='titulo-tab-clase-cortes-tension'>".$idiomas->_("Cortes de tensión")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-clase-compra-energia' id='titulo-tab-clase-compra-energia'>".$idiomas->_("Compra de energía")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-clase-gas' id='titulo-tab-clase-gas'>".$idiomas->_("Gas")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-clase-agua' id='titulo-tab-clase-agua'>".$idiomas->_("Agua")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-clase-generica' id='titulo-tab-clase-generica'>".$idiomas->_("Genérica")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-real' id='titulo-tab-tipo-real'>".$idiomas->_("Real")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-virtual' id='titulo-tab-tipo-virtual'>".$idiomas->_("Virtual")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-procesado' id='titulo-tab-tipo-procesado'>".$idiomas->_("Procesado")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-tipo-externo' id='titulo-tab-tipo-externo'>".$idiomas->_("Externo")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-envio' id='titulo-tab-envio'>".$idiomas->_("Envío")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-posicion-mapa' id='titulo-tab-posicion-mapa'>".$idiomas->_("Posición en mapa")."</a></li>
                </ul>
                <div id='tabs-content-administracion-sensor' class='tab-content'>";

        // Contenido de pestaña principal
        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

		$contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
					<input type='text' id='nombre_sensor'
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
                    <textarea id='descripcion_sensor'
						class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        // Si se está modificando el sensor, sólo se muestra la opción de localización 'Ninguna' si el usuario actual puede ver ese sensor sin localización
        $opciones_extra_lista_localizaciones = OPCIONES_EXTRA_LISTA_LOCALIZACIONES_NINGUNA;
        if ($anyadir_nodo == false)
        {
            $mostrar_todos_sensores = dame_mostrar_todos_sensores();
            if ($mostrar_todos_sensores == false)
            {
                $ids_sensores_usuario = dame_ids_sensores_usuario_actual(true);
                if (in_array($id_nodo, $ids_sensores_usuario) == false)
                {
                    $opciones_extra_lista_localizaciones = OPCIONES_EXTRA_LISTA_LOCALIZACIONES_SIN_OPCIONES_EXTRA;
                }
            }
        }

        $contenido .= "
            <div class='row-fluid' id='control_id_localizacion_sensor'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Localización").": "."</span><br/>
					<select id='id_localizacion_sensor' class='chosen-select-administracion'>";
        $contenido .= dame_lista_localizaciones(array($id_localizacion), $opciones_extra_lista_localizaciones);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_visible_localizaciones_hijas_sensor'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Visible en localizaciones hijas").": "."</span><br/>
					<select id='visible_localizaciones_hijas_sensor' class='select-administracion'";
        $contenido .= ">";
        $contenido .= dame_lista_valores_si_no($visible_localizaciones_hijas);
		$contenido .= "
					</select>
				</div>
			</div>";

        // No se permite cambiar la clase del sensor si se está modificando
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase").": "."</span><br/>
					<select id='clase_sensor' class='select-administracion'";
        if ($anyadir_nodo == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_clases_sensor($clase, true, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
        $contenido .= "
					</select>
				</div>
			</div>";

        // No se permite cambiar el tipo de sensor si se está modificando
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
					<select id='tipo_sensor' class='select-administracion'";
        if ($anyadir_nodo == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_tipos_sensor($tipo);
        $contenido .= "
					</select>
				</div>
			</div>";

        // No se permite cambiar el tipo de valores del sensor si se está modificando
        $contenido .= "
            <div class='row-fluid' id='control_tipo_valores_sensor'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de valores").": "."</span><br/>
                    <select id='tipo_valores_sensor' class='select-administracion'";
        if ($anyadir_nodo == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_tipos_valores_sensor($tipo_valores);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_cambio_valores_puntuales_sensor'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Cambio de valores puntuales").": "."</span><br/>
					<select id='cambio_valores_puntuales_sensor' class='select-administracion'>";
        $contenido .= dame_lista_tipos_cambio_valores_puntuales_sensor($cambio_valores_puntuales);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_incrementos_tiempo_real_horarios_sensor'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Incrementos en tiempo real horarios").": "."</span><br/>
					<select id='incrementos_tiempo_real_horarios_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($incrementos_tiempo_real_horarios);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_incrementos_negativos_validos_sensor'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Incrementos negativos válidos").": "."</span><br/>
					<select id='incrementos_negativos_validos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($incrementos_negativos_validos);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_granularidad_cuartohoraria_sensor'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Granularidad cuartohoraria").": "."</span><br/>
					<select id='granularidad_cuartohoraria_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($granularidad_cuartohoraria);
		$contenido .= "
					</select>
				</div>
			</div>";

        // Si se está modificando el sensor, sólo se muestra la opción de grupo 'Ninguno' si el usuario actual puede ver ese sensor sin grupo
        $opciones_extra_lista_grupos = OPCIONES_EXTRA_LISTA_NODOS_NINGUNO;
        if ($anyadir_nodo == false)
        {
            $mostrar_todos_sensores = dame_mostrar_todos_sensores();
            if ($mostrar_todos_sensores == false)
            {
                $ids_sensores_usuario = dame_ids_sensores_usuario_actual(false);
                if (in_array($id_nodo, $ids_sensores_usuario) == false)
                {
                    $opciones_extra_lista_grupos = OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA;
                }
            }
        }
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupo").": "."</span><br/>
					<select id='id_grupo_sensor' class='chosen-select-administracion'>";
		$contenido .= dame_lista_grupos_sensores($clase, array($id_grupo), $opciones_extra_lista_grupos);
		$contenido .= "
					</select>
				</div>
			</div>";

        // Almacenamiento de valores en base de datos
        $contenido .= "
            <div class='row-fluid' id='control_guardar_valores_base_datos_sensor'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Guardar valores en base de datos").": "."</span><br/>
					<select id='guardar_valores_base_datos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($guardar_valores_base_datos);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_notificar_todos_eventos_sensor'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Notificar todos los eventos").": "."</span><br/>
					<select id='notificar_todos_eventos_sensor' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($notificar_todos_eventos);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestañas de parámetros específicos de clase
        $parametros_clase_energia_activa = NULL;
        $parametros_clase_energia_reactiva = NULL;
        $parametros_clase_cortes_tension = NULL;
        $parametros_clase_compra_energia = NULL;
        $parametros_clase_gas = NULL;
        $parametros_clase_agua = NULL;
        $parametros_clase_generica = NULL;
        switch ($clase)
        {
            case CLASE_SENSOR_ENERGIA_ACTIVA:
            {
                $parametros_clase_energia_activa = $parametros_clase;
                break;
            }
            case CLASE_SENSOR_ENERGIA_REACTIVA:
            {
                $parametros_clase_energia_reactiva = $parametros_clase;
                break;
            }
            case CLASE_SENSOR_CORTES_TENSION:
            {
                $parametros_clase_cortes_tension = $parametros_clase;
                break;
            }
            case CLASE_SENSOR_COMPRA_ENERGIA:
            {
                $parametros_clase_compra_energia = $parametros_clase;
                break;
            }
            case CLASE_SENSOR_GAS:
            {
                $parametros_clase_gas = $parametros_clase;
                break;
            }
            case CLASE_SENSOR_AGUA:
            {
                $parametros_clase_agua = $parametros_clase;
                break;
            }
            case CLASE_SENSOR_GENERICA:
            {
                $parametros_clase_generica = $parametros_clase;
                break;
            }
        }

        $contenido .= "
                    <div class='tab-pane' id='tab-clase-energia-activa'>";
        $contenido .= dame_controles_sensor_pestanya_clase_energia_activa($parametros_clase_energia_activa);
        $contenido .= "
                    </div>";

        $contenido .= "
                    <div class='tab-pane' id='tab-clase-energia-reactiva'>";
        $contenido .= dame_controles_sensor_pestanya_clase_energia_reactiva($parametros_clase_energia_reactiva);
        $contenido .= "
                    </div>";

        $contenido .= "
                    <div class='tab-pane' id='tab-clase-cortes-tension'>";
        $contenido .= dame_controles_sensor_pestanya_clase_cortes_tension($parametros_clase_cortes_tension);
        $contenido .= "
                    </div>";

        $contenido .= "
                    <div class='tab-pane' id='tab-clase-compra-energia'>";
        $contenido .= dame_controles_sensor_pestanya_clase_compra_energia($parametros_clase_compra_energia);
        $contenido .= "
                    </div>";

        $contenido .= "
                    <div class='tab-pane' id='tab-clase-gas'>";
        $contenido .= dame_controles_sensor_pestanya_clase_gas($parametros_clase_gas);
        $contenido .= "
                    </div>";

        $contenido .= "
                    <div class='tab-pane' id='tab-clase-agua'>";
        $contenido .= dame_controles_sensor_pestanya_clase_agua($parametros_clase_gas);
        $contenido .= "
                    </div>";

        $contenido .= "
                    <div class='tab-pane' id='tab-clase-generica'>";
        $contenido .= dame_controles_sensor_pestanya_clase_generica($parametros_clase_generica);
        $contenido .= "
                    </div>";

		// Contenido de pestañaa de parámetros específicos de tipo
        $parametros_tipo_real = NULL;
        $parametros_tipo_virtual = NULL;
        $parametros_tipo_procesado = NULL;
        $parametros_tipo_externo = NULL;
        switch ($tipo)
        {
            case TIPO_SENSOR_REAL:
            {
                $parametros_tipo_real = $parametros_tipo;
                break;
            }
            case TIPO_SENSOR_VIRTUAL:
            {
                $parametros_tipo_virtual = $parametros_tipo;
                break;
            }
            case TIPO_SENSOR_PROCESADO:
            {
                $parametros_tipo_procesado = $parametros_tipo;
                break;
            }
            case TIPO_SENSOR_EXTERNO:
            {
                $parametros_tipo_externo = $parametros_tipo;
                break;
            }
        }

        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-real'>";
        $contenido .= dame_controles_sensor_pestanya_tipo_real($parametros_tipo_real, $calibracion);
        $contenido .= "
                    </div>";

        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-virtual'>";
        $contenido .= dame_controles_sensor_pestanya_tipo_virtual($parametros_tipo_virtual, $clase);
        $contenido .= "
                    </div>";

        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-procesado'>";
        $contenido .= dame_controles_sensor_pestanya_tipo_procesado($parametros_tipo_procesado, $calibracion);
        $contenido .= "
                    </div>";

        $contenido .= "
                    <div class='tab-pane' id='tab-tipo-externo'>";
        $contenido .= dame_controles_sensor_pestanya_tipo_externo($parametros_tipo_externo, $anyadir_nodo, $calibracion);
        $contenido .= "
                    </div>";

        // Contenido de pestaña de envío
        $contenido .= "
                    <div class='tab-pane' id='tab-envio'>";

        $contenido .= "
            <div class='row-fluid' id='control_frecuencia_muestreo_sensor'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Frecuencia de muestreo")." (".$idiomas->_("segundos")."): "."</span><br/>
					<input type='text' id='frecuencia_muestreo_sensor'
						class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$frecuencia_muestreo."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Frecuencia de envío")." (".$idiomas->_("segundos")."): "."</span><br/>
					<input type='text' id='frecuencia_envio_sensor'
						class='TLNT_input_mandatory TLNT_input_numerical input-administracion' value='".$frecuencia_envio."'>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Posición en el mapa
        $contenido .= "
                    <div class='tab-pane' id='tab-posicion-mapa'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar en mapa").": "."</span><br/>
					<select id='mostrar_en_mapa' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($mostrar_en_mapa);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= dame_localizador_mapa(
            "",
            ORIGEN_MAPA_POSICION,
            $parametros_origen_mapa,
            $latitud_mapa,
            $longitud_mapa,
            $zoom_mapa);

        $contenido .=
                    "</div>";

        $contenido .= "
                </div>
            </div>";

        // Características de clase de sensor
        $caracteristicas_clase_sensor = NodoSensor::dame_caracteristicas_clase_sensor($clase);
        $clase_granularidad_cuartohoraria = $caracteristicas_clase_sensor["granularidad_cuartohoraria"];

        // Se añaden los parámetros (no visibles) específicos del sensor en un 'div' oculto
        if ($anyadir_nodo == false)
        {
            $contenido .= '
                <div id="parametros_ventana_anyadir_modificar_sensor"
                    nombre="'.$nombre.'"
                    id_localizacion="'.$id_localizacion.'"
                    visible_localizaciones_hijas="'.$visible_localizaciones_hijas.'"
                    clase_granularidad_cuartohoraria="'.$clase_granularidad_cuartohoraria.'"
                    latitud_mapa="'.$latitud_mapa.'"
                    longitud_mapa="'.$longitud_mapa.'"
                    hidden>
                </div>';
        }

        return ("OK");
	}


    function rellena_contenido_ventana_anyadir_modificar_grupo_sensores($anyadir_nodo, $id_nodo, &$contenido)
	{
		$idiomas = new Idiomas();

		// Si hay que modificar el grupo, se recupera la información actual de la base de datos
		if ($anyadir_nodo == false)
		{
            $fila_grupo_sensores = dame_fila_grupo_sensores($id_nodo);

			$nombre = $fila_grupo_sensores["nombre"];
            $descripcion = $fila_grupo_sensores["descripcion"];
            $id_localizacion = $fila_grupo_sensores["localizacion"];
            $clase = $fila_grupo_sensores["clase"];
		}
        else
        {
            $id_localizacion = dame_localizacion_defecto_anyadir_nodo();
            $clase = ID_NINGUNO;
        }

        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
					<input type='text' id='nombre_grupo_sensores'
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
                    <textarea id='descripcion_grupo_sensores'
						class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        // Si se está modificando el grupo, sólo se muestra la opción de localización 'Ninguna' si el usuario actual puede ver ese grupo sin localización
        $opciones_extra_lista_localizaciones = OPCIONES_EXTRA_LISTA_LOCALIZACIONES_NINGUNA;
        if ($anyadir_nodo == false)
        {
            $mostrar_todos_sensores = dame_mostrar_todos_sensores();
            if ($mostrar_todos_sensores == false)
            {
                $ids_grupos_sensores_usuario = dame_ids_grupos_sensores_usuario_actual(false);
                if (in_array($id_nodo, $ids_grupos_sensores_usuario) == false)
                {
                    $opciones_extra_lista_localizaciones = OPCIONES_EXTRA_LISTA_LOCALIZACIONES_SIN_OPCIONES_EXTRA;
                }
            }
        }

        $contenido .= "
            <div class='row-fluid' id='control_id_localizacion_grupo_sensores'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Localización").": "."</span><br/>
					<select id='id_localizacion_grupo_sensores' class='chosen-select-administracion'>";
        $contenido .= dame_lista_localizaciones(array($id_localizacion), $opciones_extra_lista_localizaciones);
		$contenido .= "
					</select>
				</div>
			</div>";

        // No se permite cambiar la clase del grupo de sensores si se está modificando
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase").": "."</span><br/>
					<select id='clase_grupo_sensores' class='select-administracion'";
        if ($anyadir_nodo == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_clases_sensor($clase, true, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
        $contenido .= "
					</select>
				</div>
			</div>";

        return ("OK");
	}


    function rellena_contenido_ventana_anyadir_modificar_actuador($anyadir_nodo, $id_nodo, &$contenido)
	{
		$idiomas = new Idiomas();

        // Se recupera el origen del mapa 'final'
        $parametros_origen_mapa = array("modulo" => MODULO_ACTUADORES);
        $resultado_origen_mapa = dame_origen_mapa_final(ORIGEN_MAPA_POSICION, $parametros_origen_mapa);
        $origen_mapa = $resultado_origen_mapa["origen_mapa_final"];
        $id_origen_mapa = $resultado_origen_mapa["id_origen_mapa_final"];

		// Si hay que modificar el actuador (o es un duplicado), se recupera la información actual de la base de datos
		if ($id_nodo != ID_NINGUNO)
		{
            $fila_actuador = dame_fila_actuador($id_nodo);

			$nombre = $fila_actuador["nombre"];
            $descripcion = $fila_actuador["descripcion"];
            $id_localizacion = $fila_actuador["localizacion"];
            $visible_localizaciones_hijas = $fila_actuador["visible_localizaciones_hijas"];
            $clase = $fila_actuador["clase"];
            $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_actuador["parametros_clase"]);
            $tipo = $fila_actuador["tipo"];
            $parametros_tipo = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_actuador["parametros_tipo"]);
            $calibracion = formatea_calibracion($fila_actuador["calibracion"]);
			$id_grupo = $fila_actuador["grupo"];
            $id_programacion = $fila_actuador["programacion"];

            // Posición en mapa
            $info_posicion_mapa = dame_info_posicion_mapa_base_datos(
                TIPO_ELEMENTO_MAPA_ACTUADOR,
                $id_nodo,
                $origen_mapa,
                $id_origen_mapa);
            if ($info_posicion_mapa === NULL)
            {
                // Se recupera la información del mapa del origen correspondiente
                switch ($origen_mapa)
                {
                    case ORIGEN_MAPA_RED:
                    {
                        $fila_origen_mapa = dame_fila_red($id_origen_mapa);
                        break;
                    }
                    case ORIGEN_MAPA_LOCALIZACION:
                    {
                        $fila_origen_mapa = dame_fila_localizacion($id_origen_mapa);
                        break;
                    }
                    default:
                    {
                        throw new Exception("Origen de mapa desconocido: '".$origen_mapa."'");
                    }
                }
                $mostrar_en_mapa = VALOR_NO;
                $latitud_mapa = $fila_origen_mapa["latitud_mapa_defecto"];
                $longitud_mapa = $fila_origen_mapa["longitud_mapa_defecto"];
                $zoom_mapa = $fila_origen_mapa["zoom_mapa_defecto"];
            }
            else
            {
                $mostrar_en_mapa = VALOR_SI;
                $latitud_mapa = $info_posicion_mapa["latitud"];
                $longitud_mapa = $info_posicion_mapa["longitud"];
                $zoom_mapa = $info_posicion_mapa["zoom"];
            }
		}
        else
        {
            // Valores por defecto al añadir un actuador
            $id_localizacion = dame_localizacion_defecto_anyadir_nodo();
            $visible_localizaciones_hijas = VALOR_NO;
            $clase = CLASE_NINGUNA;
            $parametros_clase = NULL;
            $tipo = TIPO_NINGUNO;
            $parametros_tipo = NULL;
            $id_grupo = ID_NINGUNO;

            // Se recupera la información del mapa del origen correspondiente
            switch ($origen_mapa)
            {
                case ORIGEN_MAPA_RED:
                {
                    $fila_origen_mapa = dame_fila_red($id_origen_mapa);
                    break;
                }
                case ORIGEN_MAPA_LOCALIZACION:
                {
                    $fila_origen_mapa = dame_fila_localizacion($id_origen_mapa);
                    break;
                }
                default:
                {
                    throw new Exception("Origen de mapa desconocido: '".$origen_mapa."'");
                }
            }
            $mostrar_en_mapa = VALOR_NO;
            $latitud_mapa = $fila_origen_mapa["latitud_mapa_defecto"];
            $longitud_mapa = $fila_origen_mapa["longitud_mapa_defecto"];
            $zoom_mapa = $fila_origen_mapa["zoom_mapa_defecto"];
        }

        // Se muestran las siguientes pestañas:
        // - Principal, genérica, interfaz y posición en mapa
        $contenido = "
            <div id='tabs-administracion-actuador' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-clase-generica' id='titulo-tab-clase-generica'>".$idiomas->_("Genérica")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-interfaz' id='titulo-tab-interfaz'>".$idiomas->_("Interfaz")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-posicion-mapa' id='titulo-tab-posicion-mapa'>".$idiomas->_("Posición en mapa")."</a></li>
                </ul>
                <div id='tabs-content-administracion-actuador' class='tab-content'>";

        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

        // Contenido de pestaña de datos
		$contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
					<input type='text' id='nombre_actuador'
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
                    <textarea id='descripcion_actuador'
						class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        // Si se está modificando el actuador, sólo se muestra la opción de localización 'Ninguna' si el usuario actual puede ver ese actuador sin localización
        $opciones_extra_lista_localizaciones = OPCIONES_EXTRA_LISTA_LOCALIZACIONES_NINGUNA;
        if ($anyadir_nodo == false)
        {
            $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
            if ($mostrar_todos_actuadores == false)
            {
                $ids_actuadores_usuario = dame_ids_actuadores_usuario_actual(true);
                if (in_array($id_nodo, $ids_actuadores_usuario) == false)
                {
                    $opciones_extra_lista_localizaciones = OPCIONES_EXTRA_LISTA_LOCALIZACIONES_SIN_OPCIONES_EXTRA;
                }
            }
        }

        $contenido .= "
            <div class='row-fluid' id='control_id_localizacion_actuador'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Localización").": "."</span><br/>
					<select id='id_localizacion_actuador' class='chosen-select-administracion'>";
        $contenido .= ">";
		$contenido .= dame_lista_localizaciones(array($id_localizacion), $opciones_extra_lista_localizaciones);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_visible_localizaciones_hijas_actuador'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Visible en localizaciones hijas").": "."</span><br/>
					<select id='visible_localizaciones_hijas_actuador' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($visible_localizaciones_hijas);
		$contenido .= "
					</select>
				</div>
			</div>";

        // No se permite cambiar la clase del actuador si se está modificando
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase").": "."</span><br/>
					<select id='clase_actuador' class='select-administracion'";
        if ($anyadir_nodo == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_clases_actuador($clase, true, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
        $contenido .= "
					</select>
				</div>
			</div>";

        // No se permite cambiar el tipo de actuador si se está modificando
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
					<select id='tipo_actuador' class='select-administracion'";
        if ($anyadir_nodo == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_tipos_actuador($tipo);
        $contenido .= "
					</select>
				</div>
			</div>";

        // Si se está modificando el actuador, sólo se muestra la opción de grupo 'Ninguno' si el usuario actual puede ver ese actuador sin grupo
        $opciones_extra_lista_grupos = OPCIONES_EXTRA_LISTA_NODOS_NINGUNO;
        if ($anyadir_nodo == false)
        {
            $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
            if ($mostrar_todos_actuadores == false)
            {
                $ids_actuadores_usuario = dame_ids_actuadores_usuario_actual(false);
                if (in_array($id_nodo, $ids_actuadores_usuario) == false)
                {
                    $opciones_extra_lista_grupos = OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA;
                }
            }
        }
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupo").": "."</span><br/>
					<select id='id_grupo_actuador' class='chosen-select-administracion'>";
		$contenido .= dame_lista_grupos_actuadores($clase, array($id_grupo), $opciones_extra_lista_grupos);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Programación").": "."</span><br/>
                    <select id='id_programacion_actuador' class='chosen-select-administracion'>";
        $contenido .= dame_lista_programaciones_clase_actuador($clase, $id_programacion);
		$contenido .= "
                    </select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestañsa de parámetros específicos de clase
        $parametros_clase_generica = NULL;
        switch ($clase)
        {
            case CLASE_ACTUADOR_GENERICA:
            {
                $parametros_clase_generica = $parametros_clase;
                break;
            }
        }

        $contenido .= "
                    <div class='tab-pane' id='tab-clase-generica'>";
        $contenido .= dame_controles_actuador_pestanya_clase_generica($parametros_clase_generica);
        $contenido .= "
                    </div>";

        // Contenido de pestaña de parámetros específicos de tipo (interfaz)
        $contenido .= "
                    <div class='tab-pane' id='tab-interfaz'>";
        $contenido .= dame_controles_actuador_pestanya_tipo($tipo, $parametros_tipo, $calibracion);
        $contenido .= "
                    </div>";


        // Posición en el mapa
        $contenido .= "
                    <div class='tab-pane' id='tab-posicion-mapa'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar en mapa").": "."</span><br/>
					<select id='mostrar_en_mapa' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($mostrar_en_mapa);
		$contenido .= "
					</select>
				</div>
			</div>";

		$contenido .= dame_localizador_mapa(
            "",
            ORIGEN_MAPA_POSICION,
            $parametros_origen_mapa,
            $latitud_mapa,
            $longitud_mapa,
            $zoom_mapa);

        $contenido .=
                    "</div>";

        $contenido .= "
                </div>
            </div>";

        // Se añaden los parámetros (no visibles) especificos del actuador en un 'div' oculto
        if ($anyadir_nodo == false)
        {
            $contenido .= '
                <div id="parametros_ventana_anyadir_modificar_actuador"
                    id_localizacion="'.$id_localizacion.'"
                    visible_localizaciones_hijas="'.$visible_localizaciones_hijas.'"
                    id_programacion="'.$id_programacion.'"
                    id_grupo="'.$id_grupo.'"
                    latitud_mapa="'.$latitud_mapa.'"
                    longitud_mapa="'.$longitud_mapa.'"
                    hidden>
                </div>';
        }

        return ("OK");
	}


    function rellena_contenido_ventana_anyadir_modificar_grupo_actuadores($anyadir_nodo, $id_nodo, &$contenido)
	{
		$idiomas = new Idiomas();

		// Si hay que modificar el grupo, se recupera la información actual de la base de datos
		if ($anyadir_nodo == false)
		{
            $fila_grupo_actuadores = dame_fila_grupo_actuadores($id_nodo);

			$nombre = $fila_grupo_actuadores["nombre"];
            $descripcion = $fila_grupo_actuadores["descripcion"];
            $id_localizacion = $fila_grupo_actuadores["localizacion"];
            $clase = $fila_grupo_actuadores["clase"];
			$id_programacion = $fila_grupo_actuadores["programacion"];
		}
        else
        {
            $id_localizacion = dame_localizacion_defecto_anyadir_nodo();
            $clase = ID_NINGUNO;
            $id_programacion = ID_NINGUNO;
        }

        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
					<input type='text' id='nombre_grupo_actuadores'
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
                    <textarea id='descripcion_grupo_actuadores'
						class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                </div>
            </div>";

        // Si se está modificando el grupo, sólo se muestra la opción de localización 'Ninguna' si el usuario actual puede ver ese grupo sin localización
        $opciones_extra_lista_localizaciones = OPCIONES_EXTRA_LISTA_LOCALIZACIONES_NINGUNA;
        if ($anyadir_nodo == false)
        {
            $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
            if ($mostrar_todos_actuadores == false)
            {
                $ids_grupos_actuadores_usuario = dame_ids_grupos_actuadores_usuario_actual(false);
                if (in_array($id_nodo, $ids_grupos_actuadores_usuario) == false)
                {
                    $opciones_extra_lista_localizaciones = OPCIONES_EXTRA_LISTA_LOCALIZACIONES_SIN_OPCIONES_EXTRA;
                }
            }
        }

        $contenido .= "
            <div class='row-fluid' id='control_id_localizacion_grupo_actuadores'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Localización").": "."</span><br/>
					<select id='id_localizacion_grupo_actuadores' class='chosen-select-administracion'>";
        $contenido .= dame_lista_localizaciones(array($id_localizacion), $opciones_extra_lista_localizaciones);
		$contenido .= "
					</select>
				</div>
			</div>";

        // No se permite cambiar el tipo del grupo de actuadores si se está modificando
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Clase").": "."</span><br/>
					<select id='clase_grupo_actuadores' class='select-administracion'";
        if ($anyadir_nodo == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_clases_actuador($clase, true, OPCIONES_EXTRA_LISTA_CLASES_NINGUNA);
        $contenido .= "
					</select>
				</div>
			</div>";

		$contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Programación").": "."</span><br/>
                    <select id='id_programacion_grupo_actuadores' class='chosen-select-administracion'>";
        $contenido .= dame_lista_programaciones_clase_actuador($clase, $id_programacion);
		$contenido .= "
                    </select>
				</div>
			</div>";

        // Se añaden los parámetros (no visibles) especificos del grupo de actuadores en un 'div' oculto
        if ($anyadir_nodo == false)
        {
            $contenido .= '
                <div id="parametros_ventana_anyadir_modificar_grupo_actuadores"
                    nombre_grupo_actuadores="'.$nombre.'"
                    id_programacion="'.$id_programacion.'"
                    hidden>
                </div>';
        }

        return ("OK");
	}


    //
    // Funciones auxiliares
    //


    function dame_localizacion_defecto_anyadir_nodo()
    {
        $id_localizacion_actual = $_SESSION["id_localizacion"];
        switch ($id_localizacion_actual)
        {
            case ID_NINGUNO:
            case ID_TODOS:
            case ID_DESACTIVADO:
            case ID_LOCALIZACIONES_SELECCIONADAS_AND:
            case ID_LOCALIZACIONES_SELECCIONADAS_OR:
            {
                $id_localizacion = ID_NINGUNO;
                break;
            }
            default:
            {
                $id_localizacion = $id_localizacion_actual;
                break;
            }
        }
        return ($id_localizacion);
    }
?>
