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
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_ELEMENTO_PLANTILLA_INFORME, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_elemento = $_POST['id_elemento'];
    $nombre = $_POST['nombre'];
    $id_plantilla_informe = $_POST['id_plantilla_informe'];
    $tipo = $_POST['tipo'];
    $parametros_tipo = $_POST['parametros_tipo'];
    $parametros_tipo_json = $_POST['parametros_tipo_json'];
    $elementos_informe = $_POST['elementos_informe'];
    $modo_visibilidad = $_POST['modo_visibilidad'];
    $parametros_requeridos = $_POST['parametros_requeridos'];
    $tipo_anterior = $_POST['tipo_anterior'];

    // Se comprueba si existe otro elemento con el mismo nombre en la misma plantilla de informe
    // (se permiten nombres iguales en elementos "generales")
    $consulta_existe = "
        SELECT *
        FROM elementos_plantillas_informes
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')
            AND (id <> '".$bd_red->_($id_elemento)."')";
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
        // Se modifica el elemento
        $operacion_modificacion = "
            UPDATE elementos_plantillas_informes
            SET
                nombre = '".$bd_red->_($nombre)."',
                plantilla_informe = '".$bd_red->_($id_plantilla_informe)."',
                tipo = '".$bd_red->_($tipo)."',
                parametros_tipo = '".$bd_red->_($parametros_tipo)."',
                parametros_tipo_json = '".$bd_red->_($parametros_tipo_json)."',
                elementos_informe = '".$bd_red->_($elementos_informe)."',
                modo_visibilidad = '".$bd_red->_($modo_visibilidad)."',
                parametros_requeridos = '".$bd_red->_($parametros_requeridos)."'
            WHERE
                id = '".$bd_red->_($id_elemento)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            $res = "OK";
            $msg = $idiomas->_("Elemento modificado correctamente");

            // Acciones a realizar al modificar un elemento
            realiza_acciones_elemento_modificado(
                $id_plantilla_informe,
                $id_elemento,
                $tipo,
                $tipo_anterior,
                $parametros_tipo,
                $parametros_tipo_json);
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Realiza acciones al modificar un elemento
    function realiza_acciones_elemento_modificado(
        $id_plantilla_informe,
        $id_elemento,
        $tipo,
        $tipo_anterior,
        $cadena_parametros_tipo,
        $cadena_parametros_tipo_json)
    {
        // Se actualiza el usuario de la plantilla de informe (si es necesario)
        actualiza_usuario_plantilla_informe($id_plantilla_informe);

        // Acciones a realizar dependiendo del tipo de elemento
        switch ($tipo_anterior)
        {
            case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO:
            case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO:
            {
                if ($tipo != $tipo_anterior)
                {
                    $eliminar_anyadir_texto_elemento = true;
                    switch ($tipo)
                    {
                        case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
                        case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO:
                        {
                            $tipo_plantilla_informe = dame_tipo_plantilla_informe($id_plantilla_informe);
                            if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                            {
                                $eliminar_anyadir_texto_elemento = false;
                            }
                            break;
                        }
                    }
                    if ($eliminar_anyadir_texto_elemento == true)
                    {
                        elimina_texto_elemento_informes_automaticos_plantilla_informe($id_plantilla_informe, $tipo_anterior, $id_elemento);
                        switch ($tipo)
                        {
                            case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
                            case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO:
                            case TIPO_ELEMENTO_PLANTILLA_INFORME_TEXTO:
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
                                break;
                            }
                        }
                    }
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN:
            {
                if ($tipo != $tipo_anterior)
                {
                    elimina_imagen_informes_automaticos_plantilla_informe($id_plantilla_informe, $id_elemento);
                }
                break;
            }
        }
    }
?>
