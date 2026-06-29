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
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_PLANTILLA_INFORME, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
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
    $id_plantilla_informe_anterior = $_POST["id_plantilla_informe_anterior"];
    $id_red_destino = $_POST["id_red_destino"];
    $id_usuario_destino = $_POST["id_usuario_destino"];

    // Id de red y de usuario de plantilla de informe
    $id_red_plantilla_informe = $_SESSION["id_red"];
    $id_usuario_plantilla_informe = $_SESSION["id_usuario"];
    $perfil_usuario_plantilla_informe = $_SESSION["perfil"];

    // Flag de duplicado de plantilla de informe
    $duplicado_plantilla_informe = ($id_plantilla_informe_anterior != ID_NINGUNO);

    // Duplicado de plantilla de informe a otra red (del mismo usuario)
    if (($duplicado_plantilla_informe == true) &&
        (($tipo == TIPO_PLANTILLA_INFORME_CONFIGURABLE) && ($id_red_destino != $id_red_plantilla_informe)))
    {
        // Comprobación de elementos visibles
        $nombre_primer_elemento_no_visible = "";
        $elementos_plantilla_informe_visibles = dame_elementos_plantilla_informe_visibles_red_usuario(
            $id_plantilla_informe_anterior,
            $id_red_destino,
            $_SESSION["id_usuario"],
            $_SESSION["perfil"],
            $nombre_primer_elemento_no_visible);
        if ($elementos_plantilla_informe_visibles == false)
        {
            $mensaje_error = $idiomas->_("La plantilla de informe tiene elementos no visibles en la red destino")."\n"."(".$nombre_primer_elemento_no_visible.")";
            $resultado = array(
                "res" => "ERROR",
                "msg" => $mensaje_error);
            print(json_encode($resultado));
            return;
        }

        // Comprobación de elementos configurables
        $nombre_primer_elemento_no_configurable = "";
        $elementos_plantilla_informe_configurables = dame_elementos_plantilla_informe_configurables(
            $id_plantilla_informe_anterior,
            $nombre_primer_elemento_no_configurable);
        if ($elementos_plantilla_informe_configurables == false)
        {
            $mensaje_error = $idiomas->_("La plantilla de informe tiene elementos no configurables")."\n"."(".$nombre_primer_elemento_no_configurable.")";
            $resultado = array(
                "res" => "ERROR",
                "msg" => $mensaje_error);
            print(json_encode($resultado));
            return;
        }

        // Red de la plantilla de informe
        $id_red_plantilla_informe = $id_red_destino;
    }

    // Duplicado de plantilla de informe a otro usuario
    if (($duplicado_plantilla_informe == true) && ($id_usuario_destino != ID_NINGUNO))
    {
        // Comprobación de elementos visibles
        $nombre_primer_elemento_no_visible = "";
        $elementos_plantilla_informe_visibles_usuario_destino = dame_elementos_plantilla_informe_visibles_usuario(
            $id_plantilla_informe_anterior,
            $id_usuario_destino,
            $nombre_primer_elemento_no_visible);
        if ($elementos_plantilla_informe_visibles_usuario_destino == false)
        {
            $mensaje_error = $idiomas->_("La plantilla de informe tiene elementos no visibles por el usuario destino")."\n"."(".$nombre_primer_elemento_no_visible.")";
            $resultado = array(
                "res" => "ERROR",
                "msg" => $mensaje_error);
            print(json_encode($resultado));
            return;
        }

        // Usuario de la plantilla de informe
        $id_usuario_plantilla_informe = $id_usuario_destino;
        $perfil_usuario_plantilla_informe = PERFIL_USUARIO_ESTANDAR;
    }

    // Se comprueba si existe una plantilla de informe con el mismo nombre:
    // - Si el usuario es estándar: Se comprueba en las plantillas del mismo usuario
    // - Si el usuario no es estándar: Se comprueba en las plantillas de los usuarios no estándar
    switch ($perfil_usuario_plantilla_informe)
    {
        case PERFIL_USUARIO_ESTANDAR:
        {
            $consulta_existe = "
                SELECT nombre
                FROM plantillas_informes
                WHERE
                    (nombre = '".$bd_red->_($nombre)."')
                    AND (usuario = '".$bd_red->_($id_usuario_plantilla_informe)."')
                    AND (red = '".$bd_red->_($id_red_plantilla_informe)."')";
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
                        OR ((plantillas_informes.usuario = '') AND (usuarios.id = '".$bd_red->_($id_usuario_plantilla_informe)."')))
                    AND (plantillas_informes.red = '".$bd_red->_($id_red_plantilla_informe)."')";
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
        // Se añade la plantilla de informe
        $operacion_insercion = "
            INSERT INTO plantillas_informes (
                nombre,
                red,
                usuario,
                descripcion,
                tipo,
                titulo_informe,
                periodo_tiempo_defecto,
                iniciar_comienzo_periodo_tiempo_defecto,
                tipo_seleccion_horario_semanal_fechas,
                logo_personalizado,
                nombre_logo,
                tema
            ) VALUES (
                '".$bd_red->_($nombre)."',
                '".$bd_red->_($id_red_plantilla_informe)."',
                '".$bd_red->_($id_usuario_plantilla_informe)."',
                '".$bd_red->_($descripcion)."',
                '".$bd_red->_($tipo)."',
                '".$bd_red->_($titulo_informe)."',
                '".$bd_red->_($periodo_tiempo_defecto)."',
                '".$bd_red->_($iniciar_comienzo_periodo_tiempo_defecto)."',
                '".$bd_red->_($tipo_seleccion_horario_semanal_fechas)."',
                '".$bd_red->_($logo_personalizado)."',
                '".$bd_red->_($nombre_logo)."',
                '".$bd_red->_($tema)."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Se recupera el id de la plantilla de informe añadida
            $id_plantilla_informe = $bd_red->dame_id_autoincremental_ultima_insercion();

            // Si el identificador de plantilla de informe existe, es un duplicado de una programacion existente:
            // - Se duplican los elementos (si los hay)
            if ($id_plantilla_informe_anterior != ID_NINGUNO)
            {
                // Duplica los parámetros y los elementos de la plantilla de informe anterior
                $ids_parametros_plantilla_informe_anterior = NULL;
                $ids_parametros_plantilla_informe = NULL;
                $ids_elementos_plantilla_informe_anterior = NULL;
                $ids_elementos_plantilla_informe = NULL;
                duplica_parametros_plantilla_informe_anterior(
                    $id_red_plantilla_informe,
                    $id_plantilla_informe_anterior,
                    $id_plantilla_informe,
                    $ids_parametros_plantilla_informe_anterior,
                    $ids_parametros_plantilla_informe);
                duplica_elementos_plantilla_informe_anterior(
                    $id_red_plantilla_informe,
                    $id_plantilla_informe_anterior,
                    $id_plantilla_informe,
                    $ids_parametros_plantilla_informe_anterior,
                    $ids_parametros_plantilla_informe,
                    $ids_elementos_plantilla_informe_anterior,
                    $ids_elementos_plantilla_informe);
            }

            $res = "OK";
            $msg = $idiomas->_("Plantilla de informe añadida correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_insercion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "id_plantilla_informe" => $id_plantilla_informe))
    );
?>
