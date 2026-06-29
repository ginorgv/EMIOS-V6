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
    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/util_acciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_hijas_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_HIJA_LOCALIZACION, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_hija_localizacion = $_POST['id_hija_localizacion'];
    $id_localizacion_padre = $_POST['id_localizacion_padre'];
    $id_localizacion_hija = $_POST['id_localizacion_hija'];
    $id_localizacion_hija_anterior = $_POST['id_localizacion_hija_anterior'];

    // Se comprueba si existe otra hija de localización con las mismas localizaciones padre e hija
    $consulta_existe = "
        SELECT *
        FROM hijas_localizaciones
        WHERE
            (localizacion_padre = '".$bd_red->_($id_localizacion_padre)."')
            AND (localizacion_hija = '".$bd_red->_($id_localizacion_hija)."')
            AND (id <> '".$bd_red->_($id_hija_localizacion)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("Ya existe esta localización hija");
    }
    else
    {
        // Carga de información de localizaciones padres e hijas
        $info_localizaciones_padres = NULL;
        $info_localizaciones_hijas = NULL;
        carga_informacion_localizaciones_padres_hijas($info_localizaciones_padres, $info_localizaciones_hijas);
        elimina_localizacion_padre($info_localizaciones_padres, $id_localizacion_padre, $id_localizacion_hija_anterior);
        elimina_localizacion_hija($info_localizaciones_hijas, $id_localizacion_padre, $id_localizacion_hija_anterior);
        anyade_localizacion_padre($info_localizaciones_padres, $id_localizacion_padre, $id_localizacion_hija);
        anyade_localizacion_hija($info_localizaciones_hijas, $id_localizacion_padre, $id_localizacion_hija);

        // Comprobaciones antes de modificar la hija de la localización:
        // - Comprobación de bucle en las localizaciones hijas (después de modificar la localización hija)
        // - Comprobar que las localizaciones de los grupos y sus nodos son correctas (después de modificar la localización hija)
        $modificar_hija_localizacion = true;

        // Comprobación de bucle en las localizaciones hijas (después de modificar la localización hija)
        if ($modificar_hija_localizacion == true)
        {
            $existe_bucle = existe_bucle_localizaciones_hijas($info_localizaciones_hijas);
            if ($existe_bucle == true)
            {
                $modificar_hija_localizacion = false;

                $res = "ERROR";
                $msg = $idiomas->_("Hay un bucle en las localizaciones hijas");
            }
        }

        // Comprobación de que las localizaciones de los grupos sus nodos son correctas (después de modificar la localización hija)
        if ($modificar_hija_localizacion == true)
        {
            $localizaciones_grupos_sensores_correctas = dame_localizaciones_grupos_localizacion_correctas(
                $info_localizaciones_padres,
                $info_localizaciones_hijas,
                TIPO_NODO_SENSOR,
                $id_localizacion_padre);
            if ($localizaciones_grupos_sensores_correctas == false)
            {
                $modificar_hija_localizacion = false;

                $res = "ERROR";
                $msg = $idiomas->_("Las localizaciones de los grupos de sensores y los sensores asignados a los mismos son incorrectas");
            }
        }
        if ($modificar_hija_localizacion == true)
        {
            $localizaciones_grupos_actuadores_correctas = dame_localizaciones_grupos_localizacion_correctas(
                $info_localizaciones_padres,
                $info_localizaciones_hijas,
                TIPO_NODO_ACTUADOR,
                $id_localizacion_padre);
            if ($localizaciones_grupos_actuadores_correctas == false)
            {
                $modificar_hija_localizacion = false;

                $res = "ERROR";
                $msg = $idiomas->_("Las localizaciones de los grupos de actuadores y los actuadores asignados a los mismos son incorrectas");
            }
        }

        // Se modifica la hija de la localización
        if ($modificar_hija_localizacion == true)
        {
            // Se recupera la fila anterior (antes de la modificación)
            $fila_hija_localizacion_anterior = dame_fila_hija_localizacion($id_hija_localizacion);

            // Se modifica la hija de la localización
            $operacion_modificacion = "
                UPDATE hijas_localizaciones
                SET
                    localizacion_hija = '".$bd_red->_($id_localizacion_hija)."'
                WHERE
                    id = '".$bd_red->_($id_hija_localizacion)."'";
            $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
            if ($res_modificacion == true)
            {
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

                // Se recupera la fila actual
                $fila_hija_localizacion_actual = dame_fila_hija_localizacion($id_hija_localizacion);

                // Se añade la acción de usuario
                anyade_accion_usuario_modificar_hija_localizacion(
                    $fila_hija_localizacion_actual,
                    $fila_hija_localizacion_anterior);

                $res = "OK";
                $msg = $idiomas->_("Localización hija modificada correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_modificacion."'");
            }
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


    // Añade la acción de usuario de modificación de la hija de localización
    function anyade_accion_usuario_modificar_hija_localizacion($fila_actual, $fila_anterior)
    {
        // Identificadores de las localizaciones padre e hijas
        $id_localizacion_padre = $fila_actual["localizacion_padre"];
        $id_localizacion_hija = $fila_actual["localizacion_hija"];
        $id_localizacion_hija_anterior = $fila_anterior["localizacion_hija"];

        // Se recuperan los nombres de las localizaciones padre e hijas
        $fila_localizacion_padre = dame_fila_localizacion($id_localizacion_padre);
        $fila_localizacion_hija = dame_fila_localizacion($id_localizacion_hija);
        $fila_localizacion_hija_anterior = dame_fila_localizacion($id_localizacion_hija_anterior);
        $nombre_localizacion_padre = $fila_localizacion_padre["nombre"];
        $nombre_localizacion_hija = $fila_localizacion_hija["nombre"];
        $nombre_localizacion_hija_anterior = $fila_localizacion_hija_anterior["nombre"];

        // Tipo de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_MODIFICAR_HIJA_LOCALIZACION;

        // Parámetros de la acción (sólo se muestran los modificados: actuales y anteriores)
        $parametros_accion_usuario = array();
        $parametros_accion_usuario_anteriores = array();
        if ($nombre_localizacion_hija != $nombre_localizacion_hija_anterior)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_LOCALIZACION_HIJA] = $nombre_localizacion_hija;
            $parametros_accion_usuario_anteriores[PARAMETRO_ACCION_USUARIO_NOMBRE_LOCALIZACION_HIJA] = $nombre_localizacion_hija_anterior;
        }

        // Si no hay parámetros de la acción es que no se ha modificado nada, no se añade la acción
        if (count($parametros_accion_usuario) == 0)
        {
            return;
        }

        // Objeto de la acción (se tiene en cuenta si se ha cambiado el nombre)
        if ($nombre_localizacion_hija == $nombre_localizacion_hija_anterior)
        {
            $objeto_accion_usuario = $nombre_localizacion_hija." (".$nombre_localizacion_padre.")";
        }
        else
        {
            $objeto_accion_usuario = implode(SEPARADOR_PARAMETROS_SIMPLES, array(
                $nombre_localizacion_hija." (".$nombre_localizacion_padre.")",
                $nombre_localizacion_hija_anterior));
        }

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            $parametros_accion_usuario_anteriores,
            NULL);
    }
?>
