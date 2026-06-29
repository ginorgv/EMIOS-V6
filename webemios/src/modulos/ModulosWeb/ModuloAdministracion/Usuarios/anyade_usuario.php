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
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/PlantillasInformes/util_plantillas_informes.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_ANYADIR_USUARIO, $_POST);

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

    // Parámetros del usuario anterior (si es un duplicado)
    $id_usuario_anterior = $_POST['id_usuario_anterior'];
    $cadena_redes_anteriores = $_POST["cadena_redes_anteriores"];
    $ids_redes_anteriores = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_redes_anteriores);
    $cadena_parametros_modulo_personal_anteriores = $_POST['cadena_parametros_modulo_personal_anteriores'];
    $cadena_parametros_modulo_localizaciones_anteriores = $_POST['cadena_parametros_modulo_localizaciones_anteriores'];
    $cadena_parametros_modulo_sensores_anteriores = $_POST['cadena_parametros_modulo_sensores_anteriores'];
    $cadena_parametros_modulo_actuadores_anteriores = $_POST['cadena_parametros_modulo_actuadores_anteriores'];

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

    // Comprobaciones antes de añadir el usuario
    // - Se comprueba si ya existe otro usuario con el mismo identificador
    // - Comprobación de usuario correcto
    // - Comprobación de contraseña correcta
    // - Comprobación de perfil permitido
    $anyadir_usuario = true;

	// Se comprueba si existe un usuario con el mismo identificador
    if ($anyadir_usuario == true)
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
            $anyadir_usuario = false;

            $res = "ERROR";
            $msg = $idiomas->_("Ya existe un usuario con este identificador");
        }
    }

    // Comprobación de usuario correcto
    if ($anyadir_usuario == true)
    {
        $id_usuario_valido = dame_id_usuario_valido($id_usuario);
        if ($id_usuario_valido == false)
        {
            $anyadir_usuario = false;

            $res = "ERROR";
            $msg = $idiomas->_("El identificador de usuario debe tener al menos 6 caracteres");
        }
    }

    // Comprobación de contraseña correcta
    if ($anyadir_usuario == true)
    {
        $contrasenya_valida = dame_contrasenya_usuario_valida($contrasenya);
        if ($contrasenya_valida == false)
        {
            $anyadir_usuario = false;

            $res = "ERROR";
            $msg = $idiomas->_("La contraseña debe tener al menos 8 caracteres y contener letras y números");
        }
    }

    // Comprobación de perfil permitido:
    // - Si el perfil no es permitido es que se ha intentado "hackear", se muestra un aviso y se añade al log
    if ($anyadir_usuario == true)
    {
        $perfil_usuario_permitido = true;
        switch ($_SESSION["perfil"])
        {
            case PERFIL_USUARIO_ESTANDAR:
            {
                $perfil_usuario_permitido = false;
                break;
            }
            case PERFIL_USUARIO_ADMINISTRADOR:
            {
                switch ($perfil)
                {
                    case PERFIL_USUARIO_ADMINISTRADOR:
                    case PERFIL_USUARIO_SUPERADMINISTRADOR:
                    {
                        $perfil_usuario_permitido = false;
                        break;
                    }
                }
                break;
            }
        }
        if ($perfil_usuario_permitido == false)
        {
            $log->warn("[".$_SESSION["id_usuario"]."] "."Se ha detectado una adición de perfil de usuario no permitido");

            $anyadir_usuario = false;

            $res = "ERROR";
            $msg = $idiomas->_("Se ha detectado una adición de perfil de usuario no permitido");
        }
    }

    // Se añade el usuario
    if ($anyadir_usuario == true)
    {
        // Se encriptan las contraseñas
        $contrasenya_encriptada = crypt($contrasenya);
        $contrasenya_api_http_md5 = md5($contrasenya);

        // Se añade el usuario
        $operacion_insercion = "
            INSERT INTO usuarios (
                id,
                contrasenya,
                nombre,
                perfil,
                idioma,
                tamanyo_letra,
                pantalla_completa_inicio,
                preferencias_modulos,
                api_http,
                contrasenya_api_http
            ) VALUES (
                '".$bd_red->_($id_usuario)."',
                '".$contrasenya_encriptada."',
                '".$bd_red->_($nombre)."',
                '".$bd_red->_($perfil)."',
                '".$bd_red->_($idioma)."',
                '".$bd_red->_($tamanyo_letra)."',
                '".$bd_red->_($pantalla_completa_inicio)."',
                '".$bd_red->_($cadena_preferencias_modulos)."',
                '".$bd_red->_($api_http)."',
                '".$contrasenya_api_http_md5."'
            )";
        $res_insercion = $bd_red->ejecuta_operacion($operacion_insercion);
        if ($res_insercion == true)
        {
            // Se recuperan las redes del usuario (según su perfil)
            switch ($perfil)
            {
                case PERFIL_USUARIO_ESTANDAR:
                case PERFIL_USUARIO_ADMINISTRADOR:
                {
                    // Si hay red seleccionada se añade la red actual,
                    // si no las redes seleccionadas en la ventana de añadir usuario
                    if ($id_red_actual != ID_NINGUNO)
                    {
                        $ids_redes = array($id_red_actual);
                    }
                    break;
                }
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    // Se recuperan todas las redes
                    $ids_redes = array();
                    $consulta_redes = "
                        SELECT
                            id
                        FROM redes";
                    $res_redes = $bd_red->ejecuta_consulta($consulta_redes);
                    if ($res_redes == false)
                    {
                        throw new Exception("Error en la consulta: '".$consulta_redes."'");
                    }
                    while ($fila_red = $res_redes->dame_siguiente_fila())
                    {
                        array_push($ids_redes, $fila_red["id"]);
                    }
                    break;
                }
            }

            // Se recorren las redes
            foreach ($ids_redes as $id_red)
            {
                // Se añaden las redes de cada usuario (sólo si no es superadministrador)
                if ($perfil != PERFIL_USUARIO_SUPERADMINISTRADOR)
                {
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
                }

                // Adición de parámetros por defecto de los módulos correspondientes en los siguientes casos:
                // - El usuario es estándar y no hay red seleccionada y:
                //   - No es un duplicado o es un duplicado y la red no estaba en las redes del usuario anterior (origen de la copia)
                //     (si es un duplicado y la red estaba en las redes del usuario anterior, se copiarán después del usuario anterior)
                switch ($perfil)
                {
                    case PERFIL_USUARIO_ESTANDAR:
                    {
                        if (($id_red_actual == ID_NINGUNO) &&
                            (($id_usuario_anterior == "") || (in_array($id_red, $ids_redes_anteriores) == false)))
                        {
                            anyade_parametros_defecto_modulo_personal_usuario($id_usuario, $id_red);
                            anyade_parametros_defecto_modulo_localizaciones_usuario($id_usuario, $id_red);
                            anyade_parametros_defecto_modulo_sensores_usuario($id_usuario, $id_red);
                            anyade_parametros_defecto_modulo_actuadores_usuario($id_usuario, $id_red);
                        }
                        break;
                    }
                }
            }

            // Si el usuario es estándar y hay red seleccionada
            if (($perfil == PERFIL_USUARIO_ESTANDAR) AND ($id_red_actual != ID_NINGUNO))
            {
                // Se añaden las licencias del usuario
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
                            '".$bd_red->_($id_red_actual)."'
                        )";
                    $res_insercion_licencia = $bd_red->ejecuta_operacion($operacion_insercion_licencia);
                    if ($res_insercion_licencia == false)
                    {
                        throw new Exception("Error en la operación: '".$operacion_insercion_licencia."'");
                    }
                }

                // Se añaden las secciones del usuario
                foreach ($secciones as $modulo => $secciones_modulo)
                {
                    if (count($secciones_modulo) > 0)
                    {
                        $cadena_secciones_modulo = implode(SEPARADOR_PARAMETROS_SIMPLES, $secciones_modulo);
                        $operacion_insercion_secciones = "
                            INSERT INTO secciones_usuarios (
                                usuario,
                                modulo,
                                red,
                                secciones
                            ) VALUES (
                                '".$bd_red->_($id_usuario)."',
                                '".$bd_red->_($modulo)."',
                                '".$bd_red->_($id_red_actual)."',
                                '".$bd_red->_($cadena_secciones_modulo)."'
                            )";
                        $res_insercion_seccion = $bd_red->ejecuta_operacion($operacion_insercion_secciones);
                        if ($res_insercion_seccion == false)
                        {
                            throw new Exception("Error en la operación: '".$operacion_insercion_secciones."'");
                        }
                    }
                }

                // Se añaden las cadenas de identificadores de sensores y actuadores
                $cadena_ids_sensores = dame_cadena_ids_parametros_modulo_ids($parametros_modulo_sensores["ids_sensores"]);
                $cadena_ids_grupos_sensores = dame_cadena_ids_parametros_modulo_ids($parametros_modulo_sensores["ids_grupos_sensores"]);
                $parametros_modulo_sensores["cadena_ids_sensores"] = $cadena_ids_sensores;
                $parametros_modulo_sensores["cadena_ids_grupos_sensores"] = $cadena_ids_grupos_sensores;
                $cadena_ids_actuadores = dame_cadena_ids_parametros_modulo_ids($parametros_modulo_actuadores["ids_actuadores"]);
                $cadena_ids_grupos_actuadores = dame_cadena_ids_parametros_modulo_ids($parametros_modulo_actuadores["ids_grupos_actuadores"]);
                $parametros_modulo_actuadores["cadena_ids_actuadores"] = $cadena_ids_actuadores;
                $parametros_modulo_actuadores["cadena_ids_grupos_actuadores"] = $cadena_ids_grupos_actuadores;

                // Se añaden los parámetros de los módulos
                anyade_parametros_modulo_personal_usuario($id_usuario, $id_red, $parametros_modulo_personal);
                anyade_parametros_modulo_localizaciones_usuario($id_usuario, $id_red, $parametros_modulo_localizaciones);
                anyade_parametros_modulo_sensores_usuario($id_usuario, $id_red, $parametros_modulo_sensores);
                anyade_parametros_modulo_actuadores_usuario($id_usuario, $id_red, $parametros_modulo_actuadores);
            }

            // Si el identificador de usuario existe, es un duplicado de un usuario existente:
            // - Se duplican los parámetros de los módulos (diferentes a la red actual)
            // - Se duplican los widgets y los informes automáticos
            //   (sólo si no se ha modificado el perfil, los módulos o los parámetros de los módulos)
            if ($id_usuario_anterior != "")
            {
                // Se recupera el perfil del usuario anterior
                $fila_usuario_anterior = dame_fila_usuario($id_usuario_anterior);
                $perfil_anterior = $fila_usuario_anterior["perfil"];

                // Duplica las licencias, las secciones y los parámetros de los módulos de las redes
                switch ($perfil)
                {
                    case PERFIL_USUARIO_ESTANDAR:
                    {
                        if ($id_red_actual == ID_NINGUNO)
                        {
                            duplica_licencias_usuario_anterior($id_usuario_anterior, $id_usuario);
                            duplica_secciones_usuario_anterior($id_usuario_anterior, $id_usuario);
                            duplica_parametros_modulos_usuario_anterior($id_usuario_anterior, $id_usuario);
                        }
                        break;
                    }
                }

                // Duplica los elementos del usuario anterior
                $duplicar_elementos_usuario_anterior = ($perfil_anterior == $perfil);

                // Si hay red seleccionada y el perfil es estándar, se comprueba si se han modificado los módulos del usuario
                if (($duplicar_elementos_usuario_anterior == true) &&
                    ($id_red_actual != ID_NINGUNO) &&
                    ($perfil_anterior == PERFIL_USUARIO_ESTANDAR))
                {
                    // Si los módulos o los parámetros del usuario se han modificado, no se duplican los elementos del usuario anterior
                    $modulos_usuario_anterior = dame_modulos_usuario($id_usuario_anterior, $perfil_anterior, $id_red_actual);
                    $modulos_usuario = dame_modulos_usuario($id_usuario, $perfil, $id_red_actual);
                    $modulos_iguales = ($modulos_usuario_anterior == $modulos_usuario);

                    // Si los módulos son los mismos, se comprueban los parámetros de los módulos
                    if ($modulos_iguales == true)
                    {
                        // Cadenas de parámetros de módulos
                        $cadena_parametros_modulo_personal = dame_cadena_parametros_modulo_personal_usuario($parametros_modulo_personal);
                        $cadena_parametros_modulo_localizaciones = dame_cadena_parametros_modulo_localizaciones_usuario($parametros_modulo_localizaciones);
                        $cadena_parametros_modulo_sensores = dame_cadena_parametros_modulo_sensores_usuario($parametros_modulo_sensores);
                        $cadena_parametros_modulo_actuadores = dame_cadena_parametros_modulo_actuadores_usuario($parametros_modulo_actuadores);

                        $duplicar_elementos_usuario_anterior =
                            (($parametros_modulo_personal === NULL) || ($cadena_parametros_modulo_personal_anteriores == $cadena_parametros_modulo_personal)) &&
                            (($parametros_modulo_localizaciones === NULL) || ($cadena_parametros_modulo_localizaciones_anteriores == $cadena_parametros_modulo_localizaciones)) &&
                            (($parametros_modulo_sensores === NULL) || ($cadena_parametros_modulo_sensores_anteriores == $cadena_parametros_modulo_sensores)) &&
                            (($parametros_modulo_actuadores === NULL) || ($cadena_parametros_modulo_actuadores_anteriores == $cadena_parametros_modulo_actuadores));
                    }
                    else
                    {
                        $duplicar_elementos_usuario_anterior = false;
                    }
                }
                if ($duplicar_elementos_usuario_anterior == true)
                {
                    // Nota: Sólo se duplican las plantillas de informes si el usuario es estándar
                    // (entre administradores, se comparten las plantillas de informes - son visibles por todos ellos)
                    $informes_automaticos_duplicados = array();
                    if ($perfil == PERFIL_USUARIO_ESTANDAR)
                    {
                        duplica_plantillas_informes_usuario_anterior($id_usuario_anterior, $id_usuario, $informes_automaticos_duplicados);
                    }
                    duplica_widgets_usuario_anterior($id_usuario_anterior, $id_usuario);
                    duplica_informes_automaticos_usuario_anterior($id_usuario_anterior, $id_usuario, $informes_automaticos_duplicados);
                }
            }

            $res = "OK";
            $msg = $idiomas->_("Usuario añadido correctamente");
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


    // Duplica las licencias del usuario anterior
    function duplica_licencias_usuario_anterior($id_usuario_anterior, $id_usuario)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren las licencias del usuario (origen del actual) que sean de redes del usuario actual,
        // se cambia el usuario y se añaden
        $consulta_licencias_usuarios = "
            SELECT licencias_usuarios.*
            FROM
                licencias_usuarios,
                redes_usuarios,
                licencias
            WHERE
                (licencias_usuarios.usuario = '".$bd_red->_($id_usuario_anterior)."')
                AND (redes_usuarios.usuario = '".$bd_red->_($id_usuario)."')
                AND (licencias.red = redes_usuarios.red)
                AND (licencias.id = licencias_usuarios.licencia)";
        $res_licencias_usuarios = $bd_red->ejecuta_consulta($consulta_licencias_usuarios);
        if ($res_licencias_usuarios == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_licencias_usuarios."'");
        }

        while ($fila_licencia_usuario = $res_licencias_usuarios->dame_siguiente_fila())
        {
            $operacion_insercion_licencia_usuario = "
                INSERT INTO licencias_usuarios (
                    usuario,
                    licencia,
                    red
                ) VALUES (
                    '".$bd_red->_($id_usuario)."',
                    '".$bd_red->_($fila_licencia_usuario["licencia"])."',
                    '".$bd_red->_($fila_licencia_usuario["red"])."'
                )";
            $res_insercion_licencia_usuario = $bd_red->ejecuta_operacion($operacion_insercion_licencia_usuario);
            if ($res_insercion_licencia_usuario == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_licencia_usuario."'");
            }
        }
    }


    // Duplica las secciones del usuario anterior
    function duplica_secciones_usuario_anterior($id_usuario_anterior, $id_usuario)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren las secciones del usuario (origen del actual) que sean de redes del usuario actual,
        // se cambia el usuario y se añaden
        $consulta_secciones_usuarios = "
            SELECT secciones_usuarios.*
            FROM
                secciones_usuarios,
                redes_usuarios
            WHERE
                (secciones_usuarios.usuario = '".$bd_red->_($id_usuario_anterior)."')
                AND (redes_usuarios.usuario = '".$bd_red->_($id_usuario)."')
                AND (secciones_usuarios.red = redes_usuarios.red)";
        $res_secciones_usuarios = $bd_red->ejecuta_consulta($consulta_secciones_usuarios);
        if ($res_secciones_usuarios == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_secciones_usuarios."'");
        }

        while ($fila_seccion_usuario = $res_secciones_usuarios->dame_siguiente_fila())
        {
            $operacion_insercion_seccion_usuario = "
                INSERT INTO secciones_usuarios (
                    usuario,
                    modulo,
                    red,
                    secciones
                ) VALUES (
                    '".$bd_red->_($id_usuario)."',
                    '".$bd_red->_($fila_seccion_usuario["modulo"])."',
                    '".$bd_red->_($fila_seccion_usuario["red"])."',
                    '".$bd_red->_($fila_seccion_usuario["secciones"])."'
                )";
            $res_insercion_seccion_usuario = $bd_red->ejecuta_operacion($operacion_insercion_seccion_usuario);
            if ($res_insercion_seccion_usuario == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_seccion_usuario."'");
            }
        }
    }


    // Duplica los parámetros de los módulos del usuario anterior
    function duplica_parametros_modulos_usuario_anterior($id_usuario_anterior, $id_usuario)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren los parámetros de módulos del usuario (origen del actual) que sean de redes del usuario actual,
        // se cambia el usuario y se añaden
        $consulta_modulos_usuarios = "
            SELECT
                modulos_usuarios.modulo AS modulo,
                modulos_usuarios.red AS red,
                modulos_usuarios.parametros AS parametros
            FROM
                modulos_usuarios,
                redes_usuarios
            WHERE
                (modulos_usuarios.usuario = '".$bd_red->_($id_usuario_anterior)."')
                AND (redes_usuarios.usuario = '".$bd_red->_($id_usuario)."')
                AND (modulos_usuarios.red = redes_usuarios.red)";
        $res_modulos_usuarios = $bd_red->ejecuta_consulta($consulta_modulos_usuarios);
        if ($res_modulos_usuarios == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_modulos_usuarios."'");
        }

        while ($fila_modulo_usuario = $res_modulos_usuarios->dame_siguiente_fila())
        {
            $operacion_insercion_modulo_usuario = "
                INSERT INTO modulos_usuarios (
                    usuario,
                    modulo,
                    red,
                    parametros
                ) VALUES (
                    '".$bd_red->_($id_usuario)."',
                    '".$bd_red->_($fila_modulo_usuario["modulo"])."',
                    '".$bd_red->_($fila_modulo_usuario["red"])."',
                    '".$bd_red->_($fila_modulo_usuario["parametros"])."'
                )";
            $res_insercion_modulo_usuario = $bd_red->ejecuta_operacion($operacion_insercion_modulo_usuario);
            if ($res_insercion_modulo_usuario == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_modulo_usuario."'");
            }
        }
    }


    // Duplica los widgets del usuario anterior
    function duplica_widgets_usuario_anterior($id_usuario_anterior, $id_usuario)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren las pestañas de widgets del usuario anterior, se cambia el usuario y se añaden
        // (se duplican los widgets de cada una de las pestañas)
        $consulta_pestanyas_widgets = "
            SELECT pestanyas_widgets.*
            FROM
                pestanyas_widgets,
                redes_usuarios
            WHERE
                (pestanyas_widgets.usuario = '".$bd_red->_($id_usuario_anterior)."')
                AND (redes_usuarios.usuario = '".$bd_red->_($id_usuario)."')
                AND (pestanyas_widgets.red = redes_usuarios.red)";
        $res_pestanyas_widgets = $bd_red->ejecuta_consulta($consulta_pestanyas_widgets);
        if ($res_pestanyas_widgets == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_pestanyas_widgets."'");
        }

        while ($fila_pestanya_widgets = $res_pestanyas_widgets->dame_siguiente_fila())
        {
            // Se añade la pestaña de widgets
            $operacion_insercion_pestanya_widgets = "
                INSERT INTO pestanyas_widgets (
                    red,
                    usuario,
                    modulo,
                    nombre,
                    posicion,
                    actualizacion_periodica_rotatoria,
                    numeros_columnas_filas_widgets,
                    titulos_filas_widgets,
                    ajustar_altura_widgets,
                    parametros_apariencia_pestanya,
                    parametros_apariencia_widgets,
                    parametros_opciones_pantalla_completa
                ) VALUES (
                    '".$bd_red->_($fila_pestanya_widgets["red"])."',
                    '".$bd_red->_($id_usuario)."',
                    '".$bd_red->_($fila_pestanya_widgets["modulo"])."',
                    '".$bd_red->_($fila_pestanya_widgets["nombre"])."',
                    '".$bd_red->_($fila_pestanya_widgets["posicion"])."',
                    '".$bd_red->_($fila_pestanya_widgets["actualizacion_periodica_rotatoria"])."',
                    '".$bd_red->_($fila_pestanya_widgets["numeros_columnas_filas_widgets"])."',
                    '".$bd_red->_($fila_pestanya_widgets["titulos_filas_widgets"])."',
                    '".$bd_red->_($fila_pestanya_widgets["ajustar_altura_widgets"])."',
                    '".$bd_red->_($fila_pestanya_widgets["parametros_apariencia_pestanya"])."',
                    '".$bd_red->_($fila_pestanya_widgets["parametros_apariencia_widgets"])."',
                    '".$bd_red->_($fila_pestanya_widgets["parametros_opciones_pantalla_completa"])."'
                )";
            $res_insercion_pestanya_widgets = $bd_red->ejecuta_operacion($operacion_insercion_pestanya_widgets);
            if ($res_insercion_pestanya_widgets == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_pestanya_widgets."'");
            }

            // Identificadores de la pestaña de widgets anterior y la duplicada
            $id_pestanya_widgets_anterior = $fila_pestanya_widgets["id"];
            $id_pestanya_widgets_duplicada = $bd_red->dame_id_autoincremental_ultima_insercion();

            // Si la pestaña de widgets tiene imagen de fondo, se duplica
            $parametros_apariencia_pestanya = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_pestanya_widgets["parametros_apariencia_pestanya"]);
            $imagen_fondo_pestanya_widgets = $parametros_apariencia_pestanya[INDICE_PARAMETRO_APARIENCIA_PESTANYA_PESTANYA_WIDGETS_IMAGEN_FONDO];
            if ($imagen_fondo_pestanya_widgets == VALOR_SI)
            {
                duplica_imagen_base_datos(ORIGEN_IMAGEN_PESTANYA_WIDGETS_FONDO, $id_pestanya_widgets_anterior, $id_pestanya_widgets_duplicada);
            }

            // Se recorren los widgets de la pestaña anterior, se cambia el usuario y la pestaña y se añaden
            $consulta_widgets = "
                SELECT *
                FROM widgets
                WHERE
                    pestanya = '".$bd_red->_($id_pestanya_widgets_anterior)."'";
            $res_widgets = $bd_red->ejecuta_consulta($consulta_widgets);
            if ($res_widgets == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_widgets."'");
            }

            while ($fila_widget = $res_widgets->dame_siguiente_fila())
            {
                $operacion_insercion_widget = "
                    INSERT INTO widgets (
                        usuario,
                        red,
                        nombre,
                        posicion,
                        tipo,
                        parametros_tipo,
                        pestanya,
                        numero_columnas
                    ) VALUES (
                        '".$bd_red->_($id_usuario)."',
                        '".$bd_red->_($fila_widget["red"])."',
                        '".$bd_red->_($fila_widget["nombre"])."',
                        '".$bd_red->_($fila_widget["posicion"])."',
                        '".$bd_red->_($fila_widget["tipo"])."',
                        '".$bd_red->_($fila_widget["parametros_tipo"])."',
                        '".$bd_red->_($id_pestanya_widgets_duplicada)."',
                        '".$bd_red->_($fila_widget["numero_columnas"])."'
                    )";
                $res_insercion_widget = $bd_red->ejecuta_operacion($operacion_insercion_widget);
                if ($res_insercion_widget == false)
                {
                    throw new Exception("Error en la operación: '".$operacion_insercion_widget."'");
                }

                // Si el widget es de tipo imagen, se duplica la imagen
                switch ($fila_widget["tipo"])
                {
                    case TIPO_WIDGET_IMAGEN:
                    {
                        $id_widget_anyadido = $bd_red->dame_id_autoincremental_ultima_insercion();
                        $id_origen_anterior = implode(SEPARADOR_PARAMETROS_SIMPLES, array($fila_widget["pestanya"], $fila_widget["id"]));
                        $id_origen_duplicado = implode(SEPARADOR_PARAMETROS_SIMPLES, array($id_pestanya_widgets_duplicada, $id_widget_anyadido));
                        duplica_imagen_base_datos(ORIGEN_IMAGEN_WIDGET_IMAGEN, $id_origen_anterior, $id_origen_duplicado);
                        break;
                    }
                }
            }
        }
    }


    // Duplica las plantillas de informes del usuario anterior
    function duplica_plantillas_informes_usuario_anterior($id_usuario_anterior, $id_usuario, &$ids_informes_automaticos_duplicados)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren las plantillas de informes del usuario anterior, se cambia el usuario y se añaden
        $consulta_plantillas_informes = "
            SELECT
                plantillas_informes.id AS id,
                plantillas_informes.red AS red,
                plantillas_informes.usuario AS usuario,
                plantillas_informes.nombre AS nombre,
                plantillas_informes.descripcion AS descripcion,
                plantillas_informes.tipo AS tipo,
                plantillas_informes.titulo_informe AS titulo_informe,
                plantillas_informes.periodo_tiempo_defecto AS periodo_tiempo_defecto,
                plantillas_informes.tipo_seleccion_horario_semanal_fechas AS tipo_seleccion_horario_semanal_fechas
            FROM
                plantillas_informes,
                redes_usuarios
            WHERE
                (plantillas_informes.usuario = '".$bd_red->_($id_usuario_anterior)."')
                AND (redes_usuarios.usuario = '".$bd_red->_($id_usuario)."')
                AND (plantillas_informes.red = redes_usuarios.red)";
        $res_plantillas_informes = $bd_red->ejecuta_consulta($consulta_plantillas_informes);
        if ($res_plantillas_informes == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_plantillas_informes."'");
        }

        while ($fila_plantilla_informes = $res_plantillas_informes->dame_siguiente_fila())
        {
            // Se añade la plantilla de informes
            $operacion_insercion_plantilla_informes = "
                INSERT INTO plantillas_informes (
                    nombre,
                    red,
                    usuario,
                    descripcion,
                    tipo,
                    titulo_informe,
                    periodo_tiempo_defecto,
                    tipo_seleccion_horario_semanal_fechas
                ) VALUES (
                    '".$bd_red->_($fila_plantilla_informes["nombre"])."',
                    '".$bd_red->_($fila_plantilla_informes["red"])."',
                    '".$bd_red->_($id_usuario)."',
                    '".$bd_red->_($fila_plantilla_informes["descripcion"])."',
                    '".$bd_red->_($fila_plantilla_informes["tipo"])."',
                    '".$bd_red->_($fila_plantilla_informes["titulo_informe"])."',
                    '".$bd_red->_($fila_plantilla_informes["periodo_tiempo_defecto"])."',
                    '".$bd_red->_($fila_plantilla_informes["tipo_seleccion_horario_semanal_fechas"])."'
                )";
            $res_insercion_plantilla_informes = $bd_red->ejecuta_operacion($operacion_insercion_plantilla_informes);
            if ($res_insercion_plantilla_informes == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_plantilla_informes."'");
            }

            // Identificadores de la plantilla anterior y la duplicada
            $id_plantilla_informe_anterior = $fila_plantilla_informes["id"];
            $id_plantilla_informe_duplicada = $bd_red->dame_id_autoincremental_ultima_insercion();

            // Duplica los parámetros y los elementos de la plantilla de informe anterior
            $ids_parametros_plantilla_informe_anterior = NULL;
            $ids_parametros_plantilla_informe = NULL;
            $ids_elementos_plantilla_informe_anterior = NULL;
            $ids_elementos_plantilla_informe = NULL;
            duplica_parametros_plantilla_informe_anterior(
                $fila_plantilla_informes["red"],
                $id_plantilla_informe_anterior,
                $id_plantilla_informe_duplicada,
                $ids_parametros_plantilla_informe_anterior,
                $ids_parametros_plantilla_informe);
            duplica_elementos_plantilla_informe_anterior(
                $fila_plantilla_informes["red"],
                $id_plantilla_informe_anterior,
                $id_plantilla_informe_duplicada,
                $ids_parametros_plantilla_informe_anterior,
                $ids_parametros_plantilla_informe,
                $ids_elementos_plantilla_informe_anterior,
                $ids_elementos_plantilla_informe);

            // Duplica los informes automáticos de la plantilla de informe anterior
            $ids_informes_automaticos_duplicados_plantilla_informe_anterior = duplica_informes_automaticos_usuario_anterior_plantilla_informe_anterior(
                $id_usuario_anterior,
                $id_usuario,
                $id_plantilla_informe_anterior,
                $id_plantilla_informe_duplicada,
                $ids_parametros_plantilla_informe_anterior,
                $ids_parametros_plantilla_informe,
                $ids_elementos_plantilla_informe_anterior,
                $ids_elementos_plantilla_informe);
            $ids_informes_automaticos_duplicados = array_merge(
                $ids_informes_automaticos_duplicados,
                $ids_informes_automaticos_duplicados_plantilla_informe_anterior);
        }
    }


    // Duplica los informes automáticos del usuario anterior de la plantilla de informe anterior
    function duplica_informes_automaticos_usuario_anterior_plantilla_informe_anterior(
        $id_usuario_anterior,
        $id_usuario,
        $id_plantilla_informe_anterior,
        $id_plantilla_informe,
        $ids_parametros_plantilla_informe_anterior,
        $ids_parametros_plantilla_informe,
        $ids_elementos_plantilla_informe_anterior,
        $ids_elementos_plantilla_informe)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Identificadores de informes duplicados
        $ids_informes_automaticos_duplicados_plantilla_informe_anterior = array();

        // Se recorren los informes automáticos del usuario anterior (origen del actual), se cambia el usuario y se añaden
        $consulta_informes_automaticos = "
            SELECT *
            FROM informes_automaticos
            WHERE
                (usuario = '".$bd_red->_($id_usuario_anterior)."')
                AND (tipo = '".TIPO_INFORME_PERSONAL_INFORME_PLANTILLA_INFORME."')
                AND (SUBSTRING_INDEX(SUBSTRING_INDEX(parametros_tipo, '".SEPARADOR_PARAMETROS_COMPUESTOS."', ".(INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_ID_PLANTILLA_INFORME + 1)."), '".SEPARADOR_PARAMETROS_COMPUESTOS."', -1) = '".$id_plantilla_informe_anterior."')";

        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            // Se modifican los parámetros de tipo del informe automático:
            // - Se modifica el identificador de plantilla de informe
            // - Se modifican los identificadores de los parámetros de la plantilla de informe
            // - Se modifican los identificadores de los elementos portada de la plantilla de informe
            // - Se modifican los identificadores de los elementos título de la plantilla de informe
            // - Se modifican los identificadores de los elementos texto de la plantilla de informe
            // - Se modifican los identificadores de los elementos imagen de la plantilla de informe

            // Parámetros de tipo JSON del informe automático
            $cadena_parametros_tipo_json_informe_automatico = $fila_informe_automatico["parametros_tipo_json"];
            $cadena_parametros_tipo_json_informe_automatico_modificada = $cadena_parametros_tipo_json_informe_automatico;

            // Se modifica el identificador de plantilla de informe
            $parametros_tipo_informe_automatico = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_informe_automatico["parametros_tipo"]);
            $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_ID_PLANTILLA_INFORME] = $id_plantilla_informe;

            // Se modifican los identificadores de los parámetros de la plantilla de informe
            $cadena_ids_parametros_plantilla_informe_informe_automatico = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_PARAMETROS];
            if ($cadena_ids_parametros_plantilla_informe_informe_automatico != "")
            {
                $ids_parametros_plantilla_informe_informe_automatico = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_parametros_plantilla_informe_informe_automatico);
                for ($i = 0; $i < count($ids_parametros_plantilla_informe_informe_automatico); $i++)
                {
                    $id_parametro_plantilla_informe_informe_automatico = $ids_parametros_plantilla_informe_informe_automatico[$i];
                    $indice_parametro = array_search($id_parametro_plantilla_informe_informe_automatico, $ids_parametros_plantilla_informe_anterior);
                    if (($indice_parametro !== false) && ($indice_parametro !== NULL))
                    {
                        $ids_parametros_plantilla_informe_informe_automatico[$i] = $ids_parametros_plantilla_informe[$indice_parametro];
                    }
                }
                $cadena_ids_parametros_plantilla_informe_informe_automatico_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_parametros_plantilla_informe_informe_automatico);
                $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_PARAMETROS] = $cadena_ids_parametros_plantilla_informe_informe_automatico_modificada;
            }

            // Se modifican los identificadores de los elementos portada de la plantilla de informe (de los parámetros tipo y tipo json)
            $cadena_ids_elementos_portada_plantilla_informe_informe_automatico = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_PORTADA];
            if ($cadena_ids_elementos_portada_plantilla_informe_informe_automatico != "")
            {
                $ids_elementos_portada_plantilla_informe_informe_automatico = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_portada_plantilla_informe_informe_automatico);
                for ($i = 0; $i < count($ids_elementos_portada_plantilla_informe_informe_automatico); $i++)
                {
                    $id_elemento_portada_plantilla_informe_informe_automatico = $ids_elementos_portada_plantilla_informe_informe_automatico[$i];
                    $indice_elemento = array_search($id_elemento_portada_plantilla_informe_informe_automatico, $ids_elementos_plantilla_informe_anterior);
                    if (($indice_elemento !== false) && ($indice_elemento !== NULL))
                    {
                        $ids_elementos_portada_plantilla_informe_informe_automatico[$i] = $ids_elementos_plantilla_informe[$indice_elemento];
                        $cadena_parametros_tipo_json_informe_automatico_modificada = str_replace(
                            '"subtitulo_elemento_portada_'.$ids_elementos_plantilla_informe_anterior[$indice_elemento].'"',
                            '"subtitulo_elemento_portada_'.$ids_elementos_plantilla_informe[$indice_elemento].'"',
                            $cadena_parametros_tipo_json_informe_automatico_modificada);
                    }
                }
                $cadena_ids_elementos_portada_plantilla_informe_informe_automatico_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_elementos_portada_plantilla_informe_informe_automatico);
                $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_PORTADA] = $cadena_ids_elementos_portada_plantilla_informe_informe_automatico_modificada;
            }

            // Se modifican los identificadores de los elementos título de la plantilla de informe (de los parámetros tipo y tipo json)
            $cadena_ids_elementos_titulo_plantilla_informe_informe_automatico = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TITULO];
            if ($cadena_ids_elementos_titulo_plantilla_informe_informe_automatico != "")
            {
                $ids_elementos_titulo_plantilla_informe_informe_automatico = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_titulo_plantilla_informe_informe_automatico);
                for ($i = 0; $i < count($ids_elementos_titulo_plantilla_informe_informe_automatico); $i++)
                {
                    $id_elemento_titulo_plantilla_informe_informe_automatico = $ids_elementos_titulo_plantilla_informe_informe_automatico[$i];
                    $indice_elemento = array_search($id_elemento_titulo_plantilla_informe_informe_automatico, $ids_elementos_plantilla_informe_anterior);
                    if (($indice_elemento !== false) && ($indice_elemento !== NULL))
                    {
                        $ids_elementos_portada_plantilla_informe_informe_automatico[$i] = $ids_elementos_plantilla_informe[$indice_elemento];
                        $cadena_parametros_tipo_json_informe_automatico_modificada = str_replace(
                            '"titulo_elemento_titulo_'.$ids_elementos_plantilla_informe_anterior[$indice_elemento].'"',
                            '"titulo_elemento_titulo_'.$ids_elementos_plantilla_informe[$indice_elemento].'"',
                            $cadena_parametros_tipo_json_informe_automatico_modificada);
                    }
                }
                $cadena_ids_elementos_titulo_plantilla_informe_informe_automatico_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_elementos_titulo_plantilla_informe_informe_automatico);
                $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TITULO] = $cadena_ids_elementos_titulo_plantilla_informe_informe_automatico_modificada;
            }

            // Se modifican los identificadores de los elementos texto de la plantilla de informe (de los parámetros tipo y tipo json)
            $cadena_ids_elementos_texto_plantilla_informe_informe_automatico = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TEXTO];
            if ($cadena_ids_elementos_texto_plantilla_informe_informe_automatico != "")
            {
                $ids_elementos_texto_plantilla_informe_informe_automatico = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_texto_plantilla_informe_informe_automatico);
                for ($i = 0; $i < count($ids_elementos_texto_plantilla_informe_informe_automatico); $i++)
                {
                    $id_elemento_texto_plantilla_informe_informe_automatico = $ids_elementos_texto_plantilla_informe_informe_automatico[$i];
                    $indice_elemento = array_search($id_elemento_texto_plantilla_informe_informe_automatico, $ids_elementos_plantilla_informe_anterior);
                    if (($indice_elemento !== false) && ($indice_elemento !== NULL))
                    {
                        $ids_elementos_texto_plantilla_informe_informe_automatico[$i] = $ids_elementos_plantilla_informe[$indice_elemento];
                        $cadena_parametros_tipo_json_informe_automatico_modificada = str_replace(
                            '"texto_elemento_texto_'.$ids_elementos_plantilla_informe_anterior[$indice_elemento].'"',
                            '"texto_elemento_texto_'.$ids_elementos_plantilla_informe[$indice_elemento].'"',
                            $cadena_parametros_tipo_json_informe_automatico_modificada);
                    }
                }
                $cadena_ids_elementos_texto_plantilla_informe_informe_automatico_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_elementos_texto_plantilla_informe_informe_automatico);
                $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_TEXTO] = $cadena_ids_elementos_texto_plantilla_informe_informe_automatico_modificada;
            }

            // Se modifican los identificadores de los elementos imagen de la plantilla de informe
            $cadena_ids_elementos_imagen_plantilla_informe_informe_automatico = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_IMAGEN];
            if ($cadena_ids_elementos_imagen_plantilla_informe_informe_automatico != "")
            {
                $ids_elementos_imagen_plantilla_informe_informe_automatico = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_imagen_plantilla_informe_informe_automatico);
                for ($i = 0; $i < count($ids_elementos_imagen_plantilla_informe_informe_automatico); $i++)
                {
                    $id_elemento_imagen_plantilla_informe_informe_automatico = $ids_elementos_imagen_plantilla_informe_informe_automatico[$i];
                    $indice_elemento = array_search($id_elemento_imagen_plantilla_informe_informe_automatico, $ids_elementos_plantilla_informe_anterior);
                    if (($indice_elemento !== false) && ($indice_elemento !== NULL))
                    {
                        $ids_elementos_imagen_plantilla_informe_informe_automatico[$i] = $ids_elementos_plantilla_informe[$indice_elemento];
                    }
                }
                $cadena_ids_elementos_imagen_plantilla_informe_informe_automatico_modificada = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_elementos_imagen_plantilla_informe_informe_automatico);
                $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_IMAGEN] = $cadena_ids_elementos_imagen_plantilla_informe_informe_automatico_modificada;
            }

            // Parámetros de tipo de informe automático modificada
            $cadena_parametros_tipo_informe_automatico_modificada = implode(SEPARADOR_PARAMETROS_COMPUESTOS, $parametros_tipo_informe_automatico);

            // Se añade el informe automático
            $operacion_insercion_informe_automatico = "
                INSERT INTO informes_automaticos (
                    usuario,
                    red,
                    nombre,
                    periodicidad,
                    parametros_periodicidad,
                    parametros_periodo_tiempo,
                    numero_horas_desplazamiento,
                    tipo,
                    parametros_tipo,
                    parametros_tipo_json,
                    direcciones_email_destino
                ) VALUES (
                    '".$bd_red->_($id_usuario)."',
                    '".$bd_red->_($fila_informe_automatico["red"])."',
                    '".$bd_red->_($fila_informe_automatico["nombre"])."',
                    '".$bd_red->_($fila_informe_automatico["periodicidad"])."',
                    '".$bd_red->_($fila_informe_automatico["parametros_periodicidad"])."',
                    '".$bd_red->_($fila_informe_automatico["parametros_periodo_tiempo"])."',
                    '".$bd_red->_($fila_informe_automatico["numero_horas_desplazamiento"])."',
                    '".$bd_red->_($fila_informe_automatico["tipo"])."',
                    '".$bd_red->_($cadena_parametros_tipo_informe_automatico_modificada)."',
                    '".$bd_red->_($cadena_parametros_tipo_json_informe_automatico_modificada)."',
                    '".$bd_red->_($fila_informe_automatico["direcciones_email_destino"])."'
                )";
            $res_insercion_informe_automatico = $bd_red->ejecuta_operacion($operacion_insercion_informe_automatico);
            if ($res_insercion_informe_automatico == true)
            {
                // Identificadores de informes automáticos
                $id_informe_automatico_anterior = $fila_informe_automatico["id"];
                $id_informe_automatico_duplicado = $bd_red->dame_id_autoincremental_ultima_insercion();

                // Se duplican las imágenes personalizadas del informe automático de la plantilla de informe
                $cadena_ids_elementos_imagen_plantilla_informe_informe_automatico = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IDS_ELEMENTOS_IMAGEN];
                $cadena_imagenes_personalizadas_elementos_imagen_plantilla_informe_informe_automatico = $parametros_tipo_informe_automatico[INDICE_PARAMETRO_TIPO_INFORME_AUTOMATICO_PERSONAL_INFORME_PLANTILLA_INFORME_IMAGENES_PERSONALIZADAS_ELEMENTOS_IMAGEN];
                if ($cadena_ids_elementos_imagen_plantilla_informe_informe_automatico != "")
                {
                    $ids_elementos_imagen_plantilla_informe_informe_automatico_anterior = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_imagen_plantilla_informe_informe_automatico);
                    $ids_elementos_imagen_plantilla_informe_informe_automatico_duplicado = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids_elementos_imagen_plantilla_informe_informe_automatico_modificada);
                    $imagenes_personalizadas_elementos_imagen_plantilla_informe_informe_automatico = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_imagenes_personalizadas_elementos_imagen_plantilla_informe_informe_automatico);
                    for ($i = 0; $i < count($ids_elementos_imagen_plantilla_informe_informe_automatico_anterior); $i++)
                    {
                        $id_elemento_imagen_plantilla_informe_informe_automatico_anterior = $ids_elementos_imagen_plantilla_informe_informe_automatico_anterior[$i];
                        $id_elemento_imagen_plantilla_informe_informe_automatico_duplicado = $ids_elementos_imagen_plantilla_informe_informe_automatico_duplicado[$i];

                        $imagen_personalizada_elemento_imagen_plantilla_informe_informe_automatico = $imagenes_personalizadas_elementos_imagen_plantilla_informe_informe_automatico[$i];
                        if ($imagen_personalizada_elemento_imagen_plantilla_informe_informe_automatico == VALOR_SI)
                        {
                            $id_origen_anterior = implode(SEPARADOR_PARAMETROS_SIMPLES, array($id_informe_automatico_anterior, $id_elemento_imagen_plantilla_informe_informe_automatico_anterior));
                            $id_origen_duplicado = implode(SEPARADOR_PARAMETROS_SIMPLES, array($id_informe_automatico_duplicado, $id_elemento_imagen_plantilla_informe_informe_automatico_duplicado));
                            duplica_imagen_base_datos(ORIGEN_IMAGEN_INFORME_AUTOMATICO_PLANTILLA_INFORME_IMAGEN, $id_origen_anterior, $id_origen_duplicado);
                        }
                    }
                }
            }
            else
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_informe_automatico."'");
            }

            // Se añade el identificador del informe automático que se ha duplicado
            array_push($ids_informes_automaticos_duplicados_plantilla_informe_anterior, $fila_informe_automatico["id"]);
        }

        // Se devuelven los identificadores de los informes automáticos duplicados
        return ($ids_informes_automaticos_duplicados_plantilla_informe_anterior);
    }


    // Duplica los informes automáticos del usuario anterior
    function duplica_informes_automaticos_usuario_anterior($id_usuario_anterior, $id_usuario, $ids_informes_automaticos_duplicados)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recorren los informes automáticos del usuario anterior (origen del actual), se cambia el usuario y se añaden
        $consulta_informes_automaticos = "
            SELECT informes_automaticos.*
            FROM
                informes_automaticos,
                redes_usuarios
            WHERE
                (informes_automaticos.usuario = '".$bd_red->_($id_usuario_anterior)."')
                AND (redes_usuarios.usuario = '".$bd_red->_($id_usuario)."')
                AND (informes_automaticos.red = redes_usuarios.red)";
        $res_informes_automaticos = $bd_red->ejecuta_consulta($consulta_informes_automaticos);
        if ($res_informes_automaticos == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_informes_automaticos."'");
        }

        while ($fila_informe_automatico = $res_informes_automaticos->dame_siguiente_fila())
        {
            if (in_array($fila_informe_automatico["id"], $ids_informes_automaticos_duplicados) == true)
            {
                continue;
            }

            $operacion_insercion_informe_automatico = "
                INSERT INTO informes_automaticos (
                    usuario,
                    red,
                    nombre,
                    periodicidad,
                    parametros_periodicidad,
                    parametros_periodo_tiempo,
                    numero_horas_desplazamiento,
                    tipo,
                    parametros_tipo,
                    parametros_tipo_json,
                    direcciones_email_destino
                ) VALUES (
                    '".$bd_red->_($id_usuario)."',
                    '".$bd_red->_($fila_informe_automatico["red"])."',
                    '".$bd_red->_($fila_informe_automatico["nombre"])."',
                    '".$bd_red->_($fila_informe_automatico["periodicidad"])."',
                    '".$bd_red->_($fila_informe_automatico["parametros_periodicidad"])."',
                    '".$bd_red->_($fila_informe_automatico["parametros_periodo_tiempo"])."',
                    '".$bd_red->_($fila_informe_automatico["numero_horas_desplazamiento"])."',
                    '".$bd_red->_($fila_informe_automatico["tipo"])."',
                    '".$bd_red->_($fila_informe_automatico["parametros_tipo"])."',
                    '".$bd_red->_($fila_informe_automatico["parametros_tipo_json"])."',
                    '".$bd_red->_($fila_informe_automatico["direcciones_email_destino"])."'
                )";
            $res_insercion_informe_automatico = $bd_red->ejecuta_operacion($operacion_insercion_informe_automatico);
            if ($res_insercion_informe_automatico == false)
            {
                throw new Exception("Error en la operación: '".$operacion_insercion_informe_automatico."'");
            }
        }
    }
?>
