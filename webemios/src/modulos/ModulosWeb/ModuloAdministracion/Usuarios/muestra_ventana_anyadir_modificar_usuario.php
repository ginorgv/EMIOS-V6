<?php
	session_start();
    if (count($_SESSION) == 0)
    {
        print(json_encode(array("res" => "SESION_EXPIRADA")));
        exit();
    }

	include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/activa_captura_excepciones.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/AuditoriaAcciones.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/localizaciones/util_localizaciones.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/administracion/util_administracion_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/ModuloActuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/Usuario.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/util_administracion_usuarios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/ModuloPersonal.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloRed/ModuloRed.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/ModuloLocalizaciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/ModuloProyectos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/ModuloSensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ModuloSmartmeter.php');


    AuditoriaAcciones::comprobar_registrar_accion(ACCION_MOSTRAR_VENTANA_ANYADIR_MODIFICAR_USUARIO, $_POST);

    $idiomas = new Idiomas();

    $res = "";
    $msg = "";
	$titulo = "";
	$contenido = "";
	$pie = "";

    // Parámetros
    $id_usuario = $_POST["id_usuario"];
    if ($id_usuario === NULL)
    {
        $id_usuario = ID_NINGUNO;
    }
    $tipo_operacion_administracion = $_POST["tipo_operacion_administracion"];

    // Añadir o modificar usuario
    $anyadir_usuario = (($id_usuario == ID_NINGUNO) || ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO));
    if ($anyadir_usuario == true)
    {
        $titulo .= $idiomas->_("Añadir");
    }
    else
    {
        $titulo .= $idiomas->_("Modificar");
    }
    $pie .= '<button class="btn btn-success boton_administracion_anyadir_modificar_usuario">'.$titulo.'</button>';
    $pie .= '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$idiomas->_("Cancelar").'</button>';

    // Título
    $titulo .= " ".$idiomas->_("usuario");
    if (($anyadir_usuario == true) && ($tipo_operacion_administracion == TIPO_OPERACION_ADMINISTRACION_DUPLICADO))
    {
        $titulo .= " (".$idiomas->_("duplicar").")";
    }

    // Se recupera el contenido de la ventana
    $error = rellena_contenido_ventana_anyadir_modificar_usuario($anyadir_usuario, $id_usuario, $contenido);
    if ($error == "OK")
    {
        $res = "OK";
    }
    else
    {
        $res = "ERROR";
        $msg = $error;
    }

    print(json_encode(array(
        "res" => $res,
        "msg" => $msg,
        "titulo" => $titulo,
        "contenido" => $contenido,
        "pie" => $pie))
    );


	//
	// Funcion para mostrar el contenido de la ventana de anyadir/modificar usuario
	//


	// Función que rellena el contenido de la ventana de anyadir/modificar usuario
	function rellena_contenido_ventana_anyadir_modificar_usuario($anyadir_usuario, $id_usuario, &$contenido)
	{
		$idiomas = new Idiomas();
		$bd_red = BaseDatosRed::dame_base_datos();

        // Red actual
        $id_red_actual = $_SESSION["id_red"];

		// Si hay que modificar el usuario (o es un duplicado), se recupera la información actual de la base de datos
        if ($id_usuario != ID_NINGUNO)
		{
            $fila_usuario = dame_fila_usuario($id_usuario);

            // No se rellena la contraseña para evitar que se pueda recuperar la contraseña original con bases de datos de "hashes" MD5
            $contrasenya = "";
            $comprobacion_contrasenya = $contrasenya;
            $nombre = $fila_usuario["nombre"];
            $perfil = $fila_usuario["perfil"];
            $idioma = $fila_usuario["idioma"];
            $tamanyo_letra = $fila_usuario["tamanyo_letra"];
            $pantalla_completa_inicio = $fila_usuario["pantalla_completa_inicio"];
            $preferencias_modulos = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $fila_usuario["preferencias_modulos"]);
            $modo_seleccion_localizacion_actual = $preferencias_modulos[INDICE_PREFERENCIAS_MODULOS_USUARIO_MODO_SELECCION_LOCALIZACION_ACTUAL];
            $modulo_defecto = $preferencias_modulos[INDICE_PREFERENCIAS_MODULOS_USUARIO_MODULO_DEFECTO];
            $seccion_defecto = $preferencias_modulos[INDICE_PREFERENCIAS_MODULOS_USUARIO_SECCION_DEFECTO];
            $accion_inicial = $preferencias_modulos[INDICE_PREFERENCIAS_MODULOS_USUARIO_ACCION_INICIAL];
            $parametros_accion_inicial = $preferencias_modulos[INDICE_PREFERENCIAS_MODULOS_USUARIO_PARAMETROS_ACCION_INICIAL];
            $api_http = $fila_usuario["api_http"];
            $contrasenya_api_http = $fila_usuario["contrasenya_api_http"];

            // Se recuperan los identificadores de las redes asociadas a este usuario
            $ids_redes = array();
            if (($perfil == PERFIL_USUARIO_ESTANDAR) OR ($perfil == PERFIL_USUARIO_ADMINISTRADOR))
            {
                $ids_redes = dame_ids_redes_usuario($id_usuario, $perfil);
            }

            // Perfil del usuario actual
            switch ($_SESSION["perfil"])
            {
                // Administrador
                case PERFIL_USUARIO_ADMINISTRADOR:
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    // Si el usuario es estándar y hay red seleccionada
                    if (($perfil == PERFIL_USUARIO_ESTANDAR) AND ($id_red_actual != ID_NINGUNO))
                    {
                        // Se recuperan los identificadores de las licencias asociadas a este usuario
                        $consulta_licencias = "
                            SELECT licencia
                            FROM licencias_usuarios
                            WHERE
                                licencias_usuarios.usuario = '".$bd_red->_($id_usuario)."'";
                        $res_licencias = $bd_red->ejecuta_consulta($consulta_licencias);
                        if ($res_licencias == false)
                        {
                            throw new Exception("Error en la consulta: '".$consulta_licencias."'");
                        }
                        $ids_licencias = array();
                        while ($fila_licencia = $res_licencias->dame_siguiente_fila())
                        {
                            array_push($ids_licencias, $fila_licencia["licencia"]);
                        }

                        // Se recuperan las secciones del usuario
                        $secciones = dame_secciones_usuario($id_usuario, $id_red_actual);

                        // Se recuperan los parámetros del módulo Personal del usuario
                        $consulta_parametros_modulo_personal = "
                            SELECT parametros
                            FROM modulos_usuarios
                            WHERE
                                (modulo = '".MODULO_PERSONAL."')
                                AND (usuario = '".$bd_red->_($id_usuario)."')
                                AND (red = '".$bd_red->_($id_red_actual)."')";
                        $res_parametros_modulo_personal = $bd_red->ejecuta_consulta($consulta_parametros_modulo_personal);
                        if (($res_parametros_modulo_personal == false) || ($res_parametros_modulo_personal->dame_numero_filas() == 0))
                        {
                            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_parametros_modulo_personal."'");
                        }

                        $fila_parametros_modulo_personal = $res_parametros_modulo_personal->dame_siguiente_fila();
                        $cadena_parametros_modulo_personal = $fila_parametros_modulo_personal["parametros"];
                        $parametros_modulo_personal = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_modulo_personal);
                        $numero_maximo_informes_automaticos = $parametros_modulo_personal[INDICE_PARAMETRO_MODULO_PERSONAL_NUMERO_MAXIMO_INFORMES_AUTOMATICOS];
                        $administracion_widgets = $parametros_modulo_personal[INDICE_PARAMETRO_MODULO_PERSONAL_ADMINISTRACION_WIDGETS];
                        $administracion_plantillas_informes = $parametros_modulo_personal[INDICE_PARAMETRO_MODULO_PERSONAL_ADMINISTRACION_PLANTILLAS_INFORMES];
                        $administracion_informes_automaticos = $parametros_modulo_personal[INDICE_PARAMETRO_MODULO_PERSONAL_ADMINISTRACION_INFORMES_AUTOMATICOS];
                        $mostrar_otros_modulos = $parametros_modulo_personal[INDICE_PARAMETRO_MODULO_PERSONAL_MOSTRAR_OTROS_MODULOS];

                        // Se recuperan los parámetros del módulo Localizaciones del usuario
                        $consulta_parametros_modulo_localizaciones = "
                            SELECT parametros
                            FROM modulos_usuarios
                            WHERE
                                (modulo = '".MODULO_LOCALIZACIONES."')
                                AND (usuario = '".$bd_red->_($id_usuario)."')
                                AND (red = '".$bd_red->_($id_red_actual)."')";
                        $res_parametros_modulo_localizaciones = $bd_red->ejecuta_consulta($consulta_parametros_modulo_localizaciones);
                        if (($res_parametros_modulo_localizaciones == false) || ($res_parametros_modulo_localizaciones->dame_numero_filas() == 0))
                        {
                            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_parametros_modulo_localizaciones."'");
                        }

                        $fila_parametros_modulo_localizaciones = $res_parametros_modulo_localizaciones->dame_siguiente_fila();
                        $cadena_parametros_modulo_localizaciones = $fila_parametros_modulo_localizaciones["parametros"];
                        $parametros_modulo_localizaciones = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_modulo_localizaciones);
                        $permiso_todas_localizaciones = $parametros_modulo_localizaciones[INDICE_PARAMETRO_MODULO_LOCALIZACIONES_PERMISO_TODAS_LOCALIZACIONES];
                        $ids_localizaciones = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_modulo_localizaciones[INDICE_PARAMETRO_MODULO_LOCALIZACIONES_IDS_LOCALIZACIONES]);
                        $administracion_localizaciones = $parametros_modulo_localizaciones[INDICE_PARAMETRO_MODULO_LOCALIZACIONES_ADMINISTRACION_LOCALIZACIONES];
                        $administracion_instalaciones = $parametros_modulo_localizaciones[INDICE_PARAMETRO_MODULO_LOCALIZACIONES_ADMINISTRACION_INSTALACIONES];

                        // Se recuperan los parámetros del módulo Sensores del usuario
                        $consulta_parametros_modulo_sensores = "
                            SELECT parametros
                            FROM modulos_usuarios
                            WHERE
                                (modulo = '".MODULO_SENSORES."')
                                AND (usuario = '".$bd_red->_($id_usuario)."')
                                AND (red = '".$bd_red->_($id_red_actual)."')";
                        $res_parametros_modulo_sensores = $bd_red->ejecuta_consulta($consulta_parametros_modulo_sensores);
                        if (($res_parametros_modulo_sensores == false) || ($res_parametros_modulo_sensores->dame_numero_filas() == 0))
                        {
                            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_parametros_modulo_sensores."'");
                        }

                        $fila_parametros_modulo_sensores = $res_parametros_modulo_sensores->dame_siguiente_fila();
                        $cadena_parametros_modulo_sensores = $fila_parametros_modulo_sensores["parametros"];
                        $parametros_modulo_sensores = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_modulo_sensores);
                        $permiso_todos_sensores = $parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_PERMISO_TODOS_SENSORES];
                        $ids_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_IDS_SENSORES]);
                        $ids_grupos_sensores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_IDS_GRUPOS_SENSORES]);
                        $administracion_sensores = $parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_ADMINISTRACION_SENSORES];
                        $administracion_comentarios_sensores = $parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_ADMINISTRACION_COMENTARIOS_SENSORES];
                        $lectura_sensores = $parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_LECTURA_SENSORES];
                        $exportacion_sensores = $parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_EXPORTACION_SENSORES];
                        $administracion_eventos = $parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_ADMINISTRACION_EVENTOS];
                        $envio_valores_manuales_sensores = $parametros_modulo_sensores[INDICE_PARAMETRO_MODULO_SENSORES_ENVIO_VALORES_MANUALES_SENSORES];

                        // Se recuperan los parámetros del módulo Actuadores del usuario
                        $consulta_parametros_modulo_actuadores = "
                            SELECT parametros
                            FROM modulos_usuarios
                            WHERE
                                (modulo = '".MODULO_ACTUADORES."')
                                AND (usuario = '".$bd_red->_($id_usuario)."')
                                AND (red = '".$bd_red->_($id_red_actual)."')";
                        $res_parametros_modulo_actuadores = $bd_red->ejecuta_consulta($consulta_parametros_modulo_actuadores);
                        if (($res_parametros_modulo_actuadores == false) || ($res_parametros_modulo_actuadores->dame_numero_filas() == 0))
                        {
                            throw new Exception("Error o no existe la información en la base de datos: '".$consulta_parametros_modulo_actuadores."'");
                        }

                        $fila_parametros_modulo_actuadores = $res_parametros_modulo_actuadores->dame_siguiente_fila();
                        $cadena_parametros_modulo_actuadores = $fila_parametros_modulo_actuadores["parametros"];
                        $parametros_modulo_actuadores = explode(SEPARADOR_PARAMETROS_COMPUESTOS, $cadena_parametros_modulo_actuadores);
                        $permiso_todos_actuadores = $parametros_modulo_actuadores[INDICE_PARAMETRO_MODULO_ACTUADORES_PERMISO_TODOS_ACTUADORES];
                        $ids_actuadores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_modulo_actuadores[INDICE_PARAMETRO_MODULO_ACTUADORES_IDS_ACTUADORES]);
                        $ids_grupos_actuadores = explode(SEPARADOR_PARAMETROS_SIMPLES, $parametros_modulo_actuadores[INDICE_PARAMETRO_MODULO_ACTUADORES_IDS_GRUPOS_ACTUADORES]);
                        $administracion_actuadores = $parametros_modulo_actuadores[INDICE_PARAMETRO_MODULO_ACTUADORES_ADMINISTRACION_ACTUADORES];
                        $administracion_comentarios_actuadores = $parametros_modulo_actuadores[INDICE_PARAMETRO_MODULO_ACTUADORES_ADMINISTRACION_COMENTARIOS_ACTUADORES];
                        $acciones_actuadores = $parametros_modulo_actuadores[INDICE_PARAMETRO_MODULO_ACTUADORES_ACCIONES_ACTUADORES];
                        $administracion_programaciones = $parametros_modulo_actuadores[INDICE_PARAMETRO_MODULO_ACTUADORES_ADMINISTRACION_PROGRAMACIONES];
                        $administracion_reglas = $parametros_modulo_actuadores[INDICE_PARAMETRO_MODULO_ACTUADORES_ADMINISTRACION_REGLAS];
                    }
                    else
                    {
                        $ids_licencias = array();
                        $secciones = array();

                        $numero_maximo_informes_automaticos = 0;
                        $administracion_widgets = VALOR_SI;
                        $administracion_plantillas_informes = VALOR_NO;
                        $administracion_informes_automaticos = VALOR_NO;
                        $mostrar_otros_modulos = VALOR_NO;

                        $permiso_todas_localizaciones = VALOR_SI;
                        $ids_localizaciones = array();
                        $administracion_localizaciones = VALOR_NO;
                        $administracion_instalaciones = VALOR_NO;

                        $permiso_todos_sensores = VALOR_SI;
                        $ids_sensores = array();
                        $ids_grupos_sensores = array();
                        $administracion_sensores = VALOR_NO;
                        $administracion_comentarios_sensores = VALOR_NO;
                        $lectura_sensores = VALOR_NO;
                        $exportacion_sensores = VALOR_SI;
                        $administracion_eventos = VALOR_NO;
                        $envio_valores_manuales_sensores = VALOR_NO;

                        $permiso_todos_actuadores = VALOR_SI;
                        $ids_actuadores = array();
                        $ids_grupos_actuadores = array();
                        $administracion_actuadores = VALOR_NO;
                        $administracion_comentarios_actuadores = VALOR_NO;
                        $acciones_actuadores = VALOR_NO;
                        $administracion_programaciones = VALOR_NO;
                        $administracion_reglas = VALOR_NO;
                    }
                    break;
                }
                case PERFIL_USUARIO_ESTANDAR:
                {
                    // Variables no utilizadas pero que hay que inicializar
                    $numero_maximo_informes_automaticos = 0;
                    $administracion_widgets = VALOR_SI;
                    $administracion_plantillas_informes = VALOR_SI;
                    $administracion_informes_automaticos = VALOR_SI;
                    $mostrar_otros_modulos = VALOR_SI;

                    $permiso_todas_localizaciones = VALOR_SI;
                    $ids_localizaciones = array();
                    $administracion_localizaciones = VALOR_NO;
                    $administracion_instalaciones = VALOR_NO;

                    $permiso_todos_sensores = VALOR_SI;
                    $ids_sensores = array();
                    $ids_grupos_sensores = array();
                    $administracion_sensores = VALOR_NO;
                    $administracion_comentarios_sensores = VALOR_NO;
                    $lectura_sensores = VALOR_NO;
                    $exportacion_sensores = VALOR_SI;
                    $administracion_eventos = VALOR_NO;
                    $envio_valores_manuales_sensores = VALOR_NO;

                    $permiso_todos_actuadores = VALOR_SI;
                    $ids_actuadores = array();
                    $ids_grupos_actuadores = array();
                    $administracion_actuadores = VALOR_NO;
                    $administracion_comentarios_actuadores = VALOR_NO;
                    $acciones_actuadores = VALOR_NO;
                    $administracion_programaciones = VALOR_NO;
                    $administracion_reglas = VALOR_NO;
                    break;
                }
            }
		}
        else
        {
            // Contenido vacio
            $contenido = "";

            // Identificador de usuario vacío por defecto
            $id_usuario = "";

            // Sin redes por defecto
            $ids_redes = array();

            // Sin licencias ni secciones por defecto
            $ids_licencias = array();
            $secciones = array();

            // Parámetros por defecto de los módulos
            $numero_maximo_informes_automaticos = 0;
            $administracion_widgets = VALOR_SI;
            $administracion_plantillas_informes = VALOR_NO;
            $administracion_informes_automaticos = VALOR_NO;
            $mostrar_otros_modulos = VALOR_NO;

            $permiso_todas_localizaciones = VALOR_SI;
            $ids_localizaciones = array();
            $administracion_localizaciones = VALOR_NO;
            $administracion_instalaciones = VALOR_NO;

            $permiso_todos_sensores = VALOR_SI;
            $ids_sensores = array();
            $ids_grupos_sensores = array();
            $administracion_sensores = VALOR_NO;
            $administracion_comentarios_sensores = VALOR_NO;
            $lectura_sensores = VALOR_NO;
            $exportacion_sensores = VALOR_SI;
            $administracion_eventos = VALOR_NO;
            $envio_valores_manuales_sensores = VALOR_NO;

            $permiso_todos_actuadores = VALOR_SI;
            $ids_actuadores = array();
            $ids_grupos_actuadores = array();
            $administracion_actuadores = VALOR_NO;
            $administracion_comentarios_actuadores = VALOR_NO;
            $acciones_actuadores = VALOR_NO;
            $administracion_programaciones = VALOR_NO;
            $administracion_reglas = VALOR_NO;

            $contrasenya_api_http = "";
            $api_http = VALOR_NO;

            // Idioma de la red actual por defecto
            if ($id_red_actual != ID_NINGUNO)
            {
                $fila_red = dame_fila_red($id_red_actual);
                $idioma = $fila_red["idioma"];
            }
            else
            {
                $idioma = "es_ES";
            }

            // Tamaño de letra
            $tamanyo_letra = TAMANYO_LETRA_DEFECTO;

            // Pantalla completa al inicio
            $pantalla_completa_inicio = VALOR_NO;

            // Preferencias de módulos
            $modo_seleccion_localizacion_actual = MODO_SELECCION_LOCALIZACION_ACTUAL_UNICA;
            $modulo_defecto = MODULO_PERSONAL;
            $seccion_defecto = SECCION_PERSONAL_WIDGETS;
            $accion_inicial = ID_NINGUNO;
        }

        // Módulos disponibles
        $estado_modulo_personal = MODULO_NO_DISPONIBLE;
        $estado_modulo_red = MODULO_NO_DISPONIBLE;
        $estado_modulo_localizaciones = MODULO_NO_DISPONIBLE;
        $estado_modulo_sensores = MODULO_NO_DISPONIBLE;
        $estado_modulo_actuadores = MODULO_NO_DISPONIBLE;
        $estado_modulo_smartmeter = MODULO_NO_DISPONIBLE;
        $estado_modulo_proyectos = MODULO_NO_DISPONIBLE;
        foreach (array_keys($_SESSION["modulos"]) AS $modulo)
        {
            switch ($modulo)
            {
                case MODULO_PERSONAL:
                {
                    $estado_modulo_personal = MODULO_DISPONIBLE;
                    break;
                }
                case MODULO_RED:
                {
                    $estado_modulo_red = MODULO_DISPONIBLE;
                    break;
                }
                case MODULO_LOCALIZACIONES:
                {
                    $estado_modulo_localizaciones = MODULO_DISPONIBLE;
                    break;
                }
                case MODULO_SENSORES:
                {
                    $estado_modulo_sensores = MODULO_DISPONIBLE;
                    break;
                }
                case MODULO_ACTUADORES:
                {
                    $estado_modulo_actuadores = MODULO_DISPONIBLE;
                    break;
                }
                case MODULO_SMARTMETER:
                {
                    $estado_modulo_smartmeter = MODULO_DISPONIBLE;
                    break;
                }
                case MODULO_PROYECTOS:
                {
                    $estado_modulo_proyectos = MODULO_DISPONIBLE;
                    break;
                }
            }
        }

        // Se muestran las siguientes pestañas:
        // - Principal, licencias y secciones
        //   (las pestañas de parámetros específicos de cada uno de los módulos se muestran sólo si la red tiene licencias de estos módulos)
        $contenido .= "
            <div id='tabs-administracion-usuario' class='tabbable'>";
        $contenido .= "
                <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                    <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-principal' id='titulo-tab-principal'>".$idiomas->_("Principal")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-redes-usuario' id='titulo-tab-redes-usuario'>".$idiomas->_("Redes")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-licencias-usuario' id='titulo-tab-licencias-usuario'>".$idiomas->_("Módulos")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-secciones-usuario' id='titulo-tab-secciones-usuario'>".$idiomas->_("Secciones")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya ".$estado_modulo_personal."' href='#tab-personal-usuario' id='titulo-tab-personal-usuario'>".dame_nombre_modulo(MODULO_PERSONAL)."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya ".$estado_modulo_localizaciones."' href='#tab-localizaciones-usuario' id='titulo-tab-localizaciones-usuario'>".dame_nombre_modulo(MODULO_LOCALIZACIONES)."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya ".$estado_modulo_sensores."' href='#tab-sensores-usuario' id='titulo-tab-sensores-usuario'>".dame_nombre_modulo(MODULO_SENSORES)."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya ".$estado_modulo_actuadores."' href='#tab-actuadores-usuario' id='titulo-tab-actuadores-usuario'>".dame_nombre_modulo(MODULO_ACTUADORES)."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-preferencias-usuario' id='titulo-tab-preferencias-usuario'>".$idiomas->_("Preferencias")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-preferencias-modulos-usuario' id='titulo-tab-preferencias-modulos-usuario'>".$idiomas->_("Preferencias de módulos")."</a></li>
                    <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-api-usuario' id='titulo-tab-api-usuario'>".$idiomas->_("API HTTP")."</a></li>
                </ul>
                <div id='tabs-content-administracion-usuario' class='tab-content'>";

        $contenido .= "
                    <div class='tab-pane active' id='tab-principal'>";

        // Contenido de pestaña principal
        // (https://stackoverflow.com/questions/12374442/chrome-ignores-autocomplete-off)
		$contenido .= "
			<div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Identificador").": "."</span><br/>
					<input type='text' id='id_usuario'
						class='TLNT_input_mandatory TLNT_input_login input-administracion' value='".htmlspecialchars($id_usuario, ENT_QUOTES)."'>
                </div>
			</div>

            <input type='password' style='display:none'>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Contraseña").": "."</span><br/>
					<input type='password' id='contrasenya_usuario'
						class='input-administracion' value='".$contrasenya."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Repetir contraseña").": "."</span><br/>
					<input type='password' id='comprobacion_contrasenya_usuario'
						class='input-administracion' value='".$comprobacion_contrasenya."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Nombre").": "."</span><br/>
					<input type='text' id='nombre_usuario'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($nombre, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Perfil").": "."</span><br/>
					<select id='perfil_usuario' class='select-administracion'";
        if ($anyadir_usuario == false)
        {
            $contenido .= " disabled";
        }
        $contenido .= ">";
        $contenido .= dame_lista_perfiles_usuario($perfil, OPCIONES_EXTRA_LISTA_PERFILES_USUARIO_SIN_OPCIONES_EXTRA);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de redes
        $contenido .= "
                    <div class='tab-pane' id='tab-redes-usuario'>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Redes").": "."</span><br/>
                    <div id='select_redes_usuario_no_visible' hidden></div>
					<select id='ids_redes_usuario'
                        name='ids_redes_usuario'
                        multiple='multiple'
						class='select-administracion' hidden>";
        $contenido .= dame_lista_redes($ids_redes);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de licencias
        $contenido .= "
                    <div class='tab-pane' id='tab-licencias-usuario'>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Módulos").": "."</span><br/>
                    <div id='select_licencias_usuario_no_visible' hidden></div>
					<select id='ids_licencias_usuario'
                        name='ids_licencias_usuario'
                        multiple='multiple'
						class='select-administracion' hidden>";
        $contenido .= dame_lista_licencias_red($id_red_actual, $ids_licencias);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de secciones
        $contenido .= "
                    <div class='tab-pane' id='tab-secciones-usuario'>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'";
        if ($estado_modulo_personal == MODULO_NO_DISPONIBLE)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">";
        $contenido .= "
				<div class='span12'><span class='titulo-campo-administracion'>".dame_nombre_modulo(MODULO_PERSONAL).": "."</span><br/>
                    <div id='select_secciones_personal_usuario_no_visible' hidden></div>
					<select id='ids_secciones_personal_usuario'
                        name='ids_secciones_personal_usuario'
                        multiple='multiple'
                        class='select-administracion' hidden>";
		$contenido .= dame_lista_secciones_modulo(MODULO_PERSONAL, NULL, $secciones[MODULO_PERSONAL]);
		$contenido .= "
					</select>
				</div>
			</div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'";
        if ($estado_modulo_red == MODULO_NO_DISPONIBLE)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">";
        $contenido .= "
				<div class='span12'><span class='titulo-campo-administracion'>".dame_nombre_modulo(MODULO_RED).": "."</span><br/>
                    <div id='select_secciones_red_usuario_no_visible' hidden></div>
					<select id='ids_secciones_red_usuario'
                        name='ids_secciones_red_usuario'
                        multiple='multiple'
                        class='select-administracion' hidden>";
		$contenido .= dame_lista_secciones_modulo(MODULO_RED, NULL, $secciones[MODULO_RED]);
		$contenido .= "
					</select>
				</div>
			</div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'";
        if ($estado_modulo_localizaciones == MODULO_NO_DISPONIBLE)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
				<div class='span12'><span class='titulo-campo-administracion'>".dame_nombre_modulo(MODULO_LOCALIZACIONES).": "."</span><br/>
                    <div id='select_secciones_localizaciones_usuario_no_visible' hidden></div>
					<select id='ids_secciones_localizaciones_usuario'
                        name='ids_secciones_localizaciones_usuario'
                        multiple='multiple'
                        class='select-administracion' hidden>";
		$contenido .= dame_lista_secciones_modulo(MODULO_LOCALIZACIONES, NULL, $secciones[MODULO_LOCALIZACIONES]);
		$contenido .= "
					</select>
				</div>
			</div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'";
        if ($estado_modulo_sensores == MODULO_NO_DISPONIBLE)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
				<div class='span12'><span class='titulo-campo-administracion'>".dame_nombre_modulo(MODULO_SENSORES).": "."</span><br/>
                    <div id='select_secciones_sensores_usuario_no_visible' hidden></div>
					<select id='ids_secciones_sensores_usuario'
                        name='ids_secciones_sensores_usuario'
                        multiple='multiple'
                        class='select-administracion' hidden>";
		$contenido .= dame_lista_secciones_modulo(MODULO_SENSORES, NULL, $secciones[MODULO_SENSORES]);
		$contenido .= "
					</select>
				</div>
			</div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'";
        if ($estado_modulo_actuadores == MODULO_NO_DISPONIBLE)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
				<div class='span12'><span class='titulo-campo-administracion'>".dame_nombre_modulo(MODULO_ACTUADORES).": "."</span><br/>
                    <div id='select_secciones_actuadores_usuario_no_visible' hidden></div>
					<select id='ids_secciones_actuadores_usuario'
                        name='ids_secciones_actuadores_usuario'
                        multiple='multiple'
                        class='select-administracion' hidden>";
		$contenido .= dame_lista_secciones_modulo(MODULO_ACTUADORES, NULL, $secciones[MODULO_ACTUADORES]);
		$contenido .= "
					</select>
				</div>
			</div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'";
        if ($estado_modulo_smartmeter == MODULO_NO_DISPONIBLE)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
				<div class='span12'><span class='titulo-campo-administracion'>".dame_nombre_modulo(MODULO_SMARTMETER).": "."</span><br/>
                    <div id='select_secciones_smartmeter_usuario_no_visible' hidden></div>
					<select id='ids_secciones_smartmeter_usuario'
                        name='ids_secciones_smartmeter_usuario'
                        multiple='multiple'
                        class='select-administracion' hidden>";
		$contenido .= dame_lista_secciones_modulo(MODULO_SMARTMETER, NULL, $secciones[MODULO_SMARTMETER]);
		$contenido .= "
					</select>
				</div>
			</div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid'";
        if ($estado_modulo_proyectos == MODULO_NO_DISPONIBLE)
        {
            $contenido .= " hidden";
        }
        $contenido .= ">
				<div class='span12'><span class='titulo-campo-administracion'>".dame_nombre_modulo(MODULO_PROYECTOS).": "."</span><br/>
                    <div id='select_secciones_proyectos_usuario_no_visible' hidden></div>
					<select id='ids_secciones_proyectos_usuario'
                        name='ids_secciones_proyectos_usuario'
                        multiple='multiple'
                        class='select-administracion' hidden>";
		$contenido .= dame_lista_secciones_modulo(MODULO_PROYECTOS, NULL, $secciones[MODULO_PROYECTOS]);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña del módulo Personal
        $contenido .= "
                    <div class='tab-pane' id='tab-personal-usuario'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Número máximo de informes automáticos").": "."</span><br/>
					<input type='text' id='numero_maximo_informes_automaticos_personal_usuario'
						class='TLNT_input_mandatory TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($numero_maximo_informes_automaticos, ENT_QUOTES)."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Administración de widgets").": "."</span><br/>
					<select id='administracion_widgets_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($administracion_widgets);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Administración de plantillas de informes").": "."</span><br/>
					<select id='administracion_plantillas_informes_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($administracion_plantillas_informes);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Administración de informes automáticos").": "."</span><br/>
					<select id='administracion_informes_automaticos_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($administracion_informes_automaticos);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Mostrar otros módulos").": "."</span><br/>
					<select id='mostrar_otros_modulos_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($mostrar_otros_modulos);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña del módulo Localizaciones
        $contenido .= "
                    <div class='tab-pane' id='tab-localizaciones-usuario'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Todas las localizaciones").": "."</span><br/>
					<select id='permiso_todas_localizaciones_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($permiso_todas_localizaciones);
		$contenido .= "
					</select>
				</div>
			</div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid' id='control_ids_localizaciones_usuario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Localizaciones").": "."</span><br/>
                    <div id='select_localizaciones_usuario_no_visible' hidden></div>
					<select id='ids_localizaciones_usuario'
                        name='ids_localizaciones_usuario'
                        max_selected='".NUMERO_MAXIMO_LOCALIZACIONES_USUARIO."' multiple='multiple'
						class='select-administracion' hidden>";
        $contenido .= dame_lista_localizaciones($ids_localizaciones, OPCIONES_EXTRA_LISTA_LOCALIZACIONES_SIN_OPCIONES_EXTRA);
        $contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Administración de localizaciones").": "."</span><br/>
					<select id='administracion_localizaciones_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($administracion_localizaciones);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_administracion_instalaciones_usuario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Administración de instalaciones").": "."</span><br/>
					<select id='administracion_instalaciones_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($administracion_instalaciones);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña del módulo Sensores
        $contenido .= "
                    <div class='tab-pane' id='tab-sensores-usuario'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Todos los sensores").": "."</span><br/>
					<select id='permiso_todos_sensores_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($permiso_todos_sensores);
		$contenido .= "
					</select>
				</div>
			</div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid' id='control_ids_sensores_usuario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sensores").": "."</span><br/>
                    <div id='select_sensores_usuario_no_visible' hidden></div>
					<select id='ids_sensores_usuario'
                        name='ids_sensores_usuario'
                        max_selected='".NUMERO_MAXIMO_SENSORES_USUARIO."' multiple='multiple'
						class='select-administracion' hidden>";
        $contenido .= dame_lista_sensores(CLASE_TODAS, $ids_sensores, OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA_TODOS_NODOS);
        $contenido .= "
					</select>
				</div>
			</div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid' id='control_ids_grupos_sensores_usuario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupos").": "."</span><br/>
                    <div id='select_grupos_sensores_usuario_no_visible' hidden></div>
					<select id='ids_grupos_sensores_usuario'
                        name='ids_grupos_sensores_usuario'
                        max_selected='".NUMERO_MAXIMO_GRUPOS_SENSORES_USUARIO."' multiple='multiple'
						class='select-administracion' hidden>";
        $contenido .= dame_lista_grupos_sensores(CLASE_TODAS, $ids_grupos_sensores, OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA_TODOS_NODOS);
        $contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Administración de sensores").": "."</span><br/>
					<select id='administracion_sensores_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($administracion_sensores);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_administracion_comentarios_sensores_usuario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Administración de comentarios de sensores").": "."</span><br/>
					<select id='administracion_comentarios_sensores_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($administracion_comentarios_sensores);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_lectura_sensores_usuario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Lectura de sensores").": "."</span><br/>
					<select id='lectura_sensores_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($lectura_sensores);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_exportacion_sensores_usuario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Exportación de sensores").": "."</span><br/>
					<select id='exportacion_sensores_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($exportacion_sensores);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_envio_valores_manuales_sensores_usuario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Envío de valores manuales de sensores").": "."</span><br/>
					<select id='envio_valores_manuales_sensores_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($envio_valores_manuales_sensores);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_administracion_eventos_usuario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Administración de eventos").": "."</span><br/>
					<select id='administracion_eventos_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($administracion_eventos);
		$contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña del módulo Actuadores
        $contenido .= "
                    <div class='tab-pane' id='tab-actuadores-usuario'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Todos los actuadores").": "."</span><br/>
					<select id='permiso_todos_actuadores_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($permiso_todos_actuadores);
		$contenido .= "
					</select>
				</div>
			</div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid' id='control_ids_actuadores_usuario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Actuadores").": "."</span><br/>
                    <div id='select_actuadores_usuario_no_visible' hidden></div>
					<select id='ids_actuadores_usuario'
                        name='ids_actuadores_usuario'
                        max_selected='".NUMERO_MAXIMO_ACTUADORES_USUARIO."' multiple='multiple'
						class='select-administracion' hidden>";
        $contenido .= dame_lista_actuadores(CLASE_TODAS, $ids_actuadores, OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA_TODOS_NODOS);
        $contenido .= "
					</select>
				</div>
			</div>";

        // Nota: En las listas dobles es necesario el atributo 'name'
        $contenido .= "
            <div class='row-fluid' id='control_ids_grupos_actuadores_usuario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Grupos").": "."</span><br/>
                    <div id='select_grupos_actuadores_usuario_no_visible' hidden></div>
					<select id='ids_grupos_actuadores_usuario'
                        name='ids_grupos_actuadores_usuario'
                        max_selected='".NUMERO_MAXIMO_GRUPOS_ACTUADORES_USUARIO."' multiple='multiple'
						class='select-administracion' hidden>";
        $contenido .= dame_lista_grupos_actuadores(CLASE_TODAS, $ids_grupos_actuadores, OPCIONES_EXTRA_LISTA_NODOS_SIN_OPCIONES_EXTRA_TODOS_NODOS);
        $contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Administración de actuadores").": "."</span><br/>
					<select id='administracion_actuadores_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($administracion_actuadores);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_administracion_comentarios_actuadores_usuario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Administración de comentarios de actuadores").": "."</span><br/>
					<select id='administracion_comentarios_actuadores_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($administracion_comentarios_actuadores);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_acciones_actuadores_usuario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Acciones de actuadores").": "."</span><br/>
					<select id='acciones_actuadores_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($acciones_actuadores);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_administracion_programaciones_usuario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Administración de programaciones").": "."</span><br/>
					<select id='administracion_programaciones_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($administracion_programaciones);
		$contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid' id='control_administracion_reglas_usuario'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Administración de reglas").": "."</span><br/>
					<select id='administracion_reglas_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($administracion_reglas);
		$contenido .= "
					</select>
				</div>
			</div>";

		$contenido .= "
                    </div>";

        // Contenido de pestaña de preferencias
        $contenido .= "
                    <div class='tab-pane' id='tab-preferencias-usuario'>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Idioma").": "."</span><br/>
					<select id='idioma_usuario' class='select-administracion'>";
        $contenido .= dame_lista_idiomas($idioma);
        $contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Tamaño de letra").": "."</span><br/>
					<input type='range' id='tamanyo_letra_usuario' class='slider-administracion'
                        min='".TAMANYO_LETRA_MINIMO."' max='".TAMANYO_LETRA_MAXIMO."' value='".$tamanyo_letra."'>
				</div>
			</div>

            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Pantalla completa inicial").": "."</span><br/>
					<select id='pantalla_completa_inicio_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($pantalla_completa_inicio);
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
                    </div>";

        // Contenido de pestaña de preferencias de módulos
        $contenido .= "
                    <div class='tab-pane' id='tab-preferencias-modulos-usuario'>";

        // Flags de controles visibles (en las pestaña de preferencias de módulos)
        $control_modo_seleccion_localizacion_visible = VALOR_SI;

        $contenido .= "
            <div class='row-fluid'";

        // Nota: Se oculta el modo de selección de localización si se cumplen las siguientes condiciones:
        // - El usuario es estándar y está editando su propio perfil
        // - Hay red seleccionada y no tiene el módulo personal o no se permite la visualización de otros módulos
        if (($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR) && ($perfil == PERFIL_USUARIO_ESTANDAR))
        {
            if (($_SESSION["id_red"] != ID_NINGUNO) &&
                (($estado_modulo_personal == MODULO_NO_DISPONIBLE) ||
                ($_SESSION["parametros_modulo_personal"]["mostrar_otros_modulos"] == VALOR_NO)))
            {
                $contenido .= " hidden";
                $control_modo_seleccion_localizacion_visible = VALOR_NO;
            }
        }
        $contenido .= ">
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Modo de selección de localización").": "."</span><br/>
					<select id='modo_seleccion_localizacion_actual_usuario' class='select-administracion'>";
        $contenido .= dame_lista_modos_seleccion_localizacion_actual($modo_seleccion_localizacion_actual);
        $contenido .= "
					</select>
				</div>
			</div>";

        // Si el usuario es superadministrador o tiene más de una red no se muestran los parámetros
        // que sólo aplican cuando el usuario tiene una sola red
        if (($perfil == PERFIL_USUARIO_SUPERADMINISTRADOR) || (count($ids_redes) != 1))
        {
            $mostrar_parametros_usuario_unica_red = false;
            $id_red_usuario = ID_NINGUNO;
        }
        else
        {
            // Si es usuario estándar y no es el usuario actual, no se muestran los parámetros de una sola red
            // (para evitar problemas y conflictos con modulos y secciones asignados)
            if (($perfil == PERFIL_USUARIO_ESTANDAR) && (strtolower($id_usuario) != $_SESSION["id_usuario"]))
            {
                $mostrar_parametros_usuario_unica_red = false;
                $id_red_usuario = ID_NINGUNO;
            }
            else
            {
                $mostrar_parametros_usuario_unica_red = true;
                $id_red_usuario = $ids_redes[0];
            }
        }

        $contenido .= "
            <div class='row-fluid'";
        if ($mostrar_parametros_usuario_unica_red == false)
        {
            $contenido.= " hidden";
        }
        $contenido .= ">
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Módulo por defecto").": "."</span><br/>
					<select id='modulo_defecto_usuario' class='select-administracion'>";
        $contenido .= dame_lista_modulos_defecto_usuario(
            $id_usuario,
            $perfil,
            $id_red_usuario,
            $modulo_defecto);
        $contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'";
        if ($mostrar_parametros_usuario_unica_red == false)
        {
            $contenido.= " hidden";
        }
        $contenido .= ">
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Sección por defecto").": "."</span><br/>
					<select id='seccion_defecto_usuario' class='select-administracion'>";
        $contenido .= dame_lista_secciones_modulo_defecto_usuario(
            $id_usuario,
            $id_red_usuario,
            $modulo_defecto,
            $seccion_defecto);
        $contenido .= "
					</select>
				</div>
			</div>

            <div class='row-fluid'";
        if ($mostrar_parametros_usuario_unica_red == false)
        {
            $contenido.= " hidden";
        }
        $contenido .= ">
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Acción inicial").": "."</span><br/>
					<select id='accion_inicial_usuario' class='select-administracion'>";
        $contenido .= dame_lista_acciones_iniciales_modulo_seccion($modulo_defecto, $seccion_defecto, $accion_inicial);
        $contenido .= "
					</select>
				</div>
			</div>

            <div";
        if ($mostrar_parametros_usuario_unica_red == false)
        {
            $contenido.= " hidden";
        }
        $contenido .= ">
                <div class='row-fluid' id='control_parametros_accion_inicial_usuario'>
                    <div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_('Parámetros').": "."</span><br/>
                        <input type='text' id='parametros_accion_inicial_usuario'
                            class='TLNT_input_valid_characters input-administracion' value='".htmlspecialchars($parametros_accion_inicial, ENT_QUOTES)."'>
                        <span id='boton_administracion_ayuda_parametros_accion_inicial_usuario' class='clickable'>
                            <i class='icon-question-sign color-azul icono-ayuda'></i>
                        </span>
                    </div>
                </div>
            </div>";

		$contenido .= "
                    </div>";

        // Contenido de pestaña de API
        $contenido .= "
                    <div class='tab-pane' id='tab-api-usuario'>";

        $contenido .= "
            <div id='control_api_http' class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Habilitado").": "."</span><br/>
					<select id='api_http_usuario' class='select-administracion'>";
        $contenido .= dame_lista_valores_si_no($api_http);
        $contenido .= "
					</select>
				</div>
			</div>";

        $contenido .= "
            <div class='row-fluid'>
				<div class='span12'><span class='titulo-campo-administracion'>".$idiomas->_("Contraseña").": "."</span><br/>
					<input type='text' id='contrasenya_api_http_usuario'
						class='TLNT_input_valid_characters input-administracion' readonly='readonly' value='".$contrasenya_api_http."'>
				</div>
			</div>";

		$contenido .= "
                    </div>";

        $contenido .= "
                </div>
            </div>";

        // Se añaden los parámetros (no visibles) en un 'div' oculto
        sort($ids_redes);
        $cadena_ids_redes = implode(SEPARADOR_PARAMETROS_SIMPLES, $ids_redes);
        if ($mostrar_parametros_usuario_unica_red == true)
        {
            $valor_mostrar_parametros_usuario_unica_red = VALOR_SI;
        }
        else {
            $valor_mostrar_parametros_usuario_unica_red = VALOR_NO;
        }
        $contenido .= '
            <div id="parametros_ventana_anyadir_modificar_usuario"
                id_red_actual="'.$_SESSION["id_red"].'"
                anyadir_usuario="'.$anyadir_usuario.'"
                id_usuario="'.$id_usuario.'"
                id_red_usuario="'.$id_red_usuario.'",
                cadena_redes_usuario="'.$cadena_ids_redes.'",
                cadena_parametros_modulo_personal_usuario="'.$cadena_parametros_modulo_personal.'",
                cadena_parametros_modulo_localizaciones_usuario="'.$cadena_parametros_modulo_localizaciones.'",
                cadena_parametros_modulo_sensores_usuario="'.$cadena_parametros_modulo_sensores.'",
                cadena_parametros_modulo_actuadores_usuario="'.$cadena_parametros_modulo_actuadores.'",
                control_modo_seleccion_localizacion_visible="'.$control_modo_seleccion_localizacion_visible.'",
                mostrar_parametros_usuario_unica_red="'.$valor_mostrar_parametros_usuario_unica_red.'"
                hidden>
            </div>';

        return ("OK");
	}
?>
