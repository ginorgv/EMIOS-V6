<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/util_sistema.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosDatos.php');
    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_informes_automaticos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_inicializacion.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_widgets.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/ModuloActuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/ModuloAdministracion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/Usuario.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/util_administracion_usuarios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/ModuloLocalizaciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloMonitorizacion/ModuloMonitorizacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/ModuloPersonal.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/ModuloProyectos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloRed/ModuloRed.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/ModuloSensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ModuloSmartmeter.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/util_modulo_smartmeter.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/util_modulos_web.php');


    // Constantes

    // Indices de parámetros del módulo Personal
	define("INDICE_PARAMETRO_MODULO_PERSONAL_NUMERO_MAXIMO_INFORMES_AUTOMATICOS", 0);
    define("INDICE_PARAMETRO_MODULO_PERSONAL_ADMINISTRACION_WIDGETS", 1);
    define("INDICE_PARAMETRO_MODULO_PERSONAL_ADMINISTRACION_PLANTILLAS_INFORMES", 2);
    define("INDICE_PARAMETRO_MODULO_PERSONAL_ADMINISTRACION_INFORMES_AUTOMATICOS", 3);
    define("INDICE_PARAMETRO_MODULO_PERSONAL_MOSTRAR_OTROS_MODULOS", 4);

    // Indices de parámetros del módulo Localizaciones
	define("INDICE_PARAMETRO_MODULO_LOCALIZACIONES_PERMISO_TODAS_LOCALIZACIONES", 0);
    define("INDICE_PARAMETRO_MODULO_LOCALIZACIONES_IDS_LOCALIZACIONES", 1);
    define("INDICE_PARAMETRO_MODULO_LOCALIZACIONES_ADMINISTRACION_LOCALIZACIONES", 2);
    define("INDICE_PARAMETRO_MODULO_LOCALIZACIONES_ADMINISTRACION_INSTALACIONES", 3);

    // Indices de parámetros del módulo Sensores
	define("INDICE_PARAMETRO_MODULO_SENSORES_PERMISO_TODOS_SENSORES", 0);
    define("INDICE_PARAMETRO_MODULO_SENSORES_IDS_SENSORES", 1);
    define("INDICE_PARAMETRO_MODULO_SENSORES_IDS_GRUPOS_SENSORES", 2);
    define("INDICE_PARAMETRO_MODULO_SENSORES_ADMINISTRACION_SENSORES", 3);
    define("INDICE_PARAMETRO_MODULO_SENSORES_ADMINISTRACION_COMENTARIOS_SENSORES", 4);
    define("INDICE_PARAMETRO_MODULO_SENSORES_LECTURA_SENSORES", 5);
    define("INDICE_PARAMETRO_MODULO_SENSORES_EXPORTACION_SENSORES", 6);
    define("INDICE_PARAMETRO_MODULO_SENSORES_ADMINISTRACION_EVENTOS", 7);
    define("INDICE_PARAMETRO_MODULO_SENSORES_ENVIO_VALORES_MANUALES_SENSORES", 8);

    // Indices de parámetros del módulo Actuadores
	define("INDICE_PARAMETRO_MODULO_ACTUADORES_PERMISO_TODOS_ACTUADORES", 0);
    define("INDICE_PARAMETRO_MODULO_ACTUADORES_IDS_ACTUADORES", 1);
    define("INDICE_PARAMETRO_MODULO_ACTUADORES_IDS_GRUPOS_ACTUADORES", 2);
    define("INDICE_PARAMETRO_MODULO_ACTUADORES_ADMINISTRACION_ACTUADORES", 3);
    define("INDICE_PARAMETRO_MODULO_ACTUADORES_ADMINISTRACION_COMENTARIOS_ACTUADORES", 4);
    define("INDICE_PARAMETRO_MODULO_ACTUADORES_ACCIONES_ACTUADORES", 5);
    define("INDICE_PARAMETRO_MODULO_ACTUADORES_ADMINISTRACION_PROGRAMACIONES", 6);
    define("INDICE_PARAMETRO_MODULO_ACTUADORES_ADMINISTRACION_REGLAS", 7);


    // Devuelve las redes del usuario
	function dame_ids_redes_usuario($id_usuario, $perfil)
	{
        $bd_red = BaseDatosRed::dame_base_datos();

        switch ($perfil)
        {
            case PERFIL_USUARIO_ESTANDAR:
            case PERFIL_USUARIO_ADMINISTRADOR:
            {
                $consulta_redes = "
                    SELECT
                        red AS red
                    FROM redes_usuarios
                    WHERE
                        usuario = '".$bd_red->_($id_usuario)."'
                    ORDER BY red ASC";
                break;
            }
            case PERFIL_USUARIO_SUPERADMINISTRADOR:
            {
                $consulta_redes = "
                    SELECT
                        id AS red
                    FROM redes
                    ORDER BY id ASC";
                break;
            }
            default:
            {
                throw new Exception("Perfil usuario desconocido");
            }
        }
        $res_redes = $bd_red->ejecuta_consulta($consulta_redes);
        if ($res_redes == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_redes."'");
        }

        $ids_redes = array();
        while ($fila_red = $res_redes->dame_siguiente_fila())
        {
            array_push($ids_redes, $fila_red["red"]);
        }
        return ($ids_redes);
    }


    // Devuelve los nombres de las redes del usuario
	function dame_nombres_redes_usuario($id, $perfil)
	{
        $bd_red = BaseDatosRed::dame_base_datos();

        switch ($perfil)
        {
            case PERFIL_USUARIO_ESTANDAR:
            case PERFIL_USUARIO_ADMINISTRADOR:
            {
                $consulta_redes = "
                    SELECT
                        redes.nombre AS nombre
                    FROM
                        redes,
                        redes_usuarios
                    WHERE
                        (redes_usuarios.usuario = '".$bd_red->_($id)."')
                        AND (redes_usuarios.red = redes.id)
                    ORDER BY redes.nombre ASC";
                break;
            }
            case PERFIL_USUARIO_SUPERADMINISTRADOR:
            {
                $consulta_redes = "
                    SELECT
                        redes.nombre AS nombre
                    FROM
                        redes,
                        redes_usuarios
                    ORDER BY redes.nombre ASC";
                break;
            }
            default:
            {
                throw new Exception("Perfil usuario desconocido");
            }
        }
        $res_redes = $bd_red->ejecuta_consulta($consulta_redes);
        if ($res_redes == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_redes."'");
        }

        $nombres_redes = array();
        while ($fila_red = $res_redes->dame_siguiente_fila())
        {
            array_push($nombres_redes, $fila_red["nombre"]);
        }
        return ($nombres_redes);
    }


    // Devuelve los módulos del usuario
	function dame_modulos_usuario($id, $perfil, $id_red)
	{
        try
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            switch ($perfil)
            {
                case PERFIL_USUARIO_ESTANDAR:
                {
                    $consulta_licencias = "
                        SELECT
                            licencias.modulo AS modulo
                        FROM
                            usuarios,
                            licencias_usuarios,
                            licencias
                        WHERE
                            (usuarios.id = licencias_usuarios.usuario)
                            AND (licencias_usuarios.licencia = licencias.id)
                            AND (usuarios.id = '".$bd_red->_($id)."')
                            AND (licencias.red = ".$bd_red->_($id_red).")
                            AND (licencias.activada = '".VALOR_SI."')
                        ORDER BY
                            licencias.modulo ASC";
                    break;
                }
                case PERFIL_USUARIO_ADMINISTRADOR:
                {
                    $consulta_licencias = "
                        SELECT
                            licencias.modulo AS modulo
                        FROM licencias
                        WHERE
                            (licencias.red = ".$bd_red->_($id_red).")
                            AND (licencias.activada = '".VALOR_SI."')
                        ORDER BY
                            licencias.modulo ASC";
                    break;
                }
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    $consulta_licencias = "
                        SELECT
                            DISTINCT(modulo) AS modulo
                        FROM licencias
                        WHERE
                            licencias.red = ".$bd_red->_($id_red)."
                        ORDER BY
                            licencias.modulo ASC";
                    break;
                }
                default:
                {
                    throw new Exception("Perfil usuario desconocido");
                }
            }
            $res_licencias = $bd_red->ejecuta_consulta($consulta_licencias);
            if ($res_licencias == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_licencias."'");
            }

            $modulos = array();
            while ($fila_licencia = $res_licencias->dame_siguiente_fila())
            {
                $modulo = $fila_licencia["modulo"];
                $anyadir_modulo = dame_anyadir_modulo($modulo);
                if ($anyadir_modulo == true)
                {
                    array_push($modulos, $modulo);
                }
            }
            switch ($perfil)
            {
                case PERFIL_USUARIO_ESTANDAR:
                case PERFIL_USUARIO_ADMINISTRADOR:
                {
                    array_push($modulos, MODULO_ADMINISTRACION);
                    break;
                }
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    array_push($modulos, MODULO_ADMINISTRACION);
                    array_push($modulos, MODULO_MONITORIZACION);

                    // Si no existe el módulo de red se añade para el superadministrador
                    if (($_SESSION["id_red"] != ID_NINGUNO) && (array_key_exists(MODULO_RED, $modulos) == false))
                    {
                        array_push($modulos, MODULO_RED);
                    }
                    break;
                }
            }
            return ($modulos);
        }
        catch (Exception $exception)
        {
            // Se añade información de la excepción en el log
            $log = dame_log();
            $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $exception);

            return (array());
        }
    }


    // Devuelve si hay que añadir el módulo (según la configuración de la red)
    function dame_anyadir_modulo($modulo)
    {
        $anyadir_modulo = true;
        switch ($modulo)
        {
            case MODULO_SMARTMETER:
            {
                if (($_SESSION["pais_tarifas_electricas"] == PAIS_NINGUNO) &&
                    ($_SESSION["pais_tarifas_gas"] == PAIS_NINGUNO) &&
                    ($_SESSION["pais_tarifas_agua"] == PAIS_NINGUNO))
                {
                    $anyadir_modulo = false;
                }
                break;
            }
            default:
            {
                break;
            }
        }
        return ($anyadir_modulo);
    }


    // Devuelve el número de licencias desactivadas de los módulos del usuario
	function dame_numero_licencias_desactivadas_modulos_usuario($id, $perfil, $id_red)
	{
        $bd_red = BaseDatosRed::dame_base_datos();

        switch ($perfil)
        {
            case PERFIL_USUARIO_ESTANDAR:
            {
                $consulta_licencias = "
                    SELECT
                        COUNT(licencias.modulo) AS numero_licencias_desactivadas
                    FROM
                        usuarios,
                        licencias_usuarios,
                        licencias
                    WHERE
                        (usuarios.id = licencias_usuarios.usuario)
                        AND (licencias_usuarios.licencia = licencias.id)
                        AND (usuarios.id = '".$bd_red->_($id)."')
                        AND (licencias.red = ".$bd_red->_($id_red).")
                        AND (licencias.activada = '".VALOR_NO."')
                    ORDER BY licencias.modulo ASC";
                break;
            }
            case PERFIL_USUARIO_ADMINISTRADOR:
            {
                $consulta_licencias = "
                    SELECT
                        COUNT(licencias.modulo) AS numero_licencias_desactivadas
                    FROM licencias
                    WHERE
                        (licencias.red = ".$bd_red->_($id_red).")
                        AND (licencias.activada = '".VALOR_NO."')
                    ORDER BY licencias.modulo ASC";
                break;
            }
            case PERFIL_USUARIO_SUPERADMINISTRADOR:
            {
                return (0);
            }
            default:
            {
                throw new Exception("Perfil usuario desconocido");
            }
        }
        $res_licencias = $bd_red->ejecuta_consulta($consulta_licencias);
        if (($res_licencias == false) || ($res_licencias->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_licencias."'");
        }

        $fila_licencias = $res_licencias->dame_siguiente_fila();
        $numero_licencias_desactivadas = $fila_licencias["numero_licencias_desactivadas"];
        return ($numero_licencias_desactivadas);
    }


    // Devuelve las secciones del usuario
	function dame_secciones_usuario($id, $id_red)
	{
        try
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta_secciones = "
                SELECT
                    modulo,
                    secciones
                FROM secciones_usuarios
                WHERE
                    (usuario = '".$bd_red->_($id)."')
                    AND (red = '".$bd_red->_($id_red)."')
                ORDER BY modulo ASC";
            $res_secciones = $bd_red->ejecuta_consulta($consulta_secciones);
            if ($res_secciones == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_secciones."'");
            }

            $secciones = array();
            while ($fila_secciones = $res_secciones->dame_siguiente_fila())
            {
                $secciones[$fila_secciones['modulo']] = explode(SEPARADOR_PARAMETROS_SIMPLES, $fila_secciones['secciones']);
            }
            return ($secciones);
        }
        catch (Exception $exception)
        {
            // Se añade información de la excepción en el log
            $log = dame_log();
            $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $exception);

            return (array());
        }
    }


    // Se añaden los módulos del usuario a la sesión (y los parámetros de los módulos)
    function inicializa_modulos()
    {
        $_SESSION["modulos"] = array();

        // Se recuperan los módulos del usuario
        $modulos = dame_modulos_usuario(
            $_SESSION["id_usuario"],
            $_SESSION["perfil"],
            $_SESSION["id_red"]);

        // Si el usuario es estándar y hay red seleccionadada se recuperan las secciones y se añaden al módulo correspondiente
        if (($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR) && ($_SESSION["id_red"] != ID_NINGUNO))
        {
            $secciones = dame_secciones_usuario($_SESSION["id_usuario"], $_SESSION["id_red"]);
        }

        // Se recuperan los módulos ordenados
        $todos_modulos_ordenados = dame_todos_modulos_ordenados();
        foreach ($todos_modulos_ordenados as $modulo)
        {
            // Administración
            if (in_array($modulo, $modulos))
            {
                switch ($modulo)
                {
                    case MODULO_ADMINISTRACION:
                    {
                        $_SESSION["modulos"][MODULO_ADMINISTRACION] = array(
                            "nombre" => NOMBRE_MODULO_ADMINISTRACION,
                            "seccion_defecto" => ModuloAdministracion::dame_seccion_defecto()
                        );
                        break;
                    }
                    case MODULO_MONITORIZACION:
                    {
                        $_SESSION["modulos"][MODULO_MONITORIZACION] = array(
                            "nombre" => NOMBRE_MODULO_MONITORIZACION,
                            "seccion_defecto" => ModuloMonitorizacion::dame_seccion_defecto()
                        );
                        break;
                    }
                    case MODULO_PERSONAL:
                    {
                        $parametros_modulo_personal = array(
                            "nombre" => NOMBRE_MODULO_PERSONAL
                        );
                        if ($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR)
                        {
                            $parametros_modulo_personal["secciones"] = $secciones[MODULO_PERSONAL];
                        }
                        else
                        {
                            $parametros_modulo_personal["secciones"] = NULL;
                        }
                        $seccion_defecto = ModuloPersonal::dame_seccion_defecto($parametros_modulo_personal["secciones"]);
                        $parametros_modulo_personal["seccion_defecto"] = $seccion_defecto;
                        $_SESSION["modulos"][MODULO_PERSONAL] = $parametros_modulo_personal;

                        // Inicialización de parámetros del módulo Personal del usuario
                        inicializa_parametros_modulo_personal_usuario();
                        break;
                    }
                    case MODULO_RED:
                    {
                        $parametros_modulo_red = array(
                            "nombre" => NOMBRE_MODULO_RED
                        );
                        if ($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR)
                        {
                            $parametros_modulo_red["secciones"] = $secciones[MODULO_RED];
                        }
                        else
                        {
                            $parametros_modulo_red["secciones"] = NULL;
                        }
                        $seccion_defecto = ModuloRed::dame_seccion_defecto($parametros_modulo_red["secciones"]);
                        $parametros_modulo_red["seccion_defecto"] = $seccion_defecto;
                        $_SESSION["modulos"][MODULO_RED] = $parametros_modulo_red;
                        break;
                    }
                    case MODULO_LOCALIZACIONES:
                    {
                        $parametros_modulo_localizaciones = array(
                            "nombre" => NOMBRE_MODULO_LOCALIZACIONES
                        );
                        if ($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR)
                        {
                            $parametros_modulo_localizaciones["secciones"] = $secciones[MODULO_LOCALIZACIONES];
                        }
                        else
                        {
                            $parametros_modulo_localizaciones["secciones"] = NULL;
                        }
                        $seccion_defecto = ModuloLocalizaciones::dame_seccion_defecto($parametros_modulo_localizaciones["secciones"]);
                        $parametros_modulo_localizaciones["seccion_defecto"] = $seccion_defecto;
                        $_SESSION["modulos"][MODULO_LOCALIZACIONES] = $parametros_modulo_localizaciones;

                        // Inicialización de parámetros del módulo Localizaciones del usuario
                        inicializa_parametros_modulo_localizaciones_usuario();
                        break;
                    }
                    case MODULO_SENSORES:
                    {
                        $parametros_modulo_sensores = array(
                            "nombre" => NOMBRE_MODULO_SENSORES
                        );
                        if ($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR)
                        {
                            $parametros_modulo_sensores["secciones"] = $secciones[MODULO_SENSORES];
                        }
                        else
                        {
                            $parametros_modulo_sensores["secciones"] = NULL;
                        }
                        $seccion_defecto = ModuloSensores::dame_seccion_defecto($parametros_modulo_sensores["secciones"]);
                        $parametros_modulo_sensores["seccion_defecto"] = $seccion_defecto;
                        $_SESSION["modulos"][MODULO_SENSORES] = $parametros_modulo_sensores;

                        // Inicialización de parámetros del módulo Sensores del usuario
                        inicializa_parametros_modulo_sensores_usuario();
                        break;
                    }
                    case MODULO_ACTUADORES:
                    {
                        $parametros_modulo_actuadores = array(
                            "nombre" => NOMBRE_MODULO_ACTUADORES
                        );
                        if ($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR)
                        {
                            $parametros_modulo_actuadores["secciones"] = $secciones[MODULO_ACTUADORES];
                        }
                        else
                        {
                            $parametros_modulo_actuadores["secciones"] = NULL;
                        }
                        $seccion_defecto = ModuloActuadores::dame_seccion_defecto($parametros_modulo_actuadores["secciones"]);
                        $parametros_modulo_actuadores["seccion_defecto"] = $seccion_defecto;
                        $_SESSION["modulos"][MODULO_ACTUADORES] = $parametros_modulo_actuadores;

                        // Inicialización de parámetros del módulo Actuadores del usuario
                        inicializa_parametros_modulo_actuadores_usuario();
                        break;
                    }
                    case MODULO_SMARTMETER:
                    {
                        $parametros_modulo_smartmeter = array(
                            "nombre" => NOMBRE_MODULO_SMARTMETER
                        );
                        if ($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR)
                        {
                            $parametros_modulo_smartmeter["secciones"] = $secciones[MODULO_SMARTMETER];
                        }
                        else
                        {
                            $parametros_modulo_smartmeter["secciones"] = NULL;
                        }
                        $seccion_defecto = ModuloSmartmeter::dame_seccion_defecto($parametros_modulo_smartmeter["secciones"]);
                        $parametros_modulo_smartmeter["seccion_defecto"] = $seccion_defecto;
                        $_SESSION["modulos"][MODULO_SMARTMETER] = $parametros_modulo_smartmeter;
                        break;
                    }
                    case MODULO_PROYECTOS:
                    {
                        $parametros_modulo_proyectos = array(
                            "nombre" => NOMBRE_MODULO_PROYECTOS
                        );
                        if ($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR)
                        {
                            $parametros_modulo_proyectos["secciones"] = $secciones[MODULO_PROYECTOS];
                        }
                        else
                        {
                            $parametros_modulo_proyectos["secciones"] = NULL;
                        }
                        $seccion_defecto = ModuloProyectos::dame_seccion_defecto($parametros_modulo_proyectos["secciones"]);
                        $parametros_modulo_proyectos["seccion_defecto"] = $seccion_defecto;
                        $_SESSION["modulos"][MODULO_PROYECTOS] = $parametros_modulo_proyectos;
                        break;
                    }
                }
            }
        }

        // Se establece la localización por defecto (aunque no se tenga el módulo Localizaciones)
        // - Nota: Si hay localización establecida no se modifica (se está abriendo una nueva pestaña en el navegador)
        if (!isset($_SESSION["id_localizacion"]))
        {
            establece_localizacion_defecto();
        }
    }


    // Se recupera el menú de módulos
    function dame_menu_modulos($modulo_seleccionado = "")
    {
        try
        {
            $idiomas = new Idiomas();

            $menu = "";

            // Se inicializan los módulos
            inicializa_modulos();
            $modulos = $_SESSION["modulos"];

            // Si el usuario es estándar o administrador se recuperan el número de redes del usuario
            if (($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR) || ($_SESSION["perfil"] == PERFIL_USUARIO_ADMINISTRADOR))
            {
                $ids_redes = dame_ids_redes_usuario($_SESSION["id_usuario"], $_SESSION["perfil"]);
                $numero_redes = count($ids_redes);
            }

            // Módulo por defecto
            switch ($_SESSION["perfil"])
            {
                case PERFIL_USUARIO_ESTANDAR:
                {
                    if (dame_modulo_disponible_sesion(MODULO_PERSONAL) == true)
                    {
                        $mostrar_modulos_diferentes_modulo_personal = ($_SESSION["parametros_modulo_personal"]["mostrar_otros_modulos"] == VALOR_SI);
                    }
                    else
                    {
                        $mostrar_modulos_diferentes_modulo_personal = true;
                    }
                    if ($numero_redes == 1)
                    {
                        if ((dame_modulo_disponible_sesion($_SESSION["modulo_defecto"]) == true) &&
                            (dame_seccion_disponible_sesion($_SESSION["modulo_defecto"], $_SESSION["seccion_defecto"] == true)))
                        {
                            if (($mostrar_modulos_diferentes_modulo_personal == true) ||
                                (($_SESSION["modulo_defecto"] == MODULO_PERSONAL) || ($_SESSION["modulo_defecto"] == MODULO_ADMINISTRACION)))
                            {
                                $modulo_defecto = $_SESSION["modulo_defecto"];
                                $seccion_defecto = $_SESSION["seccion_defecto"];
                            }
                            else
                            {
                                $modulo_defecto = MODULO_ADMINISTRACION;
                                $seccion_defecto = SECCION_ADMINISTRACION_USUARIOS;
                            }
                        }
                        else
                        {
                            $modulo_defecto = MODULO_ADMINISTRACION;
                            $seccion_defecto = SECCION_ADMINISTRACION_USUARIOS;
                        }
                    }
                    else
                    {
                        $modulo_defecto = MODULO_ADMINISTRACION;
                        $seccion_defecto = SECCION_ADMINISTRACION_SELECCION_RED;
                    }
                    break;
                }
                case PERFIL_USUARIO_ADMINISTRADOR:
                {
                    $mostrar_modulos_diferentes_modulo_personal = true;
                    if ($numero_redes == 1)
                    {
                        if (dame_modulo_disponible_sesion($_SESSION["modulo_defecto"]) == true)
                        {
                            $modulo_defecto = $_SESSION["modulo_defecto"];
                            $seccion_defecto = $_SESSION["seccion_defecto"];
                        }
                        else
                        {
                            $modulo_defecto = MODULO_ADMINISTRACION;
                            $seccion_defecto = SECCION_ADMINISTRACION_USUARIOS;
                        }
                    }
                    else
                    {
                        $modulo_defecto = MODULO_ADMINISTRACION;
                        $seccion_defecto = SECCION_ADMINISTRACION_SELECCION_RED;
                    }
                    break;
                }
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    $mostrar_modulos_diferentes_modulo_personal = true;
                    $modulo_defecto = MODULO_ADMINISTRACION;
                    $seccion_defecto = SECCION_ADMINISTRACION_SELECCION_RED;
                    break;
                }
            }

            // Se añaden los módulos al menú de módulos
            $menu .= "<div class='menu-modulos-opciones-modulos'>";
            foreach ($modulos as $modulo => $parametros_modulo)
            {
                // Se comprueba si sólo hay que mostrar el módulo personal (el módulo administración se muestra siempre)
                if (($mostrar_modulos_diferentes_modulo_personal == false) &&
                    (($modulo != MODULO_PERSONAL) && ($modulo != MODULO_ADMINISTRACION)))
                {
                    continue;
                }

                // Se añade el módulo por defecto (oculto)
                if ($modulo == $modulo_defecto)
                {
                    $menu .= "<div id='modulo_seccion_defecto' modulo='".$modulo."' seccion='".$seccion_defecto."' hidden></div>";
                }

                // Se añade el módulo al menú de módulos
                $menu .=
                    "<div class='menu-modulos-opcion-modulo elemento-no-seleccionable";
                if ($modulo_seleccionado == $modulo)
                {
                    $menu .= " modulo-actual";
                }
                $menu .= "' id='modulo-".$modulo."'>
                        <a href='#".$modulo."#".$parametros_modulo["seccion_defecto"]."'>".$idiomas->_($parametros_modulo["nombre"])."</a>
                    </div>";
            }
            $menu .= "</div>";
            $menu .= "<div class='menu-modulos-opcion-salir elemento-no-seleccionable pull-right'>".$idiomas->_("Salir")."</div>";

            // Se añaden el perfil y la red actual (ocultos)
            $menu .= "<div id='perfil_usuario_actual' perfil='".$_SESSION["perfil"]."' hidden></div>";
            $menu .= "<div id='id_red_actual' id_red='".$_SESSION["id_red"]."' hidden></div>";

            // Se añade si hay licencias desactivadas (oculto)
            $numero_licencias_desactivadas = dame_numero_licencias_desactivadas_modulos_usuario(
                $_SESSION["id_usuario"],
                $_SESSION["perfil"],
                $_SESSION["id_red"]);
            if ($numero_licencias_desactivadas > 0)
            {
                $menu .= "<div id='licencias_desactivadas_modulos' hidden></div>";
            }

            return ($menu);
        }
        catch (Exception $exception)
        {
            // Se añade información de la excepción en el log
            $log = dame_log();
            $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $exception);

            return (NULL);
        }
    }


    // Función de autenticación del usuario
    function autentica_usuario($id_usuario, $contrasenya)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $log = dame_log();

        // Se recupera la información del usuario
        $consulta_usuario = "
            SELECT *
            FROM usuarios
            WHERE
                id = '".$bd_red->_($id_usuario)."'";
        $res_usuario = $bd_red->ejecuta_consulta($consulta_usuario);
        if ($res_usuario == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_usuario."'");
        }
        if ($res_usuario->dame_numero_filas() == 0)
        {
            $log->info("[".$id_usuario."] "."Usuario desconocido");

            $res = "ERROR";
            $msg = $idiomas->_("Credenciales incorrectas");
        }
        else
        {
            $fila_usuario = $res_usuario->dame_siguiente_fila();
            $contrasenya_encriptada = $fila_usuario['contrasenya'];
            $contrasenya_correcta = (crypt($contrasenya, $contrasenya_encriptada) == $contrasenya_encriptada);
            $utilizada_contrasenya_superadministrador = false;
            $utilizada_contrasenya_administrador = false;
            if ($contrasenya_correcta == false)
            {
                // Comprobación de contraseña de usuario 'superadministrador'
                $consulta_usuarios_superadministradores = "
                    SELECT
                        id,
                        contrasenya
                    FROM usuarios
                    WHERE
                        perfil = '".PERFIL_USUARIO_SUPERADMINISTRADOR."'";
                $res_usuarios_superadministradores = $bd_red->ejecuta_consulta($consulta_usuarios_superadministradores);
                if ($res_usuarios_superadministradores == false)
                {
                    throw new Exception("Error en la consulta: '".$consulta_usuarios_superadministradores."'");
                }
                while ($fila_usuario_superadministrador = $res_usuarios_superadministradores->dame_siguiente_fila())
                {
                    $contrasenya_encriptada_superadministrador = $fila_usuario_superadministrador['contrasenya'];
                    $contrasenya_superadministrador_correcta = (crypt($contrasenya, $contrasenya_encriptada_superadministrador) == $contrasenya_encriptada_superadministrador);
                    if ($contrasenya_superadministrador_correcta == true)
                    {
                        $contrasenya_correcta = true;
                        $utilizada_contrasenya_superadministrador = true;
                    }
                }

                // Comprobación de contraseña de usuario 'administrador' (si el usuario es estándar)
                if (($contrasenya_correcta == false) && ($fila_usuario["perfil"] == PERFIL_USUARIO_ESTANDAR))
                {
                    // Se recuperan los identificadores de las redes asociadas a este usuario
                    $ids_redes = dame_ids_redes_usuario($id_usuario, PERFIL_USUARIO_ESTANDAR);

                    // Se recuperan los usuarios administradores
                    $consulta_usuarios_administradores = "
                        SELECT
                            id,
                            contrasenya
                        FROM usuarios
                        WHERE
                            perfil = '".PERFIL_USUARIO_ADMINISTRADOR."'";
                    $res_usuarios_administradores = $bd_red->ejecuta_consulta($consulta_usuarios_administradores);
                    if ($res_usuarios_administradores == false)
                    {
                        throw new Exception("Error en la consulta: '".$consulta_usuarios_administradores."'");
                    }
                    while ($fila_usuario_administrador = $res_usuarios_administradores->dame_siguiente_fila())
                    {
                        $id_usuario_administrador = $fila_usuario_administrador['id'];
                        $contrasenya_encriptada_administrador = $fila_usuario_administrador['contrasenya'];
                        $contrasenya_administrador_correcta = (crypt($contrasenya, $contrasenya_encriptada_administrador) == $contrasenya_encriptada_administrador);

                        // Se comprueba si el usuario administrador tiene al menos las redes del usuario
                        if ($contrasenya_administrador_correcta == true)
                        {
                            $ids_redes_usuario_administrador = dame_ids_redes_usuario($id_usuario_administrador, PERFIL_USUARIO_ADMINISTRADOR);
                            $numero_redes_usuario_no_administrador = count(array_diff($ids_redes, $ids_redes_usuario_administrador));
                            if ($numero_redes_usuario_no_administrador == 0)
                            {
                                $contrasenya_correcta = true;
                                $utilizada_contrasenya_administrador = true;
                                break;
                            }
                        }
                    }
                }
            }
            if ($contrasenya_correcta == true)
            {
                // Usuario y perfil
                $_SESSION["id_usuario"] = $id_usuario;
                $_SESSION["perfil"] = $fila_usuario['perfil'];

                // Preferencias
                $_SESSION["idioma"] = $fila_usuario['idioma'];
                $_SESSION["tamanyo_letra"] = $fila_usuario['tamanyo_letra'];
                $_SESSION["pantalla_completa_inicio"] = ($fila_usuario['pantalla_completa_inicio'] == VALOR_SI);

                // Preferencias de módulos
                $preferencias_modulos = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_usuario['preferencias_modulos']);
                $modo_seleccion_localizacion_actual = $preferencias_modulos[INDICE_PREFERENCIAS_MODULOS_USUARIO_MODO_SELECCION_LOCALIZACION_ACTUAL];
                $modulo_defecto = $preferencias_modulos[INDICE_PREFERENCIAS_MODULOS_USUARIO_MODULO_DEFECTO];
                $seccion_defecto = $preferencias_modulos[INDICE_PREFERENCIAS_MODULOS_USUARIO_SECCION_DEFECTO];
                $accion_inicial = $preferencias_modulos[INDICE_PREFERENCIAS_MODULOS_USUARIO_ACCION_INICIAL];
                $cadena_parametros_accion_inicial = $preferencias_modulos[INDICE_PREFERENCIAS_MODULOS_USUARIO_PARAMETROS_ACCION_INICIAL];
                $_SESSION["modo_seleccion_localizacion_actual"] = $modo_seleccion_localizacion_actual;
                $_SESSION["modulo_defecto"] = $modulo_defecto;
                $_SESSION["seccion_defecto"] = $seccion_defecto;
                $_SESSION["accion_inicial"] = $accion_inicial;
                $parametros_accion_inicial = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_parametros_accion_inicial);
                switch ($accion_inicial)
                {
                    case ACCION_INICIAL_ACTUALIZACION_PERIODICA_WIDGETS:
                    {
                        $numero_segundos_actualizacion_periodica = $parametros_accion_inicial[0];
                        $parametros_accion_inicial = array(
                            $_SESSION["pantalla_completa_inicio"],
                            ID_NINGUNO,
                            $numero_segundos_actualizacion_periodica);
                        break;
                    }
                }
                $_SESSION["parametros_accion_inicial"] = $parametros_accion_inicial;

                // Se recupera la red del usuario
                switch ($_SESSION["perfil"])
                {
                    case PERFIL_USUARIO_ESTANDAR:
                    case PERFIL_USUARIO_ADMINISTRADOR:
                    {
                        $ids_redes = dame_ids_redes_usuario($_SESSION["id_usuario"], $_SESSION["perfil"]);
                        if (count($ids_redes) == 1)
                        {
                            $_SESSION["id_red"] = $ids_redes[0];
                        }
                        else
                        {
                            $_SESSION["id_red"] = ID_NINGUNO;
                        }
                        break;
                    }
                    case PERFIL_USUARIO_SUPERADMINISTRADOR:
                    {
                        $_SESSION["id_red"] = ID_NINGUNO;
                        break;
                    }
                }

                // Acciones al iniciar la sesión
                realiza_acciones_inicio_sesion(
                    $utilizada_contrasenya_superadministrador,
                    $utilizada_contrasenya_administrador);

                // Se guarda en la sesión si se ha utilizado la contraseña de administrador o superadministrador
                $_SESSION["utilizada_contrasenya_admin_superadmin"] =
                    ($utilizada_contrasenya_administrador == true) || ($utilizada_contrasenya_superadministrador == true);

                $res = "OK";
                $msg = "";
            }
            else
            {
                $log->info("[".$id_usuario."] "."Contraseña incorrecta");

                $res = "ERROR";
                $msg = $idiomas->_("Credenciales incorrectas");
            }
        }

        return (array(
            "res" => $res,
            "msg" => $msg));
    }


    // Acciones que se ejecutan al iniciar la sesión
    function realiza_acciones_inicio_sesion(
        $utilizada_contrasenya_superadministrador,
        $utilizada_contrasenya_administrador)
    {
        // Se cargan los parámetros de la red
        carga_parametros_red($_SESSION["id_red"]);

        // Se eliminan los ficheros temporales del usuario (si existen)
        elimina_ficheros_temporales_usuario($_SESSION["id_usuario"], false);

        // Se crea el directorio para los ficheros temporales del usuario (si no existe)
        crea_directorio_ficheros_temporales_usuario($_SESSION["id_usuario"]);

        // Añade la acción de usuario
        anyade_accion_usuario_inicio_sesion(
            $_SESSION["id_usuario"],
            $_SESSION["perfil"],
            $_SESSION["id_red"],
            $utilizada_contrasenya_superadministrador,
            $utilizada_contrasenya_administrador);
    }


    // Acciones que se ejecutan al salir de la sesión
    function realiza_acciones_fin_sesion()
    {
        // Se eliminan los ficheros temporales del usuario
        elimina_ficheros_temporales_usuario($_SESSION["id_usuario"], false);

        // Añade la acción de usuario
        anyade_accion_usuario_fin_sesion(
            $_SESSION["id_usuario"],
            $_SESSION["perfil"],
            $_SESSION["id_red"]);
    }


    // Crea el directorio de ficheros temporales del usuario (si no existe ya)
    function crea_directorio_ficheros_temporales_usuario($id_usuario)
    {
        $directorio_ficheros_temporales_usuario = $_SESSION["directorio"].'/rsc/ficheros/tmp/'.$id_usuario;
        if (file_exists($directorio_ficheros_temporales_usuario) == false)
        {
            // https://stackoverflow.com/questions/3997641/why-cant-php-create-a-directory-with-777-permissions
            $mascara_anterior = umask(0);
            $ret = mkdir($directorio_ficheros_temporales_usuario, 0777, true);
            umask($mascara_anterior);
            if ($ret == false)
            {
                throw new Exception("No se ha podido crear el directorio de ficheros temporales del usuario: '".$directorio_ficheros_temporales_usuario."'");
            }
        }
        return ($directorio_ficheros_temporales_usuario);
    }


    // Devuelve el directorio de ficheros temporales del usuario
    function dame_directorio_ficheros_temporales_usuario($id_usuario)
    {
        $directorio_ficheros_temporales_usuario = crea_directorio_ficheros_temporales_usuario($id_usuario);
        return ($directorio_ficheros_temporales_usuario);
    }


    // Elimina los ficheros temporales del usuario
    function elimina_ficheros_temporales_usuario($id_usuario, $eliminar_directorio)
    {
        $directorio_ficheros_temporales_usuario = $_SESSION["directorio"].'/rsc/ficheros/tmp/'.$id_usuario;
        if (file_exists($directorio_ficheros_temporales_usuario) == true)
        {
            elimina_ficheros_directorio($directorio_ficheros_temporales_usuario, $eliminar_directorio);
        }
    }


    // Devuelve si un módulo está disponible (en la sesión actual)
    function dame_modulo_disponible_sesion($modulo)
    {
        if (array_key_exists($modulo, $_SESSION["modulos"]) == true)
        {
            $modulo_disponible = true;
        }
        else
        {
            $modulo_disponible = false;
        }
        return ($modulo_disponible);
    }


    // Devuelve si una sección está disponible (en la sesión actual)
    function dame_seccion_disponible_sesion($modulo, $seccion)
    {
        // Se recuperan las secciones del módulo (todas)
        $modulo_web = dame_modulo_web($modulo);
        $secciones_modulo = $modulo_web->dame_secciones(NULL);
        if (in_array($seccion, $secciones_modulo) == true)
        {
            $existe_seccion = true;
        }
        else
        {
            $existe_seccion = false;
        }

        // Perfil del usuario
        switch ($_SESSION["perfil"])
        {
            case PERFIL_USUARIO_ESTANDAR:
            {
                switch ($modulo)
                {
                    case MODULO_ADMINISTRACION:
                    {
                        switch ($seccion)
                        {
                            case SECCION_ADMINISTRACION_USUARIOS:
                            case SECCION_ADMINISTRACION_SELECCION_RED:
                            {
                                $seccion_disponible = true;
                                break;
                            }
                            default:
                            {
                                $seccion_disponible = false;
                                break;
                            }
                        }
                        break;
                    }
                    default:
                    {
                        $secciones_usuario = dame_secciones_usuario($_SESSION["id_usuario"], $_SESSION["id_red"]);
                        $secciones_modulo_usuario = $secciones_usuario[$modulo];
                        if (count($secciones_modulo_usuario) == 0)
                        {
                            $secciones_modulo_usuario = $secciones_modulo;
                        }
                        if (in_array($seccion, $secciones_modulo_usuario) == true)
                        {
                            $seccion_disponible = true;
                        }
                        else
                        {
                            $seccion_disponible = false;
                        }
                        break;
                    }
                }
                break;
            }
            case PERFIL_USUARIO_ADMINISTRADOR:
            {
                switch ($modulo)
                {
                    case MODULO_ADMINISTRACION:
                    {
                        switch ($seccion)
                        {
                            case SECCION_ADMINISTRACION_USUARIOS:
                            case SECCION_ADMINISTRACION_SELECCION_RED:
                            {
                                $seccion_disponible = true;
                                break;
                            }
                            default:
                            {
                                $seccion_disponible = false;
                                break;
                            }
                        }
                        break;
                    }
                    default:
                    {
                        $seccion_disponible = $existe_seccion;
                    }
                }
                break;
            }
            case PERFIL_USUARIO_SUPERADMINISTRADOR:
            {
                $seccion_disponible = $existe_seccion;
                break;
            }
        }

        return ($seccion_disponible);
    }


    // Devuelve la descripción de la sección de un módulo
    function dame_descripcion_seccion_modulo($modulo, $seccion)
    {
        switch ($modulo)
        {
            case MODULO_PERSONAL:
            {
                $descripcion_seccion = ModuloPersonal::dame_descripcion_seccion($seccion);
                break;
            }
            case MODULO_RED:
            {
                $descripcion_seccion = ModuloRed::dame_descripcion_seccion($seccion);
                break;
            }
            case MODULO_LOCALIZACIONES:
            {
                $descripcion_seccion = ModuloLocalizaciones::dame_descripcion_seccion($seccion);
                break;
            }
            case MODULO_SENSORES:
            {
                $descripcion_seccion = ModuloSensores::dame_descripcion_seccion($seccion);
                break;
            }
            case MODULO_ACTUADORES:
            {
                $descripcion_seccion = ModuloActuadores::dame_descripcion_seccion($seccion);
                break;
            }
            case MODULO_SMARTMETER:
            {
                $descripcion_seccion = ModuloSmartmeter::dame_descripcion_seccion($seccion);
                break;
            }
            case MODULO_PROYECTOS:
            {
                $descripcion_seccion = ModuloProyectos::dame_descripcion_seccion($seccion);
                break;
            }
            default:
            {
                throw new Exception("Modulo desconocido: '".$modulo."'");
            }
        }
        return ($descripcion_seccion);
    }


    //
    // Funciones de parámetros de módulos
    //


    // Se leen y guardan en la sesión los parámetros del módulo Personal del usuario actual
    function inicializa_parametros_modulo_personal_usuario()
    {
        // Se leen de base de datos los parámetros del módulo del usuario
        if ($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta_parametros_modulo_personal = "
                SELECT
                    parametros
                FROM modulos_usuarios
                WHERE
                    (modulo = '".MODULO_PERSONAL."')
                    AND (usuario = '".$_SESSION["id_usuario"]."')
                    AND (red = '".$_SESSION["id_red"]."')";
            $res_parametros_modulo_personal = $bd_red->ejecuta_consulta($consulta_parametros_modulo_personal);
            if (($res_parametros_modulo_personal == false) || ($res_parametros_modulo_personal->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_parametros_modulo_personal."'");
            }
            $fila_parametros_modulo_personal = $res_parametros_modulo_personal->dame_siguiente_fila();

            $cadena_parametros_modulo_personal = $fila_parametros_modulo_personal["parametros"];
            $parametros_modulo_personal = dame_parametros_modulo_personal_usuario($cadena_parametros_modulo_personal);
            $_SESSION["parametros_modulo_personal"] = $parametros_modulo_personal;
        }
    }


    // Se leen y guardan en la sesión los parámetros del módulo Localizaciones del usuario actual
    function inicializa_parametros_modulo_localizaciones_usuario()
    {
        // Se leen de base de datos los parámetros del módulo del usuario
        if ($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta_parametros_modulo_localizaciones = "
                SELECT
                    parametros
                FROM modulos_usuarios
                WHERE
                    (modulo = '".MODULO_LOCALIZACIONES."')
                    AND (usuario = '".$_SESSION["id_usuario"]."')
                    AND (red = '".$_SESSION["id_red"]."')";
            $res_parametros_modulo_localizaciones = $bd_red->ejecuta_consulta($consulta_parametros_modulo_localizaciones);
            if (($res_parametros_modulo_localizaciones == false) || ($res_parametros_modulo_localizaciones->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_parametros_modulo_localizaciones."'");
            }
            $fila_parametros_modulo_localizaciones = $res_parametros_modulo_localizaciones->dame_siguiente_fila();

            $cadena_parametros_modulo_localizaciones = $fila_parametros_modulo_localizaciones["parametros"];
            $parametros_modulo_localizaciones = dame_parametros_modulo_localizaciones_usuario($cadena_parametros_modulo_localizaciones);
            $_SESSION["parametros_modulo_localizaciones"] = $parametros_modulo_localizaciones;
        }
    }


    // Se leen y guardan en la sesión los parámetros del módulo Sensores del usuario actual
    function inicializa_parametros_modulo_sensores_usuario()
    {
        // Se leen de base de datos los parámetros del módulo del usuario
        if ($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta_parametros_modulo_sensores = "
                SELECT parametros
                FROM modulos_usuarios
                WHERE
                    (modulo = '".MODULO_SENSORES."')
                    AND (usuario = '".$_SESSION["id_usuario"]."')
                    AND (red = '".$_SESSION["id_red"]."')";
            $res_parametros_modulo_sensores = $bd_red->ejecuta_consulta($consulta_parametros_modulo_sensores);
            if (($res_parametros_modulo_sensores == false) || ($res_parametros_modulo_sensores->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_parametros_modulo_sensores."'");
            }
            $fila_parametros_modulo_sensores = $res_parametros_modulo_sensores->dame_siguiente_fila();

            $cadena_parametros_modulo_sensores = $fila_parametros_modulo_sensores["parametros"];
            $parametros_modulo_sensores = dame_parametros_modulo_sensores_usuario($cadena_parametros_modulo_sensores);
            $_SESSION["parametros_modulo_sensores"] = $parametros_modulo_sensores;
        }
    }


    // Se leen y guardan en la sesión los parámetros del módulo Actuadores del usuario actual
    function inicializa_parametros_modulo_actuadores_usuario()
    {
        // Se leen de base de datos los parámetros del módulo del usuario
        if ($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            $consulta_parametros_modulo_actuadores = "
                SELECT
                    parametros
                FROM modulos_usuarios
                WHERE
                    (modulo = '".MODULO_ACTUADORES."')
                    AND (usuario = '".$_SESSION["id_usuario"]."')
                    AND (red = '".$_SESSION["id_red"]."')";
            $res_parametros_modulo_actuadores = $bd_red->ejecuta_consulta($consulta_parametros_modulo_actuadores);
            if (($res_parametros_modulo_actuadores == false) || ($res_parametros_modulo_actuadores->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_parametros_modulo_actuadores."'");
            }
            $fila_parametros_modulo_actuadores = $res_parametros_modulo_actuadores->dame_siguiente_fila();

            $cadena_parametros_modulo_actuadores = $fila_parametros_modulo_actuadores["parametros"];
            $parametros_modulo_actuadores = dame_parametros_modulo_actuadores_usuario($cadena_parametros_modulo_actuadores);
            $_SESSION["parametros_modulo_actuadores"] = $parametros_modulo_actuadores;
        }
    }


    // Añade los parámetros por defecto del módulo Personal de un usuario en la red especificada
    function anyade_parametros_defecto_modulo_personal_usuario($id_usuario, $id_red)
    {
        $parametros_modulo_personal = dame_parametros_defecto_modulo_personal();
        anyade_parametros_modulo_personal_usuario($id_usuario, $id_red, $parametros_modulo_personal);
    }


    // Añade los parámetros por defecto del módulo Localizaciones de un usuario en la red especificada
    function anyade_parametros_defecto_modulo_localizaciones_usuario($id_usuario, $id_red)
    {
        $parametros_modulo_localizaciones = dame_parametros_defecto_modulo_localizaciones();
        anyade_parametros_modulo_localizaciones_usuario($id_usuario, $id_red, $parametros_modulo_localizaciones);
    }


    // Añade los parámetros por defecto del módulo Sensores de un usuario en la red especificada
    function anyade_parametros_defecto_modulo_sensores_usuario($id_usuario, $id_red)
    {
        $parametros_modulo_sensores = dame_parametros_defecto_modulo_sensores();
        anyade_parametros_modulo_sensores_usuario($id_usuario, $id_red, $parametros_modulo_sensores);
    }


    // Añade los parámetros por defecto del módulo Actuadores de un usuario en la red especificada
    function anyade_parametros_defecto_modulo_actuadores_usuario($id_usuario, $id_red)
    {
        $parametros_modulo_actuadores = dame_parametros_defecto_modulo_actuadores();
        anyade_parametros_modulo_actuadores_usuario($id_usuario, $id_red, $parametros_modulo_actuadores);
    }


    // Devuelve los parámetros por defecto del módulo Personal
    function dame_parametros_defecto_modulo_personal()
    {
        $numero_maximo_informes_automaticos = -1;
        $administracion_widgets = VALOR_SI;
        $administracion_informes_automaticos = VALOR_NO;
        $administracion_plantillas_informes = VALOR_NO;
        $mostrar_otros_modulos = VALOR_NO;

        $parametros_defecto_modulo_personal = array(
            "numero_maximo_informes_automaticos" => $numero_maximo_informes_automaticos,
            "administracion_widgets" => $administracion_widgets,
            "administracion_informes_automaticos" => $administracion_informes_automaticos,
            "administracion_plantillas_informes" => $administracion_plantillas_informes,
            "mostrar_otros_modulos" => $mostrar_otros_modulos);
        return ($parametros_defecto_modulo_personal);
    }


    // Devuelve los parámetros por defecto del módulo Localizaciones
    function dame_parametros_defecto_modulo_localizaciones()
    {
        $permiso_todas_localizaciones = VALOR_SI;
        $ids_localizaciones = array();
        $administracion_localizaciones = VALOR_NO;
        $administracion_instalaciones = VALOR_NO;

        $parametros_defecto_modulo_localizaciones = array(
            "permiso_todas_localizaciones" => $permiso_todas_localizaciones,
            "ids_localizaciones" => $ids_localizaciones,
            "administracion_localizaciones" => $administracion_localizaciones,
            "administracion_instalaciones" => $administracion_instalaciones);
        return ($parametros_defecto_modulo_localizaciones);
    }


    // Devuelve los parámetros por defecto del módulo Sensores
    function dame_parametros_defecto_modulo_sensores()
    {
        $permiso_todos_sensores = VALOR_SI;
        $cadena_ids_sensores = "";
        $cadena_ids_grupos_sensores = "";
        $ids_sensores = array();
        $ids_grupos_sensores = array();
        $administracion_sensores = VALOR_NO;
        $administracion_comentarios_sensores = VALOR_NO;
        $lectura_sensores = VALOR_NO;
        $exportacion_sensores = VALOR_SI;
        $administracion_eventos = VALOR_NO;
        $envio_valores_manuales_sensores = VALOR_NO;

        $parametros_defecto_modulo_sensores = array(
            "permiso_todos_sensores" => $permiso_todos_sensores,
            "cadena_ids_sensores" => $cadena_ids_sensores,
            "cadena_ids_grupos_sensores" => $cadena_ids_grupos_sensores,
            "ids_sensores" => $ids_sensores,
            "ids_grupos_sensores" => $ids_grupos_sensores,
            "administracion_sensores" => $administracion_sensores,
            "administracion_comentarios_sensores" => $administracion_comentarios_sensores,
            "lectura_sensores" => $lectura_sensores,
            "exportacion_sensores" => $exportacion_sensores,
            "administracion_eventos" => $administracion_eventos,
            "envio_valores_manuales_sensores" => $envio_valores_manuales_sensores);
        return ($parametros_defecto_modulo_sensores);
    }


    // Devuelve los parámetros por defecto del módulo Actuadores
    function dame_parametros_defecto_modulo_actuadores()
    {
        $permiso_todos_actuadores = VALOR_SI;
        $cadena_ids_actuadores = "";
        $cadena_ids_grupos_actuadores = "";
        $ids_actuadores = array();
        $ids_grupos_actuadores = array();
        $administracion_actuadores = VALOR_NO;
        $administracion_comentarios_actuadores = VALOR_NO;
        $acciones_actuadores = VALOR_NO;
        $administracion_programaciones = VALOR_NO;
        $administracion_reglas = VALOR_NO;

        $parametros_defecto_modulo_actuadores = array(
            "permiso_todos_actuadores" => $permiso_todos_actuadores,
            "cadena_ids_actuadores" => $cadena_ids_actuadores,
            "cadena_ids_grupos_actuadores" => $cadena_ids_grupos_actuadores,
            "ids_actuadores" => $ids_actuadores,
            "ids_grupos_actuadores" => $ids_grupos_actuadores,
            "administracion_actuadores" => $administracion_actuadores,
            "administracion_comentarios_actuadores" => $administracion_comentarios_actuadores,
            "acciones_actuadores" => $acciones_actuadores,
            "administracion_programaciones" => $administracion_programaciones,
            "administracion_reglas" => $administracion_reglas);
        return ($parametros_defecto_modulo_actuadores);
    }


    // Añade los parámetros módulo Personal de un usuario en la red especificada
    function anyade_parametros_modulo_personal_usuario($id_usuario, $id_red, $parametros_modulo_personal)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $cadena_parametros_modulo_personal = dame_cadena_parametros_modulo_personal_usuario($parametros_modulo_personal);
        $operacion_insercion_parametros_modulo_personal = "
            INSERT INTO modulos_usuarios (
                modulo,
                usuario,
                red,
                parametros
            ) VALUES (
                '".MODULO_PERSONAL."',
                '".$bd_red->_($id_usuario)."',
                '".$bd_red->_($id_red)."',
                '".$bd_red->_($cadena_parametros_modulo_personal)."'
            )";
        $res_insercion_parametros_modulo_personal = $bd_red->ejecuta_operacion($operacion_insercion_parametros_modulo_personal);
        if ($res_insercion_parametros_modulo_personal == false)
        {
            throw new Exception("Error en la operación: '".$operacion_insercion_parametros_modulo_personal."'");
        }
    }


    // Añade los parámetros módulo Localizaciones de un usuario en la red especificada
    function anyade_parametros_modulo_localizaciones_usuario($id_usuario, $id_red, $parametros_modulo_localizaciones)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $cadena_parametros_modulo_localizaciones = dame_cadena_parametros_modulo_localizaciones_usuario($parametros_modulo_localizaciones);
        $operacion_insercion_parametros_modulo_localizaciones = "
            INSERT INTO modulos_usuarios (
                modulo,
                usuario,
                red,
                parametros
            ) VALUES (
                '".MODULO_LOCALIZACIONES."',
                '".$bd_red->_($id_usuario)."',
                '".$bd_red->_($id_red)."',
                '".$bd_red->_($cadena_parametros_modulo_localizaciones)."'
            )";
        $res_insercion_parametros_modulo_localizaciones = $bd_red->ejecuta_operacion($operacion_insercion_parametros_modulo_localizaciones);
        if ($res_insercion_parametros_modulo_localizaciones == false)
        {
            throw new Exception("Error en la operación: '".$operacion_insercion_parametros_modulo_localizaciones."'");
        }
    }


    // Añade los parámetros módulo Sensores de un usuario en la red especificada
    function anyade_parametros_modulo_sensores_usuario($id_usuario, $id_red, $parametros_modulo_sensores)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $cadena_parametros_modulo_sensores = dame_cadena_parametros_modulo_sensores_usuario($parametros_modulo_sensores);
        $operacion_insercion_parametros_modulo_sensores = "
            INSERT INTO modulos_usuarios (
                modulo,
                usuario,
                red,
                parametros
            ) VALUES (
                '".MODULO_SENSORES."',
                '".$bd_red->_($id_usuario)."',
                '".$bd_red->_($id_red)."',
                '".$bd_red->_($cadena_parametros_modulo_sensores)."'
            )";
        $res_insercion_parametros_modulo_sensores = $bd_red->ejecuta_operacion($operacion_insercion_parametros_modulo_sensores);
        if ($res_insercion_parametros_modulo_sensores == false)
        {
            throw new Exception("Error en la operación: '".$operacion_insercion_parametros_modulo_sensores."'");
        }
    }


    // Añade los parámetros módulo Actuadores de un usuario en la red especificada
    function anyade_parametros_modulo_actuadores_usuario($id_usuario, $id_red, $parametros_modulo_actuadores)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $cadena_parametros_modulo_actuadores = dame_cadena_parametros_modulo_actuadores_usuario($parametros_modulo_actuadores);
        $operacion_insercion_parametros_modulo_actuadores = "
            INSERT INTO modulos_usuarios (
                modulo,
                usuario,
                red,
                parametros
            ) VALUES (
                '".MODULO_ACTUADORES."',
                '".$bd_red->_($id_usuario)."',
                '".$bd_red->_($id_red)."',
                '".$bd_red->_($cadena_parametros_modulo_actuadores)."'
            )";
        $res_insercion_parametros_modulo_actuadores = $bd_red->ejecuta_operacion($operacion_insercion_parametros_modulo_actuadores);
        if ($res_insercion_parametros_modulo_actuadores == false)
        {
            throw new Exception("Error en la operación: '".$operacion_insercion_parametros_modulo_actuadores."'");
        }
    }


    // Modifica los parámetros módulo Personal de un usuario en la red especificada
    function modifica_parametros_modulo_personal_usuario($id_usuario, $id_red, $parametros_modulo_personal)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $operacion_borrado_parametros_modulo_personal = "
            DELETE
            FROM modulos_usuarios
            WHERE
                (modulo = '".MODULO_PERSONAL."')
                AND (usuario = '".$bd_red->_($id_usuario)."')
                AND (red = '".$bd_red->_($id_red)."')";
        $res_borrado_parametros_modulo_personal = $bd_red->ejecuta_operacion($operacion_borrado_parametros_modulo_personal);
        if ($res_borrado_parametros_modulo_personal == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_parametros_modulo_personal."'");
        }
        anyade_parametros_modulo_personal_usuario($id_usuario, $id_red, $parametros_modulo_personal);
    }


    // Modifica los parámetros módulo Localizaciones de un usuario en la red especificada
    function modifica_parametros_modulo_localizaciones_usuario($id_usuario, $id_red, $parametros_modulo_localizaciones)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $operacion_borrado_parametros_modulo_localizaciones = "
            DELETE
            FROM modulos_usuarios
            WHERE
                (modulo = '".MODULO_LOCALIZACIONES."')
                AND (usuario = '".$bd_red->_($id_usuario)."')
                AND (red = '".$bd_red->_($id_red)."')";
        $res_borrado_parametros_modulo_localizaciones = $bd_red->ejecuta_operacion($operacion_borrado_parametros_modulo_localizaciones);
        if ($res_borrado_parametros_modulo_localizaciones == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_parametros_modulo_localizaciones."'");
        }
        anyade_parametros_modulo_localizaciones_usuario($id_usuario, $id_red, $parametros_modulo_localizaciones);
    }


    // Modifica los parámetros módulo Sensores de un usuario en la red especificada
    function modifica_parametros_modulo_sensores_usuario($id_usuario, $id_red, $parametros_modulo_sensores)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $operacion_borrado_parametros_modulo_sensores = "
            DELETE
            FROM modulos_usuarios
            WHERE
                (modulo = '".MODULO_SENSORES."')
                AND (usuario = '".$bd_red->_($id_usuario)."')
                AND (red = '".$bd_red->_($id_red)."')";
        $res_borrado_parametros_modulo_sensores = $bd_red->ejecuta_operacion($operacion_borrado_parametros_modulo_sensores);
        if ($res_borrado_parametros_modulo_sensores == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_parametros_modulo_sensores."'");
        }
        anyade_parametros_modulo_sensores_usuario($id_usuario, $id_red, $parametros_modulo_sensores);
    }


    // Modifica los parámetros módulo Actuadores de un usuario en la red especificada
    function modifica_parametros_modulo_actuadores_usuario($id_usuario, $id_red, $parametros_modulo_actuadores)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $operacion_borrado_parametros_modulo_actuadores = "
            DELETE
            FROM modulos_usuarios
            WHERE
                (modulo = '".MODULO_ACTUADORES."')
                AND (usuario = '".$bd_red->_($id_usuario)."')
                AND (red = ".$bd_red->_($id_red).")";
        $res_borrado_parametros_modulo_actuadores = $bd_red->ejecuta_operacion($operacion_borrado_parametros_modulo_actuadores);
        if ($res_borrado_parametros_modulo_actuadores == false)
        {
            throw new Exception("Error en la operación: '".$operacion_borrado_parametros_modulo_actuadores."'");
        }
        anyade_parametros_modulo_actuadores_usuario($id_usuario, $id_red, $parametros_modulo_actuadores);
    }


    //
    // Funciones de modificación automática de parámetros de módulos
    //


    // Añade la localización a los parámetros del módulo Localizaciones del usuario actual
    function anyade_localizacion_parametros_modulo_localizaciones_usuario_actual($id_localizacion)
    {
        $mostrar_todas_localizaciones = dame_mostrar_todas_localizaciones();
        if ($mostrar_todas_localizaciones == true)
        {
            return;
        }

        $ids_localizaciones = dame_ids_localizaciones_usuario_actual(false);
        array_push($ids_localizaciones, $id_localizacion);

        $permiso_todas_localizaciones = $_SESSION["parametros_modulo_localizaciones"]["permiso_todas_localizaciones"];
        $administracion_localizaciones = $_SESSION["parametros_modulo_localizaciones"]["administracion_localizaciones"];
        $administracion_instalaciones = $_SESSION["parametros_modulo_localizaciones"]["administracion_instalaciones"];

        $parametros_modulo_localizaciones = array(
            "permiso_todas_localizaciones" => $permiso_todas_localizaciones,
            "ids_localizaciones" => $ids_localizaciones,
            "administracion_localizaciones" => $administracion_localizaciones,
            "administracion_instalaciones" => $administracion_instalaciones);
        modifica_parametros_modulo_localizaciones_usuario($_SESSION["id_usuario"], $_SESSION["id_red"], $parametros_modulo_localizaciones);
        inicializa_parametros_modulo_localizaciones_usuario();
    }


    // Añade el sensor o el grupo de sensores a los parámetros del módulo Sensores del usuario actual
    function anyade_sensor_grupo_parametros_modulo_sensores_usuario_actual($tipo_nodo, $id_nodo)
    {
        $todos_sensores_visibles = (($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
            ($_SESSION["parametros_modulo_sensores"]["permiso_todos_sensores"] == VALOR_SI));
        if ($todos_sensores_visibles == true)
        {
            return;
        }

        $cadena_ids_sensores = $_SESSION["parametros_modulo_sensores"]["cadena_ids_sensores"];
        $cadena_ids_grupos_sensores = $_SESSION["parametros_modulo_sensores"]["cadena_ids_grupos_sensores"];
        $ids_sensores = dame_ids_cadena_ids_parametros_modulo($cadena_ids_sensores);
        $ids_grupos_sensores = dame_ids_cadena_ids_parametros_modulo($cadena_ids_grupos_sensores);
        switch ($tipo_nodo)
        {
            case TIPO_NODO_SENSOR:
            {
                array_push($ids_sensores, $id_nodo);
                sort($ids_sensores);
                $cadena_ids_sensores = dame_cadena_ids_parametros_modulo_ids($ids_sensores);
                break;
            }
            case TIPO_NODO_GRUPO_SENSORES:
            {
                array_push($ids_grupos_sensores, $id_nodo);
                sort($ids_grupos_sensores);
                $cadena_ids_grupos_sensores = dame_cadena_ids_parametros_modulo_ids($ids_grupos_sensores);
                break;
            }
        }

        $permiso_todos_sensores = $_SESSION["parametros_modulo_sensores"]["permiso_todos_sensores"];
        $administracion_sensores = $_SESSION["parametros_modulo_sensores"]["administracion_sensores"];
        $administracion_comentarios_sensores = $_SESSION["parametros_modulo_sensores"]["administracion_comentarios_sensores"];
        $lectura_sensores = $_SESSION["parametros_modulo_sensores"]["lectura_sensores"];
        $exportacion_sensores = $_SESSION["parametros_modulo_sensores"]["exportacion_sensores"];
        $administracion_eventos = $_SESSION["parametros_modulo_sensores"]["administracion_eventos"];
        $envio_valores_manuales_sensores = $_SESSION["parametros_modulo_sensores"]["envio_valores_manuales_sensores"];

        $parametros_modulo_sensores = array(
            "permiso_todos_sensores" => $permiso_todos_sensores,
            "cadena_ids_sensores" => $cadena_ids_sensores,
            "cadena_ids_grupos_sensores" => $cadena_ids_grupos_sensores,
            "ids_sensores" => $ids_sensores,
            "ids_grupos_sensores" => $ids_grupos_sensores,
            "administracion_sensores" => $administracion_sensores,
            "administracion_comentarios_sensores" => $administracion_comentarios_sensores,
            "lectura_sensores" => $lectura_sensores,
            "exportacion_sensores" => $exportacion_sensores,
            "administracion_eventos" => $administracion_eventos,
            "envio_valores_manuales_sensores" => $envio_valores_manuales_sensores);
        modifica_parametros_modulo_sensores_usuario($_SESSION["id_usuario"], $_SESSION["id_red"], $parametros_modulo_sensores);
        inicializa_parametros_modulo_sensores_usuario();
    }


    // Añade el actuador o el grupo de actuadores a los parámetros del módulo Actuadores del usuario actual
    function anyade_actuador_grupo_parametros_modulo_actuadores_usuario_actual($tipo_nodo, $id_nodo)
    {
        $todos_actuadores_visibles = (($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR) ||
            ($_SESSION["parametros_modulo_actuadores"]["permiso_todos_actuadores"] == VALOR_SI));
        if ($todos_actuadores_visibles == true)
        {
            return;
        }

        $cadena_ids_actuadores = $_SESSION["parametros_modulo_actuadores"]["cadena_ids_actuadores"];
        $cadena_ids_grupos_actuadores = $_SESSION["parametros_modulo_actuadores"]["cadena_ids_grupos_actuadores"];
        $ids_actuadores = dame_ids_cadena_ids_parametros_modulo($cadena_ids_actuadores);
        $ids_grupos_actuadores = dame_ids_cadena_ids_parametros_modulo($cadena_ids_grupos_actuadores);
        switch ($tipo_nodo)
        {
            case TIPO_NODO_ACTUADOR:
            {
                array_push($ids_actuadores, $id_nodo);
                sort($ids_actuadores);
                $cadena_ids_actuadores = dame_cadena_ids_parametros_modulo_ids($ids_actuadores);
                break;
            }
            case TIPO_NODO_GRUPO_ACTUADORES:
            {
                array_push($ids_grupos_actuadores, $id_nodo);
                sort($ids_grupos_actuadores);
                $cadena_ids_grupos_actuadores = dame_cadena_ids_parametros_modulo_ids($ids_grupos_actuadores);
                break;
            }
        }

        $permiso_todos_actuadores = $_SESSION["parametros_modulo_actuadores"]["permiso_todos_actuadores"];
        $administracion_actuadores = $_SESSION["parametros_modulo_actuadores"]["administracion_actuadores"];
        $administracion_comentarios_actuadores = $_SESSION["parametros_modulo_actuadores"]["administracion_comentarios_actuadores"];
        $acciones_actuadores = $_SESSION["parametros_modulo_actuadores"]["acciones_actuadores"];
        $administracion_programaciones = $_SESSION["parametros_modulo_actuadores"]["administracion_programaciones"];
        $administracion_reglas = $_SESSION["parametros_modulo_actuadores"]["administracion_reglas"];

        $parametros_modulo_actuadores = array(
            "permiso_todos_actuadores" => $permiso_todos_actuadores,
            "cadena_ids_actuadores" => $cadena_ids_actuadores,
            "cadena_ids_grupos_actuadores" => $cadena_ids_grupos_actuadores,
            "ids_actuadores" => $ids_actuadores,
            "ids_grupos_actuadores" => $ids_grupos_actuadores,
            "administracion_actuadores" => $administracion_actuadores,
            "administracion_comentarios_actuadores" => $administracion_comentarios_actuadores,
            "acciones_actuadores" => $acciones_actuadores,
            "administracion_programaciones" => $administracion_programaciones,
            "administracion_reglas" => $administracion_reglas);
        modifica_parametros_modulo_actuadores_usuario($_SESSION["id_usuario"], $_SESSION["id_red"], $parametros_modulo_actuadores);
        inicializa_parametros_modulo_actuadores_usuario();
    }


    // Elimina la localización de los parámetros del módulo Localizaciones de los usuarios
    function elimina_localizacion_parametros_modulo_localizaciones_usuarios($id_localizacion)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $parametros_usuario_actual_modificados = false;

        $consulta_parametros_modulo_localizaciones = "
            SELECT
                usuario,
                red,
                parametros
            FROM modulos_usuarios
            WHERE
                (modulo = '".MODULO_LOCALIZACIONES."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_parametros_modulo_localizaciones = $bd_red->ejecuta_consulta($consulta_parametros_modulo_localizaciones);
        if ($res_parametros_modulo_localizaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros_modulo_localizaciones."'");
        }
        while ($fila_parametros_modulo_localizaciones = $res_parametros_modulo_localizaciones->dame_siguiente_fila())
        {
            $id_usuario = $fila_parametros_modulo_localizaciones["usuario"];
            $id_red = $fila_parametros_modulo_localizaciones["red"];
            $cadena_parametros_modulo_localizaciones = $fila_parametros_modulo_localizaciones["parametros"];

            $parametros_modulo_localizaciones = dame_parametros_modulo_localizaciones_usuario($cadena_parametros_modulo_localizaciones);
            $ids_localizaciones = $parametros_modulo_localizaciones["ids_localizaciones"];
            if (in_array($id_localizacion, $ids_localizaciones) == false)
            {
                continue;
            }
            $clave = array_search($id_localizacion, $ids_localizaciones);
            unset($ids_localizaciones[$clave]);
            $parametros_modulo_localizaciones["ids_localizaciones"] = $ids_localizaciones;

            modifica_parametros_modulo_localizaciones_usuario($id_usuario, $id_red, $parametros_modulo_localizaciones);
            if (strtolower($id_usuario) == $_SESSION["id_usuario"])
            {
                $parametros_usuario_actual_modificados = true;
            }
        }

        // Se inicializan los parámetros del módulo Localizaciones del usuario actual (si es necesario)
        $mostrar_todas_localizaciones = dame_mostrar_todas_localizaciones();
        if ($mostrar_todas_localizaciones == true)
        {
            return;
        }
        if ($parametros_usuario_actual_modificados == true)
        {
            inicializa_parametros_modulo_localizaciones_usuario();
        }
    }


    // Elimina el sensor o el grupo de sensores de los parámetros del módulo Sensores de los usuarios
    function elimina_sensor_grupo_parametros_modulo_sensores_usuarios($tipo_nodo, $id_nodo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $parametros_usuario_actual_modificados = false;
        $consulta_parametros_modulo_sensores = "
            SELECT
                usuario,
                parametros
            FROM modulos_usuarios
            WHERE
                (modulo = '".MODULO_SENSORES."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_parametros_modulo_sensores = $bd_red->ejecuta_consulta($consulta_parametros_modulo_sensores);
        if ($res_parametros_modulo_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros_modulo_sensores."'");
        }
        while ($fila_parametros_modulo_sensores = $res_parametros_modulo_sensores->dame_siguiente_fila())
        {
            $id_usuario = $fila_parametros_modulo_sensores["usuario"];
            $cadena_parametros_modulo_sensores = $fila_parametros_modulo_sensores["parametros"];

            $parametros_modulo_sensores = dame_parametros_modulo_sensores_usuario($cadena_parametros_modulo_sensores);
            switch ($tipo_nodo)
            {
                case TIPO_NODO_SENSOR:
                {
                    $ids_sensores = $parametros_modulo_sensores["ids_sensores"];
                    if (in_array($id_nodo, $ids_sensores) == false)
                    {
                        continue;
                    }
                    $clave = array_search($id_nodo, $ids_sensores);
                    unset($ids_sensores[$clave]);
                    $parametros_modulo_sensores["ids_sensores"] = $ids_sensores;
                    $parametros_modulo_sensores["cadena_ids_sensores"] = dame_cadena_ids_parametros_modulo_ids($ids_sensores);
                    break;
                }
                case TIPO_NODO_GRUPO_SENSORES:
                {
                    $ids_grupos_sensores = $parametros_modulo_sensores["ids_grupos_sensores"];
                    if (in_array($id_nodo, $ids_grupos_sensores) == false)
                    {
                        continue;
                    }
                    $clave = array_search($id_nodo, $ids_grupos_sensores);
                    unset($ids_grupos_sensores[$clave]);
                    $parametros_modulo_sensores["ids_grupos_sensores"] = $ids_grupos_sensores;
                    $parametros_modulo_sensores["cadena_ids_grupos_sensores"] = dame_cadena_ids_parametros_modulo_ids($ids_grupos_sensores);
                    break;
                }
            }

            modifica_parametros_modulo_sensores_usuario($id_usuario, $_SESSION["id_red"], $parametros_modulo_sensores);
            if (strtolower($id_usuario) == $_SESSION["id_usuario"])
            {
                $parametros_usuario_actual_modificados = true;
            }
        }

        // Se inicializan los parámetros del módulo Sensores del usuario actual (si es necesario)
        $mostrar_todos_sensores = dame_mostrar_todos_sensores();
        if ($mostrar_todos_sensores == true)
        {
            return;
        }
        if ($parametros_usuario_actual_modificados == true)
        {
            inicializa_parametros_modulo_sensores_usuario();
        }
    }


    // Elimina el actuador o el grupo de actuadores de los parámetros del módulo Actuadores de los usuarios
    function elimina_actuador_grupo_parametros_modulo_actuadores_usuarios($tipo_nodo, $id_nodo)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $parametros_usuario_actual_modificados = false;
        $consulta_parametros_modulo_actuadores = "
            SELECT
                usuario,
                parametros
            FROM modulos_usuarios
            WHERE
                (modulo = '".MODULO_ACTUADORES."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_parametros_modulo_actuadores = $bd_red->ejecuta_consulta($consulta_parametros_modulo_actuadores);
        if ($res_parametros_modulo_actuadores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros_modulo_actuadores."'");
        }
        while ($fila_parametros_modulo_actuadores = $res_parametros_modulo_actuadores->dame_siguiente_fila())
        {
            $id_usuario = $fila_parametros_modulo_actuadores["usuario"];
            $cadena_parametros_modulo_actuadores = $fila_parametros_modulo_actuadores["parametros"];

            $parametros_modulo_actuadores = dame_parametros_modulo_actuadores_usuario($cadena_parametros_modulo_actuadores);
            switch ($tipo_nodo)
            {
                case TIPO_NODO_ACTUADOR:
                {
                    $ids_actuadores = $parametros_modulo_actuadores["ids_actuadores"];
                    if (in_array($id_nodo, $ids_actuadores) == false)
                    {
                        continue;
                    }
                    $clave = array_search($id_nodo, $ids_actuadores);
                    unset($ids_actuadores[$clave]);
                    $parametros_modulo_actuadores["ids_actuadores"] = $ids_actuadores;
                    $parametros_modulo_actuadores["cadena_ids_actuadores"] = dame_cadena_ids_parametros_modulo_ids($ids_actuadores);
                    break;
                }
                case TIPO_NODO_GRUPO_ACTUADORES:
                {
                    $ids_grupos_actuadores = $parametros_modulo_actuadores["ids_grupos_actuadores"];
                    if (in_array($id_nodo, $ids_grupos_actuadores) == false)
                    {
                        continue;
                    }
                    $clave = array_search($id_nodo, $ids_grupos_actuadores);
                    unset($ids_grupos_actuadores[$clave]);
                    $parametros_modulo_actuadores["ids_grupos_actuadores"] = $ids_grupos_actuadores;
                    $parametros_modulo_actuadores["cadena_ids_grupos_actuadores"] = dame_cadena_ids_parametros_modulo_ids($ids_grupos_actuadores);
                    break;
                }
            }

            modifica_parametros_modulo_actuadores_usuario($id_usuario, $_SESSION["id_red"], $parametros_modulo_actuadores);
            if (strtolower($id_usuario) == $_SESSION["id_usuario"])
            {
                $parametros_usuario_actual_modificados = true;
            }
        }

        // Se inicializan los parámetros del módulo Actuadores del usuario actual (si es necesario)
        $mostrar_todos_actuadores = dame_mostrar_todos_actuadores();
        if ($mostrar_todos_actuadores == true)
        {
            return;
        }
        if ($parametros_usuario_actual_modificados == true)
        {
            inicializa_parametros_modulo_actuadores_usuario();
        }
    }


    //
    // Funciones de conversión de parámetros de módulos
    //


    // Se recuperan los parámetros de la cadena de parámetros del módulo Personal
    function dame_parametros_modulo_personal_usuario($cadena_parametros_modulo_personal)
    {
        $elementos_parametros_modulo_personal = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_modulo_personal);

        $parametros_modulo_personal = array();
        $parametros_modulo_personal["numero_maximo_informes_automaticos"] = $elementos_parametros_modulo_personal[INDICE_PARAMETRO_MODULO_PERSONAL_NUMERO_MAXIMO_INFORMES_AUTOMATICOS];
        $parametros_modulo_personal["administracion_widgets"] = $elementos_parametros_modulo_personal[INDICE_PARAMETRO_MODULO_PERSONAL_ADMINISTRACION_WIDGETS];
        $parametros_modulo_personal["administracion_plantillas_informes"] = $elementos_parametros_modulo_personal[INDICE_PARAMETRO_MODULO_PERSONAL_ADMINISTRACION_PLANTILLAS_INFORMES];
        $parametros_modulo_personal["administracion_informes_automaticos"] = $elementos_parametros_modulo_personal[INDICE_PARAMETRO_MODULO_PERSONAL_ADMINISTRACION_INFORMES_AUTOMATICOS];
        $parametros_modulo_personal["mostrar_otros_modulos"] = $elementos_parametros_modulo_personal[INDICE_PARAMETRO_MODULO_PERSONAL_MOSTRAR_OTROS_MODULOS];

        return ($parametros_modulo_personal);
    }


    // Devuelve la cadena de parámetros del módulo Personal de un usuario
    function dame_cadena_parametros_modulo_personal_usuario($parametros_modulo_personal)
    {
        $numero_maximo_informes_automaticos = $parametros_modulo_personal["numero_maximo_informes_automaticos"];
        $administracion_widgets = $parametros_modulo_personal["administracion_widgets"];
        $administracion_plantillas_informes = $parametros_modulo_personal["administracion_plantillas_informes"];
        $administracion_informes_automaticos = $parametros_modulo_personal["administracion_informes_automaticos"];
        $mostrar_otros_modulos = $parametros_modulo_personal["mostrar_otros_modulos"];

        $cadena_parametros_modulo_personal = implode(SEPARADOR_PARAMETROS_COMPUESTOS, array(
            $numero_maximo_informes_automaticos,
            $administracion_widgets,
            $administracion_plantillas_informes,
            $administracion_informes_automaticos,
            $mostrar_otros_modulos));
        return ($cadena_parametros_modulo_personal);
    }


    // Se recuperan los parámetros de la cadena de parámetros del módulo Localizaciones
    function dame_parametros_modulo_localizaciones_usuario($cadena_parametros_modulo_localizaciones)
    {
        $elementos_parametros_modulo_localizaciones = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_modulo_localizaciones);

        $parametros_modulo_localizaciones = array();
        $parametros_modulo_localizaciones["permiso_todas_localizaciones"] = $elementos_parametros_modulo_localizaciones[INDICE_PARAMETRO_MODULO_LOCALIZACIONES_PERMISO_TODAS_LOCALIZACIONES];
        $cadena_ids_localizaciones = $elementos_parametros_modulo_localizaciones[INDICE_PARAMETRO_MODULO_LOCALIZACIONES_IDS_LOCALIZACIONES];
        $parametros_modulo_localizaciones["administracion_localizaciones"] = $elementos_parametros_modulo_localizaciones[INDICE_PARAMETRO_MODULO_LOCALIZACIONES_ADMINISTRACION_LOCALIZACIONES];
        $parametros_modulo_localizaciones["administracion_instalaciones"] = $elementos_parametros_modulo_localizaciones[INDICE_PARAMETRO_MODULO_LOCALIZACIONES_ADMINISTRACION_INSTALACIONES];

        // Conversiones de cadenas de identificadores de parámetros
        $ids_localizaciones = dame_ids_cadena_ids_parametros_modulo($cadena_ids_localizaciones);
        $parametros_modulo_localizaciones["ids_localizaciones"] = $ids_localizaciones;

        return ($parametros_modulo_localizaciones);
    }


    // Devuelve la cadena de parámetros del módulo Localizaciones de un usuario
    function dame_cadena_parametros_modulo_localizaciones_usuario($parametros_modulo_localizaciones)
    {
        $permiso_todas_localizaciones = $parametros_modulo_localizaciones["permiso_todas_localizaciones"];
        $cadena_ids_localizaciones = dame_cadena_ids_parametros_modulo_ids($parametros_modulo_localizaciones["ids_localizaciones"]);
        $administracion_localizaciones = $parametros_modulo_localizaciones["administracion_localizaciones"];
        $administracion_instalaciones = $parametros_modulo_localizaciones["administracion_instalaciones"];

        $cadena_parametros_modulo_localizaciones = implode(SEPARADOR_PARAMETROS_COMPUESTOS, array(
            $permiso_todas_localizaciones,
            $cadena_ids_localizaciones,
            $administracion_localizaciones,
            $administracion_instalaciones));
        return ($cadena_parametros_modulo_localizaciones);
    }


    // Se recuperan los parámetros de la cadena de parámetros del módulo Sensores
    function dame_parametros_modulo_sensores_usuario($cadena_parametros_modulo_sensores)
    {
        $elementos_parametros_modulo_sensores = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_modulo_sensores);

        $parametros_modulo_sensores = array();
        $parametros_modulo_sensores["permiso_todos_sensores"] = $elementos_parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_PERMISO_TODOS_SENSORES];
        $parametros_modulo_sensores["cadena_ids_sensores"] = $elementos_parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_IDS_SENSORES];
        $parametros_modulo_sensores["cadena_ids_grupos_sensores"] = $elementos_parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_IDS_GRUPOS_SENSORES];
        $parametros_modulo_sensores["administracion_sensores"] = $elementos_parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_ADMINISTRACION_SENSORES];
        $parametros_modulo_sensores["administracion_comentarios_sensores"] = $elementos_parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_ADMINISTRACION_COMENTARIOS_SENSORES];
        $parametros_modulo_sensores["lectura_sensores"] = $elementos_parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_LECTURA_SENSORES];
        $parametros_modulo_sensores["exportacion_sensores"] = $elementos_parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_EXPORTACION_SENSORES];
        $parametros_modulo_sensores["administracion_eventos"] = $elementos_parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_ADMINISTRACION_EVENTOS];
        $parametros_modulo_sensores["envio_valores_manuales_sensores"] = $elementos_parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_ENVIO_VALORES_MANUALES_SENSORES];

        // Conversiones de cadenas de identificadores de parámetros
        $ids_sensores = dame_ids_cadena_ids_parametros_modulo($parametros_modulo_sensores["cadena_ids_sensores"]);
        $ids_grupos_sensores = dame_ids_cadena_ids_parametros_modulo($parametros_modulo_sensores["cadena_ids_grupos_sensores"]);
        $parametros_modulo_sensores["ids_sensores"] = $ids_sensores;
        $parametros_modulo_sensores["ids_grupos_sensores"] = $ids_grupos_sensores;

        return ($parametros_modulo_sensores);
    }


    // Devuelve la cadena de parámetros del módulo Sensores de un usuario
    function dame_cadena_parametros_modulo_sensores_usuario($parametros_modulo_sensores)
    {
        $permiso_todos_sensores = $parametros_modulo_sensores["permiso_todos_sensores"];
        $cadena_ids_sensores = $parametros_modulo_sensores["cadena_ids_sensores"];
        $cadena_ids_grupos_sensores = $parametros_modulo_sensores["cadena_ids_grupos_sensores"];
        $administracion_sensores = $parametros_modulo_sensores["administracion_sensores"];
        $administracion_comentarios_sensores = $parametros_modulo_sensores["administracion_comentarios_sensores"];
        $lectura_sensores = $parametros_modulo_sensores["lectura_sensores"];
        $exportacion_sensores = $parametros_modulo_sensores["exportacion_sensores"];
        $administracion_eventos = $parametros_modulo_sensores["administracion_eventos"];
        $envio_valores_manuales_sensores = $parametros_modulo_sensores["envio_valores_manuales_sensores"];

        $cadena_parametros_modulo_sensores = implode(SEPARADOR_PARAMETROS_COMPUESTOS, array(
            $permiso_todos_sensores,
            $cadena_ids_sensores,
            $cadena_ids_grupos_sensores,
            $administracion_sensores,
            $administracion_comentarios_sensores,
            $lectura_sensores,
            $exportacion_sensores,
            $administracion_eventos,
            $envio_valores_manuales_sensores));
        return ($cadena_parametros_modulo_sensores);
    }


    // Se recuperan los parámetros de la cadena de parámetros del módulo Actuadores
    function dame_parametros_modulo_actuadores_usuario($cadena_parametros_modulo_actuadores)
    {
        $elementos_parametros_modulo_actuadores = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_modulo_actuadores);

        $parametros_modulo_actuadores = array();
        $parametros_modulo_actuadores["permiso_todos_actuadores"] = $elementos_parametros_modulo_actuadores[INDICE_PARAMETRO_MODULO_ACTUADORES_PERMISO_TODOS_ACTUADORES];
        $parametros_modulo_actuadores["cadena_ids_actuadores"] = $elementos_parametros_modulo_actuadores[INDICE_PARAMETRO_MODULO_ACTUADORES_IDS_ACTUADORES];
        $parametros_modulo_actuadores["cadena_ids_grupos_actuadores"] = $elementos_parametros_modulo_actuadores[INDICE_PARAMETRO_MODULO_ACTUADORES_IDS_GRUPOS_ACTUADORES];
        $parametros_modulo_actuadores["administracion_actuadores"] = $elementos_parametros_modulo_actuadores[INDICE_PARAMETRO_MODULO_ACTUADORES_ADMINISTRACION_ACTUADORES];
        $parametros_modulo_actuadores["administracion_comentarios_actuadores"] = $elementos_parametros_modulo_actuadores[INDICE_PARAMETRO_MODULO_ACTUADORES_ADMINISTRACION_COMENTARIOS_ACTUADORES];
        $parametros_modulo_actuadores["acciones_actuadores"] = $elementos_parametros_modulo_actuadores[INDICE_PARAMETRO_MODULO_ACTUADORES_ACCIONES_ACTUADORES];
        $parametros_modulo_actuadores["administracion_programaciones"] = $elementos_parametros_modulo_actuadores[INDICE_PARAMETRO_MODULO_ACTUADORES_ADMINISTRACION_PROGRAMACIONES];
        $parametros_modulo_actuadores["administracion_reglas"] = $elementos_parametros_modulo_actuadores[INDICE_PARAMETRO_MODULO_ACTUADORES_ADMINISTRACION_REGLAS];

        // Conversiones de cadenas de identificadores de parámetros
        $ids_actuadores = dame_ids_cadena_ids_parametros_modulo($parametros_modulo_actuadores["cadena_ids_actuadores"]);
        $ids_grupos_actuadores = dame_ids_cadena_ids_parametros_modulo($parametros_modulo_actuadores["cadena_ids_grupos_actuadores"]);
        $parametros_modulo_actuadores["ids_actuadores"] = $ids_actuadores;
        $parametros_modulo_actuadores["ids_grupos_actuadores"] = $ids_grupos_actuadores;

        return ($parametros_modulo_actuadores);
    }


    // Devuelve la cadena de parámetros del módulo Actuadores de un usuario
    function dame_cadena_parametros_modulo_actuadores_usuario($parametros_modulo_actuadores)
    {
        $permiso_todos_actuadores = $parametros_modulo_actuadores["permiso_todos_actuadores"];
        $cadena_ids_actuadores = $parametros_modulo_actuadores["cadena_ids_actuadores"];
        $cadena_ids_grupos_actuadores = $parametros_modulo_actuadores["cadena_ids_grupos_actuadores"];
        $administracion_actuadores = $parametros_modulo_actuadores["administracion_actuadores"];
        $administracion_comentarios_actuadores = $parametros_modulo_actuadores["administracion_comentarios_actuadores"];
        $acciones_actuadores = $parametros_modulo_actuadores["acciones_actuadores"];
        $administracion_programaciones = $parametros_modulo_actuadores["administracion_programaciones"];
        $administracion_reglas = $parametros_modulo_actuadores["administracion_reglas"];

        $cadena_parametros_modulo_actuadores = implode(SEPARADOR_PARAMETROS_COMPUESTOS, array(
            $permiso_todos_actuadores,
            $cadena_ids_actuadores,
            $cadena_ids_grupos_actuadores,
            $administracion_actuadores,
            $administracion_comentarios_actuadores,
            $acciones_actuadores,
            $administracion_programaciones,
            $administracion_reglas));
        return ($cadena_parametros_modulo_actuadores);
    }


    // Devuelve los identificadores a partir de la cadena de identificadores de parámetros
    function dame_ids_cadena_ids_parametros_modulo($cadena_ids)
    {
        if ($cadena_ids == "")
        {
            $ids = array();
        }
        else
        {
            $ids = explode(SEPARADOR_PARAMETROS_SIMPLES, $cadena_ids);
        }
        return ($ids);
    }


    // Devuelve la cadena de identificadores de parámetros a partir de los identificadores
    function dame_cadena_ids_parametros_modulo_ids($ids)
    {
        $cadena_ids = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids);
        return ($cadena_ids);
    }


    //
    // Funciones de elementos no visibles
    //


    // Elimina y modifica los elementos no visibles de los parámetros de módulos de los usuarios
    function elimina_modifica_elementos_no_visibles_parametros_modulos_usuarios()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Se recuperan los parámetros de los módulos:
        // - Localizaciones
        // - Sensores
        // - Actuadores

        // Localizaciones
        $consulta_parametros_modulo_localizaciones = "
            SELECT
                usuario,
                parametros
            FROM modulos_usuarios
            WHERE
                (modulo = '".MODULO_LOCALIZACIONES."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_parametros_modulo_localizaciones = $bd_red->ejecuta_consulta($consulta_parametros_modulo_localizaciones);
        if ($res_parametros_modulo_localizaciones == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros_modulo_localizaciones."'");
        }
        $parametros_modulo_localizaciones = array();
        while ($fila_parametros_modulo_localizaciones = $res_parametros_modulo_localizaciones->dame_siguiente_fila())
        {
            $id_usuario = $fila_parametros_modulo_localizaciones["usuario"];
            $cadena_parametros = $fila_parametros_modulo_localizaciones["parametros"];

            $parametros_modulo_localizaciones[$id_usuario] = dame_parametros_modulo_localizaciones_usuario($cadena_parametros);
        }

        // Sensores
        $consulta_parametros_modulo_sensores = "
            SELECT
                usuario,
                parametros
            FROM modulos_usuarios
            WHERE
                (modulo = '".MODULO_SENSORES."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_parametros_modulo_sensores = $bd_red->ejecuta_consulta($consulta_parametros_modulo_sensores);
        if ($res_parametros_modulo_sensores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros_modulo_sensores."'");
        }
        $parametros_modulo_sensores = array();
        while ($fila_parametros_modulo_sensores = $res_parametros_modulo_sensores->dame_siguiente_fila())
        {
            $id_usuario = $fila_parametros_modulo_sensores["usuario"];
            $cadena_parametros = $fila_parametros_modulo_sensores["parametros"];

            $parametros_modulo_sensores[$id_usuario] = dame_parametros_modulo_sensores_usuario($cadena_parametros);
        }

        // Actuadores
        $consulta_parametros_modulo_actuadores = "
            SELECT
                usuario,
                parametros
            FROM modulos_usuarios
            WHERE
                (modulo = '".MODULO_ACTUADORES."')
                AND (red = '".$_SESSION["id_red"]."')";
        $res_parametros_modulo_actuadores = $bd_red->ejecuta_consulta($consulta_parametros_modulo_actuadores);
        if ($res_parametros_modulo_actuadores == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_parametros_modulo_actuadores."'");
        }
        $parametros_modulo_actuadores = array();
        while ($fila_parametros_modulo_actuadores = $res_parametros_modulo_actuadores->dame_siguiente_fila())
        {
            $id_usuario = $fila_parametros_modulo_actuadores["usuario"];
            $cadena_parametros = $fila_parametros_modulo_actuadores["parametros"];

            $parametros_modulo_actuadores[$id_usuario] = dame_parametros_modulo_actuadores_usuario($cadena_parametros);
        }

        // Se recuperan y se recorren todos los usuarios estándar:
        // - Se eliminan / modifican los elementos no visibles de cada uno de los usuarios
        $consulta_usuarios = "
            SELECT
                usuarios.id AS id
            FROM
                usuarios,
                redes_usuarios
            WHERE
                (usuarios.perfil = '".PERFIL_USUARIO_ESTANDAR."')
                AND (redes_usuarios.red = ".$_SESSION["id_red"].")
                AND (usuarios.id = redes_usuarios.usuario)";
        $res_usuarios = $bd_red->ejecuta_consulta($consulta_usuarios);
        if ($res_usuarios == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_usuarios."'");
        }
        while ($fila_usuario = $res_usuarios->dame_siguiente_fila())
        {
            $id_usuario = $fila_usuario["id"];

            // Parámetros del usuario para la eliminación de elementos dependientes de su configuración
            $parametros_usuario = array(
                "parametros_modulo_localizaciones" => $parametros_modulo_localizaciones[$id_usuario],
                "parametros_modulo_sensores" => $parametros_modulo_sensores[$id_usuario],
                "parametros_modulo_actuadores" => $parametros_modulo_actuadores[$id_usuario],
            );

            // Se eliminan y modifican los elementos no visibles de los parámetros de módulos del usuario
            elimina_modifica_elementos_no_visibles_parametros_modulos_usuario(
                $id_usuario,
                PERFIL_USUARIO_ESTANDAR,
                $_SESSION["id_red"],
                $parametros_usuario);
        }
    }


    // Elimina y modifica los elementos no visibles de los parámetros de módulos de un usuario
    function elimina_modifica_elementos_no_visibles_parametros_modulos_usuario(
        $id_usuario,
        $perfil,
        $id_red,
        $parametros_usuario)
    {
        // Se eliminan los 'widgets' no visibles de un usuario (si es necesario)
        elimina_widgets_no_visibles_usuario($id_usuario, $perfil, $id_red, $parametros_usuario);

        // Se eliminan y modifican los 'elementos de plantillas de informes' con elementos no visibles de un usuario (si es necesario)
        elimina_modifica_elementos_plantillas_informes_no_visibles_usuario($id_usuario, $perfil, $id_red, $parametros_usuario);

        // Se eliminan los 'informes automáticos' no visibles de un usuario (si es necesario)
        elimina_informes_automaticos_no_visibles_usuario($id_usuario, $perfil, $id_red, $parametros_usuario);

        // Se modifican los valores de los parámetros de los 'informes automáticos' de 'plantillas de informes' no visibles de un usuario (si es necesario)
        modifica_informes_automaticos_plantillas_informes_no_visibles_usuario($id_usuario, $perfil, $id_red, $parametros_usuario);
    }


    //
    // Funciones de localizaciones
    //


    // Establece la localización inicial por defecto
    function establece_localizacion_defecto()
    {
        // Localización por defecto
        $mostrar_controles_localizaciones = dame_mostrar_controles_localizaciones();
        if ($mostrar_controles_localizaciones == true)
        {
            switch ($_SESSION["perfil"])
            {
                case PERFIL_USUARIO_ESTANDAR:
                {
                    $ids_localizaciones_usuario = dame_ids_localizaciones_usuario_actual(false);
                    if (count($ids_localizaciones_usuario) == 1)
                    {
                        $modo_seleccion_localizacion_actual = $_SESSION["modo_seleccion_localizacion_actual"];
                        switch ($modo_seleccion_localizacion_actual)
                        {
                            case MODO_SELECCION_LOCALIZACION_ACTUAL_UNICA:
                            {
                                $id_localizacion_defecto = $ids_localizaciones_usuario[0];
                                break;
                            }
                            case MODO_SELECCION_LOCALIZACION_ACTUAL_MULTIPLE:
                            {
                                $id_localizacion_defecto = ID_LOCALIZACIONES_SELECCIONADAS_AND;
                                $ids_localizaciones_seleccionadas_defecto = array($ids_localizaciones_usuario[0]);
                                break;
                            }
                        }
                    }
                    else
                    {
                        $id_localizacion_defecto = ID_TODOS;
                        $ids_localizaciones_seleccionadas_defecto = array();
                    }

                    break;
                }
                case PERFIL_USUARIO_ADMINISTRADOR:
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    $id_localizacion_defecto = ID_DESACTIVADO;
                    $ids_localizaciones_seleccionadas_defecto = array();
                    break;
                }
            }
        }
        else
        {
            $id_localizacion_defecto = ID_DESACTIVADO;
            $ids_localizaciones_seleccionadas_defecto = array();
        }
        $_SESSION["id_localizacion"] = $id_localizacion_defecto;
        $_SESSION["ids_localizaciones_seleccionadas"] = $ids_localizaciones_seleccionadas_defecto;
    }


    //
    // Funciones de usuarios internos
    //


    // Carga la información del usuario interno
    function carga_informacion_usuario_interno($id_usuario, $fila_usuario)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Usuario
        $_SESSION["id_usuario"] = $id_usuario;

        // Se recupera la fila del usuario
        if ($fila_usuario === NULL)
        {
            $consulta_usuario = "
                SELECT *
                FROM usuarios
                WHERE
                    id = '".$bd_red->_($_SESSION["id_usuario"])."'";
            $res_usuario = $bd_red->ejecuta_consulta($consulta_usuario);
            if (($res_usuario == false) || ($res_usuario->dame_numero_filas() == 0))
            {
                throw new Exception("Error o no existe la información en la base de datos: '".$consulta_usuario."'");
            }
            $fila_usuario = $res_usuario->dame_siguiente_fila();
        }

        // Se guardan el perfil y el idioma del usuario
        $perfil_usuario = $fila_usuario['perfil'];
        $idioma_usuario = $fila_usuario['idioma'];
        $_SESSION["perfil"] = $perfil_usuario;
        $_SESSION["idioma"] = $idioma_usuario;

        // Carga los parámetros de los módulos del usuario interno
        carga_parametros_modulos_usuario_interno();

        // Por defecto sin localización seleccionada
        $_SESSION["id_localizacion"] = ID_DESACTIVADO;
    }


    // Se cargan los parámetros de los módulos del usuario interno (de los módulos necesarios)
    function carga_parametros_modulos_usuario_interno()
    {
        $_SESSION["modulos"] = array();

        // Se recuperan los módulos del usuario
        $modulos = dame_modulos_usuario(
            $_SESSION["id_usuario"],
            $_SESSION["perfil"],
            $_SESSION["id_red"]);

        // Se carga la información de los módulos necesarios
        switch ($_SESSION["usuario_interno"])
        {
            case USUARIO_INTERNO_SERVICIOS:
            case USUARIO_INTERNO_API_HTTP:
            {
                if (in_array(MODULO_LOCALIZACIONES, $modulos))
                {
                    inicializa_parametros_modulo_localizaciones_usuario();
                }
                if (in_array(MODULO_SENSORES, $modulos))
                {
                    inicializa_parametros_modulo_sensores_usuario();
                }
                if (in_array(MODULO_ACTUADORES, $modulos))
                {
                    inicializa_parametros_modulo_actuadores_usuario();
                }

                break;
            }
        }
    }


    //
    // Funciones de listas de usuarios
    //


    // Crea una lista desplegable para la selección de un perfil de usuario
    function dame_control_lista_perfiles_usuario($id_controles, $opciones_extra, $nombre_lista)
    {
        $control_lista_perfiles_usuario = "
            <div id='control_perfil_usuario_".$id_controles."'>";
        $control_lista_perfiles_usuario .= "
                <div id='etiqueta_perfil_usuario_".$id_controles."'>".htmlspecialchars($nombre_lista, ENT_QUOTES).": "."</div>";
        $control_lista_perfiles_usuario .= "
                <select id='perfil_usuario_".$id_controles."'";
        $control_lista_perfiles_usuario .= "
                    class='filtro-desplegable' hidden>";
        $control_lista_perfiles_usuario .= dame_lista_perfiles_usuario(NULL, $opciones_extra);
        $control_lista_perfiles_usuario .= "
                </select>";
        $control_lista_perfiles_usuario .= "
            </div>";

        return ($control_lista_perfiles_usuario);
    }


    // Devuelve la lista de usuarios con el perfil especificado
    function dame_lista_usuarios($id_perfil, $opciones_extra)
    {
        $idiomas = new Idiomas();
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_usuarios = "
            SELECT
                usuarios.id,
                usuarios.nombre
            FROM
                usuarios,
                redes_usuarios
            WHERE
                (redes_usuarios.red = ".$_SESSION["id_red"].")
                AND (usuarios.id = redes_usuarios.usuario)
                AND (usuarios.perfil = '".$bd_red->_($id_perfil)."')
            ORDER BY usuarios.nombre ASC";
        $res_usuarios = $bd_red->ejecuta_consulta($consulta_usuarios);
        if ($res_usuarios == false)
        {
            throw new Exception("Error en la consulta: '".$consulta_usuarios."'");
        }

        $lista_usuarios = "";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_USUARIOS_ACTUAL)
        {
            $lista_usuarios .= "<option value='".ID_NINGUNO."'>".$idiomas->_("Actual")."</option>";
        }
        while ($fila_usuario = $res_usuarios->dame_siguiente_fila())
        {
            $lista_usuarios .= "<option value='".htmlspecialchars($fila_usuario['id'], ENT_QUOTES)."'>".
                htmlspecialchars($fila_usuario['nombre']." (".$fila_usuario['id'].")", ENT_QUOTES)."</option>";
        }

        return ($lista_usuarios);
    }


    //
    // Funciones auxiliares
    //


    // Se recupera la fila del usuario
    function dame_fila_usuario($id_usuario)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_usuario = "
            SELECT *
            FROM usuarios
            WHERE
                id = '".$bd_red->_($id_usuario)."'";
        $res_usuario = $bd_red->ejecuta_consulta($consulta_usuario);
        if (($res_usuario == false) || ($res_usuario->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_usuario."'");
        }
        $fila_usuario = $res_usuario->dame_siguiente_fila();
        return ($fila_usuario);
    }


    // Se recupera el nombre del usuario
    function dame_nombre_usuario($id_usuario)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_usuario = "
            SELECT nombre
            FROM usuarios
            WHERE
                id = '".$bd_red->_($id_usuario)."'";
        $res_usuario = $bd_red->ejecuta_consulta($consulta_usuario);
        if (($res_usuario == false) || ($res_usuario->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_usuario."'");
        }
        $fila_usuario = $res_usuario->dame_siguiente_fila();
        $nombre_usuario = $fila_usuario['nombre'];
        return ($nombre_usuario);
    }


    // Se recupera la descripción del usuario
    function dame_descripcion_usuario()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_usuario = "
            SELECT nombre
            FROM usuarios
            WHERE
                id = '".$bd_red->_($_SESSION["id_usuario"])."'";
        $res_usuario = $bd_red->ejecuta_consulta($consulta_usuario);
        if (($res_usuario == false) || ($res_usuario->dame_numero_filas() == 0))
        {
            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_usuario."'");
        }
        $fila_usuario = $res_usuario->dame_siguiente_fila();
        $nombre_usuario = $fila_usuario['nombre'];
        $nombre_red = dame_nombre_red($_SESSION["id_red"]);
        $nombre_usuario_red = htmlspecialchars($nombre_usuario." (".$nombre_red.")", ENT_QUOTES);
        return ($nombre_usuario_red);
    }


    //
    // Funciones de acciones de usuario
    //


    // Añade la acción de usuario de inicio de sesión
    function anyade_accion_usuario_inicio_sesion(
        $id_usuario,
        $perfil_usuario,
        $id_red,
        $utilizada_contrasenya_superadministrador,
        $utilizada_contrasenya_administrador)
    {
        // Nombres de usuario y de red
        $nombre_usuario = dame_nombre_usuario($id_usuario);
        $nombre_red = dame_nombre_red($id_red);

        // Dirección IP del cliente
        $direccion_ip_cliente = dame_direccion_ip_cliente();

        // Tipo y objeto de la acción de usuario
        $tipo_accion_usuario = TIPO_ACCION_USUARIO_INICIO_SESION;
        $objeto_accion_usuario = $nombre_usuario;
        if ($id_red != ID_NINGUNO)
        {
            $objeto_accion_usuario .= " (".$nombre_red.")";
        }

        // Se añade la acción de usuario de inicio de sesión ('login')
        $parametros_accion_usuario = array();
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_USUARIO] = $nombre_usuario;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PERFIL_USUARIO] = $perfil_usuario;
        if ($utilizada_contrasenya_superadministrador == true)
        {
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_CONTRASENYA_USUARIO] = TIPO_CONTRASENYA_USUARIO_SUPERADMINISTRADOR;
        }
        else
        {
            if ($utilizada_contrasenya_administrador == true)
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_CONTRASENYA_USUARIO] = TIPO_CONTRASENYA_USUARIO_ADMINISTRADOR;
            }
            else
            {
                $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_TIPO_CONTRASENYA_USUARIO] = TIPO_CONTRASENYA_USUARIO_PERSONAL;
            }
        }
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_RED] = $nombre_red;
        $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_DIRECCION_IP] = $direccion_ip_cliente;


        // Se añade la acción de usuario
        anyade_accion_usuario(
            $tipo_accion_usuario,
            $objeto_accion_usuario,
            $parametros_accion_usuario,
            NULL,
            NULL);
    }


    // Añade la acción de usuario de fin de sesión
    function anyade_accion_usuario_fin_sesion(
        $id_usuario,
        $perfil_usuario,
        $id_red)
    {
        // Nota: Si se sale de sesión y se ha cambiado la ip de la base de datos en 'config.ini'
        // (sólo se hace en desarrollo, se captura la excepción para que no se muestre mensaje de error)
        try
        {
            // Nombres de usuario y de red
            $nombre_usuario = dame_nombre_usuario($id_usuario);
            $nombre_red = dame_nombre_red($id_red);

            // Tipo y objeto de la acción de usuario
            $tipo_accion_usuario = TIPO_ACCION_USUARIO_FIN_SESION;
            $objeto_accion_usuario = $nombre_usuario;
            if ($id_red != ID_NINGUNO)
            {
                $objeto_accion_usuario .= " (".$nombre_red.")";
            }

            // Se añade la acción de usuario de salida de sesión (logout)
            $parametros_accion_usuario = array();
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_USUARIO] = $nombre_usuario;
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_PERFIL_USUARIO] = $perfil_usuario;
            $parametros_accion_usuario[PARAMETRO_ACCION_USUARIO_NOMBRE_RED] = $nombre_red;

            // Se añade la acción de usuario
            anyade_accion_usuario(
                $tipo_accion_usuario,
                $objeto_accion_usuario,
                $parametros_accion_usuario,
                NULL,
                NULL);
        }
        catch (Exception $exception)
        {
            // Se añade información de la excepción en el log
            $log = dame_log();
            $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $exception);
        }
    }


    //
    // Funciones de permisos de usuario
    //


    // Devuelve los identificadores de los usuarios visibles para el usuario actual
    function dame_ids_usuarios_usuario_actual()
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        // Identificadores de usuarios
        $ids_usuarios = array();
        $consulta = Usuario::dame_consulta_usuarios("", PERFIL_USUARIO_TODOS);
        $res = $bd_red->ejecuta_consulta($consulta);
        if ($res == false)
        {
            throw new Exception("Error en la consulta: '".$consulta."'");
        }
        while ($fila = $res->dame_siguiente_fila())
        {
            $id_usuario = $fila["id"];
            array_push($ids_usuarios, $id_usuario);
        }
        return ($ids_usuarios);
    }
?>
