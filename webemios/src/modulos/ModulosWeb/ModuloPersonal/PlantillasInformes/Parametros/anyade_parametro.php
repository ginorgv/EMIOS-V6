<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/InformesFichero/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_ELEMENTO_PLANTILLA_INFORME, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $nombre = $_POST['nombre'];
    $id_plantilla_informe = $_POST['id_plantilla_informe'];
    $tipo = $_POST['tipo'];
    $parametros_tipo = $_POST['parametros_tipo'];

    // Se comprueba si existe un parámetro con el mismo nombre en la misma plantilla de informe
    $consulta_existe = "
        SELECT *
        FROM parametros_plantillas_informes
        WHERE
            (nombre = '".$bd_red->_($nombre)."')
            AND (plantilla_informe = '".$bd_red->_($id_plantilla_informe)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe un parámetro con el mismo nombre");
    }
    else
    {
        // Se añade el parámetro  en la última posición
        $consulta_maxima_posicion = "
            SELECT
                MAX(posicion) AS max_posicion
            FROM parametros_plantillas_informes
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
            INSERT INTO parametros_plantillas_informes (
                nombre,
                red,
                plantilla_informe,
                posicion,
                tipo,
                parametros_tipo
            ) VALUES (
                '".$bd_red->_($nombre)."',
                '".$_SESSION["id_red"]."',
                '".$bd_red->_($id_plantilla_informe)."',
                '".$bd_red->_($posicion)."',
                '".$bd_red->_($tipo)."',
                '".$bd_red->_($parametros_tipo)."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            $res = "OK";
            $msg = $idiomas->_("Parámetro añadido correctamente");

            // Acciones a realizar al añadir un parámetro
            $id_parametro = $bd_red->dame_id_autoincremental_ultima_insercion();
            realiza_acciones_parametro_anyadido($id_plantilla_informe, $id_elemento, $tipo);
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Realiza acciones al añadir un parámetro
    function realiza_acciones_parametro_anyadido($id_plantilla_informe, $id_parametro)
    {
        // Se actualiza el usuario de la plantilla de informe (si es necesario)
        actualiza_usuario_plantilla_informe($id_plantilla_informe);

        // Se añade el parámetro (con valor 'ninguno') a los informes automáticos de plantillas de informes (configurables) correspondientes
        anyade_parametro_informes_automaticos_plantilla_informe($id_plantilla_informe, $id_parametro);
    }
?>
