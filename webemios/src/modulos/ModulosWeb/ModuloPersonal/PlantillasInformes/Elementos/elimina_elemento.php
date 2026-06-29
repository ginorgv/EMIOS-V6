<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');
	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_ELEMENTO_PLANTILLA_INFORME, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_elemento = $_POST['id_elemento'];
    $id_plantilla_informe = $_POST['id_plantilla_informe'];
    $tipo = $_POST['tipo'];

	$operacion_borrado = "
        DELETE
        FROM elementos_plantillas_informes
        WHERE
            id = '".$bd_red->_($id_elemento)."'";
    $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
    if ($res_borrado == true)
    {
        $res = "OK";
        $msg = $idiomas->_("Elemento eliminado correctamente");

        // Acciones a realizar al eliminar un elemento
        realiza_acciones_elemento_eliminado($id_plantilla_informe, $id_elemento, $tipo);
    }
    else
    {
        throw new Exception("Error en la operación: '".$operacion_borrado."'");
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg))
    );


    //
    // Funciones auxiliares
    //


    // Realiza acciones al eliminar un elemento
    function realiza_acciones_elemento_eliminado($id_plantilla_informe, $id_elemento, $tipo)
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
                $eliminar_texto_elemento = true;
                switch ($tipo)
                {
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_PORTADA:
                    case TIPO_ELEMENTO_PLANTILLA_INFORME_TITULO:
                    {
                        $tipo_plantilla_informe = dame_tipo_plantilla_informe($id_plantilla_informe);
                        if ($tipo_plantilla_informe == TIPO_PLANTILLA_INFORME_FIJO)
                        {
                            $eliminar_texto_elemento = false;
                        }
                        break;
                    }
                }
                if ($eliminar_texto_elemento == true)
                {
                    elimina_texto_elemento_informes_automaticos_plantilla_informe($id_plantilla_informe, $tipo, $id_elemento);
                }
                break;
            }
            case TIPO_ELEMENTO_PLANTILLA_INFORME_IMAGEN:
            {
                $id_origen = implode(SEPARADOR_PARAMETROS_SIMPLES, array($id_plantilla_informe, $id_elemento));
                elimina_imagen_base_datos(ORIGEN_IMAGEN_ELEMENTO_PLANTILLA_INFORME_IMAGEN, $id_origen);
                elimina_imagen_informes_automaticos_plantilla_informe($id_plantilla_informe, $id_elemento);
                break;
            }
        }
    }
?>
