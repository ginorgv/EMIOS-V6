<?php
	session_start();

	include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');
    include_once($_SESSION["directorio"].'/comun/src/lib/Idiomas/Idiomas.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
	include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_bases_datos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/util_modulos_web.php');


    // Constantes

    // Indices de preferencias de módulos
    define("INDICE_PREFERENCIAS_MODULOS_USUARIO_MODO_SELECCION_LOCALIZACION_ACTUAL", 0);
	define("INDICE_PREFERENCIAS_MODULOS_USUARIO_MODULO_DEFECTO", 1);
    define("INDICE_PREFERENCIAS_MODULOS_USUARIO_SECCION_DEFECTO", 2);
    define("INDICE_PREFERENCIAS_MODULOS_USUARIO_ACCION_INICIAL", 3);
    define("INDICE_PREFERENCIAS_MODULOS_USUARIO_PARAMETROS_ACCION_INICIAL", 4);


	// Clase que representa a un usuario
	class Usuario
	{
		// Funciones estáticas de usuario


		// Devuelve la cabecera para la tabla de usuarios
		static function dame_cabecera_tabla()
		{
			$idiomas = new Idiomas();

			return (array(
                $idiomas->_("Identificador"),
                $idiomas->_("Nombre"),
                $idiomas->_("Perfil")
			));
		}


        // Devuelve la consulta para la tabla de usuarios
        static function dame_consulta_usuarios($filtro, $perfil)
        {
            $bd_red = BaseDatosRed::dame_base_datos();

            switch ($_SESSION["perfil"])
            {
                case PERFIL_USUARIO_ESTANDAR:
                {
                    $consulta = "
                        SELECT *
                        FROM usuarios
                        WHERE
                            id = '".$_SESSION["id_usuario"]."'
                        ORDER BY id ASC";
                    break;
                }
                default:
                {
                    // No hay red seleccionada
                    if ($_SESSION["id_red"] == ID_NINGUNO)
                    {
                        switch ($_SESSION["perfil"])
                        {
                            case PERFIL_USUARIO_ADMINISTRADOR:
                            {
                                // Redes del usuario actual
                                $consulta_redes_usuarios = "
                                    SELECT red
                                    FROM redes_usuarios
                                    WHERE
                                        usuario = '".$_SESSION["id_usuario"]."'";
                                $res_redes_usuarios = $bd_red->ejecuta_consulta($consulta_redes_usuarios);
                                if ($res_redes_usuarios == false)
                                {
                                    throw new Exception("Error en la consulta: '".$consulta_redes_usuarios."'");
                                }
                                $ids_redes = array();
                                while ($fila_red_usuario = $res_redes_usuarios->dame_siguiente_fila())
                                {
                                    array_push($ids_redes, $fila_red_usuario['red']);
                                }

                                // Usuarios estándar con alguna red de las redes del usuario actual
                                $ids_usuarios = array();
                                if (count($ids_redes) > 0)
                                {
                                    $consulta_usuarios = "
                                        SELECT usuarios.id AS id
                                        FROM
                                            usuarios,
                                            redes_usuarios
                                        WHERE
                                            (usuarios.perfil = '".PERFIL_USUARIO_ESTANDAR."')
                                            AND (redes_usuarios.usuario = usuarios.id)";
                                    $cadena_ids_redes_consulta = dame_cadena_ids_consulta($ids_redes);
                                    $consulta_usuarios .= "
                                        AND (redes_usuarios.red IN (".$cadena_ids_redes_consulta."))";
                                    $res_usuarios = $bd_red->ejecuta_consulta($consulta_usuarios);
                                    if ($res_usuarios == false)
                                    {
                                        throw new Exception("Error en la consulta: '".$consulta_usuarios."'");
                                    }
                                    while ($fila_usuario = $res_usuarios->dame_siguiente_fila())
                                    {
                                        array_push($ids_usuarios, "'".$fila_usuario['id']."'");
                                    }
                                }

                                // Información de usuarios estándar obtenida en la consulta anterior (más la información del usuario actual)
                                $consulta = "
                                    SELECT
                                        id,
                                        nombre,
                                        perfil
                                    FROM usuarios
                                    WHERE
                                        ((id = '".$_SESSION["id_usuario"]."')";
                                if (count($ids_usuarios) > 0)
                                {
                                    $cadena_ids_usuarios_consulta = dame_cadena_ids_consulta($ids_usuarios);
                                    $consulta .= "
                                        OR (id IN (".$cadena_ids_usuarios_consulta."))";
                                }
                                $consulta .= ")";
                                if ($filtro != "")
                                {
                                    $campos = array("id", "nombre");
                                    $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                                    $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
                                }
                                if ($perfil != PERFIL_USUARIO_TODOS)
                                {
                                    $consulta .= " AND (perfil = '".$perfil."')";
                                }
                                $consulta .= "
                                    ORDER BY id ASC";
                                break;
                            }
                            case PERFIL_USUARIO_SUPERADMINISTRADOR:
                            {
                                $consulta = "
                                    SELECT
                                        id,
                                        nombre,
                                        perfil
                                    FROM usuarios";
                                if ($filtro != "")
                                {
                                    $campos = array("id", "nombre");
                                    $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                                    $consulta .= " WHERE ".$condicion_consulta_filtro_busqueda;
                                }
                                if ($perfil != PERFIL_USUARIO_TODOS)
                                {
                                    if ($filtro != "")
                                    {
                                        $consulta .= " AND (perfil = '".$perfil."')";
                                    }
                                    else
                                    {
                                        $consulta .= " WHERE (perfil = '".$perfil."')";
                                    }
                                }
                                $consulta .= "
                                    ORDER BY id ASC";
                                break;
                            }
                        }
                    }
                    else
                    {
                        // Hay red seleccionada
                        switch ($_SESSION["perfil"])
                        {
                            case PERFIL_USUARIO_ADMINISTRADOR:
                            {
                                // Usuarios estándar con la red actual
                                $consulta_usuarios = "
                                    SELECT usuarios.id AS id
                                    FROM
                                        usuarios,
                                        redes_usuarios
                                    WHERE
                                        (redes_usuarios.red = '".$_SESSION["id_red"]."')
                                        AND (usuarios.id = redes_usuarios.usuario)
                                        AND (usuarios.perfil = '".PERFIL_USUARIO_ESTANDAR."')";
                                if ($filtro != "")
                                {
                                    $campos = array("usuarios.id", "usuarios.nombre");
                                    $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                                    $consulta_usuarios .= " AND ".$condicion_consulta_filtro_busqueda;
                                }
                                $res_usuarios = $bd_red->ejecuta_consulta($consulta_usuarios);
                                if ($res_usuarios == false)
                                {
                                    throw new Exception("Error en la consulta: '".$consulta_usuarios."'");
                                }
                                $ids_usuarios = array();
                                while ($fila_usuario = $res_usuarios->dame_siguiente_fila())
                                {
                                    array_push($ids_usuarios, "'".$fila_usuario['id']."'");
                                }

                                // Información de usuarios estándar obtenida en la consulta anterior (más la información del usuario actual)
                                $consulta = "
                                    SELECT
                                        id,
                                        nombre,
                                        perfil
                                    FROM usuarios
                                    WHERE
                                        ((id = '".$_SESSION["id_usuario"]."')";
                                if (count($ids_usuarios) > 0)
                                {
                                    $cadena_ids_usuarios_consulta = dame_cadena_ids_consulta($ids_usuarios);
                                    $consulta .= "
                                        OR (id IN (".$cadena_ids_usuarios_consulta."))";
                                }
                                $consulta .= ")";
                                if ($filtro != "")
                                {
                                    $campos = array("id", "nombre");
                                    $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                                    $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
                                }
                                if ($perfil != PERFIL_USUARIO_TODOS)
                                {
                                    $consulta .= " AND (perfil = '".$perfil."')";
                                }
                                $consulta .= "
                                    ORDER BY id ASC";
                                break;
                            }
                            case PERFIL_USUARIO_SUPERADMINISTRADOR:
                            {
                                // Se muestran todos los usuarios de la red actual (pero no el propio usuario superadministrador)
                                $consulta = "
                                    SELECT
                                        usuarios.id AS id,
                                        usuarios.nombre AS nombre,
                                        usuarios.perfil AS perfil
                                    FROM
                                        usuarios,
                                        redes_usuarios
                                    WHERE
                                        (redes_usuarios.red = '".$_SESSION["id_red"]."')
                                        AND (usuarios.id = redes_usuarios.usuario)";
                                if ($filtro != "")
                                {
                                    $campos = array("usuarios.id", "usuarios.nombre");
                                    $condicion_consulta_filtro_busqueda = dame_condicion_consulta_filtro_busqueda($campos, $filtro);
                                    $consulta .= " AND ".$condicion_consulta_filtro_busqueda;
                                }
                                if ($perfil != PERFIL_USUARIO_TODOS)
                                {
                                    $consulta .= " AND (usuarios.perfil = '".$perfil."')";
                                }
                                $consulta .= "
                                    ORDER BY usuarios.id ASC";
                                break;
                            }
                        }
                    }
                }
            }

			return ($consulta);
        }


        // Devuelve la tabla de usuarios
        static function dame_tabla_usuarios($filtro, $perfil)
        {
            $idiomas = new Idiomas();
            $bd_red = BaseDatosRed::dame_base_datos();

            $opciones = array();
            $mostrar_boton_anyadir_usuario = ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR);
            if ($mostrar_boton_anyadir_usuario == true)
            {
                $boton_anyadir_usuario = "<i id='anyade_modifica_usuario' class='icon-plus color-blanco boton_administracion_mostrar_ventana_anyadir_modificar_usuario boton-tabla-datos'></i>";
                array_push($opciones, $boton_anyadir_usuario);
            }
            $boton_actualizar_tabla_usuarios = "<i id='actualiza_usuarios' class='icon-refresh color-blanco boton_administracion_actualizar_tabla_usuarios boton-tabla-datos'></i>";
            array_push($opciones, $boton_actualizar_tabla_usuarios);
            $boton_ayuda_tabla_usuarios = "<i id='ayuda_usuarios' class='icon-question-sign color-blanco boton_administracion_ayuda_tabla_usuarios boton-tabla-datos'></i>";
            array_push($opciones, $boton_ayuda_tabla_usuarios);

            // Se crea la tabla
            $params_tabla = array(
                "opciones" => $opciones,
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_USUARIOS,
                "tipo_fila" => TIPO_FILA_TABLA_DATOS_DETALLES,
                "generar_valores_xml" => true
            );
            $tabla = new TablaDatos(
                "tabla-administracion-usuarios",
                $idiomas->_("Usuarios"),
                TIPO_TABLA_DATOS_LISTA,
                $params_tabla
            );
            $cabecera = Usuario::dame_cabecera_tabla();
            $tabla->anyade_cabecera("", $cabecera);

            // Se añade cada uno de los usuarios a la tabla y el pie de tabla
            $consulta = Usuario::dame_consulta_usuarios($filtro, $perfil);
			$res = $bd_red->ejecuta_consulta($consulta);
            if ($res == false)
            {
                throw new Exception("Error en la consulta: '".$consulta."'");
            }
            $numero_usuarios = $res->dame_numero_filas();
            while ($fila = $res->dame_siguiente_fila())
            {
                $usuario = new Usuario($fila);
                $params_fila = array(
                    "opciones" => $usuario->dame_opciones_tabla()
                );
                $tabla->anyade_fila(
                    "datosUsuario__".$fila["id"],
                    $usuario->dame_datos_tabla(),
                    $params_fila
                );
            }
			$tabla->anyade_pie($idiomas->_("Usuarios").": ".$numero_usuarios);

            return ($tabla->dame_tabla());
        }


		// Miembros de usuario


		public $idiomas;

		public $id;
        public $params;


		// Funciones de usuario


		function __construct($params = array())
		{
			$this->idiomas = new Idiomas();

            $this->id = $params["id"];
			$this->params = $params;
		}


		// Datos para la tabla
		function dame_datos_tabla()
		{
            // Iconos de datos erróneos
            $icono_fila_con_errores = dame_icono_fila_con_errores();
            $icono_dato_erroneo = dame_icono_dato_erroneo();

            // Inicialización de datos de la tabla
            $id = $icono_dato_erroneo;
            $nombre = $icono_dato_erroneo;
            $nombre_perfil = $icono_dato_erroneo;

            // Se recuperan los datos de la tabla
            $id_correcto = false;
            try
            {
                // Id
                $id = htmlspecialchars($this->id, ENT_QUOTES);
                $id_correcto = true;

                // Nombre
                $nombre = htmlspecialchars($this->params["nombre"], ENT_QUOTES);

                // Nombre de perfil
                $nombre_perfil = Usuario::dame_descripcion_perfil_usuario($this->params["perfil"]);
            }
            catch (Exception $e)
            {
                // Se añade información de la excepción en el log
                $log = dame_log();
                $log->error("[".$_SESSION["id_usuario"]."] "."Excepción capturada: ", $e);

                // Se añade icono de error en el id
                if ($id_correcto == true)
                {
                    $id = "[".$icono_fila_con_errores."] ".$id;
                }
            }

            // Se devuelven los datos de la tabla
            return (array(
                $id,
                $nombre,
                $nombre_perfil
			));
		}


		// Devuelve las opciones para mostrar en la tabla
		function dame_opciones_tabla()
		{
            switch ($_SESSION["perfil"])
            {
                case PERFIL_USUARIO_ESTANDAR:
                {
                    $editar = "<i id='anyade_modifica__".$this->id."' class='icon-pencil color-gris boton_administracion_mostrar_ventana_anyadir_modificar_usuario boton-tabla-datos'></i>";
                    $opciones = array($editar);
                    break;
                }
                default:
                {
                    if (strtolower($this->id) == $_SESSION["id_usuario"])
                    {
                        $editar = "<i id='anyade_modifica__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' class='icon-pencil color-gris boton_administracion_mostrar_ventana_anyadir_modificar_usuario boton-tabla-datos'></i>";
                        $duplicar = "<i class='icon-copy color-gris-muy-claro'></i>";
                        $vacio = "<i class='icon-remove color-gris-muy-claro'></i>";
                        $opciones = array($vacio, $duplicar, $editar);
                    }
                    else
                    {
                        $editar = "<i id='anyade_modifica__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_MODIFICACION."' class='icon-pencil color-gris boton_administracion_mostrar_ventana_anyadir_modificar_usuario boton-tabla-datos'></i>";
                        $duplicar = "<i id='anyade_modifica__".$this->id."__".TIPO_OPERACION_ADMINISTRACION_DUPLICADO."' class='icon-copy color-gris boton_administracion_mostrar_ventana_anyadir_modificar_usuario boton-tabla-datos'></i>";
                        $borrar = "<i id='elimina__".$this->id."' class='icon-remove color-gris boton_administracion_eliminar_usuario boton-tabla-datos'></i>";
                        $opciones = array($borrar, $duplicar, $editar);
                    }
                    break;
                }
            }
			return ($opciones);
		}


        // Devuelve los detalles de la tabla
        function dame_detalles_tabla()
		{
            $info = "";

            // Si el usuario es administrador o estándar sin red seleccionada actual, se muestran las redes asignadas
            if (($this->params["perfil"] == PERFIL_USUARIO_ADMINISTRADOR) OR
                (($this->params["perfil"] == PERFIL_USUARIO_ESTANDAR) AND ($_SESSION["id_red"] == ID_NINGUNO)))
            {
                // Si el perfil del usuario es estándar y el usuario actual es administrador,
                // sólo se mostrarán las redes que el usuario actual (administrador) tiene asignadas
                $comprobar_redes_asignadas_usuario_actual = false;
                if (($this->params["perfil"] == PERFIL_USUARIO_ESTANDAR) AND ($_SESSION["perfil"] == PERFIL_USUARIO_ADMINISTRADOR))
                {
                    $comprobar_redes_asignadas_usuario_actual = true;
                    $nombres_redes_asignadas_usuario_actual = dame_nombres_redes_usuario($_SESSION["id_usuario"], $_SESSION["perfil"]);
                }
                $nombres_redes_asignadas = dame_nombres_redes_usuario($this->id, $this->params["perfil"]);

                $info .= "<i class='icon-info-sign color-azul'></i> ";
                $numero_redes = 0;
                if (count($nombres_redes_asignadas) > 0)
                {
                    $lista_redes .= "<ul>";
                    foreach ($nombres_redes_asignadas as $nombre_red_asignada)
                    {
                        if (($comprobar_redes_asignadas_usuario_actual == true) AND (in_array($nombre_red_asignada, $nombres_redes_asignadas_usuario_actual) == false))
                        {
                            continue;
                        }
                        $lista_redes .= "<li>".htmlspecialchars($nombre_red_asignada, ENT_QUOTES)."</li>";
                        $numero_redes += 1;
                    }
                    $lista_redes .= "</ul>";
                }
                if ($numero_redes > 0)
                {
                    $texto_redes = $numero_redes;
                    if ($numero_redes == 1)
                    {
                        $texto_redes .= " ".$this->idiomas->_("red asignada");
                    }
                    else
                    {
                        $texto_redes .= " ".$this->idiomas->_("redes asignadas");
                    }
                    $info .= $this->idiomas->_("Este usuario tiene")." ".$texto_redes.":";
                    $info .= $lista_redes;
                }
                else
                {
                    $info .= $this->idiomas->_("Este usuario no tiene ninguna red asignada")."<br/>";
                }
                $info .= "<br/>";
            }

            // Se recuperan los módulos activos del usuario (y las secciones si el usuario es estándar)
            if (($this->params["perfil"] == PERFIL_USUARIO_ESTANDAR) AND ($_SESSION["id_red"] != ID_NINGUNO))
            {
                $modulos = dame_modulos_usuario($this->id, $this->params["perfil"], $_SESSION["id_red"]);
                $modulos = dame_modulos_ordenados($modulos);
                $secciones = dame_secciones_usuario($this->id, $_SESSION["id_red"]);
                $numero_modulos = 0;
                $nombres_modulos = "<ul>";
                foreach ($modulos as $modulo)
                {
                    switch ($modulo)
                    {
                        case MODULO_ADMINISTRACION:
                        case MODULO_MONITORIZACION:
                        {
                            break;
                        }
                        default:
                        {
                            $nombres_modulos .= "<li>".dame_nombre_modulo($modulo)."</li>";
                            if (array_key_exists($modulo, $secciones) == true)
                            {
                                $secciones_modulo = $secciones[$modulo];
                                $secciones_modulo = dame_secciones_modulo_ordenadas($secciones_modulo, $modulo);
                                $nombres_modulos .= "<ul>";
                                foreach ($secciones_modulo as $seccion_modulo)
                                {
                                    $nombres_modulos .= "<li>".dame_descripcion_seccion_modulo($modulo, $seccion_modulo)."</li>";
                                }
                                $nombres_modulos .= "</ul>";
                            }
                            $numero_modulos += 1;
                            break;
                        }
                    }
                }
                $nombres_modulos .= "</ul>";
                $info .= "<i class='icon-info-sign color-azul'></i> ";
                if ($numero_modulos > 0)
                {
                    $texto_modulos = $numero_modulos;
                    if ($numero_modulos == 1)
                    {
                        $texto_modulos .= " ".$this->idiomas->_("módulo activado");
                    }
                    else
                    {
                        $texto_modulos .= " ".$this->idiomas->_("módulos activados");
                    }
                    $texto_modulos .= ": ".$nombres_modulos;
                    $info .= $this->idiomas->_("Este usuario tiene")." ".$texto_modulos;
                }
                else
                {
                    $info .= $this->idiomas->_("Este usuario no tiene ningún módulo activado")."<br/>";
                }
                $info .= "<br/>";
            }

            // Información de API HTTP
            $info .= "<i class='icon-info-sign color-azul'></i> ";
            $info .= $this->idiomas->_("API HTTP").":";
            $parametros_api_http = "<ul>";
            if ($this->params["perfil"] == PERFIL_USUARIO_ESTANDAR)
            {
                $parametros_api_http .= "<li>";
                $parametros_api_http .= $this->idiomas->_("Habilitado").": ";
                $parametros_api_http .= dame_descripcion_valores_si_no($this->params["api_http"]);
                $parametros_api_http .= "</li>";
            }
            $parametros_api_http .= "<li>";
            $parametros_api_http .= $this->idiomas->_("Contraseña").": ";
            $parametros_api_http .= $this->params["contrasenya_api_http"];
            $parametros_api_http .= "</li>";
            $parametros_api_http .= "</ul>";
            $info .= $parametros_api_http;

			return ($info);
        }


        //
        // Funciones de descripciones
        //


        // Devuelve la descripción de un perfil de usuario
		static function dame_descripcion_perfil_usuario($perfil)
		{
			$idiomas = new Idiomas();

			switch ($perfil)
            {
                case PERFIL_USUARIO_ESTANDAR:
                {
                    $descripcion = $idiomas->_("Estándar");
                    break;
                }
                case PERFIL_USUARIO_ADMINISTRADOR:
                {
                    $descripcion = $idiomas->_("Administrador");
                    break;
                }
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    $descripcion = $idiomas->_("Superusuario");
                    break;
                }
                default:
                {
                    $descripcion = $idiomas->_("Desconocido");
                }
            }
            return ($descripcion);
		}


        // Devuelve la desdcripcion del tipo de contraseña de un usuario
		static function dame_descripcion_tipo_contrasenya_usuario($tipo_contrasenya_usuario)
		{
			$idiomas = new Idiomas();

			switch ($tipo_contrasenya_usuario)
            {
                case TIPO_CONTRASENYA_USUARIO_PERSONAL:
                {
                    $descripcion = $idiomas->_("Personal");
                    break;
                }
                case TIPO_CONTRASENYA_USUARIO_ADMINISTRADOR:
                {
                    $descripcion = $idiomas->_("Administrador");
                    break;
                }
                case TIPO_CONTRASENYA_USUARIO_SUPERADMINISTRADOR:
                {
                    $descripcion = $idiomas->_("Superadministrador");
                    break;
                }
                default:
                {
                    $descripcion = $idiomas->_("Desconocido");
                    break;
                }
            }
            return ($descripcion);
		}
    }
?>
