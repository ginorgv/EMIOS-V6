<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/util_informes_consumos_costes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/agua/util_informes_consumos_costes_agua.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/agua/Espanya/util_informes_consumos_costes_agua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/agua/Espanya/util_informes_facturas_agua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/agua/util_agua.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/InformesFichero/util_informes_personalizados_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/TarifaAgua_Espanya.php');


    //
    // Funciones de información de informes personalizados (agua - España)
    //


    // Devuelve el estudio general de un sensor
    function dame_estudio_general_sensor_agua_Espanya($parametros)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Parámetros
        $medicion = $parametros["medicion"];
        $id_ratio = $parametros["id_ratio"];
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $apartados = $parametros["apartados"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        $minutos_desfase_utc = $parametros["minutos_desfase_utc"];
        $tipo_informe = $parametros["tipo_informe"];

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Inicialización de resultados
        $datos_instalacion = array();
        $datos_portada = array();
        $datos_analisis_consumo = array();
        $datos_excesos_caudal = array();
        $datos_simulacion_factura = array();

        // Se recuperan las filas de valores del sensor (por horas y cuartos de hora)
        $parametros_filas_valores_sensor_horas = array(
            "id_sensor" => $id_sensor,
            "intervalo_valores" => INTERVALO_VALORES_HORA,
            "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
            "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local);
        $filas_valores_sensor_horas = dame_filas_valores_sensor($parametros_filas_valores_sensor_horas);

        // Se recuperan las filas de valores del sensor (por días)
        $parametros_filas_valores_sensor_dias = $parametros_filas_valores_sensor_horas;
        $parametros_filas_valores_sensor_dias["intervalo_valores"] = INTERVALO_VALORES_DIA;
        $filas_valores_sensor_dias = dame_filas_valores_sensor($parametros_filas_valores_sensor_dias);

        // Se recupera el identificador de tarifa del sensor
        $id_tarifa = dame_id_tarifa_id_sensor_fecha($id_sensor, $cadena_fecha_hora_inicio_local_local);

        // Portada
        if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_PORTADA_AGUA_ESPANYA, $apartados) == true)
        {
            // Nombre de red
            $consulta_nombre_red = "
                SELECT nombre
                FROM redes
                WHERE
                   id = '".$_SESSION["id_red"]."'";
            $res_nombre_red = $bd_red->ejecuta_consulta($consulta_nombre_red);
            if ($res_nombre_red == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_nombre_red."'");
            }
            $fila_nombre_red = $res_nombre_red->dame_siguiente_fila();
            $nombre_red = $fila_nombre_red["nombre"];

            // Descripción de sensor
            $consulta_descripcion_sensor = "
                SELECT
                    descripcion
                FROM sensores
                WHERE
                    id = '".$bd_red->_($id_sensor)."'";
            $res_descripcion_sensor = $bd_red->ejecuta_consulta($consulta_descripcion_sensor);
            if ($res_descripcion_sensor == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_descripcion_sensor."'");
            }
            $fila_descripcion_sensor = $res_descripcion_sensor->dame_siguiente_fila();
            $descripcion_sensor = $fila_descripcion_sensor["descripcion"];
            if ($descripcion_sensor == "")
            {
                $descripcion_sensor = $nombre_sensor;
            }

            // Fechas de inicio y fin
            $cadena_fecha_inicio_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);
            $cadena_fecha_fin_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);
            $cadena_fechas_locales_locales = $cadena_fecha_inicio_local_local." - ".$cadena_fecha_fin_local_local;

            // Datos del apartado
            $datos_portada["nombre_red"] = htmlspecialchars(strtoupper($nombre_red), ENT_QUOTES);
            $datos_portada["descripcion_sensor"] = htmlspecialchars($descripcion_sensor, ENT_QUOTES);
            $datos_portada["cadena_fechas"] = $cadena_fechas_locales_locales;
        }

        // Recuperación de información para el apartado de instalación
        if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_INSTALACION_AGUA_ESPANYA, $apartados) == true)
        {
            $datos_instalacion = dame_datos_instalacion_sensor(MEDICION_AGUA, $id_sensor, $id_tarifa);
        }

        // Análisis de consumo
        if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_AGUA_ESPANYA, $apartados) == true)
        {
            // Información de agua (consumo por días)
            $parametros_informacion_sensor_agua_consumo_dias = array(
                "id_ratio" => $id_ratio,
                "clase_sensor" => CLASE_SENSOR_AGUA,
                "id_sensor" => $id_sensor,
                "nombre_sensor" => $nombre_sensor,
                "campo" => CAMPO_INCREMENTO,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
                "intervalo_valores" => INTERVALO_VALORES_DIA,
                "tipo_mapa_calor" => TIPO_MAPA_CALOR_NINGUNO,
                "minutos_desfase_utc" => $minutos_desfase_utc,
                "tipo_informe" => $tipo_informe);
            $res_informacion_sensor_agua_consumo_dias = dame_informacion_sensor_agua(
                $parametros_informacion_sensor_agua_consumo_dias,
                $filas_valores_sensor_dias);

            // Datos del apartado (gráfica de consumos)
            $datos_analisis_consumo["hay_datos_grafica_consumos"] = $res_informacion_sensor_agua_consumo_dias["hay_datos"];
            if ($datos_analisis_consumo["hay_datos_grafica_consumos"] == true)
            {
                $datos_analisis_consumo["max_consumo"] = $res_informacion_sensor_agua_consumo_dias["max_valor"];
                $datos_analisis_consumo["etiquetas_grafica_consumos"] = $res_informacion_sensor_agua_consumo_dias["etiquetas_graficas"];
                $datos_analisis_consumo["grafica_consumos"] = $res_informacion_sensor_agua_consumo_dias["grafica_valores"];
            }

            // Información de agua (consumo por horas)
            $parametros_informacion_sensor_agua_consumo_horas = array(
                "id_ratio" => $id_ratio,
                "clase_sensor" => CLASE_SENSOR_AGUA,
                "id_sensor" => $id_sensor,
                "nombre_sensor" => $nombre_sensor,
                "campo" => CAMPO_INCREMENTO,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local,
                "intervalo_valores" => INTERVALO_VALORES_HORA,
                "tipo_mapa_calor" => TIPO_MAPA_CALOR_SEMANAL,
                "minutos_desfase_utc" => $minutos_desfase_utc,
                "tipo_informe" => $tipo_informe);
            $res_informacion_sensor_agua_consumo_horas = dame_informacion_sensor_agua(
                $parametros_informacion_sensor_agua_consumo_horas,
                $filas_valores_sensor_horas);

            // Datos del apartado (mapa de calor de consumos)
            $datos_analisis_consumo["hay_datos_mapa_calor_consumos"] = $res_informacion_sensor_agua_consumo_horas["hay_datos"];
            if ($datos_analisis_consumo["hay_datos_mapa_calor_consumos"] == true)
            {
                $datos_analisis_consumo["colores_mapa_calor_consumos"] = $res_informacion_sensor_agua_consumo_horas["colores_mapa_calor_valores"];
                $datos_analisis_consumo["dias_mapa_calor_consumos"] = $res_informacion_sensor_agua_consumo_horas["dias_mapa_calor_valores"];
                $datos_analisis_consumo["datos_mapa_calor_consumos"] = $res_informacion_sensor_agua_consumo_horas["datos_mapa_calor_valores"];
                $datos_analisis_consumo["unidad_medida_consumo"] = $res_informacion_sensor_agua_consumo_horas["unidad_medida"];
            }
        }

        // Análisis de consumo
        if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_AGUA_ESPANYA, $apartados) == true)
        {
            // Información de los periodos
            $info_periodos = dame_info_periodos_fechas_inicio_fin_parametros_duracion_separacion_periodos(
                $cadena_fecha_hora_inicio_local_local,
                $cadena_fecha_hora_fin_local_local,
                NULL);
            $cadena_fecha_hora_inicio_anterior_local_local = $info_periodos["cadena_fecha_hora_inicio_anterior_local_local"];
            $cadena_fecha_hora_inicio_posterior_local_local = $info_periodos["cadena_fecha_hora_inicio_posterior_local_local"];
            $numero_dias_periodo = $info_periodos["numero_dias_periodo"];

            // Información de comparación de periodos (por días)
            $parametros_consumos_costes_sensor_periodos = array(
                "medicion" => $medicion,
                "id_ratio" => $id_ratio,
                "clase_sensor" => CLASE_SENSOR_AGUA,
                "id_sensor" => $id_sensor,
                "nombre_sensor" => $nombre_sensor,
                "fecha_hora_inicio_anterior" => $cadena_fecha_hora_inicio_anterior_local_local,
                "fecha_hora_inicio_posterior" => $cadena_fecha_hora_inicio_posterior_local_local,
                "numero_dias_periodo" => $numero_dias_periodo,
                "intervalo_valores" => INTERVALO_VALORES_DIA,
                "minutos_desfase_utc" => $minutos_desfase_utc);
            $res_consumos_costes_sensor_periodos = dame_consumos_costes_sensor_periodos($parametros_consumos_costes_sensor_periodos);

            // Datos del apartado (consumo por periodos)
            if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_AGUA_ESPANYA, $apartados) == true)
            {
                $datos_analisis_consumo["hay_datos_consumos_periodos"] = $res_consumos_costes_sensor_periodos["hay_datos"];
                if ($datos_analisis_consumo["hay_datos_consumos_periodos"] == true)
                {
                    $datos_analisis_consumo["max_consumo_periodos"] = $res_consumos_costes_sensor_periodos["max_consumo"];
                    $datos_analisis_consumo["etiquetas_consumos_periodos"] = $res_consumos_costes_sensor_periodos["etiquetas"];
                    $datos_analisis_consumo["etiquetas_tooltips_consumos_periodos"] = $res_consumos_costes_sensor_periodos["etiquetas_tooltips"];
                    $datos_analisis_consumo["min_fecha_consumo_periodos"] = $res_consumos_costes_sensor_periodos["min_fecha_consumo"];
                    $datos_analisis_consumo["max_fecha_consumo_periodos"] = $res_consumos_costes_sensor_periodos["max_fecha_consumo"];
                    $datos_analisis_consumo["grafica_consumos_periodos"] = $res_consumos_costes_sensor_periodos["grafica_consumos"];
                }
            }
        }

        // Simulación de factura
        if (in_array(APARTADO_INFORME_ESTUDIO_GENERAL_SIMULACION_FACTURA_AGUA_ESPANYA, $apartados) == true)
        {
            // Flag para recuperar la información de simulación de factura
            $recuperar_datos_simulacion_factura = true;
            if ($recuperar_datos_simulacion_factura == true)
            {
                $recuperar_datos_simulacion_factura = ($id_tarifa != ID_NINGUNO);
            }

            // Datos del apartado (simulación de factura)
            $datos_simulacion_factura["hay_datos_simulacion_factura"] = $recuperar_datos_simulacion_factura;
            if ($datos_simulacion_factura["hay_datos_simulacion_factura"] == true)
            {
                // Información de simulación de factura
                $parametros_simulacion_factura_sensor_tarifa = array(
                    "id_sensor" => $id_sensor,
                    "nombre_sensor" => $nombre_sensor,
                    "id_tarifa" => $id_tarifa,
                    "fecha_hora_inicio" => $cadena_fecha_hora_inicio_local_local,
                    "fecha_hora_fin" => $cadena_fecha_hora_fin_local_local);
                $res_simulacion_factura_sensor_tarifa = dame_simulacion_factura_sensor_tarifa_agua_Espanya(
                    $parametros_simulacion_factura_sensor_tarifa);

                // Datos del apartado
                $datos_simulacion_factura["hay_datos"] = $res_simulacion_factura_sensor_tarifa["hay_datos"];
                if ($datos_simulacion_factura["hay_datos"] == true)
                {
                    $datos_simulacion_factura["tabla_coste_consumo"] = $res_simulacion_factura_sensor_tarifa["tabla_coste_consumo"];
                    $datos_simulacion_factura["tabla_consumo"] = $res_simulacion_factura_sensor_tarifa["tabla_consumo"];
                    $datos_simulacion_factura["tabla_termino_fijo"] = $res_simulacion_factura_sensor_tarifa["tabla_termino_fijo"];
                    $datos_simulacion_factura["tabla_otros_conceptos"] = $res_simulacion_factura_sensor_tarifa["tabla_otros_conceptos"];
                }
            }
        }

        // Se devuelve el resultado
        $resultado = array(
            "res" => "OK",
            "datos_portada" => $datos_portada,
            "datos_instalacion" => $datos_instalacion,
            "datos_analisis_consumo" => $datos_analisis_consumo,
            "datos_excesos_caudal" => $datos_excesos_caudal,
            "datos_simulacion_factura" => $datos_simulacion_factura);
        return ($resultado);
    }


    //
    // Funciones para los apartados de informes personalizados
    //


    // Crea los controles de texto para los apartados de estudio general
    function dame_controles_textos_estudio_general_agua_Espanya()
    {
        $idiomas = new Idiomas();

        $numero_maximo_caracteres = NUMERO_MAXIMO_CARACTERES_TEXTO;
        $controles_textos = "
            <div id='textos-smartmeter-estudio-general' class='controles-textos-informe'>
                <div class='contenedor-texto-informe-sin-margen-superior'>
                    <span>".$idiomas->_('Texto de introducción').": "."</span>".
                    "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".$numero_maximo_caracteres."'>".
                        "(0 "." / ".$numero_maximo_caracteres.")"."</span><br/>
                    <textarea class='area-texto-informe' id='texto-introduccion-estudio-general' rows='1'></textarea>
                </div>
            </div>";
        return ($controles_textos);
    }


    // Devuelve la lista de apartados del informe de estudio general
    function dame_lista_apartados_estudio_general_agua_Espanya()
    {
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_PORTADA_AGUA_ESPANYA."' sort_id='01'>".dame_descripcion_apartado_estudio_general_agua_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_PORTADA_AGUA_ESPANYA)."</option>";
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_INTRODUCCION_AGUA_ESPANYA."' sort_id='02'>".dame_descripcion_apartado_estudio_general_agua_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_INTRODUCCION_AGUA_ESPANYA)."</option>";
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_INSTALACION_AGUA_ESPANYA."' sort_id='03'>".dame_descripcion_apartado_estudio_general_agua_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_INSTALACION_AGUA_ESPANYA)."</option>";
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_AGUA_ESPANYA."' sort_id='04'>".dame_descripcion_apartado_estudio_general_agua_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_AGUA_ESPANYA)."</option>";
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_SIMULACION_FACTURA_AGUA_ESPANYA."' sort_id='08'>".dame_descripcion_apartado_estudio_general_agua_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_SIMULACION_FACTURA_AGUA_ESPANYA)."</option>";
        $lista_apartados .= "<option value='".APARTADO_INFORME_ESTUDIO_GENERAL_CONCLUSIONES_AGUA_ESPANYA."' sort_id='09'>".dame_descripcion_apartado_estudio_general_agua_Espanya(APARTADO_INFORME_ESTUDIO_GENERAL_CONCLUSIONES_AGUA_ESPANYA)."</option>";

        return ($lista_apartados);
    }


    // Devuelve la descripción de los apartados del informe de estudio general
    function dame_descripcion_apartado_estudio_general_agua_Espanya($apartado)
    {
        switch ($apartado)
        {
            case APARTADO_INFORME_ESTUDIO_GENERAL_PORTADA_AGUA_ESPANYA:
            {
                $descripcion = "Portada";
                break;
            }
            case APARTADO_INFORME_ESTUDIO_GENERAL_INTRODUCCION_AGUA_ESPANYA:
            {
                $descripcion = "Introducción";
                break;
            }
            case APARTADO_INFORME_ESTUDIO_GENERAL_INSTALACION_AGUA_ESPANYA:
            {
                $descripcion = "Instalación";
                break;
            }
            case APARTADO_INFORME_ESTUDIO_GENERAL_ANALISIS_CONSUMO_AGUA_ESPANYA:
            {
                $descripcion = "Análisis de consumo";
                break;
            }
            case APARTADO_INFORME_ESTUDIO_GENERAL_SIMULACION_FACTURA_AGUA_ESPANYA:
            {
                $descripcion = "Simulación de factura";
                break;
            }
            case APARTADO_INFORME_ESTUDIO_GENERAL_CONCLUSIONES_AGUA_ESPANYA:
            {
                $descripcion = "Conclusiones";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_(htmlspecialchars($descripcion, ENT_QUOTES)));
    }


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_smartmeter_estudio_general_agua_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-estudio-general'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-estudio-general' hidden>
                        <div id='apartado_portada_estudio_general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-portada-estudio-general' class='titulo-informe'>".$idiomas->_("Portada")."</div>
                            </div>
                            <div class='texto-grande-portada-informe' id='nombre-red-portada-estudio-general'></div>
                            <div class='texto-mediano-portada-informe' id='descripcion-sensor-portada-estudio-general'></div>
                            <div class='texto-pequenyo-portada-informe' id='fechas-portada-estudio-general'></div>
                        </div>

                        <div id='apartado-introduccion-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-introduccion-estudio-general' class='titulo-informe'>".$idiomas->_("Introducción")."</div>
                            </div>
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".$idiomas->_('Introducción').": "."</span><br/>
                                <textarea readonly class='area-texto-informe' id='introduccion-estudio-general' rows='1'></textarea>
                            </div>
                        </div>

                        <div id='apartado-instalacion-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-instalacion-estudio-general' class='titulo-informe'>".$idiomas->_("Instalación")."</div>
                            </div>
                            <table class='tabla-parametros'>
                                <tbody>
                                    <tr>
                                        <td style='width:15%'><b>".$idiomas->_("CUPS").":"."</b></td>
                                        <td style='width:85%' id='cups-instalacion-estudio-general'></td>
                                    </tr>
                                    <tr>
                                        <td style='width:15%'><b>".$idiomas->_("Descripción").":"."</b></td>
                                        <td style='width:85%' id= 'descripcion-instalacion-estudio-general'></td>
                                    </tr>
                                    <tr>
                                        <td style='width:15%'><b>".$idiomas->_("Tipo").":"."</b></td>
                                        <td style='width:85%' id= 'tipo-instalacion-estudio-general'></td>
                                    </tr>
                                    <tr>
                                        <td style='width:15%'><b>".$idiomas->_("Fecha de inicio").":"."</b></td>
                                        <td style='width:85%' id='fecha-inicio-instalacion-estudio-general'></td>
                                    </tr>
                                    <tr>
                                        <td style='width:15%'><b>".$idiomas->_("Fecha de fin").":"."</b></td>
                                        <td style='width:85%' id='fecha-fin-instalacion-estudio-general'>/td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class='tabla-datos100' id='contenedor-tabla-tramos-tarifa-agua-instalacion-estudio-general'></div>
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".$idiomas->_('Notas').": "."</span>".
                                "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".NUMERO_MAXIMO_CARACTERES_NOTAS."'>".
                                    "(0"." / ".NUMERO_MAXIMO_CARACTERES_NOTAS.")"."</span><br/>
                                <textarea class='area-texto-informe' id='notas-instalacion-estudio-general' rows='1'></textarea>
                            </div>
                        </div>

                        <div id='apartado-analisis-consumo-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-analisis-consumo-estudio-general' class='titulo-informe'>".$idiomas->_("Análisis de consumo")."</div>
                            </div>
                            <div class='grafica100' id='grafica-consumos-analisis-consumo-estudio-general'></div>
                            <div class='grafica100' id='grafica-consumos-periodos-analisis-consumo-estudio-general'></div>
                            <div class='mapa-calor100' id='mapa-calor-semanal-consumos-analisis-consumo-estudio-general'></div>
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".$idiomas->_('Notas').": "."</span>".
                                "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".NUMERO_MAXIMO_CARACTERES_NOTAS."'>".
                                    "(0"." / ".NUMERO_MAXIMO_CARACTERES_NOTAS.")"."</span><br/>
                                <textarea class='area-texto-informe' id='notas-analisis-consumo-estudio-general' rows='1'></textarea>
                            </div>
                        </div>

                        <div id='apartado-simulacion-factura-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-simulacion-factura-estudio-general' class='titulo-informe'>".$idiomas->_("Simulación de factura")."</div>
                            </div>
                            <div class='titulo-tabla-datos100' id='titulo-resumen-simulacion-factura-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-coste-consumo-simulacion-factura-estudio-general'></div>
                            <div class='titulo-tabla-datos100' id='titulo-detalles-simulacion-factura-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-consumo-simulacion-factura-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-termino-fijo-simulacion-factura-estudio-general'></div>
                            <div class='tabla-datos100' id='contenedor-tabla-otros-conceptos-simulacion-factura-estudio-general'></div>
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".$idiomas->_('Notas').": "."</span>".
                                "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".NUMERO_MAXIMO_CARACTERES_NOTAS."'>".
                                    "(0"." / ".NUMERO_MAXIMO_CARACTERES_NOTAS.")"."</span><br/>
                                <textarea class='area-texto-informe' id='notas-simulacion-factura-estudio-general' rows='1'></textarea>
                            </div>
                        </div>

                        <div id='apartado-conclusiones-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-conclusiones-estudio-general' class='titulo-informe'>".$idiomas->_("Conclusiones")."</div>
                            </div>
                            <div class='contenedor-texto-informe'>
                                <span class='titulo-texto-informe'>".$idiomas->_('Conclusiones').": "."</span>".
                                "<span class='titulo-campo-administracion contador-caracteres-textarea' numero_maximo_caracteres='".NUMERO_MAXIMO_CARACTERES_NOTAS."'>".
                                    "(0"." / ".NUMERO_MAXIMO_CARACTERES_NOTAS.")"."</span><br/>
                                <textarea class='area-texto-informe' id='conclusiones-estudio-general' rows='1'></textarea>
                            </div>
                        </div>

                        <div id='apartado-avisos-estudio-general' class='apartado-informe'>
                            <div class='contenedor-titulo-informe'>
                                <div id='titulo-avisos-estudio-general' class='titulo-informe'>".$idiomas->_("Avisos")."</div>
                            </div>
                            <div class='texto100' id='texto-avisos-estudio-general'></div>
                        </div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Portada
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-portada'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='texto-grande-portada-informe-informe-fichero-con-salto-pagina' id='nombre-red-portada-estudio-general'></div>
                        <div class='texto-mediano-portada-informe-informe-fichero' id='descripcion-sensor-portada-estudio-general'></div>
                        <div class='texto-pequenyo-portada-informe-informe-fichero' id='fechas-portada-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Introducción
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-introduccion-informe-fichero-estudio-general'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-introduccion'></div>
                        <div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='introduccion-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Instalación
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-instalacion'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-instalacion'></div>
                        <table class='tabla-parametros-informe-fichero'>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("CUPS").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='cups-instalacion-estudio-general'></td>
                            </tr>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Descripción").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='descripcion-instalacion-estudio-general'></td>
                            </tr>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Tipo").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='tipo-instalacion-estudio-general'></td>
                            </tr>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de inicio").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='fecha-inicio-instalacion-estudio-general'></td>
                            </tr>
                            <tr>
                                <td class='titulo-parametro-informe-fichero'><b>".$idiomas->_("Fecha de fin").":"."</b></td>
                                <td class='contenido-parametro-informe-fichero' id='fecha-fin-instalacion-estudio-general'></td>
                            </tr>
                        </table>
                        <br/>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-tramos-tarifa-agua-instalacion-estudio-general'></div>
                        <div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='notas-instalacion-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de análisis de consumo
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-analisis-consumo'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-analisis-consumo'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-analisis-consumo-estudio-general'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-consumos-periodos-analisis-consumo-estudio-general'></div>
                        <div class='mapa-calor100-informe-fichero separacion-superior-elementos-informe-fichero' id='mapa-calor-semanal-consumos-analisis-consumo-estudio-general'></div>
                        <div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='notas-analisis-consumo-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de simulación de factura
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-simulacion-factura'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-simulacion-factura'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='titulo-resumen-simulacion-factura-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-coste-consumo-simulacion-factura-estudio-general'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='titulo-detalles-simulacion-factura-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-consumo-simulacion-factura-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-termino-fijo-simulacion-factura-estudio-general'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-otros-conceptos-simulacion-factura-estudio-general'></div>
                        <div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='notas-simulacion-factura-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de conclusiones
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-conclusiones'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-conclusiones'></div>
                        <div class='texto-informe-informe-fichero separacion-superior-elementos-informe-fichero' id='conclusiones-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de avisos
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-estudio-general-avisos'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_informes_personalizados(TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-estudio-general-avisos'></div>
                        <div class='texto100-informe-fichero separacion-superior-elementos-informe-fichero' id='texto-avisos-estudio-general'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }
?>
