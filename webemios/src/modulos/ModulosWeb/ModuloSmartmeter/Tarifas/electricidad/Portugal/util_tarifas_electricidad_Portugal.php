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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/TarifaElectrica_Portugal.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/Tarifa.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');



    // TABLA FILTRO DE TARIFAS
    // Función que genera la tabla de filtros de tarifas que se muestra justo antes de la tabla de tarifas.
    function dame_tabla_filtro_tarifas_tabla_electricidad_Portugal()
    {
        $idiomas = new Idiomas();

        // Se recuperan los controles a mostrar
        $controles = array();
        $id_controles = "smartmeter_filtro_tarifas_tabla_electricidad_Portugal";

        // Tipos de tarifa eléctrica
        $control_lista_tipos .= "<div id='etiqueta_tipo_tarifa_electrica_".$id_controles."'>".$idiomas->_("Tipo").": "."</div>";
        $control_lista_tipos .= "<select id='tipo_tarifa_electrica_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_tipos .= dame_lista_tipos_tarifa_electricidad_Portugal(TIPO_TARIFA_TODOS, OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_TODOS);
        $control_lista_tipos .= "</select>";
        array_push($controles, $control_lista_tipos);

        // Ciclos de las tarifas (cómo se configuran los periodos tarifarios)
        $control_lista_ciclos .= "<div id='etiqueta_ciclo_tarifa_electrica_".$id_controles."'>".$idiomas->_("Ciclo").": "."</div>";
        $control_lista_ciclos .= "<select id='ciclo_tarifa_electrica_".$id_controles."' class='filtro-desplegable'>";
        $control_lista_ciclos .= dame_lista_ciclos_tarifa_electricidad_Portugal(CICLO_TARIFA_ELECTRICA_PORTUGAL_TODOS, OPCIONES_EXTRA_LISTA_CICLOS_TARIFA_ELECTRICA_TODOS);
        $control_lista_ciclos .= "</select>";
        array_push($controles, $control_lista_ciclos);

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



    // Devuelve la lista de tipos de tarifa eléctrica
    function dame_lista_tipos_tarifa_electricidad_Portugal($tipo_seleccionado, $opciones_extra)
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
        $tipos_tarifa = TarifaElectrica_Portugal::dame_tipos_tarifa_electrica();
        foreach ($tipos_tarifa as $tipo_tarifa)
        {
            $nombre_tipo_tarifa = TarifaElectrica_Portugal::dame_descripcion_tipo_tarifa_electrica($tipo_tarifa);
            $lista .= "<option value='".$tipo_tarifa."'";
			if ($tipo_tarifa == $tipo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_tipo_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }


    // Devuelve la lista de ciclos de tarifa eléctrica
    // Los ciclos determinan cómo se distribuyen los tramos horarios.
    function dame_lista_ciclos_tarifa_electricidad_Portugal($ciclo_seleccionado, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $lista = "";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_CICLOS_TARIFA_ELECTRICA_TODOS)
        {
            $lista .= "<option value='".CICLO_TARIFA_ELECTRICA_PORTUGAL_TODOS."'>".$idiomas->_("Todos")."</option>";
        }
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_CICLOS_TARIFA_ELECTRICA_NINGUNO)
        {
            $lista .= "<option value='".CICLO_TARIFA_ELECTRICA_PORTUGAL_NINGUNO."'>".$idiomas->_("Ninguno")."</option>";
        }
        $ciclos_tarifa = TarifaElectrica_Portugal::dame_ciclos_tarifa_electrica();
        foreach ($ciclos_tarifa as $ciclo_tarifa)
        {
            $nombre_ciclo_tarifa = TarifaElectrica_Portugal::dame_descripcion_ciclo_tarifa_electrica($ciclo_tarifa);
            $lista .= "<option value='".$ciclo_tarifa."'";
			if ($ciclo_tarifa == $ciclo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_ciclo_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);

    }

    // Devuelve la lista de ciclos de tarifa eléctrica
    // Los ciclos determinan cómo se distribuyen los tramos horarios.
    function dame_lista_regiones_Portugal($ciclo_seleccionado, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $lista = "";
        if ($opciones_extra == REGIONES_PORTUGAL_TODOS)
        {
            $lista .= "<option value='".REGIONES_PORTUGAL_TODOS."'>".$idiomas->_("Todas")."</option>";
        }
        if ($opciones_extra == REGIONES_PORTUGAL_NINGUNO)
        {
            $lista .= "<option value='".REGIONES_PORTUGAL_NINGUNO."'>".$idiomas->_("Ninguna")."</option>";
        }
        $ciclos_tarifa = TarifaElectrica_Portugal::dame_regiones_Portugal();
        foreach ($ciclos_tarifa as $ciclo_tarifa)
        {
            $nombre_ciclo_tarifa = TarifaElectrica_Portugal::dame_descripcion_regiones_Portugal($ciclo_tarifa);
            $lista .= "<option value='".$ciclo_tarifa."'";
			if ($ciclo_tarifa == $ciclo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_ciclo_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);

    }


    // Devuelve la lista de tarifas eléctricas
    function dame_lista_tarifas_electricidad_Portugal($ids_tarifas_seleccionadas, $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();
            $consulta_tarifas_electricas = "
            SELECT
                id,
                nombre,
                tipo
            FROM ".TABLA_TARIFAS_ELECTRICAS_PORTUGAL."
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
            $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Portugal::dame_caracteristicas_tipo_tarifa_electrica($fila_tarifa_electrica['tipo']);

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


    // Devuelve la tabla de tramos de una tarifa eléctrica
    function dame_tabla_tramos_tarifa_electricidad_Portugal($id_tarifa, $fila_tarifa_electrica, $incluir_salto_linea = true)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        // Fila de tarifa eléctrica
        if ($fila_tarifa_electrica === NULL)
        {
            $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_PORTUGAL, $id_tarifa);
        }

        // Tipo y contrato de tarifa eléctrica
        $tipo_tarifa_electrica = $fila_tarifa_electrica["tipo"];
        $contrato_tarifa_electrica = $fila_tarifa_electrica["contrato"];

        $cabecera_tabla = array(
            $idiomas->_("Tramo"),
            $idiomas->_("Precio de consumo"),
            $idiomas->_("Precio de consumo de tarifa de acceso"),
        );
        $params_tabla = array(
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_FIJO_PORTUGAL,
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_TRAMOS_TARIFA_ELECTRICA_FIJO_PORTUGAL),
            "generar_valores_xml" => true
        );

        $titulo_tabla_tramos_tarifa_electrica = $idiomas->_("Tramos de tarifa eléctrica");
        $tabla = new TablaDatos(
            "tabla-tramos-tarifa-electrica",
            $titulo_tabla_tramos_tarifa_electrica,
            TIPO_TABLA_DATOS_LISTA,
            $params_tabla
        );
        $tabla->anyade_cabecera("", $cabecera_tabla);

        // Tramos con potencias iguales
        $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Portugal::dame_caracteristicas_tipo_tarifa_electrica($tipo_tarifa_electrica);
        $tramos_potencias_iguales = $caracteristicas_tipo_tarifa_electrica["tramos_potencias_iguales"];

        // Se recupera la información de los tramos y se añade a la tabla
        $consulta_tramos_tarifa_electrica = "
            SELECT *
            FROM ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_PORTUGAL."
            WHERE
               tarifa_electrica = '".$bd_red->_($id_tarifa)."'";
        $res_tramos_tarifa_electrica = $bd_red->ejecuta_consulta($consulta_tramos_tarifa_electrica);
        if ($res_tramos_tarifa_electrica == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_tramos_tarifa_electrica."'");
        }
        while ($fila_tramos_tarifa_electrica = $res_tramos_tarifa_electrica->dame_siguiente_fila())
        {
						// Cadenas de los datos del tramo
						$cadena_tramo_ponta = "Tramo Ponta";
						$cadena_tramo_cheia = "Tramo Cheia";
						$cadena_tramo_vazio_normal = "Tramo Vazio Normal";
						$cadena_tramo_super_vazio = "Tramo Super Vazio";

						$precio_consumo_ponta = $fila_tramos_tarifa_electrica["precio_consumo_ponta"];
						$precio_consumo_cheia = $fila_tramos_tarifa_electrica["precio_consumo_cheia"];
						$precio_consumo_vazio_normal = $fila_tramos_tarifa_electrica["precio_consumo_vazio"];
						$precio_consumo_super_vazio = $fila_tramos_tarifa_electrica["precio_consumo_super_vazio"];
						$precio_acceso_ponta = $fila_tramos_tarifa_electrica["precio_consumo_tarifa_acceso_ponta"];
						$precio_acceso_cheia = $fila_tramos_tarifa_electrica["precio_consumo_tarifa_acceso_cheia"];
						$precio_acceso_vazio_normal = $fila_tramos_tarifa_electrica["precio_consumo_tarifa_acceso_vazio"];
						$precio_acceso_super_vazio = $fila_tramos_tarifa_electrica["precio_consumo_tarifa_acceso_super_vazio"];

            $cadena_precio_consumo_ponta = formatea_numero($precio_consumo_ponta, 6)." ".$idiomas->_("€")."/".$idiomas->_("kWh");
						$cadena_precio_consumo_cheia = formatea_numero($precio_consumo_cheia, 6)." ".$idiomas->_("€")."/".$idiomas->_("kWh");
						$cadena_precio_consumo_vazio_normal = formatea_numero($precio_consumo_vazio_normal, 6)." ".$idiomas->_("€")."/".$idiomas->_("kWh");
						$cadena_precio_consumo_super_vazio = formatea_numero($precio_consumo_super_vazio, 6)." ".$idiomas->_("€")."/".$idiomas->_("kWh");

						$cadena_precio_acceso_ponta = formatea_numero($precio_acceso_ponta, 6)." ".$idiomas->_("€")."/".$idiomas->_("kWh");
						$cadena_precio_acceso_cheia = formatea_numero($precio_acceso_cheia, 6)." ".$idiomas->_("€")."/".$idiomas->_("kWh");
						$cadena_precio_acceso_vazio_normal = formatea_numero($precio_acceso_vazio_normal, 6)." ".$idiomas->_("€")."/".$idiomas->_("kWh");
						$cadena_precio_acceso_super_vazio = formatea_numero($precio_acceso_super_vazio, 6)." ".$idiomas->_("€")."/".$idiomas->_("kWh");

						$fila_tramo_ponta = array(
                $cadena_tramo_ponta,
                $cadena_precio_consumo_ponta,
                $cadena_precio_acceso_ponta);
            $tabla->anyade_fila("", $fila_tramo_ponta);

						$fila_tramo_cheia = array(
                $cadena_tramo_cheia,
                $cadena_precio_consumo_cheia,
                $cadena_precio_acceso_cheia);
            $tabla->anyade_fila("", $fila_tramo_cheia);

						$fila_tramo_vazio_normal = array(
                $cadena_tramo_vazio_normal,
                $cadena_precio_consumo_vazio_normal,
                $cadena_precio_acceso_vazio_normal);
            $tabla->anyade_fila("", $fila_tramo_vazio_normal);

						$fila_tramo_super_vazio = array(
                $cadena_tramo_super_vazio,
                $cadena_precio_consumo_super_vazio,
                $cadena_precio_acceso_super_vazio);
            $tabla->anyade_fila("", $fila_tramo_super_vazio);

						$info = $tabla->dame_tabla($incluir_salto_linea);

						// Parametros potencia
						$precio_potencia_contratada = $fila_tramos_tarifa_electrica["precio_potencia_contratada"];
						$potencia_contratada = $fila_tramos_tarifa_electrica["potencia_contratada"];
						$precio_potencia_ponta = $fila_tramos_tarifa_electrica["precio_potencia_ponta"];

						$cadena_potencia_contratada = formatea_numero($potencia_contratada, 5)." ".$idiomas->_("kW");
						$cadena_precio_potencia_contratada = formatea_numero($precio_potencia_contratada, 6)." ".$idiomas->_("€")."/".$idiomas->_("kW")."-".$idiomas->_("día");
						$cadena_precio_potencia_ponta= formatea_numero($precio_potencia_ponta, 6)." ".$idiomas->_("€")."/".$idiomas->_("kW")."-".$idiomas->_("día");

						$info .= "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Potencia").": ";
            $info .= "<ul>";
						$info .= "<li>";
						$descripcion_potencia_contratada = $idiomas->_("Potencia contratada");
						$info .= $descripcion_potencia_contratada.": ".$cadena_potencia_contratada."<br/>";
						$info .= "</li>";
						$info .= "<li>";
						$descripcion_precio_potencia_contratada = $idiomas->_("Precio potencia contratada");
						$info .= $descripcion_precio_potencia_contratada.": ".$cadena_precio_potencia_contratada."<br/>";
						$info .= "</li>";
						$info .= "<li>";
						$descripcion_precio_potencia_ponta = $idiomas->_("Precio potencia ponta");
						$info .= $descripcion_precio_potencia_ponta.": ".$cadena_precio_potencia_ponta."<br/>";
						$info .= "</li>";
						$info .= "</ul>";

						$info .= "<i class='icon-info-sign color-azul'></i> ".
                $idiomas->_("Energía Reactiva").": ";
            $info .= "<ul>";
						// Parametros reactiva
						$precio_reactiva_inductiva = $fila_tramos_tarifa_electrica["precio_inductiva"];
						$precio_reactiva_capacitiva = $fila_tramos_tarifa_electrica["precio_capacitiva"];
						$cadena_precio_reactiva_inductiva= formatea_numero($precio_reactiva_inductiva, 6)." ".$idiomas->_("€")."/".$idiomas->_("kVArh");
						$cadena_precio_reactiva_capacitiva= formatea_numero($precio_reactiva_capacitiva, 6)." ".$idiomas->_("€")."/".$idiomas->_("kVArh");

						$info .= "<li>";
						$descripcion_precio_inductiva = $idiomas->_("Precio energia reactiva inductiva");
						$info .= $descripcion_precio_inductiva.": ".$cadena_precio_reactiva_inductiva."<br/>";
						$info .= "</li>";
						$info .= "<li>";
						$descripcion_precio_capacitiva = $idiomas->_("Precio energia reactiva capacitiva");
						$info .= $descripcion_precio_capacitiva.": ".$cadena_precio_reactiva_capacitiva."<br/>";
						$info .= "</li>";

				}

        return ($info);
    }



     // Devuelve el contenido de las pestañas de la ventana de administración de tarifas eléctricas
     function dame_contenido_pestanyas_ventana_administracion_tarifas_electricidad_Portugal(
        $tipo_administracion,
        $id_tarifa,
        $nombre,
        $descripcion,
        $tipo,
        $ciclo,
        $region,
        $id_grupo,
        $expiracion,
        $cadena_fecha_expiracion_local_local ,
        $numero_dias_preaviso_expiracion,
        $precio_consumo_ponta,
        $precio_consumo_cheia,
        $precio_consumo_vazio_normal,
        $precio_consumo_super_vazio,
        $precio_acceso_ponta,
        $precio_acceso_cheia,
        $precio_acceso_vazio_normal,
        $precio_acceso_super_vazio,
        $potencia_contratada,
        $precio_potencia_contratada,
        $precio_potencia_ponta,
        $precio_energia_reactiva_inductiva,
        $precio_energia_reactiva_capacitiva,
        $impuesto_electrico ,
        $iva,
        $contribucion_audiovisual ,
        $iva_reducido)
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

        // Se crea el contenido de las pestañas de tarifas eléctricas
        $contenido = "
            <div id='tabs-administracion-tarifa-electrica' class='tabbable' tipo-administracion='".$tipo_administracion."'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-contrato-fijo' id='titulo-tab-precios_consumo'>".$idiomas->_("Precios de consumo")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-precios-consumo-tarifa-acceso-tramos' id='titulo-tab-precios-consumo-tarifa-acceso-tramos'>".$idiomas->_("Precios de consumo de tarifa de acceso")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-precios-potencias-tramos' id='titulo-tab-precios-potencias-tramos'>".$idiomas->_("Potencias")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-energia-reactiva' id='titulo-tab-energia-reactiva'>".$idiomas->_("Precios energía reactiva")."</a></li>
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
                $opciones_extra_lista_ciclos_tarifa_electrica = OPCIONES_EXTRA_LISTA_CICLOS_TARIFA_ELECTRICA_NINGUNO;
                $opciones_extra_lista_regiones_tarifa_electrica = OPCIONES_EXTRA_LISTA_CICLOS_TARIFA_ELECTRICA_NINGUNO;
                break;
            }
            case TIPO_ADMINISTRACION_TARIFAS_MULTIPLE:
            {
                $opciones_extra_lista_tipos_tarifa_electrica = OPCIONES_EXTRA_LISTA_TIPOS_TARIFA_TODOS;
                $opciones_extra_lista_contratos_tarifa_electrica = OPCIONES_EXTRA_LISTA_CONTRATOS_TARIFA_ELECTRICA_TODOS;
                $opciones_extra_lista_ciclos_tarifa_electrica = OPCIONES_EXTRA_LISTA_CICLOS_TARIFA_ELECTRICA_TODOS;
                $opciones_extra_lista_regiones_tarifa_electrica = OPCIONES_EXTRA_LISTA_CICLOS_TARIFA_ELECTRICA_TODOS;
                break;
            }
        }
        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tipo").": "."</span><br/>
                    <select id='tipo_tarifa_electrica' class='select-administracion'>";
        $contenido .= dame_lista_tipos_tarifa_electricidad_Portugal($tipo, $opciones_extra_lista_tipos_tarifa_electrica);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Ciclo").": "."</span><br/>
                    <select id='ciclo_tarifa_electrica' class='select-administracion'>";
        $contenido .= dame_lista_ciclos_tarifa_electricidad_Portugal($ciclo, $opciones_extra_lista_ciclos_tarifa_electrica);
        $contenido .= "
                    </select>
                </div>
            </div>

            <div class='row-fluid'>
            <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Región").": "."</span><br/>
                <select id='region_tarifa_electrica' class='select-administracion'>";
            $contenido .= dame_lista_regiones_Portugal($region, $opciones_extra_lista_regiones_tarifa_electrica);
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
        /*if ($tipo_administracion == TIPO_ADMINISTRACION_TARIFAS_MULTIPLE)
        {
            $contenido .= "
                <div class='row-fluid'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tarifas").": "."</span><br/>
                        <div id='select_tarifas_electricas_tarifa_electrica_no_visible' hidden></div>
                        <select id='ids_tarifas_electricas_tarifa_electrica'
                            name='ids_tarifas_electricas_tarifa_electrica'
                            max_selected='".ID_NINGUNO."' multiple='multiple'
                            class='select-administracion' hidden>";
            $contenido .= dame_lista_tarifas_tipo_contrato_electricidad_Portugal($tipo, $contrato);
            $contenido .= "
                        </select>
                    </div>
                </div>";
        }*/

        $contenido .= "
                    </div>";

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

				// Contenido de pestaña de precios de consumo
        $contenido .= "
                    <div class='tab-pane' id='tab-contrato-fijo'>";

				// Se añaden los controles de cada uno de los tramos

				$titulo_campo_tramo_ponta = $idiomas->_("Precio de consumo en tramo Ponta")." (".$idiomas->_("€")."/".$idiomas->_("kWh").")";
				$contenido .= "
						<div class='row-fluid'>
								<div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_tramo_ponta.": "."</span><br/>
										<input type='text' id='precio_consumo_tramo_tarifa_electrica_ponta'
												class='".$clase_controles."TLNT_input_float input-administracion' value='".$precio_consumo_ponta."'>
								</div>
						</div>";

				$titulo_campo_tramo_cheia = $idiomas->_("Precio de consumo en tramo Cheia")." (".$idiomas->_("€")."/".$idiomas->_("kWh").")";
				$contenido .= "
						<div class='row-fluid'>
								<div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_tramo_cheia.": "."</span><br/>
										<input type='text' id='precio_consumo_tramo_tarifa_electrica_cheia'
												class='".$clase_controles."TLNT_input_float input-administracion' value='".$precio_consumo_cheia."'>
								</div>
						</div>";

				$titulo_campo_tramo_vazio_normal = $idiomas->_("Precio de consumo en tramo Vazio Normal")." (".$idiomas->_("€")."/".$idiomas->_("kWh").")";
				$contenido .= "
						<div class='row-fluid'>
								<div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_tramo_vazio_normal.": "."</span><br/>
										<input type='text' id='precio_consumo_tramo_tarifa_electrica_vazio_normal'
												class='".$clase_controles."TLNT_input_float input-administracion' value='".$precio_consumo_vazio_normal."'>
								</div>
						</div>";

				$titulo_campo_tramo_super_vazio = $idiomas->_("Precio de consumo en tramo Super Vazio")." (".$idiomas->_("€")."/".$idiomas->_("kWh").")";
				$contenido .= "
						<div class='row-fluid'>
								<div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_tramo_super_vazio.": "."</span><br/>
										<input type='text' id='precio_consumo_tramo_tarifa_electrica_super_vazio'
												class='".$clase_controles."TLNT_input_float input-administracion' value='".$precio_consumo_super_vazio."'>
								</div>
						</div>";

				$contenido .= "
                    </div>";

        // Contenido de pestaña de precios de consumo de tarifa de acceso
        $contenido .= "
                    <div class='tab-pane' id='tab-precios-consumo-tarifa-acceso-tramos'>";
				$contenido .= "
						<div class='row-fluid'>
								<div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_tramo_ponta.": "."</span><br/>
										<input type='text' id='precio_acceso_tramo_tarifa_electrica_ponta'
												class='".$clase_controles."TLNT_input_float input-administracion' value='".$precio_acceso_ponta."'>
								</div>
						</div>";

				$titulo_campo_tramo_cheia = $idiomas->_("Precio de acceso en tramo Cheia")." (".$idiomas->_("€")."/".$idiomas->_("kWh").")";
				$contenido .= "
						<div class='row-fluid'>
								<div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_tramo_cheia.": "."</span><br/>
										<input type='text' id='precio_acceso_tramo_tarifa_electrica_cheia'
												class='".$clase_controles."TLNT_input_float input-administracion' value='".$precio_acceso_cheia."'>
								</div>
						</div>";

				$titulo_campo_tramo_vazio_normal = $idiomas->_("Precio de acceso en tramo Vazio Normal")." (".$idiomas->_("€")."/".$idiomas->_("kWh").")";
				$contenido .= "
						<div class='row-fluid'>
								<div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_tramo_vazio_normal.": "."</span><br/>
										<input type='text' id='precio_acceso_tramo_tarifa_electrica_vazio_normal'
												class='".$clase_controles."TLNT_input_float input-administracion' value='".$precio_acceso_vazio_normal."'>
								</div>
						</div>";

				$titulo_campo_tramo_super_vazio = $idiomas->_("Precio de acceso en tramo Super Vazio")." (".$idiomas->_("€")."/".$idiomas->_("kWh").")";
				$contenido .= "
						<div class='row-fluid'>
								<div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_tramo_super_vazio.": "."</span><br/>
										<input type='text' id='precio_acceso_tramo_tarifa_electrica_super_vazio'
												class='".$clase_controles."TLNT_input_float input-administracion' value='".$precio_acceso_super_vazio."'>
								</div>
						</div>";
				$contenido .= "
                    </div>";

        // Contenido de pestaña de precios de potencias
        $contenido .= "
                    <div class='tab-pane' id='tab-precios-potencias-tramos'>";

				$titulo_campo_precio_potencia_contratada = $idiomas->_("Precio de potencia contratada")." (".$idiomas->_("€")."/".$idiomas->_("kW")."-".$idiomas->_("día").")";
				$contenido .= "
						<div class='row-fluid'>
								<div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_precio_potencia_contratada.": "."</span><br/>
										<input type='text' id='precio_potencia_contratada_tarifa_electrica'
												class='".$clase_controles."TLNT_input_float input-administracion' value='".$precio_potencia_contratada."'>
								</div>
						</div>";

				$titulo_campo_potencia_contratada = $idiomas->_("Potencia contratada")." (".$idiomas->_("kW").")";
				$contenido .= "
						<div class='row-fluid'>
								<div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_potencia_contratada.": "."</span><br/>
										<input type='text' id='potencia_contratada_tarifa_electrica'
												class='".$clase_controles."TLNT_input_float input-administracion' value='".$potencia_contratada."'>
								</div>
						</div>";

				$titulo_campo_potencia_ponta = $idiomas->_("Precio de potencia hora ponta")." (".$idiomas->_("€")."/".$idiomas->_("kW")."-".$idiomas->_("día").")";
				$contenido .= "
						<div class='row-fluid'>
								<div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_potencia_ponta.": "."</span><br/>
										<input type='text' id='precio_potencia_ponta_tarifa_electrica'
												class='".$clase_controles."TLNT_input_float input-administracion' value='".$precio_potencia_ponta."'>
								</div>
						</div>";

				$contenido .= "
                    </div>";

        // Contenido de pestaña de potencias
        $contenido .= "
            <div class='tab-pane' id='tab-energia-reactiva'>";
				$titulo_campo_energia_reactiva_inductiva = $idiomas->_("Penalización de energía reactiva inductiva")." (".$idiomas->_("€")."/".$idiomas->_("kVAhr").")";
				$contenido .= "
						<div class='row-fluid'>
								<div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_energia_reactiva_inductiva.": "."</span><br/>
										<input type='text' id='precio_energia_reactiva_inductiva_tarifa_electrica'
												class='".$clase_controles."TLNT_input_float input-administracion' value='".$precio_energia_reactiva_inductiva."'>
								</div>
						</div>";

				$titulo_campo_energia_reactiva_capacitiva = $idiomas->_("Penalización de energía reactiva capacitiva")." (".$idiomas->_("€")."/".$idiomas->_("kVAhr").")";
				$contenido .= "
						<div class='row-fluid'>
								<div class='span12'><span class='titulo-campo-administracion'>".$titulo_campo_energia_reactiva_capacitiva.": "."</span><br/>
										<input type='text' id='precio_energia_reactiva_capacitiva_tarifa_electrica'
												class='".$clase_controles."TLNT_input_float input-administracion' value='".$precio_energia_reactiva_capacitiva."'>
								</div>
						</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de factura
        $contenido .= "
                    <div class='tab-pane' id='tab-factura'>";

        $contenido .= "
            <div class='row-fluid'>
                <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Impuesto eléctrico")." (%)".": "."</span><br/>
                    <input type='text' id='impuesto_electrico_tarifa_electrica'
                    class='".$clase_controles."TLNT_input_float input-administracion' value='".$impuesto_electrico."'>
                </div>
            </div>";

        $contenido .= "
            <div class='row-fluid' id='control_iva_tarifa_electrica'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("IVA")." (%)".": "."</span><br/>
					<input type='text' id='iva_tarifa_electrica'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$iva."'>
				</div>
			</div>

            <div class='row-fluid' id='control_contribucion_audiovisual'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Contribuçao Audio-Visual").": "."</span><br/>
					<input type='text' id='contribucion_audiovisual_tarifa_electrica'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$contribucion_audiovisual."'>
				</div>
			</div>

            <div class='row-fluid' id='control_iva_reducido_tarifa_electrica'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("IVA reducido")." (%)".": "."</span><br/>
					<input type='text' id='iva_reducido_tarifa_electrica'
						class='".$clase_controles."TLNT_input_float input-administracion' value='".$iva_reducido."'>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        $contenido .= "
                </div>
            </div>";

        return ($contenido);
    }

		function dame_fila_tabla_tarifa_electricidad_Portugal($parametros)
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
        $tarifa = new TarifaElectrica_Portugal($fila_tarifa);
        $params_fila = array(
            "tipo" => TIPO_FILA_TABLA_DATOS_DETALLES,
            "opciones" => $tarifa->dame_opciones_tabla(),
            "numero_columnas" => NUMERO_COLUMNAS_TABLA_TARIFAS_ELECTRICAS_PORTUGAL,
            "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_TARIFAS_ELECTRICAS_PORTUGAL));
        $info_tabla = $tarifa->dame_info_tabla(MEDICION_ELECTRICIDAD);
        $datos_tabla = $info_tabla["datos"];
        $fila = TablaDatos::dame_fila(
            $datos_tabla,
            $params_fila);

        $id_datos = "datosTarifaElectrica_Portugal__".$id_tarifa;
        $resultado = array(
            "res" => "OK",
            "id_datos" => $id_datos,
            "fila" => $fila);
        return ($resultado);
    }


		// Devuelve la información de los tramos de la tarifa eléctrica
		function dame_info_tramos_tarifa_electricidad_Portugal($id_tarifa)
		{
			$bd_red = BaseDatosRed::dame_base_datos();

		  // Se recuperan los tramos existentes en la base de datos de la tarifa eléctrica
		  $consulta_tramos_tarifa_electrica = "
		  		SELECT *
		    	FROM ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_PORTUGAL."
		    	WHERE
		    		tarifa_electrica = ".$bd_red->_($id_tarifa);
		  $res_tramos_tarifa_electrica = $bd_red->ejecuta_consulta($consulta_tramos_tarifa_electrica);
		  if ($res_tramos_tarifa_electrica == false)
		  {
		  		throw new Exception("Error en la consulta: '".$consulta_tramos_tarifa_electrica."'");
		  }

		  $fila_tramos_tarifa_electrica = $res_tramos_tarifa_electrica->dame_siguiente_fila();

			return ($fila_tramos_tarifa_electrica);
		}


		// Devuelve las filas de los tramos de la tarifa
		function dame_filas_tramos_tarifa_electricidad_Portugal($id_tarifa)
		{
				$bd_red = BaseDatosRed::dame_base_datos();

				$consulta_tramos = "
						SELECT *
						FROM ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_PORTUGAL."
						WHERE
								tarifa_electrica = '".$bd_red->_($id_tarifa)."'
						ORDER BY
								id ASC";
				$res_tramos = $bd_red->ejecuta_consulta($consulta_tramos);
				if ($res_tramos == false)
				{
						throw new Exception("Ha ocurrido un error en la consulta: '".$consulta_tramos."'");
				}
				$fila_tramo = $res_tramos->dame_siguiente_fila();
				return ($fila_tramo);
		}


		// Guarda fecha de recálculos de tarifas
		function guarda_fecha_recalculo_datos_electricidad_Portugal($parametros)
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
                    AND ((SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_Portugal_ID_TARIFA_ELECTRICA + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) IN (".$cadena_ids_tarifas_electricas_consulta."))
                        OR (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_clase, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_Portugal_ID_GRUPO_TARIFAS_ELECTRICAS + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) IN (".$cadena_ids_grupos_tarifas_electricas_consulta.")))";
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

		function asigna_tarifa_grupo_tarifas_sensores_electricidad_Portugal($parametros)
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
            $id_tarifa_sensor = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_PORTUGAL_ID_TARIFA_ELECTRICA];
            $id_grupo_tarifas_sensor = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_PORTUGAL_ID_GRUPO_TARIFAS_ELECTRICAS];
            if (($id_tarifa == $id_tarifa_sensor) && ($id_grupo_tarifas == $id_grupo_tarifas_sensor))
            {
                continue;
            }

            // Parámetros de clase con la nueva tarifa o grupo de tarifas
            $cadena_parametros_clase_modificados = implode(SEPARADOR_PARAMETROS_COMPUESTOS, array(
                $id_tarifa,
                $id_grupo_tarifas,
                $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_PORTUGAL_CUPS],
                $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_PORTUGAL_ERROR_MAXIMO_VALIDACION_FACTURAS_ENERGIA],
                $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_PORTUGAL_ERROR_MAXIMO_VALIDACION_FACTURAS_POTENCIA],
                $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_PORTUGAL_ERROR_MAXIMO_VALIDACION_FACTURAS_OTROS_CONCEPTOS_COSTE_TOTAL],
                $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_PORTUGAL_TIPO_FICHERO_VALIDACION_FACTURAS],
                $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_PORTUGAL_PREFIJO_FICHERO_VALIDACION_FACTURAS]));

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



    /*
    // Crea una lista desplegable para la selección de una tarifa eléctrica
    function dame_control_lista_tarifas_electricidad_Portugal(
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
        $control_lista_tarifas_electricas .= dame_lista_tarifas_electricidad_Portugal(array($id_tarifa), $opciones_extra);
        $control_lista_tarifas_electricas .= "
            </select>";

        return ($control_lista_tarifas_electricas);
    }




    // Devuelve la lista de tarifas eléctricas de un tipo y contrato
    function dame_lista_tarifas_tipo_contrato_electricidad_Portugal($tipo, $contrato)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_tarifas_electricas = "
            SELECT
                id,
                nombre,
                tipo
            FROM ".TABLA_TARIFAS_ELECTRICAS_Portugal."
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




    // Devuelve la lista de bonificaciones de 85 % de tarifa eléctrica
    function dame_lista_bonificaciones_85_tarifa_electricidad_Portugal($bonificacion_85_seleccionada, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $lista = "";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_BONIFICACION_85_TARIFA_ELECTRICA_NINGUNA)
        {
            $lista .= "<option value='".BONIFICACION_85_TARIFA_ELECTRICA_NINGUNA."'>".$idiomas->_("Ninguna")."</option>";
        }
        $bonificaciones_85_tarifa = TarifaElectrica_Portugal::dame_bonificaciones_85_tarifa_electrica();
        foreach ($bonificaciones_85_tarifa as $bonificacion_85_tarifa)
        {
            $nombre_bonificacion_85_tarifa = TarifaElectrica_Portugal::dame_descripcion_bonificacion_85_tarifa_electrica($bonificacion_85_tarifa);
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
    function dame_lista_tipos_medida_tarifa_electricidad_Portugal($tipo_medida_seleccionado, $opciones_extra)
    {
        $idiomas = new Idiomas();

        $lista = "";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_TIPOS_MEDIDA_TARIFA_ELECTRICA_NINGUNA)
        {
            $lista .= "<option value='".TIPO_MEDIDA_TARIFA_ELECTRICA_NINGUNA."'>".$idiomas->_("Ninguna")."</option>";
        }
        $tipos_medida_tarifa = TarifaElectrica_Portugal::dame_tipos_medida_tarifa_electrica();
        foreach ($tipos_medida_tarifa as $tipo_medida_tarifa)
        {
            $nombre_tipo_medida_tarifa = TarifaElectrica_Portugal::dame_descripcion_tipo_medida_tarifa_electrica($tipo_medida_tarifa);
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
    function dame_lista_ids_indicadores_omie_pass_pool_tarifa_electricidad_Portugal($id_indicador_seleccionado)
    {
        $ids_indicadores_omie_pass_pool_tarifa = TarifaElectrica_Portugal::dame_ids_indicadores_omie_coste_pass_pool_tarifa_electrica();
        foreach ($ids_indicadores_omie_pass_pool_tarifa as $id_indicador_omie_pass_pool_tarifa)
        {
            $nombre_id_indicador_omie_pass_pool_tarifa = TarifaElectrica_Portugal::dame_descripcion_id_indicador_omie_pass_pool_tarifa_electrica($id_indicador_omie_pass_pool_tarifa);
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
    function dame_lista_tipos_calculo_coste_pass_pool_tarifa_electricidad_Portugal($tipo_seleccionado)
    {
        $tipos_calculo_coste_pass_pool_tarifa = TarifaElectrica_Portugal::dame_tipos_calculo_coste_pass_pool_tarifa_electrica();
        foreach ($tipos_calculo_coste_pass_pool_tarifa as $tipo_calculo_coste_pass_pool_tarifa)
        {
            $nombre_tipo_calculo_coste_pass_pool_tarifa = TarifaElectrica_Portugal::dame_descripcion_tipo_calculo_coste_pass_pool_tarifa_electrica($tipo_calculo_coste_pass_pool_tarifa);
            $lista .= "<option value='".$tipo_calculo_coste_pass_pool_tarifa."'";
			if ($tipo_calculo_coste_pass_pool_tarifa == $tipo_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($nombre_tipo_calculo_coste_pass_pool_tarifa, ENT_QUOTES)."</option>";
        }

        return ($lista);
    }






    // Devuelve los controles de los parámetros de excesos de potencia de máximos mensuales
	function dame_controles_parametros_excesos_potencia_maximos_mensuales_electricidad_Portugal($bonificacion_85, $tipo_administracion)
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
        $controles .= dame_lista_bonificaciones_85_tarifa_electricidad_Portugal($bonificacion_85, $opciones_extra_lista_bonificacion_85_tarifa_electrica);
        $controles .= "
                    </select>
                </div>
            </div>";

        return ($controles);
    }


    // Devuelve los controles de los parámetros de medida de datos de facturación
	function dame_controles_parametros_medida_datos_facturacion_electricidad_Portugal($tipo_medida, $potencia_nominal_transformador, $tipo_administracion)
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
        $controles .= dame_lista_tipos_medida_tarifa_electricidad_Portugal($tipo_medida, $opciones_extra_lista_tipos_medida_tarifa_electrica);
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





    // Devuelve los controles de los coeficientes de precios de 'pass-pool' de tramos
	function dame_controles_coeficientes_precio_consumo_pass_pool_tramos_tarifa_tipo_electricidad_Portugal($id_tarifa, $tipo, $tipo_administracion)
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
				FROM ".TABLA_TRAMOS_TARIFAS_ELECTRICAS_Portugal."
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
            $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Portugal::dame_caracteristicas_tipo_tarifa_electrica($tipo);
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













    //
    // Funciones de parámetros de energía eléctrica
    //


    // Se recupera una tabla con información de los parámetros de energía eléctrica
	function dame_tabla_informacion_parametros_energia_electricidad_Portugal()
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
                    $tabla_valores_indicadores_energia_electrica = TABLA_VALORES_INDICADORES_1_ENERGIA_ELECTRICA_Portugal;
                    break;
                }
                case 2:
                {
                    $tabla_valores_indicadores_energia_electrica = TABLA_VALORES_INDICADORES_2_ENERGIA_ELECTRICA_Portugal;
                    break;
                }
                default:
                {
                    throw new Exception("Id de indicador de grupo desconocido: '".$id_grupo_indicadores_energia_electrica."'");
                }
            }
            $res_horas_ultimos_valores_parametros = dame_horas_ultimos_valores_parametros_energia_electricidad_Portugal($tabla_valores_indicadores_energia_electrica);
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

        // Última hora de coeficientes de pérdidas de energía eléctrica
        $res_horas_ultimos_valores_parametros = dame_horas_ultimos_valores_parametros_energia_electricidad_Portugal(TABLA_COEFICIENTES_PERDIDAS_ENERGIA_ELECTRICA_Portugal);
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
        $res_horas_ultimos_valores_parametros = dame_horas_ultimos_valores_parametros_energia_electricidad_Portugal(TABLA_VALORES_PERDIDAS_ENERGIA_ELECTRICA_Portugal_2021);
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
        $res_horas_ultimos_valores_parametros = dame_horas_ultimos_valores_parametros_energia_electricidad_Portugal(TABLA_VALORES_PERDIDAS_ENERGIA_ELECTRICA_Portugal);
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
        $res_horas_ultimos_valores_parametros = dame_horas_ultimos_valores_parametros_energia_electricidad_Portugal(TABLA_VALORES_PVPC_ENERGIA_ELECTRICA_Portugal);
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
        $res_horas_ultimos_valores_parametros = dame_horas_ultimos_valores_parametros_energia_electricidad_Portugal(TABLA_VALORES_DESVIOS_ENERGIA_ELECTRICA_Portugal);
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
        $boton_actualizar_informacion_parametros_energia_electrica = "<i id='boton_smartmeter_actualizar_informacion_parametros_energia_electricidad_Portugal' class='icon-refresh color-blanco boton-tabla-datos'></i>";
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
	function dame_horas_ultimos_valores_parametros_energia_electricidad_Portugal($tabla_parametros_energia_electrica)
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










    //
    // Funciones de instalacion
    //


    // Devuelve información de la instalación del sensor especificado
    function dame_datos_instalacion_sensor_electricidad_Portugal($id_sensor, $id_tarifa)
    {
        $idiomas = new Idiomas();

        // Se recupera el identificador de tarifa y el cups
        $fila_sensor = dame_fila_sensor($id_sensor);
        $cadena_parametros_clase = $fila_sensor["parametros_clase"];
        $parametros_clase = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_clase);
        $cups = $parametros_clase[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_Portugal_CUPS];

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
            $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_Portugal, $id_tarifa);
            $descripcion = $fila_tarifa_electrica["descripcion"];
            if ($descripcion == "")
            {
                $descripcion = $fila_tarifa_electrica["nombre"];
            }
            $tipo = $fila_tarifa_electrica["tipo"];
            $contrato = $fila_tarifa_electrica["contrato"];
            if ($contrato == CONTRATO_TARIFA_ELECTRICA_Portugal_PASS_THROUGH)
            {
                $formula_precio_consumo = $fila_tarifa_electrica["formula_precio_consumo_pass_through"];
            }
            $tabla_tramos_tarifa_electrica = dame_tabla_tramos_tarifa_electricidad_Portugal($id_tarifa, $fila_tarifa_electrica);

            // Datos del apartado (información de tarifa eléctrica)
            $datos_instalacion["descripcion"] = $descripcion;
            $datos_instalacion["contrato"] = $contrato;
            $datos_instalacion["descripcion_tipo"] = TarifaElectrica_Portugal::dame_descripcion_tipo_tarifa_electrica($tipo);
            $datos_instalacion["descripcion_contrato"] = TarifaElectrica_Portugal::dame_descripcion_contrato_tarifa_electrica($contrato);
            if ($contrato == CONTRATO_TARIFA_ELECTRICA_Portugal_PASS_THROUGH)
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


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_instalacion_electricidad_Portugal(
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


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_instalacion_electricidad_Portugal(
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
        $datos_elemento = dame_datos_instalacion_sensor_electricidad_Portugal($id_sensor, $id_tarifa);
        return ($datos_elemento);
    }


    //
    // Funciones de obtención de información de tarifas
    //



    // Devuelve la fila de periodo de cálculo de costes pass-pool de la tarifa
    function dame_fila_periodo_calculo_costes_pass_pool_tarifa_electricidad_Portugal($id_periodo_calculo_costes)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_periodo_calculo_costes = "
            SELECT *
            FROM ".TABLA_PERIODOS_CALCULO_COSTES_PASS_POOL_TARIFAS_ELECTRICAS_Portugal."
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
    function dame_fila_concepto_coste_pass_through_tarifa_electricidad_Portugal($id_concepto_coste)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_concepto_coste = "
            SELECT *
            FROM ".TABLA_CONCEPTOS_COSTE_PASS_THROUGH_TARIFAS_ELECTRICAS_Portugal."
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


    function anyade_parametros_tramos_accion_usuario_tarifa_electricidad_Portugal(
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
            case CONTRATO_TARIFA_ELECTRICA_Portugal_FIJO:
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TRAMOS_TARIFA_ELECTRICA] = $precios_consumo_tramos;
                break;
            }
            case CONTRATO_TARIFA_ELECTRICA_Portugal_PASS_POOL:
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


    function anyade_parametros_tramos_accion_usuario_modificacion_tarifa_electricidad_Portugal(
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
                if ($fila_actual["contrato"] == CONTRATO_TARIFA_ELECTRICA_Portugal_FIJO)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TRAMOS_TARIFA_ELECTRICA] = $precios_consumo_tramos;
                }
                if ($fila_anterior["contrato"] == CONTRATO_TARIFA_ELECTRICA_Portugal_FIJO)
                {
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TRAMOS_TARIFA_ELECTRICA] = $precios_consumo_tramos_anteriores;
                }
            }
            if ($coeficientes_a_precio_consumo_pass_pool_tramos_modificados == true)
            {
                if ($fila_actual["contrato"] == CONTRATO_TARIFA_ELECTRICA_Portugal_PASS_POOL)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_COEFICIENTES_A_PRECIO_CONSUMO_PASS_POOL_TRAMOS_TARIFA_ELECTRICA] = $coeficientes_a_precio_consumo_pass_pool_tramos;
                }
                if ($fila_anterior["contrato"] == CONTRATO_TARIFA_ELECTRICA_Portugal_PASS_POOL)
                {
                    $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_COEFICIENTE_A_PRECIO_CONSUMO_PASS_POOL] = $coeficientes_a_precio_consumo_pass_pool_tramos_anteriores;
                }
            }
            if ($coeficientes_b_precio_consumo_pass_pool_tramos_modificados == true)
            {
                if ($fila_actual["contrato"] == CONTRATO_TARIFA_ELECTRICA_Portugal_PASS_POOL)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_COEFICIENTES_B_PRECIO_CONSUMO_PASS_POOL_TRAMOS_TARIFA_ELECTRICA] = $coeficientes_b_precio_consumo_pass_pool_tramos;
                }
                if ($fila_anterior["contrato"] == CONTRATO_TARIFA_ELECTRICA_Portugal_PASS_POOL)
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
            anyade_parametros_tramos_accion_usuario_tarifa_electricidad_Portugal(
                $fila_actual,
                $filas_tramos_actuales,
                $parametros_accion_usuario);
            anyade_parametros_tramos_accion_usuario_tarifa_electricidad_Portugal(
                $fila_anterior,
                $filas_tramos_anteriores,
                $parametros_accion_usuario_anteriores);
        }
    }


    function anyade_parametros_tramos_accion_usuario_modificacion_tarifas_electricidad_Portugal(
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
                if ($contrato == CONTRATO_TARIFA_ELECTRICA_Portugal_FIJO)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PRECIOS_CONSUMO_TRAMOS_TARIFA_ELECTRICA] = $precios_consumo_tramos;
                }
            }
            if ($coeficientes_a_precio_consumo_pass_pool_tramos_modificados == true)
            {
                if ($contrato == CONTRATO_TARIFA_ELECTRICA_Portugal_PASS_POOL)
                {
                    $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_COEFICIENTES_A_PRECIO_CONSUMO_PASS_POOL_TRAMOS_TARIFA_ELECTRICA] = $coeficientes_a_precio_consumo_pass_pool_tramos;
                }
            }
            if ($coeficientes_b_precio_consumo_pass_pool_tramos_modificados == true)
            {
                if ($contrato == CONTRATO_TARIFA_ELECTRICA_Portugal_PASS_POOL)
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


*/
?>
