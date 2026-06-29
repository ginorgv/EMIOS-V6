<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/InformesFichero/util_facturas_informes_fichero.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Facturas/util_informes_facturas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/util_tarifas.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/Tarifas/electricidad/Espanya/TarifaElectrica_Espanya.php');


    //
    // Funciones de información de facturas (electricidad - España)
    //


    // Devuelve la información de simulación de factura de un sensor y tarifa
    function dame_simulacion_factura_sensor_tarifa_electricidad_Espanya($parametros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $id_tarifa = $parametros["id_tarifa"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];
        if (isset($parametros["exclusion_fechas"]))
        {
            $exclusion_fechas = json_decode($parametros["exclusion_fechas"], true);
        }
        else
        {
            $exclusion_fechas = NULL;
        }

        // Se comprueba si el sensor es visible por el usuario actual
        $ids_sensores_usuario_actual = dame_todos_ids_sensores_usuario_actual();
        if (in_array($id_sensor, $ids_sensores_usuario_actual) == false)
        {
            throw new Exception("Sensor no visible por el usuario actual (id: '".$id_sensor."')");
        }

        // Se recupera el identificador de tarifa eléctrica si no hay tarifa eléctrica seleccionada
        // (realmente está seleccionada la tarifa actual)
        if ($id_tarifa == ID_NINGUNO)
        {
            $id_tarifa = dame_id_tarifa_id_sensor_fecha($id_sensor, $cadena_fecha_hora_inicio_local_local);
            $recalcular_costes_energia_activa = VALOR_NO;
        }
        else
        {
            $recalcular_costes_energia_activa = VALOR_SI;
        }

        // Si no hay tarifa, se devuelve error
        if ($id_tarifa == ID_NINGUNO)
        {
            $mensaje_error = $idiomas->_("El sensor no tiene tarifa eléctrica asignada");
            $resultado = array(
                "res" => "ERROR",
                "msg" => $mensaje_error);
            return ($resultado);
        }

        // Información de tarifa
        $fila_tarifa_electrica = dame_fila_tarifa(TABLA_TARIFAS_ELECTRICAS_ESPANYA, $id_tarifa);
        $tipo_tarifa_electrica = $fila_tarifa_electrica["tipo"];
        $log = dame_log();
        $log -> debug("El contenido de la fila es: ");
        $log -> debug($fila_tarifa_electrica);
        $prorrateo = $fila_tarifa_electrica["prorrateo"];


        // Conversión de fechas
        $zona_horaria = dame_zona_horaria_local();
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
        $cadena_fecha_hora_fin_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
        $cadena_fecha_inicio_local_local = convierte_formato_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);
        $cadena_fecha_fin_local_local = convierte_formato_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $_SESSION["formato_fecha_local"]);

        // Exclusión de fechas
        $cadena_exclusion_fechas = dame_cadena_fechas($exclusion_fechas);

        // Parámetros de la función a llamar
        $parametros_funcion_externa =
            array(
                "llamante" => "web_emios",
                "nombre" => NOMBRE_FUNCION_CALCULA_DATOS_SIMULACION_FACTURA_SENSOR_TARIFA,
                "medicion" => MEDICION_ELECTRICIDAD,
                "pais_tarifas" => PAIS_ESPANYA,
                "nombre_sensor" => $nombre_sensor,
                "id_red" => $_SESSION["id_red"],
                "id_tarifa" => $id_tarifa,
                "recalcular_costes_energia_activa" => $recalcular_costes_energia_activa,
                "fecha_hora_inicio" => $cadena_fecha_hora_inicio_funciones_utc,
                "fecha_hora_fin" => $cadena_fecha_hora_fin_funciones_utc,
                "exclusion_fechas" => $cadena_exclusion_fechas
            );

        // Llamada a función 'externa'
        $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);
        
        $log = dame_log();
        $log -> debug("El resultado de la fucnion externa es: ");
        $log -> debug($resultado_funcion_externa);
        $log -> debug("La ruta de procesado es: ");
        $log -> debug($ruta_procesado_emios);
        
        // Si no hay datos de consumo, no se hace nada
        $hay_datos_consumo = $resultado_funcion_externa["hay_datos_consumo"];
        if ($hay_datos_consumo == False)
        {
            $resultado = array(
                "res" => "OK",
                "hay_datos" => false);
            return ($resultado);
        }
        else
        {
            // Tabla de datos
            $params_tabla_datos = array(
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_DATOS_FACTURA,
                "generar_valores_xml" => true
            );
            $titulo_tabla_datos = $idiomas->_("Datos");
            $tabla_datos = new TablaDatos(
                "tabla-datos-simulador-factura-electrica",
                $titulo_tabla_datos,
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla_datos
            );

            // Se recupera el CUPS del sensor
            $fila_sensor = dame_fila_sensor($id_sensor);
            $parametros_clase_sensor = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_sensor['parametros_clase']);
            $cups = $parametros_clase_sensor[INDICE_PARAMETRO_CLASE_SENSOR_ENERGIA_ACTIVA_ESPANYA_CUPS];

            // Filas de la tabla de datos
            $datos_nombre_sensor = array($idiomas->_("Sensor"), $nombre_sensor);
            if ($cups != "")
            {
                $datos_cups_sensor = array($idiomas->_("CUPS"), $cups);
            }
            $datos_nombre_tarifa = array(
                $idiomas->_("Tarifa"),
                $fila_tarifa_electrica["nombre"]." (".TarifaElectrica_Espanya::dame_descripcion_tipo_tarifa_electrica($tipo_tarifa_electrica).")");
            $datos_fecha_inicio = array($idiomas->_("Fecha de inicio"), $cadena_fecha_inicio_local_local);
            $datos_fecha_fin = array($idiomas->_("Fecha de fin"), $cadena_fecha_fin_local_local);
            $error_costes_consumo_simulacion_factura = $resultado_funcion_externa["aerror_costes_consumo_simulacion_factura"];
            
            if (count($error_costes_consumo_simulacion_factura) > 1) {
                $cadena_fecha_inicio_error_parametros = convierte_formato_fecha($error_costes_consumo_simulacion_factura[1], 
                FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                $cadena_fecha_fin_error_parametros = convierte_formato_fecha($error_costes_consumo_simulacion_factura[2], 
                FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);

                $error_parametros = array("<b><font color='#B22222'>(*) Aviso importante</font></b>", "<b><font color='#B22222'>
                Faltan parámetros de energía eléctrica desde el día $cadena_fecha_inicio_error_parametros.
                 Por ello, la simulación de la factura y el cálculo del consumo se han realizado para menos días
                de los que se especifican.</font></b>");       

                $datos_fecha_fin = array($idiomas->_("Fecha de fin"), $cadena_fecha_fin_local_local. 
                ", calculada hasta el día $cadena_fecha_inicio_error_parametros <b><font color='#B22222'>(*)</b>");
            }          


            $tabla_datos->anyade_fila("fila-nombre-sensor", $datos_nombre_sensor);
            if ($cups != "")
            {
                $tabla_datos->anyade_fila("fila-cups-sensor", $datos_cups_sensor);
            }
            $tabla_datos->anyade_fila("fila-nombre-tarifa", $datos_nombre_tarifa);
            $tabla_datos->anyade_fila("fila-fecha-inicio", $datos_fecha_inicio);
            $tabla_datos->anyade_fila("fila-fecha-fin", $datos_fecha_fin);
            // Si desde la capa de procesado llega el error de que no hay parámetros se anyade la fila a la tabla 
            // de resumen de la factura con las fechas para las que no hay parámetros       
            if (count($error_costes_consumo_simulacion_factura) > 1){                                
                if (strcmp($error_costes_consumo_simulacion_factura[0], "SIN_VALORES_PARAMETROS_ENERGIA_ELECTRICA_PASS_THROUGH") === 0){
                    $tabla_datos->anyade_fila("fila-error-parametros", $error_parametros);                    
                }
            }

            // Características de tipo de la tarifa eléctrica
            $caracteristicas_tipo_tarifa_electrica = TarifaElectrica_Espanya::dame_caracteristicas_tipo_tarifa_electrica($tipo_tarifa_electrica, $prorrateo);

            $log = dame_log();
            $log -> debug("El tipo de tarifa es:");
            $log -> debug($tipo_tarifa_electrica);
            $log -> debug("y sus características de la tarifa son: ");
            $log -> debug($caracteristicas_tipo_tarifa_electrica);

            // Se ajusta el valor del prorrateo en el cálculo del exceso de potencia 
            //con lo que ha especificado el usuario al crear la tarifa
            if ($prorrateo == PRORRATEO_TARIFA_NO){
                $caracteristicas_tipo_tarifa_electrica["prorrateo_potencias"] = false;
                $log = dame_log();
                $log -> debug("Entra al if y el valor es:");
                $log -> debug(strval($caracteristicas_tipo_tarifa_electrica["prorrateo_potencias"]));
            }
            else{
                $caracteristicas_tipo_tarifa_electrica["prorrateo_potencias"] = true;
                $log = dame_log();
                $log -> debug("No entra al if y el valor es:");
                $log -> debug($caracteristicas_tipo_tarifa_electrica["prorrateo_potencias"]);
            }	

            $log -> debug("Las características de la tarifa después de ajustar el prorrateo son: ");
            $log -> debug($caracteristicas_tipo_tarifa_electrica);

            // Unidad de medida de coste
            $unidad_medida_coste = $_SESSION["moneda"];

            // Número de días
            $numero_dias = $resultado_funcion_externa["numero_dias"];

            // Energía activa
            $numero_dias_consumo_energia_activa = $resultado_funcion_externa["numero_dias_consumo_energia_activa"];
            $cadena_hora_inicio_consumos_energia_activa_funciones_utc = $resultado_funcion_externa["hora_inicio_consumos_energia_activa"];
            $cadena_hora_fin_consumos_energia_activa_funciones_utc = $resultado_funcion_externa["hora_fin_consumos_energia_activa"];
            $datos_energia_activa_tramos = $resultado_funcion_externa["datos_energia_activa_tramos"];

            // Aviso en informe de número de días de factura menor que número de días de consumo de energía activa
            if ($numero_dias > $numero_dias_consumo_energia_activa)
            {
                // Conversión de fechas
                $cadena_hora_inicio_consumos_energia_activa_funciones_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_inicio_consumos_energia_activa_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_fin_consumos_energia_activa_funciones_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_fin_consumos_energia_activa_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_inicio_consumos_energia_activa_local_local = convierte_formato_fecha($cadena_hora_inicio_consumos_energia_activa_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                $cadena_hora_fin_consumos_energia_activa_local_local = convierte_formato_fecha($cadena_hora_fin_consumos_energia_activa_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                $aviso_falta_consumo = array("<b><font color='#B22222'>(*) Aviso importante</font></b>"
                ,   "<b><font color='#B22222'> 
                    El número de horas de la factura es mayor que el número de horas de consumo de energía activa.
                    Hora de inicio de consumo $cadena_hora_inicio_consumos_energia_activa_local_local,
                    hora de fin de consumo $cadena_hora_fin_consumos_energia_activa_local_local
                    </font></b>");                   
                $tabla_datos->anyade_fila("fila-error-parametros", $aviso_falta_consumo);
            }

            // Tablas de energía activa
            $params_tablas_energia_activa = array(
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_ENERGIA_ACTIVA_SENSOR_TARIFA_ELECTRICA_ESPANYA,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_ENERGIA_ACTIVA_SENSOR_TARIFA_ELECTRICA_ESPANYA),
                "generar_valores_xml" => true
            );
            $cabecera_tablas_energia_activa = array(
                $idiomas->_("Tramo"),
                $idiomas->_("Consumo"),
                $idiomas->_("Coste")
            );

            // Consumo y costes totales de energía activa
            $consumo_energia_activa_total = $resultado_funcion_externa["consumo_energia_activa_total"];
            $coste_energia_activa_total = $resultado_funcion_externa["coste_energia_activa_total"];
            $coste_energia_activa_tarifa_acceso = $resultado_funcion_externa["coste_energia_activa_tarifa_acceso"];
            $coste_energia_activa_directo = $resultado_funcion_externa["coste_energia_activa_directo"];

            // Se calculan el consumo y el coste total y se añaden datos de energía activa de tramos ordenados a las filas de la tabla
            // (si hay coste de consumo de tarifa de acceso y directo se muestran dos tablas de energía activa)
            $datos_tabla_energia_activa = NULL;
            $datos_tabla_energia_activa_directo = NULL;
            $datos_tabla_energia_activa_tarifa_acceso = NULL;

            // TO DO: Para SAI ELIMINAMOS EL DESGLOSE DE CONSUMIDOR DIRECTO Y TARIFA DE ACCESO
            // TO DO: Para la red Ejener de axon lo mismo
            $id_red = $_SESSION["id_red"];
            $bd_red = BaseDatosRed::dame_base_datos();
            $consulta = "SELECT clientes.nombre, redes.nombre AS nombre_red FROM clientes INNER JOIN redes ON clientes.id=redes.cliente WHERE redes.id = ".$id_red;
            $res = $bd_red->ejecuta_consulta($consulta);
            $fila = $res->dame_siguiente_fila();
            $nombre_cliente = $fila["nombre"];
            $nombre_red = $fila["nombre_red"];


        if ($coste_energia_activa_tarifa_acceso >= 0 && !is_null($coste_energia_activa_directo))
            {
                // SI EL CLIENTE NO ES SAI o red Ejener, PINTAMOS LAS TABLAS DESGLOSADAS EN CONSUMIDOR DIRECTO Y ATR
                if(($nombre_cliente != 'SAI Ingeniería') AND ($nombre_red != 'Ejener'))
                {
                    // Tablas de energía activa
                    $tabla_energia_activa_directo = new TablaDatos(
                        "tabla-energia-activa-directo-simulador-factura-electrica",
                        $idiomas->_("Energía activa")." (".$idiomas->_("consumidor directo").")",
                        TIPO_TABLA_DATOS_LISTA,
                        $params_tablas_energia_activa
                    );
                    $tabla_energia_activa_directo->anyade_cabecera("", $cabecera_tablas_energia_activa);
                    $tabla_energia_activa_tarifa_acceso = new TablaDatos(
                        "tabla-energia-activa-tarifa-acceso-simulador-factura-electrica",
                        $idiomas->_("Energía activa")." (".$idiomas->_("tarifa de acceso").")",
                        TIPO_TABLA_DATOS_LISTA,
                        $params_tablas_energia_activa
                    );
                    $tabla_energia_activa_tarifa_acceso->anyade_cabecera("", $cabecera_tablas_energia_activa);

                    // Se recorren los datos de los tramos
                    foreach ($datos_energia_activa_tramos as $tramo => $datos_energia_activa_tramo)
                    {
                        // Consumo, precios y costes
                        $consumo_energia_activa_tramo = $datos_energia_activa_tramo["consumo"];
                        $precio_energia_activa_directo_tramo = $datos_energia_activa_tramo["precio_consumo_directo"];
                        $precio_energia_activa_tarifa_acceso_tramo = $datos_energia_activa_tramo["precio_consumo_tarifa_acceso"];
                        $coste_energia_activa_directo_tramo = $datos_energia_activa_tramo["coste_directo"];
                        $coste_energia_activa_tarifa_acceso_tramo = $datos_energia_activa_tramo["coste_tarifa_acceso"];

                        // Se crean los datos para la fila de la tabla y se añade
                        $cadena_consumo_tramo = formatea_numero($consumo_energia_activa_tramo, 2)." ".$idiomas->_("kWh")."<font class='color-gris-muy-claro'> x </font>";
                        $cadena_consumo_directo_tramo = $cadena_consumo_tramo.formatea_numero($precio_energia_activa_directo_tramo, 6)." ".$idiomas->_("€")."/".$idiomas->_("kWh");
                        $cadena_consumo_tarifa_acceso_tramo = $cadena_consumo_tramo.formatea_numero($precio_energia_activa_tarifa_acceso_tramo, 6)." ".$idiomas->_("€")."/".$idiomas->_("kWh");
                        $cadena_coste_directo_tramo = formatea_numero($coste_energia_activa_directo_tramo, 2, false)." ".$unidad_medida_coste;
                        $cadena_coste_tarifa_acceso_tramo = formatea_numero($coste_energia_activa_tarifa_acceso_tramo, 2, false)." ".$unidad_medida_coste;

                        // Se añade la filas de las tablas
                        $datos_fila_energia_activa_directo_tramo = array(
                            "P".$tramo,
                            $cadena_consumo_directo_tramo,
                            $cadena_coste_directo_tramo);
                        $tabla_energia_activa_directo->anyade_fila("fila-energia-activa-directo-tramo".$tramo, $datos_fila_energia_activa_directo_tramo);
                        $datos_fila_energia_activa_tarifa_acceso_tramo = array(
                            "P".$tramo,
                            $cadena_consumo_tarifa_acceso_tramo,
                            $cadena_coste_tarifa_acceso_tramo);
                        $tabla_energia_activa_tarifa_acceso->anyade_fila("fila-energia-activa-tarifa-acceso-tramo".$tramo, $datos_fila_energia_activa_tarifa_acceso_tramo);
                    }

                    // Costes totales de energía activa
                    $pie_tabla_energia_activa_directo = $idiomas->_("Coste total").": ".formatea_numero($coste_energia_activa_directo, 2, false)." ".$unidad_medida_coste;
                    $tabla_energia_activa_directo->anyade_pie($pie_tabla_energia_activa_directo);
                    $pie_tabla_energia_activa_tarifa_acceso = $idiomas->_("Coste total").": ".formatea_numero($coste_energia_activa_tarifa_acceso, 2, false)." ".$unidad_medida_coste;
                    $tabla_energia_activa_tarifa_acceso->anyade_pie($pie_tabla_energia_activa_tarifa_acceso);

                    // Datos de las tablas de energía activa
                    $datos_tabla_energia_activa_directo = $tabla_energia_activa_directo->dame_tabla();
                    $datos_tabla_energia_activa_tarifa_acceso = $tabla_energia_activa_tarifa_acceso->dame_tabla();

                }else
                {
                    // PARA SAI PONEMOS LA TABLA UNICA DE ENERGIA
                    $tabla_energia_activa = new TablaDatos(
                        "tabla-energia-activa-simulador-factura-electrica",
                        $idiomas->_("Energía activa"),
                        TIPO_TABLA_DATOS_LISTA,
                        $params_tablas_energia_activa
                    );
                    $tabla_energia_activa->anyade_cabecera("", $cabecera_tablas_energia_activa);

                    // Se recorren los datos de los tramos
                    foreach ($datos_energia_activa_tramos as $tramo => $datos_energia_activa_tramo)
                    {
                        // Consumo, precios y costes (sumamos los precios y costes para juntarlos en una única tabla)
                        $consumo_energia_activa_tramo = $datos_energia_activa_tramo["consumo"];
                        $precio_energia_activa_directo_tramo = $datos_energia_activa_tramo["precio_consumo_directo"];
                        $precio_energia_activa_tarifa_acceso_tramo = $datos_energia_activa_tramo["precio_consumo_tarifa_acceso"];
                        $precio_energia_activa_tramo = $precio_energia_activa_directo_tramo + $precio_energia_activa_tarifa_acceso_tramo;
                        $coste_energia_activa_directo_tramo = $datos_energia_activa_tramo["coste_directo"];
                        $coste_energia_activa_tarifa_acceso_tramo = $datos_energia_activa_tramo["coste_tarifa_acceso"];
                        $coste_energia_activa_tramo = $coste_energia_activa_directo_tramo + $coste_energia_activa_tarifa_acceso_tramo;

                        // Se crean los datos para la fila de la tabla y se añade
                        $cadena_consumo_tramo = formatea_numero($consumo_energia_activa_tramo, 2)." ".$idiomas->_("kWh")."<font class='color-gris-muy-claro'> x </font>";
                        $cadena_consumo_tramo .= formatea_numero($precio_energia_activa_tramo, 6)." ".$idiomas->_("€")."/".$idiomas->_("kWh");
                        $cadena_coste_tramo = formatea_numero($coste_energia_activa_tramo, 2, false)." ".$unidad_medida_coste;

                        // Se añade la fila de la tabla
                        $datos_fila_energia_activa_tramo = array(
                            "P".$tramo,
                            $cadena_consumo_tramo,
                            $cadena_coste_tramo);
                        $tabla_energia_activa->anyade_fila("fila-energia-activa-tramo".$tramo, $datos_fila_energia_activa_tramo);
                    }

                    // Coste total de energía activa
                    $pie_tabla_energia_activa = $idiomas->_("Coste total").": ".formatea_numero($coste_energia_activa_total, 2, false)." ".$unidad_medida_coste;
                    $tabla_energia_activa->anyade_pie($pie_tabla_energia_activa);

                    // Datos de la tabla de energía activa
                    $datos_tabla_energia_activa = $tabla_energia_activa->dame_tabla();
                }
            }
            else
            {
                // Tabla de energía activa
                $tabla_energia_activa = new TablaDatos(
                    "tabla-energia-activa-simulador-factura-electrica",
                    $idiomas->_("Energía activa"),
                    TIPO_TABLA_DATOS_LISTA,
                    $params_tablas_energia_activa
                );
                $tabla_energia_activa->anyade_cabecera("", $cabecera_tablas_energia_activa);

                // Se recorren los datos de los tramos
                foreach ($datos_energia_activa_tramos as $tramo => $datos_energia_activa_tramo)
                {
                    // Consumo, precio y coste (total)
                    $consumo_energia_activa_tramo = $datos_energia_activa_tramo["consumo"];
                    $precio_energia_activa_tramo = $datos_energia_activa_tramo["precio_consumo_total"];
                    $coste_energia_activa_tramo = $datos_energia_activa_tramo["coste_total"];

                    // Se crean los datos para la fila de la tabla y se añade
                    $cadena_consumo_tramo = formatea_numero($consumo_energia_activa_tramo, 2)." ".$idiomas->_("kWh")."<font class='color-gris-muy-claro'> x </font>";
                    $cadena_consumo_tramo .= formatea_numero($precio_energia_activa_tramo, 6)." ".$idiomas->_("€")."/".$idiomas->_("kWh");
                    $cadena_coste_tramo = formatea_numero($coste_energia_activa_tramo, 2, false)." ".$unidad_medida_coste;

                    // Se añade la fila de la tabla
                    $datos_fila_energia_activa_tramo = array(
                        "P".$tramo,
                        $cadena_consumo_tramo,
                        $cadena_coste_tramo);
                    $tabla_energia_activa->anyade_fila("fila-energia-activa-tramo".$tramo, $datos_fila_energia_activa_tramo);
                }

                // Coste total de energía activa
                $pie_tabla_energia_activa = $idiomas->_("Coste total").": ".formatea_numero($coste_energia_activa_total, 2, false)." ".$unidad_medida_coste;
                $tabla_energia_activa->anyade_pie($pie_tabla_energia_activa);

                // Datos de la tabla de energía activa
                $datos_tabla_energia_activa = $tabla_energia_activa->dame_tabla();
            }

            // Potencia
            $tipo_calculo_coste_potencias = $resultado_funcion_externa["tipo_calculo_coste_potencias"];
            $datos_potencia_tramos = $resultado_funcion_externa["datos_potencia_tramos"];

            // Tabla de potencia
            switch ($tipo_calculo_coste_potencias)
            {
                case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES:
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_EXCESOS_MAXIMOS_MENSUALES_ESPANYA;
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_EXCESOS_MAXIMOS_MENSUALES_ESPANYA);
                    break;
                }
                case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS:
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_EXCESOS_CUARTOHORARIOS_ESPANYA;
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_EXCESOS_CUARTOHORARIOS_ESPANYA);
                    break;
                }
                case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO:
                    {
                        $numero_columnas = NUMERO_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_EXCESOS_CUARTOHORARIOS_ESPANYA;
                        $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_EXCESOS_CUARTOHORARIOS_ESPANYA);
                        break;
                    }
                case TIPO_CALCULO_COSTE_POTENCIAS_SIN_EXCESOS:
                {
                    $numero_columnas = NUMERO_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_SIN_EXCESOS_ESPANYA;
                    $anchuras_columnas = unserialize(ANCHURAS_COLUMNAS_TABLA_POTENCIA_SENSOR_TARIFA_ELECTRICA_SIN_EXCESOS_ESPANYA);
                }
            }

            $params_tabla_potencia = array(
                "numero_columnas" => $numero_columnas,
                "anchuras_columnas" => $anchuras_columnas,
                "generar_valores_xml" => true
            );
            $tabla_potencia = new TablaDatos(
                "tabla-potencia-simulador-factura-electrica",
                $idiomas->_("Potencia"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla_potencia
            );
            switch ($tipo_calculo_coste_potencias)
            {
                case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES:
                {
                    $cabecera_tabla_potencia = array(
                        $idiomas->_("Tramo"),
                        $idiomas->_("Potencia contratada"),
                        $idiomas->_("Potencia máxima"),
                        $idiomas->_("Potencia facturada"),
                        $idiomas->_("Coste")
                    );
                    break;
                }
                case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS:
                {
                    $cabecera_tabla_potencia = array(
                        $idiomas->_("Tramo"),
                        $idiomas->_("Potencia"),
                        $idiomas->_("Coste")
                    );
                    break;
                }
                case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO:
                {
                    $cabecera_tabla_potencia = array(
                        $idiomas->_("Tramo"),
                        $idiomas->_("Potencia"),
                        $idiomas->_("Coste")
                    );
                    break;
                }
                case TIPO_CALCULO_COSTE_POTENCIAS_SIN_EXCESOS:
                {
                    $cabecera_tabla_potencia = array(
                        $idiomas->_("Tramo"),
                        $idiomas->_("Potencia"),
                        $idiomas->_("Coste")
                    );
                    break;
                }
            }
            $tabla_potencia->anyade_cabecera("", $cabecera_tabla_potencia);

            // Se agrupan las filas de potencias por tramos "agrupados" (si es necesario)
            $tramos_potencias_iguales = $caracteristicas_tipo_tarifa_electrica["tramos_potencias_iguales"];
            if ($tramos_potencias_iguales !== NULL)
            {
                $datos_potencias_tramos_agrupadas = array();
                $numero_tramo_agrupado = 1;
                foreach ($tramos_potencias_iguales as $nombre_tramo => $tramos_potencia_igual)
                {
                    $datos_potencia_tramo_agrupado = array(
                        "nombre" => $nombre_tramo,
                        "potencia_contratada" => NULL,
                        "potencia_maxima" => -INF,
                        "potencia_facturada" => NULL,
                        "precio_potencia" => 0,
                        "coste_facturado" => 0,
												"numero_tramo_exceso" => 0);
                    foreach ($tramos_potencia_igual as $tramo_potencia_igual)
                    {
                        $datos_potencia_tramo = $datos_potencia_tramos[$tramo_potencia_igual];
                        if ($datos_potencia_tramo_agrupado["potencia_contratada"] === NULL)
                        {
                            $datos_potencia_tramo_agrupado["potencia_contratada"] = $datos_potencia_tramo["potencia_contratada"];
                        }
                        if ($datos_potencia_tramo_agrupado["potencia_maxima"] < $datos_potencia_tramo["potencia_maxima"])
                        {
                            $datos_potencia_tramo_agrupado["potencia_maxima"] = $datos_potencia_tramo["potencia_maxima"];
														$datos_potencia_tramo_agrupado["numero_tramo_exceso"] = $tramo_potencia_igual;
                        }
                        if ($datos_potencia_tramo_agrupado["potencia_facturada"] === NULL)
                        {
                            $datos_potencia_tramo_agrupado["potencia_facturada"] += $datos_potencia_tramo["potencia_facturada"];
                        }
                        $datos_potencia_tramo_agrupado["precio_potencia"] += $datos_potencia_tramo["precio_potencia"];
                        $datos_potencia_tramo_agrupado["coste_facturado"] += $datos_potencia_tramo["coste_facturado"];
                    }
                    $datos_potencias_tramos_agrupadas[$numero_tramo_agrupado] = $datos_potencia_tramo_agrupado;
                    $numero_tramo_agrupado += 1;
                }
                $datos_potencia_tramos = $datos_potencias_tramos_agrupadas;
				$datos_excesos_potencia_tramos = $datos_exceso_potencia_tramo_agrupado;

            }

            // Se calcula el coste total y se añaden datos de potencia de tramos ordenados a las filas de la tabla
            $coste_potencia_total = $resultado_funcion_externa["coste_potencia_total"];
            foreach ($datos_potencia_tramos as $tramo => $datos_potencia_tramo)
            {
                // Nombre del tramo (si es agrupado se le asigna un nombre, si no es el número de tramo)
                if (array_key_exists("nombre", $datos_potencia_tramo) == true)
                {
                    $nombre_tramo = $datos_potencia_tramo["nombre"];
                }
                else
                {
                    $nombre_tramo = "P".$tramo;
                }

                // Potencias y coste
                $potencia_contratada_tramo = $datos_potencia_tramo["potencia_contratada"];
                $potencia_maxima_tramo = $datos_potencia_tramo["potencia_maxima"];
                $potencia_facturada_tramo = $datos_potencia_tramo["potencia_facturada"];
                $precio_potencia_tramo = $datos_potencia_tramo["precio_potencia"];
                $coste_potencia_tramo = $datos_potencia_tramo["coste_facturado"];

                // Cadenas de potencias y coste
                $cadena_potencia_contratada_tramo = formatea_numero($potencia_contratada_tramo, 2)." ".$idiomas->_("kW");
                $cadena_potencia_maxima_tramo = formatea_numero($potencia_maxima_tramo, 2)." ".$idiomas->_("kW");
                $cadena_coste_potencia_tramo = formatea_numero($coste_potencia_tramo, 2, false)." ".$unidad_medida_coste;

                // Detalle de potencia facturada
                $cadena_potencia_facturada_tramo = formatea_numero($potencia_facturada_tramo, 2)." ".$idiomas->_("kW")."<font class='color-gris-muy-claro'> x </font>";
                $cadena_potencia_facturada_tramo .= formatea_numero($precio_potencia_tramo, 6)." ".$idiomas->_("€")."/".$idiomas->_("kW")."-".$idiomas->_("día")."<font class='color-gris-muy-claro'> x </font>";
                $cadena_potencia_facturada_tramo .= $numero_dias." ".$idiomas->_("días");

                // Se crean los datos para la fila de la tabla y se añade
                switch ($tipo_calculo_coste_potencias)
                {
                    case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES:
                    {
                        $datos_fila_potencia_tramo = array(
                            $nombre_tramo,
                            $cadena_potencia_contratada_tramo,
                            $cadena_potencia_maxima_tramo,
                            $cadena_potencia_facturada_tramo,
                            $cadena_coste_potencia_tramo);
                        break;
                    }
                    case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS:
                    {
                        $datos_fila_potencia_tramo = array(
                            $nombre_tramo,
                            $cadena_potencia_facturada_tramo,
                            $cadena_coste_potencia_tramo);
                        break;
                    }
                    case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO:
                    {
                        $datos_fila_potencia_tramo = array(
                            $nombre_tramo,
                            $cadena_potencia_facturada_tramo,
                            $cadena_coste_potencia_tramo);
                        break;
                    }
                    case TIPO_CALCULO_COSTE_POTENCIAS_SIN_EXCESOS:
                    {
                        $datos_fila_potencia_tramo = array(
                            $nombre_tramo,
                            $cadena_potencia_facturada_tramo,
                            $cadena_coste_potencia_tramo);
                        break;
                    }
                }
                $tabla_potencia->anyade_fila("fila-potencia-tramo".$tramo, $datos_fila_potencia_tramo);
            }

            // Coste total de potencia
            $pie_tabla_potencia = $idiomas->_("Coste total").": ".formatea_numero($coste_potencia_total, 2, false)." ".$unidad_medida_coste;
            $tabla_potencia->anyade_pie($pie_tabla_potencia);


            // Potencia máxima y excesos de potencia
            switch ($tipo_calculo_coste_potencias)
            {
                case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS:
                {
                    $hay_datos_potencia_maxima_excesos_potencia = true;
                    $datos_excesos_potencia_tramos = $resultado_funcion_externa["datos_excesos_potencia_tramos"];

                    // Tabla de potencia máxima y excesos de potencia
                    $params_tabla_potencia_maxima_excesos_potencia = array(
                        "numero_columnas" => NUMERO_COLUMNAS_TABLA_POTENCIA_MAXIMA_EXCESOS_POTENCIA_SENSOR_TARIFA_ELECTRICA_ESPANYA,
                        "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_POTENCIA_MAXIMA_EXCESOS_POTENCIA_SENSOR_TARIFA_ELECTRICA_ESPANYA),
                        "generar_valores_xml" => true
                    );
                    $tabla_potencia_maxima_excesos_potencia = new TablaDatos(
                        "tabla-potencia-maxima-excesos-potencia-simulador-factura-electrica",
                        $idiomas->_("Potencia máxima y excesos de potencia"),
                        TIPO_TABLA_DATOS_LISTA,
                        $params_tabla_potencia_maxima_excesos_potencia
                    );
                    $cabecera_tabla_potencia_maxima_excesos_potencia = array(
                        $idiomas->_("Tramo"),
                        $idiomas->_("Potencia máxima"),
                        $idiomas->_("Excesos de potencia")." (".$idiomas->_("Aei").")",
                        $idiomas->_("Coste")
                    );
                    $tabla_potencia_maxima_excesos_potencia->anyade_cabecera("", $cabecera_tabla_potencia_maxima_excesos_potencia);

                    // Se calcula el coste total y se añaden datos de potencia máxima y excesos de potencia de tramos ordenados a las filas de la tabla
                    $coste_excesos_potencia_total = $resultado_funcion_externa["coste_excesos_potencia_total"];
                    foreach ($datos_energia_activa_tramos as $tramo => $datos_energia_activa_tramo)
                    {

					// Nombre del tramo (si es agrupado se le asigna un nombre, si no es el número de tramo)
					if (array_key_exists("nombre", $datos_potencia_tramo) == true)
					{
						$nombre_tramo = $datos_potencia_tramo["nombre"];
					}
					else
					{
						$nombre_tramo = "P".$tramo;
					}

					// Potencia máxima
                    $datos_potencia_tramo = $datos_potencia_tramos[$tramo];
                    $potencia_maxima_tramo = $datos_potencia_tramo["potencia_maxima"];

                    // Constantes de cálculo de coste de excesos de potencia
                    $precio_penalizacion_sobrepotencia_tramo = TarifaElectrica_Espanya::dame_precio_penalizacion_sobrepotencia_Espanya($tipo_tarifa_electrica);
                    $log = dame_log();
                    $log -> debug("El precio_penalizacion_sobrepotencia_tramo es: ");
                    $log -> debug($precio_penalizacion_sobrepotencia_tramo);
                    if (stripos($tipo_tarifa_electrica, "2025_abril") !== false or stripos($tipo_tarifa_electrica, "2026") !== false) {
                        $precio_penalizacion_sobrepotencia_tramo = $precio_penalizacion_sobrepotencia_tramo[$tramo - 1];
                        $log = dame_log();
                        $log -> debug("DENTRO DEL IF DE ABRIL 2025 penalizacion_sobrepotencia_tramo es: ");
                        $log -> debug($precio_penalizacion_sobrepotencia_tramo);
                    }
					$penalizacion_potencia_tarifa = TarifaElectrica_Espanya::dame_penalizacion_potencias_Espanya($tipo_tarifa_electrica);
                    $k_tramo = $penalizacion_potencia_tarifa[$tramo - 1];

                    // Aei y coste
                    if (array_key_exists($tramo, $datos_excesos_potencia_tramos) == true)
                    {
                        $datos_exceso_potencia_tramo = $datos_excesos_potencia_tramos[$tramo];                                              
                        $aei_tramo = $datos_exceso_potencia_tramo["aei"];
                        $coste_exceso_potencia_tramo = $datos_exceso_potencia_tramo["penalizacion_sobrepotencia"];
                        $log = dame_log();
                        $log -> debug("[SMR] La información de los datos de excesos de potencia es: ");
                        $log -> debug($datos_exceso_potencia_tramo);
                    }
                    else
                    {
                        $aei_tramo = 0;
                        $coste_exceso_potencia_tramo = 0;
                    }

                    // Se crean los datos para la fila de la tabla y se añade
					if($caracteristicas_tipo_tarifa_electrica["prorrateo_potencias"])
					{
						$numero_dias_prorrateo_tramo = $datos_exceso_potencia_tramo["numero_dias_prorrateo"];

                        // Comprobar qué parametros se tienen que mostrar en el texto para añadirlos o no
                        $texto_divisor_30 = mostrar_division_30($tipo_tarifa_electrica) ? "<font class='color-gris-muy-claro'> / </font> 30 " : "";
                        $texto_multiplicacion_k_tramo = mostrar_multiplicacion_k_tramo($tipo_tarifa_electrica) ? (formatea_numero($k_tramo,2) . "<font class='color-gris-muy-claro'> x </font>") : "";
						$datos_fila_potencia_maxima_exceso_potencia_tramo = array(
                            $nombre_tramo,
                            formatea_numero($potencia_maxima_tramo, 2)." ".$idiomas->_("kW"),
                            formatea_numero($aei_tramo, 2)." ".$idiomas->_("kW")."<font class='color-gris-muy-claro'> x </font>".
                                formatea_numero($precio_penalizacion_sobrepotencia_tramo, 6)." ".$unidad_medida_coste."/".$idiomas->_("kW")."<font class='color-gris-muy-claro'> x </font>".
                                $texto_multiplicacion_k_tramo .
                                $numero_dias_prorrateo_tramo." ".$idiomas->_("días").
                                $texto_divisor_30,
                            formatea_numero($coste_exceso_potencia_tramo, 2, false)." ".$unidad_medida_coste);

					}
					else
					{
                        // Comprobar qué parametros se tienen que mostrar en el texto para añadirlos o no
                        $texto_multiplicacion_k_tramo = mostrar_multiplicacion_k_tramo($tipo_tarifa_electrica) ? ("<font class='color-gris-muy-claro'> x </font>".formatea_numero($k_tramo,2)) : "";
                        $datos_fila_potencia_maxima_exceso_potencia_tramo = array(
                            $nombre_tramo,
                            formatea_numero($potencia_maxima_tramo, 2)." ".$idiomas->_("kW"),
                            formatea_numero($aei_tramo, 2)." ".$idiomas->_("kW")."<font class='color-gris-muy-claro'> x </font>".
                                formatea_numero($precio_penalizacion_sobrepotencia_tramo, 6)." ".$unidad_medida_coste."/".$idiomas->_("kW").
                                $texto_multiplicacion_k_tramo,
                            formatea_numero($coste_exceso_potencia_tramo, 2, false)." ".$unidad_medida_coste);
												}
                        $tabla_potencia_maxima_excesos_potencia->anyade_fila("fila-potencia-maxima-exceso-potencia-tramo".$tramo, $datos_fila_potencia_maxima_exceso_potencia_tramo);
                    }

                    // Coste total de excesos de potencia
                    $pie_tabla_potencia_maxima_excesos_potencia = $idiomas->_("Coste total").": ".formatea_numero($coste_excesos_potencia_total, 2, false)." ".$unidad_medida_coste;
                    $tabla_potencia_maxima_excesos_potencia->anyade_pie($pie_tabla_potencia_maxima_excesos_potencia);

                    // Datos de la tabla
                    $datos_tabla_potencia_maxima_excesos_potencia = $tabla_potencia_maxima_excesos_potencia->dame_tabla();
                    break;
                }
                case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO:
                {
                    $hay_datos_potencia_maxima_excesos_potencia = true;
                    $datos_excesos_potencia_tramos = $resultado_funcion_externa["datos_excesos_potencia_tramos"];

                    // Tabla de potencia máxima y excesos de potencia
                    $params_tabla_potencia_maxima_excesos_potencia = array(
                        "numero_columnas" => NUMERO_COLUMNAS_TABLA_POTENCIA_MAXIMA_EXCESOS_POTENCIA_SENSOR_TARIFA_ELECTRICA_ESPANYA,
                        "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_POTENCIA_MAXIMA_EXCESOS_POTENCIA_SENSOR_TARIFA_ELECTRICA_ESPANYA),
                        "generar_valores_xml" => true
                    );
                    $tabla_potencia_maxima_excesos_potencia = new TablaDatos(
                        "tabla-potencia-maxima-excesos-potencia-simulador-factura-electrica",
                        $idiomas->_("Potencia máxima y excesos de potencia"),
                        TIPO_TABLA_DATOS_LISTA,
                        $params_tabla_potencia_maxima_excesos_potencia
                    );
                    $cabecera_tabla_potencia_maxima_excesos_potencia = array(
                        $idiomas->_("Tramo"),
                        $idiomas->_("Potencia máxima"),
                        $idiomas->_("Excesos de potencia")." (".$idiomas->_("Maxímetro").")",
                        $idiomas->_("Coste")
                    );
                    $tabla_potencia_maxima_excesos_potencia->anyade_cabecera("", $cabecera_tabla_potencia_maxima_excesos_potencia);

                    // Se calcula el coste total y se añaden datos de potencia máxima y excesos de potencia de tramos ordenados a las filas de la tabla
                    $coste_excesos_potencia_total = $resultado_funcion_externa["coste_excesos_potencia_total"];
                    foreach ($datos_potencia_tramos as $tramo => $datos_potencia_tramo)
                    {
						$tramo_potencia = $tramo;
						// Nombre del tramo (si es agrupado se le asigna un nombre, si no es el número de tramo)
						if (array_key_exists("nombre", $datos_potencia_tramo) == true)
						{
							$nombre_tramo = $datos_potencia_tramo["nombre"];
							$tramo_potencia = $datos_potencia_tramo["numero_tramo_exceso"];
						}
						else
						{
							$nombre_tramo = "P".$tramo;
						}


						// Potencia máxima
                        $datos_potencia_tramo = $datos_potencia_tramos[$tramo];
                        $potencia_maxima_tramo = $datos_potencia_tramo["potencia_maxima"];

                        // Constantes de cálculo de coste de excesos de potencia
                        $precio_penalizacion_sobrepotencia_tramo = TarifaElectrica_Espanya::dame_precio_penalizacion_sobrepotencia_Espanya($tipo_tarifa_electrica);
                        $log = dame_log();
                        $log -> debug("El precio_penalizacion_sobrepotencia_tramo es: ");
                        $log -> debug($precio_penalizacion_sobrepotencia_tramo);
                        if (stripos($tipo_tarifa_electrica, "2025_ABRIL") !== false or stripos($tipo_tarifa_electrica, "2026") !== false) {
                            $precio_penalizacion_sobrepotencia_tramo = $precio_penalizacion_sobrepotencia_tramo[$tramo - 1];
                            $log = dame_log();
                            $log -> debug("Entra al if de 2025 ");
                        }
                        $penalizacion_potencia_tarifa = TarifaElectrica_Espanya::dame_penalizacion_potencias_Espanya($tipo_tarifa_electrica);

                        // Aei y coste
                        if (array_key_exists($tramo, $datos_excesos_potencia_tramos) == true)
                        {
                            $datos_exceso_potencia_tramo = $datos_excesos_potencia_tramos[$tramo_potencia];
                            $aei_tramo = $datos_exceso_potencia_tramo["aei"];
                            $coste_exceso_potencia_tramo = $datos_exceso_potencia_tramo["penalizacion_sobrepotencia"];
                            $log = dame_log();
                            $log -> debug("[SMR] La información de los datos de excesos de potencia es: ");
                            $log -> debug($datos_exceso_potencia_tramo);
                        }
                        else
                        {
                            $aei_tramo = 0;
                            $coste_exceso_potencia_tramo = 0;
                        }
						$numero_dias_prorrateo_tramo = $datos_exceso_potencia_tramo["numero_dias_prorrateo"];
                        // Se crean los datos para la fila de la tabla y se añade
                        $log = dame_log();
                        $log -> debug("El prorrateo en el método donde se calcula el precio de la penalización:");
                        $log -> debug($caracteristicas_tipo_tarifa_electrica["prorrateo_potencias"]);
                        
						if($caracteristicas_tipo_tarifa_electrica["prorrateo_potencias"])
						{
                             // Comprobar qué parametros se tienen que mostrar en el texto para añadirlos o no
                            $texto_divisor_30 = mostrar_division_30($tipo_tarifa_electrica) ? "<font class='color-gris-muy-claro'> / </font> 30 " : "";
							$datos_fila_potencia_maxima_exceso_potencia_tramo = array(
								$nombre_tramo,
								formatea_numero($potencia_maxima_tramo, 2)." ".$idiomas->_("kW"),
								formatea_numero($aei_tramo, 2)." ".$idiomas->_("kW")."<font class='color-gris-muy-claro'> x </font>".
								formatea_numero($precio_penalizacion_sobrepotencia_tramo, 6)." ".$unidad_medida_coste."/".$idiomas->_("kW").
                                    "<font class='color-gris-muy-claro'> x </font>".$numero_dias_prorrateo_tramo." ".$idiomas->_("días").
                                    $texto_divisor_30,
								formatea_numero($coste_exceso_potencia_tramo, 2, false)." ".$unidad_medida_coste);

						}
						elseif ($caracteristicas_tipo_tarifa_electrica["precio_exceso_potencia_dia"])
						{
							$datos_fila_potencia_maxima_exceso_potencia_tramo = array(
								$nombre_tramo,
								formatea_numero($potencia_maxima_tramo, 2)." ".$idiomas->_("kW"),
								formatea_numero($aei_tramo, 2)." ".$idiomas->_("kW")."<font class='color-gris-muy-claro'> x </font>".
								formatea_numero($precio_penalizacion_sobrepotencia_tramo, 6)." ".$unidad_medida_coste."/".$idiomas->_("kW").
                                    "<font class='color-gris-muy-claro'> x </font>".$numero_dias_prorrateo_tramo." ".$idiomas->_("días"),
								formatea_numero($coste_exceso_potencia_tramo, 2, false)." ".$unidad_medida_coste);
						}
						else{
							$datos_fila_potencia_maxima_exceso_potencia_tramo = array(
                                $nombre_tramo,
                                formatea_numero($potencia_maxima_tramo, 2)." ".$idiomas->_("kW"),
                                formatea_numero($aei_tramo, 2)." ".$idiomas->_("kW")."<font class='color-gris-muy-claro'> x </font>".
                                formatea_numero($precio_penalizacion_sobrepotencia_tramo, 6)." ".$unidad_medida_coste."/".$idiomas->_("kW"),
                                formatea_numero($coste_exceso_potencia_tramo, 2, false)." ".$unidad_medida_coste);
						}
                        $tabla_potencia_maxima_excesos_potencia->anyade_fila("fila-potencia-maxima-exceso-potencia-tramo".$tramo, $datos_fila_potencia_maxima_exceso_potencia_tramo);
                    }

                    // Coste total de excesos de potencia
                    $pie_tabla_potencia_maxima_excesos_potencia = $idiomas->_("Coste total").": ".formatea_numero($coste_excesos_potencia_total, 2, false)." ".$unidad_medida_coste;
                    $tabla_potencia_maxima_excesos_potencia->anyade_pie($pie_tabla_potencia_maxima_excesos_potencia);

                    // Datos de la tabla
                    $datos_tabla_potencia_maxima_excesos_potencia = $tabla_potencia_maxima_excesos_potencia->dame_tabla();
                    break;
                }
                case TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES:
                {
                    $hay_datos_potencia_maxima_excesos_potencia = false;
                    $datos_tabla_potencia_maxima_excesos_potencia = "";
                    break;
                }
                case TIPO_CALCULO_COSTE_POTENCIAS_SIN_EXCESOS:
                {
                    $hay_datos_potencia_maxima_excesos_potencia = false;
                    $datos_tabla_potencia_maxima_excesos_potencia = "";
                    break;
                }
            }

            // Energía reactiva
            if (array_key_exists("datos_energia_reactiva_inductiva_tramos", $resultado_funcion_externa) == true or
							array_key_exists("datos_energia_reactiva_capacitiva_tramos", $resultado_funcion_externa))
            {
                $hay_datos_energia_reactiva = true;

                // Tabla de energía reactiva
                $params_tabla_energia_reactiva = array(
                    "numero_columnas" => NUMERO_COLUMNAS_TABLA_ENERGIA_REACTIVA_SENSOR_TARIFA_ELECTRICA_ESPANYA,
                    "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_ENERGIA_REACTIVA_SENSOR_TARIFA_ELECTRICA_ESPANYA),
                    "generar_valores_xml" => true
                );
                $tabla_energia_reactiva = new TablaDatos(
                    "tabla-energia-reactiva-simulador-factura-electrica",
                    $idiomas->_("Energía reactiva"),
                    TIPO_TABLA_DATOS_LISTA,
                    $params_tabla_energia_reactiva
                );
                $cabecera_tabla_energia_reactiva = array(
                    $idiomas->_("Tramo"),
                    $idiomas->_("Coseno de phi"),
                    $idiomas->_("Exceso"),
                    $idiomas->_("Coste")
                );
                $tabla_energia_reactiva->anyade_cabecera("", $cabecera_tabla_energia_reactiva);

                // Se calcula el coste total y se añaden datos de energía reactiva de tramos ordenados a las filas de la tabla
                $coste_energia_reactiva_total = $resultado_funcion_externa["coste_energia_reactiva_total"];
                foreach ($resultado_funcion_externa["datos_energia_reactiva_inductiva_tramos"] as $tramo => $datos_energia_reactiva_tramo)
                {
                    // Coseno de phi, exceso de reactiva y coste
                    $coseno_phi_energia_reactiva_tramo = $datos_energia_reactiva_tramo["coseno_phi"];
                    $exceso_energia_reactiva_tramo = $datos_energia_reactiva_tramo["exceso"];
                    $coste_energia_reactiva_tramo = $datos_energia_reactiva_tramo["coste"];

                    // Se crean los datos para la fila de la tabla y se añade
                    $datos_fila_energia_reactiva_tramo = array(
                        "P".$tramo,
                        formatea_numero($coseno_phi_energia_reactiva_tramo, 2),
                        formatea_numero($exceso_energia_reactiva_tramo, 2)." ".$idiomas->_("kVAr"),
                        formatea_numero($coste_energia_reactiva_tramo, 2, false)." ".$unidad_medida_coste);
                    $tabla_energia_reactiva->anyade_fila("fila-energia-reactiva-inductiva-tramo".$tramo, $datos_fila_energia_reactiva_tramo);
                }
								foreach ($resultado_funcion_externa["datos_energia_reactiva_capacitiva_tramos"] as $tramo => $datos_energia_reactiva_tramo)
                {
                    // Coseno de phi, exceso de reactiva y coste
                    $coseno_phi_energia_reactiva_tramo = $datos_energia_reactiva_tramo["coseno_phi"];
                    $exceso_energia_reactiva_tramo = $datos_energia_reactiva_tramo["exceso"];
                    $coste_energia_reactiva_tramo = $datos_energia_reactiva_tramo["coste"];

                    // Se crean los datos para la fila de la tabla y se añade
                    $datos_fila_energia_reactiva_tramo = array(
                        "P".$tramo." (".$idiomas->_("Capacitiva").")",
                        formatea_numero($coseno_phi_energia_reactiva_tramo, 2),
                        formatea_numero($exceso_energia_reactiva_tramo, 2)." ".$idiomas->_("kVAr"),
                        formatea_numero($coste_energia_reactiva_tramo, 2, false)." ".$unidad_medida_coste);
                    $tabla_energia_reactiva->anyade_fila("fila-energia-reactiva-capacitiva-tramo".$tramo, $datos_fila_energia_reactiva_tramo);
                }

                // Coste total de energía reactiva
                $pie_tabla_energia_reactiva = $idiomas->_("Coste total").": ".formatea_numero($coste_energia_reactiva_total, 2, false)." ".$unidad_medida_coste;
                $tabla_energia_reactiva->anyade_pie($pie_tabla_energia_reactiva);

                // Datos de la tabla
                $datos_tabla_energia_reactiva = $tabla_energia_reactiva->dame_tabla();
            }
            else
            {
                $hay_datos_energia_reactiva = false;
                $datos_tabla_energia_reactiva = "";
            }

            // Coste total de energía y potencia (sin otros conceptos)
            $coste_energia_potencia_total = $resultado_funcion_externa["coste_energia_potencia_total"];

            // Concepto pendiente MEFF REE
            if (array_key_exists("coste_concepto_pendiente_MEFF_REE", $resultado_funcion_externa) == true)
            {
                $hay_concepto_pendiente_MEFF_REE = true;
            }
            else
            {
                $hay_concepto_pendiente_MEFF_REE = false;
            }

            // Otros conceptos
            $impuesto_electrico = $resultado_funcion_externa["impuesto_electrico"];
            $tipo_alquiler_contador = $resultado_funcion_externa["tipo_alquiler_contador"];
            $alquiler_contador = $resultado_funcion_externa["alquiler_contador"];
            $costes_conceptos_adicionales = $resultado_funcion_externa["costes_conceptos_adicionales"];
            $coste_impuesto_electrico = $resultado_funcion_externa["coste_impuesto_electrico"];
            $coste_alquiler_contador = $resultado_funcion_externa["coste_alquiler_contador"];
            if ($hay_concepto_pendiente_MEFF_REE == true)
            {
                $coste_concepto_pendiente_MEFF_REE = $resultado_funcion_externa["coste_concepto_pendiente_MEFF_REE"];
            }
            $coste_conceptos_adicionales = $resultado_funcion_externa["coste_conceptos_adicionales"];
            $coste_total_otros_conceptos = $resultado_funcion_externa["coste_total_otros_conceptos"];

            // Tabla de otros conceptos
            $params_tabla_otros_conceptos = array(
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_OTROS_CONCEPTOS_SENSOR_TARIFA_ELECTRICA_ESPANYA,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_OTROS_CONCEPTOS_SENSOR_TARIFA_ELECTRICA_ESPANYA),
                "generar_valores_xml" => true
            );
            $tabla_otros_conceptos = new TablaDatos(
                "tabla-otros-conceptos-simulador-factura-electrica",
                $idiomas->_("Otros conceptos"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla_otros_conceptos
            );
            $cabecera_tabla_otros_conceptos = array(
                $idiomas->_("Concepto"),
                $idiomas->_("Cálculo"),
                $idiomas->_("Coste")
            );
            $tabla_otros_conceptos->anyade_cabecera("", $cabecera_tabla_otros_conceptos);

            // Impuesto eléctrico
            if ($impuesto_electrico > 0)
            {
                // Se crean los datos para la fila de la tabla y se añade
                $datos_fila_impuesto_electrico = array(
                    $idiomas->_("Impuesto eléctrico"),
                    formatea_numero($coste_energia_potencia_total, 2, false)." ".$unidad_medida_coste."<font class='color-gris-muy-claro'> x </font>".
                        $impuesto_electrico." "."%",
                    formatea_numero($coste_impuesto_electrico, 2, false)." ".$unidad_medida_coste);
                $tabla_otros_conceptos->anyade_fila("fila-impuesto-electrico", $datos_fila_impuesto_electrico);
            }

            // Alquiler del contador
            if ($alquiler_contador > 0)
            {
                // Coste del alquiler del contador
                switch ($tipo_alquiler_contador)
                {
                    case TIPO_ALQUILER_CONTADOR_DIARIO:
                    {
                        $texto_calculo_alquiler_contador = formatea_numero($alquiler_contador, 2, false)." ".$unidad_medida_coste."/".$idiomas->_("día")."<font class='color-gris-muy-claro'> x </font>".
                            $numero_dias." ".$idiomas->_("días");
                        break;
                    }
                    case TIPO_ALQUILER_CONTADOR_FIJO:
                    {
                        $texto_calculo_alquiler_contador = formatea_numero($alquiler_contador, 2, false)." ".$unidad_medida_coste;
                        break;
                    }
                }

                // Se crean los datos para la fila de la tabla y se añade
                $datos_fila_alquiler_contador = array(
                    $idiomas->_("Alquiler de contador"),
                    $texto_calculo_alquiler_contador,
                    formatea_numero($coste_alquiler_contador, 2, false)." ".$unidad_medida_coste);
                $tabla_otros_conceptos->anyade_fila("fila-alquiler-contador", $datos_fila_alquiler_contador);
            }
            else
            {
                $coste_alquiler_contador = 0;
            }

            // Concepto pendiente 'MEFF-REE'
            if ($hay_concepto_pendiente_MEFF_REE == true)
            {
                // Se crean los datos para la fila de la tabla y se añade
                if ($coste_concepto_pendiente_MEFF_REE !== NULL)
                {
                    $cadena_coste_concepto_pendiente_MEFF_REE = formatea_numero($coste_concepto_pendiente_MEFF_REE, 2, false)." ".$unidad_medida_coste;
                }
                else
                {
                    $cadena_coste_concepto_pendiente_MEFF_REE = $idiomas->_("ND");
                }
                $datos_fila_concepto_pendiente_MEFF_REE = array(
                    $idiomas->_("Pendiente MEFF-REE"),
                    $cadena_coste_concepto_pendiente_MEFF_REE,
                    $cadena_coste_concepto_pendiente_MEFF_REE);
                $tabla_otros_conceptos->anyade_fila("fila-concepto-pendiente-MEFF-REE", $datos_fila_concepto_pendiente_MEFF_REE);
            }

            // Conceptos adicionales de factura
            $info_conceptos_adicionales_factura = dame_info_conceptos_adicionales_factura_tarifa(MEDICION_ELECTRICIDAD, $id_tarifa);
            for ($i = 0; $i < count($info_conceptos_adicionales_factura); $i++)
            {
                $info_concepto_adicional_factura = $info_conceptos_adicionales_factura[$i];
                $coste_concepto_adicional_factura = $costes_conceptos_adicionales[$i];

                // Formateado del cálculo según el tipo de concepto adicional
                switch ($info_concepto_adicional_factura["tipo"])
                {
                    case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_FIJO:
                    {
                        $cadena_calculo_coste_concepto_adicional = formatea_numero($info_concepto_adicional_factura["coste"], 2, false)." ".$unidad_medida_coste;
                        break;
                    }
                    case TIPO_CONCEPTO_ADICIONAL_FACTURA_TARIFA_DIARIO:
                    {
                        $cadena_calculo_coste_concepto_adicional = formatea_numero($info_concepto_adicional_factura["coste"], 2)." ".$unidad_medida_coste."/".$idiomas->_("día").
                            "<font class='color-gris-muy-claro'> x </font>".
                            $numero_dias." ".$idiomas->_("días");
                        break;
                    }
                }

                // Se crean los datos para la fila de la tabla y se añade
                $datos_fila_concepto_adicional = array(
                    $info_concepto_adicional_factura["nombre"],
                    $cadena_calculo_coste_concepto_adicional,
                    formatea_numero($coste_concepto_adicional_factura, 2, false)." ".$unidad_medida_coste);
                $tabla_otros_conceptos->anyade_fila("fila-concepto-adicional", $datos_fila_concepto_adicional);
            }

            // Base imponible
            $base_imponible =
                $coste_energia_potencia_total +
                $coste_impuesto_electrico +
                $coste_alquiler_contador +
                $coste_conceptos_adicionales;
            if (($hay_concepto_pendiente_MEFF_REE == true) && ($coste_concepto_pendiente_MEFF_REE !== NULL))
            {
                $base_imponible += $coste_concepto_pendiente_MEFF_REE;
            }

            // IVA / IGICs
            $tipo_tarifa_canarias = $caracteristicas_tipo_tarifa_electrica["tipo_tarifa_canarias"];
            if ($tipo_tarifa_canarias == false)
            {
                $iva = $resultado_funcion_externa["iva"];
                $coste_iva = $resultado_funcion_externa["coste_iva"];

                // Se crean los datos para la fila de la tabla y se añade
                $datos_fila_iva = array(
                    $idiomas->_("IVA"),
                    formatea_numero($base_imponible, 2, false)." ".$unidad_medida_coste."<font class='color-gris-muy-claro'> x </font>".
                        $iva." "."%",
                    formatea_numero($coste_iva, 2, false)." ".$unidad_medida_coste);
                $tabla_otros_conceptos->anyade_fila("fila-iva", $datos_fila_iva);
            }
            else
            {
                $igic_reducido = $resultado_funcion_externa["igic_reducido"];
                $igic_normal = $resultado_funcion_externa["igic_normal"];
                $coste_igic_reducido = $resultado_funcion_externa["coste_igic_reducido"];
                $coste_igic_normal = $resultado_funcion_externa["coste_igic_normal"];

                // Conceptos con IGIC 'normal'
                $coste_otros_conceptos_igic_normal = ($coste_alquiler_contador + $coste_conceptos_adicionales);

                // Se crean los datos para las filas de la tabla y se añaden
                $datos_fila_igic_reducido = array(
                    $idiomas->_("IGIC")." (".$idiomas->_("reducido").")",
                    formatea_numero($base_imponible - $coste_otros_conceptos_igic_normal, 2, false)." ".$unidad_medida_coste."<font class='color-gris-muy-claro'> x </font>".
                        $igic_reducido." "."%",
                    formatea_numero($coste_igic_reducido, 2, false)." ".$unidad_medida_coste);
                $tabla_otros_conceptos->anyade_fila("fila-igic-reducido", $datos_fila_igic_reducido);
                if ($coste_otros_conceptos_igic_normal > 0)
                {
                    $datos_fila_igic_normal = array(
                        $idiomas->_("IGIC")." (".$idiomas->_("normal").")",
                        formatea_numero($coste_otros_conceptos_igic_normal, 2, false)." ".$unidad_medida_coste."<font class='color-gris-muy-claro'> x </font>".
                            $igic_normal." "."%",
                        formatea_numero($coste_igic_normal, 2, false)." ".$unidad_medida_coste);
                    $tabla_otros_conceptos->anyade_fila("fila-igic-normal", $datos_fila_igic_normal);
                }
            }

            // Coste total de otros conceptos
            $pie_tabla_otros_conceptos = $idiomas->_("Coste total").": ".formatea_numero($coste_total_otros_conceptos, 2, false)." ".$unidad_medida_coste;
            $tabla_otros_conceptos->anyade_pie($pie_tabla_otros_conceptos);

            // Datos de la tabla
            $datos_tabla_otros_conceptos = $tabla_otros_conceptos->dame_tabla();

            // Tabla coste y consumo (total y diario)
            $params_tabla_coste_consumo = array(
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_COSTE_CONSUMO_SENSOR_TARIFA_ELECTRICA_ESPANYA,
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_TABLA_COSTE_CONSUMO_SENSOR_TARIFA_ELECTRICA_ESPANYA),
                "generar_valores_xml" => true
            );
            $tabla_coste_consumo = new TablaDatos(
                "tabla-coste-consumo-simulador-factura-electrica",
                $idiomas->_("Coste y consumo"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla_coste_consumo
            );
            $cabecera_tabla_coste_consumo = array(
                $idiomas->_("Coste total")." (". $idiomas->_("base imponible").")",
                $idiomas->_("Consumo total"),
                $idiomas->_("Coste diario"),
                $idiomas->_("Consumo diario")
            );
            $tabla_coste_consumo->anyade_cabecera("", $cabecera_tabla_coste_consumo);

            // Costes y consumos
            $coste_total = $resultado_funcion_externa["coste_total"];
            $consumo_total = $consumo_energia_activa_total;
            $coste_diario = $coste_total / $numero_dias;
            $consumo_diario = $consumo_total / $numero_dias;

            // Se crean los datos para la fila de la tabla y se añade
            $datos_fila_coste_consumo = array(
                formatea_numero($coste_total, 2, false)." ".$unidad_medida_coste." (".
                    formatea_numero($base_imponible, 2, false)." ".$unidad_medida_coste.")",
                formatea_numero($consumo_total, 2)." ".$idiomas->_("kWh"),
                formatea_numero($coste_diario, 2, false)." ".$unidad_medida_coste."/".$idiomas->_("día"),
                formatea_numero($consumo_diario, 2)." ".$idiomas->_("kWh")."/".$idiomas->_("día"));
            $tabla_coste_consumo->anyade_fila("fila-general", $datos_fila_coste_consumo);
            
            // Si hay un error debido a la ausencia de parámetros de energía eléctrica se muestra el error en la sección
            // correspondiente al consumo
            if (count($error_costes_consumo_simulacion_factura) > 1) {                              
                if (strcmp($error_costes_consumo_simulacion_factura[0], "SIN_VALORES_PARAMETROS_ENERGIA_ELECTRICA_PASS_THROUGH") === 0) {                    
                    $cadena_fecha_inicio_error_parametros = convierte_formato_fecha($error_costes_consumo_simulacion_factura[1], 
                    FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                    $pie_tabla_coste_consumo = " <b><font color = '#B22222'>                    
                    (*) Aviso importante: La simulación de la factura se ha realizado
                    hasta el día $cadena_fecha_inicio_error_parametros
                     debido a la ausencia de parámetros de energía eléctrica. De este modo, los días posteriores no se han incluido en 
                    el cálculo de la factura y por ende, del consumo. Así pues, el total de la energía consumida no es correcto 
                    (el cálculo se realizará de manera correcta para todos los días especificados cuando se publiquen los parámetros). 
                    </b></font>";                  
                }
            }
            else {
                $pie_tabla_coste_consumo = $idiomas->_("Coste total").": ".formatea_numero($coste_total, 2, false)." ".$unidad_medida_coste;
            }

            // Coste total de la factura
            $tabla_coste_consumo->anyade_pie($pie_tabla_coste_consumo);

            // Gráfica de porcentajes de costes por concepto (y etiquetas de conceptos)
            $grafica_porcentajes_costes_conceptos = new VectorDatos();
            $etiquetas_conceptos = new VectorDatos();
            $datos_porcentajes_costes_conceptos = new VectorDatos();
            $datos_porcentajes_costes_conceptos->anyade_tupla_etiqueta_dato($idiomas->_("Energía activa"), $coste_energia_activa_total);
            $etiquetas_conceptos->anyade_etiqueta($idiomas->_("Energía activa"));
            $datos_porcentajes_costes_conceptos->anyade_tupla_etiqueta_dato($idiomas->_("Potencia"), $coste_potencia_total);
            $etiquetas_conceptos->anyade_etiqueta($idiomas->_("Potencia"));
            if ($hay_datos_potencia_maxima_excesos_potencia == true)
            {
                $datos_porcentajes_costes_conceptos->anyade_tupla_etiqueta_dato($idiomas->_("Excesos de potencia"), $coste_excesos_potencia_total);
                $etiquetas_conceptos->anyade_etiqueta($idiomas->_("Excesos de potencia"));
            }
            if ($hay_datos_energia_reactiva == true)
            {
                $datos_porcentajes_costes_conceptos->anyade_tupla_etiqueta_dato($idiomas->_("Energía reactiva"), $coste_energia_reactiva_total);
                $etiquetas_conceptos->anyade_etiqueta($idiomas->_("Energía reactiva"));
            }
            if ($coste_total_otros_conceptos >= 0)
            {
                $coste_total_otros_conceptos_pocentajes_costes_conceptos = $coste_total_otros_conceptos;
            }
            else
            {
                $coste_total_otros_conceptos_pocentajes_costes_conceptos = 0;
            }
            $datos_porcentajes_costes_conceptos->anyade_tupla_etiqueta_dato($idiomas->_("Otros conceptos"), $coste_total_otros_conceptos_pocentajes_costes_conceptos);
            $etiquetas_conceptos->anyade_etiqueta($idiomas->_("Otros conceptos"));
            $grafica_porcentajes_costes_conceptos->anyade_dato($datos_porcentajes_costes_conceptos->dame_datos());

            // Información de reparto de costes
            $res_informacion_reparto_costes = dame_informacion_reparto_costes_simulacion_factura($parametros, $coste_total);
            $hay_datos_reparto_costes = $res_informacion_reparto_costes["hay_datos_reparto_costes"];
            $datos_tabla_reparto_costes = $res_informacion_reparto_costes["datos_tabla_reparto_costes"];
            $datos_grafica_porcentajes_reparto_costes = $res_informacion_reparto_costes["datos_grafica_porcentajes_reparto_costes"];
            $datos_etiquetas_sensores_reparto_costes = $res_informacion_reparto_costes["datos_etiquetas_sensores_reparto_costes"];

            // Mensaje de aviso
            $msg_aviso = "";
            $avisos = array();

            // Aviso de número de días de factura menor que número de días de consumo de energía activa
            if ($numero_dias > $numero_dias_consumo_energia_activa)
            {
                // Conversión de fechas
                $cadena_hora_inicio_consumos_energia_activa_funciones_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_inicio_consumos_energia_activa_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_fin_consumos_energia_activa_funciones_local = cambia_zona_horaria_cadena_fecha_hora($cadena_hora_fin_consumos_energia_activa_funciones_utc, FORMATO_FECHA_HORA_FUNCIONES, ZONA_HORARIA_UTC, $zona_horaria);
                $cadena_hora_inicio_consumos_energia_activa_local_local = convierte_formato_fecha($cadena_hora_inicio_consumos_energia_activa_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);
                $cadena_hora_fin_consumos_energia_activa_local_local = convierte_formato_fecha($cadena_hora_fin_consumos_energia_activa_funciones_local, FORMATO_FECHA_HORA_FUNCIONES, $_SESSION["formato_fecha_hora_local"]);

                // Se añade el aviso
                $aviso = $idiomas->_("El número de horas de la factura es mayor que el número de horas de consumo de energía activa")."\n(".
                    $idiomas->_("hora de inicio de consumo").": ".$cadena_hora_inicio_consumos_energia_activa_local_local.", ".
                    $idiomas->_("hora de fin de consumo").": ".$cadena_hora_fin_consumos_energia_activa_local_local.")";
                array_push($avisos, $aviso);
            }

            // Aviso de coste pendiente 'MEFF-REE' no disponible
            if (($hay_concepto_pendiente_MEFF_REE == true) && ($coste_concepto_pendiente_MEFF_REE === NULL))
            {
                // Se añade el aviso
                $aviso = $idiomas->_("El coste pendiente MEFF-REE no está disponible");
                array_push($avisos, $aviso);
            }

            // Se crea el mensaje de aviso
            $numero_avisos = count($avisos);
            if ($numero_avisos == 1)
            {
                $msg_aviso = $avisos[0];
            }
            else
            {
                foreach ($avisos as $aviso)
                {
                    if ($msg_aviso != "")
                    {
                        $msg_aviso .= "\n";
                    }
                    $msg_aviso .= "- ".$aviso;
                }
            }

            // Resultado
            $resultado = array(
                "res" => "OK",
                "hay_datos" => true,
                "msg_aviso" => $msg_aviso,
                "tabla_datos" => $tabla_datos->dame_tabla(),
                "tabla_coste_consumo" => $tabla_coste_consumo->dame_tabla(),
                "tabla_energia_activa" => $datos_tabla_energia_activa,
                "tabla_energia_activa_directo" => $datos_tabla_energia_activa_directo,
                "tabla_energia_activa_tarifa_acceso" => $datos_tabla_energia_activa_tarifa_acceso,
                "tabla_potencia" => $tabla_potencia->dame_tabla(),
                "hay_datos_potencia_maxima_excesos_potencia" => $hay_datos_potencia_maxima_excesos_potencia,
                "tabla_potencia_maxima_excesos_potencia" => $datos_tabla_potencia_maxima_excesos_potencia,
                "hay_datos_energia_reactiva" => $hay_datos_energia_reactiva,
                "tabla_energia_reactiva" => $datos_tabla_energia_reactiva,
                "tabla_otros_conceptos" => $datos_tabla_otros_conceptos,
                "grafica_porcentajes_costes_conceptos" => $grafica_porcentajes_costes_conceptos->dame_datos(),
                "etiquetas_conceptos" => $etiquetas_conceptos->dame_datos(),
                "hay_datos_reparto_costes" => $hay_datos_reparto_costes,
                "tabla_reparto_costes" => $datos_tabla_reparto_costes,
                "grafica_porcentajes_reparto_costes" => $datos_grafica_porcentajes_reparto_costes,
                "etiquetas_sensores_reparto_costes" => $datos_etiquetas_sensores_reparto_costes,
                "unidad_medida_coste" => $unidad_medida_coste);
        }

        // Se devuelve el resultado
        return ($resultado);
    }


    //
    // Funciones auxiliares
    //


    // Devuelve el coste de un concepto de simulación de factura de un sensor y tarifa
    function dame_coste_concepto_simulacion_factura_sensor_electricidad_Espanya($parametros)
    {
        // Parámetros
        $id_sensor = $parametros["id_sensor"];
        $nombre_sensor = $parametros["nombre_sensor"];
        $concepto_factura = $parametros["concepto_factura"];
        $cadena_fecha_hora_inicio_local_local = $parametros["fecha_hora_inicio"];
        $cadena_fecha_hora_fin_local_local = $parametros["fecha_hora_fin"];

        // Se recupera la tarifa eléctrica del sensor especificado
        $id_tarifa = dame_id_tarifa_id_sensor_fecha($id_sensor, $cadena_fecha_hora_inicio_local_local);
        if ($id_tarifa == ID_NINGUNO)
        {
            $resultado = array(
                "res" => "OK",
                "tarifa_asignada" => false,
                "hay_datos" => false);
        }
        else
        {
            // Conversión de fechas
            $zona_horaria = dame_zona_horaria_local();
            $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
            $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
            $cadena_fecha_hora_inicio_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
            $cadena_fecha_hora_fin_funciones_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);

            // Parámetros de la función a llamar
            $parametros_funcion_externa =
                array(
                    "llamante" => "web_emios",
                    "nombre" => NOMBRE_FUNCION_CALCULA_DATOS_SIMULACION_FACTURA_SENSOR_TARIFA,
                    "medicion" => MEDICION_ELECTRICIDAD,
                    "pais_tarifas" => PAIS_ESPANYA,
                    "nombre_sensor" => $nombre_sensor,
                    "id_red" => $_SESSION["id_red"],
                    "id_tarifa" => $id_tarifa,
                    "recalcular_costes_energia_activa" => VALOR_NO,
                    "fecha_hora_inicio" => $cadena_fecha_hora_inicio_funciones_utc,
                    "fecha_hora_fin" => $cadena_fecha_hora_fin_funciones_utc,
                    "exclusion_fechas" => ""
                );

            // Llamada a función 'externa'
            $ruta_procesado_emios = dame_valor_entrada_ini("ruta_procesado_emios");
            $resultado_funcion_externa = ejecuta_funcion_externa($ruta_procesado_emios, $parametros_funcion_externa, false);

            // Si no hay datos de consumo, no se hace nada
            $hay_datos_consumo = $resultado_funcion_externa["hay_datos_consumo"];
            if ($hay_datos_consumo == false)
            {
                $resultado = array(
                    "res" => "OK",
                    "tarifa_asignada" => true,
                    "hay_datos" => false);
            }
            else
            {
                // No mostrar valores para conceptos de coste cuando sean tarifas de cierre con el mes sin terminar
                if($resultado_funcion_externa["tarifa_cierre_mes_sin_terminar"] and in_array($concepto_factura, [CONCEPTO_FACTURA_ELECTRICA_ENERGIA_ACTIVA_ESPANYA, CONCEPTO_FACTURA_ELECTRICA_ENERGIA_POTENCIA_ESPANYA, CONCEPTO_FACTURA_ELECTRICA_OTROS_CONCEPTOS_ESPANYA, CONCEPTO_FACTURA_ELECTRICA_TOTAL_ESPANYA])){
                    $resultado = array(
                        "res" => "OK",
                        "tarifa_asignada" => true,
                        "hay_datos" => false);
                } 
                else {
                    // Se recupera el coste del concepto especificado de la factura eléctrica
                    $coste_concepto_factura = NULL;
                    switch ($concepto_factura)
                    {
                        case CONCEPTO_FACTURA_ELECTRICA_TOTAL_ESPANYA:
                        {
                            $coste_concepto_factura = $resultado_funcion_externa["coste_total"];
                            break;
                        }
                        case CONCEPTO_FACTURA_ELECTRICA_ENERGIA_POTENCIA_ESPANYA:
                        {
                            $coste_concepto_factura = $resultado_funcion_externa["coste_energia_potencia_total"];
                            break;
                        }
                        case CONCEPTO_FACTURA_ELECTRICA_ENERGIA_ACTIVA_ESPANYA:
                        {
                            $coste_concepto_factura = $resultado_funcion_externa["coste_energia_activa_total"];
                            break;
                        }
                        case CONCEPTO_FACTURA_ELECTRICA_POTENCIA_ESPANYA:
                        {
                            $coste_concepto_factura = $resultado_funcion_externa["coste_potencia_total"];
                            break;
                        }
                        case CONCEPTO_FACTURA_ELECTRICA_EXCESOS_POTENCIA_ESPANYA:
                        {
                            $coste_concepto_factura = $resultado_funcion_externa["coste_excesos_potencia_total"];
                            break;
                        }
                        case CONCEPTO_FACTURA_ELECTRICA_ENERGIA_REACTIVA_ESPANYA:
                        {
                            $coste_concepto_factura = $resultado_funcion_externa["coste_energia_reactiva_total"];
                            break;
                        }
                        case CONCEPTO_FACTURA_ELECTRICA_OTROS_CONCEPTOS_ESPANYA:
                        {
                            $coste_concepto_factura = $resultado_funcion_externa["coste_total_otros_conceptos"];
                            break;
                        }
                        default:
                        {
                            throw new Exception("Concepto de factura eléctrica desconocido: '".$concepto_factura."'");
                        }
                    }

                    // Unidad de medida de coste
                    $unidad_medida_coste = $_SESSION["moneda"];

                    // Resultado
                    $resultado = array(
                        "res" => "OK",
                        "tarifa_asignada" => true,
                        "hay_datos" => true,
                        "coste_concepto_factura" => $coste_concepto_factura,
                        "unidad_medida_coste" => $unidad_medida_coste);
                }
            }
        }

        // Se devuelve el resutlado
        return ($resultado);
    }


    //
    // Funciones de elementos de informes
    //


    function dame_elementos_informe_smartmeter_simulador_factura_electricidad_Espanya()
    {
        $elementos_informe = array();
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_DATOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_DATOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_RESUMEN);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_COSTE_CONSUMO);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_DETALLES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_ENERGIA_ACTIVA);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_POTENCIA);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_POTENCIA_MAXIMA_EXCESOS_POTENCIA);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_ENERGIA_REACTIVA);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_OTROS_CONCEPTOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_GRAFICA_PORCENTAJES_COSTES_CONCEPTOS);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_REPARTO_COSTES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_REPARTO_COSTES);
        array_push($elementos_informe, ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_GRAFICA_PORCENTAJES_REPARTO_COSTES);
        return ($elementos_informe);
    }


    function dame_descripcion_elemento_informe_simulacion_factura_electricidad_Espanya($elemento_informe)
    {
        switch ($elemento_informe)
        {
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_DATOS:
            {
                $descripcion = "Título de datos de factura";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_DATOS:
            {
                $descripcion = "Tabla de datos de factura";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_RESUMEN:
            {
                $descripcion = "Título de resumen de factura";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_COSTE_CONSUMO:
            {
                $descripcion = "Tabla de coste y consumo";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_DETALLES:
            {
                $descripcion = "Título de detalles de factura";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_ENERGIA_ACTIVA:
            {
                $descripcion = "Tabla de energía activa";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_POTENCIA:
            {
                $descripcion = "Tabla de potencia";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_POTENCIA_MAXIMA_EXCESOS_POTENCIA:
            {
                $descripcion = "Tabla de potencia máxima y excesos de potencia";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_ENERGIA_REACTIVA:
            {
                $descripcion = "Tabla de energía reactiva";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_OTROS_CONCEPTOS:
            {
                $descripcion = "Tabla de otros conceptos";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_GRAFICA_PORCENTAJES_COSTES_CONCEPTOS:
            {
                $descripcion = "Gráfica de porcentajes de costes por concepto";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TITULO_REPARTO_COSTES:
            {
                $descripcion = "Título de reparto de costes";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_TABLA_REPARTO_COSTES:
            {
                $descripcion = "Tabla de reparto de costes";
                break;
            }
            case ELEMENTO_INFORME_SMARTMETER_SIMULADOR_FACTURA_ELECTRICIDAD_ESPANYA_GRAFICA_PORCENTAJES_REPARTO_COSTES:
            {
                $descripcion = "Gráfica de porcentajes de reparto de costes";
                break;
            }
            default:
            {
                $descripcion = "Desconocido";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    //
    // Funciones de informes
    //


    function dame_html_informe_tipo_smartmeter_simulador_factura_electricidad_Espanya($tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_informe = "";
        switch ($tipo_informe)
        {
            case TIPO_INFORME_WEB_EMIOS:
            {
                $html_informe = "
                    <div class='texto-informe-vacio elemento-no-seleccionable' id='informe-sin-datos-smartmeter-simulador-factura'>
                        <i class='icon-info-sign color-azul'></i> ".$idiomas->_("No hay datos")."
                    </div>
                    <div id='informe-smartmeter-simulador-factura' hidden>
                        <div class='titulo-tabla-datos100' id='titulo-datos-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-datos-simulador-factura'></div>
                        <div class='titulo-tabla-datos100' id='titulo-resumen-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-coste-consumo-simulador-factura'></div>
                        <div class='titulo-tabla-datos100' id='titulo-detalles-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-energia-activa-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-energia-activa-directo-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-energia-activa-tarifa-acceso-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-potencia-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-potencia-maxima-excesos-potencia-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-energia-reactiva-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-otros-conceptos-simulador-factura'></div>
                        <div class='grafica100' id='grafica-porcentajes-costes-conceptos-simulador-factura'></div>
                        <div class='titulo-tabla-datos100' id='titulo-reparto-costes-simulador-factura'></div>
                        <div class='tabla-datos100' id='contenedor-tabla-reparto-costes-simulador-factura'></div>
                        <div class='grafica100' id='grafica-porcentajes-reparto-costes-simulador-factura'></div>
                    </div>";
                break;
            }
            case TIPO_INFORME_FICHERO:
            {
                // Página de simulación de factura (1)
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-simulador-factura-1'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_facturas(TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-simulador-factura-1'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='titulo-datos-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-datos-simulador-factura'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='titulo-resumen-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-coste-consumo-simulador-factura'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='titulo-detalles-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-energia-activa-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-energia-activa-directo-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-energia-activa-tarifa-acceso-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-potencia-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-potencia-maxima-excesos-potencia-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-energia-reactiva-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-otros-conceptos-simulador-factura'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-porcentajes-costes-conceptos-simulador-factura'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";

                // Página de simulación de factura (2)
                $html_informe .= "
                    <div class='pagina-informe-fichero' id='pagina-informe-fichero-simulador-factura-2'>";
                $html_informe .= dame_html_cabecera_informe_fichero_smartmeter_facturas(TIPO_INFORME_SMARTMETER_SIMULADOR_FACTURA);
                $html_informe .= "
                        <div class='titulo-informe-fichero' id='titulo-informe-fichero-simulador-factura-2'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='titulo-reparto-costes-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero' id='contenedor-tabla-reparto-costes-simulador-factura'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero' id='grafica-porcentajes-reparto-costes-simulador-factura'></div>
                        <div class='fin-pagina-informe-fichero'></div>
                    </div>";
                break;
            }
        }
        return ($html_informe);
    }


    //
    // Funciones de plantillas de informes
    //


    function dame_html_elemento_plantilla_informe_tipo_smartmeter_simulador_factura_electricidad_Espanya(
        $numero_elemento,
        $nombre_elemento,
        $parametros_tipo_elemento,
        $tipo_informe)
    {
        $idiomas = new Idiomas();

        $html_elemento = "";
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
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-tarifa-asignada-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay tarifa asignada")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='titulo-tabla-datos100 elemento-oculto' id='".$prefijo_elemento."titulo-datos-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-datos-simulador-factura'></div>
                        <div class='titulo-tabla-datos100 elemento-oculto' id='".$prefijo_elemento."titulo-resumen-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-coste-consumo-simulador-factura'></div>
                        <div class='titulo-tabla-datos100 elemento-oculto' id='".$prefijo_elemento."titulo-detalles-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-activa-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-activa-directo-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-activa-tarifa-acceso-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-potencia-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-potencia-maxima-excesos-potencia-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-reactiva-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-otros-conceptos-simulador-factura'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-porcentajes-costes-conceptos-simulador-factura'></div>
                        <div class='titulo-tabla-datos100 elemento-oculto' id='".$prefijo_elemento."titulo-reparto-costes-simulador-factura'></div>
                        <div class='tabla-datos100 elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-reparto-costes-simulador-factura'></div>
                        <div class='grafica100 elemento-oculto' id='".$prefijo_elemento."grafica-porcentajes-reparto-costes-simulador-factura'></div>
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
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-tarifa-asignada-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay tarifa asignada")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div class='texto-aviso-elemento-informe' id='elemento-sin-datos-elemento".$numero_elemento."' hidden>
                        <i class='icon-warning-sign color-rojo'></i> ".$idiomas->_("No hay datos disponibles")." (".htmlspecialchars(strtolower($nombre_elemento), ENT_QUOTES).")
                    </div>
                    <div id='contenido-elemento".$numero_elemento."'>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."titulo-datos-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-datos-simulador-factura'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."titulo-resumen-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-coste-consumo-simulador-factura'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."titulo-detalles-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-activa-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-activa-directo-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-activa-tarifa-acceso-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-potencia-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-potencia-maxima-excesos-potencia-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-energia-reactiva-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-otros-conceptos-simulador-factura'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-porcentajes-costes-conceptos-simulador-factura'></div>
                        <div class='titulo-tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."titulo-reparto-costes-simulador-factura'></div>
                        <div class='tabla-datos100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."contenedor-tabla-reparto-costes-simulador-factura'></div>
                        <div class='grafica100-informe-fichero separacion-superior-elementos-informe-fichero elemento-oculto' id='".$prefijo_elemento."grafica-porcentajes-reparto-costes-simulador-factura'></div>
                    </div>";
                break;
            }
        }
        return ($html_elemento);
    }


    function dame_datos_elemento_plantilla_informe_tipo_smartmeter_simulador_factura_electricidad_Espanya(
        $numero_elemento,
        $parametros_tipo_elemento,
        $parametros_informe)
    {
        // Parámetros de tipo de elemento
        $medicion = $parametros_tipo_elemento["medicion"];
        $id_sensor = $parametros_tipo_elemento["id_sensor"];
        $id_tarifa = $parametros_tipo_elemento["id_tarifa"];
        $ids_sensores_reparto_costes = $parametros_tipo_elemento["ids_sensores_reparto_costes"];
        if ($parametros_tipo_elemento["exclusion_fechas"] !== NULL)
        {
            $cadena_exclusion_fechas_json = json_encode($parametros_tipo_elemento["exclusion_fechas"]);
        }
        else
        {
            $cadena_exclusion_fechas_json = "";
        }

        // Si no hay sensor seleccionado, se devuelve sin sensor
        if ($id_sensor == ID_NINGUNO)
        {
            $resultado = array(
                "res" => "OK",
                "sin_sensor_seleccionado" => true);
            return ($resultado);
        }

        // Se comprueba si el sensor tiene tarifa asignada (si es necesario)
        if ($id_tarifa == ID_NINGUNO)
        {
            $id_tarifa_sensor = dame_id_tarifa_id_sensor($id_sensor);
            if ($id_tarifa_sensor == ID_NINGUNO)
            {
                $resultado = array(
                    "res" => "OK",
                    "sin_tarifa_asignada" => true);
                return ($resultado);
            }
        }

        $nombre_sensor = dame_nombre_sensor($id_sensor);
        $nombres_sensores_reparto_costes = dame_nombres_sensores($ids_sensores_reparto_costes);
        $parametros_informe["medicion"] = $medicion;
        $parametros_informe["id_sensor"] = $id_sensor;
        $parametros_informe["nombre_sensor"] = $nombre_sensor;
        $parametros_informe["id_tarifa"] = $id_tarifa;
        $parametros_informe["ids_sensores_reparto_costes"] = $ids_sensores_reparto_costes;
        $parametros_informe["nombres_sensores_reparto_costes"] = $nombres_sensores_reparto_costes;
        $parametros_informe["exclusion_fechas"] = $cadena_exclusion_fechas_json;
        $parametros_informe["numero_elemento_plantilla_informe"] = $numero_elemento;

        $datos_elemento = dame_simulacion_factura_sensor_tarifa_electricidad_Espanya($parametros_informe);
        return ($datos_elemento);
    }

    // Se tiene que quitar el texto de la división entre 30 cuando:
    // - La tarifa sea 2025_abril o posterior
    // - La tarifa sea de tipo 2.0
    function mostrar_division_30($tipo_tarifa_electrica) {
        if (stripos($tipo_tarifa_electrica, "2025_abril") !== false or stripos($tipo_tarifa_electrica, "2026") !== false) {
            $tipo = explode('_', $tipo_tarifa_electrica)[1];
            if (stripos($tipo, "20") !== false) {
                return false;
            }
        }

        return true;
    }

    
    // Se tiene que quitar el texto de la multiplicación (x1) cuando:
    // - La tarifa sea 2025_abril o posterior
    // - La tarifa sea de tipo 3.0 o superior
    // - Sea de tipo de medida 1,2,3 (con potencia contratada >50 en alguno de sus periodos tarifarios)
    function mostrar_multiplicacion_k_tramo($tipo_tarifa_electrica) {
        //Comprobación case-insensitive de la cadena
        if (stripos($tipo_tarifa_electrica, "2025_abril") !== false or stripos($tipo_tarifa_electrica, "2026") !== false) {
            $tipo = explode('_', $tipo_tarifa_electrica)[1];
            if (stripos($tipo, "30") !== false || stripos($tipo, "61") !== false ||stripos($tipo, "62") !== false || stripos($tipo, "63") !== false || stripos($tipo, "64") !== false) {
                return false;
            }
        }

        return true;
    }
?>
