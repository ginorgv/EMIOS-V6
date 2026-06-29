<?php
	session_start();

    include_once($_SESSION["directorio"].'/comun/log/log.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloActuadores/ModuloActuadores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/ModuloAdministracion.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/ModuloAdministracion/Usuarios/Usuario.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloLocalizaciones/ModuloLocalizaciones.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloMonitorizacion/ModuloMonitorizacion.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloPersonal/ModuloPersonal.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloProyectos/ModuloProyectos.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloRed/ModuloRed.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSensores/ModuloSensores.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloSmartmeter/ModuloSmartmeter.php');
    include_once($_SESSION["directorio"].'/src/Modulos/ModulosWeb/util_modulos_web.php');


    function dame_lista_perfiles_usuario($id_perfil_seleccionado, $opciones_extra)
    {
        $idiomas = new Idiomas();

        switch ($_SESSION["perfil"])
        {
            case PERFIL_USUARIO_ESTANDAR:
            {
                $perfiles_usuario = array(PERFIL_USUARIO_ESTANDAR);
                break;
            }
            case PERFIL_USUARIO_ADMINISTRADOR:
            {
                $perfiles_usuario = array(PERFIL_USUARIO_ESTANDAR, PERFIL_USUARIO_ADMINISTRADOR);
                break;
            }
            case PERFIL_USUARIO_SUPERADMINISTRADOR:
            {
                if ($_SESSION["id_red"] == ID_NINGUNO)
                {
                    $perfiles_usuario = array(
                        PERFIL_USUARIO_ESTANDAR,
                        PERFIL_USUARIO_ADMINISTRADOR,
                        PERFIL_USUARIO_SUPERADMINISTRADOR);
                }
                else
                {
                    $perfiles_usuario = array(PERFIL_USUARIO_ESTANDAR, PERFIL_USUARIO_ADMINISTRADOR);
                }
                break;
            }
        }

        $lista = "";
        if ($opciones_extra == OPCIONES_EXTRA_LISTA_PERFILES_USUARIO_TODOS)
        {
            $lista .= "<option value=".PERFIL_USUARIO_TODOS.">".$idiomas->_("Todos")."</option>";
        }
        foreach ($perfiles_usuario as $perfil_usuario)
		{
			$lista .= "<option value='".$perfil_usuario."'";
			if ($perfil_usuario == $id_perfil_seleccionado)
			{
				$lista .= " selected";
			}
			$lista .= ">".Usuario::dame_descripcion_perfil_usuario($perfil_usuario)."</option>";
		}
        return ($lista);
    }


    function dame_lista_redes($ids_redes_seleccionadas)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        switch ($_SESSION["perfil"])
        {
            case PERFIL_USUARIO_SUPERADMINISTRADOR:
            {
                $consulta_redes = "
                    SELECT
                        id,
                        nombre
                    FROM redes
                    ORDER BY nombre ASC";
                break;
            }
            case PERFIL_USUARIO_ADMINISTRADOR:
            case PERFIL_USUARIO_ESTANDAR:
            {
                $consulta_redes = "
                    SELECT
                        redes.id AS id,
                        redes.nombre AS nombre
                    FROM redes, redes_usuarios
                    WHERE
                        (redes.id = redes_usuarios.red)
                        AND (redes_usuarios.usuario = '".$_SESSION["id_usuario"]."')
                    ORDER BY redes.nombre ASC";
                break;
            }
        }
		$res_redes = $bd_red->ejecuta_consulta($consulta_redes);
        if ($res_redes == false)
		{
		    throw new Exception("Error en la consulta: '".$consulta_redes."'");
		}

        $lista = "";
		while ($fila_red = $res_redes->dame_siguiente_fila())
		{
			$lista .= "<option value='".$fila_red['id']."'";
            if (in_array($fila_red['id'], $ids_redes_seleccionadas))
			{
				$lista .= " selected";
			}
			$lista .= ">".htmlspecialchars($fila_red['nombre'], ENT_QUOTES)."</option>";
		}
        return ($lista);
    }


    function dame_lista_licencias_red($id_red, $ids_licencias_seleccionadas)
    {
        $bd_red = BaseDatosRed::dame_base_datos();

        $consulta_licencias = "
			SELECT
                id,
                modulo
			FROM licencias
            WHERE
                red = '".$bd_red->_($id_red)."'
			ORDER BY modulo ASC";
		$res_licencias = $bd_red->ejecuta_consulta($consulta_licencias);
        if ($res_licencias == false)
		{
		    throw new Exception("Error en la consulta: '".$consulta_licencias."'");
		}

        $lista = "";
		while ($fila_licencia = $res_licencias->dame_siguiente_fila())
		{
            $id_ordenacion_lista = dame_id_ordenacion_lista_modulo($fila_licencia['modulo']);
			$lista .= "<option value='".$fila_licencia['id']."' sort_id='".$id_ordenacion_lista."'";
            if (in_array($fila_licencia['id'], $ids_licencias_seleccionadas))
			{
				$lista .= " selected";
			}
			$lista .= ">".dame_nombre_modulo($fila_licencia['modulo'])."</option>";
		}
        return ($lista);
    }


    function dame_lista_modulos($modulo_seleccionado)
    {
        $idiomas = new Idiomas();

        $modulos = array(
            MODULO_PERSONAL,
            MODULO_LOCALIZACIONES,
            MODULO_RED,
            MODULO_SENSORES,
            MODULO_ACTUADORES,
            MODULO_SMARTMETER,
            MODULO_PROYECTOS);
        sort($modulos);

        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), ID_NINGUNO, $modulo_seleccionado);
        foreach ($modulos AS $modulo)
        {
            $nombre_modulo = dame_nombre_modulo($modulo);
            $lista .= "<option value='".$modulo."'";
            if ($modulo == $modulo_seleccionado)
            {
                $lista .= " selected";
            }
            $lista .= ">".htmlspecialchars($nombre_modulo, ENT_QUOTES)."</option>";
        }
        return ($lista);
    }


    function dame_lista_modulos_defecto_usuario(
        $id_usuario,
        $perfil_usuario,
        $id_red,
        $modulo_seleccionado)
    {
        // Si no hay usuario seleccionado, se devuelve ninguno
        $idiomas = new Idiomas();
        if ($id_usuario === "")
        {
            $lista_modulos = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), ID_NINGUNO, $modulo_seleccionado);
            return ($lista_modulos);
        }

        // Si no hay red seleccionada, se muestra sólo el módulo seleccionado (no es visible)
        if ($id_red == ID_NINGUNO)
        {
            $modulos = array($modulo_seleccionado);
        }
        else
        {
            // Si el usuario es estándar, tiene el módulo personal y no se muestran otros módulos
            // (sólo se permiten los módulos administración y personal)
            $modulos = dame_modulos_usuario($id_usuario, $perfil_usuario, $id_red);
            if (($perfil_usuario == PERFIL_USUARIO_ESTANDAR) && (in_array(MODULO_PERSONAL, $modulos) == true))
            {
                $mostrar_modulos_diferentes_modulo_personal = ($_SESSION["parametros_modulo_personal"]["mostrar_otros_modulos"] == VALOR_SI);
                if ($mostrar_modulos_diferentes_modulo_personal == false)
                {
                    $modulos = array(MODULO_ADMINISTRACION, MODULO_PERSONAL);
                }
            }
            sort($modulos);
        }

        // Se crea la lista de módulos
        $lista_modulos = dame_opcion_valor_lista_simple($idiomas->_("Ninguno"), ID_NINGUNO, $modulo_seleccionado);
        foreach ($modulos AS $modulo)
        {
            $nombre_modulo = dame_nombre_modulo($modulo);
            $lista_modulos .= "<option value='".$modulo."'";
            if ($modulo == $modulo_seleccionado)
            {
                $lista_modulos .= " selected";
            }
            $lista_modulos .= ">".htmlspecialchars($nombre_modulo, ENT_QUOTES)."</option>";
        }
        return ($lista_modulos);
    }


    function dame_lista_secciones_modulo($modulo, $secciones, $secciones_seleccionadas)
    {
        $idiomas = new Idiomas();

        switch ($modulo)
        {
            case ID_NINGUNO:
            {
                $lista_secciones = dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), ID_NINGUNO, NULL);
                break;
            }
            case MODULO_ADMINISTRACION:
            {
                $lista_secciones = ModuloAdministracion::dame_lista_secciones($secciones, $secciones_seleccionadas);
                break;
            }
            case MODULO_MONITORIZACION:
            {
                $lista_secciones = ModuloMonitorizacion::dame_lista_secciones($secciones, $secciones_seleccionadas);
                break;
            }
            case MODULO_PERSONAL:
            {
                $lista_secciones = ModuloPersonal::dame_lista_secciones($secciones, $secciones_seleccionadas);
                break;
            }
            case MODULO_RED:
            {
                $lista_secciones = ModuloRed::dame_lista_secciones($secciones, $secciones_seleccionadas);
                break;
            }
            case MODULO_LOCALIZACIONES:
            {
                $lista_secciones = ModuloLocalizaciones::dame_lista_secciones($secciones, $secciones_seleccionadas);
                break;
            }
            case MODULO_SENSORES:
            {
                $lista_secciones = ModuloSensores::dame_lista_secciones($secciones, $secciones_seleccionadas);
                break;
            }
            case MODULO_ACTUADORES:
            {
                $lista_secciones = ModuloActuadores::dame_lista_secciones($secciones, $secciones_seleccionadas);
                break;
            }
            case MODULO_SMARTMETER:
            {
                $lista_secciones = ModuloSmartmeter::dame_lista_secciones($secciones, $secciones_seleccionadas);
                break;
            }
            case MODULO_PROYECTOS:
            {
                $lista_secciones = ModuloProyectos::dame_lista_secciones($secciones, $secciones_seleccionadas);
                break;
            }
            default:
            {
                throw new Exception("Módulo desconocido: '".$modulo."'");
            }
        }
        return ($lista_secciones);
    }


    function dame_lista_secciones_modulo_defecto_usuario(
        $id_usuario,
        $id_red,
        $modulo,
        $seccion_seleccionada)
    {
        // Si no hay usuario seleccionado, se devuelve ninguna
        $idiomas = new Idiomas();
        if ($id_usuario === "")
        {
            $lista_secciones = dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), ID_NINGUNO, NULL);
            return ($lista_secciones);
        }

        // Se recuperan las secciones del módulo del usuario (si es usuario estándar) y se crea la lista de secciones
        $secciones_modulo = NULL;
        $fila_usuario = dame_fila_usuario($id_usuario);
        $perfil = $fila_usuario["perfil"];
        if ($perfil == PERFIL_USUARIO_ESTANDAR)
        {
            $secciones = dame_secciones_usuario($id_usuario, $id_red);
            if (array_key_exists($modulo, $secciones) == true)
            {
                $secciones_modulo = $secciones[$modulo];
            }
        }
        $lista_secciones = dame_lista_secciones_modulo($modulo, $secciones_modulo, array($seccion_seleccionada));
        return ($lista_secciones);
    }


    function dame_lista_acciones_iniciales_modulo_seccion($modulo, $seccion, $accion_inicial_seleccionada)
    {
        $idiomas = new Idiomas();

        $lista = dame_opcion_valor_lista_simple($idiomas->_("Ninguna"), ID_NINGUNO, $accion_inicial_seleccionada);
        switch ($modulo)
        {
            case MODULO_PERSONAL:
            {
                switch ($seccion)
                {
                    case SECCION_PERSONAL_WIDGETS:
                    {
                        $lista .= dame_opcion_valor_lista_simple(
                            dame_descripcion_accion_inicial(ACCION_INICIAL_ACTUALIZACION_PERIODICA_WIDGETS),
                            ACCION_INICIAL_ACTUALIZACION_PERIODICA_WIDGETS,
                            $accion_inicial_seleccionada);
                        break;
                    }
                }
                break;
            }
        }
        return ($lista);
    }


    function dame_descripcion_accion_inicial($accion_inicial)
    {
        switch ($accion_inicial)
        {
            case ACCION_INICIAL_ACTUALIZACION_PERIODICA_WIDGETS:
            {
                $descripcion = "Actualización periódica de widgets";
                break;
            }
            default:
            {
                $descripcion = "Desconocida";
                break;
            }
        }

        $idiomas = new Idiomas();
        return ($idiomas->_($descripcion));
    }


    //
    // Funciones de validaciones de usuario y contraseña
    //


    function dame_id_usuario_valido($id_usuario)
    {
        if (strlen($id_usuario) < NUMERO_MINIMO_CARACTERES_ID_USUARIO)
        {
            return (false);
        }
        return (true);
    }


    function dame_contrasenya_usuario_valida($contrasenya)
    {
        if (strlen($contrasenya) < NUMERO_MINIMO_CARACTERES_CONTRASENYA_USUARIO)
        {
            return (false);
        }
        $caracteres_correctos = (intval(preg_match('/^[a-z\d]+$/i', $contrasenya)) &&
            (preg_match('/[a-z]/i', $contrasenya)) &&
            (preg_match('/\d/', $contrasenya)));
        return ($caracteres_correctos);
    }
?>
