<?php
	session_start();

    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloWebEmios.php');

    include_once($_SESSION["directorio"].'/comun/src/lib/herramientas/TablaDatos.php');

    include_once($_SESSION["directorio"].'/src/lib/modulos/AccionesUsuario/AccionUsuario.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Alarmas/Alarma.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Comentarios/Comentario.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/mapas/util_mapa.php');
	include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/Nodo.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Nodos/util_nodos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/Procesado/OperacionDatosSensor.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_modulos.php');
    include_once($_SESSION["directorio"].'/src/lib/modulos/util_redes.php');
    include_once($_SESSION["directorio"].'/src/modulos/ModulosWeb/ModuloRed/util_modulo_red.php');


	class ModuloRed extends ModuloWebEmios
	{
        function __construct()
        {
            // Super
            parent::__construct(MODULO_RED, NOMBRE_MODULO_RED);
        }


        //
        // Funciones virtuales sobreescritas
        //


        static function dame_secciones($parametros_extra)
        {
            $secciones = array(
                SECCION_RED_PRINCIPAL,
                SECCION_RED_ALARMAS,
                SECCION_RED_ACCIONES_USUARIO,
                SECCION_RED_COMENTARIOS,
                SECCION_RED_TOPOLOGIA,
                SECCION_RED_MAPA
            );
            return ($secciones);
        }


        static function dame_descripcion_seccion($seccion)
        {
            switch ($seccion)
            {
                case SECCION_RED_PRINCIPAL:
                {
                    $descripcion = "Principal";
                    break;
                }
                case SECCION_RED_ALARMAS:
                {
                    $descripcion = "Alarmas";
                    break;
                }
                case SECCION_RED_ACCIONES_USUARIO:
                {
                    $descripcion = "Acciones";
                    break;
                }
                case SECCION_RED_COMENTARIOS:
                {
                    $descripcion = "Comentarios";
                    break;
                }
                case SECCION_RED_TOPOLOGIA:
                {
                    $descripcion = "Topología";
                    break;
                }
                case SECCION_RED_MAPA:
                {
                    $descripcion = "Mapa";
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
            $html = "<div id='modulo' name='".MODULO_RED."' hidden></div>";

            // Se añade el contenido de la sección
            $GLOBALS["reutilizar_consultas_bases_datos"] = true;
            $res = "OK";
            switch ($seccion)
            {
                case SECCION_RED_PRINCIPAL:
                {
                    if ($_SESSION["perfil"] == PERFIL_USUARIO_SUPERADMINISTRADOR)
                    {
                        $html .= $this->dame_herramientas_principal();
                    }
                    $html .= $this->dame_principal();
                    break;
                }
                case SECCION_RED_ALARMAS:
                {
                    $html .= $this->dame_tabla_filtro_alarmas();
                    $html .= $this->dame_tabla_alarmas();
                    break;
                }
                case SECCION_RED_ACCIONES_USUARIO:
                {
                    $html .= $this->dame_tabla_filtro_acciones_usuario();
                    $html .= $this->dame_tabla_acciones_usuario();
                    break;
                }
                case SECCION_RED_COMENTARIOS:
                {
                    $html .= $this->dame_tabla_filtro_comentarios();
                    $html .= $this->dame_tabla_comentarios();
                    break;
                }
                case SECCION_RED_TOPOLOGIA:
                {
                    $html .= $this->dame_tabla_filtro_topologia();
                    $html .= $this->dame_topologia();
                    break;
                }
                case SECCION_RED_MAPA:
                {
                    $html .= $this->dame_mapa();
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
		// Funciones para obtener el contenido de las secciones
		//


        function dame_herramientas_principal()
		{
			// Se recuperan los controles a mostrar
			$boton_recargar_configuraciones_dispositivos = "<br/><button id='boton_recargar_configuraciones_dispositivos' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_red'>".$this->idiomas->_("Recargar configuraciones de dispositivos")."</button><br/><br/>";
            $boton_leer_estado_dispositivos = "<br/><button id='boton_leer_estado_dispositivos' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_red'>".$this->idiomas->_("Leer estado de dispositivos")."</button><br/><br/>";
            $boton_ping = "<br/><button id='boton_ping' class='btn-mini btn btn-success boton_red_envia_accion_herramientas_red'>".$this->idiomas->_("Ping axones")."</button><br/><br/>";
            $botones = array(
                $boton_recargar_configuraciones_dispositivos,
                $boton_leer_estado_dispositivos,
                $boton_ping);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-red-herramientas-red",
                $this->idiomas->_("Herramientas de red"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

			$params_fila = array(
                "clase_fila" => "botones-herramientas",
                "clase_dato" => "boton-herramientas",
                "numero_columnas" => NUMERO_COLUMNAS_TABLA_HERRAMIENTAS_RED
            );
			$tabla->anyade_fila("botones-herramientas-red", $botones, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_principal()
		{
            // Se muestran las pestañas de dispositivos y axones sólo si el usuario es superadministrador o hay dispositivos en la red
            $numero_dispositivos = dame_numero_dispositivos();
            
            $id_red = $_SESSION["id_red"];
            $bd_red = BaseDatosRed::dame_base_datos();
            $consulta = "SELECT clientes.nombre FROM clientes INNER JOIN redes ON clientes.id=redes.cliente WHERE redes.id = ".$id_red;
            $res = $bd_red->ejecuta_consulta($consulta);
            $fila = $res->dame_siguiente_fila();
            $nombre_cliente = $fila["nombre"];

            $mostrar_pestanyas_dispositivos_axones = (($_SESSION["perfil"] == PERFIL_USUARIO_SUPERADMINISTRADOR) || ($numero_dispositivos > 0) || ($nombre_cliente == 'Future Sense'));
            

            if ($mostrar_pestanyas_dispositivos_axones == true)
            {
                $contenido = "
                    <div id='tabs' class='tabbable'>";
                $contenido .= "
                        <ul data-tabs='tabs' class='nav nav-tabs elemento-no-seleccionable'>
                            <li class='active'><a data-toggle='tab' class='titulo-pestanya' href='#tab-red'>".$this->idiomas->_("Red")."</a></li>";
                if ($mostrar_pestanyas_dispositivos_axones == true)
                {
                    $contenido .= "
                            <li><a data-toggle='tab' class='titulo-pestanya' href='#tab-dispositivos'>".$this->idiomas->_("Dispositivos")."</a></li>";
                    if ($_SESSION["perfil"] != PERFIL_USUARIO_ADMINISTRADOR)
                    {
                        $contenido .="<li><a data-toggle='tab' class='titulo-pestanya' href='#tab-axones'>".$this->idiomas->_("Axones")."</a></li>";
                    }
                }
                $contenido .= "
                        </ul>
                        <div id='tabs-red-principal' class='tab-content'>";

                $contenido .= "
                            <div class='tab-pane active' id='tab-red'>";
                $contenido .= $this->dame_informacion();
                $contenido .= "
                            </div>";

                if ($mostrar_pestanyas_dispositivos_axones == true)
                {
                    $contenido .= "
                            <div class='tab-pane' id='tab-dispositivos'>
                                <div id='tabla".TIPO_NODO_DISPOSITIVO."'>".
                                    dame_tabla_nodos(TIPO_NODO_DISPOSITIVO)."
                                </div>
                            </div>
                            
                            <div class='tab-pane' id='tab-axones'>
                                <div id='tabla".TIPO_NODO_AXON."'>".
                                    dame_tabla_nodos(TIPO_NODO_AXON)."
                                </div>
                            </div>";
                }
                $contenido .= "
                        </div>
                    </div>";
            }
            else
            {
                $contenido .= $this->dame_informacion();
            }

			return ($contenido);
		}


        function dame_informacion()
		{
            $tabla_informacion_red_actual = dame_tabla_informacion_red_actual();
            return ($tabla_informacion_red_actual->dame_tabla());
		}


		function dame_tabla_alarmas()
		{
            $contenido = "<div id='tablaAlarmas'>".Alarma::dame_tabla_alarmas(
                MODULO_RED,
                "",
                NULL,
                NULL)."</div>";
			return ($contenido);
		}


		function dame_tabla_acciones_usuario()
		{
            $contenido = "<div id='tablaAccionesUsuario'>".AccionUsuario::dame_tabla_acciones(
                MODULO_RED,
                "",
                NULL,
                NULL)."</div>";
			return ($contenido);
		}


		function dame_tabla_comentarios()
		{
            $contenido = "<div id='tablaComentarios'>".Comentario::dame_tabla_comentarios(MODULO_RED, "", NULL, NULL)."</div>";
			return ($contenido);
		}


        function dame_topologia()
		{
            // Se recuperan los controles a mostrar
			$topologia = "
                <div class='topologiaarbol' id='topologia-red'>
                    <div id='grafico-topologia-red' class='grafico-topologiaarbol'></div>
                </div>";

            // Se introduce la información en una tabla
            $boton_actualizar_topologia_red = "<i id='boton_red_actualizar_topologia_red' class='icon-refresh color-blanco boton-tabla-datos'></i>";
            $opciones = array($boton_actualizar_topologia_red);

             // Se crea la tabla contenedora
            $params_tabla = array(
                "opciones" => $opciones
            );
            $tabla = new TablaDatos(
                "tabla-red-topologia-red",
                $this->idiomas->_("Topología de red"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );
			$params_contenido = array("clase_contenido" => "contenedor-topologiaarbol");
			$tabla->anyade_contenido("", $topologia, $params_contenido);

            return ($tabla->dame_tabla());
		}


		function dame_mapa()
		{
            // Se recuperan los controles a mostrar
			$mapa = "<div class='mapa' id='mapa_seccion'></div>";

            // Se crea la tabla contenedora
            $params_tabla = array(
                "opciones" => dame_botones_mapa()
            );
            $tabla = new TablaDatos(
                "tabla-red-mapa",
                $this->idiomas->_("Mapa de red"),
                TIPO_TABLA_DATOS_CONTENEDOR,
                $params_tabla
            );

            $params_contenido = array(
                "clase_contenido" => "mapa",
                "sin_margenes" => true
            );
			$tabla->anyade_contenido("", $mapa, $params_contenido);

            return ($tabla->dame_tabla());
		}


        //
        // Funciones auxiliares para obtener el contenido de las secciones
        //


        function dame_tabla_filtro_alarmas()
		{
            // Se recuperan los controles a mostrar
            $filtro = dame_filtro_texto_fechas(
                "red_filtro_alarmas",
                "00:00",
                "23:59",
                $this->idiomas->_("Origen y descripción"),
                PERIODO_DEFECTO_RED_ALARMAS,
                "");

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-red-filtro-alarmas",
                $this->idiomas->_("Filtro de alarmas"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_ALARMAS_RED)
            );
			$tabla->anyade_fila("filtro-alarmas", $filtro, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_tabla_filtro_acciones_usuario()
		{
            // Se recuperan los controles a mostrar
            $filtro = dame_filtro_texto_fechas(
                "red_filtro_acciones_usuario",
                "00:00",
                "23:59",
                $this->idiomas->_("Usuario, tipo y objeto"),
                PERIODO_DEFECTO_RED_ACCIONES_USUARIO,
                "");
            $boton_exportar = dame_boton_formulario("red_exportar_acciones_usuario", $this->idiomas->_("Exportar acciones"));
            array_push($filtro, $boton_exportar);

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-red-filtro-acciones-usuario",
                $this->idiomas->_("Filtro de acciones"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "clase_ultima_fila" => "boton-formulario-ultima-fila",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_ACCIONES_USUARIO_RED),
                "numero_columnas" => NUMERO_COLUMNAS_FILTRO_ACCIONES_USUARIO_RED
            );
			$tabla->anyade_fila("filtro-acciones-usuario", $filtro, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_tabla_filtro_comentarios()
		{
            // Se recuperan los controles a mostrar
            $filtro = dame_filtro_texto_fechas(
                "red_filtro_comentarios",
                "00:00",
                "23:59",
                $this->idiomas->_("Usuario, tipo y objeto"),
                PERIODO_DEFECTO_RED_COMENTARIOS,
                "");

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-red-filtro-comentarios",
                $this->idiomas->_("Filtro de comentarios"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_COMENTARIOS_RED)
            );
			$tabla->anyade_fila("filtro-comentarios", $filtro, $params_fila);

			return ($tabla->dame_tabla());
		}


        function dame_tabla_filtro_topologia()
        {
            // Se recuperan los controles a mostrar
            $filtro_topologia = $this->dame_filtro_topologia("red_filtro_topologia_red");

            // Se crea la tabla contenedora
            $tabla = new TablaDatos(
                "tabla-red-filtro-topologia",
                $this->idiomas->_("Filtro de sensores y actuadores"),
                TIPO_TABLA_DATOS_CONTENEDOR
            );

            $params_fila = array(
                "clase_dato" => "filtro-informes",
                "anchuras_columnas" => unserialize(ANCHURAS_COLUMNAS_FILTRO_TOPOLOGIA_RED),
            );
            $tabla->anyade_fila("filtro-topologia", $filtro_topologia, $params_fila);

            return ($tabla->dame_tabla());
        }


        // Crea una lista de clases de sensor y una lista de clases de actuador con un botón para filtrar
        function dame_filtro_topologia()
        {
            $idiomas = new Idiomas();

            $id_controles = "red_filtro_topologia_red";
            $control_lista_clases_sensor = dame_control_lista_clases_sensor(
                $id_controles,
                OPCIONES_EXTRA_LISTA_CLASES_TODAS_NINGUNA,
                true,
                true,
                $idiomas->_("Clase de sensor"));
            $control_lista_clases_actuador = dame_control_lista_clases_actuador(
                $id_controles,
                OPCIONES_EXTRA_LISTA_CLASES_TODAS_NINGUNA,
                true,
                $idiomas->_("Clase de actuador"));
            $boton = dame_boton_formulario($id_controles, $idiomas->_("Filtrar"));

            $controles = array(
                $control_lista_clases_sensor,
                $control_lista_clases_actuador,
                $boton
            );
            return ($controles);
        }
	}
?>
