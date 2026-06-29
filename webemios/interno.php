<?php
    include_once('comprueba_tipo_peticion_http.php');

    // Se permiten las 'cookies' no seguras (sólo es en localhost)
    ini_set("session.cookie_secure", 0);

    // Se elimina la sesión anterior y se inicia la sesión
    session_unset();
    session_destroy();
    session_start();

    // Se guarda el directorio
    $_SESSION["directorio"] = getcwd();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');


    // Log
    $log = dame_log();
    $log->info("Parámetros GET: '".json_encode($_GET)."'");

    // Nota: La función 'ob_start' guarda la salida en un 'buffer'; de esta forma si se captura una excepción
    // se puede borrar el contenido de la salida hasta el momento (con la función 'ob_end_clean' y devolver un error HTTP)
    // (https://www.php.net/manual/es/function.ob-start.php)
    ob_start();

    try
    {
        // Se ejecuta el script interno correspondiente
        $tipo_script_interno = $_GET["tipo_script_interno"];
        switch ($tipo_script_interno)
        {
            case TIPO_SCRIPT_INTERNO_INFORME_FICHERO:
            {
                // Se recupera el título del informe (existe en el caso de ser informe automático)
                $titulo_informe = $_GET["titulo_informe"];
                if ($titulo_informe !== NULL)
                {
                    $_SESSION["titulo_informe"] = $titulo_informe;
                }

                // Se establece el tamaño de letra
                $_SESSION["tamanyo_letra"] = TAMANYO_LETRA_INFORMES_FICHERO;

                // Se ejecuta el script del informe correspondiente
                $tipo_informe = $_GET["tipo_informe"];
                switch ($tipo_informe)
                {
                    // Informes de Personal
                    case TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME:
                    {
                        $ruta_informes_fichero_plantillas_informes = "/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_informe_plantilla_informe.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_plantillas_informes.$nombre_script_informe_fichero);
                        break;
                    }

                    // Informes de Sensores (información)
                    case TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA:
                    case TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD:
                    case TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR:
                    case TIPO_INFORME_SENSORES_INFORMACION_VIENTO:
                    case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA:
                    case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA:
                    case TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION:
                    case TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA:
                    case TIPO_INFORME_SENSORES_INFORMACION_GAS:
                    case TIPO_INFORME_SENSORES_INFORMACION_AGUA:
                    case TIPO_INFORME_SENSORES_INFORMACION_GENERICA:
                    {
                        $ruta_informes_fichero_informacion_sensores = "/src/modulos/ModulosWeb/ModuloSensores/Informacion/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_SENSORES_INFORMACION_TEMPERATURA:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_informacion_temperatura.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_INFORMACION_HUMEDAD:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_informacion_humedad.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_INFORMACION_LUZ_INTERIOR:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_informacion_luz_interior.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_INFORMACION_VIENTO:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_informacion_viento.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_ACTIVA:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_informacion_energia_activa.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_INFORMACION_ENERGIA_REACTIVA:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_informacion_energia_reactiva.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_INFORMACION_CORTES_TENSION:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_informacion_cortes_tension.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_INFORMACION_COMPRA_ENERGIA:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_informacion_compra_energia.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_INFORMACION_GAS:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_informacion_gas.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_INFORMACION_AGUA:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_informacion_agua.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_INFORMACION_GENERICA:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_informacion_generica.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_informacion_sensores.$nombre_script_informe_fichero);
                        break;
                    }
                    // Informes de Sensores (eventos)
                    case TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                    {
                        $ruta_informes_fichero_eventos_sensores = "/src/modulos/ModulosWeb/ModuloSensores/Eventos/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_SENSORES_ACTIVACIONES_EVENTOS:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_activaciones_eventos.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_eventos_sensores.$nombre_script_informe_fichero);
                        break;
                    }
                    // Informes de Sensores (análisis)
                    case TIPO_INFORME_SENSORES_ANALISIS_HORARIO:
                    case TIPO_INFORME_SENSORES_ANALISIS_DIARIO:
                    case TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
                    {
                        $ruta_informes_fichero_analisis_sensores = "/src/modulos/ModulosWeb/ModuloSensores/Analisis/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_SENSORES_ANALISIS_HORARIO:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_analisis_horario.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_ANALISIS_DIARIO:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_analisis_diario.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_ANALISIS_COMPORTAMIENTO:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_analisis_comportamiento.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_analisis_sensores.$nombre_script_informe_fichero);
                        break;
                    }
                    // Informes de Sensores (comparación)
                    case TIPO_INFORME_SENSORES_COMPARACION_PERIODOS:
                    case TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
                    case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
                    case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
                    case TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO:
                    case TIPO_INFORME_SENSORES_VALORES_GENERALES:
                    case TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES:
                    {
                        $ruta_informes_fichero_comparacion_sensores = "/src/modulos/ModulosWeb/ModuloSensores/Comparacion/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_SENSORES_COMPARACION_PERIODOS:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_comparacion_periodos.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_COMPARACION_PERFIL_HORARIO:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_comparacion_perfil_horario.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_IGUALES:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_comparacion_campos_iguales.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_COMPARACION_CAMPOS_DIFERENTES:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_comparacion_campos_diferentes.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_ANALISIS_COMPARATIVO:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_analisis_comparativo.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_VALORES_GENERALES:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_valores_generales.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_INCREMENTOS_TOTALES:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_incrementos_totales.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_comparacion_sensores.$nombre_script_informe_fichero);
                        break;
                    }
                    // Informes de Sensores (estadística)
                    case TIPO_INFORME_SENSORES_HISTOGRAMA:
                    case TIPO_INFORME_SENSORES_CORRELACION:
                    {
                        $ruta_informes_fichero_estadistica_sensores = "/src/modulos/ModulosWeb/ModuloSensores/Estadistica/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_SENSORES_HISTOGRAMA:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_histograma.php";
                                break;
                            }
                            case TIPO_INFORME_SENSORES_CORRELACION:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_correlacion.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_estadistica_sensores.$nombre_script_informe_fichero);
                        break;
                    }
                    // Informes de Actuadores (información)
                    case TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                    {
                        $ruta_informes_fichero_informacion_sensores = "/src/modulos/ModulosWeb/ModuloActuadores/Informacion/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_ACTUADORES_INFORMACION_ACCIONES_ENVIADAS:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_informacion_acciones_enviadas.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_informacion_sensores.$nombre_script_informe_fichero);
                        break;
                    }
                    // Informes de Smartmeter (consumos y costes)
                    case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                    case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
                    case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS:
                    case TIPO_INFORME_SMARTMETER_EXCESOS_POTENCIA:
                    case TIPO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA:
                    case TIPO_INFORME_SMARTMETER_CORTES_TENSION:
                    case TIPO_INFORME_SMARTMETER_EXCESOS_CAUDAL:
                    case TIPO_INFORME_SMARTMETER_COMPARACION_PERIODOS:
                    case TIPO_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
                    {
                        $ruta_informes_fichero_consumos_costes_smartmeter = "/src/modulos/ModulosWeb/ModuloSmartmeter/ConsumosCostes/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_GENERALES:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_consumos_costes_generales.php";
                                break;
                            }
                            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TOTALES:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_consumos_costes_totales.php";
                                break;
                            }
                            case TIPO_INFORME_SMARTMETER_CONSUMOS_COSTES_TRAMOS:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_consumos_costes_tramos.php";
                                break;
                            }
                            case TIPO_INFORME_SMARTMETER_EXCESOS_POTENCIA:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_excesos_potencia.php";
                                break;
                            }
                            case TIPO_INFORME_SMARTMETER_EXCESOS_ENERGIA_REACTIVA:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_excesos_energia_reactiva.php";
                                break;
                            }
                            case TIPO_INFORME_SMARTMETER_CORTES_TENSION:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_cortes_tension.php";
                                break;
                            }
                            case TIPO_INFORME_SMARTMETER_EXCESOS_CAUDAL:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_excesos_caudal.php";
                                break;
                            }
                            case TIPO_INFORME_SMARTMETER_COMPARACION_PERIODOS:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_comparacion_periodos.php";
                                break;
                            }
                            case TIPO_INFORME_SMARTMETER_SIMULADOR_TARIFAS:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_simulador_tarifas.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_consumos_costes_smartmeter.$nombre_script_informe_fichero);
                        break;
                    }
                    // Informes de Smartmeter (autoconsumo)
                    case TIPO_INFORME_SMARTMETER_SIMULADOR_AUTOCONSUMO:
                    {
                        $ruta_informes_fichero_autoconsumo_smartmeter = "/src/modulos/ModulosWeb/ModuloSmartmeter/Autoconsumo/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_SMARTMETER_SIMULADOR_AUTOCONSUMO:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_simulador_autoconsumo.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_autoconsumo_smartmeter.$nombre_script_informe_fichero);
                        break;
                    }
                    // Informes de Smartmeter (potencias)
                    case TIPO_INFORME_SMARTMETER_OPTIMIZADOR_POTENCIAS_AUTOMATICO:
                    case TIPO_INFORME_SMARTMETER_OPTIMIZADOR_POTENCIAS_MANUAL:
                    case TIPO_INFORME_SMARTMETER_SIMULADOR_POTENCIAS_AUTOMATICO:
                    case TIPO_INFORME_SMARTMETER_SIMULADOR_POTENCIAS_MANUAL:
                    {
                        $ruta_informes_fichero_potencias_smartmeter = "/src/modulos/ModulosWeb/ModuloSmartmeter/Potencias/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_SMARTMETER_OPTIMIZADOR_POTENCIAS_AUTOMATICO:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_optimizador_potencias_automatico.php";
                                break;
                            }
                            case TIPO_INFORME_SMARTMETER_OPTIMIZADOR_POTENCIAS_MANUAL:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_optimizador_potencias_manual.php";
                                break;
                            }
                            case TIPO_INFORME_SMARTMETER_SIMULADOR_POTENCIAS_AUTOMATICO:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_simulador_potencias_automatico.php";
                                break;
                            }
                            case TIPO_INFORME_SMARTMETER_SIMULADOR_POTENCIAS_MANUAL:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_simulador_potencias_manual.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_potencias_smartmeter.$nombre_script_informe_fichero);
                        break;
                    }
                    // Informes de Smartmeter (energía reactiva)
                    case TIPO_INFORME_SMARTMETER_SIMULADOR_BATERIA_CONDENSADORES:
                    {
                        $ruta_informes_fichero_energia_reactiva_smartmeter = "/src/modulos/ModulosWeb/ModuloSmartmeter/EnergiaReactiva/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_SMARTMETER_SIMULADOR_BATERIA_CONDENSADORES:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_simulador_bateria_condensadores.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_energia_reactiva_smartmeter.$nombre_script_informe_fichero);
                        break;
                    }
                    // Informes de Smartmeter (compra de energía)
                    case TIPO_INFORME_SMARTMETER_PREVISION_COMPRA_ENERGIA:
                    case TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA:
                    case TIPO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA:
                    {
                        $ruta_informes_fichero_compra_energia_smartmeter = "/src/modulos/ModulosWeb/ModuloSmartmeter/CompraEnergia/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_SMARTMETER_PREVISION_COMPRA_ENERGIA:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_prevision_compra_energia.php";
                                break;
                            }
                            case TIPO_INFORME_SMARTMETER_DESVIOS_COMPRA_ENERGIA:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_desvios_compra_energia.php";
                                break;
                            }
                            case TIPO_INFORME_SMARTMETER_DESVIOS_PONDERADOS_COMPRA_ENERGIA:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_desvios_ponderados_compra_energia.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_compra_energia_smartmeter.$nombre_script_informe_fichero);
                        break;
                    }
                    // Informes de Smartmeter (caudales)
                    case TIPO_INFORME_SMARTMETER_OPTIMIZADOR_CAUDALES_AUTOMATICO:
                    case TIPO_INFORME_SMARTMETER_OPTIMIZADOR_CAUDALES_MANUAL:
                    case TIPO_INFORME_SMARTMETER_SIMULADOR_CAUDALES_AUTOMATICO:
                    case TIPO_INFORME_SMARTMETER_SIMULADOR_CAUDALES_MANUAL:
                    {
                        $ruta_informes_fichero_caudales_smartmeter = "/src/modulos/ModulosWeb/ModuloSmartmeter/Caudales/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_SMARTMETER_OPTIMIZADOR_CAUDALES_AUTOMATICO:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_optimizador_caudales_automatico.php";
                                break;
                            }
                            case TIPO_INFORME_SMARTMETER_OPTIMIZADOR_CAUDALES_MANUAL:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_optimizador_caudales_manual.php";
                                break;
                            }
                            case TIPO_INFORME_SMARTMETER_SIMULADOR_CAUDALES_AUTOMATICO:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_simulador_caudales_automatico.php";
                                break;
                            }
                            case TIPO_INFORME_SMARTMETER_SIMULADOR_CAUDALES_MANUAL:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_simulador_caudales_manual.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_caudales_smartmeter.$nombre_script_informe_fichero);
                        break;
                    }
                    // Informes de Smartmeter (facturas)
                    case TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA:
                    {
                        $ruta_informes_fichero_facturas_smartmeter = "/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_simulador_factura.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_facturas_smartmeter.$nombre_script_informe_fichero);
                        break;
                    }
                    // Informes de Smartmeter (informes personalizados)
                    case TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL:
                    {
                        $ruta_informes_fichero_informes_personalizados_smartmeter = "/src/modulos/ModulosWeb/ModuloSmartmeter/InformesPersonalizados/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_SMARTMETER_ESTUDIO_GENERAL:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_estudio_general.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_informes_personalizados_smartmeter.$nombre_script_informe_fichero);
                        break;
                    }
                    // Informes de Proyectos (líneas base)
                    case TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                    {
                        $ruta_informes_fichero_lineas_base_proyectos = "/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_PROYECTOS_SIMULADOR_LINEA_BASE:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_simulador_linea_base.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_lineas_base_proyectos.$nombre_script_informe_fichero);
                        break;
                    }
                    // Informes de Proyectos (información)
                    case TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO:
                    {
                        $ruta_informes_fichero_informacion_proyectos = "/src/modulos/ModulosWeb/ModuloProyectos/Informacion/InformesFichero/";
                        switch ($tipo_informe)
                        {
                            case TIPO_INFORME_PROYECTOS_INFORMACION_PROYECTO:
                            {
                                $nombre_script_informe_fichero = "genera_informe_fichero_informacion_proyecto.php";
                                break;
                            }
                        }
                        include_once($_SESSION["directorio"].$ruta_informes_fichero_informacion_proyectos.$nombre_script_informe_fichero);
                        break;
                    }
                    default:
                    {
                        throw Exception("Tipo de informe desconocido: '".$tipo_informe."'");
                    }
                }
                break;
            }
            default:
            {
                throw Exception("Tipo de script 'interno' desconocido: '".$tipo_script."'");
            }
        }
    }
    catch (Exception $e)
    {
        $log->error("Excepción capturada: ", $e);

        // Se elimina la salida del buffer y se devuelve un error HTTP
        ob_end_clean();
        print("ERROR");
        header("HTTP/1.0 500 Internal Server Error");
        die();
    }
?>