<?php
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/AccionUsuario.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Alarmas/Alarma.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/InformeAutomatico.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/HistoricoProcesado.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/ImportacionesValoresSensores/HistoricoImportacionValoresSensor.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/ImportacionesValoresSensores/ImportacionValoresSensorPendiente.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/util_procesado.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_actuadores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Programaciones/Programacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/AccionRegla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Acciones/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/HistoricoRegla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Regla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/SucesoRegla.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/Reglas/Sucesos/util_sucesos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Clientes/Cliente.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Licencias/Licencia.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/Usuario.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/EquipoInstalacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Equipos/util_equipos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/Instalacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Instalaciones/util_instalaciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/Localizacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/Ratio.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/Ratios/util_ratios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/PlantillaInforme.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/Proyectos/Proyecto.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/LineasBase/LineaBase.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/Evento.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/Eventos/HistoricoEvento.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/ValidacionFacturaElectrica.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/GrupoTarifas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/agua/Espanya/TarifaAgua_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/TarifaElectrica_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Portugal/TarifaElectrica_Portugal.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/gas/Espanya/TarifaGas_Espanya.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');


    // Devuelve los detalles para el tipo de fila desplegada en una tabla
    function dame_detalles_tabla($id_datos)
    {
        $bd_red = BaseDatosRed::dame_base_datos();
        $bd_datos = BaseDatosDatos::dame_base_datos();
        $idiomas = new Idiomas();

        $parametros_accion = array("id_datos" => $id_datos);
        AuditoriaAcciones::comprobar_registrar_accion(ACCION_OBTENER_DETALLES_TABLA, $parametros_accion);

        // Se comprueba si es posible mostrar los detalles de la tabla
        comprueba_id_datos_detalles_tabla($id_datos);

        // Información a devolver
        $herramientas_detalles_tabla = "";
        $detalles_tabla = "";
        $ids_nombres_tablas_detalles_tabla = array();

        // Tipo de dato de la tabla
        $params = explode("__", $id_datos);
        $tipo_dato_fila = $params[0];
        switch ($tipo_dato_fila)
        {
            // Nodos
            case "datosNodo".TIPO_NODO_RED:
            {
                $id_red = $params[1];
                $nodo = Nodo::crea_nodo($id_red, TIPO_NODO_RED);
                $detalles_tabla = $nodo->dame_detalles_tabla(false);
                break;
            }
            case "datosNodo".TIPO_NODO_DISPOSITIVO:
            {
                $id_dispositivo = $params[1];
                $nodo = Nodo::crea_nodo($id_dispositivo, TIPO_NODO_DISPOSITIVO);
                $herramientas_detalles_tabla = $nodo->dame_herramientas_detalles_tabla();
                $detalles_tabla = $nodo->dame_detalles_tabla();
                break;
            }
            case "datosNodo".TIPO_NODO_AXON:
            {
                $id_axon = $params[1];
                $nodo = Nodo::crea_nodo($id_axon, TIPO_NODO_AXON);
                $herramientas_detalles_tabla =  $nodo->dame_herramientas_detalles_tabla();
                $detalles_tabla = $nodo->dame_detalles_tabla();
                break;
            }
            case "datosNodo".TIPO_NODO_SENSOR:
            {
                $id_sensor = $params[1];
                $nodo = Nodo::crea_nodo($id_sensor, TIPO_NODO_SENSOR);
                $herramientas_detalles_tabla = $nodo->dame_herramientas_detalles_tabla();
                $detalles_tabla = $nodo->dame_detalles_tabla();
                $id_nombre_tabla_hijos = array("hijos-sensor".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_sensor, $idiomas->_("Hijos"));
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_hijos);
                break;
            }
            case "datosNodo".TIPO_NODO_GRUPO_SENSORES:
            {
                $id_grupo_sensores = $params[1];
                $nodo = Nodo::crea_nodo($id_grupo_sensores, TIPO_NODO_GRUPO_SENSORES);
                $herramientas_detalles_tabla = $nodo->dame_herramientas_detalles_tabla();
                $detalles_tabla = $nodo->dame_detalles_tabla();
                break;
            }
            case "datosNodo".TIPO_NODO_ACTUADOR:
            {
                $id_actuador = $params[1];
                $nodo = Nodo::crea_nodo($id_actuador, TIPO_NODO_ACTUADOR);
                $herramientas_detalles_tabla = $nodo->dame_herramientas_detalles_tabla();
                $detalles_tabla = $nodo->dame_detalles_tabla();
                break;
            }
            case "datosNodo".TIPO_NODO_GRUPO_ACTUADORES:
            {
                $id_grupo_actuadores = $params[1];
                $nodo = Nodo::crea_nodo($id_grupo_actuadores, TIPO_NODO_GRUPO_ACTUADORES);
                $herramientas_detalles_tabla = $nodo->dame_herramientas_detalles_tabla();
                $detalles_tabla = $nodo->dame_detalles_tabla();
                break;
            }
            // Otros elementos
            case "datosCliente":
            {
                $id_cliente = $params[1];
                $consulta = "
                    SELECT *
                    FROM clientes
                    WHERE
                        id = '".$bd_red->_($id_cliente)."'";
                $res = $bd_red->ejecuta_consulta($consulta);
                if (($res == false) || ($res->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
                }
                $fila = $res->dame_siguiente_fila();
                $cliente = new Cliente($fila);
                $detalles_tabla = $cliente->dame_detalles_tabla();
                break;
            }
            case "datosLicencia":
            {
                $id_licencia = $params[1];
                $consulta = "
                    SELECT *
                    FROM licencias
                    WHERE
                        id = '".$bd_red->_($id_licencia)."'";
                $res = $bd_red->ejecuta_consulta($consulta);
                if (($res == false) || ($res->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
                }
                $fila = $res->dame_siguiente_fila();
                $licencia = new Licencia($fila);
                $detalles_tabla = $licencia->dame_detalles_tabla();
                break;
            }
            case "datosUsuario":
            {
                $id_usuario = $params[1];
                $fila_usuario = dame_fila_usuario($id_usuario);
                $usuario = new Usuario($fila_usuario);
                $detalles_tabla = $usuario->dame_detalles_tabla();
                break;
            }
            case "datosHistoricoProcesado":
            {
                $id_historico_procesado = $params[1];
                $fila_historico_procesado = dame_fila_historico_procesado($id_historico_procesado);
                $historico_procesado = new HistoricoProcesado($fila_historico_procesado);
                $herramientas_detalles_tabla = $historico_procesado->dame_herramientas_detalles_tabla();
                $detalles_tabla = $historico_procesado->dame_detalles_tabla();
                $id_nombre_tabla_tareas = array("tareas-historico-procesado".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$params[1], $idiomas->_("Tareas"));
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_tareas);
                break;
            }
            case "datosImportacionValoresSensorPendiente":
            {
                // Nota: Se muestra un mensaje en elos detalles "controlado" por si ya no existe la importación de valores de sensor pendiente
                // (puede haberse ejecutado ya y no haberse actualizado la tabla)
                $id_importacion_pendiente = $params[1];
                try
                {
                    $fila_importacion_pendiente = dame_fila_importacion_valores_sensor_pendiente($id_importacion_pendiente);
                    $importacion_pendiente = new ImportacionValoresSensorPendiente($fila_importacion_pendiente);
                    $detalles_tabla = $importacion_pendiente->dame_detalles_tabla();
                }
                catch (Exception $e)
                {
                    $detalles_tabla = "<i class='icon-warning-sign color-rojo'></i> ".
                        $idiomas->_("La importación de valores de sensor pendiente no existe (se ha ejecutado o eliminado)");
                }
                break;
            }
            case "datosHistoricoImportacionValoresSensor":
            {
                $id_importacion = $params[1];
                $consulta = "
                    SELECT *
                    FROM importaciones_valores_sensores
                    WHERE
                        id = '".$bd_datos->_($id_importacion)."'";
                $res = $bd_datos->ejecuta_consulta($consulta);
                if (($res == false) || ($res->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
                }
                $fila = $res->dame_siguiente_fila();
                $historico_importacion = new HistoricoImportacionValoresSensor($fila);
                $herramientas_detalles_tabla = $historico_importacion->dame_herramientas_detalles_tabla();
                $detalles_tabla = $historico_importacion->dame_detalles_tabla();
                break;
            }
            case "datosAlarma":
            {
                $id_alarma = $params[1];
                $consulta = "
                    SELECT *
                    FROM alarmas
                    WHERE
                        id = '".$bd_datos->_($id_alarma)."'";
                $res = $bd_datos->ejecuta_consulta($consulta);
                if (($res == false) || ($res->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
                }
                $fila = $res->dame_siguiente_fila();
                $alarma = new Alarma($fila);
                $detalles_tabla = $alarma->dame_detalles_tabla();
                break;
            }
            case "datosInformeAutomatico":
            {
                $id_informe_automatico = $params[1];
                $consulta = "
                    SELECT *
                    FROM informes_automaticos
                    WHERE
                        id = '".$bd_red->_($id_informe_automatico)."'";
                $res = $bd_red->ejecuta_consulta($consulta);
                if (($res == false) || ($res->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
                }

                $fila = $res->dame_siguiente_fila();
                $informe_automatico = new InformeAutomatico($fila);
                $detalles_tabla = $informe_automatico->dame_detalles_tabla();
                break;
            }
            case "datosPlantillaInforme":
            {
                $id_plantilla_informe = $params[1];
                $fila_plantilla_informe = dame_fila_plantilla_informe($id_plantilla_informe);
                $plantilla_informe = new PlantillaInforme($fila_plantilla_informe);
                $detalles_tabla = $plantilla_informe->dame_detalles_tabla();
                $id_nombre_tabla_parametros = array("parametros-plantilla-informe".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$params[1], $idiomas->_("Parámetros"));
                $id_nombre_tabla_elementos = array("elementos-plantilla-informe".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$params[1], $idiomas->_("Elementos"));
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_parametros);
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_elementos);
                break;
            }
            case "datosAccionUsuario":
            {
                $id_accion_usuario = $params[1];
                $consulta = "
                    SELECT *
                    FROM acciones_usuario
                    WHERE
                        id = '".$bd_datos->_($id_accion_usuario)."'";
                $res = $bd_datos->ejecuta_consulta($consulta);
                if (($res == false) || ($res->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
                }
                $fila = $res->dame_siguiente_fila();
                $accion = new AccionUsuario($fila);
                $herramientas_detalles_tabla = $accion->dame_herramientas_detalles_tabla();
                $detalles_tabla = $accion->dame_detalles_tabla();
                break;
            }
            case "datosLocalizacion":
            {
                $id_localizacion = $params[1];
                $fila_localizacion = dame_fila_localizacion($id_localizacion);
                $localizacion = new Localizacion($fila_localizacion);
                $herramientas_detalles_tabla = $localizacion->dame_herramientas_detalles_tabla();
                $detalles_tabla = $localizacion->dame_detalles_tabla();
                $id_nombre_tabla_hijas = array("hijas-localizacion".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_localizacion, $idiomas->_("Hijas"));
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_hijas);
                break;
            }
            case "datosInstalacion":
            {
                $id_instalacion = $params[1];
                $fila_instalacion = dame_fila_instalacion($id_instalacion);
                $instalacion = new Instalacion($fila_instalacion);
                $herramientas_detalles_tabla = $instalacion->dame_herramientas_detalles_tabla();
                $detalles_tabla = $instalacion->dame_detalles_tabla();
                $id_nombre_tabla_equipos = array("equipos-instalacion".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_instalacion, $idiomas->_("Equipos"));
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_equipos);
                break;
            }
            case "datosEquipoInstalacion":
            {
                $id_equipo_instalacion = $params[2];
                $fila_equipo_instalacion = dame_fila_equipo_instalacion($id_equipo_instalacion);
                $equipo_instalacion = new EquipoInstalacion($fila_equipo_instalacion);
                $detalles_tabla = $equipo_instalacion->dame_detalles_tabla();
                break;
            }
            case "datosRatio":
            {
                $id_ratio = $params[1];
                $fila_ratio = dame_fila_ratio($id_ratio);
                $ratio = new Ratio($fila_ratio);
                $detalles_tabla = $ratio->dame_detalles_tabla();
                break;
            }
            case "datosEvento":
            {
                $id_evento = $params[1];
                $fila_evento = dame_fila_evento($id_evento);
                $evento = new Evento($fila_evento);
                $herramientas_detalles_tabla = $evento->dame_herramientas_detalles_tabla();
                $detalles_tabla = $evento->dame_detalles_tabla();
                $id_nombre_tabla_rangos_dias = array("rangos-dias-".ORIGEN_RANGOS_DIAS_EVENTO.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_evento, $idiomas->_("Rangos de días"));
                $id_nombre_tabla_periodos = array("periodos-".ORIGEN_PERIODOS_EVENTO.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_evento, $idiomas->_("Periodos"));
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_rangos_dias);
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_periodos);
                break;
            }
            case "datosHistoricoEvento":
            {
                $id_activacion_eventos = $params[2];
                $consulta = "
                    SELECT *
                    FROM activaciones_eventos
                    WHERE
                        id = '".$bd_datos->_($id_activacion_eventos)."'";
                $res = $bd_datos->ejecuta_consulta($consulta);
                if (($res == false) || ($res->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
                }
                $fila = $res->dame_siguiente_fila();
                $historico_evento = new HistoricoEvento($fila);
                $detalles_tabla = $historico_evento->dame_detalles_tabla();
                break;
            }
            case "datosRegla":
            {
                $id_regla = $params[1];
                $fila_regla = dame_fila_regla($id_regla);
                $regla = new Regla($fila_regla);
                $herramientas_detalles_tabla = $regla->dame_herramientas_detalles_tabla();
                $detalles_tabla = $regla->dame_detalles_tabla();
                $id_nombre_tabla_rangos_dias = array("rangos-dias-".ORIGEN_RANGOS_DIAS_REGLA.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_regla, $idiomas->_("Rangos de días"));
                $id_nombre_tabla_periodos = array("periodos-".ORIGEN_PERIODOS_REGLA.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_regla, $idiomas->_("Periodos"));
                $id_nombre_tabla_sucesos = array("sucesos-regla".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_regla, $idiomas->_("Sucesos"));
                $id_nombre_tabla_acciones_activacion = array("acciones-regla-".TIPO_ACCION_ACTIVACION.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_regla, $idiomas->_("Acciones de activación"));
                $id_nombre_tabla_acciones_desactivacion = array("acciones-regla-".TIPO_ACCION_DESACTIVACION.SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_regla, $idiomas->_("Acciones de desactivación"));
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_rangos_dias);
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_periodos);
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_sucesos);
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_acciones_activacion);
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_acciones_desactivacion);
                break;
            }
            case "datosSucesoRegla":
            {
                $id_suceso_regla = $params[2];
                $fila_suceso_regla = dame_fila_suceso_regla($id_suceso_regla);
                $suceso_regla = new SucesoRegla($fila_suceso_regla);
                $detalles_tabla = $suceso_regla->dame_detalles_tabla();
                break;
            }
            case "datosAccionRegla":
            {
                $id_accion_regla = $params[3];
                $fila_accion_regla = dame_fila_accion_regla($id_accion_regla);
                $accion_regla = new AccionRegla($fila_accion_regla);
                $detalles_tabla = $accion_regla->dame_detalles_tabla();
                break;
            }
            case "datosHistoricoRegla":
            {
                $id_historico_regla = $params[1];
                $consulta = "
                    SELECT *
                    FROM activaciones_reglas
                    WHERE
                        id = '".$bd_datos->_($id_historico_regla)."'";
                $res = $bd_datos->ejecuta_consulta($consulta);
                if (($res == false) || ($res->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
                }
                $fila = $res->dame_siguiente_fila();
                $historico_regla = new HistoricoRegla($fila);
                $detalles_tabla = $historico_regla->dame_detalles_tabla();
                break;
            }
            case "datosProgramacion":
            {
                $id_programacion = $params[1];
                $fila_programacion = dame_fila_programacion($id_programacion);
                $programacion = new Programacion($fila_programacion);
                $detalles_tabla = $programacion->dame_detalles_tabla();
                $id_nombre_tabla_acciones = array("acciones-programacion".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_programacion, $idiomas->_("Acciones"));
                $id_nombre_tabla_excepciones = array("excepciones-programacion".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_programacion, $idiomas->_("Excepciones"));
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_acciones);
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_excepciones);
                break;
            }
            case "datosValidacionFacturaElectrica_Espanya":
            {
                $id_validacion_factura = $params[1];
                $consulta = "
                    SELECT *
                    FROM ".TABLA_VALIDACIONES_FACTURAS_ELECTRICAS_ESPANYA."
                    WHERE
                        id = '".$bd_datos->_($id_validacion_factura)."'";
                $res = $bd_datos->ejecuta_consulta($consulta);
                if (($res == false) || ($res->dame_numero_filas() == 0))
                {
                    throw new Exception("Error o no existe la información en la base de datos: '".$consulta."'");
                }
                $fila = $res->dame_siguiente_fila();
                $validacion_factura_electrica = new ValidacionFacturaElectrica_Espanya($fila);
                $herramientas_detalles_tabla = $validacion_factura_electrica->dame_herramientas_detalles_tabla();
                $detalles_tabla = $validacion_factura_electrica->dame_detalles_tabla();
                break;
            }
            case "datosTarifaElectrica_Espanya":
            {
                $id_tarifa_electrica = $params[1];
                $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa_electrica);
                $tarifa_electrica = new TarifaElectrica_Espanya($fila_tarifa_electrica);
                $herramientas_detalles_tabla = $tarifa_electrica->dame_herramientas_detalles_tabla();
                $detalles_tabla = $tarifa_electrica->dame_detalles_tabla();
                $id_nombre_tabla_tramos = array("tramos-tarifa-electrica".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_tarifa_electrica, $idiomas->_("Tramos"));
                $id_nombre_tabla_conceptos_adicionales_factura = array("conceptos-adicionales-factura-tarifa".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_tarifa_electrica, $idiomas->_("Conceptos adicionales de factura"));
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_tramos);
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_conceptos_adicionales_factura);
                break;
            }
            case "datosTarifaElectrica_Portugal":
            {
                $id_tarifa_electrica = $params[1];
                $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_PORTUGAL, $id_tarifa_electrica);
                $tarifa_electrica = new TarifaElectrica_Portugal($fila_tarifa_electrica);
                $herramientas_detalles_tabla = $tarifa_electrica->dame_herramientas_detalles_tabla();
                $detalles_tabla = $tarifa_electrica->dame_detalles_tabla();
                $id_nombre_tabla_tramos = array("tramos-tarifa-electrica".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_tarifa_electrica, $idiomas->_("Tramos"));
                $id_nombre_tabla_conceptos_adicionales_factura = array("conceptos-adicionales-factura-tarifa".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_tarifa_electrica, $idiomas->_("Conceptos adicionales de factura"));
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_tramos);
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_conceptos_adicionales_factura);
                break;
            }
            case "datosTarifaGas_Espanya":
            {
                $id_tarifa_gas = $params[1];
                $fila_tarifa_gas = dame_fila_tarifa(TABLA_TARIFAS_GAS_ESPANYA, $id_tarifa_gas);
                $tarifa_gas = new TarifaGas_Espanya($fila_tarifa_gas);
                $herramientas_detalles_tabla = $tarifa_gas->dame_herramientas_detalles_tabla();
                $detalles_tabla = $tarifa_gas->dame_detalles_tabla();
                $id_nombre_tabla_parametros = array("parametros-tarifa-gas".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_tarifa_gas, $idiomas->_("Caudales y precios"));
                $id_nombre_tabla_conceptos_adicionales_factura = array("conceptos-adicionales-factura-tarifa".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_tarifa_gas, $idiomas->_("Conceptos adicionales de factura"));
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_parametros);
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_conceptos_adicionales_factura);
                break;
            }
            case "datosTarifaAgua_Espanya":
            {
                $id_tarifa_agua = $params[1];
                $fila_tarifa_agua = dame_fila_tarifa(TABLA_TARIFAS_AGUA_ESPANYA, $id_tarifa_agua);
                $tarifa_agua = new TarifaAgua_Espanya($fila_tarifa_agua);
                $herramientas_detalles_tabla = $tarifa_agua->dame_herramientas_detalles_tabla();
                $detalles_tabla = $tarifa_agua->dame_detalles_tabla();
                $id_nombre_tabla_tramos = array("tramos-tarifa-agua".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_tarifa_agua, $idiomas->_("Tramos"));
                $id_nombre_tabla_conceptos_adicionales_factura = array("conceptos-adicionales-factura-tarifa".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_tarifa_agua, $idiomas->_("Conceptos adicionales de factura"));
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_tramos);
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_conceptos_adicionales_factura);
                break;
            }
            case "datosGrupoTarifas":
            {
                $medicion = $params[1];
                $id_grupo_tarifas = $params[2];
                $tabla_grupos_tarifas = dame_nombre_tabla_grupos_tarifas($medicion);
                $fila_grupo_tarifas = dame_fila_grupo_tarifas($tabla_grupos_tarifas, $id_grupo_tarifas);
                $grupo_tarifas = new GrupoTarifas($fila_grupo_tarifas);
                $herramientas_detalles_tabla = $grupo_tarifas->dame_herramientas_detalles_tabla();
                $detalles_tabla = $grupo_tarifas->dame_detalles_tabla($medicion);
                break;
            }
            case "datosProyecto":
            {
                $id_proyecto = $params[1];
                $fila_proyecto = dame_fila_proyecto($id_proyecto);
                $proyecto = new Proyecto($fila_proyecto);
                $herramientas_detalles_tabla = $proyecto->dame_herramientas_detalles_tabla();
                $detalles_tabla = $proyecto->dame_detalles_tabla();
                $id_nombre_tabla_valores_adicionales = array("valores-adicionales-proyecto".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_proyecto, $idiomas->_("Valores adicionales"));
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_valores_adicionales);
                break;
            }
            case "datosLineaBase":
            {
                $id_linea_base = $params[1];
                $fila_linea_base = dame_fila_linea_base($id_linea_base);
                $linea_base = new LineaBase($fila_linea_base);
                $herramientas_detalles_tabla = $linea_base->dame_herramientas_detalles_tabla();
                $detalles_tabla = $linea_base->dame_detalles_tabla();
                $id_nombre_tabla_variables = array("variables-linea-base".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_linea_base, $idiomas->_("Variables"));
                $id_nombre_tabla_excepciones = array("excepciones-linea-base".SEPARADOR_ELEMENTOS_IDS_TABLAS_DETALLES.$id_linea_base, $idiomas->_("Excepciones"));
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_variables);
                array_push($ids_nombres_tablas_detalles_tabla, $id_nombre_tabla_excepciones);
                break;
            }
            case "datosExcepcionLineaBase":
            {
                $id_linea_base_padre = $params[1];
                $id_excepcion_linea_base = $params[2];
                $detalles_tabla = LineaBase::dame_detalles_tabla_excepcion_linea_base($id_linea_base_padre, $id_excepcion_linea_base);
                break;
            }
            default:
            {
                throw new Exception("Detalles de elemento de tabla no implementados: '".$tipo_dato_fila."'");
            }
        }

        // Se crea el html con los detalles de la fila de la tabla
        $detalles_fila = TablaDatos::dame_detalles_fila($herramientas_detalles_tabla, $detalles_tabla);

        // Se devuelve la información de los detalles de la fila de la tabla
        return (array(
            "res" => "OK",
            "html" => $detalles_fila,
            "ids_nombres_tablas" => $ids_nombres_tablas_detalles_tabla));
    }


    // Se comprueba si es posible mostrar los detalles de la tabla
    function comprueba_id_datos_detalles_tabla($id_datos)
    {
        // Información del usuario actual
        $id_usuario = $_SESSION["id_usuario"];
        $perfil_usuario = $_SESSION["perfil"];

        // Identificador permitido
        $id_permitido = false;

        // Tipo de dato de la tabla
        $params = explode("__", $id_datos);
        $tipo_dato_fila = $params[0];
        switch ($tipo_dato_fila)
        {
            // Nodos
            case "datosNodo".TIPO_NODO_RED:
            {
                if ($perfil_usuario == PERFIL_USUARIO_SUPERADMINISTRADOR)
                {
                    $id_permitido = true;
                    break;
                }
                break;
            }
            case "datosNodo".TIPO_NODO_DISPOSITIVO:
            case "datosNodo".TIPO_NODO_AXON:
            {
                $id_permitido = true;
                break;
            }
            case "datosNodo".TIPO_NODO_SENSOR:
            {
                $id_sensor = $params[1];
                $ids_sensores_usuario_actual = dame_ids_sensores_usuario_actual(true);
                if (in_array($id_sensor, $ids_sensores_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosNodo".TIPO_NODO_GRUPO_SENSORES:
            {
                $id_grupo_sensores = $params[1];
                $ids_grupos_sensores_usuario_actual = dame_ids_grupos_sensores_usuario_actual(true);
                if (in_array($id_grupo_sensores, $ids_grupos_sensores_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosNodo".TIPO_NODO_ACTUADOR:
            {
                $id_actuador = $params[1];
                $ids_actuadores_usuario_actual = dame_ids_actuadores_usuario_actual(true);
                if (in_array($id_actuador, $ids_actuadores_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosNodo".TIPO_NODO_GRUPO_ACTUADORES:
            {
                $id_grupo_actuadores = $params[1];
                $ids_grupos_actuadores_usuario_actual = dame_ids_grupos_actuadores_usuario_actual(true);
                if (in_array($id_grupo_actuadores, $ids_grupos_actuadores_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            // Otros elementos
            case "datosCliente":
            {
                if ($perfil_usuario == PERFIL_USUARIO_SUPERADMINISTRADOR)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosLicencia":
            {
                switch ($perfil_usuario)
                {
                    case PERFIL_USUARIO_ADMINISTRADOR:
                    case PERFIL_USUARIO_SUPERADMINISTRADOR:
                    {
                        $id_permitido = true;
                        break;
                    }
                }
                break;
            }
            case "datosUsuario":
            {
                $id_usuario = $params[1];
                $ids_usuarios_usuario_actual = dame_ids_usuarios_usuario_actual();
                if (in_array($id_usuario, $ids_usuarios_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosImportacionValoresSensorPendiente":
            case "datosHistoricoImportacionValoresSensor":
            {
                $id_permitido = true;
                break;
            }
            case "datosHistoricoProcesado":
            {
                if ($perfil_usuario == PERFIL_USUARIO_SUPERADMINISTRADOR)
                {
                    $id_permitido = true;
                    break;
                }
                break;
            }
            case "datosAlarma":
            {
                $id_permitido = true;
                break;
            }
            case "datosInformeAutomatico":
            {
                $id_informe_automatico = $params[1];
                $ids_informes_automaticos_usuario_actual = dame_ids_informes_automaticos_usuario_actual();
                if (in_array($id_informe_automatico, $ids_informes_automaticos_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosPlantillaInforme":
            {
                $id_plantilla_informe = $params[1];
                $ids_plantillas_informes_usuario_actual = dame_ids_plantillas_informes_usuario_actual();
                if (in_array($id_plantilla_informe, $ids_plantillas_informes_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosAccionUsuario":
            {
                $id_permitido = true;
                break;
            }
            case "datosLocalizacion":
            {
                $id_localizacion = $params[1];
                $ids_localizaciones_usuario_actual = dame_ids_localizaciones_usuario_actual(true);
                if (in_array($id_localizacion, $ids_localizaciones_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosInstalacion":
            case "datosEquipoInstalacion":
            {
                $id_instalacion = $params[1];
                $ids_instalaciones_usuario_actual = Instalacion::dame_ids_instalaciones_usuario_actual();
                if (in_array($id_instalacion, $ids_instalaciones_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosRatio":
            {
                $id_ratio = $params[1];
                $ids_ratios_usuario_actual = dame_ids_ratios_usuario_actual();
                if (in_array($id_ratio, $ids_ratios_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosEvento":
            {
                $id_evento = $params[1];
                $ids_eventos_usuario_actual = Evento::dame_ids_eventos_usuario_actual();
                if (in_array($id_evento, $ids_eventos_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosHistoricoEvento":
            {
                $id_permitido = true;
                break;
            }
            case "datosRegla":
            case "datosSucesoRegla":
            case "datosAccionRegla":
            {
                $id_regla = $params[1];
                $ids_reglas_usuario_actual = Regla::dame_ids_reglas_usuario_actual();
                if (in_array($id_regla, $ids_reglas_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosHistoricoRegla":
            {
                $id_permitido = true;
                break;
            }
            case "datosProgramacion":
            {
                $id_programacion = $params[1];
                $ids_programaciones_usuario_actual = Programacion::dame_ids_programaciones_usuario_actual();
                if (in_array($id_programacion, $ids_programaciones_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosValidacionFacturaElectrica_Espanya":
            {
                $id_permitido = true;
                break;
            }
            case "datosTarifaElectrica_Espanya":
            {
                $id_tarifa_electrica = $params[1];
                $ids_tarifas_electricas_usuario_actual = Tarifa::dame_ids_tarifas_usuario_actual(MEDICION_ELECTRICIDAD);
                if (in_array($id_tarifa_electrica, $ids_tarifas_electricas_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosTarifaElectrica_Portugal":
            {
                $id_tarifa_electrica = $params[1];
                $ids_tarifas_electricas_usuario_actual = Tarifa::dame_ids_tarifas_usuario_actual(MEDICION_ELECTRICIDAD);
                if (in_array($id_tarifa_electrica, $ids_tarifas_electricas_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosTarifaGas_Espanya":
            {
                $id_tarifa_gas = $params[1];
                $ids_tarifas_gas_usuario_actual = Tarifa::dame_ids_tarifas_usuario_actual(MEDICION_GAS);
                if (in_array($id_tarifa_gas, $ids_tarifas_gas_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosTarifaAgua_Espanya":
            {
                $id_tarifa_agua = $params[1];
                $ids_tarifas_agua_usuario_actual = Tarifa::dame_ids_tarifas_usuario_actual(MEDICION_AGUA);
                if (in_array($id_tarifa_agua, $ids_tarifas_agua_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosGrupoTarifas":
            {
                $medicion = $params[1];
                $id_grupo_tarifas = $params[2];
                $ids_grupos_tarifas_usuario_actual = GrupoTarifas::dame_ids_grupos_tarifas_usuario_actual($medicion);
                if (in_array($id_grupo_tarifas, $ids_grupos_tarifas_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosProyecto":
            {
                $id_proyecto = $params[1];
                $ids_proyectos_usuario_actual = Proyecto::dame_ids_proyectos_usuario_actual();
                if (in_array($id_proyecto, $ids_proyectos_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            case "datosLineaBase":
            case "datosExcepcionLineaBase":
            {
                $id_linea_base = $params[1];
                $ids_lineas_base_usuario_actual = LineaBase::dame_ids_lineas_base_usuario_actual();
                if (in_array($id_linea_base, $ids_lineas_base_usuario_actual) == true)
                {
                    $id_permitido = true;
                }
                break;
            }
            default:
            {
                throw new Exception("Detalles de elemento de tabla no implementados: '".$tipo_dato_fila."'");
            }
        }

        // Si el identificador no está permitido, se eleva una excepción
        if ($id_permitido == false)
        {
            throw new Exception("Identificador de detalles de tabla incorrecto: '".$id_datos."'");
        }
    }
?>
