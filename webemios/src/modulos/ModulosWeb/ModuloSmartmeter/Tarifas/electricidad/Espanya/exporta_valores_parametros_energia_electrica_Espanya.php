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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_EXPORTAR_VALORES_PARAMETROS_ENERGIA_ELECTRICA, $_POST);

	$idiomas = new Idiomas();
    $bd_datos = BaseDatosDatos::dame_base_datos();

    // Parámetros
    $cadena_fecha_hora_inicio_local_local = $_POST["fecha_hora_inicio"];
    $cadena_fecha_hora_fin_local_local = $_POST["fecha_hora_fin"];
    $punto_decimal_exportacion_valores_parametros = $_POST["punto_decimal"];

    // Zona horaria local
    $zona_horaria = dame_zona_horaria_local();

    // Flag para exportar los valores de los parámetros
    $exportar_valores_parametros = true;

    // Se comprueba el número de días de exportación de valores de parámetros (sólo si el usuario no es superadministrador)
    if ($_SESSION["perfil"] != PERFIL_USUARIO_SUPERADMINISTRADOR)
    {
        $fecha_hora_inicio_local = convierte_cadena_a_fecha($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria);
        $fecha_hora_fin_local = convierte_cadena_a_fecha($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria);
        $numero_dias_recalculo = $fecha_hora_fin_local->diff($fecha_hora_inicio_local)->days + 1;
        if ($numero_dias_recalculo > NUMERO_MAXIMO_DIAS_EXPORTACION_VALORES_PARAMETROS_ENERGIA_ELECTRICA)
        {
            $res = "ERROR";
            $msg = $idiomas->_("El número de días de exportación de valores de parámetros es mayor que el máximo permitido")." (".NUMERO_MAXIMO_DIAS_EXPORTACION_VALORES_PARAMETROS_ENERGIA_ELECTRICA.")";
            $exportar_valores_parametros = false;
        }
    }

    // Se exportan los valores de los parámetros
    if ($exportar_valores_parametros == true)
    {
        // Conversión de fechas
        $cadena_fecha_hora_inicio_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_inicio_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_fin_local_utc = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fin_local_local, $_SESSION["formato_fecha_hora_local"], $zona_horaria, ZONA_HORARIA_UTC);
        $cadena_fecha_hora_inicio_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_inicio_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);
        $cadena_fecha_hora_fin_base_datos_utc = convierte_formato_fecha($cadena_fecha_hora_fin_local_utc, $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_BASE_DATOS);

        // Directorios de usuario
        $directorio_usuario_servidor = $_SESSION["directorio"].'/rsc/ficheros/tmp/'.$_SESSION["id_usuario"];
        $directorio_usuario_cliente = './rsc/ficheros/tmp/'.$_SESSION["id_usuario"];

        // Rutas relativas de ficheros de valores exportados
        $rutas_relativas_ficheros_valores_exportados = array();

        // Parámetros de energía eléctrica
        $nombres_tablas_valores_parametros = array(
            TABLA_VALORES_INDICADORES_1_ENERGIA_ELECTRICA_ESPANYA,
            TABLA_VALORES_INDICADORES_2_ENERGIA_ELECTRICA_ESPANYA,
            TABLA_COEFICIENTES_PERDIDAS_ENERGIA_ELECTRICA_ESPANYA,
            TABLA_VALORES_PERDIDAS_ENERGIA_ELECTRICA_ESPANYA,
            TABLA_VALORES_PERDIDAS_ENERGIA_ELECTRICA_ESPANYA_2021,
            TABLA_VALORES_PVPC_ENERGIA_ELECTRICA_ESPANYA,
            TABLA_VALORES_DESVIOS_ENERGIA_ELECTRICA_ESPANYA);
        $numero_valores_exportados = 0;
        foreach ($nombres_tablas_valores_parametros as $nombre_tabla_valores_parametros)
        {
            $consulta_valores_parametros = "
                SELECT *
                FROM ".$nombre_tabla_valores_parametros."
                WHERE
                    (hora >= '".$bd_datos->_($cadena_fecha_hora_inicio_base_datos_utc)."')
                    AND (hora <= '".$bd_datos->_($cadena_fecha_hora_fin_base_datos_utc)."')
                ORDER BY hora ASC";
            $res_valores_parametros = $bd_datos->ejecuta_consulta($consulta_valores_parametros);
            if ($res_valores_parametros == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_valores_parametros."'");
            }
            $filas_valores_parametros_csv = array();
            while ($fila_valores_parametros_csv = $res_valores_parametros->dame_siguiente_fila())
            {
                array_push($filas_valores_parametros_csv, $fila_valores_parametros_csv);
            }

            // Se crea el fichero CSV de valores exportados
            $numero_valores_exportados += crea_fichero_csv_valores_parametros_energia_electrica_Espanya(
                $nombre_tabla_valores_parametros,
                $filas_valores_parametros_csv,
                $punto_decimal_exportacion_valores_parametros,
                $rutas_relativas_ficheros_valores_exportados);
        }

        // Se crea el mensaje de resultado a mostrar
        $res = "OK";
        if (($numero_valores_exportados == 0) && ($numero_incrementos_valores_exportados == 0))
        {
            $msg = $idiomas->_("No se han exportado valores");
        }
        else
        {
            $msg = $idiomas->_("Valores exportados correctamente").":\n";
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "rutas_ficheros_valores_exportados" => $rutas_relativas_ficheros_valores_exportados))
    );


    //
	// Funciones para crear ficheros CSV
	//


    // Crea un fichero CSV con los parámetros argumento
    function crea_fichero_csv_valores_parametros_energia_electrica_Espanya(
        $nombre_tabla_valores_parametros,
        $filas_valores_parametros_csv,
        $punto_decimal_exportacion_valores_parametros,
        &$rutas_relativas_ficheros_valores_exportados)
    {
        $idiomas = new Idiomas();

        // Pasos de creación de un fichero CSV de valores exportados de un sensor:
        // - Conversión de punto decimal (a ",")
        // - Conversión de fechas UTC a zona horaria especificada
        // - Se crea el nombre del fichero
        // - Se crean las rutas tanto de cliente como de servidor
        // - Se crean las cabeceras del fichero CSV
        // - Se escribe el fichero CSV

        if ($punto_decimal_exportacion_valores_parametros == ",")
        {
            for ($i = 0; $i < count($filas_valores_parametros_csv); $i++)
            {
                foreach ($filas_valores_parametros_csv[$i] as $clave => $valor)
                {
                    if (($clave == "hora") ||
                        ($clave == "hora_recuperacion") ||
                        ($clave == "tipo_valores"))
                    {
                        continue;
                    }
                    $valor = str_replace(".", ",", $valor);
                    $filas_valores_parametros_csv[$i][$clave] = $valor;
                }
            }
        }

        $zona_horaria_local = dame_zona_horaria_local();
        for ($i = 0; $i < count($filas_valores_parametros_csv); $i++)
        {
            $cadena_fecha_hora_fichero_csv_utc = convierte_formato_fecha($filas_valores_parametros_csv[$i]["hora"], FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO_CSV);
            $cadena_fecha_hora_fichero_csv_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_fichero_csv_utc, FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO_CSV, ZONA_HORARIA_UTC, $zona_horaria_local);
            $cadena_fecha_hora_recuperacion_fichero_csv_utc = convierte_formato_fecha($filas_valores_parametros_csv[$i]["hora_recuperacion"], FORMATO_FECHA_HORA_BASE_DATOS, FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO_CSV);
            $cadena_fecha_hora_recuperacion_fichero_csv_local = cambia_zona_horaria_cadena_fecha_hora($cadena_fecha_hora_recuperacion_fichero_csv_utc, FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO_CSV, ZONA_HORARIA_UTC, $zona_horaria_local);
            $tipo_valores_fichero_csv = $filas_valores_parametros_csv[$i]["tipo_valores"];
            switch ($tipo_valores_fichero_csv)
            {
                case TIPO_VALORES_PARAMETROS_ENERGIA_ELECTRICA_ESTIMADOS:
                {
                    $descripcion_tipo_valores_fichero_csv = $idiomas->_("Estimados");
                    break;
                }
                case TIPO_VALORES_PARAMETROS_ENERGIA_ELECTRICA_AJUSTADOS:
                {
                    $descripcion_tipo_valores_fichero_csv = $idiomas->_("Ajustados");
                    break;
                }
                default:
                {
                    $descripcion_tipo_valores_fichero_csv = $idiomas->_("Desconocido");
                    break;
                }
            }
            $filas_valores_parametros_csv[$i]["hora"] = $cadena_fecha_hora_fichero_csv_local;
            $filas_valores_parametros_csv[$i]["hora_recuperacion"] = $cadena_fecha_hora_recuperacion_fichero_csv_local;
            $filas_valores_parametros_csv[$i]["tipo_valores"] = $descripcion_tipo_valores_fichero_csv;
            if ($zona_horaria_local != ZONA_HORARIA_UTC)
            {
                $horario_verano = dame_horario_verano_cadena_fecha_hora_utc($cadena_fecha_hora_fichero_csv_utc, FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO_CSV, $zona_horaria_local);
                $cadena_horario_verano = $horario_verano? 1:0;
                $filas_valores_parametros_csv[$i]["horario_verano"] = $cadena_horario_verano;
            }
        }

        // Se recupera el directorio del usuario
        $directorio_absoluto_ficheros_temporales_usuario = dame_directorio_ficheros_temporales_usuario($_SESSION["id_usuario"]);
        $directorio_relativo_ficheros_temporales_usuario = str_replace($_SESSION["directorio"], ".", $directorio_absoluto_ficheros_temporales_usuario);

        $numero_valores_exportados = count($filas_valores_parametros_csv);
        if ($numero_valores_exportados > 0)
        {
            $cadena_fecha_hora_inicio_valores_fichero_local = convierte_formato_fecha($filas_valores_parametros_csv[0]["hora"], FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO_CSV, FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO);
            $cadena_fecha_hora_fin_valores_fichero_local = convierte_formato_fecha($filas_valores_parametros_csv[$numero_valores_exportados - 1]["hora"], FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO_CSV, FORMATO_FECHA_HORA_SIN_SEGUNDOS_FICHERO);
            $nombre_fichero_valores_exportados = $nombre_tabla_valores_parametros."-".$cadena_fecha_hora_inicio_valores_fichero_local."-".$cadena_fecha_hora_fin_valores_fichero_local;
            $nombre_fichero_valores_exportados .= ".csv";

            $ruta_absoluta_fichero_valores_exportados = $directorio_absoluto_ficheros_temporales_usuario.'/'.$nombre_fichero_valores_exportados;
            $ruta_relativa_fichero_valores_exportados = $directorio_relativo_ficheros_temporales_usuario.'/'.$nombre_fichero_valores_exportados;

            $cabecera_fichero_valores_parametros = array();
            $nombre_columna_hora = $idiomas->_("Fecha");
            $nombre_columna_hora_recuperacion = $idiomas->_("Fecha de recuperación");
            $nombre_columna_tipo_valores = $idiomas->_("Tipo de valores");
            array_push($cabecera_fichero_valores_parametros, $nombre_columna_hora);
            array_push($cabecera_fichero_valores_parametros, $nombre_columna_hora_recuperacion);
            array_push($cabecera_fichero_valores_parametros, $nombre_columna_tipo_valores);
            foreach ($filas_valores_parametros_csv[0] as $clave => $valor)
            {
                if (($clave == "hora") ||
                    ($clave == "hora_recuperacion") ||
                    ($clave == "tipo_valores") ||
                    ($clave == "horario_verano"))
                {
                    continue;
                }
                array_push($cabecera_fichero_valores_parametros, $clave);
            }
            if ($zona_horaria_local != ZONA_HORARIA_UTC)
            {
                array_push($cabecera_fichero_valores_parametros, $idiomas->_("Horario de verano"));
            }
            array_unshift($filas_valores_parametros_csv, $cabecera_fichero_valores_parametros);

            escribe_fichero_valores_csv($ruta_absoluta_fichero_valores_exportados, $filas_valores_parametros_csv);
            array_push($rutas_relativas_ficheros_valores_exportados, $ruta_relativa_fichero_valores_exportados);
        }

        return ($numero_valores_exportados);
    }
?>
