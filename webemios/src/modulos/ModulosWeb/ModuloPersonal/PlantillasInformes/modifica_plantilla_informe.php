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
    include_once($_SESSION["directorio"].'/src/lib/modulos/imagenes/util_imagenes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_PLANTILLA_INFORME, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_plantilla_informe = $_POST["id_plantilla_informe"];
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $tipo = $_POST["tipo"];
    $titulo_informe = $_POST["titulo_informe"];
    $periodo_tiempo_defecto = $_POST["periodo_tiempo_defecto"];
    $iniciar_comienzo_periodo_tiempo_defecto = $_POST["iniciar_comienzo_periodo_tiempo_defecto"];
    $tipo_seleccion_horario_semanal_fechas = $_POST["tipo_seleccion_horario_semanal_fechas"];
    $logo_personalizado = $_POST["logo_personalizado"];
    $nombre_logo = $_POST["nombre_logo"];
    $tema = $_POST["tema"];

    // Parámetros auxiliares
    $logo_personalizado_anterior = $_POST['logo_personalizado_anterior'];

    // Se comprueba si existe otra plantilla de informe con el mismo nombre:
    // - Si el usuario es estándar: Se comprueba en las plantillas del mismo usuario
    // - Si el usuario no es estándar: Se comprueba en las plantillas de los usuarios no estándar
    switch ($_SESSION["perfil"])
    {
        case PERFIL_USUARIO_ESTANDAR:
        {
            $consulta_existe = "
                SELECT nombre
                FROM plantillas_informes
                WHERE
                    (nombre = '".$bd_red->_($nombre)."')
                    AND (usuario = '".$_SESSION["id_usuario"]."')
                    AND (red = '".$_SESSION["id_red"]."')
                    AND (id <> '".$bd_red->_($id_plantilla_informe)."')";
            break;
        }
        default:
        {
            $consulta_existe = "
                SELECT plantillas_informes.nombre
                FROM
                    plantillas_informes,
                    usuarios
                WHERE
                    (plantillas_informes.nombre = '".$bd_red->_($nombre)."')
                    AND
                        (((plantillas_informes.usuario = usuarios.id) AND (usuarios.perfil <> '".PERFIL_USUARIO_ESTANDAR."'))
                        OR ((plantillas_informes.usuario = '') AND (usuarios.id = '".$_SESSION["id_usuario"]."')))
                    AND (plantillas_informes.red = '".$_SESSION["id_red"]."')
                    AND (plantillas_informes.id <> '".$bd_red->_($id_plantilla_informe)."')";
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
        $msg = $idiomas->_("Ya existe una plantilla de informe con el mismo nombre");
    }
    else
    {
        // Si antes había logo personalizado y ahora no, se eliminan los logos anteriores
        if (($logo_personalizado_anterior == VALOR_SI) && ($logo_personalizado == VALOR_NO))
        {
            elimina_imagen_base_datos(ORIGEN_IMAGEN_PLANTILLA_INFORMES_LOGO_PDF, $id_plantilla_informe);
        }

        // Se modifica la plantilla de informes
        $operacion_modificacion = "
            UPDATE plantillas_informes
            SET
                usuario = '".$bd_red->_($_SESSION["id_usuario"])."',
                nombre = '".$bd_red->_($nombre)."',
                descripcion = '".$bd_red->_($descripcion)."',
                tipo = '".$bd_red->_($tipo)."',
                titulo_informe = '".$bd_red->_($titulo_informe)."',
                periodo_tiempo_defecto = '".$bd_red->_($periodo_tiempo_defecto)."',
                iniciar_comienzo_periodo_tiempo_defecto = '".$bd_red->_($iniciar_comienzo_periodo_tiempo_defecto)."',
                tipo_seleccion_horario_semanal_fechas = '".$bd_red->_($tipo_seleccion_horario_semanal_fechas)."',
                logo_personalizado = '".$bd_red->_($logo_personalizado)."',
                nombre_logo = '".$bd_red->_($nombre_logo)."',
                tema = '".$bd_red->_($tema)."'
            WHERE
                id = '".$bd_red->_($id_plantilla_informe)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            $res = "OK";
            $msg = $idiomas->_("Plantilla de informe modificada correctamente");
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
?>
