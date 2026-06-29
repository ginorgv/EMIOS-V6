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


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_HIJA_LOCALIZACION, $_POST);

	$idiomas = new Idiomas();
    $bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros
    $id_localizacion_padre = $_POST['id_localizacion_padre'];
    $id_localizacion_hija = $_POST['id_localizacion_hija'];

    // Se comprueba si existe una hija de localización con las mismas localizaciones padre e hija
    $consulta_existe = "
        SELECT *
        FROM hijas_localizaciones
        WHERE
            (localizacion_padre = '".$bd_red->_($id_localizacion_padre)."')
            AND (localizacion_hija = '".$bd_red->_($id_localizacion_hija)."')";
    $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
    if ($res_existe == false)
    {
        throw new Exception("Error en la consulta: '".$consulta_existe."'");
    }
    if ($res_existe->dame_numero_filas() > 0)
    {
        $res = "ERROR";
        $msg = $idiomas->_("La localización hija ya está añadida");
    }
    else
    {
        // Comprobaciones antes de añadir la hija de localización:
        // - Comprobación de bucle en las localizaciones hijas
        $anyadir_hija_localizacion = true;

        // Comprobación de bucle en las localizaciones hijas
        if ($anyadir_hija_localizacion == true)
        {
            $info_localizaciones_padres = NULL;
            $info_localizaciones_hijas = NULL;
            carga_informacion_localizaciones_padres_hijas($info_localizaciones_padres, $info_localizaciones_hijas);
            anyade_localizacion_padre($info_localizaciones_padres, $id_localizacion_padre, $id_localizacion_hija);
            anyade_localizacion_hija($info_localizaciones_hijas, $id_localizacion_padre, $id_localizacion_hija);

            $existe_bucle = existe_bucle_localizaciones_hijas($info_localizaciones_hijas);
            if ($existe_bucle == true)
            {
                $anyadir_hija_localizacion = false;

                $res = "ERROR";
                $msg = $idiomas->_("Hay un bucle en las localizaciones hijas");
            }
        }

        // Se añade la hija de la localización
        if ($anyadir_hija_localizacion == true)
        {
            // Se añade la hija de la localización
            $operacion_insercion = "
                INSERT INTO hijas_localizaciones (
                    red,
                    localizacion_padre,
                    localizacion_hija
                ) VALUES (
                    '".$_SESSION["id_red"]."',
                    '".$bd_red->_($id_localizacion_padre)."',
                    '".$bd_red->_($id_localizacion_hija)."'
                )";
            $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
            if ($res_insercion == true)
            {
                // Se recuperan el id y la fila de la hija de localización añadida
                $id_hija_localizacion = $bd_red->dame_id_autoincremental_ultima_insercion();
                $fila_hija_localizacion = dame_fila_hija_localizacion($id_hija_localizacion);

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

                // Se añade la acción de usuario
                anyade_accion_usuario_anyadir_hija_localizacion($fila_hija_localizacion);

                $res = "OK";
                $msg = $idiomas->_("Localización hija añadida correctamente");
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_insercion."'");
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


    // Añade la acción de usuario de adición de la hija de localización
    function anyade_accion_usuario_anyadir_hija_localizacion($fila)
    {
        // Nombres de las localizaciones padre e hija
        $fila_localizacion_padre = dame_fila_localizacion($fila["localizacion_padre"]);
        $fila_localizacion_hija = dame_fila_localizacion($fila["localizacion_hija"]);
        $nombre_localizacion_padre = $fila_localizacion_padre["nombre"];
        $nombre_localizacion_hija = $fila_localizacion_hija["nombre"];

        // Tipo y objeto de la acción
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_ANYADIR_HIJA_LOCALIZACION;
        $objeto_accion_usuario = $nombre_localizacion_hija." (".$nombre_localizacion_padre.")";

        // Parámetros de la acción
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_LOCALIZACION_PADRE] = $nombre_localizacion_padre;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_LOCALIZACION_HIJA] = $nombre_localizacion_hija;

        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }
?>
