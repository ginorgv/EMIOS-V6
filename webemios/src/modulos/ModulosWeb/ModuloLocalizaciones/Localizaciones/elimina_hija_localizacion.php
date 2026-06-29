<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/src/lib/herramientas/activa_captura_excepciones.php');
	include_once($_SESSION["directorio"].'/src/lib/Idiomas/Idiomas.php');

	include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_hijas_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ELIMINAR_HIJA_LOCALIZACION, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_hija_localizacion = $_POST['id_hija_localizacion'];
    $id_localizacion_padre = $_POST['id_localizacion_padre'];
    $id_localizacion_hija = $_POST['id_localizacion_hija'];

    // Carga de información de localizaciones
    $info_localizaciones_padres = NULL;
    $info_localizaciones_hijas = NULL;
    carga_informacion_localizaciones_padres_hijas($info_localizaciones_padres, $info_localizaciones_hijas);
    elimina_localizacion_padre($info_localizaciones_padres, $id_localizacion_padre, $id_localizacion_hija);
    elimina_localizacion_hija($info_localizaciones_hijas, $id_localizacion_padre, $id_localizacion_hija);

    // Comprobaciones antes de eliminar la hija de la localización:
    // - Comprobar que las localizaciones de los grupos y sus nodos son correctas (después de eliminar la localización hija)
    $eliminar_hija_localizacion = true;

    // Comprobación de que las localizaciones de los grupos sus nodos son correctas (después de eliminar la localización hija)
    if ($eliminar_hija_localizacion == true)
    {
        $localizaciones_grupos_sensores_correctas = dame_localizaciones_grupos_localizacion_correctas(
            $info_localizaciones_padres,
            $info_localizaciones_hijas,
            TIPO_NODO_SENSOR,
            $id_localizacion_padre);
        if ($localizaciones_grupos_sensores_correctas == false)
        {
            $eliminar_hija_localizacion = false;

            $res = "ERROR";
            $msg = $idiomas->_("Las localizaciones de los grupos de sensores y los sensores asignados a los mismos son incorrectas");
        }
    }
    if ($eliminar_hija_localizacion == true)
    {
        $localizaciones_grupos_actuadores_correctas = dame_localizaciones_grupos_localizacion_correctas(
            $info_localizaciones_padres,
            $info_localizaciones_hijas,
            TIPO_NODO_ACTUADOR,
            $id_localizacion_padre);
        if ($localizaciones_grupos_actuadores_correctas == false)
        {
            $eliminar_hija_localizacion = false;

            $res = "ERROR";
            $msg = $idiomas->_("Las localizaciones de los grupos de actuadores y los actuadores asignados a los mismos son incorrectas");
        }
    }

    // Se elimina la hija de la localización
    if ($eliminar_hija_localizacion == true)
    {
        // Se recupera la fila de la hija de la localización
        $fila_hija_localizacion = dame_fila_hija_localizacion($id_hija_localizacion);

        // Se elimina la hija de la localización
        $operacion_borrado = "
            DELETE
            FROM hijas_localizaciones
            WHERE
                id = '".$bd_red->_($id_hija_localizacion)."'";
        $res_borrado = $bd_red->ejecuta_operacion($operacion_borrado);
        if ($res_borrado == true)
        {
            // Localizaciones padre de la localización hija eliminada
            $id_localizaciones_padre = $info_localizaciones_padres[$id_localizacion_hija];

            // Recarga la información de las localizaciones padres e hijas
            $info_localizaciones_padres = NULL;
            $info_localizaciones_hijas = NULL;
            carga_informacion_localizaciones_padres_hijas($info_localizaciones_padres, $info_localizaciones_hijas, true);

            // Se actualiza el orden de la localización padre (y sus padres recursivamente)
            $ordenes_localizaciones = NULL;
            carga_ordenes_localizaciones_padres_hijas($ordenes_localizaciones);
            $numero_localizaciones_actualizadas = actualiza_orden_localizaciones_ascendientes(
                $info_localizaciones_padres,
                $info_localizaciones_hijas,
                $ordenes_localizaciones,
                $id_localizacion_padre);

            // Se eliminan y modifican los elementos que han dejado de ser visibles de los parámetros de los módulos de los usuarios
            // (pueden dejar de ver los sensores que pertenecían a la localización hija y la han dejado de visualizar)
            elimina_modifica_elementos_no_visibles_parametros_modulos_usuarios();

            // Se añade la acción de usuario
            anyade_accion_usuario_eliminar_hija_localizacion($fila_hija_localizacion);

            $res = "OK";
            $msg = $idiomas->_("Localización hija eliminada correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_borrado."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "numero_localizaciones_actualizadas" => $numero_localizaciones_actualizadas))
    );


    //
    // Funciones auxiliares
    //


    // Añade la acción de usuario de eliminación de la hija de localización
    function anyade_accion_usuario_eliminar_hija_localizacion($fila)
    {
        // Filas de las localizaciones padre e hija
        $fila_localizacion_padre = dame_fila_localizacion($fila["localizacion_padre"]);
        $fila_localizacion_hija = dame_fila_localizacion($fila["localizacion_hija"]);

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ELIMINAR_HIJA_LOCALIZACION;
        $objeto_accion_usuario = $fila_localizacion_hija["nombre"]." (".$fila_localizacion_padre["nombre"].")";

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            NULL,
            NULL,
            NULL);
    }
?>
