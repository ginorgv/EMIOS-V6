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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/TarifaGas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/Tarifa.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    // Crea una lista desplegable para la selección de una tarifa de gas
    function dame_control_lista_tarifas_gas_Espanya(
        $id_controles,
        $id_tarifa,
        $mostrar_etiquetas,
        $opciones_extra)
    {
        $idiomas = new Idiomas();

        $control_lista_tarifas_gas = "";
        if ($mostrar_etiquetas == true)
        {
            $control_lista_tarifas_gas .= "<div id='etiqueta_tarifa_gas_".$id_controles."'>".$idiomas->_("Tarifa de gas").": "."</div>";
        }
        $control_lista_tarifas_gas .= "
            <select id='id_tarifa_".$id_controles."'";
        $control_lista_tarifas_gas .= "
                class='chosen-select' hidden>";
        $control_lista_tarifas_gas .= dame_lista_tarifas_gas_Espanya(array($id_tarifa), $opciones_extra);
        $control_lista_tarifas_gas .= "
            </select>";

        return ($control_lista_tarifas_gas);
    }


    // Devuelve la lista de tarifas de gas
    function dame_lista_tarifas_gas_Espanya($ids_tarifas_seleccionadas, $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_tarifas_gas = "
            SELECT
                id,
                nombre,
                tipo
            FROM ".TABLA_TARIFAS_GAS_ESPANYA."
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_TARIFAS_SIN_GRUPO)
        {
            $consulta_tarifas_gas .= "
                AND (grupo = '".ID_NINGUNO."')";
        }
        $consulta_tarifas_gas .= "
            ORDER BY nombre ASC";
        $res_tarifas_gas = $bd_red->ejecuta_consulta($consulta_tarifas_gas);
        if ($res_tarifas_gas == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tarifas_gas."'");
        }

        // Identificadores de tarifas del usuario actual
        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        if ($mostrar_todos_sensores == false)
        {
            $ids_tarifas_usuario = Tarifa::dame_ids_tarifas_usuario_actual(MEDICION_GAS);
        }

        $lista_tarifas_gas = "";
        if ($opciones_extra != OPCIONES_EXTRA_LISTA_TARIFAS_SIN_NINGUNA)
        {
            switch ($opciones_extra)
            {
                case OPCIONES_EXTRA_LISTA_TARIFAS_TARIFA_VIGENTE_SEGUN_FECHAS:
                {
                    $lista_tarifas_gas .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Tarifa vigente según fechas")."</option>";
                    break;
                }
                case OPCIONES_EXTRA_LISTA_TARIFAS_ACTUAL:
                {
                    $lista_tarifas_gas .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Actual")."</option>";
                    break;
                }
                default:
                {
                    $lista_tarifas_gas .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguna")."</option>";
                    break;
                }
            }
        }
        while ($fila_tarifa_gas = $res_tarifas_gas->dame_siguiente_fila())
        {
            $anyadir_tarifa = false;
            $caracteristicas_tipo_tarifa_gas = TarifaGas_Espanya::dame_caracteristicas_tipo_tarifa_gas($fila_tarifa_gas['tipo']);
            switch ($opciones_extra)
            {
                case OPCIONES_EXTRA_LISTA_TARIFAS_GAS_TIPO_CALCULO_COSTE_TERMINO_FIJO_CON_EXCESOS:
                {
                    if ($caracteristicas_tipo_tarifa_gas["tipo_calculo_coste_termino_fijo"] != TIPO_CALCULO_COSTE_TERMINO_FIJO_SIN_EXCESOS)
                    {
                        $anyadir_tarifa = true;
                    }
                    break;
                }
                default:
                {
                    $anyadir_tarifa = true;
                    break;
                }
            }

            if ($anyadir_tarifa == true)
            {
                if ($mostrar_todos_sensores == false)
                {
                    if (in_array($fila_tarifa_gas['id'], $ids_tarifas_usuario) == false)
                    {
                        $anyadir_tarifa = false;
                    }
                }
            }

            if ($anyadir_tarifa == true)
            {
                $lista_tarifas_gas .= "<option value='".$fila_tarifa_gas['id']."'";
                if (in_array($fila_tarifa_gas['id'], $ids_tarifas_seleccionadas) == true)
                {
                    $lista_tarifas_gas .= " selected";
                }
                $lista_tarifas_gas .= ">".htmlspecialchars($fila_tarifa_gas['nombre'], ENT_QUOTES)."</option>";
            }
        }

        return ($lista_tarifas_gas);
    }


    // Devuelve la lista de tarifas de gas de un tipo
    function dame_lista_tarifas_tipo_gas_Espanya($tipo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_tarifas_gas = "
            SELECT
                id,
                nombre,
                tipo
            FROM ".TABLA_TARIFAS_GAS_ESPANYA."
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($tipo != TIPO_TARIFA_TODOS)
        {
            $consulta_tarifas_gas .= "
                AND (tipo = '".$bd_red->_($tipo)."')";
        }
        $consulta_tarifas_gas .= "
            ORDER BY nombre ASC";
        $res_tarifas_gas = $bd_red->ejecuta_consulta($consulta_tarifas_gas);
        if ($res_tarifas_gas == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tarifas_gas."'");
        }

        // Identificadores de tarifas del usuario actual
        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        if ($mostrar_todos_sensores == false)
        {
            $ids_tarifas_usuario = Tarifa::dame_ids_tarifas_usuario_actual(MEDICION_GAS);
        }

        while ($fila_tarifa_gas = $res_tarifas_gas->dame_siguiente_fila())
        {
            $anyadir_tarifa = true;
            if ($mostrar_todos_sensores == false)
            {
                if (in_array($fila_tarifa_gas['id'], $ids_tarifas_usuario) == false)
                {
                    $anyadir_tarifa = false;
                }
            }

            if ($anyadir_tarifa == true)
            {
                $lista_tarifas_gas .= "<option value='".$fila_tarifa_gas['id']."'>".
                    htmlspecialchars($fila_tarifa_gas['nombre'], ENT_QUOTES)."</option>";
            }
        }
        return ($lista_tarifas_gas);
    }


    // Devuelve la lista de tipos de tarifa de gas
    function dame_lista_tipos_tarifa_gas_Espanya($tipo_seleccionado, $opciones_extra)
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
        $tipos_tarifa = TarifaGas_Espanya::dame_tipos_tarifa_gas();
        foreach ($tipos_tarifa as $tipo_tarifa)
        {
            $nombre_tipo_tarifa = TarifaGas_Espanya::dame_descripcion_tipo_tarifa_gas($tipo_tarifa);
            $lista .= "<option value='".$tipo_tarifa."'";
			if ($tipo_tarifa == $tipo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_tipo_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de tipos de alquiler de contador
    function dame_lista_tipos_alquiler_contador_tarifa_gas_Espanya($tipo_seleccionado)
    {
        $tipos_alquiler_contador_tarifa = TarifaGas_Espanya::dame_tipos_alquiler_contador_tarifa_gas();
        foreach ($tipos_alquiler_contador_tarifa as $tipo_alquiler_contador_tarifa)
        {
            $nombre_tipo_alquiler_contador_tarifa = TarifaGas_Espanya::dame_descripcion_tipo_alquiler_contador_tarifa_gas($tipo_alquiler_contador_tarifa);
            $lista .= "<option value='".$tipo_alquiler_contador_tarifa."'";
			if ($tipo_alquiler_contador_tarifa == $tipo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_tipo_alquiler_contador_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve el contenido de las pestañas de la ventana de administración de tarifas de gas
    function dame_contenido_pestanyas_ventana_administracion_tarifas_gas_Espanya(
        $tipo_administracion,
        $id_tarifa,
        $nombre,
        $descripcion,
        $tipo,
        $id_grupo,
        $expiracion,
        $cadena_fecha_expiracion_local_local,
        $numero_dias_preaviso_expiracion,
        $factor_conversion,
        $precio_consumo,
        $precio_caudal_diario,
        $caudal_diario,
        $precio_termino_fijo_diario,
        $impuesto_gas,
        $tipo_alquiler_contador,
        $alquiler_contador,
        $iva)
    {
        $idiomas = new Idiomas();

        // Unidad de medida de coste
        $unidad_medida_coste = $_SESSION["moneda"];

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

        // Se crea el contenido de las pestañas de tarifas de gas
        $contenido = "
            <div id='tabs-administracion-tarifa-gas' class='tabbable' tipo-administracion='".$tipo_administracion."'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-parametros' id='titulo-tab-parametros'>".$idiomas->_("Caudales y precios")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-factura' id='titulo-tab-factura'>".$idiomas->_("Factura")."</a></li>
                </ul>
                <div id='tabs-content-administracion-tarifa-gas' class='tab-content'>";

        // Contenido de pestaña principal (diferente para una y múltiples tarifas)
        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

        // Nombre y descripción (única tarifa)
        if ($tipo_administracion == TIPO_ADMINISTRACION_TARIFAS_UNICA)
        {
            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
                        <input type='text' id='nombre_tarifa_gas'
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
                        <textarea id='descripcion_tarifa_gas'
                            class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                    </div>
                </div>";
        }

        // Tipo de tarifa de gas
        switch ($tipo_administracion)
        {
            case TIPO_ADMINISTRACION_TARIFAS_UNICA:
            {
                $opciones_extra_lista_tipos_tarifa_gas = OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_NINGUNO;
                break;
            }
            case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE:
            {
                $opciones_extra_lista_tipos_tarifa_gas = OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_TODOS;
                break;
            }
        }
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
                    <select id='tipo_tarifa_gas' class='select-administracion'>";
        $contenido .= dame_lista_tipos_tarifa_gas_Espanya($tipo, $opciones_extra_lista_tipos_tarifa_gas);
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
            $contenido .= dame_lista_grupos_tarifas(MEDICION_GAS, $id_grupo, OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_NINGUNO);
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
                        <div id='select_tarifas_gas_tarifa_gas_no_visible' hidden></div>
                        <select id='ids_tarifas_gas_tarifa_gas'
                            name='ids_tarifas_gas_tarifa_gas'
                            max_selected='".ID_NINGUNO."' multiple='multiple'
                            class='select-administracion' hidden>";
            $contenido .= dame_lista_tarifas_tipo_gas_Espanya($tipo);
            $contenido .= "
                        </select>
                    </div>
                </div>";
        }

        $contenido .= "
                    </div>";

        // Contenido de pestaña de parámetros
        $contenido .= "
                    <div class='tab-pane' id='tab-parametros'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Factor de conversión")." (".$idiomas->_("kWh")."/".$idiomas->_("m3").")".": "."</span><br/>
                    <input type='text' id='factor_conversion_tarifa_gas'
                        class='TLNT_input_mandatory TLNT_input_float input-administracion' value='".$factor_conversion."'>
                </div>
            </div>

						<div class='row-fluid' id='control_precio_consumo_tarifa_gas'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Precio de consumo")." (".$idiomas->_("€")."/".$idiomas->_("kWh").")".": "."</span><br/>
                    <input type='text' id='precio_consumo_tarifa_gas'
                        class='TLNT_input_float input-administracion' value='".$precio_consumo."'>
                </div>
            </div>

            <div class='row-fluid' id='control_precio_caudal_diario_tarifa_gas'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Precio de caudal diario")." (".$idiomas->_("€")."/".$idiomas->_("kWh").")".": "."</span><br/>
                    <input type='text' id='precio_caudal_diario_tarifa_gas'
                        class='TLNT_input_float input-administracion' value='".$precio_caudal_diario."'>
                </div>
            </div>

            <div class='row-fluid' id='control_caudal_diario_tarifa_gas'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Caudal diario contratado")." (".$idiomas->_("kWh").")".": "."</span><br/>
                    <input type='text' id='caudal_diario_tarifa_gas'
                        class='TLNT_input_float input-administracion' value='".$caudal_diario."'>
                </div>
            </div>

            <div class='row-fluid' id='control_precio_termino_fijo_diario_tarifa_gas'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Precio de término fijo diario")." (".$idiomas->_("€").")".": "."</span><br/>
                    <input type='text' id='precio_termino_fijo_diario_tarifa_gas'
                        class='TLNT_input_float input-administracion' value='".$precio_termino_fijo_diario."'>
                </div>
            </div>";

				// Contenido de la pestaña para las tarifas 2021 - 2022
				$contenido .= "
						<div class='row-fluid' id='control_capacidad_contratada_gas'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Capacidad contratada")." (".$idiomas->_("kWh")."/".$idiomas->_("día").")".": "."</span><br/>
                    <input type='text' id='capacidad_contratada_tarifa_gas'
                        class='TLNT_input_float input-administracion' value='".$caudal_diario."'>
                </div>
            </div>

            <div class='row-fluid' id='control_termino_fijo_gas'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Término fijo")." (".$idiomas->_("€")."/".$idiomas->_("kWh")."/".$idiomas->_("día")."/".$idiomas->_("año").")".": "."</span><br/>
                    <input type='text' id='termino_fijo_tarifa_gas'
                        class='TLNT_input_float input-administracion' value='".$precio_termino_fijo_diario."'>
                </div>
            </div>
			<div class='row-fluid' id='control_termino_fijo_gas_por_cliente'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Término fijo")." (".$idiomas->_("€")."/".$idiomas->_("Cliente")."/".$idiomas->_("año").")".": "."</span><br/>
                    <input type='text' id='termino_fijo_tarifa_gas_por_cliente'
                        class='TLNT_input_float input-administracion' value='".$precio_termino_fijo_diario."'>
                </div>
            </div>

            <div class='row-fluid' id='control_termino_variable_gas'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Término variable")." (".$idiomas->_("€")."/".$idiomas->_("kWh").")".": "."</span><br/>
                    <input type='text' id='termino_variable_tarifa_gas'
                        class='TLNT_input_float input-administracion' value='".$precio_consumo."'>
                </div>
            </div>

            <div class='row-fluid' id='control_excesos_demanda_gas'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Exceso de demanda")." (".$idiomas->_("€")."/".$idiomas->_("kWh")."/".$idiomas->_("día")."/".$idiomas->_("año").")".": "."</span><br/>
                    <input type='text' id='exceso_demanda_tarifa_gas'
                        class='TLNT_input_float input-administracion' value='".$precio_caudal_diario."'>
                </div>
            </div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de factura
        $contenido .= "
                    <div class='tab-pane' id='tab-factura'>";

		$contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Impuesto de gas")." (".$idiomas->_("€")."/".$idiomas->_("kWh").")".": "."</span><br/>
					<input type='text' id='impuesto_gas_tarifa_gas'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$impuesto_gas."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de alquiler de contador").": "."</span><br/>
					<select id='tipo_alquiler_contador_tarifa_gas' class='select-administracion'>";
        $contenido .= dame_lista_tipos_alquiler_contador_tarifa_gas_Espanya($tipo_alquiler_contador);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Alquiler de contador")." (".$unidad_medida_coste.")".": "."</span><br/>
					<input type='text' id='alquiler_contador_tarifa_gas'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$alquiler_contador."'>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("IVA")." (%)".": "."</span><br/>
					<input type='text' id='iva_tarifa_gas'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$iva."'>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        $contenido .= "
                </div>
            </div>";
        return ($contenido);
    }


    // Devuelve la tabla de parámetros de una tarifa de gas
    function dame_tabla_parametros_tarifa_gas_Espanya($id_tarifa, $fila_tarifa_gas, $incluir_salto_linea = true)
    {
        $idiomas = new Idiomas();

        if ($fila_tarifa_gas === NULL)
        {
            $fila_tarifa_gas = dame_fila_tarifa(TABLA_TARIFAS_GAS_ESPANYA, $id_tarifa);
        }
        $tipo_tarifa_gas = $fila_tarifa_gas["tipo"];
        $caracteristicas_tipo_tarifa_gas = TarifaGas_Espanya::dame_caracteristicas_tipo_tarifa_gas($tipo_tarifa_gas);
        $tipo_calculo_coste_termino_fijo = $caracteristicas_tipo_tarifa_gas["tipo_calculo_coste_termino_fijo"];
        switch ($tipo_calculo_coste_termino_fijo)
        {
            case TIPO_CALCULO_COSTE_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES:
            {
                $numero_columnas = NUMERO_COLUMNAS_TABLA_PARAMETROS_TARIFA_GAS_TIPO_CALCULO_TERMINO_FIJO_EXCESOS_MAXIMOS_MENSUALES;
                $cabecera = array(
                    $idiomas->_("Factor de conversión"),
                    $idiomas->_("Precio de consumo"),
                    $idiomas->_("Precio de caudal diario"),
                    $idiomas->_("Caudal diario contratado"));
                $factor_conversion = formatea_numero($fila_tarifa_gas["factor_conversion"], 5).
                    " ".$idiomas->_("kWh")."/".$idiomas->_("m3");
                $precio_consumo = formatea_numero($fila_tarifa_gas["precio_consumo"], 8).
                    " ".$idiomas->_("€")."/".$idiomas->_("kWh");
                $precio_caudal_diario = formatea_numero($fila_tarifa_gas["precio_caudal_diario"], 8).
                    " ".$idiomas->_("€")."/".$idiomas->_("kWh");
                $caudal_diario = formatea_numero($fila_tarifa_gas["caudal_diario"], 5).
                    " ".$idiomas->_("kWh");
                $fila_datos = array(
                    $factor_conversion,
                    $precio_consumo,
                    $precio_caudal_diario,
                    $caudal_diario
                );
                break;
            }
            case TIPO_CALCULO_COSTE_TERMINO_FIJO_SIN_EXCESOS:
            {
                $numero_columnas = NUMERO_COLUMNAS_TABLA_PARAMETROS_TARIFA_GAS_TIPO_CALCULO_TERMINO_FIJO_SIN_EXCESOS;
                $cabecera = array(
                    $idiomas->_("Factor de conversión"),
                    $idiomas->_("Precio de consumo"),
                    $idiomas->_("Precio de término fijo diario"));
                $factor_conversion = formatea_numero($fila_tarifa_gas["factor_conversion"], 5).
                    " ".$idiomas->_("kWh")."/".$idiomas->_("m3");
                $precio_consumo = formatea_numero($fila_tarifa_gas["precio_consumo"], 8).
                    " ".$idiomas->_("€")."/".$idiomas->_("kWh");
                $precio_termino_fijo_diario = formatea_numero($fila_tarifa_gas["precio_termino_fijo_diario"], 8).
                    " ".$idiomas->_("€");
                $fila_datos = array(
                    $factor_conversion,
                    $precio_consumo,
                    $precio_termino_fijo_diario
                );
                break;
            }
			case TIPO_CALCULO_COSTE_TARIFAS_2021:
            {
                $numero_columnas = NUMERO_COLUMNAS_TABLA_PARAMETROS_TARIFA_GAS_TIPO_CALCULO_TARIFAS_2021;
								$cabecera = array(
                    $idiomas->_("Factor de conversión"),
                    $idiomas->_("Precio término variable"),
                    $idiomas->_("Precio término fijo"),
                    $idiomas->_("Caudal diario contratado"),
										$idiomas->_("Precio capacidad demandada"));
                $factor_conversion = formatea_numero($fila_tarifa_gas["factor_conversion"], 5).
                    " ".$idiomas->_("kWh")."/".$idiomas->_("m3");
                $precio_consumo = formatea_numero($fila_tarifa_gas["precio_consumo"], 8).
                    " ".$idiomas->_("€")."/".$idiomas->_("kWh");
                $precio_caudal_diario = formatea_numero($fila_tarifa_gas["precio_termino_fijo_diario"], 8).
                    " ".$idiomas->_("€")."/".$idiomas->_("kWh")."/".$idiomas->_("Día")."/".$idiomas->_("Año");
                $caudal_diario = formatea_numero($fila_tarifa_gas["caudal_diario"], 5).
                    " ".$idiomas->_("kWh");
				$precio_capacidad_demandada = formatea_numero($fila_tarifa_gas["precio_caudal_diario"], 8).
		            " ".$idiomas->_("€")."/".$idiomas->_("kWh");
                $fila_datos = array(
                    $factor_conversion,
                    $precio_consumo,
                    $precio_caudal_diario,
                    $caudal_diario,
					$precio_capacidad_demandada
                );
                break;
            }
			case TIPO_CALCULO_COSTE_POR_CLIENTE:
            {
                $numero_columnas = NUMERO_COLUMNAS_TABLA_PARAMETROS_TARIFA_GAS_TIPO_CALCULO_TF_POR_CLIENTE;
				$cabecera = array(
                    $idiomas->_("Factor de conversión"),
                    $idiomas->_("Precio término variable"),
                    $idiomas->_("Precio término fijo"));
                $factor_conversion = formatea_numero($fila_tarifa_gas["factor_conversion"], 5).
                    " ".$idiomas->_("kWh")."/".$idiomas->_("m3");
                $precio_consumo = formatea_numero($fila_tarifa_gas["precio_consumo"], 8).
                    " ".$idiomas->_("€")."/".$idiomas->_("kWh");
                $precio_caudal_diario = formatea_numero($fila_tarifa_gas["precio_termino_fijo_diario"], 8).
                    " ".$idiomas->_("€")."/".$idiomas->_("Cliente")."/".$idiomas->_("Año");
                $fila_datos = array(
                    $factor_conversion,
                    $precio_consumo,
                    $precio_caudal_diario,
                );
                break;
            }
            default:
            {
                throw new Exception("Tipo de cálculo de coste de término fijo desconocido: '".$tipo_calculo_coste_termino_fijo."'");
            }
        }
        $titulo_tabla_parametros_tarifa_gas = $idiomas->_("Caudales y precios");
        $params_tabla = array(
            "numero_columnas" => $numero_columnas,
            "generar_valores_xml" => true
        );
        $tabla = new TablaDatos(
            "tabla-parametros-tarifa-gas",
            $titulo_tabla_parametros_tarifa_gas,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $tabla->anyade_cabecera("", $cabecera);
        $tabla->anyade_fila("", $fila_datos);

        return ($tabla->dame_tabla($incluir_salto_linea));
    }


    //
    // Funciones para las secciones de tarifas
    //


    function dame_tabla_filtro_tarifas_tabla_gas_Espanya()
    {
        $idiomas = new Idiomas();

        // Se recuperan los controles a mostrar
        $controles = array();
        $id_controles = "smartmeter_filtro_tarifas_tabla_gas_Espanya";

        // Tipos de tarifa de gas
        $control_lista_tipos .= "<div id='etiqueta_tipo_tarifa_gas_".$id_controles."'>".$idiomas->_("Tipo").": "."</div>";
        $control_lista_tipos .= "<select id='tipo_tarifa_gas_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_tipos .= dame_lista_tipos_tarifa_gas_Espanya(TIPO_TARIFA_TODOS, OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_TODOS);
        $control_lista_tipos .= "</select>";
        array_push($controles, $control_lista_tipos);

        // Grupos de tarifas de gas
        $control_lista_grupos .= "<div id='etiqueta_grupo_tarifas_gas_".$id_controles."'>".$idiomas->_("Grupo").": "."</div>";
        $control_lista_grupos .= "<select id='id_grupo_tarifas_gas_".$id_controles."' class='chosen-select' hidden>";
        $control_lista_grupos .= dame_lista_grupos_tarifas(MEDICION_GAS, ID_TODOS, OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_TODOS_NINGUNO);
        $control_lista_grupos .= "</select>";
        array_push($controles, $control_lista_grupos);

        // Estado de tarifas (de expiración)
        $control_lista_estados .= dame_control_lista_estados_tarifa($id_controles, $idiomas->_("Estado"));
        array_push($controles, $control_lista_estados);

        // Nombre
        $filtro_tarifas_gas = dame_filtro_texto_controles_extra($id_controles, $idiomas->_("Nombre"), $controles);

        // Se crea la tabla contenedora
        $tabla = new TablaDatos(
            "tabla-smartmeter-filtro-tarifas-gas-tabla",
            $idiomas->_("Filtro de tarifas"),
            TIPO_TABLA_DATOS_CONTENEDOR
        );

        $params_fila = array(
            "clase_dato" => "filtro-informes",
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_TARIFAS_GAS_TABLA_ESPANYA)
        );
        $tabla->anyade_fila("filtro-tarifas-gas-tabla", $filtro_tarifas_gas, $params_fila);

        return ($tabla->dame_tabla());
    }


    function guarda_fecha_recalculo_datos_gas_Espanya($parametros)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $ids_tarifas = $parametros["ids_tarifas"];
        $ids_grupos_tarifas = $parametros["ids_grupos_tarifas"];
        $cadena_fecha_hora_local_local = $parametros["fecha_hora"];

        // Zona horaria local
        $zona_horaria = dame_zona_horaria_local();

        // Flag para guardar la fecha de recálculo
        $guardar_fecha_recalculo = true;

        // Se comprueba el número de días de recálculos (sólo si el usuario no es superadministrador)
        if ($_SESSION["perfil"] != PERFIL_USUARIO_SUPERADMINISTRADOR)
        {
            $fecha_hora_actual_local = dame_fecha_hora_actual_local();
            $fecha_hora_local = convierte_cadena_a_fecha($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria);
            if ($fecha_hora_local <= $fecha_hora_actual_local)
            {
                $numero_dias_recalculo = $fecha_hora_actual_local->diff($fecha_hora_local)->days + 1;
                if ($numero_dias_recalculo > NUMERO_MAXIMO_DIAS_RECALCULO_DATOS)
                {
                    $res = "ERROR";
                    $msg = $idiomas->_("El número de días de recálculo es mayor que el máximo permitido")." (".NUMERO_MAXIMO_DIAS_RECALCULO_DATOS.")";
                    $guardar_fecha_recalculo = false;
                }
            }
        }

        if ($guardar_fecha_recalculo == true)
        {
            // Se recuperan los sensores de gas correspondientes a las tarifas o grupos de tarifas seleccionados
            $cadena_ids_tarifas_gas_consulta = dame_cadena_ids_consulta($ids_tarifas);
            $cadena_ids_grupos_tarifas_gas_consulta = dame_cadena_ids_consulta($ids_grupos_tarifas);
            $consulta_sensores_gas = "
                SELECT
                    id,
                    nombre,
                    tipo,
                    granularidad_cuartohoraria
                FROM sensores
                WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (clase = '".CLASE_SENSOR_GAS."')
                    AND ((SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_TARIFA_GAS + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) IN (".$cadena_ids_tarifas_gas_consulta."))
                        OR (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_GRUPO_TARIFAS_GAS + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) IN (".$cadena_ids_grupos_tarifas_gas_consulta.")))";
            $res_sensores_gas = $bd_red->ejecuta_consulta($consulta_sensores_gas);
            if ($res_sensores_gas == false)
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensores_gas."'");
            }
            $ids_sensores_gas = array();
            $info_sensores_gas = array();
            while ($fila_sensor_gas = $res_sensores_gas->dame_siguiente_fila())
            {
                array_push($ids_sensores_gas, $fila_sensor_gas["id"]);
                $info_sensor_gas = array(
                    "nombre" => $fila_sensor_gas["nombre"],
                    "tipo" => $fila_sensor_gas["tipo"],
                    "granularidad_cuartohoraria" => $fila_sensor_gas["granularidad_cuartohoraria"]);
                array_push($info_sensores_gas, $info_sensor_gas);
            }

            // Conversión de fecha
            $cadena_fecha_hora_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
            $cadena_fecha_hora_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

            // Se actualizan las horas de recálculos de gas
            if (count($info_sensores_gas) > 0)
            {
                actualiza_hora_tablas_recalculos_valores_clase_sensores(
                    $cadena_fecha_hora_base_datos_utc,
                    CLASE_SENSOR_GAS,
                    $info_sensores_gas);
            }

            $res = "OK";
            $msg = $idiomas->_("Fecha de inicio de recálculo de datos guardada correctamente").".\n".
                $idiomas->_("Los datos se recalcularán en el siguiente procesado de datos. Esto puede tardar unos minutos");
        }

        $resultado = array(
            "res" => $res,
            "msg" => $msg);
        return ($resultado);
    }


    function asigna_tarifa_grupo_tarifas_sensores_gas_Espanya($parametros)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $id_tarifa = $parametros["id_tarifa"];
        $id_grupo_tarifas = $parametros["id_grupo_tarifas"];
        $ids_sensores = $parametros["ids_sensores"];

        // Se recuperan los sensores de gas correspondientes a las tarifas seleccionadas
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
            $id_tarifa_sensor = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_TARIFA_GAS];
            $id_grupo_tarifas_sensor = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_ID_GRUPO_TARIFAS_GAS];
            if (($id_tarifa == $id_tarifa_sensor) && ($id_grupo_tarifas == $id_grupo_tarifas_sensor))
            {
                continue;
            }

            // Parámetros de clase con la nueva tarifa o grupo de tarifas
            $cadena_parametros_clase_modificados = implode(SEPARADOR_PARAMETROS_COMPUESTOS, array(
                $id_tarifa,
                $id_grupo_tarifas,
                $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_CUPS]));

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


    function dame_fila_tabla_tarifa_gas_Espanya($parametros)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $id_tarifa = $parametros["id_tarifa"];

        $tabla_tarifas = dame_nombre_tabla_tarifas(MEDICION_GAS);
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
        $tarifa = new TarifaGas_Espanya($fila_tarifa);
        $params_fila = array(
            "tipo" => TIPO_FILA_TABLA_DATOS_DETALLES,
            "opciones" => $tarifa->dame_opciones_tabla(),
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_TARIFAS_GAS_ESPANYA,
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_TARIFAS_GAS_ESPANYA));
        $info_tabla = $tarifa->dame_info_tabla(MEDICION_GAS);
        $datos_tabla = $info_tabla["datos"];
        $fila = TablaDatos::dame_fila(
            $datos_tabla,
            $params_fila);

        $id_datos = "datosTarifaGas_Espanya__".$id_tarifa;
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
    function dame_datos_instalacion_sensor_gas_Espanya($id_sensor, $id_tarifa)
    {
        $idiomas = new Idiomas();

        // Se recupera el identificador de tarifa y el cups
        $fila_sensor = dame_fila_sensor($id_sensor);
        $cadena_parametros_clase = $fila_sensor["parametros_clase"];
        $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase);
        $cups = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_GAS_ESPANYA_CUPS];

        // Datos de la instalación (CUPS)
        if ($cups == "")
        {
            $datos_instalacion["cups"] = $idiomas->_("ND");
        }
        else
        {
            $datos_instalacion["cups"] = $cups;
        }

        // Datos de la instalación (información de tarifa de gas)
        $recuperar_informacion_tarifa_gas = ($id_tarifa != ID_NINGUNO);
        $datos_instalacion["hay_informacion_tarifa_gas"] = $recuperar_informacion_tarifa_gas;
        if ($datos_instalacion["hay_informacion_tarifa_gas"] == true)
        {
            // Información de tarifa de gas
            $fila_tarifa_gas = dame_fila_tarifa(TABLA_TARIFAS_GAS_ESPANYA, $id_tarifa);
            $descripcion = $fila_tarifa_gas["descripcion"];
            if ($descripcion == "")
            {
                $descripcion = $fila_tarifa_gas["nombre"];
            }
            $tipo = $fila_tarifa_gas["tipo"];
            $tabla_parametros_tarifa_gas = dame_tabla_parametros_tarifa_gas_Espanya($id_tarifa, $fila_tarifa_gas);

            // Datos del apartado (información de tarifa de gas)
            $datos_instalacion["descripcion"] = $descripcion;
            $datos_instalacion["descripcion_tipo"] = TarifaGas_Espanya::dame_descripcion_tipo_tarifa_gas($tipo);
            $datos_instalacion["tabla_parametros_tarifa_gas"] = $tabla_parametros_tarifa_gas;
        }
        return ($datos_instalacion);
    }


    //
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_instalacion_gas_Espanya(
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
                        <div class='tabla-datos100' id='".$prefijo_elemento."contenedor-tabla-parametros-tarifa-gas-instalacion'></div>
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
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='".$prefijo_elemento."contenedor-tabla-parametros-tarifa-gas-instalacion'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_instalacion_gas_Espanya(
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
        $datos_elemento = dame_datos_instalacion_sensor_gas_Espanya($id_sensor, $id_tarifa);
        return ($datos_elemento);
    }
?>
