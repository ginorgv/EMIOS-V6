<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/ElementoPlantillaInforme.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/Elementos/util_administracion_elementos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_ELEMENTO_PLANTILLA_INFORME, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST['nombre'];
    $id_plantilla_informe = $_POST['id_plantilla_informe'];
    $tipo = $_POST['tipo'];
    $id_plantilla_informe_destino = $_POST['id_plantilla_informe_destino'];
    $parametros_tipo = $_POST['parametros_tipo'];
    $parametros_tipo_json = $_POST['parametros_tipo_json'];
    $elementos_informe = $_POST['elementos_informe'];
    $modo_visibilidad = $_POST['modo_visibilidad'];
    $parametros_requeridos = $_POST['parametros_requeridos'];

    // Identificador de plantilla de informe
    $id_plantilla_informe_anterior = $id_plantilla_informe;
    if ($id_plantilla_informe_destino != ID_NINGUNO)
    {
        $id_plantilla_informe = $id_plantilla_informe_destino;
    }

    // Se comprueba si existe un elemento con el mismo nombre en la misma plantilla de informe
    // (se permiten nombres iguales en elementos "generales")
    $consulta_existe = "
        SELECT *
        FROM elementos_plantillas_informes
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')";
    switch ($tipo)
    {
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_PAGINA:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_SALTO_LINEA:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_NOTAS:
        case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN:
        {
            $consulta_existe .= "
                AND (tipo <> '".$bd_red->_($tipo)."')";
            break;
        }
    }
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un elemento con el mismo nombre");
    }
    else
    {
        // Se añade el elemento en la última posición
        $consulta_maxima_posicion = "
            SELECT
                MAX(posicion) AS max_posicion
            FROM elementos_plantillas_informes
            WHERE
                plantilla_informe = '".$id_plantilla_informe."'";
        $res_maxima_posicion = $bd_red->ejecuta_consulta($consulta_maxima_posicion);
        if ($res_maxima_posicion == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_maxima_posicion."'");
        }
        $fila_maxima_posicion = $res_maxima_posicion->dame_siguiente_fila();

        $maxima_posicion = $fila_maxima_posicion["max_posicion"];
        if ($maxima_posicion === NULL)
        {
            $posicion = 1;
        }
        else
        {
            $posicion = $maxima_posicion + 1;
        }

        $operacion_insercion = "
            INSERT INTO elementos_plantillas_informes (
                nombre,
                red,
                plantilla_informe,
                posicion,
                tipo,
                parametros_tipo,
                parametros_tipo_json,
                elementos_informe,
                modo_visibilidad,
                parametros_requeridos
            ) VALUES (
                '".$bd_red->_($nombre)."',
                '".$_SESSION["id_red"]."',
                '".$bd_red->_($id_plantilla_informe)."',
                '".$bd_red->_($posicion)."',
                '".$bd_red->_($tipo)."',
                '".$bd_red->_($parametros_tipo)."',
                '".$bd_red->_($parametros_tipo_json)."',
                '".$bd_red->_($elementos_informe)."',
                '".$bd_red->_($modo_visibilidad)."',
                '".$bd_red->_($parametros_requeridos)."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Identificador del elemento añadido
            $id_elemento = $bd_red->dame_id_autoincremental_ultima_insercion();

            // Si la plantilla destino es diferente de la plantilla original, se eliminan los parámetros del elemento
            if ($id_plantilla_informe != $id_plantilla_informe_anterior)
            {
                elimina_parametros_elemento_plantilla_informe($id_plantilla_informe, $id_elemento);
            }

            $res = "OK";
            $msg = $idiomas->_("Elemento añadido correctamente");

            // Acciones a realizar al añadir un elemento
            realiza_acciones_elemento_anyadido(
                $id_plantilla_informe,
                $id_elemento,
                $tipo,
                $parametros_tipo,
                $parametros_tipo_json);
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "id_elemento" => $id_elemento))
    );


    //
    // Funciones auxiliares
    //


    // Realiza acciones al añadir un elemento
    function realiza_acciones_elemento_anyadido(
        $id_plantilla_informe,
        $id_elemento,
        $tipo,
        $cadena_parametros_tipo,
        $cadena_parametros_tipo_json)
    {
        // Se actualiza el usuario de la plantilla de informe (si es necesario)
        actualiza_usuario_plantilla_informe($id_plantilla_informe);

        // Acciones a realizar dependiendo del tipo de elemento
        switch ($tipo)
        {
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO:
            {
                $anyadir_texto_elemento = true;
                switch ($tipo)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO:
                    {
                        $tipo_plantilla_informe = dame_tipo_plantilla_informe($id_plantilla_informe);
                        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                        {
                            $anyadir_texto_elemento = false;
                        }
                        break;
                    }
                }
                if ($anyadir_texto_elemento == true)
                {
                    $parametros_tipo = ElementoPlantillaInforme::dame_nombres_valores_parametros_tipo_elemento(
                        $tipo,
                        $cadena_parametros_tipo,
                        $cadena_parametros_tipo_json);
                    switch ($tipo)
                    {
                        case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
                        {
                            $texto = $parametros_tipo["subtitulo"];
                            break;
                        }
                        case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO:
                        {
                            $texto = $parametros_tipo["titulo"];
                            break;
                        }
                        case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO:
                        {
                            $texto = $parametros_tipo["texto"];
                            break;
                        }
                    }
                    anyade_texto_elemento_informes_automaticos_plantilla_informe(
                        $id_plantilla_informe,
                        $tipo,
                        $id_elemento,
                        $texto);
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN:
            {
                anyade_imagen_informes_automaticos_plantilla_informe($id_plantilla_informe, $id_elemento);
                break;
            }
        }
    }
?>
