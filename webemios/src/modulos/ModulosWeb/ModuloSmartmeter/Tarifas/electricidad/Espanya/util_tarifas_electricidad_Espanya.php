<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
	include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/TarifaElectrica_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/Tarifa.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    // Crea una lista desplegable para la selección de una tarifa eléctrica
    function dame_control_lista_tarifas_electricidad_Espanya(
        $id_controles,
        $id_tarifa,
        $mostrar_etiquetas,
        $opciones_extra)
    {
        $idiomas = new Idiomas();

        $control_lista_tarifas_electricas = "";
        if ($mostrar_etiquetas == true)
        {
            $control_lista_tarifas_electricas .= "<div id='etiqueta_tarifa_electrica_".$id_controles."'>".$idiomas->_("Tarifa eléctrica").": "."</div>";
        }
        $control_lista_tarifas_electricas .= "
            <select id='id_tarifa_".$id_controles."'";
        $control_lista_tarifas_electricas .= "
                class='chosen-select' hidden>";
        $control_lista_tarifas_electricas .= dame_lista_tarifas_electricidad_Espanya(array($id_tarifa), $opciones_extra);
        $control_lista_tarifas_electricas .= "
            </select>";

        return ($control_lista_tarifas_electricas);
    }


    // Devuelve la lista de tarifas eléctricas
    function dame_lista_tarifas_electricidad_Espanya($ids_tarifas_seleccionadas, $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_tarifas_electricas = "
            SELECT
                id,
                nombre,
                tipo
            FROM ".TABLA_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_TARIFAS_SIN_GRUPO)
        {
            $consulta_tarifas_electricas .= "
                AND (grupo = '".ID_NINGUNO."')";
        }
        $consulta_tarifas_electricas .= "
            ORDER BY nombre ASC";
        $res_tarifas_electricas = $bd_red->ejecuta_consulta($consulta_tarifas_electricas);
        if ($res_tarifas_electricas == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tarifas_electricas."'");
        }

        // Identificadores de tarifas del usuario actual
        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        if ($mostrar_todos_sensores == false)
        {
            $ids_tarifas_usuario = Tarifa::dame_ids_tarifas_usuario_actual(MEDICION_ELECTRICIDAD);
        }

        $lista_tarifas_electricas = "";
        if ($opciones_extra != OPCIONES_EXTRA_LISTA_TARIFAS_SIN_NINGUNA)
        {
            switch ($opciones_extra)
            {
                case OPCIONES_EXTRA_LISTA_TARIFAS_TARIFA_VIGENTE_SEGUN_FECHAS:
                {
                    $lista_tarifas_electricas .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Tarifa vigente según fechas")."</option>";
                    break;
                }
                case OPCIONES_EXTRA_LISTA_TARIFAS_ACTUAL:
                {
                    $lista_tarifas_electricas .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Actual")."</option>";
                    break;
                }
                default:
                {
                    $lista_tarifas_electricas .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Ninguna")."</option>";
                    break;
                }
            }
        }
        while ($fila_tarifa_electrica = $res_tarifas_electricas->dame_siguiente_fila())
        {
            $anyadir_tarifa = false;
            $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($fila_tarifa_electrica['tipo']);
            switch ($opciones_extra)
            {
                case OPCIONES_EXTRA_LISTA_TARIFAS_ELECTRICAS_TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES:
                {
                    if ($caracteristicas_tipo_tarifa_electrica["tipo_calculo_coste_potencias"] == TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES)
                    {
                        $anyadir_tarifa = true;
                    }
                    break;
                }
                case OPCIONES_EXTRA_LISTA_TARIFAS_ELECTRICAS_TIPO_CALCULO_COSTE_POTENCIAS_CON_EXCESOS:
                {
                    if ($caracteristicas_tipo_tarifa_electrica["tipo_calculo_coste_potencias"] != TIPO_CALCULO_COSTE_POTENCIAS_SIN_EXCESOS)
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
                    if (in_array($fila_tarifa_electrica['id'], $ids_tarifas_usuario) == false)
                    {
                        $anyadir_tarifa = false;
                    }
                }
            }

            if ($anyadir_tarifa == true)
            {
                $lista_tarifas_electricas .= "<option value='".$fila_tarifa_electrica['id']."'";
                if (in_array($fila_tarifa_electrica['id'], $ids_tarifas_seleccionadas) == true)
                {
                    $lista_tarifas_electricas .= " selected";
                }
                $lista_tarifas_electricas .= ">".htmlspecialchars($fila_tarifa_electrica['nombre'], ENT_QUOTES)."</option>";
            }
        }

        return ($lista_tarifas_electricas);
    }


    // Devuelve la lista de tarifas eléctricas de un tipo y contrato
    function dame_lista_tarifas_tipo_contrato_electricidad_Espanya($tipo, $contrato)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_tarifas_electricas = "
            SELECT
                id,
                nombre,
                tipo
            FROM ".TABLA_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                (red = '".$_SESSION["id_red"]."')";
        if ($tipo != TIPO_TARIFA_TODOS)
        {
            $consulta_tarifas_electricas .= "
                AND (tipo = '".$bd_red->_($tipo)."')";
        }
        if ($contrato != CONTRATO_TARIFA_ELECTRICA_TODOS)
        {
            $consulta_tarifas_electricas .= "
                AND (contrato = '".$bd_red->_($contrato)."')";
        }
        $consulta_tarifas_electricas .= "
            ORDER BY nombre ASC";
        $res_tarifas_electricas = $bd_red->ejecuta_consulta($consulta_tarifas_electricas);
        if ($res_tarifas_electricas == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tarifas_electricas."'");
        }

        // Identificadores de tarifas del usuario actual
        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        if ($mostrar_todos_sensores == false)
        {
            $ids_tarifas_usuario = Tarifa::dame_ids_tarifas_usuario_actual(MEDICION_ELECTRICIDAD);
        }

        while ($fila_tarifa_electrica = $res_tarifas_electricas->dame_siguiente_fila())
        {
            $anyadir_tarifa = true;
            if ($mostrar_todos_sensores == false)
            {
                if (in_array($fila_tarifa_electrica['id'], $ids_tarifas_usuario) == false)
                {
                    $anyadir_tarifa = false;
                }
            }

            if ($anyadir_tarifa == true)
            {
                $lista_tarifas_electricas .= "<option value='".$fila_tarifa_electrica['id']."'>".
                    htmlspecialchars($fila_tarifa_electrica['nombre'], ENT_QUOTES)."</option>";
            }
        }
        return ($lista_tarifas_electricas);
    }


    // Devuelve la lista de tipos de tarifa eléctrica
    function dame_lista_tipos_tarifa_electricidad_Espanya($tipo_seleccionado, $opciones_extra)
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
        $tipos_tarifa = TarifaElectrica_Espanya::dame_tipos_tarifa_electrica();
        foreach ($tipos_tarifa as $tipo_tarifa)
        {
            $nombre_tipo_tarifa = TarifaElectrica_Espanya::dame_descripcion_tipo_tarifa_electrica($tipo_tarifa);
            $lista .= "<option value='".$tipo_tarifa."'";
			if ($tipo_tarifa == $tipo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_tipo_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de contratos de tarifa eléctrica
    function dame_lista_contratos_tarifa_electricidad_Espanya($contrato_seleccionado, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $lista = "";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_TODOS)
        {
            $lista .= "<option value='".CONTRATO_TARIFA_ELECTRICA_TODOS."'>".$idiomas->_("Todos")."</option>";
        }
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_NINGUNO)
        {
            $lista .= "<option value='".CONTRATO_TARIFA_ELECTRICA_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        }
        $contratos_tarifa = TarifaElectrica_Espanya::dame_contratos_tarifa_electrica();
        foreach ($contratos_tarifa as $contrato_tarifa)
        {
            $nombre_contrato_tarifa = TarifaElectrica_Espanya::dame_descripcion_contrato_tarifa_electrica($contrato_tarifa);
            $lista .= "<option value='".$contrato_tarifa."'";
			if ($contrato_tarifa == $contrato_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_contrato_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de bonificaciones de 85 % de tarifa eléctrica
    function dame_lista_bonificaciones_85_tarifa_electricidad_Espanya($bonificacion_85_seleccionada, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $lista = "";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_BONIFICACION_85_TARIFA_ELECTRICA_NINGUNA)
        {
            $lista .= "<option value='".BONIFICACION_85_TARIFA_ELECTRICA_NINGUNA."'>".$idiomas->_("Ninguna")."</option>";
        }
        $bonificaciones_85_tarifa = TarifaElectrica_Espanya::dame_bonificaciones_85_tarifa_electrica();
        foreach ($bonificaciones_85_tarifa as $bonificacion_85_tarifa)
        {
            $nombre_bonificacion_85_tarifa = TarifaElectrica_Espanya::dame_descripcion_bonificacion_85_tarifa_electrica($bonificacion_85_tarifa);
            $lista .= "<option value='".$bonificacion_85_tarifa."'";
			if ($bonificacion_85_tarifa == $bonificacion_85_seleccionada)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_bonificacion_85_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de tipos de medida de tarifa eléctrica
    function dame_lista_tipos_medida_tarifa_electricidad_Espanya($tipo_medida_seleccionado, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $lista = "";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_MEDIDA_TARIFA_ELECTRICA_NINGUNA)
        {
            $lista .= "<option value='".TIPO_MEDIDA_TARIFA_ELECTRICA_NINGUNA."'>".$idiomas->_("Ninguna")."</option>";
        }
        $tipos_medida_tarifa = TarifaElectrica_Espanya::dame_tipos_medida_tarifa_electrica();
        foreach ($tipos_medida_tarifa as $tipo_medida_tarifa)
        {
            $nombre_tipo_medida_tarifa = TarifaElectrica_Espanya::dame_descripcion_tipo_medida_tarifa_electrica($tipo_medida_tarifa);
            $lista .= "<option value='".$tipo_medida_tarifa."'";
			if ($tipo_medida_tarifa == $tipo_medida_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_tipo_medida_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de ids de indicadores OMIE de 'pass-pool' de tarifa eléctrica
    function dame_lista_ids_indicadores_omie_pass_pool_tarifa_electricidad_Espanya($id_indicador_seleccionado)
    {
        $ids_indicadores_omie_pass_pool_tarifa = TarifaElectrica_Espanya::dame_ids_indicadores_omie_coste_pass_pool_tarifa_electrica();
        foreach ($ids_indicadores_omie_pass_pool_tarifa as $id_indicador_omie_pass_pool_tarifa)
        {
            $nombre_id_indicador_omie_pass_pool_tarifa = TarifaElectrica_Espanya::dame_descripcion_id_indicador_omie_pass_pool_tarifa_electrica($id_indicador_omie_pass_pool_tarifa);
            $lista .= "<option value='".$id_indicador_omie_pass_pool_tarifa."'";
			if ($id_indicador_omie_pass_pool_tarifa == $id_indicador_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_id_indicador_omie_pass_pool_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de tipos de cálculo de coste 'pass-pool' de tarifa eléctrica
    function dame_lista_tipos_calculo_coste_pass_pool_tarifa_electricidad_Espanya($tipo_seleccionado)
    {
        $tipos_calculo_coste_pass_pool_tarifa = TarifaElectrica_Espanya::dame_tipos_calculo_coste_pass_pool_tarifa_electrica();
        foreach ($tipos_calculo_coste_pass_pool_tarifa as $tipo_calculo_coste_pass_pool_tarifa)
        {
            $nombre_tipo_calculo_coste_pass_pool_tarifa = TarifaElectrica_Espanya::dame_descripcion_tipo_calculo_coste_pass_pool_tarifa_electrica($tipo_calculo_coste_pass_pool_tarifa);
            $lista .= "<option value='".$tipo_calculo_coste_pass_pool_tarifa."'";
			if ($tipo_calculo_coste_pass_pool_tarifa == $tipo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_tipo_calculo_coste_pass_pool_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de tipos de alquiler de contador
    function dame_lista_tipos_alquiler_contador_tarifa_electricidad_Espanya($tipo_seleccionado)
    {
        $tipos_alquiler_contador_tarifa = TarifaElectrica_Espanya::dame_tipos_alquiler_contador_tarifa_electrica();
        foreach ($tipos_alquiler_contador_tarifa as $tipo_alquiler_contador_tarifa)
        {
            $nombre_tipo_alquiler_contador_tarifa = TarifaElectrica_Espanya::dame_descripcion_tipo_alquiler_contador_tarifa_electrica($tipo_alquiler_contador_tarifa);
            $lista .= "<option value='".$tipo_alquiler_contador_tarifa."'";
			if ($tipo_alquiler_contador_tarifa == $tipo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_tipo_alquiler_contador_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve el contenido de las pestañas de la ventana de administración de tarifas eléctricas
    function dame_contenido_pestanyas_ventana_administracion_tarifas_electricidad_Espanya(
        $tipo_administracion,
        $id_tarifa,
        $nombre,
        $descripcion,
        $tipo,
        $contrato,
        $id_grupo,
        $expiracion,
        $cadena_fecha_expiracion_local_local,
        $numero_dias_preaviso_expiracion,
        $bonificacion_85,
        $tipo_medida,
        $potencia_nominal_transformador,
        $formula_precio_consumo_pass_through,
        $id_indicador_omie_pass_pool,
        $tipo_calculo_coste_pass_pool,
        $dia_calculo_coste_automatico_pass_pool,
		$fecha_inicio_contrato_cierre,
        $impuesto_electrico,
        $tipo_alquiler_contador,
        $alquiler_contador,
        $iva,
        $igic_reducido,
        $igic_normal,
        $prorrateo
        )
    {
        $idiomas = new Idiomas();

        // Unidad de medida de coste
        $unidad_medida_coste = $_SESSION["moneda"];

        // Clase de controles (obligatorios)
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

        // EMG : Si es una tarifa TD con potencia contratada inferior a 50kW le quitamos el sufijo "_MAX" para que se muestre correctamente la ventana
        if (endsWith($tipo, "_MAX"))
        {
            $tipo = substr($tipo, 0, -4);
        }
        $log = dame_log();
        // Se crea el contenido de las pestañas de tarifas eléctricas
		// Se crea todo el contenido de todas las pestañas, pero solo se muestran las necesarias según el tipo de contrato
        $contenido = "
            <div id='tabs-administracion-tarifa-electrica' class='tabbable' tipo-administracion='".$tipo_administracion."'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-contrato-fijo' id='titulo-tab-contrato-fijo'>".$idiomas->_("Precios de consumo")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-contrato-pass-pool' id='titulo-tab-contrato-pass-pool'>".$idiomas->_("Pass-pool")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-contrato-pass-through' id='titulo-tab-contrato-pass-through'>".$idiomas->_("Pass-through")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-contrato-cierre' id='titulo-tab-contrato-cierre'>".$idiomas->_("Cierre")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-precios-consumo-tarifa-acceso-tramos' id='titulo-tab-precios-consumo-tarifa-acceso-tramos'>".$idiomas->_("Precios de consumo de tarifa de acceso")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-precios-potencias-tramos' id='titulo-tab-precios-potencias-tramos'>".$idiomas->_("Precios de potencias contratadas")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-potencias-tramos' id='titulo-tab-potencias-tramos'>".$idiomas->_("Potencias contratadas")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-excesos-potencia-maximos-mensuales' id='titulo-tab-excesos-potencia-maximos-mensuales'>".$idiomas->_("Excesos de potencia")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-medida-datos-facturacion' id='titulo-tab-medida-datos-facturacion'>".$idiomas->_("Medida de datos de facturación")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-factura' id='titulo-tab-factura'>".$idiomas->_("Factura")."</a></li>
                </ul>
                <div id='tabs-content-administracion-tarifa-electrica' class='tab-content'>";

        // Contenido de pestaña principal (diferente para una y múltiples tarifas)
        $contenido .= "
		<div class='tab-pane active' id='tab-principal'>";

        // Nombre y descripción (única tarifa)
        if ($tipo_administracion == TIPO_ADMINISTRACION_TARIFAS_UNICA)
        {
            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
                        <input type='text' id='nombre_tarifa_electrica'
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
                        <textarea id='descripcion_tarifa_electrica'
                            class='TLNT_input_valid_characters input-administracion' rows='2'>".htmlspecialchars($descripcion, ENT_QUOTES)."</textarea>
                    </div>
                </div>";
        }

        // Tipo y contrato de tarifa eléctrica
        switch ($tipo_administracion)
        {
            case TIPO_ADMINISTRACION_TARIFAS_UNICA:
            {
                $opciones_extra_lista_tipos_tarifa_electrica = OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_NINGUNO;
                $opciones_extra_lista_contratos_tarifa_electrica = OPCIONES_EXTRA_LISTA_CONTRATOS_TARIFA_ELECTRICA_NINGUNO;
                break;
            }
            case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE:
            {
                $opciones_extra_lista_tipos_tarifa_electrica = OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_TODOS;
                $opciones_extra_lista_contratos_tarifa_electrica = OPCIONES_EXTRA_LISTA_CONTRATOS_TARIFA_ELECTRICA_TODOS;
                break;
            }
        }
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
                    <select id='tipo_tarifa_electrica' class='select-administracion'>";
        $contenido .= dame_lista_tipos_tarifa_electricidad_Espanya($tipo, $opciones_extra_lista_tipos_tarifa_electrica);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Contrato").": "."</span><br/>
                    <select id='contrato_tarifa_electrica' class='select-administracion'>";
        $contenido .= dame_lista_contratos_tarifa_electricidad_Espanya($contrato, $opciones_extra_lista_contratos_tarifa_electrica);
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
            $contenido .= dame_lista_grupos_tarifas(MEDICION_ELECTRICIDAD, $id_grupo, OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_NINGUNO);
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
                        <div id='select_tarifas_electricas_tarifa_electrica_no_visible' hidden></div>
                        <select id='ids_tarifas_electricas_tarifa_electrica'
                            name='ids_tarifas_electricas_tarifa_electrica'
                            max_selected='".ID_NINGUNO."' multiple='multiple'
                            class='select-administracion' hidden>";
            $contenido .= dame_lista_tarifas_tipo_contrato_electricidad_Espanya($tipo, $contrato);
            $contenido .= "
                        </select>
                    </div>
                </div>";
        }

        $contenido .= "
			</div>";

        // Contenido de pestaña de contrato fijo (precios de consumo)
        $contenido .= "
		<div class='tab-pane' id='tab-contrato-fijo'>"
			. dame_controles_precios_consumo_tramos_tarifa_tipo_electricidad_Espanya($id_tarifa, $tipo, $tipo_administracion) .
		"</div>";

        // Contenido de pestaña de contrato 'pass-pool' (parámetros de 'pass-pool')
        $contenido .= "
		<div class='tab-pane' id='tab-contrato-pass-pool'>
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("OMIE").": "."</span><br/>
					<select id='id_indicador_omie_pass_pool_tarifa_electrica' class='select-administracion'>"
						. dame_lista_ids_indicadores_omie_pass_pool_tarifa_electricidad_Espanya($id_indicador_omie_pass_pool) .
					"</select>
				</div>
			</div>

            <div id='controles-coeficientes-precio-consumo-pass-pool-tramos-tarifa-electrica'>"
				. dame_controles_coeficientes_precio_consumo_pass_pool_tramos_tarifa_tipo_electricidad_Espanya($id_tarifa, $tipo, $tipo_administracion) .
			"</div>
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de cálculo de coste").": "."</span><br/>
					<select id='tipo_calculo_coste_pass_pool_tarifa_electrica' class='select-administracion'>"
						. dame_lista_tipos_calculo_coste_pass_pool_tarifa_electricidad_Espanya($tipo_calculo_coste_pass_pool) .
					"</select>
				</div>
			</div>

			<div class='row-fluid' id='control_dia_calculo_coste_automatico_pass_pool_tarifa_electrica'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Día de cálculo de coste automático").": "."</span><br/>
					<input type='text' id='dia_calculo_coste_automatico_pass_pool_tarifa_electrica'
						class='".$clase_controles."TLNT_input_integer input-administracion' value='".$dia_calculo_coste_automatico_pass_pool."'>
				</div>
			</div>
        </div>";

        // Contenido de pestaña de contrato 'pass-through'
        $contenido .= "
		<div class='tab-pane' id='tab-contrato-pass-through'>";	

        // Fórmula de precio de consumo
        $numero_caracteres_actuales = dame_numero_caracteres($formula_precio_consumo_pass_through);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_FORMULA_PRECIO_CONSUMO;
        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'>
					<span class='titulo-campo-administracion'>".$idiomas->_("Fórmula de precio de consumo")." (€/".$idiomas->_("MWh").")".": "."</span>".
					"<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
						"(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
					<textarea id='formula_precio_consumo_pass_through_tarifa_electrica'
						class='".$clase_controles."input-administracion' rows='5'>".htmlspecialchars($formula_precio_consumo_pass_through, ENT_QUOTES)."</textarea>
					<span id='boton_smartmeter_ayuda_formula_precio_consumo_pass_through_tarifa_electrica' class='clickable'>
						<i class='icon-question-sign color-azul icono-ayuda'></i>
					</span>
				</div>
			</div>
		</div>";


        // Contenido de pestaña de contrato 'cierre'
        $contenido .= "
		<div class='tab-pane' id='tab-contrato-cierre'>
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("OMIE").": "."</span><br/>
					<select id='id_indicador_omie_cierre_tarifa_electrica' class='select-administracion'>"
						. dame_lista_ids_indicadores_omie_pass_pool_tarifa_electricidad_Espanya($id_indicador_omie_pass_pool) . 
					"</select>
				</div>
			</div>
			<div class='row-fluid' id='control_fecha_inicio_contrato_cierre'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Fecha inicio de contrato").": "."</span><br/>
					<input size='10' type='text' id='fecha_inicio_contrato_cierre' class='datepicker selector-fechas-administracion' value='".$fecha_inicio_contrato_cierre."'>
				</div>
			</div>";

        // Fórmula de precio de consumo
		// TODO: controlar numero de caracteres
        $numero_caracteres_actuales = dame_numero_caracteres($formula_precio_consumo_pass_through);
        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_FORMULA_PRECIO_CONSUMO;
        $contenido .= "
			<div class='row-fluid'>
				<div class='span12'>
					<span class='titulo-campo-administracion'>".$idiomas->_("Fórmula de precio de consumo")." (€/".$idiomas->_("MWh").")".": "."</span>".
					"<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
						"(".$numero_caracteres_actuales. " / ".$numero_maximo_caracteres.")"."</span><br/>
					<textarea id='formula_precio_consumo_cierre_tarifa_electrica'
						class='".$clase_controles."input-administracion' rows='5'>".htmlspecialchars($formula_precio_consumo_pass_through, ENT_QUOTES)."</textarea>
					<span id='boton_smartmeter_ayuda_formula_precio_consumo_cierre_tarifa_electrica' class='clickable'>
						<i class='icon-question-sign color-azul icono-ayuda'></i>
					</span>
				</div>
			</div>
		</div>";

        // Contenido de pestaña de precios de consumo de tarifa de acceso
        $contenido .= "
		<div class='tab-pane' id='tab-precios-consumo-tarifa-acceso-tramos'>"
			. dame_controles_precios_consumo_tarifa_acceso_tramos_tarifa_tipo_electricidad_Espanya($id_tarifa, $tipo, $tipo_administracion) . 
		"</div>";

        // Contenido de pestaña de precios de potencias
        $contenido .= "
		<div class='tab-pane' id='tab-precios-potencias-tramos'>"
			. dame_controles_precios_potencias_tramos_tarifa_tipo_electricidad_Espanya($id_tarifa, $tipo, $tipo_administracion) .
        "</div>";

        // Contenido de pestaña de potencias
        $contenido .= "
        <div class='tab-pane' id='tab-potencias-tramos'>"
			. dame_controles_potencias_tramos_tarifa_tipo_electricidad_Espanya($id_tarifa, $tipo, $tipo_administracion, $prorrateo) .
		"</div>";

        // Contenido de pestaña de parámetros de excesos de potencia de máximos mensuales (bonificación 85 %)
        $contenido .= "
		<div class='tab-pane' id='tab-excesos-potencia-maximos-mensuales'>"
			. dame_controles_parametros_excesos_potencia_maximos_mensuales_electricidad_Espanya($bonificacion_85, $tipo_administracion) .
		"</div>";

        // Contenido de pestaña de medida de datos de facturación (tipo de medida, potencia nominal del transformador)
        $contenido .= "
		<div class='tab-pane' id='tab-medida-datos-facturacion'>"
			. dame_controles_parametros_medida_datos_facturacion_electricidad_Espanya($tipo_medida, $potencia_nominal_transformador, $tipo_administracion) .
		"</div>";

        // Contenido de pestaña de factura
        $contenido .= "
		<div class='tab-pane' id='tab-factura'>
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Impuesto eléctrico")." (%)".": "."</span><br/>
					<input type='text' id='impuesto_electrico_tarifa_electrica'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$impuesto_electrico."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de alquiler de contador").": "."</span><br/>
					<select id='tipo_alquiler_contador_tarifa_electrica' class='select-administracion'>"
						. dame_lista_tipos_alquiler_contador_tarifa_electricidad_Espanya($tipo_alquiler_contador) .
					"</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Alquiler de contador")." (".$unidad_medida_coste.")".": "."</span><br/>
					<input type='text' id='alquiler_contador_tarifa_electrica'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$alquiler_contador."'>
				</div>
			</div>
			
            <div class='row-fluid' id='control_iva_tarifa_electrica'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("IVA")." (%)".": "."</span><br/>
					<input type='text' id='iva_tarifa_electrica'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$iva."'>
				</div>
			</div>

            <div class='row-fluid' id='control_igic_reducido_tarifa_electrica'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("IGIC")." (".$idiomas->_("reducido").") (%)".": "."</span><br/>
					<input type='text' id='igic_reducido_tarifa_electrica'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$igic_reducido."'>
				</div>
			</div>

            <div class='row-fluid' id='control_igic_normal_tarifa_electrica'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("IGIC")." (".$idiomas->_("normal").") (%)".": "."</span><br/>
					<input type='text' id='igic_normal_tarifa_electrica'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$igic_normal."'>
				</div>
			</div>
		</div>";

        $contenido .= "
                </div>
            </div>";
        return ($contenido);
    }


    // Devuelve los controles de los parámetros de excesos de potencia de máximos mensuales
	function dame_controles_parametros_excesos_potencia_maximos_mensuales_electricidad_Espanya($bonificacion_85, $tipo_administracion)
	{
        $idiomas = new Idiomas();

        // Opciones extra
        switch ($tipo_administracion)
        {
            case TIPO_ADMINISTRACION_TARIFAS_UNICA:
            {
                $opciones_extra_lista_bonificacion_85_tarifa_electrica = OPCIONES_EXTRA_LISTA_BONIFICACION_85_TARIFA_ELECTRICA_SIN_OPCIONES_EXTRA;
                break;
            }
            case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE:
            {
                $opciones_extra_lista_bonificacion_85_tarifa_electrica = OPCIONES_EXTRA_LISTA_BONIFICACION_85_TARIFA_ELECTRICA_NINGUNA;
                break;
            }
            default:
            {
                throw new Exception("Tipo de administración desconocido: '".$tipo_administracion."'");
            }
        }

        // Bonificación 85 %
        $controles .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Bonificación 85 %").": "."</span><br/>
                    <select id='bonificacion_85_tarifa_electrica' class='select-administracion'>";
        $controles .= dame_lista_bonificaciones_85_tarifa_electricidad_Espanya($bonificacion_85, $opciones_extra_lista_bonificacion_85_tarifa_electrica);
        $controles .= "
                    </select>
                </div>
            </div>";

        return ($controles);
    }


    // Devuelve los controles de los parámetros de medida de datos de facturación
	function dame_controles_parametros_medida_datos_facturacion_electricidad_Espanya($tipo_medida, $potencia_nominal_transformador, $tipo_administracion)
	{
        $idiomas = new Idiomas();

        // Opciones extra y clase de controles (obligatorios)
        switch ($tipo_administracion)
        {
            case TIPO_ADMINISTRACION_TARIFAS_UNICA:
            {
                $opciones_extra_lista_tipos_medida_tarifa_electrica = OPCIONES_EXTRA_LISTA_TIPOS_MEDIDA_TARIFA_ELECTRICA_SIN_OPCIONES_EXTRA;
                $clase_controles = "TLNT_input_mandatory"." ";
                break;
            }
            case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE:
            {
                $opciones_extra_lista_tipos_medida_tarifa_electrica = OPCIONES_EXTRA_LISTA_TIPOS_MEDIDA_TARIFA_ELECTRICA_NINGUNA;
                $clase_controles = "";
                break;
            }
            default:
            {
                throw new Exception("Tipo de administración desconocido: '".$tipo_administracion."'");
            }
        }

        $controles .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo de medida").": "."</span><br/>
                    <select id='tipo_medida_tarifa_electrica' class='select-administracion'>";
        $controles .= dame_lista_tipos_medida_tarifa_electricidad_Espanya($tipo_medida, $opciones_extra_lista_tipos_medida_tarifa_electrica);
        $controles .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid' id='control_potencia_nominal_transformador_tarifa_electrica'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Potencia nominal del transformador")." (".$idiomas->_("kVA").")".": "."</span><br/>
					<input type='text' id='potencia_nominal_transformador_tarifa_electrica'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$potencia_nominal_transformador."'>
				</div>
			</div>";

        return ($controles);
    }


    // Devuelve los controles de los precios de consumo de tramos
	function dame_controles_precios_consumo_tramos_tarifa_tipo_electricidad_Espanya($id_tarifa, $tipo, $tipo_administracion)
	{
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Control obligatorio y valor por defecto
        switch ($tipo_administracion)
        {
            case TIPO_ADMINISTRACION_TARIFAS_UNICA:
            {
                $clase_controles = "TLNT_input_mandatory"." ";
                $valor_defecto_controles = 0;
                break;
            }
            case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE:
            {
                $clase_controles = "";
                $valor_defecto_controles = "";
                break;
            }
            default:
            {
                throw new Exception("Tipo de administración desconocido: '".$tipo_administracion."'");
            }
        }

        // Se recuperan los tramos existentes en la base de datos de la tarifa eléctrica
        if ($id_tarifa != ID_NINGUNO)
        {
            $consulta_tramos_tarifa_electrica = "
				SELECT *
				FROM ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_ESPANYA."
				WHERE
					tarifa_electrica = '".$bd_red->_($id_tarifa)."'
                ORDER BY tramo";
			$res_tramos_tarifa_electrica = $bd_red->ejecuta_consulta($consulta_tramos_tarifa_electrica);
            if ($res_tramos_tarifa_electrica == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tramos_tarifa_electrica."'");
            }

            $tramos = array();
            while ($fila_tramos_tarifa_electrica = $res_tramos_tarifa_electrica->dame_siguiente_fila())
            {
                array_push($tramos, $fila_tramos_tarifa_electrica);
            }
            $numero_tramos = $res_tramos_tarifa_electrica->dame_numero_filas();
        }
        else
        {
            // Se recuperan los tramos de la tarifa eléctrica
            $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($tipo);
            $numero_tramos = $caracteristicas_tipo_tarifa_electrica["numero_tramos"];
            $tramos = array();
            if ($numero_tramos > 0)
            {
                foreach (range(1, $numero_tramos) as $numero_tramo)
                {
                    $tramo = array();
                    $tramo["tarifa_electrica"] = $id_tarifa;
                    $tramo["tramo"] = $numero_tramo;
                    $tramo["precio_consumo"] = $valor_defecto_controles;
                    array_push($tramos, $tramo);
                }
            }
        }

        // Se añaden los controles de cada uno de los tramos
        foreach ($tramos as $tramo)
        {
            $titulo_campo_tramo = $idiomas->_("Precio de consumo en tramo")." ".$tramo["tramo"]." (".$idiomas->_("€")."/".$idiomas->_("kWh").")";
            $nombre_id_campo_tramo = "precio_consumo_tramo_tarifa_electrica__".$tramo["tramo"];
            $valor_campo_tramo = $tramo["precio_consumo"];

            $controles .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_tramo.": "."</span><br/>
                        <input type='text' id='".$nombre_id_campo_tramo."'
                            class='".$clase_controles."TLNT_input_float input-administracion' value='".$valor_campo_tramo."'>
                    </div>
                </div>";
        }

        return ($controles);
    }


    // Devuelve los controles de los coeficientes de precios de 'pass-pool' de tramos
	function dame_controles_coeficientes_precio_consumo_pass_pool_tramos_tarifa_tipo_electricidad_Espanya($id_tarifa, $tipo, $tipo_administracion)
	{
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Control obligatorio y valor por defecto
        switch ($tipo_administracion)
        {
            case TIPO_ADMINISTRACION_TARIFAS_UNICA:
            {
                $clase_controles = "TLNT_input_mandatory"." ";
                $valor_defecto_controles = 0;
                break;
            }
            case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE:
            {
                $clase_controles = "";
                $valor_defecto_controles = "";
                break;
            }
            default:
            {
                throw new Exception("Tipo de administración desconocido: '".$tipo_administracion."'");
            }
        }

        // Se recuperan los tramos existentes en la base de datos de la tarifa eléctrica
        if ($id_tarifa != ID_NINGUNO)
        {
            $consulta_tramos_tarifa_electrica = "
				SELECT *
				FROM ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_ESPANYA."
				WHERE
					tarifa_electrica = '".$bd_red->_($id_tarifa)."'
                ORDER BY tramo";
			$res_tramos_tarifa_electrica = $bd_red->ejecuta_consulta($consulta_tramos_tarifa_electrica);
            if ($res_tramos_tarifa_electrica == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tramos_tarifa_electrica."'");
            }

            $tramos = array();
            while ($fila_tramos_tarifa_electrica = $res_tramos_tarifa_electrica->dame_siguiente_fila())
            {
                array_push($tramos, $fila_tramos_tarifa_electrica);
            }
            $numero_tramos = $res_tramos_tarifa_electrica->dame_numero_filas();
        }
        else
        {
            // Se recuperan los tramos de la tarifa eléctrica
            $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($tipo);
            $numero_tramos = $caracteristicas_tipo_tarifa_electrica["numero_tramos"];
            $tramos = array();
            if ($numero_tramos > 0)
            {
                foreach (range(1, $numero_tramos) as $numero_tramo)
                {
                    $tramo = array();
                    $tramo["tarifa_electrica"] = $id_tarifa;
                    $tramo["tramo"] = $numero_tramo;
                    $tramo["coeficiente_a_precio_consumo_pass_pool"] = $valor_defecto_controles;
                    $tramo["coeficiente_b_precio_consumo_pass_pool"] = $valor_defecto_controles;
                    array_push($tramos, $tramo);
                }
            }
        }

        // Se añaden los controles de cada uno de los tramos
        foreach ($tramos as $tramo)
        {
            $titulo_campo_tramo = $idiomas->_("Coeficientes de precio de consumo A y B (€/MWh) en tramo")." ".$tramo["tramo"];
            $nombre_id_campo_tramo_1 = "coeficiente_a_precio_consumo_pass_pool_tarifa_electrica__".$tramo["tramo"];
            $nombre_id_campo_tramo_2 = "coeficiente_b_precio_consumo_pass_pool_tarifa_electrica__".$tramo["tramo"];
            $valor_campo_tramo_1 = $tramo["coeficiente_a_precio_consumo_pass_pool"];
            $valor_campo_tramo_2 = $tramo["coeficiente_b_precio_consumo_pass_pool"];

            $controles .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_tramo.": "."</span><br/>
                        <input type='text' id='".$nombre_id_campo_tramo_1."'
                            class='".$clase_controles."TLNT_input_float input-administracion-50-izda' value='".$valor_campo_tramo_1."'>
                        <input type='text' id='".$nombre_id_campo_tramo_2."'
                            class='".$clase_controles."TLNT_input_float input-administracion-50-dcha' value='".$valor_campo_tramo_2."'>
                    </div>
                </div>";
        }

        return ($controles);
    }


    // Devuelve los controles de los precios de consumo de tarifa de acceso de tramos
	function dame_controles_precios_consumo_tarifa_acceso_tramos_tarifa_tipo_electricidad_Espanya($id_tarifa, $tipo, $tipo_administracion)
	{
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Control obligatorio y valor por defecto
        switch ($tipo_administracion)
        {
            case TIPO_ADMINISTRACION_TARIFAS_UNICA:
            {
                $clase_controles = "TLNT_input_mandatory"." ";
				switch ($tipo)
				{                 
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2022:
                    {
                        $valor_defecto_peaje = [0.027787, 0.019146, 0.000703];
                        //$valor_defecto_cargos = [0.072969,0.014594,0.003648];
                        $valor_defecto_cargos = [0.046622,0.009324,0.002331];
                        break;
                    }
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022_MAX:
                    {
                        $valor_defecto_peaje = [0.017752, 0.014567, 0.007955, 0.005361, 0.000321, 0.000321];
                        //$valor_defecto_cargos = [0.040678,0.030119,0.016271,0.008136,0.005215,0.003254];
                        $valor_defecto_cargos = [0.025990,0.019244,0.010396,0.005198,0.003332,0.002079];
                        break;
                    }
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022_MAX:
                    {
                        $valor_defecto_peaje = [0.017364, 0.014247, 0.008124, 0.005428, 0.000315, 0.000315];
                        //$valor_defecto_cargos = [0.022119,0.016384,0.008848,0.004424,0.002836,0.00177];
                        $valor_defecto_cargos = [0.014132,0.010468,0.005653,0.002826,0.001812,0.001131];
                        break;
                    }
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2022:
                    {
                        $valor_defecto_peaje = [0.009168, 0.007529, 0.004228, 0.002954, 0.000174, 0.000174];
                        //$valor_defecto_cargos = [0.010378,0.007687,0.004151,0.002076,0.001331,0.00083];
                        $valor_defecto_cargos = [0.006631,0.004911,0.002652,0.001326,0.000850,0.000530];
                        break;
                    }
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2022:
                    {
                        $valor_defecto_peaje = [0.007774, 0.006515, 0.003917, 0.00188, 0.000235, 0.000235];
                        //$valor_defecto_cargos = [0.008507,0.006302,0.003403,0.001701,0.001091,0.000681];
                        $valor_defecto_cargos = [0.005435,0.004026,0.002174,0.001087,0.000697,0.000435];
                        break;
                    }
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2022:
                    {
                        $valor_defecto_peaje = [0.007046, 0.005743, 0.003063, 0.002433, 0.000156, 0.000156];
                        //$valor_defecto_cargos = [0.003232,0.002394,0.001293,0.000646,0.000414,0.000259];
                        $valor_defecto_cargos = [0.002065,0.001530,0.000826,0.000413,0.000265,0.000165];
                        break;
                    }
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2023:
					{
						$valor_defecto_peaje = [0.029098, 0.019794, 0.000980];                        
						$valor_defecto_cargos = [0.043893, 0.008779, 0.002195];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023_MAX:
					{
						$valor_defecto_peaje = [0.019466, 0.015685, 0.006382, 0.004645, 0.000412, 0.000412];
						$valor_defecto_cargos = [0.024469, 0.018118, 0.009788, 0.004894, 0.003137, 0.001958];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023_MAX:
					{
						$valor_defecto_peaje = [0.018036, 0.014354, 0.005965, 0.004393, 0.000362, 0.000362];
						$valor_defecto_cargos = [0.013305, 0.009856, 0.005322, 0.002661, 0.001706, 0.001064];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2023:
					{
						$valor_defecto_peaje = [0.010719, 0.008707, 0.003427, 0.002349, 0.000172, 0.000172];						
                        $valor_defecto_cargos = [0.006243, 0.004624, 0.002497, 0.001249, 0.000800, 0.000499];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2023:
					{
						$valor_defecto_peaje = [0.008957, 0.007052, 0.002994, 0.002055, 0.000197, 0.000197];
                        $valor_defecto_cargos = [0.005117, 0.003791, 0.002047, 0.001023, 0.000656, 0.000409];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2023:
					{
						$valor_defecto_peaje = [0.008625, 0.006738, 0.002988, 0.001948, 0.000153, 0.000153];
                        $valor_defecto_cargos = [0.001944, 0.001440, 0.000778, 0.000389, 0.000249, 0.000156];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2024:
					{
						$valor_defecto_peaje = [0.033081, 0.019184, 0.000557];                        
						$valor_defecto_cargos = [0.043893, 0.008779, 0.002195];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024_MAX:
					{
						$valor_defecto_peaje = [0.023974, 0.012820, 0.007573, 0.005495, 0.000424, 0.000234];
						$valor_defecto_cargos = [0.024469, 0.018118, 0.009788, 0.004894, 0.003137, 0.001958];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024_MAX:
					{
						$valor_defecto_peaje = [0.021899, 0.011675, 0.007394, 0.005376, 0.000406, 0.000212];
						$valor_defecto_cargos = [0.013305, 0.009856, 0.005322, 0.002661, 0.001706, 0.001064];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2024:
					{
						$valor_defecto_peaje = [0.011872, 0.006530, 0.003686, 0.002774, 0.000249, 0.000090];						
                        $valor_defecto_cargos = [0.006243, 0.004624, 0.002497, 0.001249, 0.000800, 0.000499];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2024:
					{
						$valor_defecto_peaje = [0.010399, 0.005651, 0.003603, 0.002659, 0.000238, 0.000140];
                        $valor_defecto_cargos = [0.005117, 0.003791, 0.002047, 0.001023, 0.000656, 0.000409];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2024:
					{
						$valor_defecto_peaje = [0.008757, 0.004806, 0.003067, 0.002206, 0.000139, 0.000089];
                        $valor_defecto_cargos = [0.001944, 0.001440, 0.000778, 0.000389, 0.000249, 0.000156];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025_ABRIL:
					{
						$valor_defecto_peaje = [0.034234, 0.016540, 0.000079];                        
						$valor_defecto_cargos = [0.058305, 0.011661, 0.002915];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL_MAX:
					{
						$valor_defecto_peaje = [0.028528, 0.012343, 0.004673, 0.002682, 0.000119, 0.000031];
						$valor_defecto_cargos = [0.032503, 0.024066, 0.013001, 0.006501, 0.004167, 0.002600];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL_MAX:
					{
						$valor_defecto_peaje = [0.027104, 0.011894, 0.004726, 0.002739, 0.000122, 0.000029];
						$valor_defecto_cargos = [0.017674, 0.013092, 0.007069, 0.003535, 0.002266, 0.001414];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025_ABRIL:
					{
						$valor_defecto_peaje = [0.014770, 0.006840, 0.002279, 0.001219, 0.000063, 0.000020];						
                        $valor_defecto_cargos = [0.008293, 0.006142, 0.003317, 0.001659, 0.001063, 0.000663];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025_ABRIL:
					{
						$valor_defecto_peaje = [0.012294, 0.005470, 0.001931, 0.001063, 0.000055, 0.000015];
                        $valor_defecto_cargos = [0.006798, 0.005035, 0.002719, 0.001360, 0.000871, 0.000544];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025_ABRIL:
					{
						$valor_defecto_peaje = [0.007944, 0.003569, 0.001288, 0.000681, 0.000036, 0.000004];
                        $valor_defecto_cargos = [0.002582, 0.001913, 0.001033, 0.000516, 0.000331, 0.000207];
						break;
					}

                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2026:
					{
						$valor_defecto_peaje = [0.033261, 0.016409, 0.000077];                        
						$valor_defecto_cargos = [0.064292, 0.012858, 0.003215];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026_MAX:
					{
						$valor_defecto_peaje = [0.027511, 0.012376, 0.004943, 0.002627, 0.000111, 0.000031];
						$valor_defecto_cargos = [0.035841, 0.026538, 0.014336, 0.007168, 0.004595, 0.002867];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026_MAX:
					{
						$valor_defecto_peaje = [0.026785, 0.012281, 0.005133, 0.00278, 0.00012, 0.000029];
						$valor_defecto_cargos = [0.019489, 0.014436, 0.007795, 0.003898, 0.002499, 0.001559];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2026:
					{
						$valor_defecto_peaje = [0.014736, 0.007202, 0.002542, 0.001254, 0.000062, 0.00002];						
                        $valor_defecto_cargos = [0.009144, 0.006773, 0.003658, 0.001829, 0.001172, 0.000732];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2026:
					{
						$valor_defecto_peaje = [0.011279, 0.005324, 0.001994, 0.000995, 0.000048, 0.000014];
                        $valor_defecto_cargos = [0.007496, 0.005552, 0.002998, 0.001499, 0.000961, 0.0006];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2026:
					{
						$valor_defecto_peaje = [0.008427, 0.003946, 0.001458, 0.000716, 0.000038, 0.000004];
                        $valor_defecto_cargos = [0.002848, 0.002109, 0.001139, 0.00057, 0.000365, 0.000228];
						break;
					}

					default:
					{
						$valor_defecto_peaje = [0, 0, 0, 0,0,0];
						$valor_defecto_cargos = [0, 0, 0, 0,0,0];
					}

				}
                break;
            }
            case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE:
            {
                $clase_controles = "";
				$valor_defecto_peaje = [0, 0, 0, 0,0,0];
				$valor_defecto_cargos = [0, 0, 0, 0,0,0];

                break;
            }
            default:
            {
                throw new Exception("Tipo de administración desconocido: '".$tipo_administracion."'");
            }
        }

        // Se recuperan los tramos existentes en la base de datos de la tarifa eléctrica
        if ($id_tarifa != ID_NINGUNO)
        {
            $consulta_tramos_tarifa_electrica = "
				SELECT *
				FROM ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_ESPANYA."
				WHERE
					tarifa_electrica = '".$bd_red->_($id_tarifa)."'
                ORDER BY tramo";
			$res_tramos_tarifa_electrica = $bd_red->ejecuta_consulta($consulta_tramos_tarifa_electrica);
            if ($res_tramos_tarifa_electrica == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tramos_tarifa_electrica."'");
            }

            $tramos = array();
            while ($fila_tramos_tarifa_electrica = $res_tramos_tarifa_electrica->dame_siguiente_fila())
            {
                array_push($tramos, $fila_tramos_tarifa_electrica);
            }
            $numero_tramos = $res_tramos_tarifa_electrica->dame_numero_filas();
        }
        else
        {
            // Se recuperan los tramos de la tarifa eléctrica
            $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($tipo);
            $numero_tramos = $caracteristicas_tipo_tarifa_electrica["numero_tramos"];
            $tramos = array();
            if ($numero_tramos > 0)
            {
                foreach (range(1, $numero_tramos) as $numero_tramo)
                {
                    $tramo = array();
                    $tramo["tarifa_electrica"] = $id_tarifa;
                    $tramo["tramo"] = $numero_tramo;
                    // Aquí es donde se calcula el precio de consumo
                    $tramo["precio_consumo_tarifa_acceso"] = $valor_defecto_peaje[$numero_tramo -1] + $valor_defecto_cargos[$numero_tramo -1];
                    array_push($tramos, $tramo);
                }
            }
        }

        // Se añaden los controles de cada uno de los tramos
        foreach ($tramos as $tramo)
        {
            $titulo_campo_tramo = $idiomas->_("Precio de consumo de tarifa de acceso en tramo")." ".$tramo["tramo"]." (".$idiomas->_("€")."/".$idiomas->_("kWh").")";
            $nombre_id_campo_tramo = "precio_consumo_tarifa_acceso_tramo_tarifa_electrica__".$tramo["tramo"];
            $valor_campo_tramo = $tramo["precio_consumo_tarifa_acceso"];

            $controles .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_tramo.": "."</span><br/>
                        <input type='text' id='".$nombre_id_campo_tramo."'
                            class='".$clase_controles."TLNT_input_float input-administracion' value='".$valor_campo_tramo."'>
                    </div>
                </div>";
        }

        // Bucle para ver si los valores son o no por defecto. SMR changed 27/04/2022

        $defecto = true;              

        foreach ($tramos as $tramo) {
            $titulo_campo_tramo = $idiomas->_("Precio de consumo de tarifa de acceso en tramo")." ".$tramo["tramo"]." (".$idiomas->_("€")."/".$idiomas->_("kWh").")";
            $nombre_id_campo_tramo = "precio_consumo_tarifa_acceso_tramo_tarifa_electrica__".$tramo["tramo"];
            $valor_campo_tramo = $tramo["precio_consumo_tarifa_acceso"];
            $numero_tramo_var = $tramo["tramo"]-1;    
            $valores_por_defecto_tramos_suma = $valor_defecto_cargos[$numero_tramo_var] + $valor_defecto_peaje[$numero_tramo_var];          
            if (strval($valores_por_defecto_tramos_suma) !== strval($valor_campo_tramo)) {
                $defecto = false;
            }
        }

    if ($defecto == true) {
        if ($valor_defecto_peaje[0] != 0) {
            if (strpos(strval($tipo), '2022') !== false) {
                $controles .= "
                        <div class='row-fluid'>
                            <div class='span12'>                    
                            Nota: Los valores mostrados por defecto corresponden a la suma de peajes publicados en BOE-A-2021-21208 y los cargos publicados en BOE-2022-4972.  Tienen efecto desde el 1/4/2022 hasta 31/12/2022
                            </div>
                        </div>";

            }
            if (strpos(strval($tipo), '2023') !== false) {
                $controles .= "
                        <div class='row-fluid'>
                            <div class='span12'>                    
                            Nota: Los valores mostrados por defecto corresponden a la suma de peajes publicados en BOE-A-2022-21799 y los cargos publicados en BOE-A-2022-23737. Tienen efecto desde el 01/01/2023 hasta 31/12/2023
                            </div>
                        </div>";
            }

            if (strpos(strval($tipo), '2024') !== false) {
                $controles .= "
                        <div class='row-fluid'>
                            <div class='span12'>                    
                            Nota: Los valores mostrados por defecto corresponden a la suma de peajes publicados en BOE-A-2023-26251 y los cargos publicados en BOE-A-2022-23737. Tienen efecto desde el 01/01/2024 hasta 31/12/2024
                            </div>
                        </div>";
            }

            if (strpos(strval($tipo), '2025') !== false) {
                $controles .= "
                        <div class='row-fluid'>
                            <div class='span12'>                    
                            Nota: Los valores mostrados por defecto corresponden a la suma de peajes publicados en BOE-A-2024-26218 y los cargos publicados en BOE-A-2024-27289. Tienen efecto desde el 01/01/2025 hasta 31/12/2025.
                            </div>
                        </div>";
            }

            if (strpos(strval($tipo), '2026') !== false) {
                $controles .= "
                        <div class='row-fluid'>
                            <div class='span12'>                    
                            Nota: Los valores mostrados por defecto corresponden a la suma de peajes publicados en BOE-A-2026-26348 y los cargos publicados en BOE-A-2025-26705. Tienen efecto desde el 01/01/2026 hasta 31/12/2026.
                            </div>
                        </div>";
            }
        }

    }

        if ($defecto == false){
            if ($valor_defecto_peaje[0] != 0){
                $controles .= "
                    <div class='row-fluid'>
                        <div class='span12'>                    
                            Valores diferentes a los establecidos por defecto.
                        </div>
                    </div>";
            }             
        }

        return ($controles);
    }


    // Devuelve los controles de los precios de potencias de tramos
	function dame_controles_precios_potencias_tramos_tarifa_tipo_electricidad_Espanya($id_tarifa, $tipo, $tipo_administracion)
	{
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Control obligatorio y valor por defecto
        switch ($tipo_administracion)
        {
            case TIPO_ADMINISTRACION_TARIFAS_UNICA:
            {
                $clase_controles = "TLNT_input_mandatory"." ";
				switch ($tipo)
				{
					case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2022:
					{
						$valor_defecto_peaje = [22.988256, 0, 0.93889];
                        //$valor_defecto_cargos = [4.970533,0, 0.319666];
						$valor_defecto_cargos = [3.175787,0, 0.204242];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022_MAX:
                    {
                        $valor_defecto_peaje = [10.49392, 9.152492, 3.688512, 2.802739, 1.122833, 1.122833];
                        //$valor_defecto_cargos = [6.176299, 3.090846, 2.245571, 2.245571, 2.245571, 1.029383];
                        $valor_defecto_cargos = [3.946179, 1.974813, 1.434747, 1.434747, 1.434747, 0.657696];
                        break;
                    }
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022_MAX:
					{
						$valor_defecto_peaje = [18.320805, 18.320805, 9.988571, 7.565889, 0.50255, 0.50255];
						//$valor_defecto_cargos = [6.411267, 3.20854, 2.33137, 2.33137, 2.33137, 1.068544];
                        $valor_defecto_cargos = [4.096305, 2.050010 , 1.489566, 1.489566, 1.489566, 0.682718];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2022:
					{
						$valor_defecto_peaje = [13.592890, 13.592890, 6.648956, 6.048771, 0.418446, 0.418446];
						//$valor_defecto_cargos = [3.764914,1.884462,1.36906,1.36906,1.36906,0.627486];
                        $valor_defecto_cargos = [2.405490,1.204026,0.874724,0.874724,0.874724,0.400915];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2022:
					{
						$valor_defecto_peaje = [10.021051, 10.021051, 5.543157, 3.240960, 0.638147, 0.638147];
						//$valor_defecto_cargos = [3.014497,1.508533,1.096011,1.096011,1.096011,0.502416];
                        $valor_defecto_cargos = [1.926031,0.963837,0.700266,0.700266,0.700266,0.321005];
						break;
					}
					case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2022:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2022:
					{
						$valor_defecto_peaje = [10.314368, 7.894062, 3.797235, 2.795290, 0.528120, 0.528120];
						//$valor_defecto_cargos = [1.474591,0.737911,0.536215,0.536215,0.536215,0.245765];
                        $valor_defecto_cargos = [0.942150,0.471468,0.342600,0.342600,0.342600,0.157025];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2023:
					{
                        // Peajes transporte y distribución, término potencia desde 1/1/23 (€/kW/año) (BOE-A-2023-21799)
						$valor_defecto_peaje = [22.393140, 0, 1.150425];
                        $valor_defecto_cargos = [2.989915, 0, 0.192288];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023_MAX:
					{
                        // Peajes transporte y distribución, término potencia desde 1/1/23 (€/kW/año) (BOE-A-2023-21799)						
                        // Valores del 01/01/2023 al 31/01/2023
                        $valor_defecto_peaje = [10.267292, 10.039843, 2.651271, 2.303199, 1.381933, 1.381933];						
                        $valor_defecto_cargos = [3.715217, 1.859231, 1.350774, 1.350774, 1.350774, 0.619203];
                        break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023_MAX:
					{
                        //Actualizada 23/12/2023
						$valor_defecto_peaje = [19.108658, 17.911151, 8.925198, 7.158278, 0.506199, 0.506199];						
                        $valor_defecto_cargos = [3.856557, 1.930027, 1.402384, 1.402384, 1.402384, 0.642759];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2023:
					{
                        //Actualizada 23/12/2023 
						$valor_defecto_peaje = [13.561685, 13.526788, 5.420822, 4.094881, 0.374203, 0.374203];						
                        $valor_defecto_cargos = [2.264702, 1.133557, 0.823528, 0.823528, 0.823528, 0.377450];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2023:
					{
                        //Actualizada 23/12/2023
						$valor_defecto_peaje = [9.880203, 9.471228, 4.796920, 3.592008, 0.487055, 0.487055];						
                        $valor_defecto_cargos = [1.813304, 0.907425, 0.659281, 0.659281, 0.659281, 0.302217];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2023:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2023:
					{
                        //Actualizada 23/12/2023
						$valor_defecto_peaje = [8.443077, 7.279110, 3.590719, 2.751326, 0.349732, 0.349732];						
                        $valor_defecto_cargos = [0.887008, 0.443874, 0.322548, 0.322548, 0.322548, 0.147835];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2024:
                    {
                        // Peajes transporte y distribución, término potencia desde 1/1/24 (€/kW/año) (BOE-A-2023-26251)
                        $valor_defecto_peaje = [22.401746, 0, 0.776564];
                        $valor_defecto_cargos = [2.989915, 0, 0.192288];
                        break;
                    }
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024_MAX:
					{
                        // Peajes transporte y distribución, término potencia desde 1/1/24 (€/kW/año) (BOE-A-2023-26251)						
                        // Valores del 01/01/2024 al 31/01/2024
                        $valor_defecto_peaje = [11.997830, 7.687805, 3.307437, 2.791786, 0.934435, 0.934435];				
                        $valor_defecto_cargos = [3.715217, 1.859231, 1.350774, 1.350774, 1.350774, 0.619203];
                        break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024_MAX:
					{
                        //Actualizada 21/12/2023 (BOE-A-2023-26251)
						$valor_defecto_peaje = [20.557850, 12.762884, 9.926251, 7.848380, 0.325141, 0.325141];						
                        $valor_defecto_cargos = [3.856557, 1.930027, 1.402384, 1.402384, 1.402384, 0.642759];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2024:
					{
                        //Actualizada 21/12/2023 (BOE-A-2023-26251)
						$valor_defecto_peaje = [13.138413, 8.751207, 5.615670, 4.671118, 0.238475, 0.238475];						
                        $valor_defecto_cargos = [2.264702, 1.133557, 0.823528, 0.823528, 0.823528, 0.377450];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2024:
					{
                        //Actualizada 21/12/2023 (BOE-A-2023-26251)
						$valor_defecto_peaje = [10.474293, 6.510420, 5.241724, 4.138835, 0.341465, 0.341465];						
                        $valor_defecto_cargos = [1.813304, 0.907425, 0.659281, 0.659281, 0.659281, 0.302217];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2024:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2024:
					{
                        //Actualizada 21/12/2023 (BOE-A-2023-26251)
						$valor_defecto_peaje = [7.310560, 4.116430, 3.161822, 2.873850, 0.194493, 0.194493];						
                        $valor_defecto_cargos = [0.887008, 0.443874, 0.322548, 0.322548, 0.322548, 0.147835];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025_ABRIL:
                    {
                        // Peajes transporte y distribución, término potencia desde 1/1/24 (€/kW/año) (BOE-A-2024-26218)
                        // Se pone como segundo valor un 0 porque tiene 2 valores correspondientes a potencia punta y solo 1 potencia valle
                        $valor_defecto_peaje = [22.958932, 0, 0.442165];
                        $valor_defecto_cargos = [3.971618, 0, 0.255423];
                        break;
                    }
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL_MAX:
					{
                        // Peajes transporte y distribución, término potencia desde 1/1/24 (€/kW/año) (BOE-A-2024-26218)						
                        // Valores del 01/01/2025 al 31/01/2025
                        $valor_defecto_peaje = [14.723431, 7.781964, 2.468252, 1.887267, 0.533883, 0.533883];				
                        $valor_defecto_cargos = [4.935064, 2.469687, 1.794284, 1.794284, 1.794284, 0.822511];
                        break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL_MAX:
					{
                        //Actualizada 21/12/2023 (BOE-A-2024-26218)
						$valor_defecto_peaje = [23.669055, 12.513915, 4.696330, 3.309245, 0.069965, 0.062286];						
                        $valor_defecto_cargos = [5.122811, 2.563728, 1.862840, 1.862840, 1.862840, 0.853802];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025_ABRIL:
					{
                        //Actualizada 21/12/2023 (BOE-A-2024-26218)
						$valor_defecto_peaje = [16.620368, 9.426053, 2.481516, 1.512028, 0.059278, 0.052654];						
                        $valor_defecto_cargos = [3.008289, 1.505746, 1.093923, 1.093923, 1.093923, 0.501382];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025_ABRIL:
					{
                        //Actualizada 21/12/2023 (BOE-A-2024-26218)
						$valor_defecto_peaje = [10.791377, 6.502236, 2.118318, 1.380541, 0.045332, 0.039905];						
                        $valor_defecto_cargos = [2.408681, 1.205367, 0.875748, 0.875748, 0.875748, 0.401447];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025_ABRIL:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025_ABRIL:
					{
                        //Actualizada 21/12/2023 (BOE-A-2024-26218)
						$valor_defecto_peaje = [6.590215, 3.939980, 0.956817, 0.665081, 0.019779, 0.013181];						
                        $valor_defecto_cargos = [1.178247, 0.589615, 0.428453, 0.428453, 0.428453, 0.196374];
						break;
					}
                    
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2026:
                    {
                        // Peajes transporte y distribución, término potencia
                        $valor_defecto_peaje = [23.324952, 0, 0.44377];
                        $valor_defecto_cargos = [4.379461, 0, 0.281653];
                        break;
                    }
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026_MAX:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026_MAX:
					{
                        $valor_defecto_peaje = [14.935084, 7.894323, 2.502996, 1.907795, 0.535313, 0.535313];				
                        $valor_defecto_cargos = [5.441843, 2.723298, 1.978538, 1.978538, 1.978538, 0.906974];
                        break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026_MAX:
					case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026_MAX:
					{
						$valor_defecto_peaje = [23.946498, 12.687713, 4.747747, 3.339695, 0.070979, 0.062703];						
                        $valor_defecto_cargos = [5.64887, 2.826996, 2.054134, 2.054134, 2.054134, 0.941478];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2026:
					{
						$valor_defecto_peaje = [16.786379, 9.455297, 2.502855, 1.521894, 0.059359, 0.052513];						
                        $valor_defecto_cargos = [3.317209, 1.660371, 1.206258, 1.206258, 1.206258, 0.552868];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2026:
					{
						$valor_defecto_peaje = [10.397365, 6.258717, 2.096386, 1.366437, 0.044362, 0.038723];						
                        $valor_defecto_cargos = [2.656027, 1.329146, 0.965679, 0.965679, 0.965679, 0.442671];
						break;
					}
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2026:
                    case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2026:
					{
						$valor_defecto_peaje = [6.606205, 3.935625, 0.987554, 0.686109, 0.020376, 0.013971];						
                        $valor_defecto_cargos = [1.29924, 0.650162, 0.472451, 0.472451, 0.472451, 0.21654];
						break;
					}

					default:
					{
						$valor_defecto_peaje = [0, 0, 0, 0,0,0];
						$valor_defecto_cargos = [0, 0, 0, 0,0,0];
					}
				}

                break;
            }
            case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE:
            {
                $clase_controles = "";
				$valor_defecto_peaje = [0, 0, 0, 0,0,0];
				$valor_defecto_cargos = [0, 0, 0, 0,0,0];
                break;
            }
            default:
            {
                throw new Exception("Tipo de administración desconocido: '".$tipo_administracion."'");
            }
        }

        // Características del tipo de tarifa eléctrica
        $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($tipo);

        // Obtener el año de la tarifa asignada
        if (preg_match('/_\d{4}/', $tipo, $coincidencias)) {
            $anyo = $coincidencias[0];
            $anyo = str_replace('_', '', $anyo);
        }
        else {
            // Si no pone el año en el nombre asumir que es un año no bisiesto
            $anyo = '2021';
        }
        $numero_dias_anyo = obtener_dias_anyo($anyo);

        // Se recuperan los tramos existentes en la base de datos de la tarifa eléctrica
        if ($id_tarifa != ID_NINGUNO)
        {
            $consulta_tramos_tarifa_electrica = "
				SELECT *
				FROM ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_ESPANYA."
				WHERE
					tarifa_electrica = '".$bd_red->_($id_tarifa)."'
                ORDER BY tramo";
			$res_tramos_tarifa_electrica = $bd_red->ejecuta_consulta($consulta_tramos_tarifa_electrica);
            if ($res_tramos_tarifa_electrica == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tramos_tarifa_electrica."'");
            }

            $info_tramos = array();
            while ($fila_tramos_tarifa_electrica = $res_tramos_tarifa_electrica->dame_siguiente_fila())
            {
                array_push($info_tramos, $fila_tramos_tarifa_electrica);
            }
        }
        else
        {
            // Se recuperan los tramos de la tarifa eléctrica
            $numero_tramos = $caracteristicas_tipo_tarifa_electrica["numero_tramos"];
            $info_tramos = array();
            if ($numero_tramos > 0)
            {
                foreach (range(1, $numero_tramos) as $numero_tramo)
                {
                    $info_tramo = array();
                    $info_tramo["tarifa_electrica"] = $id_tarifa;
                    $info_tramo["tramo"] = $numero_tramo;
                    $info_tramo["precio_potencia"] = ($valor_defecto_peaje[$numero_tramo - 1] + $valor_defecto_cargos[$numero_tramo - 1])/$numero_dias_anyo;
                    array_push($info_tramos, $info_tramo);
                }
            }
        }

        // Se agrupan las informaciones de los tramos por tramos "agrupados" (si es necesario), también la información de cargos y peajes
        $tramos_potencias_iguales = $caracteristicas_tipo_tarifa_electrica["tramos_potencias_iguales"];
        if ($tramos_potencias_iguales !== NULL)
        {
            $numero_tramo_agrupado = 1;
            $info_tramos_agrupados = array();
            $valor_defecto_cargos_agrupados = array();
            $valor_defecto_peajes_agrupados = array();
            foreach ($tramos_potencias_iguales as $nombre_tramo => $tramos_potencia_igual)
            {
                $info_tramo_agrupado = array(
                    "tarifa_electrica" => NULL,
                    "nombre" => $nombre_tramo,
                    "tramo" => $numero_tramo_agrupado,
                    "precio_potencia" => "");
                $valor_defecto_cargo_agrupado = 0;
                $valor_defecto_peaje_agrupado = 0;
                foreach ($tramos_potencia_igual as $tramo_potencia_igual)
                {
                    $info_tramo = $info_tramos[$tramo_potencia_igual - 1];
                    if ($info_tramo_agrupado["tarifa_electrica"] === NULL)
                    {
                        $info_tramo_agrupado["tarifa_electrica"] = $info_tramo["tarifa_electrica"];
                    }
                    if ($info_tramo["precio_potencia"] != "")
                    {
                        if ($info_tramo_agrupado["precio_potencia"] == "")
                        {
                            $info_tramo_agrupado["precio_potencia"] = 0;
                        }
                        $info_tramo_agrupado["precio_potencia"] += $info_tramo["precio_potencia"];
                    }

                    $valor_defecto_cargo_agrupado += $valor_defecto_cargos[$tramo_potencia_igual - 1];
                    $valor_defecto_peaje_agrupado += $valor_defecto_peaje[$tramo_potencia_igual - 1];
                }
                array_push($info_tramos_agrupados, $info_tramo_agrupado);
                array_push($valor_defecto_cargos_agrupados, $valor_defecto_cargo_agrupado);
                array_push($valor_defecto_peajes_agrupados, $valor_defecto_peaje_agrupado);
                $numero_tramo_agrupado += 1;
            }

            $info_tramos = $info_tramos_agrupados;
            $valor_defecto_cargos = $valor_defecto_cargos_agrupados;
            $valor_defecto_peaje = $valor_defecto_peajes_agrupados;
        }

        // Se añaden los controles de cada uno de los tramos
        foreach ($info_tramos as $info_tramo)
        {
            // Nombre del tramo (si es agrupado se le asigna un nombre, si no es el número de tramo)
            if (array_key_exists("nombre", $info_tramo) == true)
            {
                $nombre_tramo = htmlspecialchars(strtolower($info_tramo["nombre"]), ENT_QUOTES);
            }
            else
            {
                $nombre_tramo = $info_tramo["tramo"];
            }

            $titulo_campo_tramo = $idiomas->_("Precio de potencia contratada en tramo")." ".$nombre_tramo." (".$idiomas->_("€")."/".$idiomas->_("kW")."-".$idiomas->_("día").")";
            $nombre_id_campo_tramo = "precio_potencia_tramo_tarifa_electrica__".$info_tramo["tramo"];
            $valor_campo_tramo = $info_tramo["precio_potencia"];

            $controles .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_tramo.": "."</span><br/>
                        <input type='text' id='".$nombre_id_campo_tramo."'
                            class='".$clase_controles."TLNT_input_float input-administracion' value='".$valor_campo_tramo."'>
                    </div>
                </div>";

        }

        $defecto = true;
        foreach ($info_tramos as $info_tramo) {         
            
            $valor_campo_tramo = $info_tramo["precio_potencia"];
            $numero_tramo_var = $info_tramo["tramo"] - 1;            
            $valores_por_defecto_sin_round = ($valor_defecto_cargos[$numero_tramo_var] + $valor_defecto_peaje[$numero_tramo_var])/$numero_dias_anyo;
            // Comparison can not be done with float type, it must be done with string equivalent value
            if (strval($valores_por_defecto_sin_round) !== strval($valor_campo_tramo)) {
                $defecto = false;                                 
            }                     
        }
        
    if ($defecto == true) {
        if ($valor_defecto_peaje[0] != 0) {
            $log = dame_log();
            $log->info("El tipo de tarifa que llega a la ventana de potencias contratadas es: ");
            $log->info(strval($tipo));
            // Se anyade esta distinción para mostrar el texto correspondiente al anyo de la tarifa
            if (strpos(strval($tipo), '2022') !== false) {
                $controles .= "
                        <div class='row-fluid'>
                            <div class='span12'>                    
                            Nota: Los valores mostrados por defecto corresponden a la suma de peajes publicados en BOE-A-2021-21208 y los cargos publicados en BOE-2022-4972.  Tienen efecto desde el 1/4/2022 hasta 31/12/2022
                            </div>
                        </div>";
            }
            if (strpos(strval($tipo), '2023') !== false) {
                $controles .= "
                        <div class='row-fluid'>
                            <div class='span12'>                    
                            Nota: Los valores mostrados por defecto corresponden a la suma de peajes publicados en BOE-A-2022-21799 y los cargos publicados en BOE-A-2022-23737. Tienen efecto desde el 01/01/2023 hasta 31/12/2023
                            </div>
                        </div>";
            }

            if (strpos(strval($tipo), '2024') !== false) {
                $controles .= "
                        <div class='row-fluid'>
                            <div class='span12'>                    
                            Nota: Los valores mostrados por defecto corresponden a la suma de peajes publicados en BOE-A-2023-26251 y los cargos publicados en BOE-A-2022-23737. Tienen efecto desde el 01/01/2024 hasta 31/12/2024
                            </div>
                        </div>";
            }

            if (strpos(strval($tipo), '2025') !== false) {
                $controles .= "
                        <div class='row-fluid'>
                            <div class='span12'>                    
                            Nota: Los valores mostrados por defecto corresponden a la suma de peajes publicados en BOE-A-2024-26218 y los cargos publicados en BOE-A-2024-27289. Tienen efecto desde el 01/01/2025 hasta 31/12/2025
                            </div>
                        </div>";
            }

            if (strpos(strval($tipo), '2026') !== false) {
                $controles .= "
                        <div class='row-fluid'>
                            <div class='span12'>                    
                            Nota: Los valores mostrados por defecto corresponden a la suma de peajes publicados en BOE-A-2025-26348 y los cargos publicados en BOE-A-2025-26705. Tienen efecto desde el 01/01/2026 hasta 31/12/2026
                            </div>
                        </div>";
            }
        }
    }

        if ($defecto == false){
            if ($valor_defecto_peaje[0] != 0){
                $controles .= "
                    <div class='row-fluid'>
                        <div class='span12'>                    
                            Valores diferentes a los establecidos por defecto.
                        </div>
                    </div>"; 
            }            
        }

    return ($controles);
}


    // Devuelve los controles las potencias de tramos
    function dame_controles_potencias_tramos_tarifa_tipo_electricidad_Espanya($id_tarifa, $tipo, $tipo_administracion, $prorrateo)
	{
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Control obligatorio y valor por defecto
        switch ($tipo_administracion)
        {
            case TIPO_ADMINISTRACION_TARIFAS_UNICA:
            {
                $clase_controles = "TLNT_input_mandatory"." ";
                $valor_defecto_controles = 0;
                break;
            }
            case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE:
            {
                $clase_controles = "";
                $valor_defecto_controles = "";
                break;
            }
            default:
            {
                throw new Exception("Tipo de administración desconocido: '".$tipo_administracion."'");
            }
        }

        // Características del tipo de tarifa eléctrica
        $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($tipo);

        // Se recuperan los tramos existentes en la base de datos de la tarifa eléctrica
        if ($id_tarifa != ID_NINGUNO)
        {
            $consulta_tramos_tarifa_electrica = "
				SELECT *
				FROM ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_ESPANYA."
				WHERE
					tarifa_electrica = '".$bd_red->_($id_tarifa)."'
                ORDER BY tramo";
			$res_tramos_tarifa_electrica = $bd_red->ejecuta_consulta($consulta_tramos_tarifa_electrica);
            if ($res_tramos_tarifa_electrica == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_tramos_tarifa_electrica."'");
            }

            $info_tramos = array();
            while ($fila_tramos_tarifa_electrica = $res_tramos_tarifa_electrica->dame_siguiente_fila())
            {
                array_push($info_tramos, $fila_tramos_tarifa_electrica);
            }
        }
        else
        {
            // Se recuperan los tramos de la tarifa eléctrica
            $numero_tramos = $caracteristicas_tipo_tarifa_electrica["numero_tramos"];
            $info_tramos = array();
            if ($numero_tramos > 0)
            {
                foreach (range(1, $numero_tramos) as $numero_tramo)
                {
                    $info_tramo = array();
                    $info_tramo["tarifa_electrica"] = $id_tarifa;
                    $info_tramo["tramo"] = $numero_tramo;
                    $info_tramo["potencia"] = $valor_defecto_controles;
                    array_push($info_tramos, $info_tramo);
                }
            }
        }

        // Se agrupan las informaciones de los tramos por tramos "agrupados" (si es necesario)
        $tramos_potencias_iguales = $caracteristicas_tipo_tarifa_electrica["tramos_potencias_iguales"];
        if ($tramos_potencias_iguales !== NULL)
        {
            $info_tramos_agrupados = array();
            $numero_tramo_agrupado = 1;
            foreach ($tramos_potencias_iguales as $nombre_tramo => $tramos_potencia_igual)
            {
                $info_tramo_agrupado = array(
                    "tarifa_electrica" => NULL,
                    "nombre" => $nombre_tramo,
                    "tramo" => $numero_tramo_agrupado,
                    "potencia" => NULL);
                foreach ($tramos_potencia_igual as $tramo_potencia_igual)
                {
                    $info_tramo = $info_tramos[$tramo_potencia_igual - 1];
                    if ($info_tramo_agrupado["tarifa_electrica"] === NULL)
                    {
                        $info_tramo_agrupado["tarifa_electrica"] = $info_tramo["tarifa_electrica"];
                    }
                    if ($info_tramo_agrupado["potencia"] === NULL)
                    {
                        $info_tramo_agrupado["potencia"] = $info_tramo["potencia"];
                    }
                }
                array_push($info_tramos_agrupados, $info_tramo_agrupado);
                $numero_tramo_agrupado += 1;
            }
            $info_tramos = $info_tramos_agrupados;
        }

        $opcion_prorrateo = $caracteristicas_tipo_tarifa_electrica["prorrateo_potencias"];
        $log = dame_log();
        $log -> info("1 La variable opción prorrateo en la ventana de potencias contratadas es: ");
        $log -> info($opcion_prorrateo);
        $log -> info(" 2 La variable prorrateo es:");
        $log -> info($opcion_prorrateo);
		$controles = "";
        if ($opcion_prorrateo == true){
            $controles .= "
                    <div class='row-fluid'>
                        <div class='span12'><span class='titulo-campo-administracion'>" 
                        . $idiomas->_("Prorrateo en el cálculo de los excesos de potencia") 
                        . ": " . "</span><br/>
                            <select id='prorrateo_tarifa' class='select-administracion'>";
            $controles .= dame_lista_prorrateo($prorrateo);
            $controles .= "
                            </select><br/>
                            <div class='row-fluid'>
                                <div class='span12'>   
                                    <i class='icon-info-sign color-azul icono-ayuda'></i>
                                    El prorrateo sólo es de aplicación cuando el método de cálculo de los excesos de potencia es por mediciones cuartohorarias 
                                    (puntos de medida de tipo 1,2, y 3 con potencias contratada > 50 kW).
                                </div>  
                            </div>            
                            <hr style='background-color: #9F9F9F;'>
                        </div>
                    </div>";            
        }

        // Se añaden los controles de cada uno de los tramos
        foreach ($info_tramos as $info_tramo)
        {
            // Nombre del tramo (si es agrupado se le asigna un nombre, si no es el número de tramo)
            if (array_key_exists("nombre", $info_tramo) == true)
            {
                $nombre_tramo = htmlspecialchars(strtolower($info_tramo["nombre"]), ENT_QUOTES);
            }
            else
            {
                $nombre_tramo = $info_tramo["tramo"];
            }
            $titulo_campo_tramo = $idiomas->_("Potencia contratada en tramo")." ".$nombre_tramo." (".$idiomas->_("kW").")";
            $nombre_id_campo_tramo = "potencia_tramo_tarifa_electrica__".$info_tramo["tramo"];
            $valor_campo_tramo = $info_tramo["potencia"];

            $controles .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_tramo.": "."</span><br/>
                        <input type='text' id='".$nombre_id_campo_tramo."'
                            class='".$clase_controles."TLNT_input_float input-administracion' value='".$valor_campo_tramo."'>
                    </div>
                </div>";
        }

        return ($controles);
    }


    // Devuelve la información de los tramos de la tarifa eléctrica
	function dame_info_tramos_tarifa_electricidad_Espanya($id_tarifa)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan los tramos existentes en la base de datos de la tarifa eléctrica
        $consulta_tramos_tarifa_electrica = "
            SELECT *
            FROM ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                tarifa_electrica = '".$bd_red->_($id_tarifa)."'
            ORDER BY
                tramo";
        $res_tramos_tarifa_electrica = $bd_red->ejecuta_consulta($consulta_tramos_tarifa_electrica);
        if ($res_tramos_tarifa_electrica == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tramos_tarifa_electrica."'");
        }

        $info_tramos = array();
        while ($fila_tramos_tarifa_electrica = $res_tramos_tarifa_electrica->dame_siguiente_fila())
        {
            $info_tramo = array(
                "precio_consumo" => $fila_tramos_tarifa_electrica["precio_consumo"],
                "precio_consumo_tarifa_acceso" => $fila_tramos_tarifa_electrica["precio_consumo_tarifa_acceso"],
                "precio_potencia" => $fila_tramos_tarifa_electrica["precio_potencia"],
                "potencia" => $fila_tramos_tarifa_electrica["potencia"]);
            $info_tramos[$fila_tramos_tarifa_electrica["tramo"]] = $info_tramo;
        }
        return ($info_tramos);
    }


    // Devuelve la tabla de tramos de una tarifa eléctrica
    function dame_tabla_tramos_tarifa_electricidad_Espanya($id_tarifa, $fila_tarifa_electrica, $incluir_salto_linea = true)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Fila de tarifa eléctrica
        if ($fila_tarifa_electrica === NULL)
        {
            $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa);
        }

        // Tipo y contrato de tarifa eléctrica
        $tipo_tarifa_electrica = $fila_tarifa_electrica["tipo"];
        $contrato_tarifa_electrica = $fila_tarifa_electrica["contrato"];

        // Cabecera de la tabla
        switch ($contrato_tarifa_electrica)
        {
            case CONTRATO_TARIFA_ELECTRICA_ESPANYA_FIJO:
            {
                $cabecera_tabla = array(
                    $idiomas->_("Tramo"),
                    $idiomas->_("Precio de consumo"),
                    $idiomas->_("Precio de consumo de tarifa de acceso"),
                    $idiomas->_("Precio de potencia contratada"),
                    $idiomas->_("Potencia contratada")
                );
                $params_tabla = array(
                    "numero_columnas" => NUMERO_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_FIJO,
                    "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_FIJO),
                    "generar_valores_xml" => true
                );

                break;
            }
            case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL:
            {
                $cabecera_tabla = array(
                    $idiomas->_("Tramo"),
                    $idiomas->_("Coeficientes de precio de consumo A y B"),
                    $idiomas->_("Precio de consumo de tarifa de acceso"),
                    $idiomas->_("Precio de potencia contratada"),
                    $idiomas->_("Potencia contratada")
                );
                $params_tabla = array(
                    "numero_columnas" => NUMERO_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_PASS_POOL,
                    "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_PASS_POOL),
                    "generar_valores_xml" => true
                );
                break;
            }
            case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH:
            {
                $cabecera_tabla = array(
                    $idiomas->_("Tramo"),
                    $idiomas->_("Precio de consumo de tarifa de acceso"),
                    $idiomas->_("Precio de potencia contratada"),
                    $idiomas->_("Potencia contratada")
                );
                $params_tabla = array(
                    "numero_columnas" => NUMERO_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_PASS_THROUGH,
                    "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_PASS_THROUGH),
                    "generar_valores_xml" => true
                );
                break;
            }
            case CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE:
            {
                $cabecera_tabla = array(
                    $idiomas->_("Tramo"),
                    $idiomas->_("Precio de consumo de tarifa de acceso"),
                    $idiomas->_("Precio de potencia contratada"),
                    $idiomas->_("Potencia contratada")
                );
                $params_tabla = array(
                    "numero_columnas" => NUMERO_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_CIERRE,
                    "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_CIERRE),
                    "generar_valores_xml" => true
                );
                break;
            }
        }
        $titulo_tabla_tramos_tarifa_electrica = $idiomas->_("Tramos de tarifa eléctrica");
        $tabla = new TablaDatos(
            "tabla-tramos-tarifa-electrica",
            $titulo_tabla_tramos_tarifa_electrica,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $tabla->anyade_cabecera("", $cabecera_tabla);

        // Tramos con potencias iguales
        $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($tipo_tarifa_electrica);
        $tramos_potencias_iguales = $caracteristicas_tipo_tarifa_electrica["tramos_potencias_iguales"];

        // Se recupera la información de los tramos y se añade a la tabla
        $consulta_tramos_tarifa_electrica = "
            SELECT *
            FROM ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                tarifa_electrica = '".$bd_red->_($id_tarifa)."'";
        $res_tramos_tarifa_electrica = $bd_red->ejecuta_consulta($consulta_tramos_tarifa_electrica);
        if ($res_tramos_tarifa_electrica == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tramos_tarifa_electrica."'");
        }
        while ($fila_tramos_tarifa_electrica = $res_tramos_tarifa_electrica->dame_siguiente_fila())
        {
            $tramo = $fila_tramos_tarifa_electrica["tramo"];
            $precio_consumo = $fila_tramos_tarifa_electrica["precio_consumo"];
            $coeficiente_a_precio_consumo_pass_pool = $fila_tramos_tarifa_electrica["coeficiente_a_precio_consumo_pass_pool"];
            $coeficiente_b_precio_consumo_pass_pool = $fila_tramos_tarifa_electrica["coeficiente_b_precio_consumo_pass_pool"];
            $precio_consumo_tarifa_acceso = $fila_tramos_tarifa_electrica["precio_consumo_tarifa_acceso"];
            $precio_potencia = $fila_tramos_tarifa_electrica["precio_potencia"];
            $potencia = $fila_tramos_tarifa_electrica["potencia"];

            // Cadenas de los datos del tramo
            $cadena_tramo = "P".$fila_tramos_tarifa_electrica["tramo"];
            $cadena_precio_consumo = formatea_numero($precio_consumo, 6)." ".$idiomas->_("€")."/".$idiomas->_("kWh");
            $cadena_coeficientes_precio_consumo = formatea_numero($coeficiente_a_precio_consumo_pass_pool, 5).
                " - ".formatea_numero($coeficiente_b_precio_consumo_pass_pool, 5)." ".$idiomas->_("€")."/".$idiomas->_("MWh");
            $cadena_precio_consumo_tarifa_acceso = formatea_numero($precio_consumo_tarifa_acceso, 6)." ".$idiomas->_("€")."/".$idiomas->_("kWh");
            $cadena_precio_potencia = formatea_numero($precio_potencia, 6)." ".$idiomas->_("€")."/".$idiomas->_("kW")."-".$idiomas->_("día");
            $cadena_potencia = formatea_numero($potencia, 5)." ".$idiomas->_("kW");

            // Se modifican los datos de potencias si hay tramos con potencias iguales
            if ($tramos_potencias_iguales !== NULL)
            {
                foreach ($tramos_potencias_iguales as $nombre_tramo => $tramos_potencia_igual)
                {
                    if (in_array($tramo, $tramos_potencia_igual) == true)
                    {
                        $precio_potencia_tramo_agrupado = $precio_potencia * count($tramos_potencia_igual);
                        $cadena_precio_potencia = formatea_numero($precio_potencia_tramo_agrupado, 6)." ".$idiomas->_("€")."/".$idiomas->_("kW")."-".$idiomas->_("día").
                            " (".strtolower($nombre_tramo).")";
                        $cadena_potencia .= " (".strtolower($nombre_tramo).")";
                    }
                }
            }

            // Diferentes columnas según el contrato de tarifa eléctrica
            switch ($contrato_tarifa_electrica)
            {
                case CONTRATO_TARIFA_ELECTRICA_ESPANYA_FIJO:
                {
                    $fila_tramo = array(
                        $cadena_tramo,
                        $cadena_precio_consumo,
                        $cadena_precio_consumo_tarifa_acceso,
                        $cadena_precio_potencia,
                        $cadena_potencia);
                    break;
                }
                case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL:
                {
                    $fila_tramo = array(
                        $cadena_tramo,
                        $cadena_coeficientes_precio_consumo,
                        $cadena_precio_consumo_tarifa_acceso,
                        $cadena_precio_potencia,
                        $cadena_potencia);
                    break;
                }
                case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH:
                {
                    $fila_tramo = array(
                        $cadena_tramo,
                        $cadena_precio_consumo_tarifa_acceso,
                        $cadena_precio_potencia,
                        $cadena_potencia);
                    break;
                }
                case CONTRATO_TARIFA_ELECTRICA_ESPANYA_CIERRE:
                {
                    $fila_tramo = array(
                        $cadena_tramo,
                        $cadena_precio_consumo_tarifa_acceso,
                        $cadena_precio_potencia,
                        $cadena_potencia);
                    break;
                }
            }
            $tabla->anyade_fila("", $fila_tramo);
        }

        return ($tabla->dame_tabla($incluir_salto_linea));
    }


    //
    // Funciones de parámetros de energía eléctrica
    //


    // Se recupera una tabla con información de los parámetros de energía eléctrica
	function dame_tabla_informacion_parametros_energia_electricidad_Espanya()
	{
        $idiomas = new Idiomas();

        $info = "";
        $info .= "<div class='informacion-tabla-datos'>";

        // Última hora de valores de indicadores de energía eléctrica (1)
        $ids_grupos_indicadores_energia_electrica = array(1, 2);
        foreach ($ids_grupos_indicadores_energia_electrica as $id_grupo_indicadores_energia_electrica)
        {
            switch ($id_grupo_indicadores_energia_electrica)
            {
                case 1:
                {
                    $tabla_valores_indicadores_energia_electrica = TABLA_VALORES_INDICADORES_1_ENERGIA_ELECTRICA_ESPANYA;
                    break;
                }
                case 2:
                {
                    $tabla_valores_indicadores_energia_electrica = TABLA_VALORES_INDICADORES_2_ENERGIA_ELECTRICA_ESPANYA;
                    break;
                }
                default:
                {
                    throw new Exception("Id de indicador de grupo desconocido: '".$id_grupo_indicadores_energia_electrica."'");
                }
            }
            $res_horas_ultimos_valores_parametros = dame_horas_ultimos_valores_parametros_energia_electricidad_Espanya($tabla_valores_indicadores_energia_electrica);
            $cadena_hora_valores_estimados = $res_horas_ultimos_valores_parametros["cadena_hora_valores_estimados"];
            $cadena_hora_recuperacion_valores_estimados = $res_horas_ultimos_valores_parametros["cadena_hora_recuperacion_valores_estimados"];
            $cadena_hora_valores_ajustados = $res_horas_ultimos_valores_parametros["cadena_hora_valores_ajustados"];
            $cadena_hora_recuperacion_valores_ajustados = $res_horas_ultimos_valores_parametros["cadena_hora_recuperacion_valores_ajustados"];
            if ($cadena_hora_valores_estimados === NULL)
            {
                $info .= "<i class='icon-warning-sign color-rojo'></i> ".
                    $idiomas->_("No hay valores de indicadores de energía eléctrica")." (".$id_grupo_indicadores_energia_electrica.")<br/>";
            }
            else
            {
                $info .= "<i class='icon-info-sign color-azul'></i> ".
                    $idiomas->_("Fecha de últimos valores de indicadores de energía eléctrica")." (".$id_grupo_indicadores_energia_electrica."): ".$cadena_hora_valores_estimados.
                    " (".$idiomas->_("fecha de recuperación").": ".$cadena_hora_recuperacion_valores_estimados.")"."<br/>";
            }
            $info .= "<ul><li>";
            if ($cadena_hora_valores_ajustados !== NULL)
            {
                $info .= $idiomas->_("Fecha de últimos valores ajustados").": ".$cadena_hora_valores_ajustados.
                    " (".$idiomas->_("fecha de recuperación").": ".$cadena_hora_recuperacion_valores_ajustados.")"."<br/>";
            }
            else
            {
                $info .= $idiomas->_("No hay valores ajustados");
            }
            $info .= "</li></ul>";
        }

        // Última hora de valores de compensación del gas AJOM
        $res_horas_ultimos_valores_parametros = dame_horas_ultimos_valores_parametros_energia_electricidad_Espanya(TABLA_VALORES_COMPENSACION_GAS_AJOM_ESPANYA);
        $cadena_hora_valores_estimados = $res_horas_ultimos_valores_parametros["cadena_hora_valores_estimados"];
        $cadena_hora_recuperacion_valores_estimados = $res_horas_ultimos_valores_parametros["cadena_hora_recuperacion_valores_estimados"];
        $cadena_hora_valores_ajustados = $res_horas_ultimos_valores_parametros["cadena_hora_valores_ajustados"];
        $cadena_hora_recuperacion_valores_ajustados = $res_horas_ultimos_valores_parametros["cadena_hora_recuperacion_valores_ajustados"];
        if ($cadena_hora_valores_estimados === NULL)
        {
            $info .= "<i class='icon-warning-sign color-rojo'></i> ".
                $idiomas->_("No hay valores de compensación del gas (AJOM)")."<br/>";
        }
        else
        {
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Fecha de últimos valores de compensación del gas (AJOM)").": ".$cadena_hora_valores_estimados.
                " (".$idiomas->_("fecha de recuperación").": ".$cadena_hora_recuperacion_valores_estimados.")"."<br/>";
        }
        $info .= "<ul><li>";
        if ($cadena_hora_valores_ajustados !== NULL)
        {
            $info .= $idiomas->_("Fecha de últimos valores ajustados").": ".$cadena_hora_valores_ajustados.
                " (".$idiomas->_("fecha de recuperación").": ".$cadena_hora_recuperacion_valores_ajustados.")"."<br/>";
        }
        else
        {
            $info .= $idiomas->_("No hay valores ajustados");
        }
        $info .= "</li></ul>"; 

        // Última hora hora de valores de compensación del gas MAJ3
        $res_horas_ultimos_valores_parametros = dame_horas_ultimos_valores_parametros_energia_electricidad_Espanya(TABLA_VALORES_COMPENSACION_GAS_MAJ3_ESPANYA);
        $cadena_hora_valores_estimados = $res_horas_ultimos_valores_parametros["cadena_hora_valores_estimados"];
        $cadena_hora_recuperacion_valores_estimados = $res_horas_ultimos_valores_parametros["cadena_hora_recuperacion_valores_estimados"];
        $cadena_hora_valores_ajustados = $res_horas_ultimos_valores_parametros["cadena_hora_valores_ajustados"];
        $cadena_hora_recuperacion_valores_ajustados = $res_horas_ultimos_valores_parametros["cadena_hora_recuperacion_valores_ajustados"];
        if ($cadena_hora_valores_estimados === NULL)
        {
            $info .= "<i class='icon-warning-sign color-rojo'></i> ".
                $idiomas->_("No hay valores de compensación del gas (MAJ3)")."<br/>";
        }
        else
        {
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Fecha de últimos valores de compensación del gas (MAJ3)").": ".$cadena_hora_valores_estimados.
                " (".$idiomas->_("fecha de recuperación").": ".$cadena_hora_recuperacion_valores_estimados.")"."<br/>";
        }
        $info .= "<ul><li>";
        if ($cadena_hora_valores_ajustados !== NULL)
        {
            $info .= $idiomas->_("Fecha de últimos valores ajustados").": ".$cadena_hora_valores_ajustados.
                " (".$idiomas->_("fecha de recuperación").": ".$cadena_hora_recuperacion_valores_ajustados.")"."<br/>";
        }
        else
        {
            $info .= $idiomas->_("No hay valores ajustados");
        }
        $info .= "</li></ul>"; 

        // Última hora de coeficientes de pérdidas de energía eléctrica
        $res_horas_ultimos_valores_parametros = dame_horas_ultimos_valores_parametros_energia_electricidad_Espanya(TABLA_COEFICIENTES_PERDIDAS_ENERGIA_ELECTRICA_ESPANYA);
        $cadena_hora_valores_estimados = $res_horas_ultimos_valores_parametros["cadena_hora_valores_estimados"];
        $cadena_hora_recuperacion_valores_estimados = $res_horas_ultimos_valores_parametros["cadena_hora_recuperacion_valores_estimados"];
        $cadena_hora_valores_ajustados = $res_horas_ultimos_valores_parametros["cadena_hora_valores_ajustados"];
        $cadena_hora_recuperacion_valores_ajustados = $res_horas_ultimos_valores_parametros["cadena_hora_recuperacion_valores_ajustados"];
        if ($cadena_hora_valores_estimados === NULL)
        {
            $info .= "<i class='icon-warning-sign color-rojo'></i> ".
                $idiomas->_("No hay coeficientes de pérdidas de energía eléctrica")."<br/>";
        }
        else
        {
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Fecha de últimos coeficientes de pérdidas de energía eléctrica").": ".$cadena_hora_valores_estimados.
                " (".$idiomas->_("fecha de recuperación").": ".$cadena_hora_recuperacion_valores_estimados.")"."<br/>";
        }
        $info .= "<ul><li>";
        if ($cadena_hora_valores_ajustados !== NULL)
        {
            $info .= $idiomas->_("Fecha de últimos valores ajustados").": ".$cadena_hora_valores_ajustados.
                " (".$idiomas->_("fecha de recuperación").": ".$cadena_hora_recuperacion_valores_ajustados.")"."<br/>";
        }
        else
        {
            $info .= $idiomas->_("No hay valores ajustados");
        }
        $info .= "</li></ul>";

        // Última hora de coeficientes de pérdidas de energía eléctrica Tarifas Junio 2021
        $res_horas_ultimos_valores_parametros = dame_horas_ultimos_valores_parametros_energia_electricidad_Espanya(TABLA_VALORES_PERDIDAS_ENERGIA_ELECTRICA_ESPANYA_2021);
        $cadena_hora_valores_estimados = $res_horas_ultimos_valores_parametros["cadena_hora_valores_estimados"];
        $cadena_hora_recuperacion_valores_estimados = $res_horas_ultimos_valores_parametros["cadena_hora_recuperacion_valores_estimados"];
        $cadena_hora_valores_ajustados = $res_horas_ultimos_valores_parametros["cadena_hora_valores_ajustados"];
        $cadena_hora_recuperacion_valores_ajustados = $res_horas_ultimos_valores_parametros["cadena_hora_recuperacion_valores_ajustados"];
        if ($cadena_hora_valores_estimados === NULL)
        {
            $info .= "<i class='icon-warning-sign color-rojo'></i> ".
                $idiomas->_("No hay coeficientes de pérdidas de energía eléctrica")."<br/>";
        }
        else
        {
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Fecha de últimos coeficientes de pérdidas de energía eléctrica").": ".$cadena_hora_valores_estimados.
                " (".$idiomas->_("fecha de recuperación").": ".$cadena_hora_recuperacion_valores_estimados.")"."<br/>";
        }
        $info .= "<ul><li>";
        if ($cadena_hora_valores_ajustados !== NULL)
        {
            $info .= $idiomas->_("Fecha de últimos valores ajustados").": ".$cadena_hora_valores_ajustados.
                " (".$idiomas->_("fecha de recuperación").": ".$cadena_hora_recuperacion_valores_ajustados.")"."<br/>";
        }
        else
        {
            $info .= $idiomas->_("No hay valores ajustados");
        }
        $info .= "</li></ul>";

        // Última hora de valores de pérdidas de energía eléctrica
        $res_horas_ultimos_valores_parametros = dame_horas_ultimos_valores_parametros_energia_electricidad_Espanya(TABLA_VALORES_PERDIDAS_ENERGIA_ELECTRICA_ESPANYA);
        $cadena_hora_valores_estimados = $res_horas_ultimos_valores_parametros["cadena_hora_valores_estimados"];
        $cadena_hora_recuperacion_valores_estimados = $res_horas_ultimos_valores_parametros["cadena_hora_recuperacion_valores_estimados"];
        $cadena_hora_valores_ajustados = $res_horas_ultimos_valores_parametros["cadena_hora_valores_ajustados"];
        $cadena_hora_recuperacion_valores_ajustados = $res_horas_ultimos_valores_parametros["cadena_hora_recuperacion_valores_ajustados"];
        if ($cadena_hora_valores_estimados === NULL)
        {
            $info .= "<i class='icon-warning-sign color-rojo'></i> ".
                $idiomas->_("No hay valores de pérdidas de energía eléctrica")."<br/>";
        }
        else
        {
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Fecha de últimos valores de pérdidas de energía eléctrica").": ".$cadena_hora_valores_estimados.
                " (".$idiomas->_("fecha de recuperación").": ".$cadena_hora_recuperacion_valores_estimados.")"."<br/>";
        }
        $info .= "<ul><li>";
        if ($cadena_hora_valores_ajustados !== NULL)
        {
            $info .= $idiomas->_("Fecha de últimos valores ajustados").": ".$cadena_hora_valores_ajustados.
                " (".$idiomas->_("fecha de recuperación").": ".$cadena_hora_recuperacion_valores_ajustados.")"."<br/>";
        }
        else
        {
            $info .= $idiomas->_("No hay valores ajustados");
        }
        $info .= "</li></ul>";

        // Última hora de valores de PVPC de energía eléctrica
        $res_horas_ultimos_valores_parametros = dame_horas_ultimos_valores_parametros_energia_electricidad_Espanya(TABLA_VALORES_PVPC_ENERGIA_ELECTRICA_ESPANYA);
        $cadena_hora_valores_estimados = $res_horas_ultimos_valores_parametros["cadena_hora_valores_estimados"];
        $cadena_hora_recuperacion_valores_estimados = $res_horas_ultimos_valores_parametros["cadena_hora_recuperacion_valores_estimados"];
        $cadena_hora_valores_ajustados = $res_horas_ultimos_valores_parametros["cadena_hora_valores_ajustados"];
        $cadena_hora_recuperacion_valores_ajustados = $res_horas_ultimos_valores_parametros["cadena_hora_recuperacion_valores_ajustados"];
        if ($cadena_hora_valores_estimados === NULL)
        {
            $info .= "<i class='icon-warning-sign color-rojo'></i> ".
                $idiomas->_("No hay valores de PVPC de energía eléctrica")."<br/>";
        }
        else
        {
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Fecha de últimos valores de PVPC de energía eléctrica").": ".$cadena_hora_valores_estimados.
                " (".$idiomas->_("fecha de recuperación").": ".$cadena_hora_recuperacion_valores_estimados.")"."<br/>";
        }
        $info .= "<ul><li>";
        if ($cadena_hora_valores_ajustados !== NULL)
        {
            $info .= $idiomas->_("Fecha de últimos valores ajustados").": ".$cadena_hora_valores_ajustados.
                " (".$idiomas->_("fecha de recuperación").": ".$cadena_hora_recuperacion_valores_ajustados.")"."<br/>";
        }
        else
        {
            $info .= $idiomas->_("No hay valores ajustados");
        }
        $info .= "</li></ul>";

        // Última hora de valores de desvíos de energía eléctrica
        $res_horas_ultimos_valores_parametros = dame_horas_ultimos_valores_parametros_energia_electricidad_Espanya(TABLA_VALORES_DESVIOS_ENERGIA_ELECTRICA_ESPANYA);
        $cadena_hora_valores_estimados = $res_horas_ultimos_valores_parametros["cadena_hora_valores_estimados"];
        $cadena_hora_recuperacion_valores_estimados = $res_horas_ultimos_valores_parametros["cadena_hora_recuperacion_valores_estimados"];
        $cadena_hora_valores_ajustados = $res_horas_ultimos_valores_parametros["cadena_hora_valores_ajustados"];
        $cadena_hora_recuperacion_valores_ajustados = $res_horas_ultimos_valores_parametros["cadena_hora_recuperacion_valores_ajustados"];
        if ($cadena_hora_valores_estimados === NULL)
        {
            $info .= "<i class='icon-warning-sign color-rojo'></i> ".
                $idiomas->_("No hay valores de desvíos de energía eléctrica")."<br/>";
        }
        else
        {
            $info .= "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Fecha de últimos valores de desvíos de energía eléctrica").": ".$cadena_hora_valores_estimados.
                " (".$idiomas->_("fecha de recuperación").": ".$cadena_hora_recuperacion_valores_estimados.")"."<br/>";
        }
        $info .= "<ul><li>";
        if ($cadena_hora_valores_ajustados !== NULL)
        {
            $info .= $idiomas->_("Fecha de últimos valores ajustados").": ".$cadena_hora_valores_ajustados.
                " (".$idiomas->_("fecha de recuperación").": ".$cadena_hora_recuperacion_valores_ajustados.")"."<br/>";
        }
        else
        {
            $info .= $idiomas->_("No hay valores ajustados");
        }
        $info .= "</li></ul>";

        // Se introduce la información en una tabla
        $boton_actualizar_informacion_parametros_energia_electrica = "<i id='boton_smartmeter_actualizar_informacion_parametros_energia_electricidad_Espanya' class='icon-refresh color-blanco boton-tabla-datos'></i>";
        $opciones = array($boton_actualizar_informacion_parametros_energia_electrica);

        $params_tabla = array(
            "opciones" => $opciones
        );
        $tabla = new TablaDatos(
            "tabla-smartmeter-informacion-parametros-energia-electrica",
            $idiomas->_("Información de parámetros de energía eléctrica"),
            TIPO_TABLA_DATOS_CONTENEDOR,
            $params_tabla
        );
        $tabla->anyade_contenido("", $info);

        // Se devuelve la tabla
        return ($tabla);
    }


    // Se recuperan las horas de últimos valores de la tabla de parámetros de energía eléctrica especificada
	function dame_horas_ultimos_valores_parametros_energia_electricidad_Espanya($tabla_parametros_energia_electrica)
	{
        $bd_datos = BaseDatosDatos::dame_base_datos();
        $zona_horaria = dame_zona_horaria_local();

        // Última hora de valores de parámetros de energía eléctrica (estimados)
        $consulta_valores_parametros_energia_electrica_estimados = "
            SELECT
                hora,
                hora_recuperacion
            FROM ".$tabla_parametros_energia_electrica."
            WHERE
                tipo_valores = '".$bd_datos->_(TIPO_VALORES_PARAMETROS_ENERGIA_ELECTRICA_ESTIMADOS)."'
            ORDER BY hora DESC
            LIMIT 1";
        $res_valores_parametros_energia_electrica_estimados = $bd_datos->ejecuta_consulta($consulta_valores_parametros_energia_electrica_estimados);
        if ($res_valores_parametros_energia_electrica_estimados == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_parametros_energia_electrica_estimados."'");
        }
        if ($res_valores_parametros_energia_electrica_estimados->dame_numero_filas() == 0)
        {
            $cadena_hora_ultimos_valores_parametros_energia_electrica_estimados_local = NULL;
            $cadena_hora_recuperacion_ultimos_valores_parametros_energia_electrica_estimados_local = NULL;
        }
        else
        {
            $fila_valores_parametros_energia_electrica_estimados = $res_valores_parametros_energia_electrica_estimados->dame_siguiente_fila();
            $hora_ultimos_valores_parametros_energia_electrica_estimados = $fila_valores_parametros_energia_electrica_estimados["hora"];
            $hora_recuperacion_ultimos_valores_parametros_energia_electrica_estimados = $fila_valores_parametros_energia_electrica_estimados["hora_recuperacion"];

            $cadena_hora_ultimos_valores_parametros_energia_electrica_estimados_utc = convierte_formato_fecha($hora_ultimos_valores_parametros_energia_electrica_estimados, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
            $cadena_hora_ultimos_valores_parametros_energia_electrica_estimados_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_ultimos_valores_parametros_energia_electrica_estimados_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_hora_recuperacion_ultimos_valores_parametros_energia_electrica_estimados_utc = convierte_formato_fecha($hora_recuperacion_ultimos_valores_parametros_energia_electrica_estimados, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
            $cadena_hora_recuperacion_ultimos_valores_parametros_energia_electrica_estimados_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_recuperacion_ultimos_valores_parametros_energia_electrica_estimados_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
        }

        // Última hora de valores de parámetros de energía eléctrica (ajustados)
        $consulta_valores_parametros_energia_electrica_ajustados = "
            SELECT
                hora,
                hora_recuperacion
            FROM ".$tabla_parametros_energia_electrica."
            WHERE
                tipo_valores = '".$bd_datos->_(TIPO_VALORES_PARAMETROS_ENERGIA_ELECTRICA_AJUSTADOS)."'
            ORDER BY hora DESC
            LIMIT 1";
        $res_valores_parametros_energia_electrica_ajustados = $bd_datos->ejecuta_consulta($consulta_valores_parametros_energia_electrica_ajustados);
        if ($res_valores_parametros_energia_electrica_ajustados == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_valores_parametros_energia_electrica_ajustados."'");
        }
        if ($res_valores_parametros_energia_electrica_ajustados->dame_numero_filas() == 0)
        {
            $cadena_hora_ultimos_valores_parametros_energia_electrica_ajustados_local = NULL;
            $cadena_hora_recuperacion_ultimos_valores_parametros_energia_electrica_ajustados_local = NULL;
        }
        else
        {
            $fila_valores_parametros_energia_electrica_ajustados = $res_valores_parametros_energia_electrica_ajustados->dame_siguiente_fila();
            $hora_ultimos_valores_parametros_energia_electrica_ajustados = $fila_valores_parametros_energia_electrica_ajustados["hora"];
            $hora_recuperacion_ultimos_valores_parametros_energia_electrica_ajustados = $fila_valores_parametros_energia_electrica_ajustados["hora_recuperacion"];

            $cadena_hora_ultimos_valores_parametros_energia_electrica_ajustados_utc = convierte_formato_fecha($hora_ultimos_valores_parametros_energia_electrica_ajustados, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
            $cadena_hora_ultimos_valores_parametros_energia_electrica_ajustados_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_ultimos_valores_parametros_energia_electrica_ajustados_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
            $cadena_hora_recuperacion_ultimos_valores_parametros_energia_electrica_ajustados_utc = convierte_formato_fecha($hora_recuperacion_ultimos_valores_parametros_energia_electrica_ajustados, FORMATO_FECHA_HORA_BASE_DATOS, $_SESSION["formato_fecha_hora_local"]);
            $cadena_hora_recuperacion_ultimos_valores_parametros_energia_electrica_ajustados_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_recuperacion_ultimos_valores_parametros_energia_electrica_ajustados_utc, $_SESSION["formato_fecha_hora_local"], ZONA_HORARIA_UTC, $zona_horaria);
        }

        return (array(
            "cadena_hora_valores_estimados" => $cadena_hora_ultimos_valores_parametros_energia_electrica_estimados_local,
            "cadena_hora_recuperacion_valores_estimados" => $cadena_hora_recuperacion_ultimos_valores_parametros_energia_electrica_estimados_local,
            "cadena_hora_valores_ajustados" => $cadena_hora_ultimos_valores_parametros_energia_electrica_ajustados_local,
            "cadena_hora_recuperacion_valores_ajustados" => $cadena_hora_recuperacion_ultimos_valores_parametros_energia_electrica_ajustados_local));
    }


    //
    // Funciones para las secciones de tarifas
    //


    function dame_tabla_filtro_tarifas_tabla_electricidad_Espanya()
    {
        $idiomas = new Idiomas();

        // Se recuperan los controles a mostrar
        $controles = array();
        $id_controles = "smartmeter_filtro_tarifas_tabla_electricidad_Espanya";

        // Tipos de tarifa eléctrica
        $control_lista_tipos .= "<div id='etiqueta_tipo_tarifa_electrica_".$id_controles."'>".$idiomas->_("Tipo").": "."</div>";
        $control_lista_tipos .= "<select id='tipo_tarifa_electrica_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_tipos .= dame_lista_tipos_tarifa_electricidad_Espanya(TIPO_TARIFA_TODOS, OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_TODOS);
        $control_lista_tipos .= "</select>";
        array_push($controles, $control_lista_tipos);

        // Contratos de tarifa eléctrica
        $control_lista_contratos .= "<div id='etiqueta_contrato_tarifa_electrica_".$id_controles."'>".$idiomas->_("Contrato").": "."</div>";
        $control_lista_contratos .= "<select id='contrato_tarifa_electrica_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_contratos .= dame_lista_contratos_tarifa_electricidad_Espanya(CONTRATO_TARIFA_ELECTRICA_TODOS, OPCIONES_EXTRA_LISTA_CONTRATOS_TARIFA_ELECTRICA_TODOS);
        $control_lista_contratos .= "</select>";
        array_push($controles, $control_lista_contratos);

        // Grupos de tarifas eléctricas
        $control_lista_grupos .= "<div id='etiqueta_grupo_tarifas_electricas_".$id_controles."'>".$idiomas->_("Grupo").": "."</div>";
        $control_lista_grupos .= "<select id='id_grupo_tarifas_electricas_".$id_controles."' class='chosen-select' hidden>";
        $control_lista_grupos .= dame_lista_grupos_tarifas(MEDICION_ELECTRICIDAD, ID_TODOS, OPCIONES_EXTRA_LISTA_GRUPOS_TARIFAS_TODOS_NINGUNO);
        $control_lista_grupos .= "</select>";
        array_push($controles, $control_lista_grupos);

        // Estado de tarifas (de expiración)
        $control_lista_estados .= dame_control_lista_estados_tarifa($id_controles, $idiomas->_("Estado"));
        array_push($controles, $control_lista_estados);

        // Nombre
        $filtro_tarifas_electricas = dame_filtro_texto_controles_extra($id_controles, $idiomas->_("Nombre"), $controles);

        // Se crea la tabla contenedora
        $tabla = new TablaDatos(
            "tabla-smartmeter-filtro-tarifas-electricas-tabla",
            $idiomas->_("Filtro de tarifas"),
            TIPO_TABLA_DATOS_CONTENEDOR
        );

        $params_fila = array(
            "clase_dato" => "filtro-informes",
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_TARIFAS_ELECTRICAS_TABLA_ESPANYA)
        );
        $tabla->anyade_fila("filtro-tarifas-electricas-tabla", $filtro_tarifas_electricas, $params_fila);

        return ($tabla->dame_tabla());
    }


    function guarda_fecha_recalculo_datos_electricidad_Espanya($parametros)
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
            // Se recuperan los sensores de energía activa correspondientes a las tarifas o grupos de tarifas seleccionados
            $cadena_ids_tarifas_electricas_consulta = dame_cadena_ids_consulta($ids_tarifas);
            $cadena_ids_grupos_tarifas_electricas_consulta = dame_cadena_ids_consulta($ids_grupos_tarifas);
            $consulta_sensores_energia_activa = "
                SELECT
                    id,
                    nombre,
                    tipo,
                    granularidad_cuartohoraria
                FROM sensores
                WHERE
                    (red = '".$_SESSION["id_red"]."')
                    AND (clase = '".CLASE_SENSOR_ENERGIA_ACTIVA."')
                    AND ((SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_TARIFA_ELECTRICA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) IN (".$cadena_ids_tarifas_electricas_consulta."))
                        OR (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_GRUPO_TARIFAS_ELECTRICAS + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) IN (".$cadena_ids_grupos_tarifas_electricas_consulta.")))";
            $res_sensores_energia_activa = $bd_red->ejecuta_consulta($consulta_sensores_energia_activa);
            if ($res_sensores_energia_activa == false)
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensores_energia_activa."'");
            }
            $ids_sensores_energia_activa = array();
            $info_sensores_energia_activa = array();
            while ($fila_sensor_energia_activa = $res_sensores_energia_activa->dame_siguiente_fila())
            {
                array_push($ids_sensores_energia_activa, $fila_sensor_energia_activa["id"]);
                $info_sensor_energia_activa = array(
                    "nombre" => $fila_sensor_energia_activa["nombre"],
                    "tipo" => $fila_sensor_energia_activa["tipo"],
                    "granularidad_cuartohoraria" => $fila_sensor_energia_activa["granularidad_cuartohoraria"]);
                array_push($info_sensores_energia_activa, $info_sensor_energia_activa);
            }

            // Se recuperan los sensores de energía reactiva asociados a los sensores de energía activa
            $info_sensores_energia_reactiva = array();
            if (count($ids_sensores_energia_activa) > 0)
            {
                $cadena_ids_sensores_energia_activa_consulta = dame_cadena_ids_consulta($ids_sensores_energia_activa);
                $consulta_sensores_energia_reactiva = "
                    SELECT
                        nombre,
                        tipo,
                        granularidad_cuartohoraria
                    FROM sensores
                    WHERE
                        (clase = '".CLASE_SENSOR_ENERGIA_REACTIVA."')
                        AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_REACTIVA_ID_SENSOR_ENERGIA_ACTIVA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) IN (".$cadena_ids_sensores_energia_activa_consulta."))";
                $res_sensores_energia_reactiva = $bd_red->ejecuta_consulta($consulta_sensores_energia_reactiva);
                if ($res_sensores_energia_reactiva == false)
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta_sensores_energia_reactiva."'");
                }
                while ($fila_sensor_energia_reactiva = $res_sensores_energia_reactiva->dame_siguiente_fila())
                {
                    $info_sensor_energia_reactiva = array(
                        "nombre" => $fila_sensor_energia_reactiva["nombre"],
                        "tipo" => $fila_sensor_energia_reactiva["tipo"],
                        "granularidad_cuartohoraria" => $fila_sensor_energia_reactiva["granularidad_cuartohoraria"]);
                    array_push($info_sensores_energia_reactiva, $info_sensor_energia_reactiva);
                }
            }

            // Conversión de fechas
            $cadena_fecha_hora_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
            $cadena_fecha_hora_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

            // Se actualizan las horas de recálculos de energía activa
            if (count($info_sensores_energia_activa) > 0)
            {
                actualiza_hora_tablas_recalculos_valores_clase_sensores(
                    $cadena_fecha_hora_base_datos_utc,
                    CLASE_SENSOR_ENERGIA_ACTIVA,
                    $info_sensores_energia_activa);
            }

            // Se actualizan las horas de recálculos de energía reactiva
            if (count($info_sensores_energia_reactiva) > 0)
            {
                actualiza_hora_tablas_recalculos_valores_clase_sensores(
                    $cadena_fecha_hora_base_datos_utc,
                    CLASE_SENSOR_ENERGIA_REACTIVA,
                    $info_sensores_energia_reactiva);
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


    function asigna_tarifa_grupo_tarifas_sensores_electricidad_Espanya($parametros)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $id_tarifa = $parametros["id_tarifa"];
        $id_grupo_tarifas = $parametros["id_grupo_tarifas"];
        $ids_sensores = $parametros["ids_sensores"];

        // Se recuperan los sensores de energía activa correspondientes a las tarifas seleccionadas
        $cadena_ids_sensores_consulta = dame_cadena_ids_consulta($ids_sensores);
        $consulta_sensores = "
            SELECT
                id,
                nombre,
                parametros_clase,
                administrable
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
            $nombre_sensor = $fila_sensor["nombre"];
            $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor["parametros_clase"]);
            $administrable = $fila_sensor["administrable"];

            // Si el sensor es no administrable, no se puede modificar la tarifa eléctrica
            if ($administrable == VALOR_NO)
            {
                $mensaje_error = $idiomas->_("No se permite modificar el sensor")."\n(".
                    $nombre_sensor.")";
                $resultado = array(
                    "res" => "ERROR",
                    "msg" => $mensaje_error);
                return ($resultado);
            }

            // Si no se ha modificado la tarifa o el grupo de tarifas no se modifica el sensor
            $id_tarifa_sensor = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_TARIFA_ELECTRICA];
            $id_grupo_tarifas_sensor = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ID_GRUPO_TARIFAS_ELECTRICAS];
            if (($id_tarifa == $id_tarifa_sensor) && ($id_grupo_tarifas == $id_grupo_tarifas_sensor))
            {
                continue;
            }

            // Parámetros de clase con la nueva tarifa o grupo de tarifas
            $cadena_parametros_clase_modificados = implode(SEPARADOR_PARAMETROS_COMPUESTOS, array(
                $id_tarifa,
                $id_grupo_tarifas,
                $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_CUPS],
                $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_ENERGIA],
                $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_POTENCIA],
                $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_ERROR_MAXIMO_VALIDACION_FACTURAS_OTROS_CONCEPTOS_COSTE_TOTAL],
                $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_TIPO_FICHERO_VALIDACION_FACTURAS],
                $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_PREFIJO_FICHERO_VALIDACION_FACTURAS]));

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

        $resultado = array(
            "res" => "OK",
            "msg" => $idiomas->_("Tarifa o grupo de tarifas asignado correctamente"));
        return ($resultado);
    }


    function dame_fila_tabla_tarifa_electricidad_Espanya($parametros)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $id_tarifa = $parametros["id_tarifa"];

        $tabla_tarifas = dame_nombre_tabla_tarifas(MEDICION_ELECTRICIDAD);
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
        $tarifa = new TarifaElectrica_Espanya($fila_tarifa);
        $params_fila = array(
            "tipo" => TIPO_FILA_TABLA_DATOS_DETALLES,
            "opciones" => $tarifa->dame_opciones_tabla(),
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_TARIFAS_ELECTRICAS_ESPANYA,
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_TARIFAS_ELECTRICAS_ESPANYA));
        $info_tabla = $tarifa->dame_info_tabla(MEDICION_ELECTRICIDAD);
        $datos_tabla = $info_tabla["datos"];
        $fila = TablaDatos::dame_fila(
            $datos_tabla,
            $params_fila);

        $id_datos = "datosTarifaElectrica_Espanya__".$id_tarifa;
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
    function dame_datos_instalacion_sensor_electricidad_Espanya($id_sensor, $id_tarifa)
    {
        $idiomas = new Idiomas();

        // Se recupera el identificador de tarifa y el cups
        $fila_sensor = dame_fila_sensor($id_sensor);
        $cadena_parametros_clase = $fila_sensor["parametros_clase"];
        $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase);
        $cups = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_CUPS];

        // Datos de la instalación (CUPS)
        if ($cups == "")
        {
            $datos_instalacion["cups"] = $idiomas->_("ND");
        }
        else
        {
            $datos_instalacion["cups"] = $cups;
        }

        // Datos de la instalación (información de tarifa eléctrica)
        $recuperar_informacion_tarifa_electrica = ($id_tarifa != ID_NINGUNO);
        $datos_instalacion["hay_informacion_tarifa_electrica"] = $recuperar_informacion_tarifa_electrica;
        if ($datos_instalacion["hay_informacion_tarifa_electrica"] == true)
        {
            // Información de tarifa eléctrica
            $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa);
            $descripcion = $fila_tarifa_electrica["descripcion"];
            if ($descripcion == "")
            {
                $descripcion = $fila_tarifa_electrica["nombre"];
            }
            $tipo = $fila_tarifa_electrica["tipo"];
            $contrato = $fila_tarifa_electrica["contrato"];
            if ($contrato == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH)
            {
                $formula_precio_consumo = $fila_tarifa_electrica["formula_precio_consumo_pass_through"];
            }
            $tabla_tramos_tarifa_electrica = dame_tabla_tramos_tarifa_electricidad_Espanya($id_tarifa, $fila_tarifa_electrica);

            // Datos del apartado (información de tarifa eléctrica)
            $datos_instalacion["descripcion"] = $descripcion;
            $datos_instalacion["contrato"] = $contrato;
            $datos_instalacion["descripcion_tipo"] = TarifaElectrica_Espanya::dame_descripcion_tipo_tarifa_electrica($tipo);
            $datos_instalacion["descripcion_contrato"] = TarifaElectrica_Espanya::dame_descripcion_contrato_tarifa_electrica($contrato);
            if ($contrato == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_THROUGH)
            {
                $datos_instalacion["formula_precio_consumo"] = $formula_precio_consumo;
            }
            $datos_instalacion["tabla_tramos_tarifa_electrica"] = $tabla_tramos_tarifa_electrica;
        }
        return ($datos_instalacion);
    }


    //
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_instalacion_electricidad_Espanya(
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
                                    <td style='width:15%'><b>".$idiomas->_("Contrato").":"."</b></td>
                                    <td style='width:85%' id='".$prefijo_elemento."contrato-instalacion'></td>
                                </tr>
                                <tr>
                                    <td style='width:15%'><b id='".$prefijo_elemento."titulo-formula-precio-consumo-instalacion'>".$idiomas->_("Fórmula de precio de consumo").":"."</b></td>
                                    <td style='width:85%' id='".$prefijo_elemento."formula-precio-consumo-instalacion' class='elemento-oculto'></td>
                                </tr>
                                <tr>
                                    <td style='width:15%'><b>".$idiomas->_("Fecha de inicio").":"."</b></td>
                                    <td style='width:85%' id='".$prefijo_elemento."fecha-inicio-instalacion'></td>
                                </tr>
                                <tr>
                                    <td style='width:15%'><b>".$idiomas->_("Fecha de fin").":"."</b></td>
                                    <td style='width:85%' id='".$prefijo_elemento."fecha-fin-instalacion'></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class='tabla-datos100' id='".$prefijo_elemento."contenedor-tabla-tramos-tarifa-electrica-instalacion'></div>
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
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Contrato").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='".$prefijo_elemento."contrato-instalacion'></td>
                            </tr>
                            <tr>
                                <td class='titulo-parametro-informe-fichero' id='".$prefijo_elemento."titulo-formula-precio-consumo-instalacion'><b>".$idiomas->_("Fórmula de precio de consumo").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='".$prefijo_elemento."formula-precio-consumo-instalacion'></td>
                            </tr>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='".$prefijo_elemento."fecha-inicio-instalacion'></td>
                            </tr>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='".$prefijo_elemento."fecha-fin-instalacion'></td>
                            </tr>
                        </table>
                        <br/>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='".$prefijo_elemento."contenedor-tabla-tramos-tarifa-electrica-instalacion'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_instalacion_electricidad_Espanya(
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

        $id_sensor = $parametros_tipo_elemento["id_sensor"];
        $id_tarifa = dame_id_tarifa_id_sensor_fecha($id_sensor, $parametros_informe["fecha_hora_inicio"]);
        $datos_elemento = dame_datos_instalacion_sensor_electricidad_Espanya($id_sensor, $id_tarifa);
        return ($datos_elemento);
    }


    //
    // Funciones de obtención de información de tarifas
    //


    // Devuelve las filas de los tramos de la tarifa
    function dame_filas_tramos_tarifa_electricidad_Espanya($id_tarifa)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_tramos = "
            SELECT *
            FROM ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                tarifa_electrica = '".$bd_red->_($id_tarifa)."'
            ORDER BY
                id ASC";
        $res_tramos = $bd_red->ejecuta_consulta($consulta_tramos);
        if ($res_tramos == false)
        {
            throw new Exception("Ha ocurrido un error en la consulta: '".$consulta_tramos."'");
        }
        $filas_tramos = array();
        while ($fila_tramo = $res_tramos->dame_siguiente_fila())
        {
            array_push($filas_tramos, $fila_tramo);
        }
        return ($filas_tramos);
    }


    // Devuelve la fila de periodo de cálculo de costes pass-pool de la tarifa
    function dame_fila_periodo_calculo_costes_pass_pool_tarifa_electricidad_Espanya($id_periodo_calculo_costes)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_periodo_calculo_costes = "
            SELECT *
            FROM ".TABLA_PERIODOS_CALCULO_COSTES_PASS_POOL_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                id = '".$bd_red->_($id_periodo_calculo_costes)."'";
        $res_periodo_calculo_costes = $bd_red->ejecuta_consulta($consulta_periodo_calculo_costes);
        if (($res_periodo_calculo_costes == false) || ($res_periodo_calculo_costes->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_periodo_calculo_costes."'");
        }
        $fila_periodo_calculo_costes = $res_periodo_calculo_costes->dame_siguiente_fila();
        return ($fila_periodo_calculo_costes);
    }


    // Devuelve la fila de concepto de coste pass-through de la tarifa
    function dame_fila_concepto_coste_pass_through_tarifa_electricidad_Espanya($id_concepto_coste)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_concepto_coste = "
            SELECT *
            FROM ".TABLA_CONCEPTOS_COSTE_PASS_THROUGH_TARIFAS_ELECTRICAS_ESPANYA."
            WHERE
                id = '".$bd_red->_($id_concepto_coste)."'";
        $res_concepto_coste = $bd_red->ejecuta_consulta($consulta_concepto_coste);
        if (($res_concepto_coste == false) || ($res_concepto_coste->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_concepto_coste."'");
        }
        $fila_concepto_coste = $res_concepto_coste->dame_siguiente_fila();
        return ($fila_concepto_coste);
    }


    //
    // Funciones de acciones de usuario
    //


    function anyade_parametros_tramos_accion_usuario_tarifa_electricidad_Espanya(
        $fila,
        $filas_tramos,
        &$parametros_accion_usuario)
    {
        // Información de tramos
        $precios_consumo_tramos = array();
        $coeficientes_a_precio_consumo_pass_pool_tramos = array();
        $coeficientes_b_precio_consumo_pass_pool_tramos = array();
        $precios_consumo_tarifa_acceso_tramos = array();
        $precios_potencia_tramos = array();
        $potencias_tramos = array();
        foreach ($filas_tramos as $fila_tramo)
        {
            $numero_tramo = $fila_tramo["tramo"];
            $precios_consumo_tramos[$numero_tramo] = $fila_tramo["precio_consumo"];
            $coeficientes_a_precio_consumo_pass_pool_tramos[$numero_tramo] = $fila_tramo["coeficiente_a_precio_consumo_pass_pool"];
            $coeficientes_b_precio_consumo_pass_pool_tramos[$numero_tramo] = $fila_tramo["coeficiente_b_precio_consumo_pass_pool"];
            $precios_consumo_tarifa_acceso_tramos[$numero_tramo] = $fila_tramo["precio_consumo_tarifa_acceso"];
            $precios_potencia_tramos[$numero_tramo] = $fila_tramo["precio_potencia"];
            $potencias_tramos[$numero_tramo] = $fila_tramo["potencia"];
        }

        // Parámetros de tramos de la acción
        switch ($fila["contrato"])
        {
            case CONTRATO_TARIFA_ELECTRICA_ESPANYA_FIJO:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TRAMOS_TARIFA_ELECTRICA] = $precios_consumo_tramos;
                break;
            }
            case CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_COEFICIENTES_A_PRECIO_CONSUMO_PASS_POOL_TRAMOS_TARIFA_ELECTRICA] = $coeficientes_a_precio_consumo_pass_pool_tramos;
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_COEFICIENTES_B_PRECIO_CONSUMO_PASS_POOL_TRAMOS_TARIFA_ELECTRICA] = $coeficientes_b_precio_consumo_pass_pool_tramos;
                break;
            }
        }
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TARIFA_ACCESO_TRAMOS_TARIFA_ELECTRICA] = $precios_consumo_tarifa_acceso_tramos;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIOS_POTENCIAS_TRAMOS_TARIFA_ELECTRICA] = $precios_potencia_tramos;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_POTENCIAS_TRAMOS_TARIFA_ELECTRICA] = $potencias_tramos;
    }


    function anyade_parametros_tramos_accion_usuario_modificacion_tarifa_electricidad_Espanya(
        $fila_actual,
        $fila_anterior,
        $filas_tramos_actuales,
        $filas_tramos_anteriores,
        &$parametros_accion_usuario,
        &$parametros_accion_usuario_anteriores)
    {
        // Información de tramos
        // - Si el tipo no ha cambiado, sólo se añade la información que ha cambiado de los tramos
        // - Si el tipo ha cambiado, se añade toda la información de los tramos actuales y anteriores
        if ($fila_actual["tipo"] == $fila_anterior["tipo"])
        {
            $precios_consumo_tramos_modificados = false;
            $coeficientes_a_precio_consumo_pass_pool_tramos_modificados = false;
            $coeficientes_b_precio_consumo_pass_pool_tramos_modificados = false;
            $precios_consumo_tarifa_acceso_tramos_modificados = false;
            $precios_potencia_tramos_modificados = false;
            $potencias_tramos_modificadas = false;
            $precios_consumo_tramos = array();
            $coeficientes_a_precio_consumo_pass_pool_tramos = array();
            $coeficientes_b_precio_consumo_pass_pool_tramos = array();
            $precios_consumo_tarifa_acceso_tramos = array();
            $precios_potencia_tramos = array();
            $potencias_tramos = array();
            $precios_consumo_tramos_anteriores = array();
            $coeficientes_a_precio_consumo_pass_pool_tramos_anteriores = array();
            $coeficientes_b_precio_consumo_pass_pool_tramos_anteriores = array();
            $precios_consumo_tarifa_acceso_tramos_anteriores = array();
            $precios_potencia_tramos_anteriores = array();
            $potencias_tramos_anteriores = array();
            for ($i = 0; $i < count($filas_tramos_actuales); $i++)
            {
                $fila_tramo_actual = $filas_tramos_actuales[$i];
                $fila_tramo_anterior = $filas_tramos_anteriores[$i];
                $numero_tramo = $fila_tramo_actual["tramo"];
                if ($fila_tramo_actual["precio_consumo"] != $fila_tramo_anterior["precio_consumo"])
                {
                    $precios_consumo_tramos[$numero_tramo] = $fila_tramo_actual["precio_consumo"];
                    $precios_consumo_tramos_anteriores[$numero_tramo] = $fila_tramo_anterior["precio_consumo"];
                    $precios_consumo_tramos_modificados = true;
                }
                if ($fila_tramo_actual["coeficiente_a_precio_consumo_pass_pool"] != $fila_tramo_anterior["coeficiente_a_precio_consumo_pass_pool"])
                {
                    $coeficientes_a_precio_consumo_pass_pool_tramos[$numero_tramo] = $fila_tramo_actual["coeficiente_a_precio_consumo_pass_pool"];
                    $coeficientes_a_precio_consumo_pass_pool_tramos_anteriores[$numero_tramo] = $fila_tramo_anterior["coeficiente_a_precio_consumo_pass_pool"];
                    $coeficientes_a_precio_consumo_pass_pool_tramos_modificados = true;
                }
                if ($fila_tramo_actual["coeficiente_b_precio_consumo_pass_pool"] != $fila_tramo_anterior["coeficiente_b_precio_consumo_pass_pool"])
                {
                    $coeficientes_b_precio_consumo_pass_pool_tramos[$numero_tramo] = $fila_tramo_actual["coeficiente_b_precio_consumo_pass_pool"];
                    $coeficientes_b_precio_consumo_pass_pool_tramos_anteriores[$numero_tramo] = $fila_tramo_anterior["coeficiente_b_precio_consumo_pass_pool"];
                    $coeficientes_b_precio_consumo_pass_pool_tramos_modificados = true;
                }
                if ($fila_tramo_actual["precio_consumo_tarifa_acceso"] != $fila_tramo_anterior["precio_consumo_tarifa_acceso"])
                {
                    $precios_consumo_tarifa_acceso_tramos[$numero_tramo] = $fila_tramo_actual["precio_consumo_tarifa_acceso"];
                    $precios_consumo_tarifa_acceso_tramos_anteriores[$numero_tramo] = $fila_tramo_anterior["precio_consumo_tarifa_acceso"];
                    $precios_consumo_tarifa_acceso_tramos_modificados = true;
                }
                if ($fila_tramo_actual["precio_potencia"] != $fila_tramo_anterior["precio_potencia"])
                {
                    $precios_potencia_tramos[$numero_tramo] = $fila_tramo_actual["precio_potencia"];
                    $precios_potencia_tramos_anteriores[$numero_tramo] = $fila_tramo_anterior["precio_potencia"];
                    $precios_potencia_tramos_modificados = true;
                }
                if ($fila_tramo_actual["potencia"] != $fila_tramo_anterior["potencia"])
                {
                    $potencias_tramos[$numero_tramo] = $fila_tramo_actual["potencia"];
                    $potencias_tramos_anteriores[$numero_tramo] = $fila_tramo_anterior["potencia"];
                    $potencias_tramos_modificadas = true;
                }
            }

            // Parámetros de tramos de la acción
            if ($precios_consumo_tramos_modificados == true)
            {
                if ($fila_actual["contrato"] == CONTRATO_TARIFA_ELECTRICA_ESPANYA_FIJO)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TRAMOS_TARIFA_ELECTRICA] = $precios_consumo_tramos;
                }
                if ($fila_anterior["contrato"] == CONTRATO_TARIFA_ELECTRICA_ESPANYA_FIJO)
                {
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TRAMOS_TARIFA_ELECTRICA] = $precios_consumo_tramos_anteriores;
                }
            }
            if ($coeficientes_a_precio_consumo_pass_pool_tramos_modificados == true)
            {
                if ($fila_actual["contrato"] == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_COEFICIENTES_A_PRECIO_CONSUMO_PASS_POOL_TRAMOS_TARIFA_ELECTRICA] = $coeficientes_a_precio_consumo_pass_pool_tramos;
                }
                if ($fila_anterior["contrato"] == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL)
                {
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_COEFICIENTE_A_PRECIO_CONSUMO_PASS_POOL] = $coeficientes_a_precio_consumo_pass_pool_tramos_anteriores;
                }
            }
            if ($coeficientes_b_precio_consumo_pass_pool_tramos_modificados == true)
            {
                if ($fila_actual["contrato"] == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_COEFICIENTES_B_PRECIO_CONSUMO_PASS_POOL_TRAMOS_TARIFA_ELECTRICA] = $coeficientes_b_precio_consumo_pass_pool_tramos;
                }
                if ($fila_anterior["contrato"] == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL)
                {
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_COEFICIENTE_B_PRECIO_CONSUMO_PASS_POOL] = $coeficientes_b_precio_consumo_pass_pool_tramos_anteriores;
                }
            }
            if ($precios_consumo_tarifa_acceso_tramos_modificados == true)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TARIFA_ACCESO_TRAMOS_TARIFA_ELECTRICA] = $precios_consumo_tarifa_acceso_tramos;
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TARIFA_ACCESO_TRAMOS_TARIFA_ELECTRICA] = $precios_consumo_tarifa_acceso_tramos_anteriores;
            }
            if ($precios_potencia_tramos_modificados == true)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIOS_POTENCIAS_TRAMOS_TARIFA_ELECTRICA] = $precios_potencia_tramos;
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIOS_POTENCIAS_TRAMOS_TARIFA_ELECTRICA] = $precios_potencia_tramos_anteriores;
            }
            if ($potencias_tramos_modificadas == true)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_POTENCIAS_TRAMOS_TARIFA_ELECTRICA] = $potencias_tramos;
                $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_POTENCIAS_TRAMOS_TARIFA_ELECTRICA] = $potencias_tramos_anteriores;
            }
        }
        else
        {
            anyade_parametros_tramos_accion_usuario_tarifa_electricidad_Espanya(
                $fila_actual,
                $filas_tramos_actuales,
                $parametros_accion_usuario);
            anyade_parametros_tramos_accion_usuario_tarifa_electricidad_Espanya(
                $fila_anterior,
                $filas_tramos_anteriores,
                $parametros_accion_usuario_anteriores);
        }
    }


    function anyade_parametros_tramos_accion_usuario_modificacion_tarifas_electricidad_Espanya(
        $tipo,
        $contrato,
        $info_campos_tramos_tarifas_electricas,
        &$parametros_accion_usuario)
    {
        // Si son todos los tipos, no se modifican los tramos
        if ($tipo == TIPO_TARIFA_TODOS)
        {
            return;
        }

        // Información de tramos
        $precios_consumo_tramos_modificados = false;
        $coeficientes_a_precio_consumo_pass_pool_tramos_modificados = false;
        $coeficientes_b_precio_consumo_pass_pool_tramos_modificados = false;
        $precios_consumo_tarifa_acceso_tramos_modificados = false;
        $precios_potencia_tramos_modificados = false;
        $potencias_tramos_modificadas = false;
        $precios_consumo_tramos = array();
        $coeficientes_a_precio_consumo_pass_pool_tramos = array();
        $coeficientes_b_precio_consumo_pass_pool_tramos = array();
        $precios_consumo_tarifa_acceso_tramos = array();
        $precios_potencia_tramos = array();
        $potencias_tramos = array();
        foreach ($info_campos_tramos_tarifas_electricas as $numero_tramo => $info_campos_tramo_tarifas_electricas)
        {
            $info_campos_tramo_modificados_tarifas_electricas = dame_info_campos_modificados($info_campos_tramo_tarifas_electricas);
            foreach ($info_campos_tramo_modificados_tarifas_electricas as $info_campo_tramo_modificado_tarifas_electricas)
            {
                $nombre_campo_tramo_modificado = $info_campo_tramo_modificado_tarifas_electricas["nombre"];
                $valor_campo_tramo_modificado = $info_campo_tramo_modificado_tarifas_electricas["valor"];
                switch ($nombre_campo_tramo_modificado)
                {
                    case "precio_consumo":
                    {
                        $precios_consumo_tramos[$numero_tramo] = $valor_campo_tramo_modificado;
                        $precios_consumo_tramos_modificados = true;
                        break;
                    }
                    case "coeficiente_a_precio_consumo_pass_pool":
                    {
                        $coeficientes_a_precio_consumo_pass_pool_tramos[$numero_tramo] = $valor_campo_tramo_modificado;
                        $coeficientes_a_precio_consumo_pass_pool_tramos_modificados = true;
                        break;
                    }
                    case "coeficiente_b_precio_consumo_pass_pool":
                    {
                        $coeficientes_b_precio_consumo_pass_pool_tramos[$numero_tramo] = $valor_campo_tramo_modificado;
                        $coeficientes_b_precio_consumo_pass_pool_tramos_modificados = true;
                        break;
                    }
                    case "precio_consumo_tarifa_acceso":
                    {
                        $precios_consumo_tarifa_acceso_tramos[$numero_tramo] = $valor_campo_tramo_modificado;
                        $precios_consumo_tarifa_acceso_tramos_modificados = true;
                        break;
                    }
                    case "precio_potencia":
                    {
                        $precios_potencia_tramos[$numero_tramo] = $valor_campo_tramo_modificado;
                        $precios_potencia_tramos_modificados = true;
                        break;
                    }
                    case "potencia":
                    {
                        $potencias_tramos[$numero_tramo] = $valor_campo_tramo_modificado;
                        $potencias_tramos_modificadas = true;
                        break;
                    }
                }
            }

            // Parámetros de tramos de la acción
            if ($precios_consumo_tramos_modificados == true)
            {
                if ($contrato == CONTRATO_TARIFA_ELECTRICA_ESPANYA_FIJO)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TRAMOS_TARIFA_ELECTRICA] = $precios_consumo_tramos;
                }
            }
            if ($coeficientes_a_precio_consumo_pass_pool_tramos_modificados == true)
            {
                if ($contrato == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_COEFICIENTES_A_PRECIO_CONSUMO_PASS_POOL_TRAMOS_TARIFA_ELECTRICA] = $coeficientes_a_precio_consumo_pass_pool_tramos;
                }
            }
            if ($coeficientes_b_precio_consumo_pass_pool_tramos_modificados == true)
            {
                if ($contrato == CONTRATO_TARIFA_ELECTRICA_ESPANYA_PASS_POOL)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_COEFICIENTES_B_PRECIO_CONSUMO_PASS_POOL_TRAMOS_TARIFA_ELECTRICA] = $coeficientes_b_precio_consumo_pass_pool_tramos;
                }
            }
            if ($precios_consumo_tarifa_acceso_tramos_modificados == true)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TARIFA_ACCESO_TRAMOS_TARIFA_ELECTRICA] = $precios_consumo_tarifa_acceso_tramos;
            }
            if ($precios_potencia_tramos_modificados == true)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIOS_POTENCIAS_TRAMOS_TARIFA_ELECTRICA] = $precios_potencia_tramos;
            }
            if ($potencias_tramos_modificadas == true)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_POTENCIAS_TRAMOS_TARIFA_ELECTRICA] = $potencias_tramos;
            }
        }

    }
    
    function anyade_concepto_coste($id_tarifa_electrica, $nombre, $formula_precio_consumo)
    {
        // Comprobaciones antes de añadir el concepto de coste:
        // - Se valida la fórmula de cálculo de precio de consumo
        $anyadir_concepto_coste = true;

        // Si los datos son correctos se evalua la función de cálculo de precio de consumo
        if ($anyadir_concepto_coste == true)
        {
            // Parámetros de la función a llamar
            $parametros_funcion_externa =
                array(
                    "llamante" => "web_emios",
                    "nombre" => NOMBRE_FUNCION_EVALUA_FORMULA_PRECIO_CONSUMO_PASS_THROUGH_ESPANYA,
                    "formula_precio_consumo_pass_through" => $formula_precio_consumo
                );

            // Llamada a función 'externa'
            $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
            $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

            // Si la fórmula de valores es incorrecta se devuelve un error
            if ($resultado_funcion_externa["formula_correcta"] == 0)
            {
                $anyadir_concepto_coste = False;

                $error = $resultado_funcion_externa["error"];
                $descripcion_error = dame_descripcion_error_funcion_variables($error);

                return "ERROR";
            }
        }

        // Se añade el concepto de coste de la tarifa eléctrica
        if ($anyadir_concepto_coste == true)
        {
            $bd_red = BaseDatosRed::dame_base_datos();
            // Se añade el concepto de coste de la tarifa eléctrica
            $operacion_insercion = "
                INSERT INTO ".TABLA_CONCEPTOS_COSTE_PASS_THROUGH_TARIFAS_ELECTRICAS_ESPANYA." (
                    nombre,
                    red,
                    tarifa_electrica,
                    formula_precio_consumo
                ) VALUES (
                    '".$bd_red->_($nombre)."',
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_tarifa_electrica)."',
                    '".$bd_red->_($formula_precio_consumo)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recuperan el id y la fila del concepto de coste añadido
                $id_concepto_coste = $bd_red->dame_id_autoincremental_ultima_insercion();
                $fila_concepto_coste = dame_fila_concepto_coste_pass_through_tarifa_electricidad_Espanya($id_concepto_coste);

                // Se añade la acción de usuario
                anyade_accion_usuario_anyadir_concepto_coste_pass_through_tarifa_electricidad_Espanya($fila_concepto_coste);
                
                return "OK";
            }


        }
    }
        
        
    // Añade la acción de usuario de adición del concepto de coste de una tarifa eléctrica
    function anyade_accion_usuario_anyadir_concepto_coste_pass_through_tarifa_electricidad_Espanya($fila)
    {
        // Nombre de tarifa eléctrica
        $nombre_tarifa_electrica = dame_nombre_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $fila["tarifa_electrica"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_CONCEPTO_COSTE_PASS_THROUGH_TARIFA_ELECTRICIDAD_ESPANYA;
        $objeto_accion_usuario = $fila["nombre"]." (".$nombre_tarifa_electrica.")";

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE] = $fila["nombre"];
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_FORMULA_PRECIO_CONSUMO_PASS_THROUGH] = $fila["formula_precio_consumo"];

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }

    function obtener_dias_anyo($anyo){
        if ((($anyo % 4 == 0) && ($anyo % 100 != 0)) || ($anyo % 400 == 0)) {
            // Año bisiesto
            return 366;
        } else {
            // Año no bisiesto
            return 365;
        }
    }

?>
