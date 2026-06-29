<?php
	session_start();

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloWebEmios.php');

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');

    include_once($_SESSION["directorio"].'/src/lib/BasesDatos/BaseDatosRed.php');
    include_once($_SESSION["directorio"].'/src/lib/constantes/constantes.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_usuarios.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Clientes/Cliente.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Preferencias/Preferencias.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Licencias/Licencia.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/Usuario.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloAdministracion/Usuarios/util_administracion_usuarios.php');


	class ModuloAdministracion extends ModuloWebEmios
	{
        function __construct()
        {
            // Super
            parent::__construct(MODULO_ADMINISTRACION, NOMBRE_MODULO_ADMINISTRACION);
        }


        //
        // Funciones virtuales sobreescritas
        //


        static function dame_secciones($parametros_extra)
        {
            switch ($_SESSION["perfil"])
            {
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    $secciones = array(
                        SECCION_ADMINISTRACION_REDES,
                        SECCION_ADMINISTRACION_USUARIOS,
                        SECCION_ADMINISTRACION_PREFERENCIAS,
                        SECCION_ADMINISTRACION_SELECCION_RED
                    );
                    break;
                }
                case PERFIL_USUARIO_ADMINISTRADOR:
                {
                    $secciones = array(
                        SECCION_ADMINISTRACION_USUARIOS,
                        SECCION_ADMINISTRACION_SELECCION_RED
                    );
                    break;
                }
                case PERFIL_USUARIO_ESTANDAR:
                {
                    $ids_redes = dame_ids_redes_usuario($_SESSION["id_usuario"], $_SESSION["perfil"]);
                    if (count($ids_redes) > 1)
                    {
                        $secciones = array(
                            SECCION_ADMINISTRACION_USUARIOS,
                            SECCION_ADMINISTRACION_SELECCION_RED
                        );
                    }
                    else
                    {
                        $secciones = array(
                            SECCION_ADMINISTRACION_USUARIOS
                        );
                    }
                    break;
                }
            }
            return ($secciones);
        }


        static function dame_descripcion_seccion($seccion)
        {
            switch ($seccion)
            {
                case SECCION_ADMINISTRACION_REDES:
                {
                    $descripcion = "Redes";
                    break;
                }
                case SECCION_ADMINISTRACION_USUARIOS:
                {
                    $descripcion = "Usuarios";
                    break;
                }
                case SECCION_ADMINISTRACION_PREFERENCIAS:
                {
                    $descripcion = "Preferencias";
                    break;
                }
                case SECCION_ADMINISTRACION_SELECCION_RED:
                {
                    $descripcion = "Selección de red";
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


        function dame_contenido_seccion($seccion, $parametros_extra)
		{
            // Módulo
            $html = "<div id='modulo' name='".MODULO_ADMINISTRACION."' hidden></div>";

            // Se añade el contenido de la sección
            $GLOBALS["reutilizar_consultas_bases_datos"] = true;
            $res = "OK";
            switch ($seccion)
            {
                case SECCION_ADMINISTRACION_REDES:
                {
                    $html .= $this->dame_redes();
                    break;
                }
                case SECCION_ADMINISTRACION_USUARIOS:
                {
                    $html .= $this->dame_usuarios();
                    break;
                }
                case SECCION_ADMINISTRACION_PREFERENCIAS:
                {
                    $html .= $this->dame_preferencias();
                    break;
                }
                case SECCION_ADMINISTRACION_SELECCION_RED:
                {
                    $html .= $this->dame_seleccion_red();
                    break;
                }
                default:
                {
                    $res = "ERROR";
                    $msg = $this->idiomas->_("Sección desconocida");
                    break;
                }
            }

            print(json_encode(array(
                "res" => $res,
                "msg" => $msg,
                "html" => $html))
            );
		}


        //
        // Funciones sobreescritas
        //


        static function dame_seccion_defecto($secciones = NULL)
        {
            switch ($_SESSION["perfil"])
            {
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    $seccion_defecto = SECCION_ADMINISTRACION_SELECCION_RED;
                    break;
                }
                case PERFIL_USUARIO_ADMINISTRADOR:
                case PERFIL_USUARIO_ESTANDAR:
                {
                    $ids_redes = dame_ids_redes_usuario($_SESSION["id_usuario"], $_SESSION["perfil"]);
                    if (count($ids_redes) > 1)
                    {
                        $seccion_defecto = SECCION_ADMINISTRACION_SELECCION_RED;
                    }
                    else
                    {
                        $seccion_defecto = SECCION_ADMINISTRACION_USUARIOS;
                    }
                }
            }
            return ($seccion_defecto);
        }


		//
		// Funciones para obtener el contenido de las secciones
		//


		function dame_redes()
		{
            $contenido = "
				<div id='tabs' class='tabbable'>";
			$contenido .= "
					<ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-clientes'>".$this->idiomas->_("Clientes")."</a></li>
						<li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-redes'>".$this->idiomas->_("Redes")."</a></li>
					</ul>
					<div id='tabs-administracion-redes' class='tab-content'>";

            $contenido .= "
						<div class='tab-pane' id='tab-clientes'>
                            <div id='tablaClientes'>".Cliente::dame_tabla_clientes()."</div>
						</div>";

            $contenido .= "
						<div class='tab-pane active' id='tab-redes'>";
            $contenido .= $this->dame_tabla_filtro_redes_tabla();
            $contenido .= "
                            <div id='tabla".TIPO_NODO_RED."'>".
                                dame_tabla_nodos(TIPO_NODO_RED).
                            "</div>
						</div>";

			$contenido .= "
					</div>
				</div>";

			return ($contenido);
		}


		function dame_usuarios()
		{
            $contenido = "
				<div id='tabs' class='tabbable'>";
            if (($_SESSION["id_red"] == ID_NINGUNO) || ($_SESSION["perfil"] == PERFIL_USUARIO_ESTANDAR))
            {
                $contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-usuarios'>".$this->idiomas->_("Usuarios")."</a></li>
                    </ul>
                    <div id='tabs-administracion-usuarios' class='tab-content'>";

                $contenido .= "
                        <div class='tab-pane active' id='tab-usuarios'>";
                if (($_SESSION["id_red"] == ID_NINGUNO) && ($_SESSION["perfil"] != PERFIL_USUARIO_ESTANDAR))
                {
                    $contenido .= $this->dame_tabla_filtro_usuarios_tabla();
                }
                $contenido .= "
                            <div id='tablaUsuarios'>".Usuario::dame_tabla_usuarios("", PERFIL_USUARIO_TODOS)."</div>
                        </div>";
            }
            else
            {
                $contenido .= "
                    <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                        <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-licencias'>".$this->idiomas->_("Licencias")."</a></li>
                        <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-usuarios'>".$this->idiomas->_("Usuarios")."</a></li>
                    </ul>
                    <div id='tabs-administracion-usuarios' class='tab-content'>";

                $contenido .= "
                        <div class='tab-pane' id='tab-licencias'>
                            <div id='tablaLicencias'>".Licencia::dame_tabla_licencias()."</div>
                        </div>";

                $contenido .= "
                        <div class='tab-pane active' id='tab-usuarios'>";
                $contenido .= $this->dame_tabla_filtro_usuarios_tabla();
                $contenido .= "
                            <div id='tablaUsuarios'>".Usuario::dame_tabla_usuarios("", PERFIL_USUARIO_TODOS)."</div>
                        </div>";
            }
			$contenido .= "
					</div>
				</div>";

			return ($contenido);
		}


        function dame_preferencias()
		{
            $contenido .= "<div id='tablaPreferencias'>".Preferencias::dame_tabla_preferencias()."</div>";
			return ($contenido);
		}


		function dame_seleccion_red()
		{
            $bd_red = BaseDatosRed::dame_base_datos();

            $lista_redes = $this->idiomas->_("Red actual").": <br/>
                <select id='id_red_actual_administracion_seleccion_red' class='chosen-select'>";

            switch ($_SESSION["perfil"])
            {
                case PERFIL_USUARIO_ESTANDAR:
                case PERFIL_USUARIO_ADMINISTRADOR:
                {
                    $consulta_redes = "
                        SELECT
                            redes.id AS id,
                            clientes.nombre AS cliente,
                            redes.nombre AS nombre
                        FROM
                            redes_usuarios,
                            redes,
                            clientes
                        WHERE
                            (redes_usuarios.usuario = '".$_SESSION["id_usuario"]."')
                            AND (redes_usuarios.red = redes.id)
                            AND (redes.cliente = clientes.id)
                        ORDER BY
                            clientes.nombre ASC,
                            redes.nombre ASC";
                    break;
                }
                case PERFIL_USUARIO_SUPERADMINISTRADOR:
                {
                    $consulta_redes = "
                        SELECT
                            redes.id AS id,
                            clientes.nombre AS cliente,
                            redes.nombre AS nombre
                        FROM
                            redes,
                            clientes
                        WHERE
                            redes.cliente = clientes.id
                        ORDER BY
                            clientes.nombre ASC,
                            redes.nombre ASC";
                    break;
                }
            }
            $res_redes = $bd_red->ejecuta_consulta($consulta_redes);
            if ($res_redes == false)
            {
                throw new Exception("Error en la consulta: '".$consulta_redes."'");
            }
            $lista_redes .= "<option value='".ID_NINGUNO."'>".$this->idiomas->_("Ninguna")."</option>";
            while ($fila_red = $res_redes->dame_siguiente_fila())
            {
                $lista_redes .= "<option value='".$fila_red['id']."'";
                if ($fila_red['id'] == $_SESSION["id_red"])
                {
                    $lista_redes .= " selected";
                }
                $lista_redes .= ">".htmlspecialchars("[".$fila_red["cliente"]."] ".$fila_red['nombre'], ENT_QUOTES)."</option>";
            }
            $lista_redes .= "
                </select>";

            $boton = "
                <button id='boton_administracion_seleccionar_red_actual' class='boton-formulario btn-mini btn btn-success'>".
                    $this->idiomas->_("Seleccionar red")."
				</button>";

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-administracion-seleccion-red",
                $this->idiomas->_("Selección de red"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_dato" => "desplegable-simple margenes-verticales",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_PARAMETROS_SELECCION_RED)
            );
			$tabla->anyade_fila("seleccion-red", array($lista_redes, $boton), $params_fila);

            return ($tabla->dame_tabla());
		}


        //
        // Funciones auxiliares para obtener el contenido de las secciones
        //


        function dame_tabla_filtro_redes_tabla()
		{
            // Se recuperan los controles a mostrar
            $id_controles = "administracion_filtro_redes_tabla";
            $filtro_redes = dame_filtro_texto_controles_extra($id_controles, $this->idiomas->_("Cliente y nombre"), array());

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-administracion-filtro-redes-tabla",
                $this->idiomas->_("Filtro de redes"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_REDES_TABLA)
            );
			$tabla->anyade_fila("filtro-redes-tabla", $filtro_redes, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_tabla_filtro_usuarios_tabla()
		{
            // Se recuperan los controles a mostrar
            $controles = array();
            $id_controles = "administracion_filtro_usuarios_tabla";

            // Perfil del usuario
            $control_lista_perfiles = dame_control_lista_perfiles_usuario($id_controles, OPCIONES_EXTRA_LISTA_PERFILES_USUARIO_TODOS, $this->idiomas->_("Perfil"));
            array_push($controles, $control_lista_perfiles);
            $filtro_redes = dame_filtro_texto_controles_extra($id_controles, $this->idiomas->_("Identificador y nombre"), $controles);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-administracion-filtro-usuarios-tabla",
                $this->idiomas->_("Filtro de usuarios"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_USUARIOS_TABLA)
            );
			$tabla->anyade_fila("filtro-usuarios-tabla", $filtro_redes, $params_fila);

			return ($tabla->dame_tabla());
		}
    }
?>
