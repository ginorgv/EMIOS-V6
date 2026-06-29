<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_cadenas.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_GENERAR_INFORME_FICHERO, $_POST);

    $idiomas = new Idiomas();

    // Parámetros del informe
    // Nota: Los parámetros del informe se pasan en formato JSON (se utiliza 'ajax' directamente, no 'POST')
    // (https://stackoverflow.com/questions/16104078/appending-array-to-formdata-and-send-via-ajax)
    $ficheros = $_FILES;
    $parametros_informe = json_decode($_POST["parametros_informe"], true);

    // Se añaden los parámetros comunes a todos los informes automáticos
    $parametros_informe["id_usuario"] = $_SESSION["id_usuario"];
    $parametros_informe["id_red"] = $_SESSION["id_red"];
    $parametros_informe["idioma"] = $_SESSION["idioma"];

    // Si el informe es una plantilla de informe,
    // - Se establecen el título y nombre del informe
    // - Se añaden los parámetros vacíos (si no hay)
    // - Se añade el tema
    if ($parametros_informe["tipo"] == TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME)
    {
        // Información de la plantilla de informe
        $id_plantilla_informe = $parametros_informe["id_plantilla_informe"];
        $fila_plantilla_informe = dame_fila_plantilla_informe($id_plantilla_informe);
        $nombre = $fila_plantilla_informe["nombre"];
        $titulo_informe = $fila_plantilla_informe["titulo_informe"];
        $tema = $fila_plantilla_informe["tema"];

        // Se establecen el título y nombre del informe
        $titulo = $titulo_informe;
        if ($titulo == "")
        {
            $titulo = $nombre;
        }
        $nombre_informe = convierte_ascii_estandar($titulo);
        $nombre_informe = reemplaza_caracteres_no_alfanumericos($nombre_informe, "_");
        $nombre_informe = strtolower($nombre_informe);
        $parametros_informe["titulo"] = $titulo;
        $parametros_informe["nombre_informe"] = $nombre_informe;

        // Se añaden los parámetros vacíos (si no hay)
        if (array_key_exists("ids_parametros", $parametros_informe) == false)
        {
            $parametros_informe["ids_parametros"] = "";
            $parametros_informe["valores_parametros"] = "";
        }
        if (array_key_exists("ids_elementos_portada", $parametros_informe) == false)
        {
            $parametros_informe["ids_elementos_portada"] = "";
        }
        if (array_key_exists("ids_elementos_titulo", $parametros_informe) == false)
        {
            $parametros_informe["ids_elementos_titulo"] = "";
        }
        if (array_key_exists("ids_elementos_texto", $parametros_informe) == false)
        {
            $parametros_informe["ids_elementos_texto"] = "";
        }
        if (array_key_exists("ids_elementos_notas", $parametros_informe) == false)
        {
            $parametros_informe["ids_elementos_notas"] = "";
        }
        if (array_key_exists("ids_elementos_imagen", $parametros_informe) == false)
        {
            $parametros_informe["ids_elementos_imagen"] = "";
        }

        // Se añade el tema
        $parametros_informe["tema"] = $tema;
    }

    // Se recupera el directorio de ficheros temporales del usuario
    $directorio_absoluto_ficheros_temporales_usuario = dame_directorio_ficheros_temporales_usuario($_SESSION["id_usuario"]);
    $directorio_relativo_ficheros_temporales_usuario = str_replace($_SESSION["directorio"], ".", $directorio_absoluto_ficheros_temporales_usuario);

    // Sustitución de '\\' por '*' (para evitar problemas con json)
    $directorio_absoluto_ficheros_temporales_usuario_json = str_replace("\\", "*", $directorio_absoluto_ficheros_temporales_usuario);
    $parametros_informe["directorio"] = $directorio_absoluto_ficheros_temporales_usuario_json;

    // Conversión de formatos de fechas locales a formato de función
    if (array_key_exists("fecha_hora_inicio", $parametros_informe) == true)
    {
        $parametros_informe["fecha_hora_inicio"] = convierte_formato_fecha($parametros_informe["fecha_hora_inicio"], $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
    }
    if (array_key_exists("fecha_hora_fin", $parametros_informe) == true)
    {
        $parametros_informe["fecha_hora_fin"] = convierte_formato_fecha($parametros_informe["fecha_hora_fin"], $_SESSION["formato_fecha_hora_local"], FORMATO_FECHA_HORA_FUNCIONES);
    }
    if (array_key_exists("fecha_inicio_anterior", $parametros_informe) == true)
    {
        $parametros_informe["fecha_hora_inicio_anterior"] = convierte_formato_fecha($parametros_informe["fecha_inicio_anterior"], $_SESSION["formato_fecha_local"], FORMATO_FECHA_FUNCIONES);
    }
    if (array_key_exists("fecha_inicio_posterior", $parametros_informe) == true)
    {
        $parametros_informe["fecha_hora_inicio_posterior"] = convierte_formato_fecha($parametros_informe["fecha_inicio_posterior"], $_SESSION["formato_fecha_local"], FORMATO_FECHA_FUNCIONES);
    }

    // Recuperación de ruta de fichero (para pasar ficheros como parámetros, p.e.: fichero de potencias máximas)
    if (array_key_exists("fichero", $ficheros) == true)
    {
        // Se copia el fichero al directorio de los ficheros informe (si no se borra después de ejecutar el script PHP automáticamente)
        $ruta_fichero_temporal = $ficheros["fichero"]["tmp_name"];
        $ruta_fichero = $directorio_absoluto_ficheros_temporales_usuario."/fichero.tmp";
        copy($ruta_fichero_temporal, $ruta_fichero);

        // Sustitución de '\\' por '*' (para evitar problemas con json)
        $ruta_fichero_json = str_replace("\\", "*", $ruta_fichero);
        $parametros_informe["ruta_fichero"] = $ruta_fichero_json;

        // Se recupera el nombre del fichero
        $parametros_informe["nombre_fichero"] = $ficheros["fichero"]["name"];
    }

    // Parámetros tipo json (se pasan los parámetros de tipo json en un fichero
    // porque luego se utiliza este fichero en la llamada a la función 'web' para generar el informe fichero)
    $hay_parametros_tipo_json = (array_key_exists("parametros_tipo_json", $parametros_informe) == true);
    if ($hay_parametros_tipo_json == true)
    {
        // Se guardan los parámetros json en un fichero (para evitar el límite de caracteres en línea de comando y en URL)
        $nombre_fichero_parametros_json = "informe_fichero_".dame_timestamp_ahora_milisegundos_utc().".json";
        $ruta_fichero_parametros_json = $directorio_absoluto_ficheros_temporales_usuario."/".$nombre_fichero_parametros_json;
        $res_escritura_fichero_parametros_json = file_put_contents($ruta_fichero_parametros_json, $parametros_informe["parametros_tipo_json"]);
        if ($ruta_fichero_parametros_json === false)
        {
            throw new Exception("No se ha podido escribir el fichero de parámetros (json) (ruta: '".$res_escritura_fichero_parametros_json."')");
        }
        unset($parametros_informe["parametros_tipo_json"]);

        // Sustitución de '\\' por '*' (para evitar problemas con json)
        $ruta_fichero_parametros_json_json = str_replace("\\", "*", $ruta_fichero_parametros_json);
        $parametros_informe["ruta_fichero_parametros_tipo_json"] = $ruta_fichero_parametros_json_json;
    }

    // Llamada a la función externa de generación del informe
    try
    {
        $parametros_funcion_externa = array(
            "llamante" => "web_emios",
            "nombre" => NOMBRE_FUNCION_GENERA_INFORME_FICHERO,
            "parametros_informe" => $parametros_informe);

        $ruta_servicios_emios = dame_valor_entrada_ini("ruta_servicios_emios");
        $resultado_funcion_externa = ejecuta_funcion_externa($ruta_servicios_emios, $parametros_funcion_externa, false);

        // Nota: No hay finally en PHP (a partir de 5.5 sí lo hay)
        // Se borra el fichero de parámetros tipo json
        // (Nota desarrollo: Comentar para no borrar el fichero de parámetros json)
        if ($hay_parametros_tipo_json == true)
        {
            unlink($ruta_fichero_parametros_json);
        }
    }
    catch (Exception $e)
    {
        // Se borra el fichero de parámetros tipo json
        // (Nota desarrollo: Comentar para no borrar el fichero de parámetros json)
        if ($hay_parametros_tipo_json == true)
        {
            unlink($ruta_fichero_parametros_json);
        }

        // Se relanza la excepción
        throw $e;
    }

    // Se recupera el nombre del fichero 'pdf' generado
    $nombre_fichero = $resultado_funcion_externa["nombre_fichero_informe"];

    // El enlace de descarga es relativo al directorio local '.' (el directorio '.' es el directorio raiz en JavaScript)
    $res = "OK";
    $enlace_descarga = $directorio_relativo_ficheros_temporales_usuario.'/'.$nombre_fichero;

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "enlace_descarga" => $enlace_descarga,
        "titulo" => $parametros_informe["titulo"]))
	);
?>
