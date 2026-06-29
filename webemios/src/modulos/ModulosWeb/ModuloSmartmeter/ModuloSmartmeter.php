<?php
	session_start();

	include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloWebEmios.php');

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_mediciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Autoconsumo/util_informes_autoconsumo.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/util_informes_caudales.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/util_informes_compra_energia.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/electricidad/util_informes_consumos_costes_electricidad.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/util_informes_consumos_costes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/EnergiaReactiva/util_informes_energia_reactiva.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Espanya/util_facturas_electricidad_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/electricidad/Espanya/ValidacionFacturaElectrica_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/util_facturas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/util_informes_facturas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/util_informes_informes_personalizados.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/util_resultados_mensuales.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/util_informes_potencias.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/TarifaAgua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/util_tarifas_agua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/TarifaElectrica_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/util_tarifas_electricidad_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/TarifaElectrica_Portugal.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/util_tarifas_electricidad_Portugal.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/TarifaGas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/util_tarifas_gas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/GrupoTarifas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/util_modulo_smartmeter.php');


	class ModuloSmartmeter extends ModuloWebEmios
	{
        function __construct()
        {
            // Super
            parent::__construct(MODULO_SMARTMETER, NOMBRE_MODULO_SMARTMETER);
        }


        //
        // Funciones virtuales sobreescritas
        //


        static function dame_secciones($parametros_extra)
        {
            // Si no hay parametros extra, es para la sección por defecto, se devuelven todas
            if ($parametros_extra === NULL)
            {
                $secciones = array(
                    SECCION_SMARTMETER_CONSUMOS_COSTES,
                    SECCION_SMARTMETER_AUTOCONSUMO,
                    SECCION_SMARTMETER_POTENCIAS,
                    SECCION_SMARTMETER_ENERGIA_REACTIVA,
                    SECCION_SMARTMETER_COMPRA_ENERGIA,
                    SECCION_SMARTMETER_CAUDALES,
                    SECCION_SMARTMETER_FACTURAS,
                    SECCION_SMARTMETER_INFORMES_PERSONALIZADOS,
                    SECCION_SMARTMETER_TARIFAS);
                return ($secciones);
            }

            // Secciones dependientes de las características de la medición actual
            $medicion = $parametros_extra["medicion"];
            $caracteristicas_tarifas = dame_caracteristicas_tarifas_pais_medicion($medicion);

            $secciones = array();
            array_push($secciones, SECCION_SMARTMETER_CONSUMOS_COSTES);
            if ($caracteristicas_tarifas["autoconsumo"] == true)
            {
                array_push($secciones, SECCION_SMARTMETER_AUTOCONSUMO);
            }
            if ($caracteristicas_tarifas["potencias"] == true)
            {
                array_push($secciones, SECCION_SMARTMETER_POTENCIAS);
            }
            if ($caracteristicas_tarifas["energia_reactiva"] == true)
            {
                array_push($secciones, SECCION_SMARTMETER_ENERGIA_REACTIVA);
            }
            if ($caracteristicas_tarifas["compra_energia"] == true)
            {
                array_push($secciones, SECCION_SMARTMETER_COMPRA_ENERGIA);
            }
            if ($caracteristicas_tarifas["caudales"] == true)
            {
                array_push($secciones, SECCION_SMARTMETER_CAUDALES);
            }
            if ($caracteristicas_tarifas["facturas"] == true)
            {
                array_push($secciones, SECCION_SMARTMETER_FACTURAS);
            }
            if ($caracteristicas_tarifas["informe_estudio_general"] == true)
            {
                array_push($secciones, SECCION_SMARTMETER_INFORMES_PERSONALIZADOS);
            }
            if ($caracteristicas_tarifas["tarifas"] == true)
            {
                array_push($secciones, SECCION_SMARTMETER_TARIFAS);
            }

            return ($secciones);
        }


        static function dame_descripcion_seccion($seccion)
        {
            switch ($seccion)
            {
                case SECCION_SMARTMETER_CONSUMOS_COSTES:
                {
                    $descripcion = "Consumos y costes";
                    break;
                }
                case SECCION_SMARTMETER_AUTOCONSUMO:
                {
                    $descripcion = "Autoconsumo";
                    break;
                }
                case SECCION_SMARTMETER_POTENCIAS:
                {
                    $descripcion = "Potencias";
                    break;
                }
                case SECCION_SMARTMETER_ENERGIA_REACTIVA:
                {
                    $descripcion = "Energía reactiva";
                    break;
                }
                case SECCION_SMARTMETER_COMPRA_ENERGIA:
                {
                    $descripcion = "Compra de energía";
                    break;
                }
                case SECCION_SMARTMETER_CAUDALES:
                {
                    $descripcion = "Caudales";
                    break;
                }
                case SECCION_SMARTMETER_FACTURAS:
                {
                    $descripcion = "Facturas";
                    break;
                }
                case SECCION_SMARTMETER_INFORMES_PERSONALIZADOS:
                {
                    $descripcion = "Informes personalizados";
                    break;
                }
                case SECCION_SMARTMETER_TARIFAS:
                {
                    $descripcion = "Tarifas";
                    break;
                }
                default:
                {
                    $descripcion = "Desconocida";
                    break;
                }
            }

            $idiomas = new Idiomas();
            return ($idiomas->_($descripcion));
        }


        static function dame_cadena_parametros_extra_enlace_seccion($seccion, $parametros_extra)
        {
            $cadena_parametros_extra_enlace_seccion = "#medicion=".$parametros_extra["medicion"];
            return ($cadena_parametros_extra_enlace_seccion);
        }


        function dame_contenido_seccion($seccion, $parametros_extra)
		{
            // Módulo
            $html = "<div id='modulo' name='".MODULO_SMARTMETER."' hidden></div>";

            // Se añade la tabla de selección de localización actual
            $mostrar_controles_localizaciones = dame_mostrar_controles_localizaciones();
            if ($mostrar_controles_localizaciones == true)
            {
                $mostrar_seleccion_ratio = false;
                $seleccion_ratio_visible = false;
                switch ($seccion)
                {
                    case SECCION_SMARTMETER_CONSUMOS_COSTES:
                    case SECCION_SMARTMETER_INFORMES_PERSONALIZADOS:
                    {
                        $mostrar_seleccion_ratio = true;
                        $seleccion_ratio_visible = false;
                        break;
                    }
                }
                $contenido_oculto = false;
                if (array_key_exists("seleccion_localizacion_actual_desplegada", $parametros_extra) == true)
                {
                    $contenido_oculto = ($parametros_extra["seleccion_localizacion_actual_desplegada"] == VALOR_NO);
                }
                $html .= dame_tabla_seleccion_localizacion_actual_ratio(
                    $mostrar_seleccion_ratio,
                    $seleccion_ratio_visible,
                    $contenido_oculto);
            }

            // Medición
            $medicion = $parametros_extra["medicion"];
            switch ($medicion)
            {
                case MEDICION_ELECTRICIDAD:
                case MEDICION_GAS:
                case MEDICION_AGUA:
                {
                    break;
                }
                default:
                {
                    $mensaje_error = $this->idiomas->_("Medición desconocida");
                    $contenido_seccion_error = dame_contenido_seccion_error($mensaje_error);
                    print(json_encode(array(
                        "res" => "OK",
                        "msg_error" => htmlspecialchars($mensaje_error, ENT_QUOTES),
                        "html" => $contenido_seccion_error)));
                    return;
                }
            }
            if ($this->dame_medicion_visible_seccion($medicion, $seccion) == false)
            {
                $mensaje_error = $this->idiomas->_("Medición incorrecta");
                $contenido_seccion_error = dame_contenido_seccion_error($mensaje_error);
                print(json_encode(array(
                    "res" => "OK",
                    "msg_error" => htmlspecialchars($mensaje_error, ENT_QUOTES),
                    "html" => $contenido_seccion_error)));
                return;
            }

            // Se añaden los botones de selección de medición
            $html .= $this->dame_controles_seleccion_medicion_seccion($medicion, $seccion);

            // Se añade el contenido de la sección
            $GLOBALS["reutilizar_consultas_bases_datos"] = true;
            $res = "OK";
            switch ($seccion)
            {
                case SECCION_SMARTMETER_CONSUMOS_COSTES:
                {
                    $html .= $this->dame_consumos_costes($medicion);
                    break;
                }
                case SECCION_SMARTMETER_AUTOCONSUMO:
                {
                    $html .= $this->dame_autoconsumo($medicion);
                    break;
                }
                case SECCION_SMARTMETER_POTENCIAS:
                {
                    $html .= $this->dame_potencias();
                    break;
                }
                case SECCION_SMARTMETER_ENERGIA_REACTIVA:
                {
                    $html .= $this->dame_energia_reactiva();
                    break;
                }
                case SECCION_SMARTMETER_COMPRA_ENERGIA:
                {
                    $administracion_sensores = NodoSensor::dame_administracion_sensores();
                    if ($administracion_sensores == true)
                    {
                        $html .= $this->dame_herramientas_compra_energia();
                    }
                    $html .= $this->dame_compra_energia();
                    break;
                }
                case SECCION_SMARTMETER_CAUDALES:
                {
                    $html .= $this->dame_caudales();
                    break;
                }
                case SECCION_SMARTMETER_FACTURAS:
                {
                    $html .= $this->dame_facturas($medicion);
                    break;
                }
                case SECCION_SMARTMETER_INFORMES_PERSONALIZADOS:
                {
                    $html .= $this->dame_informes_personalizados($medicion);
                    break;
                }
                case SECCION_SMARTMETER_TARIFAS:
                {
                    $administracion_tarifas = Tarifa::dame_administracion_tarifas();
                    if ($administracion_tarifas == true)
                    {
                        $html .= $this->dame_herramientas_tarifas($medicion);
                    }
                    $html .= $this->dame_tarifas($medicion);
                    break;
                }
                default:
                {
                    $res = "ERROR";
                    $msg = $this->idiomas->_("Sección desconocida");
                    break;
                }
            }

            print(json_encode(array(
                "res" => $res,
                "msg" => $msg,
                "html" => $html))
            );
		}



        //
        // Funciones de controles de mediciones
        //


        function dame_controles_seleccion_medicion_seccion($medicion_actual, $seccion)
        {
            // Se recorren las mediciones y se añaden las mediciones
            // (Nota: Si alguna sección no está disponible en la medición, se añade la sección equivalente - o sección por defecto -
            //  al identificador del botón de medición para cambiar a esa sección si se selecciona esa medición)
            $controles = "<div class='btn-group seleccion-medicion elemento-no-seleccionable'>";
            $mediciones = array(
                MEDICION_ELECTRICIDAD,
                MEDICION_GAS,
                MEDICION_AGUA);
            foreach ($mediciones as $medicion)
            {
                switch ($medicion)
                {
                    case MEDICION_ELECTRICIDAD:
                    {
                        $icono_boton_medicion = "<i class='icon-bolt color-blanco'></i>";
                        break;
                    }
                    case MEDICION_GAS:
                    {
                        $icono_boton_medicion = "<i class='icon-fire color-blanco'></i>";
                        break;
                    }
                    case MEDICION_AGUA:
                    {
                        $icono_boton_medicion = "<i class='icon-tint color-blanco'></i>";
                        break;
                    }
                    default:
                    {
                        throw new Exception("Medición desconocida: '".$medicion."'");
                    }
                }
                if ($medicion == $medicion_actual)
                {
                    $clase_boton_medicion = "btn-medicion-seleccionada";
                }
                else
                {
                    $clase_boton_medicion = "btn-medicion-no-seleccionada";
                }
                $id_boton_medicion = "boton_medicion__".$medicion;
                $medicion_visible_seccion = $this->dame_medicion_visible_seccion($medicion, $seccion);
                if ($medicion_visible_seccion == false)
                {
                    $seccion_medicion = SECCION_SMARTMETER_CONSUMOS_COSTES;
                    switch ($medicion_actual)
                    {
                        case MEDICION_ELECTRICIDAD:
                        {
                            if (($seccion == SECCION_SMARTMETER_POTENCIAS) && ($medicion == MEDICION_GAS))
                            {
                                $seccion_medicion = SECCION_SMARTMETER_CAUDALES;
                            }
                            break;
                        }
                        case MEDICION_GAS:
                        {
                            if (($seccion == SECCION_SMARTMETER_CAUDALES) && ($medicion == MEDICION_ELECTRICIDAD))
                            {
                                $seccion_medicion = SECCION_SMARTMETER_POTENCIAS;
                            }
                            break;
                        }
                    }
                    if ($seccion_medicion !== NULL)
                    {
                        $id_boton_medicion .= "__".$seccion_medicion;
                    }
                }
                if (($medicion_visible_seccion == true) || ($seccion_medicion !== NULL))
                {
                    $descripcion_medicion = dame_descripcion_medicion($medicion);
                    $controles .= "
                        <button type='button' id='".$id_boton_medicion."' class='btn boton-medicion ".$clase_boton_medicion." boton_smartmeter_medicion'>".
                            $icono_boton_medicion." ".$descripcion_medicion."
                        </button>";
                }
            }
            $controles .= "</div><br/>";
            return ($controles);
        }


        function dame_medicion_visible_seccion($medicion, $seccion)
        {
            $parametros_extra = array("medicion" => $medicion);
            $secciones_medicion = $this->dame_secciones($parametros_extra);
            $medicion_visible_seccion = (in_array($seccion, $secciones_medicion) == true);
            return ($medicion_visible_seccion);
        }


        //
		// Funciones para obtener el contenido de las secciones
		//


        function dame_consumos_costes($medicion)
        {
            // Se recupera el número de informes automáticos (actual)
            $numero_informes_automaticos = dame_numero_informes_automaticos();

            // Se recuperan las características de las tarifas
            $caracteristicas_tarifas = dame_caracteristicas_tarifas_pais_medicion($medicion);

            // Se obtiene el nombre de cliente para posteriormente poder filtrarle según que opciones
            $id_red = $_SESSION["id_red"];
            $bd_red = BaseDatosRed::dame_base_datos();
            $consulta = "SELECT clientes.nombre FROM clientes INNER JOIN redes ON clientes.id=redes.cliente WHERE redes.id = ".$id_red;
            $res = $bd_red->ejecuta_consulta($consulta);
            $fila = $res->dame_siguiente_fila();
            $nombre_cliente = $fila["nombre"];


            $contenido = "
                <div id='tabs' class='tabbable'>";
            $contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable' id='pestanyas-consumos-costes-smartmeter'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-consumos-costes-generales'>".$this->idiomas->_("Consumos y costes generales")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-consumos-costes-totales'>".$this->idiomas->_("Consumos y costes totales")."</a></li>";
            if ($caracteristicas_tarifas["tramos"] == true)
            {
                $contenido .= "
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-consumos-costes-tramos'>".$this->idiomas->_("Consumos y costes por tramo")."</a></li>";
            }
            if ($caracteristicas_tarifas["potencias"] == true)
            {
                $contenido .= "
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-excesos-potencia'>".$this->idiomas->_("Excesos de potencia")."</a></li>";
            }
            if ($caracteristicas_tarifas["energia_reactiva"] == true)
            {
                $contenido .= "
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-excesos-energia-reactiva'>".$this->idiomas->_("Excesos de energía reactiva")."</a></li>";
            }
            if ($caracteristicas_tarifas["cortes_tension"] == true)
            {
                $contenido .= "
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-cortes-tension'>".$this->idiomas->_("Cortes de tensión")."</a></li>";
            }
            if ($caracteristicas_tarifas["caudales"] == true)
            {
                $contenido .= "
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-excesos-caudal'>".$this->idiomas->_("Excesos de caudal")."</a></li>";
            }
            if ($nombre_cliente != 'Yoiba Energy')
            {
            $contenido .= "
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-comparacion-periodos'>".$this->idiomas->_("Comparación de periodos")."</a></li>";
            }
            if (($caracteristicas_tarifas["curva_coste"] == true)  AND ($nombre_cliente != 'Yoiba Energy'))
            {
                $contenido .= "
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-simulador-tarifas'>".$this->idiomas->_("Simulador de tarifas")."</a></li>";
            }
            $contenido .= "
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-mapa-consumos-costes'>".$this->idiomas->_("Mapa de consumos y costes")."</a></li>
                    </ul>
                    <div id='tabs-consumos-costes' class='tab-content'>";

            $contenido .= "<div class='tab-pane active pestanya-consumos-costes-smartmeter' id='tab-consumos-costes-generales'>";
            $contenido .= $this->dame_consumos_costes_generales($medicion, $numero_informes_automaticos);
            $contenido .= "</div>";

            $contenido .= "<div class='tab-pane pestanya-consumos-costes-smartmeter' id='tab-consumos-costes-totales'>";
            $contenido .= $this->dame_consumos_costes_totales($medicion, $numero_informes_automaticos);
            $contenido .= "</div>";

            if ($caracteristicas_tarifas["tramos"] == true)
            {
                $contenido .= "<div class='tab-pane pestanya-consumos-costes-smartmeter' id='tab-consumos-costes-tramos'>";
                $contenido .= $this->dame_consumos_costes_tramos($numero_informes_automaticos);
                $contenido .= "</div>";
            }

            if ($caracteristicas_tarifas["potencias"] == true)
            {
                $contenido .= "<div class='tab-pane pestanya-consumos-costes-smartmeter' id='tab-excesos-potencia'>";
                $contenido .= $this->dame_excesos_potencia($numero_informes_automaticos);
                $contenido .= "</div>";
            }

            if ($caracteristicas_tarifas["energia_reactiva"] == true)
            {
                $contenido .= "<div class='tab-pane pestanya-consumos-costes-smartmeter' id='tab-excesos-energia-reactiva'>";
                $contenido .= $this->dame_excesos_energia_reactiva($numero_informes_automaticos);
                $contenido .= "</div>";
            }

            if ($caracteristicas_tarifas["cortes_tension"] == true)
            {
                $contenido .= "<div class='tab-pane pestanya-consumos-costes-smartmeter' id='tab-cortes-tension'>";
                $contenido .= $this->dame_cortes_tension($numero_informes_automaticos);
                $contenido .= "</div>";
            }

            if ($caracteristicas_tarifas["caudales"] == true)
            {
                $contenido .= "<div class='tab-pane pestanya-consumos-costes-smartmeter' id='tab-excesos-caudal'>";
                $contenido .= $this->dame_excesos_caudal($numero_informes_automaticos);
                $contenido .= "</div>";
            }

            $contenido .= "<div class='tab-pane pestanya-consumos-costes-smartmeter' id='tab-comparacion-periodos'>";
            $contenido .= $this->dame_comparacion_periodos($medicion, $numero_informes_automaticos);
            $contenido .= "</div>";

            if ($caracteristicas_tarifas["curva_coste"] == true)
            {
                $contenido .= "<div class='tab-pane pestanya-consumos-costes-smartmeter' id='tab-simulador-tarifas'>";
                $contenido .= $this->dame_simulador_tarifas($medicion);
                $contenido .= "</div>";
            }

            $contenido .= "<div class='tab-pane pestanya-consumos-costes-smartmeter' id='tab-mapa-consumos-costes'>";
            $contenido .= $this->dame_mapa_consumos_costes($medicion);
            $contenido .= "</div>";

            $contenido .= "
                    </div>
                </div>";

            return ($contenido);
        }


		function dame_consumos_costes_generales($medicion, $numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_consumos_costes_generales";

            $clase_sensor = dame_clase_sensor_medicion($medicion);
			$control_lista_doble_sensores = dame_control_lista_doble_sensores(
                $sufijo_controles,
                $clase_sensor,
                MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_CONSUMOS_COSTES_GENERALES,
                $this->idiomas->_("Sensores"));

            $control_lista_intervalos_valores = $this->dame_control_lista_intervalos_valores_consumos_costes($sufijo_controles, $clase_sensor);
            $control_lista_agregaciones = dame_control_lista_agregaciones($sufijo_controles, TIPO_VALORES_SENSOR_INCREMENTALES, TIPOS_AGREGACION_SIN_CLASES);
            $control_lista_comentarios = dame_control_lista_comentarios_informes($sufijo_controles);
            $opciones = array(
                $control_lista_intervalos_valores,
                $control_lista_agregaciones,
                $control_lista_comentarios);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $horas_inicio_fin = dame_horas_inicio_fin_informe_medicion(TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES, $medicion);
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                $horas_inicio_fin["hora_inicio"],
                $horas_inicio_fin["hora_fin"],
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SMARTMETER_CONSUMOS_COSTES_GENERALES),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-generales",
                $this->idiomas->_("Consumos y costes generales"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensores")));
            $params_contenido_sensores = array(
                "clase_contenido" => "lista-sensores"
            );
            $tabla->anyade_contenido("", $control_lista_doble_sensores, $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CONSUMOS_COSTES_GENERALES),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_CONSUMOS_COSTES_GENERALES
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_consumos_costes_generales(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_consumos_costes_totales($medicion, $numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_consumos_costes_totales";

            $clase_sensor = dame_clase_sensor_medicion($medicion);
			$control_lista_doble_sensores = dame_control_lista_doble_sensores(
                $sufijo_controles,
                $clase_sensor,
                MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_CONSUMOS_COSTES_TOTALES,
                $this->idiomas->_("Sensores"));

            $control_lista_intervalos_valores = $this->dame_control_lista_intervalos_valores_consumos_costes($sufijo_controles, $clase_sensor);
            $opciones = array($control_lista_intervalos_valores);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
			$horas_inicio_fin = dame_horas_inicio_fin_informe_medicion(TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES, $medicion);
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                $horas_inicio_fin["hora_inicio"],
                $horas_inicio_fin["hora_fin"],
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SMARTMETER_CONSUMOS_COSTES_TOTALES),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-totales",
                $this->idiomas->_("Consumos y costes totales"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensores")));
            $params_contenido_sensores = array(
                "clase_contenido" => "lista-sensores"
            );
            $tabla->anyade_contenido("", $control_lista_doble_sensores, $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CONSUMOS_COSTES_TOTALES),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_CONSUMOS_COSTES_TOTALES
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_consumos_costes_totales(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_consumos_costes_tramos($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_consumos_costes_tramos";

			$control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                CLASE_SENSOR_ENERGIA_ACTIVA,
                true,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                NULL,
                NULL,
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SMARTMETER_CONSUMOS_COSTES_TRAMOS),
                array(),
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-consumos-costes-tramos",
                $this->idiomas->_("Consumos y costes por tramo"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$tabla->anyade_cabecera("", array($this->idiomas->_("Sensor")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR)
            );
            $tabla->anyade_fila("", array($control_lista_sensores), $params_contenido_sensores);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CONSUMOS_COSTES_TRAMOS),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_CONSUMOS_COSTES_TRAMOS
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_consumos_costes_tramos_electricidad(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_excesos_potencia($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_excesos_potencia";

            $control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                CLASE_SENSOR_ENERGIA_ACTIVA,
                true,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $control_lista_granularidades = $this->dame_control_lista_granularidades_excesos_potencia($sufijo_controles);
            $opciones = array($control_lista_granularidades);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
			$fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SMARTMETER_EXCESOS_POTENCIA),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-excesos-potencia",
                $this->idiomas->_("Excesos de potencia"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$tabla->anyade_cabecera("", array($this->idiomas->_("Sensor")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR)
            );
            $tabla->anyade_fila("", array($control_lista_sensores), $params_contenido_sensores);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_EXCESOS_POTENCIA),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_EXCESOS_POTENCIA
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_excesos_potencia_electricidad(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_excesos_energia_reactiva($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_excesos_energia_reactiva";

            $control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                CLASE_SENSOR_ENERGIA_REACTIVA,
                true,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
			$fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SMARTMETER_EXCESOS_POTENCIA),
                NULL,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-excesos-energia-reactiva",
                $this->idiomas->_("Excesos de energía reactiva"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$tabla->anyade_cabecera("", array($this->idiomas->_("Sensor")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR)
            );
            $tabla->anyade_fila("", array($control_lista_sensores), $params_contenido_sensores);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_EXCESOS_ENERGIA_REACTIVA),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_EXCESOS_ENERGIA_REACTIVA
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_excesos_energia_reactiva_electricidad(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_cortes_tension($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_cortes_tension";

            $control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                CLASE_SENSOR_CORTES_TENSION,
                true,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
			$fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SMARTMETER_CORTES_TENSION),
                array(),
                $botones_extra);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-cortes-tension",
                $this->idiomas->_("Cortes de tensión"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$tabla->anyade_cabecera("", array($this->idiomas->_("Sensor")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR)
            );
            $tabla->anyade_fila("", array($control_lista_sensores), $params_contenido_sensores);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_CORTES_TENSION),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_CORTES_TENSION
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_cortes_tension_electricidad(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_excesos_caudal($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_excesos_caudal";

            $control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                CLASE_SENSOR_GAS,
                true,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
			$horas_inicio_fin = dame_horas_inicio_fin_informe_medicion(TIPO_INFORME_SMARTMETER_EXCESOS_CAUDAL, MEDICION_GAS);
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                $horas_inicio_fin["hora_inicio"],
                $horas_inicio_fin["hora_fin"],
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SMARTMETER_EXCESOS_CAUDAL),
                array(),
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-excesos-caudal",
                $this->idiomas->_("Excesos de caudal"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$tabla->anyade_cabecera("", array($this->idiomas->_("Sensor")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR)
            );
            $tabla->anyade_fila("", array($control_lista_sensores), $params_contenido_sensores);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_EXCESOS_CAUDAL),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_EXCESOS_CAUDAL
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_excesos_caudal_gas(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_comparacion_periodos($medicion, $numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_comparacion_periodos";

            $clase_sensor = dame_clase_sensor_medicion($medicion);
			$control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                $clase_sensor,
                true,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $control_lista_intervalos_valores = $this->dame_control_lista_intervalos_valores_consumos_costes($sufijo_controles, $clase_sensor);
            $opciones = array($control_lista_intervalos_valores);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
			$periodos = dame_filtro_periodos_informe($sufijo_controles,
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SMARTMETER_COMPARACION_PERIODOS),
                modifica_dias_duracion_periodos_defecto_informe(
                    PERIODO_DEFECTO_SMARTMETER_COMPARACION_PERIODOS,
                    DIAS_DURACION_DEFECTO_SMARTMETER_PERIODO),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-comparacion-periodos",
                $this->idiomas->_("Comparación de periodos"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$tabla->anyade_cabecera("", array($this->idiomas->_("Sensor")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR)
            );
            $tabla->anyade_fila("", array($control_lista_sensores), $params_contenido_sensores);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Periodos")));
            $params_fila_periodos = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_COMPARACION_PERIODOS_CONSUMOS_COSTES),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_COMPARACION_PERIODOS_CONSUMOS_COSTES
            );
			$tabla->anyade_fila("periodos-sensores", $periodos, $params_fila_periodos);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_comparacion_periodos(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_simulador_tarifas($medicion)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_simulador_tarifas";

            $clase_sensor = dame_clase_sensor_medicion($medicion);
            $control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                $clase_sensor,
                true,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
			$control_lista_doble_tarifas = dame_control_lista_doble_tarifas(
                "smartmeter_simulador_tarifas",
                $medicion,
                MAX_TARIFAS_SELECCIONADAS_DEFECTO_LISTA_TARIFAS_SIMULADOR_TARIFAS);

            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
			$horas_inicio_fin = dame_horas_inicio_fin_informe_medicion(TIPO_INFORME_SMARTMETER_SIMULADOR_TARIFAS, $medicion);
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                $horas_inicio_fin["hora_inicio"],
                $horas_inicio_fin["hora_fin"],
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SMARTMETER_SIMULADOR_TARIFAS),
                array(),
                $botones_extra);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-simulador-tarifas",
                $this->idiomas->_("Simulador de tarifas"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$tabla->anyade_cabecera("", array($this->idiomas->_("Sensor")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR)
            );
            $tabla->anyade_fila("", array($control_lista_sensores), $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Tarifas")));
            $params_contenido_tarifas = array(
                "clase_contenido" => "lista-tarifas"
            );
            $tabla->anyade_contenido("", $control_lista_doble_tarifas, $params_contenido_tarifas);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_TARIFAS),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_SIMULADOR_TARIFAS
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_simulador_tarifas(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_mapa_consumos_costes($medicion)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_mapa_consumos_costes";

            $clase_sensor = dame_clase_sensor_medicion($medicion);
			$control_lista_doble_sensores = dame_control_lista_doble_sensores(
                $sufijo_controles,
                $clase_sensor,
                MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_MAPA_CONSUMOS_COSTES,
                $this->idiomas->_("Sensores"));

            $opciones = array();
            $botones_extra = array();
			$horas_inicio_fin = dame_horas_inicio_fin_informe_medicion(TIPO_INFORME_SMARTMETER_MAPA_CONSUMOS_COSTES, $medicion);
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                $horas_inicio_fin["hora_inicio"],
                $horas_inicio_fin["hora_fin"],
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SMARTMETER_MAPA_CONSUMOS_COSTES),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-mapa-consumos-costes",
                $this->idiomas->_("Mapa de consumos y costes"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensores")));
            $params_contenido_sensores = array(
                "clase_contenido" => "lista-sensores"
            );
            $tabla->anyade_contenido("", $control_lista_doble_sensores, $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_MAPA_CONSUMOS_COSTES),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_MAPA_CONSUMOS_COSTES
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_mapa = array(
                "sin_margenes" => "true"
            );
            $mapa = "
                <div class='texto-contenido-vacio'>
                    <i class='icon-info-sign color-azul'></i> ".$this->idiomas->_("No hay datos")."
                </div>";
            $tabla->anyade_contenido("contenedor-mapa-consumos-costes", $mapa, $params_contenido_mapa);

			return ($tabla->dame_tabla());
		}


        function dame_autoconsumo($medicion)
        {
            $contenido = "
                <div id='tabs' class='tabbable'>";
            $contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-simulador-autoconsumo'>".$this->idiomas->_("Simulador de autoconsumo")."</a></li>
                    </ul>
                    <div id='tabs-autoconsumo' class='tab-content'>";

            $contenido .= "<div class='tab-pane active' id='tab-simulador-autoconsumo'>";
            $contenido .= $this->dame_simulador_autoconsumo($medicion);
            $contenido .= "</div>";

            $contenido .= "
                    </div>
                </div>";

            return ($contenido);
        }


        function dame_simulador_autoconsumo($medicion)
        {
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_simulador_autoconsumo";

            // Se recuperan las características de las tarifas
            $caracteristicas_tarifas = dame_caracteristicas_tarifas_pais_medicion($medicion);

            $clase_sensor = dame_clase_sensor_medicion($medicion);
			$control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                $clase_sensor,
                true,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
            $control_lista_sensores_autoconsumo = dame_control_lista_sensores(
                "generacion_".$sufijo_controles,
                $clase_sensor,
                true,
                $this->idiomas->_("Sensor de generación"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
            if ($caracteristicas_tarifas["curva_coste"] == true)
            {
                $control_lista_tarifas = dame_control_lista_tarifas(
                    $sufijo_controles,
                    $medicion,
                    ID_NINGUNO,
                    true,
                    OPCIONES_EXTRA_LISTA_TARIFAS_SIN_OPCIONES_EXTRA);
            }

            $control_lista_tipos_autoconsumo = $this->dame_control_lista_tipos_autoconsumo($sufijo_controles);
            $unidad_medida_consumo = dame_unidad_medida_consumo_clase_sensor($clase_sensor);
            $control_capacidad_acumulacion = dame_entrada_numero(
                "capacidad_acumulacion_".$sufijo_controles,
                $this->idiomas->_("Capacidad de acumulación")." (".$unidad_medida_consumo.")",
                0,
                TAMANYO_CONTROL_MEDIANO);
            $control_factor_multiplicacion_generacion = dame_entrada_numero(
                "factor_multiplicacion_generacion_".$sufijo_controles,
                $this->idiomas->_("Factor de multiplicación de generación"),
                1,
                TAMANYO_CONTROL_MEDIANO);
            $opciones = array(
                $control_lista_tipos_autoconsumo,
                $control_capacidad_acumulacion,
                $control_factor_multiplicacion_generacion);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                NULL,
                NULL,
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SMARTMETER_SIMULADOR_AUTOCONSUMO),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-simulador-autoconsumo",
                $this->idiomas->_("Simulador de autoconsumo"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            if ($caracteristicas_tarifas["curva_coste"] == true)
            {
                $tabla->anyade_cabecera("", array($this->idiomas->_("Sensores y tarifa")));
                $params_contenido_sensores_tarifa = array(
                    "clase_dato" => "desplegable-simple",
                    "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_SENSOR_TARIFA)
                );
                $controles_sensores = array(
                    $control_lista_sensores,
                    $control_lista_sensores_autoconsumo,
                    $control_lista_tarifas);
                $tabla->anyade_fila("", $controles_sensores, $params_contenido_sensores_tarifa);
            }
            else
            {
                $tabla->anyade_cabecera("", array($this->idiomas->_("Sensores")));
                $params_contenido_sensores_tarifa = array(
                    "clase_dato" => "desplegable-simple",
                    "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_SENSOR)
                );
                $controles_sensores = array(
                    $control_lista_sensores,
                    $control_lista_sensores_autoconsumo);
                $tabla->anyade_fila("", $controles_sensores, $params_contenido_sensores_tarifa);
            }

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_AUTOCONSUMO),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_SIMULADOR_AUTOCONSUMO
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_simulador_autoconsumo(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
        }


        function dame_potencias()
        {
            $contenido = "
                <div id='tabs' class='tabbable'>";
            $contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-optimizador-potencias-automatico'>".$this->idiomas->_("Optimizador de potencias automático")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-optimizador-potencias-manual'>".$this->idiomas->_("Optimizador de potencias manual")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-simulador-potencias-automatico'>".$this->idiomas->_("Simulador de potencias automático")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-simulador-potencias-manual'>".$this->idiomas->_("Simulador de potencias manual")."</a></li>
                    </ul>
                    <div id='tabs-potencias' class='tab-content'>";

            $contenido .= "<div class='tab-pane active' id='tab-optimizador-potencias-automatico'>";
            $contenido .= $this->dame_optimizador_potencias_automatico();
            $contenido .= "</div>";

            $contenido .= "<div class='tab-pane' id='tab-optimizador-potencias-manual'>";
            $contenido .= $this->dame_optimizador_potencias_manual();
            $contenido .= "</div>";

            $contenido .= "<div class='tab-pane' id='tab-simulador-potencias-automatico'>";
            $contenido .= $this->dame_simulador_potencias_automatico();
            $contenido .= "</div>";

            $contenido .= "<div class='tab-pane' id='tab-simulador-potencias-manual'>";
            $contenido .= $this->dame_simulador_potencias_manual();
            $contenido .= "</div>";

            $contenido .= "
                    </div>
                </div>";

            return ($contenido);
        }


        function dame_optimizador_potencias_automatico()
        {
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_optimizador_potencias_automatico";

			$controles_listas_sensores_tarifas_electricas = $this->dame_controles_listas_sensores_tarifas(
                $sufijo_controles,
                CLASE_SENSOR_ENERGIA_ACTIVA,
                MEDICION_ELECTRICIDAD,
                true,
                OPCIONES_EXTRA_LISTA_TARIFAS_ELECTRICAS_TIPO_CALCULO_COSTE_POTENCIAS_CON_EXCESOS);

            $control_lista_granularidades = $this->dame_control_lista_granularidades_excesos_potencia($sufijo_controles);
            $control_lista_rangos_potencias = $this->dame_control_lista_rangos_potencias($sufijo_controles);
            $control_diferencia_potencia = dame_entrada_numero(
                "diferencia_potencia_".$sufijo_controles,
                $this->idiomas->_("Diferencia de potencia")." (".$this->idiomas->_("kW").")",
                0,
                TAMANYO_CONTROL_MEDIANO);
            $opciones = array(
                $control_lista_granularidades,
                $control_lista_rangos_potencias,
                $control_diferencia_potencia);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
			$fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                NULL,
                NULL,
                PERIODO_DEFECTO_SMARTMETER_OPTIMIZADOR_POTENCIAS,
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-optimizador-potencias-automatico",
                $this->idiomas->_("Optimizador de potencias automático"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y tarifa")));
            $params_contenido_sensor = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_TARIFA)
            );
            $tabla->anyade_fila("", $controles_listas_sensores_tarifas_electricas, $params_contenido_sensor);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_OPTIMIZADOR_POTENCIAS_AUTOMATICO),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_OPTIMIZADOR_POTENCIAS_AUTOMATICO
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            $nombre_control_diferencia_potencia = $this->idiomas->_("diferencia de potencia");
            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal")." (".$nombre_control_diferencia_potencia.")",
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas")." (".$nombre_control_diferencia_potencia.")",
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas")." (".$nombre_control_diferencia_potencia.")",
                $inclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_optimizador_potencias_automatico(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
        }


        function dame_optimizador_potencias_manual()
        {
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_optimizador_potencias_manual";

			$controles_lista_tarifas_electricas = $this->dame_controles_listas_tarifas_electricas_optimizador_simulador_potencias_manual($sufijo_controles);
            $controles_filtro_optimizador_potencias_manual = $this->dame_controles_filtro_optimizador_simulador_potencias_manual($sufijo_controles);

            // Se crea la tabla contenedora
            $boton_ayuda_tabla_optimizador_potencias_manual = "<i id='boton_smartmeter_ayuda_optimizador_potencias_manual'"." ".
                "class='icon-question-sign color-blanco boton-tabla-datos'></i>";
            $opciones_tabla_informe = array($boton_ayuda_tabla_optimizador_potencias_manual);
            $params_tabla = array(
                "opciones" => $opciones_tabla_informe
            );
            $tabla = new TablaDatos(
                "tabla-smartmeter-optimizador-potencias-manual",
                $this->idiomas->_("Optimizador de potencias manual"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Tarifas")));
            $params_contenido_sensor = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_TARIFAS_ELECTRICAS)
            );
            $tabla->anyade_fila("", $controles_lista_tarifas_electricas, $params_contenido_sensor);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_OPTIMIZADOR_POTENCIAS_MANUAL),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_OPTIMIZADOR_POTENCIAS_MANUAL
            );
			$tabla->anyade_fila("datos-sensores", $controles_filtro_optimizador_potencias_manual, $params_fila);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_optimizador_potencias_manual(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
        }


        function dame_simulador_potencias_automatico()
        {
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_simulador_potencias_automatico";

			$controles_listas_sensores_tarifas_electricas = $this->dame_controles_listas_sensores_tarifas(
                $sufijo_controles,
                CLASE_SENSOR_ENERGIA_ACTIVA,
                MEDICION_ELECTRICIDAD,
                true,
                OPCIONES_EXTRA_LISTA_TARIFAS_ELECTRICAS_TIPO_CALCULO_COSTE_POTENCIAS_CON_EXCESOS);

            $controles_potencias = $this->dame_controles_potencias_simulador_potencias($sufijo_controles);
            $controles_potencias_sin_tarifa_electrica_seleccionada = $this->dame_controles_potencias_sin_tarifa_electrica_seleccionada_simulador_potencias($sufijo_controles);

            $control_lista_granularidades = $this->dame_control_lista_granularidades_excesos_potencia($sufijo_controles);
            $control_lista_rangos_potencias = $this->dame_control_lista_rangos_potencias($sufijo_controles);
            $control_diferencia_potencia = dame_entrada_numero(
                "diferencia_potencia_".$sufijo_controles,
                $this->idiomas->_("Diferencia de potencia")." (".$this->idiomas->_("kW").")",
                0,
                TAMANYO_CONTROL_MEDIANO);
            $opciones = array(
                $control_lista_granularidades,
                $control_lista_rangos_potencias,
                $control_diferencia_potencia);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
			$fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                NULL,
                NULL,
                PERIODO_DEFECTO_SMARTMETER_SIMULADOR_POTENCIAS,
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-simulador-potencias-automatico",
                $this->idiomas->_("Simulador de potencias automático"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y tarifa")));
            $params_contenido_sensor = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_TARIFA)
            );
            $tabla->anyade_fila("", $controles_listas_sensores_tarifas_electricas, $params_contenido_sensor);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Potencias")));
            $params_fila_controles_potencias = array(
                "clase_dato" => "filtro-informes",
                "numero_columnas" => NUMERO_COLUMNAS_POTENCIAS_SIMULADOR_POTENCIAS
            );
            $params_fila_controles_sin_potencias = array(
                "clase_dato" => "filtro-informes anchura80"
            );
            $tabla->anyade_fila("potencias_".$sufijo_controles, $controles_potencias, $params_fila_controles_potencias);
            $tabla->anyade_fila("potencias_sin_tarifa_electrica_seleccionada_".$sufijo_controles, $controles_potencias_sin_tarifa_electrica_seleccionada, $params_fila_controles_sin_potencias);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_POTENCIAS_AUTOMATICO),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_SIMULADOR_POTENCIAS_AUTOMATICO
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            $nombre_control_diferencia_potencia = $this->idiomas->_("diferencia de potencia");
            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal")." (".$nombre_control_diferencia_potencia.")",
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas")." (".$nombre_control_diferencia_potencia.")",
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas")." (".$nombre_control_diferencia_potencia.")",
                $inclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_simulador_potencias_automatico(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
        }


        function dame_simulador_potencias_manual()
        {
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_simulador_potencias_manual";

			$controles_lista_tarifas_electricas = $this->dame_controles_listas_tarifas_electricas_optimizador_simulador_potencias_manual($sufijo_controles);
            $controles_potencias = $this->dame_controles_potencias_simulador_potencias($sufijo_controles);
            $controles_potencias_sin_tarifa_electrica_seleccionada = $this->dame_controles_potencias_sin_tarifa_electrica_seleccionada_simulador_potencias($sufijo_controles);
            $controles_filtro_simulador_potencias_manual = $this->dame_controles_filtro_optimizador_simulador_potencias_manual($sufijo_controles);

            // Se crea la tabla contenedora
            $boton_ayuda_tabla_simulador_potencias_manual = "<i id='boton_smartmeter_ayuda_simulador_potencias_manual'"." ".
                "class='icon-question-sign color-blanco boton-tabla-datos'></i>";
            $opciones_tabla_informe = array($boton_ayuda_tabla_simulador_potencias_manual);
            $params_tabla = array(
                "opciones" => $opciones_tabla_informe
            );
            $tabla = new TablaDatos(
                "tabla-smartmeter-simulador-potencias-manual",
                $this->idiomas->_("Simulador de potencias manual"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Tarifas")));
            $params_contenido_sensor = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_TARIFAS_ELECTRICAS)
            );
            $tabla->anyade_fila("", $controles_lista_tarifas_electricas, $params_contenido_sensor);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Potencias")));
            $params_fila_controles_potencias = array(
                "clase_dato" => "filtro-informes",
                "numero_columnas" => NUMERO_COLUMNAS_POTENCIAS_SIMULADOR_POTENCIAS
            );
            $params_fila_controles_sin_potencias = array(
                "clase_dato" => "filtro-informes anchura80"
            );
            $tabla->anyade_fila("potencias_".$sufijo_controles, $controles_potencias, $params_fila_controles_potencias);
            $tabla->anyade_fila("potencias_sin_tarifa_electrica_seleccionada_".$sufijo_controles, $controles_potencias_sin_tarifa_electrica_seleccionada, $params_fila_controles_sin_potencias);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Datos")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_POTENCIAS_MANUAL),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_SIMULADOR_POTENCIAS_MANUAL
            );
			$tabla->anyade_fila("datos-sensores", $controles_filtro_simulador_potencias_manual, $params_fila);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_simulador_potencias_manual(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
        }


        function dame_energia_reactiva()
        {
            $contenido = "
                <div id='tabs' class='tabbable'>";
            $contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-calculo-bateria-condensadores'>".$this->idiomas->_("Simulador de batería de condensadores")."</a></li>
                    </ul>
                    <div id='tabs-energia-reactiva' class='tab-content'>";

            $contenido .= "<div class='tab-pane active' id='tab-simulador-bateria-condensadores'>";
            $contenido .= $this->dame_simulador_bateria_condensadores();
            $contenido .= "</div>";

            $contenido .= "
                    </div>
                </div>";

            return ($contenido);
        }


        function dame_simulador_bateria_condensadores()
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_simulador_bateria_condensadores";

            $control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                CLASE_SENSOR_ENERGIA_REACTIVA,
                true,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $control_diferencia_bateria_condensadores = dame_entrada_numero(
                "diferencia_capacidad_".$sufijo_controles,
                $this->idiomas->_("Diferencia de capacidad")." (".$this->idiomas->_("kVAr").")",
                0,
                TAMANYO_CONTROL_MEDIANO);
            $opciones = array($control_diferencia_bateria_condensadores);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));

            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SMARTMETER_CALCULO_BATERIA_CONDENSADORES),
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $boton_ayuda_simulador_bateria_condensadores = "<i id='boton_smartmeter_ayuda_simulador_bateria_condensadores'"." ".
                "class='icon-question-sign color-blanco boton-tabla-datos'></i>";
            $opciones_tabla_informe = array($boton_ayuda_simulador_bateria_condensadores);
            $params_tabla = array(
                "opciones" => $opciones_tabla_informe
            );
            $tabla = new TablaDatos(
                "tabla-smartmeter-simulador-bateria-condensadores",
                $this->idiomas->_("Simulador de batería de condensadores"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

			$tabla->anyade_cabecera("", array($this->idiomas->_("Sensor")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR)
            );
            $tabla->anyade_fila("", array($control_lista_sensores), $params_contenido_sensores);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_BATERIA_CONDENSADORES),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_SIMULADOR_BATERIA_CONDENSADORES
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_simulador_bateria_condensadores(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_herramientas_compra_energia()
		{
            // Se recuperan los controles a mostrar
            $botones = array();
            $numero_columnas_tabla_herramientas_compra_energia = NUMERO_COLUMNAS_TABLA_HERRAMIENTAS_COMPRA_ENERGIA;

            // Botones
            $boton_importar_valores_diarios_sensor = "<br/><button id='boton_importar_valores_diarios_compra_energia_sensor' class='btn-mini btn btn-success boton_smartmeter_mostrar_ventana_importacion_valores_diarios_compra_energia_sensor'>".$this->idiomas->_("Importar valores diarios")."</button><br/><br/>";
            $boton_recalcular_valores_sensor = "<br/><button id='boton_recalcular_valores_compra_energia_sensor' class='btn-mini btn btn-success boton_smartmeter_mostrar_ventana_recalculo_valores_compra_energia_sensor'>".$this->idiomas->_("Recalcular valores")."</button><br/><br/>";
            array_push($botones, $boton_importar_valores_diarios_sensor);
            array_push($botones, $boton_recalcular_valores_sensor);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-herramientas-compra-energia",
                $this->idiomas->_("Herramientas de compra de energía"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_fila" => "botones-herramientas",
                "clase_dato" => "boton-herramientas",
                "numero_columnas" => $numero_columnas_tabla_herramientas_compra_energia
            );
			$tabla->anyade_fila("botones-herramientas-compra-energia", $botones, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_compra_energia()
        {
            // Se recupera el número de informes automáticos (actual)
            $numero_informes_automaticos = dame_numero_informes_automaticos();

            $contenido = "
                <div id='tabs' class='tabbable'>";
            $contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-prevision-compra-energia'>".$this->idiomas->_("Previsión de compra de energía")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-desvios-compra-energia'>".$this->idiomas->_("Desvíos de compra de energía")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-desvios-ponderados-compra-energia'>".$this->idiomas->_("Desvíos ponderados de compra de energía")."</a></li>
                    </ul>
                    <div id='tabs-compra-energia' class='tab-content'>";

            $contenido .= "<div class='tab-pane active' id='tab-prevision-compra-energia'>";
            $contenido .= $this->dame_prevision_compra_energia();
            $contenido .= "</div>";
            $contenido .= "<div class='tab-pane' id='tab-desvios-compra-energia'>";
            $contenido .= $this->dame_desvios_compra_energia($numero_informes_automaticos);
            $contenido .= "</div>";
            $contenido .= "<div class='tab-pane' id='tab-desvios-ponderados-compra-energia'>";
            $contenido .= $this->dame_desvios_ponderados_compra_energia($numero_informes_automaticos);
            $contenido .= "</div>";

            $contenido .= "
                    </div>
                </div>";

            return ($contenido);
        }


        function dame_prevision_compra_energia()
		{
            $idiomas = new Idiomas();

            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_prevision_compra_energia";

            $control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                CLASE_SENSOR_COMPRA_ENERGIA,
                true,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $control_fecha_inicio_perfil_horario = dame_control_fecha_inicio(
                "perfil_horario_".$sufijo_controles,
                $idiomas->_("Inicio de perfil horario"),
                NULL,
                PERIODO_DEFECTO_SMARTMETER_PREVISION_COMPRA_ENERGIA_PERFIL_HORARIO);
            $control_fecha_fin_perfil_horario = dame_control_fecha_fin(
                "perfil_horario_".$sufijo_controles,
                $idiomas->_("Fin de perfil horario"),
                NULL,
                "");
            $control_lista_tipos_perfil_horario = $this->dame_control_lista_tipos_perfil_horario_prevision_compra_energia($sufijo_controles);
            $control_agrupaciones_dias_semana_perfil_horario = dame_entrada_cadena(
                "agrupaciones_dias_semana_".$sufijo_controles,
                $this->idiomas->_("Agrupaciones de días de la semana"),
                "",
                TAMANYO_CONTROL_GRANDE,
                true);
            $opciones = array(
                $control_fecha_inicio_perfil_horario,
                $control_fecha_fin_perfil_horario,
                $control_lista_tipos_perfil_horario,
                $control_agrupaciones_dias_semana_perfil_horario);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $administracion_sensores = NodoSensor::dame_administracion_sensores();
            if ($administracion_sensores == true)
            {
                $boton_exportar_importar_valores_diarios = dame_boton_formulario($sufijo_controles."_exportar_importar_valores_diarios", $idiomas->_("Exportar e importar valores diarios"), false);
                array_push($botones_extra, $boton_exportar_importar_valores_diarios);
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                NULL,
                NULL,
                PERIODO_DEFECTO_SMARTMETER_PREVISION_COMPRA_ENERGIA,
                $opciones,
                $botones_extra);

            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-prevision-compra-energia",
                $this->idiomas->_("Previsión de compra de energía"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$tabla->anyade_cabecera("", array($this->idiomas->_("Sensor")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR)
            );
            $tabla->anyade_fila("", array($control_lista_sensores), $params_contenido_sensores);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_PREVISION_COMPRA_ENERGIA),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_PREVISION_COMPRA_ENERGIA
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_prevision_compra_energia(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_desvios_compra_energia($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_desvios_compra_energia";

            $control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                CLASE_SENSOR_COMPRA_ENERGIA,
                true,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                PERIODO_DEFECTO_SMARTMETER_DESVIOS_COMPRA_ENERGIA,
                array(),
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-desvios-compra-energia",
                $this->idiomas->_("Desvíos de compra de energía"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$tabla->anyade_cabecera("", array($this->idiomas->_("Sensor")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR)
            );
            $tabla->anyade_fila("", array($control_lista_sensores), $params_contenido_sensores);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_DESVIOS_COMPRA_ENERGIA),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_DESVIOS_COMPRA_ENERGIA
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_desvios_compra_energia(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_desvios_ponderados_compra_energia($numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_desvios_ponderados_compra_energia";

            $controles_listas_sensores_sensores_hijos = dame_controles_listas_sensores_sensores_hijos(
                $sufijo_controles,
                CLASE_SENSOR_COMPRA_ENERGIA,
                true,
                $this->idiomas->_("Sensor de compra de energía"),
                $this->idiomas->_("Sensor"));

            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                "00:00",
                "23:59",
                PERIODO_DEFECTO_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA,
                array(),
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, true, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-desvios-ponderados-compra-energia",
                $this->idiomas->_("Desvíos ponderados de compra de energía"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensores")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_SENSOR_HIJO)
            );
            $tabla->anyade_fila("", $controles_listas_sensores_sensores_hijos, $params_contenido_sensores);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_DESVIOS_PONDERADOS_COMPRA_ENERGIA),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_DESVIOS_PONDERADOS_COMPRA_ENERGIA
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal"),
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas"),
                $inclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_desvios_ponderados_compra_energia(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
		}


        function dame_caudales()
        {
            $contenido = "
                <div id='tabs' class='tabbable'>";
            $contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-optimizador-caudales-automatico'>".$this->idiomas->_("Optimizador de caudales automático")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-optimizador-caudales-manual'>".$this->idiomas->_("Optimizador de caudales manual")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-simulador-caudales-automatico'>".$this->idiomas->_("Simulador de caudales automático")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-simulador-caudales-manual'>".$this->idiomas->_("Simulador de caudales manual")."</a></li>
                    </ul>
                    <div id='tabs-potencias' class='tab-content'>";

            $contenido .= "<div class='tab-pane active' id='tab-optimizador-caudales-automatico'>";
            $contenido .= $this->dame_optimizador_caudales_automatico();
            $contenido .= "</div>";

            $contenido .= "<div class='tab-pane' id='tab-optimizador-caudales-manual'>";
            $contenido .= $this->dame_optimizador_caudales_manual();
            $contenido .= "</div>";

            $contenido .= "<div class='tab-pane' id='tab-simulador-caudales-automatico'>";
            $contenido .= $this->dame_simulador_caudales_automatico();
            $contenido .= "</div>";

            $contenido .= "<div class='tab-pane' id='tab-simulador-caudales-manual'>";
            $contenido .= $this->dame_simulador_caudales_manual();
            $contenido .= "</div>";

            $contenido .= "
                    </div>
                </div>";

            return ($contenido);
        }


        function dame_optimizador_caudales_automatico()
        {
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_optimizador_caudales_automatico";

			$controles_listas_sensores_tarifas_gas = $this->dame_controles_listas_sensores_tarifas(
                $sufijo_controles,
                CLASE_SENSOR_GAS,
                MEDICION_GAS,
                true,
                OPCIONES_EXTRA_LISTA_TARIFAS_GAS_TIPO_CALCULO_COSTE_TERMINO_FIJO_CON_EXCESOS);

            $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
            switch ($pais_tarifas_gas)
            {
                case PAIS_ESPANYA:
                {
                    $nombre_control_diferencia_caudal = $this->idiomas->_("Diferencia de caudal diario");
                    $sufijo_nombre_controles_horario_semanal_exclusion_fechas = $this->idiomas->_("diferencia de caudal diario");
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                }
            }
            $control_diferencia_caudal = dame_entrada_numero(
                "diferencia_caudal_".$sufijo_controles,
                $nombre_control_diferencia_caudal." (".$this->idiomas->_("kWh").")",
                0,
                TAMANYO_CONTROL_MEDIANO);
            $opciones = array($control_diferencia_caudal);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
			$fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                NULL,
                NULL,
                PERIODO_DEFECTO_SMARTMETER_OPTIMIZADOR_CAUDALES,
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, false, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-optimizador-caudales-automatico",
                $this->idiomas->_("Optimizador de caudales automático"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y tarifa")));
            $params_contenido_sensor = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_TARIFA)
            );
            $tabla->anyade_fila("", $controles_listas_sensores_tarifas_gas, $params_contenido_sensor);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_OPTIMIZADOR_CAUDALES_AUTOMATICO),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_OPTIMIZADOR_CAUDALES_AUTOMATICO
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal")." (".$sufijo_nombre_controles_horario_semanal_exclusion_fechas.")",
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas")." (".$sufijo_nombre_controles_horario_semanal_exclusion_fechas.")",
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas")." (".$sufijo_nombre_controles_horario_semanal_exclusion_fechas.")",
                $inclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_optimizador_caudales_automatico(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
        }


        function dame_optimizador_caudales_manual()
        {
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_optimizador_caudales_manual";

			$controles_lista_tarifas_gas = $this->dame_controles_listas_tarifas_gas_optimizador_simulador_caudales_manual($sufijo_controles);
            $controles_filtro_optimizador_caudales_manual = $this->dame_controles_filtro_optimizador_simulador_caudales_manual($sufijo_controles);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-optimizador-caudales-manual",
                $this->idiomas->_("Optimizador de caudales manual"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Tarifas")));
            $params_contenido_sensor = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_TARIFAS_GAS)
            );
            $tabla->anyade_fila("", $controles_lista_tarifas_gas, $params_contenido_sensor);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_OPTIMIZADOR_CAUDALES_MANUAL),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_OPTIMIZADOR_CAUDALES_MANUAL
            );
			$tabla->anyade_fila("datos-sensores", $controles_filtro_optimizador_caudales_manual, $params_fila);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_optimizador_caudales_manual(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
        }


        function dame_simulador_caudales_automatico()
        {
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_simulador_caudales_automatico";

			$controles_listas_sensores_tarifas_gas = $this->dame_controles_listas_sensores_tarifas(
                $sufijo_controles,
                CLASE_SENSOR_GAS,
                MEDICION_GAS,
                true,
                OPCIONES_EXTRA_LISTA_TARIFAS_GAS_TIPO_CALCULO_COSTE_TERMINO_FIJO_CON_EXCESOS);

            $controles_caudales = $this->dame_controles_caudales_simulador_caudales($sufijo_controles);
            $controles_caudales_sin_tarifa_gas_seleccionada = $this->dame_controles_caudales_sin_tarifa_gas_seleccionada_simulador_caudales($sufijo_controles);

            $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
            switch ($pais_tarifas_gas)
            {
                case PAIS_ESPANYA:
                {
                    $nombre_control_diferencia_caudal = $this->idiomas->_("Diferencia de caudal diario");
                    $sufijo_nombre_controles_horario_semanal_exclusion_fechas = $this->idiomas->_("diferencia de caudal diario");
                    break;
                }
                default:
                {
                    throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                }
            }
            $control_diferencia_caudal = dame_entrada_numero(
                "diferencia_caudal_".$sufijo_controles,
                $nombre_control_diferencia_caudal." (".$this->idiomas->_("kWh").")",
                0,
                TAMANYO_CONTROL_MEDIANO);
            $opciones = array($control_diferencia_caudal);
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
			$fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                NULL,
                NULL,
                PERIODO_DEFECTO_SMARTMETER_SIMULADOR_CAUDALES,
                $opciones,
                $botones_extra);

            $horario_semanal = dame_controles_horario_semanal($sufijo_controles, ORIGEN_CONTROLES_INFORMES, false, NULL);
            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);
            $inclusion_fechas = dame_controles_fechas("inclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-simulador-caudales-automatico",
                $this->idiomas->_("Simulador de caudales automático"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y tarifa")));
            $params_contenido_sensor = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_TARIFA)
            );
            $tabla->anyade_fila("", $controles_listas_sensores_tarifas_gas, $params_contenido_sensor);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Caudales")));
            $params_fila_controles_caudales = array(
                "clase_dato" => "filtro-informes"
            );
            $params_fila_controles_sin_caudales = array(
                "clase_dato" => "filtro-informes anchura80"
            );
            $tabla->anyade_fila("caudales_".$sufijo_controles, $controles_caudales, $params_fila_controles_caudales);
            $tabla->anyade_fila("caudales_sin_tarifa_gas_seleccionada_".$sufijo_controles, $controles_caudales_sin_tarifa_gas_seleccionada, $params_fila_controles_sin_caudales);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_CAUDALES_AUTOMATICO),
                "numero_columnas" => NUMERO_COLUMNAS_PARAMETROS_SIMULADOR_CAUDALES_AUTOMATICO
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            anyade_controles_horario_semanal_tabla_informe(
                $sufijo_controles,
                $tabla,
                $this->idiomas->_("Horario semanal")." (".$sufijo_nombre_controles_horario_semanal_exclusion_fechas.")",
                $horario_semanal);
            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas")." (".$sufijo_nombre_controles_horario_semanal_exclusion_fechas.")",
                $exclusion_fechas);
            anyade_controles_fechas_tabla_informe(
                "inclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Inclusión de fechas")." (".$sufijo_nombre_controles_horario_semanal_exclusion_fechas.")",
                $inclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_simulador_caudales_automatico(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
        }


        function dame_simulador_caudales_manual()
        {
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_simulador_caudales_manual";

			$controles_lista_tarifas_gas = $this->dame_controles_listas_tarifas_gas_optimizador_simulador_caudales_manual($sufijo_controles);
            $controles_caudales = $this->dame_controles_caudales_simulador_caudales($sufijo_controles);
            $controles_caudales_sin_tarifa_gas_seleccionada = $this->dame_controles_caudales_sin_tarifa_gas_seleccionada_simulador_caudales($sufijo_controles);
            $controles_filtro_simulador_caudales_manual = $this->dame_controles_filtro_optimizador_simulador_caudales_manual($sufijo_controles);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-simulador-caudales-manual",
                $this->idiomas->_("Simulador de caudales manual"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Tarifas")));
            $params_contenido_sensor = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_TARIFAS_GAS)
            );
            $tabla->anyade_fila("", $controles_lista_tarifas_gas, $params_contenido_sensor);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Caudales")));
            $params_fila_controles_caudales = array(
                "clase_dato" => "filtro-informes"
            );
            $params_fila_controles_sin_caudales = array(
                "clase_dato" => "filtro-informes anchura80"
            );
            $tabla->anyade_fila("caudales_smartmeter_simulador_caudales_manual", $controles_caudales, $params_fila_controles_caudales);
            $tabla->anyade_fila("caudales_sin_tarifa_gas_seleccionada_smartmeter_simulador_caudales_manual", $controles_caudales_sin_tarifa_gas_seleccionada, $params_fila_controles_sin_caudales);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Datos")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_CAUDALES_MANUAL),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_SIMULADOR_CAUDALES_MANUAL
            );
			$tabla->anyade_fila("datos-sensores", $controles_filtro_simulador_caudales_manual, $params_fila);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_simulador_caudales_manual(TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
        }


        function dame_facturas($medicion)
        {
            // Se recupera el número de informes automáticos (actual)
            $numero_informes_automaticos = dame_numero_informes_automaticos();

            // Se recuperan las características de las tarifas
            $caracteristicas_tarifas = dame_caracteristicas_tarifas_pais_medicion($medicion);

            $contenido = "
                <div id='tabs' class='tabbable'>";
            $contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-simulador-factura'>".$this->idiomas->_("Simulador de factura")."</a></li>";
            if ($caracteristicas_tarifas["validacion_facturas"] == true)
            {
                $contenido .= "
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-validacion-facturas'>".$this->idiomas->_("Validación de facturas y cierres")."</a></li>";
            }
            $contenido .= "
                    </ul>
                    <div id='tabs-facturas-electricas' class='tab-content'>";

            $contenido .= "<div class='tab-pane active' id='tab-simulador-factura'>";
            $contenido .= $this->dame_simulador_factura($medicion, $numero_informes_automaticos);
            $contenido .= "</div>";

            if ($caracteristicas_tarifas["validacion_facturas"] == true)
            {
                $contenido .= "<div class='tab-pane' id='tab-validacion-facturas'>";
                $contenido .= $this->dame_validacion_facturas($medicion);
                $contenido .= "</div>";
            }

            $contenido .= "
                    </div>
                </div>";

            return ($contenido);
        }


        function dame_simulador_factura($medicion, $numero_informes_automaticos)
		{
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_simulador_factura";

            $caracteristicas_tarifas = dame_caracteristicas_tarifas_pais_medicion($medicion);
            if ($caracteristicas_tarifas["curva_coste"] == true)
            {
                $opciones_extra_lista_tarifas = OPCIONES_EXTRA_LISTA_TARIFAS_TARIFA_VIGENTE_SEGUN_FECHAS;
            }
            else
            {
                $opciones_extra_lista_tarifas = OPCIONES_EXTRA_LISTA_TARIFAS_ACTUAL;
            }
            $clase_sensor = dame_clase_sensor_medicion($medicion);
			$controles_listas_sensores_tarifas = $this->dame_controles_listas_sensores_tarifas(
                $sufijo_controles,
                $clase_sensor,
                $medicion,
                true,
                $opciones_extra_lista_tarifas);

            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
			$fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                NULL,
                NULL,
                PERIODO_DEFECTO_SMARTMETER_SIMULADOR_FACTURA,
                array(),
                $botones_extra);

            $exclusion_fechas = dame_controles_fechas("exclusion_fechas_".$sufijo_controles, ORIGEN_CONTROLES_INFORMES, NULL);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-simulador-factura",
                $this->idiomas->_("Simulador de factura"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor y tarifa")));
            $params_contenido_sensor = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR_TARIFA)
            );
            $tabla->anyade_fila("", $controles_listas_sensores_tarifas, $params_contenido_sensor);

            $id_lista_doble_sensores_reparto_costes = "lista_doble_sensores_reparto_costes_".$sufijo_controles;
            $ids_elementos_desplegables_reparto_costes = array($id_lista_doble_sensores_reparto_costes);
            $tabla->anyade_cabecera_elementos_desplegables(
                "cabecera-reparto-costes-simuladores-facturas",
                array($this->idiomas->_("Reparto de costes")),
                $ids_elementos_desplegables_reparto_costes,
                false);
            $control_lista_doble_sensores_reparto_costes = "<div id='".$id_lista_doble_sensores_reparto_costes."'>";
            $control_lista_doble_sensores_reparto_costes .= dame_control_lista_doble_sensores(
                "reparto_costes_".$sufijo_controles,
                $clase_sensor,
                MAX_SENSORES_SELECCIONADOS_LISTA_SENSORES_REPARTO_COSTES_SIMULADOR_FACTURA,
                $this->idiomas->_("Sensores"));
            $control_lista_doble_sensores_reparto_costes .= "</div>";
            $params_fila_sensores_reparto_costes = array(
                "oculta" => true
            );
            $tabla->anyade_fila(
                $id_lista_doble_sensores_reparto_costes,
                array($control_lista_doble_sensores_reparto_costes),
                $params_fila_sensores_reparto_costes);

			$tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SIMULADOR_FACTURAS),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_SIMULADOR_FACTURAS
            );
			$tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            anyade_controles_fechas_tabla_informe(
                "exclusion-fechas-".$sufijo_controles,
                $tabla,
                $this->idiomas->_("Exclusión de fechas"),
                $exclusion_fechas);

			// Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_simulador_factura($medicion, TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

			return ($tabla->dame_tabla());
        }


        function dame_validacion_facturas($medicion)
		{
            $contenido = "";

            $administracion_validaciones_facturas = dame_administracion_validaciones_facturas();
            if ($administracion_validaciones_facturas == true)
            {
                $contenido .= $this->dame_herramientas_validacion_facturas();
            }
            $contenido .= $this->dame_validaciones_facturas($medicion);

            return ($contenido);
        }


        function dame_herramientas_validacion_facturas()
		{
            // Se recuperan los controles a mostrar
			$boton_validar_facturas = "<br/><button id='boton_validar_facturas' class='btn-mini btn btn-success boton_smartmeter_mostrar_ventana_validacion_facturas'>".
                $this->idiomas->_("Validar facturas y cierres")."</button><br/><br/>";
            $botones = array(
                $boton_validar_facturas);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-herramientas-validacion-facturas",
                $this->idiomas->_("Herramientas de validación de facturas y cierres"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_fila" => "botones-herramientas",
                "clase_dato" => "boton-herramientas",
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_HERRAMIENTAS_VALIDACIONES_FACTURAS
            );
			$tabla->anyade_fila("botones-herramientas-validaciones-facturas", $botones, $params_fila);

			return ($tabla->dame_tabla());
        }


        function dame_validaciones_facturas($medicion)
		{
            $contenido .= "
                <div id='datos-validaciones-facturas-smartmeter'>";
            switch ($medicion)
            {
                case MEDICION_ELECTRICIDAD:
                {
                    // Selección de país
                    $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                    switch ($pais_tarifas_electricas)
                    {
                        case PAIS_ESPANYA:
                        {
                            $contenido .= dame_tabla_filtro_validaciones_facturas_electricidad_Espanya();
                            $contenido .= ValidacionFacturaElectrica_Espanya::dame_tabla_validaciones_facturas_electricas();
                            break;
                        }

                        case PAIS_PORTUGAL:
                        {
                            // No está disponible la validación de facturas eléctricas de Portugal.
                            break;
                        }

                        default:
                        {
                            throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                        }
                    }
                    break;
                }
                default:
                {
                    throw new Exception("Medición desconocida: '".$medicion."'");
                }
            }
            $contenido .= "
                </div>
                <br/>";

			return ($contenido);
		}


        function dame_informes_personalizados($medicion)
        {
            // Se recupera el número de informes automáticos (actual)
            $numero_informes_automaticos = dame_numero_informes_automaticos();

            // Se recuperan las características de las tarifas
            $caracteristicas_tarifas = dame_caracteristicas_tarifas_pais_medicion($medicion);

            $id_red = $_SESSION["id_red"];
            $bd_red = BaseDatosRed::dame_base_datos();
            $consulta = "SELECT clientes.nombre FROM clientes INNER JOIN redes ON clientes.id=redes.cliente WHERE redes.id = ".$id_red;
            $res = $bd_red->ejecuta_consulta($consulta);
            $fila = $res->dame_siguiente_fila();
            $nombre_cliente = $fila["nombre"];

            $contenido = "
                <div id='tabs' class='tabbable'>";
            $contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable' id='pestanyas-informes-personalizados-smartmeter'>";
                    
                    // En el caso de ser Gas se muestra otro panel de navegación con los resultados mensuales

                    if(($medicion == MEDICION_GAS)  AND ($nombre_cliente == 'Yoiba Energy')){
                        
                        //Para realizar los controles de mostrar u ocultar los DIV se crea una función JS

                        $contenido .="
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-resultados-mensuales' onclick='funcion_oculta_estudio_general()'>".$this->idiomas->_("Resultados Mensuales")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-estudio-general' onclick='funcion_muestra_estudio_general()'>".$this->idiomas->_("Estudio general")."</a></li>
                        </ul>
                        <div id='tabs-potencias' class='tab-content'>";
                    }

                    // El apartado estudio general se muestra como activo siempre que no sea GAS
                    else{
                    $contenido .= "
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-estudio-general'>".$this->idiomas->_("Estudio general")."</a></li>
                    </ul>
                    <div id='tabs-potencias' class='tab-content'>";
                    }
            
            // Si estamos en medicion de tipo gas se mostrará la pestaña de Resultados mensuales

            // Desarrollo exclusivo para YOIBA
            //
            
            
            if (($medicion == MEDICION_GAS) AND ($nombre_cliente == 'Yoiba Energy')){
                $contenido .= "<div class='tab-pane active pestanya-informes-personalizados-smartmeter' id='tab-resultados-mensuales'>";
                if ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR){
                    $contenido .= $this->dame_tabla_formulario_subir_fichero($medicion);
                }
                $contenido .= $this->dame_resultados_mensuales($medicion);
                $contenido .= "</div>";
                $contenido .= "<div class='tab-pane pestanya-informes-personalizados-smartmeter' id='tab-estudio-general'>";
            }
            // En caso contrario se muestra la pestaña de estudio general
            else{
                $contenido .= "<div class='tab-pane active pestanya-informes-personalizados-smartmeter' id='tab-estudio-general'>";
            }

            // Se piden los controles de la tabla dame estudio general
            $contenido .= $this->dame_estudio_general($medicion, $numero_informes_automaticos);
            $contenido .= "</div>";

            $contenido .= "
                    </div>
                </div>";

            return ($contenido);
        }


        function dame_estudio_general($medicion, $numero_informes_automaticos)
        {
            // Se recuperan los controles a mostrar
            $sufijo_controles = "smartmeter_estudio_general";

            $clase_sensor = dame_clase_sensor_medicion($medicion);
            $control_lista_sensores = dame_control_lista_sensores(
                $sufijo_controles,
                $clase_sensor,
                true,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);

            $control_lista_apartados = dame_control_lista_doble_apartados_estudio_general($sufijo_controles, $medicion);
            $controles_textos = dame_controles_textos_estudio_general($medicion);

            $opciones = array();
            $botones_extra = array(dame_boton_generar_pdf($sufijo_controles, true));
            $mostrar_boton_informe_automatico = dame_posible_anyadir_informe_automatico($numero_informes_automaticos);
            if ($mostrar_boton_informe_automatico == true)
            {
                array_push($botones_extra, dame_boton_anyadir_informe_automatico($sufijo_controles, true));
            }
            $fechas = dame_filtro_fechas_informe(
                $sufijo_controles,
                NULL,
                NULL,
                modifica_periodo_defecto_informe(PERIODO_DEFECTO_SMARTMETER_INFORME_GENERAL),
                $opciones,
                $botones_extra);

            // Se crea la tabla contenedora
            $boton_ayuda_tabla_estudio_general = "<i id='boton_smartmeter_ayuda_estudio_general'"." ".
                "class='icon-question-sign color-blanco boton-tabla-datos'></i>";
            $opciones_tabla_informe = array($boton_ayuda_tabla_estudio_general);
            $params_tabla = array(
                "opciones" => $opciones_tabla_informe
            );
            $tabla = new TablaDatos(
                "tabla-smartmeter-estudio-general",
                $this->idiomas->_("Estudio general"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $tabla->anyade_cabecera("", array($this->idiomas->_("Sensor")));
            $params_contenido_sensores = array(
                "clase_dato" => "desplegable-simple",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SENSOR)
            );
            $tabla->anyade_fila("", array($control_lista_sensores), $params_contenido_sensores);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Apartados")));
            $params_contenido_apartados = array(
                "clase_contenido" => "lista-apartados"
            );
            $tabla->anyade_contenido("", $control_lista_apartados, $params_contenido_apartados);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Textos")));
            $params_contenido_textos = array(
                "clase_contenido" => "textos-informe"
            );
            $tabla->anyade_contenido("", $controles_textos, $params_contenido_textos);

            $tabla->anyade_cabecera("", array($this->idiomas->_("Configuración")));
            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_ESTUDIO_GENERAL),
                "numero_columnas" => NUMEROS_COLUMNAS_PARAMETROS_ESTUDIO_GENERAL
            );
            $tabla->anyade_fila("fechas-smartmeter", $fechas, $params_fila);

            // Resultado del informe
            $tabla->anyade_cabecera("", array($this->idiomas->_("Informe")));
            $params_contenido_informe = array(
                "clase_contenido" => "informe"
            );
            $informe = dame_html_informe_tipo_smartmeter_estudio_general($medicion, TIPO_INFORME_WEB_EMIOS);
            $tabla->anyade_contenido("", $informe, $params_contenido_informe);

            return ($tabla->dame_tabla());
        }
           
        function dame_tabla_formulario_subir_fichero($medicion){
            $idiomas = new Idiomas();

            // Se recuperan los controles a mostrar
            $controles = array();
            $id_controles = "smartmeter_resultados_mensuales_subir_fichero";
            
            // Nombre del fichero
            $control_nombre_fichero = "<div id='etiqueta_nombre_fichero_excel_resultados_mensuales'>".$idiomas->_("Nombre").": </div>";
            $control_nombre_fichero .= "<input type='text' class='filtro-texto' id='nombre_fichero_excel_resultados_mensuales'>";

            // Selección del fichero
            $control_seleccionar_fichero = "<div class='row-fluid'>
				<div class='span12'>".$idiomas->_("Fichero").": "."</div>
                    <input type='file' id='fichero_importacion_valores_sensor_file' name='file'>
                    <input type='text' id='fichero_importacion_valores_sensor_text'
                        class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' readonly>
                    <button id='boton_importacion_valores_sensor_seleccionar_fichero' class='btn-mini btn btn-success boton-seleccion-fichero-administracion'>...</button>
        		</div>";
            // Lista de sensores
            $control_lista_sensores = dame_control_lista_sensores($id_controles,CLASE_SENSOR_GAS,true,$this->idiomas->_("Sensor"),OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
            
            // Botón Subir fichero
            $boton_subir_fichero = dame_boton_formulario($id_controles."_boton","Subir fichero");
            
            $controles_tabla_subir_fichero = array($control_nombre_fichero);
            array_push($controles_tabla_subir_fichero, $control_seleccionar_fichero);
            array_push($controles_tabla_subir_fichero, $control_lista_sensores);
            array_push($controles_tabla_subir_fichero, $boton_subir_fichero);
            

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-subir-fichero-excel",
                $idiomas->_("Subir ficheros"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => array(18,-1,18,-1)
            );
            $tabla->anyade_fila("subir-fichero-excel-tabla", $controles_tabla_subir_fichero, $params_fila);

            return ($tabla->dame_tabla());
        }

        function dame_resultados_mensuales($medicion)
        {
            // Esto es lo que hay que modificar para a�adir el listado de los ficheros xls            
            $contenido .= "
                <div id='tablaFicherosXLS'>".
                    dame_tabla_ficheros_excel_disponibles()."
                </div>";                            
			return ($contenido);
		
        }


        function dame_herramientas_tarifas($medicion)
		{
            // Se recuperan las características de las tarifas
            $caracteristicas_tarifas = dame_caracteristicas_tarifas_pais_medicion($medicion);

			// Clases de botones dependientes de la medición y país de las tarifas correspondientes
            switch ($medicion)
            {
                case MEDICION_ELECTRICIDAD:
                {
                    // Selección de país
                    $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
                    switch ($pais_tarifas_electricas)
                    {
                        case PAIS_ESPANYA:
                        {
                            $clase_boton_mostrar_ventana_modificar_tarifas = "boton_smartmeter_mostrar_ventana_modificar_tarifas_electricidad_Espanya";
                            break;
                        }

                        case PAIS_PORTUGAL:
                        {
                            //$clase_boton_mostrar_ventana_modificar_tarifas = "boton_smartmeter_mostrar_ventana_modificar_tarifas_electricidad_Espanya";
                            break;
                        }

                        default:
                        {
                            throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                        }
                    }
                    break;
                }
                case MEDICION_GAS:
                {
                    // Selección de país
                    $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
                    switch ($pais_tarifas_gas)
                    {
                        case PAIS_ESPANYA:
                        {
                            $clase_boton_mostrar_ventana_modificar_tarifas = "boton_smartmeter_mostrar_ventana_modificar_tarifas_gas_Espanya";
                            break;
                        }
                        default:
                        {
                            throw new Exception("País de tarifas de gas incorrecto: '".$pais_tarifas_gas."'");
                        }
                    }
                    break;
                }
                case MEDICION_AGUA:
                {
                    // Selección de país
                    $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];
                    switch ($pais_tarifas_agua)
                    {
                        case PAIS_ESPANYA:
                        {
                            $clase_boton_mostrar_ventana_modificar_tarifas = "boton_smartmeter_mostrar_ventana_modificar_tarifas_agua_Espanya";
                            break;
                        }
                        default:
                        {
                            throw new Exception("País de tarifas de agua incorrecto: '".$pais_tarifas_agua."'");
                        }
                    }
                    break;
                }
                default:
                {
                    throw new Exception("Medición desconocida: '".$medicion."'");
                }
            }

            // Se recuperan los controles a mostrar
            $botones = array();
            $numero_columnas_tabla_herramientas_tarifas = NUMERO_COLUMNAS_TABLA_HERRAMIENTAS_TARIFAS;

            // Recalcular datos
            if ($caracteristicas_tarifas["curva_coste"] == true)
            {
                $boton_recalcular_datos = "<br/><button id='boton_recalcular_datos' class='btn-mini btn btn-success boton_smartmeter_mostrar_ventana_recalculo_datos'>".$this->idiomas->_("Recalcular datos")."</button><br/><br/>";
                array_push($botones, $boton_recalcular_datos);
                $numero_columnas_tabla_herramientas_tarifas += 1;
            }

            // Botones visibles siempre
            $boton_modificar_tarifas = "<br/><button id='boton_modificar_tarifas' class='btn-mini btn btn-success ".$clase_boton_mostrar_ventana_modificar_tarifas."'>".$this->idiomas->_("Modificar tarifas")."</button><br/><br/>";
            $boton_asignar_tarifa_grupo_tarifas_sensores = "<br/><button id='boton_asignar_tarifa_grupo_tarifas_sensores' class='btn-mini btn btn-success boton_smartmeter_mostrar_ventana_asignacion_tarifa_grupo_tarifas_sensores'>".$this->idiomas->_("Asignar tarifa o grupo de tarifas")."</button><br/><br/>";
            array_push($botones, $boton_modificar_tarifas);
            array_push($botones, $boton_asignar_tarifa_grupo_tarifas_sensores);

            // Botones opcionales
            switch ($medicion)
            {
                case MEDICION_ELECTRICIDAD:
                {
                    switch ($pais_tarifas_electricas)
                    {
                        case PAIS_ESPANYA:
                        {
                            $boton_exportar_valores_parametros_energia_electrica = "<br/><button id='boton_smartmeter_mostrar_ventana_exportacion_valores_parametros_energia_electrica_Espanya' class='btn-mini btn btn-success boton_smartmeter_mostrar_ventana_exportacion_valores_parametros_energia_electrica_Espanya'>".$this->idiomas->_("Exportar valores de parámetros de energía eléctrica")."</button><br/><br/>";
                            array_push($botones, $boton_exportar_valores_parametros_energia_electrica);
                            $numero_columnas_tabla_herramientas_tarifas += 1;
                            $exportacion_sensores = NodoSensor::dame_exportacion_sensores();
                            if ($exportacion_sensores == true)
                            {
                                $boton_exportar_costes_conceptos_consumo_sensor = "<br/><button id='boton_smartmeter_mostrar_ventana_exportacion_costes_conceptos_consumo_sensor_electricidad_Espanya' class='btn-mini btn btn-success boton_smartmeter_mostrar_ventana_exportacion_costes_conceptos_consumo_sensor_electricidad_Espanya'>".$this->idiomas->_("Exportar costes de conceptos de consumo de un sensor")."</button><br/><br/>";
                                array_push($botones, $boton_exportar_costes_conceptos_consumo_sensor);
                                $numero_columnas_tabla_herramientas_tarifas += 1;
                            }
                            break;
                        }

                        case PAIS_PORTUGAL:
                        {
														// De momento en Portugal no estamos leyendo los parámetros de energía eléctrica, así que no tiene sentido el botón exportar
                            break;
                        }

                    }
                    break;
                }
            }

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-herramientas-tarifas",
                $this->idiomas->_("Herramientas de tarifas"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_fila" => "botones-herramientas",
                "clase_dato" => "boton-herramientas",
                "numero_columnas" => $numero_columnas_tabla_herramientas_tarifas
            );
			$tabla->anyade_fila("botones-herramientas-tarifas", $botones, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_tarifas($medicion)
		{
            $pais_tarifas_electricas = $_SESSION["pais_tarifas_electricas"];
            $pais_tarifas_gas = $_SESSION["pais_tarifas_gas"];
            $pais_tarifas_agua = $_SESSION["pais_tarifas_agua"];

            $contenido = "
                <div id='tabs' class='tabbable'>";
			$contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-tarifas'>".$this->idiomas->_("Tarifas")."</a></li>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-grupos-tarifas'>".$this->idiomas->_("Grupos de tarifas")."</a></li>";
            switch ($medicion)
            {
                case MEDICION_ELECTRICIDAD:
                {
                    switch ($pais_tarifas_electricas)
                    {
                        case PAIS_ESPANYA:
                        {
                            $contenido .= "
                                <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-parametros-energia-electrica'>".$this->idiomas->_("Parámetros de energía eléctrica")."</a></li>";
                            break;
                        }

                        case PAIS_PORTUGAL:
                        {
                            // Los parámetros de energía eléctrica se utilizan para calcular las tarifas indexadas.
                            // En Portugal no los necesitamos
                            break;
                        }

                        default:
                        {
                            throw new Exception("País de tarifas eléctricas incorrecto: '".$pais_tarifas_electricas."'");
                        }
                    }
                    break;
                }
            }
            $contenido .= "
                    </ul>
                    <div id='tabs-tarifas' class='tab-content'>";

			$contenido .= "
                        <div class='tab-pane active' id='tab-tarifas'>";
            switch ($medicion)
            {
                case MEDICION_ELECTRICIDAD:
                {
                    switch ($pais_tarifas_electricas)
                    {
                        case PAIS_ESPANYA:
                        {
                            $contenido .= dame_tabla_filtro_tarifas_tabla_electricidad_Espanya();
                            $contenido .= "
                                <div id='tablaTarifasElectricas'>".
                                    TarifaElectrica_Espanya::dame_tabla_tarifas_electricas(
                                        "",
                                        TIPO_TARIFA_TODOS,
                                        CONTRATO_TARIFA_ELECTRICA_TODOS,
                                        ID_TODOS,
                                        ESTADO_TARIFA_TODOS)."
                                </div>";
                            break;
                        }

                        case PAIS_PORTUGAL:
                        {
                            // Pintamos la tabla de filtro de tarifas que se muestra justo encima de la tabla de tarifas.
                            $contenido .= dame_tabla_filtro_tarifas_tabla_electricidad_Portugal();
                            $contenido .= "
                                <div id='tablaTarifasElectricas'>".
                                    TarifaElectrica_Portugal::dame_tabla_tarifas_electricas(
                                        "",
                                        TIPO_TARIFA_TODOS,
                                        CICLO_TARIFA_ELECTRICA_PORTUGAL_TODOS,
                                        ID_TODOS,
                                        ESTADO_TARIFA_TODOS)."
                                </div>";
                            break;
                        }

                    }
                    break;
                }
                case MEDICION_GAS:
                {
                    switch ($pais_tarifas_gas)
                    {
                        case PAIS_ESPANYA:
                        {
                            $contenido .= dame_tabla_filtro_tarifas_tabla_gas_Espanya();
                            $contenido .= "
                                <div id='tablaTarifasGas'>".
                                    TarifaGas_Espanya::dame_tabla_tarifas_gas(
                                        "",
                                        TIPO_TARIFA_TODOS,
                                        ID_TODOS,
                                        ESTADO_TARIFA_TODOS)."
                                </div>";
                            break;
                        }
                    }
                    break;
                }
                case MEDICION_AGUA:
                {
                    switch ($pais_tarifas_agua)
                    {
                        case PAIS_ESPANYA:
                        {
                            $contenido .= dame_tabla_filtro_tarifas_tabla_agua_Espanya();
                            $contenido .= "
                                <div id='tablaTarifasAgua'>".
                                    TarifaAgua_Espanya::dame_tabla_tarifas_agua(
                                        "",
                                        TIPO_TARIFA_TODOS,
                                        ID_TODOS,
                                        ESTADO_TARIFA_TODOS)."
                                </div>";
                            break;
                        }
                    }
                    break;
                }
                default:
                {
                    throw new Exception("Medición desconocida: '".$medicion."'");
                }
            }
            $contenido .= "
                        </div>";

            $contenido .= "
                        <div class='tab-pane' id='tab-grupos-tarifas'>";
            $contenido .= $this->dame_tabla_filtro_grupos_tarifas_tabla();
            $contenido .= "
                            <div id='tablaGruposTarifas'>".
                                GrupoTarifas::dame_tabla_grupos_tarifas(
                                    $medicion,
                                    "",
                                    ESTADO_TARIFA_TODOS)."
                            </div>
                        </div>";

            switch ($medicion)
            {
                case MEDICION_ELECTRICIDAD:
                {
                    switch ($pais_tarifas_electricas)
                    {
                        case PAIS_ESPANYA:
                        {
                            $contenido .= "
                                <div class='tab-pane' id='tab-parametros-energia-electrica'>";
                            $tabla_informacion_parametros_energia_electrica = dame_tabla_informacion_parametros_energia_electricidad_Espanya();
                            $contenido .= $tabla_informacion_parametros_energia_electrica->dame_tabla();
                            $contenido .= "
                                </div>";
                            break;
                        }

                        case PAIS_PORTUGAL:
                        {
                            // En portugal no tenemos parámetros de energía eléctrica, así que no tiene sentido la pestaña
                            break;
                        }

                    }
                    break;
                }
            }

            $contenido .= "
					</div>
				</div>";

			return ($contenido);
		}


        //
        // Funciones auxiliares para obtener el contenido de las secciones
        //


        function dame_tabla_filtro_grupos_tarifas_tabla()
		{
            // Se recuperan los controles a mostrar
            $controles = array();
            $id_controles = "smartmeter_filtro_grupos_tarifas_tabla";

            // Estado de tarifas (de expiración)
            $control_lista_estados .= dame_control_lista_estados_tarifa($id_controles, $this->idiomas->_("Estado"));
            array_push($controles, $control_lista_estados);

            // Nombre
			$filtro_grupos_tarifas_electricas = dame_filtro_texto_controles_extra($id_controles, $this->idiomas->_("Nombre"), $controles);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-smartmeter-filtro-grupos-tarifas-tabla",
                $this->idiomas->_("Filtro de grupos"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_GRUPOS_TARIFAS_TABLA)
            );
			$tabla->anyade_fila("filtro-grupos-tarifas-tabla", $filtro_grupos_tarifas_electricas, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_controles_listas_sensores_tarifas(
            $id_controles,
            $clase_sensor,
            $medicion,
            $mostrar_etiquetas,
            $opciones_extra)
        {
            $control_lista_sensores = dame_control_lista_sensores(
                $id_controles,
                $clase_sensor,
                $mostrar_etiquetas,
                $this->idiomas->_("Sensor"),
                OPCIONES_EXTRA_LISTA_NODOS_NINGUNO);
            $control_lista_tarifas = dame_control_lista_tarifas(
                $id_controles,
                $medicion,
                ID_NINGUNO,
                $mostrar_etiquetas,
                $opciones_extra);

            $controles_listas = array(
                $control_lista_sensores,
                $control_lista_tarifas
            );
            return ($controles_listas);
        }


        function dame_control_lista_intervalos_valores_consumos_costes($id_controles, $clase_sensor)
        {
            $lista_intervalos_valores = dame_lista_intervalos_valores_informes_consumos_costes_clase_sensor($clase_sensor, INTERVALO_VALORES_HORA);
            $control_lista_intervalos_valores = dame_control_lista(
                $id_controles,
                "intervalo_valores",
                $this->idiomas->_("Intervalo de valores"),
                $lista_intervalos_valores,
                "filtro-desplegable");
            return ($control_lista_intervalos_valores);
        }


        function dame_control_lista_tipos_autoconsumo($id_controles)
        {
            $control_lista_tipos_autoconsumo = dame_control_lista_valores(
                $id_controles,
                "tipo_autoconsumo",
                $this->idiomas->_("Tipo de autoconsumo"),
                array(
                    array(TIPO_AUTOCONSUMO_SIN_ACUMULACION, dame_descripcion_tipo_autoconsumo(TIPO_AUTOCONSUMO_SIN_ACUMULACION)),
                    array(TIPO_AUTOCONSUMO_CON_ACUMULACION, dame_descripcion_tipo_autoconsumo(TIPO_AUTOCONSUMO_CON_ACUMULACION))),
                TIPO_AUTOCONSUMO_SIN_ACUMULACION,
                "filtro-desplegable");
            return ($control_lista_tipos_autoconsumo);
        }


        function dame_control_lista_granularidades_excesos_potencia($id_controles)
        {
            $lista_granularidades = dame_lista_granularidades_informe_excesos_potencia(GRANULARIDAD_CUARTOHORARIA);
            $control_lista_granularidades = dame_control_lista(
                $id_controles,
                "granularidad",
                $this->idiomas->_("Granularidad"),
                $lista_granularidades,
                "filtro-desplegable");
            return ($control_lista_granularidades);
        }


        function dame_control_lista_rangos_potencias($id_controles)
        {
            $control_lista_rangos_potencias = dame_control_lista_valores(
                $id_controles,
                "rango_potencias",
                $this->idiomas->_("Rango de potencias"),
                array(
                    array(RANGO_POTENCIAS_MINIMO, dame_descripcion_rango_potencias(RANGO_POTENCIAS_MINIMO)),
                    array(RANGO_POTENCIAS_MEDIO, dame_descripcion_rango_potencias(RANGO_POTENCIAS_MEDIO)),
                    array(RANGO_POTENCIAS_MAXIMO, dame_descripcion_rango_potencias(RANGO_POTENCIAS_MAXIMO))),
                RANGO_POTENCIAS_MAXIMO,
                "filtro-desplegable");
            return ($control_lista_rangos_potencias);
        }


        function dame_controles_listas_tarifas_electricas_optimizador_simulador_potencias_manual($id_controles)
        {
            $control_lista_tarifas_electricas = dame_control_lista_tarifas(
                $id_controles,
                MEDICION_ELECTRICIDAD,
                ID_NINGUNO,
                true,
                OPCIONES_EXTRA_LISTA_TARIFAS_ELECTRICAS_TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES);

            $controles_listas = array(
                $control_lista_tarifas_electricas
            );
            return ($controles_listas);
        }


        function dame_controles_filtro_optimizador_simulador_potencias_manual($id_controles)
        {
            $botones_seleccion_fichero_descarga_plantilla = "
                <div class='row-fluid'>
                    <div class='span12'>
                        <span class='titulo-campo-administracion'>".$this->idiomas->_("Fichero de potencias máximas").": "."</span><br/>
                        <input type='file' id='fichero_".$id_controles."_file'>
                        <input type='text' id='fichero_".$id_controles."_text'
                            class='nombre-fichero-filtro-informes' readonly>
                        <button id='boton_".$id_controles."_seleccion_fichero'
                            class='boton-seleccion-fichero-administracion btn-mini btn btn-success'>...</button>
                        <button id='boton_".$id_controles."_descargar_plantilla_fichero'
                            class='boton-seleccion-fichero-administracion btn-mini btn btn-success'>".$this->idiomas->_("Descargar plantilla")."</button>
                    </div>
                    <div id='fichero_".$id_controles."_oculto' nombre='' hidden></div>
                </div>";
            $controles_filtro_optimizador_simulador_potencias_manual = array(
                $botones_seleccion_fichero_descarga_plantilla);

            $boton_ver_informe = dame_boton_formulario($id_controles."_ver_informe", $this->idiomas->_("Ver informe"));
            $boton_generar_pdf = dame_boton_generar_pdf($id_controles, true);
            array_push($controles_filtro_optimizador_simulador_potencias_manual, $boton_ver_informe);
            array_push($controles_filtro_optimizador_simulador_potencias_manual, $boton_generar_pdf);
            return ($controles_filtro_optimizador_simulador_potencias_manual);
        }


        function dame_controles_potencias_simulador_potencias($id_controles)
        {
            $idiomas = new Idiomas();

            $controles_potencias = array();
            for ($i = 1; $i <= NUMERO_MAXIMO_TRAMOS_TARIFA_ELECTRICA; $i++)
            {
                $control_potencia = "<div id='etiqueta_potencia_manual_tramo_".$i."_".$id_controles."'>".$idiomas->_("Potencia de tramo")." ".$i." (".$idiomas->_("kW").")</div>
                    <input size='5' type='text' class='input-texto-informes-mediano' id='potencia_manual_tramo_".$i."_".$id_controles."' value='0'>";
                array_push($controles_potencias, $control_potencia);
            }
            return ($controles_potencias);
        }


        function dame_controles_potencias_sin_tarifa_electrica_seleccionada_simulador_potencias($id_controles)
        {
            $idiomas = new Idiomas();

            $controles_potencias = array();
            $control_texto_ninguna_tarifa_electrica = "<div id='texto_ninguna_tarifa_electrica_".$id_controles."'><i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay tarifa seleccionada")."</div>";
            array_push($controles_potencias, $control_texto_ninguna_tarifa_electrica);
            return ($controles_potencias);
        }


        function dame_control_lista_tipos_perfil_horario_prevision_compra_energia($id_controles)
        {
            $control_lista_tipos_perfil_horario_prevision_compra_energia = dame_control_lista_valores(
                $id_controles,
                "tipo_perfil_horario",
                $this->idiomas->_("Tipo de perfil horario"),
                array(
                    array(TIPO_PERFIL_HORARIO_SEMANAL, dame_descripcion_tipo_perfil_horario(TIPO_PERFIL_HORARIO_SEMANAL)),
                    array(TIPO_PERFIL_HORARIO_DIARIO, dame_descripcion_tipo_perfil_horario(TIPO_PERFIL_HORARIO_DIARIO)),
                    array(TIPO_PERFIL_HORARIO_CONFIGURABLE, dame_descripcion_tipo_perfil_horario(TIPO_PERFIL_HORARIO_CONFIGURABLE))),
                NULL,
                "filtro-desplegable");
            return ($control_lista_tipos_perfil_horario_prevision_compra_energia);
        }


        function dame_controles_listas_tarifas_gas_optimizador_simulador_caudales_manual($id_controles)
        {
            $control_lista_tarifas_gas = dame_control_lista_tarifas(
                $id_controles,
                MEDICION_GAS,
                ID_NINGUNO,
                true,
                OPCIONES_EXTRA_LISTA_TARIFAS_GAS_TIPO_CALCULO_COSTE_TERMINO_FIJO_CON_EXCESOS);

            $controles_listas = array(
                $control_lista_tarifas_gas
            );
            return ($controles_listas);
        }


        function dame_controles_filtro_optimizador_simulador_caudales_manual($id_controles)
        {
            $botones_seleccion_fichero_descarga_plantilla = "
                <div class='row-fluid'>
                    <div class='span12'>
                        <span class='titulo-campo-administracion'>".$this->idiomas->_("Fichero de caudales máximos").": "."</span><br/>
                        <input type='file' id='fichero_".$id_controles."_file'>
                        <input type='text' id='fichero_".$id_controles."_text'
                            class='nombre-fichero-filtro-informes' readonly>
                        <button id='boton_".$id_controles."_seleccion_fichero'
                            class='boton-seleccion-fichero-administracion btn-mini btn btn-success'>...</button>
                        <button id='boton_".$id_controles."_descargar_plantilla_fichero'
                            class='boton-seleccion-fichero-administracion btn-mini btn btn-success'>".$this->idiomas->_("Descargar plantilla")."</button>
                    </div>
                    <div id='fichero_".$id_controles."_oculto' nombre='' hidden></div>
                </div>";
            $controles_filtro_optimizador_simulador_caudales_manual = array(
                $botones_seleccion_fichero_descarga_plantilla);

            $boton_ver_informe = dame_boton_formulario($id_controles."_ver_informe", $this->idiomas->_("Ver informe"));
            $boton_generar_pdf = dame_boton_generar_pdf($id_controles, true);
            array_push($controles_filtro_optimizador_simulador_caudales_manual, $boton_ver_informe);
            array_push($controles_filtro_optimizador_simulador_caudales_manual, $boton_generar_pdf);
            return ($controles_filtro_optimizador_simulador_caudales_manual);
        }


        function dame_controles_caudales_simulador_caudales($id_controles)
        {
            $idiomas = new Idiomas();

            $controles_caudales = array();
            $control_caudal = "<div id='etiqueta_caudal_manual_".$id_controles."'>".$idiomas->_("Caudal diario")." (".$idiomas->_("kWh").")</div>
                <input size='5' type='text' class='input-texto-informes-mediano' id='caudal_manual_".$id_controles."' value='0'>";
            array_push($controles_caudales, $control_caudal);
            return ($controles_caudales);
        }


        function dame_controles_caudales_sin_tarifa_gas_seleccionada_simulador_caudales($id_controles)
        {
            $idiomas = new Idiomas();

            $controles_potencias = array();
            $control_texto_ninguna_tarifa_gas = "<div id='texto_ninguna_tarifa_gas_".$id_controles."'><i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay tarifa seleccionada")."</div>";
            array_push($controles_potencias, $control_texto_ninguna_tarifa_gas);
            return ($controles_potencias);
        }
	}
?>
