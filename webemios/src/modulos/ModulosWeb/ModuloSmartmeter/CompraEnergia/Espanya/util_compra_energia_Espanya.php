<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_matematicas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/herramientas/util_tiempos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_sensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/util_herramientas_sensores.php');


    //
    // Funciones de compra de energía (Espanya)
    //


    // Importa los valores diarios de compra de energía de un sensor
    function importa_valores_diarios_compra_energia_sensor_Espanya($parametros, $ficheros)
    {
        $idiomas = new Idiomas();

        // Parámetros
        $nombre_sensor = $parametros["nombre_sensor"];
        if (array_key_exists("ruta_fichero_valores_diarios", $parametros) == true)
        {
            $ruta_fichero_valores_diarios_servidor = $parametros["ruta_fichero_valores_diarios"];
            $ruta_fichero_valores_diarios_temporal = NULL;
        }
        else
        {
            $ruta_fichero_valores_diarios_servidor = NULL;
            $ruta_fichero_valores_diarios_temporal = $ficheros["fichero_valores"]["tmp_name"];
            $nombre_fichero_valores_diarios = basename($ficheros["fichero_valores"]["name"]);
        }
        $origen_importacion_valores = $parametros["origen_importacion_valores"];

        // Se importan los valores diarios:
        // - 1. Se convierte el fichero de valores diarios en fichero de valores horarios (con hora UTC)
        // - 2. Se añade la importación de valores de sensor pendiente (de valores horarios)
        // - 3. Se elimina elfichero de valores horarios creado anteriormente (sólo se utiliza para añadir la importación de valores)

        // Rutas de ficheros
        $directorio_usuario = $_SESSION["directorio"].'/rsc/ficheros/tmp/'.$_SESSION["id_usuario"];
        if ($ruta_fichero_valores_diarios_servidor !== NULL)
        {
            // Nota: Se convierte la ruta de fichero de valores diarios del servidor de relativa a absoluta
            $nombre_fichero_valores_diarios = basename($ruta_fichero_valores_diarios_servidor);
            $ruta_fichero_valores_diarios_servidor = $_SESSION["directorio"].substr($ruta_fichero_valores_diarios_servidor, 1);
        }
        else
        {
            $ruta_fichero_valores_diarios_servidor = $ruta_fichero_valores_diarios_temporal;
        }
        $ruta_fichero_valores_horarios_servidor = $directorio_usuario."/".substr($nombre_fichero_valores_diarios, 0, -4)."_horarios.csv";

        // Se convierte el fichero de valores diarios en fichero de valores horarios (con hora UTC)
        try
        {
            convierte_fichero_valores_diarios_en_fichero_valores_horarios_utc(
                $ruta_fichero_valores_diarios_servidor,
                $ruta_fichero_valores_horarios_servidor,
                convierte_valor_horario_compra_energia_sensor_Espanya);
        }
        catch (Exception $exception)
        {
            // Se añade información de la excepción en el log
            $log = dame_log();
            $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $exception);

            // Se devuelve el error
            return(array(
                "res" => "ERROR",
                "msg" => $idiomas->_("Formato de fichero de valores diarios incorrecto"))
            );
        }

        // Se recupera la fila del sensor
        $fila_sensor = dame_fila_sensor_nombre($_SESSION["id_red"], $nombre_sensor);

        // Formato de hora
        $formato_hora_python = convierte_formato_fecha_hora_a_formato_hora_python($_SESSION["formato_fecha_local"].", H:M:S");
        $formato_hora_python_sustituto_separador = str_replace(SEPARADOR_PARAMETROS_SIMPLES, SUSTITUTO_SEPARADOR, $formato_hora_python);

        // Opciones de fichero de valores
        $opciones_fichero_valores = array();
        $opciones_fichero_valores[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_FORMATO_FICHERO] = FORMATO_FICHERO_VALORES_PERSONALIZADO;
        $opciones_fichero_valores[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_CARACTER_SEPARADOR] = ";";
        $opciones_fichero_valores[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_PUNTO_DECIMAL] = ".";
        $opciones_fichero_valores[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_CABECERAS] = VALOR_NO;
        $opciones_fichero_valores[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_NUMERO_LINEAS_CABECERAS] = 0;
        $opciones_fichero_valores[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_NUMERO_COLUMNA_FECHA] = 0;
        $opciones_fichero_valores[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_FORMATO_FECHA] = $formato_hora_python_sustituto_separador;
        $opciones_fichero_valores[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_NUMERO_COLUMNA_HORA] = "";
        $opciones_fichero_valores[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_FORMATO_HORA] = "";
        $opciones_fichero_valores[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_ZONA_HORARIA] = ZONA_HORARIA_UTC;
        $opciones_fichero_valores[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_NUMERO_COLUMNA_HORARIO_VERANO] = "";
        $opciones_fichero_valores[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_NUMERO_VALORES] = 1;
        $opciones_fichero_valores[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_SEGUNDOS_INCREMENTOS] = 3600;
        $opciones_fichero_valores[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_FICHERO_CSV_TIPO_INCREMENTOS] = TIPO_INCREMENTOS_VALORES_SENSOR_FECHA_INICIAL;
        $cadena_opciones_fichero_valores = implode(SEPARADOR_PARAMETROS_SIMPLES, $opciones_fichero_valores);

        // Opciones de valores de fichero de valores
        $opciones_valores_fichero_valores = array();
        $opciones_valores_fichero_valores[INDICE_PARAMETRO_IMPORTACION_VALORES_SENSOR_OPCIONES_VALORES_CSV_NUMERO_COLUMNA] = 1;
        $cadena_opciones_valores_fichero_valores = implode(SEPARADOR_PARAMETROS_SIMPLES, $opciones_valores_fichero_valores);

        // Parámetros para añadir la importación de valores de sensor pendiente
        $parametros_importacion_valores = array();
        $parametros_importacion_valores["id_sensor"] = $fila_sensor["id"];
        $parametros_importacion_valores["nombre_sensor"] = $fila_sensor["nombre_sensor"];
        $parametros_importacion_valores["clase_sensor"] = $fila_sensor["clase"];
        $parametros_importacion_valores["aplicar_calibracion"] = VALOR_NO;
        $parametros_importacion_valores["tipo_valores"] = TIPO_VALORES_SENSOR_INCREMENTALES;
        $parametros_importacion_valores["opciones_fichero_valores"] = $cadena_opciones_fichero_valores;
        $parametros_importacion_valores["opciones_valores_fichero_valores"] = $cadena_opciones_valores_fichero_valores;

        // Ficheros para añadir la importación de valores de sensor pendiente
        $ficheros_importacion_valores = array();
        $ficheros_importacion_valores["fichero_valores"] = array(
            "tmp_name" => $ruta_fichero_valores_horarios_servidor,
            "name" => basename($ruta_fichero_valores_horarios_servidor));

        // Se añade la importación de valores de sensor pendiente
        $resultado_adicion_importacion = anyade_importacion_valores_sensor_pendiente($parametros_importacion_valores, $ficheros_importacion_valores);
        if ($resultado_adicion_importacion["res"] == "OK")
        {
            $res = "OK";
            switch ($origen_importacion_valores)
            {
                case ORIGEN_IMPORTACION_VALORES_DIARIOS_COMPRA_ENERGIA_SENSOR_HERRAMIENTAS:
                {
                    $msg = $idiomas->_("Importación de valores diarios añadida correctamente");
                    break;
                }
                case ORIGEN_IMPORTACION_VALORES_DIARIOS_COMPRA_ENERGIA_SENSOR_INFORME:
                {
                    $msg = $idiomas->_("Exportación de valores diarios correcta e importación de valores diarios añadida correctamente");
                    break;
                }
                default:
                {
                    throw new Exception("Origen de importación de valores desconocido: '".$origen_importacion_valores."'");
                }
            }
        }
        else
        {
            $res = $resultado_adicion_importacion["res"];
            $msg = $resultado_adicion_importacion["msg"];
        }

        // Se elimina el fichero de valores horarios
        unlink($ruta_fichero_valores_horarios_servidor);

        // Se devuelve el resultado
        return(array(
            "res" => $res,
            "msg" => $msg)
        );
    }


    // Convierte el valor horario de compra de energía de un sensor (convierte de MWh a kWh)
    function convierte_valor_horario_compra_energia_sensor_Espanya($valor)
    {
        $valor *= 1000;
        return ($valor);
    }
?>
