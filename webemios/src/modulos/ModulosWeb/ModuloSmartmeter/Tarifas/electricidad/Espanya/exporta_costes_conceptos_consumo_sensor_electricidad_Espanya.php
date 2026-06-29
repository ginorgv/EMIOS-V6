<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_tiempos.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_ficheros.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_EXPORTAR_COSTES_CONCEPTOS_CONSUMO_SENSOR_ELECTRICIDAD_ESPANYA, $_POST);

	$idiomas = new Idiomas();
    $bd_datos = BaseDatosDatos::dame_base_datos();

    // Parámetros
    $nombre_sensor = $_POST["nombre_sensor"];
    $cadena_fecha_inicio_local_local = $_POST["fecha_inicio"];
    $cadena_fecha_fin_local_local = $_POST["fecha_fin"];
    $punto_decimal_exportacion_costes_conceptos = $_POST["punto_decimal"];

    // Zona horaria local
    $zona_horaria = dame_zona_horaria_local();

    // Flag para exportar los costes de los conceptos
    $exportar_costes_conceptos = true;

    // Se comprueba el número de días de exportación de valores de parámetros (sólo si el usuario no es superadministrador)
    if ($_SESSION["perfil"] != PERFIL_USUARIO_SUPERADMINISTRADOR)
    {
        $fecha_inicio_local = convierte_cadena_a_fecha($cadena_fecha_inicio_local_local, $_SESSION["formato_fecha_local"], $zona_horaria);
        $fecha_fin_local = convierte_cadena_a_fecha($cadena_fecha_fin_local_local, $_SESSION["formato_fecha_local"], $zona_horaria);
        $numero_dias_recalculo = $fecha_fin_local->diff($fecha_inicio_local)->days + 1;
        if ($numero_dias_recalculo > NUMERO_MAXIMO_DIAS_EXPORTACION_COSTES_CONCEPTOS_CONSUMO_SENSOR_ELECTRICIDAD)
        {
            $res = "ERROR";
            $msg = $idiomas->_("El número de días de exportación de costes de conceptos de consumo es mayor que el máximo permitido")." (".NUMERO_MAXIMO_DIAS_EXPORTACION_COSTES_CONCEPTOS_CONSUMO_SENSOR_ELECTRICIDAD.")";
            $exportar_costes_conceptos = false;
        }
    }

    // Se exportan los costes de los conceptos
    if ($exportar_costes_conceptos == true)
    {
        // Conversión de fechas
        $cadena_fecha_hora_inicio_local_local = $cadena_fecha_inicio_local_local.", 00:00:00";
        $cadena_fecha_hora_fin_local_local = $cadena_fecha_fin_local_local.", 23:59:59";
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Directorios de usuario
        $directorio_usuario_servidor = $_SESSION["directorio"].'/rsc/ficheros/tmp/'.$_SESSION["id_usuario"];
        $directorio_usuario_cliente = './rsc/ficheros/tmp/'.$_SESSION["id_usuario"];

        // Costes de conceptos de consumo
        $consulta_costes_conceptos = "
            SELECT *
            FROM ".TABLA_DATOS_ENERGIA_ACTIVA.SUFIJO_TABLA_HORAS."
            WHERE
                (red = '".$_SESSION["id_red"]."')
                AND (sensor = '".$bd_datos->_($nombre_sensor)."')
                AND (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')
                AND (coste IS NOT NULL)
            ORDER BY hora ASC";
        $res_costes_conceptos = $bd_datos->ejecuta_consulta($consulta_costes_conceptos);
        if ($res_costes_conceptos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_costes_conceptos."'");
        }
        $filas_costes_conceptos = array();
        while ($fila_coste_conceptos = $res_costes_conceptos->dame_siguiente_fila())
        {
            array_push($filas_costes_conceptos, $fila_coste_conceptos);
        }

        // Se crea el fichero CSV de costes de conceptos
        $res = NULL;
        $msg = NULL;
        $ruta_relativa_fichero_costes_exportados = NULL;
        crea_fichero_csv_costes_conceptos_consumo_sensor_electricidad_Espanya(
            $nombre_sensor,
            $filas_costes_conceptos,
            $punto_decimal_exportacion_costes_conceptos,
            $ruta_relativa_fichero_costes_exportados,
            $res,
            $msg);
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "ruta_fichero_costes_exportados" => $ruta_relativa_fichero_costes_exportados))
    );


    //
	// Funciones para crear ficheros CSV
	//


    // Crea un fichero CSV con los parámetros argumento
    function crea_fichero_csv_costes_conceptos_consumo_sensor_electricidad_Espanya(
        $nombre_sensor,
        $filas_costes_conceptos,
        $punto_decimal_exportacion_costes_conceptos,
        &$ruta_relativa_fichero_costes_exportados,
        &$res,
        &$msg)
    {
        $idiomas = new Idiomas();

        $numero_costes_conceptos = count($filas_costes_conceptos);
        if ($numero_costes_conceptos == 0)
        {
            $res = "OK";
            $msg = $idiomas->_("No se han exportado costes");
            return;
        }
        $primera_fila_costes_conceptos = $filas_costes_conceptos[0];

        // Pasos de creación de un fichero CSV de costes de conceptos de consumo de un sensor:
        // - Lectura de conceptos de la primera hora (deben ser las mismas en todas las horas)
        //
        //
        // - Conversión de punto decimal (a ",")
        // - Conversión de fechas UTC a zona horaria especificada
        // - Se crea el nombre del fichero
        // - Se crean las rutas tanto de cliente como de servidor
        // - Se crean las cabeceras del fichero CSV
        // - Se escribe el fichero CSV

        $nombres_conceptos_consumo = array();
        array_push($nombres_conceptos_consumo, $idiomas->_("Consumo")." (".$idiomas->_("kWh").")");
        array_push($nombres_conceptos_consumo, $idiomas->_("Tramo"));
        array_push($nombres_conceptos_consumo, $idiomas->_("Total")." (€)");
        $coste_conceptos_json = $primera_fila_costes_conceptos["coste_conceptos_json"];
        if ($coste_conceptos_json !== NULL)
        {
            $coste_conceptos = json_decode($coste_conceptos_json, true);
            ksort($coste_conceptos);
            foreach($coste_conceptos as $cadena_indice_nombre_concepto => $coste_concepto)
            {
                $indice_nombre_concepto = explode(SEPARADOR_INDICE_NOMBRE_CONCEPTO_CONSUMO, $cadena_indice_nombre_concepto);
                $nombre_concepto = $indice_nombre_concepto[1];
                switch ($nombre_concepto)
                {
                    case COSTE_CONCEPTO_CONSUMO_DIRECTO:
                    {
                        $nombre_concepto = $idiomas->_("Directo (€)");
                        break;
                    }
                    case COSTE_CONCEPTO_CONSUMO_TARIFA_ACCESO:
                    {
                        $nombre_concepto = $idiomas->_("ATR (€)");
                        break;
                    }
                    case COSTE_CONCEPTO_CONSUMO_OTROS:
                    {
                        $nombre_concepto = $idiomas->_("Otros (€)");
                        break;
                    }
                }
                array_push($nombres_conceptos_consumo, $nombre_concepto);
            }
        }

        $filas_costes_conceptos_csv = array();
        $zona_horaria_local = dame_zona_horaria_local();
        for ($i = 0; $i < count($filas_costes_conceptos); $i++)
        {
            $fila_coste_conceptos_csv = array();

            $cadena_fecha_hora_fichero_csv_utc = convierte_formato_fecha($filas_costes_conceptos[$i]["hora"], FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO_CSV);
            $cadena_fecha_hora_fichero_csv_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fichero_csv_utc, FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO_CSV, ZONA_HORARIA_UTC, $zona_horaria_local);
            $consumo = $filas_costes_conceptos[$i]["incremento"];
            $tramo = $filas_costes_conceptos[$i]["tramo"];
            $coste = $filas_costes_conceptos[$i]["coste"];

            $fila_coste_conceptos_csv["hora"] = $cadena_fecha_hora_fichero_csv_local;

            $nombres_conceptos_consumo_bucle = array();
            array_push($nombres_conceptos_consumo_bucle, $idiomas->_("Consumo")." (".$idiomas->_("kWh").")");
            array_push($nombres_conceptos_consumo_bucle, $idiomas->_("Tramo"));
            array_push($nombres_conceptos_consumo_bucle, $idiomas->_("Total")." (€)");
            $consumo = formatea_numero($consumo, 8, false);
            if ($punto_decimal_exportacion_costes_conceptos == ",")
            {
                $consumo = str_replace(".", ",", $consumo);
            }
            $coste = formatea_numero($coste, 8, false);
            if ($punto_decimal_exportacion_costes_conceptos == ",")
            {
                $coste = str_replace(".", ",", $coste);
            }
            $fila_coste_conceptos_csv["consumo"] = $consumo;
            $fila_coste_conceptos_csv["tramo"] = $tramo;
            $fila_coste_conceptos_csv["total"] = $coste;
            $coste_conceptos_json = $filas_costes_conceptos[$i]["coste_conceptos_json"];
            if ($coste_conceptos_json !== NULL)
            {
                $coste_conceptos = json_decode($coste_conceptos_json, true);
                ksort($coste_conceptos);

                foreach($coste_conceptos as $cadena_indice_nombre_concepto => $coste_concepto)
                {
                    $indice_nombre_concepto = explode(SEPARADOR_INDICE_NOMBRE_CONCEPTO_CONSUMO, $cadena_indice_nombre_concepto);
                    $nombre_concepto = $indice_nombre_concepto[1];
                    switch ($nombre_concepto)
                    {
                        case COSTE_CONCEPTO_CONSUMO_DIRECTO:
                        {
                            $nombre_concepto = $idiomas->_("Directo (€)");
                            break;
                        }
                        case COSTE_CONCEPTO_CONSUMO_TARIFA_ACCESO:
                        {
                            $nombre_concepto = $idiomas->_("ATR (€)");
                            break;
                        }
                        case COSTE_CONCEPTO_CONSUMO_OTROS:
                        {
                            $nombre_concepto = $idiomas->_("Otros (€)");
                            break;
                        }
                    }
                    array_push($nombres_conceptos_consumo_bucle, $nombre_concepto);
                    $coste_concepto = formatea_numero($coste_concepto, 8, false);
                    if ($punto_decimal_exportacion_costes_conceptos == ",")
                    {
                        $coste_concepto = str_replace(".", ",", $coste_concepto);
                    }
                    $fila_coste_conceptos_csv[$nombre_concepto] = $coste_concepto;
                }
            }
            if ($nombres_conceptos_consumo != $nombres_conceptos_consumo_bucle)
            {
                $res = "ERROR";
                $msg = $idiomas->_("Los conceptos de consumo no son iguales en todas las horas").
                    "\n(".$idiomas->_("hora").": ".$cadena_fecha_hora_fichero_csv_local.")";
                return;
            }

            if ($zona_horaria_local != ZONA_HORARIA_UTC)
            {
                $horario_verano = dame_horario_verano_cadena_fecha_hora_utc($cadena_fecha_hora_fichero_csv_utc, FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO_CSV, $zona_horaria_local);
                $cadena_horario_verano = $horario_verano? 1:0;
                $fila_coste_conceptos_csv["horario_verano"] = $cadena_horario_verano;
            }

            array_push($filas_costes_conceptos_csv, $fila_coste_conceptos_csv);
        }

        // Se recupera el directorio del usuario
        $directorio_absoluto_ficheros_temporales_usuario = dame_directorio_ficheros_temporales_usuario($_SESSION["id_usuario"]);
        $directorio_relativo_ficheros_temporales_usuario = str_replace($_SESSION["directorio"], ".", $directorio_absoluto_ficheros_temporales_usuario);

        $cadena_fecha_hora_inicio_costes_fichero_local = convierte_formato_fecha($filas_costes_conceptos_csv[0]["hora"], FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO_CSV, FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO);
        $cadena_fecha_hora_fin_costes_fichero_local = convierte_formato_fecha($filas_costes_conceptos_csv[$numero_costes_conceptos - 1]["hora"], FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO_CSV, FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO);
        $sufijo_nombre_sensor = convierte_ascii_estandar($nombre_sensor);
        $sufijo_nombre_sensor = reemplaza_caracteres_no_alfanumericos($sufijo_nombre_sensor, "_");
        $nombre_fichero_costes_exportados = $sufijo_nombre_sensor."-".$cadena_fecha_hora_inicio_costes_fichero_local."-".$cadena_fecha_hora_fin_costes_fichero_local;
        $nombre_fichero_costes_exportados .= ".csv";

        $ruta_absoluta_fichero_costes_exportados = $directorio_absoluto_ficheros_temporales_usuario.'/'.$nombre_fichero_costes_exportados;
        $ruta_relativa_fichero_costes_exportados = $directorio_relativo_ficheros_temporales_usuario.'/'.$nombre_fichero_costes_exportados;

        $cabecera_fichero_costes_conceptos = array();
        $nombre_columna_hora = $idiomas->_("Fecha");
        array_push($cabecera_fichero_costes_conceptos, $nombre_columna_hora);
        foreach ($nombres_conceptos_consumo as $nombre_concepto_consumo)
        {
            array_push($cabecera_fichero_costes_conceptos, $nombre_concepto_consumo);
        }
        if ($zona_horaria_local != ZONA_HORARIA_UTC)
        {
            array_push($cabecera_fichero_costes_conceptos, $idiomas->_("Horario de verano"));
        }
        array_unshift($filas_costes_conceptos_csv, $cabecera_fichero_costes_conceptos);
        escribe_fichero_valores_csv($ruta_absoluta_fichero_costes_exportados, $filas_costes_conceptos_csv);

        $res = "OK";
        $msg = $idiomas->_("Costes exportados correctamente");
    }
?>
