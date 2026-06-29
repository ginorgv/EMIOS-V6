<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');

	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/TarifaAgua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/Tarifa.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    // Crea una lista desplegable para la selección de una tarifa de agua
    function dame_control_lista_tarifas_agua_Espanya(
        $id_controles,
        $id_tarifa,
        $mostrar_etiquetas,
        $opciones_extra)
    {
        $idiomas = new Idiomas();

        $control_lista_tarifas_agua = "";
        if ($mostrar_etiquetas == true)
        {
            $control_lista_tarifas_agua .= "<div id='etiqueta_tarifa_agua_".$id_controles."'>".$idiomas->_("Tarifa de agua").": "."</div>";
        }
        $control_lista_tarifas_agua .= "
            <select id='id_tarifa_".$id_controles."'";
        $control_lista_tarifas_agua .= "
                class='chosen-select' hidden>";
        $control_lista_tarifas_agua .= dame_lista_tarifas_agua_Espanya(array($id_tarifa), $opciones_extra);
        $control_lista_tarifas_agua .= "
            </select>";

        return ($control_lista_tarifas_agua);
    }


    // Devuelve la lista de tarifas de agua
    function dame_lista_tarifas_agua_Espanya($ids_tarifas_seleccionadas, $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_tarifas_agua = "
            SELECT
                id,
                nombre,
                tipo
            FROM ".TABLA_TARIFAS_AGUA_ESPANYA."
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_TARIFAS_SIN_GRUPO)
        {
            $consulta_tarifas_agua .= "
                AND (grupo = '".ID_NINGUNO."')";
        }
        $consulta_tarifas_agua .= "
            ORDER BY nombre ASC";
        $res_tarifas_agua = $bd_red->ejecuta_consulta($consulta_tarifas_agua);
        if ($res_tarifas_agua == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tarifas_agua."'");
        }

        // Identificadores de tarifas del usuario actual
        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        if ($mostrar_todos_sensores == false)
        {
            $ids_tarifas_usuario = Tarifa::dame_ids_tarifas_usuario_actual(MEDICION_AGUA);
        }

        $lista_tarifas_agua = "";
        if ($opciones_extra != OPCIONES_EXTRA_LISTA_TARIFAS_SIN_NINGUNA)
        {
            switch ($opciones_extra)
            {
                case OPCIONES_EXTRA_LISTA_TARIFAS_TARIFA_VIGENTE_SEGUN_FECHAS:
                {
                    $lista_tarifas_agua .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Tarifa vigente según fechas")."</option>";
                    break;
                }
                case OPCIONES_EXTRA_LISTA_TARIFAS_ACTUAL:
                {
                    $lista_tarifas_agua .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Actual")."</option>";
                    break;
                }
                default:
                {
                    $lista_tarifas_agua .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguna")."</option>";
                    break;
                }
            }
        }
        while ($fila_tarifa_agua = $res_tarifas_agua->dame_siguiente_fila())
        {
            $anyadir_tarifa = true;

            if ($anyadir_tarifa == true)
            {
                if ($mostrar_todos_sensores == false)
                {
                    if (in_array($fila_tarifa_agua['id'], $ids_tarifas_usuario) == false)
                    {
                        $anyadir_tarifa = false;
                    }
                }
            }

            if ($anyadir_tarifa == true)
            {
                $lista_tarifas_agua .= "<option value='".$fila_tarifa_agua['id']."'";
                if (in_array($fila_tarifa_agua['id'], $ids_tarifas_seleccionadas) == true)
                {
                    $lista_tarifas_agua .= " selected";
                }
                $lista_tarifas_agua .= ">".htmlspecialchars($fila_tarifa_agua['nombre'], ENT_QUOTES)."</option>";
            }
        }

        return ($lista_tarifas_agua);
    }


    // Devuelve la lista de tarifas de agua de un tipo
    function dame_lista_tarifas_tipo_agua_Espanya($tipo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_tarifas_agua = "
            SELECT
                id,
                nombre,
                tipo
            FROM ".TABLA_TARIFAS_AGUA_ESPANYA."
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($tipo != TIPO_TARIFA_TODOS)
        {
            $consulta_tarifas_agua .= "
                AND (tipo = '".$bd_red->_($tipo)."')";
        }
        $consulta_tarifas_agua .= "
            ORDER BY nombre ASC";
        $res_tarifas_agua = $bd_red->ejecuta_consulta($consulta_tarifas_agua);
        if ($res_tarifas_agua == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tarifas_agua."'");
        }

        // Identificadores de tarifas del usuario actual
        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        if ($mostrar_todos_sensores == false)
        {
            $ids_tarifas_usuario = Tarifa::dame_ids_tarifas_usuario_actual(MEDICION_AGUA);
        }

        while ($fila_tarifa_agua = $res_tarifas_agua->dame_siguiente_fila())
        {
            $anyadir_tarifa = true;
            if ($mostrar_todos_sensores == false)
            {
                if (in_array($fila_tarifa_agua['id'], $ids_tarifas_usuario) == false)
                {
                    $anyadir_tarifa = false;
                }
            }

            if ($anyadir_tarifa == true)
            {
                $lista_tarifas_agua .= "<option value='".$fila_tarifa_agua['id']."'>".
                    htmlspecialchars($fila_tarifa_agua['nombre'], ENT_QUOTES)."</option>";
            }
        }
        return ($lista_tarifas_agua);
    }


    // Devuelve la lista de tipos de tarifa de agua
    function dame_lista_tipos_tarifa_agua_Espanya($tipo_seleccionado, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $lista = "";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_TODOS)
        {
            $lista .= "<option value='".TIPO_TARIFA_TODOS."'>".$idiomas->_("Todos")."</option>";
        }
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_NINGUNO)
        {
            $lista .= "<option value='".TIPO_TARIFA_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        }
        $tipos_tarifa = TarifaAgua_Espanya::dame_tipos_tarifa_agua();
        foreach ($tipos_tarifa as $tipo_tarifa)
        {
            $nombre_tipo_tarifa = TarifaAgua_Espanya::dame_descripcion_tipo_tarifa_agua($tipo_tarifa);
            $lista .= "<option value='".$tipo_tarifa."'";
			if ($tipo_tarifa == $tipo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_tipo_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de tipos de límites de consumo por tramo tarifa de agua
    function dame_lista_tipos_limites_consumo_tramos_tarifa_agua_Espanya($tipo_seleccionado, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $lista = "";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_LIMITES_CONSUMO_TRAMOS_TARIFA_NINGUNO)
        {
            $lista .= "<option value='".TIPO_LIMITES_CONSUMO_TRAMOS_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        }
        $tipos_limites_consumo_tramos_tarifa = TarifaAgua_Espanya::dame_tipos_limites_consumo_tramos_tarifa_agua();
        foreach ($tipos_limites_consumo_tramos_tarifa as $tipo_limites_consumo_tramos_tarifa)
        {
            $nombre_tipo_limites_consumo_tramos_tarifa = TarifaAgua_Espanya::dame_descripcion_tipo_limites_consumo_tramos_tarifa_agua($tipo_limites_consumo_tramos_tarifa);
            $lista .= "<option value='".$tipo_limites_consumo_tramos_tarifa."'";
			if ($tipo_limites_consumo_tramos_tarifa == $tipo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_tipo_limites_consumo_tramos_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de tipos de alquiler de contador
    function dame_lista_tipos_alquiler_contador_tarifa_agua_Espanya($tipo_seleccionado)
    {
        $tipos_alquiler_contador_tarifa = TarifaAgua_Espanya::dame_tipos_alquiler_contador_tarifa_agua();
        foreach ($tipos_alquiler_contador_tarifa as $tipo_alquiler_contador_tarifa)
        {
            $nombre_tipo_alquiler_contador_tarifa = TarifaAgua_Espanya::dame_descripcion_tipo_alquiler_contador_tarifa_agua($tipo_alquiler_contador_tarifa);
            $lista .= "<option value='".$tipo_alquiler_contador_tarifa."'";
			if ($tipo_alquiler_contador_tarifa == $tipo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_tipo_alquiler_contador_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve el contenido de las pestañas de la ventana de administración de tarifas de agua
    function dame_contenido_pestanyas_ventana_administracion_tarifas_agua_Espanya(
        $tipo_administracion,
        $id_tarifa,
        $nombre,
        $descripcion,
        $tipo,
        $id_grupo,
        $expiracion,
        $cadena_fecha_expiracion_local_local,
        $numero_dias_preaviso_expiracion,
        $tipo_limites_consumo_tramos,
        $cadena_limites_consumo_tramos,
        $cadena_precios_consumo_tramos,
        $tipo_alquiler_contador,
        $alquiler_contador,
        $iva_consumo,
        $igic_consumo,
        $iva_alquiler_contador,
        $igic_alquiler_contador)
    {
        $idiomas = new Idiomas();

        // Unidad de medida de coste
        $unidad_medida_coste = $_SESSION["moneda"];

        // Formateado de cadenas
        $cadena_limites_consumo_tramos = str_replace(SEPARADOR_PARAMETROS_SIMPLES, ", ", $cadena_limites_consumo_tramos);
        $cadena_precios_consumo_tramos = str_replace(SEPARADOR_PARAMETROS_SIMPLES, ", ", $cadena_precios_consumo_tramos);

        // Controles obligatorios
        switch ($tipo_administracion)
        {
            case TIPO_ADMINISTRACION_TARIFAS_UNICA:
            {
                $clase_controles = "TLNT_input_mandatory"." ";
                break;
            }
            case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE:
            {
                $clase_controles = "";
                break;
            }
            default:
            {
                throw new Exception("Tipo de administración desconocido: '".$tipo_administracion."'");
            }
        }

        // Se crea el contenido de las pestañas de tarifas de agua
        $contenido = "
            <div id='tabs-administracion-tarifa-agua' class='tabbable' tipo-administracion='".$tipo_administracion."'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-precios-consumo' id='titulo-tab-precios-consumo'>".$idiomas->_("Precios de consumo")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-factura' id='titulo-tab-factura'>".$idiomas->_("Factura")."</a></li>
                </ul>
                <div id='tabs-content-administracion-tarifa-agua' class='tab-content'>";

        // Contenido de pestaña principal (diferente para una y múltiples tarifas)
        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

        // Nombre y descripción (única tarifa)
        if ($tipo_administracion == TIPO_ADMINISTRACION_TARIFAS_UNICA)
        {
            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
                        <input type='text' id='nombre_tarifa_agua'
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
                        <textarea id='descripcion_tarifa_agua'
                            class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                    </div>
                </div>";
        }

        // Tipo de tarifa de agua
        switch ($tipo_administracion)
        {
            case TIPO_ADMINISTRACION_TARIFAS_UNICA:
            {
                $opciones_extra_lista_tipos_tarifa_agua = OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_NINGUNO;
                break;
            }
            case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE:
            {
                $opciones_extra_lista_tipos_tarifa_agua = OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_TODOS;
                break;
            }
        }
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
                    <select id='tipo_tarifa_agua' class='select-administracion'>";
        $contenido .= dame_lista_tipos_tarifa_agua_Espanya($tipo, $opciones_extra_lista_tipos_tarifa_agua);
        $contenido .= "
                    </select>
                </div>
            </div>";

        // Grupo de tarifas (única tarifa)
        if ($tipo_administracion == TIPO_ADMINISTRACION_TARIFAS_UNICA)
        {
            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupo").": "."</span><br/>
                        <select id='id_grupo_tarifa' class='chosen-select-administracion'>";
            $contenido .= dame_lista_grupos_tarifas(MEDICION_AGUA, $id_grupo, OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_NINGUNO);
            $contenido .= "
                        </select>
                    </div>
                </div>";
        }

        // Expiración de tarifa
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Expiración").": "."</span><br/>
                    <select id='expiracion_tarifa' class='select-administracion'>";
        $contenido .= dame_lista_expiraciones_tarifa($expiracion);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_fecha_expiracion_tarifa'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha de expiración").": "."</span><br/>
                    <input size='10' type='text' id='fecha_expiracion_tarifa' class='datepicker selector-fechas-administracion'
                        readonly='readonly' value='".$cadena_fecha_expiracion_local_local."'>
                </div>
			</div>

            <div class='row-fluid' id='control_numero_dias_preaviso_expiracion_tarifa'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número de días de preaviso de expiración").": "."</span><br/>
					<input type='text' id='numero_dias_preaviso_expiracion_tarifa'
						class='".$clase_controles."TLNT_input_integer input-administracion' value='".$numero_dias_preaviso_expiracion."'>
				</div>
			</div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        if ($tipo_administracion == TIPO_ADMINISTRACION_TARIFAS_MULTIPLE)
        {
            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tarifas").": "."</span><br/>
                        <div id='select_tarifas_agua_tarifa_agua_no_visible' hidden></div>
                        <select id='ids_tarifas_agua_tarifa_agua'
                            name='ids_tarifas_agua_tarifa_agua'
                            max_selected='".ID_NINGUNO."' multiple='multiple'
                            class='select-administracion' hidden>";
            $contenido .= dame_lista_tarifas_tipo_agua_Espanya($tipo);
            $contenido .= "
                        </select>
                    </div>
                </div>";
        }

        $contenido .= "
                    </div>";

        // Contenido de pestaña de precios de consumo
        $contenido .= "
                    <div class='tab-pane' id='tab-precios-consumo'>";

        switch ($tipo_administracion)
        {
            case TIPO_ADMINISTRACION_TARIFAS_UNICA:
            {
                $opciones_extra_lista_tipos_limites_consumo_tramos_tarifa_agua = OPCIONES_EXTRA_LISTA_TIPOS_LIMITES_CONSUMO_TRAMOS_TARIFA_SIN_OPCIONES_EXTRA;
                break;
            }
            case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE:
            {
                $opciones_extra_lista_tipos_limites_consumo_tramos_tarifa_agua = OPCIONES_EXTRA_LISTA_TIPOS_LIMITES_CONSUMO_TRAMOS_TARIFA_NINGUNO;
                break;
            }
        }
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de límites de consumo por tramo").": "."</span><br/>
					<select id='tipo_limites_consumo_tramos_tarifa_agua' class='select-administracion'>";
        $contenido .= dame_lista_tipos_limites_consumo_tramos_tarifa_agua_Espanya($tipo_limites_consumo_tramos, $opciones_extra_lista_tipos_limites_consumo_tramos_tarifa_agua);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Límites de consumo por tramo")." (".$idiomas->_("m3").")".": "."</span><br/>
                    <input type='text' id='limites_consumo_tramos_tarifa_agua'
                        class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($cadena_limites_consumo_tramos, ENT_QUOTES)."'>
                    <span id='boton_smartmeter_ayuda_limites_consumo_tramos_tarifa_agua' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Precios de consumo por tramo")." (".$idiomas->_("€")."/".$idiomas->_("m3").")".": "."</span><br/>
                    <input type='text' id='precios_consumo_tramos_tarifa_agua'
                        class='".$clase_controles." TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($cadena_precios_consumo_tramos, ENT_QUOTES)."'>
                    <span id='boton_smartmeter_ayuda_precios_consumo_tramos_tarifa_agua' class='clickable'>
                        <i class='icon-question-sign color-azul icono-ayuda'></i>
                    </span>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de factura
        $contenido .= "
                    <div class='tab-pane' id='tab-factura'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de alquiler de contador").": "."</span><br/>
					<select id='tipo_alquiler_contador_tarifa_agua' class='select-administracion'>";
        $contenido .= dame_lista_tipos_alquiler_contador_tarifa_agua_Espanya($tipo_alquiler_contador);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Alquiler de contador")." (".$unidad_medida_coste.")".": "."</span><br/>
					<input type='text' id='alquiler_contador_tarifa_agua'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$alquiler_contador."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid' id='control_iva_consumo_tarifa_agua'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("IVA de consumo")." (%)".": "."</span><br/>
					<input type='text' id='iva_consumo_tarifa_agua'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$iva_consumo."'>
				</div>
			</div>

            <div class='row-fluid' id='control_igic_consumo_tarifa_agua'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("IGIC de consumo")." (%)".": "."</span><br/>
					<input type='text' id='igic_consumo_tarifa_agua'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$igic_consumo."'>
				</div>
			</div>

            <div class='row-fluid' id='control_iva_alquiler_contador_tarifa_agua'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("IVA de alquiler de contador")." (%)".": "."</span><br/>
					<input type='text' id='iva_alquiler_contador_tarifa_agua'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$iva_alquiler_contador."'>
				</div>
			</div>

            <div class='row-fluid' id='control_igic_alquiler_contador_tarifa_agua'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("IGIC de alquiler de contador")." (%)".": "."</span><br/>
					<input type='text' id='igic_alquiler_contador_tarifa_agua'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$igic_alquiler_contador."'>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        $contenido .= "
                </div>
            </div>";
        return ($contenido);
    }


    // Devuelve la tabla de tramos de una tarifa de agua
    function dame_tabla_tramos_tarifa_agua_Espanya($id_tarifa, $fila_tarifa_agua, $incluir_salto_linea = true)
    {
        $idiomas = new Idiomas();

        if ($fila_tarifa_agua === NULL)
        {
            $fila_tarifa_agua = dame_fila_tarifa(TABLA_TARIFAS_AGUA_ESPANYA, $id_tarifa);
        }
        $cabecera_tabla = array(
            $idiomas->_("Límite de consumo"),
            $idiomas->_("Precio de consumo")
        );
        $params_tabla = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_TRAMOS_TARIFA_AGUA,
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_TRAMOS_TARIFA_AGUA),
            "generar_valores_xml" => true
        );
        $titulo_tabla_tramos_tarifa_agua = $idiomas->_("Tramos de tarifa de agua");
        $tabla = new TablaDatos(
            "tabla-tramos-tarifa-agua",
            $titulo_tabla_tramos_tarifa_agua,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $tabla->anyade_cabecera("", $cabecera_tabla);

        $tipo_limites_consumo_tramos = $fila_tarifa_agua["tipo_limites_consumo_tramos"];
        $cadena_limites_consumo_tramos = $fila_tarifa_agua["limites_consumo_tramos"];
        $cadena_precios_consumo_tramos = $fila_tarifa_agua["precios_consumo_tramos"];
        if ($cadena_limites_consumo_tramos == "")
        {
            $limites_consumo_tramos = array();
        }
        else
        {
            $limites_consumo_tramos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_limites_consumo_tramos);
        }
        $precios_consumo_tramos = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_precios_consumo_tramos);
        $numero_tramos = count($precios_consumo_tramos);
        for ($i = 0; $i < $numero_tramos; $i++)
        {
            if ($i < ($numero_tramos - 1))
            {
                $limite_consumo_tramo = $limites_consumo_tramos[$i];
                $cadena_limite_consumo_tramo = $idiomas->_("Hasta")." ".$limite_consumo_tramo." ".$idiomas->_("m3");
                switch ($tipo_limites_consumo_tramos)
                {
                    case TIPO_LIMITES_CONSUMO_TRAMOS_DIARIO:
                    {
                        $cadena_limite_consumo_tramo .= "/".$idiomas->_("día");
                        break;
                    }
                }
            }
            else
            {
                $cadena_limite_consumo_tramo = $idiomas->_("Sin límite de consumo");
            }
            $precio_consumo_tramo = $precios_consumo_tramos[$i];
            $cadena_precio_consumo_tramo = formatea_numero($precio_consumo_tramo, 6, true)." ".$idiomas->_("€")."/".$idiomas->_("m3");
            $fila_tramo = array(
                $cadena_limite_consumo_tramo,
                $cadena_precio_consumo_tramo);

            $tabla->anyade_fila("", $fila_tramo);
        }

        return ($tabla->dame_tabla($incluir_salto_linea));
    }


    //
    // Funciones para las secciones de tarifas
    //


    function dame_tabla_filtro_tarifas_tabla_agua_Espanya()
    {
        $idiomas = new Idiomas();

        // Se recuperan los controles a mostrar
        $controles = array();
        $id_controles = "smartmeter_filtro_tarifas_tabla_agua_Espanya";

        // Tipos de tarifa de agua
        $control_lista_tipos .= "<div id='etiqueta_tipo_tarifa_agua_".$id_controles."'>".$idiomas->_("Tipo").": "."</div>";
        $control_lista_tipos .= "<select id='tipo_tarifa_agua_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_tipos .= dame_lista_tipos_tarifa_agua_Espanya(TIPO_TARIFA_TODOS, OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_TODOS);
        $control_lista_tipos .= "</select>";
        array_push($controles, $control_lista_tipos);

        // Grupos de tarifas de agua
        $control_lista_grupos .= "<div id='etiqueta_grupo_tarifas_agua_".$id_controles."'>".$idiomas->_("Grupo").": "."</div>";
        $control_lista_grupos .= "<select id='id_grupo_tarifas_agua_".$id_controles."' class='chosen-select' hidden>";
        $control_lista_grupos .= dame_lista_grupos_tarifas(MEDICION_AGUA, ID_TODOS, OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_TODOS_NINGUNO);
        $control_lista_grupos .= "</select>";
        array_push($controles, $control_lista_grupos);

        // Estado de tarifas (de expiración)
        $control_lista_estados .= dame_control_lista_estados_tarifa($id_controles, $idiomas->_("Estado"));
        array_push($controles, $control_lista_estados);

        // Nombre
        $filtro_tarifas_agua = dame_filtro_texto_controles_extra($id_controles, $idiomas->_("Nombre"), $controles);

        // Se crea la tabla contenedora
        $tabla = new TablaDatos(
            "tabla-smartmeter-filtro-tarifas-agua-tabla",
            $idiomas->_("Filtro de tarifas"),
            TIPO_TABLA_DATOS_CONTENEDOR
        );

        $params_fila = array(
            "clase_dato" => "filtro-informes",
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_TARIFAS_AGUA_TABLA_ESPANYA)
        );
        $tabla->anyade_fila("filtro-tarifas-agua-tabla", $filtro_tarifas_agua, $params_fila);

        return ($tabla->dame_tabla());
    }


    function asigna_tarifa_grupo_tarifas_sensores_agua_Espanya($parametros)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $id_tarifa = $parametros["id_tarifa"];
        $id_grupo_tarifas = $parametros["id_grupo_tarifas"];
        $ids_sensores = $parametros["ids_sensores"];

        // Se recuperan los sensores de agua correspondientes a las tarifas seleccionadas
        $cadena_ids_sensores_consulta = dame_cadena_ids_consulta($ids_sensores);
        $consulta_sensores = "
            SELECT
                id,
                parametros_clase
            FROM sensores
            WHERE
                id IN (".$cadena_ids_sensores_consulta.")";
        $res_sensores = $bd_red->ejecuta_consulta($consulta_sensores);
        if ($res_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_sensores."'");
        }
        while ($fila_sensor = $res_sensores->dame_siguiente_fila())
        {
            $id_sensor = $fila_sensor["id"];
            $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor["parametros_clase"]);

            // Si no se ha modificado la tarifa o el grupo de tarifas no se modifica el sensor
            $id_tarifa_sensor = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_TARIFA_AGUA];
            $id_grupo_tarifas_sensor = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_ID_GRUPO_TARIFAS_AGUA];
            if (($id_tarifa == $id_tarifa_sensor) && ($id_grupo_tarifas == $id_grupo_tarifas_sensor))
            {
                continue;
            }

            // Parámetros de clase con la nueva tarifa o grupo de tarifas
            $cadena_parametros_clase_modificados = implode(SEPARADOR_PARAMETROS_COMPUESTOS, array(
                $id_tarifa,
                $id_grupo_tarifas,
                $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_CUPS]));

            // Se modifica el sensor
            $operacion_modificacion = "
                UPDATE sensores
                SET
                    parametros_clase = '".$bd_red->_($cadena_parametros_clase_modificados)."'
                WHERE
                    id = '".$bd_red->_($id_sensor)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == false)
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }
        }

        $msg = $idiomas->_("Tarifa o grupo de tarifas asignado correctamente");
        $resultado = array(
            "res" => "OK",
            "msg" => $msg);
        return ($resultado);
    }


    function dame_fila_tabla_tarifa_agua_Espanya($parametros)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $id_tarifa = $parametros["id_tarifa"];

        $tabla_tarifas = dame_nombre_tabla_tarifas(MEDICION_AGUA);
        $consulta_tarifa = "
            SELECT *
            FROM ".$tabla_tarifas."
            WHERE
                id = '".$bd_red->_($id_tarifa)."'";
        $res_tarifa = $bd_red->ejecuta_consulta($consulta_tarifa);
        if (($res_tarifa == false) || ($res_tarifa->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_tarifa."'");
        }

        $fila_tarifa = $res_tarifa->dame_siguiente_fila();
        $tarifa = new TarifaAgua_Espanya($fila_tarifa);
        $params_fila = array(
            "tipo" => TIPO_FILA_TABLA_DATOS_DETALLES,
            "opciones" => $tarifa->dame_opciones_tabla(),
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_TARIFAS_AGUA_ESPANYA,
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_TARIFAS_AGUA_ESPANYA));
        $info_tabla = $tarifa->dame_info_tabla(MEDICION_AGUA);
        $datos_tabla = $info_tabla["datos"];
        $fila = TablaDatos::dame_fila(
            $datos_tabla,
            $params_fila);

        $id_datos = "datosTarifaAgua_Espanya__".$id_tarifa;
        $resultado = array(
            "res" => "OK",
            "id_datos" => $id_datos,
            "fila" => $fila);
        return ($resultado);
    }


    //
    // Funciones de instalacion
    //


    // Devuelve información de la instalación del sensor especificado
    function dame_datos_instalacion_sensor_agua_Espanya($id_sensor, $id_tarifa)
    {
        $idiomas = new Idiomas();

        // Se recupera el identificador de tarifa y el cups
        $fila_sensor = dame_fila_sensor($id_sensor);
        $cadena_parametros_clase = $fila_sensor["parametros_clase"];
        $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase);
        $cups = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_AGUA_ESPANYA_CUPS];

        // Datos de la instalación (CUPS)
        if ($cups == "")
        {
            $datos_instalacion["cups"] = $idiomas->_("ND");
        }
        else
        {
            $datos_instalacion["cups"] = $cups;
        }

        // Datos de la instalación (información de tarifa de agua)
        $recuperar_informacion_tarifa_agua = ($id_tarifa != ID_NINGUNO);
        $datos_instalacion["hay_informacion_tarifa_agua"] = $recuperar_informacion_tarifa_agua;
        if ($datos_instalacion["hay_informacion_tarifa_agua"] == true)
        {
            // Información de tarifa de agua
            $fila_tarifa_agua = dame_fila_tarifa(TABLA_TARIFAS_AGUA_ESPANYA, $id_tarifa);
            $descripcion = $fila_tarifa_agua["descripcion"];
            if ($descripcion == "")
            {
                $descripcion = $fila_tarifa_agua["nombre"];
            }
            $tipo = $fila_tarifa_agua["tipo"];
            $tabla_tramos_tarifa_agua = dame_tabla_tramos_tarifa_agua_Espanya($id_tarifa, $fila_tarifa_agua);

            // Datos del apartado (información de tarifa de agua)
            $datos_instalacion["descripcion"] = $descripcion;
            $datos_instalacion["descripcion_tipo"] = TarifaAgua_Espanya::dame_descripcion_tipo_tarifa_agua($tipo);
            $datos_instalacion["tabla_tramos_tarifa_agua"] = $tabla_tramos_tarifa_agua;
        }
        return ($datos_instalacion);
    }


    //
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_instalacion_agua_Espanya(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        $idiomas = new Idiomas();

        $prefijo_elemento = "elemento".$numero_elemento."-";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <table class='tabla-parametros'>
                            <tbody>
                                <tr>
                                    <td style='width:15%'><b>".$idiomas->_("CUPS").":"."</b></td>
                                    <td style='width:85%' id='".$prefijo_elemento."cups-instalacion'></td>
                                </tr>
                                <tr>
                                    <td style='width:15%'><b>".$idiomas->_("Descripción").":"."</b></td>
                                    <td style='width:85%' id='".$prefijo_elemento."descripcion-instalacion'></td>
                                </tr>
                                <tr>
                                    <td style='width:15%'><b>".$idiomas->_("Tipo").":"."</b></td>
                                    <td style='width:85%' id='".$prefijo_elemento."tipo-instalacion'></td>
                                </tr>
                                <tr>
                                    <td style='width:15%'><b>".$idiomas->_("Fecha de inicio").":"."</b></td>
                                    <td style='width:85%' id='".$prefijo_elemento."fecha-inicio-instalacion'></td>
                                </tr>
                                <tr>
                                    <td style='width:15%'><b>".$idiomas->_("Fecha de fin").":"."</b></td>
                                    <td style='width:85%' id='".$prefijo_elemento."fecha-fin-instalacion'>/td>
                                </tr>
                            </tbody>
                        </table>
                        <div class='tabla-datos100' id='".$prefijo_elemento."contenedor-tabla-tramos-tarifa-agua-instalacion'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                $html_elemento .= "
                    <div class='texto-aviso-elemento-informe' id='elemento-error-datos-elemento".$numero_elemento."' hidden></div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-sensor-seleccionado-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay sensor seleccionado")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <table class='tabla-parametros-informe-fichero'>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("CUPS").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='".$prefijo_elemento."cups-instalacion'></td>
                            </tr>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Descripción").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='".$prefijo_elemento."descripcion-instalacion'></td>
                            </tr>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='".$prefijo_elemento."tipo-instalacion'></td>
                            </tr>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='".$prefijo_elemento."fecha-fin-instalacion'></td>
                            </tr>
                        </table>
                        <br/>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='".$prefijo_elemento."contenedor-tabla-tramos-tarifa-agua-instalacion'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_instalacion_agua_Espanya(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Si no hay sensor seleccionado, se devuelve sin sensor
        if ($parametros_tipo_elemento["id_sensor"] == ID_NINGUNO)
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensor_seleccionado" => true);
            return ($resultado);
        }

        // Se recupera el identificador de tarifa del sensor
        $id_sensor = $parametros_tipo_elemento["id_sensor"];
        $id_tarifa = dame_id_tarifa_id_sensor_fecha($id_sensor, $parametros_informe["fecha_hora_inicio"]);
        $datos_elemento = dame_datos_instalacion_sensor_agua_Espanya($id_sensor, $id_tarifa);
        return ($datos_elemento);
    }
?>
