<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_criptografia.php');
	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/util_administracion_usuarios.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MODIFICAR_USUARIO, $_POST);

	$idiomas = new Idiomas();
	$bd_red = BaseDatosRed::dame_base_datos();

    // Parámetros codificados
    $cadena_datos_principal_codificada = $_POST["datos_principal"];
    $cadena_datos_principal = decodifica_cadena_peticion_php($cadena_datos_principal_codificada);
    $datos_principal = json_decode($cadena_datos_principal, true);
    $id_usuario = $datos_principal["id_usuario"];
    $contrasenya = $datos_principal['contrasenya'];
    $nombre = $datos_principal['nombre'];
    $perfil = $datos_principal['perfil'];

    // Log
    $log = dame_log();
    $log->info("[".$_SESSION["id_usuario"]."] "."Parámetros codificados: '".$cadena_datos_principal."'");

    // Parámetros
    $licencias = $_POST['licencias'];
    $secciones = $_POST['secciones'];
    $ids_redes = $_POST['ids_redes'];
    $parametros_modulo_personal = $_POST['parametros_modulo_personal'];
    $parametros_modulo_localizaciones = $_POST['parametros_modulo_localizaciones'];
    $parametros_modulo_sensores = $_POST['parametros_modulo_sensores'];
    $parametros_modulo_actuadores = $_POST['parametros_modulo_actuadores'];
    $idioma = $_POST['idioma'];
    $tamanyo_letra = $_POST['tamanyo_letra'];
    $pantalla_completa_inicio = $_POST['pantalla_completa_inicio'];
    $cadena_preferencias_modulos = $_POST['preferencias_modulos'];
    $api_http = $_POST['api_http'];

    // Parámetros del usuario anterior
    $id_usuario_anterior = $_POST['id_usuario_anterior'];

    // Si los parámetros de algún módulo están vacíos (no está el módulo, se recuperan los parámetros por defecto)
    if ($parametros_modulo_personal === NULL)
    {
        $parametros_modulo_personal = dame_parametros_defecto_modulo_personal();
    }
    if ($parametros_modulo_localizaciones === NULL)
    {
        $parametros_modulo_localizaciones = dame_parametros_defecto_modulo_localizaciones();
    }
    if ($parametros_modulo_sensores === NULL)
    {
        $parametros_modulo_sensores = dame_parametros_defecto_modulo_sensores();
    }
    if ($parametros_modulo_actuadores === NULL)
    {
        $parametros_modulo_actuadores = dame_parametros_defecto_modulo_actuadores();
    }

    // Red actual
    $id_red_actual = $_SESSION["id_red"];

    // Flag de usuario actual
    if (strtolower($id_usuario_anterior) == $_SESSION["id_usuario"])
    {
        $usuario_actual = VALOR_SI;
    }
    else
    {
        $usuario_actual = VALOR_NO;
    }

    // Comprobaciones antes de modificar el usuario
    // - Si se ha modificado el identificador, se comprueba si ya existe otro usuario con el mismo identificador
    // - Comprobación de usuario correcto
    // - Comprobación de contraseña correcta
    // - Comprobación de cambio de perfil no permitido
    $modificar_usuario = true;

    // Si se ha modificado el identificador, se comprueba si ya existe otro usuario con el mismo identificador
    if ($modificar_usuario == true)
    {
        if ($id_usuario != $id_usuario_anterior)
        {
            $consulta_existe = "
                SELECT id
                FROM usuarios
                WHERE
                    id = '".$bd_red->_($id_usuario)."'";
            $res_existe = $bd_red->ejecuta_consulta($consulta_existe);
            if ($res_existe == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_existe."'");
            }
            if ($res_existe->dame_numero_filas() > 0)
            {
                $modificar_usuario = false;

                $res = "ERROR";
                $msg = $idiomas->_("Ya existe un usuario con este identificador");
            }
        }
    }

    // Comprobación de usuario correcto
    if ($modificar_usuario == true)
    {
        if ($id_usuario != $id_usuario_anterior)
        {
            $id_usuario_valido = dame_id_usuario_valido($id_usuario);
            if ($id_usuario_valido == false)
            {
                $modificar_usuario = false;

                $res = "ERROR";
                $msg = $idiomas->_("El identificador de usuario debe tener al menos 6 caracteres");
            }
        }
    }

    // Comprobación de contraseña correcta
    if ($modificar_usuario == true)
    {
        if ($contrasenya != "")
        {
            $contrasenya_valida = dame_contrasenya_usuario_valida($contrasenya);
            if ($contrasenya_valida == false)
            {
                $modificar_usuario = false;

                $res = "ERROR";
                $msg = $idiomas->_("La contraseña debe tener al menos 8 caracteres y contener letras y números");
            }
        }
    }

    // Comprobación de cambio de perfil no permitido:
    // - Si ha cambiado el perfil es que se ha intentado "hackear", se muestra un aviso y se añade al log
    if ($modificar_usuario == true)
    {
        // Se recupera el perfil del usuario anterior
        $fila_usuario_anterior = dame_fila_usuario($id_usuario_anterior);
        $perfil_anterior = $fila_usuario_anterior["perfil"];

        // Se comprueba si se intenta un cambio de perfil
        if ($perfil != $perfil_anterior)
        {
            $log->warn("[".$_SESSION["id_usuario"]."] "."Se ha detectado un cambio de perfil de usuario no permitido");
            $modificar_usuario = false;

            $res = "ERROR";
            $msg = $idiomas->_("Se ha detectado un cambio de perfil de usuario no permitido");
        }
    }

    // Se modifica el usuario
    if ($modificar_usuario == true)
    {
        // Si ha cambiado el identificador de usuario
        if ($id_usuario != $id_usuario_anterior)
        {
            // Se elimina el directorio de ficheros temporales del usuario
            elimina_ficheros_temporales_usuario($id_usuario_anterior, true);

            // Si es el usuario actual, se modifica el identificador de usuario en la sesión
            // y se crea el directorio de ficheros temporales del usuario
            if ($usuario_actual == VALOR_SI)
            {
                $_SESSION["id_usuario"] = $id_usuario;
                crea_directorio_ficheros_temporales_usuario($_SESSION["id_usuario"]);
            }
        }

        // Si se ha modificado la contraseña se encriptan las contraseñas
        if ($contrasenya != "")
        {
            $contrasenya_encriptada = crypt($contrasenya);
            $contrasenya_api_http_md5 = md5($contrasenya);
            $modificar_contrasenyas = true;
        }
        else
        {
            $modificar_contrasenyas = false;
        }

        // Se modifica el usuario
        $operacion_modificacion = "
			UPDATE usuarios
			SET
                id = '".$bd_red->_($id_usuario)."',
				nombre = '".$bd_red->_($nombre)."',
                idioma = '".$bd_red->_($idioma)."',
                tamanyo_letra = '".$bd_red->_($tamanyo_letra)."',
                pantalla_completa_inicio = '".$bd_red->_($pantalla_completa_inicio)."',
                preferencias_modulos = '".$bd_red->_($cadena_preferencias_modulos)."',
                api_http = '".$bd_red->_($api_http)."'";
        if ($modificar_contrasenyas == true)
        {
            $operacion_modificacion .= ",
                contrasenya = '".$contrasenya_encriptada."',
                contrasenya_api_http = '".$contrasenya_api_http_md5."'";
        }
        $operacion_modificacion .= "
			WHERE
				id = '".$bd_red->_($id_usuario_anterior)."'";
        $res_modificacion = $bd_red->ejecuta_operacion($operacion_modificacion);
        if ($res_modificacion == true)
        {
            // Actualización de las redes asignadas:
            // - Si el usuario a modificar es administrador
            // - Si el usuario actual no es estándar y está modificando un usuario estándar sin red actual seleccionada (con varias redes)
            if (($perfil == PERFIL_USUARIO_ADMINISTRADOR) ||
                (($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) && ($perfil == PERFIL_USUARIO_ESTANDAR) && ($id_red_actual == ID_NINGUNO)))
            {
                // Se recuperan las redes 'anteriores' asociadas a este usuario
                $ids_redes_anteriores = dame_ids_redes_usuario($id_usuario_anterior, $perfil);

                // Si el perfil del usuario es estándar y el usuario actual es administrador,
                // se ignorarán las redes anteriores que no están asignadas al usuario actual
                // (no se pueden eliminar redes de un usuario del cual el administrador que lo modifica no tiene asignadas)
                $comprobar_redes_anteriores_usuario_actual = false;
                if (($perfil == PERFIL_USUARIO_ESTANDAR) && ($_SESSION["perfil"] == PERFIL_USUARIO_ADMINISTRADOR))
                {
                    $comprobar_redes_anteriores_asignadas_usuario_actual = true;
                    $ids_redes_asignadas_usuario_actual = dame_ids_redes_usuario($_SESSION["id_usuario"], $_SESSION["perfil"]);
                }

                // Se recorren las redes 'anteriores'
                foreach ($ids_redes_anteriores as $id_red_anterior)
                {
                    // Si la red anterior no está asignada al usuario actual, se ignora
                    if (($comprobar_redes_anteriores_asignadas_usuario_actual == true) && (in_array($id_red_anterior, $ids_redes_asignadas_usuario_actual) == false))
                    {
                        continue;
                    }

                    // Se eliminan los elementos de la red anterior eliminada
                    if (in_array($id_red_anterior, $ids_redes) == False)
                    {
                        elimina_elementos_red_usuario($id_red_anterior, $id_usuario_anterior, $perfil);
                    }
                }

                // Se recorren las redes 'nuevas'
                foreach ($ids_redes as $id_red)
                {
                    // Se añade la red al usuario
                    if (in_array($id_red, $ids_redes_anteriores) == False)
                    {
                        anyade_red_usuario($id_red, $id_usuario, $perfil);
                    }
                }
            }

            // Si el usuario que modifica y el usuario modificado no son estándar y hay red seleccionada,
            // se actualizan las licencias y los parámetros de los módulos
            if (($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) &&
                ($perfil == PERFIL_USUARIO_ESTANDAR) && ($id_red_actual != ID_NINGUNO))
            {
                // Se actualizan las licencias y secciones del usuario en la red actual
                actualiza_licencias_secciones_red_usuario($id_red_actual, $id_usuario_anterior, $id_usuario, $licencias, $secciones);

                // Se añaden las cadenas de identificadores de sensores y actuadores
                $cadena_ids_sensores = dame_cadena_ids_parametros_modulo_ids($parametros_modulo_sensores["ids_sensores"]);
                $cadena_ids_grupos_sensores = dame_cadena_ids_parametros_modulo_ids($parametros_modulo_sensores["ids_grupos_sensores"]);
                $parametros_modulo_sensores["cadena_ids_sensores"] = $cadena_ids_sensores;
                $parametros_modulo_sensores["cadena_ids_grupos_sensores"] = $cadena_ids_grupos_sensores;
                $cadena_ids_actuadores = dame_cadena_ids_parametros_modulo_ids($parametros_modulo_actuadores["ids_actuadores"]);
                $cadena_ids_grupos_actuadores = dame_cadena_ids_parametros_modulo_ids($parametros_modulo_actuadores["ids_grupos_actuadores"]);
                $parametros_modulo_actuadores["cadena_ids_actuadores"] = $cadena_ids_actuadores;
                $parametros_modulo_actuadores["cadena_ids_grupos_actuadores"] = $cadena_ids_grupos_actuadores;

                // Se modifican los parámetros de los módulos
                modifica_parametros_modulo_personal_usuario($id_usuario_anterior, $id_red_actual, $parametros_modulo_personal);
                modifica_parametros_modulo_localizaciones_usuario($id_usuario_anterior, $id_red_actual, $parametros_modulo_localizaciones);
                modifica_parametros_modulo_sensores_usuario($id_usuario_anterior, $id_red_actual, $parametros_modulo_sensores);
                modifica_parametros_modulo_actuadores_usuario($id_usuario_anterior, $id_red_actual, $parametros_modulo_actuadores);
            }

            // Si ha cambiado el id, se modifica en las tablas correspondientes
            if ($id_usuario_anterior != $id_usuario)
            {
                modifica_id_usuario($id_usuario_anterior, $id_usuario);
            }

            // Si el usuario que modifica y el usuario modificado no son estándar y hay red seleccionada,
            // se eliminan los elementos no visibles (después de actualizar el id de usuario en las tablas correspondientes)
            if (($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) &&
                ($perfil == PERFIL_USUARIO_ESTANDAR) && ($id_red_actual != ID_NINGUNO))
            {
                // Eliminación de elementos no visibles de los parámetros de módulos del usuario (dependientes de su nueva configuración)
                $parametros_usuario = array(
                    "parametros_modulo_localizaciones" => $parametros_modulo_localizaciones,
                    "parametros_modulo_sensores" => $parametros_modulo_sensores,
                    "parametros_modulo_actuadores" => $parametros_modulo_actuadores
                );
                elimina_modifica_elementos_no_visibles_parametros_modulos_usuario($id_usuario, $perfil, $id_red_actual, $parametros_usuario);
            }

            $res = "OK";
            $msg = $idiomas->_("Usuario modificado correctamente");
        }
        else
        {
            throw new Exception("Error en la operación: '".$operacion_modificacion."'");
        }
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "usuario_actual" => $usuario_actual))
    );


    //
    // Funciones auxiliares
    //


    // Elimina los elementos de una red del usuario especificado en la base de datos
    function elimina_elementos_red_usuario($id_red, $id_usuario, $perfil)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $operacion_borrado_red = "
            DELETE
            FROM redes_usuarios
            WHERE
                (red = '".$bd_red->_($id_red)."')
                AND (usuario = '".$bd_red->_($id_usuario)."')";
        $res_borrado_red = $bd_red->ejecuta_operacion($operacion_borrado_red);
        if ($res_borrado_red == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_red."'");
        }

        $operacion_borrado_parametros_modulos_red = "
            DELETE
            FROM modulos_usuarios
            WHERE
                (red = '".$bd_red->_($id_red)."')
                AND (usuario = '".$bd_red->_($id_usuario)."')";
        $res_borrado_parametros_modulos_red = $bd_red->ejecuta_operacion($operacion_borrado_parametros_modulos_red);
        if ($res_borrado_parametros_modulos_red == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_parametros_modulos_red."'");
        }

        $operacion_borrado_pestanyas_widgets_red = "
            DELETE
            FROM pestanyas_widgets
            WHERE
                (red = ".$bd_red->_($id_red).")
                AND (usuario = '".$bd_red->_($id_usuario)."')";
        $res_borrado_pestanyas_widgets_red = $bd_red->ejecuta_operacion($operacion_borrado_pestanyas_widgets_red);
        if ($res_borrado_pestanyas_widgets_red == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_pestanyas_widgets_red."'");
        }

        $operacion_borrado_widgets_red = "
            DELETE
            FROM widgets
            WHERE
                (red = ".$bd_red->_($id_red).")
                AND (usuario = '".$bd_red->_($id_usuario)."')";
        $res_borrado_widgets_red = $bd_red->ejecuta_operacion($operacion_borrado_widgets_red);
        if ($res_borrado_widgets_red == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_widgets_red."'");
        }

        // - Si el usuario es estandar: Se eliminan las plantillas de informes de este usuario (sólo si es estandar, que se hace si es administrador?)
        // - Si el usuario es administrador: Se establece el usuario a ninguno
        switch ($perfil)
        {
            case PERFIL_USUARIO_ESTANDAR:
            {
                $operacion_borrado_parametros_plantillas_informes = "
                    DELETE parametros_plantillas_informes
                    FROM
                        parametros_plantillas_informes,
                        plantillas_informes
                    WHERE
                        (parametros_plantillas_informes.plantilla_informe = plantillas_informes.id)
                        AND (plantillas_informes.red = '".$bd_red->_($id_red)."')
                        AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')";
                $res_borrado_parametros_plantillas_informes = $bd_red->ejecuta_operacion($operacion_borrado_parametros_plantillas_informes);
                if ($res_borrado_parametros_plantillas_informes == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_borrado_parametros_plantillas_informes."'");
                }

                $operacion_borrado_elementos_plantillas_informes = "
                    DELETE elementos_plantillas_informes
                    FROM
                        elementos_plantillas_informes,
                        plantillas_informes
                    WHERE
                        (elementos_plantillas_informes.plantilla_informe = plantillas_informes.id)
                        AND (plantillas_informes.red = '".$bd_red->_($id_red)."')
                        AND (plantillas_informes.usuario = '".$bd_red->_($id_usuario)."')";
                $res_borrado_elementos_plantillas_informes = $bd_red->ejecuta_operacion($operacion_borrado_elementos_plantillas_informes);
                if ($res_borrado_elementos_plantillas_informes == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_borrado_elementos_plantillas_informes."'");
                }

                $operacion_borrado_plantillas_informes = "
                    DELETE
                    FROM plantillas_informes
                    WHERE
                        (red = '".$bd_red->_($id_red)."')
                        AND (usuario = '".$bd_red->_($id_usuario)."')";
                $res_borrado_plantillas_informes = $bd_red->ejecuta_operacion($operacion_borrado_plantillas_informes);
                if ($res_borrado_plantillas_informes == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_borrado_plantillas_informes."'");
                }
                break;
            }
            default:
            {
                $operacion_modificacion_plantillas_informes = "
                    UPDATE plantillas_informes
                    SET
                        usuario = ''
                    WHERE
                        (red = '".$bd_red->_($id_red)."')
                        AND (usuario = '".$bd_red->_($id_usuario)."')";
                $res_modificacion_plantillas_informes = $bd_red->ejecuta_operacion($operacion_modificacion_plantillas_informes);
                if ($res_modificacion_plantillas_informes == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_modificacion_plantillas_informes."'");
                }
                break;
            }
        }

        $operacion_borrado_informes_automaticos_red = "
            DELETE
            FROM informes_automaticos
            WHERE
                (red = ".$bd_red->_($id_red).")
                AND (usuario = '".$bd_red->_($id_usuario)."')";
        $res_borrado_informes_automaticos_red = $bd_red->ejecuta_operacion($operacion_borrado_informes_automaticos_red);
        if ($res_borrado_informes_automaticos_red == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_informes_automaticos_red."'");
        }

        // Si el usuario es estándar se eliminan las licencias, y secciones de la red (ya se han borrado los parámetros de los módulos)
        if ($perfil == PERFIL_USUARIO_ESTANDAR)
        {
            $operacion_borrado_licencias_red = "
                DELETE licencias_usuarios.*
                FROM
                    licencias_usuarios,
                    licencias
                WHERE
                    (licencias_usuarios.usuario = '".$bd_red->_($id_usuario)."')
                    AND (licencias.id = licencias_usuarios.licencia)
                    AND (licencias.red = '".$bd_red->_($id_red)."')";
            $res_borrado_licencias_red = $bd_red->ejecuta_operacion($operacion_borrado_licencias_red);
            if ($res_borrado_licencias_red == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_licencias_red."'");
            }

            $operacion_borrado_secciones_red = "
                DELETE
                FROM secciones_usuarios
                WHERE
                    (red = '".$bd_red->_($id_red)."')
                    AND (usuario = '".$bd_red->_($id_usuario)."')";
            $res_borrado_secciones_red = $bd_red->ejecuta_operacion($operacion_borrado_secciones_red);
            if ($res_borrado_secciones_red == false)
            {
                throw new Exception("Error en la operación: '".$operacion_borrado_parametros_modulos_red."'");
            }
        }
    }


    // Actualiza las licencias y secciones del usuario en la red especificada
    function actualiza_licencias_secciones_red_usuario($id_red, $id_usuario_anterior, $id_usuario, $licencias, $secciones)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se actualizan las licencias del usuario
        $operacion_borrado_licencias = "
            DELETE licencias_usuarios.*
            FROM
                licencias_usuarios,
                licencias
            WHERE
                (licencias_usuarios.usuario = '".$bd_red->_($id_usuario_anterior)."')
                AND (licencias.id = licencias_usuarios.licencia)
                AND (licencias.red = '".$bd_red->_($id_red)."')";
        $res_borrado_licencias = $bd_red->ejecuta_operacion($operacion_borrado_licencias);
        if ($res_borrado_licencias == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_licencias."'");
        }
        foreach ($licencias as $licencia)
        {
            $operacion_insercion_licencia = "
                INSERT INTO licencias_usuarios (
                    usuario,
                    licencia,
                    red
                ) VALUES (
                    '".$bd_red->_($id_usuario)."',
                    '".$bd_red->_($licencia)."',
                    '".$bd_red->_($id_red)."'
                )";
            $res_insercion_licencia = $bd_red->ejecuta_operacion($operacion_insercion_licencia);
            if ($res_insercion_licencia == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_licencia."'");
            }
        }

        // Se actualizan las secciones del usuario
        $operacion_borrado_secciones = "
            DELETE
            FROM secciones_usuarios
            WHERE
                (usuario = '".$bd_red->_($id_usuario_anterior)."')
                AND (red = '".$bd_red->_($id_red)."')";
        $res_borrado_secciones = $bd_red->ejecuta_operacion($operacion_borrado_secciones);
        if ($res_borrado_secciones == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_secciones."'");
        }
        foreach ($secciones as $modulo => $secciones_modulo)
        {
            if (count($secciones_modulo) > 0)
            {
                $cadena_secciones_modulo = implode(SEPARADOR_PARAMETROS_SIMPLES, $secciones_modulo);
                $operacion_insercion_seccion = "
                    INSERT INTO secciones_usuarios (
                        usuario,
                        modulo,
                        red,
                        secciones
                    ) VALUES (
                        '".$bd_red->_($id_usuario)."',
                        '".$bd_red->_($modulo)."',
                        '".$bd_red->_($id_red)."',
                        '".$bd_red->_($cadena_secciones_modulo)."'
                    )";
                $res_insercion_seccion = $bd_red->ejecuta_operacion($operacion_insercion_seccion);
                if ($res_insercion_seccion == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_insercion_seccion."'");
                }
            }
        }
    }


    // Modifica el identificador del usuario en la base de datos
    function modifica_id_usuario($id_usuario_anterior, $id_usuario)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $operacion_actualizacion_redes = "
            UPDATE redes_usuarios
            SET
                usuario = '".$bd_red->_($id_usuario)."'
            WHERE
                usuario = '".$bd_red->_($id_usuario_anterior)."'";
        $res_actualizacion_redes = $bd_red->ejecuta_operacion($operacion_actualizacion_redes);
        if ($res_actualizacion_redes == false)
        {
            throw new Exception("Error en la operación: '".$operacion_actualizacion_redes."'");
        }

        $operacion_actualizacion_licencias = "
            UPDATE licencias_usuarios
            SET
                usuario = '".$bd_red->_($id_usuario)."'
            WHERE
                usuario = '".$bd_red->_($id_usuario_anterior)."'";
        $res_actualizacion_licencias = $bd_red->ejecuta_operacion($operacion_actualizacion_licencias);
        if ($res_actualizacion_licencias == false)
        {
            throw new Exception("Error en la operación: '".$operacion_actualizacion_licencias."'");
        }

        $operacion_actualizacion_parametros_modulos = "
            UPDATE modulos_usuarios
            SET
                usuario = '".$bd_red->_($id_usuario)."'
            WHERE
                usuario = '".$bd_red->_($id_usuario_anterior)."'";
        $res_actualizacion_parametros_modulos = $bd_red->ejecuta_operacion($operacion_actualizacion_parametros_modulos);
        if ($res_actualizacion_parametros_modulos == false)
        {
            throw new Exception("Error en la operación: '".$operacion_actualizacion_parametros_modulos."'");
        }

        $operacion_actualizacion_pestanyas_widgets = "
            UPDATE pestanyas_widgets
            SET
                usuario = '".$bd_red->_($id_usuario)."'
            WHERE
                usuario = '".$bd_red->_($id_usuario_anterior)."'";
        $res_actualizacion_pestanyas_widgets = $bd_red->ejecuta_operacion($operacion_actualizacion_pestanyas_widgets);
        if ($res_actualizacion_pestanyas_widgets == false)
        {
            throw new Exception("Error en la operación: '".$operacion_actualizacion_pestanyas_widgets."'");
        }

        $operacion_actualizacion_plantillas_informes = "
            UPDATE plantillas_informes
            SET
                usuario = '".$bd_red->_($id_usuario)."'
            WHERE
                usuario = '".$bd_red->_($id_usuario_anterior)."'";
        $res_actualizacion_plantillas_informes = $bd_red->ejecuta_operacion($operacion_actualizacion_plantillas_informes);
        if ($res_actualizacion_plantillas_informes == false)
        {
            throw new Exception("Error en la operación: '".$operacion_actualizacion_plantillas_informes."'");
        }

        $operacion_actualizacion_informes_automaticos = "
            UPDATE informes_automaticos
            SET
                usuario = '".$bd_red->_($id_usuario)."'
            WHERE
                usuario = '".$bd_red->_($id_usuario_anterior)."'";
        $res_actualizacion_informes_automaticos = $bd_red->ejecuta_operacion($operacion_actualizacion_informes_automaticos);
        if ($res_actualizacion_informes_automaticos == false)
        {
            throw new Exception("Error en la operación: '".$operacion_actualizacion_informes_automaticos."'");
        }

        $operacion_actualizacion_widgets = "
            UPDATE widgets
            SET
                usuario = '".$bd_red->_($id_usuario)."'
            WHERE
                usuario = '".$bd_red->_($id_usuario_anterior)."'";
        $res_actualizacion_widgets = $bd_red->ejecuta_operacion($operacion_actualizacion_widgets);
        if ($res_actualizacion_widgets == false)
        {
            throw new Exception("Error en la operación: '".$operacion_actualizacion_widgets."'");
        }

        $operacion_actualizacion_peticiones_api_http = "
            UPDATE peticiones_api_http
            SET
                usuario = '".$bd_red->_($id_usuario)."'
            WHERE
                usuario = '".$bd_red->_($id_usuario_anterior)."'";
        $res_actualizacion_peticiones_api_http = $bd_red->ejecuta_operacion($operacion_actualizacion_peticiones_api_http);
        if ($res_actualizacion_peticiones_api_http == false)
        {
            throw new Exception("Error en la operación: '".$operacion_actualizacion_peticiones_api_http."'");
        }
    }


    // Añade la red al usuario en la base de datos
    function anyade_red_usuario($id_red, $id_usuario, $perfil)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $operacion_insercion_red = "
            INSERT INTO redes_usuarios (
                red,
                usuario
            ) VALUES (
                '".$bd_red->_($id_red)."',
                '".$bd_red->_($id_usuario)."'
            )";
        $res_insercion_red = $bd_red->ejecuta_operacion($operacion_insercion_red);
        if ($res_insercion_red == false)
        {
            throw new Exception("Error en la operación: '".$operacion_insercion_red."'");
        }

        // Se añaden los parámetros por defecto de los módulos correspondientes
        switch ($perfil)
        {
            case PERFIL_USUARIO_ESTANDAR:
            {
                anyade_parametros_defecto_modulo_personal_usuario($id_usuario, $id_red);
                anyade_parametros_defecto_modulo_localizaciones_usuario($id_usuario, $id_red);
                anyade_parametros_defecto_modulo_sensores_usuario($id_usuario, $id_red);
                anyade_parametros_defecto_modulo_actuadores_usuario($id_usuario, $id_red);
                break;
            }
        }
    }
?>
